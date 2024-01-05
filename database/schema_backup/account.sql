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
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(40) NOT NULL,
  `aktion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `allianz` int NOT NULL DEFAULT '0',
  `nickname` varchar(300) NOT NULL DEFAULT 'Kolonist',
  `passwort` varchar(200) NOT NULL,
  `email` varchar(250) NOT NULL,
  `inaktiv` int DEFAULT '0',
  `sponsor` tinyint NOT NULL DEFAULT '0',
  `mitglied` int NOT NULL DEFAULT '0',
  `bild` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `beschreibung` text NOT NULL,
  `regdatum` datetime NOT NULL,
  `regip` varchar(200) NOT NULL,
  `urlaub` tinyint NOT NULL DEFAULT '0',
  `level` int NOT NULL DEFAULT '1',
  `mapper` tinyint(1) NOT NULL DEFAULT '0',
  `beta` tinyint(1) NOT NULL DEFAULT '1',
  `kills` int NOT NULL DEFAULT '0',
  `chat` tinyint NOT NULL DEFAULT '1',
  `moderator` tinyint(1) NOT NULL DEFAULT '0',
  `gruppe` int NOT NULL DEFAULT '0',
  `gruppeinvite` tinyint(1) NOT NULL DEFAULT '0',
  `wpunkte` double NOT NULL DEFAULT '0',
  `sessionid` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-01-05 20:53:32
