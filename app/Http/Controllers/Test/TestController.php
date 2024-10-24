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
    public function TestCari()
    {
        dd(json_decode($this->referensi->getReferensiKelas()));
        // dd(json_decode($this->referensi->getFasilitasKesehatan('Bumi', '2')));
    }
    public function Test()
    {
        // dd(json_decode($this->referensi->getReferensiKelas()));
        $data = [
            'kodekelas' =>  'T',
            'koderuang' =>  'T',
            'namaruang' => 'T',
            'kapasitas' => '0',
            'tersedia' => '0',
            'tersediapria' => '0',
            'tersediawanita' => '0',
            'tersediapriawanita' => '0',
        ];
        $data = json_decode($this->referensi->addRuangan(json_encode($data)));
        dd($data);
    }
}
