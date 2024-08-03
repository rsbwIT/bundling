<?php

namespace App\Http\Controllers\DetailTindakan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class RalanDokter extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    function RalanDokter(Request $request) {
        $actionCari = '/ralan-dokter';
        $penjab = $this->cacheService->getPenjab();
        $dokter = $this->cacheService->getDokter();


        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));
        $kdDokter = ($request->input('kdDokter')  == null) ? "" : explode(',', $request->input('kdDokter'));


        $RalanDokter = DB::table('pasien')
            ->select('rawat_jl_dr.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'rawat_jl_dr.kd_jenis_prw',
                'jns_perawatan.nm_perawatan',
                'rawat_jl_dr.kd_dokter',
                'dokter.nm_dokter',
                'rawat_jl_dr.tgl_perawatan',
                'rawat_jl_dr.jam_rawat',
                'penjab.png_jawab',
                'poliklinik.nm_poli',
                'rawat_jl_dr.material',
                'rawat_jl_dr.bhp',
                'rawat_jl_dr.tarif_tindakandr',
                'rawat_jl_dr.kso',
                'rawat_jl_dr.menejemen',
                'rawat_jl_dr.biaya_rawat',
                'bayar_piutang.tgl_bayar')
            ->join('reg_periksa','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
            ->join('rawat_jl_dr','reg_periksa.no_rawat','=','rawat_jl_dr.no_rawat')
            ->join('dokter','rawat_jl_dr.kd_dokter','=','dokter.kd_dokter')
            ->join('jns_perawatan','rawat_jl_dr.kd_jenis_prw','=','jns_perawatan.kd_jenis_prw')
            ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
            ->join('penjab','reg_periksa.kd_pj','=','penjab.kd_pj')
            ->join('bayar_piutang','reg_periksa.no_rawat','=','bayar_piutang.no_rawat')
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->whereBetween('bayar_piutang.tgl_bayar',[$tanggl1, $tanggl2])
            ->where(function ($query) use ($kdPenjamin, $kdDokter) {
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }
                if ($kdDokter) {
                    $query->whereIn('rawat_jl_dr.kd_dokter', $kdDokter);
                }
            })
            ->where(function($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            // ->groupBy('rawat_jl_dr.no_rawat','rawat_jl_dr.kd_jenis_prw','rawat_jl_dr.jam_rawat','rawat_jl_dr.tarif_tindakandr','rawat_jl_dr.tgl_perawatan')
            ->orderBy('rawat_jl_dr.no_rawat','desc')
            ->get();

        return view('detail-tindakan.ralan-dokter',[
            'actionCari'=> $actionCari,
            'penjab'=> $penjab,
            'dokter'=> $dokter,
            'RalanDokter'=> $RalanDokter,
        ]);
    }
}
