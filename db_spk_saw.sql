-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 07, 2026 at 04:53 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_spk_saw`
--

-- --------------------------------------------------------

--
-- Table structure for table `alternatif`
--

CREATE TABLE `alternatif` (
  `id_alternatif` int NOT NULL,
  `id_periode` int NOT NULL,
  `kode_alternatif` varchar(10) NOT NULL,
  `nama_alternatif` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `alternatif`
--

INSERT INTO `alternatif` (`id_alternatif`, `id_periode`, `kode_alternatif`, `nama_alternatif`) VALUES
(1, 2, 'S1', 'Gayo Prima'),
(2, 2, 'S2', 'Toraja Mandiri'),
(3, 2, 'S3', 'Flores Jaya'),
(4, 2, 'S4', 'Kintamani Raya'),
(5, 2, 'S5', 'Malabar Estate'),
(6, 2, 'S6', 'Ijen Raung'),
(7, 2, 'S7', 'Mandailing Arabica'),
(8, 2, 'S8', 'Dampit Robusta'),
(9, 2, 'S9', 'Kerinci Mountain');

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id_kriteria` int NOT NULL,
  `id_periode` int DEFAULT NULL,
  `kode_kriteria` varchar(10) NOT NULL,
  `nama_kriteria` varchar(100) NOT NULL,
  `tipe` enum('Benefit','Cost') NOT NULL,
  `bobot` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id_kriteria`, `id_periode`, `kode_kriteria`, `nama_kriteria`, `tipe`, `bobot`) VALUES
(7, NULL, 'C1', 'Harga per kg (Rp ribu)', 'Cost', 85),
(8, NULL, 'C2', 'Skor Cupping (0–100)', 'Benefit', 95),
(9, NULL, 'C3', 'Lama Pengiriman (Hari)', 'Cost', 75),
(10, NULL, 'C4', 'Kapasitas Pasokan (Kg/bulan)', 'Benefit', 60),
(11, NULL, 'C5', 'Kualitas Layanan (Skala 1–5)', 'Benefit', 70),
(12, NULL, 'C6', 'Tempo Pembayaran (Hari)', 'Benefit', 80),
(13, 2, 'C1', 'Harga per kg (Rp ribu)', 'Cost', 85),
(14, 2, 'C2', 'Skor Cupping (0–100)', 'Benefit', 95),
(15, 2, 'C3', 'Lama Pengiriman (Hari)', 'Cost', 75),
(16, 2, 'C4', 'Kapasitas Pasokan (Kg/bulan)', 'Benefit', 60),
(17, 2, 'C5', 'Kualitas Layanan (Skala 1–5)', 'Benefit', 70),
(18, 2, 'C6', 'Tempo Pembayaran (Hari)', 'Benefit', 80);

-- --------------------------------------------------------

--
-- Table structure for table `penilaian`
--

CREATE TABLE `penilaian` (
  `id_penilaian` int NOT NULL,
  `id_alternatif` int NOT NULL,
  `id_kriteria` int NOT NULL,
  `nilai` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penilaian`
--

INSERT INTO `penilaian` (`id_penilaian`, `id_alternatif`, `id_kriteria`, `nilai`) VALUES
(1, 1, 7, 125),
(2, 1, 8, 84),
(3, 1, 9, 5),
(4, 1, 10, 200),
(5, 1, 11, 4),
(6, 1, 12, 14),
(7, 2, 7, 140),
(8, 2, 8, 87),
(9, 2, 9, 7),
(10, 2, 10, 150),
(11, 2, 11, 5),
(12, 2, 12, 30),
(13, 3, 7, 110),
(14, 3, 8, 80),
(15, 3, 9, 3),
(16, 3, 10, 300),
(17, 3, 11, 3),
(18, 3, 12, 7),
(19, 4, 7, 135),
(20, 4, 8, 85),
(21, 4, 9, 4),
(22, 4, 10, 250),
(23, 4, 11, 4),
(24, 4, 12, 14),
(25, 5, 7, 145),
(26, 5, 8, 89),
(27, 5, 9, 6),
(28, 5, 10, 120),
(29, 5, 11, 5),
(30, 5, 12, 30),
(31, 6, 7, 115),
(32, 6, 8, 81),
(33, 6, 9, 3),
(34, 6, 10, 350),
(35, 6, 11, 3),
(36, 6, 12, 14),
(37, 7, 7, 130),
(38, 7, 8, 86),
(39, 7, 9, 5),
(40, 7, 10, 180),
(41, 7, 11, 4),
(42, 7, 12, 21),
(43, 8, 7, 90),
(44, 8, 8, 75),
(45, 8, 9, 2),
(46, 8, 10, 500),
(47, 8, 11, 3),
(48, 8, 12, 7),
(49, 9, 7, 120),
(50, 9, 8, 83),
(51, 9, 9, 4),
(52, 9, 10, 220),
(53, 9, 11, 4),
(54, 9, 12, 21),
(55, 1, 13, 125),
(56, 1, 14, 84),
(57, 1, 15, 5),
(58, 1, 16, 200),
(59, 1, 17, 4),
(60, 1, 18, 14),
(61, 2, 13, 140),
(62, 2, 14, 87),
(63, 2, 15, 7),
(64, 2, 16, 150),
(65, 2, 17, 5),
(66, 2, 18, 30),
(67, 3, 13, 110),
(68, 3, 14, 80),
(69, 3, 15, 3),
(70, 3, 16, 300),
(71, 3, 17, 3),
(72, 3, 18, 7),
(73, 4, 13, 135),
(74, 4, 14, 85),
(75, 4, 15, 4),
(76, 4, 16, 250),
(77, 4, 17, 4),
(78, 4, 18, 14),
(79, 5, 13, 145),
(80, 5, 14, 89),
(81, 5, 15, 6),
(82, 5, 16, 120),
(83, 5, 17, 5),
(84, 5, 18, 30),
(85, 6, 13, 115),
(86, 6, 14, 81),
(87, 6, 15, 3),
(88, 6, 16, 350),
(89, 6, 17, 3),
(90, 6, 18, 14),
(91, 7, 13, 130),
(92, 7, 14, 86),
(93, 7, 15, 5),
(94, 7, 16, 180),
(95, 7, 17, 4),
(96, 7, 18, 21),
(97, 8, 13, 90),
(98, 8, 14, 75),
(99, 8, 15, 2),
(100, 8, 16, 500),
(101, 8, 17, 3),
(102, 8, 18, 7),
(103, 9, 13, 120),
(104, 9, 14, 83),
(105, 9, 15, 4),
(106, 9, 16, 220),
(107, 9, 17, 4),
(108, 9, 18, 21);

-- --------------------------------------------------------

--
-- Table structure for table `periode`
--

CREATE TABLE `periode` (
  `id_periode` int NOT NULL,
  `nama_periode` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `periode`
--

INSERT INTO `periode` (`id_periode`, `nama_periode`) VALUES
(2, 'Seleksi Supplier Jan 2026');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`id_alternatif`),
  ADD KEY `id_periode` (`id_periode`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id_kriteria`),
  ADD KEY `fk_kriteria_periode` (`id_periode`);

--
-- Indexes for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`id_penilaian`),
  ADD KEY `id_alternatif` (`id_alternatif`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indexes for table `periode`
--
ALTER TABLE `periode`
  ADD PRIMARY KEY (`id_periode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alternatif`
--
ALTER TABLE `alternatif`
  MODIFY `id_alternatif` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id_kriteria` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id_penilaian` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `periode`
--
ALTER TABLE `periode`
  MODIFY `id_periode` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alternatif`
--
ALTER TABLE `alternatif`
  ADD CONSTRAINT `alternatif_ibfk_1` FOREIGN KEY (`id_periode`) REFERENCES `periode` (`id_periode`) ON DELETE CASCADE;

--
-- Constraints for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD CONSTRAINT `fk_kriteria_periode` FOREIGN KEY (`id_periode`) REFERENCES `periode` (`id_periode`) ON DELETE CASCADE;

--
-- Constraints for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD CONSTRAINT `penilaian_ibfk_1` FOREIGN KEY (`id_alternatif`) REFERENCES `alternatif` (`id_alternatif`) ON DELETE CASCADE,
  ADD CONSTRAINT `penilaian_ibfk_2` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id_kriteria`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
