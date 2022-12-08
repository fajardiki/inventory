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
  `barang` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`kode_brg`),
  UNIQUE KEY `nama_brg` (`nama_brg`),
  KEY `kode_rak` (`kode_rak`),
  CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`kode_rak`) REFERENCES `rak` (`kode_rak`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `barang` */

insert  into `barang`(`kode_brg`,`nama_brg`,`ukuran`,`harga`,`stok`,`stok_ambang`,`kode_rak`,`barang`) values ('AB002','Box Styrofoam Tuna','37x23x16cm',15000,121,15,'RA1',NULL),('BX001','Box Styrofoam 2KG','28,5x15,5x16,5 cm',12000,112,20,'RA1',NULL),('ICE450','Ice Gel Thermapack 450gr','-',13500,20,5,'RB1',NULL);

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

insert  into `pembelian`(`no_faktur`,`username`,`tgl_transaksi`,`total`) values ('PT123456789','admin1','2022-11-16',0),('PT80802818031','admin1','2022-12-01',0),('TEST71222','admin1','2022-12-07',0),('xx1','admin1','2022-01-01',0);

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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `pembelian_barang` */

insert  into `pembelian_barang`(`id_detail`,`no_faktur`,`kode_brg`,`kode_sup`,`jumlah`,`total`) values (6,'PT80802818031','BX001','SPT001',200,0),(8,'PT123456789','ICE450','SPT001',10,0),(13,'TEST71222','AB002','SPT001',5,75000),(15,'TEST71222','AB002','SPT001',5,75000);

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

insert  into `penjualan`(`no_transaksi`,`username`,`tgl_transaksi`,`total`) values ('AB1213242942','admin1','2022-12-01',0),('TK20220718DFK9N','admin1','2022-07-18',0),('hhhlada121314','admin1','2022-12-01',0);

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `penjualan_barang` */

insert  into `penjualan_barang`(`id_detail`,`no_transaksi`,`kode_brg`,`jumlah`,`total`) values (1,'TK20220718DFK9N','BX001',10,0),(2,'TK20220718DFK9N','ICE450',5,0),(10,'AB1213242942','AB002',7,0),(13,'AB1213242942','ICE450',6,0),(14,'TK20220718DFK9N','ICE450',2,0),(15,'TK20220718DFK9N','ICE450',2,0),(16,'AB1213242942','ICE450',4,0);

/*Table structure for table `rak` */

DROP TABLE IF EXISTS `rak`;

CREATE TABLE `rak` (
  `kode_rak` varchar(10) COLLATE utf8_bin NOT NULL,
  `kapasitas` int(11) NOT NULL,
  PRIMARY KEY (`kode_rak`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `rak` */

insert  into `rak`(`kode_rak`,`kapasitas`) values ('1',-5),('RA1',200),('RB1',250);

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
