-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-12-12 03:50:52
-- 服务器版本： 10.1.25-MariaDB
-- PHP Version: 7.1.7

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
-- 表的结构 `ad`
--

CREATE TABLE `ad` (
  `id` int(11) UNSIGNED NOT NULL,
  `ad_category_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `view_count` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `sponsor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_id_1` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `ad_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publish_time` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `expiration_time` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `ad_category`
--

CREATE TABLE `ad_category` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ads_count` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `comment`
--

CREATE TABLE `comment` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `sender_id` int(11) UNSIGNED NOT NULL,
  `receiver_id` int(11) UNSIGNED NOT NULL,
  `section_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `course_question`
--

CREATE TABLE `course_question` (
  `id` int(11) UNSIGNED NOT NULL,
  `course_code_id` int(11) UNSIGNED NOT NULL,
  `prof_id` int(11) UNSIGNED NOT NULL,
  `questioner_user_id` int(11) UNSIGNED NOT NULL,
  `answerer_user_id` int(11) UNSIGNED NOT NULL,
  `solution_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_id_1` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `img_id_2` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `img_id_3` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `reward_amount` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `count_solutions` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `count_views` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `time_posted` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `time_solved` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `course_solution`
--

CREATE TABLE `course_solution` (
  `id` int(11) UNSIGNED NOT NULL,
  `question_id` int(11) UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_id_1` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `img_id_2` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `img_id_3` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `time_posted` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `time_approved` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `count_views` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `questioner_user_id` int(11) UNSIGNED NOT NULL,
  `answerer_user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `event`
--

CREATE TABLE `event` (
  `id` int(11) UNSIGNED NOT NULL,
  `event_category_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_id_1` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `img_id_2` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `img_id_3` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `location_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_fee` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `publish_time` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `expiration_time` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `event_time` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `max_participants` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `count_participants` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `count_views` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `count_comments` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `sponsor_user_id` int(11) UNSIGNED NOT NULL,
  `sponsor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sponsor_wechat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sponsor_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sponsor_telephone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `event_category`
--

CREATE TABLE `event_category` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `count_events` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `event_participant`
--

CREATE TABLE `event_participant` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `event_id` int(11) UNSIGNED NOT NULL,
  `register_time` int(11) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `transaction`
--

CREATE TABLE `transaction` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ad`
--
ALTER TABLE `ad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ad_category_id` (`ad_category_id`);

--
-- Indexes for table `ad_category`
--
ALTER TABLE `ad_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `course_question`
--
ALTER TABLE `course_question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `answerer_user_id` (`answerer_user_id`),
  ADD KEY `course_code_id` (`course_code_id`),
  ADD KEY `prof_id` (`prof_id`),
  ADD KEY `questioner_user_id` (`questioner_user_id`);

--
-- Indexes for table `course_solution`
--
ALTER TABLE `course_solution`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questioner_user_id` (`questioner_user_id`),
  ADD KEY `answerer_user_id` (`answerer_user_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sponsor_user_id` (`sponsor_user_id`),
  ADD KEY `event_category_id` (`event_category_id`);

--
-- Indexes for table `event_category`
--
ALTER TABLE `event_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_participant`
--
ALTER TABLE `event_participant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `ad`
--
ALTER TABLE `ad`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- 使用表AUTO_INCREMENT `ad_category`
--
ALTER TABLE `ad_category`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- 使用表AUTO_INCREMENT `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- 使用表AUTO_INCREMENT `course_question`
--
ALTER TABLE `course_question`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- 使用表AUTO_INCREMENT `course_solution`
--
ALTER TABLE `course_solution`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- 使用表AUTO_INCREMENT `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- 使用表AUTO_INCREMENT `event_category`
--
ALTER TABLE `event_category`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- 使用表AUTO_INCREMENT `event_participant`
--
ALTER TABLE `event_participant`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- 使用表AUTO_INCREMENT `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;
--
-- 限制导出的表
--

--
-- 限制表 `ad`
--
ALTER TABLE `ad`
  ADD CONSTRAINT `ad_ibfk_1` FOREIGN KEY (`ad_category_id`) REFERENCES `ad_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `course_question`
--
ALTER TABLE `course_question`
  ADD CONSTRAINT `course_question_ibfk_2` FOREIGN KEY (`course_code_id`) REFERENCES `course_code` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_question_ibfk_3` FOREIGN KEY (`prof_id`) REFERENCES `professor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_question_ibfk_4` FOREIGN KEY (`questioner_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `course_solution`
--
ALTER TABLE `course_solution`
  ADD CONSTRAINT `course_solution_ibfk_1` FOREIGN KEY (`questioner_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_solution_ibfk_2` FOREIGN KEY (`answerer_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_solution_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `course_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_2` FOREIGN KEY (`sponsor_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `event_ibfk_3` FOREIGN KEY (`event_category_id`) REFERENCES `event_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `event_participant`
--
ALTER TABLE `event_participant`
  ADD CONSTRAINT `event_participant_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `event_participant_ibfk_3` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
