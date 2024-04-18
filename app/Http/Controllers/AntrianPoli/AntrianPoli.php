<?php

namespace App\Http\Controllers\AntrianPoli;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AntrianPoli extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function AntrianPoli() {
        $antrianPoli = DB::table('bw_display_poli')
            ->select('bw_display_poli.kd_display', 'bw_display_poli.nama_display')
            ->get();
            $antrianPoli->map(function ($item){
                $item->getPoli = DB::table('bw_ruang_poli')
                ->select('bw_ruang_poli.kd_ruang_poli',
                        'bw_ruang_poli.nama_ruang_poli',
                        'bw_ruang_poli.kd_display',
                        'bw_ruang_poli.posisi_display_poli')
                        ->where('bw_ruang_poli.kd_display', $item->kd_display)
                        ->get();
        });
         return view('antrian-poli.antrian-poli', [
            'antrianPoli' => $antrianPoli
         ]);
    }
    public function display() {
        $getSetting = $this->cacheService->getSetting();
        return view('antrian-poli.display',[
            'getSetting' => $getSetting
        ]);
    }
    public function panggilpoli() {
        return view('antrian-poli.panggil-poli');
    }
    public function settingPoli() {
        return view('antrian-poli.setting-poli');
    }
}
