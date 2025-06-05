<?php

namespace App\Http\Controllers\AntrianFarmasi;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use url;

class AntrianFarmasi1 extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function AntrianFarmasi1()
    {
        $antrianFarmasi1 = DB::table('bw_display_farmasi')
            ->select('bw_display_farmasi.kd_display_farmasi', 'bw_display_farmasi.nama_display_farmasi')
            ->get();
        $antrianFarmasi1->map(function ($item) {
            $item->getFarmasi = DB::table('bw_ruang_farmasi')
                ->select(
                    'bw_ruang_farmasi.kd_ruang_farmasi',
                    'bw_ruang_farmasi.nama_ruang_farmasi',
                    'bw_ruang_farmasi.kd_display_farmasi',
                    'bw_ruang_farmasi.posisi_display_farmasi'
                )
                ->where('bw_ruang_farmasi.kd_display_farmasi', $item->kd_display_farmasi)
                ->get();
        });
        return view('antrian-farmasi.antrian-farmasi1', [
            'antrianFarmasi1' => $antrianFarmasi1
        ]);
    }
    public function displayFarmasi()
    {
        $getSetting = $this->cacheService->getSetting();
        return view('antrian-farmasi.displayFarmasi', [
            'getSetting' => $getSetting
        ]);
    }
    public function panggilfarmasi()
    {
        return view('antrian-farmasi.panggil-farmasi1');
    }

    //     public function PanggilFarmasi1()
    // {
    //     $antrian = DB::table('antrian')
    //         ->join('bw_display_farmasi', 'antrian.keterangan', '=', 'bw_display_farmasi.nama_display_farmasi')
    //         ->whereDate('antrian.tanggal', now()->toDateString())
    //         ->select(
    //             'antrian.nomor_antrian',
    //             'antrian.rekam_medik',
    //             'antrian.nama_pasien',
    //             'antrian.keterangan',
    //             'bw_display_farmasi.nama_display_farmasi',
    //             'bw_display_farmasi.kd_display_farmasi'
    //         )
    //         ->get(); // Tambahkan get() di sini

    //     dd($antrian);

    //     // return  view("antrian-farmasi.antrian-farmasi1", [
    //     //             'results' => $antrian
    //     //             // 'penjab' => $penjab,
    //     //         ]);




    // }



    public function settingFarmasi()
    {
        return view('antrian-Farmasi.setting-Farmasi');
    }

    public function downloadAutorunfarmasi(Request $request)
    {
        $kdDisplayFarmasi = $request->kd_display_farmasi;
        $url = url('/displayfarmasi?kd_display_farmasi=' . $kdDisplayFarmasi);
        $fileName = 'autorun-display-farmasi-' . $kdDisplayFarmasi . '.bat';
        $content = <<<BAT
            @echo off
            set URL={$url}
            REM Jalankan Microsoft Edge dengan URL dalam mode fullscreen
            start msedge --start-fullscreen %URL%
            REM Tutup script ini setelah selesai
            exit
            BAT;
        $filePath = storage_path($fileName);
        File::put($filePath, $content);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    
}
