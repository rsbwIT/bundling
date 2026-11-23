<?php

namespace App\Http\Controllers\PasienKamarInap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengkajianGangguanEmosionalController extends Controller
{
    /**
     * Tampilkan Form Input / Edit Pengkajian Gangguan Emosional
     */
    public function form($noRawatFormatted)
    {
        // Format kembali no_rawat jika dikirim tanpa slash atau dengan slash
        $noRawat = str_replace('-', '/', $noRawatFormatted);

        // Ambil data pasien rawat inap
        $pasien = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kamar_inap', function($join) {
                $join->on('reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
                    ->where('kamar_inap.tgl_keluar', '=', '0000-00-00');
            })
            ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->leftJoin('dpjp_ranap', 'reg_periksa.no_rawat', '=', 'dpjp_ranap.no_rawat')
            ->leftJoin('dokter', 'dpjp_ranap.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->select([
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'penjab.png_jawab',
                'kamar.kd_kamar',
                'bangsal.nm_bangsal',
                'kamar.kelas',
                'dokter.kd_dokter',
                'dokter.nm_dokter as nama_dokter_dpjp'
            ])
            ->where('reg_periksa.no_rawat', $noRawat)
            ->first();

        if (!$pasien) {
            abort(404, 'Data Pasien Rawat Inap tidak ditemukan.');
        }

        // Ambil data pengkajian yang sudah ada jika pernah diisi
        $pengkajian = DB::table('bw_pengkajian_gangguan_emosional')
            ->where('no_rawat', $noRawat)
            ->first();

        // Decode JSON fields jika ada
        if ($pengkajian) {
            $pengkajian->predisposisi_trauma_masa_lalu = json_decode($pengkajian->predisposisi_trauma_masa_lalu ?? '[]', true) ?: [];
            $pengkajian->mental_persepsi = json_decode($pengkajian->mental_persepsi ?? '[]', true) ?: [];
            $pengkajian->masalah_keperawatan = json_decode($pengkajian->masalah_keperawatan ?? '[]', true) ?: [];
        }

        // Daftar Dokter & Petugas untuk dropdown
        $dokterList = DB::table('dokter')->where('status', '1')->orderBy('nm_dokter')->get();
        $petugasList = DB::table('petugas')->where('status', '1')->orderBy('nama')->get();

        return view('pasienkamarinap.pengkajian_emosional.form', compact('pasien', 'pengkajian', 'dokterList', 'petugasList', 'noRawatFormatted'));
    }

    /**
     * Simpan Data Pengkajian Gangguan Emosional
     */
    public function store(Request $request)
    {
        $noRawat = $request->input('no_rawat');

        $traumaMasaLalu = $request->input('predisposisi_trauma_masa_lalu', []);
        $mentalPersepsi = $request->input('mental_persepsi', []);
        $masalahKeperawatan = $request->input('masalah_keperawatan', []);

        $namaPetugas = null;
        if ($request->filled('nip_petugas')) {
            $petugas = DB::table('petugas')->where('nip', $request->input('nip_petugas'))->first();
            $namaPetugas = $petugas ? $petugas->nama : null;
        }

        $namaDokter = null;
        if ($request->filled('kd_dokter_dpjp')) {
            $dokter = DB::table('dokter')->where('kd_dokter', $request->input('kd_dokter_dpjp'))->first();
            $namaDokter = $dokter ? $dokter->nm_dokter : null;
        }

        DB::table('bw_pengkajian_gangguan_emosional')->updateOrInsert(
            ['no_rawat' => $noRawat],
            [
                'tanggal'                           => $request->input('tanggal') ? Carbon::parse($request->input('tanggal')) : now(),
                'informasi_dari'                    => $request->input('informasi_dari'),
                'alasan_masuk'                      => $request->input('alasan_masuk'),
                'predisposisi_riwayat_jiwa_lalu'    => $request->input('predisposisi_riwayat_jiwa_lalu'),
                'predisposisi_pengobatan_sebelumnya'=> $request->input('predisposisi_pengobatan_sebelumnya'),
                'predisposisi_trauma_masa_lalu'     => json_encode($traumaMasaLalu),
                'predisposisi_trauma_lain_ket'      => $request->input('predisposisi_trauma_lain_ket'),
                'predisposisi_riwayat_keluarga'     => $request->input('predisposisi_riwayat_keluarga'),
                'predisposisi_pengalaman_buruk'     => $request->input('predisposisi_pengalaman_buruk'),
                'sosial_orang_berarti'              => $request->input('sosial_orang_berarti'),
                'sosial_hambatan_berhubungan'       => $request->input('sosial_hambatan_berhubungan'),
                'spiritual_nilai_keyakinan'         => $request->input('spiritual_nilai_keyakinan'),
                'spiritual_kegiatan_ibadah'         => $request->input('spiritual_kegiatan_ibadah'),
                'mental_kesadaran'                  => $request->input('mental_kesadaran'),
                'mental_penampilan'                 => $request->input('mental_penampilan'),
                'mental_penampilan_ket'             => $request->input('mental_penampilan_ket'),
                'mental_pembicaraan'                => $request->input('mental_pembicaraan'),
                'mental_pembicaraan_ket'            => $request->input('mental_pembicaraan_ket'),
                'mental_persepsi'                   => json_encode($mentalPersepsi),
                'mental_alam_perasaan'              => $request->input('mental_alam_perasaan'),
                'mental_alam_perasaan_ket'          => $request->input('mental_alam_perasaan_ket'),
                'masalah_keperawatan'               => json_encode($masalahKeperawatan),
                'masalah_keperawatan_lain_ket'      => $request->input('masalah_keperawatan_lain_ket'),
                'rencana_keperawatan'               => $request->input('rencana_keperawatan'),
                'nip_petugas'                       => $request->input('nip_petugas'),
                'nama_petugas'                      => $namaPetugas,
                'tgl_pengkajian_petugas'            => $request->input('tgl_pengkajian_petugas') ? Carbon::parse($request->input('tgl_pengkajian_petugas')) : now(),
                'kd_dokter_dpjp'                    => $request->input('kd_dokter_dpjp'),
                'nama_dokter_dpjp'                  => $namaDokter,
                'tgl_verifikasi_dpjp'               => $request->input('tgl_verifikasi_dpjp') ? Carbon::parse($request->input('tgl_verifikasi_dpjp')) : null,
                'updated_at'                        => now()
            ]
        );

        return redirect()->back()->with('success', 'Formulir Pengkajian Gangguan Emosional atau Psikiatri berhasil disimpan!');
    }

    /**
     * Cetak Lembar Formulir Pengkajian (Format persis seperti PDF)
     */
    public function cetak($noRawatFormatted)
    {
        $noRawat = str_replace('-', '/', $noRawatFormatted);

        $setting = DB::table('setting')->first();

        $pasien = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kamar_inap', function($join) {
                $join->on('reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat');
            })
            ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->leftJoin('dpjp_ranap', 'reg_periksa.no_rawat', '=', 'dpjp_ranap.no_rawat')
            ->leftJoin('dokter', 'dpjp_ranap.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->select([
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'penjab.png_jawab',
                'kamar.kd_kamar',
                'bangsal.nm_bangsal',
                'kamar.kelas',
                'dokter.nm_dokter'
            ])
            ->where('reg_periksa.no_rawat', $noRawat)
            ->orderBy('kamar_inap.tgl_masuk', 'desc')
            ->first();

        if (!$pasien) {
            abort(404, 'Data Pasien tidak ditemukan.');
        }

        $pengkajian = DB::table('bw_pengkajian_gangguan_emosional')
            ->where('no_rawat', $noRawat)
            ->first();

        if ($pengkajian) {
            $pengkajian->predisposisi_trauma_masa_lalu = json_decode($pengkajian->predisposisi_trauma_masa_lalu ?? '[]', true) ?: [];
            $pengkajian->mental_persepsi = json_decode($pengkajian->mental_persepsi ?? '[]', true) ?: [];
            $pengkajian->masalah_keperawatan = json_decode($pengkajian->masalah_keperawatan ?? '[]', true) ?: [];
        }

        return view('pasienkamarinap.pengkajian_emosional.cetak', compact('setting', 'pasien', 'pengkajian'));
    }
}
