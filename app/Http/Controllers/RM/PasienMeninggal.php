<?php

namespace App\Http\Controllers\RM;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use Illuminate\Http\Request;

class PasienMeninggal extends Controller
{

    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function PasienMeninggal(Request $request)
    {
        // $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));
        // $penjab = $this->cacheService->getPenjab();
        $data = DB::table('pasien_mati')
            ->join('pasien', 'pasien_mati.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'pasien_mati.kd_dokter', '=', 'dokter.kd_dokter')
            ->select(
                'pasien_mati.tanggal',
                'pasien_mati.jam',
                'pasien_mati.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tmp_lahir',
                'pasien.tgl_lahir',
                'pasien.gol_darah',
                'pasien.stts_nikah',
                'pasien.agama',
                'pasien_mati.keterangan',
                'pasien_mati.temp_meninggal',
                'pasien_mati.icd1',
                'pasien_mati.icd2',
                'pasien_mati.icd3',
                'pasien_mati.icd4',
                'dokter.nm_dokter'
            )
            ->whereBetween('pasien_mati.tanggal', [$request->tgl1, $request->tgl2])
            ->orderBy('pasien_mati.tanggal', 'desc');
        // ->get();
        // dd($data);
        $results = $data->paginate(1000);

        return  view("rm.pasien-meninggal", [
            'results' => $results,
            // 'penjab' => $penjab,
        ]);
    }
}
