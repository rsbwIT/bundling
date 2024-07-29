<?php

namespace App\Http\Controllers\RM;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\BulanRomawi;
use App\Services\Rm\QueryBorlos;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Borlos extends Controller
{
    public  function Borlosetc()
    {
        return view('rm.borlos');
    }

    public  function Bto()
    {
        $year = 2024;
        $Ruangan = DB::table('bw_borlos')
            ->select('bw_borlos.ruangan', 'bw_borlos.jml_bed')
            ->get();
        $ndrResult = [];
        for ($month = 1; $month <= 12; $month++) {
            $total_pasien_mati_48jam[$month] = 0;
            $total_pasien_meninggal[$month] = 0;
            $total_pasien_keluar[$month] = 0;
            $total_jmlHari_Rawat[$month] = 0;
            $total_lama_Di_Rawat[$month] = 0;
        }
        foreach ($Ruangan as $room) {
            for ($month = 1; $month <= 12; $month++) {
                $kamar = $room->ruangan;
                $start_Date = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
                $end_Date = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
                $pasien_keluar = QueryBorlos::pasienKeluar($start_Date, $end_Date, $kamar);
                $pasien_mati_48jam = QueryBorlos::pasienMati48jam($start_Date, $end_Date, $kamar);
                $jml_Hari_Rawat = QueryBorlos::jmlHariRawat($start_Date, $end_Date, $kamar);
                $lama_Di_Rawat = QueryBorlos::lamaDiRawat($start_Date, $end_Date, $kamar);
                $pasien_meninggal = QueryBorlos::pasienMati($start_Date, $end_Date, $kamar);

                $ndrResult[$kamar][BulanRomawi::BulanIndo2(sprintf("%02d", $month))] = [
                    'pasien_keluar' => $pasien_keluar,
                    'pasien_mati_48jam' => $pasien_mati_48jam,
                    'pasien_meninggal' => $pasien_meninggal,
                    'jml_Hari_Rawat' => $jml_Hari_Rawat,
                    'lama_Di_Rawat' => $lama_Di_Rawat,
                ];

                $total_pasien_mati_48jam[$month] += $pasien_mati_48jam;
                $total_pasien_meninggal[$month] += $pasien_meninggal;
                $total_pasien_keluar[$month] += $pasien_keluar;
                $total_jmlHari_Rawat[$month] += $jml_Hari_Rawat;
                $total_lama_Di_Rawat[$month] += $lama_Di_Rawat;
            }
        }

        $TotalNdr = [];
        foreach ($total_pasien_mati_48jam as $month => $total_pasien_mati_48jam) {
            $jumlah_kunjungan_perbulan = DB::table('reg_periksa')
            ->select('reg_periksa.tgl_registrasi', 'reg_periksa.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi', [$start_Date,  $end_Date])
            ->count();
            $TotalNdr[BulanRomawi::BulanIndo2(sprintf("%02d", $month))] = [
                'total_pasien_keluar' => $total_pasien_keluar[$month],
                'total_pasien_mati_48jam' => $total_pasien_mati_48jam,
                'total_pasien_meninggal' => $total_pasien_meninggal[$month],
                'total_jmlHari_Rawat' => $total_jmlHari_Rawat[$month],
                'total_lama_Di_Rawat' => $total_lama_Di_Rawat[$month],
                'jumlah_kunjungan_perbulan' => $jumlah_kunjungan_perbulan,
            ];
        }
        $ndrResult['SEMUA RUANGAN'] = $TotalNdr;
        dd($ndrResult);
    }
}
