<?php

namespace App\Http\Controllers\Test;

use setasign\Fpdi\Fpdi;
use Spatie\PdfToImage\Pdf;
use Illuminate\Http\Request;
use App\Services\TestService;
use App\Services\Bpjs\Referensi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{

    function TestDelete(Request $request) {
        //   $data = [
            //     // ['kode_brng'=>'GU000003240', 'kd_bangsal' => 'DepRJ', 'stok_minimal_medis' =>750],
            //     // ['kode_brng'=>'B000007050', 'kd_bangsal' => 'DepRJ', 'stok_minimal_medis' =>720],
            // ];
            // foreach ($data as $row) {
            //     DB::table('stok_minimal_medis')->insert($row);
        // }
    }

    // function Test(){
        // $getPenyakit = DB::table('diagnosa_pasien')
        //     ->select('penyakit.nm_penyakit',
        //             'diagnosa_pasien.kd_penyakit',
        //             DB::raw('COUNT(diagnosa_pasien.kd_penyakit) AS Jumlah'
        //     ))
        //     ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
        //     ->join('reg_periksa', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        //     ->where('diagnosa_pasien.prioritas', '=', '1')
        //     ->where('reg_periksa.status_lanjut', '=', 'ranap')
        //     ->whereBetween('reg_periksa.tgl_registrasi',['2024-01-01','2024-02-06'])
        //     ->groupBy('diagnosa_pasien.kd_penyakit')
        //     ->orderBy('Jumlah', 'DESC')
        //     ->take(10)
        //     ->get();
        //     $getPenyakit->map(function ($item) {
        //         $item->getDokter = DB::table('reg_periksa')
        //         ->select('dokter.nm_dokter','reg_periksa.kd_dokter',  'penyakit.nm_penyakit', 'diagnosa_pasien.kd_penyakit', DB::raw('COUNT(reg_periksa.kd_dokter) AS Jumlah_yang_menangani_penyakit'))
        //         ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        //         ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
        //         ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
        //         ->where('diagnosa_pasien.kd_penyakit', $item->kd_penyakit)
        //         ->where('reg_periksa.status_lanjut', '=', 'ranap')
        //         ->where('diagnosa_pasien.prioritas', '=', '1')
        //         ->whereBetween('reg_periksa.tgl_registrasi',['2024-01-01','2024-02-06'])
        //         ->groupBy('reg_periksa.kd_dokter')
        //         ->orderBy('Jumlah_yang_menangani_penyakit', 'DESC')
        //         // ->take(5)
        //         ->get();
        //         return $item;
        //     });

        // return view('test.test', ['getPenyakit' => $getPenyakit]);
    // }

    public function dashboardTanggal() {
        $data = DB::table('')
        ->where('')
        ->delete();

        dd($data);
    }


}
