-- MariaDB dump 10.19  Distrib 10.4.27-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: system_authentication_authorization
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
-- Table structure for table `controller_permissions`
--

DROP TABLE IF EXISTS `controller_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `controller_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controller_name` varchar(100) NOT NULL,
  `database_connection_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_controller` (`controller_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `controller_permissions`
--

LOCK TABLES `controller_permissions` WRITE;
/*!40000 ALTER TABLE `controller_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `controller_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `database_connection_scopes`
--

DROP TABLE IF EXISTS `database_connection_scopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `database_connection_scopes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connection_name` varchar(100) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `database_connection_scopes`
--

LOCK TABLES `database_connection_scopes` WRITE;
/*!40000 ALTER TABLE `database_connection_scopes` DISABLE KEYS */;
INSERT INTO `database_connection_scopes` VALUES (1,'cms_masters','Master Data','Master data tables','2025-11-23 02:40:50','2025-11-23 02:40:50'),(2,'cms_lpk_candidates','LPK Candidates','LPK candidate data','2025-11-23 02:40:50','2025-11-23 02:40:50'),(3,'cms_lpk_candidate_documents','LPK Candidate Documents','LPK candidate documents','2025-11-23 02:40:50','2025-11-23 02:40:50'),(4,'cms_tmm_apprentices','TMM Apprentices','TMM apprentice data','2025-11-23 02:40:50','2025-11-23 02:40:50'),(5,'cms_tmm_apprentice_documents','TMM Apprentice Documents','TMM apprentice documents','2025-11-23 02:40:50','2025-11-23 02:40:50'),(7,'cms_tmm_stakeholders','TMM Stakeholders','TMM stakeholder data','2025-11-23 02:40:50','2025-11-23 02:40:50'),(8,'cms_tmm_trainees','TMM Trainees','TMM trainee data','2025-11-23 02:40:50','2025-11-23 02:40:50');
/*!40000 ALTER TABLE `database_connection_scopes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_logs`
--

DROP TABLE IF EXISTS `email_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_key` varchar(50) DEFAULT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_recipient` (`recipient_email`),
  KEY `idx_status` (`status`),
  KEY `idx_template_key` (`template_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Email sending logs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_logs`
--

LOCK TABLES `email_logs` WRITE;
/*!40000 ALTER TABLE `email_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_key` varchar(50) NOT NULL COMMENT 'Unique identifier for template',
  `subject` varchar(255) NOT NULL COMMENT 'Email subject line',
  `body_html` text NOT NULL COMMENT 'HTML version of email body',
  `body_text` text DEFAULT NULL COMMENT 'Plain text version of email body',
  `variables` text DEFAULT NULL COMMENT 'JSON array of available variables',
  `description` varchar(255) DEFAULT NULL COMMENT 'Template description',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Template active status',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_key` (`template_key`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Email templates for system notifications';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_templates`
--

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
INSERT INTO `email_templates` VALUES (1,'institution_registration','Complete Your Institution Registration - TMM System','<!DOCTYPE html>\r\n<html>\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <style>\r\n        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\r\n        .container { max-width: 600px; margin: 0 auto; padding: 20px; }\r\n        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }\r\n        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }\r\n        .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }\r\n        .info-box { background: white; padding: 15px; border-left: 4px solid #667eea; margin: 15px 0; }\r\n        .footer { text-align: center; color: #6c757d; font-size: 12px; margin-top: 20px; }\r\n    </style>\r\n</head>\r\n<body>\r\n    <div class=\"container\">\r\n        <div class=\"header\">\r\n            <h1>Welcome to TMM System</h1>\r\n        </div>\r\n        <div class=\"content\">\r\n            <p>Dear <strong>{{institution_name}}</strong>,</p>\r\n            \r\n            <p>Your institution has been successfully registered in our Training Management System. To complete your registration and access the system, please follow the steps below:</p>\r\n            \r\n            <div class=\"info-box\">\r\n                <strong>Your Login Credentials:</strong><br>\r\n                Username: <strong>{{username}}</strong><br>\r\n                Email: <strong>{{email}}</strong>\r\n            </div>\r\n            \r\n            <p>Click the button below to set your password and complete the registration:</p>\r\n            \r\n            <p style=\"text-align: center;\">\r\n                <a href=\"{{registration_url}}\" class=\"button\">Complete Registration</a>\r\n            </p>\r\n            \r\n            <p><strong>Important:</strong> This registration link will expire on <strong>{{expiry_date}}</strong>. Please complete your registration before this date.</p>\r\n            \r\n            <p>If you did not expect this email or have any questions, please contact our support team.</p>\r\n            \r\n            <div class=\"footer\">\r\n                <p>Best regards,<br>TMM System Administration Team</p>\r\n                <p>&copy; 2025 Training Management System. All rights reserved.</p>\r\n            </div>\r\n        </div>\r\n    </div>\r\n</body>\r\n</html>','Welcome to TMM Apprentice Management System\r\n\r\nDear {{institution_name}},\r\n\r\nYour institution has been successfully registered in our Training Management System. To complete your registration and access the system, please follow the steps below:\r\n\r\nYour Login Credentials:\r\nUsername: {{username}}\r\nEmail: {{email}}\r\n\r\nComplete your registration by visiting this link:\r\n{{registration_url}}\r\n\r\nIMPORTANT: This registration link will expire on {{expiry_date}}. Please complete your registration before this date.\r\n\r\nIf you did not expect this email or have any questions, please contact our support team.\r\n\r\nBest regards,\r\nTMM System Administration Team\r\n\r\n© 2025 Training Management System. All rights reserved.','[\"institution_name\", \"username\", \"email\", \"registration_url\", \"expiry_date\"]','Email sent to institutions to complete registration process',1,'2025-11-23 19:02:09','2025-11-23 19:02:09');
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_controller_permissions`
--

DROP TABLE IF EXISTS `role_controller_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_controller_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `controller_permission_id` int(11) NOT NULL,
  `can_view` tinyint(1) DEFAULT 0,
  `can_add` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_controller` (`role_id`,`controller_permission_id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_controller_permission_id` (`controller_permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_controller_permissions`
--

LOCK TABLES `role_controller_permissions` WRITE;
/*!40000 ALTER TABLE `role_controller_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_controller_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_database_scopes`
--

DROP TABLE IF EXISTS `role_database_scopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_database_scopes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `database_connection_scope_id` int(11) NOT NULL,
  `access_level` enum('none','read','write','full') DEFAULT 'read',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_scope` (`role_id`,`database_connection_scope_id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_database_connection_scope_id` (`database_connection_scope_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_database_scopes`
--

LOCK TABLES `role_database_scopes` WRITE;
/*!40000 ALTER TABLE `role_database_scopes` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_database_scopes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'administrator','Administrator','Full system access',1,'2025-11-23 02:40:50','2025-11-23 02:40:50'),(2,'management','Management/Director','Read-only access to all interfaces for review and monitoring',1,'2025-11-23 02:40:50','2025-11-23 02:40:50'),(3,'tmm-recruitment','TMM Recruitment','TMM Recruitment team',1,'2025-11-23 02:40:50','2025-11-23 02:40:50'),(4,'tmm-training','TMM Training','TMM Training team',1,'2025-11-23 02:40:50','2025-11-23 02:40:50'),(5,'tmm-documentation','TMM Documentation','TMM Documentation team',1,'2025-11-23 02:40:50','2025-11-23 02:40:50'),(6,'lpk-penyangga','LPK Penyangga','LPK Penyangga access for all LPK institutions',1,'2025-11-23 02:40:50','2025-11-23 02:40:50');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_role` (`user_id`,`role_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (1,1,1,'2025-11-23 02:41:31'),(2,2,2,'2025-11-23 02:41:31'),(3,3,2,'2025-11-23 02:41:31'),(4,4,3,'2025-11-23 02:41:31'),(5,5,3,'2025-11-23 02:41:31'),(6,6,4,'2025-11-23 02:41:31'),(7,7,4,'2025-11-23 02:41:31'),(8,8,5,'2025-11-23 02:41:31'),(9,9,5,'2025-11-23 02:41:31'),(10,10,6,'2025-11-23 02:41:31'),(11,11,6,'2025-11-23 02:41:31'),(12,12,6,'2025-11-23 02:41:31'),(13,13,6,'2025-11-23 02:41:31'),(14,14,6,'2025-11-23 02:41:31');
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `institution_id` int(11) DEFAULT NULL COMMENT 'Link to vocational_training_institutions or special_skill_support_institutions for lpk-penyangga users',
  `institution_type` enum('vocational_training','special_skill_support') DEFAULT NULL COMMENT 'Type of institution for lpk-penyangga users',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_institution_id` (`institution_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@tmm.com','$2y$10$cka06dLU8frl88JDTPaHZug1RT9KiB9N54hxRnPvvUssJL1mSXJoG','System Administrator',1,NULL,NULL,'2025-11-23 02:41:31','2025-11-23 02:41:31'),(2,'director','director@tmm.com','$2y$10$buQsp/nT5dvSN3Ny.oRpyedb9IQqMyVeXw/noqdKyQ19ImhnJmcQG','Director TMM',1,NULL,NULL,'2025-11-23 02:41:31','2025-11-23 02:41:31'),(3,'manager','manager@tmm.com','$2y$10$qP6/ppj7dQn0q2W5CE9Tzet3MAjkK6M2vQ6fwwh7YW7r9904q7yAa','General Manager',1,NULL,NULL,'2025-11-23 02:41:31','2025-11-23 02:41:31'),(4,'recruitment1','recruitment1@tmm.com','$2y$10$OnPpaO0PoLVGEhd0OU3yWOzIZRbza.7ViWiV9E.iFwpzZIIZj0.pO','Recruitment Officer 1',1,NULL,NULL,'2025-11-23 02:41:31','2025-11-23 02:41:31'),(5,'recruitment2','recruitment2@tmm.com','$2y$10$OnPpaO0PoLVGEhd0OU3yWOzIZRbza.7ViWiV9E.iFwpzZIIZj0.pO','Recruitment Officer 2',1,NULL,NULL,'2025-11-23 02:41:31','2025-11-23 02:41:31'),(6,'training1','training1@tmm.com','$2y$10$XMYIWrA23o9LLMzcmokHlO8Xwi1rKf9RSXeU1/mLN/C4yY/HotiwK','Training Coordinator 1',1,NULL,NULL,'2025-11-23 02:41:31','2025-11-23 02:41:31'),(7,'training2','training2@tmm.com','$2y$10$XMYIWrA23o9LLMzcmokHlO8Xwi1rKf9RSXeU1/mLN/C4yY/HotiwK','Training Coordinator 2',1,NULL,NULL,'2025-11-23 02:41:31','2025-11-23 02:41:31'),(8,'documentation1','documentation1@tmm.com','$2y$10$B37Hov2M1CrVlwWk1/meEumjIQDJVBh7QnjP8FEBHCWyBSsDtr/Iy','Documentation Officer 1',1,NULL,NULL,'2025-11-23 02:41:31','2025-11-23 02:41:31'),(9,'documentation2','documentation2@tmm.com','$2y$10$B37Hov2M1CrVlwWk1/meEumjIQDJVBh7QnjP8FEBHCWyBSsDtr/Iy','Documentation Officer 2',1,NULL,NULL,'2025-11-23 02:41:31','2025-11-23 02:41:31'),(10,'lpk_semarang','lpk.semarang@example.com','$2y$10$buLzegmczTjrYKY7j.4bM.RNBZEwDyCZF8UhUEjrsewiBKafr9nJK','Admin LPK Semarang',1,1,'vocational_training','2025-11-23 02:41:31','2025-11-23 02:41:31'),(11,'lpk_makasar','lpk.makasar@example.com','$2y$10$buLzegmczTjrYKY7j.4bM.RNBZEwDyCZF8UhUEjrsewiBKafr9nJK','Admin LPK Makasar',1,2,'vocational_training','2025-11-23 02:41:31','2025-11-23 02:41:31'),(12,'lpk_medan','lpk.medan@example.com','$2y$10$buLzegmczTjrYKY7j.4bM.RNBZEwDyCZF8UhUEjrsewiBKafr9nJK','Admin LPK Medan',1,3,'vocational_training','2025-11-23 02:41:31','2025-11-23 02:41:31'),(13,'lpk_padang','lpk.padang@example.com','$2y$10$buLzegmczTjrYKY7j.4bM.RNBZEwDyCZF8UhUEjrsewiBKafr9nJK','Admin LPK Padang',1,4,'vocational_training','2025-11-23 02:41:31','2025-11-23 02:41:31'),(14,'lpk_bekasi','lpk.bekasi@example.com','$2y$10$buLzegmczTjrYKY7j.4bM.RNBZEwDyCZF8UhUEjrsewiBKafr9nJK','Admin LPK Bekasi',1,5,'vocational_training','2025-11-23 02:41:31','2025-11-23 02:41:31');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-29  1:44:57
