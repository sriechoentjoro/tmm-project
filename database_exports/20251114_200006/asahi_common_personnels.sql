-- MySQL dump 10.16  Distrib 10.1.13-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: asahi_common_personnels
-- ------------------------------------------------------
-- Server version	10.1.13-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `asahi_common_personnels`
--

/*!40000 DROP DATABASE IF EXISTS `asahi_common_personnels`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `asahi_common_personnels` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `asahi_common_personnels`;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'PT Asahi Family','2025-10-17 17:00:00','2025-10-18 00:00:00'),(2,'CV Asahi Family','2025-10-17 17:00:00','2025-10-18 00:00:00');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'HR & GA','2012-05-25 05:00:57','2012-05-25 11:01:21'),(2,'Financial & Accounting','2012-05-25 05:00:57','2012-05-25 11:01:21'),(3,'Engineering & Quality Assurance','2012-05-25 05:00:57','2012-05-25 11:01:21'),(4,'Production','2012-05-25 05:00:57','2012-05-25 11:01:21'),(5,'PPIC','2012-05-25 05:00:57','2012-05-25 11:01:21'),(6,'Business Process Improvement','2013-01-30 07:21:48','2013-01-30 14:22:12');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_statuses`
--

DROP TABLE IF EXISTS `employee_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_statuses`
--

LOCK TABLES `employee_statuses` WRITE;
/*!40000 ALTER TABLE `employee_statuses` DISABLE KEYS */;
INSERT INTO `employee_statuses` VALUES (1,'permanent','Permanent','2013-09-26 10:24:42','2013-09-26 16:25:06'),(2,'probation','Probation','2013-09-26 10:24:42','2013-09-26 16:25:06'),(3,'contract_1st','Contract 1st','2013-09-26 10:24:42','2013-09-26 16:25:06'),(4,'contract_2nd','Contract 2nd','2013-09-26 10:24:42','2013-09-26 16:25:06'),(5,'contract_3rd','Contract 3rd','2013-09-26 10:24:42','2013-09-26 16:25:06'),(6,'resigned','Resigned','2013-09-26 10:24:42','2013-09-26 16:25:06'),(7,'vendor','Vendor','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `employee_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `genders`
--

DROP TABLE IF EXISTS `genders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `genders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `genders`
--

LOCK TABLES `genders` WRITE;
/*!40000 ALTER TABLE `genders` DISABLE KEYS */;
INSERT INTO `genders` VALUES (1,'Male','2012-05-26 03:42:40','0000-00-00 00:00:00'),(2,'Female','2012-05-26 03:42:40','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `genders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `language_abilities`
--

DROP TABLE IF EXISTS `language_abilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language_abilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `language_abilities`
--

LOCK TABLES `language_abilities` WRITE;
/*!40000 ALTER TABLE `language_abilities` DISABLE KEYS */;
INSERT INTO `language_abilities` VALUES (1,'native','Native','2013-09-26 10:24:42','0000-00-00 00:00:00'),(2,'business','Business','2013-09-26 10:24:42','0000-00-00 00:00:00'),(3,'depend-on-dictionary','Depend on Dictionary','2013-09-26 10:24:42','0000-00-00 00:00:00'),(4,'poor','Poor','2013-09-26 10:24:42','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `language_abilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marital_statuses`
--

DROP TABLE IF EXISTS `marital_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marital_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marital_statuses`
--

LOCK TABLES `marital_statuses` WRITE;
/*!40000 ALTER TABLE `marital_statuses` DISABLE KEYS */;
INSERT INTO `marital_statuses` VALUES (1,'single','Single','2013-09-26 10:24:42','2013-09-26 16:25:06'),(2,'married','Married','2014-02-19 06:56:59','2013-09-26 16:25:06');
/*!40000 ALTER TABLE `marital_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personnels`
--

DROP TABLE IF EXISTS `personnels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personnels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `gender_id` int(1) DEFAULT NULL,
  `religion_id` int(11) DEFAULT NULL,
  `marital_status_id` int(1) DEFAULT NULL,
  `birth_place` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `employee_code` varchar(9) DEFAULT NULL,
  `employee_status_id` int(11) DEFAULT NULL,
  `employee_date_join` date DEFAULT NULL,
  `employee_date_resigned` date DEFAULT NULL,
  `propinsi_id` int(11) DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `kecamatan_id` int(11) DEFAULT NULL,
  `kelurahan_id` int(11) DEFAULT NULL,
  `post_code` varchar(5) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `telephone` varchar(13) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `strata_id` int(11) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personnels`
--

LOCK TABLES `personnels` WRITE;
/*!40000 ALTER TABLE `personnels` DISABLE KEYS */;
INSERT INTO `personnels` VALUES (1,'HALIM DAMANHURI',1,1,1,'BANYUMAS','1982-05-08','000060706',1,'2006-07-13','0000-00-00',0,0,0,0,'','jl . perumahan griya ciwangi bl.j kav.2 no.12 rt.029 rw.005','srikuncoro@yahoo.com','',1,8,4,4,'2025-11-04 07:21:13','2012-06-20 00:00:00'),(2,'DIAN KURNIAWAN',1,1,1,'SRAGEN','1977-08-07','000070706',1,'2006-07-13','0000-00-00',0,0,0,0,'','kp. ciloa i, rt.01/06 desa panyocokan-ciwidey-bandung','srikuncoro1972@gmail.com','',1,8,4,7,'2025-11-04 07:21:13','2012-06-20 00:00:00'),(3,'SRI KUNCORO',1,1,1,'JAKARTA','1980-04-04','000080706',1,'2006-07-13','0000-00-00',11,152,1797,25947,'51114','duren jaya blok c 364 bekasi timur','sriechoentjoro@gmail.com','081111111',1,6,8,7,'0000-00-00 00:00:00','2012-06-20 00:00:00'),(4,'GILANG',1,1,1,'TEGAL','1979-10-14','000100706',1,'2006-07-13','0000-00-00',12,NULL,NULL,NULL,'','','afimaintenanceservice@gmail.com','',1,NULL,3,7,'2025-11-04 07:21:13','2012-06-20 00:00:00'),(5,'VINA',2,1,1,'SUKABUMI','1981-01-29','000110706',1,'2006-07-13','0000-00-00',0,0,0,0,'','kp. cicau rt. 04 / 05 selawi sukaraja kab. sukabumi ja-bar','vina.test@asahi.local','',1,8,3,7,'2025-11-04 09:40:49','2012-06-20 00:00:00'),(6,'SHOWANG',1,1,1,'SUKOHARJO','1980-10-10','000130706',1,'2006-07-13','0000-00-00',0,0,0,0,'','jl. gudang air no. 16 kel. rambutan ciracas jakarta selatan','showang.test@asahi.local','',1,10,9,8,'2025-11-04 09:40:49','2012-06-20 00:00:00'),(7,'DANI',1,1,1,'BOGOR','1983-10-26','000160706',1,'2006-07-13','0000-00-00',0,0,0,0,'','jl. raya bogor km 23 komplek blk blok 1/07 cijantung jak-timur','dani.test@asahi.local','',1,7,8,7,'2025-11-04 09:40:49','2012-06-20 00:00:00'),(8,'ABDUL',1,1,1,'KENDARI','1982-04-15','000200706',1,'2006-07-13','0000-00-00',0,0,0,0,'','jl. mahoni raya no. 3 perumnas suraditakec cisauk tangerang','','',1,8,3,4,'2025-11-04 07:21:13','2012-06-20 00:00:00'),(9,'HIKMAD',1,1,2,'BANDUNG','1974-04-13','000270706',1,'2006-07-13','0000-00-00',0,0,0,0,'','jl. pengadilan b-j 1 sukasari tangerang','','',1,6,10,8,'2025-11-04 07:21:13','2012-06-20 00:00:00'),(10,'ROWANG',1,1,1,'KUPANG','1978-04-04','000300706',1,'2006-07-13','0000-00-00',0,0,0,0,'','jl. jatayu i n. 06 kebayoran lama utara jak-selatan','','',1,8,4,7,'2025-11-04 07:21:13','2012-06-20 00:00:00');
/*!40000 ALTER TABLE `personnels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `main_roles` text NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES (1,'Direktur Utama','Pemimpin tertinggi perusahaan, penentu arah strategis.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(2,'Manajer Operasional','Mengawasi pengiriman, jadwal, dan efisiensi armada.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(3,'Supervisor Armada','Mengatur sopir, rute, dan kinerja kendaraan harian.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(4,'Sopir Truk','Mengemudikan truk untuk pengiriman barang.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(5,'Mekanik','Melakukan perawatan dan perbaikan kendaraan.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(6,'Staf Logistik','Mengelola dokumen pengiriman dan koordinasi muatan.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(7,'Staf Gudang','Menangani bongkar muat dan penyimpanan barang.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(8,'Staf Administrasi','Mengurus dokumen, arsip, dan tugas administratif.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(9,'HR Officer','Mengelola rekrutmen, pelatihan, dan data karyawan.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(10,'Akuntan','Menyusun laporan keuangan dan mengelola transaksi.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(11,'Customer Service','Menangani pertanyaan dan keluhan pelanggan.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(12,'IT Support','Menangani sistem pelacakan, jaringan, dan perangkat lunak.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(13,'Legal Officer','Mengurus perizinan, kontrak, dan kepatuhan hukum.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(14,'Safety Officer','Memastikan keselamatan kerja dan pelatihan K3.','2025-10-29 01:56:14','2025-10-29 01:56:14'),(15,'Purchasing Officer','Mengelola pembelian suku cadang dan perlengkapan.','2025-10-29 01:56:14','2025-10-29 01:56:14');
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `religions`
--

DROP TABLE IF EXISTS `religions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `religions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `religions`
--

LOCK TABLES `religions` WRITE;
/*!40000 ALTER TABLE `religions` DISABLE KEYS */;
INSERT INTO `religions` VALUES (1,'moslem','Moslem','2013-09-26 10:24:42','0000-00-00 00:00:00'),(2,'christian','Christian','2013-09-26 10:24:42','0000-00-00 00:00:00'),(3,'protestant','Protestant','2013-09-26 10:24:42','0000-00-00 00:00:00'),(4,'hindu','Hindu','2013-09-26 10:24:42','0000-00-00 00:00:00'),(5,'budha','Budha','2013-09-26 10:24:42','0000-00-00 00:00:00'),(6,'kepercayaan','Kepercayaan','2013-09-26 10:24:42','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `religions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stratas`
--

DROP TABLE IF EXISTS `stratas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stratas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stratas`
--

LOCK TABLES `stratas` WRITE;
/*!40000 ALTER TABLE `stratas` DISABLE KEYS */;
INSERT INTO `stratas` VALUES (1,'sd','SD','2013-09-26 11:24:17','0000-00-00 00:00:00'),(2,'smp','SMP','2013-09-26 11:24:17','0000-00-00 00:00:00'),(3,'sma','SMA','2013-09-26 11:24:17','0000-00-00 00:00:00'),(4,'smk','SMK','2013-09-26 11:24:17','0000-00-00 00:00:00'),(5,'d1','D1','2013-09-26 11:24:17','0000-00-00 00:00:00'),(6,'d2','D2','2013-09-26 11:24:17','0000-00-00 00:00:00'),(7,'d3','D3','2013-09-26 11:24:17','0000-00-00 00:00:00'),(8,'s1','S1','2013-09-26 11:24:17','0000-00-00 00:00:00'),(9,'s2','S2','2013-09-26 11:24:17','0000-00-00 00:00:00'),(10,'s3','S3','2013-09-26 11:24:17','0000-00-00 00:00:00'),(11,'post-doctoral','Post Doctoral','2013-09-26 11:24:17','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `stratas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'asahi_common_personnels'
--

--
-- Dumping routines for database 'asahi_common_personnels'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-14 20:00:07
