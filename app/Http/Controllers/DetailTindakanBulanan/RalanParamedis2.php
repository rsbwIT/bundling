<?php

namespace App\Http\Controllers\DetailTindakanBulanan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class RalanParamedis2 extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    function RalanParamedis2(Request $request) {
        $action2 = '/ralan-paramedis2';
        $penjab = $this->cacheService->getPenjab();
        $petugas = $this->cacheService->getPetugas();
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));
        $kdPetugas = ($request->input('kdPetugas') == null) ? "" : explode(',', $request->input('kdPetugas'));
        // $status = ($request->statusLunas == null ? "Lunas" : $request->statusLunas);
        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $statusLunas = $request->statusLunas;
        $jenisTanggal = $request->jenisTanggal;


        $RalanParamedis2 = DB::table('pasien')
            ->select('rawat_jl_pr.no_rawat',
                'nota_jalan.no_nota',
                'nota_jalan.tanggal',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'rawat_jl_pr.kd_jenis_prw',
                'jns_perawatan.nm_perawatan',
                'rawat_jl_pr.nip',
                'petugas.nama',
                'rawat_jl_pr.tgl_perawatan',
                'rawat_jl_pr.jam_rawat',
                'penjab.png_jawab',
                'poliklinik.nm_poli',
                'rawat_jl_pr.material',
                'rawat_jl_pr.bhp',
                'rawat_jl_pr.tarif_tindakanpr',
                'rawat_jl_pr.kso',
                'rawat_jl_pr.menejemen',
                'rawat_jl_pr.biaya_rawat',
                'bayar_piutang.tgl_bayar',
                'piutang_pasien.status'
                )
            ->join('reg_periksa','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
            ->join('rawat_jl_pr','rawat_jl_pr.no_rawat','=','reg_periksa.no_rawat')
            ->join('jns_perawatan','rawat_jl_pr.kd_jenis_prw','=','jns_perawatan.kd_jenis_prw')
            ->join('petugas','rawat_jl_pr.nip','=','petugas.nip')
            ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
            ->join('penjab','reg_periksa.kd_pj','=','penjab.kd_pj')
            ->leftJoin('nota_jalan', 'reg_periksa.no_rawat', '=', 'nota_jalan.no_rawat')
            ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
            ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'rawat_jl_pr.no_rawat')
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->where(function ($query) use ($jenisTanggal, $tanggl1, $tanggl2) {
                if ($jenisTanggal == 'bayar') {
                     $query->whereBetween('nota_jalan.tanggal', [$tanggl1, $tanggl2]);
                } else {
                     $query->whereBetween('reg_periksa.tgl_registrasi', [$tanggl1, $tanggl2]);
                }
            })
            ->where(function ($query) use ($kdPenjamin, $kdPetugas, $statusLunas) {
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }
                if ($kdPetugas) {
                    $query->whereIn('petugas.nip', $kdPetugas);
                }
                
                if ($statusLunas == 'Lunas') {
                    $query->where('piutang_pasien.status', 'Lunas');
                } elseif ($statusLunas == 'Belum Lunas') {
                    $query->where('piutang_pasien.status', 'Belum Lunas');
                }
            })
            ->where(function($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            // ->groupBy('rawat_jl_pr.no_rawat','rawat_jl_pr.kd_jenis_prw','rawat_jl_pr.jam_rawat','rawat_jl_pr.tarif_tindakanpr','rawat_jl_pr.tgl_perawatan')
            ->orderBy('rawat_jl_pr.no_rawat','desc')
            ->get();
        return view('detail-tindakan-bulanan.ralan-paramedis2',[
            'action'=> $action2,
            'penjab'=> $penjab,
            'petugas'=> $petugas,
            'RalanParamedis2'=> $RalanParamedis2,
        ]);
    }
}
