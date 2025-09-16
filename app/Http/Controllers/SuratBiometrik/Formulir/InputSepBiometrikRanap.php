<?php

namespace App\Http\Controllers\SuratBiometrik\Formulir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NomorSurat;
use Carbon\Carbon;

class InputSepBiometrikRanap extends Controller
{
    /**
     * Form input SEP Ranap
     */
    public function create()
    {
        return view('suratbiometrik.formulir.inputsepbiometrikranap');
    }

    /**
     * Simpan banyak nomor SEP sekaligus → auto create surat biometrik Ranap
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
                ->where('bridging_sep.jnspelayanan', '1') // Rawat inap
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
                $failedList[] = "SEP <b>{$no_sep}</b> tidak ditemukan atau bukan rawat inap.";
                continue;
            }

            $nomorSurat = $this->generateAndSaveNomorSurat('RI', $pasien); // RI = Ranap
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

        // Ambil bulan & tahun dari tanggal registrasi pasien
        $tglRegistrasi = Carbon::parse($pasien->tgl_registrasi);
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
     * List pasien rawat inap yang sudah dibuatkan surat
     */
    public function listSuratRi()
{
    $suratList = DB::table('reg_periksa')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('bridging_sep', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
        ->join('nomor_surat', 'bridging_sep.no_sep', '=', 'nomor_surat.no_sep')
        ->join('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat') // ✅ ambil tanggal masuk/keluar
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->select(
            'nomor_surat.nomor_surat',
            'pasien.no_peserta',
            'pasien.nm_pasien',
            'bridging_sep.tglsep',
            'poliklinik.nm_poli as ruangan',
            'bridging_sep.nmdiagnosaawal as diagnosis',
            'nomor_surat.no_sep',
            'reg_periksa.no_rawat as id',
            'kamar_inap.tgl_masuk',
            'kamar_inap.tgl_keluar'
        )
        ->where('bridging_sep.jnspelayanan', '1') // hanya rawat inap
        ->orderByDesc('nomor_surat.id')
        ->get();

    return view('suratbiometrik.formulir.listsuratri', compact('suratList'));
}
    /**
     * 🔹 Print surat biometrik Ranap
     */
   public function print($id)
{
    $pasien = DB::table('bridging_sep')
        ->join('reg_periksa', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
        ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
        ->select(
            'reg_periksa.no_rawat as id',
            'pasien.nm_pasien as nama',
            'pasien.no_peserta as no_kartu_bpjs',
            'bridging_sep.no_sep',
            'bridging_sep.jnspelayanan',
            'bridging_sep.nmdiagnosaawal as diagnosis',
            'dokter.nm_dokter as nama_dokter',
            DB::raw('MIN(kamar_inap.tgl_masuk) as tgl_masuk'),
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
        ->where('bridging_sep.jnspelayanan', '1') // hanya rawat inap
        ->where('reg_periksa.no_rawat', $id)
        ->groupBy(
            'reg_periksa.no_rawat',
            'pasien.nm_pasien',
            'pasien.no_peserta',
            'bridging_sep.no_sep',
            'bridging_sep.jnspelayanan',
            'bridging_sep.nmdiagnosaawal',
            'dokter.nm_dokter'
        )
        ->first();

    if (!$pasien) {
        return redirect()->back()->with('error', 'Data pasien tidak ditemukan.');
    }

    $pasien->tgl_pulang = $pasien->tgl_pulang ?: null;

    $nomorSurat = NomorSurat::where('no_sep', $pasien->no_sep)
        ->where('jenis_surat', 'RI')
        ->value('nomor_surat');

    return view('suratbiometrik.formulir.printsuratbiometrikranap', [
        'pasien'     => $pasien,
        'nomorSurat' => $nomorSurat,
    ]);
}

}
