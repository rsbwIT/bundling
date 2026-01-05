<?php

namespace App\Http\Controllers\SkriningTBC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkriningTBC extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status; // ralan | ranap | null

        $query = DB::table('skrining_tbc')
            ->leftJoin('reg_periksa', 'skrining_tbc.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->select(
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'reg_periksa.status_lanjut',
                'penjab.png_jawab',
                'reg_periksa.no_rawat',
                'skrining_tbc.tanggal',
                'skrining_tbc.berat_badan',
                'skrining_tbc.tinggi_badan',
                'skrining_tbc.imt',
                'skrining_tbc.kasifikasi_imt',
                'skrining_tbc.lingkar_pinggang',
                'skrining_tbc.risiko_lingkar_pinggang',
                'skrining_tbc.riwayat_kontak_tbc',
                'skrining_tbc.jenis_kontak_tbc',
                'skrining_tbc.faktor_resiko_pernah_terdiagnosa_tbc',
                'skrining_tbc.keterangan_pernah_terdiagnosa',
                'skrining_tbc.faktor_resiko_pernah_berobat_tbc',
                'skrining_tbc.faktor_resiko_malnutrisi',
                'skrining_tbc.faktor_resiko_merokok',
                'skrining_tbc.faktor_resiko_riwayat_dm',
                'skrining_tbc.faktor_resiko_odhiv',
                'skrining_tbc.faktor_resiko_lansia',
                'skrining_tbc.faktor_resiko_ibu_hamil',
                'skrining_tbc.faktor_resiko_wbp',
                'skrining_tbc.faktor_resiko_tinggal_diwilayah_padat_kumuh',
                'skrining_tbc.abnormalitas_tbc',
                'skrining_tbc.gejala_tbc_batuk',
                'skrining_tbc.gejala_tbc_bb_turun',
                'skrining_tbc.gejala_tbc_demam',
                'skrining_tbc.gejala_tbc_berkeringat_malam_hari',
                'skrining_tbc.keterangan_gejala_penyakit_lain',
                'skrining_tbc.kesimpulan_skrining',
                'skrining_tbc.keterangan_hasil_skrining'
            );

        // 🔥 FILTER STATUS RALAN / RANAP
        if ($status === 'ralan') {
            $query->where('reg_periksa.status_lanjut', 'ralan');
        } elseif ($status === 'ranap') {
            $query->where('reg_periksa.status_lanjut', 'ranap');
        }

        $data = $query
            ->orderBy('skrining_tbc.tanggal', 'desc')
            ->get();

        return view('skriningtbc.skriningtbc', compact('data', 'status'));
    }
}
