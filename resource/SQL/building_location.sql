-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2017 at 01:27 PM
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
-- Table structure for table `building_location`
--

CREATE TABLE `building_location` (
  `id` int(3) UNSIGNED NOT NULL,
  `initial` char(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '大楼缩写',
  `full_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `info` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '简介',
  `latit` double NOT NULL COMMENT '纬度',
  `longt` double NOT NULL COMMENT '经度'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `building_location`
--

INSERT INTO `building_location` (`id`, `initial`, `full_name`, `info`, `latit`, `longt`) VALUES
(4, 'TM', 'Tait McKenzie Centre', '', 0, 0),
(10, 'CC', 'Calumet College', '', 0, 0),
(15, 'WSC', 'William Small Centre', '', 0, 0),
(19, 'LAS', 'Lassonde Building', '', 0, 0),
(24, 'YL', 'York Lanes', '', 0, 0),
(25, 'SCL', 'Scott Library', '', 0, 0),
(26, 'CLH', 'Curtis Lecture Halls', '', 0, 0),
(30, 'VH', 'Vari Hall', '', 0, 0),
(39, 'DB', 'Victor Phillip Dahdaleh Building (Formerly TEL)', '', 0, 0),
(81, 'BRG', 'Bergeron Centre for Engineering Excellence', '', 0, 0),
(92, 'ACE', 'Accolade East', '', 0, 0),
(93, 'ACW', 'Accolade West', '', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `building_location`
--
ALTER TABLE `building_location`
  ADD PRIMARY KEY (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
