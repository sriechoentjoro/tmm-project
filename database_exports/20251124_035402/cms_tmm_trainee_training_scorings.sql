-- ============================================
-- Database: cms_tmm_trainee_training_scorings
-- Exported: 20251124_035402
-- ============================================

DROP DATABASE IF EXISTS `cms_tmm_trainee_training_scorings`;
CREATE DATABASE `cms_tmm_trainee_training_scorings` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cms_tmm_trainee_training_scorings`;
-- MariaDB dump 10.19  Distrib 10.4.27-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cms_tmm_trainee_training_scorings
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
-- Table structure for table `master_training_competencies`
--

DROP TABLE IF EXISTS `master_training_competencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_training_competencies` (
  `id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_training_competencies`
--

LOCK TABLES `master_training_competencies` WRITE;
/*!40000 ALTER TABLE `master_training_competencies` DISABLE KEYS */;
INSERT INTO `master_training_competencies` VALUES (1,'Test bab 1 ~ 8',NULL),(2,'Test bab 9 ~ 17',NULL),(3,'Test bab 18 ~ 25',NULL),(4,'Test bab 1 ~ 25',NULL),(5,'Test N5 ke 1 - Moji Goi',NULL),(6,'Test N5 ke 1 - Dokai',NULL),(7,'Test N5 ke 1 - Choukai',NULL),(8,'Test N5 ke 2 - Moji Goi',NULL),(9,'Test N5 ke 2 - Dokai',NULL),(10,'Test N5 ke 2 - Choukai',NULL),(11,'Test N5 ke 3 - Moji Goi',NULL),(12,'Test N5 ke 3 - Dokai',NULL),(13,'Test N5 ke 3 - Choukai',NULL),(14,'Test N4 - Moji Goi',NULL),(15,'Test N4 - Dokai',NULL),(16,'Test N4 - Choukai',NULL),(17,'Sikap - 1',NULL),(18,'Sikap - 2',NULL),(19,'Sikap - 3',NULL),(20,'Sikap - 4',NULL),(21,'Sikap - 5',NULL),(22,'Sikap - 6',NULL),(23,'Sikap - 7',NULL),(24,'Sikap - 8',NULL),(25,'Sikap - 9',NULL),(26,'Sikap - 10',NULL),(27,'Sikap - 11',NULL),(28,'Bahasa - 1',NULL),(29,'Bahasa - 2',NULL),(30,'Bahasa - 3',NULL),(31,'Bahasa - 4',NULL),(32,'Bahasa - 5',NULL),(33,'Bahasa - 6',NULL),(34,'Penulisan',NULL),(35,'Pendengaran',NULL),(36,'Tata Bahasa',NULL),(37,'Percakapan',NULL);
/*!40000 ALTER TABLE `master_training_competencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_training_test_score_grades`
--

DROP TABLE IF EXISTS `master_training_test_score_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_training_test_score_grades` (
  `id` int(11) NOT NULL,
  `title` char(2) DEFAULT NULL,
  `min_score` int(11) DEFAULT NULL,
  `max_score` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_training_test_score_grades`
--

LOCK TABLES `master_training_test_score_grades` WRITE;
/*!40000 ALTER TABLE `master_training_test_score_grades` DISABLE KEYS */;
INSERT INTO `master_training_test_score_grades` VALUES (1,'A',81,100,'Sangat Baik'),(2,'B',61,80,'Baik'),(3,'C',41,60,'Cukup'),(4,'D',21,40,'Kurang'),(5,'E',0,20,'Sangat Kurang');
/*!40000 ALTER TABLE `master_training_test_score_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainee_score_averages`
--

DROP TABLE IF EXISTS `trainee_score_averages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trainee_score_averages` (
  `id` int(11) NOT NULL,
  `trainee_id` int(11) NOT NULL,
  `master_training_competency_id` int(11) NOT NULL,
  `score_average` decimal(5,2) NOT NULL,
  `master_training_test_score_grade_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trainee_score_averages`
--

LOCK TABLES `trainee_score_averages` WRITE;
/*!40000 ALTER TABLE `trainee_score_averages` DISABLE KEYS */;
/*!40000 ALTER TABLE `trainee_score_averages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainee_training_test_scores`
--

DROP TABLE IF EXISTS `trainee_training_test_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trainee_training_test_scores` (
  `id` int(11) NOT NULL,
  `trainee_id` int(11) NOT NULL,
  `master_training_competency_id` int(11) NOT NULL,
  `test_date` date NOT NULL,
  `score` int(11) NOT NULL,
  `master_training_test_score_grade_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trainee_training_test_scores`
--

LOCK TABLES `trainee_training_test_scores` WRITE;
/*!40000 ALTER TABLE `trainee_training_test_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `trainee_training_test_scores` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-24  3:54:04
