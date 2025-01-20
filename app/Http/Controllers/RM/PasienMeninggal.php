<?php

namespace App\Http\Controllers\RM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PasienMeninggal extends Controller
{
    public function getPasienMeninggal(Request $request)
    {

        $getPasien =  DB::table('pasien_mati')
            ->select(
                'pasien_mati.tanggal as tgl_meninggal',
                'pasien_mati.jam as jam_meninggal',
                'pasien.nm_pasien',
                'reg_periksa.status_lanjut',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                DB::raw("TIMESTAMPDIFF(HOUR, CONCAT(reg_periksa.tgl_registrasi, ' ', reg_periksa.jam_reg), CONCAT(pasien_mati.tanggal, ' ', pasien_mati.jam)) as selisih_jam")
            )
            ->join('reg_periksa', 'pasien_mati.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->whereBetween('reg_periksa.tgl_registrasi', ['2025-01-01', '2025-01-20'])
            // ->having('selisih_jam', '>', 0)
            ->get();
        return view('rm.pasien-meninggal', [
            'getPasien' => $getPasien,
        ]);
    }
}
