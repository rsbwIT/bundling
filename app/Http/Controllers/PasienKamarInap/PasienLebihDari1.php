<?php

namespace App\Http\Controllers\PasienKamarInap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PasienLebihDari1 extends Controller
{
    /**
     * TAMPILAN UTAMA (GROUP BY)
     */
    public function index(Request $request)
    {
        // default tanggal
        $dari   = $request->dari   ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-t');

        // QUERY GROUP
        $data = DB::table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->select(
                'rp.no_rkm_medis',
                'p.nm_pasien',
                DB::raw("DATE_FORMAT(rp.tgl_registrasi, '%Y-%m') as bulan"),
                DB::raw("COUNT(*) as jumlah_kunjungan")
            )
            ->whereBetween('rp.tgl_registrasi', [$dari, $sampai])
            ->groupBy('rp.no_rkm_medis', 'p.nm_pasien', 'bulan')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('bulan')
            ->orderByDesc('jumlah_kunjungan')
            ->get();

        // SUMMARY
        $total_pasien = $data->groupBy('no_rkm_medis')->count();
        $total_kunjungan = $data->sum('jumlah_kunjungan');

        return view('pasienkamarinap.pasienlebihdari1', compact(
            'data',
            'dari',
            'sampai',
            'total_pasien',
            'total_kunjungan'
        ));
    }

    /**
     * DETAIL UNTUK MODAL (AJAX)
     */
    public function detail(Request $request)
    {
        $no_rm = $request->no_rkm_medis;
        $bulan = $request->bulan;

        $detail = DB::table('reg_periksa as rp')
            ->join('poliklinik as pl', 'rp.kd_poli', '=', 'pl.kd_poli')
            ->join('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->where('rp.no_rkm_medis', $no_rm)
            ->whereRaw("DATE_FORMAT(rp.tgl_registrasi, '%Y-%m') = ?", [$bulan])
            ->select(
                'rp.tgl_registrasi',
                'pl.nm_poli',
                'd.nm_dokter'
            )
            ->orderBy('rp.tgl_registrasi')
            ->get();

        return response()->json($detail);
    }
}