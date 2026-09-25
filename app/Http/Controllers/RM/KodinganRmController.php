<?php

namespace App\Http\Controllers\RM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KodinganRmController extends Controller
{
    public function index(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', Carbon::now()->format('Y-m-d'));
        $tanggalSelesai = $request->get('tanggal_selesai', Carbon::now()->format('Y-m-d'));
        $searchTerm = $request->get('search', '');
        $filterStatus = $request->get('filter_status', 'semua');
        $perPage = $request->get('per_page', 50);

        $query = DB::table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('poliklinik as pol', 'rp.kd_poli', '=', 'pol.kd_poli')
            ->leftJoin('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->leftJoin('kodingan_versi_rm as krm', 'rp.no_rawat', '=', 'krm.no_rawat')
            ->leftJoin('kamar_inap as ki', 'rp.no_rawat', '=', 'ki.no_rawat')
            ->leftJoin('resume_pasien as rr', 'rp.no_rawat', '=', 'rr.no_rawat')
            ->leftJoin('resume_pasien_ranap as rrr', 'rp.no_rawat', '=', 'rrr.no_rawat');

        // Filter berdasarkan tanggal: ranap pakai tgl_keluar (kamar_inap), lainnya pakai tgl_registrasi
        if ($filterStatus == 'ranap') {
            $query->where('rp.status_lanjut', 'Ranap')
                  ->whereBetween('ki.tgl_keluar', [$tanggalMulai, $tanggalSelesai]);
        } elseif ($filterStatus == 'ralan') {
            $query->where('rp.status_lanjut', 'Ralan')
                  ->whereBetween('rp.tgl_registrasi', [$tanggalMulai, $tanggalSelesai]);
        } else {
            $query->whereBetween('rp.tgl_registrasi', [$tanggalMulai, $tanggalSelesai]);
        }

        // Exclude pasien dengan status Batal
        $query->where('rp.stts', '!=', 'Batal');

        if (!empty($searchTerm)) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('rp.no_rawat', 'LIKE', "{$searchTerm}%")
                  ->orWhere('rp.no_rkm_medis', 'LIKE', "{$searchTerm}%")
                  ->orWhere('p.nm_pasien', 'LIKE', "%{$searchTerm}%");
            });
        }

        $query->select([
            'rp.no_rawat',
            'rp.no_rkm_medis',
            'p.nm_pasien',
            'rp.status_lanjut',
            'rp.tgl_registrasi',
            'ki.tgl_keluar as tgl_pulang',
            'pol.nm_poli',
            'pj.png_jawab',
            'rp.stts',
            'krm.icd10',
            'krm.icd9',
            DB::raw('COALESCE(
                NULLIF(CONCAT_WS(", ", NULLIF(NULLIF(TRIM(rr.diagnosa_utama), ""), "-"), NULLIF(NULLIF(TRIM(rr.diagnosa_sekunder), ""), "-"), NULLIF(NULLIF(TRIM(rr.diagnosa_sekunder2), ""), "-"), NULLIF(NULLIF(TRIM(rr.diagnosa_sekunder3), ""), "-"), NULLIF(NULLIF(TRIM(rr.diagnosa_sekunder4), ""), "-")), ""),
                NULLIF(CONCAT_WS(", ", NULLIF(NULLIF(TRIM(rrr.diagnosa_utama), ""), "-"), NULLIF(NULLIF(TRIM(rrr.diagnosa_sekunder), ""), "-"), NULLIF(NULLIF(TRIM(rrr.diagnosa_sekunder2), ""), "-"), NULLIF(NULLIF(TRIM(rrr.diagnosa_sekunder3), ""), "-"), NULLIF(NULLIF(TRIM(rrr.diagnosa_sekunder4), ""), "-")), "")
            ) as icd10_dokter'),
            DB::raw('COALESCE(
                NULLIF(CONCAT_WS(", ", NULLIF(NULLIF(TRIM(rr.prosedur_utama), ""), "-"), NULLIF(NULLIF(TRIM(rr.prosedur_sekunder), ""), "-"), NULLIF(NULLIF(TRIM(rr.prosedur_sekunder2), ""), "-"), NULLIF(NULLIF(TRIM(rr.prosedur_sekunder3), ""), "-")), ""),
                NULLIF(CONCAT_WS(", ", NULLIF(NULLIF(TRIM(rrr.prosedur_utama), ""), "-"), NULLIF(NULLIF(TRIM(rrr.prosedur_sekunder), ""), "-"), NULLIF(NULLIF(TRIM(rrr.prosedur_sekunder2), ""), "-"), NULLIF(NULLIF(TRIM(rrr.prosedur_sekunder3), ""), "-")), "")
            ) as icd9_dokter')
        ]);

        // Untuk ranap, urutkan berdasarkan tgl_keluar (tanggal pulang)
        if ($filterStatus == 'ranap') {
            $dataPasien = $query->orderBy('ki.tgl_keluar', 'DESC')->paginate($perPage);
        } else {
            $dataPasien = $query->orderBy('rp.tgl_registrasi', 'DESC')->paginate($perPage);
        }

        return view('rm.kodingan-rm', compact(
            'dataPasien', 'tanggalMulai', 'tanggalSelesai', 'searchTerm', 'filterStatus', 'perPage'
        ));
    }

    public function cariIcd10(Request $request)
    {
        $search = $request->get('q', '');
        $query = DB::table('penyakit')
            ->select('kd_penyakit', 'nm_penyakit');
            
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('kd_penyakit', 'like', "%{$search}%")
                  ->orWhere('nm_penyakit', 'like', "%{$search}%");
            });
        }
        $data = $query->limit(50)->get();
        return response()->json($data);
    }

    public function cariIcd9(Request $request)
    {
        $search = $request->get('q', '');
        $query = DB::table('icd9')
            ->select('kode', 'deskripsi_panjang', 'deskripsi_pendek');
            
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('deskripsi_panjang', 'like', "%{$search}%")
                  ->orWhere('deskripsi_pendek', 'like', "%{$search}%");
            });
        }
        $data = $query->limit(50)->get();
        return response()->json($data);
    }

    public function top10(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', Carbon::now()->format('Y-m-d'));
        $tanggalSelesai = $request->get('tanggal_selesai', Carbon::now()->format('Y-m-d'));
        $filterStatus = $request->get('filter_status', 'semua');

        $query = DB::table('reg_periksa as rp')
            ->join('kodingan_versi_rm as krm', 'rp.no_rawat', '=', 'krm.no_rawat')
            ->leftJoin('kamar_inap as ki', 'rp.no_rawat', '=', 'ki.no_rawat')
            ->whereNotNull('krm.icd10')
            ->where('krm.icd10', '!=', '');

        // Filter berdasarkan tanggal: ranap pakai tgl_keluar (kamar_inap), lainnya pakai tgl_registrasi
        if ($filterStatus == 'ranap') {
            $query->where('rp.status_lanjut', 'Ranap')
                  ->whereBetween('ki.tgl_keluar', [$tanggalMulai, $tanggalSelesai]);
        } elseif ($filterStatus == 'ralan') {
            $query->where('rp.status_lanjut', 'Ralan')
                  ->whereBetween('rp.tgl_registrasi', [$tanggalMulai, $tanggalSelesai]);
        } else {
            $query->whereBetween('rp.tgl_registrasi', [$tanggalMulai, $tanggalSelesai]);
        }

        // Exclude pasien dengan status Batal
        $query->where('rp.stts', '!=', 'Batal');

        $data = $query->pluck('krm.icd10');
        
        $counts = [];
        foreach ($data as $icd10Str) {
            $codes = explode(',', $icd10Str);
            foreach ($codes as $code) {
                $code = trim($code);
                if (!empty($code)) {
                    if (!isset($counts[$code])) {
                        $counts[$code] = 0;
                    }
                    $counts[$code]++;
                }
            }
        }

        arsort($counts);
        $top10Codes = array_slice($counts, 0, 10, true);
        
        $result = [];
        if (!empty($top10Codes)) {
            $penyakit = DB::table('penyakit')
                ->whereIn('kd_penyakit', array_keys($top10Codes))
                ->pluck('nm_penyakit', 'kd_penyakit');
                
            $no = 1;
            foreach ($top10Codes as $code => $count) {
                $result[] = [
                    'no' => $no++,
                    'kode' => $code,
                    'nama' => isset($penyakit[$code]) ? $penyakit[$code] : '-',
                    'jumlah' => $count
                ];
            }
        }

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $no_rawat = $request->input('no_rawat');
        $icd10 = $request->input('icd10');
        $icd9 = $request->input('icd9');

        if (empty($no_rawat)) {
            return response()->json(['success' => false, 'message' => 'No rawat tidak valid.']);
        }

        $exists = DB::table('kodingan_versi_rm')->where('no_rawat', $no_rawat)->exists();

        if ($exists) {
            DB::table('kodingan_versi_rm')->where('no_rawat', $no_rawat)->update([
                'icd10' => $icd10,
                'icd9' => $icd9,
                'updated_at' => Carbon::now()
            ]);
        } else {
            DB::table('kodingan_versi_rm')->insert([
                'no_rawat' => $no_rawat,
                'icd10' => $icd10,
                'icd9' => $icd9,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Data kodingan berhasil disimpan.']);
    }
}
