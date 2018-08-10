-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 10, 2018 at 03:34 AM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 7.0.30

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
-- Table structure for table `institution`
--

CREATE TABLE `institution` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('大学','高中') COLLATE utf8mb4_unicode_ci NOT NULL,
  `coordinate` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `term_start_dates` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `term_end_dates` char(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video`
--

CREATE TABLE `video` (
  `id` int(11) UNSIGNED NOT NULL,
  `url` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` int(11) UNSIGNED NOT NULL,
  `length` int(11) UNSIGNED NOT NULL,
  `album_id` int(11) UNSIGNED NOT NULL,
  `section_id` int(11) UNSIGNED NOT NULL,
  `instructor_id` int(11) UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` char(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `publish_time` int(11) UNSIGNED NOT NULL,
  `update_time` int(11) UNSIGNED NOT NULL,
  `title` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_img_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `review_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0=waiting,1=pass,-1=fail',
  `review_response` char(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sort` int(11) UNSIGNED NOT NULL,
  `report` tinyint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_album`
--

CREATE TABLE `video_album` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` char(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_id` int(11) UNSIGNED NOT NULL,
  `course_code_id` int(11) UNSIGNED NOT NULL,
  `professor_id` int(11) UNSIGNED NOT NULL,
  `institution_id` int(11) UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `publish_time` int(11) UNSIGNED NOT NULL,
  `last_modified_time` int(11) UNSIGNED NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_available` tinyint(1) NOT NULL DEFAULT '0',
  `cover_img_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `count_video` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `count_participants` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `count_clicks` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `count_comments` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `sort` tinyint(8) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_album_tag`
--

CREATE TABLE `video_album_tag` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` char(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_img_id` int(11) UNSIGNED NOT NULL,
  `count_album` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_album_tag_video_album`
--

CREATE TABLE `video_album_tag_video_album` (
  `id` int(11) UNSIGNED NOT NULL,
  `album_id` int(11) UNSIGNED NOT NULL,
  `tag_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_section`
--

CREATE TABLE `video_section` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `album_id` int(11) UNSIGNED NOT NULL,
  `count_video` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `sort` tinyint(8) UNSIGNED NOT NULL DEFAULT '0',
  `publish_time` int(11) UNSIGNED NOT NULL,
  `update_time` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_stats`
--

CREATE TABLE `video_stats` (
  `id` int(11) UNSIGNED NOT NULL,
  `video_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `count_player_play` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `count_player_pause` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `count_player_jump` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `total_time` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `institution`
--
ALTER TABLE `institution`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`id`),
  ADD KEY `album_id` (`album_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `instructor_id` (`instructor_id`),
  ADD KEY `fk_video_instructor_id` (`instructor_id`) USING BTREE,
  ADD KEY `fk_video_image_id` (`cover_img_id`) USING BTREE,
  ADD KEY `fk_video_section_id` (`section_id`) USING BTREE;

--
-- Indexes for table `video_album`
--
ALTER TABLE `video_album`
  ADD PRIMARY KEY (`id`),
  ADD KEY `title` (`title`(191)),
  ADD KEY `course_code_id` (`course_code_id`),
  ADD KEY `professor_id` (`professor_id`),
  ADD KEY `fk_video_album_image_id` (`cover_img_id`) USING BTREE,
  ADD KEY `fk_video_album_user_id` (`user_id`) USING BTREE;

--
-- Indexes for table `video_album_tag`
--
ALTER TABLE `video_album_tag`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_video_album_category_title` (`title`) USING BTREE;

--
-- Indexes for table `video_album_tag_video_album`
--
ALTER TABLE `video_album_tag_video_album`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_video_album_tag_video_album_tag_album_id` (`tag_id`,`album_id`),
  ADD KEY `fk_video_album_tag_album_album_id` (`album_id`);

--
-- Indexes for table `video_section`
--
ALTER TABLE `video_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video_stats`
--
ALTER TABLE `video_stats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `video_id` (`video_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `institution`
--
ALTER TABLE `institution`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video`
--
ALTER TABLE `video`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_album`
--
ALTER TABLE `video_album`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_album_tag`
--
ALTER TABLE `video_album_tag`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_album_tag_video_album`
--
ALTER TABLE `video_album_tag_video_album`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_section`
--
ALTER TABLE `video_section`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_stats`
--
ALTER TABLE `video_stats`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `video`
--
ALTER TABLE `video`
  ADD CONSTRAINT `fk_video_album_id` FOREIGN KEY (`album_id`) REFERENCES `video_album` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_video_instructor_id` FOREIGN KEY (`instructor_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_video_section_id` FOREIGN KEY (`section_id`) REFERENCES `video_section` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `video_album`
--
ALTER TABLE `video_album`
  ADD CONSTRAINT `fk_video_album_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `video_album_tag_video_album`
--
ALTER TABLE `video_album_tag_video_album`
  ADD CONSTRAINT `fk_video_album_tag_album_album_id` FOREIGN KEY (`album_id`) REFERENCES `video_album` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_video_album_tag_album_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `video_album_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
