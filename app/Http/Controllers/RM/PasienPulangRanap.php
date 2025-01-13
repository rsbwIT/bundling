<?php

namespace App\Http\Controllers\RM;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use Illuminate\Http\Request;

class PasienPulangRanap extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function PasienPulangRanap(Request $request)
    {
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));
        $penjab = $this->cacheService->getPenjab();
        $data = DB::table('kamar_inap')
    ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
    ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
    ->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
    ->join('dokter', 'dpjp_ranap.kd_dokter', '=', 'dokter.kd_dokter')
    ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
    ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
    ->whereBetween('reg_periksa.tgl_registrasi', [$request->tgl1, $request->tgl2])
    ->where(function ($query) use ($kdPenjamin) {
        if ($kdPenjamin) {
            $query->whereIn('penjab.kd_pj', $kdPenjamin);
        }
    })
    ->select(
        'reg_periksa.no_rawat',
        'pasien.no_rkm_medis',
        'pasien.nm_pasien',
        'bangsal.nm_bangsal',
        'penjab.png_jawab',
        'kamar_inap.diagnosa_akhir',
        'kamar_inap.lama',
        'dpjp_ranap.kd_dokter',
        'dokter.nm_dokter',
        'kamar_inap.tgl_keluar'
    );
    // ->whereBetween('kamar_inap.tgl_keluar', [$tgl_awal, $tgl_akhir])
    // ->get();
    // dd($data);
    $results = $data->get();

    return  view("rm.pasien-pulang-ranap", [
        'results' => $results,
        'penjab' => $penjab,
    ]);
    }
    //
}
