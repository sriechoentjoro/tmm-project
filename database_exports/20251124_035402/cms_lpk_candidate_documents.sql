-- ============================================
-- Database: cms_lpk_candidate_documents
-- Exported: 20251124_035402
-- ============================================

DROP DATABASE IF EXISTS `cms_lpk_candidate_documents`;
CREATE DATABASE `cms_lpk_candidate_documents` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cms_lpk_candidate_documents`;
-- MariaDB dump 10.19  Distrib 10.4.27-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cms_lpk_candidate_documents
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
-- Table structure for table `apprentice_document_management_dashboards`
--

DROP TABLE IF EXISTS `apprentice_document_management_dashboards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apprentice_document_management_dashboards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `candidate_id` int(11) NOT NULL,
  `total_documents` int(11) DEFAULT 0,
  `total_ready` int(11) DEFAULT 0,
  `total_pending` int(11) DEFAULT 0,
  `total_missing` int(11) DEFAULT 0,
  `last_updated` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apprentice_document_management_dashboards`
--

LOCK TABLES `apprentice_document_management_dashboards` WRITE;
/*!40000 ALTER TABLE `apprentice_document_management_dashboards` DISABLE KEYS */;
/*!40000 ALTER TABLE `apprentice_document_management_dashboards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `candidate_document_categories`
--

DROP TABLE IF EXISTS `candidate_document_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidate_document_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidate_document_categories`
--

LOCK TABLES `candidate_document_categories` WRITE;
/*!40000 ALTER TABLE `candidate_document_categories` DISABLE KEYS */;
INSERT INTO `candidate_document_categories` VALUES (1,'Identitas','Dokumen identitas pribadi'),(2,'Pendidikan','Dokumen pendidikan dan pelatihan'),(3,'Kesehatan','Dokumen pemeriksaan kesehatan'),(4,'Pernyataan','Surat pernyataan dan izin'),(5,'Tambahan','Dokumen tambahan atau opsional');
/*!40000 ALTER TABLE `candidate_document_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `candidate_document_management_dashboard_details`
--

DROP TABLE IF EXISTS `candidate_document_management_dashboard_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidate_document_management_dashboard_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dashboard_id` int(11) NOT NULL,
  `document_type` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidate_document_management_dashboard_details`
--

LOCK TABLES `candidate_document_management_dashboard_details` WRITE;
/*!40000 ALTER TABLE `candidate_document_management_dashboard_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `candidate_document_management_dashboard_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `candidate_documents_master_list`
--

DROP TABLE IF EXISTS `candidate_documents_master_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidate_documents_master_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 1,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidate_documents_master_list`
--

LOCK TABLES `candidate_documents_master_list` WRITE;
/*!40000 ALTER TABLE `candidate_documents_master_list` DISABLE KEYS */;
INSERT INTO `candidate_documents_master_list` VALUES (1,'Fotokopi KTP',1,1,'Wajib untuk verifikasi identitas'),(2,'Fotokopi Kartu Keluarga',1,1,'Digunakan untuk data keluarga'),(3,'Fotokopi Akta Kelahiran',1,1,'Verifikasi usia dan asal'),(4,'Pas Foto 3x4 dan 4x6',1,1,'Latar belakang putih, terbaru'),(5,'Fotokopi Paspor',1,0,'Jika sudah memiliki paspor'),(6,'Fotokopi NPWP',1,0,'Jika diminta oleh LPK'),(7,'Fotokopi Ijazah Terakhir',2,1,'Minimal SMA/SMK/Paket C'),(8,'Fotokopi Transkrip Nilai',2,1,'Nilai akademik'),(9,'Sertifikat Pelatihan/Kursus',2,0,'Jika relevan dengan bidang kerja'),(10,'Surat Keterangan Sehat',3,1,'Dari dokter umum'),(11,'Hasil Tes Buta Warna',3,1,'Wajib untuk bidang tertentu'),(12,'Hasil Rontgen dan Medical Check-up',3,0,'Biasanya setelah seleksi awal'),(13,'Surat Izin Orang Tua/Wali',4,1,'Bermaterai, untuk peserta di bawah 21 tahun'),(14,'Surat Pernyataan Bersedia Magang 3 Tahun',4,1,'Bermaterai'),(15,'Surat Pernyataan Tidak Bertato/Tindik',4,1,'Bermaterai'),(16,'Formulir Pendaftaran LPK',4,1,'Diisi saat mendaftar'),(17,'CV / Daftar Riwayat Hidup',4,1,'Format bebas atau sesuai LPK'),(18,'Sertifikat Kemampuan Bahasa Jepang',5,0,'Jika sudah mengikuti pelatihan'),(19,'Surat Rekomendasi Sekolah/Tempat Kerja',5,0,'Jika tersedia'),(20,'Sertifikat Organisasi/Kegiatan Sosial',5,0,'Menambah nilai profil');
/*!40000 ALTER TABLE `candidate_documents_master_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `candidate_submission_documents`
--

DROP TABLE IF EXISTS `candidate_submission_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidate_submission_documents` (
  `applicant_id` int(11) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `submitted` tinyint(1) DEFAULT 0,
  `submission_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidate_submission_documents`
--

LOCK TABLES `candidate_submission_documents` WRITE;
/*!40000 ALTER TABLE `candidate_submission_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `candidate_submission_documents` ENABLE KEYS */;
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
