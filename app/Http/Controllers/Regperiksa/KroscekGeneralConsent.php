<?php

namespace App\Http\Controllers\Regperiksa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class KroscekGeneralConsent extends Controller
{
    /**
     * Menampilkan halaman view kroscek general consent
     * Catatan: Khusus Rawat Jalan, yang wajib General Consent adalah pasien baru (stts_daftar = 'Baru')
     */
    public function index(Request $request)
    {
        // 1. Ambil input filter
        $tanggal = $request->get('tanggal', '');
        $tanggalMulai = $request->get('tanggal_mulai', '');
        $tanggalSelesai = $request->get('tanggal_selesai', '');
        $searchTerm = $request->get('search', '');
        $filterStatusGc = $request->get('filter_status_gc', 'semua'); // semua, sudah, belum
        $filterLanjut = $request->get('filter_lanjut', 'wajib_gc');   // wajib_gc (default), semua, ralan_baru, ralan_lama, ranap, igd, batal
        $filterPenjamin = $request->get('filter_penjamin', '');
        $perPage = $request->get('per_page', 100);
        $excludedPoli = $request->get('excluded_poli', []);

        // 2. Tentukan Mode Tanggal
        $isRangeActive = !empty($tanggalMulai) && !empty($tanggalSelesai);

        if ($isRangeActive) {
            $tanggal = '';
        } else {
            $tanggalMulai = '';
            $tanggalSelesai = '';
            if (empty($tanggal)) {
                $tanggal = Carbon::now()->format('Y-m-d');
            }
        }

        // 3. Ambil master poliklinik & penjamin untuk dropdown filter
        $allPoli = DB::table('poliklinik')
            ->select('kd_poli', 'nm_poli')
            ->where('status', '1')
            ->orderBy('nm_poli', 'asc')
            ->get();

        $allPenjab = DB::table('penjab')
            ->select('kd_pj', 'png_jawab')
            ->where('status', '1')
            ->orderBy('png_jawab', 'asc')
            ->get();

        // 4. Ambil statistik dan daftar pasien
        $statistik = $this->getStatistikData($tanggal, $tanggalMulai, $tanggalSelesai, $excludedPoli, $filterPenjamin);
        $daftarPasien = $this->getDaftarPasienData(
            $tanggal,
            $tanggalMulai,
            $tanggalSelesai,
            $searchTerm,
            $filterStatusGc,
            $filterLanjut,
            $filterPenjamin,
            $perPage,
            $excludedPoli
        );

        return view('regperiksa.kroscek-general-consent', compact(
            'statistik',
            'daftarPasien',
            'tanggal',
            'tanggalMulai',
            'tanggalSelesai',
            'searchTerm',
            'filterStatusGc',
            'filterLanjut',
            'filterPenjamin',
            'perPage',
            'allPoli',
            'allPenjab',
            'excludedPoli'
        ));
    }

    /**
     * Cek keberadaan tabel General Consent di database Khanza secara aman
     */
    private function getGcJoinInfo()
    {
        $hasSuratPersetujuanUmum = Schema::hasTable('surat_persetujuan_umum');
        $hasPersetujuanUmum = Schema::hasTable('persetujuan_umum');
        $hasBerkasDigital = Schema::hasTable('berkas_digital_perawatan');

        return [
            'has_spu' => $hasSuratPersetujuanUmum,
            'has_pu' => $hasPersetujuanUmum,
            'has_bdp' => $hasBerkasDigital,
        ];
    }

    /**
     * Hitung statistik General Consent
     * Aturan: Ralan hanya wajib jika pasien Baru (stts_daftar = 'Baru'). Ranap semua pasien.
     */
    private function getStatistikData($tanggal, $tanggalMulai = null, $tanggalSelesai = null, $excludedPoli = [], $filterPenjamin = '')
    {
        try {
            $gcInfo = $this->getGcJoinInfo();

            $query = DB::table('reg_periksa as rp')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis');

            // Join SPU jika ada
            if ($gcInfo['has_spu']) {
                $query->leftJoin('surat_persetujuan_umum as spu', 'rp.no_rawat', '=', 'spu.no_rawat');
            }

            // Join PU jika ada
            if ($gcInfo['has_pu']) {
                $query->leftJoin('persetujuan_umum as pu', 'rp.no_rawat', '=', 'pu.no_rawat');
            }

            // Filter tanggal
            if ($tanggalMulai && $tanggalSelesai) {
                $query->whereBetween('rp.tgl_registrasi', [$tanggalMulai, $tanggalSelesai]);
            } else {
                $query->where('rp.tgl_registrasi', $tanggal);
            }

            // Filter poli yang dikecualikan
            if (!empty($excludedPoli)) {
                $query->whereNotIn('rp.kd_poli', $excludedPoli);
            }

            // Filter penjamin
            if (!empty($filterPenjamin)) {
                $query->where('rp.kd_pj', $filterPenjamin);
            }

            // Kondisi General Consent
            $gcCondition = "0";
            if ($gcInfo['has_spu'] && $gcInfo['has_pu']) {
                $gcCondition = "(spu.no_surat IS NOT NULL OR pu.no_rawat IS NOT NULL)";
            } elseif ($gcInfo['has_spu']) {
                $gcCondition = "spu.no_surat IS NOT NULL";
            } elseif ($gcInfo['has_pu']) {
                $gcCondition = "pu.no_rawat IS NOT NULL";
            }

            // Kondisi Wajib GC: Ranap ATAU Ralan Baru ATAU IGD Baru
            $isWajibGc = "(rp.status_lanjut = 'Ranap' OR rp.stts_daftar = 'Baru')";

            $selectRaw = "
                COUNT(DISTINCT rp.no_rawat) as total_semua_reg,
                SUM(CASE WHEN rp.stts = 'Batal' THEN 1 ELSE 0 END) as total_batal,
                SUM(CASE WHEN rp.stts != 'Batal' THEN 1 ELSE 0 END) as total_aktif,
                
                -- Pasien yang wajib GC (Ralan Baru & Ranap)
                SUM(CASE WHEN rp.stts != 'Batal' AND {$isWajibGc} THEN 1 ELSE 0 END) as total_wajib_gc,
                SUM(CASE WHEN rp.stts != 'Batal' AND {$isWajibGc} AND {$gcCondition} THEN 1 ELSE 0 END) as total_sudah_gc,
                SUM(CASE WHEN rp.stts != 'Batal' AND {$isWajibGc} AND NOT ({$gcCondition}) THEN 1 ELSE 0 END) as total_belum_gc,
                
                -- Ralan Baru (Khusus Baru)
                SUM(CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' AND rp.stts_daftar = 'Baru' THEN 1 ELSE 0 END) as total_ralan_baru,
                SUM(CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' AND rp.stts_daftar = 'Baru' AND {$gcCondition} THEN 1 ELSE 0 END) as total_ralan_baru_sudah_gc,
                SUM(CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' AND rp.stts_daftar = 'Baru' AND NOT ({$gcCondition}) THEN 1 ELSE 0 END) as total_ralan_baru_belum_gc,
                
                -- Ralan Lama (Tidak wajib GC per kunjungan)
                SUM(CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' AND rp.stts_daftar != 'Baru' THEN 1 ELSE 0 END) as total_ralan_lama,
                
                -- Ranap (Semua pasien Ranap)
                SUM(CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as total_ranap,
                SUM(CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ranap' AND {$gcCondition} THEN 1 ELSE 0 END) as total_ranap_sudah_gc,
                SUM(CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ranap' AND NOT ({$gcCondition}) THEN 1 ELSE 0 END) as total_ranap_belum_gc,
                
                -- IGD Baru
                SUM(CASE WHEN rp.stts != 'Batal' AND rp.kd_poli = 'IGDK' AND rp.stts_daftar = 'Baru' THEN 1 ELSE 0 END) as total_igd_baru,
                SUM(CASE WHEN rp.stts != 'Batal' AND rp.kd_poli = 'IGDK' AND rp.stts_daftar = 'Baru' AND {$gcCondition} THEN 1 ELSE 0 END) as total_igd_baru_sudah_gc,
                SUM(CASE WHEN rp.stts != 'Batal' AND rp.kd_poli = 'IGDK' AND rp.stts_daftar = 'Baru' AND NOT ({$gcCondition}) THEN 1 ELSE 0 END) as total_igd_baru_belum_gc
            ";

            return $query->selectRaw($selectRaw)->first();
        } catch (\Exception $e) {
            return (object) [
                'total_semua_reg' => 0,
                'total_batal' => 0,
                'total_aktif' => 0,
                'total_wajib_gc' => 0,
                'total_sudah_gc' => 0,
                'total_belum_gc' => 0,
                'total_ralan_baru' => 0,
                'total_ralan_baru_sudah_gc' => 0,
                'total_ralan_baru_belum_gc' => 0,
                'total_ralan_lama' => 0,
                'total_ranap' => 0,
                'total_ranap_sudah_gc' => 0,
                'total_ranap_belum_gc' => 0,
                'total_igd_baru' => 0,
                'total_igd_baru_sudah_gc' => 0,
                'total_igd_baru_belum_gc' => 0,
            ];
        }
    }

    /**
     * Ambil daftar pasien untuk kroscek General Consent
     */
    private function getDaftarPasienData(
        $tanggal,
        $tanggalMulai = null,
        $tanggalSelesai = null,
        $searchTerm = '',
        $filterStatusGc = 'semua',
        $filterLanjut = 'wajib_gc',
        $filterPenjamin = '',
        $perPage = 100,
        $excludedPoli = []
    ) {
        try {
            $gcInfo = $this->getGcJoinInfo();

            $query = DB::table('reg_periksa as rp')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->join('poliklinik as pol', 'rp.kd_poli', '=', 'pol.kd_poli')
                ->join('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
                ->join('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj');

            // Join SPU jika ada
            if ($gcInfo['has_spu']) {
                $query->leftJoin('surat_persetujuan_umum as spu', 'rp.no_rawat', '=', 'spu.no_rawat');
            }

            // Join PU jika ada
            if ($gcInfo['has_pu']) {
                $query->leftJoin('persetujuan_umum as pu', 'rp.no_rawat', '=', 'pu.no_rawat');
            }

            // Filter tanggal
            if ($tanggalMulai && $tanggalSelesai) {
                $query->whereBetween('rp.tgl_registrasi', [$tanggalMulai, $tanggalSelesai]);
            } else {
                $query->where('rp.tgl_registrasi', $tanggal);
            }

            // Filter poli pengecualian
            if (!empty($excludedPoli)) {
                $query->whereNotIn('rp.kd_poli', $excludedPoli);
            }

            // Filter penjamin
            if (!empty($filterPenjamin)) {
                $query->where('rp.kd_pj', $filterPenjamin);
            }

            // Kondisi GC SQL
            $gcCondition = "0";
            if ($gcInfo['has_spu'] && $gcInfo['has_pu']) {
                $gcCondition = "(spu.no_surat IS NOT NULL OR pu.no_rawat IS NOT NULL)";
            } elseif ($gcInfo['has_spu']) {
                $gcCondition = "spu.no_surat IS NOT NULL";
            } elseif ($gcInfo['has_pu']) {
                $gcCondition = "pu.no_rawat IS NOT NULL";
            }

            // Filter status rawat lanjut & kategori pasien
            switch ($filterLanjut) {
                case 'wajib_gc':
                    // Default: Pasien wajib GC (Ranap ATAU Ralan Baru ATAU IGD Baru) dan bukan Batal
                    $query->where('rp.stts', '!=', 'Batal')
                          ->where(function($q) {
                              $q->where('rp.status_lanjut', 'Ranap')
                                ->orWhere('rp.stts_daftar', 'Baru');
                          });
                    break;
                case 'ralan_baru':
                    $query->where('rp.status_lanjut', 'Ralan')
                          ->where('rp.kd_poli', '!=', 'IGDK')
                          ->where('rp.stts_daftar', 'Baru')
                          ->where('rp.stts', '!=', 'Batal');
                    break;
                case 'ralan_lama':
                    $query->where('rp.status_lanjut', 'Ralan')
                          ->where('rp.kd_poli', '!=', 'IGDK')
                          ->where('rp.stts_daftar', '!=', 'Baru')
                          ->where('rp.stts', '!=', 'Batal');
                    break;
                case 'ranap':
                    $query->where('rp.status_lanjut', 'Ranap')
                          ->where('rp.stts', '!=', 'Batal');
                    break;
                case 'igd':
                    $query->where('rp.kd_poli', 'IGDK')
                          ->where('rp.stts', '!=', 'Batal');
                    break;
                case 'batal':
                    $query->where('rp.stts', 'Batal');
                    break;
                case 'semua':
                    // Semua pasien tanpa filter kategori
                    break;
            }

            // Filter status GC
            if ($filterStatusGc == 'sudah') {
                $query->whereRaw($gcCondition)->where('rp.stts', '!=', 'Batal');
            } elseif ($filterStatusGc == 'belum') {
                $query->whereRaw("NOT ({$gcCondition})")->where('rp.stts', '!=', 'Batal');
            }

            // Search filter
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('rp.no_rawat', 'like', "%{$searchTerm}%")
                        ->orWhere('p.nm_pasien', 'like', "%{$searchTerm}%")
                        ->orWhere('rp.no_rkm_medis', 'like', "%{$searchTerm}%")
                        ->orWhere('pol.nm_poli', 'like', "%{$searchTerm}%")
                        ->orWhere('d.nm_dokter', 'like', "%{$searchTerm}%")
                        ->orWhere('pj.png_jawab', 'like', "%{$searchTerm}%");
                });
            }

            $selectFields = [
                'rp.no_rawat',
                'rp.no_rkm_medis',
                'p.nm_pasien',
                'p.jk',
                'p.tgl_lahir',
                'rp.tgl_registrasi',
                'rp.jam_reg',
                'rp.status_lanjut',
                'rp.stts_daftar',
                'rp.kd_poli',
                'pol.nm_poli',
                'd.nm_dokter',
                'pj.png_jawab',
                'rp.stts',
                DB::raw("CASE 
                    WHEN rp.status_lanjut = 'Ranap' OR rp.stts_daftar = 'Baru' THEN 'Ya' 
                    ELSE 'Tidak' 
                END as is_wajib_gc"),
                DB::raw("CASE WHEN {$gcCondition} THEN 'Sudah' ELSE 'Belum' END as status_gc"),
            ];

            if ($gcInfo['has_spu']) {
                $selectFields[] = 'spu.no_surat as spu_no_surat';
                $selectFields[] = 'spu.tanggal as spu_tanggal';
                $selectFields[] = 'spu.nama_pj as spu_nama_pj';
            } else {
                $selectFields[] = DB::raw("NULL as spu_no_surat");
                $selectFields[] = DB::raw("NULL as spu_tanggal");
                $selectFields[] = DB::raw("NULL as spu_nama_pj");
            }

            return $query->select($selectFields)
                ->orderByRaw('CAST(rp.no_rawat AS UNSIGNED) ASC, rp.no_rawat ASC')
                ->paginate($perPage);

        } catch (\Exception $e) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, $perPage, 1, ['path' => request()->url()]
            );
        }
    }
}
