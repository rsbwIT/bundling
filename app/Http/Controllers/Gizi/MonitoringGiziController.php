<?php

namespace App\Http\Controllers\Gizi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringGiziController extends Controller
{
    public function index()
    {
        return view('gizi.monitoring.index');
    }
}
