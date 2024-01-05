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
-- Table structure for table `schiffe`
--

DROP TABLE IF EXISTS `schiffe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schiffe` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'noname',
  `x` int NOT NULL,
  `y` int NOT NULL,
  `orbit` tinyint NOT NULL DEFAULT '0',
  `system` int NOT NULL,
  `besitzer` int NOT NULL,
  `energie` float NOT NULL,
  `warpkern` int NOT NULL,
  `warpkernstatus` tinyint(1) NOT NULL DEFAULT '0',
  `hull` int NOT NULL,
  `schilde` int NOT NULL DEFAULT '0',
  `schildstatus` tinyint NOT NULL DEFAULT '0',
  `alarmstufe` varchar(10) NOT NULL DEFAULT 'green',
  `typ` char(1) NOT NULL,
  `frachtraum` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `flotte` int NOT NULL DEFAULT '0',
  `phaser` int NOT NULL DEFAULT '0',
  `torpedohitze` int NOT NULL DEFAULT '0',
  `gondeln` int NOT NULL DEFAULT '0',
  `nachricht` text NOT NULL,
  `tarnung` int NOT NULL DEFAULT '0',
  `klasse` varchar(250) NOT NULL,
  `dock` int NOT NULL DEFAULT '0',
  `loot` tinyint(1) NOT NULL DEFAULT '0',
  `kills` int NOT NULL DEFAULT '0',
  `defend` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `x` (`x`),
  KEY `y` (`y`),
  KEY `x_2` (`x`,`y`,`system`)
) ENGINE=MyISAM AUTO_INCREMENT=4129 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
