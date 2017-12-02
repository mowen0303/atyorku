-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2017 at 01:23 AM
-- Server version: 10.1.19-MariaDB
-- PHP Version: 5.6.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `atyorku`
--

-- --------------------------------------------------------

--
-- Table structure for table `map`
--

CREATE TABLE `map` (
  `id` int(3) UNSIGNED NOT NULL,
  `init` char(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '大楼缩写',
  `full_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `info` varchar(140) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '简介',
  `latitude` double DEFAULT NULL COMMENT '纬度',
  `longitude` double DEFAULT NULL COMMENT '经度',
  `shape` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '坐标数组'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `map`
--

INSERT INTO `map` (`id`, `init`, `full_name`, `info`, `latitude`, `longitude`, `shape`) VALUES
(1, 'TM', 'Tait McKenzie Centre', '', 43.774487, -79.509565, ''),
(2, 'CC', 'Calumet College', '', 43.772858, -79.50986, ''),
(3, 'WSC', 'William Small Centre', '', 43.772999, -79.507766, ''),
(4, 'LAS', 'Lassonde Building', 'The Department of Electrical Engineering and Computer Science, Lassonde School of Engineering, is one of the leading academic research depar', 43.773966, -79.505355, ''),
(5, 'YL', 'York Lanes', '', 43.774268, -79.501666, ''),
(6, 'SCL', 'Scott Library', '', 43.772421, -79.505528, ''),
(7, 'CLH', 'Curtis Lecture Halls', '', 43.773139, -79.505315, ''),
(8, 'VH', 'Vari Hall', '', 43.773077, -79.503456, ''),
(9, 'OSG', 'Osgoode Hall Law School', '', 43.770724, -79.50449, ''),
(10, 'DB', 'Victor Phillip Dahdaleh Building (Formerly TEL)', '', 43.771363, -79.500897, ''),
(11, 'BRG', 'Bergeron Centre for Engineering Excellence', '', 43.77225, -79.506505, ''),
(12, 'ACE', 'Accolade East', '', 0, 0, ''),
(13, 'ACW', 'Accolade West', '', 0, 0, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `map`
--
ALTER TABLE `map`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `init` (`init`),
  ADD KEY `init_2` (`init`),
  ADD KEY `full_name` (`full_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `map`
--
ALTER TABLE `map`
  MODIFY `id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
