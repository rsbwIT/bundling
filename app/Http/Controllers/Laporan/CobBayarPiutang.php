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

    function CobBayarPiutang()
    {
        $getCob = DB::table('bayar_piutang')
            ->select(
                'bayar_piutang.tgl_bayar',
                'bayar_piutang.no_rkm_medis',
                'bayar_piutang.no_rawat',
                'reg_periksa.status_lanjut',
                'pasien.nm_pasien'
            )
            ->leftJoin('reg_periksa', 'bayar_piutang.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'bayar_piutang.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->whereBetween('bayar_piutang.tgl_bayar', ['2024-05-01', '2024-05-31'])
            ->groupBy('bayar_piutang.no_rawat')
            ->havingRaw('COUNT(*) > 1')
            ->get();
            $getCob->map(function ($item) {
                // NOMOR NOTA
                $item->getNomorNota = DB::table('billing')
                    ->select('nm_perawatan')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('no', '=', 'No.Nota')
                    ->get();
            });
            $getCob->map(function ($item) {
                $item->getDetailCob = DB::table('detail_piutang_pasien')
                ->select('penjab.png_jawab', 'detail_piutang_pasien.totalpiutang', 'detail_piutang_pasien.sisapiutang')
                ->join('penjab','detail_piutang_pasien.kd_pj','=','penjab.kd_pj')
                ->where('detail_piutang_pasien.no_rawat',$item->no_rawat)
                ->get();
            });

        return view('laporan.cobBayarPiutang', ['getCob'=> $getCob]);
    }
}
