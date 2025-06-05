<?php

namespace App\Http\Controllers\AntrianFarmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function index()
    {
        return view('display-farmasi');
    }
}
