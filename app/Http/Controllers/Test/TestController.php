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
    public function dashboardTanggal() {
        $data = [
            'param' => "0000033695008",
            'kodedokter' => 415168,
        ];
        $data = $this->referensi->validateICARE(json_encode($data));
        dd(json_encode($data));
    }



}
