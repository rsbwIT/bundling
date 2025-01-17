<?php

namespace App\Http\Controllers\RM;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use Illuminate\Http\Request;

class StatusDataRm extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function StatusDataRm(Request $request)
    {
        $query = DB::table('reg_periksa')
        ->selectRaw("
            reg_periksa.no_rawat,
            reg_periksa.tgl_registrasi,
            dokter.nm_dokter,
            reg_periksa.stts_daftar,
            reg_periksa.no_rkm_medis,
            reg_periksa.stts,
            pasien.nm_pasien,
            poliklinik.nm_poli,
            reg_periksa.status_lanjut,
            IF(COUNT(pemeriksaan_ralan.no_rawat) > 0, 'Ada', 'Tidak Ada') AS pemeriksaan_ralan,
            IF(COUNT(pemeriksaan_ranap.no_rawat) > 0, 'Ada', 'Tidak Ada') AS pemeriksaan_ranap,
            IF(COUNT(resume_pasien.no_rawat) > 0, 'Ada', 'Tidak Ada') AS resume_pasien,
            IF(COUNT(resume_pasien_ranap.no_rawat) > 0, 'Ada', 'Tidak Ada') AS resume_pasien_ranap,
            IF(COUNT(data_triase_igd.no_rawat) > 0, 'Ada', 'Tidak Ada') AS data_triase_igd,
            IF(COUNT(penilaian_awal_keperawatan_igd.no_rawat) > 0, 'Ada', 'Tidak Ada') AS penilaian_awal_keperawatan_igd,
            IF(COUNT(diagnosa_pasien.no_rawat) > 0, 'Ada', 'Tidak Ada') AS diagnosa_pasien,
            IF(COUNT(prosedur_pasien.no_rawat) > 0, 'Ada', 'Tidak Ada') AS prosedur_pasien
        ")
        ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->leftJoin('pemeriksaan_ralan', 'reg_periksa.no_rawat', '=', 'pemeriksaan_ralan.no_rawat')
        ->leftJoin('pemeriksaan_ranap', 'reg_periksa.no_rawat', '=', 'pemeriksaan_ranap.no_rawat')
        ->leftJoin('resume_pasien', 'reg_periksa.no_rawat', '=', 'resume_pasien.no_rawat')
        ->leftJoin('resume_pasien_ranap', 'reg_periksa.no_rawat', '=', 'resume_pasien_ranap.no_rawat')
        ->leftJoin('data_triase_igd', 'reg_periksa.no_rawat', '=', 'data_triase_igd.no_rawat')
        ->leftJoin('penilaian_awal_keperawatan_igd', 'reg_periksa.no_rawat', '=', 'penilaian_awal_keperawatan_igd.no_rawat')
        ->leftJoin('diagnosa_pasien', 'reg_periksa.no_rawat', '=', 'diagnosa_pasien.no_rawat')
        ->where('reg_periksa.status_lanjut', '=', $request->status_lanjut)
        ->leftJoin('prosedur_pasien', 'reg_periksa.no_rawat', '=', 'prosedur_pasien.no_rawat')
        ->whereBetween('reg_periksa.tgl_registrasi', [$request->tgl1, $request->tgl2])
        ->groupBy('reg_periksa.no_rawat');


        //

        $results = $query->get();
        // dd ($request);
        return  view("rm.status-data-rm", [
            'results' => $results
            // 'penjab' => $penjab,
        ]);
    }
}
