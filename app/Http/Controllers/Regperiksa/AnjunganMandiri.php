<?php

namespace App\Http\Controllers\Regperiksa;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class AnjunganMandiri extends Controller
{
    public  function Anjungan()
    {
        return view('regperiksa.anjungan-mandiri');
    }

    public function Print($noRawat)
    {
        $setting = new CacheService();
        $pasien = DB::connection('db_con2')->table('reg_periksa')
            ->select(
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.no_reg',
                'pasien.nm_pasien',
                'pasien.no_rkm_medis',
                'pasien.alamat',
                'pasien.umur',
                'poliklinik.nm_poli',
                'dokter.nm_dokter'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('reg_periksa.no_rawat', '=', Crypt::decryptString($noRawat))
            ->first();
        return view('regperiksa.anjungan-mandiri-print', [
            'pasien' => $pasien,
            'setting' => $setting->getSetting(),
        ]);
    }
}
