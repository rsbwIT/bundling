<?php

namespace App\Http\Controllers\RM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BerkasRM extends Controller
{
    function BerkasRM() {
        return view('rm.berkas-rm');
    }
    function WaktuTungguPasienBayar() {
        return view('rm.waktu-tunggu-pasienbayar');
    }
}
