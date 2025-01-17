<?php

namespace App\Http\Controllers\RM;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use Illuminate\Http\Request;


class PasienRawatJalan extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function PasienRawatJalan(Request $request)
    {
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));
        $penjab = $this->cacheService->getPenjab();
        $results = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'pasien.nm_pasien',
                'penjab.png_jawab',
                DB::raw("GROUP_CONCAT(DISTINCT diagnosa_pasien.kd_penyakit SEPARATOR ', ') AS kd_penyakit"),
                DB::raw("COUNT(diagnosa_pasien.kd_penyakit) AS total_kasus"),
                'dokter.nm_dokter'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('diagnosa_pasien', 'reg_periksa.no_rawat', '=', 'diagnosa_pasien.no_rawat')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->whereBetween('reg_periksa.tgl_registrasi', [$request->tgl1, $request->tgl2])
            ->where(function ($query) use ($kdPenjamin) {
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }
            })
            ->groupBy(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'pasien.nm_pasien',
                'penjab.png_jawab',
                'dokter.nm_dokter'
            )
            ->get();
        return  view("rm.rawat-jalan", [
            'results' => $results,
            'penjab' => $penjab,
        ]);
    }
    //
}
