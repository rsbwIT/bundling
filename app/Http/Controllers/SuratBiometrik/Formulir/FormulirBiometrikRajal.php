<?php

namespace App\Http\Controllers\SuratBiometrik\Formulir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NomorSurat;

class FormulirBiometrikRajal extends Controller
{
public function create(Request $request)
{
    $no_peserta = $request->get('no_peserta');
    $no_rawat   = $request->get('no_rawat');
    $pasien     = null;
    $nomorSuratPreview = null;

    if ($no_rawat) {
        $pasien = DB::table('pasien')
            ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('bridging_sep', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->select(
                'reg_periksa.no_rawat',
                'pasien.nm_pasien',
                'pasien.no_peserta',
                'reg_periksa.tgl_registrasi',
                'poliklinik.nm_poli',
                'bridging_sep.no_sep',
                'bridging_sep.nmdiagnosaawal as diagnosis'
            )
            ->first();

        if ($pasien) {
            $nomorSuratPreview = $this->generatePreviewNomorSurat('RJ');
        }
    }

    return view('suratbiometrik.formulir.formulirbiometrikrajal', [
        'pasien' => $pasien,
        'nomorSurat' => $nomorSuratPreview
    ]);
}


public function store(Request $request)
{
    $request->validate([
        'no_rawat'       => 'required',
        'nm_pasien'      => 'required',
        'no_peserta'     => 'required',
        'no_sep'         => 'required',
        'nm_poli'        => 'required',
        'diagnosis'      => 'required',
        'tgl_registrasi' => 'required|date',
    ]);

    // simpan nomor surat baru
    $nomorSurat = $this->generateAndSaveNomorSurat('RJ', $request);

    // 👉 setelah simpan, lempar balik ke daftar Biometrik Rajal
    return redirect()
        ->route('biometrik.rajal.index')
        ->with('success', "Surat berhasil dibuat dengan nomor: $nomorSurat");
}


/**
 * 🔹 Generate nomor preview (tidak save DB)
 */
private function generatePreviewNomorSurat($jenis_surat)
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

    $last = NomorSurat::where('jenis_surat', $jenis_surat)
        ->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->orderByDesc('id')
        ->first();

    $nomorUrut = $last ? $last->nomor_urut + 1 : 1;

    return sprintf(
        "%s/%03d/RSBW/%s/%s",
        $jenis_surat, $nomorUrut, $bulan, $tahun
    );
}

/**
 * 🔹 Generate & Simpan ke DB
 */
private function generateAndSaveNomorSurat($jenis_surat, $request)
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
        'jenis_surat'   => $jenis_surat,
        'nomor_urut'    => $nomorUrut,
        'nomor_surat'   => $nomorSurat,
        'kode_rs'       => 'RSBW',
        'bulan'         => $bulan,
        'tahun'         => $tahun,
        'no_sep'        => $request->no_sep,
        'no_rawat'      => $request->no_rawat,
        'nm_pasien'     => $request->nm_pasien,
        'no_peserta'    => $request->no_peserta,
        'nm_poli'       => $request->nm_poli,
        'diagnosis'     => $request->diagnosis,
        'tgl_registrasi'=> $request->tgl_registrasi,
    ]);

    return $nomorSurat;
}
}