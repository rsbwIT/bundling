<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BridgingalatlatLis extends Controller
{
    function BridgingalatlatLis(Request $request)
    {
        return view('lab.bridgingalat-lis');
    }
}
