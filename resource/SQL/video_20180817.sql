-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 17, 2018 at 10:12 PM
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

--
-- Dumping data for table `institution`
--

INSERT INTO `institution` (`id`, `title`, `type`, `coordinate`, `term_start_dates`, `term_end_dates`) VALUES
(1, 'York University', '大学', '12.232,34.523', '', ''),
(2, 'University of Toronto', '大学', '23.232,123.232', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `product_transaction`
--

CREATE TABLE `product_transaction` (
  `id` int(11) UNSIGNED NOT NULL,
  `buyer_transaction_id` int(11) UNSIGNED NOT NULL,
  `seller_transaction_id` int(11) UNSIGNED NOT NULL,
  `state` enum('waiting_payment','waiting_seller_refund','seller_refused_refund','waiting_admin','transaction_refunded','transaction_complete') NOT NULL DEFAULT 'waiting_payment',
  `buyer_response` char(150) NOT NULL DEFAULT '',
  `seller_response` char(150) NOT NULL DEFAULT '',
  `admin_response` char(150) NOT NULL DEFAULT '',
  `update_time` int(10) UNSIGNED NOT NULL,
  `expiration_time` int(11) NOT NULL DEFAULT '0',
  `count_video_view` tinyint(8) NOT NULL DEFAULT '0' COMMENT '单个视频观看次数'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

--
-- Dumping data for table `video`
--

INSERT INTO `video` (`id`, `url`, `size`, `length`, `album_id`, `section_id`, `instructor_id`, `price`, `description`, `publish_time`, `update_time`, `title`, `cover_img_id`, `is_deleted`, `review_status`, `review_response`, `sort`, `report`) VALUES
(1, '5c7960e277674781b09533fb4eaf2d49', 32, 149, 43, 6, 1, '12.00', 'This is a test video', 1533872506, 1534535399, 'æµ‹è¯•è§†é¢‘', 1344, 0, 1, '', 1, 0),
(2, 'a392849cdce84ef2b7e1169a8a86344e', 88, 1441, 43, 7, 1, '50.00', 'Watch our video to see two Google engineers demonstrate a mock interview question. After they code, our engineers highlight best practices for interviewing at Google.', 1534534882, 1534534882, 'How to Work at Google', 1344, 0, 1, '', 1, 0),
(3, '4dae9c252a62439085d74c7cafed4524', 131, 2388, 43, 7, 1, '90.00', 'Lecture 1: The Geometry of Linear Equations.\r\nView the complete course at: http://ocw.mit.edu/18-06S05', 1534535149, 1534535149, 'Lec 1  MIT 18.06 Linear Algebr', 1346, 0, 1, '', 2, 0);

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

--
-- Dumping data for table `video_album`
--

INSERT INTO `video_album` (`id`, `title`, `description`, `user_id`, `course_code_id`, `professor_id`, `institution_id`, `price`, `publish_time`, `last_modified_time`, `is_deleted`, `is_available`, `cover_img_id`, `count_video`, `count_participants`, `count_clicks`, `count_comments`, `sort`) VALUES
(43, 'test video album one', 'This is a test video album', 1, 188, 1, 1, '900.00', 1533604161, 1533604161, 0, 1, 1340, 3, 0, 0, 0, 0);

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

--
-- Dumping data for table `video_album_tag`
--

INSERT INTO `video_album_tag` (`id`, `title`, `cover_img_id`, `count_album`) VALUES
(6, 'category_1', 1342, 0),
(7, 'category_2', 1340, 0),
(8, 'category_3', 0, 0),
(9, 'category_4', 1343, 0);

-- --------------------------------------------------------

--
-- Table structure for table `video_album_tag_video_album`
--

CREATE TABLE `video_album_tag_video_album` (
  `id` int(11) UNSIGNED NOT NULL,
  `album_id` int(11) UNSIGNED NOT NULL,
  `tag_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `video_album_tag_video_album`
--

INSERT INTO `video_album_tag_video_album` (`id`, `album_id`, `tag_id`) VALUES
(1, 43, 6);

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

--
-- Dumping data for table `video_section`
--

INSERT INTO `video_section` (`id`, `title`, `album_id`, `count_video`, `is_deleted`, `sort`, `publish_time`, `update_time`) VALUES
(1, 'section 1', 1, 0, 1, 0, 1532313914, 1532313928),
(2, 'section 1', 2, 5, 0, 0, 1532373378, 1532373378),
(3, 'section 2', 2, 0, 1, 0, 1532373388, 1532373388),
(4, 'æµ‹è¯•3', 2, 0, 1, 0, 1532874993, 1532874999),
(5, 'section 2', 2, 0, 0, 0, 1532875841, 1532875841),
(6, 'æµ‹è¯•ç« èŠ‚', 43, 1, 0, 1, 1533872373, 1533872373),
(7, 'æµ‹è¯•ç« èŠ‚ 2', 43, 2, 0, 2, 1534534450, 1534534450);

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
-- Indexes for table `product_transaction`
--
ALTER TABLE `product_transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_transaction_buyer_transaction_id` (`buyer_transaction_id`),
  ADD KEY `fk_product_transaction_seller_transaction_id` (`seller_transaction_id`);

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_transaction`
--
ALTER TABLE `product_transaction`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video`
--
ALTER TABLE `video`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `video_album`
--
ALTER TABLE `video_album`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `video_album_tag`
--
ALTER TABLE `video_album_tag`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `video_album_tag_video_album`
--
ALTER TABLE `video_album_tag_video_album`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `video_section`
--
ALTER TABLE `video_section`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `video_stats`
--
ALTER TABLE `video_stats`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_transaction`
--
ALTER TABLE `product_transaction`
  ADD CONSTRAINT `fk_product_transaction_buyer_transaction_id` FOREIGN KEY (`buyer_transaction_id`) REFERENCES `transaction` (`id`),
  ADD CONSTRAINT `fk_product_transaction_seller_transaction_id` FOREIGN KEY (`seller_transaction_id`) REFERENCES `transaction` (`id`);

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
