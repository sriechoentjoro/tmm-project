-- ============================================
-- Database: cms_tmm_apprentice_document_ticketings
-- Exported: 20251124_035402
-- ============================================

DROP DATABASE IF EXISTS `cms_tmm_apprentice_document_ticketings`;
CREATE DATABASE `cms_tmm_apprentice_document_ticketings` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cms_tmm_apprentice_document_ticketings`;
-- MariaDB dump 10.19  Distrib 10.4.27-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cms_tmm_apprentice_document_ticketings
-- ------------------------------------------------------
-- Server version	10.4.27-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `apprentice_flights`
--

DROP TABLE IF EXISTS `apprentice_flights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apprentice_flights` (
  `id` int(11) NOT NULL,
  `apprentice_ticket_id` int(11) NOT NULL,
  `master_airline_id` int(11) NOT NULL,
  `flight_number` varchar(20) NOT NULL,
  `departure_airport_id` int(11) NOT NULL,
  `arrival_airport_id` int(11) NOT NULL,
  `departure_datetime` datetime NOT NULL,
  `arrival_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apprentice_flights`
--

LOCK TABLES `apprentice_flights` WRITE;
/*!40000 ALTER TABLE `apprentice_flights` DISABLE KEYS */;
INSERT INTO `apprentice_flights` VALUES (5,5,1,'GA*888',1,2,'2025-08-15 23:58:00','2025-08-16 01:58:00'),(15,5,1,'888',1,1,'2025-08-29 14:50:00','2025-08-29 14:51:00'),(16,1,1,'GIA888',144,141,'2025-08-18 20:32:00','2025-08-18 11:32:00'),(18,23,1,'454544347647',82,144,'2025-08-11 09:00:00','2025-08-11 12:00:00'),(19,23,1,'267625839232',82,1,'2025-08-25 14:00:00','2025-08-25 16:00:00'),(22,1,1,'GA889',141,1,'2025-09-11 00:00:00','2025-09-11 08:00:00');
/*!40000 ALTER TABLE `apprentice_flights` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-24  3:54:03
