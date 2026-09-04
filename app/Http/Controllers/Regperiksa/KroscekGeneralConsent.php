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
        $hasBridgingSep = Schema::hasTable('bridging_sep');
        $hasPegawai = Schema::hasTable('pegawai');
        $hasPetugas = Schema::hasTable('petugas');
        $hasDokter = Schema::hasTable('dokter');
        $hasUser = Schema::hasTable('user');

        return [
            'has_spu' => $hasSuratPersetujuanUmum,
            'has_pu' => $hasPersetujuanUmum,
            'has_bdp' => $hasBerkasDigital,
            'has_sep' => $hasBridgingSep,
            'has_pegawai' => $hasPegawai,
            'has_petugas' => $hasPetugas,
            'has_dokter' => $hasDokter,
            'has_user' => $hasUser,
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

            // Join Berkas Digital (kode 023 = General Consent) jika ada
            if ($gcInfo['has_bdp']) {
                $query->leftJoin('berkas_digital_perawatan as bdp', function ($join) {
                    $join->on('rp.no_rawat', '=', 'bdp.no_rawat')
                        ->where('bdp.kode', '=', '023');
                });
            }

            // Join SEP jika ada
            if ($gcInfo['has_sep']) {
                $query->leftJoin('bridging_sep as sep', 'rp.no_rawat', '=', 'sep.no_rawat');
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
            $gcParts = [];
            if ($gcInfo['has_spu']) {
                $gcParts[] = "spu.no_surat IS NOT NULL";
            }
            if ($gcInfo['has_pu']) {
                $gcParts[] = "pu.no_rawat IS NOT NULL";
            }
            if ($gcInfo['has_bdp']) {
                $gcParts[] = "bdp.lokasi_file IS NOT NULL";
            }
            $gcCondition = !empty($gcParts) ? "(" . implode(" OR ", $gcParts) . ")" : "0";

            // Kondisi SEP
            $sepCondition = $gcInfo['has_sep'] ? "sep.no_sep IS NOT NULL" : "0";

            // Kondisi Wajib GC: Ranap ATAU Ralan Baru ATAU IGD Baru
            $isWajibGc = "(rp.status_lanjut = 'Ranap' OR rp.stts_daftar = 'Baru')";

            $selectRaw = "
                COUNT(DISTINCT rp.no_rawat) as total_semua_reg,
                COUNT(DISTINCT CASE WHEN rp.stts = 'Batal' THEN rp.no_rawat ELSE NULL END) as total_batal,
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' THEN rp.no_rawat ELSE NULL END) as total_aktif,
                
                -- Pasien yang wajib GC (Ralan Baru & Ranap)
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND {$isWajibGc} THEN rp.no_rawat ELSE NULL END) as total_wajib_gc,
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND {$isWajibGc} AND {$gcCondition} THEN rp.no_rawat ELSE NULL END) as total_sudah_gc,
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND {$isWajibGc} AND NOT ({$gcCondition}) THEN rp.no_rawat ELSE NULL END) as total_belum_gc,
                
                -- GC Terlewat: Wajib GC, Belum GC, tetapi SEP Sudah Dibuat
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND {$isWajibGc} AND NOT ({$gcCondition}) AND {$sepCondition} THEN rp.no_rawat ELSE NULL END) as total_terlewat_sep_ada,

                -- Ralan Baru (Khusus Baru)
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' AND rp.stts_daftar = 'Baru' THEN rp.no_rawat ELSE NULL END) as total_ralan_baru,
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' AND rp.stts_daftar = 'Baru' AND {$gcCondition} THEN rp.no_rawat ELSE NULL END) as total_ralan_baru_sudah_gc,
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' AND rp.stts_daftar = 'Baru' AND NOT ({$gcCondition}) THEN rp.no_rawat ELSE NULL END) as total_ralan_baru_belum_gc,
                
                -- Ralan Lama (Tidak wajib GC per kunjungan)
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ralan' AND rp.kd_poli != 'IGDK' AND rp.stts_daftar != 'Baru' THEN rp.no_rawat ELSE NULL END) as total_ralan_lama,
                
                -- Ranap (Semua pasien Ranap)
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ranap' THEN rp.no_rawat ELSE NULL END) as total_ranap,
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ranap' AND {$gcCondition} THEN rp.no_rawat ELSE NULL END) as total_ranap_sudah_gc,
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND rp.status_lanjut = 'Ranap' AND NOT ({$gcCondition}) THEN rp.no_rawat ELSE NULL END) as total_ranap_belum_gc,
                
                -- IGD Baru
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND rp.kd_poli = 'IGDK' AND rp.stts_daftar = 'Baru' THEN rp.no_rawat ELSE NULL END) as total_igd_baru,
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND rp.kd_poli = 'IGDK' AND rp.stts_daftar = 'Baru' AND {$gcCondition} THEN rp.no_rawat ELSE NULL END) as total_igd_baru_sudah_gc,
                COUNT(DISTINCT CASE WHEN rp.stts != 'Batal' AND rp.kd_poli = 'IGDK' AND rp.stts_daftar = 'Baru' AND NOT ({$gcCondition}) THEN rp.no_rawat ELSE NULL END) as total_igd_baru_belum_gc
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
                'total_terlewat_sep_ada' => 0,
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

            // Join Berkas Digital (kode 023 = General Consent) jika ada
            if ($gcInfo['has_bdp']) {
                $query->leftJoin('berkas_digital_perawatan as bdp', function ($join) {
                    $join->on('rp.no_rawat', '=', 'bdp.no_rawat')
                        ->where('bdp.kode', '=', '023');
                });
            }

            // Join SEP jika ada
            if ($gcInfo['has_sep']) {
                $query->leftJoin('bridging_sep as sep', 'rp.no_rawat', '=', 'sep.no_rawat');
            }

            // Group by no_rawat agar setiap registrasi pasien tepat 1 baris
            $query->groupBy('rp.no_rawat');

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

            // Filter status GC (menggunakan having karena status_gc dihitung secara agregat)
            if ($filterStatusGc == 'sudah') {
                $query->havingRaw("status_gc = 'Sudah'")->where('rp.stts', '!=', 'Batal');
            } elseif ($filterStatusGc == 'belum') {
                $query->havingRaw("status_gc = 'Belum'")->where('rp.stts', '!=', 'Batal');
            } elseif ($filterStatusGc == 'belum_sep_ada') {
                $query->havingRaw("status_gc = 'Belum' AND COUNT(sep.no_sep) > 0")
                      ->where('rp.stts', '!=', 'Batal');
            }

            // Search filter
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm, $gcInfo) {
                    $q->where('rp.no_rawat', 'like', "%{$searchTerm}%")
                        ->orWhere('p.nm_pasien', 'like', "%{$searchTerm}%")
                        ->orWhere('rp.no_rkm_medis', 'like', "%{$searchTerm}%")
                        ->orWhere('pol.nm_poli', 'like', "%{$searchTerm}%")
                        ->orWhere('d.nm_dokter', 'like', "%{$searchTerm}%")
                        ->orWhere('pj.png_jawab', 'like', "%{$searchTerm}%");
                    
                    if ($gcInfo['has_sep']) {
                        $q->orWhere('sep.no_sep', 'like', "%{$searchTerm}%")
                          ->orWhere('sep.user', 'like', "%{$searchTerm}%");

                        // Pencarian nama pembuat SEP via lookup cepat pegawai/petugas/dokter
                        $matchingUserIds = [];
                        if ($gcInfo['has_pegawai']) {
                            $pegIds = DB::table('pegawai')->where('nama', 'like', "%{$searchTerm}%")->pluck('nik')->toArray();
                            $matchingUserIds = array_merge($matchingUserIds, $pegIds);
                        }
                        if ($gcInfo['has_petugas']) {
                            $petIds = DB::table('petugas')->where('nama', 'like', "%{$searchTerm}%")->pluck('nip')->toArray();
                            $matchingUserIds = array_merge($matchingUserIds, $petIds);
                        }
                        if ($gcInfo['has_dokter']) {
                            $dokIds = DB::table('dokter')->where('nm_dokter', 'like', "%{$searchTerm}%")->pluck('kd_dokter')->toArray();
                            $matchingUserIds = array_merge($matchingUserIds, $dokIds);
                        }
                        if (!empty($matchingUserIds)) {
                            $prefixes = array_map(fn($id) => substr(trim($id), 0, 9), $matchingUserIds);
                            $allPossible = array_unique(array_merge($matchingUserIds, $prefixes));
                            $q->orWhereIn('sep.user', $allPossible);
                        }
                    }
                });
            }

            // Kondisi GC Agregat
            $gcCheckParts = [];
            if ($gcInfo['has_spu']) $gcCheckParts[] = "COUNT(spu.no_surat) > 0";
            if ($gcInfo['has_pu'])  $gcCheckParts[] = "COUNT(pu.no_rawat) > 0";
            if ($gcInfo['has_bdp']) $gcCheckParts[] = "COUNT(bdp.lokasi_file) > 0";
            $gcCheckStr = !empty($gcCheckParts) ? implode(" OR ", $gcCheckParts) : "0";

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
                DB::raw("CASE WHEN {$gcCheckStr} THEN 'Sudah' ELSE 'Belum' END as status_gc"),
            ];

            if ($gcInfo['has_spu']) {
                $selectFields[] = DB::raw("MAX(spu.no_surat) as spu_no_surat");
                $selectFields[] = DB::raw("MAX(spu.tanggal) as spu_tanggal");
                $selectFields[] = DB::raw("MAX(spu.nama_pj) as spu_nama_pj");
                $selectFields[] = DB::raw("MAX(spu.nip) as spu_nip");
                $selectFields[] = DB::raw("NULL as spu_nama_petugas");
            } else {
                $selectFields[] = DB::raw("NULL as spu_no_surat");
                $selectFields[] = DB::raw("NULL as spu_tanggal");
                $selectFields[] = DB::raw("NULL as spu_nama_pj");
                $selectFields[] = DB::raw("NULL as spu_nip");
                $selectFields[] = DB::raw("NULL as spu_nama_petugas");
            }

            if ($gcInfo['has_bdp']) {
                $selectFields[] = DB::raw("MAX(bdp.lokasi_file) as bdp_file");
            } else {
                $selectFields[] = DB::raw("NULL as bdp_file");
            }

            if ($gcInfo['has_sep']) {
                // Utamakan SEP Ranap (jnspelayanan=1) jika ada, atau SEP terbaru
                $selectFields[] = DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(sep.no_sep ORDER BY sep.jnspelayanan ASC, sep.tglsep DESC), ',', 1) as sep_no_sep");
                $selectFields[] = DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(sep.user ORDER BY sep.jnspelayanan ASC, sep.tglsep DESC), ',', 1) as sep_user");
                $selectFields[] = DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(sep.tglsep ORDER BY sep.jnspelayanan ASC, sep.tglsep DESC), ',', 1) as sep_tglsep");
                $selectFields[] = DB::raw("COUNT(DISTINCT sep.no_sep) as total_sep");
                $selectFields[] = DB::raw("NULL as sep_nama_petugas");
            } else {
                $selectFields[] = DB::raw("NULL as sep_no_sep");
                $selectFields[] = DB::raw("NULL as sep_user");
                $selectFields[] = DB::raw("NULL as sep_tglsep");
                $selectFields[] = DB::raw("0 as total_sep");
                $selectFields[] = DB::raw("NULL as sep_nama_petugas");
            }

            $paginated = $query->select($selectFields)
                ->orderByRaw('CAST(rp.no_rawat AS UNSIGNED) ASC, rp.no_rawat ASC')
                ->paginate($perPage);

            // Layer 2: Post-processing fallback resolution (Memastikan ID angka terpotong seperti NIK/NIP 180811560 terkonversi ke nama)
            if (($gcInfo['has_sep'] || $gcInfo['has_spu']) && $paginated->count() > 0) {
                $rawUserIds = [];
                foreach ($paginated as $item) {
                    $val = trim($item->sep_nama_petugas ?? $item->sep_user ?? '');
                    if (!empty($val)) {
                        $rawUserIds[] = $val;
                    }
                    $userVal = trim($item->sep_user ?? '');
                    if (!empty($userVal)) {
                        $rawUserIds[] = $userVal;
                    }
                    $spuNipVal = trim($item->spu_nip ?? '');
                    if (!empty($spuNipVal) && $spuNipVal != '-') {
                        $rawUserIds[] = $spuNipVal;
                    }
                }

                if (!empty($rawUserIds)) {
                    $rawUserIds = array_values(array_unique($rawUserIds));
                    $map = [];

                    // Exact matches first
                    if ($gcInfo['has_pegawai']) {
                        $peg = DB::table('pegawai')
                            ->selectRaw("TRIM(nik) as nik_clean, nama")
                            ->whereIn(DB::raw("TRIM(nik)"), $rawUserIds)
                            ->get();
                        foreach ($peg as $r) {
                            if (!empty($r->nik_clean)) $map[$r->nik_clean] = $r->nama;
                        }
                    }

                    if ($gcInfo['has_petugas']) {
                        $pet = DB::table('petugas')
                            ->selectRaw("TRIM(nip) as nip_clean, nama")
                            ->whereIn(DB::raw("TRIM(nip)"), $rawUserIds)
                            ->get();
                        foreach ($pet as $r) {
                            if (!empty($r->nip_clean) && !isset($map[$r->nip_clean])) $map[$r->nip_clean] = $r->nama;
                        }
                    }

                    if ($gcInfo['has_dokter']) {
                        $dok = DB::table('dokter')
                            ->selectRaw("TRIM(kd_dokter) as kd_clean, nm_dokter")
                            ->whereIn(DB::raw("TRIM(kd_dokter)"), $rawUserIds)
                            ->get();
                        foreach ($dok as $r) {
                            if (!empty($r->kd_clean) && !isset($map[$r->kd_clean])) $map[$r->kd_clean] = $r->nm_dokter;
                        }
                    }

                    // Cek ID yang belum terkonversi pada tabel user (terenkripsi AES)
                    $unmapped = array_diff($rawUserIds, array_keys($map));
                    if (!empty($unmapped) && $gcInfo['has_user']) {
                        $userDec = DB::table('user')
                            ->selectRaw("TRIM(CAST(AES_DECRYPT(id_user, 'nur') AS CHAR(50))) as dec_id")
                            ->whereIn(DB::raw("TRIM(CAST(AES_DECRYPT(id_user, 'nur') AS CHAR(50)))"), $unmapped)
                            ->get();

                        $decIds = $userDec->pluck('dec_id')->filter()->map(fn($i) => trim($i))->toArray();

                        if (!empty($decIds)) {
                            if ($gcInfo['has_pegawai']) {
                                $pegExtra = DB::table('pegawai')
                                    ->selectRaw("TRIM(nik) as nik_clean, nama")
                                    ->whereIn(DB::raw("TRIM(nik)"), $decIds)
                                    ->get();
                                foreach ($pegExtra as $r) {
                                    if (!empty($r->nik_clean)) $map[$r->nik_clean] = $r->nama;
                                }
                            }
                            if ($gcInfo['has_petugas']) {
                                $petExtra = DB::table('petugas')
                                    ->selectRaw("TRIM(nip) as nip_clean, nama")
                                    ->whereIn(DB::raw("TRIM(nip)"), $decIds)
                                    ->get();
                                foreach ($petExtra as $r) {
                                    if (!empty($r->nip_clean) && !isset($map[$r->nip_clean])) $map[$r->nip_clean] = $r->nama;
                                }
                            }
                        }
                    }

                    // Prefix / Truncated ID matching (untuk NIK terpotong seperti '180811560' -> '1808115607030002')
                    $stillUnmapped = array_diff($rawUserIds, array_keys($map));
                    foreach ($stillUnmapped as $rawId) {
                        $rawIdClean = trim($rawId);
                        if (strlen($rawIdClean) >= 5) {
                            if ($gcInfo['has_pegawai']) {
                                // Prioritaskan departemen Rekam Medis (RM), PRT, atau Pendaftaran (PDF)
                                $pRows = DB::table('pegawai')
                                    ->select('nik', 'nama', 'departemen')
                                    ->where(DB::raw("TRIM(nik)"), 'like', $rawIdClean . '%')
                                    ->orderByRaw("CASE 
                                        WHEN departemen = 'RM' OR departemen = 'PRT' OR UPPER(departemen) LIKE '%REKAM MEDIS%' OR UPPER(departemen) LIKE '%PENDAFTARAN%' OR UPPER(nama) LIKE '%(PDF)%' OR UPPER(nama) LIKE '%PENDAFTARAN%' OR UPPER(nama) LIKE '%(PRT)%' THEN 1 
                                        WHEN departemen IN ('RJ', 'IGD', 'RNAP') THEN 2 
                                        ELSE 3 
                                    END ASC")
                                    ->get();

                                if ($pRows->isNotEmpty()) {
                                    $pdfMatches = $pRows->filter(function($p) {
                                        $dept = strtoupper($p->departemen ?? '');
                                        $nama = strtoupper($p->nama ?? '');
                                        return $dept == 'RM' || $dept == 'PRT' || str_contains($dept, 'REKAM MEDIS') || str_contains($dept, 'PENDAFTARAN') || str_contains($nama, '(PDF)') || str_contains($nama, 'PENDAFTARAN') || str_contains($nama, '(PRT)');
                                    });

                                    if ($pdfMatches->count() > 1) {
                                        // Jika ada lebih dari 1 kandidat petugas pendaftaran
                                        $primary = $pdfMatches->first();
                                        $otherNames = $pdfMatches->slice(1)->pluck('nama')->map(fn($n) => trim(preg_replace('/\([A-Z]+\)\s*/', '', $n)))->implode(' / ');
                                        $map[$rawIdClean] = $primary->nama;
                                        $candidatesMap[$rawIdClean] = "Opsi lain: " . $otherNames . " (" . $rawIdClean . ")";
                                    } else {
                                        $map[$rawIdClean] = $pRows->first()->nama;
                                    }
                                    continue;
                                }
                            }
                            if ($gcInfo['has_petugas']) {
                                $ptNama = DB::table('petugas')
                                    ->where(DB::raw("TRIM(nip)"), 'like', $rawIdClean . '%')
                                    ->orderByRaw("CASE WHEN nama LIKE '%(PDF)%' THEN 1 ELSE 2 END ASC")
                                    ->value('nama');
                                if ($ptNama) {
                                    $map[$rawIdClean] = $ptNama;
                                    continue;
                                }
                            }
                            if ($gcInfo['has_dokter']) {
                                $dNama = DB::table('dokter')
                                    ->where(DB::raw("TRIM(kd_dokter)"), 'like', $rawIdClean . '%')
                                    ->value('nm_dokter');
                                if ($dNama) {
                                    $map[$rawIdClean] = $dNama;
                                    continue;
                                }
                            }
                        }
                    }

                    // Terapkan nama hasil konversi ke tiap item
                    foreach ($paginated as $item) {
                        $curPetugas = trim($item->sep_nama_petugas ?? '');
                        $curUser = trim($item->sep_user ?? '');
                        $curSpuNip = trim($item->spu_nip ?? '');

                        $resolvedKey = isset($map[$curPetugas]) ? $curPetugas : (isset($map[$curUser]) ? $curUser : null);

                        if ($resolvedKey) {
                            $item->sep_nama_petugas = $map[$resolvedKey];
                            if (isset($candidatesMap[$resolvedKey])) {
                                $item->sep_user_candidates = $candidatesMap[$resolvedKey];
                            }
                        }

                        if (!empty($curSpuNip) && isset($map[$curSpuNip])) {
                            $item->spu_nama_petugas = $map[$curSpuNip];
                        }
                    }
                }
            }

            return $paginated;

        } catch (\Exception $e) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, $perPage, 1, ['path' => request()->url()]
            );
        }
    }

    /**
     * Menampilkan view formulir persetujuan umum (General Consent)
     */
    public function lihatForm(Request $request, $no_surat)
    {
        // 1. Ambil data surat_persetujuan_umum
        $spu = DB::table('surat_persetujuan_umum')->where('no_surat', $no_surat)->first();
        if (!$spu) {
            return abort(404, 'Formulir General Consent dengan Nomor Surat ' . $no_surat . ' tidak ditemukan.');
        }

        // 2. Ambil data reg_periksa & pasien lengkap
        $pasien = DB::table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('kelurahan as kel', 'p.kd_kel', '=', 'kel.kd_kel')
            ->leftJoin('kecamatan as kec', 'p.kd_kec', '=', 'kec.kd_kec')
            ->leftJoin('kabupaten as kab', 'p.kd_kab', '=', 'kab.kd_kab')
            ->where('rp.no_rawat', $spu->no_rawat)
            ->select(
                'rp.no_rawat',
                'rp.tgl_registrasi',
                'rp.jam_reg',
                'p.no_rkm_medis',
                'p.nm_pasien',
                'p.jk',
                'p.umur',
                'p.tgl_lahir',
                'p.no_tlp',
                'p.alamat',
                'kel.nm_kel',
                'kec.nm_kec',
                'kab.nm_kab',
                DB::raw("CONCAT(p.alamat, ', ', IFNULL(kel.nm_kel, '-'), ', ', IFNULL(kec.nm_kec, '-'), ', ', IFNULL(kab.nm_kab, '-')) as alamat_lengkap")
            )
            ->first();

        // 3. Ambil setting instansi RS
        $setting = DB::table('setting')->first();

        // 4. Ambil tambahan teks & hak kelas
        $tambahan = DB::table('surat_persetujuan_umum_tambahan_teks')->where('no_surat', $no_surat)->first();

        // 5. Ambil pelepasan informasi medis
        $pelepasan = DB::table('surat_persetujuan_umum_pelepasan_informasi')->where('no_surat', $no_surat)->first();
        $pelepasan1 = ($pelepasan && !empty($pelepasan->pelepasan1)) ? $pelepasan->pelepasan1 : ($pasien->nm_pasien ?? '');
        $pelepasan2 = ($pelepasan && !empty($pelepasan->pelepasan2)) ? $pelepasan->pelepasan2 : ($spu->nama_pj ?? '');

        // 6. Ambil nama petugas admisi / pembuat surat
        $namaPetugas = '';
        if (!empty($spu->nip) && $spu->nip != '-') {
            $namaPetugas = DB::table('petugas')->where('nip', $spu->nip)->value('nama');
            if (empty($namaPetugas)) {
                $namaPetugas = DB::table('pegawai')->where('nik', $spu->nip)->value('nama');
            }
        }

        // 7. Ambil photo tanda tangan pembuat pernyataan
        $photoRow = DB::table('surat_persetujuan_umum_pembuat_pernyataan')->where('no_surat', $no_surat)->first();
        $photoUrl = null;
        if ($photoRow && !empty($photoRow->photo)) {
            $khanzaUrl = rtrim(env('URL_KHANZA', 'http://192.168.5.88'), '/');
            // Photo biasanya tersimpan 'pages/upload/PSU....jpeg'
            $photoUrl = $khanzaUrl . '/webapps/persetujuanumum/' . ltrim($photoRow->photo, '/');
        }

        return view('regperiksa.cetak-general-consent', compact(
            'spu',
            'pasien',
            'setting',
            'tambahan',
            'pelepasan1',
            'pelepasan2',
            'namaPetugas',
            'photoUrl'
        ));
    }

    /**
     * Helper redirect / lihat form via no_rawat
     */
    public function lihatFormByNoRawat(Request $request, $no_rawat)
    {
        // Cari no_surat di surat_persetujuan_umum
        $spu = DB::table('surat_persetujuan_umum')->where('no_rawat', $no_rawat)->first();
        if ($spu) {
            return $this->lihatForm($request, $spu->no_surat);
        }

        // Jika tidak ada di spu, cek di berkas_digital_perawatan
        $bdp = DB::table('berkas_digital_perawatan')
            ->where('no_rawat', $no_rawat)
            ->where('kode', '023')
            ->first();

        if ($bdp && !empty($bdp->lokasi_file)) {
            $khanzaUrl = rtrim(env('URL_KHANZA', 'http://192.168.5.88'), '/');
            return redirect($khanzaUrl . '/webapps/berkasrawat/' . ltrim($bdp->lokasi_file, '/'));
        }

        return abort(404, 'Formulir General Consent untuk No. Rawat ' . $no_rawat . ' belum tersedia.');
    }
}

