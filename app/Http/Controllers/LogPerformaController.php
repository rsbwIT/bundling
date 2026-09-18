<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogPerforma;

class LogPerformaController extends Controller
{
    public function index()
    {
        // Ambil top 100 log terlambat dalam 7 hari terakhir
        $logs = LogPerforma::where('waktu_akses', '>=', now()->subDays(7))
            ->orderBy('waktu_loading_detik', 'desc')
            ->limit(100)
            ->get();

        return view('monitoring.performa', compact('logs'));
    }
}
