<?php

namespace App\Http\Controllers\PasienKamarInap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KamarInapDr extends Controller
{
    public function index()
    {
        $data = DB::select("
            SELECT
                reg_periksa.no_rawat,
                reg_periksa.no_rkm_medis,
                pasien.nm_pasien,
                penjab.png_jawab,
                kamar_inap.tgl_masuk,
                kamar_inap.jam_masuk,
                kamar_inap.diagnosa_awal,
                kamar_inap.diagnosa_akhir,
                CONCAT(kamar_inap.kd_kamar, ' - ', bangsal.nm_bangsal) AS kamar_bangsal,
                dokter.nm_dokter
            FROM reg_periksa
            INNER JOIN dpjp_ranap
                ON reg_periksa.no_rawat = dpjp_ranap.no_rawat
            INNER JOIN kamar_inap
                ON reg_periksa.no_rawat = kamar_inap.no_rawat
            INNER JOIN dokter
                ON dpjp_ranap.kd_dokter = dokter.kd_dokter
            INNER JOIN pasien
                ON reg_periksa.no_rkm_medis = pasien.no_rkm_medis
            INNER JOIN penjab
                ON reg_periksa.kd_pj = penjab.kd_pj
            INNER JOIN kamar
                ON kamar_inap.kd_kamar = kamar.kd_kamar
            INNER JOIN bangsal
                ON bangsal.kd_bangsal = kamar.kd_bangsal
            WHERE kamar_inap.stts_pulang = '-'
        ");

        return view('pasienkamarinap.kamarinapdr', compact('data'));
    }
}

