<?php

namespace App\Services;

use App\Services\ValueENV;
use Illuminate\Support\Facades\DB;

class QueryResumeDll
{
    // 1 GET SEP
    static function getSEP($noRawat, $noSep)
    {
        return DB::table('bridging_sep')
            ->select(
                'bridging_sep.no_sep',
                'reg_periksa.no_reg',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'bridging_sep.no_rawat',
                'bridging_sep.tglsep',
                'bridging_sep.tglrujukan',
                'bridging_sep.no_rujukan',
                'bridging_sep.kdppkrujukan',
                'bridging_sep.nmppkrujukan',
                'bridging_sep.kdppkpelayanan',
                'bridging_sep.nmppkpelayanan',
                'bridging_sep.jnspelayanan',
                'bridging_sep.catatan',
                'bridging_sep.diagawal',
                'bridging_sep.nmdiagnosaawal',
                'bridging_sep.kdpolitujuan',
                'bridging_sep.nmpolitujuan',
                'bridging_sep.klsrawat',
                'bridging_sep.klsnaik',
                'bridging_sep.pembiayaan',
                'bridging_sep.pjnaikkelas',
                'bridging_sep.lakalantas',
                'bridging_sep.user',
                'bridging_sep.nomr',
                'bridging_sep.nama_pasien',
                'bridging_sep.tanggal_lahir',
                'bridging_sep.peserta',
                'bridging_sep.jkel',
                'bridging_sep.no_kartu',
                'bridging_sep.tglpulang',
                'bridging_sep.asal_rujukan',
                'bridging_sep.eksekutif',
                'bridging_sep.cob',
                'bridging_sep.notelep',
                'bridging_sep.katarak',
                'bridging_sep.tglkkl',
                'bridging_sep.keterangankkl',
                'bridging_sep.suplesi',
                'bridging_sep.no_sep_suplesi',
                'bridging_sep.kdprop',
                'bridging_sep.nmprop',
                'bridging_sep.kdkab',
                'bridging_sep.nmkab',
                'bridging_sep.kdkec',
                'bridging_sep.nmkec',
                'bridging_sep.noskdp',
                'bridging_sep.kddpjp',
                'bridging_sep.nmdpdjp',
                'bridging_sep.tujuankunjungan',
                'bridging_sep.flagprosedur',
                'bridging_sep.penunjang',
                'bridging_sep.asesmenpelayanan',
                'bridging_sep.kddpjplayanan',
                'bridging_sep.nmdpjplayanan',
                'penjab.png_jawab'
            )
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('bridging_sep.no_rawat', '=', $noRawat)
            ->where('bridging_sep.no_sep', '=', $noSep)
            ->first();
    }

    // 2 GET RESUME FISO
    static function  getResumeFiso($noRawat)
    {
        $dokter = ValueENV::getDokterFiso();
        $resumeFiso = DB::table('pemeriksaan_ralan')
            ->select(
                'pemeriksaan_ralan.no_rawat',
                'pemeriksaan_ralan.tgl_perawatan',
                'pemeriksaan_ralan.jam_rawat',
                'pemeriksaan_ralan.suhu_tubuh',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.respirasi',
                'pemeriksaan_ralan.tinggi',
                'pemeriksaan_ralan.berat',
                'pemeriksaan_ralan.spo2',
                'pemeriksaan_ralan.gcs',
                'pemeriksaan_ralan.kesadaran',
                'pemeriksaan_ralan.keluhan',
                'pemeriksaan_ralan.pemeriksaan',
                'pemeriksaan_ralan.alergi',
                'pemeriksaan_ralan.lingkar_perut',
                'pemeriksaan_ralan.rtl',
                'pemeriksaan_ralan.penilaian',
                'pemeriksaan_ralan.instruksi',
                'pemeriksaan_ralan.evaluasi',
                'pemeriksaan_ralan.nip',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_dokter',
                'reg_periksa.kd_poli',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'dokter.nm_dokter',
                'reg_periksa.tgl_registrasi'
            )
            ->join('reg_periksa', 'pemeriksaan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('pemeriksaan_ralan.no_rawat', '=', $noRawat)
            ->first();
        if ($resumeFiso) {
            $resumeFiso->dokter_fiso = $dokter; // Add 'dokter' value to the object
        }
        return $resumeFiso;
    }
    // 3 Get Resume Ranap
    static function getResumeRanap($noRawat)
    {
        $resumeRanap = DB::table('resume_pasien_ranap')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.umurdaftar',
                'reg_periksa.almt_pj',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.status_lanjut',
                'kamar_inap.kd_kamar',
                'kamar_inap.tgl_masuk',
                'kamar_inap.tgl_keluar',
                'pasien.nm_pasien',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'pasien.jk as jenis_kelamin',
                'pasien.pekerjaan',
                'dokter.nm_dokter',
                'resume_pasien_ranap.no_rawat',
                'resume_pasien_ranap.kd_dokter',
                'resume_pasien_ranap.diagnosa_awal',
                'resume_pasien_ranap.alasan',
                'resume_pasien_ranap.keluhan_utama',
                'resume_pasien_ranap.pemeriksaan_fisik',
                'resume_pasien_ranap.jalannya_penyakit',
                'resume_pasien_ranap.pemeriksaan_penunjang',
                'resume_pasien_ranap.hasil_laborat',
                'resume_pasien_ranap.tindakan_dan_operasi',
                'resume_pasien_ranap.obat_di_rs',
                'resume_pasien_ranap.diagnosa_utama',
                'resume_pasien_ranap.kd_diagnosa_utama',
                'resume_pasien_ranap.diagnosa_sekunder',
                'resume_pasien_ranap.kd_diagnosa_sekunder',
                'resume_pasien_ranap.diagnosa_sekunder2',
                'resume_pasien_ranap.kd_diagnosa_sekunder2',
                'resume_pasien_ranap.diagnosa_sekunder3',
                'resume_pasien_ranap.kd_diagnosa_sekunder3',
                'resume_pasien_ranap.diagnosa_sekunder4',
                'resume_pasien_ranap.kd_diagnosa_sekunder4',
                'resume_pasien_ranap.prosedur_utama',
                'resume_pasien_ranap.kd_prosedur_utama',
                'resume_pasien_ranap.prosedur_sekunder',
                'resume_pasien_ranap.kd_prosedur_sekunder',
                'resume_pasien_ranap.prosedur_sekunder2',
                'resume_pasien_ranap.kd_prosedur_sekunder2',
                'resume_pasien_ranap.prosedur_sekunder3',
                'resume_pasien_ranap.kd_prosedur_sekunder3',
                'resume_pasien_ranap.alergi',
                'resume_pasien_ranap.diet',
                'resume_pasien_ranap.lab_belum',
                'resume_pasien_ranap.edukasi',
                'resume_pasien_ranap.cara_keluar',
                'resume_pasien_ranap.ket_keluar',
                'resume_pasien_ranap.keadaan',
                'resume_pasien_ranap.ket_keadaan',
                'resume_pasien_ranap.dilanjutkan',
                'resume_pasien_ranap.ket_dilanjutkan',
                'resume_pasien_ranap.kontrol',
                'resume_pasien_ranap.obat_pulang'
            )
            ->join('reg_periksa', 'resume_pasien_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('dokter', 'resume_pasien_ranap.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('resume_pasien_ranap.no_rawat', '=', $noRawat)
            ->orderBy('reg_periksa.tgl_registrasi', 'asc')
            ->orderBy('reg_periksa.status_lanjut', 'asc')
            ->first();
        if ($resumeRanap) {
            $resumeRanap->dpjp_ranap = DB::table('dpjp_ranap')
                ->select('dpjp_ranap.kd_dokter', 'dokter.nm_dokter')
                ->join('dokter', 'dpjp_ranap.kd_dokter', '=', 'dokter.kd_dokter')
                ->where('dpjp_ranap.no_rawat', '=', $resumeRanap->no_rawat)
                ->where('dpjp_ranap.kd_dokter', '!=', $resumeRanap->kd_dokter)
                ->get();
        }
        return $resumeRanap;
    }

    // 4 Get Resume Ralan
    static function getResumeRalan($noRawat)
    {
        return DB::table('resume_pasien')
            ->select(
                'reg_periksa.tgl_registrasi',
                'poliklinik.nm_poli',
                'reg_periksa.almt_pj',
                'pasien.pekerjaan',
                'reg_periksa.umurdaftar',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.tmp_lahir',
                'pasien.tgl_lahir',
                'dokter.kd_dokter',
                'dokter.nm_dokter',
                'pasien.jk',
                'pasien.alamat',
                'pasien.umur',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'resume_pasien.no_rawat',
                'resume_pasien.kd_dokter',
                'resume_pasien.keluhan_utama',
                'resume_pasien.jalannya_penyakit',
                'resume_pasien.pemeriksaan_penunjang',
                'resume_pasien.hasil_laborat',
                'resume_pasien.diagnosa_utama',
                'resume_pasien.kd_diagnosa_utama',
                'resume_pasien.diagnosa_sekunder',
                'resume_pasien.kd_diagnosa_sekunder',
                'resume_pasien.diagnosa_sekunder2',
                'resume_pasien.kd_diagnosa_sekunder2',
                'resume_pasien.diagnosa_sekunder3',
                'resume_pasien.kd_diagnosa_sekunder3',
                'resume_pasien.diagnosa_sekunder4',
                'resume_pasien.kd_diagnosa_sekunder4',
                'resume_pasien.prosedur_utama',
                'resume_pasien.kd_prosedur_utama',
                'resume_pasien.prosedur_sekunder',
                'resume_pasien.kd_prosedur_sekunder',
                'resume_pasien.prosedur_sekunder2',
                'resume_pasien.kd_prosedur_sekunder2',
                'resume_pasien.prosedur_sekunder3',
                'resume_pasien.kd_prosedur_sekunder3',
                'resume_pasien.kondisi_pulang',
                'resume_pasien.obat_pulang'
            )
            ->join('reg_periksa', 'resume_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', function ($join) {
                $join->on('resume_pasien.kd_dokter', '=', 'dokter.kd_dokter')
                    ->on('reg_periksa.kd_dokter', '=', 'dokter.kd_dokter');
            })
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where('resume_pasien.no_rawat', '=', $noRawat)
            ->first();
    }

    // 5 Get Biling
    static function getBiling($noRawat)
    {
        return DB::table('billing')
            ->select(
                'billing.noindex',
                'billing.no_rawat',
                'billing.tgl_byr',
                'billing.no',
                'billing.nm_perawatan',
                'billing.pemisah',
                'billing.biaya',
                'billing.jumlah',
                'billing.tambahan',
                'billing.totalbiaya',
                'billing.status'
            )
            ->where('billing.no_rawat', '=', $noRawat)
            ->get();
    }

    // 6 Get Laborat
    static  function getLaborat($noRawat)
    {
        $getLaborat = DB::table('periksa_lab')
            ->select(
                'periksa_lab.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.alamat',
                'pasien.umur',
                'petugas.nama as nama_petugas',
                'petugas.nip',
                'periksa_lab.tgl_periksa',
                'periksa_lab.jam',
                'periksa_lab.dokter_perujuk',
                'periksa_lab.kd_dokter',
                'dokter.nm_dokter',
                'dokter_pj.nm_dokter as nm_dokter_pj',
                'penjab.png_jawab',
                'kamar_inap.kd_kamar',
                'kamar.kd_bangsal',
                'poliklinik.nm_poli',
                'bangsal.nm_bangsal'
            )
            ->join('reg_periksa', 'periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('petugas', 'periksa_lab.nip', '=', 'petugas.nip')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('dokter', 'periksa_lab.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('dokter as dokter_pj', 'periksa_lab.dokter_perujuk', '=', 'dokter_pj.kd_dokter')
            ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where('periksa_lab.kategori', '=', 'PK')
            ->where('periksa_lab.no_rawat', '=', $noRawat)
            ->groupBy('periksa_lab.no_rawat', 'periksa_lab.tgl_periksa', 'periksa_lab.jam')
            ->orderBy('periksa_lab.tgl_periksa', 'desc')
            ->orderBy('periksa_lab.jam', 'desc')
            ->get();
        $getLaborat->map(function ($periksa) {
            $periksa->getPeriksaLab = DB::table('periksa_lab')
                ->select('jns_perawatan_lab.kd_jenis_prw', 'jns_perawatan_lab.nm_perawatan', 'periksa_lab.biaya')
                ->join('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
                ->where([
                    ['periksa_lab.kategori', 'PK'],
                    ['periksa_lab.no_rawat', $periksa->no_rawat],
                    ['periksa_lab.tgl_periksa', $periksa->tgl_periksa],
                    ['periksa_lab.jam', $periksa->jam],
                ])
                ->orderBy('jns_perawatan_lab.kd_jenis_prw', 'asc')
                ->get();
            $periksa->getPeriksaLab->map(function ($detaillab) use ($periksa) {
                $detaillab->getDetailLab = DB::table('detail_periksa_lab')
                    ->select('detail_periksa_lab.no_rawat', 'detail_periksa_lab.tgl_periksa', 'template_laboratorium.Pemeriksaan', 'detail_periksa_lab.nilai', 'template_laboratorium.satuan', 'detail_periksa_lab.nilai_rujukan', 'detail_periksa_lab.biaya_item', 'detail_periksa_lab.keterangan', 'detail_periksa_lab.kd_jenis_prw')
                    ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template')
                    ->where([
                        ['detail_periksa_lab.kd_jenis_prw', $detaillab->kd_jenis_prw],
                        ['detail_periksa_lab.no_rawat', $periksa->no_rawat],
                        ['detail_periksa_lab.tgl_periksa', $periksa->tgl_periksa],
                        ['detail_periksa_lab.jam', $periksa->jam],
                    ])
                    ->orderBy('template_laboratorium.urut', 'asc')
                    ->get();
            });
        });
        return $getLaborat;
    }

    // 7 Get Radiologi
    static function getRadiologi($noRawat)
    {
        return DB::table('hasil_radiologi')
            ->select(
                'hasil_radiologi.no_rawat',
                'hasil_radiologi.tgl_periksa',
                'hasil_radiologi.jam',
                'hasil_radiologi.hasil',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_dokter',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.umur',
                'pasien.alamat',
                'dokter.nm_dokter',
                'kamar_inap.kd_kamar',
                'bangsal.nm_bangsal',
                'poliklinik.nm_poli',
                'periksa_radiologi.nip',
                'periksa_radiologi.kd_dokter as kd_dokter_pj',
                'dokter_pj.nm_dokter as nm_dokter_pj',
                'jns_perawatan_radiologi.nm_perawatan',
                'pegawai.nama as nama_pegawai'
            )
            ->join('reg_periksa', 'hasil_radiologi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('periksa_radiologi', function ($join) {
                $join->on('periksa_radiologi.no_rawat', '=', 'hasil_radiologi.no_rawat')
                    ->on('hasil_radiologi.jam', '=', 'periksa_radiologi.jam');
            })
            ->join('jns_perawatan_radiologi', 'periksa_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
            ->join('pegawai', 'periksa_radiologi.nip', '=', 'pegawai.nik')
            ->join('dokter AS dokter_pj', 'periksa_radiologi.kd_dokter', '=', 'dokter_pj.kd_dokter')
            ->where('hasil_radiologi.no_rawat', '=', $noRawat)
            ->orderBy('hasil_radiologi.tgl_periksa', 'asc')
            ->get();
    }

    // 8 Get Awal Medis
    static function getAwalMedis($noRawat)
    {
        return DB::table('penilaian_medis_igd')
            ->select(
                'penilaian_medis_igd.no_rawat',
                'pasien.nm_pasien',
                'pasien.tgl_lahir',
                'reg_periksa.no_rkm_medis',
                'dokter.nm_dokter',
                'pasien.jk',
                'penilaian_medis_igd.tanggal',
                'penilaian_medis_igd.kd_dokter',
                'penilaian_medis_igd.anamnesis',
                'penilaian_medis_igd.hubungan',
                'penilaian_medis_igd.keluhan_utama',
                'penilaian_medis_igd.rps',
                'penilaian_medis_igd.rpd',
                'penilaian_medis_igd.rpk',
                'penilaian_medis_igd.rpo',
                'penilaian_medis_igd.alergi',
                'penilaian_medis_igd.keadaan',
                'penilaian_medis_igd.gcs',
                'penilaian_medis_igd.kesadaran',
                'penilaian_medis_igd.td',
                'penilaian_medis_igd.nadi',
                'penilaian_medis_igd.rr',
                'penilaian_medis_igd.suhu',
                'penilaian_medis_igd.spo',
                'penilaian_medis_igd.bb',
                'penilaian_medis_igd.tb',
                'penilaian_medis_igd.kepala',
                'penilaian_medis_igd.mata',
                'penilaian_medis_igd.gigi',
                'penilaian_medis_igd.leher',
                'penilaian_medis_igd.thoraks',
                'penilaian_medis_igd.abdomen',
                'penilaian_medis_igd.genital',
                'penilaian_medis_igd.ekstremitas',
                'penilaian_medis_igd.ket_fisik',
                'penilaian_medis_igd.ket_lokalis',
                'penilaian_medis_igd.ekg',
                'penilaian_medis_igd.rad',
                'penilaian_medis_igd.lab',
                'penilaian_medis_igd.diagnosis',
                'penilaian_medis_igd.tata'
            )
            ->join('reg_periksa', 'penilaian_medis_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'penilaian_medis_igd.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('penilaian_medis_igd.no_rawat', '=', $noRawat)
            ->first();
    }

    // 9 Get Surat Kematian
    static function getSuratKematian($noRawat)
    {
        return DB::table('pasien_mati')
            ->select(
                'pasien_mati.tanggal',
                'pasien_mati.jam',
                'pasien_mati.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tmp_lahir',
                'pasien.tgl_lahir',
                'pasien.gol_darah',
                'pasien.stts_nikah',
                'pasien.umur',
                'pasien.alamat',
                'pasien.agama',
                'pasien_mati.keterangan',
                'pasien_mati.temp_meninggal',
                'pasien_mati.icd1',
                'pasien_mati.icd2',
                'pasien_mati.icd3',
                'pasien_mati.icd4',
                'pasien_mati.kd_dokter',
                'dokter.nm_dokter',
                'reg_periksa.no_rawat'
            )
            ->join('pasien', 'pasien_mati.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'pasien_mati.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('reg_periksa.no_rawat', '=', $noRawat)
            ->first();
    }

    // 10 Get Laporan Operasi RANAP
    static function getLaporanOprasi($noRawat, $status_lanjut)
    {
        $laporanOprasi =  DB::table('operasi')
            ->select(
                'operasi.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'operasi.tgl_operasi',
                'operasi.jenis_anasthesi',
                'operasi.kategori',
                'laporan_operasi.diagnosa_preop',
                'laporan_operasi.diagnosa_postop',
                'laporan_operasi.jaringan_dieksekusi',
                'laporan_operasi.selesaioperasi',
                'laporan_operasi.permintaan_pa',
                'laporan_operasi.laporan_operasi',
                'dokter1.nm_dokter AS operator1',
                'dokter1.kd_dokter AS kd_dokter1',
                'dokter2.nm_dokter AS operator2',
                'dokter3.nm_dokter AS operator3',
                'petugas1.nama AS asistenoperator1',
                'petugas2.nama AS asistenoperator2',
                'petugas3.nama AS asistenoperator3',
                'petugas4.nama AS instrumen',
                'dokter4.nm_dokter AS dokteranak',
                'petugas5.nama AS perawatresusitas',
                'dokter5.nm_dokter AS anastesi',
                'petugas6.nama AS asistenanastesi',
                'petugas7.nama AS asistenanastesi2',
                'petugas8.nama AS bidan1',
                'petugas9.nama AS bidan2',
                'petugas10.nama AS bidan3',
                'petugas11.nama AS perawatluar',
                'petugas12.nama AS omloop',
                'petugas13.nama AS omloop2',
                'petugas14.nama AS omloop3',
                'petugas15.nama AS omloop4',
                'petugas16.nama AS omloop5',
                'dokter6.nm_dokter AS pjanak',
                'dokter7.nm_dokter AS dokumum',
                'penilaian_awal_keperawatan_kebidanan_ranap.riwayat_persalinan_g',
                'penilaian_awal_keperawatan_kebidanan_ranap.riwayat_persalinan_p',
                'penilaian_awal_keperawatan_kebidanan_ranap.riwayat_persalinan_a',
                'penilaian_awal_keperawatan_kebidanan_ranap.riwayat_persalinan_hidup',
                DB::raw("(SELECT nm_bangsal FROM bangsal
                    INNER JOIN kamar ON bangsal.kd_bangsal=kamar.kd_bangsal
                    INNER JOIN kamar_inap ON kamar.kd_kamar=kamar_inap.kd_kamar
                    WHERE no_rawat='$noRawat'
                    ORDER BY tgl_masuk DESC LIMIT 1) AS nm_bangsal")
            )
            ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penilaian_awal_keperawatan_kebidanan_ranap', 'reg_periksa.no_rawat', '=', 'penilaian_awal_keperawatan_kebidanan_ranap.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('laporan_operasi', function ($join) {
                $join->on('operasi.no_rawat', '=', 'laporan_operasi.no_rawat')
                    ->on('operasi.tgl_operasi', '=', 'laporan_operasi.tanggal');
            })
            ->leftJoin('dokter AS dokter1', 'dokter1.kd_dokter', '=', 'operasi.operator1')
            ->leftJoin('dokter AS dokter2', 'dokter2.kd_dokter', '=', 'operasi.operator2')
            ->leftJoin('dokter AS dokter3', 'dokter3.kd_dokter', '=', 'operasi.operator3')
            ->leftJoin('petugas AS petugas1', 'petugas1.nip', '=', 'operasi.asisten_operator1')
            ->leftJoin('petugas AS petugas2', 'petugas2.nip', '=', 'operasi.asisten_operator2')
            ->leftJoin('petugas AS petugas3', 'petugas3.nip', '=', 'operasi.asisten_operator3')
            ->leftJoin('petugas AS petugas4', 'petugas4.nip', '=', 'operasi.instrumen')
            ->leftJoin('dokter AS dokter4', 'dokter4.kd_dokter', '=', 'operasi.dokter_anak')
            ->leftJoin('petugas AS petugas5', 'petugas5.nip', '=', 'operasi.perawaat_resusitas')
            ->leftJoin('dokter AS dokter5', 'dokter5.kd_dokter', '=', 'operasi.dokter_anestesi')
            ->leftJoin('petugas AS petugas6', 'petugas6.nip', '=', 'operasi.asisten_anestesi')
            ->leftJoin('petugas AS petugas7', 'petugas7.nip', '=', 'operasi.asisten_anestesi2')
            ->leftJoin('petugas AS petugas8', 'petugas8.nip', '=', 'operasi.bidan')
            ->leftJoin('petugas AS petugas9', 'petugas9.nip', '=', 'operasi.bidan2')
            ->leftJoin('petugas AS petugas10', 'petugas10.nip', '=', 'operasi.bidan3')
            ->leftJoin('petugas AS petugas11', 'petugas11.nip', '=', 'operasi.perawat_luar')
            ->leftJoin('petugas AS petugas12', 'petugas12.nip', '=', 'operasi.omloop')
            ->leftJoin('petugas AS petugas13', 'petugas13.nip', '=', 'operasi.omloop2')
            ->leftJoin('petugas AS petugas14', 'petugas14.nip', '=', 'operasi.omloop3')
            ->leftJoin('petugas AS petugas15', 'petugas15.nip', '=', 'operasi.omloop4')
            ->leftJoin('petugas AS petugas16', 'petugas16.nip', '=', 'operasi.omloop5')
            ->leftJoin('dokter AS dokter6', 'dokter6.kd_dokter', '=', 'operasi.dokter_pjanak')
            ->leftJoin('dokter AS dokter7', 'dokter7.kd_dokter', '=', 'operasi.dokter_umum')
            ->where('operasi.no_rawat', $noRawat)
            ->get();
        $laporanOprasi->map(function ($item) use ($status_lanjut) {
            if ($status_lanjut == 'Ranap') {
                $item->pemeriksaanRanap = DB::table('pemeriksaan_ranap')
                    ->select(
                        'pemeriksaan_ranap.no_rawat',
                        'pemeriksaan_ranap.tgl_perawatan',
                        'pemeriksaan_ranap.jam_rawat',
                        'pemeriksaan_ranap.suhu_tubuh',
                        'pemeriksaan_ranap.tensi',
                        'pemeriksaan_ranap.nadi',
                        'pemeriksaan_ranap.respirasi',
                        'pemeriksaan_ranap.tinggi',
                        'pemeriksaan_ranap.berat',
                        'pemeriksaan_ranap.gcs',
                        'pemeriksaan_ranap.keluhan',
                        'pemeriksaan_ranap.pemeriksaan',
                        'pemeriksaan_ranap.alergi',
                        'pemeriksaan_ranap.rtl',
                        'pemeriksaan_ranap.penilaian'
                    )
                    ->where('pemeriksaan_ranap.no_rawat', $item->no_rawat)
                    ->whereRaw("CONCAT(pemeriksaan_ranap.tgl_perawatan, ' ', pemeriksaan_ranap.jam_rawat) <= '$item->tgl_operasi'")
                    ->orderByDesc('pemeriksaan_ranap.tgl_perawatan')
                    ->orderByDesc('pemeriksaan_ranap.jam_rawat')
                    ->first();
            } else {
                $item->pemeriksaanRanap = DB::table('pemeriksaan_ralan')
                    ->select(
                        'pemeriksaan_ralan.no_rawat',
                        'pemeriksaan_ralan.tgl_perawatan',
                        'pemeriksaan_ralan.jam_rawat',
                        'pemeriksaan_ralan.suhu_tubuh',
                        'pemeriksaan_ralan.tensi',
                        'pemeriksaan_ralan.nadi',
                        'pemeriksaan_ralan.respirasi',
                        'pemeriksaan_ralan.tinggi',
                        'pemeriksaan_ralan.berat',
                        'pemeriksaan_ralan.gcs',
                        'pemeriksaan_ralan.keluhan',
                        'pemeriksaan_ralan.pemeriksaan',
                        'pemeriksaan_ralan.alergi',
                        'pemeriksaan_ralan.rtl',
                        'pemeriksaan_ralan.penilaian'
                    )
                    ->where('pemeriksaan_ralan.no_rawat', $item->no_rawat)
                    ->whereRaw("CONCAT(pemeriksaan_ralan.tgl_perawatan, ' ', pemeriksaan_ralan.jam_rawat) <= '$item->tgl_operasi'")
                    ->orderByDesc('pemeriksaan_ralan.tgl_perawatan')
                    ->orderByDesc('pemeriksaan_ralan.jam_rawat')
                    ->first();
            }
        });
        return $laporanOprasi;
    }

    // GET RESEP OBAT
    public  static  function getResepObat($no_rawat)
    {
        $getResepObat =  DB::table('resep_obat')
            ->select(
                'resep_obat.no_resep',
                'resep_obat.tgl_perawatan',
                'resep_obat.jam',
                'resep_obat.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'resep_obat.kd_dokter',
                'dokter.nm_dokter'
            )
            ->join('reg_periksa', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'resep_obat.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('resep_obat.no_rawat', $no_rawat)
            ->get();
        $getResepObat->map(function ($item) {
            // NON RACIk
            $item->ResepNonracik = DB::table('detail_pemberian_obat')
                ->select(
                    'databarang.kode_brng',
                    'databarang.nama_brng',
                    'detail_pemberian_obat.jml',
                    'detail_pemberian_obat.biaya_obat',
                    'detail_pemberian_obat.embalase',
                    'detail_pemberian_obat.tuslah',
                    'detail_pemberian_obat.total',
                    'kodesatuan.satuan',
                    'aturan_pakai.aturan'
                )
                ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
                ->join('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
                ->leftJoin('aturan_pakai', function ($join) {
                    $join->on('detail_pemberian_obat.tgl_perawatan', '=', 'aturan_pakai.tgl_perawatan')
                        ->on('detail_pemberian_obat.jam', '=', 'aturan_pakai.jam')
                        ->on('detail_pemberian_obat.no_rawat', '=', 'aturan_pakai.no_rawat')
                        ->on('detail_pemberian_obat.kode_brng', '=', 'aturan_pakai.kode_brng');
                })
                ->where('detail_pemberian_obat.tgl_perawatan', $item->tgl_perawatan)
                ->where('detail_pemberian_obat.jam', $item->jam)
                ->where('detail_pemberian_obat.no_rawat', $item->no_rawat)
                ->whereNotIn('databarang.kode_brng', function ($query) use ($item) {
                    $query->select('detail_obat_racikan.kode_brng')
                        ->from('detail_obat_racikan')
                        ->where('detail_obat_racikan.tgl_perawatan', $item->tgl_perawatan)
                        ->where('detail_obat_racikan.jam', $item->jam)
                        ->where('detail_obat_racikan.no_rawat',  $item->no_rawat);
                })
                ->orderBy('databarang.kode_brng')
                ->get();
            // RACIK
            $item->ResepRacik = DB::table('obat_racikan')
                ->select(
                    'obat_racikan.no_racik',
                    'obat_racikan.tgl_perawatan',
                    'obat_racikan.jam',
                    'obat_racikan.no_rawat',
                    'obat_racikan.nama_racik',
                    'obat_racikan.kd_racik',
                    'metode_racik.nm_racik as metode',
                    'obat_racikan.jml_dr',
                    'obat_racikan.aturan_pakai',
                    'obat_racikan.keterangan'
                )
                ->join('metode_racik', 'obat_racikan.kd_racik', '=', 'metode_racik.kd_racik')
                ->where('obat_racikan.tgl_perawatan', $item->tgl_perawatan)
                ->where('obat_racikan.jam', $item->jam)
                ->where('obat_racikan.no_rawat', $item->no_rawat)
                ->get();
            $item->ResepRacik->map(function ($detail) {
                $detail->detailResepRacik = DB::table('detail_pemberian_obat')
                    ->select(
                        'databarang.kode_brng',
                        'databarang.nama_brng',
                        'detail_pemberian_obat.jml',
                        'detail_pemberian_obat.biaya_obat',
                        'detail_pemberian_obat.embalase',
                        'detail_pemberian_obat.tuslah',
                        'detail_pemberian_obat.total'
                    )
                    ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
                    ->join('detail_obat_racikan', function ($join) {
                        $join->on('detail_pemberian_obat.kode_brng', '=', 'detail_obat_racikan.kode_brng')
                            ->on('detail_pemberian_obat.tgl_perawatan', '=', 'detail_obat_racikan.tgl_perawatan')
                            ->on('detail_pemberian_obat.jam', '=', 'detail_obat_racikan.jam')
                            ->on('detail_pemberian_obat.no_rawat', '=', 'detail_obat_racikan.no_rawat');
                    })
                    ->where('detail_pemberian_obat.tgl_perawatan', $detail->tgl_perawatan)
                    ->where('detail_pemberian_obat.jam', $detail->jam)
                    ->where('detail_pemberian_obat.no_rawat', $detail->no_rawat)
                    ->where('detail_obat_racikan.no_racik',  $detail->no_racik)
                    ->orderBy('databarang.kode_brng', 'asc')
                    ->get();
            });
        });
        return $getResepObat;
    }

    // GET SOAPIE RALAN
    public  static function getSoapieRalan($noRawat)
    {
        $soapiePasien = DB::table('pemeriksaan_ralan')
            ->select(
                'pemeriksaan_ralan.no_rawat',
                'pemeriksaan_ralan.tgl_perawatan',
                'pemeriksaan_ralan.jam_rawat',
                'pemeriksaan_ralan.suhu_tubuh',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.respirasi',
                'pemeriksaan_ralan.tinggi',
                'pemeriksaan_ralan.berat',
                'pemeriksaan_ralan.spo2',
                'pemeriksaan_ralan.gcs',
                'pemeriksaan_ralan.kesadaran',
                'pemeriksaan_ralan.keluhan',
                'pemeriksaan_ralan.pemeriksaan',
                'pemeriksaan_ralan.alergi',
                'pemeriksaan_ralan.lingkar_perut',
                'pemeriksaan_ralan.rtl',
                'pemeriksaan_ralan.penilaian',
                'pemeriksaan_ralan.instruksi',
                'pemeriksaan_ralan.evaluasi',
                'pemeriksaan_ralan.nip',
                'dokter.nm_dokter',
                'dokter.kd_dokter',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.tmp_lahir',
                'pasien.tgl_lahir',
                'pasien.nm_ibu',
                'pasien.jk',
                'pasien.alamat',
                'pasien.gol_darah',
                'pasien.stts_nikah',
                'pasien.agama',
                'pasien.cacat_fisik'
            )
            ->join('dokter', 'pemeriksaan_ralan.nip', '=', 'dokter.kd_dokter')
            ->join('reg_periksa', 'pemeriksaan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('pemeriksaan_ralan.no_rawat', $noRawat)
            ->orderBy('pemeriksaan_ralan.tgl_perawatan', 'asc')
            ->orderBy('pemeriksaan_ralan.jam_rawat', 'asc')
            ->get();
        $soapiePasien->map(function ($item) {
            $item->getDiagnosa = DB::table('diagnosa_pasien')
                ->select('penyakit.nm_penyakit', 'diagnosa_pasien.kd_penyakit', 'diagnosa_pasien.prioritas')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $item->no_rawat)
                ->orderBy('diagnosa_pasien.prioritas', 'asc')
                ->get();
            $item->getProcedure = DB::table('prosedur_pasien')
                ->select('icd9.deskripsi_pendek', 'prosedur_pasien.kode', 'prosedur_pasien.prioritas')
                ->join('icd9', 'prosedur_pasien.kode', '=', 'icd9.kode')
                ->where('prosedur_pasien.no_rawat', $item->no_rawat)
                ->orderBy('prosedur_pasien.prioritas', 'asc')
                ->get();
        });
        return $soapiePasien;
    }
    // GET SOAPIE RANAP
    public  static function getSoapieRanap($noRawat)
    {
        $soapiePasien = DB::table('pemeriksaan_ranap')
            ->select(
                'pemeriksaan_ranap.tgl_perawatan',
                'pemeriksaan_ranap.jam_rawat',
                'pemeriksaan_ranap.suhu_tubuh',
                'pemeriksaan_ranap.tensi',
                'pemeriksaan_ranap.nadi',
                'pemeriksaan_ranap.respirasi',
                'pemeriksaan_ranap.tinggi',
                'pemeriksaan_ranap.berat',
                'pemeriksaan_ranap.spo2',
                'pemeriksaan_ranap.gcs',
                'pemeriksaan_ranap.kesadaran',
                'pemeriksaan_ranap.keluhan',
                'pemeriksaan_ranap.pemeriksaan',
                'pemeriksaan_ranap.alergi',
                'pemeriksaan_ranap.penilaian',
                'pemeriksaan_ranap.rtl',
                'pemeriksaan_ranap.instruksi',
                'pemeriksaan_ranap.evaluasi',
                'pemeriksaan_ranap.nip',
                'dokter.nm_dokter',
                'dokter.kd_dokter',
                'pasien.nm_pasien',
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.alamat',
                'pasien.jk',
                'pasien.tmp_lahir',
                'pasien.tgl_lahir',
                'pasien.nm_ibu',
                'pasien.gol_darah',
                'pasien.stts_nikah',
                'pasien.agama',
                'pasien.cacat_fisik'
            )
            ->join('dokter', 'pemeriksaan_ranap.nip', '=', 'dokter.kd_dokter')
            ->join('reg_periksa', 'pemeriksaan_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('pemeriksaan_ranap.no_rawat', $noRawat)
            ->orderBy('pemeriksaan_ranap.tgl_perawatan', 'asc')
            ->orderBy('pemeriksaan_ranap.jam_rawat', 'asc')
            ->take(1)
            ->get();
        $soapiePasien->map(function ($item) {
            $item->getDiagnosa = DB::table('diagnosa_pasien')
                ->select('penyakit.nm_penyakit', 'diagnosa_pasien.kd_penyakit', 'diagnosa_pasien.prioritas')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $item->no_rawat)
                ->orderBy('diagnosa_pasien.prioritas', 'asc')
                ->get();
            $item->getProcedure = DB::table('prosedur_pasien')
                ->select('icd9.deskripsi_pendek', 'prosedur_pasien.kode', 'prosedur_pasien.prioritas')
                ->join('icd9', 'prosedur_pasien.kode', '=', 'icd9.kode')
                ->where('prosedur_pasien.no_rawat', $item->no_rawat)
                ->orderBy('prosedur_pasien.prioritas', 'asc')
                ->get();
        });
        return $soapiePasien;
    }

    // GET TRIASE IGD
    public static function getTriaseIGD($noRawat)
    {

        $getTriaseIGD = DB::table('data_triase_igd')
            ->select(
                'data_triase_igd.no_rawat',
                'data_triase_igd.tgl_kunjungan',
                'data_triase_igd.cara_masuk',
                'data_triase_igd.alat_transportasi',
                'data_triase_igd.alasan_kedatangan',
                'data_triase_igd.keterangan_kedatangan',
                'data_triase_igd.kode_kasus',
                'data_triase_igd.tekanan_darah',
                'data_triase_igd.nadi',
                'data_triase_igd.pernapasan',
                'data_triase_igd.suhu',
                'data_triase_igd.saturasi_o2',
                'data_triase_igd.nyeri',
                'master_triase_macam_kasus.macam_kasus',
                'pasien.nm_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'reg_periksa.no_rkm_medis'
            )
            ->join('master_triase_macam_kasus', 'data_triase_igd.kode_kasus', '=', 'master_triase_macam_kasus.kode_kasus')
            ->join('reg_periksa', 'data_triase_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('data_triase_igd.no_rawat', $noRawat)
            ->get();
        $getTriaseIGD->map(function ($item) {
            $item->triaseSekender = DB::table('data_triase_igdsekunder')
                ->select(
                    'data_triase_igdsekunder.no_rawat',
                    'data_triase_igdsekunder.anamnesa_singkat as keluhan_utama',
                    DB::raw("'-' as kebutuhan_khusus"),
                    'data_triase_igdsekunder.catatan',
                    'data_triase_igdsekunder.plan',
                    'data_triase_igdsekunder.tanggaltriase',
                    'data_triase_igdsekunder.nik',
                    'pegawai.nama'
                )
                ->join('pegawai', 'data_triase_igdsekunder.nik', '=', 'pegawai.nik')
                ->where('data_triase_igdsekunder.no_rawat', $item->no_rawat)
                ->get();
            $item->triasePrimer = DB::table('data_triase_igdprimer')
                ->select(
                    'data_triase_igdprimer.no_rawat',
                    'data_triase_igdprimer.keluhan_utama',
                    'data_triase_igdprimer.kebutuhan_khusus',
                    'data_triase_igdprimer.catatan',
                    'data_triase_igdprimer.plan',
                    'data_triase_igdprimer.tanggaltriase',
                    'data_triase_igdprimer.nik',
                    'pegawai.nama'
                )
                ->join('pegawai', 'data_triase_igdprimer.nik', '=', 'pegawai.nik')
                ->where('data_triase_igdprimer.no_rawat', $item->no_rawat)
                ->get();
            $scales = [1, 2, 3, 4, 5];
            foreach ($scales as $scale) {
                $item->{'data_triase_igddetail_skala' . $scale} = DB::table('data_triase_igddetail_skala' . $scale)
                    ->select('master_triase_skala' . $scale . '.pengkajian_skala' . $scale, 'master_triase_pemeriksaan.nama_pemeriksaan')
                    ->join('master_triase_skala' . $scale, 'data_triase_igddetail_skala' . $scale . '.kode_skala' . $scale, '=', 'master_triase_skala' . $scale . '.kode_skala' . $scale)
                    ->join('master_triase_pemeriksaan', 'master_triase_skala' . $scale . '.kode_pemeriksaan', '=', 'master_triase_pemeriksaan.kode_pemeriksaan')
                    ->where('data_triase_igddetail_skala' . $scale . '.no_rawat', $item->no_rawat)
                    ->get();
            }
        });

        return $getTriaseIGD;
    }

    public static function suratPriBpjs($noRawat)
    {
        return DB::table('bridging_surat_pri_bpjs')
            ->select(
                'bridging_surat_pri_bpjs.no_surat',
                'bridging_surat_pri_bpjs.tgl_surat',
                'bridging_surat_pri_bpjs.nm_dokter_bpjs',
                'bridging_surat_pri_bpjs.nm_poli_bpjs',
                'pasien.no_peserta',
                'pasien.jk',
                'pasien.nm_pasien',
                'pasien.tgl_lahir',
                'bridging_surat_pri_bpjs.diagnosa',
                'reg_periksa.kd_dokter'
            )
            ->join('reg_periksa', 'bridging_surat_pri_bpjs.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('bridging_surat_pri_bpjs.no_rawat', '=', $noRawat)
            ->first();
    }
}
