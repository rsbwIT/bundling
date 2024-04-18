<?php

namespace App\Http\Controllers\Keperawatan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LaporanLogbokKaru extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function LaporanLogbokKaru(Request $request)
    {
        $petugas = $this->cacheService->getPetugas();
        $kdPetugas = ($request->input('kdPetugas') == null) ? explode(',', session('auth')['id_user']) : explode(',', $request->input('kdPetugas'));
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;

        $getPetugas = DB::table('petugas')
            ->select('petugas.nip', 'petugas.nama')
            ->where('petugas.status', '=', '1')
            ->whereIn('petugas.nip', $kdPetugas)
            ->get();
            $getPetugas->map(function ($item) use ($tanggl1, $tanggl2) {
                $item->getlogKepala = DB::table('bw_logbook_karu')
                    ->select('bw_logbook_karu.kd_kegiatan', 'bw_logbook_karu.user', 'bw_logbook_karu.mandiri', 'bw_logbook_karu.supervisi', 'bw_logbook_karu.tanggal', 'bw_nm_kegiatan_karu.nama_kegiatan')
                    ->join('bw_nm_kegiatan_karu', 'bw_logbook_karu.kd_kegiatan', '=', 'bw_nm_kegiatan_karu.kd_kegiatan')
                    ->where('bw_logbook_karu.user', $item->nip)
                    ->whereBetween('bw_logbook_karu.tanggal',[$tanggl1 , $tanggl2 ])
                    ->get();
            });

        return view('keperawatan.laporan-logbook-karu', [
            'petugas' => $petugas,
            'getPetugas' => $getPetugas,
        ]);
    }
}
