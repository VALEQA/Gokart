-- Membuat Database
CREATE DATABASE IF NOT EXISTS `racinghub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `racinghub`;

-- Tabel: paket_bermain
CREATE TABLE `paket_bermain` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama_paket` varchar(100) DEFAULT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `durasi_menit` int(11) DEFAULT NULL,
  `maksimal_orang` int(11) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: users
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama_lengkap` varchar(100) NOT NULL,
  `nomor_hp` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_bermain` int(11) DEFAULT 0,
  `best_time` decimal(5,3) DEFAULT 99.999,
  `booking_aktif` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: booking
CREATE TABLE `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) DEFAULT NULL,
  `paket_id` int(11) DEFAULT NULL,
  `tanggal_booking` date DEFAULT NULL,
  `jam_booking` time DEFAULT NULL,
  `jumlah_orang` int(11) DEFAULT NULL,
  `total_harga` int(11) DEFAULT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status` enum('aktif','selesai','dibatalkan') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_booking_paket` FOREIGN KEY (`paket_id`) REFERENCES `paket_bermain` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: hasil_balapan
CREATE TABLE `hasil_balapan` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `booking_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sektor_1` decimal(5,3) DEFAULT 99.999,
  `sektor_2` decimal(5,3) DEFAULT 99.999,
  `sektor_3` decimal(5,3) DEFAULT 99.999,
  `total_lap` decimal(5,3) DEFAULT 99.999,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  CONSTRAINT `fk_hasil_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_hasil_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;