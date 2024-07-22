<?php

namespace App\Http\Controllers\BriggingBpjs;

use Illuminate\Http\Request;
use App\Services\Bpjs\Referensi;
use App\Http\Controllers\Controller;

class KirimTaskId extends Controller
{

    function KirimTaskId() {
        return view('briging-bpjs.kirim-taskid');
    }

    function KirimTaskId2() {
        return view('briging-bpjs.kirim-taskid2');
    }

    function CariSepVclaim() {
        return view('briging-bpjs.cari-sep-vclaim');
    }
    function UpdateJadwalHfis() {
        return view('briging-bpjs.update-jadwal-dokter-hfis');
    }
    function Icare() {
        return view('briging-bpjs.icare');
    }

}
