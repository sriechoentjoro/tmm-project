-- ============================================
-- Database: cms_tmm_apprentice_documents
-- Exported: 20251124_035402
-- ============================================

DROP DATABASE IF EXISTS `cms_tmm_apprentice_documents`;
CREATE DATABASE `cms_tmm_apprentice_documents` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cms_tmm_apprentice_documents`;
-- MariaDB dump 10.19  Distrib 10.4.27-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cms_tmm_apprentice_documents
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
-- Table structure for table `apprentice_record_coe_visas`
--

DROP TABLE IF EXISTS `apprentice_record_coe_visas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apprentice_record_coe_visas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apprentice_id` int(11) NOT NULL,
  `master_coe_type_id` int(11) NOT NULL,
  `date_coe_received` date NOT NULL,
  `date_visa_application` date NOT NULL,
  `date_visa_received` date NOT NULL,
  `place_visa_issued` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apprentice_record_coe_visas`
--

LOCK TABLES `apprentice_record_coe_visas` WRITE;
/*!40000 ALTER TABLE `apprentice_record_coe_visas` DISABLE KEYS */;
INSERT INTO `apprentice_record_coe_visas` VALUES (1,1,2,'2025-07-01','2025-06-15','2025-07-01','Jakarta'),(2,110,1,'2025-08-05','2025-08-16','2025-08-28','YOGYAKARTA');
/*!40000 ALTER TABLE `apprentice_record_coe_visas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `apprentice_record_medical_check_ups`
--

DROP TABLE IF EXISTS `apprentice_record_medical_check_ups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apprentice_record_medical_check_ups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apprentice_id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `date_issued` date NOT NULL,
  `master_medical_check_up_result_id` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `clinic` varchar(256) NOT NULL,
  `mcu_files` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apprentice_record_medical_check_ups`
--

LOCK TABLES `apprentice_record_medical_check_ups` WRITE;
/*!40000 ALTER TABLE `apprentice_record_medical_check_ups` DISABLE KEYS */;
INSERT INTO `apprentice_record_medical_check_ups` VALUES (1,1,'Medical check up pertama','2025-08-08',1,'Kurangi rokok dan melek malam agar tidak tumbuh uban.','Purwosari','files\\medicalCheckUp\\ASHRAF_WwgSdA80AemJmF4p.pdf'),(2,5,'MCU pertama','2025-08-19',3,'Kalau berdiri dari duduk mata rasanya berkunang kunang diduga kurang tidur','Purwakarta','files/medicalCheckUp/FINAL_RUNDOWN_ACARA_HUT-RI_PT_RHINO_INDUSTRY_INDONESIA_BonoDkVCOKP9yeWu.pdf'),(3,1,'Medikal kedua','2025-08-25',3,'Memiliki Asma','Kimia Farma','files/medicalCheckUp/20231010_183534_zF4R7paxnSyofb8g.jpg'),(4,110,'ADMIN','2025-08-05',2,'MASUK ANGIN','KIMIA FARMA PWK','files/medicalCheckUp/20231005_141120_f8AzM42Yayfm7P7z.jpg');
/*!40000 ALTER TABLE `apprentice_record_medical_check_ups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `apprentice_record_pasports`
--

DROP TABLE IF EXISTS `apprentice_record_pasports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apprentice_record_pasports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apprentice_id` int(11) NOT NULL,
  `date_issue` date NOT NULL,
  `place_issue` varchar(256) NOT NULL,
  `date_received` date NOT NULL,
  `date_paid` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apprentice_record_pasports`
--

LOCK TABLES `apprentice_record_pasports` WRITE;
/*!40000 ALTER TABLE `apprentice_record_pasports` DISABLE KEYS */;
/*!40000 ALTER TABLE `apprentice_record_pasports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `apprentice_submission_documents`
--

DROP TABLE IF EXISTS `apprentice_submission_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apprentice_submission_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apprentice_id` int(11) NOT NULL,
  `apprenticeship_submission_document_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `master_document_submission_status_id` int(11) DEFAULT 2,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apprentice_submission_documents`
--

LOCK TABLES `apprentice_submission_documents` WRITE;
/*!40000 ALTER TABLE `apprentice_submission_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `apprentice_submission_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_apprentice_coe_types`
--

DROP TABLE IF EXISTS `master_apprentice_coe_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_apprentice_coe_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_apprentice_coe_types`
--

LOCK TABLES `master_apprentice_coe_types` WRITE;
/*!40000 ALTER TABLE `master_apprentice_coe_types` DISABLE KEYS */;
INSERT INTO `master_apprentice_coe_types` VALUES (1,'COE Biasa'),(2,'COE Electronic');
/*!40000 ALTER TABLE `master_apprentice_coe_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_apprentice_departure_documents`
--

DROP TABLE IF EXISTS `master_apprentice_departure_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_apprentice_departure_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `format` varchar(100) DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 1,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_apprentice_departure_documents`
--

LOCK TABLES `master_apprentice_departure_documents` WRITE;
/*!40000 ALTER TABLE `master_apprentice_departure_documents` DISABLE KEYS */;
INSERT INTO `master_apprentice_departure_documents` VALUES (1,'Visa','PDF / Original',1,'2025-10-24 20:23:05','2025-10-24 20:23:05'),(2,'Flight Ticket','PDF / Copy',1,'2025-10-24 20:23:05','2025-10-24 20:23:05'),(3,'Passport','PDF / Original',1,'2025-10-24 20:23:05','2025-10-24 20:23:05'),(4,'COE','PDF / Copy',1,'2025-10-24 20:23:05','2025-10-24 20:23:05'),(5,'Insurance Document','PDF / Signed',1,'2025-10-24 20:23:05','2025-10-24 20:23:05'),(6,'Medical Check-Up Certificate','PDF / Signed',1,'2025-10-24 20:23:05','2025-10-24 20:23:05');
/*!40000 ALTER TABLE `master_apprentice_departure_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_apprentice_submission_document_categories`
--

DROP TABLE IF EXISTS `master_apprentice_submission_document_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_apprentice_submission_document_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_apprentice_submission_document_categories`
--

LOCK TABLES `master_apprentice_submission_document_categories` WRITE;
/*!40000 ALTER TABLE `master_apprentice_submission_document_categories` DISABLE KEYS */;
INSERT INTO `master_apprentice_submission_document_categories` VALUES (1,'Sending Organization','Dokumen dari LPK atau organisasi pengirim','2025-10-24 20:23:04','2025-10-24 20:23:04'),(2,'Candidate','Dokumen terkait peserta magang','2025-10-24 20:23:04','2025-10-24 20:23:04'),(3,'Legal / Regulatory','Dokumen hukum dan peraturan','2025-10-24 20:23:04','2025-10-24 20:23:04'),(4,'Translation','Dokumen hasil terjemahan','2025-10-24 20:23:04','2025-10-24 20:23:04');
/*!40000 ALTER TABLE `master_apprentice_submission_document_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_apprentice_submission_documents`
--

DROP TABLE IF EXISTS `master_apprentice_submission_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_apprentice_submission_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_jp` varchar(255) DEFAULT NULL,
  `format` varchar(100) DEFAULT NULL,
  `is_translation_required` tinyint(1) DEFAULT 0,
  `is_required` tinyint(1) DEFAULT 1,
  `master_apprenticeship_submission_document_category_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_apprentice_submission_documents`
--

LOCK TABLES `master_apprentice_submission_documents` WRITE;
/*!40000 ALTER TABLE `master_apprentice_submission_documents` DISABLE KEYS */;
INSERT INTO `master_apprentice_submission_documents` VALUES (1,'Letter of Recommendation from Sending Country','???????????????????????????','PDF / Original',1,1,1,'2025-10-24 20:23:04','2025-10-24 20:23:04'),(2,'SO Business Registration Certificate','????????????????????????????????????','PDF / Copy',0,1,1,'2025-10-24 20:23:04','2025-10-24 20:23:04'),(3,'SO Profile Summary (Form 2-9)','????????????????????????2-9???','Excel / PDF',0,1,1,'2025-10-24 20:23:04','2025-10-24 20:23:04'),(4,'Expense Statement (Form 2-10)','??????????????????????????????2-10???','Excel / PDF',0,1,1,'2025-10-24 20:23:04','2025-10-24 20:23:04'),(5,'Ethical Conduct Pledge (Form 2-11)','??????????????????2-11???','PDF / Signed',0,1,1,'2025-10-24 20:23:04','2025-10-24 20:23:04'),(6,'Candidate Contract','????????????????????????','PDF / Signed',0,1,2,'2025-10-24 20:23:04','2025-10-24 20:23:04'),(7,'Candidate Expense Breakdown (Form 1-21)','??????????????????????????????1-21???','Excel / PDF',0,1,2,'2025-10-24 20:23:04','2025-10-24 20:23:04'),(8,'Candidate Recommendation Letter (Form 1-23)','???????????????????????????1-23???','PDF / Original',1,1,2,'2025-10-24 20:23:04','2025-10-24 20:23:04'),(9,'Technical Intern Law Summary (Home Country)','???????????????????????????????????????','PDF / Translated',1,1,3,'2025-10-24 20:23:04','2025-10-24 20:23:04'),(10,'Translator???s Signature Page','????????????????????????','PDF / Signed',0,1,4,'2025-10-24 20:23:04','2025-10-24 20:23:04');
/*!40000 ALTER TABLE `master_apprentice_submission_documents` ENABLE KEYS */;
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
