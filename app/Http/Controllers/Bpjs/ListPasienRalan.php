<?php

namespace App\Http\Controllers\Bpjs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ListPasienRalan extends Controller
{
    function lisPaseinRalan(Request $request){
        $tanggl1 = date('Y-m-d', strtotime('-1 day'));
        $tanggl2 = date('Y-m-d', strtotime('-1 day'));
        $penjamnin = 'BPJ';

        $daftarPasien = DB::table('reg_periksa')
            ->select('reg_periksa.no_rkm_medis', 'reg_periksa.no_rawat', 'reg_periksa.status_bayar', 'bridging_sep.no_sep', 'pasien.nm_pasien', 'bridging_sep.tglsep',
            'poliklinik.nm_poli', 'bw_file_casemix_hasil.file'
            )
            ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
            ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
            ->leftJoin('bridging_sep','bridging_sep.no_rawat','=','reg_periksa.no_rawat')
            ->leftJoin('bw_file_casemix_hasil','bw_file_casemix_hasil.no_rawat','=','reg_periksa.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi',[$tanggl1, $tanggl2])
            ->where('reg_periksa.status_lanjut','=','Ralan')
            ->where('reg_periksa.kd_pj','=', $penjamnin)
            ->get();

        session(['tgl1' => $tanggl1]);
        session(['tgl2' => $tanggl2]);
        // session(['page' => $request->page]);
        session(['statusLanjut' => 'Ralan']);

        return view('bpjs.listpasien-ralan', [
            'daftarPasien'=>$daftarPasien,
            'penjamnin'=>$penjamnin,
            'tanggl1'=>$tanggl1,
            'tanggl2'=>$tanggl2,
        ]);
    }

    function cariListPaseinRalan(Request $request){
        $tanggl1 = $request->tgl1 ?: date('Y-m-d');
        $tanggl2 = $request->tgl2 ?: date('Y-m-d');
        $penjamnin = 'BPJ';

        $daftarPasien = DB::table('reg_periksa')
            ->select('reg_periksa.no_rkm_medis', 'reg_periksa.no_rawat', 'reg_periksa.status_bayar', 'bridging_sep.no_sep', 'pasien.nm_pasien', 'bridging_sep.tglsep',
            'poliklinik.nm_poli', 'bw_file_casemix_hasil.file')
            ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
            ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
            ->leftJoin('bridging_sep','bridging_sep.no_rawat','=','reg_periksa.no_rawat')
            ->leftJoin('bw_file_casemix_hasil','bw_file_casemix_hasil.no_rawat','=','reg_periksa.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi',[$tanggl1, $tanggl2])
            ->where('reg_periksa.status_lanjut','=','Ralan')
            ->where('reg_periksa.kd_pj','=', $penjamnin)
            ->get();

        //  // GET ALL BERKAS
        //  $daftarPasien->map(function ($item) {
        //     $item->getAllBerkas = DB::connection('db_con2')
        //         ->table('file_casemix')
        //         ->select('file')
        //         ->where('no_rawat', $item->no_rawat)
        //         ->where('jenis_berkas', 'HASIL')
        //         ->get();
        // });

        session(['tgl1' => $request->tgl1]);
        session(['tgl2' => $request->tgl2]);
        // session(['page' => $request->page]);
        session(['statusLanjut' => 'Ralan']);

        return view('bpjs.listpasien-ralan', [
            'daftarPasien'=>$daftarPasien,
            'penjamnin'=>$penjamnin,
            'tanggl1'=>$tanggl1,
            'tanggl2'=>$tanggl2,
        ]);
    }

}
