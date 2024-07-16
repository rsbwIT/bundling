<?php

namespace App\Http\Controllers\Farmasi;

use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\QueryResumeDll;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ViewSepResepController2 extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    function ViewSepResepController2(Request $request) {
        $getSetting = $this->cacheService->getSetting();
        $noRawat = $request->cariNorawat;
        $noSep = $request->cariNoSep;

        $cekNorawat = DB::table('reg_periksa')
            ->select('status_lanjut', 'kd_poli')
            ->where('no_rawat', '=', $noRawat);
        $jumlahData = $cekNorawat->count();
        $statusLanjut = $cekNorawat->first();

        if ($jumlahData > 0) {
            // 1 BERKAS SEP
            $getSEP = QueryResumeDll::getSEP($noRawat, $noSep);
            if ($getSEP) {
                $getSEP->getResep = QueryResumeDll::getResepObat($getSEP->no_rawat);
            }
        }
        else {
            $getSetting = '';
            $jumlahData = '';
            $getSEP = '';
        }
        return view('farmasi.view-berkas-sep-resep2', [
            'getSetting' => $getSetting,
            'jumlahData' => $jumlahData,
            'getSEP' => $getSEP,
            'statusLanjut' => $statusLanjut,
        ]);
    }
}
