<?php
// app/Services/CacheService.php

namespace App\Services;

class BulanRomawi
{
    public static function getBulanRomawi()
    {
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romanMonths[date('n')];
    }
    public static function BulanIndo($bulan) {
        $bulan_array = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        );
        return $bulan_array[$bulan];
    }
    public static function BulanIndo2($bulan) {
        $bulan_array = array(
            '01' => 'Jan',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'Mei',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Agu',
            '09' => 'Sep',
            '10' => 'Okt',
            '11' => 'Nov',
            '12' => 'Des'
        );
        return $bulan_array[$bulan];
    }

    public static function angkaToAbjad($bulan) {
        $bulan_array = array(
            '0'  => 'A',
            '1'  => 'B',
            '2'  => 'C',
            '3'  => 'D',
            '4'  => 'E',
            '5'  => 'F',
            '6'  => 'G',
            '7'  => 'H',
            '8'  => 'I',
            '9'  => 'J',
            '10' => 'K',
            '11' => 'L',
            '12' => 'M',
            '13' => 'N',
            '14' => 'O',
            '15' => 'P',
            '16' => 'Q',
            '17' => 'R',
            '18' => 'S',
            '19' => 'T',
            '20' => 'U',
            '21' => 'V',
            '22' => 'W',
            '23' => 'X',
            '24' => 'Y',
            '25' => 'Z'
        );
        return $bulan_array[$bulan] ?? null;
    }



}
