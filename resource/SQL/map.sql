-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2017 at 11:28 PM
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
  `init` char(4) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '大楼缩写',
  `full_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `info` varchar(140) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '简介',
  `lat` double NOT NULL COMMENT '纬度',
  `lng` double NOT NULL COMMENT '经度',
  `shape` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '坐标数组'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `map`
--

INSERT INTO `map` (`id`, `init`, `full_name`, `info`, `lat`, `lng`, `shape`) VALUES
(4, 'TM', 'Tait McKenzie Centre', '', 0, 0, ''),
(10, 'CC', 'Calumet College', '', 0, 0, ''),
(15, 'WSC', 'William Small Centre', '', 0, 0, ''),
(19, 'LAS', 'Lassonde Building', '', 0, 0, ''),
(24, 'YL', 'York Lanes', '', 0, 0, ''),
(25, 'SCL', 'Scott Library', '', 0, 0, ''),
(26, 'CLH', 'Curtis Lecture Halls', '', 0, 0, ''),
(30, 'VH', 'Vari Hall', '', 0, 0, ''),
(39, 'DB', 'Victor Phillip Dahdaleh Building (Formerly TEL)', '', 0, 0, ''),
(81, 'BRG', 'Bergeron Centre for Engineering Excellence', '', 0, 0, ''),
(92, 'ACE', 'Accolade East', '', 0, 0, ''),
(93, 'ACW', 'Accolade West', '', 0, 0, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `map`
--
ALTER TABLE `map`
  ADD PRIMARY KEY (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
