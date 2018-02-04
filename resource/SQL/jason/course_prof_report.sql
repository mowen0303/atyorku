-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 04, 2018 at 07:09 PM
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
-- Table structure for table `course_prof_report`
--

CREATE TABLE `course_prof_report` (
  `id` int(11) UNSIGNED NOT NULL,
  `course_code_id` int(11) UNSIGNED NOT NULL,
  `prof_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `homework_diff` decimal(4,1) NOT NULL DEFAULT '0.0',
  `test_diff` decimal(4,1) NOT NULL DEFAULT '0.0',
  `content_diff` decimal(4,1) NOT NULL DEFAULT '0.0',
  `overall_diff` decimal(4,1) NOT NULL DEFAULT '0.0',
  `avg_grade` enum('A+','A','B+','B','C+','C','D+','D','E','F','') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `recommendation_ratio` float NOT NULL DEFAULT '0',
  `rating_count` int(11) NOT NULL DEFAULT '0',
  `count_questions` int(11) NOT NULL DEFAULT '0',
  `count_solved_questions` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course_prof_report`
--
ALTER TABLE `course_prof_report`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_course_prof_report_course_prof` (`course_code_id`,`prof_id`),
  ADD KEY `fk_course_prof_report_professor` (`prof_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course_prof_report`
--
ALTER TABLE `course_prof_report`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course_prof_report`
--
ALTER TABLE `course_prof_report`
  ADD CONSTRAINT `fk_course_prof_report_course_code` FOREIGN KEY (`course_code_id`) REFERENCES `course_code` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_course_prof_report_professor` FOREIGN KEY (`prof_id`) REFERENCES `professor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
