-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2026 at 04:50 PM
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
-- Database: `racinghub`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `paket_id` int(11) DEFAULT NULL,
  `tanggal_booking` date DEFAULT NULL,
  `jam_booking` time DEFAULT NULL,
  `jumlah_orang` int(11) DEFAULT NULL,
  `total_harga` int(11) DEFAULT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status` enum('aktif','selesai','dibatalkan') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id`, `user_id`, `paket_id`, `tanggal_booking`, `jam_booking`, `jumlah_orang`, `total_harga`, `bukti_transfer`, `status`, `created_at`) VALUES
(1, 3, 4, '2026-05-21', '18:00:00', 1, 300000, NULL, 'selesai', '2026-05-21 12:13:17'),
(2, 3, 2, '2026-05-21', '14:00:00', 1, 250000, NULL, 'selesai', '2026-05-21 13:10:36'),
(3, 1, 3, '2026-05-21', '18:00:00', 3, 400000, NULL, 'selesai', '2026-05-21 13:11:26'),
(4, 1, 4, '2026-05-21', '18:00:00', 1, 300000, '6a0f15df8ea98.png', 'selesai', '2026-05-21 14:25:20'),
(5, 1, 3, '2026-05-21', '16:00:00', 3, 400000, '6a0f175dd4082.png', 'selesai', '2026-05-21 14:31:50'),
(6, 3, 3, '2026-05-21', '17:00:00', 3, 400000, '6a0f17b1f3487.png', 'selesai', '2026-05-21 14:33:11'),
(7, 3, 4, '2026-05-21', '17:00:00', 1, 300000, '6a0f2701ddddc.png', 'selesai', '2026-05-21 15:38:25'),
(8, 3, 4, '2026-05-22', '18:00:00', 1, 300000, NULL, 'aktif', '2026-05-22 04:02:35'),
(9, 3, 4, '2026-05-22', '15:00:00', 1, 300000, '6a0fd632bf208.png', 'selesai', '2026-05-22 04:04:45'),
(10, 4, 3, '2026-05-24', '13:00:00', 3, 400000, NULL, 'aktif', '2026-05-24 14:37:51');

-- --------------------------------------------------------

--
-- Table structure for table `hasil_balapan`
--

CREATE TABLE `hasil_balapan` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sektor_1` decimal(5,3) DEFAULT 99.999,
  `sektor_2` decimal(5,3) DEFAULT 99.999,
  `sektor_3` decimal(5,3) DEFAULT 99.999,
  `total_lap` decimal(5,3) DEFAULT 99.999,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hasil_balapan`
--

INSERT INTO `hasil_balapan` (`id`, `booking_id`, `user_id`, `sektor_1`, `sektor_2`, `sektor_3`, `total_lap`, `created_at`) VALUES
(1, 6, 3, 12.000, 12.000, 12.000, 36.000, '2026-05-21 14:40:28'),
(2, 5, 1, 10.000, 9.000, 8.000, 27.000, '2026-05-21 14:41:25');

-- --------------------------------------------------------

--
-- Table structure for table `paket_bermain`
--

CREATE TABLE `paket_bermain` (
  `id` int(11) NOT NULL,
  `nama_paket` varchar(100) DEFAULT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `durasi_menit` int(11) DEFAULT NULL,
  `maksimal_orang` int(11) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paket_bermain`
--

INSERT INTO `paket_bermain` (`id`, `nama_paket`, `deskripsi`, `durasi_menit`, `maksimal_orang`, `harga`) VALUES
(1, 'Beginner', 'Pemula', 20, 1, 150000),
(2, 'ProRace', 'Pro', 30, 1, 250000),
(3, 'Keluarga', 'Keluarga', 30, 3, 400000),
(4, 'PRO MAx', 'PROMAX', 50, 1, 300000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nomor_hp` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_bermain` int(11) DEFAULT 0,
  `best_time` decimal(5,3) DEFAULT 99.999,
  `booking_aktif` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `nomor_hp`, `email`, `password`, `role`, `created_at`, `total_bermain`, `best_time`, `booking_aktif`) VALUES
(1, 'Race Director', '08123456789', 'admin@gokart.com', '$2y$10$AdxOnxCVV3UZEY4qKFVW4OJIN/Ma.cM2Yh7Na0cN4R24RYmYH7fgi', 'admin', '2026-05-21 12:04:42', 1, 27.000, 0),
(3, 'Andi brambang', '081234567890', 'andi@gmail.com', '$2y$10$LMDtG8okX/bLDjxD8wWUvOkSFZcflIUtJf474fAHvROXkP1ZCGez.', 'user', '2026-05-21 12:06:18', 3, 36.000, 0),
(4, 'dwi', '022545', 'Dwi@gmail.com', '$2y$10$P8zY.A2Ya4JQIS15tzm9O.bWnCYVm2ldVDxdi5pFxWKlxAMtqglSy', 'user', '2026-05-24 14:37:34', 0, 99.999, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_booking_user` (`user_id`),
  ADD KEY `fk_booking_paket` (`paket_id`);

--
-- Indexes for table `hasil_balapan`
--
ALTER TABLE `hasil_balapan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hasil_booking` (`booking_id`),
  ADD KEY `fk_hasil_user` (`user_id`);

--
-- Indexes for table `paket_bermain`
--
ALTER TABLE `paket_bermain`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `hasil_balapan`
--
ALTER TABLE `hasil_balapan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `paket_bermain`
--
ALTER TABLE `paket_bermain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_booking_paket` FOREIGN KEY (`paket_id`) REFERENCES `paket_bermain` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hasil_balapan`
--
ALTER TABLE `hasil_balapan`
  ADD CONSTRAINT `fk_hasil_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hasil_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
