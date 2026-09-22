<?php

namespace App\Http\Controllers\Keperawatan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LaporanLogBook2 extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function getLookBook(Request $request) {
        $petugas = $this->cacheService->getPetugas();
        $kdPetugas = ($request->input('kdPetugas') == null) ? explode(',',session('auth')['id_user']) : explode(',', $request->input('kdPetugas'));
        $tanggl1 = $request->tgl1 ?: date('Y-m-d');
        $tanggl2 = $request->tgl2 ?: date('Y-m-d');

        $getPetugas = DB::table('petugas')
            ->select('petugas.nip', 'petugas.nama')
            ->where('petugas.status', '=', '1')
            ->whereIn('petugas.nip', $kdPetugas)
            ->get();
            $getPetugas->map(function ($item) use ($tanggl1, $tanggl2) {
                $item->getKegiatan = DB::table('bw_logbook_keperawatan')
                    ->select('bw_nm_kegiatan_keperawatan.nama_kegiatan','bw_logbook_keperawatan.kd_kegiatan', 'bw_logbook_keperawatan.user', DB::raw('COUNT(bw_logbook_keperawatan.id_logbook) AS jumlah_id_logbook'))
                    ->join('bw_nm_kegiatan_keperawatan', 'bw_nm_kegiatan_keperawatan.kd_kegiatan', '=', 'bw_logbook_keperawatan.kd_kegiatan')
                    ->where('bw_logbook_keperawatan.user', $item->nip)
                    ->whereBetween('bw_logbook_keperawatan.tanggal', [$tanggl1, $tanggl2])
                    ->groupBy('bw_logbook_keperawatan.kd_kegiatan')
                    ->get();
                    $item->getKegiatan->map(function ($item) use ($tanggl1, $tanggl2) {
                        $item->getPasien = DB::table('bw_logbook_keperawatan')
                            ->select('bw_logbook_keperawatan.kd_kegiatan', 'bw_logbook_keperawatan.no_rkm_medis','bw_logbook_keperawatan.user', DB::raw('COUNT(bw_logbook_keperawatan.id_logbook) AS jumlah_id_logbook'))
                            ->where('bw_logbook_keperawatan.kd_kegiatan', $item->kd_kegiatan)
                            ->whereBetween('bw_logbook_keperawatan.tanggal', [$tanggl1, $tanggl2])
                            ->where('bw_logbook_keperawatan.user', $item->user)
                            ->groupBy('bw_logbook_keperawatan.no_rkm_medis')
                            ->get();
                            return $item;
            });
            });



        return view('keperawatan.laporan-log-book2',[
            'petugas'=>$petugas,
            'getPetugas' => $getPetugas,
            'tanggl1' => $tanggl1,
            'tanggl2' => $tanggl2,
        ]);
    }
}
