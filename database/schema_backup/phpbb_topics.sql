-- MySQL dump 10.13  Distrib 8.0.35, for Linux (x86_64)
--
-- Host: localhost    Database: game2
-- ------------------------------------------------------
-- Server version	8.0.35-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `phpbb_topics`
--

DROP TABLE IF EXISTS `phpbb_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phpbb_topics` (
  `topic_id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `forum_id` mediumint unsigned NOT NULL DEFAULT '0',
  `icon_id` mediumint unsigned NOT NULL DEFAULT '0',
  `topic_attachment` tinyint unsigned NOT NULL DEFAULT '0',
  `topic_approved` tinyint unsigned NOT NULL DEFAULT '1',
  `topic_reported` tinyint unsigned NOT NULL DEFAULT '0',
  `topic_title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `topic_poster` mediumint unsigned NOT NULL DEFAULT '0',
  `topic_time` int unsigned NOT NULL DEFAULT '0',
  `topic_time_limit` int unsigned NOT NULL DEFAULT '0',
  `topic_views` mediumint unsigned NOT NULL DEFAULT '0',
  `topic_replies` mediumint unsigned NOT NULL DEFAULT '0',
  `topic_replies_real` mediumint unsigned NOT NULL DEFAULT '0',
  `topic_status` tinyint NOT NULL DEFAULT '0',
  `topic_type` tinyint NOT NULL DEFAULT '0',
  `topic_first_post_id` mediumint unsigned NOT NULL DEFAULT '0',
  `topic_first_poster_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `topic_first_poster_colour` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `topic_last_post_id` mediumint unsigned NOT NULL DEFAULT '0',
  `topic_last_poster_id` mediumint unsigned NOT NULL DEFAULT '0',
  `topic_last_poster_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `topic_last_poster_colour` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `topic_last_post_subject` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `topic_last_post_time` int unsigned NOT NULL DEFAULT '0',
  `topic_last_view_time` int unsigned NOT NULL DEFAULT '0',
  `topic_moved_id` mediumint unsigned NOT NULL DEFAULT '0',
  `topic_bumped` tinyint unsigned NOT NULL DEFAULT '0',
  `topic_bumper` mediumint unsigned NOT NULL DEFAULT '0',
  `poll_title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `poll_start` int unsigned NOT NULL DEFAULT '0',
  `poll_length` int unsigned NOT NULL DEFAULT '0',
  `poll_max_options` tinyint NOT NULL DEFAULT '1',
  `poll_last_vote` int unsigned NOT NULL DEFAULT '0',
  `poll_vote_change` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `forum_id_type` (`forum_id`,`topic_type`),
  KEY `last_post_time` (`topic_last_post_time`),
  KEY `topic_approved` (`topic_approved`),
  KEY `forum_appr_last` (`forum_id`,`topic_approved`,`topic_last_post_id`),
  KEY `fid_time_moved` (`forum_id`,`topic_last_post_time`,`topic_moved_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
