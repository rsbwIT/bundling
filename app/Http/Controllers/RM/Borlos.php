<?php

namespace App\Http\Controllers\RM;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Borlos extends Controller
{
    public  function Bor()
    {
        return view('rm.borlos');
    }
    public  function Los()
    {
        $Ruangan = DB::table('bw_borlos')
            ->select('bw_borlos.ruangan')
            // ->where('bw_borlos.ruangan', 'ANGGREK')
            ->get();
        $year = 2024;
        $losResult = [];
        for ($month = 1; $month <= 12; $month++) {
            $total_lama_dirawat[$month] = 0;
            $total_pasien_keluar[$month] = 0;
        }
        foreach ($Ruangan as $room) {
            $kamar = $room->ruangan;
            for ($month = 1; $month <= 12; $month++) {
                $start_Date = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
                $end_Date = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
                $lama_dirawat = DB::table(function ($query) use ($start_Date,  $end_Date, $kamar) {
                    $query->select(DB::raw('SUM(kamar_inap.lama) AS total_days_hospitalized'))
                        ->from('reg_periksa')
                        ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                        ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                        ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                        ->whereBetween('reg_periksa.tgl_registrasi', [$start_Date,  $end_Date])
                        ->where('bangsal.nm_bangsal', 'like', '%' . $kamar . '%')
                        ->groupBy('bangsal.nm_bangsal');
                }, 'subquery')
                    ->sum('total_days_hospitalized');
                $pasien_keluar = DB::table(function ($query) use ($start_Date,  $end_Date, $kamar) {
                    $query->select(DB::raw('COUNT(kamar_inap.no_rawat) AS pasien_keluar'))
                        ->from('reg_periksa')
                        ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                        ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                        ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                        ->whereBetween('reg_periksa.tgl_registrasi', [$start_Date,  $end_Date])
                        ->where('bangsal.nm_bangsal', 'like', '%' . $kamar . '%')
                        ->groupBy('bangsal.nm_bangsal');
                }, 'subquery')
                    ->sum('pasien_keluar');
                $a = (int) ($lama_dirawat ?? 0);
                $b = (int) ($pasien_keluar ?? 0);
                $los = $pasien_keluar > 0 ? $a / $b : 0;

                $losResult[$kamar][$month] = [
                    'lama_dirawat' => $lama_dirawat,
                    'pasien_keluar' => $pasien_keluar,
                    'los' => $los
                ];
                $total_lama_dirawat[$month] += $a;
                $total_pasien_keluar[$month] += $b;
            }
        }
        $TotalBor = [];
        foreach ($total_lama_dirawat as $month => $total_lamarawat) {
            $totalPasienKeluar = $total_pasien_keluar[$month];
            $hitung_total_los = $totalPasienKeluar > 0 ? $total_lamarawat / $totalPasienKeluar : 0;
            $TotalBor[$month] = [
                'lama_dirawat' => $total_lamarawat,
                'pasien_keluar' => $totalPasienKeluar,
                'los' => $hitung_total_los
            ];
        }
        $losResult['SEMUA RUANGAN'] = $TotalBor;
        dd($losResult);
    }
}
