<?php

namespace App\Services\Lab;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class QueryLab
{

    public static function getDatakhanza($carinomor, $tanggal1,  $tanggal2, $status_lanjut)
    {
        $getDatakhanza =  DB::table('permintaan_lab')
            ->select(
                'reg_periksa.status_lanjut',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_pj',
                'reg_periksa.kd_dokter',
                'reg_periksa.kd_poli',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'pasien.no_tlp',
                'pasien.email',
                'pasien.nip',
                'perujuk.kd_dokter as kd_dr_perujuk',
                'perujuk.nm_dokter as dr_perujuk',
                'dokter.nm_dokter',
                'penjab.png_jawab',
                'poliklinik.nm_poli',
                'permintaan_lab.noorder',
                'permintaan_lab.tgl_permintaan',
                'permintaan_lab.jam_permintaan',
                'kamar_inap.kd_kamar',
                'kamar.kelas',
                'bangsal.nm_bangsal',
                'bangsal.kd_bangsal',
                DB::raw('"N" as cito'),
                DB::raw('"N" as med_legal'),
                DB::raw('"" as reserve1'),
                DB::raw('"" as reserve2'),
                DB::raw('"" as reserve3'),
                DB::raw('"" as reserve4'),
                DB::raw('"N" as order_control')
            )
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'permintaan_lab.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('dokter as perujuk', 'permintaan_lab.dokter_perujuk', '=', 'perujuk.kd_dokter')
            ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->where('reg_periksa.status_lanjut', $status_lanjut)
            ->whereBetween('permintaan_lab.tgl_permintaan', [$tanggal1, $tanggal2])
            ->where(function ($query) use ($carinomor) {
                if ($carinomor) {
                    $query->orwhere('reg_periksa.no_rkm_medis', 'LIKE', "%$carinomor%")
                        ->orwhere('pasien.nm_pasien', 'LIKE', "%$carinomor%")
                        ->orwhere('reg_periksa.no_rawat', 'LIKE', "%$carinomor%")
                        ->orwhere('permintaan_lab.noorder', 'LIKE', "%$carinomor%");
                }
            })
            ->get();
        $getDatakhanza->map(function ($item) {
            $item->Permintaan = DB::table('permintaan_pemeriksaan_lab')
                ->select('permintaan_pemeriksaan_lab.kd_jenis_prw', 'jns_perawatan_lab.nm_perawatan')
                ->where('permintaan_pemeriksaan_lab.noorder', $item->noorder)
                ->join('jns_perawatan_lab', 'permintaan_pemeriksaan_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
                ->get();
        });
        return $getDatakhanza;
    }
}
