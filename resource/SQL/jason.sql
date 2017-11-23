-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 23, 2017 at 06:34 AM
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
-- Table structure for table `book`
--

DROP TABLE IF EXISTS `book`;
CREATE TABLE `book` (
  `id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `name` char(255) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `book_category_id` int(11) DEFAULT NULL,
  `course_id` int(11) NOT NULL,
  `image_id_one` int(11) DEFAULT NULL,
  `image_id_two` int(11) DEFAULT NULL,
  `image_id_three` int(11) DEFAULT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `term_year` int(4) UNSIGNED DEFAULT NULL,
  `term_semester` enum('FALL','WINTER','SUMMER') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paper_type` enum('Midterm','Final','Quiz','Assignment') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count_comments` smallint(6) UNSIGNED NOT NULL,
  `count_view` smallint(6) UNSIGNED NOT NULL,
  `report` smallint(6) UNSIGNED NOT NULL,
  `sort` tinyint(3) UNSIGNED NOT NULL,
  `publish_time` int(11) NOT NULL,
  `last_modified_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `book_category`
--

DROP TABLE IF EXISTS `book_category`;
CREATE TABLE `book_category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `books_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book_category`
--

INSERT INTO `book_category` (`id`, `name`, `books_count`) VALUES
(23, 'æ•™ç§‘ä¹¦', 1),
(24, 'ä½œä¸š', 1);

-- --------------------------------------------------------

--
-- Table structure for table `course_prof_report`
--

DROP TABLE IF EXISTS `course_prof_report`;
CREATE TABLE `course_prof_report` (
  `id` int(11) NOT NULL,
  `course_code_id` int(11) NOT NULL,
  `prof_id` int(11) NOT NULL,
  `homework_diff` tinyint(4) NOT NULL,
  `test_diff` tinyint(4) NOT NULL,
  `content_diff` tinyint(4) NOT NULL,
  `overall_diff` tinyint(4) NOT NULL,
  `rating_count` int(11) NOT NULL,
  `count_questions` int(11) NOT NULL,
  `count_solved_questions` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_rate`
--

DROP TABLE IF EXISTS `course_rate`;
CREATE TABLE `course_rate` (
  `id` int(11) NOT NULL,
  `course_code_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `prof_id` int(11) NOT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `course_report`
--

DROP TABLE IF EXISTS `course_report`;
CREATE TABLE `course_report` (
  `id` int(11) NOT NULL,
  `course_code_id` int(11) NOT NULL,
  `homework_diff` tinyint(4) NOT NULL,
  `test_diff` tinyint(4) NOT NULL,
  `content_diff` tinyint(4) NOT NULL,
  `overall_diff` tinyint(4) NOT NULL,
  `rating_count` int(11) NOT NULL,
  `count_questions` int(11) NOT NULL,
  `count_solved_questions` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE `image` (
  `id` int(11) NOT NULL,
  `url` char(255) NOT NULL,
  `thumbnail_url` char(255) NOT NULL,
  `size` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `applied_table` char(25) NOT NULL COMMENT '''book'',''user'',''event'',''course'',''forum'',''guide'',''guide_class''',
  `publish_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `professor`
--

DROP TABLE IF EXISTS `professor`;
CREATE TABLE `professor` (
  `id` int(11) NOT NULL,
  `name` char(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `view_count` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `professor_report`
--

DROP TABLE IF EXISTS `professor_report`;
CREATE TABLE `professor_report` (
  `id` int(11) NOT NULL,
  `prof_id` int(11) NOT NULL,
  `homework_diff` tinyint(4) NOT NULL,
  `test_diff` tinyint(4) NOT NULL,
  `content_diff` tinyint(4) NOT NULL,
  `overall_diff` tinyint(4) NOT NULL,
  `rating_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`(191)),
  ADD KEY `book_category_id_fk` (`book_category_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `image_id_one_fk` (`image_id_one`),
  ADD KEY `image_id_three_fk` (`image_id_three`),
  ADD KEY `image_id_two_fk` (`image_id_two`),
  ADD KEY `count_view_index` (`count_view`),
  ADD KEY `course_id_fk` (`course_id`),
  ADD KEY `professor_id_fk` (`professor_id`);

--
-- Indexes for table `book_category`
--
ALTER TABLE `book_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `course_prof_report`
--
ALTER TABLE `course_prof_report`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_prof_id_uc` (`course_code_id`,`prof_id`),
  ADD KEY `prof_id` (`prof_id`);

--
-- Indexes for table `course_rate`
--
ALTER TABLE `course_rate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_code_id_fk` (`course_code_id`),
  ADD KEY `prof_id_fk` (`prof_id`);

--
-- Indexes for table `course_report`
--
ALTER TABLE `course_report`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code_id_uc` (`course_code_id`) USING BTREE;

--
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`),
  ADD UNIQUE KEY `thumbnail_url` (`thumbnail_url`);

--
-- Indexes for table `professor`
--
ALTER TABLE `professor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `professor_name_uc` (`name`) USING BTREE;

--
-- Indexes for table `professor_report`
--
ALTER TABLE `professor_report`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prof_id_uc` (`prof_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `book_category`
--
ALTER TABLE `book_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `course_prof_report`
--
ALTER TABLE `course_prof_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_rate`
--
ALTER TABLE `course_rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `professor`
--
ALTER TABLE `professor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `professor_report`
--
ALTER TABLE `professor_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course_report`
--
ALTER TABLE `course_report`
  ADD CONSTRAINT `course_report_ibfk_1` FOREIGN KEY (`course_code_id`) REFERENCES `course_code` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
