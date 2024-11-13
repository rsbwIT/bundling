<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BayarPiutangKaryawan extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    function bayarPiutangKaryawan(Request $request)
    {
        $url = 'bayar-piutang-karyawan';
        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;

        $status = ($request->statusLunas == null ? "Lunas" : $request->statusLunas);
        $kdPetugas = ($request->input('kdPetugas') == null) ? "" : explode(',', $request->input('kdPetugas'));

        $piutangKaryawan = DB::table('piutang')
            ->select(
                'piutang.no_rkm_medis',
                'pasien.nm_pasien',
                'piutang.tgl_piutang',
                'piutang.jns_jual',
                'piutang.uangmuka',
                'piutang.status',
                'piutang.nota_piutang',
                'piutang.sisapiutang',
                'bayar_piutang.besar_cicilan',
                'bayar_piutang.catatan',
                'bayar_piutang.no_rawat',
                'rekening.nm_rek',
                'bayar_piutang.diskon_piutang',
                'bayar_piutang.tidak_terbayar',
                'bayar_piutang.tgl_bayar'
            )
            ->join('pasien', 'piutang.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('bayar_piutang', 'piutang.nota_piutang', '=', 'bayar_piutang.no_rawat')
            ->leftJoin('rekening', 'bayar_piutang.kd_rek', '=', 'rekening.kd_rek')
            ->leftJoin('reg_periksa', 'piutang.nota_piutang', '=', 'reg_periksa.no_rawat')
            ->where(function ($query) use ($status, $tanggl1, $tanggl2) {
                if ($status == "Lunas") {
                    $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2]);
                } elseif ($status == "Belum Lunas") {
                    $query->whereBetween('piutang.tgl_piutang', [$tanggl1, $tanggl2]);
                }
            })
            ->where(function ($query) use ($cariNomor) {
                $query->orWhere('piutang.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->whereNull('reg_periksa.no_rawat')
            ->get();

        return view('laporan.bayarPiutangKaryawan', [
            'url' => $url,
            'piutangKaryawan' => $piutangKaryawan,
        ]);
    }
}
