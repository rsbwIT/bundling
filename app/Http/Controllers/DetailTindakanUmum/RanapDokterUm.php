<?php

namespace App\Http\Controllers\DetailTindakanUmum;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RanapDokterUm extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function RanapDokterUm(Request $request)
    {
        $actionCari = '/ranap-dokter-umum';
        $dokter = $this->cacheService->getDokter();

        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $kdDokter = ($request->input('kdDokter')  == null) ? "" : explode(',', $request->input('kdDokter'));

        $ranapDokterUmum = DB::table('pasien')
            ->select(
                'rawat_inap_dr.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'rawat_inap_dr.kd_jenis_prw',
                'jns_perawatan_inap.nm_perawatan',
                'rawat_inap_dr.kd_dokter',
                'dokter.nm_dokter',
                'rawat_inap_dr.tgl_perawatan',
                'rawat_inap_dr.jam_rawat',
                'penjab.png_jawab',
                DB::raw("IFNULL((SELECT bangsal.nm_bangsal FROM kamar_inap INNER JOIN kamar INNER JOIN bangsal ON kamar_inap.kd_kamar = kamar.kd_kamar AND kamar.kd_bangsal = bangsal.kd_bangsal WHERE kamar_inap.no_rawat = rawat_inap_dr.no_rawat LIMIT 1), 'Ruang Terhapus') AS ruang"),
                'rawat_inap_dr.material',
                'rawat_inap_dr.bhp',
                'rawat_inap_dr.tarif_tindakandr',
                'rawat_inap_dr.kso',
                'rawat_inap_dr.menejemen',
                'rawat_inap_dr.biaya_rawat',
                DB::raw("COALESCE(nota_inap.tanggal, nota_jalan.tanggal) as tgl_byr"),
                DB::raw("COALESCE(nota_inap.no_nota, nota_jalan.no_nota) as no_nota")
            )
            ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('rawat_inap_dr', 'rawat_inap_dr.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('jns_perawatan_inap', 'rawat_inap_dr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
            ->join('dokter', 'rawat_inap_dr.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('nota_inap', 'reg_periksa.no_rawat', '=', 'nota_inap.no_rawat')
            ->leftJoin('nota_jalan', 'reg_periksa.no_rawat', '=', 'nota_jalan.no_rawat')
            ->where('billing.no', '=', 'No.Nota')
            ->where('penjab.kd_pj', 'UMU')
            ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
            ->where(function ($query) use ($kdDokter) {
                if ($kdDokter) {
                    $query->whereIn('rawat_inap_dr.kd_dokter', $kdDokter);
                }
            })
            ->where(function ($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->orderByDesc('rawat_inap_dr.no_rawat')
            ->get();
        $RalanDokterUmum = DB::table('pasien')
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
                DB::raw("COALESCE(nota_inap.tanggal, nota_jalan.tanggal) as tgl_byr"),
                DB::raw("COALESCE(nota_inap.no_nota, nota_jalan.no_nota) as no_nota"))
            ->join('reg_periksa','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
            ->join('rawat_jl_dr','reg_periksa.no_rawat','=','rawat_jl_dr.no_rawat')
            ->join('dokter','rawat_jl_dr.kd_dokter','=','dokter.kd_dokter')
            ->join('jns_perawatan','rawat_jl_dr.kd_jenis_prw','=','jns_perawatan.kd_jenis_prw')
            ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
            ->join('penjab','reg_periksa.kd_pj','=','penjab.kd_pj')
            ->join('billing','billing.no_rawat','=','reg_periksa.no_rawat')
            ->leftJoin('nota_inap', 'reg_periksa.no_rawat', '=', 'nota_inap.no_rawat')
            ->leftJoin('nota_jalan', 'reg_periksa.no_rawat', '=', 'nota_jalan.no_rawat')
            ->where('billing.no','=','No.Nota')
            ->where('penjab.kd_pj','UMU')
            ->where('reg_periksa.status_lanjut', 'Ranap')
            ->whereBetween('billing.tgl_byr',[$tanggl1, $tanggl2])
            ->where(function ($query) use ( $kdDokter) {
                if ($kdDokter) {
                    $query->whereIn('rawat_jl_dr.kd_dokter', $kdDokter);
                }
            })
            ->where(function($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->orderBy('rawat_jl_dr.no_rawat','desc')
            ->get();

            return view('detail-tindakan-umum.ranap-dokter-um', [
            'actionCari' => $actionCari,
            'dokter' => $dokter,
            'ranapDokterUmum' => $ranapDokterUmum,
            'RalanDokterUmum' => $RalanDokterUmum,
        ]);
    }
}
