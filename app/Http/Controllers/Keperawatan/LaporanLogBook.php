<?php

namespace App\Http\Controllers\Keperawatan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LaporanLogBook extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    function getLookBook(Request $request) {
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
                $item->getPasien = DB::table('bw_logbook_keperawatan')
                    ->select('bw_logbook_keperawatan.id_logbook', 'bw_logbook_keperawatan.no_rkm_medis', 'bw_logbook_keperawatan.tanggal')
                    ->where('bw_logbook_keperawatan.user', $item->nip)
                    ->whereBetween('bw_logbook_keperawatan.tanggal',[$tanggl1 , $tanggl2 ])
                    ->groupBy('bw_logbook_keperawatan.no_rkm_medis','bw_logbook_keperawatan.tanggal')
                    ->orderBy('bw_logbook_keperawatan.tanggal','asc')
                    ->get();
                    $item->getPasien->map(function ($item) {
                         // Get Kegiatan Dasar
                        $item->getLogPerawat = DB::table('bw_logbook_keperawatan')
                        ->select('bw_logbook_keperawatan.id_logbook', 'bw_logbook_keperawatan.kd_kegiatan', 'bw_nm_kegiatan_keperawatan.nama_kegiatan', 'bw_logbook_keperawatan.user', 'bw_logbook_keperawatan.mandiri', 'bw_logbook_keperawatan.supervisi')
                        ->join('bw_nm_kegiatan_keperawatan','bw_logbook_keperawatan.kd_kegiatan','=','bw_nm_kegiatan_keperawatan.kd_kegiatan')
                        ->where('bw_logbook_keperawatan.no_rkm_medis', $item->no_rkm_medis)
                        ->where('bw_logbook_keperawatan.tanggal', $item->tanggal)
                        ->orderBy('bw_logbook_keperawatan.kd_kegiatan','asc')
                        ->get();
                        // Get Kewenangan Khusus
                        $item->getKewenanganKhusus = DB::table('bw_logbook_keperawatan_kewenangankhusus')
                        ->select('bw_logbook_keperawatan_kewenangankhusus.id_kewenangankhusus', 'bw_logbook_keperawatan_kewenangankhusus.kd_kewenangan', 'bw_kewenangankhusus_keperawatan.nama_kewenangan' , 'bw_logbook_keperawatan_kewenangankhusus.user', 'bw_logbook_keperawatan_kewenangankhusus.mandiri', 'bw_logbook_keperawatan_kewenangankhusus.supervisi', 'bw_logbook_keperawatan_kewenangankhusus.tanggal')
                        ->join('bw_kewenangankhusus_keperawatan','bw_logbook_keperawatan_kewenangankhusus.kd_kewenangan','=','bw_kewenangankhusus_keperawatan.kd_kewenangan')
                        ->where('bw_logbook_keperawatan_kewenangankhusus.no_rkm_medis',$item->no_rkm_medis)
                        ->where('bw_logbook_keperawatan_kewenangankhusus.tanggal',$item->tanggal)
                        ->orderBy('bw_logbook_keperawatan_kewenangankhusus.kd_kewenangan','asc')
                        ->get();
                        return $item;
                    });
                $item->getKegiatanLain = DB::table('bw_logbook_keperawatan_kegiatanlain')
                    ->select('bw_jenis_lookbook_kegiatan_lain.nama_kegiatan', 'bw_logbook_keperawatan_kegiatanlain.judul', 'bw_logbook_keperawatan_kegiatanlain.deskripsi', 'bw_logbook_keperawatan_kegiatanlain.mandiri', 'bw_logbook_keperawatan_kegiatanlain.supervisi', 'bw_logbook_keperawatan_kegiatanlain.user', 'bw_logbook_keperawatan_kegiatanlain.tanggal')
                    ->join('bw_jenis_lookbook_kegiatan_lain','bw_jenis_lookbook_kegiatan_lain.id_kegiatan','=','bw_logbook_keperawatan_kegiatanlain.id_kegiatan')
                    ->where('bw_logbook_keperawatan_kegiatanlain.user', $item->nip)
                    ->whereBetween('bw_logbook_keperawatan_kegiatanlain.tanggal',[$tanggl1 , $tanggl2 ])
                    ->get();
                    return $item;
            });

        return view('keperawatan.laporan-log-book',[
            'petugas'=>$petugas,
            'getPetugas'=>$getPetugas,
        ]);
    }
}
