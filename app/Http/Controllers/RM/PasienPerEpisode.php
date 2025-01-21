<?php

namespace App\Http\Controllers\RM;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use Illuminate\Http\Request;

class PasienPerEpisode extends Controller
{

    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function PasienPerEpisode(Request $request)
    {
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));
        $penjab = $this->cacheService->getPenjab();
        $reg_periksa = DB::table('reg_periksa as rp')
            ->select([
                'rp.tgl_registrasi',
                'rp.no_rkm_medis'
            ])
            ->whereBetween('rp.tgl_registrasi', [$request->tgl1, $request->tgl2])
            ->groupBy('rp.no_rkm_medis')
            ->having(DB::raw('COUNT(rp.no_rkm_medis)'), '>', 1)
            ->get();
        $episode = DB::table('reg_periksa as rp')
            ->select([
                'rp.tgl_registrasi',
                'rp.no_rkm_medis',
                'rp.kd_poli',
                'rp.status_bayar',
                'pl.nm_poli',
                'ps.nm_pasien',
                'rp.kd_dokter as kd_dokter_reg',
                'rp.status_lanjut',
                'rp.kd_pj',
                'dr_reg.nm_dokter as nm_dokter_reg',
                DB::raw("
            CASE
                WHEN rp.status_lanjut = 'ralan' THEN NULL
                ELSE dr_dpjp.nm_dokter
            END AS nm_dokter_dpjp
        ")
            ])
            ->join('pasien as ps', 'rp.no_rkm_medis', '=', 'ps.no_rkm_medis')
            ->join('dokter as dr_reg', 'rp.kd_dokter', '=', 'dr_reg.kd_dokter')
            ->leftJoin('dpjp_ranap as drp', 'rp.no_rawat', '=', 'drp.no_rawat')
            ->leftJoin('dokter as dr_dpjp', 'drp.kd_dokter', '=', 'dr_dpjp.kd_dokter')
            ->leftJoin('poliklinik as pl', 'rp.kd_poli', '=', 'pl.kd_poli')
            ->whereNotIn('rp.kd_poli', ['U0057', 'U0066', 'U022I', 'FIS', 'U014I', 'FISI', 'U0061', 'U017I'])
            ->whereBetween('rp.tgl_registrasi', [$request->tgl1, $request->tgl2])
            ->where('rp.kd_pj', 'bpj')
            ->where('rp.status_bayar', ['Sudah Bayar'])
            ->orderBy('ps.nm_pasien', 'asc')
            ->whereIn('rp.no_rkm_medis', $reg_periksa->pluck('no_rkm_medis'));

        // ->get();
        // dd($episode);
        $results = $episode->paginate(1000);


        return  view("rm.pasien-per-episode", [
            'results' => $results,
            'penjab' => $penjab,
        ]);
    }
    //
}
