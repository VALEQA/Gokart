-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2026 at 11:19 AM
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
-- Database: `racing_hub`
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
  `status` enum('aktif','selesai','dibatalkan') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id`, `user_id`, `paket_id`, `tanggal_booking`, `jam_booking`, `jumlah_orang`, `total_harga`, `status`, `created_at`) VALUES
(1, 2, 2, '2026-04-15', '15:00:00', 1, 250000, 'aktif', '2026-05-08 19:55:13'),
(2, 2, 1, '2026-04-10', '13:00:00', 1, 150000, 'selesai', '2026-05-08 19:55:13'),
(3, 3, 3, '2026-04-16', '17:00:00', 3, 400000, 'aktif', '2026-05-08 19:55:13'),
(4, 2, 1, '2026-05-19', '14:00:00', 3, 400000, 'aktif', '2026-05-09 05:13:15'),
(5, 2, 1, '2026-05-30', '15:00:00', 3, 400000, 'aktif', '2026-05-09 05:14:08'),
(6, 2, 1, '2026-05-25', '18:00:00', 1, 250000, 'aktif', '2026-05-09 06:08:05'),
(7, 2, 1, '2026-05-14', '11:00:00', 3, 400000, 'aktif', '2026-05-09 06:10:19'),
(0, 2, 1, '2026-05-10', '10:00:00', 1, 250000, 'aktif', '2026-05-10 08:25:59');

-- --------------------------------------------------------

--
-- Table structure for table `hasil_balapan`
--

CREATE TABLE `hasil_balapan` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `waktu_tercepat` time DEFAULT NULL,
  `posisi_finish` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hasil_balapan`
--

INSERT INTO `hasil_balapan` (`id`, `booking_id`, `user_id`, `waktu_tercepat`, `posisi_finish`, `created_at`) VALUES
(1, 2, 2, '00:01:45', 1, '2026-05-08 19:55:42');

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
(3, 'Keluarga', 'Keluarga', 30, 3, 400000);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `nomor_hp`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Race Director', '08123456789', 'admin@gokart.com', '$2y$10$S9pM9kP9vX7vK3.vI.G8/.LpXN.Y8M6p4G2M4G2M4G2M4G2M4G2M4', 'admin', '2026-05-10 07:50:30'),
(2, 'fasi', '082113996387', 'andi@email.com', '$2y$10$sCUfVmAEKr9tGFHtD6vyo.ovoWM9unyzlXEML.u4eG5uUGN1G7Zfu', 'user', '2026-05-10 07:54:03'),
(3, 'Ahmad Faishal Baihaqi', '082113996387', 'ilyas089688678669@gmail.com', '$2y$10$S0C122B89z4o7Ftl2DGZseSZQvR2rDlQlPNvPCaAFmV/xd4vfSTLK', 'user', '2026-05-10 08:46:49');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
