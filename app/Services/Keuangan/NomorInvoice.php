<?php
// app/Services/CacheService.php

namespace App\Services\Keuangan;

use App\Services\BulanRomawi;
use Illuminate\Support\Facades\DB;

class NomorInvoice
{
    public static function getAutonumberInvoice($kode_asurasni, $status_lanjut)
    {
        try {
            $kodeSts = $status_lanjut == "Ranap" ? "KEURI" : "KEURJ";
            $year = date('Y');
            $getNumber = DB::table('bw_invoice_asuransi')
                ->select('nomor_tagihan')
                ->where('kode_asuransi', $kode_asurasni)
                ->where('status_lanjut', $status_lanjut)
                ->where(DB::raw('SUBSTRING_INDEX(SUBSTRING_INDEX(nomor_tagihan, "/", -1), "/", 1)'), $year)
                ->orderBy('nomor_tagihan', 'desc')
                ->first();
            if ($getNumber) {
                $newNumber = intval($getNumber->nomor_tagihan) + 1;
            } else {
                $newNumber = 1;
            }
            $newNumberFormatted = str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            $month = BulanRomawi::getBulanRomawi();

            $finalInvoiceNumber = "{$newNumberFormatted}/{$kode_asurasni}/{$kodeSts}/RSBW/{$month}/{$year}";

            return $finalInvoiceNumber;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public static function Terbilang($x)
    {
        $abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        if ($x < 12) {
            return " " . $abil[$x];
        } elseif ($x < 20) {
            return self::Terbilang($x - 10) . " belas";
        } elseif ($x < 100) {
            return self::Terbilang(intdiv($x, 10)) . " puluh" . self::Terbilang($x % 10);
        } elseif ($x < 200) {
            return " seratus" . self::Terbilang($x - 100);
        } elseif ($x < 1000) {
            return self::Terbilang(intdiv($x, 100)) . " ratus" . self::Terbilang($x % 100);
        } elseif ($x < 2000) {
            return " seribu" . self::Terbilang($x - 1000);
        } elseif ($x < 1000000) {
            return self::Terbilang(intdiv($x, 1000)) . " ribu" . self::Terbilang($x % 1000);
        } elseif ($x < 1000000000) {
            return self::Terbilang(intdiv($x, 1000000)) . " juta" . self::Terbilang($x % 1000000);
        }elseif ($x < 1000000000000) {
            return self::Terbilang(intdiv($x, 1000000000)) . " miliar" . self::Terbilang($x % 1000000000);
        }  else {
            return "Number is too large";
        }
    }
}
