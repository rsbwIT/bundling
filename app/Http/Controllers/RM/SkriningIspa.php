<?php

namespace App\Http\Controllers\RM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class SkriningIspa extends Controller
{
    public function index(Request $request)
    {
        // Default tanggal awal & akhir bulan ini
        $tgl_awal  = $request->tgl_awal  ?? Carbon::today()->startOfMonth()->toDateString();
        $tgl_akhir = $request->tgl_akhir ?? Carbon::today()->toDateString();
        $keyword   = $request->keyword;
        $bangsal   = $request->bangsal;
        $penjamin  = $request->penjamin;

        // ================= QUERY DASAR =================
        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->join('kecamatan', 'pasien.kd_kec', '=', 'kecamatan.kd_kec')
            ->join('kelurahan', 'pasien.kd_kel', '=', 'kelurahan.kd_kel')
            ->join('diagnosa_pasien', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.alamat',
                'pasien.jk',
                DB::raw("CONCAT(reg_periksa.umurdaftar, ' ', reg_periksa.sttsumur) AS umur"),
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'pasien.tgl_daftar',
                'reg_periksa.stts_daftar',
                'kamar_inap.kd_kamar',
                'bangsal.nm_bangsal',
                DB::raw("CONCAT(pasien.alamat, ', ', kelurahan.nm_kel, ', ', kecamatan.nm_kec, ', ', kabupaten.nm_kab) AS almt_pj"),
                'kamar_inap.stts_pulang',
                'kamar_inap.tgl_masuk',
                'dokter.nm_dokter',
                'penyakit.kd_penyakit',
                'penyakit.nm_penyakit'
            )
            ->where('reg_periksa.status_lanjut', 'Ranap')
            ->where('reg_periksa.stts', '<>', 'Batal')
            ->where('kamar_inap.stts_pulang', '<>', 'Pindah Kamar')
            ->whereIn('penyakit.kd_penyakit', ['J06.9', 'J18.9'])
            ->where('diagnosa_pasien.prioritas', 1)
            ->whereBetween('reg_periksa.tgl_registrasi', [$tgl_awal, $tgl_akhir])
            // Filter Bangsal
            ->when($bangsal, function ($q) use ($bangsal) {
                $q->where('bangsal.nm_bangsal', 'like', "%$bangsal%");
            })
            // Filter Penjamin
            ->when($penjamin, function ($q) use ($penjamin) {
                $q->where('penjab.png_jawab', 'like', "%$penjamin%");
            })
            // Filter Keyword (cari nama pasien, alamat, dokter, no RM, kamar)
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('pasien.alamat', 'like', "%$keyword%")
                        ->orWhere('pasien.nm_pasien', 'like', "%$keyword%")
                        ->orWhere('dokter.nm_dokter', 'like', "%$keyword%")
                        ->orWhere('reg_periksa.no_rkm_medis', 'like', "%$keyword%")
                        ->orWhere('kamar_inap.kd_kamar', 'like', "%$keyword%");
                });
            })
            ->groupBy('reg_periksa.no_rawat')
            ->orderBy('reg_periksa.tgl_registrasi', 'asc');

        // ================= DATA =================
        $results = $query->get();

        // ================= SUMMARY =================
        $totalPasien = $results->count();
        $totalLaki = $results->where('jk', 'L')->count();
        $totalPerempuan = $results->where('jk', 'P')->count();

        // Hitung per kode penyakit
        $totalJ069 = $results->where('kd_penyakit', 'J06.9')->count();
        $totalJ189 = $results->where('kd_penyakit', 'J18.9')->count();

        // ================= SUMMARY KELOMPOK UMUR =================
        // Konversi umur ke tahun lalu kelompokkan
        $getUmurTahun = function ($item) {
            $umur = (int) $item->umurdaftar;
            $satuan = strtolower(trim($item->sttsumur));
            if ($satuan === 'th') return $umur;
            if ($satuan === 'bl') return $umur / 12;
            if ($satuan === 'hr') return $umur / 365;
            return $umur;
        };

        $isMeninggal = fn($i) => stripos($i->stts_pulang, 'Meninggal') !== false;

        // Definisi kelompok umur
        $kategoriUmur = [
            ['nama' => 'Balita (0-4 Th)',   'min' => 0,  'max' => 4.99],
            ['nama' => 'Anak (5-9 Th)',     'min' => 5,  'max' => 9.99],
            ['nama' => 'Remaja (10-18 Th)', 'min' => 10, 'max' => 18.99],
            ['nama' => 'Dewasa (19-59 Th)', 'min' => 19, 'max' => 59.99],
            ['nama' => 'Lansia (≥60 Th)',   'min' => 60, 'max' => 999],
        ];

        $summaryUmur = [];
        foreach ($kategoriUmur as $kat) {
            $filtered = $results->filter(fn($i) => $getUmurTahun($i) >= $kat['min'] && $getUmurTahun($i) <= $kat['max']);

            $j069 = $filtered->where('kd_penyakit', 'J06.9');
            $j189 = $filtered->where('kd_penyakit', 'J18.9');

            $summaryUmur[] = [
                'nama'        => $kat['nama'],
                'j069_l'      => $j069->where('jk', 'L')->count(),
                'j069_p'      => $j069->where('jk', 'P')->count(),
                'j069_total'  => $j069->count(),
                'j189_l'      => $j189->where('jk', 'L')->count(),
                'j189_p'      => $j189->where('jk', 'P')->count(),
                'j189_total'  => $j189->count(),
                'total'       => $filtered->count(),
                'meninggal'   => $filtered->filter($isMeninggal)->count(),
            ];
        }

        // Total keseluruhan
        $meninggalTotal = $results->filter($isMeninggal)->count();

        return view('rm.skrining-ispa', compact(
            'results',
            'tgl_awal',
            'tgl_akhir',
            'totalPasien',
            'totalLaki',
            'totalPerempuan',
            'totalJ069',
            'totalJ189',
            'summaryUmur',
            'meninggalTotal'
        ));
    }
}
