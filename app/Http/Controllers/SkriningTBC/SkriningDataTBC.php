<?php

namespace App\Http\Controllers\SkriningTBC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkriningDataTBC extends Controller
{
    public function index(Request $request)
    {
        // FILTER TANGGAL
        if ($request->has(['tgl_dari', 'tgl_sampai'])) {
            $tgl_dari   = $request->tgl_dari;
            $tgl_sampai = $request->tgl_sampai;
        } else {
            $tgl_dari   = date('Y-m-d');
            $tgl_sampai = date('Y-m-d');
        }

        // DATA TABEL
        $data = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.status_lanjut',
                'pasien.nm_pasien',
                'poliklinik.nm_poli',
                DB::raw("
                    (SELECT bangsal.nm_bangsal 
                     FROM kamar_inap 
                     JOIN kamar ON kamar_inap.kd_kamar = kamar.kd_kamar 
                     JOIN bangsal ON kamar.kd_bangsal = bangsal.kd_bangsal 
                     WHERE kamar_inap.no_rawat = reg_periksa.no_rawat 
                     ORDER BY kamar_inap.tgl_masuk DESC, kamar_inap.jam_masuk DESC 
                     LIMIT 1) as nm_bangsal
                 "),
                DB::raw("
                    CASE 
                        WHEN skrining_tbc.no_rawat IS NOT NULL THEN 1
                        ELSE 0
                    END AS status_skrining_tbc
                ")
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('skrining_tbc', 'reg_periksa.no_rawat', '=', 'skrining_tbc.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tgl_dari, $tgl_sampai])
            ->orderBy('reg_periksa.tgl_registrasi')
            ->get();

        // ================= TOTAL =================
        $total_pasien = $data->count();

        $total_sudah = $data->where('status_skrining_tbc', 1)->count();

        $total_belum = $data->where('status_skrining_tbc', 0)->count();

        return view('skriningtbc.skriningdatatbc', compact(
            'data',
            'tgl_dari',
            'tgl_sampai',
            'total_pasien',
            'total_sudah',
            'total_belum'
        ));
    }
}
