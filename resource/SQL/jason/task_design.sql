-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 29, 2018 at 02:15 PM
-- Server version: 10.1.26-MariaDB
-- PHP Version: 7.0.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
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
-- Table structure for table `task_design`
--

CREATE TABLE `task_design` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` char(255) NOT NULL,
  `bonus` int(11) NOT NULL,
  `icon_id` int(11) UNSIGNED NOT NULL,
  `book` int(6) NOT NULL DEFAULT '0',
  `course_rating` int(6) NOT NULL DEFAULT '0',
  `course_question` int(6) NOT NULL DEFAULT '0',
  `knowledge` int(6) NOT NULL DEFAULT '0',
  `forum` int(6) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `task_design`
--
ALTER TABLE `task_design`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `task_design`
--
ALTER TABLE `task_design`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
