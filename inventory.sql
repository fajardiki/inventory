/*
SQLyog Ultimate v11.5 (64 bit)
MySQL - 10.4.6-MariaDB : Database - inventory
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`inventory` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `inventory`;

/*Table structure for table `barang` */

DROP TABLE IF EXISTS `barang`;

CREATE TABLE `barang` (
  `kode_brg` char(20) COLLATE utf8_bin NOT NULL,
  `nama_brg` varchar(100) COLLATE utf8_bin NOT NULL,
  `ukuran` varchar(50) COLLATE utf8_bin NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `stok_ambang` int(11) NOT NULL,
  `kode_rak` varchar(10) COLLATE utf8_bin NOT NULL,
  `gambar` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`kode_brg`),
  UNIQUE KEY `nama_brg` (`nama_brg`),
  KEY `kode_rak` (`kode_rak`),
  CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`kode_rak`) REFERENCES `rak` (`kode_rak`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `barang` */

insert  into `barang`(`kode_brg`,`nama_brg`,`ukuran`,`harga`,`stok`,`stok_ambang`,`kode_rak`,`gambar`) values ('AB002','Box Styrofoam Tuna','37x23x16cm',15000,0,15,'RA1','Figure_1.png'),('BRG-1','Barang Test !','2x2',15000,0,10,'RA1','flutter.png'),('BX001','Box Styrofoam 2KG','28,5x15,5x16,5 cm',12000,0,20,'RA1','Figure_1.png'),('ICE450','Ice Gel Thermapack 450gr','-',13500,0,5,'RB1','461264-reactJS-Facebook-JavaScript-minimalism-artwork-simple_background-748x421.jpg'),('test_01','test','20X20',12000,0,10,'1','wp3082278-github-wallpapers.jpg');

/*Table structure for table `pembelian` */

DROP TABLE IF EXISTS `pembelian`;

CREATE TABLE `pembelian` (
  `no_faktur` varchar(50) COLLATE utf8_bin NOT NULL,
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `tgl_transaksi` date NOT NULL,
  `total` bigint(20) NOT NULL,
  PRIMARY KEY (`no_faktur`),
  KEY `username` (`username`),
  CONSTRAINT `pembelian_ibfk_1` FOREIGN KEY (`username`) REFERENCES `pengguna` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `pembelian` */

insert  into `pembelian`(`no_faktur`,`username`,`tgl_transaksi`,`total`) values ('PT123456789','admin1','2022-11-16',135000),('PT80802818031','admin1','2022-12-01',2400000),('TEST1','admin1','0000-00-00',600000),('TEST171222','admin1','2022-12-17',1290000),('TEST181222','admin1','2022-12-18',1303500),('TEST71222','admin1','2022-12-07',150000);

/*Table structure for table `pembelian_barang` */

DROP TABLE IF EXISTS `pembelian_barang`;

CREATE TABLE `pembelian_barang` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `no_faktur` varchar(50) COLLATE utf8_bin NOT NULL,
  `kode_brg` char(20) COLLATE utf8_bin NOT NULL,
  `kode_sup` char(10) COLLATE utf8_bin NOT NULL,
  `jumlah` int(11) NOT NULL,
  `total` bigint(20) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `no_faktur` (`no_faktur`),
  KEY `kode_brg` (`kode_brg`),
  KEY `kode_sup` (`kode_sup`),
  CONSTRAINT `pembelian_barang_ibfk_1` FOREIGN KEY (`no_faktur`) REFERENCES `pembelian` (`no_faktur`),
  CONSTRAINT `pembelian_barang_ibfk_2` FOREIGN KEY (`kode_brg`) REFERENCES `barang` (`kode_brg`),
  CONSTRAINT `pembelian_barang_ibfk_3` FOREIGN KEY (`kode_sup`) REFERENCES `supplier` (`kode_sup`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `pembelian_barang` */

insert  into `pembelian_barang`(`id_detail`,`no_faktur`,`kode_brg`,`kode_sup`,`jumlah`,`total`) values (6,'PT80802818031','BX001','SPT001',200,2400000),(8,'PT123456789','ICE450','SPT001',10,135000),(13,'TEST71222','AB002','SPT001',5,75000),(15,'TEST71222','AB002','SPT001',5,75000),(29,'TEST171222','AB002','SPT001',20,300000),(30,'TEST171222','BRG-1','SPT001',20,240000),(31,'TEST171222','BX001','SPT001',20,240000),(32,'TEST171222','ICE450','SPT001',20,270000),(33,'TEST171222','test_01','SPT001',20,240000),(34,'TEST181222','AB002','SPT001',20,300000),(35,'TEST181222','BRG-1','SPT001',20,240000),(36,'TEST181222','BX001','SPT001',20,240000),(37,'TEST181222','ICE450','SPT001',21,283500),(38,'TEST181222','test_01','SPT001',20,240000),(40,'TEST1','AB002','SPT001',40,600000);

/*Table structure for table `pengguna` */

DROP TABLE IF EXISTS `pengguna`;

CREATE TABLE `pengguna` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `password` varchar(64) COLLATE utf8_bin NOT NULL,
  `jenis` enum('admin','pemilik') COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `pengguna` */

insert  into `pengguna`(`username`,`password`,`jenis`) values ('admin1','$2y$10$pCftCttKX.tpDSYyE7W4T.EagkyjXhLmlbMlcSM2bRlYsBmHzWLnC','admin'),('pemilik1','$2y$10$pCftCttKX.tpDSYyE7W4T.EagkyjXhLmlbMlcSM2bRlYsBmHzWLnC','pemilik'),('root','$2y$10$5zYomzjfHeoGc/j.smNy.uAJzTSeNcIL0yG0IGUA8VoxN2NZ6rJcC','admin');

/*Table structure for table `penjualan` */

DROP TABLE IF EXISTS `penjualan`;

CREATE TABLE `penjualan` (
  `no_transaksi` varchar(20) COLLATE utf8_bin NOT NULL,
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `tgl_transaksi` date NOT NULL,
  `total` bigint(20) NOT NULL,
  PRIMARY KEY (`no_transaksi`),
  KEY `username` (`username`),
  CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`username`) REFERENCES `pengguna` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `penjualan` */

insert  into `penjualan`(`no_transaksi`,`username`,`tgl_transaksi`,`total`) values ('AB1213242942','admin1','2022-12-01',351000),('TEST171222','admin1','2022-12-17',2400000),('TK20220718DFK9N','admin1','2022-07-18',187500),('hhhlada121314','admin1','2022-12-01',0),('tessss','admin1','2022-12-17',750000),('test','admin1','2022-12-17',0);

/*Table structure for table `penjualan_barang` */

DROP TABLE IF EXISTS `penjualan_barang`;

CREATE TABLE `penjualan_barang` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `no_transaksi` varchar(20) COLLATE utf8_bin NOT NULL,
  `kode_brg` char(20) COLLATE utf8_bin NOT NULL,
  `jumlah` int(11) NOT NULL,
  `total` bigint(20) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `penjualan_barang_ibfk_1` (`no_transaksi`),
  KEY `kode_brg` (`kode_brg`),
  CONSTRAINT `penjualan_barang_ibfk_1` FOREIGN KEY (`no_transaksi`) REFERENCES `penjualan` (`no_transaksi`),
  CONSTRAINT `penjualan_barang_ibfk_2` FOREIGN KEY (`kode_brg`) REFERENCES `barang` (`kode_brg`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `penjualan_barang` */

insert  into `penjualan_barang`(`id_detail`,`no_transaksi`,`kode_brg`,`jumlah`,`total`) values (1,'TK20220718DFK9N','BX001',10,120000),(2,'TK20220718DFK9N','ICE450',5,67500),(10,'AB1213242942','AB002',7,105000),(13,'AB1213242942','ICE450',6,81000),(19,'AB1213242942','BX001',10,120000),(20,'AB1213242942','AB002',3,45000),(21,'TEST171222','BX001',180,2160000),(22,'TEST171222','test_01',20,240000),(24,'tessss','AB002',20,300000),(25,'tessss','AB002',30,450000);

/*Table structure for table `rak` */

DROP TABLE IF EXISTS `rak`;

CREATE TABLE `rak` (
  `kode_rak` varchar(10) COLLATE utf8_bin NOT NULL,
  `kapasitas` int(11) NOT NULL,
  PRIMARY KEY (`kode_rak`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `rak` */

insert  into `rak`(`kode_rak`,`kapasitas`) values ('1',-5),('RA1',200),('RB1',250);

/*Table structure for table `stokbarang` */

DROP TABLE IF EXISTS `stokbarang`;

CREATE TABLE `stokbarang` (
  `nomor` int(11) NOT NULL AUTO_INCREMENT,
  `kode_brg` char(20) NOT NULL,
  `no_faktur` varchar(50) DEFAULT NULL,
  `no_transaksi` varchar(20) DEFAULT NULL,
  `nomorstokopname` int(11) DEFAULT NULL,
  `id_detail` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `tgl_transaksi` date DEFAULT NULL,
  PRIMARY KEY (`nomor`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=latin1;

/*Data for the table `stokbarang` */

insert  into `stokbarang`(`nomor`,`kode_brg`,`no_faktur`,`no_transaksi`,`nomorstokopname`,`id_detail`,`jumlah`,`tgl_transaksi`) values (8,'ICE450','PT123456789','',NULL,8,10,'2022-12-18'),(12,'BX001','PT80802818031','',NULL,6,200,'2022-12-18'),(13,'AB002','TEST71222','',NULL,13,5,'2022-12-18'),(14,'AB002','TEST71222','',NULL,15,5,'2022-12-18'),(20,'ICE450','','AB1213242942',NULL,13,-6,'2022-12-18'),(21,'BX001','','AB1213242942',NULL,19,-10,'2022-12-18'),(22,'BX001','','TK20220718DFK9N',NULL,1,-10,'2022-12-18'),(23,'ICE450','','TK20220718DFK9N',NULL,2,-5,'2022-12-18'),(26,'AB002','','AB1213242942',NULL,20,-3,'2022-12-18'),(31,'test_01','TEST171222','',NULL,1,20,'2022-12-18'),(33,'AB002','TEST171222','',NULL,29,20,'2022-12-18'),(34,'BRG-1','TEST171222','',NULL,30,20,'2022-12-18'),(35,'BX001','TEST171222','',NULL,31,20,'2022-12-18'),(36,'test_01','TEST171222','',NULL,33,20,'2022-12-18'),(37,'ICE450','TEST171222','',NULL,32,20,'2022-12-18'),(38,'AB002','TEST181222','',NULL,34,20,'2022-12-18'),(39,'BRG-1','TEST181222','',NULL,35,20,'2022-12-18'),(40,'BX001','TEST181222','',NULL,36,20,'2022-12-18'),(42,'test_01','TEST181222','',NULL,38,20,'2022-12-18'),(43,'BX001','','TEST171222',NULL,21,-180,'2022-12-18'),(44,'test_01','','TEST171222',NULL,22,-20,'2022-12-18'),(45,'ICE450','TEST181222','',NULL,37,21,'2022-12-18'),(51,'AB002','','AB1213242942',NULL,10,-7,'2022-12-18'),(55,'AB002','','tessss',0,24,-20,'2022-12-18'),(56,'AB002','TEST1','',0,40,40,'2022-12-18'),(57,'AB002','','tessss',0,25,-30,'2022-12-18'),(62,'AB002','','',19,0,10,'2022-12-18'),(63,'BRG-1','','',20,0,-20,'2022-12-18'),(64,'BX001','','',21,0,-20,'2022-12-18'),(65,'AB002','','',22,0,-20,'2022-12-18'),(66,'ICE450','','',23,0,-20,'2022-12-18'),(67,'test_01','','',24,0,-20,'2022-12-18'),(68,'AB002','','',25,0,0,'2022-12-18'),(69,'BRG-1','','',26,0,20,'2022-12-18'),(70,'AB002','','',27,0,20,'2022-12-18'),(71,'BX001','','',28,0,20,'2022-12-18'),(72,'test_01','','',29,0,20,'2022-12-18');

/*Table structure for table `stokopname` */

DROP TABLE IF EXISTS `stokopname`;

CREATE TABLE `stokopname` (
  `nomor` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(15) NOT NULL,
  `kode_brg` char(20) NOT NULL,
  `jumlah_stok` int(11) NOT NULL,
  `jumlah_stok_opname` int(11) NOT NULL,
  `jumlah_selisih` int(11) NOT NULL,
  `tgl_transaksi` date DEFAULT NULL,
  PRIMARY KEY (`nomor`),
  UNIQUE KEY `UNIQUE` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

/*Data for the table `stokopname` */

insert  into `stokopname`(`nomor`,`kode`,`kode_brg`,`jumlah_stok`,`jumlah_stok_opname`,`jumlah_selisih`,`tgl_transaksi`) values (19,'OPN/22/12/001','AB002',30,40,10,'2022-12-18'),(20,'OPN/22/12/002','BRG-1',40,20,-20,'2022-12-18'),(21,'OPN/22/12/003','BX001',40,20,-20,'2022-12-18'),(22,'OPN/22/12/004','AB002',40,20,-20,'2022-12-18'),(23,'OPN/22/12/005','ICE450',40,20,-20,'2022-12-18'),(24,'OPN/22/12/006','test_01',40,20,-20,'2022-12-18'),(25,'OPN/22/12/007','AB002',20,20,0,'2022-12-18'),(26,'OPN/22/12/008','BRG-1',20,40,20,'2022-12-18'),(27,'OPN/22/12/009','AB002',20,40,20,'2022-12-18'),(28,'OPN/22/12/010','BX001',20,40,20,'2022-12-18'),(29,'OPN/22/12/011','test_01',20,40,20,'2022-12-18');

/*Table structure for table `supplier` */

DROP TABLE IF EXISTS `supplier`;

CREATE TABLE `supplier` (
  `kode_sup` char(10) COLLATE utf8_bin NOT NULL,
  `nama_sup` varchar(50) COLLATE utf8_bin NOT NULL,
  `alamat_sup` varchar(200) COLLATE utf8_bin NOT NULL,
  `telp_sup` varchar(16) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`kode_sup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `supplier` */

insert  into `supplier`(`kode_sup`,`nama_sup`,`alamat_sup`,`telp_sup`) values ('SPT001','PT Cahaya Qorrindo Sejahtera','Jalan Banjar Wijaya, Ruko Azzores B 17A No 6, \r\nRT.001/RW.003, Poris Plawad Indah, Cipondoh, Tangerang City, Banten 15142','0856-9491-9296'),('SPT002','Panca Cipta Bersama','Jl. Aria Jaya Santika KM 3.5 Pasirnangka - Tigaraksa - Tangerang Banten','0215996583');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
