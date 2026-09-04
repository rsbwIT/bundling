<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListPasienRanap2 extends Controller
{
    public function lisPaseinRanap2() {
         return view('bpjs.listpasien-ranap2');
    }
}
