<?php

namespace App\Http\Controllers\SuratBiometrik\Formulir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NomorSurat;

class FormulirBiometrikRanap extends Controller
{
    public function create(Request $request)
    {
        $no_peserta = $request->get('no_peserta');
        $no_rawat   = $request->get('no_rawat');
        $pasien     = null;
        $nomorSuratPreview = null;
        $warning = null;

        if ($no_rawat || $no_peserta) {
            $query = DB::table('pasien')
                ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
                ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                ->leftJoin('bridging_sep', function ($join) {
                    $join->on('reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
                         ->where('bridging_sep.jnspelayanan', '=', 1); // hanya rawat inap
                })
                ->select(
                    'reg_periksa.no_rawat',
                    'pasien.nm_pasien',
                    'pasien.no_peserta',
                    'reg_periksa.tgl_registrasi',
                    'kamar_inap.tgl_masuk', // ✅ ambil langsung dari kamar_inap
                    'bangsal.nm_bangsal as ruang_rawat',
                    'bridging_sep.no_sep',
                    'bridging_sep.nmdiagnosaawal as diagnosis',
                    'bridging_sep.jnspelayanan'
                );

            if ($no_rawat) {
                $query->where('reg_periksa.no_rawat', $no_rawat);
            } elseif ($no_peserta) {
                $query->where('pasien.no_peserta', $no_peserta);
            }

            // ambil kamar_inap paling awal sesuai no_rawat
            $pasien = $query->orderBy('kamar_inap.tgl_masuk', 'asc')->first();

            if ($pasien) {
                if (is_null($pasien->no_sep)) {
                    $warning = "Perhatian: Pasien belum memiliki SEP Rawat Inap (jns_pelayanan=1).";
                }
                $nomorSuratPreview = $this->generatePreviewNomorSurat('RI');
            }
        }

        return view('suratbiometrik.formulir.formulirbiometrikranap', [
            'pasien'     => $pasien,
            'nomorSurat' => $nomorSuratPreview,
            'warning'    => $warning,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_rawat'    => 'required|string',
            'nm_pasien'   => 'required|string',
            'no_peserta'  => 'required|string',
            'no_sep'      => 'required|string',
            'ruang_rawat' => 'required|string',
            'diagnosis'   => 'required|string',
            'tgl_masuk'   => 'required|date',
        ]);

        $nomorSurat = $this->generateAndSaveNomorSurat('RI', $request);

        return redirect()
            ->route('biometrik.ranap.index')
            ->with('success', "Surat berhasil dibuat dengan nomor: $nomorSurat");
    }

    private function generatePreviewNomorSurat($jenis_surat)
    {
        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        $bulan = $bulanRomawi[date('n')];
        $tahun = date('Y');

        $last = NomorSurat::where('jenis_surat', $jenis_surat)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderByDesc('id')
            ->first();

        $nomorUrut = $last ? $last->nomor_urut + 1 : 1;

        return sprintf("%s/%03d/RSBW/%s/%s", $jenis_surat, $nomorUrut, $bulan, $tahun);
    }

    private function generateAndSaveNomorSurat($jenis_surat, Request $request)
    {
        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        $bulan = $bulanRomawi[date('n')];
        $tahun = date('Y');

        $last = NomorSurat::where('jenis_surat', $jenis_surat)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderByDesc('id')
            ->first();

        $nomorUrut = $last ? $last->nomor_urut + 1 : 1;

        $nomorSurat = sprintf("%s/%03d/RSBW/%s/%s", $jenis_surat, $nomorUrut, $bulan, $tahun);

        NomorSurat::create([
            'jenis_surat'    => $jenis_surat,
            'nomor_urut'     => $nomorUrut,
            'nomor_surat'    => $nomorSurat,
            'kode_rs'        => 'RSBW',
            'bulan'          => $bulan,
            'tahun'          => $tahun,
            'no_sep'         => $request->no_sep,
            'no_rawat'       => $request->no_rawat,
            'nm_pasien'      => $request->nm_pasien,
            'no_peserta'     => $request->no_peserta,
            'ruang_rawat'    => $request->ruang_rawat,
            'diagnosis'      => $request->diagnosis,
            'tgl_masuk'      => $request->tgl_masuk,
        ]);

        return $nomorSurat;
    }
}