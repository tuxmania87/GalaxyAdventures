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
-- Table structure for table `phpbb_privmsgs`
--

DROP TABLE IF EXISTS `phpbb_privmsgs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phpbb_privmsgs` (
  `msg_id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `root_level` mediumint unsigned NOT NULL DEFAULT '0',
  `author_id` mediumint unsigned NOT NULL DEFAULT '0',
  `icon_id` mediumint unsigned NOT NULL DEFAULT '0',
  `author_ip` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `message_time` int unsigned NOT NULL DEFAULT '0',
  `enable_bbcode` tinyint unsigned NOT NULL DEFAULT '1',
  `enable_smilies` tinyint unsigned NOT NULL DEFAULT '1',
  `enable_magic_url` tinyint unsigned NOT NULL DEFAULT '1',
  `enable_sig` tinyint unsigned NOT NULL DEFAULT '1',
  `message_subject` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `message_text` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `message_edit_reason` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `message_edit_user` mediumint unsigned NOT NULL DEFAULT '0',
  `message_attachment` tinyint unsigned NOT NULL DEFAULT '0',
  `bbcode_bitfield` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `bbcode_uid` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL DEFAULT '',
  `message_edit_time` int unsigned NOT NULL DEFAULT '0',
  `message_edit_count` smallint unsigned NOT NULL DEFAULT '0',
  `to_address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `bcc_address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `message_reported` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`msg_id`),
  KEY `author_ip` (`author_ip`),
  KEY `message_time` (`message_time`),
  KEY `author_id` (`author_id`),
  KEY `root_level` (`root_level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
