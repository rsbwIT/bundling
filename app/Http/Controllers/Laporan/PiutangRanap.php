<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PiutangRanap extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function CariPiutangRanap(Request $request)
    {
        $pencarian = '/cari-piutang-ranap';
        $penjab = $this->cacheService->getPenjab();
        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1 ?: date('Y-m-d');
        $tanggl2 = $request->tgl2 ?: date('Y-m-d');
        $status = ($request->statusLunas == "Lunas") ? "Lunas" : (($request->statusLunas == "Belum Lunas") ? "Belum Lunas" : "");
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));


        // qwery lama berdasarkan tanggal pulang


        // $piutangRanap = DB::table('kamar_inap')
        //     ->select(
        //         'kamar_inap.no_rawat',
        //         'reg_periksa.no_rkm_medis',
        //         'pasien.nm_pasien',
        //         'kamar_inap.tgl_keluar',
        //         'penjab.png_jawab',
        //         'kamar_inap.stts_pulang',
        //         'kamar.kd_kamar',
        //         'bangsal.nm_bangsal',
        //         'piutang_pasien.uangmuka',
        //         'piutang_pasien.totalpiutang',
        //         'reg_periksa.status_lanjut'
        //     )
        //     ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
        //     ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        //     ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        //     ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
        //     ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
        //     ->join('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        //     ->whereBetween('kamar_inap.tgl_keluar', [$tanggl1, $tanggl2])
        //     ->where(function ($query) use ($status, $kdPenjamin) {
        //         if ($status) {
        //             $query->where('piutang_pasien.status', $status);
        //         }
        //         if ($kdPenjamin) {
        //             $query->whereIn('penjab.kd_pj', $kdPenjamin);
        //         }
        //     })
        //     ->where(function ($query) use ($cariNomor) {
        //         if (!empty($cariNomor)) {
        //             $query->where(function($q) use ($cariNomor) {
        //                 $q->orWhere('reg_periksa.no_rawat', 'like', $cariNomor . '%');
        //                 $q->orWhere('reg_periksa.no_rkm_medis', 'like', $cariNomor . '%');
        //                 $q->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
        //             });
        //         }
        //     })
        //     ->orderBy('kamar_inap.tgl_keluar')
        //     ->orderBy('kamar_inap.jam_keluar')
        //     ->groupBy('kamar_inap.no_rawat')
        //     ->get();

        //qwery baru

        $piutangRanap = DB::table('kamar_inap')
                ->select(
                    'kamar_inap.no_rawat',
                    'reg_periksa.no_rkm_medis',
                    'pasien.nm_pasien',
                    'kamar_inap.tgl_keluar',
                    'penjab.png_jawab',
                    'kamar_inap.stts_pulang',
                    'kamar.kd_kamar',
                    'bangsal.nm_bangsal',
                    'piutang_pasien.uangmuka',
                    'piutang_pasien.totalpiutang',
                    'bayar_piutang.tgl_bayar',
                    'reg_periksa.status_lanjut'
                )
                ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
                ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                ->join('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('bayar_piutang', 'bayar_piutang.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat') // 🔹 Tambahkan join billing
                ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2]) // 🔹 Ganti filter tanggal
                ->where(function ($query) use ($status, $kdPenjamin) {
                    if ($status) {
                        $query->where('piutang_pasien.status', $status);
                    }
                    if ($kdPenjamin) {
                        $query->whereIn('penjab.kd_pj', $kdPenjamin);
                    }
                })
                ->where(function ($query) use ($cariNomor) {
                    if (!empty($cariNomor)) {
                        $query->where(function($q) use ($cariNomor) {
                            $q->orWhere('reg_periksa.no_rawat', 'like', $cariNomor . '%')
                              ->orWhere('reg_periksa.no_rkm_medis', 'like', $cariNomor . '%')
                              ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                        });
                    }
                })
                ->orderBy('billing.tgl_byr') // 🔹 Urutkan berdasarkan tanggal cetak billing
                ->orderBy('kamar_inap.jam_keluar')
                ->groupBy('kamar_inap.no_rawat')
                ->get();


        $noRawats = $piutangRanap->pluck('no_rawat')->toArray();

        $bridgingSep = DB::table('bridging_sep')->select('no_rawat', 'no_sep', 'jnspelayanan')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $notaInap = DB::table('nota_inap')->select('no_rawat', 'no_nota')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $billings = DB::table('billing')->select('no_rawat', 'status', 'totalbiaya')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $bayarPiutang = DB::table('bayar_piutang')->select('no_rawat', 'besar_cicilan', 'diskon_piutang', 'tidak_terbayar')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');

        $piutangRanap->map(function ($item) use ($bridgingSep, $notaInap, $billings, $bayarPiutang) {
            // NOMOR SEP
            $itemSeps = isset($bridgingSep[$item->no_rawat]) ? $bridgingSep[$item->no_rawat] : collect();
            $item->getNoSep = $itemSeps->filter(function($sep) use ($item) {
                if ($item->status_lanjut == 'Ralan') {
                    return $sep->jnspelayanan == '2';
                } else {
                    return $sep->jnspelayanan == '1';
                }
            })->values();

            // NOMOR NOTA
            $item->getNomorNota = isset($notaInap[$item->no_rawat]) ? $notaInap[$item->no_rawat] : collect();

            // BILLING
            $itemBillings = isset($billings[$item->no_rawat]) ? $billings[$item->no_rawat] : collect();
            $filterBilling = function($status) use ($itemBillings) {
                return $itemBillings->where('status', $status)->values();
            };

            $item->getRegistrasi = $filterBilling('Registrasi');
            $item->getObat = $filterBilling('Obat');
            $item->getReturObat = $filterBilling('Retur Obat');
            $item->getResepPulang = $filterBilling('Resep Pulang');
            $item->getRalanDokter = $filterBilling('Ralan Dokter');
            $item->getRalanDrParamedis = $filterBilling('Ralan Dokter Paramedis');
            $item->getRalanParamedis = $filterBilling('Ralan Paramedis');
            $item->getRanapDokter = $filterBilling('Ranap Dokter');
            $item->getRanapDrParamedis = $filterBilling('Ranap Dokter Paramedis');
            $item->getRanapParamedis = $filterBilling('Ranap Paramedis');
            $item->getOprasi = $filterBilling('Operasi');
            $item->getLaborat = $filterBilling('Laborat');
            $item->getRadiologi = $filterBilling('Radiologi');
            $item->getTambahan = $filterBilling('Tambahan');
            $item->getPotongan = $filterBilling('Potongan');
            $item->getKamarInap = $filterBilling('Kamar');
            $item->getHarian = $filterBilling('Harian');

            // SUDAH DIBAYAR / DISKON / TIDAK TERBAYAR
            $item->getSudahBayar = isset($bayarPiutang[$item->no_rawat]) ? $bayarPiutang[$item->no_rawat] : collect();

            return $item;
        });

        return view('laporan.piutangRanap', [
            'pencarian' => $pencarian,
            'penjab' => $penjab,
            'piutangRanap' => $piutangRanap,
        ]);
    }
}