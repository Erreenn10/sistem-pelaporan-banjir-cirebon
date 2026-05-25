-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2026 at 03:50 AM
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
-- Database: `flood_report_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', '2026-05-25 01:16:51');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `report_code` varchar(20) NOT NULL,
  `reporter_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` enum('Diterima','Ditindaklanjuti','Dikerjakan','Selesai') DEFAULT 'Diterima',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `report_code`, `reporter_name`, `email`, `phone`, `address`, `photo`, `description`, `latitude`, `longitude`, `status`, `created_at`, `updated_at`) VALUES
(1, 'FL-20241201-001', 'Ahmad Fauzi', 'ahmad@gmail.com', '081234567890', 'Jalan Pantura Pangenan Cirebon', NULL, 'Terjadi banjir akibat hujan deras sejak malam hari sehingga jalan utama tergenang air setinggi 50cm dan menghambat aktivitas warga. Drainase tidak mampu menampung air.', -6.71230000, 108.54320000, 'Diterima', '2026-05-25 01:16:51', '2026-05-25 01:16:51'),
(2, 'FL-20241201-002', 'Siti Nurjanah', 'siti@email.com', '081298765432', 'Desa Pangenan RT 02 RW 03', NULL, 'Ketinggian air mencapai 70cm, warga mulai mengungsi ke tempat yang lebih tinggi. Listrik padam sejak jam 20.00 WIB.', -6.71450000, 108.54560000, 'Ditindaklanjuti', '2026-05-25 01:16:51', '2026-05-25 01:16:51'),
(3, 'FL-20241202-003', 'Bambang Susanto', 'bambang@email.com', '081256789012', 'Perumahan Pantura Indah Blok A', NULL, 'Drainase tersumbat sampah, air tidak mengalir dengan baik sehingga menyebabkan genangan', -6.71000000, 108.54000000, 'Dikerjakan', '2026-05-25 01:16:51', '2026-05-25 01:16:51');

--
-- Triggers `reports`
--
DELIMITER $$
CREATE TRIGGER `before_insert_reports` BEFORE INSERT ON `reports` FOR EACH ROW BEGIN
    IF NEW.report_code IS NULL OR NEW.report_code = '' THEN
        SET NEW.report_code = CONCAT('FL-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 1000), 3, '0'));
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_code` (`report_code`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_report_code` (`report_code`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
