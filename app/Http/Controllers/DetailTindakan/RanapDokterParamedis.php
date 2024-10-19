<?php

namespace App\Http\Controllers\DetailTindakan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RanapDokterParamedis extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    function RanapDokterParamedis(Request $request)
    {
        $action = '/ranap-dokter-paramedis';
        $penjab = $this->cacheService->getPenjab();
        $petugas = $this->cacheService->getPetugas();
        $dokter = $this->cacheService->getDokter();

        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));
        $kdPetugas = ($request->input('kdPetugas') == null) ? "" : explode(',', $request->input('kdPetugas'));
        $kdDokter = ($request->input('kdDokter')  == null) ? "" : explode(',', $request->input('kdDokter'));
        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $status = ($request->statusLunas == null ? "Lunas" : $request->statusLunas);

        $RanapDRParamedis = DB::table('pasien')
            ->select(
                'rawat_inap_drpr.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'rawat_inap_drpr.kd_jenis_prw',
                'jns_perawatan_inap.nm_perawatan',
                'rawat_inap_drpr.kd_dokter',
                'dokter.nm_dokter',
                'rawat_inap_drpr.nip',
                'petugas.nama',
                'rawat_inap_drpr.tgl_perawatan',
                'rawat_inap_drpr.jam_rawat',
                'penjab.png_jawab',
                DB::raw("IFNULL(
                    (
                        SELECT bangsal.nm_bangsal
                        FROM kamar_inap
                        INNER JOIN kamar ON kamar_inap.kd_kamar = kamar.kd_kamar
                        INNER JOIN bangsal ON kamar.kd_bangsal = bangsal.kd_bangsal
                        WHERE kamar_inap.no_rawat = rawat_inap_drpr.no_rawat LIMIT 1
                    ), 'Ruang Terhapus') AS nm_bangsal"),
                'rawat_inap_drpr.material',
                'rawat_inap_drpr.bhp',
                'rawat_inap_drpr.tarif_tindakandr',
                'rawat_inap_drpr.tarif_tindakanpr',
                'rawat_inap_drpr.kso',
                'rawat_inap_drpr.menejemen',
                'rawat_inap_drpr.biaya_rawat',
                'bayar_piutang.tgl_bayar',
                'piutang_pasien.status'
            )
            ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('rawat_inap_drpr', 'rawat_inap_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('jns_perawatan_inap', 'rawat_inap_drpr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
            ->join('dokter', 'rawat_inap_drpr.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('petugas', 'rawat_inap_drpr.nip', '=', 'petugas.nip')
            ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
            ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'rawat_inap_drpr.no_rawat')
            ->where(function ($query) use ($kdPenjamin, $kdPetugas, $kdDokter, $status,  $tanggl1, $tanggl2) {
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }
                if ($kdPetugas) {
                    $query->whereIn('petugas.nip', $kdPetugas);
                }
                if ($kdDokter) {
                    $query->whereIn('rawat_inap_drpr.kd_dokter', $kdDokter);
                }
                if ($status == "Lunas") {
                    $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                        ->where('piutang_pasien.status', 'Lunas');
                } elseif ($status == "Belum Lunas") {
                    $query->whereBetween('piutang_pasien.tgl_piutang', [$tanggl1, $tanggl2])
                        ->where('piutang_pasien.status', 'Belum Lunas');
                }
            })
            ->where(function ($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            // ->groupBy('rawat_inap_drpr.no_rawat','rawat_inap_drpr.kd_jenis_prw','rawat_inap_drpr.jam_rawat','rawat_inap_drpr.tarif_tindakanpr','rawat_inap_drpr.tgl_perawatan')
            ->orderBy('rawat_inap_drpr.no_rawat', 'DESC')
            ->get();
        $RalanDRParamedis = DB::table('pasien')
            ->select(
                'rawat_jl_drpr.no_rawat',
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
                'bayar_piutang.tgl_bayar',
                'piutang_pasien.status'
            )
            ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('rawat_jl_drpr', 'rawat_jl_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('jns_perawatan', 'rawat_jl_drpr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->join('dokter', 'rawat_jl_drpr.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('petugas', 'rawat_jl_drpr.nip', '=', 'petugas.nip')
            ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
            ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'rawat_jl_drpr.no_rawat')
            ->where('reg_periksa.status_lanjut', 'Ranap')
            ->where(function ($query) use ($kdPenjamin, $kdPetugas, $kdDokter, $status,  $tanggl1, $tanggl2) {
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }
                if ($kdPetugas) {
                    $query->whereIn('petugas.nip', $kdPetugas);
                }
                if ($kdDokter) {
                    $query->whereIn('rawat_jl_drpr.kd_dokter', $kdDokter);
                }
                if ($status == "Lunas") {
                    $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                        ->where('piutang_pasien.status', 'Lunas');
                } elseif ($status == "Belum Lunas") {
                    $query->whereBetween('piutang_pasien.tgl_piutang', [$tanggl1, $tanggl2])
                        ->where('piutang_pasien.status', 'Belum Lunas');
                }
            })
            ->where(function ($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            // ->groupBy('rawat_jl_drpr.no_rawat','rawat_jl_drpr.kd_jenis_prw','rawat_jl_drpr.jam_rawat','rawat_jl_drpr.tarif_tindakanpr','rawat_jl_drpr.tgl_perawatan')
            ->orderBy('rawat_jl_drpr.no_rawat', 'desc')
            ->get();

        return view('detail-tindakan.ranap-dokter-paramedis', [
            'action' => $action,
            'penjab' => $penjab,
            'petugas' => $petugas,
            'dokter' => $dokter,
            'RanapDRParamedis' => $RanapDRParamedis,
            'RalanDRParamedis' => $RalanDRParamedis,
        ]);
    }
}
