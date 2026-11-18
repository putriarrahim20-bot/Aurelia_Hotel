-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: dbhotel
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `adminh`
--

DROP TABLE IF EXISTS `adminh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminh` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nm_admin` varchar(100) NOT NULL,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adminh`
--

LOCK TABLES `adminh` WRITE;
/*!40000 ALTER TABLE `adminh` DISABLE KEYS */;
INSERT INTO `adminh` VALUES (1,'admin','$2y$10$1s81RtdQI21ItoCqfLRlW.0/LL.Dj88tm7mvSv8lUGQkSnZOlgD1G','Administrator');
/*!40000 ALTER TABLE `adminh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kamar`
--

DROP TABLE IF EXISTS `kamar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kamar` (
  `idkamar` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_kamar` varchar(10) NOT NULL,
  `idtipe` int(11) NOT NULL,
  `status` enum('Aktif','Perbaikan') NOT NULL DEFAULT 'Aktif',
  PRIMARY KEY (`idkamar`),
  UNIQUE KEY `nomor_kamar` (`nomor_kamar`),
  KEY `idtipe` (`idtipe`),
  CONSTRAINT `kamar_ibfk_1` FOREIGN KEY (`idtipe`) REFERENCES `tipe_kamar` (`idtipe`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kamar`
--

LOCK TABLES `kamar` WRITE;
/*!40000 ALTER TABLE `kamar` DISABLE KEYS */;
INSERT INTO `kamar` VALUES (1,'101',1,'Aktif'),(2,'102',1,'Aktif'),(3,'103',1,'Aktif'),(4,'201',2,'Aktif'),(5,'202',2,'Aktif'),(6,'301',3,'Aktif'),(7,'401',4,'Aktif'),(8,'402',4,'Aktif');
/*!40000 ALTER TABLE `kamar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pembayaran` (
  `idbayar` int(11) NOT NULL AUTO_INCREMENT,
  `idreservasi` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `metode` enum('Tunai','Transfer','QRIS') NOT NULL,
  `keterangan` varchar(100) NOT NULL DEFAULT '',
  `tgl_bayar` datetime NOT NULL,
  PRIMARY KEY (`idbayar`),
  KEY `idreservasi` (`idreservasi`),
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`idreservasi`) REFERENCES `reservasi` (`idreservasi`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembayaran`
--

LOCK TABLES `pembayaran` WRITE;
/*!40000 ALTER TABLE `pembayaran` DISABLE KEYS */;
INSERT INTO `pembayaran` VALUES (1,1,105000,'QRIS','pelunasan','2026-11-16 14:20:08'),(2,1,245000,'Tunai','','2026-11-16 14:20:17');
/*!40000 ALTER TABLE `pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservasi`
--

DROP TABLE IF EXISTS `reservasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservasi` (
  `idreservasi` int(11) NOT NULL AUTO_INCREMENT,
  `kode_booking` char(8) NOT NULL,
  `nama_tamu` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `no_hp` varchar(20) NOT NULL,
  `idkamar` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `jumlah_tamu` int(11) NOT NULL,
  `malam` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `status` enum('Menunggu','Dikonfirmasi','Check-in','Check-out','Batal') NOT NULL DEFAULT 'Menunggu',
  `catatan` varchar(255) NOT NULL DEFAULT '',
  `tgl_pesan` datetime NOT NULL,
  `checkin_at` datetime DEFAULT NULL,
  `checkout_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idreservasi`),
  UNIQUE KEY `kode_booking` (`kode_booking`),
  KEY `idx_kamar_tanggal` (`idkamar`,`check_in`,`check_out`),
  CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`idkamar`) REFERENCES `kamar` (`idkamar`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservasi`
--

LOCK TABLES `reservasi` WRITE;
/*!40000 ALTER TABLE `reservasi` DISABLE KEYS */;
INSERT INTO `reservasi` VALUES (1,'UUJ3DB8N','Azzahra','putriarrahim20@gmail.com','089681760114',2,'2026-11-17','2026-11-18',2,1,350000,'Check-in','','2026-11-16 14:17:59','2026-11-17 19:09:37',NULL),(2,'56ZJBZHJ','Azzahra','putriarrahim20@gmail.com','089681760114',3,'2026-11-17','2026-11-18',2,1,350000,'Menunggu','','2026-11-17 19:40:16',NULL,NULL);
/*!40000 ALTER TABLE `reservasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipe_kamar`
--

DROP TABLE IF EXISTS `tipe_kamar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipe_kamar` (
  `idtipe` int(11) NOT NULL AUTO_INCREMENT,
  `nama_tipe` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_malam` int(11) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `foto` varchar(255) NOT NULL DEFAULT '-',
  PRIMARY KEY (`idtipe`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipe_kamar`
--

LOCK TABLES `tipe_kamar` WRITE;
/*!40000 ALTER TABLE `tipe_kamar` DISABLE KEYS */;
INSERT INTO `tipe_kamar` VALUES (1,'Royal Oceanfront Suite','Suite mewah seluas 85 m² di tepi pantai dengan panorama laut lepas, 1 King Bed, private jacuzzi teras, sarapan gourmet & akses Executive Lounge.',4850000,2,'pilihan_img_3.jpg'),(2,'Presidential Cliffside Villa','Vila privat eksklusif 180 m² di tebing tinggi dengan kolam renang infinity pribadi, 2 King Bed, pelayan butler 24 jam, dan pemandangan sunset spektakuler.',8900000,4,'pilihan_img_4.jpg'),(3,'Premier Sunset Suite','Kamar elegan seluas 65 m² bernuansa kayu jati minimalis dengan balkon matahari terbenam pribadi, bathtub marmer mewah, dan sarapan buffet.',3850000,2,'pilihan_img_5.jpg'),(4,'Grand Horizon Estate Villa','Kediaman resor termegah 320 m² dengan paviliun terbuka, kolam renang pribadi ganda, taman tropis, dapur pribadi, dan akses VIP.',14500000,6,'pilihan_img_6.jpg');
/*!40000 ALTER TABLE `tipe_kamar` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-18 12:00:06
