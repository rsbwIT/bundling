<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CobBayarPiutang extends Controller
{

    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    function CobBayarPiutang(Request $request)
    {
        $url ='cari-cob-bayar-piutang';

        $penjab = $this->cacheService->getPenjab();

        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1 ?: date('Y-m-d');
        $tanggl2 = $request->tgl2 ?: date('Y-m-d');

        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));

        $getCob = DB::table('bayar_piutang')
            ->select(
                'bayar_piutang.tgl_bayar',
                'bayar_piutang.no_rkm_medis',
                'bayar_piutang.no_rawat',
                'reg_periksa.status_lanjut',
                'bayar_piutang.besar_cicilan',
                'pasien.nm_pasien',
                'bayar_piutang.catatan',
                'bayar_piutang.no_rawat',
                'bayar_piutang.diskon_piutang',
                'bayar_piutang.tidak_terbayar',
                'reg_periksa.kd_pj',
                'penjab.png_jawab',
                'piutang_pasien.status',
                'piutang_pasien.uangmuka',
                'reg_periksa.status_lanjut',
            )
            ->join('pasien', 'bayar_piutang.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('reg_periksa', 'bayar_piutang.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'bayar_piutang.no_rawat')
            ->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
            ->where(function ($query) use ($kdPenjamin, $cariNomor) {
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }
                $query->orWhere('reg_periksa.no_rawat', 'like', $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->groupBy('bayar_piutang.no_rawat')
            ->havingRaw('COUNT(*) > 1')
            ->get();
            
        $noRawats = $getCob->pluck('no_rawat')->toArray();
        
        $billings = DB::table('billing')->select('no_rawat', 'nm_perawatan', 'totalbiaya', 'status', 'no')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $detailCob = DB::table('detail_piutang_pasien')
                    ->select('detail_piutang_pasien.no_rawat', 'penjab.png_jawab', 'detail_piutang_pasien.totalpiutang', 'detail_piutang_pasien.sisapiutang')
                    ->join('penjab','detail_piutang_pasien.kd_pj','=','penjab.kd_pj')
                    ->whereIn('detail_piutang_pasien.no_rawat', $noRawats)
                    ->get()->groupBy('no_rawat');

        $getCob->map(function ($item) use ($billings, $detailCob) {
            $itemBillings = isset($billings[$item->no_rawat]) ? $billings[$item->no_rawat] : collect();
            
            $item->getNomorNota = $itemBillings->where('no', 'No.Nota')->values();
            $item->getRegistrasi = $itemBillings->where('status', 'Registrasi')->values();
            $item->getObat = $itemBillings->where('status', 'Obat')->values();
            $item->getReturObat = $itemBillings->where('status', 'Retur Obat')->values();
            $item->getResepPulang = $itemBillings->where('status', 'Resep Pulang')->values();
            $item->getRalanDokter = $itemBillings->where('status', 'Ralan Dokter')->values();
            $item->getRalanDrParamedis = $itemBillings->where('status', 'Ralan Dokter Paramedis')->values();
            $item->getRalanParamedis = $itemBillings->where('status', 'Ralan Paramedis')->values();
            $item->getRanapDokter = $itemBillings->where('status', 'Ranap Dokter')->values();
            $item->getRanapDrParamedis = $itemBillings->where('status', 'Ranap Dokter Paramedis')->values();
            $item->getRanapParamedis = $itemBillings->where('status', 'Ranap Paramedis')->values();
            $item->getOprasi = $itemBillings->where('status', 'Operasi')->values();
            $item->getLaborat = $itemBillings->where('status', 'Laborat')->values();
            $item->getRadiologi = $itemBillings->where('status', 'Radiologi')->values();
            $item->getTambahan = $itemBillings->where('status', 'Tambahan')->values();
            $item->getPotongan = $itemBillings->where('status', 'Potongan')->values();
            $item->getKamarInap = $itemBillings->where('status', 'Kamar')->values();
            
            $item->getDetailCob = isset($detailCob[$item->no_rawat]) ? $detailCob[$item->no_rawat] : collect();
        });

        return view('laporan.cobBayarPiutang', [
            'url' => $url,
            'penjab' => $penjab,
            'getCob'=> $getCob

        ]);
    }
}
