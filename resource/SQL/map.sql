-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2017 at 10:53 PM
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

DROP TABLE IF EXISTS `map`;
CREATE TABLE `map` (
  `id` int(3) UNSIGNED NOT NULL,
  `init` char(4) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '大楼缩写',
  `full_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `info` varchar(140) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '简介',
  `lat` double NOT NULL DEFAULT '0' COMMENT '纬度',
  `lng` double NOT NULL DEFAULT '0' COMMENT '经度',
  `shape` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '坐标数组',
  `pic` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '大楼照片'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `map`
--

INSERT INTO `map` (`id`, `init`, `full_name`, `info`, `lat`, `lng`, `shape`, `pic`) VALUES
(1, 'TM', 'Tait McKenzie Centre', '', 0, 0, '', ''),
(2, 'CC', 'Calumet College', '', 0, 0, '', ''),
(3, 'WSC', 'William Small Centre', '', 0, 0, '', ''),
(4, 'LAS', 'Lassonde Building', '', 0, 0, '', ''),
(5, 'YL', 'York Lanes', '', 0, 0, '', ''),
(6, 'SCL', 'Scott Library', '', 0, 0, '', ''),
(7, 'CLH', 'Curtis Lecture Halls', '', 0, 0, '', ''),
(8, 'VH', 'Vari Hall', '', 0, 0, '', ''),
(9, 'OSG', 'Osgoode Hall Law School', '', 0, 0, '', ''),
(10, 'DB', 'Victor Phillip Dahdaleh Building (Formerly TEL)', '', 0, 0, '', ''),
(11, 'BRG', 'Bergeron Centre for Engineering Excellence', '', 0, 0, '', ''),
(12, 'ACE', 'Accolade East', '', 0, 0, '', ''),
(13, 'ACW', 'Accolade West', '', 0, 0, '', ''),
(14, 'LAS', 'Lassonde Building', 'The Department of Electrical Engineering and Computer Science, Lassonde School of Engineering, is one of the leading academic research depar', 43.773972, -79.505342, '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `map`
--
ALTER TABLE `map`
  ADD PRIMARY KEY (`id`);

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
