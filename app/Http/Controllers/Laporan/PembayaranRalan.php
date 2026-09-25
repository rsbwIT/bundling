<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class PembayaranRalan extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    function PembayaranRanal() {
        $tanggl1 = date('Y-m-d');
        $tanggl2 = date('Y-m-d');
        $penjab = $this->cacheService->getPenjab();
        // CORE QUERY
        $paymentRalan = DB::table('reg_periksa')
            ->select('reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.status_bayar',
                'pasien.nm_pasien',
                'dokter.nm_dokter',
                'poliklinik.nm_poli')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->whereNotIn('reg_periksa.no_rawat', function ($query) {
                $query->select('piutang_pasien.no_rawat')->from('piutang_pasien');
            })
            ->where('reg_periksa.tgl_registrasi', $tanggl1)
            ->orderBy('reg_periksa.kd_dokter')
            ->orderBy('reg_periksa.tgl_registrasi')
            ->get();
            
        $noRawats = $paymentRalan->pluck('no_rawat')->toArray();
        $notaJalan = DB::table('nota_jalan')->select('no_rawat', 'no_nota')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $billings = DB::table('billing')->select('no_rawat', 'nm_perawatan', 'totalbiaya', 'status')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');

        $paymentRalan->map(function ($item) use ($notaJalan, $billings) {
            $item->getNomorNota = isset($notaJalan[$item->no_rawat]) ? $notaJalan[$item->no_rawat] : collect();
            
            $itemBillings = isset($billings[$item->no_rawat]) ? $billings[$item->no_rawat] : collect();
            $filterBilling = function($status) use ($itemBillings) {
                return $itemBillings->where('status', $status)->values();
            };

            $item->getLaborat = $filterBilling('Laborat');
            $item->getRadiologi = $filterBilling('Radiologi');
            $item->getObat = $filterBilling('Obat');
            $item->getRalanDokter = $filterBilling('Ralan Dokter');
            $item->getRalanDrParamedis = $filterBilling('Ralan Dokter Paramedis');
            $item->getRalanParamedis = $filterBilling('Ralan Paramedis');
            $item->getTambahan = $filterBilling('Tambahan');
            $item->getPotongan = $filterBilling('Potongan');
            $item->getRegistrasi = $filterBilling('Registrasi');
            $item->getOprasi = $filterBilling('Operasi');
            return $item;
        });

        return view('laporan.pembayaranRalan', [
            'penjab'=> $penjab,
            'paymentRalan'=> $paymentRalan,
        ]);
    }

    // PENCARIAN
    function CariPembayaranRanal(Request $request) {
        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1 ?: date('Y-m-d');
        $tanggl2 = $request->tgl2 ?: date('Y-m-d');
        $penjab = $this->cacheService->getPenjab();

        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));

        $paymentRalan = DB::table('reg_periksa')
            ->select('reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.status_bayar',
                'pasien.nm_pasien',
                'dokter.nm_dokter',
                'poliklinik.nm_poli')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->whereBetween('reg_periksa.tgl_registrasi',[$tanggl1, $tanggl2])
            ->whereNotIn('reg_periksa.no_rawat', function ($query) {
                $query->select('piutang_pasien.no_rawat')->from('piutang_pasien');
            })
            ->where(function ($query) use ($kdPenjamin) {
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }
            })
            ->where(function ($query) use ($cariNomor) {
                if (!empty($cariNomor)) {
                    $query->where(function($q) use ($cariNomor) {
                $q->orWhere('reg_periksa.no_rawat', 'like', $cariNomor . '%');
                $q->orWhere('reg_periksa.no_rkm_medis', 'like', $cariNomor . '%');
                $q->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                                });
                }
            })
            ->orderBy('reg_periksa.kd_dokter')
            ->orderBy('reg_periksa.tgl_registrasi')
            ->get();
            
        $noRawats = $paymentRalan->pluck('no_rawat')->toArray();
        $notaJalan = DB::table('nota_jalan')->select('no_rawat', 'no_nota')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $billings = DB::table('billing')->select('no_rawat', 'nm_perawatan', 'totalbiaya', 'status')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');

        $paymentRalan->map(function ($item) use ($notaJalan, $billings) {
            $item->getNomorNota = isset($notaJalan[$item->no_rawat]) ? $notaJalan[$item->no_rawat] : collect();
            
            $itemBillings = isset($billings[$item->no_rawat]) ? $billings[$item->no_rawat] : collect();
            $filterBilling = function($status) use ($itemBillings) {
                return $itemBillings->where('status', $status)->values();
            };

            $item->getLaborat = $filterBilling('Laborat');
            $item->getRadiologi = $filterBilling('Radiologi');
            $item->getObat = $filterBilling('Obat');
            $item->getRalanDokter = $filterBilling('Ralan Dokter');
            $item->getRalanDrParamedis = $filterBilling('Ralan Dokter Paramedis');
            $item->getRalanParamedis = $filterBilling('Ralan Paramedis');
            $item->getTambahan = $filterBilling('Tambahan');
            $item->getPotongan = $filterBilling('Potongan');
            $item->getRegistrasi = $filterBilling('Registrasi');
            $item->getOprasi = $filterBilling('Operasi');
            return $item;
        });

        return view('laporan.pembayaranRalan', [
            'penjab'=> $penjab,
            'paymentRalan'=>$paymentRalan,
        ]);

    }
}
