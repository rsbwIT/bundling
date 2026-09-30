<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogPerforma;

class LogPerformaController extends Controller
{
    public function index(Request $request)
    {
        $tgl_awal = $request->input('tgl_awal', date('Y-m-d'));
        $tgl_akhir = $request->input('tgl_akhir', date('Y-m-d'));

        // Ambil semua data log berdasarkan rentang tanggal, urutkan dari yang paling baru
        $logs = LogPerforma::whereDate('waktu_akses', '>=', $tgl_awal)
            ->whereDate('waktu_akses', '<=', $tgl_akhir)
            ->orderBy('waktu_akses', 'desc')
            ->paginate(50);

        return view('monitoring.performa', compact('logs', 'tgl_awal', 'tgl_akhir'));
    }
}
