<?php

namespace App\Http\Controllers\Bpjs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class HomeCasemix extends Controller
{
    function crosCheckCoding() {
        return view('bpjs.croscheck-coding');
    }
    function casemixHome(){

        $getPasien = '';
        return view('bpjs.homecasemix', [
            'getPasien'=>$getPasien,
        ]);
    }

    function casemixHomeCari(Request $request){
        $getPasien = '';

        return view('bpjs.homecasemix', [
            'getPasien'=>$getPasien,
        ]);
    }
}
