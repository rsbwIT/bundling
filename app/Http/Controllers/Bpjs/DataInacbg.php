<?php

namespace App\Http\Controllers\Bpjs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DataInacbg extends Controller
{
    function Inacbg(Request $request){
        $cariNomor = $request->cariNomor;
        $noRawat = $cariNomor;
        if (strlen($cariNomor) > 15) {
            $sep = DB::table('bridging_sep')->where('no_sep', $cariNomor)->first();
            if ($sep) {
                $noRawat = $sep->no_rawat;
            }
        }

        $dataInacbg = DB::table('reg_periksa')
        ->select('reg_periksa.no_rawat',
            'bridging_sep.no_sep',
            'pasien.no_rkm_medis',
            'pasien.nm_pasien',
            'reg_periksa.status_lanjut',
            'resume_pasien.keluhan_utama',
            'resume_pasien.jalannya_penyakit',
            'resume_pasien.pemeriksaan_penunjang',
            'resume_pasien.hasil_laborat',
            'resume_pasien.kd_diagnosa_utama',
            'resume_pasien.diagnosa_utama',
            'resume_pasien.kd_diagnosa_sekunder',
            'resume_pasien.diagnosa_sekunder',
            'resume_pasien.kd_diagnosa_sekunder2',
            'resume_pasien.diagnosa_sekunder2',
            'resume_pasien.kd_diagnosa_sekunder3',
            'resume_pasien.diagnosa_sekunder3',
            'resume_pasien.kd_diagnosa_sekunder4',
            'resume_pasien.diagnosa_sekunder4',
            'resume_pasien.kd_prosedur_utama',
            'resume_pasien.prosedur_utama',
            'resume_pasien.kd_prosedur_sekunder',
            'resume_pasien.prosedur_sekunder',
            'resume_pasien.kd_prosedur_sekunder2',
            'resume_pasien.prosedur_sekunder2',
            'resume_pasien.kd_prosedur_sekunder3',
            'resume_pasien.prosedur_sekunder3',
            'piutang_pasien.totalpiutang')
        ->join('pasien','pasien.no_rkm_medis','=','reg_periksa.no_rkm_medis')
        ->join('resume_pasien','resume_pasien.no_rawat','=','reg_periksa.no_rawat')
        ->join('bridging_sep','bridging_sep.no_rawat','=','reg_periksa.no_rawat')
        ->join('piutang_pasien','piutang_pasien.no_rawat','=','reg_periksa.no_rawat')
        ->where('reg_periksa.no_rawat','=', $noRawat)
        ->get();

        return view('bpjs.datainacbg', [
            'dataInacbg'=>$dataInacbg,
        ]);
    }
}
