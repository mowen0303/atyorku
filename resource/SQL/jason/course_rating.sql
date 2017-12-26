-- MySQL dump 10.16  Distrib 10.1.26-MariaDB, for osx10.6 (i386)
--
-- Host: localhost    Database: atyorku
-- ------------------------------------------------------
-- Server version	10.1.26-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `course_rating`
--

DROP TABLE IF EXISTS `course_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_rating` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `course_code_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `prof_id` int(11) unsigned NOT NULL,
  `content_diff` tinyint(4) NOT NULL,
  `homework_diff` tinyint(4) NOT NULL,
  `test_diff` tinyint(4) NOT NULL,
  `has_textbook` tinyint(1) NOT NULL,
  `grade` enum('','A+','A','B+','B','C+','C','D+','D','E','F') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT ''''' is unknown',
  `year` smallint(6) NOT NULL,
  `term` enum('Winter','Summer','Summer 1','Summer 2','Year','Fall') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recommendation` tinyint(1) NOT NULL,
  `publish_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_course_rating_user` (`user_id`),
  KEY `fk_course_rating_course_code` (`course_code_id`) USING BTREE,
  KEY `fk_course_rating_professor` (`prof_id`) USING BTREE,
  CONSTRAINT `fk_course_rating_course_code` FOREIGN KEY (`course_code_id`) REFERENCES `course_code` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_course_rating_professor` FOREIGN KEY (`prof_id`) REFERENCES `professor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_course_rating_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_rating`
--

LOCK TABLES `course_rating` WRITE;
/*!40000 ALTER TABLE `course_rating` DISABLE KEYS */;
INSERT INTO `course_rating` VALUES (13,1342,1,920,4,4,4,0,'A+',2017,'Fall','Nice Prof. Very easy pass.',1,1514246298),(14,1323,1,2125,1,1,2,0,'A+',1996,'Summer','so easy man.',1,1514251762);
/*!40000 ALTER TABLE `course_rating` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-25 19:46:15
