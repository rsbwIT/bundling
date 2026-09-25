<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabPK extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter tanggal bayar (tanggal dari nota_jalan) dari request
        $tglMulai = $request->get('tgl_mulai', date('Y-m-d'));
        $tglSelesai = $request->get('tgl_selesai', date('Y-m-d'));
        $jenisPasien = $request->get('jenis_pasien', 'semua');

        // Jalankan query menggunakan Query Builder Laravel
        $data = DB::table('periksa_lab')
            ->select([
                'periksa_lab.no_rawat',
                DB::raw("COALESCE(nota_jalan.no_nota, nota_inap.no_nota) as no_nota"),
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'reg_periksa.tgl_registrasi',
                'dokter.nm_dokter',
                'periksa_lab.dokter_perujuk',
                'periksa_lab.bagian_rs',
                'periksa_lab.bhp',
                'periksa_lab.tarif_perujuk',
                'periksa_lab.tarif_tindakan_dokter',
                'periksa_lab.tarif_tindakan_petugas',
                'periksa_lab.kso',
                'periksa_lab.menejemen',
                'periksa_lab.biaya',
                'penjab.png_jawab as jenis_bayar',
                DB::raw("IF(penjab.png_jawab LIKE '%umum%', COALESCE(nota_jalan.tanggal, nota_inap.tanggal), bayar_piutang.tgl_bayar) as tgl_pembayaran")
            ])
            ->join('reg_periksa', 'periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'periksa_lab.dokter_perujuk', '=', 'dokter.kd_dokter')
            ->leftJoin('nota_jalan', 'reg_periksa.no_rawat', '=', 'nota_jalan.no_rawat')
            ->leftJoin('nota_inap', 'reg_periksa.no_rawat', '=', 'nota_inap.no_rawat')
            ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where(function ($query) use ($tglMulai, $tglSelesai, $jenisPasien) {
                if ($jenisPasien == 'semua') {
                    // Semua pasien (gabungan)
                    $query->where(function ($q) use ($tglMulai, $tglSelesai) {
                        $q->where('penjab.png_jawab', 'like', '%umum%')
                          ->where(function ($sq) use ($tglMulai, $tglSelesai) {
                              $sq->whereBetween('nota_jalan.tanggal', [$tglMulai, $tglSelesai])
                                 ->orWhereBetween('nota_inap.tanggal', [$tglMulai, $tglSelesai]);
                          });
                    })
                    ->orWhere(function ($q) use ($tglMulai, $tglSelesai) {
                        $q->where('penjab.png_jawab', 'not like', '%umum%')
                          ->whereBetween('bayar_piutang.tgl_bayar', [$tglMulai, $tglSelesai]);
                    });
                } elseif ($jenisPasien == 'umum') {
                    // Hanya Umum
                    $query->where('penjab.png_jawab', 'like', '%umum%')
                          ->where(function ($sq) use ($tglMulai, $tglSelesai) {
                              $sq->whereBetween('nota_jalan.tanggal', [$tglMulai, $tglSelesai])
                                 ->orWhereBetween('nota_inap.tanggal', [$tglMulai, $tglSelesai]);
                          });
                } else {
                    // Hanya Asuransi / Selain Umum
                    $query->where('penjab.png_jawab', 'not like', '%umum%')
                          ->whereBetween('bayar_piutang.tgl_bayar', [$tglMulai, $tglSelesai]);
                }
            })
            ->orderBy('periksa_lab.no_rawat', 'ASC')
            ->get();

        // Jika dipanggil lewat AJAX/API
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'tgl_mulai' => $tglMulai,
                'tgl_selesai' => $tglSelesai,
                'data' => $data
            ]);
        }

        return view('laporan.lab_pk', compact('data', 'tglMulai', 'tglSelesai'));
    }
}
