<?php

namespace App\Http\Controllers\Regperiksa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Listpasien extends Controller
{
    public function Listpasien() {
        return view('regperiksa.list-pasien');
    }
}
