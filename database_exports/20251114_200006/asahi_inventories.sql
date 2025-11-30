-- MySQL dump 10.16  Distrib 10.1.13-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: asahi_inventories
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
-- Current Database: `asahi_inventories`
--

/*!40000 DROP DATABASE IF EXISTS `asahi_inventories`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `asahi_inventories` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `asahi_inventories`;

--
-- Table structure for table `adjust_stocks`
--

DROP TABLE IF EXISTS `adjust_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adjust_stocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) DEFAULT NULL,
  `qty_adjustment` decimal(11,2) DEFAULT NULL,
  `qty_before_adjustment` decimal(11,2) DEFAULT NULL,
  `qty_after_adjustment` decimal(11,2) DEFAULT NULL,
  `reason` text,
  `personnel_id` int(11) DEFAULT NULL,
  `date_adjustment` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adjust_stocks`
--

LOCK TABLES `adjust_stocks` WRITE;
/*!40000 ALTER TABLE `adjust_stocks` DISABLE KEYS */;
INSERT INTO `adjust_stocks` VALUES (1,10901,4.00,4.00,3.00,'Maaf, salah input',34,'2013-12-31 12:02:32'),(2,10832,4.00,4.00,3.00,'maaf, salah input lagi, keasikan main ENTER (banyak yang zero variance sih)',34,'2013-12-31 12:03:38'),(3,6149,0.00,0.00,2.00,'Barang ada di customer Yorozu ',34,'2014-01-29 07:20:58'),(4,6103,0.00,0.00,0.00,'',34,'2014-01-29 07:25:43'),(5,1286,4145.00,4477.00,3910.00,'Revisi dari Sulis belum di input (pertanggal 5 January 2014)',34,'2014-01-29 09:36:49'),(6,1286,4145.00,3910.00,4477.00,'Maaf, ternyata benar 4477',34,'2014-01-29 09:39:17');
/*!40000 ALTER TABLE `adjust_stocks` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_before_adjust_stock-insert` BEFORE INSERT ON `adjust_stocks`
 FOR EACH ROW SET NEW.qty_before_adjustment = (SELECT qty FROM inventories WHERE id = NEW.inventory_id) */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_after_adjust_stocks-insert` AFTER INSERT ON `adjust_stocks`
 FOR EACH ROW UPDATE inventories SET qty = NEW.qty_adjustment WHERE NEW.inventory_id = id */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_after_adjust_stock-update` AFTER UPDATE ON `adjust_stocks`
 FOR EACH ROW UPDATE inventories SET qty = NEW.qty_adjustment WHERE NEW.inventory_id = id */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_before_adjust_stocks-delete` BEFORE DELETE ON `adjust_stocks`
 FOR EACH ROW UPDATE inventories SET qty = OLD.qty_before_adjustment WHERE OLD.inventory_id = id */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `inventories`
--

DROP TABLE IF EXISTS `inventories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `storage_id` int(11) NOT NULL DEFAULT '2',
  `rack` varchar(255) DEFAULT NULL,
  `rack_cell` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `specification` text,
  `maker` varchar(255) DEFAULT NULL,
  `qty` decimal(11,5) NOT NULL,
  `uom_id` int(11) NOT NULL,
  `stock_minimum` decimal(11,3) DEFAULT NULL,
  `remark` text,
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventories`
--

LOCK TABLES `inventories` WRITE;
/*!40000 ALTER TABLE `inventories` DISABLE KEYS */;
INSERT INTO `inventories` VALUES (1,1,'A1','01','PART-001','Engine Oil Filter','img/uploads/3411883_447c89d2-54fc-4ed4-bb5d-48420426669c_554_554_20251113085314.jpg','High quality oil filter for diesel engines','Sakura',50.00000,1,10.000,'-','2025-11-13 08:53:14','2025-11-11 01:00:51'),(2,1,'A1','02','PART-002','Brake Pad Set','img/uploads/2f76132b-80c1-40a4-93b9-eaaafc48d71f_logo-asahi-v15up_20251113103422.webp','Front brake pad set for trucks','Bendix',30.00000,3,5.000,NULL,'2025-11-13 10:34:22','2025-11-11 01:00:51'),(3,2,'B2','10','OIL-001','Diesel Engine Oil 15W-40','img/uploads/20240126_134624_20251113091941.jpg','5 liter container of premium diesel engine oil','Shell Rimula',100.00000,23,20.000,NULL,'2025-11-13 09:19:41','2025-11-11 01:00:51'),(4,2,'C3','05','TIRE-001','Truck Tire 295/80R22.5','img/uploads/719cb6d4-f147-4a83-b44d-d7d4f8e6d17d_20251113085331.jpg','Heavy duty truck tire','Bridgestone',20.00000,41,8.000,NULL,'2025-11-13 08:53:31','2025-11-11 01:00:51'),(5,1,'A1','03','PART-003','Air Filter Element',NULL,'Air filter for diesel engines','Denso',45.00000,1,10.000,NULL,'2025-11-10 19:37:57','2025-11-11 01:00:51');
/*!40000 ALTER TABLE `inventories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_images`
--

DROP TABLE IF EXISTS `inventory_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_images`
--

LOCK TABLES `inventory_images` WRITE;
/*!40000 ALTER TABLE `inventory_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_receipt_items`
--

DROP TABLE IF EXISTS `purchase_receipt_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_receipt_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_receipt_id` int(11) NOT NULL,
  `inventory_id` int(11) DEFAULT NULL,
  `item_description` varchar(500) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT '1.00',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_purchase_receipt` (`purchase_receipt_id`),
  KEY `idx_inventory` (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Purchase receipt items detail';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_receipt_items`
--

LOCK TABLES `purchase_receipt_items` WRITE;
/*!40000 ALTER TABLE `purchase_receipt_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_receipt_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_receipts`
--

DROP TABLE IF EXISTS `purchase_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL COMMENT 'Path file kwitansi',
  `file_name` varchar(255) DEFAULT NULL COMMENT 'Nama file asli',
  `file_size` int(11) DEFAULT NULL COMMENT 'Ukuran file (bytes)',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'MIME type',
  `upload_date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal upload',
  `description` text COMMENT 'Deskripsi kwitansi',
  `approval_status_id` int(11) NOT NULL DEFAULT '2' COMMENT 'Ref: asahi_online_approvals.approval_statuses (NO FK)',
  `approved_by_personnel_id` int(11) DEFAULT NULL COMMENT 'Personnel approver (NO FK)',
  `approved_date` datetime DEFAULT NULL,
  `rejection_reason` text,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `purchase_date` date DEFAULT NULL,
  `purchase_amount` bigint(20) DEFAULT NULL,
  `personnel_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_upload_date` (`upload_date`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_purchase_date` (`purchase_date`),
  KEY `idx_approval_status` (`approval_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_receipts`
--

LOCK TABLES `purchase_receipts` WRITE;
/*!40000 ALTER TABLE `purchase_receipts` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_incomings`
--

DROP TABLE IF EXISTS `stock_incomings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_incomings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) NOT NULL,
  `purchase_receipt_id` int(11) DEFAULT NULL COMMENT 'FK to purchase_receipts (NO constraint)',
  `qty_po` decimal(11,2) NOT NULL,
  `incoming_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `qty_incoming` decimal(11,2) NOT NULL,
  `price_po` decimal(11,2) DEFAULT NULL,
  `qty_upcoming` decimal(11,2) DEFAULT NULL,
  `qty_after_incoming` decimal(11,5) DEFAULT NULL,
  `qty_before_incoming` decimal(11,5) DEFAULT NULL,
  `po_number` varchar(256) DEFAULT NULL,
  `price_avg` decimal(11,2) DEFAULT NULL,
  `personnel_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_purchase_receipt` (`purchase_receipt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_incomings`
--

LOCK TABLES `stock_incomings` WRITE;
/*!40000 ALTER TABLE `stock_incomings` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_incomings` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_before_stock-incoming_add` BEFORE INSERT ON `stock_incomings`
 FOR EACH ROW BEGIN
        SET NEW.qty_before_incoming = (SELECT qty FROM inventories WHERE id = NEW.inventory_id);
    
        SET NEW.qty_after_incoming = (SELECT qty FROM inventories WHERE id = NEW.inventory_id) + NEW.qty_incoming;
    
    IF NEW.qty_po > 0 THEN
                SET NEW.qty_upcoming = NEW.qty_po - NEW.qty_incoming;
    ELSE
                SET NEW.qty_upcoming = 0;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_after_stock-incoming_add` AFTER INSERT ON `stock_incomings`
 FOR EACH ROW UPDATE inventories SET qty = qty + NEW.qty_incoming WHERE NEW.inventory_id=id */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_before_stock-incoming_edit` BEFORE UPDATE ON `stock_incomings`
 FOR EACH ROW BEGIN
        SET NEW.qty_before_incoming = (SELECT qty FROM inventories WHERE id = NEW.inventory_id);
    
         SET NEW.qty_after_incoming = OLD.qty_after_incoming + (NEW.qty_incoming - OLD.qty_incoming);

    
    IF NEW.qty_po > 0 THEN
                SET NEW.qty_upcoming = NEW.qty_po - NEW.qty_incoming;
    ELSE
                SET NEW.qty_upcoming = 0;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_after_stock-incoming_edit` AFTER UPDATE ON `stock_incomings`
 FOR EACH ROW BEGIN
        DECLARE diff INT;
    SET diff = NEW.qty_incoming - OLD.qty_incoming;

        IF diff <> 0 THEN
        UPDATE inventories
        SET qty = qty + diff
        WHERE id = NEW.inventory_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_after_stock-incoming_delete` AFTER DELETE ON `stock_incomings`
 FOR EACH ROW UPDATE inventories SET qty = qty - OLD.qty_incoming WHERE id = OLD.inventory_id */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `stock_outgoings`
--

DROP TABLE IF EXISTS `stock_outgoings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_outgoings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) NOT NULL,
  `stock_incoming_id` int(11) DEFAULT NULL COMMENT 'Reference to stock_incoming (NO FK)',
  `qty_outgoing` decimal(11,2) NOT NULL,
  `unit_price` decimal(15,2) DEFAULT NULL COMMENT 'Harga satuan dari stock_incoming',
  `outgoing_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vehicle_id` int(11) DEFAULT NULL,
  `usage_description` varchar(256) DEFAULT NULL,
  `approval_status_id` int(11) NOT NULL DEFAULT '2' COMMENT 'Ref: asahi_online_approvals.approval_statuses (NO FK)',
  `approved_by_personnel_id` int(11) DEFAULT NULL COMMENT 'Personnel approver (NO FK)',
  `approved_date` datetime DEFAULT NULL,
  `rejection_reason` text,
  `qty_after_outgoing` decimal(11,2) DEFAULT NULL,
  `qty_before_outgoing` decimal(11,5) DEFAULT NULL,
  `personnel_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_stock_incoming` (`stock_incoming_id`),
  KEY `idx_approval_status` (`approval_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_outgoings`
--

LOCK TABLES `stock_outgoings` WRITE;
/*!40000 ALTER TABLE `stock_outgoings` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_outgoings` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_before_stock-outgoings_add` BEFORE INSERT ON `stock_outgoings`
 FOR EACH ROW SET NEW.qty_before_outgoing = (SELECT qty FROM inventories WHERE id = NEW.inventory_id), NEW.qty_after_outgoing = (SELECT qty FROM inventories WHERE id = NEW.inventory_id)-NEW.qty_outgoing */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_after_stock-outgoings_add` 
AFTER INSERT ON `stock_outgoings` 
FOR EACH ROW 
BEGIN
    
    UPDATE inventories 
    SET qty = qty - NEW.qty_outgoing,
        qty_after = qty,
        qty_before = qty + NEW.qty_outgoing
    WHERE id = NEW.inventory_id;
    
    
    SET @receipt_path = NULL;
    SELECT pr.file_path INTO @receipt_path
    FROM stock_incomings si
    LEFT JOIN purchase_receipts pr ON pr.id = si.purchase_receipt_id
    WHERE si.id = NEW.stock_incoming_id
    LIMIT 1;
    
    
    SET @amount = NEW.qty_outgoing * COALESCE(NEW.unit_price, 
        (SELECT price_po FROM stock_incomings WHERE id = NEW.stock_incoming_id), 
        0);
    
    
    SET @inventory_name = (SELECT name FROM inventories WHERE id = NEW.inventory_id);
    
    
    INSERT INTO accounting_transactions (
        transaction_date,
        description,
        debit_account,
        credit_account,
        amount,
        reference_type,
        reference_id,
        personnel_id,
        vehicle_id,
        receipt_file_path,
        created,
        modified
    ) VALUES (
        NEW.outgoing_date,
        CONCAT('Pemakaian Inventory: ', @inventory_name, ' (', NEW.qty_outgoing, ' unit)', 
               CASE WHEN NEW.usage_description IS NOT NULL 
                    THEN CONCAT(' - ', NEW.usage_description) 
                    ELSE '' END),
        'Beban Pemeliharaan',
        'Persediaan Inventory',
        @amount,
        'StockOutgoing',
        NEW.id,
        NEW.personnel_id,  
        NEW.vehicle_id,    
        @receipt_path,
        NOW(),
        NOW()
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_before_stock-outgoings_edit` BEFORE UPDATE ON `stock_outgoings`
 FOR EACH ROW SET NEW.qty_before_outgoing = (SELECT qty FROM inventories WHERE id = NEW.inventory_id), NEW.qty_after_outgoing = (SELECT qty FROM inventories WHERE id = NEW.inventory_id)-NEW.qty_outgoing */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `updade_after_stock-outgoings_edit` 
AFTER UPDATE ON `stock_outgoings` 
FOR EACH ROW 
BEGIN
    
    UPDATE inventories 
    SET qty = qty + OLD.qty_outgoing - NEW.qty_outgoing,
        qty_after = qty,
        qty_before = qty - OLD.qty_outgoing + NEW.qty_outgoing
    WHERE id = NEW.inventory_id;
    
    
    SET @receipt_path = NULL;
    SELECT pr.file_path INTO @receipt_path
    FROM stock_incomings si
    LEFT JOIN purchase_receipts pr ON pr.id = si.purchase_receipt_id
    WHERE si.id = NEW.stock_incoming_id
    LIMIT 1;
    
    
    SET @amount = NEW.qty_outgoing * COALESCE(NEW.unit_price, 
        (SELECT price_po FROM stock_incomings WHERE id = NEW.stock_incoming_id), 
        0);
    
    
    SET @inventory_name = (SELECT name FROM inventories WHERE id = NEW.inventory_id);
    
    
    UPDATE accounting_transactions 
    SET 
        transaction_date = NEW.outgoing_date,
        description = CONCAT('Pemakaian Inventory: ', @inventory_name, ' (', NEW.qty_outgoing, ' unit)', 
                            CASE WHEN NEW.usage_description IS NOT NULL 
                                 THEN CONCAT(' - ', NEW.usage_description) 
                                 ELSE '' END),
        amount = @amount,
        personnel_id = NEW.personnel_id,  
        vehicle_id = NEW.vehicle_id,      
        receipt_file_path = @receipt_path,
        modified = NOW()
    WHERE reference_type = 'StockOutgoing' 
      AND reference_id = NEW.id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_after_stock-outgoings_delete`
AFTER DELETE ON stock_outgoings
FOR EACH ROW
BEGIN
    
    UPDATE inventories SET qty = qty + OLD.qty_outgoing WHERE id = OLD.inventory_id;
    
    
    DELETE FROM accounting_transactions
    WHERE reference_type = 'StockOutgoing' AND reference_id = OLD.id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `stock_takes`
--

DROP TABLE IF EXISTS `stock_takes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_takes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) DEFAULT NULL,
  `quantity_book` decimal(11,2) DEFAULT NULL,
  `quantity_actual` decimal(11,2) DEFAULT NULL,
  `uom_id` int(11) DEFAULT NULL,
  `price` decimal(65,2) DEFAULT '0.00',
  `variance_quantity` decimal(11,2) DEFAULT '0.00',
  `variance_amount` decimal(65,3) DEFAULT '0.000',
  `remark` text,
  `counted` int(1) DEFAULT '0',
  `counter` int(11) DEFAULT NULL,
  `checker` int(11) DEFAULT NULL,
  `inputer` int(11) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_stock_take` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_takes`
--

LOCK TABLES `stock_takes` WRITE;
/*!40000 ALTER TABLE `stock_takes` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_takes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `storages`
--

DROP TABLE IF EXISTS `storages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `storages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `storages`
--

LOCK TABLES `storages` WRITE;
/*!40000 ALTER TABLE `storages` DISABLE KEYS */;
INSERT INTO `storages` VALUES (1,'SP','Spare Part Room','2025-10-18 02:08:06','2014-08-29 01:19:36'),(2,'OG','Oil, Grease and Tire Room','2025-10-18 02:09:21','2014-08-29 01:19:36'),(3,'SC','Storing Car','2025-10-18 02:10:28','2014-08-29 01:19:36'),(11,'OS','Office Storage','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `storages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `title` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `telephone` varchar(256) DEFAULT NULL,
  `payment_method_id` int(11) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `account_no` int(11) DEFAULT NULL,
  `account_name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'SUP-AFI-001','Duta Motor','Cikampek, Karawang','0264-000001',1,14,1000000001,'Duta Motor'),(2,'SUP-AFI-002','Jaya Abadi','Cikampek, Karawang','0264-000002',1,8,1000000002,'Jaya Abadi'),(3,'SUP-AFI-003','Wahana Karya','Cikampek, Karawang','0264-000003',2,2,1000000003,'Wahana Karya'),(4,'SUP-AFI-004','Dwi Abadi','Cikampek, Karawang','0264-000004',2,9,1000000004,'Dwi Abadi'),(5,'SUP-AFI-005','YY Teknik','Cikampek, Karawang','0264-000005',1,22,1000000005,'YY Teknik'),(6,'SUP-AFI-006','Bengkel B','Cikampek, Karawang','0264-000006',1,13,1000000006,'Bengkel B'),(7,'SUP-AFI-007','Sudirman','Cikampek, Karawang','0264-000007',2,426,1000000007,'Sudirman'),(8,'SUP-AFI-008','Jasutra','Cikampek, Karawang','0264-000008',1,451,1000000008,'Jasutra'),(9,'SUP-AFI-009','TB Mulya J','Cikampek, Karawang','0264-000009',2,11,1000000009,'TB Mulya J'),(10,'SUP-AFI-010','Tammim M','Cikampek, Karawang','0264-000010',1,14,1000000010,'Tammim M'),(11,'SUP-AFI-011','Sri Rejeki','Cikampek, Karawang','0264-000011',1,8,1000000011,'Sri Rejeki'),(12,'SUP-AFI-012','Bandung R','Cikampek, Karawang','0264-000012',2,2,1000000012,'Bandung R'),(13,'SUP-AFI-013','Fotocopy','Cikampek, Karawang','0264-000013',2,9,1000000013,'Fotocopy'),(14,'SUP-AFI-014','Jomin Jaya','Cikampek, Karawang','0264-000014',1,22,1000000014,'Jomin Jaya'),(15,'SUP-AFI-015','CV. Cahaya','Cikampek, Karawang','0264-000015',1,13,1000000015,'CV. Cahaya'),(16,'SUP-AFI-016','CV. Denbag','Cikampek, Karawang','0264-000016',2,426,1000000016,'CV. Denbag'),(17,'SUP-AFI-017','Surya Jaya','Cikampek, Karawang','0264-000017',1,451,1000000017,'Surya Jaya'),(18,'SUP-AFI-018','JH Jok','Cikampek, Karawang','0264-000018',2,11,1000000018,'JH Jok'),(19,'SUP-AFI-019','Sahabat D','Cikampek, Karawang','0264-000019',1,14,1000000019,'Sahabat D'),(20,'SUP-AFI-020','3S Cikarang','Cikarang, Bekasi','0264-000020',2,8,1000000020,'3S Cikarang');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uoms`
--

DROP TABLE IF EXISTS `uoms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uoms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(256) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uoms`
--

LOCK TABLES `uoms` WRITE;
/*!40000 ALTER TABLE `uoms` DISABLE KEYS */;
INSERT INTO `uoms` VALUES (1,'BAG','Bag','2013-12-23 04:02:00','2013-12-23 11:03:00'),(2,'BOWL','Bowl','2013-12-23 04:02:00','2013-12-23 11:03:00'),(3,'BX','Box','2013-12-23 04:02:00','2013-12-23 11:03:00'),(4,'BKT','Bucket','2013-12-23 04:02:00','2013-12-23 11:03:00'),(5,'BND','Bundle','2013-12-23 04:02:00','2013-12-23 11:03:00'),(6,'CAN','Can','2013-12-23 04:02:00','2013-12-23 11:03:00'),(7,'CRD','Card','2013-12-23 04:02:00','2013-12-23 11:03:00'),(8,'CTN','Carton','2013-12-23 04:02:00','2013-12-23 11:03:00'),(9,'CS','Case','2013-12-23 04:02:00','2013-12-23 11:03:00'),(10,'CM','Centimeters','2013-12-23 04:02:00','2013-12-23 11:03:00'),(11,'DZ','Dozen','2013-12-23 04:02:00','2013-12-23 11:03:00'),(12,'EA','Each','2013-12-23 04:02:00','2013-12-23 11:03:00'),(13,'FT','Foot','2013-12-23 04:02:00','2013-12-23 11:03:00'),(14,'GAL','Gallon','2013-12-23 04:02:00','2013-12-23 11:03:00'),(15,'GROSS','Gross','2013-12-23 04:02:00','2013-12-23 11:03:00'),(16,'IN','Inches','2013-12-23 04:02:00','2013-12-23 11:03:00'),(17,'KIT','Kit','2013-12-23 04:02:00','2013-12-23 11:03:00'),(18,'LT','Litre','2013-12-23 04:02:00','2013-12-23 11:03:00'),(19,'LOT','Lot','2013-12-23 04:02:00','2013-12-23 11:03:00'),(20,'M','m','2022-09-22 06:49:18','2013-12-23 11:03:00'),(21,'M2','Meter2','2013-12-23 04:02:00','2013-12-23 11:03:00'),(22,'M3','Meter3','2013-12-23 04:02:00','2013-12-23 11:03:00'),(23,'ML','Mili Litre','2013-12-23 04:02:00','2013-12-23 11:03:00'),(24,'MM','Millimeter','2013-12-23 04:02:00','2013-12-23 11:03:00'),(25,'PACK','Pack','2013-12-23 04:02:00','2013-12-23 11:03:00'),(26,'PK','Pack','2013-12-23 04:02:00','2013-12-23 11:03:00'),(27,'PAIL','Pail','2013-12-23 04:02:00','2013-12-23 11:03:00'),(28,'PR','Pair','2013-12-23 04:02:00','2013-12-23 11:03:00'),(29,'PC','pcs','2022-09-22 06:49:38','2013-12-23 11:03:00'),(30,'RACK','Rack','2013-12-23 04:02:00','2013-12-23 11:03:00'),(31,'RIM','Rim','2013-12-23 04:02:00','2013-12-23 11:03:00'),(32,'RL','Roll','2013-12-23 04:02:00','2013-12-23 11:03:00'),(33,'SET','Set','2013-12-23 04:02:00','2013-12-23 11:03:00'),(34,'SHT','Sheet','2013-12-23 04:02:00','2013-12-23 11:03:00'),(35,'SGL','Single','2013-12-23 04:02:00','2013-12-23 11:03:00'),(36,'SQFT','Square ft','2013-12-23 04:02:00','2013-12-23 11:03:00'),(37,'TON','Ton','2013-12-23 04:02:00','2013-12-23 11:03:00'),(38,'TUBE','Tube','2013-12-23 04:02:00','2013-12-23 11:03:00'),(39,'UNIT','Unit','2013-12-23 04:02:00','2013-12-23 11:03:00'),(40,'YD','Yard','2013-12-23 04:02:00','2013-12-23 11:03:00'),(41,'ZAK','Zak','2013-12-23 04:02:00','2013-12-23 11:03:00'),(42,'KG','Kilo Gram','2025-08-15 09:47:17','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `uoms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'asahi_inventories'
--

--
-- Dumping routines for database 'asahi_inventories'
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
