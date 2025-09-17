<?php

namespace App\Http\Controllers\SuratBiometrik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NomorSurat;

class BiometrikRanap extends Controller
{
    /**
     * Halaman utama
     */
    public function index(Request $request)
    {
        $tgl_awal  = $request->tgl_awal ?? date('Y-m-d');
        $tgl_akhir = $request->tgl_akhir ?? date('Y-m-d');
        $listPasien = collect();

        return view('suratbiometrik.biometrikranap', compact('listPasien', 'tgl_awal', 'tgl_akhir'));
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
            ->join('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->leftJoin('bridging_sep', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
            ->select(
                DB::raw('MIN(reg_periksa.no_rawat) as id'),
                'pasien.nm_pasien as nama',
                'pasien.no_peserta as no_kartu_bpjs',
                'bridging_sep.no_sep',
                DB::raw('MAX(bangsal.nm_bangsal) as ruang_rawat'), // ambil salah satu ruang
                'bridging_sep.nmdiagnosaawal as diagnosis',
                'bridging_sep.jnspelayanan',
                DB::raw('MIN(reg_periksa.tgl_registrasi) as tgl_registrasi'),
                DB::raw('MIN(kamar_inap.tgl_masuk) as tgl_masuk') // ambil tgl masuk pertama
            )
            ->where('pasien.no_peserta', $no_peserta)
            ->where('reg_periksa.status_lanjut', 'RANAP')
            ->where('bridging_sep.jnspelayanan', 1)
            ->whereBetween('reg_periksa.tgl_registrasi', [$tgl_awal, $tgl_akhir])
            ->groupBy(
                'pasien.nm_pasien',
                'pasien.no_peserta',
                'bridging_sep.no_sep',
                'bridging_sep.nmdiagnosaawal',
                'bridging_sep.jnspelayanan'
            )
            ->orderByDesc(DB::raw('MIN(kamar_inap.tgl_masuk)'))
            ->get();

        return view('suratbiometrik.biometrikranap', compact('listPasien', 'tgl_awal', 'tgl_akhir'));
    }

    /**
     * Detail pasien berdasarkan no_rawat
     */
    public function detail($id)
    {
        try {
            $pasien = DB::table('pasien')
                ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
                ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                ->leftJoin('bridging_sep', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
                ->select(
                    'reg_periksa.no_rawat as id',
                    'pasien.nm_pasien as nama',
                    'pasien.no_peserta as no_kartu_bpjs',
                    'reg_periksa.tgl_registrasi',
                    'kamar_inap.tgl_masuk',
                    'bangsal.nm_bangsal as ruang_rawat',
                    'bridging_sep.no_sep',
                    'bridging_sep.nmdiagnosaawal as diagnosis',
                    'bridging_sep.jnspelayanan',
                    'reg_periksa.status_lanjut'
                )
                ->where('reg_periksa.no_rawat', $id)
                ->where('reg_periksa.status_lanjut', 'RANAP')
                ->where('bridging_sep.jnspelayanan', 1)
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

    /**
     * Print surat biometrik ranap
     */
    public function print($id)
{
    $id       = urldecode($id);
    $tglAwal  = request('tgl_awal');
    $tglAkhir = request('tgl_akhir');

    $pasien = DB::table('bridging_sep')
        ->join('reg_periksa', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
        ->join('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
        ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
        ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
        ->leftJoin('dpjp_ranap', 'reg_periksa.no_rawat', '=', 'dpjp_ranap.no_rawat')
        ->leftJoin('dokter', 'dpjp_ranap.kd_dokter', '=', 'dokter.kd_dokter')
        ->leftJoin('sep_ttd', 'bridging_sep.no_sep', '=', 'sep_ttd.no_sep') // 🔹 join sep_ttd
        ->select(
            'reg_periksa.no_rawat as id',
            'pasien.nm_pasien as nama',
            'pasien.no_peserta as no_kartu_bpjs',
            'bridging_sep.no_sep',
            'bridging_sep.jnspelayanan',
            'bridging_sep.nmdiagnosaawal as diagnosis',
            'bangsal.nm_bangsal as ruang_rawat',
            'dokter.nm_dokter as nama_dokter',
            'sep_ttd.ttd as file_ttd', // 🔹 ambil nama file ttd
            DB::raw('MIN(kamar_inap.tgl_masuk) as tgl_masuk'),
            DB::raw('MAX(NULLIF(kamar_inap.tgl_keluar, "0000-00-00")) as tgl_keluar'),
            DB::raw("
                MAX(
                    CASE
                        WHEN kamar_inap.jam_keluar <> '00:00:00'
                            AND kamar_inap.tgl_keluar <> '0000-00-00'
                        THEN CONCAT(kamar_inap.tgl_keluar, ' ', kamar_inap.jam_keluar)
                        ELSE NULL
                    END
                ) as tgl_pulang
            ")
        )
        ->where('reg_periksa.no_rawat', $id)
        ->where('bridging_sep.jnspelayanan', 1) // hanya rawat inap
        ->groupBy(
            'reg_periksa.no_rawat',
            'pasien.nm_pasien',
            'pasien.no_peserta',
            'bridging_sep.no_sep',
            'bridging_sep.jnspelayanan',
            'bridging_sep.nmdiagnosaawal',
            'bangsal.nm_bangsal',
            'dokter.nm_dokter',
            'sep_ttd.ttd' // 🔹 jangan lupa masuk ke group by
        );

    if ($tglAwal && $tglAkhir) {
        $pasien->whereBetween('reg_periksa.tgl_registrasi', [$tglAwal, $tglAkhir]);
    }

    $pasien = $pasien->first();

    if (!$pasien) {
        return redirect()->route('biometrik.ranap.index')
                        ->with('error', 'Data pasien tidak ditemukan.');
    }

    $pasien->tgl_keluar = $pasien->tgl_keluar ?: null;
    $pasien->tgl_pulang = $pasien->tgl_pulang ?: null;

    // cek nomor surat
    $nomorSurat = NomorSurat::where('no_sep', $pasien->no_sep)
        ->where('jenis_surat', 'RI')
        ->value('nomor_surat');

    if (!$nomorSurat) {
        return redirect()->route('formulir.biometrik.ranap.create', [
            'no_peserta' => $pasien->no_kartu_bpjs,
            'no_rawat'   => $pasien->id
        ]);
    }

    return view('suratbiometrik.formulir.printsuratbiometrikranap', [
        'pasien'     => $pasien,
        'nomorSurat' => $nomorSurat,
    ]);
}



    /**
     * Generate nomor surat unik
     */
    private function generateNomorSurat($jenis_surat = 'RI', $no_sep = null)
    {
        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        $bulan      = $bulanRomawi[date('n')];
        $tahun      = date('Y');

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
