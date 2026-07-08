-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2026 at 10:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_reservasi_futsal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('Superadmin','Admin','Kasir') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama`, `role`) VALUES
(1, 'superadmin', '$2y$10$iXs45eYC//jaEAl6FnYFrOstJ/NXpFqZIqz/OCaDo64IySXyKsBpK', 'Super Admin', 'Superadmin'),
(2, 'admin', '$2y$10$001UO60SIMTkaVFbMgKLf.sAIy9.jAr82aMJLSJ45qJ2.XyoETaim', 'Admin Futsal', 'Admin'),
(3, 'kasir', '$2y$10$/.GdreqWfeTg5g2ui6wat.CcDaE3HFak8hspOEvGKes35/lWBuWsq', 'Kasir Futsal', 'Kasir');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `id_lapangan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `status` enum('tersedia','dipesan','tutup') NOT NULL DEFAULT 'tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`id`, `id_lapangan`, `tanggal`, `jam_mulai`, `jam_selesai`, `status`) VALUES
(1, 1, '2026-07-08', '08:00:00', '10:00:00', 'dipesan'),
(2, 1, '2026-07-08', '10:00:00', '12:00:00', 'tersedia'),
(3, 1, '2026-07-08', '19:00:00', '21:00:00', 'tersedia'),
(4, 1, '2026-07-08', '21:00:00', '23:00:00', 'dipesan'),
(5, 1, '2026-07-09', '08:00:00', '10:00:00', 'tersedia'),
(6, 1, '2026-07-09', '16:00:00', '18:00:00', 'tersedia'),
(7, 2, '2026-07-08', '09:00:00', '11:00:00', 'tersedia'),
(8, 2, '2026-07-08', '18:00:00', '20:00:00', 'dipesan'),
(9, 2, '2026-07-08', '20:00:00', '22:00:00', 'tersedia'),
(10, 2, '2026-07-10', '07:00:00', '09:00:00', 'tersedia'),
(11, 2, '2026-07-10', '15:00:00', '17:00:00', 'tutup'),
(12, 3, '2026-07-08', '13:00:00', '15:00:00', 'tersedia'),
(13, 3, '2026-07-08', '15:00:00', '17:00:00', 'dipesan'),
(14, 3, '2026-07-09', '09:00:00', '11:00:00', 'tersedia'),
(15, 4, '2026-07-09', '10:00:00', '12:00:00', 'tersedia'),
(16, 4, '2026-07-09', '19:00:00', '21:00:00', 'dipesan'),
(17, 5, '2026-07-08', '08:00:00', '10:00:00', 'tersedia'),
(18, 5, '2026-07-08', '17:00:00', '19:00:00', 'tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_lapangan`
--

CREATE TABLE `kategori_lapangan` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_lapangan`
--

INSERT INTO `kategori_lapangan` (`id`, `nama`, `keterangan`) VALUES
(1, 'Indoor', NULL),
(2, 'Outdoor', NULL),
(3, 'Vinyl', NULL),
(4, 'Rumput Sintetis', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lapangan`
--

CREATE TABLE `lapangan` (
  `id` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `harga_per_jam` decimal(10,2) NOT NULL DEFAULT 0.00,
  `foto` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lapangan`
--

INSERT INTO `lapangan` (`id`, `id_kategori`, `nama`, `harga_per_jam`, `foto`, `keterangan`, `status`) VALUES
(1, 1, 'Lapangan A - Indoor Premium', 150000.00, 'lapangan_demo_1.jpg', 'Lapangan indoor ber-AC, cocok untuk pertandingan malam hari', 'aktif'),
(2, 2, 'Lapangan B - Outdoor Standard', 100000.00, 'lapangan_demo_2.jpg', 'Lapangan outdoor dengan pencahayaan lampu sorot', 'aktif'),
(3, 3, 'Lapangan C - Vinyl Kompetisi', 175000.00, 'lapangan_demo_3.jpg', 'Permukaan vinyl standar kompetisi resmi', 'aktif'),
(4, 4, 'Lapangan D - Rumput Sintetis', 125000.00, 'lapangan_demo_4.jpg', 'Rumput sintetis kualitas FIFA, minim cedera', 'aktif'),
(5, 1, 'Lapangan E - Indoor Ekonomis', 90000.00, 'lapangan_demo_5.jpg', 'Lapangan indoor tanpa AC, harga lebih terjangkau', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `id_reservasi` int(11) NOT NULL,
  `metode` enum('transfer','tunai','qris') NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `tanggal_bayar` datetime NOT NULL DEFAULT current_timestamp(),
  `status_verifikasi` enum('menunggu','diverifikasi','ditolak') NOT NULL DEFAULT 'menunggu',
  `id_admin` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `id_reservasi`, `metode`, `bukti_bayar`, `jumlah_bayar`, `tanggal_bayar`, `status_verifikasi`, `id_admin`) VALUES
(1, 1, 'tunai', NULL, 300000.00, '2026-07-08 15:00:16', 'diverifikasi', 2),
(2, 2, 'transfer', NULL, 300000.00, '2026-07-08 15:00:16', 'menunggu', NULL),
(3, 5, 'qris', NULL, 250000.00, '2026-07-08 15:00:16', 'diverifikasi', 3);

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id` int(11) NOT NULL,
  `kode` varchar(20) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `total_bayar` decimal(10,2) DEFAULT 0.00,
  `status` enum('menunggu','dikonfirmasi','dibatalkan','selesai') NOT NULL DEFAULT 'menunggu',
  `keterangan` text DEFAULT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id`, `kode`, `id_jadwal`, `nama_pemesan`, `no_hp`, `total_bayar`, `status`, `keterangan`, `id_admin`, `dibuat_pada`) VALUES
(1, 'RSVDEMO001', 1, 'Budi Santoso', '081234567890', 300000.00, 'selesai', 'Sewa rutin mingguan', 2, '2026-07-08 15:00:16'),
(2, 'RSVDEMO002', 4, 'Andi Wijaya', '081298765432', 300000.00, 'dikonfirmasi', 'Booking komunitas futsal Jumat malam', 3, '2026-07-08 15:00:16'),
(3, 'RSVDEMO003', 8, 'Citra Ramadhani', '081355566677', 200000.00, 'menunggu', 'Booking online lewat website', NULL, '2026-07-08 15:00:16'),
(4, 'RSVDEMO004', 13, 'Dedi Kurniawan', '081411122233', 350000.00, 'dibatalkan', 'Dibatalkan karena hujan deras', 2, '2026-07-08 15:00:16'),
(5, 'RSVDEMO005', 16, 'Eka Putri', '081577788899', 250000.00, 'dikonfirmasi', 'Latihan tim futsal kampus', 3, '2026-07-08 15:00:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_lapangan` (`id_lapangan`);

--
-- Indexes for table `kategori_lapangan`
--
ALTER TABLE `kategori_lapangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lapangan`
--
ALTER TABLE `lapangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_reservasi` (`id_reservasi`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`),
  ADD KEY `id_jadwal` (`id_jadwal`),
  ADD KEY `id_admin` (`id_admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `kategori_lapangan`
--
ALTER TABLE `kategori_lapangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lapangan`
--
ALTER TABLE `lapangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id`);

--
-- Constraints for table `lapangan`
--
ALTER TABLE `lapangan`
  ADD CONSTRAINT `lapangan_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_lapangan` (`id`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi` (`id`),
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id`);

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal` (`id`),
  ADD CONSTRAINT `reservasi_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
