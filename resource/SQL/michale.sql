-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-09-22 23:50:00
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
  `id` int(11) NOT NULL,
  `ad_category_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `view_count` int(11) NOT NULL,
  `sponsor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ad_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publish_time` int(11) DEFAULT NULL,
  `expiration_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `ad_category`
--

CREATE TABLE `ad_category` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ads_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `event`
--

CREATE TABLE `event` (
  `id` int(11) NOT NULL,
  `event_category_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intro` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `poster_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qr_code_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_fee` int(11) NOT NULL,
  `publish_time` int(11) NOT NULL,
  `expiration_time` int(11) NOT NULL,
  `event_time` int(11) NOT NULL,
  `max_participants` int(11) NOT NULL,
  `count_participants` int(11) NOT NULL,
  `count_views` int(11) NOT NULL,
  `sponsor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sponsor_profile_img_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sponsor_wechat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sponsor_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sponsor_telephone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `event_category`
--

CREATE TABLE `event_category` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `count_events` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `event_participant`
--

CREATE TABLE `event_participant` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_name` int(11) NOT NULL,
  `user_alias` int(11) NOT NULL,
  `user_gender` int(11) NOT NULL,
  `user_profile_img_url` int(11) NOT NULL
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
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
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
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `ad`
--
ALTER TABLE `ad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `ad_category`
--
ALTER TABLE `ad_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `event_category`
--
ALTER TABLE `event_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `event_participant`
--
ALTER TABLE `event_participant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 限制导出的表
--

--
-- 限制表 `ad`
--
ALTER TABLE `ad`
  ADD CONSTRAINT `ad_ibfk_1` FOREIGN KEY (`ad_category_id`) REFERENCES `ad_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`event_category_id`) REFERENCES `event_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
