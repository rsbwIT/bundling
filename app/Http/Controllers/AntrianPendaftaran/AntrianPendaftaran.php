<?php

namespace App\Http\Controllers\AntrianPendaftaran;

use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\DayListService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AntrianPendaftaran extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    // LIST PENDAFTARAN
    function AntrianPendaftaran()  {
        $Pendaftaran = DB::table('pendaftaran')
            ->select('kd_pendaftaran', 'nama_pendaftaran')
            ->get();
            $Pendaftaran->map(function ($item){
                $item->getLoket = DB::table('loket')
                    ->select('kd_loket', 'nama_loket', 'kd_pendaftaran')
                    ->where('kd_pendaftaran','=', $item->kd_pendaftaran)
                    ->get();
            });
        return view('antrian.pendaftaran',[
            'Pendaftaran' => $Pendaftaran
        ]);
    }

    // DISPLAY ANTRIAN
    function DisplayAntrian() {
        $getSetting = $this->cacheService->getSetting();
        return view('antrian.displayantrian',[
            'getSetting' => $getSetting
        ]);
    }

    // DISPLAY PETUGAS
    function DisplayPetugas() {
            return view('antrian.display-petugas');
    }

    // SETING ANTRIAN
    function SetingAntrian() {
        return view('antrian.setting-antrian');
    }
}
