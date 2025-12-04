-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2025 at 04:32 PM
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
-- Database: `sistem_undian_menu`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `idadmin` varchar(10) NOT NULL,
  `namaadmin` varchar(100) NOT NULL,
  `password_admin` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`idadmin`, `namaadmin`, `password_admin`) VALUES
('A001', 'Major Major', 'qwerty'),
('A002', 'Say Gex', 'kokkokpp'),
('A003', 'Sum Ting Wong', 'goobert'),
('A004', 'Wi Tu Lo', 'goobaaart');

-- --------------------------------------------------------

--
-- Table structure for table `makanan`
--

CREATE TABLE `makanan` (
  `idmakanan` varchar(10) NOT NULL,
  `namamakanan` varchar(100) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `idadmin` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `makanan`
--

INSERT INTO `makanan` (`idmakanan`, `namamakanan`, `gambar`, `idadmin`) VALUES
('M001', 'Burger Ayam', 'M001.jpg', 'A001'),
('M002', 'Mee Sardin', 'M002.jpg', 'A002'),
('M003', 'Pizza', 'M003.jpg', 'A002'),
('M004', 'Roti Bakar', 'M004.jpg', 'A003'),
('M005', 'Sushi', 'M005.jpg', 'A004');

-- --------------------------------------------------------

--
-- Table structure for table `pengundi`
--

CREATE TABLE `pengundi` (
  `idpengundi` varchar(10) NOT NULL,
  `namapengundi` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengundi`
--

INSERT INTO `pengundi` (`idpengundi`, `namapengundi`, `password`) VALUES
('P001', 'Amelia Clarke', 'bingus'),
('P002', 'Charlotte Hayes', 'floppa'),
('P003', 'Genghis Khan', 'wunkus'),
('P004', 'George Whitmore', 'wawa'),
('P005', 'Harry Foster', 'slop');

-- --------------------------------------------------------

--
-- Table structure for table `undian`
--

CREATE TABLE `undian` (
  `idpengundi` varchar(10) NOT NULL,
  `idmakanan` varchar(10) NOT NULL,
  `tarikh` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `undian`
--

INSERT INTO `undian` (`idpengundi`, `idmakanan`, `tarikh`) VALUES
('P001', 'M005', '2025-02-09'),
('P002', 'M003', '2025-03-09'),
('P003', 'M002', '2025-07-09'),
('P004', 'M004', '2025-11-09'),
('P005', 'M003', '2025-12-09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`idadmin`);

--
-- Indexes for table `makanan`
--
ALTER TABLE `makanan`
  ADD PRIMARY KEY (`idmakanan`),
  ADD KEY `idadmin` (`idadmin`);

--
-- Indexes for table `pengundi`
--
ALTER TABLE `pengundi`
  ADD PRIMARY KEY (`idpengundi`);

--
-- Indexes for table `undian`
--
ALTER TABLE `undian`
  ADD PRIMARY KEY (`idpengundi`,`idmakanan`,`tarikh`),
  ADD KEY `idmakanan` (`idmakanan`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `makanan`
--
ALTER TABLE `makanan`
  ADD CONSTRAINT `makanan_ibfk_1` FOREIGN KEY (`idadmin`) REFERENCES `admin` (`idadmin`) ON DELETE SET NULL;

--
-- Constraints for table `undian`
--
ALTER TABLE `undian`
  ADD CONSTRAINT `undian_ibfk_1` FOREIGN KEY (`idpengundi`) REFERENCES `pengundi` (`idpengundi`) ON DELETE CASCADE,
  ADD CONSTRAINT `undian_ibfk_2` FOREIGN KEY (`idmakanan`) REFERENCES `makanan` (`idmakanan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
