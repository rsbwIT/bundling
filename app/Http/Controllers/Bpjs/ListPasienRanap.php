<?php

namespace App\Http\Controllers\Bpjs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ListPasienRanap extends Controller
{
    function lisPaseinRanap(Request $request){
        $tanggl1 = date('Y-m-d');
        $tanggl2 = date('Y-m-d');
        $penjamnin = 'BPJ';

        $daftarPasien = DB::table('reg_periksa')
            ->select('reg_periksa.no_rkm_medis', 'reg_periksa.no_rawat', 'reg_periksa.status_bayar', 'bridging_sep.no_sep', 'pasien.nm_pasien', 'bridging_sep.tglsep')
            ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
            ->join('bridging_sep','bridging_sep.no_rawat','=','reg_periksa.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi',[$tanggl1, $tanggl2])
            ->where('reg_periksa.status_lanjut','=','Ranap')
            ->where('reg_periksa.kd_pj','=', $penjamnin)
            ->get();
            $downloadBerkas = DB::connection('db_con2')
                ->table('file_casemix')
                ->select('no_rawat', 'file')
                ->whereIn('no_rawat', $daftarPasien->pluck('no_rawat')->toArray())
                ->where('jenis_berkas', 'HASIL')
                ->get();

        session(['tgl1' => $tanggl1]);
        session(['tgl2' => $tanggl2]);
        session(['statusLanjut' => 'Ranap']);

        return view('bpjs.listpasien-ranap', [
            'daftarPasien'=>$daftarPasien,
            'downloadBerkas'=>$downloadBerkas,
            'penjamnin'=>$penjamnin,
            'tanggl1'=>$tanggl1,
            'tanggl2'=>$tanggl2,
        ]);
    }

    function cariListPaseinRanap(Request $request){
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $penjamnin = 'BPJ';

        $daftarPasien = DB::table('reg_periksa')
            ->select('reg_periksa.no_rkm_medis', 'reg_periksa.no_rawat', 'reg_periksa.status_bayar', 'bridging_sep.no_sep', 'pasien.nm_pasien', 'bridging_sep.tglsep')
            ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
            ->join('bridging_sep','bridging_sep.no_rawat','=','reg_periksa.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi',[$tanggl1, $tanggl2])
            ->where('reg_periksa.status_lanjut','=','Ranap')
            ->where('reg_periksa.kd_pj','=', $penjamnin)
            ->get();
            $downloadBerkas = DB::connection('db_con2')
                ->table('file_casemix')
                ->select('no_rawat', 'file')
                ->whereIn('no_rawat', $daftarPasien->pluck('no_rawat')->toArray())
                ->where('jenis_berkas', 'HASIL')
                ->get();

        session(['tgl1' => $request->tgl1]);
        session(['tgl2' => $request->tgl2]);
        session(['statusLanjut' => 'Ranap']);

        return view('bpjs.listpasien-ranap', [
            'daftarPasien'=>$daftarPasien,
            'downloadBerkas'=>$downloadBerkas,
            'penjamnin'=>$penjamnin,
            'tanggl1'=>$tanggl1,
            'tanggl2'=>$tanggl2,
        ]);
    }
}
