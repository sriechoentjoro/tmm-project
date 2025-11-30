-- ============================================
-- Database: cms_tmm_trainee_accountings
-- Exported: 20251124_035402
-- ============================================

DROP DATABASE IF EXISTS `cms_tmm_trainee_accountings`;
CREATE DATABASE `cms_tmm_trainee_accountings` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cms_tmm_trainee_accountings`;
-- MariaDB dump 10.19  Distrib 10.4.27-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cms_tmm_trainee_accountings
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
-- Table structure for table `master_currencies`
--

DROP TABLE IF EXISTS `master_currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_currencies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `currency_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_currencies`
--

LOCK TABLES `master_currencies` WRITE;
/*!40000 ALTER TABLE `master_currencies` DISABLE KEYS */;
INSERT INTO `master_currencies` VALUES (1,'AED','United Arab Emirates dirham','United Arab Emirates'),(2,'AFN','Afghani','Afghanistan'),(3,'ALL','Lek','Albania'),(4,'AMD','Armenian Dram','Armenia'),(5,'ANG','Netherlands Antillian Guilder','Netherlands Antilles'),(6,'AOA','Kwanza','Angola'),(7,'ARS','Argentine Peso','Argentina'),(8,'AUD','Australian Dollar','Australia, Australian Antarctic Territory, Christmas Island, Cocos (Keeling) Islands, Heard and McDonald Islands, Kiribati, Nauru, Norfolk Island, Tuvalu'),(9,'AWG','Aruban Guilder','Aruba'),(10,'AZN','Azerbaijanian Manat','Azerbaijan'),(11,'BAM','Convertible Marks','Bosnia and Herzegovina'),(12,'BBD','Barbados Dollar','Barbados'),(13,'BDT','Bangladeshi Taka','Bangladesh'),(14,'BGN','Bulgarian Lev','Bulgaria'),(15,'BHD','Bahraini Dinar','Bahrain'),(16,'BIF','Burundian Franc','Burundi'),(17,'BMD','Bermudian Dollar (customarily known as Bermuda Dollar)','Bermuda'),(18,'BND','Brunei Dollar','Brunei'),(19,'BOB','Boliviano','Bolivia'),(20,'BOV','Bolivian Mvdol (Funds code)','Bolivia'),(21,'BRL','Brazilian Real','Brazil'),(22,'BSD','Bahamian Dollar','Bahamas'),(23,'BTN','Ngultrum','Bhutan'),(24,'BWP','Pula','Botswana'),(25,'BYR','Belarussian Ruble','Belarus'),(26,'BZD','Belize Dollar','Belize'),(27,'CAD','Canadian Dollar','Canada'),(28,'CDF','Franc Congolais','Democratic Republic of Congo'),(29,'CHE','WIR Euro (complementary currency)','Switzerland'),(30,'CHF','Swiss Franc','Switzerland, Liechtenstein'),(31,'CHW','WIR Franc (complementary currency)','Switzerland'),(32,'CLF','Unidades de formento (Funds code)','Chile'),(33,'CLP','Chilean Peso','Chile'),(34,'CNY','Yuan Renminbi','Mainland China'),(35,'COP','Colombian Peso','Colombia'),(36,'COU','Unidad de Valor Real','Colombia'),(37,'CRC','Costa Rican Colon','Costa Rica'),(38,'CUP','Cuban Peso','Cuba'),(39,'CVE','Cape Verde Escudo','Cape Verde'),(40,'CYP','Cyprus Pound','Cyprus'),(41,'CZK','Czech Koruna','Czech Republic'),(42,'DJF','Djibouti Franc','Djibouti'),(43,'DKK','Danish Krone','Denmark, Faroe Islands, Greenland'),(44,'DOP','Dominican Peso','Dominican Republic'),(45,'DZD','Algerian Dinar','Algeria'),(46,'EEK','Kroon','Estonia'),(47,'EGP','Egyptian Pound','Egypt'),(48,'ERN','Nakfa','Eritrea'),(49,'ETB','Ethiopian Birr','Ethiopia'),(50,'EUR','Euro','European Union, see eurozone'),(51,'FJD','Fiji Dollar','Fiji'),(52,'FKP','Falkland Islands Pound','Falkland Islands'),(53,'GBP','Pound Sterling','United Kingdom'),(54,'GEL','Lari','Georgia'),(55,'GHS','Cedi','Ghana'),(56,'GIP','Gibraltar pound','Gibraltar'),(57,'GMD','Dalasi','Gambia'),(58,'GNF','Guinea Franc','Guinea'),(59,'GTQ','Quetzal','Guatemala'),(60,'GYD','Guyana Dollar','Guyana'),(61,'HKD','Hong Kong Dollar','Hong Kong Special Administrative Region'),(62,'HNL','Lempira','Honduras'),(63,'HRK','Croatian Kuna','Croatia'),(64,'HTG','Haiti Gourde','Haiti'),(65,'HUF','Forint','Hungary'),(66,'IDR','Rupiah','Indonesia'),(67,'ILS','New Israeli Shekel','Israel'),(68,'INR','Indian Rupee','Bhutan, India'),(69,'IQD','Iraqi Dinar','Iraq'),(70,'IRR','Iranian Rial','Iran'),(71,'ISK','Iceland Krona','Iceland'),(72,'JMD','Jamaican Dollar','Jamaica'),(73,'JOD','Jordanian Dinar','Jordan'),(74,'JPY','Japanese yen','Japan'),(75,'KES','Kenyan Shilling','Kenya'),(76,'KGS','Som','Kyrgyzstan'),(77,'KHR','Riel','Cambodia'),(78,'KMF','Comoro Franc','Comoros'),(79,'KPW','North Korean Won','North Korea'),(80,'KRW','South Korean Won','South Korea'),(81,'KWD','Kuwaiti Dinar','Kuwait'),(82,'KYD','Cayman Islands Dollar','Cayman Islands'),(83,'KZT','Tenge','Kazakhstan'),(84,'LAK','Kip','Laos'),(85,'LBP','Lebanese Pound','Lebanon'),(86,'LKR','Sri Lanka Rupee','Sri Lanka'),(87,'LRD','Liberian Dollar','Liberia'),(88,'LSL','Loti','Lesotho'),(89,'LTL','Lithuanian Litas','Lithuania'),(90,'LVL','Latvian Lats','Latvia'),(91,'LYD','Libyan Dinar','Libya'),(92,'MAD','Moroccan Dirham','Morocco, Western Sahara'),(93,'MDL','Moldovan Leu','Moldova'),(94,'MGA','Malagasy Ariary','Madagascar'),(95,'MKD','Denar','Former Yugoslav Republic of Macedonia'),(96,'MMK','Kyat','Myanmar'),(97,'MNT','Tugrik','Mongolia'),(98,'MOP','Pataca','Macau Special Administrative Region'),(99,'MRO','Ouguiya','Mauritania'),(100,'MTL','Maltese Lira','Malta'),(101,'MUR','Mauritius Rupee','Mauritius'),(102,'MVR','Rufiyaa','Maldives'),(103,'MWK','Kwacha','Malawi'),(104,'MXN','Mexican Peso','Mexico'),(105,'MXV','Mexican Unidad de Inversion (UDI) (Funds code)','Mexico'),(106,'MYR','Malaysian Ringgit','Malaysia'),(107,'MZN','Metical','Mozambique'),(108,'NAD','Namibian Dollar','Namibia'),(109,'NGN','Naira','Nigeria'),(110,'NIO','Cordoba Oro','Nicaragua'),(111,'NOK','Norwegian Krone','Norway'),(112,'NPR','Nepalese Rupee','Nepal'),(113,'NZD','New Zealand Dollar','Cook Islands, New Zealand, Niue, Pitcairn, Tokelau'),(114,'OMR','Rial Omani','Oman'),(115,'PAB','Balboa','Panama'),(116,'PEN','Nuevo Sol','Peru'),(117,'PGK','Kina','Papua New Guinea'),(118,'PHP','Philippine Peso','Philippines'),(119,'PKR','Pakistan Rupee','Pakistan'),(120,'PLN','Zloty','Poland'),(121,'PYG','Guarani','Paraguay'),(122,'QAR','Qatari Rial','Qatar'),(123,'RON','Romanian New Leu','Romania'),(124,'RSD','Serbian Dinar','Serbia'),(125,'RUB','Russian Ruble','Russia, Abkhazia, South Ossetia'),(126,'RWF','Rwanda Franc','Rwanda'),(127,'SAR','Saudi Riyal','Saudi Arabia'),(128,'SBD','Solomon Islands Dollar','Solomon Islands'),(129,'SCR','Seychelles Rupee','Seychelles'),(130,'SDG','Sudanese Pound','Sudan'),(131,'SEK','Swedish Krona','Sweden'),(132,'SGD','Singapore Dollar','Singapore'),(133,'SHP','Saint Helena Pound','Saint Helena'),(134,'SKK','Slovak Koruna','Slovakia'),(135,'SLL','Leone','Sierra Leone'),(136,'SOS','Somali Shilling','Somalia'),(137,'SRD','Surinam Dollar','Suriname'),(138,'STD','Dobra','S?o Tom? and Pr?ncipe'),(139,'SYP','Syrian Pound','Syria'),(140,'SZL','Lilangeni','Swaziland'),(141,'THB','Baht','Thailand'),(142,'TJS','Somoni','Tajikistan'),(143,'TMM','Manat','Turkmenistan'),(144,'TND','Tunisian Dinar','Tunisia'),(145,'TOP','Pa\'anga','Tonga'),(146,'TRY','New Turkish Lira','Turkey'),(147,'TTD','Trinidad and Tobago Dollar','Trinidad and Tobago'),(148,'TWD','New Taiwan Dollar','Taiwan and other islands that are under the effective control of the Republic of China (ROC)'),(149,'TZS','Tanzanian Shilling','Tanzania'),(150,'UAH','Hryvnia','Ukraine'),(151,'UGX','Uganda Shilling','Uganda'),(152,'USD','US Dollar','American Samoa, British Indian Ocean Territory, Ecuador, El Salvador, Guam, Haiti, Marshall Islands, Micronesia, Northern Mariana Islands, Palau, Panama, Puerto Rico, East Timor, Turks and Caicos Islands, United States, Virgin Islands'),(153,'USN','','United States'),(154,'USS','','United States'),(155,'UYU','Peso Uruguayo','Uruguay'),(156,'UZS','Uzbekistan Som','Uzbekistan'),(157,'VEB','Venezuelan bol?var','Venezuela'),(158,'VND','Vietnamese ??ng','Vietnam'),(159,'VUV','Vatu','Vanuatu'),(160,'WST','Samoan Tala','Samoa'),(161,'XAF','CFA Franc BEAC','Cameroon, Central African Republic, Congo, Chad, Equatorial Guinea, Gabon'),(162,'XAG','Silver (one Troy ounce)',''),(163,'XAU','Gold (one Troy ounce)',''),(164,'XBA','European Composite Unit (EURCO) (Bonds market unit)',''),(165,'XBB','European Monetary Unit (E.M.U.-6) (Bonds market unit)',''),(166,'XBC','European Unit of Account 9 (E.U.A.-9) (Bonds market unit)',''),(167,'XBD','European Unit of Account 17 (E.U.A.-17) (Bonds market unit)',''),(168,'XCD','East Caribbean Dollar','Anguilla, Antigua and Barbuda, Dominica, Grenada, Montserrat, Saint Kitts and Nevis, Saint Lucia, Saint Vincent and the Grenadines'),(169,'XDR','Special Drawing Rights','International Monetary Fund'),(170,'XFO','Gold franc (special settlement currency)','Bank for International Settlements'),(171,'XFU','UIC franc (special settlement currency)','International Union of Railways'),(172,'XOF','CFA Franc BCEAO','Benin, Burkina Faso, C?te d\'Ivoire, Guinea-Bissau, Mali, Niger, Senegal, Togo'),(173,'XPD','Palladium (one Troy ounce)',''),(174,'XPF','CFP franc','French Polynesia, New Caledonia, Wallis and Futuna'),(175,'XPT','Platinum (one Troy ounce)',''),(176,'XTS','Code reserved for testing purposes',''),(177,'YER','Yemeni Rial','Yemen'),(178,'ZAR','South African Rand','South Africa'),(179,'ZMK','Kwacha','Zambia'),(180,'ZWD','Zimbabwe Dollar','Zimbabwe');
/*!40000 ALTER TABLE `master_currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_payment_methods`
--

DROP TABLE IF EXISTS `master_payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_payment_methods` (
  `id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_payment_methods`
--

LOCK TABLES `master_payment_methods` WRITE;
/*!40000 ALTER TABLE `master_payment_methods` DISABLE KEYS */;
INSERT INTO `master_payment_methods` VALUES (1,'Cash'),(2,'Bank Transfer'),(3,'Credit Card');
/*!40000 ALTER TABLE `master_payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_transaction_categories`
--

DROP TABLE IF EXISTS `master_transaction_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_transaction_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_transaction_categories`
--

LOCK TABLES `master_transaction_categories` WRITE;
/*!40000 ALTER TABLE `master_transaction_categories` DISABLE KEYS */;
INSERT INTO `master_transaction_categories` VALUES (1,'Debit'),(2,'Credit');
/*!40000 ALTER TABLE `master_transaction_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainee_installments`
--

DROP TABLE IF EXISTS `trainee_installments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trainee_installments` (
  `id` int(11) NOT NULL,
  `trainee_id` int(11) NOT NULL,
  `master_transaction_category_id` int(11) NOT NULL,
  `payment_amount` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `full_payment_amount` int(11) NOT NULL,
  `payment_accummulated` int(11) NOT NULL,
  `unpaid_amount` int(11) NOT NULL,
  `master_currency_id` int(11) NOT NULL DEFAULT 66,
  `is_paid_off` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trainee_installments`
--

LOCK TABLES `trainee_installments` WRITE;
/*!40000 ALTER TABLE `trainee_installments` DISABLE KEYS */;
INSERT INTO `trainee_installments` VALUES (1,1,1,7000000,'2025-07-07',27000000,7000000,20000000,66,0),(2,3,1,1000000,'2025-07-28',5000000,1000000,4000000,74,0),(3,4,1,1500000,'2025-07-28',5000000,1500000,3500000,66,0),(4,5,1,2000000,'2025-07-28',5000000,2000000,3000000,66,0),(5,6,1,2500000,'2025-07-28',5000000,2500000,2500000,66,0),(6,7,1,3000000,'2025-07-28',5000000,3000000,2000000,74,0),(7,8,1,3500000,'2025-07-28',5000000,3500000,1500000,66,0),(8,9,1,4000000,'2025-07-28',5000000,4000000,1000000,66,0),(9,10,1,4500000,'2025-07-28',5000000,4500000,500000,66,0),(10,11,1,5000000,'2025-07-28',5000000,5000000,0,66,1),(11,12,1,5500000,'2025-07-28',6000000,5500000,500000,66,0),(12,1,1,20000000,'2025-08-08',27000000,0,0,0,1),(13,110,1,1000000,'2025-08-05',1000000,1000000,1000000,66,127);
/*!40000 ALTER TABLE `trainee_installments` ENABLE KEYS */;
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
