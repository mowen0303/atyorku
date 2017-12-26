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
-- Table structure for table `book`
--

DROP TABLE IF EXISTS `book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `price` decimal(10,2) NOT NULL,
  `name` char(255) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `book_category_id` int(11) unsigned NOT NULL,
  `course_id` int(11) unsigned NOT NULL,
  `image_id_one` int(11) unsigned NOT NULL DEFAULT '0',
  `image_id_two` int(11) unsigned NOT NULL DEFAULT '0',
  `image_id_three` int(11) unsigned NOT NULL DEFAULT '0',
  `professor_id` int(11) unsigned NOT NULL DEFAULT '0',
  `term_year` int(4) unsigned NOT NULL DEFAULT '0',
  `term_semester` enum('Fall','Winter','Summer','Unknown') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unknown',
  `count_comments` smallint(6) unsigned NOT NULL DEFAULT '0',
  `count_view` smallint(6) unsigned NOT NULL DEFAULT '0',
  `report` smallint(6) unsigned NOT NULL DEFAULT '0',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `publish_time` int(11) NOT NULL,
  `last_modified_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`(191)),
  KEY `count_view_index` (`count_view`),
  KEY `fk_book_user` (`user_id`),
  KEY `fk_book_course_code` (`course_id`),
  KEY `fk_book_image_1` (`image_id_one`),
  KEY `fk_book_image_2` (`image_id_two`),
  KEY `fk_book_image_3` (`image_id_three`),
  KEY `fk_book_professor` (`professor_id`),
  KEY `fk_book_book_category` (`book_category_id`),
  CONSTRAINT `fk_book_book_category` FOREIGN KEY (`book_category_id`) REFERENCES `book_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_book_course_code` FOREIGN KEY (`course_id`) REFERENCES `course_code` (`id`),
  CONSTRAINT `fk_book_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book`
--

LOCK TABLES `book` WRITE;
/*!40000 ALTER TABLE `book` DISABLE KEYS */;
INSERT INTO `book` VALUES (1,30.00,'90åˆ†æœŸä¸­Past Paper','',1,28,222,2,0,0,0,2017,'Fall',0,0,0,0,1512183806,1513805101);
/*!40000 ALTER TABLE `book` ENABLE KEYS */;
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
