<?php

namespace App\Http\Controllers\DetailTindakanUmum;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RanapParamedisUm extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    function RanapParamedisUm(Request $request)
    {
        $action = '/ranap-paramedis-umum';
        $petugas = $this->cacheService->getPetugas();
        $kdPetugas = ($request->input('kdPetugas') == null) ? "" : explode(',', $request->input('kdPetugas'));
        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;

        $getRanapParamedis = DB::table('pasien')
            ->select(
                'rawat_inap_pr.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'rawat_inap_pr.kd_jenis_prw',
                'jns_perawatan_inap.nm_perawatan',
                'rawat_inap_pr.nip',
                'petugas.nama',
                'rawat_inap_pr.tgl_perawatan',
                'rawat_inap_pr.jam_rawat',
                'penjab.png_jawab',
                DB::raw("IFNULL(
                    (
                        SELECT bangsal.nm_bangsal
                        FROM kamar_inap
                        INNER JOIN kamar ON kamar_inap.kd_kamar = kamar.kd_kamar
                        INNER JOIN bangsal ON kamar.kd_bangsal = bangsal.kd_bangsal
                        WHERE kamar_inap.no_rawat = rawat_inap_pr.no_rawat
                        LIMIT 1
                    ),
                    'Ruang Terhapus'
                ) AS ruang"),
                'rawat_inap_pr.material',
                'rawat_inap_pr.bhp',
                'rawat_inap_pr.tarif_tindakanpr',
                'rawat_inap_pr.kso',
                'rawat_inap_pr.menejemen',
                'rawat_inap_pr.biaya_rawat',
                DB::raw("COALESCE(nota_inap.tanggal, nota_jalan.tanggal) as tgl_byr"),
                DB::raw("COALESCE(nota_inap.no_nota, nota_jalan.no_nota) as no_nota")
            )
            ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('rawat_inap_pr', 'rawat_inap_pr.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('jns_perawatan_inap', 'rawat_inap_pr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
            ->join('petugas', 'rawat_inap_pr.nip', '=', 'petugas.nip')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('billing','billing.no_rawat','=','reg_periksa.no_rawat')
            ->leftJoin('nota_inap', 'reg_periksa.no_rawat', '=', 'nota_inap.no_rawat')
            ->leftJoin('nota_jalan', 'reg_periksa.no_rawat', '=', 'nota_jalan.no_rawat')
            ->where('billing.no','=','No.Nota')
            ->where('penjab.kd_pj','UMU')
            ->where('reg_periksa.status_lanjut', 'Ranap')
            ->whereBetween('billing.tgl_byr',[$tanggl1, $tanggl2])
            ->where(function ($query) use ($kdPetugas) {
                if ($kdPetugas) {
                    $query->whereIn('petugas.nip', $kdPetugas);
                }
            })
            ->where(function ($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->orderByDesc('rawat_inap_pr.no_rawat')
            ->get();
        $RalanParamedis = DB::table('pasien')
            ->select(
                'rawat_jl_pr.no_rawat',
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
                DB::raw("COALESCE(nota_inap.tanggal, nota_jalan.tanggal) as tgl_byr"),
                DB::raw("COALESCE(nota_inap.no_nota, nota_jalan.no_nota) as no_nota")
            )
            ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('rawat_jl_pr', 'rawat_jl_pr.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('jns_perawatan', 'rawat_jl_pr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->join('petugas', 'rawat_jl_pr.nip', '=', 'petugas.nip')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('billing','billing.no_rawat','=','reg_periksa.no_rawat')
            ->leftJoin('nota_inap', 'reg_periksa.no_rawat', '=', 'nota_inap.no_rawat')
            ->leftJoin('nota_jalan', 'reg_periksa.no_rawat', '=', 'nota_jalan.no_rawat')
            ->where('billing.no','=','No.Nota')
            ->where('penjab.kd_pj','UMU')
            ->where('reg_periksa.status_lanjut', 'Ranap')
            ->whereBetween('billing.tgl_byr',[$tanggl1, $tanggl2])
            ->where(function ($query) use ( $kdPetugas) {
                if ($kdPetugas) {
                    $query->whereIn('petugas.nip', $kdPetugas);
                }
            })
            ->where(function ($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->orderBy('rawat_jl_pr.no_rawat', 'desc')
            ->get();

        return view('detail-tindakan-umum.ranap-paramedis-um', [
            'action' => $action,
            'petugas' => $petugas,
            'getRanapParamedis' => $getRanapParamedis,
            'RalanParamedis' => $RalanParamedis,
        ]);
}
}
