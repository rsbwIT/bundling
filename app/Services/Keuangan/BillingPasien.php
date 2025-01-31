<?php
// app/Services/CacheService.php

namespace App\Services\Keuangan;

use Illuminate\Support\Facades\DB;

class BillingPasien
{
    public static function getBillingPasien($no_rawat, $status_lanjut)
    {
        return DB::table('nota_inap')
        ->select('nota_inap.no_rawat', 'nota_inap.no_nota', 'nota_inap.tanggal', 'nota_inap.jam', 'nota_inap.Uang_Muka', 'reg_periksa.no_rawat', 'pasien.nm_pasien', 'reg_periksa.status_lanjut', 'penjab.png_jawab')
        ->join('reg_periksa','nota_inap.no_rawat','=','reg_periksa.no_rawat')
        ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
        ->join('penjab','reg_periksa.kd_pj','=','penjab.kd_pj')
        ->where('nota_inap.no_rawat','=', $no_rawat)
        ->where('reg_periksa.status_lanjut','=','Ranap')
        ->get();
    }
}
