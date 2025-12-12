-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 01:59 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lostnfound`
--

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `id_item` int(5) NOT NULL,
  `nm_item` varchar(50) NOT NULL,
  `tarikh` date NOT NULL,
  `th_item` date NOT NULL,
  `etc_item` varchar(200) DEFAULT NULL,
  `img_item` varchar(255) DEFAULT NULL,
  `status` varchar(10) NOT NULL,
  `reward` varchar(50) DEFAULT NULL,
  `nm_pengguna` varchar(50) NOT NULL,
  `un_pengguna` varchar(20) NOT NULL,
  `no_pengguna` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`id_item`, `nm_item`, `tarikh`, `th_item`, `etc_item`, `img_item`, `status`, `reward`, `nm_pengguna`, `un_pengguna`, `no_pengguna`) VALUES
(9, 'Kalkulator saintifik', '0000-00-00', '2025-05-22', 'Jumpa dekat perpustakaan tadi pagi, owner kalau nak claim pergi cakap dekat pensyarah/pelajar bertugas di perpustakaan', 'uploads/1747927075_682f40235f570.jpg', 'Found', NULL, 'naufal01', 'naufal01', '+60 12 555 5312'),
(14, 'Smart Watch 500', '0000-00-00', '2025-07-16', 'Warna hitam, last nampak dalam lab TM1', 'uploads/1752035304_9c71a74da424c5868bf30d7050c25a58.png', 'Found', '', 'Danish Akmal', 'ikanPUYu', '+60 17 955 6834'),
(16, 'Bantal Katak', '0000-00-00', '2025-08-04', 'Bantal Peluk Katak comel, last dekat Umar 1, aku rasa Adam Muhaimin yang sorok', 'uploads/1754295973_katak.jpeg', 'Lost', 'Meggi Ayam', 'Alif', 'Alif', '+60 19 235 4321'),
(18, 'Bantal Peluk Kucing', '0000-00-00', '2025-07-01', 'Bantal bentuk kucing lebih kurang macam dalam gambar tapi tompok dia warna hitam', 'uploads/1754296262_images.jpeg', 'Lost', 'RM5 cash', 'Mija', 'Mija', '+60 19 388 5726'),
(19, 'Buku teks Sejarah form 5', '0000-00-00', '2025-08-04', 'Jumpa dekat BK13', 'uploads/1754296470_S09f5611523b64b4d84d9c690261dd1bat.jpg', 'Found', NULL, 'Mija', 'Mija', '+60 19 388 5726'),
(20, 'Earbud', '0000-00-00', '2025-08-04', 'Warna hitam, jumpa dekat court takraw. claim kat umar 1. nanti aku tanya2 sikit nak make sure ni ko punya.', 'uploads/1754296743_owENLFHgWCrKxKZwPmqobR.jpg', 'Found', NULL, 'Numan', 'Numan', '+60 11 595 00921'),
(21, 'Powerbank', '0000-00-00', '2025-08-05', 'Powerbank Xiaomi 50000mAh. kondisi masih okay. jumpa dekat bilik akses', 'uploads/1754356768_t.jpg', 'Found', NULL, 'Alif', 'Alif', '+60 19 235 4321'),
(25, 'Kasut Vans', '0000-00-00', '2025-08-05', 'Sebijik macam dalam gambar, saiz 40 US. last nampak dekat dorm Umar 1. siapa jumpa ada rewardnya. ', 'uploads/1754360417_kasut.png', 'Lost', 'Mistery', 'Alif', 'Alif', '+60 19 235 4321'),
(26, 'RM 60', '0000-00-00', '2025-08-25', 'siapa amik duit aku RM 60? jujur je aku tak marah. sumpah wey tak jumpa malam ni aku report warden malam ni jugak', 'uploads/1756214984_Screenshot_2025-08-26_212446.png', 'Lost', '', 'Numan', 'Numan', '+60 11 5950 0921');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(10) UNSIGNED NOT NULL,
  `nm_pengguna` varchar(20) NOT NULL,
  `dn_pengguna` varchar(20) DEFAULT NULL,
  `ps_pengguna` varchar(255) NOT NULL,
  `tarikh` date NOT NULL,
  `no_pengguna` varchar(20) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nm_pengguna`, `dn_pengguna`, `ps_pengguna`, `tarikh`, `no_pengguna`, `gambar`) VALUES
(1, 'ikanPUYu', 'Danish Akmal', '$2y$10$dS6peDHD.f8tMj6aqilLtelBhjcnoje3UR9/LRFjeCrsKomcXwTFq', '2025-05-20', '+60 17 955 6834', 'uploads/profile_ikanPUYu.png'),
(2, 'Bella', 'Bella', '$2y$10$L.0/HMrSoZFB65ty9hyhFer32Z9gDRfBlgRFLVvOK7VS2IuOwTkvO', '2025-06-04', NULL, ''),
(4, 'naufal01', 'naufal01', '$2y$10$VaqIkyPr/bUih.nWcdpwIuLt/0ZXv5uMIgDFuXlqnZVeKqwKjia..', '2025-06-26', NULL, ''),
(5, 'Numan', 'Numan', '$2y$10$fZs7/OSbdLX3OcEOzr96leEhVn7dViEv99G9ggByJHjZQbFYJQIbi', '2025-06-30', NULL, 'uploads/profile_Numan.png'),
(6, 'jasmine', 'jasmine', '$2y$10$Or/mCbzrIMM5TmGW52nrMuoGA05jCNF38/tjyHYxhA0kC4BQP8x1.', '2025-07-06', NULL, ''),
(7, 'kenit', 'kenit', '$2y$10$oETGUBqys.8c7g4VLUCAme1xLvdg78TYSOexywGqNwai2in7VqEhi', '2025-06-16', NULL, ''),
(8, 'Alif', 'Alif', '$2y$10$byeKXhwVKBYrYP7yR6kse.4RBV/SkmgNS3orbwYziPC.rXmABDxfu', '2025-06-03', NULL, 'uploads/profile_Alif.png'),
(9, 'Mija', 'Mija', '$2y$10$nxCaroiao1k0cfXfUV1aFOfUmcCo7TVss1FkVuvouPvDn4Dpj9TLG', '2025-06-02', NULL, ''),
(10, 'Adam', 'Adam Muhaimin', '$2y$10$HaaD6yqFhZDzxyK/UiplmuhBq0cyacu4WZAULh1Dicb1/OA9w8Tzy', '2025-07-08', NULL, 'uploads/profile_adam.png'),
(11, 'Mikeleo', 'Muhammad Ali', '$2y$10$oiyMG4Qq.oKIHVmFQxwb3eYSWKHEIkttyYh.OhxLVKzxXv/uL3dNu', '2025-08-22', NULL, ''),
(12, 'Adif1234', 'Adif Hasyimi', '$2y$10$zn4F6RCnGi1L37gDPdQPfOKA3LQnsNiiBseQV9mqGzzzGCsAqyRCe', '2025-10-30', NULL, ''),
(15, 'fahmi123', 'fahmi123', '$2y$10$O9kcfh5d0oMQa3n5UF7YvOgX2aRIY2NH97F.85d58c4BlddeaQ7xC', '2025-10-30', NULL, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id_item`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `nm_pengguna` (`nm_pengguna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id_item` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
