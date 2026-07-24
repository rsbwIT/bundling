<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LogBridgingBpjs;
use Carbon\Carbon;

class MonitoringBridgingController extends Controller
{
    public function index(Request $request)
    {
        $tanggalMulai = $request->input('tgl_mulai', Carbon::now()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tgl_akhir', Carbon::now()->format('Y-m-d'));
        $layanan = $request->input('layanan', '');
        $status = $request->input('status', '');

        $query = LogBridgingBpjs::query()
            ->whereDate('waktu_request', '>=', $tanggalMulai)
            ->whereDate('waktu_request', '<=', $tanggalAkhir);

        if ($layanan) {
            $query->where('layanan', 'LIKE', '%' . $layanan . '%');
        }

        if ($status === 'success') {
            $query->where('status_code', 200);
        } elseif ($status === 'failed') {
            $query->where('status_code', '!=', 200);
        }

        $logs = $query->orderBy('waktu_request', 'desc')->paginate(50);

        return view('bpjs.monitoring_bridging', compact('logs', 'tanggalMulai', 'tanggalAkhir', 'layanan', 'status'));
    }
}
