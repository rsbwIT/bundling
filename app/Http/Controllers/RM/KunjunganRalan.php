<?php

namespace App\Http\Controllers\RM;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\CacheService;

class KunjunganRalan extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)

    {

        $this->cacheService = $cacheService;
    }
    public function KunjunganRalan(Request $request)

    {

        $query = DB::table('reg_periksa')
        ->select([
            'reg_periksa.no_rawat',
            'reg_periksa.tgl_registrasi',
            'reg_periksa.stts_daftar',
            'dokter.nm_dokter',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'poliklinik.nm_poli',
            DB::raw("CONCAT(pasien.alamat, ', ', kelurahan.nm_kel, ', ', kecamatan.nm_kec, ', ', kabupaten.nm_kab) AS almt_pj"),
            'pasien.jk',
            DB::raw("CONCAT(reg_periksa.umurdaftar, ' ', reg_periksa.sttsumur) AS umur"),
            'pasien.tgl_daftar',
            DB::raw("GROUP_CONCAT(DISTINCT diagnosa_pasien.kd_penyakit SEPARATOR ', ') AS kd_penyakit"),
            DB::raw("COUNT(diagnosa_pasien.kd_penyakit) AS total_kasus")
        ])
        ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->join('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
        ->join('kecamatan', 'pasien.kd_kec', '=', 'kecamatan.kd_kec')
        ->join('kelurahan', 'pasien.kd_kel', '=', 'kelurahan.kd_kel')
        ->leftJoin('diagnosa_pasien', 'reg_periksa.no_rawat', '=', 'diagnosa_pasien.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->whereBetween('reg_periksa.tgl_registrasi', [$request->tgl1, $request->tgl2])
        ->groupBy('reg_periksa.no_rawat')
        ->orderBy('reg_periksa.tgl_registrasi', 'asc')
        ->orderBy('reg_periksa.jam_reg', 'asc');

        // // Filter tanggal
        // if (empty($tgl1) && empty($tgl2)) {
        //     $query->where('reg_periksa.tgl_registrasi', $tgl1);
        // } else if (!empty($tgl1) && !empty($tgl2)) {
        //     $query->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2]);
        // }

        // // Filter diagnosa
        // if ($filter_diagnosa === 'with') {
        //     $query->whereNotNull('diagnosa_pasien.kd_penyakit');
        // } else if ($filter_diagnosa === 'without') {
        //     $query->whereNull('diagnosa_pasien.kd_penyakit');
        // }

        // // Group By dan Order By
        // $query->groupBy('reg_periksa.no_rawat')
        //     ->orderBy('reg_periksa.tgl_registrasi')
        //     ->orderBy('reg_periksa.jam_reg');

        $results = $query->get();
        // dd ($request);
        return  view("rm.kunjungan-ralan", [
            'results' => $results,
            // 'penjab' => $penjab,
        ]);

        // $totalResults = $toalQuery->get();

        //
    }
}
