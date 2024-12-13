<?php

namespace App\Http\Controllers\InfoKamar;

use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Http\Controllers\Controller;

class InfoKamar extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    function InfoKamar() {
        $getSetting = $this->cacheService->getSetting();
        return view('display.info-kamar',[
            'getSetting' => $getSetting
        ]);
    }
    function InfoKamar2() {
        $getSetting = $this->cacheService->getSetting();
        return view('display.info-kamar2',[
            'getSetting' => $getSetting
        ]);
    }
    function InfoKamar3() {
        $getSetting = $this->cacheService->getSetting();
        return view('display.info-kamar3',[
            'getSetting' => $getSetting
        ]);
    }
    function InfoKamarRuangan() {
        return view('display.info-kamar-ruangan');
    }
}
