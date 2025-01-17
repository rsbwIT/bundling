<?php

namespace App\Http\Controllers\RM;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use Illuminate\Http\Request;

class JumlahPasien extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function JumlahPasien(Request $request)
    {


        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));
        $penjab = $this->cacheService->getPenjab();
        $pasien = DB::table('reg_periksa')
    ->select(
        'reg_periksa.tgl_registrasi',
        'reg_periksa.no_rkm_medis',
        'pasien.nm_pasien',
        'penjab.png_jawab',
        'reg_periksa.status_lanjut'
    )
    ->whereBetween('reg_periksa.tgl_registrasi', [$request->tgl1, $request->tgl2])
    ->where('reg_periksa.status_lanjut', '=', $request->status_lanjut)
    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
    ->join('penjab', function ($join) {
        $join->on('pasien.kd_pj', '=', 'penjab.kd_pj')
             ->on('reg_periksa.kd_pj', '=', 'penjab.kd_pj');
    })
    ->where(function ($query) use ($kdPenjamin) {
        if ($kdPenjamin) {
            $query->whereIn('penjab.kd_pj', $kdPenjamin);
        }
    });
        //     ->get();
        // dd($pasien);
        $results = $pasien->paginate(1000);

        return  view("rm.jumlah-pasien", [
            'results' => $results,
            'penjab' => $penjab,
        ]);

        //
    }
}
