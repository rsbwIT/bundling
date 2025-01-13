/*
 Navicat Premium Data Transfer

 Source Server         : localhot
 Source Server Type    : MySQL
 Source Server Version : 100432 (10.4.32-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : rsbw_lite

 Target Server Type    : MySQL
 Target Server Version : 100432 (10.4.32-MariaDB)
 File Encoding         : 65001

 Date: 10/01/2025 11:01:51
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for bw_borlos
-- ----------------------------
DROP TABLE IF EXISTS `bw_borlos`;
CREATE TABLE `bw_borlos`  (
  `id_ruangan` int NOT NULL AUTO_INCREMENT,
  `ruangan` varchar(122) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jml_bed` int NOT NULL,
  PRIMARY KEY (`id_ruangan`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_borlos
-- ----------------------------

-- ----------------------------
-- Table structure for bw_crosscheck_coding
-- ----------------------------
DROP TABLE IF EXISTS `bw_crosscheck_coding`;
CREATE TABLE `bw_crosscheck_coding`  (
  `no_rawat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_pengerjaan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `coder` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`no_rawat`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_crosscheck_coding
-- ----------------------------

-- ----------------------------
-- Table structure for bw_display_bad
-- ----------------------------
DROP TABLE IF EXISTS `bw_display_bad`;
CREATE TABLE `bw_display_bad`  (
  `id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kd_ruang` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ruangan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kamar` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bad` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kelas` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kd_kelas_bpjs` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nm_ruangan_bpjs` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `times_update` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_display_bad
-- ----------------------------
INSERT INTO `bw_display_bad` VALUES ('DIS024', 'KEBD', 'Kebidanan/Melati', '203', 'A', 'VIP', '0', 'VIP', 'Melati', '2024-12-19 08:13:57');
INSERT INTO `bw_display_bad` VALUES ('DIS025', 'KEBD', 'Kebidanan/Melati', '204', 'A', 'VIP', '0', 'VIP', 'Melati', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS026', 'KEBD', 'Kebidanan/Melati', '205', 'A', 'VIP', '0', 'VIP', 'Melati', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS027', 'KEBD', 'Kebidanan/Melati', '206', 'A', 'Kelas 2', '1', 'KL2', 'Melati', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS028', 'KEBD', 'Kebidanan/Melati', '206', 'B', 'Kelas 2', '1', 'KL2', 'Melati', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS029', 'KEBD', 'Kebidanan/Melati', '207', 'A', 'VIP', '0', 'VIP', 'Melati', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS046', 'AGRK', 'Anggrek', '301', 'A', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS047', 'AGRK', 'Anggrek', '301', 'B', 'Kelas 3', '0', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS048', 'AGRK', 'Anggrek', '302', 'A', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS049', 'AGRK', 'Anggrek', '302', 'B', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS050', 'AGRK', 'Anggrek', '303', 'A', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS051', 'AGRK', 'Anggrek', '303', 'B', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS052', 'AGRK', 'Anggrek', '304', 'A', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS053', 'AGRK', 'Anggrek', '304', 'B', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS054', 'AGRK', 'Anggrek', '305', 'A', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS055', 'AGRK', 'Anggrek', '305', 'B', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS056', 'AGRK', 'Anggrek', '306', 'A', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS057', 'AGRK', 'Anggrek', '306', 'B', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS058', 'AGRK', 'Anggrek', '307', 'A', 'VIP', '0', 'VIP', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS059', 'AGRK', 'Anggrek', '308', 'A', 'Kelas 2', '1', 'KL2', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS060', 'AGRK', 'Anggrek', '309', 'A', 'Kelas 2', '1', 'KL2', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS061', 'AGRK', 'Anggrek', '310', 'A', 'Kelas 2', '1', 'KL2', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS062', 'AGRK', 'Anggrek', '311', 'A', 'Kelas 2', '1', 'KL2', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS063', 'ISO', 'Anggrek', '312', 'A', 'Non Kelas', '0', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS064', 'ISO', 'Anggrek', '312', 'A', 'Non Kelas', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS065', 'AGRK', 'Anggrek', '313', 'A', 'VIP', '0', 'VIP', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS066', 'AGRK', 'Anggrek', '315', 'A', 'Kelas 3', '0', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS067', 'AGRK', 'Anggrek', '315', 'B', 'Kelas 3', '0', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS068', 'AGRK', 'Anggrek', '316', 'A', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS069', 'AGRK', 'Anggrek', '316', 'B', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS070', 'AGRK', 'Anggrek', '317', 'A', 'Kelas 3', '0', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS071', 'AGRK', 'Anggrek', '317', 'B', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS072', 'AGRK', 'Anggrek', '318', 'A', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS073', 'AGRK', 'Anggrek', '318', 'B', 'Kelas 3', '1', 'KL3', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS074', 'ISO', 'Anggrek', '319', 'A', 'Non Kelas', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS075', 'ISO', 'Anggrek', '319', 'B', 'Non Kelas', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS076', 'ISO', 'Anggrek', '320', 'A', 'Non Kelas', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS077', 'ISO', 'Anggrek', '320', 'B', 'Non Kelas', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS078', 'AGRK', 'Anggrek', '321', 'A', 'VIP', '0', 'VIP', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS079', 'AGRK', 'Anggrek', '322', 'A', 'VIP', '1', 'VIP', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS080', 'AGRK', 'Anggrek', '323', 'A', 'VIP', '0', 'VIP', 'Anggrek', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS098', 'GRD3', 'Garuda 3', '324', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS099', 'GRD3', 'Garuda 3', '324', 'B', 'Kelas 1', '0', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS100', 'GRD3', 'Garuda 3', '333', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS101', 'GRD3', 'Garuda 3', '333', 'B', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS102', 'GRD3', 'Garuda 3', '342', 'A', 'Kelas 1', '0', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS103', 'GRD3', 'Garuda 3', '342', 'B', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS105', 'GRD3', 'Garuda 3', '325', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS106', 'GRD3', 'Garuda 3', '325', 'B', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS107', 'GRD3', 'Garuda 3', '334', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS108', 'GRD3', 'Garuda 3', '334', 'B', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS110', 'GRD3', 'Garuda 3', '326', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS111', 'GRD3', 'Garuda 3', '326', 'B', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS112', 'GRD3', 'Garuda 3', '335', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS113', 'GRD3', 'Garuda 3', '335', 'B', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS114', 'GRD3', 'Garuda 3', '327', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS115', 'GRD3', 'Garuda 3', '327', 'B', 'Kelas 1', '0', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS116', 'GRD3', 'Garuda 3', '328', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS117', 'GRD3', 'Garuda 3', '328', 'B', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS118', 'ISO', 'Garuda 3', '329', 'A', 'Non Kelas', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS119', 'ISO', 'Garuda 3', '329', 'B', 'Non Kelas', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS120', 'ISO', 'Garuda 3', '330', 'A', 'Non Kelas', '0', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS121', 'ISO', 'Garuda 3', '330', 'B', 'Non Kelas', '0', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS122', 'ISO', 'Garuda 3', '331', 'A', 'Utama', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS123', 'ISO', 'Garuda 3', '331', 'B', 'Utama', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS124', 'ISO', 'Garuda 3', '331', 'C', 'Utama', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS125', 'GRD3', 'Garuda 3', '332', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS126', 'GRD3', 'Garuda 3', '332', 'B', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS127', 'GRD3', 'Garuda 3', '336', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS128', 'GRD3', 'Garuda 3', '336', 'B', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS129', 'GRD3', 'Garuda 3', '337', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS130', 'GRD3', 'Garuda 3', '337', 'B', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS131', 'GRD3', 'Garuda 3', '338', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS132', 'GRD3', 'Garuda 3', '338', 'B', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS133', 'GRD3', 'Garuda 3', '339', 'A', 'Kelas 1', '0', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS134', 'GRD3', 'Garuda 3', '339', 'B', 'Kelas 1', '0', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS135', 'GRD3', 'Garuda 3', '340', 'A', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS136', 'GRD3', 'Garuda 3', '340', 'B', 'Kelas 1', '1', 'KL1', 'Garuda 3', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS137', 'ISO', 'Garuda 3', '341', 'A', 'Non Kelas', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS138', 'ISO', 'Garuda 3', '341', 'B', 'Non Kelas', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS139', 'ISO', 'Garuda 3', '341', 'C', 'Non Kelas', '1', 'ISO', 'Isolasi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS140', 'GRD2', 'Garuda 2', '211', 'A', 'Junior Suite', '0', 'VVP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS141', 'GRD2', 'Garuda 2', '212', 'A', 'Junior Suite', '0', 'VVP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS142', 'GRD2', 'Garuda 2', '213', 'A', 'Junior Suite', '1', 'VVP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS143', 'GRD2', 'Garuda 2', '214', 'A', 'Junior Suite', '0', 'VVP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS144', 'GRD2', 'Garuda 2', '215', 'A', 'Junior Suite', '0', 'VVP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS145', 'GRD2', 'Garuda 2', '216', 'A', 'Junior Suite', '1', 'VVP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS146', 'GRD2', 'Garuda 2', '217', 'A', 'Junior Suite', '0', 'VVP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS147', 'GRD2', 'Garuda 2', '220', 'A', 'Deluxe', '1', 'VVP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS148', 'GRD2', 'Garuda 2', '222', 'A', 'Deluxe', '0', 'VVP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS149', 'GRD2', 'Garuda 2', '224', 'A', 'Deluxe', '1', 'VVP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS150', 'GRD2', 'Garuda 2', '226', 'A', 'Deluxe', '1', 'VVP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS151', 'GRD2', 'Garuda 2', '228', 'A', 'Kelas 2', '1', 'KL2', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS152', 'GRD2', 'Garuda 2', '228', 'B', 'Kelas 2', '1', 'KL2', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS156', 'INTS', 'Intensif', 'PERINA', 'A', 'Non Kelas', '1', 'SAL', 'Perinatologi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS157', 'INTS', 'Intensif', 'PERINA', 'B', 'Non Kelas', '0', 'SAL', 'Perinatologi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS158', 'INTS', 'Intensif', 'PERINA', 'C', 'Non Kelas', '0', 'SAL', 'Perinatologi', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS159', 'INTS', 'Intensif', 'N ICU', 'A', 'Non Kelas', '0', 'NIC', 'NICU Non Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS160', 'INTS', 'Intensif', 'P ICU', 'A', 'Non Kelas', '0', 'PIC', 'PICU Non Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS161', 'INTS', 'Intensif', 'Mawar', 'A', 'Non Kelas', '1', 'ICU', 'ICU Non Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS162', 'INTS', 'Intensif', 'P ICU', 'C', 'Non Kelas', '0', 'PIC', 'PICU Non Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS163', 'INTS', 'Intensif', 'P ICU', 'D', 'Non Kelas', '0', 'PIC', 'PICU Non Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS164', 'INTS', 'Intensif', 'P ICU', 'E', 'Non Kelas', '0', 'PIC', 'PICU Non Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS165', 'INTSNV', 'Intensif', 'Flamboyan', 'A', 'Non Kelas', '0', 'ICU', 'ICU Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS166', 'INTSNV', 'Intensif', 'Flamboyan', 'B', 'Non Kelas', '0', 'ICU', 'ICU Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS167', 'INTSNV', 'Intensif', 'Flamboyan', 'C', 'Non Kelas', '0', 'ICU', 'ICU Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS168', 'INTSNV', 'Intensif', 'Mawar', 'A', 'Non Kelas', '0', 'ICU', 'ICU Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS169', 'INTSNV', 'Intensif', 'Mawar', 'B', 'Non Kelas', '0', 'ICU', 'ICU Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS170', 'INTSNV', 'Intensif', 'Mawar', 'C', 'Non Kelas', '0', 'ICU', 'ICU Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS171', 'INTS', 'Intensif', 'Mawar', 'B', 'Non Kelas', '0', 'ICU', 'ICU Non Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS172', 'INTS', 'Garuda 2', '218', 'A', 'Kelas 2', '1', 'ICU', 'ICU Non Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS173', 'INTS', 'Garuda 2', '218', 'B', 'Kelas 2', '1', 'ICU', 'ICU Non Ventilator', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS175', 'GRD2', 'Garuda 2', '219', 'A', 'VIP', '1', 'VIP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS176', 'GRD2', 'Garuda 2', '221', 'A', 'VIP', '1', 'VIP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS177', 'GRD2', 'Garuda 2', '225', 'A', 'VIP', '1', 'VIP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS178', 'GRD2', 'Garuda 2', '227', 'A', 'VIP', '0', 'VIP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS179', 'GRD2', 'Garuda 2', '229', 'A', 'VIP', '0', 'VIP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS181', 'GRD2', 'Garuda 2', '232', 'A', 'Executive', '1', 'VVP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS182', 'GRD2', 'Garuda 2', '223', 'A', 'VIP', '1', 'VIP', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS185', 'MRAK', 'Merak', 'M-234', 'A', 'Kelas 2', '1', 'KL2', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS186', 'MRAK', 'Merak', 'M-234', 'B', 'Kelas 2', '1', 'KL2', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS187', 'MRAK', 'Merak', 'M-236', 'A', 'Kelas 2', '1', 'KL2', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS188', 'MRAK', 'Merak', 'M-236', 'B', 'Kelas 2', '1', 'KL2', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS189', 'MRAK', 'Merak', 'M-238', 'A', 'Kelas 2', '1', 'KL2', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS190', 'MRAK', 'Merak', 'M-238', 'B', 'Kelas 2', '1', 'KL2', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS191', 'MRAK', 'Merak', 'M-235', 'A', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS192', 'MRAK', 'Merak', 'M-235', 'B', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS193', 'MRAK', 'Merak', 'M-235', 'C', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS194', 'MRAK', 'Merak', 'M-235', 'D', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS195', 'MRAK', 'Merak', 'M-237', 'A', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS196', 'MRAK', 'Merak', 'M-237', 'B', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS197', 'MRAK', 'Merak', 'M-237', 'C', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS198', 'MRAK', 'Merak', 'M-237', 'D', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS199', 'MRAK', 'Merak', 'M-239', 'A', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS200', 'MRAK', 'Merak', 'M-239', 'B', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS201', 'MRAK', 'Merak', 'M-239', 'C', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS202', 'MRAK', 'Merak', 'M-239', 'D', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS203', 'MRAK', 'Merak', 'M-240', 'A', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS204', 'MRAK', 'Merak', 'M-240', 'B', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS205', 'MRAK', 'Merak', 'M-240', 'C', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS206', 'MRAK', 'Merak', 'M-240', 'D', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS207', 'MRAK', 'Merak', 'M-242', 'A', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS208', 'MRAK', 'Merak', 'M-242', 'B', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS209', 'MRAK', 'Merak', 'M-242', 'C', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS210', 'MRAK', 'Merak', 'M-242', 'D', 'Kelas 3', '1', 'KL3', 'Merak', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS212', 'KNRI', 'Kenari', 'K-249', 'A', 'Kelas 3', '1', 'KL3', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS213', 'KNRI', 'Kenari', 'K-249', 'B', 'Kelas 3', '1', 'KL3', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS214', 'KNRI', 'Kenari', 'K-249', 'C', 'Kelas 3', '1', 'KL3', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS215', 'KNRI', 'Kenari', 'K-249', 'D', 'Kelas 3', '1', 'KL3', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS216', 'KNRI', 'Kenari', 'K-251', 'A', 'Kelas 3', '1', 'KL3', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS217', 'KNRI', 'Kenari', 'K-251', 'B', 'Kelas 3', '0', 'KL3', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS218', 'KNRI', 'Kenari', 'K-251', 'C', 'Kelas 3', '1', 'KL3', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS219', 'KNRI', 'Kenari', 'K-251', 'D', 'Kelas 3', '1', 'KL3', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS220', 'KNRI', 'Kenari', 'K-247', 'A', 'Kelas 2', '1', 'KL2', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS221', 'KNRI', 'Kenari', 'K-247', 'B', 'Kelas 2', '1', 'KL2', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS222', 'KNRI', 'Kenari', 'K-250', 'A', 'Kelas 2', '1', 'KL2', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS223', 'KNRI', 'Kenari', 'K-250', 'B', 'Kelas 2', '1', 'KL2', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS224', 'KNRI', 'Kenari', 'K-243', 'A', 'Kelas 1', '1', 'KL1', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS225', 'KNRI', 'Kenari', 'K-243', 'B', 'Kelas 1', '1', 'KL1', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS226', 'KNRI', 'Kenari', 'K-245', 'A', 'Kelas 1', '1', 'KL1', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS227', 'KNRI', 'Kenari', 'K-245', 'B', 'Kelas 1', '1', 'KL1', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS228', 'KNRI', 'Kenari', 'K-248', 'A', 'Kelas 1', '1', 'KL1', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS229', 'KNRI', 'Kenari', 'K-248', 'B', 'Kelas 1', '1', 'KL1', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS230', 'KNRI', 'Kenari', 'K-244', 'A', 'VIP', '1', 'VIP', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS231', 'KNRI', 'Kenari', 'K-246', 'A', 'VIP', '0', 'VIP', 'Kenari', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS232', 'GRD2', 'Garuda 2', '228', 'C', 'Kelas 2', '0', 'KL2', 'Garuda 2', NULL);
INSERT INTO `bw_display_bad` VALUES ('DIS233', 'INTS', 'Intensif', 'P ICU', 'F', 'Non Kelas', '0', 'PIC', 'PICU Non Ventilator', NULL);

-- ----------------------------
-- Table structure for bw_display_poli
-- ----------------------------
DROP TABLE IF EXISTS `bw_display_poli`;
CREATE TABLE `bw_display_poli`  (
  `kd_display` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_display` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`kd_display`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_display_poli
-- ----------------------------

-- ----------------------------
-- Table structure for bw_file_casemix_hasil
-- ----------------------------
DROP TABLE IF EXISTS `bw_file_casemix_hasil`;
CREATE TABLE `bw_file_casemix_hasil`  (
  `no_rawat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_rkm_medis` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`no_rawat`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_file_casemix_hasil
-- ----------------------------

-- ----------------------------
-- Table structure for bw_file_casemix_inacbg
-- ----------------------------
DROP TABLE IF EXISTS `bw_file_casemix_inacbg`;
CREATE TABLE `bw_file_casemix_inacbg`  (
  `no_rawat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_rkm_medis` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`no_rawat`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_file_casemix_inacbg
-- ----------------------------

-- ----------------------------
-- Table structure for bw_file_casemix_remusedll
-- ----------------------------
DROP TABLE IF EXISTS `bw_file_casemix_remusedll`;
CREATE TABLE `bw_file_casemix_remusedll`  (
  `no_rawat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_rkm_medis` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`no_rawat`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_file_casemix_remusedll
-- ----------------------------

-- ----------------------------
-- Table structure for bw_file_casemix_scan
-- ----------------------------
DROP TABLE IF EXISTS `bw_file_casemix_scan`;
CREATE TABLE `bw_file_casemix_scan`  (
  `no_rawat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_rkm_medis` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`no_rawat`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_file_casemix_scan
-- ----------------------------

-- ----------------------------
-- Table structure for bw_invoice_asuransi
-- ----------------------------
DROP TABLE IF EXISTS `bw_invoice_asuransi`;
CREATE TABLE `bw_invoice_asuransi`  (
  `nomor_tagihan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kode_asuransi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_asuransi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `alamat_asuransi` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tanggl1` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggl2` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tgl_cetak` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_lanjut` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lamiran` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`nomor_tagihan`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_invoice_asuransi
-- ----------------------------

-- ----------------------------
-- Table structure for bw_jadwal_dokter
-- ----------------------------
DROP TABLE IF EXISTS `bw_jadwal_dokter`;
CREATE TABLE `bw_jadwal_dokter`  (
  `kd_dokter` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `hari_kerja` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jam_mulai` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jam_selesai` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kd_poli` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`kd_dokter`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_jadwal_dokter
-- ----------------------------

-- ----------------------------
-- Table structure for bw_jenis_lookbook
-- ----------------------------
DROP TABLE IF EXISTS `bw_jenis_lookbook`;
CREATE TABLE `bw_jenis_lookbook`  (
  `kd_jesni_lb` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_jenis_lb` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`kd_jesni_lb`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_jenis_lookbook
-- ----------------------------

-- ----------------------------
-- Table structure for bw_jenis_lookbook_kegiatan_lain
-- ----------------------------
DROP TABLE IF EXISTS `bw_jenis_lookbook_kegiatan_lain`;
CREATE TABLE `bw_jenis_lookbook_kegiatan_lain`  (
  `id_kegiatan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_kegiatan` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_kegiatan`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_jenis_lookbook_kegiatan_lain
-- ----------------------------

-- ----------------------------
-- Table structure for bw_jns_kegiatan_karu
-- ----------------------------
DROP TABLE IF EXISTS `bw_jns_kegiatan_karu`;
CREATE TABLE `bw_jns_kegiatan_karu`  (
  `kd_jns_kegiatan_karu` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nm_jenis_kegiatan` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`kd_jns_kegiatan_karu`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_jns_kegiatan_karu
-- ----------------------------

-- ----------------------------
-- Table structure for bw_karyawan
-- ----------------------------
DROP TABLE IF EXISTS `bw_karyawan`;
CREATE TABLE `bw_karyawan`  (
  `nip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jabatan` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `masa_kerja` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `golongan` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_karyawan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`nip`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_karyawan
-- ----------------------------

-- ----------------------------
-- Table structure for bw_kewenangankhusus_keperawatan
-- ----------------------------
DROP TABLE IF EXISTS `bw_kewenangankhusus_keperawatan`;
CREATE TABLE `bw_kewenangankhusus_keperawatan`  (
  `kd_kewenangan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_kewenangan` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kd_jesni_lb` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `default_mandiri` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `default_supervisi` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`kd_kewenangan`) USING BTREE,
  INDEX `kd_jesni_lb_2`(`kd_jesni_lb` ASC) USING BTREE,
  CONSTRAINT `bw_kewenangankhusus_keperawatan_ibfk_1` FOREIGN KEY (`kd_jesni_lb`) REFERENCES `bw_jenis_lookbook` (`kd_jesni_lb`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_kewenangankhusus_keperawatan
-- ----------------------------

-- ----------------------------
-- Table structure for bw_log_antrian_poli
-- ----------------------------
DROP TABLE IF EXISTS `bw_log_antrian_poli`;
CREATE TABLE `bw_log_antrian_poli`  (
  `no_rawat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kd_ruang_poli` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`no_rawat`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_log_antrian_poli
-- ----------------------------

-- ----------------------------
-- Table structure for bw_log_panggilan_poli
-- ----------------------------
DROP TABLE IF EXISTS `bw_log_panggilan_poli`;
CREATE TABLE `bw_log_panggilan_poli`  (
  `no_rawat` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kd_display` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `no_reg` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `kd_dokter` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `kd_ruang_poli` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `jam` time NULL DEFAULT NULL,
  PRIMARY KEY (`no_rawat`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_log_panggilan_poli
-- ----------------------------

-- ----------------------------
-- Table structure for bw_logbook_karu
-- ----------------------------
DROP TABLE IF EXISTS `bw_logbook_karu`;
CREATE TABLE `bw_logbook_karu`  (
  `id_logbook` int NOT NULL AUTO_INCREMENT,
  `kd_kegiatan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mandiri` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `supervisi` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_logbook`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_logbook_karu
-- ----------------------------

-- ----------------------------
-- Table structure for bw_logbook_keperawatan
-- ----------------------------
DROP TABLE IF EXISTS `bw_logbook_keperawatan`;
CREATE TABLE `bw_logbook_keperawatan`  (
  `id_logbook` int NOT NULL AUTO_INCREMENT,
  `kd_kegiatan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_rkm_medis` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mandiri` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `supervisi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_logbook`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_logbook_keperawatan
-- ----------------------------

-- ----------------------------
-- Table structure for bw_logbook_keperawatan_kegiatanlain
-- ----------------------------
DROP TABLE IF EXISTS `bw_logbook_keperawatan_kegiatanlain`;
CREATE TABLE `bw_logbook_keperawatan_kegiatanlain`  (
  `id_kegiatan_keperawatanlain` int NOT NULL AUTO_INCREMENT,
  `id_kegiatan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mandiri` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `supervisi` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_kegiatan_keperawatanlain`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_logbook_keperawatan_kegiatanlain
-- ----------------------------

-- ----------------------------
-- Table structure for bw_logbook_keperawatan_kewenangankhusus
-- ----------------------------
DROP TABLE IF EXISTS `bw_logbook_keperawatan_kewenangankhusus`;
CREATE TABLE `bw_logbook_keperawatan_kewenangankhusus`  (
  `id_kewenangankhusus` int NOT NULL AUTO_INCREMENT,
  `kd_kewenangan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_rkm_medis` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mandiri` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `supervisi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_kewenangankhusus`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_logbook_keperawatan_kewenangankhusus
-- ----------------------------

-- ----------------------------
-- Table structure for bw_maping_asuransi
-- ----------------------------
DROP TABLE IF EXISTS `bw_maping_asuransi`;
CREATE TABLE `bw_maping_asuransi`  (
  `kd_pj` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_perusahaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alamat_asuransi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kd_surat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tf_rekening_rs` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nm_tf_rekening_rs` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`kd_pj`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_maping_asuransi
-- ----------------------------

-- ----------------------------
-- Table structure for bw_nm_kegiatan_karu
-- ----------------------------
DROP TABLE IF EXISTS `bw_nm_kegiatan_karu`;
CREATE TABLE `bw_nm_kegiatan_karu`  (
  `kd_kegiatan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_kegiatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kd_jns_kegiatan_karu` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `default_mandiri` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `default_supervisi` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`kd_kegiatan`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_nm_kegiatan_karu
-- ----------------------------

-- ----------------------------
-- Table structure for bw_nm_kegiatan_keperawatan
-- ----------------------------
DROP TABLE IF EXISTS `bw_nm_kegiatan_keperawatan`;
CREATE TABLE `bw_nm_kegiatan_keperawatan`  (
  `kd_kegiatan` varchar(122) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_kegiatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kd_jesni_lb` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `default_mandiri` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `default_supervisi` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`kd_kegiatan`) USING BTREE,
  INDEX `kd_jesni_lb`(`kd_jesni_lb` ASC) USING BTREE,
  CONSTRAINT `bw_nm_kegiatan_keperawatan_ibfk_1` FOREIGN KEY (`kd_jesni_lb`) REFERENCES `bw_jenis_lookbook` (`kd_jesni_lb`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_nm_kegiatan_keperawatan
-- ----------------------------

-- ----------------------------
-- Table structure for bw_peserta_asuransi
-- ----------------------------
DROP TABLE IF EXISTS `bw_peserta_asuransi`;
CREATE TABLE `bw_peserta_asuransi`  (
  `no_rkm_medis` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nomor_kartu` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nomor_klaim` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`no_rkm_medis`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_peserta_asuransi
-- ----------------------------

-- ----------------------------
-- Table structure for bw_ruang_poli
-- ----------------------------
DROP TABLE IF EXISTS `bw_ruang_poli`;
CREATE TABLE `bw_ruang_poli`  (
  `kd_ruang_poli` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_ruang_poli` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kd_display` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `posisi_display_poli` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`kd_ruang_poli`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_ruang_poli
-- ----------------------------

-- ----------------------------
-- Table structure for bw_ruangpoli_dokter
-- ----------------------------
DROP TABLE IF EXISTS `bw_ruangpoli_dokter`;
CREATE TABLE `bw_ruangpoli_dokter`  (
  `kd_dokter` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_dokter` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kd_ruang_poli` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`kd_dokter`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_ruangpoli_dokter
-- ----------------------------

-- ----------------------------
-- Table structure for bw_setting_bundling
-- ----------------------------
DROP TABLE IF EXISTS `bw_setting_bundling`;
CREATE TABLE `bw_setting_bundling`  (
  `id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_berkas` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `urutan` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_setting_bundling
-- ----------------------------
INSERT INTO `bw_setting_bundling` VALUES ('BUN001', 'berkas-sep', '1', 1);
INSERT INTO `bw_setting_bundling` VALUES ('BUN002', 'resume-pasien', '1', 2);
INSERT INTO `bw_setting_bundling` VALUES ('BUN003', 'rincian-biaya', '1', 3);
INSERT INTO `bw_setting_bundling` VALUES ('BUN004', 'berkas-laborat', '1', 5);
INSERT INTO `bw_setting_bundling` VALUES ('BUN005', 'berkas-radiologi', '1', 4);
INSERT INTO `bw_setting_bundling` VALUES ('BUN006', 'awal-medis', '0', 6);
INSERT INTO `bw_setting_bundling` VALUES ('BUN007', 'surat-kematian', '1', 8);
INSERT INTO `bw_setting_bundling` VALUES ('BUN008', 'berkas-laporan-operasi', '0', 7);
INSERT INTO `bw_setting_bundling` VALUES ('BUN009', 'soapie-pasien', '1', 9);
INSERT INTO `bw_setting_bundling` VALUES ('BUN010', 'hasil-pemeriksaan-ekg', '0', 11);
INSERT INTO `bw_setting_bundling` VALUES ('BUN011', 'berkas-triase-igdsekunder', '1', 10);
INSERT INTO `bw_setting_bundling` VALUES ('BUN012', 'cppt-convert-resume-ralan', '0', 12);
INSERT INTO `bw_setting_bundling` VALUES ('BUN013', 'surat-pri-bpjs', '1', 13);

-- ----------------------------
-- Table structure for bw_skala_upah
-- ----------------------------
DROP TABLE IF EXISTS `bw_skala_upah`;
CREATE TABLE `bw_skala_upah`  (
  `id_skala_upah` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_karyawan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `golongan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `masa_kerja` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jumlah_upah` int NOT NULL,
  PRIMARY KEY (`id_skala_upah`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_skala_upah
-- ----------------------------

-- ----------------------------
-- Table structure for bw_test_cekin
-- ----------------------------
DROP TABLE IF EXISTS `bw_test_cekin`;
CREATE TABLE `bw_test_cekin`  (
  `kode_booking` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `task_id` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jam` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp_sec` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`kode_booking`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_test_cekin
-- ----------------------------

-- ----------------------------
-- Table structure for bw_validasi_icare
-- ----------------------------
DROP TABLE IF EXISTS `bw_validasi_icare`;
CREATE TABLE `bw_validasi_icare`  (
  `no_rawat` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kd_dokter_bpjs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`no_rawat`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of bw_validasi_icare
-- ----------------------------

-- ----------------------------
-- Table structure for file_farmasi
-- ----------------------------
DROP TABLE IF EXISTS `file_farmasi`;
CREATE TABLE `file_farmasi`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `no_rkm_medis` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_rawat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_pasein` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_berkas` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of file_farmasi
-- ----------------------------

-- ----------------------------
-- Table structure for list_dokter
-- ----------------------------
DROP TABLE IF EXISTS `list_dokter`;
CREATE TABLE `list_dokter`  (
  `kd_dokter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dokter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kd_loket` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `kuota_tambahan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '0',
  PRIMARY KEY (`kd_dokter`) USING BTREE,
  INDEX `list_dokter_kd_loket_foreign`(`kd_loket` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of list_dokter
-- ----------------------------

-- ----------------------------
-- Table structure for log_antrian_loket
-- ----------------------------
DROP TABLE IF EXISTS `log_antrian_loket`;
CREATE TABLE `log_antrian_loket`  (
  `no_rawat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kd_loket` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`no_rawat`) USING BTREE,
  INDEX `log_antrian_loket_kd_loket_foreign`(`kd_loket` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of log_antrian_loket
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
