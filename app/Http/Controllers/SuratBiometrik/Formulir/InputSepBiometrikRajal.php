<?php

namespace App\Http\Controllers\SuratBiometrik\Formulir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NomorSurat;

class InputSepBiometrikRajal extends Controller
{
    /**
     * Form input SEP Rajal
     */
    public function create()
    {
        return view('suratbiometrik.formulir.inputsepbiometrikrajal');
    }

    /**
     * Simpan banyak nomor SEP sekaligus → auto create surat biometrik
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_sep' => 'required|string',
        ], [
            'no_sep.required' => 'Nomor SEP wajib diisi.',
        ]);

        $sepList = preg_split('/[\s,]+/', trim($request->input('no_sep')));
        $sepList = array_filter($sepList);

        $successList = [];
        $failedList  = [];

        foreach ($sepList as $no_sep) {
            $pasien = DB::table('pasien')
                ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->join('bridging_sep', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
                ->where('bridging_sep.no_sep', $no_sep)
                ->where('bridging_sep.jnspelayanan', '2')
                ->select(
                    'reg_periksa.no_rawat',
                    'pasien.nm_pasien',
                    'pasien.no_peserta',
                    'reg_periksa.tgl_registrasi',
                    'poliklinik.nm_poli',
                    'bridging_sep.no_sep',
                    'bridging_sep.nmdiagnosaawal as diagnosis',
                    'bridging_sep.jnspelayanan'
                )
                ->first();

            if (!$pasien) {
                $failedList[] = "SEP <b>{$no_sep}</b> tidak ditemukan atau bukan rawat jalan.";
                continue;
            }

            $nomorSurat = $this->generateAndSaveNomorSurat('RJ', $pasien);
            $successList[] = "SEP <b>{$no_sep}</b> → Surat <b>{$nomorSurat}</b> berhasil dibuat.";
        }

        return redirect()->back()->with([
            'successList' => $successList,
            'failedList'  => $failedList
        ]);
    }

    /**
     * Generate nomor surat baru & simpan ke DB
     */
    private function generateAndSaveNomorSurat($jenis_surat, $pasien)
    {
        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        // Ambil bulan dan tahun dari tanggal registrasi pasien
        $tglRegistrasi = \Carbon\Carbon::parse($pasien->tgl_registrasi);
        $bulan = $bulanRomawi[$tglRegistrasi->month];
        $tahun = $tglRegistrasi->year;

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
            'jenis_surat'    => $jenis_surat,
            'nomor_urut'     => $nomorUrut,
            'nomor_surat'    => $nomorSurat,
            'kode_rs'        => 'RSBW',
            'bulan'          => $bulan,
            'tahun'          => $tahun,
            'no_sep'         => $pasien->no_sep,
            'no_rawat'       => $pasien->no_rawat,
            'nm_pasien'      => $pasien->nm_pasien,
            'no_peserta'     => $pasien->no_peserta,
            'nm_poli'        => $pasien->nm_poli,
            'diagnosis'      => $pasien->diagnosis,
            'tgl_registrasi' => $pasien->tgl_registrasi,
        ]);

        return $nomorSurat;
    }


    /**
     * List pasien rawat jalan yang sudah dibuatkan surat
     */
    public function listSuratRj()
{
    $suratList = DB::table('reg_periksa')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('bridging_sep', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
        ->join('nomor_surat', 'bridging_sep.no_sep', '=', 'nomor_surat.no_sep')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->select(
            'nomor_surat.nomor_surat',
            'pasien.no_peserta',
            'pasien.nm_pasien',
            'bridging_sep.tglsep',
            'poliklinik.nm_poli',
            'bridging_sep.nmdiagnosaawal as diagnosis',
            'nomor_surat.no_sep',
            'reg_periksa.no_rawat as id'
        )
        ->where('nomor_surat.nomor_surat', 'like', 'RJ/%') // 🔹 filter hanya surat RJ
        ->orderByDesc('nomor_surat.id')
        ->get();

    return view('suratbiometrik.formulir.listsuratrj', compact('suratList'));
}


    /**
     * 🔹 Print surat biometrik → arahkan ke blade printsuratbiometrikrajal
     */
    public function print($id)
{
    $pasien = DB::table('bridging_sep')
        ->join('reg_periksa', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
        ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
        ->select(
            'reg_periksa.no_rawat as id',
            'pasien.nm_pasien as nama',
            'pasien.no_peserta as no_kartu_bpjs',
            'bridging_sep.no_sep',
            'bridging_sep.tglsep as tgl_masuk',
            DB::raw('MAX(kamar_inap.tgl_keluar) as tgl_pulang'), // ✅ kasih alias tgl_pulang
            'bridging_sep.nmdiagnosaawal as diagnosis',
            'dokter.nm_dokter as nama_dokter'
        )
        ->where('bridging_sep.jnspelayanan', '1') // Rawat Inap
        ->where('reg_periksa.no_rawat', $id)
        ->groupBy(
            'reg_periksa.no_rawat',
            'pasien.nm_pasien',
            'pasien.no_peserta',
            'bridging_sep.no_sep',
            'bridging_sep.tglsep',
            'bridging_sep.nmdiagnosaawal',
            'dokter.nm_dokter'
        )
        ->first();

    if (!$pasien) {
        return redirect()->back()->with('error', 'Data pasien tidak ditemukan.');
    }

    $nomorSurat = NomorSurat::where('no_sep', $pasien->no_sep)
        ->where('jenis_surat', 'RI')
        ->value('nomor_surat');

    return view('suratbiometrik.formulir.printsuratbiometrikranap', [
        'pasien'     => $pasien,
        'nomorSurat' => $nomorSurat,
    ]);
}

}
