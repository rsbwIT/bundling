<?php

namespace App\Services\Regperiksa;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FuntionKhanza
{
    public static function noRegistrasi($kd_dokter, $kdPoli) // JIKA INGIN NOMOR REG BERDASARKAN PENJAMIN noRegistrasi($kd_dokter, $kdPoli, $penjab)
    {
        $nextNoReg = DB::table('reg_periksa')
            ->where('kd_poli', $kdPoli)
            ->where('kd_dokter', $kd_dokter)
            // ->where('kd_pj', $penjab) // JIKA INGIN NOMOR REG BERDASARKAN PENJAMIN
            ->where('tgl_registrasi', Carbon::now()->format('Y-m-d'))
            ->max(DB::raw('CONVERT(no_reg, SIGNED)'));
        return $nextNoReg = str_pad(is_null($nextNoReg) ? 1 : $nextNoReg + 1, 3, '0', STR_PAD_LEFT);
    }
    public static function noRawat()
    {
        $max =  DB::table('reg_periksa')
            ->whereDate('tgl_registrasi', Carbon::now()->format('Y-m-d'))
            ->select(DB::raw('IFNULL(MAX(CAST(RIGHT(no_rawat, 6) AS UNSIGNED)), 0) + 1 AS next_number'))
            ->pluck('next_number')
            ->first();
        return str_replace("-", "/", Carbon::now()->format('Y-m-d') . "/") . sprintf("%06s", $max);
    }

    public static function getBiayaReg($registrasi, $stts_daftar)
    {
        $reg = DB::table('poliklinik')
            ->select('poliklinik.registrasi', 'poliklinik.registrasilama')
            ->where('poliklinik.kd_poli', '=', $registrasi)
            ->first();
        if ($stts_daftar == "Baru") {
            return $reg->registrasi;
        } else {
            return $reg->registrasilama;
        }
    }

    public static function umurPasien($no_rkm_medis)
    {
        $data =  DB::table('pasien')
            ->select(
                DB::raw('TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, CURDATE()) AS tahun'),
                DB::raw('(
                TIMESTAMPDIFF(MONTH, pasien.tgl_lahir, CURDATE()) -
                (TIMESTAMPDIFF(MONTH, pasien.tgl_lahir, CURDATE()) DIV 12) * 12
            ) AS bulan'),
                DB::raw('TIMESTAMPDIFF(
                DAY,
                DATE_ADD(
                    DATE_ADD(pasien.tgl_lahir, INTERVAL TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, CURDATE()) YEAR),
                    INTERVAL TIMESTAMPDIFF(MONTH, pasien.tgl_lahir, CURDATE()) -
                    (TIMESTAMPDIFF(MONTH, pasien.tgl_lahir, CURDATE()) DIV 12) MONTH
                ),
                CURDATE()
            ) AS hari')
            )
            ->where('no_rkm_medis', $no_rkm_medis)
            ->first();
        if ($data->tahun > 0) {
            $umur       = $data->tahun;
            $sttsumur   = "Th";
        } else if ($data->tahun == 0) {
            if ($data->bulan > 0) {
                $umur       = $data->bulan;
                $sttsumur   = "Bl";
            } else if ($data->bulan == 0) {
                $umur       = $data->hari;
                $sttsumur   = "Hr";
            }
        }
        $umurpasien = [
            'umur' => $umur,
            'sttsumur' => $sttsumur,
        ];
        return $umurpasien;
    }

    public static function getStatuspoli($no_rkm_medis, $kd_poli)
    {
        return DB::table('reg_periksa')
            ->where('no_rkm_medis', $no_rkm_medis)
            ->where('kd_poli', $kd_poli)
            ->count() > 0 ? 'Lama' : 'Baru';
    }

    public static function cekRegistrasi($carinomor) {
        return DB::table('reg_periksa')
        ->select('reg_periksa.almt_pj')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->where('reg_periksa.stts','!=', 'Batal')
        ->where('reg_periksa.tgl_registrasi', Carbon::now()->format('Y-m-d'))
        ->where(function ($query) use ($carinomor) {
            $query->orWhere('pasien.no_rkm_medis', '=', $carinomor)
                ->orWhere('pasien.no_ktp', '=', $carinomor)
                ->orWhere('pasien.no_peserta', '=', $carinomor);
        })
        ->count();
    }

    public static function cekRegistrasiBelum($carinomor) {
        return DB::table('reg_periksa')
        ->select('reg_periksa.no_rkm_medis')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->where('reg_periksa.stts','=', 'Belum')
        ->where('reg_periksa.tgl_registrasi',  '>=', Carbon::tomorrow()->toDateString())
        ->where(function ($query) use ($carinomor) {
            $query->orWhere('pasien.no_rkm_medis', '=', $carinomor)
                ->orWhere('pasien.no_ktp', '=', $carinomor)
                ->orWhere('pasien.no_peserta', '=', $carinomor);
        })
        ->count();
    }

    public static function TrackerSimpan($table, $data)
    {
        $values = implode(', ', array_map(function ($value) {
            return is_string($value) ? "'$value'" : $value;
        }, array_values($data)));
        return DB::table('trackersql')->insert([
            'tanggal' => Carbon::now()->format('Y-m-d H:i:s'),
            'sqle' => 'Insert Into ' . $table . '(' . $values . ')',
            'usere' => session('auth')['id_user'],
        ]);
    }
}
