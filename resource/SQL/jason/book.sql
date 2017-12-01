-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 30, 2017 at 09:43 PM
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

CREATE TABLE `book` (
  `id` int(11) UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `name` char(255) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `book_category_id` int(11) UNSIGNED NOT NULL,
  `course_id` int(11) UNSIGNED NOT NULL,
  `image_id_one` int(11) UNSIGNED DEFAULT NULL,
  `image_id_two` int(11) UNSIGNED DEFAULT NULL,
  `image_id_three` int(11) UNSIGNED DEFAULT NULL,
  `professor_id` int(11) UNSIGNED DEFAULT NULL,
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`(191)),
  ADD KEY `count_view_index` (`count_view`),
  ADD KEY `fk_book_user` (`user_id`),
  ADD KEY `fk_book_course_code` (`course_id`),
  ADD KEY `fk_book_image_1` (`image_id_one`),
  ADD KEY `fk_book_image_2` (`image_id_two`),
  ADD KEY `fk_book_image_3` (`image_id_three`),
  ADD KEY `fk_book_professor` (`professor_id`),
  ADD KEY `fk_book_book_category` (`book_category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `fk_book_book_category` FOREIGN KEY (`book_category_id`) REFERENCES `book_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_book_course_code` FOREIGN KEY (`course_id`) REFERENCES `course_code` (`id`),
  ADD CONSTRAINT `fk_book_image_1` FOREIGN KEY (`image_id_one`) REFERENCES `image` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_book_image_2` FOREIGN KEY (`image_id_two`) REFERENCES `image` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_book_image_3` FOREIGN KEY (`image_id_three`) REFERENCES `image` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_book_professor` FOREIGN KEY (`professor_id`) REFERENCES `professor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_book_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
