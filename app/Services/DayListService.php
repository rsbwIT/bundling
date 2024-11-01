<?php
// app/Services/CacheService.php

namespace App\Services;
// DayListService.php

class DayListService
{
    public static function getDayList()
    {
        return array(
            'Sunday' => 'MINGGU',
            'Monday' => 'SENIN',
            'Tuesday' => 'SELASA',
            'Wednesday' => 'RABU',
            'Thursday' => 'KAMIS',
            'Friday' => 'JUMAT',
            'Saturday' => 'SABTU'
        );
    }
    public static function hariIndonesia($tanggal) {
        $namaHari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        $hariInggris = date('l', strtotime($tanggal));
        return $namaHari[$hariInggris];
    }

    public static function hariKhanza($tanggal) {
        $namaHari = [
            'Sunday' => 'MINGGU',
            'Monday' => 'SENIN',
            'Tuesday' => 'SELASA',
            'Wednesday' => 'RABU',
            'Thursday' => 'KAMIS',
            'Friday' => 'JUMAT',
            'Saturday' => 'SABTU'
        ];

        $hariInggris = date('l', strtotime($tanggal));
        return $namaHari[$hariInggris];
    }
    public static function hariKhanza2($tanggal) {
        $namaHari = [
            'Sunday' => 'AKHAD',
            'Monday' => 'SENIN',
            'Tuesday' => 'SELASA',
            'Wednesday' => 'RABU',
            'Thursday' => 'KAMIS',
            'Friday' => 'JUMAT',
            'Saturday' => 'SABTU'
        ];

        $hariInggris = date('l', strtotime($tanggal));
        return $namaHari[$hariInggris];
    }
}
