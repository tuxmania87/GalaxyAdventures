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
-- Table structure for table `bauplan`
--

DROP TABLE IF EXISTS `bauplan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bauplan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `baukosten` varchar(255) NOT NULL,
  `maxhull` int NOT NULL,
  `maxschilde` int NOT NULL,
  `laser` int NOT NULL,
  `maxtorpedo` int NOT NULL,
  `maxphaser` int NOT NULL,
  `maxtorpedohitze` int NOT NULL,
  `maxgondeln` int NOT NULL,
  `lager` int NOT NULL,
  `maxfrachtraum` varchar(255) NOT NULL,
  `maxenergie` int NOT NULL,
  `energieoutput` int NOT NULL,
  `flugkosten` double NOT NULL DEFAULT '1',
  `maxwarpkern` int NOT NULL,
  `skilltranswarp` int NOT NULL,
  `skilltarnung` int NOT NULL,
  `skillbase` int NOT NULL,
  `skilldeut` int NOT NULL,
  `skillerz` int NOT NULL,
  `skillbau` int NOT NULL,
  `lrs` int NOT NULL,
  `typ` char(1) NOT NULL,
  `klasse` varchar(250) NOT NULL,
  `bild` varchar(250) NOT NULL,
  `notes` text NOT NULL,
  `siedler` tinyint(1) NOT NULL,
  `bauzeit` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
