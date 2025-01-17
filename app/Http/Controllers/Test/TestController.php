<?php

namespace App\Http\Controllers\Test;

use setasign\Fpdi\Fpdi;
use Spatie\PdfToImage\Pdf;
use Illuminate\Http\Request;
use App\Services\TestService;
use App\Services\Bpjs\Referensi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Bpjs\ReferensiBPJS;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    protected $referensi;
    public function __construct()
    {
        $this->referensi = new ReferensiBPJS;
    }
    public function TestDelete()
    {
        // $data = [
        //     "kodekelas" => "ICU",
        //     "koderuang" => "INTSNV"
        // ];
        // dd(json_decode($this->referensi->deleteRuang(json_encode($data))));
        // dd(json_decode($this->referensi->getRuangan()));
        // dd(json_decode($this->referensi->getFasilitasKesehatan('Bumi', '2')));
    }
    public function Test()
    {
        dd(json_decode($this->referensi->getRuangan()));
        // $data = [
        //         'kodekelas' =>  'VVP',
        //         'koderuang' =>  'RG2',
        //         'namaruang' => 'R. GARUDA 2',
        //         'kapasitas' => '5',
        //         'tersedia' => '3',
        //         'tersediapria' => '5',
        //         'tersediawanita' => '5',
        //         'tersediapriawanita' => '5',
        //     ];
        // $data = json_decode($this->referensi->updateRuangan(json_encode($data)));
        // dd($data);

        // DB::table('bw_display_bad')
        //     ->select(
        //         'bw_display_bad.ruangan',
        //         'bw_display_bad.kd_kelas_bpjs',
        //         DB::raw('COUNT(bw_display_bad.status) AS kapasitas'),
        //         DB::raw('COUNT(CASE WHEN bw_display_bad.status = 0 THEN 0 END) AS tersedia'),
        //         DB::raw('COUNT(CASE WHEN bw_display_bad.status = 0 THEN 0 END) AS tersedia_wanita'),
        //         DB::raw('COUNT(CASE WHEN bw_display_bad.status = 0 THEN 0 END) AS tersedia_pria_wanita')
        //     )
        //     ->where('bw_display_bad.kd_kelas_bpjs', $item->kd_kelas_bpjs)
        //     ->where('bw_display_bad.ruangan', $item->ruangan)
        //     ->groupBy('bw_display_bad.kd_kelas_bpjs')
        //     ->get();
        // return view('test.test');
    }
}
