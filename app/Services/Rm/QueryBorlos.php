<?php

namespace App\Services\Rm;

use Illuminate\Support\Facades\DB;

class QueryBorlos
{

    public static function jmlHariRawat($start_Date, $end_Date, $kamar)
    {
        $jml_hari_rawat = DB::table('reg_periksa')
            ->select(DB::raw('SUM(DATEDIFF(kamar_inap.tgl_keluar, kamar_inap.tgl_masuk)) as total_jumlah_hari'))
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->whereBetween('kamar_inap.tgl_keluar', [$start_Date, $end_Date])
            ->where('bangsal.nm_bangsal', 'like', '%' . $kamar . '%')
            ->first();
        return (int) ($jml_hari_rawat->total_jumlah_hari ?? 0);
    }

    public static function pasienKeluar($start_Date, $end_Date, $kamar)
    {
        $pasienKeluar = DB::table('reg_periksa')
            ->select(DB::raw('COUNT(kamar_inap.no_rawat) AS jumlah_pasien_keluar'))
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->whereBetween('kamar_inap.tgl_keluar', [$start_Date, $end_Date])
            ->where('bangsal.nm_bangsal', 'like', '%' . $kamar . '%')
            ->groupBy('bangsal.nm_bangsal')
            ->get();
        return (int) ($pasienKeluar->sum('jumlah_pasien_keluar') ?? 0);
    }

    public static function lamaDiRawat($start_Date, $end_Date, $kamar)
    {
        $lama_dirawat = DB::table('reg_periksa')
            ->select(DB::raw('SUM(kamar_inap.lama) AS total_days_hospitalized'))
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->whereBetween('kamar_inap.tgl_keluar', [$start_Date, $end_Date])
            ->where('bangsal.nm_bangsal', 'like', '%' . $kamar . '%')
            ->groupBy('bangsal.nm_bangsal')
            ->get();
        return (int) ($lama_dirawat->sum('total_days_hospitalized') ?? 0);
    }
    public static function pasienMati48jam($start_Date, $end_Date, $kamar)
    {
        return DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->whereBetween('kamar_inap.tgl_keluar', [$start_Date, $end_Date])
            ->whereRaw("TIMESTAMPDIFF(HOUR, CONCAT(kamar_inap.tgl_masuk, ' ', kamar_inap.jam_masuk), CONCAT(kamar_inap.tgl_keluar, ' ', kamar_inap.jam_keluar)) >= 48")
            ->where('kamar_inap.stts_pulang', 'Meninggal')
            ->where('bangsal.nm_bangsal', 'LIKE', '%' . $kamar . '%')
            ->count();
    }
    public static function pasienMati($start_Date, $end_Date, $kamar)
    {
        return DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->whereBetween('kamar_inap.tgl_keluar', [$start_Date, $end_Date])
            ->where('kamar_inap.stts_pulang', 'Meninggal')
            ->where('bangsal.nm_bangsal', 'LIKE', '%' . $kamar . '%')
            ->count();
    }
}
