<?php

namespace App\Http\Controllers\RM;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\BulanRomawi;
use App\Services\Rm\QueryBorlos;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Borlos extends Controller
{
    public  function Borlosetc()
    {
        return view('rm.borlos');
    }

    public  function Toi()
    {

    }
}
