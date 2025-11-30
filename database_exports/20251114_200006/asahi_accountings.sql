-- MySQL dump 10.16  Distrib 10.1.13-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: asahi_accountings
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
-- Current Database: `asahi_accountings`
--

/*!40000 DROP DATABASE IF EXISTS `asahi_accountings`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `asahi_accountings` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `asahi_accountings`;

--
-- Table structure for table `accounting_transactions`
--

DROP TABLE IF EXISTS `accounting_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounting_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_date` date NOT NULL,
  `description` text,
  `debit_account` varchar(100) DEFAULT NULL,
  `credit_account` varchar(100) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `personnel_id` int(11) DEFAULT NULL COMMENT 'Pelaksana (NO FK)',
  `vehicle_id` int(11) DEFAULT NULL COMMENT 'Alokasi kendaraan (NO FK)',
  `receipt_file_path` varchar(500) DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_transaction_date` (`transaction_date`),
  KEY `idx_reference` (`reference_type`,`reference_id`),
  KEY `idx_personnel` (`personnel_id`),
  KEY `idx_vehicle` (`vehicle_id`),
  KEY `idx_receipt` (`receipt_file_path`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Journal entries - moved from asahi_inventories';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounting_transactions`
--

LOCK TABLES `accounting_transactions` WRITE;
/*!40000 ALTER TABLE `accounting_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounting_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banks`
--

DROP TABLE IF EXISTS `banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banks`
--

LOCK TABLES `banks` WRITE;
/*!40000 ALTER TABLE `banks` DISABLE KEYS */;
INSERT INTO `banks` VALUES (1,'Bank BRI','002'),(2,'Bank Mandiri','008'),(3,'Bank BNI','009'),(4,'Bank BCA','014'),(5,'Bank BTN','200'),(6,'Bank Syariah Indonesia (BSI)','451'),(7,'Bank CIMB Niaga','022'),(8,'Bank Danamon','011'),(9,'Bank Permata','013'),(10,'Bank Mega','426'),(11,'Bank Sinarmas','153'),(12,'Bank OCBC NISP','028'),(13,'Bank Bukopin','441'),(14,'Bank Jabar Banten (BJB)','110'),(15,'Bank DKI Jakarta','111'),(16,'Bank Jateng','113'),(17,'Bank Jatim','114'),(18,'Bank Sumut','117'),(19,'Bank Nagari','118'),(20,'Bank Riau Kepri','119'),(21,'Bank Sumsel Babel','120'),(22,'Bank Lampung','121'),(23,'Bank NTB','128'),(24,'Bank NTT','130'),(25,'Bank Kalbar','123'),(26,'Bank Kaltimtara','124'),(27,'Bank Kalteng','125'),(28,'Bank Sulteng','134'),(29,'Bank Sultra','135'),(30,'Bank Papua','132');
/*!40000 ALTER TABLE `banks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chart_of_accounts`
--

DROP TABLE IF EXISTS `chart_of_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chart_of_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chart_of_accounts`
--

LOCK TABLES `chart_of_accounts` WRITE;
/*!40000 ALTER TABLE `chart_of_accounts` DISABLE KEYS */;
INSERT INTO `chart_of_accounts` VALUES (1,'-2','Beban Sumbangan & Iuran','2025-10-29 01:12:10','0000-00-00 00:00:00'),(2,'-1','Koordinasi Lingkungan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(3,'150','Akum. Peny Inventaris Kantor','2025-10-29 01:12:10','0000-00-00 00:00:00'),(4,'151','Akum. Peny Biaya Pra Operasi','2025-10-29 01:12:10','0000-00-00 00:00:00'),(5,'300','Modal','2025-10-29 01:12:10','0000-00-00 00:00:00'),(6,'301','Laba Ditahan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(7,'302','Laba Tahun Berjalan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(8,'303','Laba Ditahan Atas Tax Amnesty','2025-10-29 01:12:10','0000-00-00 00:00:00'),(9,'399','Prive','2025-10-29 01:12:10','0000-00-00 00:00:00'),(10,'500','HPP','2025-10-29 01:12:10','0000-00-00 00:00:00'),(11,'110-10','Kas','2025-10-29 01:12:10','0000-00-00 00:00:00'),(12,'110-11','Kas Malang','2025-10-29 01:12:10','0000-00-00 00:00:00'),(13,'110-20','Kas Besar','2025-10-29 01:12:10','0000-00-00 00:00:00'),(14,'110-30','Ayat Silang','2025-10-29 01:12:10','0000-00-00 00:00:00'),(15,'120-10','Bank BCA','2025-10-29 01:12:10','0000-00-00 00:00:00'),(16,'120-11','Bank MANDIRI','2025-10-29 01:12:10','0000-00-00 00:00:00'),(17,'130-10','Piutang Usaha','2025-10-29 01:12:10','0000-00-00 00:00:00'),(18,'139-20','Piutang Karyawan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(19,'139-90','Piutang Lain','2025-10-29 01:12:10','0000-00-00 00:00:00'),(20,'140-40','Persediaan Produk Jadi','2025-10-29 01:12:10','0000-00-00 00:00:00'),(21,'151-10','Uang Muka Pembelian','2025-10-29 01:12:10','0000-00-00 00:00:00'),(22,'152-11','PPN Masukan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(23,'170-30','Mesin & Peralatan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(24,'170-40','Kendaraan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(25,'172-10','Aset dalam pembangunan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(26,'210-10','Hutang Usaha','2025-10-29 01:12:10','0000-00-00 00:00:00'),(27,'219-51','Hutang Leasing Kendaraan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(28,'219-52','Hutang Kendaraan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(29,'230-11','PPN Keluaran','2025-10-29 01:12:10','0000-00-00 00:00:00'),(30,'230-12','Hutang PPH 21','2025-10-29 01:12:10','0000-00-00 00:00:00'),(31,'230-14','Hutang PPH 29','2025-10-29 01:12:10','0000-00-00 00:00:00'),(32,'230-15','Hutang PPN','2025-10-29 01:12:10','0000-00-00 00:00:00'),(33,'230-16','Hutang PPH 25','2025-10-29 01:12:10','0000-00-00 00:00:00'),(34,'230-17','PPH 23','2025-10-29 01:12:10','0000-00-00 00:00:00'),(35,'250-10','Hutang Bank','2025-10-29 01:12:10','0000-00-00 00:00:00'),(36,'250-30','Titipan Transaksi','2025-10-29 01:12:10','0000-00-00 00:00:00'),(37,'410-09','Penjualan Jasa','2025-10-29 01:12:10','0000-00-00 00:00:00'),(38,'410-10','Penjualan Trading','2025-10-29 01:12:10','0000-00-00 00:00:00'),(39,'410-11','Penjualan Pariwisata','2025-10-29 01:12:10','0000-00-00 00:00:00'),(40,'490-11','Pendapatan Bunga BANK','2025-10-29 01:12:10','0000-00-00 00:00:00'),(41,'490-12','Pendapatan lain-lain','2025-10-29 01:12:10','0000-00-00 00:00:00'),(42,'510-50','Beban Transportasi','2025-10-29 01:12:10','0000-00-00 00:00:00'),(43,'510-51','Beban Transportasi Bus Pariwisata','2025-10-29 01:12:10','0000-00-00 00:00:00'),(44,'510-52','Beban Bongkar Muat','2025-10-29 01:12:10','0000-00-00 00:00:00'),(45,'510-53','Beban Kawalan Muatan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(46,'510-54','Beban Claim Muatan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(47,'510-60','Pembelian Trading','2025-10-29 01:12:10','0000-00-00 00:00:00'),(48,'510-61','Beban Pengiriman Dokumen','2025-10-29 01:12:10','0000-00-00 00:00:00'),(49,'610-31','Fee Marketing','2025-10-29 01:12:10','0000-00-00 00:00:00'),(50,'610-32','Entertaint & Representasi','2025-10-29 01:12:10','0000-00-00 00:00:00'),(51,'620-10','Beban Gaji Karyawan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(52,'620-11','BPJS Karyawan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(53,'620-32','Beban Listrik','2025-10-29 01:12:10','0000-00-00 00:00:00'),(54,'620-34','Beban Telepon & Internet','2025-10-29 01:12:10','0000-00-00 00:00:00'),(55,'620-40','Beban Perlengkapan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(56,'620-41','Beban Pajak Badan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(57,'620-42','Beban Atk / Foto copy','2025-10-29 01:12:10','0000-00-00 00:00:00'),(58,'620-43','Tabungan Driver','2025-10-29 01:12:10','0000-00-00 00:00:00'),(59,'620-44','Beban Gaji / Upah Overtime','2025-10-29 01:12:10','0000-00-00 00:00:00'),(60,'620-45','Beban Pajak / Surat Bangunan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(61,'620-46','Biaya Fee Konsultan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(62,'620-47','Beban THR ','2025-10-29 01:12:10','0000-00-00 00:00:00'),(63,'620-48','Beban Tenaga Kerja Lepas','2025-10-29 01:12:10','0000-00-00 00:00:00'),(64,'620-48','Bonus Karyawan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(65,'620-55','Beban Pemasaran','2025-10-29 01:12:10','0000-00-00 00:00:00'),(66,'620-61','Beban Mutasi dan Pajak Kendaraan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(67,'620-80','KIR / EMISI / GPS','2025-10-29 01:12:10','0000-00-00 00:00:00'),(68,'620-70','Beban STP & Pajak Lainnya','2025-10-29 01:12:10','0000-00-00 00:00:00'),(69,'690-10','Beban  Kantor Lainnya','2025-10-29 01:12:10','0000-00-00 00:00:00'),(70,'690-11','Beban Pembelian Sparepart','2025-10-29 01:12:10','0000-00-00 00:00:00'),(71,'690-12','Beban Pemeliharaan Bangunan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(72,'690-14','Beban Makan & Minum','2025-10-29 01:12:10','0000-00-00 00:00:00'),(73,'690-15','Beban Service Peralatan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(74,'690-40','Beban Perjalanan Dinas','2025-10-29 01:12:10','0000-00-00 00:00:00'),(75,'690-41','Beban BBM / Parkir','2025-10-29 01:12:10','0000-00-00 00:00:00'),(76,'690-42','Beban Sewa Truck','2025-10-29 01:12:10','0000-00-00 00:00:00'),(77,'690-43','Beban Obat-Obatan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(78,'690-44','Biaya Admin Bank','2025-10-29 01:12:10','0000-00-00 00:00:00'),(79,'690-45','Beban Sewa Alat','2025-10-29 01:12:10','0000-00-00 00:00:00'),(80,'690-46','Asuransi Kesehatan','2025-10-29 01:12:10','0000-00-00 00:00:00'),(81,'690-47','Beban Sewa Parkir','2025-10-29 01:12:10','0000-00-00 00:00:00'),(82,'690-49','Beban Sewa Kantor','2025-10-29 01:12:10','0000-00-00 00:00:00'),(83,'910-20','Biaya Bunga Bank','2025-10-29 01:12:10','0000-00-00 00:00:00'),(84,'910-30','Biaya Bunga Leasing','2025-10-29 01:12:10','0000-00-00 00:00:00'),(85,'990-11','Biaya Pajak Bunga Bank','2025-10-29 01:12:10','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `chart_of_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_methods`
--

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_types`
--

DROP TABLE IF EXISTS `transaction_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_debit` tinyint(1) DEFAULT '1' COMMENT '1=Debit, 0=Credit',
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Transaction types for accounting';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_types`
--

LOCK TABLES `transaction_types` WRITE;
/*!40000 ALTER TABLE `transaction_types` DISABLE KEYS */;
INSERT INTO `transaction_types` VALUES (1,'DBT','Debit',1,'Standard debit transaction',1,'2025-11-10 23:44:26','2025-11-10 23:44:26'),(2,'CRT','Credit',0,'Standard credit transaction',1,'2025-11-10 23:44:26','2025-11-10 23:44:26'),(3,'PURCHASE','Purchase Transaction',1,'Inventory purchase from supplier',1,'2025-11-10 23:44:26','2025-11-10 23:44:26'),(4,'PAYMENT','Payment Transaction',0,'Payment to supplier',1,'2025-11-10 23:44:26','2025-11-10 23:44:26'),(5,'ADJUSTMENT','Inventory Adjustment',1,'Stock adjustment entry',1,'2025-11-10 23:44:26','2025-11-10 23:44:26');
/*!40000 ALTER TABLE `transaction_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'asahi_accountings'
--

--
-- Dumping routines for database 'asahi_accountings'
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
