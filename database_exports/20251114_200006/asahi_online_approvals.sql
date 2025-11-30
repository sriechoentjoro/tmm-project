-- MySQL dump 10.16  Distrib 10.1.13-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: asahi_online_approvals
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
-- Current Database: `asahi_online_approvals`
--

/*!40000 DROP DATABASE IF EXISTS `asahi_online_approvals`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `asahi_online_approvals` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `asahi_online_approvals`;

--
-- Table structure for table `approval_matrix`
--

DROP TABLE IF EXISTS `approval_matrix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_matrix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matrix_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. Standard Purchase, Emergency Purchase',
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'PurchaseReceipt, StockOutgoing',
  `is_default` tinyint(1) DEFAULT '0' COMMENT 'Default matrix untuk reference_type ini',
  `priority` int(11) DEFAULT '100' COMMENT 'Lower = higher priority',
  `active` tinyint(1) DEFAULT '1',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reference` (`reference_type`),
  KEY `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Approval matrix untuk different scenarios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_matrix`
--

LOCK TABLES `approval_matrix` WRITE;
/*!40000 ALTER TABLE `approval_matrix` DISABLE KEYS */;
/*!40000 ALTER TABLE `approval_matrix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approval_statuses`
--

DROP TABLE IF EXISTS `approval_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'draft, pending, approved, rejected, cancelled',
  `status_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Display name',
  `status_color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'badge-info, badge-warning, badge-success, badge-danger',
  `description` text COLLATE utf8mb4_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `status_code` (`status_code`),
  KEY `idx_code` (`status_code`),
  KEY `idx_active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master table untuk approval status';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_statuses`
--

LOCK TABLES `approval_statuses` WRITE;
/*!40000 ALTER TABLE `approval_statuses` DISABLE KEYS */;
INSERT INTO `approval_statuses` VALUES (1,'draft','Draft','badge-info','Masih draft, belum submit',1,'2025-11-04 11:15:56','2025-11-04 11:15:56'),(2,'pending','Pending','badge-warning','Menunggu approval',1,'2025-11-04 11:15:56','2025-11-04 11:15:56'),(3,'approved','Approved','badge-success','Sudah di-approve',1,'2025-11-04 11:15:56','2025-11-04 11:15:56'),(4,'rejected','Rejected','badge-danger','Di-reject/ditolak',1,'2025-11-04 11:15:56','2025-11-04 11:15:56'),(5,'cancelled','Cancelled','badge-secondary','Dibatalkan oleh user',1,'2025-11-04 11:15:56','2025-11-04 11:15:56');
/*!40000 ALTER TABLE `approval_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approval_workflows`
--

DROP TABLE IF EXISTS `approval_workflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'PurchaseReceipt, StockOutgoing',
  `approval_level` tinyint(1) NOT NULL COMMENT 'Level approval (1, 2, 3)',
  `approver_role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Supervisor, Manager, Director',
  `approver_personnel_id` int(11) DEFAULT NULL COMMENT 'Specific approver (NO FK), NULL = any with role',
  `min_amount` decimal(15,2) DEFAULT NULL COMMENT 'Min amount untuk level ini',
  `max_amount` decimal(15,2) DEFAULT NULL COMMENT 'Max amount (NULL=unlimited)',
  `send_email` tinyint(1) DEFAULT '1' COMMENT 'Send email notification',
  `send_whatsapp` tinyint(1) DEFAULT '1' COMMENT 'Send WhatsApp notification',
  `is_required` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=Wajib, 0=Optional',
  `auto_approve` tinyint(1) DEFAULT '0' COMMENT 'Auto approve if conditions met',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_workflow` (`reference_type`,`approval_level`),
  KEY `idx_reference` (`reference_type`),
  KEY `idx_amount_range` (`min_amount`,`max_amount`),
  KEY `idx_approver` (`approver_personnel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Workflow rules untuk approval';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_workflows`
--

LOCK TABLES `approval_workflows` WRITE;
/*!40000 ALTER TABLE `approval_workflows` DISABLE KEYS */;
INSERT INTO `approval_workflows` VALUES (1,'Purchase Level 1','PurchaseReceipt',1,'Supervisor',1,0.00,10000000.00,1,1,1,0,1,'2025-11-04 11:15:57','2025-11-04 16:40:49'),(2,'Purchase Level 2','PurchaseReceipt',2,'Manager',2,10000001.00,50000000.00,1,1,1,0,1,'2025-11-04 11:15:57','2025-11-04 16:40:49'),(3,'Purchase Level 3','PurchaseReceipt',3,'Director',3,50000001.00,NULL,1,0,1,0,1,'2025-11-04 11:15:57','2025-11-04 16:40:49'),(4,'Stock Usage Level 1','StockOutgoing',1,'Supervisor',4,NULL,NULL,1,1,1,0,1,'2025-11-04 11:15:57','2025-11-04 16:40:49');
/*!40000 ALTER TABLE `approval_workflows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approvals`
--

DROP TABLE IF EXISTS `approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approvals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'PurchaseReceipt, StockOutgoing, dll',
  `reference_id` int(11) NOT NULL COMMENT 'ID dari transaksi (NO FK)',
  `approval_level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=First, 2=Second, dst',
  `approval_status_id` int(11) NOT NULL DEFAULT '2' COMMENT 'Reference to approval_statuses (NO FK)',
  `personnel_id` int(11) DEFAULT NULL COMMENT 'Personnel yang approve/reject (NO FK)',
  `submitted_date` datetime NOT NULL COMMENT 'Kapan diajukan',
  `approved_date` datetime DEFAULT NULL COMMENT 'Kapan di-approve/reject',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan approver',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'Alasan reject',
  `approval_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Unique token untuk approval link',
  `token_expires` datetime DEFAULT NULL COMMENT 'Token expiry datetime',
  `email_sent` tinyint(1) DEFAULT '0' COMMENT 'Email notification sent',
  `email_sent_date` datetime DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reference` (`reference_type`,`reference_id`),
  KEY `idx_status` (`approval_status_id`),
  KEY `idx_approver` (`personnel_id`),
  KEY `idx_submitted` (`submitted_date`),
  KEY `idx_approved` (`approved_date`),
  KEY `idx_token` (`approval_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Multi-level approval records';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approvals`
--

LOCK TABLES `approvals` WRITE;
/*!40000 ALTER TABLE `approvals` DISABLE KEYS */;
/*!40000 ALTER TABLE `approvals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_templates`
--

DROP TABLE IF EXISTS `notification_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification_type` enum('email','whatsapp') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'PurchaseReceipt, StockOutgoing',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email subject (NULL for WhatsApp)',
  `body_template` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Template body with placeholders',
  `active` tinyint(1) DEFAULT '1',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_name` (`template_name`),
  KEY `idx_type` (`notification_type`,`reference_type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Email & WhatsApp templates';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_templates`
--

LOCK TABLES `notification_templates` WRITE;
/*!40000 ALTER TABLE `notification_templates` DISABLE KEYS */;
INSERT INTO `notification_templates` VALUES (1,'purchase_receipt_email','email','PurchaseReceipt','Purchase Approval Required: {title}','<h2>Purchase Approval Request</h2>\n  <p><strong>Title:</strong> {title}</p>\n  <p><strong>Amount:</strong> Rp {amount}</p>\n  <p><strong>Date:</strong> {date}</p>\n  <p><strong>Requested by:</strong> {requester}</p>\n  <p><strong>Description:</strong> {description}</p>\n  <hr>\n  <p>\n    <a href=\"{approve_link}\" style=\"background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;\">APPROVE</a>\n    <a href=\"{reject_link}\" style=\"background:#dc3545;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin-left:10px;\">REJECT</a>\n  </p>\n  <p><small>Token expires in 7 days</small></p>',1,'2025-11-04 11:15:56','2025-11-04 11:15:56'),(2,'purchase_receipt_whatsapp','whatsapp','PurchaseReceipt',NULL,'???? *PURCHASE APPROVAL REQUIRED*\n\n???? *{title}*\n???? Amount: *Rp {amount}*\n???? Date: {date}\n???? Requester: {requester}\n\n{description}\n\n??? Approve: {approve_link}\n??? Reject: {reject_link}\n\n_Token expires in 7 days_',1,'2025-11-04 11:15:56','2025-11-04 11:15:56'),(3,'stock_outgoing_email','email','StockOutgoing','Stock Usage Approval Required','<h2>Stock Usage Approval Request</h2>\n  <p><strong>Item:</strong> {item_name}</p>\n  <p><strong>Quantity:</strong> {quantity} units</p>\n  <p><strong>Amount:</strong> Rp {amount}</p>\n  <p><strong>Vehicle:</strong> {vehicle}</p>\n  <p><strong>Usage:</strong> {usage_description}</p>\n  <p><strong>Requested by:</strong> {requester}</p>\n  <hr>\n  <p>\n    <a href=\"{approve_link}\" style=\"background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;\">APPROVE</a>\n    <a href=\"{reject_link}\" style=\"background:#dc3545;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin-left:10px;\">REJECT</a>\n  </p>',1,'2025-11-04 11:15:56','2025-11-04 11:15:56'),(4,'stock_outgoing_whatsapp','whatsapp','StockOutgoing',NULL,'???? *STOCK USAGE APPROVAL*\n\n???? Item: *{item_name}*\n???? Qty: {quantity} units\n???? Amount: *Rp {amount}*\n???? Vehicle: {vehicle}\n???? Usage: {usage_description}\n???? By: {requester}\n\n??? Approve: {approve_link}\n??? Reject: {reject_link}',1,'2025-11-04 11:15:56','2025-11-04 11:15:56');
/*!40000 ALTER TABLE `notification_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_request_items`
--

DROP TABLE IF EXISTS `purchase_request_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_request_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_request_id` int(11) NOT NULL,
  `inventory_id` int(11) DEFAULT NULL COMMENT 'Link to inventories if existing item',
  `item_description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Item category',
  `quantity` decimal(10,2) NOT NULL DEFAULT '1.00',
  `uom` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Unit of measure',
  `estimated_unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `estimated_subtotal` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Calculated as qty * price',
  `preferred_supplier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_request` (`purchase_request_id`),
  KEY `idx_inventory` (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Purchase request line items';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_request_items`
--

LOCK TABLES `purchase_request_items` WRITE;
/*!40000 ALTER TABLE `purchase_request_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_request_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_requests`
--

DROP TABLE IF EXISTS `purchase_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto-generated: PR-YYYYMMDD-XXX',
  `requester_personnel_id` int(11) NOT NULL COMMENT 'Employee making the request',
  `department_id` int(11) DEFAULT NULL COMMENT 'Requester department',
  `request_date` date NOT NULL,
  `required_date` date DEFAULT NULL COMMENT 'When items are needed',
  `purpose` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Business justification',
  `total_estimated_amount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total request value',
  `priority` enum('Low','Normal','High','Urgent') COLLATE utf8mb4_unicode_ci DEFAULT 'Normal',
  `status` enum('Draft','Submitted','In Review','Approved','Rejected','Cancelled','Completed') COLLATE utf8mb4_unicode_ci DEFAULT 'Draft',
  `approval_id` int(11) DEFAULT NULL COMMENT 'Link to approvals table',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_number` (`request_number`),
  KEY `idx_requester` (`requester_personnel_id`),
  KEY `idx_department` (`department_id`),
  KEY `idx_status` (`status`),
  KEY `idx_request_date` (`request_date`),
  KEY `idx_approval` (`approval_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Purchase request headers before creating purchase receipts';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_requests`
--

LOCK TABLES `purchase_requests` WRITE;
/*!40000 ALTER TABLE `purchase_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'asahi_online_approvals'
--

--
-- Dumping routines for database 'asahi_online_approvals'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-14 20:00:08
