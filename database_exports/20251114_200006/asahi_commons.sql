-- MySQL dump 10.16  Distrib 10.1.13-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: asahi_commons
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
-- Current Database: `asahi_commons`
--

/*!40000 DROP DATABASE IF EXISTS `asahi_commons`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `asahi_commons` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `asahi_commons`;

--
-- Table structure for table `counters`
--

DROP TABLE IF EXISTS `counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `counters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL,
  `time` varchar(11) NOT NULL,
  `date_visit` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `counters`
--

LOCK TABLES `counters` WRITE;
/*!40000 ALTER TABLE `counters` DISABLE KEYS */;
INSERT INTO `counters` VALUES (1,'127.0.0.1','929','22/4/2012'),(2,'127.0.0.1','956','22/4/2012'),(3,'127.0.0.1','1381','22/4/2012'),(4,'127.0.0.1','440','23/4/2012'),(5,'127.0.0.1','456','23/4/2012'),(6,'127.0.0.1','481','23/4/2012'),(7,'127.0.0.1','498','23/4/2012'),(8,'127.0.0.1','551','23/4/2012'),(9,'127.0.0.1','599','23/4/2012'),(10,'127.0.0.1','616','23/4/2012'),(11,'127.0.0.1','1013','23/4/2012'),(12,'127.0.0.1','1036','23/4/2012'),(13,'127.0.0.1','1053','23/4/2012'),(14,'127.0.0.1','317','25/4/2012'),(15,'127.0.0.1','350','25/4/2012'),(16,'127.0.0.1','464','25/4/2012'),(17,'127.0.0.1','727','25/4/2012'),(18,'127.0.0.1','794','25/4/2012'),(19,'127.0.0.1','822','25/4/2012'),(20,'127.0.0.1','844','25/4/2012'),(21,'127.0.0.1','997','25/4/2012'),(22,'127.0.0.1','1017','25/4/2012'),(23,'127.0.0.1','1033','25/4/2012'),(24,'127.0.0.1','44','26/4/2012'),(25,'127.0.0.1','70','26/4/2012'),(26,'127.0.0.1','86','26/4/2012'),(27,'127.0.0.1','102','26/4/2012'),(28,'127.0.0.1','118','26/4/2012'),(29,'127.0.0.1','917','26/4/2012'),(30,'127.0.0.1','933','26/4/2012'),(31,'127.0.0.1','949','26/4/2012'),(32,'127.0.0.1','972','26/4/2012'),(33,'127.0.0.1','989','26/4/2012'),(34,'127.0.0.1','1008','26/4/2012'),(35,'127.0.0.1','1028','26/4/2012'),(36,'127.0.0.1','1056','26/4/2012'),(37,'127.0.0.1','1077','26/4/2012'),(38,'127.0.0.1','38','27/4/2012'),(39,'127.0.0.1','75','27/4/2012'),(40,'127.0.0.1','98','27/4/2012'),(41,'127.0.0.1','489','27/4/2012'),(42,'127.0.0.1','510','27/4/2012'),(43,'127.0.0.1','541','27/4/2012'),(44,'127.0.0.1','559','27/4/2012'),(45,'127.0.0.1','620','27/4/2012'),(46,'127.0.0.1','636','27/4/2012'),(47,'127.0.0.1','694','27/4/2012'),(48,'127.0.0.1','851','27/4/2012'),(49,'127.0.0.1','868','27/4/2012'),(50,'127.0.0.1','889','27/4/2012'),(51,'127.0.0.1','992','27/4/2012'),(52,'127.0.0.1','1031','27/4/2012'),(53,'127.0.0.1','1076','27/4/2012'),(54,'127.0.0.1','1095','27/4/2012'),(55,'127.0.0.1','1385','27/4/2012'),(56,'127.0.0.1','1411','27/4/2012'),(57,'127.0.0.1','8','28/4/2012'),(58,'127.0.0.1','31','28/4/2012'),(59,'127.0.0.1','47','28/4/2012'),(60,'127.0.0.1','954','28/4/2012'),(61,'127.0.0.1','971','28/4/2012'),(62,'127.0.0.1','1435','28/4/2012'),(63,'127.0.0.1','0','29/4/2012'),(64,'127.0.0.1','81','29/4/2012'),(65,'127.0.0.1','907','29/4/2012'),(66,'127.0.0.1','952','29/4/2012'),(67,'127.0.0.1','406','1/5/2012'),(68,'127.0.0.1','694','1/5/2012'),(69,'127.0.0.1','710','1/5/2012'),(70,'127.0.0.1','829','1/5/2012'),(71,'127.0.0.1','878','1/5/2012'),(72,'127.0.0.1','924','1/5/2012'),(73,'127.0.0.1','940','1/5/2012'),(74,'127.0.0.1','958','1/5/2012'),(75,'127.0.0.1','974','1/5/2012'),(76,'127.0.0.1','1371','1/5/2012'),(77,'127.0.0.1','1023','2/5/2012'),(78,'127.0.0.1','1043','2/5/2012'),(79,'127.0.0.1','1059','2/5/2012'),(80,'127.0.0.1','492','3/5/2012'),(81,'127.0.0.1','1358','3/5/2012'),(82,'127.0.0.1','1384','3/5/2012'),(83,'127.0.0.1','1401','3/5/2012'),(84,'127.0.0.1','928','5/5/2012'),(85,'127.0.0.1','972','5/5/2012'),(86,'127.0.0.1','988','5/5/2012'),(87,'127.0.0.1','1005','5/5/2012'),(88,'127.0.0.1','218','6/5/2012'),(89,'127.0.0.1','245','6/5/2012'),(90,'127.0.0.1','373','6/5/2012'),(91,'127.0.0.1','613','6/5/2012'),(92,'127.0.0.1','632','6/5/2012'),(93,'127.0.0.1','649','6/5/2012'),(94,'127.0.0.1','693','6/5/2012'),(95,'127.0.0.1','710','6/5/2012'),(96,'127.0.0.1','726','6/5/2012'),(97,'127.0.0.1','758','6/5/2012'),(98,'127.0.0.1','776','6/5/2012'),(99,'127.0.0.1','828','6/5/2012'),(100,'127.0.0.1','844','6/5/2012'),(101,'127.0.0.1','873','6/5/2012'),(102,'127.0.0.1','902','6/5/2012'),(103,'127.0.0.1','943','6/5/2012'),(104,'127.0.0.1','959','6/5/2012'),(105,'127.0.0.1','981','6/5/2012'),(106,'127.0.0.1','1406','7/5/2012'),(107,'127.0.0.1','1422','7/5/2012'),(108,'127.0.0.1','88','8/5/2012'),(109,'127.0.0.1','121','8/5/2012'),(110,'127.0.0.1','402','11/5/2012'),(111,'127.0.0.1','761','11/5/2012'),(112,'127.0.0.1','945','12/5/2012'),(113,'127.0.0.1','981','16/5/2012'),(114,'127.0.0.1','981','16/5/2012'),(115,'127.0.0.1','615','18/5/2012'),(116,'127.0.0.1','810','18/5/2012'),(117,'127.0.0.1','423','19/5/2012'),(118,'127.0.0.1','715','22/5/2012'),(119,'127.0.0.1','715','22/5/2012'),(120,'127.0.0.1','1058','22/5/2012'),(121,'127.0.0.1','1176','22/5/2012'),(122,'127.0.0.1','366','25/5/2012'),(123,'127.0.0.1','271','26/5/2012'),(124,'127.0.0.1','290','26/5/2012'),(125,'127.0.0.1','306','26/5/2012'),(126,'127.0.0.1','322','26/5/2012'),(127,'127.0.0.1','340','26/5/2012'),(128,'127.0.0.1','356','26/5/2012'),(129,'127.0.0.1','482','26/5/2012'),(130,'127.0.0.1','1165','27/5/2012'),(131,'127.0.0.1','438','3/6/2012');
/*!40000 ALTER TABLE `counters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `is_overseas` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'C0101',0,'SHOWA INDONESIA MANUFACTURING','JL. JABABEKA VI KAV. 28-36 CIKARANG - BEKASI','2012-06-07 08:37:24','2012-06-07 16:37:00'),(2,'C0102',0,'KAYABA INDONESIA','JL. JAWA BLOK 11 NO.4 KAWASAN INDUSTRI MM 2100 JATIWANGI CIKARANG BARAT BEKASI- JAWA BARAT 17520','2012-06-07 08:37:24','2012-06-07 16:37:00'),(3,'C0103',0,'INTI GANDA PERDANA','JL. PEGANGSAAN DUA BLOK A-3 KM 1.6 PEGANGSAAN DUA KELAPA GADING JAKARTA UTARA 14250','2012-06-07 08:37:24','2012-06-07 16:37:00'),(4,'C0104',0,'MITSUBA INDONESIA','JL. SILIWANGI KERONCONG JATI UWUNG TANGERANG BANTEN - 15134','2012-06-07 08:37:24','2012-06-07 16:37:00'),(5,'C0105',0,'DHARMA PRECISION PARTS','JL. JABABEKA VI BLOK J NO. 60 KAW. INDUSTRI CIKARANG HARJA MEKAR-CIKARANG UTARA-KAB BEKASI-17520','2012-06-07 08:37:24','2012-06-07 16:37:00'),(6,'C0106',0,'HONDA PROSPECT MOTOR','JL. MITRA UTARA II KAWASAN INDUSTRI MITRAKARAWANG (KIM) DS. PARUNGMULYA KEC.CIAMPEL-KARAWANG 41361','2012-06-07 08:37:24','2012-06-07 16:37:00'),(7,'C0107',0,'ASTRA DAIHATSU MOTOR','JL. GAYA MOTOR III NO. 5 SUNTER II JAKARTA 14330','2012-06-07 08:37:24','2012-06-07 16:37:00'),(8,'C0108',0,'KALIHURIP MANDIRI','JL. KP SASAKSENG RT.01/05. KALIHURIP CIKAMPEK KARAWANG JAWA BARAT 00000 JAWA BARAT 00000','2012-06-07 08:37:24','2012-06-07 16:37:00'),(9,'C0109',0,'FUKOKU TOKAI RUBBER INDONESIA','JL. INDUSTRI SELATAN 6A BLOK GG 6A-F JABABEKA PASIR SARI CIKARANG BEKASI JAWA BARAT-17520','2012-06-07 08:37:24','2012-06-07 16:37:00'),(10,'C0110',0,'PAMINDO TIGA T','PULOGADUNG FACTORY JL. RAWA GATAL KAV.7&8 KAWASAN INDUSTRI PULOGADUNG JAKARTA TIMUR','2012-06-07 08:37:24','2012-06-07 16:37:00'),(11,'C0111',0,'SARI TAKAGI ELOK PRODUK (STEP)','JL. JABABEKA BLOK F 33 CIKARANG INDUSTRIAL ESTATE CIKARANG CIKARANG BEKASI JAWA BARAT- 17520','2012-06-07 08:37:24','2012-06-07 16:37:00'),(12,'C0112',0,'CIPTA JAYA','DUSUN KAMUNING RT.06 RW.03 KALIHURIP CIKAMPEK KARAWANG','2012-06-07 08:37:24','2012-06-07 16:37:00'),(13,'C0113',0,'DHARMA POLIMETAL','JL. RAYA SERANG KM.24 BALARAJA TANGERANG 15610 BANTEN-INDONESIA','2012-06-07 08:37:24','2012-06-07 16:37:00'),(14,'C0114',0,'MENARA TERUS MAKMUR','JL. JABABEKA XI BLOK H.3-12 KAWASAN INDST.JABABEKA CIKARANG KOTA-CIKARANG UTARA KAB.BEKASI - 17530','2012-06-07 08:37:24','2012-06-07 16:37:00'),(15,'C0115',0,'SAKURA JAVA','KAWASAN EJIP PLOT 5E SUKARESMI CIKARANG SELATAN BEKASI JAWA BARAT-17550','2012-06-07 08:37:24','2012-06-07 16:37:00'),(16,'C0116',0,'NAMICOH','EJIP INDUSTRIAL PARK PLOT 6B-1 CIBATU-CIKARANG SELATAN-KAB.BEKASI - 00000','2012-06-07 08:37:24','2012-06-07 16:37:00'),(17,'C0117',0,'KOHWA PRECISION INDONESIA','JL. MALIGI VIII LOT S-2 KAWASAN KIIC RT.00 RW.00 TELUKJAMBE BARAT KARAWANG JAWA BARAT','2012-06-07 08:37:24','2012-06-07 16:37:00'),(18,'C0118',0,'KOMPONEN FUTABA NUSAPERSADA','JL. PANGKALAN V ( NAROGONG KM. 14 ) DESA CIKIWUL BANTAR GEBANG','2012-06-07 08:37:24','2012-06-07 16:37:00'),(19,'C0119',0,'IONUDA','JL. K.H. MUKMIN RT02 RW02 KEBOANSIKEP GEDANGAN SIDOARJO JAWA TIMUR 61254','2012-06-07 08:37:24','2012-06-07 16:37:00'),(20,'C0120',0,'PT. CITRA NUGERAH KARYA','JL. JATI RAYA BLOK J3 NO.6 NEWTON TECHNO PARK LIPPO CIKARANG BEKASI 17550 BEKASI 17550','2012-06-07 08:37:24','2012-06-07 16:37:00'),(21,'C0121',0,'YUTAKA MANUFACTURING INDONESIA','MM2100 - INDUSTRIAL TOWN JL. SULAWESI I BLOCK H-4 CIKARANG BARAT BEKASI 17520','2012-06-07 08:37:24','2012-06-07 16:37:00'),(22,'C0122',0,'PT. HORAS MIDUK','JL. LINTAS CIBARUSAH PASIRANDU NO. 17 RT.04/02 PASIRSARI KEC. SERANG BARU KAB. BEKASI','2012-06-07 08:37:24','2012-06-07 16:37:00'),(23,'C0123',0,'TENRYU SAW INDONESIA','KOMP PESONA BUKIT BINTARO A.3 RT 01 RW 07 SAWAH BARU CIPUTAT','2012-06-07 08:37:24','2012-06-07 16:37:00'),(24,'C0124',0,'CANTEEN','JL. PONDOK LELE - BABAKAN SEREH RT03/03 DESA DAWUAN BARAT CIKAMPEK 41373','2012-06-07 08:37:24','2012-06-07 16:37:00'),(25,'C0125',0,'PT. SANKYU INDONESIA INTERNATIONAL','SUMMITMAS I LT. 5 JLN. JEND SUDIRMAN KAV 61-62 JAKARTA 12190','2012-06-07 08:37:24','2012-06-07 16:37:00'),(26,'C0126',0,'PT. MSK ENGINEERING INDONESIA (OR)','','2012-06-07 08:37:24','2012-06-07 16:37:00'),(27,'C0127',0,'PT. MSK ENGINEERING INDONESIA','EJIP INDUSTRIAL PARK PLOT 5F 1B1 SUKARESMI CIKARANG SELATAN BEKASI BEKASI','2012-06-07 08:37:24','2012-06-07 16:37:00'),(28,'C0128',0,'PT. NUSA TOYOTETSU CORP','MM2100 INDUSTRIAL TOWN BLOCK J-12','2012-06-07 08:37:24','2012-06-07 16:37:00'),(29,'C0129',0,'PT. AUTOTECH INDONESIA','KOTA BUKIT INDAH BLOK D-III NO 2','2012-06-07 08:37:24','2012-06-07 16:37:00'),(30,'C0130',0,'GEMALA KEMPA DAYA','JL. PEGANGSAAN II BLOK A1 KM 1.6','2012-06-07 08:37:24','2012-06-07 16:37:00'),(31,'C0131',0,'GARUDA METAL UTAMA','JL. INDUSTRI RAYA III BLOK AE NO 23 JATAKE-TANGERANG','2012-06-07 08:37:24','2012-06-07 16:37:00'),(32,'C0132',0,'EPSON','','2012-06-07 08:37:24','2012-06-07 16:37:00'),(33,'C0133',0,'PT. PERKAKAS REKADAYA NUSANTARA','DESA BUNIHAYU JALAN CAGAK-SUBANG','2012-06-07 08:37:24','2012-06-07 16:37:00'),(34,'C0134',0,'PT. AT INDONESIA','JLN. MALIGI H 1-5 KAWASAN INDUSTRI KIIC KARAWANG','2012-06-07 08:37:24','2012-06-07 16:37:00'),(35,'c0135',0,'PT.FUJI TECHNICA INDONESIA','JL. MALIGI II LOT A NO 7 KIIC KARAWANG','2012-06-07 08:37:24','2012-06-07 16:37:00'),(36,'C0136',0,'PT. INDOKARLO PERKASA','JL. RAYA JAKARTA-BOGOR KM47','2012-06-07 08:37:24','2012-06-07 16:37:00'),(37,'C0137',0,'PT. DAYTONA AZIA','JLN. FLORES III BLOK C3-3 KAWASAN MM2100','2012-06-07 08:37:24','2012-06-07 16:37:00'),(38,'C0198',0,'ONE TIME CUSTOMER (OR)','','2012-06-07 08:37:24','2012-06-07 16:37:00'),(39,'C0199',0,'ONE TIME CUSTOMER','','2012-06-07 08:37:24','2012-06-07 16:37:00'),(40,'C0201',0,'SHOWA AUTO PARTS VIETNAM CO. LTD','VIET HUNG DONG ANH HANOI VIETNAM HANOI VIETNAM','2012-06-07 08:37:24','2012-06-07 16:37:00'),(41,'C0202',0,'NOK INDONESIA','KAWASAN INDUSTRI MM2100 BLOK F 3 CIKARANG BARAT BEKASI JAWA BARAT - 17841','2012-06-07 08:37:24','2012-06-07 16:37:00'),(42,'C0203',1,'KOWA SEIMITSU','JAPAN','2012-06-07 08:37:24','2012-06-07 16:37:00'),(43,'C0204',1,'TOKAI RUBBER INDONESIA CO. LTD','JAPAN','2012-06-07 08:37:24','2012-06-07 16:37:00'),(44,'C0205',1,'NIPPON STEEL MATERIALS CO.LTD','4-14-1 SOTOKANDA CHIYODA-KU TOKYO CHIYODA-KU TOKYO','2012-06-07 08:37:24','2012-06-07 16:37:00'),(45,'C0206',1,'CATALER CORPORATION','7800 CHIHAMA KAKEGAWA-CITYJAPAN','2012-06-07 08:37:24','2012-06-07 16:37:00'),(46,'C0207',1,'SAKURA KOGYO','JAPAN','2012-06-07 08:37:24','2012-06-07 16:37:00');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `original` varchar(255) NOT NULL,
  `jpn` varchar(255) NOT NULL,
  `ind` varchar(255) NOT NULL,
  `eng` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (2,'Login','ログイン','Masuk','Login'),(4,'Home','ホーム','Beranda','Home'),(5,'Users','ユーザ','Pemakai','Users'),(6,'Conditional Orders','作業支持書','Orders','Orders'),(7,'Items','アイテム','Items','Items'),(8,'Usages','使用状況','Pemakaian','Usages'),(9,'Japanese','日本語','Bahasa Jepang','Japanese'),(10,'Indonesian','インドネシア語','Bahasa Indonesia','Indonesian'),(11,'English','英語','Bahasa Inggris','English'),(12,'Add','登録・追加','Daftar','Add new'),(13,'Edit','編集','Sunting','Edit'),(14,'View','詳細','Lihat','View'),(15,'Delete','削除','Hapus','Delete'),(16,'Decreases','サイズ変更','Perubahan Ukuran','Decreases'),(17,'Maintenances','修理状況','Reparasi','Maintenances'),(18,'Languages','翻訳設定','Bahasa','Languages'),(19,'Logout','ログアウト','Keluar','Logout'),(20,'Repair','修理','Reparasi','Repair'),(22,'Usage','貸出','Peminjaman','Usage'),(23,'Consumables','消耗品','Barang Konsumsi','Consumables'),(24,'Repairs','修理','Reparasi','Repair'),(25,'Use','使用','Pakai','Use'),(27,'Size Change','サイズ変更','Rubah Ukuran','Size Change'),(28,'Authors','管理者','Authors','Authors'),(29,'Administrative','各種設定','Administrative','Administrative'),(32,'Created','作成日付','Dibuat','Created'),(35,'Updated','更新日付','Updated','Updated'),(36,'Status Asset','資産ステータス','Status Asset','Asset Status'),(37,'Add new author','新規管理者を追加','Tambah pengelola baru','Add new author'),(38,'List Languages','翻訳言語一覧','List Terjemahan','List Languages'),(41,'Time End','終了時間','Berakhir','Time End'),(42,'Time Start','開始時間','Berawal','Time Start'),(43,'Date','日付','Tanggal','Date'),(44,'Title','タイトル','Title','Title'),(45,'are you sure you want to delete','次のアイテムを削除してもいいですか','Bener nih mau menghapus','Are you sure you want to delete'),(46,'Add new user','新規ユーザを追加','Tambah user baru','Add new user'),(47,'Code','コード','Kode','Code'),(48,'Name','氏名','Nama','Name'),(49,'Gender','性別','Jenis kelamin','Sex'),(50,'Email','Eメール','Email','Email'),(51,'Department','部署','Departemen','Department'),(52,'Section','セクション','Seksi','Section'),(53,'Group','グループ','Group','Group'),(54,'Group Sub','サブグループ','Sub Group','Sub Group'),(55,'Name Login','ログイン名','Nama Login','Login Name'),(56,'Password','パスワード','Password','Password'),(57,'Warehouses','ウェアハウス','Warehouses','Warehouses'),(58,'Users','ユーザ','Users','Users'),(59,'Genders','性別','Jenis Kelamin','Genders'),(60,'Category','カテゴリー','Kategori','Category'),(61,'Type','タイプ','Type','Type'),(62,'Authorizations','管理者','Pengelola','Authors'),(63,'Availability','有無','Keberadaan','Availability'),(64,'Rack','ラック','Rak','Rack'),(65,'Size','サイズ','Ukuran','Size'),(66,'List','リスト','List','List'),(67,'Related','関係する','Tentang','Related'),(68,'Size Changes','サイズ変更','Perubahan Ukuran','Size Changes'),(69,'Item','プラグ・ダイズ','Plug/Dies','Plug/Dies'),(70,'Warehouse','ウェアハウス','Warehouse','Warehouse'),(71,'Categories','カテゴリー','Kategori','Category'),(72,'Status Assets','資産ステータス','Status Asset','Asset Statuses'),(73,'Types','タイプ','Type','Type'),(74,'Availabilities','有無','Keberadaan','Availabilities'),(75,'Racks','ラック','Rack','Rack'),(76,'Available','有り','Ada','Available'),(77,'Unit Price','単価','Harga/Unit','Unit Price'),(78,'Sheet','シート','Lembar','Sheet'),(79,'Data All','全データ','Data Semua','Data All'),(80,'Reports','報告集','Laporan','Reports'),(81,'Purch Number','購買番号','Nomor Purch','Purch Number'),(82,'Receive Date','登録日付','Tanggal Penerimaan','Receive Date'),(83,'Asset Status','資産種類','Jenis Asset','Asset Type'),(84,'Dispose Date','償却日付','Tanggal Pembuangan','Dispose Date'),(85,'Remark','備考','Keterangan','Remark'),(86,'Rack Number','ラック番号','Nomor Rack','Rack Number'),(87,'Rack Side','ラックサイド','Rack Side','Rack Side'),(88,'Rack Row','列番号','Nomor Baris','Rack Row'),(89,'Rack Cell','セル番号','Nomor Cell','Cell Number'),(90,'Data Expensed','経費化データ','Data Terbiayakan','Data Expensed'),(91,'Data Per Rack','ラックごと棚卸データ','Data Setiap Rack','Data Per Rak'),(92,'Data Available','各ラックの棚卸データ　（有り）','Data Tersedia','Data Available'),(93,'Stock Taking Data Holded','各ラックの棚卸データ　（保留）','Data ST tiap Rack (Holded)','Stock Taking Data (Holded)'),(94,'Data Disposed','償却データ','Data Terbuang','Dispose Data'),(95,'Report By Item','アイテム報告','Laporan Per Item','Report Per Item'),(96,'Daily Drawing','日常造管','Penarikan Harian','Daily Drawing'),(97,'Aiming','目安','Bearing','Aiming'),(98,'Size Current','現在サイズ','Ukuran Akhir','Size Current'),(99,'Size Origin','元サイズ','Ukuran Awal','Original Size'),(100,'Usage Status','使用ステータス','Status Pemakaian','Usage Status'),(101,'Operator User','作業員','Operator Produksi','Production Operator'),(102,'Issue User','プラグ・ダイズ担当者','Petugas Plug/Dies Room','Plug/Dies Room PIC'),(103,'Date Issue','イッシュ日付','Tanggal Pengeluaran','Issue Date'),(104,'In Use','使用中','Dalam Pemakaian','In Use'),(105,'Returned','返却済','Telah dikembalikan','Returned');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `target` varchar(50) DEFAULT '_self',
  `position` int(11) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_position` (`position`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (1,NULL,'Dashboard','fa-home','/','_self',1,1,NULL,NULL),(2,NULL,'Inventory','fa-boxes','#','_self',2,1,NULL,NULL),(3,NULL,'Personnel','fa-users','#','_self',300,1,NULL,'2025-11-11 07:55:04'),(4,NULL,'Maintenance','fa-tools','#','_self',400,1,NULL,'2025-11-11 07:55:04'),(5,NULL,'Vehicles','fa-truck','#','_self',500,1,NULL,'2025-11-11 07:55:04'),(6,NULL,'Reports','fa-chart-bar','#','_self',6,1,NULL,NULL),(7,NULL,'Settings','fa-cog','#','_self',7,1,NULL,NULL),(8,2,'Inventories','fa-list','/inventories','_self',1,1,NULL,NULL),(9,2,'Storages','fa-warehouse','/storages','_self',2,1,NULL,NULL),(10,2,'Stock Incoming','fa-arrow-down','/stock-incomings','_self',3,1,NULL,NULL),(11,2,'Stock Outgoing','fa-arrow-up','/stock-outgoings','_self',4,1,NULL,NULL),(12,2,'Stock Take','fa-clipboard-check','/stock-takes','_self',5,1,NULL,NULL),(13,2,'Adjust Stocks','fa-balance-scale','/adjust-stocks','_self',6,1,NULL,NULL),(14,2,'Suppliers','fa-truck','/suppliers','_self',7,1,NULL,NULL),(15,3,'Personnels','fa-id-badge','/personnels','_self',40,1,NULL,'2025-11-11 07:55:04'),(16,3,'Companies','fa-building','/companies','_self',10,1,NULL,'2025-11-11 07:55:04'),(17,3,'Departments','fa-sitemap','/departments','_self',20,1,NULL,'2025-11-11 07:55:04'),(18,3,'Positions','fa-user-tie','/positions','_self',30,1,NULL,'2025-11-11 07:55:04'),(19,3,'Employee Status','fa-user-check','/employee-statuses','_self',5,1,NULL,NULL),(20,4,'Planned Jobs','fa-calendar-check','/planned-jobs','_self',20,1,NULL,'2025-11-11 07:55:04'),(21,4,'Maintenance Cards','fa-clipboard-list','/maintenance-cards','_self',10,1,NULL,'2025-11-11 07:55:04'),(22,4,'Daily Activities','fa-tasks','/daily-activities','_self',30,1,NULL,'2025-11-11 07:55:04'),(23,4,'Action Plans','fa-clipboard-list','/action-plans','_self',4,1,NULL,NULL),(24,4,'Insurance Claims','fa-file-contract','/insurance-claims','_self',5,1,NULL,NULL),(25,5,'Vehicles','fa-car','/vehicles','_self',1,1,NULL,NULL),(26,5,'Drivers','fa-id-card','/drivers','_self',30,1,NULL,'2025-11-11 07:55:04'),(27,5,'Vehicle Types','fa-list','/vehicle-types','_self',20,1,NULL,'2025-11-11 07:55:04'),(28,5,'Lesor Companies','fa-handshake','/lesor-companies','_self',40,1,NULL,'2025-11-11 07:55:04'),(29,6,'Inventory Report','fa-file-alt','/reports/inventory','_self',1,1,NULL,NULL),(30,6,'Personnel Report','fa-file-alt','/reports/personnel','_self',2,1,NULL,NULL),(31,6,'Maintenance Report','fa-file-alt','/reports/maintenance','_self',3,1,NULL,NULL),(32,6,'Vehicle Report','fa-file-alt','/reports/vehicle','_self',4,1,NULL,NULL),(33,7,'Users','fa-users-cog','/users','_self',1,1,NULL,NULL),(34,7,'Roles','fa-user-shield','/roles','_self',2,1,NULL,NULL),(35,7,'Configurations','fa-sliders-h','/settings','_self',3,1,NULL,NULL),(36,NULL,'Accounting','fa-calculator','#','_self',200,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(37,NULL,'Approvals','fa-check-circle','#','_self',600,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(38,NULL,'Master Data','fa-database','#','_self',700,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(39,NULL,'System','fa-cog','#','_self',900,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(40,36,'Chart of Accounts','fa-list','/chart-of-accounts','_self',10,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(41,36,'Counters','fa-sort-numeric-up','/counters','_self',20,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(42,36,'Payment Methods','fa-credit-card','/payment-methods','_self',30,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(43,3,'Sections','fa-object-group','/sections','_self',50,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(44,3,'Shift Groups','fa-clock','/shift-groups','_self',60,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(45,4,'Actions','fa-wrench','/actions','_self',40,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(46,4,'Safety Cards','fa-shield-alt','/safety-cards','_self',50,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(47,5,'Vehicle List','fa-car','/vehicles','_self',10,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(48,37,'Approval List','fa-clipboard-check','/approvals','_self',10,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(49,37,'Approval Matrix','fa-table','/approval-matrix','_self',20,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(50,37,'Approval Workflows','fa-stream','/approval-workflows','_self',30,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(51,38,'Provinces','fa-map-marked','/propinsis','_self',10,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(52,38,'Cities','fa-city','/kabupatens','_self',20,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(53,38,'Districts','fa-map-pin','/kecamatans','_self',30,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(54,38,'Villages','fa-home','/kelurahans','_self',40,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(55,38,'Languages','fa-language','/languages','_self',50,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(56,39,'Menu Management','fa-bars','/menus','_self',10,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(57,39,'Users','fa-user','/users','_self',20,1,'2025-11-11 07:55:04','2025-11-11 07:55:04'),(58,39,'Authorizations','fa-key','/authorizations','_self',30,1,'2025-11-11 07:55:04','2025-11-11 07:55:04');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'asahi_commons'
--

--
-- Dumping routines for database 'asahi_commons'
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
