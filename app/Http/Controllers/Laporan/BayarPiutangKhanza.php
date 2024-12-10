<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BayarPiutangKhanza extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function BayarPiutangKhanza(Request $request)
    {
        $penjab = $this->cacheService->getPenjab();
        $url = '/bayar-piutang-khanza';
        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));

        $bayarPiutang = DB::table('bayar_piutang')
            ->select(
                'bayar_piutang.tgl_bayar',
                'bayar_piutang.no_rkm_medis',
                'pasien.nm_pasien',
                'bayar_piutang.besar_cicilan',
                'bayar_piutang.catatan',
                'bayar_piutang.no_rawat',
                'bayar_piutang.kd_rek',
                'bayar_piutang.kd_rek_kontra',
                'bayar_piutang.diskon_piutang',
                'bayar_piutang.kd_rek_diskon_piutang',
                'bayar_piutang.tidak_terbayar',
                'bayar_piutang.kd_rek_tidak_terbayar',
                'penjab.kd_pj',
                'penjab.png_jawab'
            )
            ->join('pasien', 'bayar_piutang.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('reg_periksa', 'bayar_piutang.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
            ->where(function ($query) use ($cariNomor, $kdPenjamin) {
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }
                $query->orWhere('bayar_piutang.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('bayar_piutang.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->orderBy('bayar_piutang.tgl_bayar', 'asc')
            ->orderBy('bayar_piutang.no_rkm_medis', 'asc')
            ->paginate(1000);
        $bayarPiutang->map(function ($item) {
            $item->getNomorNota = DB::table('billing')
                ->select('nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('no', '=', 'No.Nota')
                ->get();
        });
        return view('laporan.bayarPiutangKhanza', [
            'penjab' => $penjab,
            'url' => $url,
            'bayarPiutang' => $bayarPiutang,
        ]);
    }
}
