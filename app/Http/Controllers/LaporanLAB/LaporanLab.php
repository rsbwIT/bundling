<?php

namespace App\Http\Controllers\LaporanLAB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanLab extends Controller
{
    public function index(Request $request)
    {
        $tglAwal = $request->filled('tgl_awal')
            ? $request->tgl_awal
            : Carbon::today()->toDateString();

        $tglAkhir = $request->filled('tgl_akhir')
            ? $request->tgl_akhir
            : Carbon::today()->toDateString();
            
        $data = DB::select("
        SELECT
            reg_periksa.no_rawat,
            pasien.no_rkm_medis,
            pasien.nm_pasien,
            pasien.tgl_lahir,
            pasien.no_ktp,
            pasien.alamat,
            detail_periksa_lab.tgl_periksa,
            jns_perawatan_lab.nm_perawatan,
            detail_periksa_lab.nilai,
            reg_periksa.status_lanjut,

            CASE
                WHEN reg_periksa.status_lanjut = 'Ralan'
                    THEN dokter.nm_dokter
                WHEN reg_periksa.status_lanjut = 'Ranap'
                    THEN dokter_ranap.nm_dokter
                ELSE '-'
            END AS nm_dokter

        FROM reg_periksa

        INNER JOIN pasien
            ON reg_periksa.no_rkm_medis = pasien.no_rkm_medis

        INNER JOIN detail_periksa_lab
            ON reg_periksa.no_rawat = detail_periksa_lab.no_rawat

        INNER JOIN jns_perawatan_lab
            ON detail_periksa_lab.kd_jenis_prw = jns_perawatan_lab.kd_jenis_prw

        LEFT JOIN dokter
            ON reg_periksa.kd_dokter = dokter.kd_dokter

        LEFT JOIN dpjp_ranap
            ON reg_periksa.no_rawat = dpjp_ranap.no_rawat

        LEFT JOIN dokter AS dokter_ranap
            ON dpjp_ranap.kd_dokter = dokter_ranap.kd_dokter

        WHERE
            detail_periksa_lab.tgl_periksa BETWEEN ? AND ?
            AND UPPER(jns_perawatan_lab.nm_perawatan) LIKE '%ANTI HIV%'

        ORDER BY detail_periksa_lab.tgl_periksa ASC
    ", [$tglAwal, $tglAkhir]);

        // TOTAL
        $totalRalan = DB::table('reg_periksa')
            ->join('detail_periksa_lab', 'reg_periksa.no_rawat', '=', 'detail_periksa_lab.no_rawat')
            ->join('jns_perawatan_lab', 'detail_periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->whereBetween('detail_periksa_lab.tgl_periksa', [$tglAwal, $tglAkhir])
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->whereRaw("UPPER(jns_perawatan_lab.nm_perawatan) LIKE '%ANTI HIV%'")
            ->count();

        $totalRanap = DB::table('reg_periksa')
            ->join('detail_periksa_lab', 'reg_periksa.no_rawat', '=', 'detail_periksa_lab.no_rawat')
            ->join('jns_perawatan_lab', 'detail_periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->whereBetween('detail_periksa_lab.tgl_periksa', [$tglAwal, $tglAkhir])
            ->where('reg_periksa.status_lanjut', 'Ranap')
            ->whereRaw("UPPER(jns_perawatan_lab.nm_perawatan) LIKE '%ANTI HIV%'")
            ->count();

        // SUDAH TERISI (ADA NILAI)
        $terisiRalan = DB::table('detail_periksa_lab')
            ->join('reg_periksa', 'detail_periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('jns_perawatan_lab', 'detail_periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->whereBetween('detail_periksa_lab.tgl_periksa', [$tglAwal, $tglAkhir])
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->whereNotNull('detail_periksa_lab.nilai')
            ->whereRaw("UPPER(jns_perawatan_lab.nm_perawatan) LIKE '%ANTI HIV%'")
            ->count();

        $terisiRanap = DB::table('detail_periksa_lab')
            ->join('reg_periksa', 'detail_periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('jns_perawatan_lab', 'detail_periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->whereBetween('detail_periksa_lab.tgl_periksa', [$tglAwal, $tglAkhir])
            ->where('reg_periksa.status_lanjut', 'Ranap')
            ->whereNotNull('detail_periksa_lab.nilai')
            ->whereRaw("UPPER(jns_perawatan_lab.nm_perawatan) LIKE '%ANTI HIV%'")
            ->count();

        // BELUM TERISI
        $belumRalan = $totalRalan - $terisiRalan;
        $belumRanap = $totalRanap - $terisiRanap;

        return view('laporanlab.laporanlab', compact(
            'data',
            'tglAwal',
            'tglAkhir',
            'totalRalan',
            'totalRanap',
            'terisiRalan',
            'terisiRanap',
            'belumRalan',
            'belumRanap'
        ));
    }
}
