<?php

namespace App\Http\Controllers\Bpjs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class HomeCasemix extends Controller
{
    function crosCheckCoding() {
        return view('bpjs.croscheck-coding');
    }
    function casemixHome(){
        // $getPasien = DB::table('reg_periksa')
            //     ->select('reg_periksa.no_rawat', 'reg_periksa.no_rkm_medis', 'reg_periksa.tgl_registrasi',
            //             'pasien.nm_pasien', 'bridging_sep.no_sep')
            //     ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
            //     ->join('bridging_sep','bridging_sep.no_rawat','=','reg_periksa.no_rawat')
            //     ->where('reg_periksa.tgl_registrasi', '=', date('y-m-d'))
            //     ->orderBy('reg_periksa.no_rkm_medis', 'asc')
            //     ->get();
            //     $cekBerkas = [];
            //     foreach ($getPasien as $item) {
            //         $berkas = DB::connection('db_con2')
            //             ->table('file_casemix')
            //             ->select('no_rawat')
            //             ->where('no_rawat', $item->no_rawat)
            //             ->where('jenis_berkas', 'INACBG')
            //             ->first();
            //         $cekBerkas[$item->no_rawat] = $berkas;
            //     }

        $getPasien = '';
        return view('bpjs.homecasemix', [
            'getPasien'=>$getPasien,
        ]);
    }

    function casemixHomeCari(Request $request){
        $getPasien = '';
        // $norawat = $request->cariNorawat;
        // $getPasien = DB::table('reg_periksa')
        //     ->select('reg_periksa.no_rawat', 'reg_periksa.no_rkm_medis', 'reg_periksa.tgl_registrasi', 'pasien.nm_pasien',
        //             'pasien.jk', 'bridging_sep.no_sep', 'bridging_sep.jnspelayanan')
        //     ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
        //     ->leftJoin('bridging_sep','bridging_sep.no_rawat','=','reg_periksa.no_rawat')
        //     ->where('reg_periksa.no_rawat', '=', $norawat)
        //     ->orWhere('reg_periksa.no_rkm_medis', '=', $norawat)
        //     ->orWhere('bridging_sep.no_sep', '=', $norawat)
        //     ->orderBy('reg_periksa.tgl_registrasi', 'desc')
        //     ->get();
            // $cekBerkas = [];
            // foreach ($getPasien as $item) {
            //     $berkas = DB::connection('db_con2')
            //         ->table('file_casemix')
            //         ->select('no_rawat')
            //         ->where('no_rawat', $item->no_rawat)
            //         ->first();
            //     $cekBerkas[$item->no_rawat] = $berkas;
            // }
            // dd($cekBerkas);
            // $cekBerkasKhanza = [];
            // foreach ($getPasien as $item) {
            //     $berkas = DB::connection('db_con2')
            //         ->table('file_casemix')
            //         ->select('no_rawat')
            //         ->where('no_rawat', $item->no_rawat)
            //         ->where('jenis_berkas', 'RESUMEDLL')
            //         ->first();
            //     $cekBerkasKhanza[$item->no_rawat] = $berkas;
            // }
            // $cekBerkasHasil = [];
            // foreach ($getPasien as $item) {
            //     $berkas = DB::connection('db_con2')
            //         ->table('file_casemix')
            //         ->select('no_rawat')
            //         ->where('no_rawat', $item->no_rawat)
            //         ->where('jenis_berkas', 'HASIL')
            //         ->first();
            //     $cekBerkasHasil[$item->no_rawat] = $berkas;
            // }
            // foreach ($getPasien as $item) {
            //     $noRawatPasien = $item->no_rawat;
            //     $downloadFile = DB::table('file_casemix')
            //     ->select('file')
            //     ->where('no_rawat', $noRawatPasien)
            //     ->where('jenis_berkas', 'HASIL')
            //     ->first();
            // }

        return view('bpjs.homecasemix', [
            'getPasien'=>$getPasien,
            // 'cekBerkas' => $cekBerkas,
            // 'cekBerkasKhanza' => $cekBerkasKhanza,
            // 'cekBerkasHasil' => $cekBerkasHasil,
            // 'downloadFile'=>$downloadFile,
        ]);
    }
}
