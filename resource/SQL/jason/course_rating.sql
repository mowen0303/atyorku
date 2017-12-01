-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 30, 2017 at 09:44 PM
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
-- Table structure for table `course_rating`
--

CREATE TABLE `course_rating` (
  `id` int(11) UNSIGNED NOT NULL,
  `course_code_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `prof_id` int(11) UNSIGNED NOT NULL,
  `content_diff` tinyint(4) NOT NULL,
  `homework_diff` tinyint(4) NOT NULL,
  `test_diff` tinyint(4) NOT NULL,
  `has_textbook` tinyint(1) NOT NULL,
  `grade` enum('A+','A','B','C','D','E','F','U') COLLATE utf8mb4_unicode_ci DEFAULT 'U' COMMENT '''U'' is unknown',
  `year` smallint(6) NOT NULL,
  `term` tinyint(4) NOT NULL,
  `comment` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recommendation` tinyint(1) NOT NULL,
  `publish_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course_rating`
--
ALTER TABLE `course_rating`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_course_rating_user` (`user_id`),
  ADD KEY `fk_course_rating_course_code` (`course_code_id`) USING BTREE,
  ADD KEY `fk_course_rating_professor` (`prof_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course_rating`
--
ALTER TABLE `course_rating`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course_rating`
--
ALTER TABLE `course_rating`
  ADD CONSTRAINT `fk_course_rating_course_code` FOREIGN KEY (`course_code_id`) REFERENCES `course_code` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_course_rating_professor` FOREIGN KEY (`prof_id`) REFERENCES `professor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_course_rating_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
