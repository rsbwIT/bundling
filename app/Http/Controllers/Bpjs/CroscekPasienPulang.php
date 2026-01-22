<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CroscekPasienPulang extends Controller
{
    public function index(Request $request)
    {
        $tanggalDari   = $request->get('tanggal_dari', date('Y-m-d'));
        $tanggalSampai = $request->get('tanggal_sampai', date('Y-m-d'));

        $data = DB::table('kamar_inap')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                DB::raw("CONCAT(kamar.kd_kamar, '-', bangsal.nm_bangsal) AS bangsal_kamar"),
                'kamar_inap.tgl_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.lama',
                'kamar_inap.stts_pulang'
            )
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->where('reg_periksa.status_lanjut', 'ranap')
            ->whereBetween('kamar_inap.tgl_keluar', [$tanggalDari, $tanggalSampai])
            ->orderBy('kamar_inap.tgl_keluar')
            ->get();

        return view('bpjs.croscekpasienpulang', compact(
            'data',
            'tanggalDari',
            'tanggalSampai'
        ));
    }
}
