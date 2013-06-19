-- MySQL dump 10.13  Distrib 5.6.10, for Win64 (x86_64)
--
-- Host: localhost    Database: farmer
-- ------------------------------------------------------
-- Server version	5.6.10-log

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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `cat_id` varchar(6) NOT NULL,
  `cat_name` varchar(50) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES ('CT001','Seafood'),('CT002','Produce'),('CT003','Dairy & Alternatives'),('CT004','Beer & Wine'),('CT005','Bakery');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `item_id` varchar(6) NOT NULL DEFAULT '',
  `cat_id` varchar(6) NOT NULL,
  `shop_id` varchar(6) NOT NULL,
  `item_name` varchar(30) DEFAULT NULL,
  `item_price` decimal(4,2) DEFAULT NULL,
  `item_desc` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`cat_id`,`item_id`,`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES ('IT0001','CT001','SP001','Fresh Wild Corvina',6.00,'iPad with retina display'),('IT0002','CT001','SP001','Wild Chilean Sea Bass',15.00,'iPhone5'),('IT0003','CT001','SP001','Fresh Salmon',6.00,'iPad mini'),('IT0006','CT002','SP002','White Sweet Corn',2.00,'AT&T U-Verse'),('IT0007','CT002','SP002','Tropical Red Mangoes',1.00,'DIRECTV'),('IT0008','CT002','SP002','Russet Potatoes',1.00,'Nation & Family Talk'),('IT0009','CT002','SP002','Radish',1.00,'Bluetooth /Hands Free'),('IT0010','CT003','SP004','Greek Yogurt',5.00,'BodyLotion'),('IT0011','CT003','SP005','Orange Juice',6.00,'Body Cream'),('IT0014','CT004','SP003','Newcastle Beer',11.00,'Baby  Bags'),('IT0015','CT004','SP003','Red Diamond',6.00,'Shoulder Bags'),('IT0016','CT004','SP003','Mendocino Beer',7.00,'Totes'),('IT0017','CT004','SP003','Castle Rock Wines',7.00,'Satchels'),('IT0033','CT005','SP005','Artisan Bread',3.00,'Shirts & Blouses'),('IT0034','CT005','SP005','Family Bread',3.00,'Sweaters'),('IT0035','CT005','SP006','Sandwich Bread',7.00,'Jeans'),('IT0036','CT005','SP005','Freshly baked Strudel',78.00,'Sleepwear');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `order_id` varchar(6) NOT NULL DEFAULT '',
  `item_id` varchar(6) NOT NULL DEFAULT '',
  `shop_id` varchar(6) NOT NULL DEFAULT '',
  `qty` int(11) DEFAULT NULL,
  `orderedat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`,`item_id`,`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES ('oi4uaj','IT0010','SP004',1,'2013-06-19 08:39:15');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `payment_id` varchar(6) NOT NULL DEFAULT '',
  `order_id` varchar(6) NOT NULL DEFAULT '',
  `shop_id` varchar(6) NOT NULL,
  `amount` double NOT NULL,
  `status` varchar(10) DEFAULT NULL,
  `paidat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`,`order_id`,`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shops`
--

DROP TABLE IF EXISTS `shops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shops` (
  `shop_id` varchar(6) NOT NULL,
  `shop_name` varchar(100) NOT NULL,
  `shop_pp_email` varchar(100) NOT NULL,
  `shop_pp_api_uname` varchar(50) NOT NULL,
  `shop_pp_api_passwd` varchar(30) NOT NULL,
  `shop_pp_api_sign` varchar(100) NOT NULL,
  `shop_loc` varchar(20) NOT NULL,
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shops`
--

LOCK TABLES `shops` WRITE;
/*!40000 ALTER TABLE `shops` DISABLE KEYS */;
INSERT INTO `shops` VALUES ('SP001','Seafood Store','mer102_1362122176_biz@gmail.com','mer102_1362122176_biz_api1.gmail.com','1362122231','AjsbJc8Z7bMatF-pWQPqoJdMdJ4AAf70D6iap-C787dthlUmSAbwoCv-','Valley Fair'),('SP002','Simon Grocery','mer102_1362122176_biz@gmail.com','mer102_1362122176_biz_api1.gmail.com','1362122231','AjsbJc8Z7bMatF-pWQPqoJdMdJ4AAf70D6iap-C787dthlUmSAbwoCv-','Valley Fair'),('SP003','Wyken vineyard','mer102_1362122176_biz@gmail.com','mer102_1362122176_biz_api1.gmail.com','1362122231','AjsbJc8Z7bMatF-pWQPqoJdMdJ4AAf70D6iap-C787dthlUmSAbwoCv-','Valley Fair'),('SP004','Chobani Dairy','mer102_1362122176_biz@gmail.com','mer102_1362122176_biz_api1.gmail.com','1362122231','AjsbJc8Z7bMatF-pWQPqoJdMdJ4AAf70D6iap-C787dthlUmSAbwoCv-','Valley Fair'),('SP005','Sprouts','mer102_1362122176_biz@gmail.com','mer102_1362122176_biz_api1.gmail.com','1362122231','AjsbJc8Z7bMatF-pWQPqoJdMdJ4AAf70D6iap-C787dthlUmSAbwoCv-','Valley Fair'),('SP006','Rudis','mer102_1362122176_biz@gmail.com','mer102_1362122176_biz_api1.gmail.com','1362122231','AjsbJc8Z7bMatF-pWQPqoJdMdJ4AAf70D6iap-C787dthlUmSAbwoCv-','Valley Fair');
/*!40000 ALTER TABLE `shops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voicepay_codes`
--

DROP TABLE IF EXISTS `voicepay_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voicepay_codes` (
  `order_id` varchar(6) NOT NULL,
  `code` varchar(4) NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voicepay_codes`
--

LOCK TABLES `voicepay_codes` WRITE;
/*!40000 ALTER TABLE `voicepay_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `voicepay_codes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-06-19 14:38:23
