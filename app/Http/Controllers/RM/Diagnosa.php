<?php

namespace App\Http\Controllers\RM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class Diagnosa extends Controller
{
    public function index(Request $request)
    {
        // Default hari ini jika kosong
        $tgl_awal  = $request->tgl_awal  ?? Carbon::today()->toDateString();
        $tgl_akhir = $request->tgl_akhir ?? Carbon::today()->toDateString();
        $keyword   = $request->keyword;

        // ================= QUERY DASAR =================
        $query = DB::table('reg_periksa as rp')
            ->join('pasien as ps', 'rp.no_rkm_medis', '=', 'ps.no_rkm_medis')
            ->join('diagnosa_pasien as dp', 'rp.no_rawat', '=', 'dp.no_rawat')
            ->join('penyakit as p', 'dp.kd_penyakit', '=', 'p.kd_penyakit')
            ->select(
                'rp.no_rawat',
                'rp.tgl_registrasi',
                'ps.nm_pasien',
                'rp.umurdaftar',
                'rp.sttsumur',
                'ps.jk',
                'dp.kd_penyakit',
                'p.nm_penyakit',
                'rp.status_lanjut'
            )
            ->whereBetween('rp.tgl_registrasi', [$tgl_awal, $tgl_akhir])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('dp.kd_penyakit', 'like', "%$keyword%")
                      ->orWhere('p.nm_penyakit', 'like', "%$keyword%");
                });
            });

        // ================= PAGINATION =================
        $data = (clone $query)
            ->orderBy('rp.tgl_registrasi', 'desc')
            ->paginate(50)
            ->appends($request->query());

        // ================= SUMMARY =================
        $summary = (clone $query)
            ->selectRaw("
                SUM(CASE WHEN rp.status_lanjut = 'Ralan' THEN 1 ELSE 0 END) as ralan,
                SUM(CASE WHEN rp.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as ranap,
                SUM(CASE WHEN rp.status_lanjut = 'IGD' THEN 1 ELSE 0 END) as igd
            ")
            ->first();

        return view('rm.diagnosa', compact(
            'data',
            'summary',
            'tgl_awal',
            'tgl_akhir'
        ));
    }
}
