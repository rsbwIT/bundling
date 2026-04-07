<?php

namespace App\Http\Controllers\DetailTindakanUmum;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RalanDokterParamedisUm extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function RalanDokterParamedisUm(Request $request) {
        $action = '/ralan-dokter-paramedis-umum';
        $petugas = $this->cacheService->getPetugas();
        $dokter = $this->cacheService->getDokter();

        $kdPetugas = ($request->input('kdPetugas') == null) ? "" : explode(',', $request->input('kdPetugas'));
        $kdDokter = ($request->input('kdDokter')  == null) ? "" : explode(',', $request->input('kdDokter'));
        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;

        $RalanDrPrUmum = DB::table('pasien')
            ->select('rawat_jl_drpr.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'rawat_jl_drpr.kd_jenis_prw',
                'jns_perawatan.nm_perawatan',
                'rawat_jl_drpr.kd_dokter',
                'dokter.nm_dokter',
                'rawat_jl_drpr.nip',
                'petugas.nama',
                'rawat_jl_drpr.tgl_perawatan',
                'rawat_jl_drpr.jam_rawat',
                'penjab.png_jawab',
                'poliklinik.nm_poli',
                'rawat_jl_drpr.material',
                'rawat_jl_drpr.bhp',
                'rawat_jl_drpr.tarif_tindakandr',
                'rawat_jl_drpr.tarif_tindakanpr',
                'rawat_jl_drpr.kso',
                'rawat_jl_drpr.menejemen',
                'rawat_jl_drpr.biaya_rawat',
                DB::raw("COALESCE(nota_inap.tanggal, nota_jalan.tanggal) as tgl_byr"),
                DB::raw("COALESCE(nota_inap.no_nota, nota_jalan.no_nota) as no_nota"))
            ->join('reg_periksa','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
            ->join('rawat_jl_drpr','rawat_jl_drpr.no_rawat','=','reg_periksa.no_rawat')
            ->join('jns_perawatan','rawat_jl_drpr.kd_jenis_prw','=','jns_perawatan.kd_jenis_prw')
            ->join('dokter','rawat_jl_drpr.kd_dokter','=','dokter.kd_dokter')
            ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
            ->join('penjab','reg_periksa.kd_pj','=','penjab.kd_pj')
            ->join('petugas','rawat_jl_drpr.nip','=','petugas.nip')
            ->join('billing','billing.no_rawat','=','reg_periksa.no_rawat')
            ->leftJoin('nota_inap', 'reg_periksa.no_rawat', '=', 'nota_inap.no_rawat')
            ->leftJoin('nota_jalan', 'reg_periksa.no_rawat', '=', 'nota_jalan.no_rawat')
            ->where('billing.no','=','No.Nota')
            ->where('penjab.kd_pj','UMU')
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->whereBetween('billing.tgl_byr',[$tanggl1, $tanggl2])
            ->where(function ($query) use ($kdPetugas, $kdDokter) {
                if ($kdPetugas) {
                    $query->whereIn('petugas.nip', $kdPetugas);
                }
                if ($kdDokter) {
                    $query->whereIn('rawat_jl_drpr.kd_dokter', $kdDokter);
                }
            })
            ->where(function($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->orderBy('rawat_jl_drpr.no_rawat','desc')
            ->get();
        return view('detail-tindakan-umum.ralan-dokter-paramedis-um',[
            'action'=>$action,
            'petugas'=>$petugas,
            'dokter'=>$dokter,
            'RalanDrPrUmum'=>$RalanDrPrUmum,
        ]);
    }
}
