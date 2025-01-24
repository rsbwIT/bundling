<?php

namespace App\Http\Controllers\RM;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use Illuminate\Http\Request;

class TabulasiIGD extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function TabulasiIGD(Request $request)
    {
        $tabulasiigd = DB::table('reg_periksa')
    ->select(
        'reg_periksa.no_rawat',
        'pasien.no_rkm_medis',
        'reg_periksa.tgl_registrasi',
        'pasien.nm_pasien',
        DB::raw("GROUP_CONCAT(DISTINCT jns_perawatan_lab.nm_perawatan SEPARATOR ', ') AS pemeriksaan_lab"),
        DB::raw("GROUP_CONCAT(DISTINCT jns_perawatan_radiologi.nm_perawatan SEPARATOR ', ') AS pemeriksaan_radiologi")
    )
    ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
    ->leftJoin('periksa_lab', 'reg_periksa.no_rawat', '=', 'periksa_lab.no_rawat')
    ->leftJoin('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
    ->leftJoin('periksa_radiologi', 'reg_periksa.no_rawat', '=', 'periksa_radiologi.no_rawat')
    ->leftJoin('jns_perawatan_radiologi', 'periksa_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
    ->where('reg_periksa.kd_poli', 'IGDK')
    ->whereBetween('reg_periksa.tgl_registrasi', [$request->tgl1, $request->tgl2])
    ->groupBy('reg_periksa.no_rawat', 'pasien.no_rkm_medis', 'reg_periksa.tgl_registrasi', 'pasien.nm_pasien')
    ->orderBy('pasien.nm_pasien', 'ASC');
    // ->get();
    // dd($tabulasiigd);
    $results = $tabulasiigd->paginate(1000);


    return  view("rm.tabulasi-igd", [
        'results' => $results,
        // 'penjab' => $penjab,
    ]);
    }

    //
}
