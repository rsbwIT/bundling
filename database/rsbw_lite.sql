-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2024 at 08:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

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
-- Table structure for table `bw_display_bad`
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
-- Table structure for table `bw_display_poli`
--

CREATE TABLE `bw_display_poli` (
  `kd_display` varchar(50) NOT NULL,
  `nama_display` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bw_file_casemix_hasil`
--

CREATE TABLE `bw_file_casemix_hasil` (
  `no_rawat` varchar(50) NOT NULL,
  `no_rkm_medis` varchar(20) NOT NULL,
  `file` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bw_file_casemix_inacbg`
--

CREATE TABLE `bw_file_casemix_inacbg` (
  `no_rawat` varchar(50) NOT NULL,
  `no_rkm_medis` varchar(20) NOT NULL,
  `file` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bw_file_casemix_remusedll`
--

CREATE TABLE `bw_file_casemix_remusedll` (
  `no_rawat` varchar(50) NOT NULL,
  `no_rkm_medis` varchar(20) NOT NULL,
  `file` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bw_file_casemix_scan`
--

CREATE TABLE `bw_file_casemix_scan` (
  `no_rawat` varchar(50) NOT NULL,
  `no_rkm_medis` varchar(20) NOT NULL,
  `file` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bw_jadwal_dokter`
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
-- Table structure for table `bw_jenis_lookbook`
--

CREATE TABLE `bw_jenis_lookbook` (
  `kd_jesni_lb` varchar(15) NOT NULL,
  `nama_jenis_lb` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bw_jenis_lookbook_kegiatan_lain`
--

CREATE TABLE `bw_jenis_lookbook_kegiatan_lain` (
  `id_kegiatan` varchar(50) NOT NULL,
  `nama_kegiatan` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bw_jns_kegiatan_karu`
--

CREATE TABLE `bw_jns_kegiatan_karu` (
  `kd_jns_kegiatan_karu` varchar(20) NOT NULL,
  `nm_jenis_kegiatan` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bw_karyawan`
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
-- Table structure for table `bw_kewenangankhusus_keperawatan`
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
-- Table structure for table `bw_logbook_karu`
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
-- Table structure for table `bw_logbook_keperawatan`
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
-- Table structure for table `bw_logbook_keperawatan_kegiatanlain`
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
-- Table structure for table `bw_logbook_keperawatan_kewenangankhusus`
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
-- Table structure for table `bw_log_antrian_poli`
--

CREATE TABLE `bw_log_antrian_poli` (
  `no_rawat` varchar(50) NOT NULL,
  `kd_ruang_poli` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bw_nm_kegiatan_karu`
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
-- Table structure for table `bw_nm_kegiatan_keperawatan`
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
-- Table structure for table `bw_ruangpoli_dokter`
--

CREATE TABLE `bw_ruangpoli_dokter` (
  `kd_dokter` varchar(50) NOT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `kd_ruang_poli` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bw_ruang_poli`
--

CREATE TABLE `bw_ruang_poli` (
  `kd_ruang_poli` varchar(50) NOT NULL,
  `nama_ruang_poli` varchar(50) NOT NULL,
  `kd_display` varchar(50) NOT NULL,
  `posisi_display_poli` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bw_skala_upah`
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
-- Table structure for table `bw_test_cekin`
--

CREATE TABLE `bw_test_cekin` (
  `kode_booking` varchar(20) NOT NULL,
  `task_id` varchar(12) NOT NULL,
  `jam` varchar(14) NOT NULL,
  `timestamp_sec` varchar(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `list_dokter`
--

CREATE TABLE `list_dokter` (
  `kd_dokter` varchar(255) NOT NULL,
  `nama_dokter` varchar(255) NOT NULL,
  `kd_loket` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loket`
--

CREATE TABLE `loket` (
  `kd_loket` varchar(255) NOT NULL,
  `nama_loket` varchar(255) NOT NULL,
  `kd_pendaftaran` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bw_display_bad`
--
ALTER TABLE `bw_display_bad`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `bw_display_poli`
--
ALTER TABLE `bw_display_poli`
  ADD PRIMARY KEY (`kd_display`);

--
-- Indexes for table `bw_file_casemix_hasil`
--
ALTER TABLE `bw_file_casemix_hasil`
  ADD PRIMARY KEY (`no_rawat`);

--
-- Indexes for table `bw_file_casemix_inacbg`
--
ALTER TABLE `bw_file_casemix_inacbg`
  ADD PRIMARY KEY (`no_rawat`);

--
-- Indexes for table `bw_file_casemix_remusedll`
--
ALTER TABLE `bw_file_casemix_remusedll`
  ADD PRIMARY KEY (`no_rawat`);

--
-- Indexes for table `bw_file_casemix_scan`
--
ALTER TABLE `bw_file_casemix_scan`
  ADD PRIMARY KEY (`no_rawat`);

--
-- Indexes for table `bw_jadwal_dokter`
--
ALTER TABLE `bw_jadwal_dokter`
  ADD PRIMARY KEY (`kd_dokter`);

--
-- Indexes for table `bw_jenis_lookbook`
--
ALTER TABLE `bw_jenis_lookbook`
  ADD PRIMARY KEY (`kd_jesni_lb`);

--
-- Indexes for table `bw_jenis_lookbook_kegiatan_lain`
--
ALTER TABLE `bw_jenis_lookbook_kegiatan_lain`
  ADD PRIMARY KEY (`id_kegiatan`);

--
-- Indexes for table `bw_jns_kegiatan_karu`
--
ALTER TABLE `bw_jns_kegiatan_karu`
  ADD PRIMARY KEY (`kd_jns_kegiatan_karu`);

--
-- Indexes for table `bw_karyawan`
--
ALTER TABLE `bw_karyawan`
  ADD PRIMARY KEY (`nip`);

--
-- Indexes for table `bw_kewenangankhusus_keperawatan`
--
ALTER TABLE `bw_kewenangankhusus_keperawatan`
  ADD PRIMARY KEY (`kd_kewenangan`),
  ADD KEY `kd_jesni_lb_2` (`kd_jesni_lb`);

--
-- Indexes for table `bw_logbook_karu`
--
ALTER TABLE `bw_logbook_karu`
  ADD PRIMARY KEY (`id_logbook`);

--
-- Indexes for table `bw_logbook_keperawatan`
--
ALTER TABLE `bw_logbook_keperawatan`
  ADD PRIMARY KEY (`id_logbook`);

--
-- Indexes for table `bw_logbook_keperawatan_kegiatanlain`
--
ALTER TABLE `bw_logbook_keperawatan_kegiatanlain`
  ADD PRIMARY KEY (`id_kegiatan_keperawatanlain`);

--
-- Indexes for table `bw_logbook_keperawatan_kewenangankhusus`
--
ALTER TABLE `bw_logbook_keperawatan_kewenangankhusus`
  ADD PRIMARY KEY (`id_kewenangankhusus`);

--
-- Indexes for table `bw_log_antrian_poli`
--
ALTER TABLE `bw_log_antrian_poli`
  ADD PRIMARY KEY (`no_rawat`);

--
-- Indexes for table `bw_nm_kegiatan_karu`
--
ALTER TABLE `bw_nm_kegiatan_karu`
  ADD PRIMARY KEY (`kd_kegiatan`);

--
-- Indexes for table `bw_nm_kegiatan_keperawatan`
--
ALTER TABLE `bw_nm_kegiatan_keperawatan`
  ADD PRIMARY KEY (`kd_kegiatan`),
  ADD KEY `kd_jesni_lb` (`kd_jesni_lb`);

--
-- Indexes for table `bw_ruangpoli_dokter`
--
ALTER TABLE `bw_ruangpoli_dokter`
  ADD PRIMARY KEY (`kd_dokter`);

--
-- Indexes for table `bw_ruang_poli`
--
ALTER TABLE `bw_ruang_poli`
  ADD PRIMARY KEY (`kd_ruang_poli`);

--
-- Indexes for table `bw_skala_upah`
--
ALTER TABLE `bw_skala_upah`
  ADD PRIMARY KEY (`id_skala_upah`);

--
-- Indexes for table `bw_test_cekin`
--
ALTER TABLE `bw_test_cekin`
  ADD PRIMARY KEY (`kode_booking`);

--
-- Indexes for table `list_dokter`
--
ALTER TABLE `list_dokter`
  ADD PRIMARY KEY (`kd_dokter`),
  ADD KEY `list_dokter_kd_loket_foreign` (`kd_loket`);

--
-- Indexes for table `loket`
--
ALTER TABLE `loket`
  ADD PRIMARY KEY (`kd_loket`),
  ADD KEY `loket_kd_pendaftaran_foreign` (`kd_pendaftaran`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bw_logbook_karu`
--
ALTER TABLE `bw_logbook_karu`
  MODIFY `id_logbook` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bw_logbook_keperawatan`
--
ALTER TABLE `bw_logbook_keperawatan`
  MODIFY `id_logbook` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bw_logbook_keperawatan_kegiatanlain`
--
ALTER TABLE `bw_logbook_keperawatan_kegiatanlain`
  MODIFY `id_kegiatan_keperawatanlain` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bw_logbook_keperawatan_kewenangankhusus`
--
ALTER TABLE `bw_logbook_keperawatan_kewenangankhusus`
  MODIFY `id_kewenangankhusus` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bw_kewenangankhusus_keperawatan`
--
ALTER TABLE `bw_kewenangankhusus_keperawatan`
  ADD CONSTRAINT `bw_kewenangankhusus_keperawatan_ibfk_1` FOREIGN KEY (`kd_jesni_lb`) REFERENCES `bw_jenis_lookbook` (`kd_jesni_lb`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `bw_nm_kegiatan_keperawatan`
--
ALTER TABLE `bw_nm_kegiatan_keperawatan`
  ADD CONSTRAINT `bw_nm_kegiatan_keperawatan_ibfk_1` FOREIGN KEY (`kd_jesni_lb`) REFERENCES `bw_jenis_lookbook` (`kd_jesni_lb`);

--
-- Constraints for table `list_dokter`
--
ALTER TABLE `list_dokter`
  ADD CONSTRAINT `list_dokter_kd_loket_foreign` FOREIGN KEY (`kd_loket`) REFERENCES `loket` (`kd_loket`) ON DELETE CASCADE;

--
-- Constraints for table `loket`
--
ALTER TABLE `loket`
  ADD CONSTRAINT `loket_kd_pendaftaran_foreign` FOREIGN KEY (`kd_pendaftaran`) REFERENCES `pendaftaran` (`kd_pendaftaran`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
