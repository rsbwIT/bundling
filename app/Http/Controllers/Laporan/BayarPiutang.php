<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BayarPiutang extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    function CariBayarPiutang(Request $request)
    {
        $url = 'cari-bayar-piutang';
        $penjab = $this->cacheService->getPenjab();

        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $statusLanjut = $request->status_lanjut;

        $status = ($request->statusLunas == null ? "Lunas" : $request->statusLunas);
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));

        $bayarPiutang = DB::table('reg_periksa')
            ->select(
                'bayar_piutang.tgl_bayar',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'bayar_piutang.besar_cicilan',
                'bayar_piutang.catatan',
                'reg_periksa.no_rawat',
                'bayar_piutang.diskon_piutang',
                'bayar_piutang.tidak_terbayar',
                'reg_periksa.kd_pj',
                'penjab.png_jawab',
                'piutang_pasien.status',
                'piutang_pasien.uangmuka',
                'reg_periksa.status_lanjut'
                // // Testing
                // 'detail_piutang_pasien.kd_pj as COB',
                // 'penjabCOB.png_jawab as png_jawabCOB'

            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            // ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
            // ->leftJoin(DB::raw("
            //     (
            //         SELECT
            //             no_rawat,
            //             MAX(tgl_bayar) AS tgl_bayar,
            //             SUM(besar_cicilan) AS besar_cicilan,
            //             SUM(diskon_piutang) AS diskon_piutang,
            //             SUM(tidak_terbayar) AS tidak_terbayar,
            //             GROUP_CONCAT(catatan SEPARATOR '; ') AS catatan
            //         FROM bayar_piutang
            //         GROUP BY no_rawat
            //     ) bayar_piutang
            // "), 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
            ->join('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
            ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            // // Testing
            // ->leftJoin('detail_piutang_pasien', function($join) {
            //     $join->on('bayar_piutang.no_rawat', '=', 'detail_piutang_pasien.no_rawat')
            //          ->on('bayar_piutang.besar_cicilan', '=', 'detail_piutang_pasien.totalpiutang');
            // })
            // ->leftJoin('penjab as penjabCOB', 'detail_piutang_pasien.kd_pj', '=', 'penjabCOB.kd_pj')
            // // /Testing

            ->where(function ($query) use ($status, $kdPenjamin, $tanggl1, $tanggl2, $statusLanjut) {

                // Filter Penjamin
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }

                // Filter Status Piutang
                if ($status == "Lunas") {
                    $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                        ->where('piutang_pasien.status', 'Lunas');
                } elseif ($status == "Belum Lunas") {
                    $query->whereBetween('piutang_pasien.tgl_piutang', [$tanggl1, $tanggl2])
                        ->where('piutang_pasien.status', 'Belum Lunas');
                }

                // ⭐ Filter Status Lanjut (Ralan / Ranap)
                if ($statusLanjut != null) {
                    $query->where('reg_periksa.status_lanjut', $statusLanjut);
                }
            })
            ->when(!empty($cariNomor), function ($query) use ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            })
            ->orderBy('bayar_piutang.no_rawat', 'asc')
            ->paginate(10000);
        $noRawats = $bayarPiutang->pluck('no_rawat')->toArray();

        // Eager load bridging_sep
        $seps = collect();
        if (!empty($noRawats)) {
            $seps = DB::table('bridging_sep')
                ->select('no_rawat', 'no_sep', 'jnspelayanan')
                ->whereIn('no_rawat', $noRawats)
                ->get()
                ->groupBy('no_rawat');
        }

        // Eager load billing
        $billings = collect();
        if (!empty($noRawats)) {
            $billings = DB::table('billing')
                ->select('no_rawat', 'no', 'status', 'totalbiaya', 'nm_perawatan')
                ->whereIn('no_rawat', $noRawats)
                ->get()
                ->groupBy('no_rawat');
        }

        $bayarPiutang->map(function ($item) use ($seps, $billings) {
            $itemSeps = $seps->get($item->no_rawat, collect());
            
            // NOMOR SEP
            $item->getNoSep = $itemSeps->filter(function($sep) use ($item) {
                if ($item->status_lanjut == 'Ralan') {
                    return $sep->jnspelayanan == '2';
                } else {
                    return $sep->jnspelayanan == '1';
                }
            })->values();

            $itemBillings = $billings->get($item->no_rawat, collect());

            // NOMOR NOTA
            $item->getNomorNota = $itemBillings->where('no', 'No.Nota')->values();
            // REGISTRASI
            $item->getRegistrasi = $itemBillings->where('status', 'Registrasi')->values();
            // Obat+Emb+Tsl / OBAT
            $item->getObat = $itemBillings->where('status', 'Obat')->values();
            // Retur Obat
            $item->getReturObat = $itemBillings->where('status', 'Retur Obat')->values();
            // Resep Pulang
            $item->getResepPulang = $itemBillings->where('status', 'Resep Pulang')->values();
            // RALAN DOKTER / 1 Paket Tindakan
            $item->getRalanDokter = $itemBillings->where('status', 'Ralan Dokter')->values();
            // RALAN DOKTER PARAMEDIS / 2 Paket Tindakan
            $item->getRalanDrParamedis = $itemBillings->where('status', 'Ralan Dokter Paramedis')->values();
            // RALAN PARAMEDIS / 3 Paket Tindakan
            $item->getRalanParamedis = $itemBillings->where('status', 'Ralan Paramedis')->values();
            // RANAP DOKTER / 4 Paket Tindakan
            $item->getRanapDokter = $itemBillings->where('status', 'Ranap Dokter')->values();
            // RANAP DOKTER PARAMEDIS / 5 Paket Tindakan
            $item->getRanapDrParamedis = $itemBillings->where('status', 'Ranap Dokter Paramedis')->values();
            // RANAP PARAMEDIS / 6 Ranap Paramedis
            $item->getRanapParamedis = $itemBillings->where('status', 'Ranap Paramedis')->values();
            // OPRASI
            $item->getOprasi = $itemBillings->where('status', 'Operasi')->values();
            // LABORAT
            $item->getLaborat = $itemBillings->where('status', 'Laborat')->values();
            // RADIOLOGI
            $item->getRadiologi = $itemBillings->where('status', 'Radiologi')->values();
            // TAMBAHAN
            $item->getTambahan = $itemBillings->where('status', 'Tambahan')->values();
            // POTONGAN
            $item->getPotongan = $itemBillings->where('status', 'Potongan')->values();
            // KAMAR INAP
            $item->getKamarInap = $itemBillings->where('status', 'Kamar')->values();

            return $item;
        });
        return view('laporan.bayarPiutang', [
            'url' => $url,
            'penjab' => $penjab,
            'bayarPiutang' => $bayarPiutang,
        ]);
    }
}
