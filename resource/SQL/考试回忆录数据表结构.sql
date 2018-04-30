-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2018-04-30 05:05:12
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
-- 表的结构 `knowledge`
--

CREATE TABLE `knowledge` (
  `id` int(10) UNSIGNED NOT NULL,
  `seller_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `knowledge_category_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `img_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `course_code_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `prof_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `count_knowledge_points` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `count_views` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `count_sold` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `count_comments` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `publish_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `term_year` int(4) UNSIGNED NOT NULL DEFAULT '0',
  `term_semester` enum('Fall','Winter','Summer','Year','Summer 1','Summer 2','') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sort` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `knowledge_category`
--

CREATE TABLE `knowledge_category` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `knowledge_point`
--

CREATE TABLE `knowledge_point` (
  `id` int(11) NOT NULL,
  `knowledge_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `knowledge`
--
ALTER TABLE `knowledge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `knowledge_ibfk_1` (`knowledge_category_id`);

--
-- Indexes for table `knowledge_category`
--
ALTER TABLE `knowledge_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `knowledge_point`
--
ALTER TABLE `knowledge_point`
  ADD PRIMARY KEY (`id`),
  ADD KEY `knowledge_id` (`knowledge_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `knowledge`
--
ALTER TABLE `knowledge`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- 使用表AUTO_INCREMENT `knowledge_category`
--
ALTER TABLE `knowledge_category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- 使用表AUTO_INCREMENT `knowledge_point`
--
ALTER TABLE `knowledge_point`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- 限制导出的表
--

--
-- 限制表 `knowledge`
--
ALTER TABLE `knowledge`
  ADD CONSTRAINT `knowledge_ibfk_1` FOREIGN KEY (`knowledge_category_id`) REFERENCES `knowledge_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `knowledge_point`
--
ALTER TABLE `knowledge_point`
  ADD CONSTRAINT `knowledge_point_ibfk_1` FOREIGN KEY (`knowledge_id`) REFERENCES `knowledge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
