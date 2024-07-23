<?php

namespace App\Http\Controllers\RM;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Borlos extends Controller
{
    public  function Borlos()
    {
        $Ruangan = [
            [
                'kamar' => 'ANGGREK',
                'jumlahtempattidur' => 34,
            ],
            [
                'kamar' => 'NURI',
                'jumlahtempattidur' => 20,
            ],
            [
                'kamar' => 'ICU',
                'jumlahtempattidur' => 7,
            ],
        ];
        $year = 2024;
        $borResults = [];
        foreach ($Ruangan as $room) {
            $kamar = $room['kamar'];
            $jumlahtempattidur = $room['jumlahtempattidur'];

            for ($month = 1; $month <= 12; $month++) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
                $jumlahhariResult = DB::table('reg_periksa')
                    ->select(DB::raw('SUM(DATEDIFF(kamar_inap.tgl_keluar, kamar_inap.tgl_masuk)) as total_jumlah_hari'))
                    ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                    ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->whereBetween('reg_periksa.tgl_registrasi', [$startDate, $endDate])
                    ->where('bangsal.nm_bangsal', 'like', '%'.$kamar.'%')
                    ->first();
                $jumlahhari = (int) ($jumlahhariResult->total_jumlah_hari ?? 0);
                $periodehari = Carbon::create($year, $month, 1)->daysInMonth;

                $bor = ($jumlahhari / ($jumlahtempattidur * $periodehari)) * 100;

                $borResults[$kamar][$month] = [
                    'jumlahhari' => $jumlahhari,
                    'jumlahtempattidur' => $jumlahtempattidur,
                    'periodehari' => $periodehari,
                    'bor' => $bor
                ];
            }
        }
        dd($borResults);
    }
}
