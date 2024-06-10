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
        $url ='cari-bayar-piutang';
        $penjab = $this->cacheService->getPenjab();

        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;

        $status = ($request->statusLunas == "Lunas") ? "Lunas" : (($request->statusLunas == "Belum Lunas") ? "Belum Lunas" : "");
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));

        $bayarPiutang = DB::table('bayar_piutang')
            ->select(
                'bayar_piutang.tgl_bayar',
                'bayar_piutang.no_rkm_medis',
                'pasien.nm_pasien',
                'bayar_piutang.besar_cicilan',
                'bayar_piutang.catatan',
                'bayar_piutang.no_rawat',
                'bayar_piutang.diskon_piutang',
                'bayar_piutang.tidak_terbayar',
                'reg_periksa.kd_pj',
                'penjab.png_jawab',
                'piutang_pasien.status',
                'piutang_pasien.uangmuka',
                'reg_periksa.status_lanjut',
                // Testing
                'detail_piutang_pasien.kd_pj as COB',
                'penjabCOB.png_jawab as png_jawabCOB'

            )
            ->join('pasien', 'bayar_piutang.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('reg_periksa', 'bayar_piutang.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'bayar_piutang.no_rawat')
            // Testing
            ->leftJoin('detail_piutang_pasien', function($join) {
                $join->on('bayar_piutang.no_rawat', '=', 'detail_piutang_pasien.no_rawat')
                     ->on('bayar_piutang.besar_cicilan', '=', 'detail_piutang_pasien.totalpiutang');
            })
            ->leftJoin('penjab as penjabCOB', 'detail_piutang_pasien.kd_pj', '=', 'penjabCOB.kd_pj')
            // /Testing

            ->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
            ->where(function ($query) use ($status, $kdPenjamin) {
                if ($status) {
                    $query->where('piutang_pasien.status', $status);
                }
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }
            })
            ->where(function ($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            // ->groupBy('bayar_piutang.no_rawat')
            ->orderBy('bayar_piutang.no_rawat', 'asc')
            ->paginate(1000);
            $bayarPiutang->map(function ($item) {
                // NOMOR NOTA
                $item->getNomorNota = DB::table('billing')
                    ->select('nm_perawatan')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('no', '=', 'No.Nota')
                    ->get();
                // REGISTRASI
                $item->getRegistrasi = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Registrasi')
                    ->get();
                // Obat+Emb+Tsl / OBAT
                $item->getObat = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Obat')
                    ->get();
                // Retur Obat
                $item->getReturObat = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Retur Obat')
                    ->get();
                // Resep Pulang
                $item->getResepPulang = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Resep Pulang')
                    ->get();
                // RALAN DOKTER / 1 Paket Tindakan
                $item->getRalanDokter = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Ralan Dokter')
                    ->get();
                // RALAN DOKTER PARAMEDIS / 2 Paket Tindakan
                $item->getRalanDrParamedis = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Ralan Dokter Paramedis')
                    ->get();
                // RALAN PARAMEDIS / 3 Paket Tindakan
                $item->getRalanParamedis = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Ralan Paramedis')
                    ->get();
                // RANAP DOKTER / 4 Paket Tindakan
                $item->getRanapDokter = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Ranap Dokter')
                    ->get();
                // RANAP DOKTER PARAMEDIS / 5 Paket Tindakan
                $item->getRanapDrParamedis = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Ranap Dokter Paramedis')
                    ->get();
                // RANAP PARAMEDIS / 6 Ranap Paramedis
                $item->getRanapParamedis = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Ranap Paramedis')
                    ->get();
                // OPRASI
                $item->getOprasi = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Operasi')
                    ->get();
                // LABORAT
                $item->getLaborat = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Laborat')
                    ->get();
                // RADIOLOGI
                $item->getRadiologi = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Radiologi')
                    ->get();
                // TAMBAHAN
                $item->getTambahan = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Tambahan')
                    ->get();
                // POTONGAN
                $item->getPotongan = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Potongan')
                    ->get();
                // KAMAR INAP
                $item->getKamarInap = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Kamar')
                    ->get();
                return $item;
            });
        return view('laporan.bayarPiutang', [
            'url' => $url,
            'penjab' => $penjab,
            'bayarPiutang' => $bayarPiutang,
        ]);
    }
}
