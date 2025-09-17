<?php

namespace App\Http\Controllers\SuratBiometrik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NomorSurat;
use App\Models\PasienPrint;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BiometrikRajal extends Controller
{
    /**
     * Halaman utama
     */
    public function index(Request $request)
    {
        $tgl_awal  = $request->tgl_awal ?? date('Y-m-d');
        $tgl_akhir = $request->tgl_akhir ?? date('Y-m-d');
        $listPasien = collect(); // supaya tetap iterable di blade

        return view('suratbiometrik.biometrikrajal', compact('listPasien', 'tgl_awal', 'tgl_akhir'));
    }

    /**
     * Cari pasien berdasarkan no_peserta + range tanggal
     */
    public function cariPasien(Request $request)
    {
        $no_peserta = $request->no_peserta;
        $tgl_awal   = $request->tgl_awal ?? date('Y-m-d');
        $tgl_akhir  = $request->tgl_akhir ?? date('Y-m-d');

        $listPasien = DB::table('pasien')
            ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('bridging_sep', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
            ->select(
                'reg_periksa.no_rawat as id', // alias biar gampang dipakai di blade
                'pasien.nm_pasien as nama',
                'pasien.no_peserta as no_kartu_bpjs',
                'bridging_sep.no_sep',
                'poliklinik.nm_poli as poli_tujuan',
                'bridging_sep.nmdiagnosaawal as diagnosis',
                'reg_periksa.tgl_registrasi'
            )
            ->where('pasien.no_peserta', $no_peserta)
            ->where('reg_periksa.status_lanjut', 'RALAN')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tgl_awal, $tgl_akhir])
            ->orderByDesc('reg_periksa.tgl_registrasi')
            ->get();

        return view('suratbiometrik.biometrikrajal', compact('listPasien', 'tgl_awal', 'tgl_akhir'));
    }

    /**
     * Ambil detail pasien berdasarkan no_rawat
     */
    public function detail($id)
    {
        try {
            $pasien = DB::table('pasien')
                ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->join('bridging_sep', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
                ->select(
                    'reg_periksa.no_rawat as id',
                    'pasien.nm_pasien as nama',
                    'pasien.no_peserta as no_kartu_bpjs',
                    'reg_periksa.tgl_registrasi',
                    'poliklinik.nm_poli as poli_tujuan',
                    'bridging_sep.no_sep',
                    'bridging_sep.nmdiagnosaawal as diagnosis',
                    'reg_periksa.status_lanjut'
                )
                ->where('reg_periksa.no_rawat', $id)
                ->where('reg_periksa.status_lanjut', 'RALAN')
                ->first();

            if (!$pasien) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nomor surat belum dibuat. Silakan buat dulu dari menu Formulir'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'pasien' => $pasien,
                'nomorSurat' => 'BM-' . $pasien->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Simpan log biometrik
     */
    public function simpan(Request $request)
    {
        $request->validate([
            'no_rawat'   => 'required',
            'keterangan' => 'nullable|string',
        ]);

        DB::table('log_biometrik')->insert([
            'no_rawat'   => $request->no_rawat,
            'keterangan' => $request->keterangan ?? '-',
            'created_at' => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil disimpan'
        ]);
    }

    public function print($id)
{
    $id = urldecode($id);
    $tglAwal  = request('tgl_awal');
    $tglAkhir = request('tgl_akhir');

    $query = DB::table('pasien')
        ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->leftJoin('bridging_sep', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat') // amanin kalo SEP kosong
        ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
        ->leftJoin('sep_ttd', 'bridging_sep.no_sep', '=', 'sep_ttd.no_sep') // 🔹 join sep_ttd
        ->select(
            'reg_periksa.no_rawat as id',
            'pasien.nm_pasien as nama',
            'pasien.no_peserta as no_kartu_bpjs',
            'bridging_sep.no_sep',
            'poliklinik.nm_poli as poli_tujuan',
            'bridging_sep.nmdiagnosaawal as diagnosis',
            'reg_periksa.tgl_registrasi',
            'dokter.nm_dokter as nama_dokter',
            'sep_ttd.ttd as file_ttd' // 🔹 ambil nama file ttd
        )
        ->where('reg_periksa.no_rawat', $id);

    if ($tglAwal && $tglAkhir) {
        $query->whereBetween('reg_periksa.tgl_registrasi', [$tglAwal, $tglAkhir]);
    }

    $pasien = $query->first();

    if (!$pasien) {
        return redirect()->route('biometrik.rajal.index')
                         ->with('error', 'Data pasien tidak ditemukan.');
    }

    // 🔹 cek apakah nomor surat sudah ada
    $nomorSurat = NomorSurat::where('no_sep', $pasien->no_sep)->value('nomor_surat');

    if (!$nomorSurat) {
        // 👉 kalau belum ada, lempar ke Formulir (dengan data pasien terisi)
        return redirect()->route('formulir.biometrik.rajal.create', [
            'no_peserta' => $pasien->no_kartu_bpjs,
            'no_rawat'   => $pasien->id
        ]);
    }

    // kalau sudah ada → langsung tampilkan surat
    return view('suratbiometrik.formulir.printsuratbiometrikrajal', [
        'pasien'     => $pasien,
        'nomorSurat' => $nomorSurat,
    ]);
}






    /**
     * Generate nomor surat unik
     */
    private function generateNomorSurat($jenis_surat = 'RJ', $no_sep = null)
    {
        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        $bulan      = $bulanRomawi[date('n')];
        $tahun      = date('Y');
        $tanggal    = date('d');
        $bulanAngka = date('m');

        if ($no_sep) {
            $existing = NomorSurat::where('no_sep', $no_sep)->first();
            if ($existing) {
                return $existing->nomor_surat;
            }
        }

        $last = NomorSurat::where('jenis_surat', $jenis_surat)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderByDesc('id')
            ->first();

        $nomorUrut = $last ? $last->nomor_urut + 1 : 1;

        $nomorSurat = sprintf(
            "%s/%03d/RSBW/%s/%s",
            $jenis_surat, $nomorUrut, $bulan, $tahun
        );

        NomorSurat::create([
            'jenis_surat' => $jenis_surat,
            'nomor_urut'  => $nomorUrut,
            'nomor_surat' => $nomorSurat,
            'kode_rs'     => 'RSBW',
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'no_sep'      => $no_sep ?? '-',
        ]);

        return $nomorSurat;
    }
}
