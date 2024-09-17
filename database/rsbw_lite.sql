-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Sep 2024 pada 03.36
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rsbw_lite`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_borlos`
--

CREATE TABLE `bw_borlos` (
  `id_ruangan` int(122) NOT NULL,
  `ruangan` varchar(122) NOT NULL,
  `jml_bed` int(122) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_crosscheck_coding`
--

CREATE TABLE `bw_crosscheck_coding` (
  `no_rawat` varchar(50) NOT NULL,
  `tanggal_pengerjaan` varchar(50) NOT NULL,
  `coder` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_display_bad`
--

CREATE TABLE `bw_display_bad` (
  `id` varchar(50) NOT NULL,
  `ruangan` varchar(50) NOT NULL,
  `kamar` varchar(55) NOT NULL,
  `bad` varchar(11) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `status` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_display_poli`
--

CREATE TABLE `bw_display_poli` (
  `kd_display` varchar(50) NOT NULL,
  `nama_display` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_file_casemix_hasil`
--

CREATE TABLE `bw_file_casemix_hasil` (
  `no_rawat` varchar(50) NOT NULL,
  `no_rkm_medis` varchar(20) NOT NULL,
  `file` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_file_casemix_inacbg`
--

CREATE TABLE `bw_file_casemix_inacbg` (
  `no_rawat` varchar(50) NOT NULL,
  `no_rkm_medis` varchar(20) NOT NULL,
  `file` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_file_casemix_remusedll`
--

CREATE TABLE `bw_file_casemix_remusedll` (
  `no_rawat` varchar(50) NOT NULL,
  `no_rkm_medis` varchar(20) NOT NULL,
  `file` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_file_casemix_scan`
--

CREATE TABLE `bw_file_casemix_scan` (
  `no_rawat` varchar(50) NOT NULL,
  `no_rkm_medis` varchar(20) NOT NULL,
  `file` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_invoice_asuransi`
--

CREATE TABLE `bw_invoice_asuransi` (
  `nomor_tagihan` varchar(50) NOT NULL,
  `kode_asuransi` varchar(100) NOT NULL,
  `nama_asuransi` varchar(100) DEFAULT NULL,
  `alamat_asuransi` varchar(150) DEFAULT NULL,
  `tanggl1` varchar(15) NOT NULL,
  `tanggl2` varchar(15) NOT NULL,
  `tgl_cetak` varchar(15) NOT NULL,
  `status_lanjut` varchar(15) NOT NULL,
  `lamiran` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_jadwal_dokter`
--

CREATE TABLE `bw_jadwal_dokter` (
  `kd_dokter` varchar(30) NOT NULL,
  `hari_kerja` varchar(30) NOT NULL,
  `jam_mulai` varchar(30) NOT NULL,
  `jam_selesai` varchar(30) NOT NULL,
  `kd_poli` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_jenis_lookbook`
--

CREATE TABLE `bw_jenis_lookbook` (
  `kd_jesni_lb` varchar(15) NOT NULL,
  `nama_jenis_lb` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_jenis_lookbook_kegiatan_lain`
--

CREATE TABLE `bw_jenis_lookbook_kegiatan_lain` (
  `id_kegiatan` varchar(50) NOT NULL,
  `nama_kegiatan` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_jns_kegiatan_karu`
--

CREATE TABLE `bw_jns_kegiatan_karu` (
  `kd_jns_kegiatan_karu` varchar(20) NOT NULL,
  `nm_jenis_kegiatan` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_karyawan`
--

CREATE TABLE `bw_karyawan` (
  `nip` varchar(30) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `jabatan` varchar(30) NOT NULL,
  `masa_kerja` varchar(10) NOT NULL,
  `golongan` varchar(10) NOT NULL,
  `jenis_karyawan` varchar(20) NOT NULL,
  `status` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_kewenangankhusus_keperawatan`
--

CREATE TABLE `bw_kewenangankhusus_keperawatan` (
  `kd_kewenangan` varchar(50) NOT NULL,
  `nama_kewenangan` varchar(250) NOT NULL,
  `kd_jesni_lb` varchar(50) NOT NULL,
  `default_mandiri` varchar(20) NOT NULL,
  `default_supervisi` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_logbook_karu`
--

CREATE TABLE `bw_logbook_karu` (
  `id_logbook` int(20) NOT NULL,
  `kd_kegiatan` varchar(20) NOT NULL,
  `user` varchar(20) NOT NULL,
  `mandiri` varchar(5) NOT NULL,
  `supervisi` varchar(5) NOT NULL,
  `tanggal` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_logbook_keperawatan`
--

CREATE TABLE `bw_logbook_keperawatan` (
  `id_logbook` int(50) NOT NULL,
  `kd_kegiatan` varchar(50) NOT NULL,
  `user` varchar(50) NOT NULL,
  `no_rkm_medis` varchar(50) NOT NULL,
  `mandiri` varchar(50) NOT NULL,
  `supervisi` varchar(50) NOT NULL,
  `tanggal` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_logbook_keperawatan_kegiatanlain`
--

CREATE TABLE `bw_logbook_keperawatan_kegiatanlain` (
  `id_kegiatan_keperawatanlain` int(50) NOT NULL,
  `id_kegiatan` varchar(50) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text NOT NULL,
  `user` varchar(50) NOT NULL,
  `mandiri` varchar(10) NOT NULL,
  `supervisi` varchar(10) NOT NULL,
  `tanggal` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_logbook_keperawatan_kewenangankhusus`
--

CREATE TABLE `bw_logbook_keperawatan_kewenangankhusus` (
  `id_kewenangankhusus` int(11) NOT NULL,
  `kd_kewenangan` varchar(50) NOT NULL,
  `user` varchar(50) NOT NULL,
  `no_rkm_medis` varchar(50) NOT NULL,
  `mandiri` varchar(50) NOT NULL,
  `supervisi` varchar(50) NOT NULL,
  `tanggal` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_log_antrian_poli`
--

CREATE TABLE `bw_log_antrian_poli` (
  `no_rawat` varchar(50) NOT NULL,
  `kd_ruang_poli` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_maping_asuransi`
--

CREATE TABLE `bw_maping_asuransi` (
  `kd_pj` varchar(15) NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat_asuransi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_nm_kegiatan_karu`
--

CREATE TABLE `bw_nm_kegiatan_karu` (
  `kd_kegiatan` varchar(20) NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `kd_jns_kegiatan_karu` varchar(20) NOT NULL,
  `default_mandiri` varchar(12) NOT NULL,
  `default_supervisi` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_nm_kegiatan_keperawatan`
--

CREATE TABLE `bw_nm_kegiatan_keperawatan` (
  `kd_kegiatan` varchar(122) NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `kd_jesni_lb` varchar(15) NOT NULL,
  `default_mandiri` varchar(12) NOT NULL,
  `default_supervisi` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_peserta_asuransi`
--

CREATE TABLE `bw_peserta_asuransi` (
  `no_rkm_medis` varchar(20) NOT NULL,
  `nomor_kartu` varchar(50) NOT NULL,
  `nomor_klaim` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_ruangpoli_dokter`
--

CREATE TABLE `bw_ruangpoli_dokter` (
  `kd_dokter` varchar(50) NOT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `kd_ruang_poli` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_ruang_poli`
--

CREATE TABLE `bw_ruang_poli` (
  `kd_ruang_poli` varchar(50) NOT NULL,
  `nama_ruang_poli` varchar(50) NOT NULL,
  `kd_display` varchar(50) NOT NULL,
  `posisi_display_poli` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_setting_bundling`
--

CREATE TABLE `bw_setting_bundling` (
  `id` varchar(20) NOT NULL,
  `nama_berkas` varchar(100) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `urutan` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bw_setting_bundling`
--

INSERT INTO `bw_setting_bundling` (`id`, `nama_berkas`, `status`, `urutan`) VALUES
('BUN001', 'berkas-sep', '1', 1),
('BUN002', 'resume-pasien', '1', 2),
('BUN003', 'rincian-biaya', '1', 3),
('BUN004', 'berkas-laborat', '1', 5),
('BUN005', 'berkas-radiologi', '1', 4),
('BUN006', 'awal-medis', '1', 6),
('BUN007', 'surat-kematian', '1', 8),
('BUN008', 'berkas-laporan-operasi', '1', 7),
('BUN009', 'soapie-pasien', '1', 9),
('BUN010', 'hasil-pemeriksaan-ekg', '1', 10),
('BUN011', 'berkas-triase-igdsekunder', '1', 11);

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_skala_upah`
--

CREATE TABLE `bw_skala_upah` (
  `id_skala_upah` varchar(20) NOT NULL,
  `jenis_karyawan` varchar(20) NOT NULL,
  `golongan` varchar(20) NOT NULL,
  `masa_kerja` varchar(10) NOT NULL,
  `jumlah_upah` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_test_cekin`
--

CREATE TABLE `bw_test_cekin` (
  `kode_booking` varchar(20) NOT NULL,
  `task_id` varchar(12) NOT NULL,
  `jam` varchar(14) NOT NULL,
  `timestamp_sec` varchar(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bw_validasi_icare`
--

CREATE TABLE `bw_validasi_icare` (
  `no_rawat` varchar(20) NOT NULL,
  `kd_dokter_bpjs` varchar(20) NOT NULL,
  `status` enum('1','0') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `file_farmasi`
--

CREATE TABLE `file_farmasi` (
  `id` int(10) NOT NULL,
  `no_rkm_medis` varchar(50) NOT NULL,
  `no_rawat` varchar(50) NOT NULL,
  `nama_pasein` varchar(100) NOT NULL,
  `jenis_berkas` varchar(50) NOT NULL,
  `file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `list_dokter`
--

CREATE TABLE `list_dokter` (
  `kd_dokter` varchar(255) NOT NULL,
  `nama_dokter` varchar(255) NOT NULL,
  `kd_loket` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `kuota_tambahan` varchar(50) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_antrian_loket`
--

CREATE TABLE `log_antrian_loket` (
  `no_rawat` varchar(255) NOT NULL,
  `kd_loket` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bw_borlos`
--
ALTER TABLE `bw_borlos`
  ADD PRIMARY KEY (`id_ruangan`);

--
-- Indeks untuk tabel `bw_crosscheck_coding`
--
ALTER TABLE `bw_crosscheck_coding`
  ADD PRIMARY KEY (`no_rawat`);

--
-- Indeks untuk tabel `bw_display_bad`
--
ALTER TABLE `bw_display_bad`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `bw_display_poli`
--
ALTER TABLE `bw_display_poli`
  ADD PRIMARY KEY (`kd_display`);

--
-- Indeks untuk tabel `bw_file_casemix_hasil`
--
ALTER TABLE `bw_file_casemix_hasil`
  ADD PRIMARY KEY (`no_rawat`);

--
-- Indeks untuk tabel `bw_file_casemix_inacbg`
--
ALTER TABLE `bw_file_casemix_inacbg`
  ADD PRIMARY KEY (`no_rawat`);

--
-- Indeks untuk tabel `bw_file_casemix_remusedll`
--
ALTER TABLE `bw_file_casemix_remusedll`
  ADD PRIMARY KEY (`no_rawat`);

--
-- Indeks untuk tabel `bw_file_casemix_scan`
--
ALTER TABLE `bw_file_casemix_scan`
  ADD PRIMARY KEY (`no_rawat`);

--
-- Indeks untuk tabel `bw_invoice_asuransi`
--
ALTER TABLE `bw_invoice_asuransi`
  ADD PRIMARY KEY (`nomor_tagihan`);

--
-- Indeks untuk tabel `bw_jadwal_dokter`
--
ALTER TABLE `bw_jadwal_dokter`
  ADD PRIMARY KEY (`kd_dokter`);

--
-- Indeks untuk tabel `bw_jenis_lookbook`
--
ALTER TABLE `bw_jenis_lookbook`
  ADD PRIMARY KEY (`kd_jesni_lb`);

--
-- Indeks untuk tabel `bw_jenis_lookbook_kegiatan_lain`
--
ALTER TABLE `bw_jenis_lookbook_kegiatan_lain`
  ADD PRIMARY KEY (`id_kegiatan`);

--
-- Indeks untuk tabel `bw_jns_kegiatan_karu`
--
ALTER TABLE `bw_jns_kegiatan_karu`
  ADD PRIMARY KEY (`kd_jns_kegiatan_karu`);

--
-- Indeks untuk tabel `bw_karyawan`
--
ALTER TABLE `bw_karyawan`
  ADD PRIMARY KEY (`nip`);

--
-- Indeks untuk tabel `bw_kewenangankhusus_keperawatan`
--
ALTER TABLE `bw_kewenangankhusus_keperawatan`
  ADD PRIMARY KEY (`kd_kewenangan`),
  ADD KEY `kd_jesni_lb_2` (`kd_jesni_lb`);

--
-- Indeks untuk tabel `bw_logbook_karu`
--
ALTER TABLE `bw_logbook_karu`
  ADD PRIMARY KEY (`id_logbook`);

--
-- Indeks untuk tabel `bw_logbook_keperawatan`
--
ALTER TABLE `bw_logbook_keperawatan`
  ADD PRIMARY KEY (`id_logbook`);

--
-- Indeks untuk tabel `bw_logbook_keperawatan_kegiatanlain`
--
ALTER TABLE `bw_logbook_keperawatan_kegiatanlain`
  ADD PRIMARY KEY (`id_kegiatan_keperawatanlain`);

--
-- Indeks untuk tabel `bw_logbook_keperawatan_kewenangankhusus`
--
ALTER TABLE `bw_logbook_keperawatan_kewenangankhusus`
  ADD PRIMARY KEY (`id_kewenangankhusus`);

--
-- Indeks untuk tabel `bw_log_antrian_poli`
--
ALTER TABLE `bw_log_antrian_poli`
  ADD PRIMARY KEY (`no_rawat`);

--
-- Indeks untuk tabel `bw_maping_asuransi`
--
ALTER TABLE `bw_maping_asuransi`
  ADD PRIMARY KEY (`kd_pj`);

--
-- Indeks untuk tabel `bw_nm_kegiatan_karu`
--
ALTER TABLE `bw_nm_kegiatan_karu`
  ADD PRIMARY KEY (`kd_kegiatan`);

--
-- Indeks untuk tabel `bw_nm_kegiatan_keperawatan`
--
ALTER TABLE `bw_nm_kegiatan_keperawatan`
  ADD PRIMARY KEY (`kd_kegiatan`),
  ADD KEY `kd_jesni_lb` (`kd_jesni_lb`);

--
-- Indeks untuk tabel `bw_peserta_asuransi`
--
ALTER TABLE `bw_peserta_asuransi`
  ADD PRIMARY KEY (`no_rkm_medis`);

--
-- Indeks untuk tabel `bw_ruangpoli_dokter`
--
ALTER TABLE `bw_ruangpoli_dokter`
  ADD PRIMARY KEY (`kd_dokter`);

--
-- Indeks untuk tabel `bw_ruang_poli`
--
ALTER TABLE `bw_ruang_poli`
  ADD PRIMARY KEY (`kd_ruang_poli`);

--
-- Indeks untuk tabel `bw_setting_bundling`
--
ALTER TABLE `bw_setting_bundling`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `bw_skala_upah`
--
ALTER TABLE `bw_skala_upah`
  ADD PRIMARY KEY (`id_skala_upah`);

--
-- Indeks untuk tabel `bw_test_cekin`
--
ALTER TABLE `bw_test_cekin`
  ADD PRIMARY KEY (`kode_booking`);

--
-- Indeks untuk tabel `bw_validasi_icare`
--
ALTER TABLE `bw_validasi_icare`
  ADD PRIMARY KEY (`no_rawat`);

--
-- Indeks untuk tabel `file_farmasi`
--
ALTER TABLE `file_farmasi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `list_dokter`
--
ALTER TABLE `list_dokter`
  ADD PRIMARY KEY (`kd_dokter`) USING BTREE,
  ADD KEY `list_dokter_kd_loket_foreign` (`kd_loket`) USING BTREE;

--
-- Indeks untuk tabel `log_antrian_loket`
--
ALTER TABLE `log_antrian_loket`
  ADD PRIMARY KEY (`no_rawat`),
  ADD KEY `log_antrian_loket_kd_loket_foreign` (`kd_loket`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bw_borlos`
--
ALTER TABLE `bw_borlos`
  MODIFY `id_ruangan` int(122) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `bw_logbook_karu`
--
ALTER TABLE `bw_logbook_karu`
  MODIFY `id_logbook` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `bw_logbook_keperawatan`
--
ALTER TABLE `bw_logbook_keperawatan`
  MODIFY `id_logbook` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `bw_logbook_keperawatan_kegiatanlain`
--
ALTER TABLE `bw_logbook_keperawatan_kegiatanlain`
  MODIFY `id_kegiatan_keperawatanlain` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `bw_logbook_keperawatan_kewenangankhusus`
--
ALTER TABLE `bw_logbook_keperawatan_kewenangankhusus`
  MODIFY `id_kewenangankhusus` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `file_farmasi`
--
ALTER TABLE `file_farmasi`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bw_kewenangankhusus_keperawatan`
--
ALTER TABLE `bw_kewenangankhusus_keperawatan`
  ADD CONSTRAINT `bw_kewenangankhusus_keperawatan_ibfk_1` FOREIGN KEY (`kd_jesni_lb`) REFERENCES `bw_jenis_lookbook` (`kd_jesni_lb`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ketidakleluasaan untuk tabel `bw_nm_kegiatan_keperawatan`
--
ALTER TABLE `bw_nm_kegiatan_keperawatan`
  ADD CONSTRAINT `bw_nm_kegiatan_keperawatan_ibfk_1` FOREIGN KEY (`kd_jesni_lb`) REFERENCES `bw_jenis_lookbook` (`kd_jesni_lb`);

--
-- Ketidakleluasaan untuk tabel `list_dokter`
--
ALTER TABLE `list_dokter`
  ADD CONSTRAINT `list_dokter_kd_loket_foreign` FOREIGN KEY (`kd_loket`) REFERENCES `loket` (`kd_loket`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `log_antrian_loket`
--
ALTER TABLE `log_antrian_loket`
  ADD CONSTRAINT `log_antrian_loket_kd_loket_foreign` FOREIGN KEY (`kd_loket`) REFERENCES `loket` (`kd_loket`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
