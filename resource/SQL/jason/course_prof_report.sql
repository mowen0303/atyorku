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
-- Table structure for table `course_prof_report`
--

DROP TABLE IF EXISTS `course_prof_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_prof_report` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `course_code_id` int(11) unsigned NOT NULL,
  `prof_id` int(11) unsigned NOT NULL DEFAULT '0',
  `homework_diff` tinyint(4) NOT NULL DEFAULT '0',
  `test_diff` tinyint(4) NOT NULL DEFAULT '0',
  `content_diff` tinyint(4) NOT NULL DEFAULT '0',
  `overall_diff` tinyint(4) NOT NULL DEFAULT '0',
  `recommendation_ratio` float NOT NULL DEFAULT '0',
  `rating_count` int(11) NOT NULL DEFAULT '0',
  `count_questions` int(11) NOT NULL DEFAULT '0',
  `count_solved_questions` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_course_prof_report_course_prof` (`course_code_id`,`prof_id`),
  KEY `fk_course_prof_report_professor` (`prof_id`),
  CONSTRAINT `fk_course_prof_report_course_code` FOREIGN KEY (`course_code_id`) REFERENCES `course_code` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_course_prof_report_professor` FOREIGN KEY (`prof_id`) REFERENCES `professor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_prof_report`
--

LOCK TABLES `course_prof_report` WRITE;
/*!40000 ALTER TABLE `course_prof_report` DISABLE KEYS */;
INSERT INTO `course_prof_report` VALUES (5,1341,3714,0,0,0,0,0,0,0,0),(6,1342,920,4,4,4,4,1,1,0,0),(7,1323,2125,1,2,1,1,1,1,0,0);
/*!40000 ALTER TABLE `course_prof_report` ENABLE KEYS */;
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
