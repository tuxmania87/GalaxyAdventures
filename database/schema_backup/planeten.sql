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
-- Table structure for table `planeten`
--

DROP TABLE IF EXISTS `planeten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `planeten` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `x` int NOT NULL,
  `y` int NOT NULL,
  `orbit` tinyint NOT NULL,
  `system` int NOT NULL DEFAULT '0',
  `besitzer` int NOT NULL,
  `energie` float NOT NULL,
  `maxenergie` int NOT NULL,
  `energieoutput` int NOT NULL,
  `warpkern` int NOT NULL,
  `maxwarpkern` int NOT NULL,
  `warpkernstatus` tinyint(1) NOT NULL,
  `laser` int NOT NULL,
  `torpedo` varchar(200) NOT NULL,
  `maxtorpedo` int NOT NULL,
  `lrs` mediumint NOT NULL,
  `hull` int NOT NULL,
  `maxhull` int NOT NULL,
  `schilde` int NOT NULL,
  `maxschilde` int NOT NULL,
  `schildstatus` tinyint NOT NULL,
  `bild` varchar(200) NOT NULL,
  `alarmstufe` varchar(10) NOT NULL DEFAULT 'green',
  `typ` char(5) NOT NULL,
  `frachtraum` varchar(255) NOT NULL,
  `maxfrachtraum` varchar(255) NOT NULL,
  `lager` int NOT NULL,
  `skillenergie` tinyint NOT NULL,
  `skillbau` tinyint NOT NULL,
  `skillerz` int NOT NULL,
  `flotte` int NOT NULL,
  `skillbase` tinyint NOT NULL,
  `skilldeut` tinyint NOT NULL,
  `quest` tinyint NOT NULL,
  `phaser` int NOT NULL,
  `maxphaser` int NOT NULL,
  `gondeln` int NOT NULL,
  `maxgondeln` int NOT NULL,
  `nachricht` text NOT NULL,
  `skilltranswarp` int NOT NULL,
  `skilltarnung` int NOT NULL,
  `heimat` int NOT NULL,
  `tarnung` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `x` (`x`),
  KEY `y` (`y`)
) ENGINE=MyISAM AUTO_INCREMENT=777 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-01-05 20:53:33
