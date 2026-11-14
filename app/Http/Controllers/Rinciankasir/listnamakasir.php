<?php

namespace App\Http\Controllers\Rinciankasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class listnamakasir extends Controller
{
    public function index(Request $request)
    {
        $tgl1 = $request->input('tgl1', date('Y-m-d'));
        $tgl2 = $request->input('tgl2', date('Y-m-d'));
        $statusLanjut = $request->input('status_lanjut', '');
        $jenisBayar = $request->input('jenis_bayar', '');

        $query = DB::table('reg_periksa as rp')
            ->select(
                'rp.no_rawat',
                'rp.tgl_registrasi',
                'p.nm_pasien',
                'pl.nm_poli',
                'ki.tgl_masuk',
                'rp.status_lanjut',
                'rp.status_bayar',
                'pj.png_jawab'
            )
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->join('poliklinik as pl', 'rp.kd_poli', '=', 'pl.kd_poli')
            ->leftJoin('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            // Menggunakan subquery untuk menghindari duplikasi baris akibat perpindahan kamar (Ranap)
            ->leftJoin(DB::raw('(SELECT no_rawat, MIN(tgl_masuk) as tgl_masuk FROM kamar_inap GROUP BY no_rawat) as ki'), 'rp.no_rawat', '=', 'ki.no_rawat')
            ->whereBetween('rp.tgl_registrasi', [$tgl1, $tgl2]);

        if ($statusLanjut) {
            $query->where('rp.status_lanjut', $statusLanjut);
        }

        if (!empty($jenisBayar)) {
            if ($jenisBayar === 'BPJS') {
                $query->where(function($q) {
                    $q->where('pj.png_jawab', 'like', '%BPJS%')
                      ->orWhere('pj.png_jawab', 'like', '%COB%')
                      ->orWhereIn('rp.kd_pj', ['BPJ', 'IAK', 'CKI']);
                });
            } elseif ($jenisBayar === 'Umum') {
                $query->where(function($q) {
                    $q->where('pj.png_jawab', 'like', '%UMUM%')
                      ->orWhere('rp.kd_pj', '-')
                      ->orWhere('rp.kd_pj', 'UMU');
                });
            } elseif ($jenisBayar === 'Asuransi') {
                $query->where('pj.png_jawab', 'not like', '%BPJS%')
                      ->where('pj.png_jawab', 'not like', '%COB%')
                      ->where('pj.png_jawab', 'not like', '%UMUM%')
                      ->whereNotIn('rp.kd_pj', ['-', 'UMU', 'BPJ', 'IAK', 'CKI']);
            }
        }

        $pasien = $query->orderBy('rp.no_rawat')->get();

        return view('rinciankasir.listnamakasir', compact('pasien', 'tgl1', 'tgl2', 'statusLanjut', 'jenisBayar'));
    }
}
