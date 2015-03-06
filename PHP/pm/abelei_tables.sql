-- MySQL dump 10.13  Distrib 5.1.39, for Win32 (ia32)
--
-- Host: localhost    Database: abelei
-- ------------------------------------------------------
-- Server version	5.1.39-community

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
-- Table structure for table `address_types`
--

DROP TABLE IF EXISTS `address_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address_types` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `batchsheetcustomerinfo`
--

DROP TABLE IF EXISTS `batchsheetcustomerinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batchsheetcustomerinfo` (
  `BatchSheetNumber` int(10) NOT NULL,
  `CustomerOrderNumber` int(10) NOT NULL DEFAULT '0',
  `CustomerOrderSeqNumber` smallint(5) NOT NULL DEFAULT '0',
  `CustomerPONumber` varchar(50) DEFAULT NULL,
  `CustomerCodeNumber` varchar(50) DEFAULT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `PackIn` varchar(30) DEFAULT NULL,
  `PackInID` varchar(100) DEFAULT NULL,
  `NumberOfPackages` int(10) DEFAULT NULL,
  `InventoryTransactionNumber` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`BatchSheetNumber`,`CustomerOrderNumber`,`CustomerOrderSeqNumber`),
  KEY `CustomerOrderNumber` (`CustomerOrderNumber`),
  KEY `CustomerOrderSeqNumber` (`CustomerOrderSeqNumber`),
  KEY `NumberOfPackages` (`NumberOfPackages`),
  KEY `InventoryTransactionNumber` (`InventoryTransactionNumber`),
  KEY `FK_batchsheetcustomerinfo_3` (`PackInID`),
  CONSTRAINT `batchsheetcustomerinfo_ibfk_1` FOREIGN KEY (`BatchSheetNumber`) REFERENCES `batchsheetmaster` (`BatchSheetNumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `batchsheetcustomerinfo_ibfk_2` FOREIGN KEY (`InventoryTransactionNumber`) REFERENCES `inventorymovements` (`TransactionNumber`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `batchsheetdetail`
--

DROP TABLE IF EXISTS `batchsheetdetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batchsheetdetail` (
  `BatchSheetNumber` int(10) NOT NULL,
  `IngredientProductNumber` varchar(12) NOT NULL,
  `IngredientSEQ` double(7,2) NOT NULL,
  `IngredientNumberExternal` varchar(20) DEFAULT NULL,
  `IngredientDesignation` varchar(100) DEFAULT NULL,
  `Intermediary` tinyint(1) NOT NULL,
  `Percentage` double(15,5) DEFAULT NULL,
  `RawMaterialLotNumbers` longtext,
  `SubBatchSheetNumber` int(10) DEFAULT NULL,
  `FEMA_NBR` varchar(15) DEFAULT NULL,
  `VendorID` int(11) unsigned DEFAULT NULL,
  `InventoryTransactionNumber` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`BatchSheetNumber`,`IngredientProductNumber`,`IngredientSEQ`),
  KEY `BatchSheetNumber1` (`SubBatchSheetNumber`),
  KEY `IngredientProductNumber` (`IngredientProductNumber`),
  KEY `IngredientSEQ` (`IngredientSEQ`),
  KEY `ProductNumberExternal` (`IngredientNumberExternal`),
  KEY `VendorID` (`VendorID`),
  KEY `InventoryTransactionNumber` (`InventoryTransactionNumber`),
  CONSTRAINT `batchsheetdetail_ibfk_14` FOREIGN KEY (`InventoryTransactionNumber`) REFERENCES `inventorymovements` (`TransactionNumber`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `batchsheetdetail_ibfk_15` FOREIGN KEY (`IngredientProductNumber`) REFERENCES `productmaster` (`ProductNumberInternal`) ON UPDATE CASCADE,
  CONSTRAINT `batchsheetdetail_ibfk_16` FOREIGN KEY (`VendorID`) REFERENCES `vendors` (`vendor_id`) ON UPDATE CASCADE,
  CONSTRAINT `batchsheetdetail_ibfk_17` FOREIGN KEY (`IngredientNumberExternal`) REFERENCES `externalproductnumberreference` (`ProductNumberExternal`) ON UPDATE CASCADE,
  CONSTRAINT `batchsheetdetail_ibfk_2` FOREIGN KEY (`BatchSheetNumber`) REFERENCES `batchsheetmaster` (`BatchSheetNumber`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `batchsheetdetaillotnumbers`
--

DROP TABLE IF EXISTS `batchsheetdetaillotnumbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batchsheetdetaillotnumbers` (
  `RecordID` int(10) NOT NULL AUTO_INCREMENT,
  `BatchSheetNumber` int(10) DEFAULT NULL,
  `IngredientProductNumber` varchar(12) DEFAULT NULL,
  `IngredientSEQ` double(7,2) DEFAULT NULL,
  `LotID` int(11) unsigned DEFAULT NULL,
  `InventoryMovementTransactionNumber` int(11) unsigned DEFAULT NULL,
  `QuantityUsedFromThisLot` double(15,5) DEFAULT NULL,
  PRIMARY KEY (`RecordID`),
  UNIQUE KEY `InventoryMovementTransactionNumber` (`InventoryMovementTransactionNumber`),
  KEY `BatchSheetNumber` (`BatchSheetNumber`),
  KEY `IngredientProductNumber` (`IngredientProductNumber`),
  KEY `LotID` (`LotID`),
  CONSTRAINT `batchsheetdetaillotnumbers_ibfk_11` FOREIGN KEY (`BatchSheetNumber`) REFERENCES `batchsheetmaster` (`BatchSheetNumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `batchsheetdetaillotnumbers_ibfk_19` FOREIGN KEY (`IngredientProductNumber`) REFERENCES `productmaster` (`ProductNumberInternal`) ON UPDATE CASCADE,
  CONSTRAINT `batchsheetdetaillotnumbers_ibfk_20` FOREIGN KEY (`LotID`) REFERENCES `lots` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `batchsheetdetaillotnumbers_ibfk_21` FOREIGN KEY (`InventoryMovementTransactionNumber`) REFERENCES `inventorymovements` (`TransactionNumber`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10563 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `batchsheetdetailpackaginglotnumbers`
--

DROP TABLE IF EXISTS `batchsheetdetailpackaginglotnumbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batchsheetdetailpackaginglotnumbers` (
  `RecordID` int(10) NOT NULL AUTO_INCREMENT,
  `BatchSheetNumber` int(10) DEFAULT NULL,
  `CustomerOrderNumber` int(10) DEFAULT NULL,
  `CustomerOrderSeqNumber` smallint(5) DEFAULT NULL,
  `CustomerPONumber` varchar(50) DEFAULT NULL,
  `PackagingProductNumber` varchar(12) DEFAULT NULL,
  `LotID` int(11) unsigned DEFAULT NULL,
  `InventoryMovementTransactionNumber` int(11) unsigned DEFAULT NULL,
  `QuantityUsedFromThisLot` double(15,5) DEFAULT NULL,
  PRIMARY KEY (`RecordID`),
  UNIQUE KEY `InventoryMovementTransactionNumber` (`InventoryMovementTransactionNumber`),
  KEY `BatchSheetNumber` (`BatchSheetNumber`),
  KEY `CustomerOrderNumber` (`CustomerOrderNumber`),
  KEY `CustomerOrderSeqNumber` (`CustomerOrderSeqNumber`),
  KEY `IngredientProductNumber` (`PackagingProductNumber`),
  KEY `LotID` (`LotID`),
  CONSTRAINT `batchsheetdetailpackaginglotnumbers_ibfk_12` FOREIGN KEY (`PackagingProductNumber`) REFERENCES `productmaster` (`ProductNumberInternal`) ON UPDATE CASCADE,
  CONSTRAINT `batchsheetdetailpackaginglotnumbers_ibfk_13` FOREIGN KEY (`LotID`) REFERENCES `lots` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `batchsheetdetailpackaginglotnumbers_ibfk_14` FOREIGN KEY (`InventoryMovementTransactionNumber`) REFERENCES `inventorymovements` (`TransactionNumber`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `batchsheetdetailpackaginglotnumbers_ibfk_4` FOREIGN KEY (`BatchSheetNumber`) REFERENCES `batchsheetmaster` (`BatchSheetNumber`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1035 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `batchsheetmaster`
--

DROP TABLE IF EXISTS `batchsheetmaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batchsheetmaster` (
  `BatchSheetNumber` int(10) NOT NULL AUTO_INCREMENT,
  `LotID` int(11) unsigned DEFAULT NULL,
  `ProductNumberExternal` varchar(20) DEFAULT NULL,
  `ProductNumberInternal` varchar(20) DEFAULT NULL,
  `ProductDesignation` varchar(100) DEFAULT NULL,
  `CustomerID` int(11) unsigned DEFAULT NULL,
  `DueDate` datetime DEFAULT NULL,
  `NetWeight` double(15,5) DEFAULT NULL,
  `TotalQuantity` double(15,5) DEFAULT NULL,
  `TotalQuantityUnitType` varchar(5) DEFAULT NULL,
  `Column1UnitType` varchar(5) DEFAULT NULL,
  `Column2UnitType` varchar(5) DEFAULT NULL,
  `Filtered` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Allergen` varchar(100) DEFAULT NULL,
  `Kosher` varchar(100) DEFAULT NULL,
  `NumberOfTimesToMake` double(7,2) DEFAULT NULL,
  `MadeBy` varchar(50) DEFAULT NULL,
  `Yield` double(7,3) DEFAULT NULL,
  `Vessel` varchar(30) DEFAULT NULL,
  `Notes` longtext,
  `ScaleNumber` varchar(10) DEFAULT NULL,
  `CommitedToInventory` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Manufactured` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `InventoryMovementTransactionNumber` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`BatchSheetNumber`),
  KEY `CustomerID` (`CustomerID`),
  KEY `NumberOfTimesToMake` (`NumberOfTimesToMake`),
  KEY `ProductNumberExternal` (`ProductNumberExternal`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `LotID` (`LotID`),
  KEY `InventoryMovementTransactionNumber` (`InventoryMovementTransactionNumber`),
  CONSTRAINT `batchsheetmaster_ibfk_1` FOREIGN KEY (`InventoryMovementTransactionNumber`) REFERENCES `inventorymovements` (`TransactionNumber`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `batchsheetmaster_ibfk_10` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`customer_id`) ON UPDATE CASCADE,
  CONSTRAINT `batchsheetmaster_ibfk_2` FOREIGN KEY (`LotID`) REFERENCES `lots` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `batchsheetmaster_ibfk_8` FOREIGN KEY (`ProductNumberExternal`) REFERENCES `externalproductnumberreference` (`ProductNumberExternal`) ON UPDATE CASCADE,
  CONSTRAINT `batchsheetmaster_ibfk_9` FOREIGN KEY (`ProductNumberInternal`) REFERENCES `productmaster` (`ProductNumberInternal`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1909 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bscustomerinfopackins`
--

DROP TABLE IF EXISTS `bscustomerinfopackins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bscustomerinfopackins` (
  `PackInID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PackIn` varchar(30) NOT NULL,
  `NumberOfPackages` int(10) DEFAULT NULL,
  `InventoryMovementTransactionNumber` int(10) unsigned DEFAULT NULL,
  `Description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`PackInID`),
  KEY `FK_bscustomerinfopackins_1` (`InventoryMovementTransactionNumber`),
  CONSTRAINT `FK_bscustomerinfopackins_1` FOREIGN KEY (`InventoryMovementTransactionNumber`) REFERENCES `inventorymovements` (`TransactionNumber`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `change_log`
--

DROP TABLE IF EXISTS `change_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `change_log` (
  `change_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(5) unsigned zerofill NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `field_name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `old_value` mediumtext CHARACTER SET utf8 NOT NULL,
  `new_value` mediumtext CHARACTER SET utf8 NOT NULL,
  `time_stamp` datetime NOT NULL,
  PRIMARY KEY (`change_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `change_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `change_log_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contacts_users`
--

DROP TABLE IF EXISTS `contacts_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts_users` (
  `contact_id` int(11) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  KEY `contact_id` (`contact_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `contacts_users_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `customer_contacts` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `contacts_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_address_phones`
--

DROP TABLE IF EXISTS `customer_address_phones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_address_phones` (
  `phone_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `address_id` int(11) unsigned NOT NULL,
  `number` varchar(255) NOT NULL,
  `type` tinyint(4) unsigned DEFAULT '1',
  `number_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`phone_id`),
  KEY `address_id` (`address_id`),
  KEY `type` (`type`),
  CONSTRAINT `customer_address_phones_ibfk_1` FOREIGN KEY (`type`) REFERENCES `phone_types` (`type_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `customer_address_phones_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `customer_addresses` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1484 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_addresses`
--

DROP TABLE IF EXISTS `customer_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_addresses` (
  `address_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) unsigned NOT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `notes` text,
  `main_location` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(4) unsigned DEFAULT '1',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`address_id`),
  KEY `type` (`type`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `customer_addresses_ibfk_1` FOREIGN KEY (`type`) REFERENCES `address_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `customer_addresses_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1194 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_contact_email`
--

DROP TABLE IF EXISTS `customer_contact_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_contact_email` (
  `email_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `type` tinyint(4) unsigned DEFAULT '1',
  PRIMARY KEY (`email_id`),
  KEY `type` (`type`),
  KEY `contact_id` (`contact_id`),
  CONSTRAINT `customer_contact_email_ibfk_1` FOREIGN KEY (`type`) REFERENCES `email_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `customer_contact_email_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `customer_contacts` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_contact_phones`
--

DROP TABLE IF EXISTS `customer_contact_phones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_contact_phones` (
  `phone_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) unsigned NOT NULL,
  `number` varchar(255) NOT NULL,
  `type` tinyint(4) unsigned DEFAULT '1',
  `number_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`phone_id`),
  KEY `type` (`type`),
  KEY `contact_id` (`contact_id`),
  CONSTRAINT `customer_contact_phones_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `customer_contacts` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `customer_contact_phones_ibfk_3` FOREIGN KEY (`type`) REFERENCES `phone_types` (`type_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18926 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_contacts`
--

DROP TABLE IF EXISTS `customer_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_contacts` (
  `contact_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) unsigned NOT NULL,
  `address_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `suffix` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `email1` varchar(75) DEFAULT NULL,
  `email2` varchar(75) DEFAULT NULL,
  `notes` text,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contact_id`),
  KEY `customer_id` (`customer_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `customer_contacts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `customer_contacts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=86888 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customerorderdetail`
--

DROP TABLE IF EXISTS `customerorderdetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customerorderdetail` (
  `CustomerOrderNumber` int(10) NOT NULL,
  `ProductNumberInternal` varchar(20) NOT NULL,
  `CustomerOrderSeqNumber` smallint(5) NOT NULL,
  `CustomerCodeNumber` varchar(50) DEFAULT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `Quantity` double(15,5) DEFAULT NULL,
  `PackSize` double(15,5) DEFAULT NULL,
  `TotalQuantityOrdered` double(15,5) DEFAULT NULL,
  `UnitOfMeasure` varchar(5) DEFAULT NULL,
  `ShipDate` datetime DEFAULT NULL,
  `BilledDate` datetime DEFAULT NULL,
  PRIMARY KEY (`CustomerOrderNumber`,`ProductNumberInternal`,`CustomerOrderSeqNumber`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  CONSTRAINT `customerorderdetail_ibfk_2` FOREIGN KEY (`CustomerOrderNumber`) REFERENCES `customerordermaster` (`OrderNumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `customerorderdetail_ibfk_3` FOREIGN KEY (`ProductNumberInternal`) REFERENCES `productmaster` (`ProductNumberInternal`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customerorderdetaillotnumbers`
--

DROP TABLE IF EXISTS `customerorderdetaillotnumbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customerorderdetaillotnumbers` (
  `RecordID` int(10) NOT NULL AUTO_INCREMENT,
  `CustomerOrderNumber` int(10) DEFAULT NULL,
  `ProductNumberInternal` varchar(20) DEFAULT NULL,
  `CustomerOrderSeqNumber` smallint(5) DEFAULT NULL,
  `LotID` int(11) unsigned DEFAULT NULL,
  `InventoryMovementTransactionNumber` int(11) unsigned DEFAULT NULL,
  `QuantityUsedFromThisLot` double(15,5) DEFAULT NULL,
  PRIMARY KEY (`RecordID`),
  KEY `CustomerOrderSeqNumber` (`CustomerOrderSeqNumber`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `PurchaseOrderNumber` (`CustomerOrderNumber`),
  KEY `LotID` (`LotID`),
  CONSTRAINT `customerorderdetaillotnumbers_ibfk_1` FOREIGN KEY (`CustomerOrderNumber`) REFERENCES `customerordermaster` (`OrderNumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `customerorderdetaillotnumbers_ibfk_2` FOREIGN KEY (`ProductNumberInternal`) REFERENCES `productmaster` (`ProductNumberInternal`) ON UPDATE CASCADE,
  CONSTRAINT `customerorderdetaillotnumbers_ibfk_3` FOREIGN KEY (`LotID`) REFERENCES `lots` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=661 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customerordermaster`
--

DROP TABLE IF EXISTS `customerordermaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customerordermaster` (
  `OrderNumber` int(10) NOT NULL AUTO_INCREMENT,
  `CustomerID` int(11) unsigned DEFAULT NULL,
  `OrderDate` datetime DEFAULT NULL,
  `ContactID` int(11) unsigned DEFAULT NULL,
  `BillToLocationID` int(11) unsigned DEFAULT NULL,
  `ShipToLocationID` int(11) unsigned DEFAULT NULL,
  `CustomerPONumber` varchar(50) DEFAULT NULL,
  `C_of_A_Requested` tinyint(1) NOT NULL,
  `MSDS_Requested` tinyint(1) NOT NULL,
  `NAFTA_Requested` tinyint(1) NOT NULL,
  `Hazardous_Info_Requested` tinyint(1) NOT NULL,
  `Kosher` tinyint(1) NOT NULL,
  `SpecialInstructions` longtext,
  `RequestedDeliveryDate` datetime DEFAULT NULL,
  `ShipVia` varchar(25) DEFAULT NULL,
  `OrderTakenByEmployeeID` int(10) DEFAULT NULL,
  `ConfirmedToCustomer` tinyint(1) DEFAULT '0',
  `ConfirmedBy` varchar(500) DEFAULT NULL,
  `ConfirmFile` varchar(125) DEFAULT NULL,
  PRIMARY KEY (`OrderNumber`),
  KEY `BillToLocationID` (`ShipToLocationID`),
  KEY `ContactID` (`ContactID`),
  KEY `ContactID1` (`BillToLocationID`),
  KEY `CustomerID` (`CustomerID`),
  KEY `QualityControlEmployeeID` (`OrderTakenByEmployeeID`),
  CONSTRAINT `customerordermaster_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`customer_id`) ON UPDATE CASCADE,
  CONSTRAINT `customerordermaster_ibfk_3` FOREIGN KEY (`BillToLocationID`) REFERENCES `customer_addresses` (`address_id`) ON UPDATE CASCADE,
  CONSTRAINT `customerordermaster_ibfk_4` FOREIGN KEY (`ShipToLocationID`) REFERENCES `customer_addresses` (`address_id`) ON UPDATE CASCADE,
  CONSTRAINT `customerordermaster_ibfk_5` FOREIGN KEY (`ContactID`) REFERENCES `customer_contacts` (`contact_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=808 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `customer_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `notes` text,
  `web_address` varchar(255) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5312 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customers_users`
--

DROP TABLE IF EXISTS `customers_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers_users` (
  `customer_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  KEY `customer_id` (`customer_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `customers_users_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `customers_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_batchsheetdetail`
--

DROP TABLE IF EXISTS `deleted_batchsheetdetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_batchsheetdetail` (
  `BatchSheetNumber` int(10) NOT NULL,
  `IngredientProductNumber` varchar(12) NOT NULL,
  `IngredientSEQ` double(7,2) NOT NULL,
  `IngredientNumberExternal` varchar(20) DEFAULT NULL,
  `IngredientDesignation` varchar(100) DEFAULT NULL,
  `Intermediary` tinyint(1) NOT NULL,
  `Percentage` double(15,5) DEFAULT NULL,
  `RawMaterialLotNumbers` longtext,
  `SubBatchSheetNumber` int(10) DEFAULT NULL,
  `FEMA_NBR` varchar(15) DEFAULT NULL,
  `VendorID` int(11) unsigned DEFAULT NULL,
  `InventoryTransactionNumber` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`BatchSheetNumber`,`IngredientProductNumber`,`IngredientSEQ`),
  KEY `BatchSheetNumber1` (`SubBatchSheetNumber`),
  KEY `IngredientProductNumber` (`IngredientProductNumber`),
  KEY `IngredientSEQ` (`IngredientSEQ`),
  KEY `ProductNumberExternal` (`IngredientNumberExternal`),
  KEY `VendorID` (`VendorID`),
  KEY `InventoryTransactionNumber` (`InventoryTransactionNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_batchsheetdetaillotnumbers`
--

DROP TABLE IF EXISTS `deleted_batchsheetdetaillotnumbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_batchsheetdetaillotnumbers` (
  `RecordID` int(10) NOT NULL AUTO_INCREMENT,
  `BatchSheetNumber` int(10) DEFAULT NULL,
  `IngredientProductNumber` varchar(12) DEFAULT NULL,
  `IngredientSEQ` double(7,2) DEFAULT NULL,
  `LotID` int(11) unsigned DEFAULT NULL,
  `InventoryMovementTransactionNumber` int(11) unsigned DEFAULT NULL,
  `QuantityUsedFromThisLot` double(15,5) DEFAULT NULL,
  PRIMARY KEY (`RecordID`),
  UNIQUE KEY `InventoryMovementTransactionNumber` (`InventoryMovementTransactionNumber`),
  KEY `BatchSheetNumber` (`BatchSheetNumber`),
  KEY `IngredientProductNumber` (`IngredientProductNumber`),
  KEY `LotID` (`LotID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_batchsheetmaster`
--

DROP TABLE IF EXISTS `deleted_batchsheetmaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_batchsheetmaster` (
  `BatchSheetNumber` int(10) NOT NULL,
  `LotID` int(11) unsigned DEFAULT NULL,
  `ProductNumberExternal` varchar(20) DEFAULT NULL,
  `ProductNumberInternal` varchar(20) DEFAULT NULL,
  `ProductDesignation` varchar(100) DEFAULT NULL,
  `CustomerID` int(11) unsigned DEFAULT NULL,
  `DueDate` datetime DEFAULT NULL,
  `NetWeight` double(15,5) DEFAULT NULL,
  `TotalQuantity` double(15,5) DEFAULT NULL,
  `TotalQuantityUnitType` varchar(5) DEFAULT NULL,
  `Column1UnitType` varchar(5) DEFAULT NULL,
  `Column2UnitType` varchar(5) DEFAULT NULL,
  `Filtered` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Allergen` varchar(100) DEFAULT NULL,
  `Kosher` varchar(100) DEFAULT NULL,
  `NumberOfTimesToMake` double(7,2) DEFAULT NULL,
  `MadeBy` varchar(50) DEFAULT NULL,
  `Yield` double(7,3) DEFAULT NULL,
  `Vessel` varchar(30) DEFAULT NULL,
  `Notes` longtext,
  `ScaleNumber` varchar(10) DEFAULT NULL,
  `CommitedToInventory` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Manufactured` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `InventoryMovementTransactionNumber` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`BatchSheetNumber`),
  KEY `CustomerID` (`CustomerID`),
  KEY `NumberOfTimesToMake` (`NumberOfTimesToMake`),
  KEY `ProductNumberExternal` (`ProductNumberExternal`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `LotID` (`LotID`),
  KEY `InventoryMovementTransactionNumber` (`InventoryMovementTransactionNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_customerorderdetail`
--

DROP TABLE IF EXISTS `deleted_customerorderdetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_customerorderdetail` (
  `CustomerOrderNumber` int(10) NOT NULL,
  `ProductNumberInternal` varchar(20) NOT NULL,
  `CustomerOrderSeqNumber` smallint(5) NOT NULL,
  `CustomerCodeNumber` varchar(50) DEFAULT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `Quantity` double(15,5) DEFAULT NULL,
  `PackSize` double(15,5) DEFAULT NULL,
  `TotalQuantityOrdered` double(15,5) DEFAULT NULL,
  `UnitOfMeasure` varchar(5) DEFAULT NULL,
  `ShipDate` datetime DEFAULT NULL,
  `BilledDate` datetime DEFAULT NULL,
  PRIMARY KEY (`CustomerOrderNumber`,`ProductNumberInternal`,`CustomerOrderSeqNumber`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_customerorderdetaillotnumbers`
--

DROP TABLE IF EXISTS `deleted_customerorderdetaillotnumbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_customerorderdetaillotnumbers` (
  `RecordID` int(10) NOT NULL AUTO_INCREMENT,
  `CustomerOrderNumber` int(10) DEFAULT NULL,
  `ProductNumberInternal` varchar(20) DEFAULT NULL,
  `CustomerOrderSeqNumber` smallint(5) DEFAULT NULL,
  `LotID` int(11) unsigned DEFAULT NULL,
  `InventoryMovementTransactionNumber` int(11) unsigned DEFAULT NULL,
  `QuantityUsedFromThisLot` double(15,5) DEFAULT NULL,
  PRIMARY KEY (`RecordID`),
  KEY `CustomerOrderSeqNumber` (`CustomerOrderSeqNumber`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `PurchaseOrderNumber` (`CustomerOrderNumber`),
  KEY `LotID` (`LotID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_externalproductnumberreference`
--

DROP TABLE IF EXISTS `deleted_externalproductnumberreference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_externalproductnumberreference` (
  `ProductNumberExternal` varchar(20) NOT NULL,
  `ProductNumberInternal` varchar(20) NOT NULL,
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `ProductNumberInternal1` (`ProductNumberExternal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_formulationdetail`
--

DROP TABLE IF EXISTS `deleted_formulationdetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_formulationdetail` (
  `ProductNumberInternal` varchar(12) NOT NULL,
  `IngredientSEQ` double(7,2) NOT NULL,
  `IngredientProductNumber` varchar(12) DEFAULT NULL,
  `Percentage` double(30,15) DEFAULT NULL,
  `VendorID` int(10) unsigned DEFAULT NULL,
  `Tier` varchar(1) DEFAULT NULL,
  `New_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `NewIngredientProductNumber` varchar(12) DEFAULT NULL,
  `Delete_This_REC` tinyint(1) NOT NULL DEFAULT '0',
  `OLD_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `OLDIngredientProductNumber` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`ProductNumberInternal`,`IngredientSEQ`),
  KEY `New_ProductNumberInternal` (`New_ProductNumberInternal`),
  KEY `New_ProductNumberInternal1` (`OLD_ProductNumberInternal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_inventorymovements`
--

DROP TABLE IF EXISTS `deleted_inventorymovements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_inventorymovements` (
  `TransactionNumber` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LotID` int(11) unsigned DEFAULT NULL,
  `ProductNumberInternal` varchar(20) DEFAULT NULL,
  `TransactionDate` datetime DEFAULT NULL,
  `Quantity` double(15,5) DEFAULT NULL,
  `TransactionType` smallint(5) DEFAULT NULL,
  `Remarks` longtext,
  `MovementStatus` varchar(1) NOT NULL DEFAULT 'C' COMMENT 'C, D, P, R - Committed, Deleted, Pending, Reserved',
  PRIMARY KEY (`TransactionNumber`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `LotID` (`LotID`),
  KEY `TransactionType` (`TransactionType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_pricesheetdetail`
--

DROP TABLE IF EXISTS `deleted_pricesheetdetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_pricesheetdetail` (
  `PriceSheetNumber` int(10) NOT NULL,
  `IngredientProductNumber` varchar(12) NOT NULL,
  `IngredientSEQ` double(7,2) NOT NULL,
  `IngredientDesignation` varchar(100) DEFAULT NULL,
  `Percentage` double(30,15) DEFAULT NULL,
  `Price` double(30,15) DEFAULT NULL,
  `PriceEffectiveDate` datetime DEFAULT NULL,
  `VendorID` int(11) unsigned DEFAULT NULL,
  `Intermediary` tinyint(1) NOT NULL DEFAULT '0',
  `Tier` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`PriceSheetNumber`,`IngredientProductNumber`,`IngredientSEQ`),
  KEY `IngredientProductNumber` (`IngredientProductNumber`),
  KEY `VendorID` (`VendorID`),
  KEY `PriceSheetNumber` (`PriceSheetNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_pricesheetmaster`
--

DROP TABLE IF EXISTS `deleted_pricesheetmaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_pricesheetmaster` (
  `PriceSheetNumber` int(10) NOT NULL AUTO_INCREMENT,
  `ProductNumberInternal` varchar(20) DEFAULT NULL,
  `ProductDesignation` varchar(100) DEFAULT NULL,
  `ProductType` varchar(10) DEFAULT NULL,
  `ProcessType` varchar(25) DEFAULT NULL,
  `DatePriced` datetime DEFAULT NULL,
  `CustomerID` int(11) unsigned DEFAULT NULL,
  `Manufacturer` varchar(50) DEFAULT NULL,
  `SprayDriedCost` double(15,5) DEFAULT NULL,
  `RibbonBlendingCost` double(15,5) DEFAULT NULL,
  `LiquidProcessingCost` double(15,5) DEFAULT NULL,
  `PackagingCost` double(15,5) DEFAULT NULL,
  `ShippingCost` double(15,5) DEFAULT NULL,
  `Cost_In_Use` double(15,5) DEFAULT NULL,
  `FOBLocation` varchar(100) DEFAULT NULL,
  `Terms` varchar(50) DEFAULT NULL,
  `MinBatch` double(15,5) DEFAULT NULL,
  `MinBatch_Units` varchar(50) DEFAULT NULL,
  `Notes` longtext,
  `Lbs_Per_Gallon` double(15,5) DEFAULT NULL,
  `Packaged_In` varchar(50) DEFAULT NULL,
  `SellingPrice` double(15,5) DEFAULT NULL,
  `SpecificGravity` double(15,5) DEFAULT NULL,
  `Original_From_Formulation` tinyint(1) DEFAULT '1',
  `ManualAdjustment` double(15,5) DEFAULT NULL,
  `ManualAdjustmentType` varchar(50) DEFAULT NULL,
  `SalesPersonEmployeeID` int(11) unsigned DEFAULT NULL,
  `Priced_ByEmployeeID` int(11) unsigned DEFAULT NULL,
  `IncludePricePerGallonInQuote` tinyint(1) DEFAULT NULL,
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`PriceSheetNumber`),
  KEY `CustomerID` (`CustomerID`),
  KEY `Priced_ByEmployeeID` (`Priced_ByEmployeeID`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `SalesPersonEmployeeID` (`SalesPersonEmployeeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_productmaster`
--

DROP TABLE IF EXISTS `deleted_productmaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_productmaster` (
  `ProductNumberInternal` varchar(20) NOT NULL,
  `AllergenEgg` tinyint(1) DEFAULT NULL,
  `AllergenMilk` tinyint(1) DEFAULT NULL,
  `AllergenPeanut` tinyint(1) DEFAULT NULL,
  `AllergenSeafood` tinyint(1) DEFAULT NULL,
  `AllergenSeed` tinyint(1) DEFAULT NULL,
  `AllergenSoybean` tinyint(1) DEFAULT NULL,
  `AllergenSulfites` tinyint(1) DEFAULT NULL,
  `AllergenTreeNuts` tinyint(1) DEFAULT NULL,
  `AllergenWheat` tinyint(1) DEFAULT NULL,
  `AllergenYellow` tinyint(1) DEFAULT NULL,
  `Appearance` longtext,
  `Ash` double(30,15) DEFAULT NULL,
  `BatchSize` varchar(50) DEFAULT NULL,
  `BatchSizeKg` double(30,15) DEFAULT NULL,
  `Biotin` double(30,15) DEFAULT NULL,
  `BoilingPoint` varchar(10) DEFAULT NULL,
  `Calcium` double(30,15) DEFAULT NULL,
  `Calories` double(30,15) DEFAULT NULL,
  `CaloriesFromFat` double(30,15) DEFAULT NULL,
  `Cholesterol` double(30,15) DEFAULT NULL,
  `Copper` double(30,15) DEFAULT NULL,
  `CurrentSellingItem` tinyint(1) DEFAULT '0',
  `DateOfFormulation` datetime DEFAULT NULL,
  `Designation` varchar(100) DEFAULT NULL,
  `DietaryFiber` double(30,15) DEFAULT NULL,
  `DeveloperID` int(10) DEFAULT NULL,
  `EmergencyFirstAidProcedure` longtext,
  `EvaporationRate` varchar(10) DEFAULT NULL,
  `ExtinguishingMedia` longtext,
  `FatCalories` double(30,15) DEFAULT NULL,
  `FEMA_NBR` varchar(15) DEFAULT NULL,
  `FinalProductNotCreatedByAbelei` tinyint(1) DEFAULT '0',
  `FlammableLimits` varchar(25) DEFAULT NULL,
  `Flashpoint` varchar(50) DEFAULT NULL,
  `FlavorAndAroma` longtext,
  `Folate` double(30,15) DEFAULT NULL,
  `FolateFolacinFolicAdic` double(30,15) DEFAULT NULL,
  `GeneralDescriptionOfFormulation` varchar(75) DEFAULT NULL,
  `GMO` longtext,
  `Halal` longtext,
  `Hazard` longtext,
  `HazardousComponents` longtext,
  `HazardousDecomposition` longtext,
  `HazardousPolymerization` tinyint(1) DEFAULT NULL,
  `HazardousPolymerizationConditions` longtext,
  `HealthHazards` longtext,
  `Incompatibility` longtext,
  `InsolubleFiber` double(30,15) DEFAULT NULL,
  `Intermediary` tinyint(1) DEFAULT NULL,
  `Iodine` double(30,15) DEFAULT NULL,
  `Iron` double(30,15) DEFAULT NULL,
  `Keywords` varchar(255) DEFAULT NULL,
  `Kosher` longtext,
  `KosherStatus` varchar(50) DEFAULT NULL,
  `LabelDeclaration` longtext,
  `Lactose` double(30,15) DEFAULT NULL,
  `LEL` varchar(15) DEFAULT NULL,
  `Magnesium` double(30,15) DEFAULT NULL,
  `Manganese` double(30,15) DEFAULT NULL,
  `ManufacturingInstructions` longtext,
  `MedicalCondition` longtext,
  `MeltingPoint` varchar(10) DEFAULT NULL,
  `MonounsaturatedFat` double(30,15) DEFAULT NULL,
  `MostRecentVendorID` int(10) DEFAULT NULL,
  `Natural_OR_Artificial` varchar(50) DEFAULT NULL,
  `Niacin` double(30,15) DEFAULT NULL,
  `NonFlavorIngredients` longtext,
  `NoteForFormulation` longtext,
  `OldDescriptionDelete` varchar(99) DEFAULT NULL,
  `Organic` tinyint(1) DEFAULT NULL,
  `OtherCarbohydrates` double(30,15) DEFAULT NULL,
  `OtherProtectiveClothing` longtext,
  `Packaging` longtext,
  `PantothenicAcid` double(30,15) DEFAULT NULL,
  `Phosphorus` double(30,15) DEFAULT NULL,
  `PolyunsaturatedFat` double(30,15) DEFAULT NULL,
  `Potassium` double(30,15) DEFAULT NULL,
  `Precautions` longtext,
  `PriceOfMaterial` double(30,15) DEFAULT NULL,
  `ProductType` varchar(10) DEFAULT NULL,
  `ProjectNumber` varchar(6) DEFAULT NULL,
  `ProtectiveGloves` longtext,
  `Protein` double(30,15) DEFAULT NULL,
  `Quality_Sensitive` varchar(1) DEFAULT NULL,
  `QuickScan` varchar(20) DEFAULT NULL,
  `RefractiveIndex` varchar(50) DEFAULT NULL,
  `Riboflavin` double(30,15) DEFAULT NULL,
  `ReplacedBy` varchar(20) DEFAULT NULL,
  `RestrictedAccess` tinyint(1) DEFAULT '0',
  `SaturatedFat` double(30,15) DEFAULT NULL,
  `SaturatedFatCalories` double(30,15) DEFAULT NULL,
  `ShelfLifeInMonths` double(7,2) DEFAULT NULL,
  `SignsAndSymptoms` longtext,
  `Sodium` double(30,15) DEFAULT NULL,
  `SolubilityInWater` longtext,
  `SolubleFiber` double(30,15) DEFAULT NULL,
  `SpecialFirefightingProcedures` longtext,
  `SpecificGravity` double(30,15) DEFAULT NULL,
  `SpecificGravityUnits` varchar(10) DEFAULT NULL,
  `Stability` tinyint(1) DEFAULT NULL,
  `StabilityConditions` longtext,
  `StepsToBeTaken` longtext,
  `StorageAndShelfLife` longtext,
  `SugarAlcohol` double(30,15) DEFAULT NULL,
  `Sugars` double(30,15) DEFAULT NULL,
  `Thiamin` double(30,15) DEFAULT NULL,
  `TotalCarbohydrates` double(30,15) DEFAULT NULL,
  `TotalFat` double(30,15) DEFAULT NULL,
  `TotalSolids` double(30,15) DEFAULT NULL,
  `TransFattyAcids` double(30,15) DEFAULT NULL,
  `UEL` varchar(15) DEFAULT NULL,
  `UnusualFire` longtext,
  `UseLevel` varchar(50) DEFAULT NULL,
  `VaporDensity` varchar(10) DEFAULT NULL,
  `VaporPressure` varchar(10) DEFAULT NULL,
  `VentilatorMechanical` varchar(50) DEFAULT NULL,
  `VentilatorSpecial` varchar(50) DEFAULT NULL,
  `VerifiedYN` tinyint(1) DEFAULT NULL,
  `VitaminA` double(30,15) DEFAULT NULL,
  `VitaminB12` double(30,15) DEFAULT NULL,
  `VitaminB6` double(30,15) DEFAULT NULL,
  `VitaminC` double(30,15) DEFAULT NULL,
  `VitaminD` double(30,15) DEFAULT NULL,
  `VitaminE` double(30,15) DEFAULT NULL,
  `WasteDisposalMethod` longtext,
  `Water` double(30,15) DEFAULT NULL,
  `WeightPerGallon` double(30,15) DEFAULT NULL,
  `WeightPerGallonUnits` varchar(10) DEFAULT NULL,
  `WorkHygienicPractices` longtext,
  `Zinc` double(30,15) DEFAULT NULL,
  `New_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `Delete_This_REC` tinyint(1) DEFAULT '0',
  `Notes` longtext,
  `New_Designation` varchar(100) DEFAULT NULL,
  `OLD_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `OLD_Designation` varchar(100) DEFAULT NULL,
  `OrderTriggerAmount` double(30,15) DEFAULT NULL,
  `MaxTargetAmount` double(30,15) DEFAULT NULL,
  `UnitOfMeasure` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`ProductNumberInternal`),
  KEY `DeveloperID` (`DeveloperID`),
  KEY `Keywords` (`Keywords`),
  KEY `MostRecentVendorID` (`MostRecentVendorID`),
  KEY `New_ProductNumberInternal` (`OLD_ProductNumberInternal`),
  KEY `PantothenicAcid` (`PantothenicAcid`),
  KEY `ProductNumberInternal` (`New_ProductNumberInternal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_productpacksize`
--

DROP TABLE IF EXISTS `deleted_productpacksize`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_productpacksize` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ProductNumberInternal` varchar(20) NOT NULL,
  `PackSize` double(15,5) NOT NULL,
  `UnitOfMeasure` varchar(15) NOT NULL DEFAULT 'lbs',
  `PackagingType` varchar(50) NOT NULL,
  `ProductNumberExternal` varchar(20) DEFAULT NULL,
  `PackIn` varchar(20) NOT NULL COMMENT 'Packin Productnumber',
  `DefaultPksz` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_productprices`
--

DROP TABLE IF EXISTS `deleted_productprices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_productprices` (
  `VendorID` int(10) NOT NULL,
  `ProductNumberInternal` varchar(20) NOT NULL,
  `Tier` varchar(1) NOT NULL,
  `DateQuoted` datetime DEFAULT NULL,
  `PriceEffectiveDate` datetime DEFAULT NULL,
  `PricePerPound` decimal(19,4) DEFAULT NULL,
  `Notes` varchar(100) DEFAULT NULL,
  `Volume` varchar(50) DEFAULT NULL,
  `Units` varchar(50) DEFAULT NULL,
  `Kosher` varchar(2) DEFAULT NULL,
  `New_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `Minimums` varchar(50) DEFAULT NULL,
  `Packaging` varchar(50) DEFAULT NULL,
  `MISC` varchar(50) DEFAULT NULL,
  `OLD_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`VendorID`,`ProductNumberInternal`,`Tier`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `Tier` (`Tier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deleted_purchaseorderdetail`
--

DROP TABLE IF EXISTS `deleted_purchaseorderdetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deleted_purchaseorderdetail` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PurchaseOrderNumber` int(11) unsigned NOT NULL,
  `ProductNumberInternal` varchar(20) NOT NULL,
  `PurchaseOrderSeqNumber` smallint(5) NOT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `PackSize` int(11) DEFAULT NULL,
  `UnitOfMeasure` varchar(5) DEFAULT NULL,
  `UnitPrice` decimal(15,2) DEFAULT NULL,
  `TotalQuantityOrdered` int(11) DEFAULT NULL,
  `TotalQuantityExpected` int(11) DEFAULT NULL,
  `VendorProductCode` varchar(50) DEFAULT NULL,
  `FinalProductNotCreatedByAbelei` tinyint(1) NOT NULL DEFAULT '0',
  `IntermediarySentToVendor` tinyint(1) NOT NULL DEFAULT '0',
  `InventoryMovementTransactionNumber` int(11) unsigned DEFAULT NULL,
  `Status` varchar(1) DEFAULT 'O' COMMENT 'A:accepted, O:open, P:pending, R:rejected',
  PRIMARY KEY (`ID`),
  KEY `VendorProductCode` (`VendorProductCode`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `PurchaseOrderNumber` (`PurchaseOrderNumber`),
  KEY `PurchaseOrderSeqNumber` (`PurchaseOrderSeqNumber`),
  KEY `InventoryMovementTransactionNumber` (`InventoryMovementTransactionNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_types`
--

DROP TABLE IF EXISTS `email_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_types` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `externalproductnumberreference`
--

DROP TABLE IF EXISTS `externalproductnumberreference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `externalproductnumberreference` (
  `ProductNumberExternal` varchar(20) NOT NULL,
  `ProductNumberInternal` varchar(20) NOT NULL,
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `ProductNumberInternal1` (`ProductNumberExternal`),
  CONSTRAINT `externalproductnumberreference_ibfk_1` FOREIGN KEY (`ProductNumberInternal`) REFERENCES `productmaster` (`ProductNumberInternal`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flavors`
--

DROP TABLE IF EXISTS `flavors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flavors` (
  `flavor_id` varchar(11) NOT NULL,
  `project_id` int(5) unsigned zerofill NOT NULL,
  `flavor_name` varchar(100) NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `suggested_level` tinyint(3) unsigned DEFAULT NULL,
  `suggested_level_other` varchar(35) DEFAULT NULL,
  `use_in` varchar(40) DEFAULT NULL,
  `other_info` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formulationdetail`
--

DROP TABLE IF EXISTS `formulationdetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formulationdetail` (
  `ProductNumberInternal` varchar(12) NOT NULL,
  `IngredientSEQ` double(7,2) NOT NULL,
  `IngredientProductNumber` varchar(12) DEFAULT NULL,
  `Percentage` double(30,15) DEFAULT NULL,
  `VendorID` int(10) unsigned DEFAULT NULL,
  `Tier` varchar(1) DEFAULT NULL,
  `New_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `NewIngredientProductNumber` varchar(12) DEFAULT NULL,
  `Delete_This_REC` tinyint(1) NOT NULL DEFAULT '0',
  `OLD_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `OLDIngredientProductNumber` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`ProductNumberInternal`,`IngredientSEQ`),
  KEY `New_ProductNumberInternal` (`New_ProductNumberInternal`),
  KEY `New_ProductNumberInternal1` (`OLD_ProductNumberInternal`),
  CONSTRAINT `formulationdetail_ibfk_1` FOREIGN KEY (`ProductNumberInternal`) REFERENCES `productmaster` (`ProductNumberInternal`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory` (
  `ProductNumberInternal` varchar(20) NOT NULL,
  `LotNumber` varchar(30) NOT NULL,
  `LotSequenceNumber` int(10) NOT NULL DEFAULT '1',
  `BeginningInventory` double(15,5) DEFAULT '0.00000',
  `CurrentInventory` double(15,5) DEFAULT '0.00000',
  `PhysicalInventory` double(15,5) DEFAULT '0.00000',
  `start_date` datetime NOT NULL,
  `last_date` datetime NOT NULL,
  `inventoryUnits` varchar(5) NOT NULL DEFAULT 'grams',
  PRIMARY KEY (`ProductNumberInternal`,`LotNumber`,`LotSequenceNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventorymovements`
--

DROP TABLE IF EXISTS `inventorymovements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventorymovements` (
  `TransactionNumber` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LotID` int(11) unsigned DEFAULT NULL,
  `ProductNumberInternal` varchar(20) DEFAULT NULL,
  `TransactionDate` datetime DEFAULT NULL,
  `Quantity` double(15,5) DEFAULT NULL,
  `TransactionType` smallint(5) DEFAULT NULL,
  `Remarks` longtext,
  `MovementStatus` varchar(1) NOT NULL DEFAULT 'C' COMMENT 'C, D, P, R - Committed, Deleted, Pending, Reserved',
  PRIMARY KEY (`TransactionNumber`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `LotID` (`LotID`),
  KEY `TransactionType` (`TransactionType`),
  CONSTRAINT `inventorymovements_ibfk_3` FOREIGN KEY (`LotID`) REFERENCES `lots` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `inventorymovements_ibfk_4` FOREIGN KEY (`ProductNumberInternal`) REFERENCES `productmaster` (`ProductNumberInternal`) ON UPDATE CASCADE,
  CONSTRAINT `inventorymovements_ibfk_5` FOREIGN KEY (`TransactionType`) REFERENCES `inventorytransactiontypes` (`TransactionID`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19655 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventorytransactiontypes`
--

DROP TABLE IF EXISTS `inventorytransactiontypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventorytransactiontypes` (
  `TransactionID` smallint(5) NOT NULL,
  `TransactionDescription` varchar(50) DEFAULT NULL,
  `InventoryMultiplier` tinyint(4) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`TransactionID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lab_assignees`
--

DROP TABLE IF EXISTS `lab_assignees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lab_assignees` (
  `assignee_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(5) unsigned zerofill NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`assignee_id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `lab_assignees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `lab_assignees_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lots`
--

DROP TABLE IF EXISTS `lots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lots` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LotNumber` varchar(30) DEFAULT NULL,
  `LotSequenceNumber` int(10) DEFAULT NULL,
  `InventoryMovementTransactionNumber` int(11) unsigned DEFAULT NULL,
  `DateManufactured` datetime DEFAULT NULL,
  `ExpirationDate` datetime DEFAULT NULL,
  `QualityControlDate` datetime DEFAULT NULL,
  `QualityControlEmployeeID` int(10) DEFAULT NULL,
  `SizeOfRetainTaken` varchar(10) DEFAULT NULL,
  `QCCofAAvailable` varchar(1) DEFAULT NULL,
  `QCCofAStandardAvailable` varchar(1) DEFAULT NULL,
  `QCDateOfStandard` datetime DEFAULT NULL,
  `QCLotNumberofStandard` varchar(30) DEFAULT NULL,
  `QCColor` varchar(100) DEFAULT NULL,
  `QCOdor` varchar(100) DEFAULT NULL,
  `QCGranulation` varchar(100) DEFAULT NULL,
  `QCBrix` varchar(100) DEFAULT NULL,
  `QCMethodForOrganolepticEvaluation` longtext,
  `QCOrganolepticOberservations` longtext,
  `QCMicrobiologicalReportNeeded` varchar(1) DEFAULT NULL,
  `QCMicrobiologicalReportDate` datetime DEFAULT NULL,
  `QCMicrobiologicalReportMeetsSpecs` varchar(1) DEFAULT NULL,
  `QCMicrobiologicalReportDoesNotMeetSpecs` longtext,
  `QCProductMeetsAllSpecs` varchar(1) DEFAULT NULL,
  `QCComments` longtext,
  `QCMoisture` varchar(100) DEFAULT NULL,
  `QCPackagingTypeAndSize` varchar(50) DEFAULT NULL,
  `QCActualSpecificGravity` varchar(50) DEFAULT NULL,
  `StorageLocation` varchar(15) DEFAULT 'Warehouse',
  `VendorID` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `LotAndSequenceNumber` (`LotNumber`,`LotSequenceNumber`),
  KEY `InventoryMovementTransactionNumber` (`InventoryMovementTransactionNumber`),
  KEY `VendorID` (`VendorID`),
  CONSTRAINT `lots_ibfk_1` FOREIGN KEY (`InventoryMovementTransactionNumber`) REFERENCES `inventorymovements` (`TransactionNumber`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5362 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `multiple_price_quotes`
--

DROP TABLE IF EXISTS `multiple_price_quotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multiple_price_quotes` (
  `quote_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_sent` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`quote_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `multiple_price_quotes_items`
--

DROP TABLE IF EXISTS `multiple_price_quotes_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multiple_price_quotes_items` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quote_id` int(10) unsigned NOT NULL,
  `price_sheet` int(10) unsigned NOT NULL,
  `internal_number` varchar(25) NOT NULL,
  `external_number` varchar(25) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `price` decimal(7,2) unsigned NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `note_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(5) unsigned zerofill NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `notes` text NOT NULL,
  `date_time` datetime NOT NULL,
  PRIMARY KEY (`note_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phone_types`
--

DROP TABLE IF EXISTS `phone_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phone_types` (
  `type_id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `price_quote_letters`
--

DROP TABLE IF EXISTS `price_quote_letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `price_quote_letters` (
  `letter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pricesheet_number` int(10) unsigned NOT NULL,
  `address_id` int(10) unsigned NOT NULL,
  `contact_name` varchar(75) NOT NULL,
  `datetime_sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent_by` varchar(100) DEFAULT NULL COMMENT 'last user who printed and sent the letter',
  PRIMARY KEY (`letter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `price_quote_option_types`
--

DROP TABLE IF EXISTS `price_quote_option_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `price_quote_option_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(75) NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `price_quote_options`
--

DROP TABLE IF EXISTS `price_quote_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `price_quote_options` (
  `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option_type_id` int(10) unsigned NOT NULL,
  `value` double(10,5) NOT NULL,
  `text` varchar(75) NOT NULL,
  `minAmount` double(15,2) DEFAULT NULL,
  `minUnit` varchar(10) DEFAULT NULL,
  `PackInID` varchar(30) DEFAULT NULL,
  `Notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pricesheetdetail`
--

DROP TABLE IF EXISTS `pricesheetdetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pricesheetdetail` (
  `PriceSheetNumber` int(10) NOT NULL,
  `IngredientProductNumber` varchar(12) NOT NULL,
  `IngredientSEQ` double(7,2) NOT NULL,
  `IngredientDesignation` varchar(100) DEFAULT NULL,
  `Percentage` double(30,15) DEFAULT NULL,
  `Price` double(30,15) DEFAULT NULL,
  `PriceEffectiveDate` datetime DEFAULT NULL,
  `VendorID` int(11) unsigned DEFAULT NULL,
  `Intermediary` tinyint(1) NOT NULL DEFAULT '0',
  `Tier` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`PriceSheetNumber`,`IngredientProductNumber`,`IngredientSEQ`),
  KEY `IngredientProductNumber` (`IngredientProductNumber`),
  KEY `VendorID` (`VendorID`),
  KEY `PriceSheetNumber` (`PriceSheetNumber`),
  CONSTRAINT `pricesheetdetail_ibfk_1` FOREIGN KEY (`IngredientProductNumber`) REFERENCES `productmaster` (`ProductNumberInternal`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pricesheetdetail_ibfk_2` FOREIGN KEY (`VendorID`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pricesheetmaster`
--

DROP TABLE IF EXISTS `pricesheetmaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pricesheetmaster` (
  `PriceSheetNumber` int(10) NOT NULL AUTO_INCREMENT,
  `ProductNumberInternal` varchar(20) DEFAULT NULL,
  `ProductDesignation` varchar(100) DEFAULT NULL,
  `ProductType` varchar(10) DEFAULT NULL,
  `ProcessType` varchar(25) DEFAULT NULL,
  `DatePriced` datetime DEFAULT NULL,
  `CustomerID` int(11) unsigned DEFAULT NULL,
  `Manufacturer` varchar(50) DEFAULT NULL,
  `SprayDriedCost` double(15,5) DEFAULT NULL,
  `RibbonBlendingCost` double(15,5) DEFAULT NULL,
  `LiquidProcessingCost` double(15,5) DEFAULT NULL,
  `PackagingCost` double(15,5) DEFAULT NULL,
  `ShippingCost` double(15,5) DEFAULT NULL,
  `Cost_In_Use` double(15,5) DEFAULT NULL,
  `FOBLocation` varchar(100) DEFAULT NULL,
  `Terms` varchar(50) DEFAULT NULL,
  `MinBatch` double(15,5) DEFAULT NULL,
  `MinBatch_Units` varchar(50) DEFAULT NULL,
  `Notes` longtext,
  `Lbs_Per_Gallon` double(15,5) DEFAULT NULL,
  `Packaged_In` varchar(50) DEFAULT NULL,
  `SellingPrice` double(15,5) DEFAULT NULL,
  `SpecificGravity` double(15,5) DEFAULT NULL,
  `Original_From_Formulation` tinyint(1) DEFAULT '1',
  `ManualAdjustment` double(15,5) DEFAULT NULL,
  `ManualAdjustmentType` varchar(50) DEFAULT NULL,
  `SalesPersonEmployeeID` int(11) unsigned DEFAULT NULL,
  `Priced_ByEmployeeID` int(11) unsigned DEFAULT NULL,
  `IncludePricePerGallonInQuote` tinyint(1) DEFAULT NULL,
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`PriceSheetNumber`),
  KEY `CustomerID` (`CustomerID`),
  KEY `Priced_ByEmployeeID` (`Priced_ByEmployeeID`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `SalesPersonEmployeeID` (`SalesPersonEmployeeID`),
  CONSTRAINT `pricesheetmaster_ibfk_1` FOREIGN KEY (`ProductNumberInternal`) REFERENCES `productmaster` (`ProductNumberInternal`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pricesheetmaster_ibfk_2` FOREIGN KEY (`SalesPersonEmployeeID`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `pricesheetmaster_ibfk_3` FOREIGN KEY (`Priced_ByEmployeeID`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `pricesheetmaster_ibfk_4` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5509 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productmaster`
--

DROP TABLE IF EXISTS `productmaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productmaster` (
  `ProductNumberInternal` varchar(20) NOT NULL,
  `AllergenEgg` tinyint(1) DEFAULT NULL,
  `AllergenMilk` tinyint(1) DEFAULT NULL,
  `AllergenPeanut` tinyint(1) DEFAULT NULL,
  `AllergenSeafood` tinyint(1) DEFAULT NULL,
  `AllergenSeed` tinyint(1) DEFAULT NULL,
  `AllergenSoybean` tinyint(1) DEFAULT NULL,
  `AllergenSulfites` tinyint(1) DEFAULT NULL,
  `AllergenTreeNuts` tinyint(1) DEFAULT NULL,
  `AllergenWheat` tinyint(1) DEFAULT NULL,
  `AllergenYellow` tinyint(1) DEFAULT NULL,
  `Appearance` longtext,
  `Ash` double(30,15) DEFAULT NULL,
  `BatchSize` varchar(50) DEFAULT NULL,
  `BatchSizeKg` double(30,15) DEFAULT NULL,
  `Biotin` double(30,15) DEFAULT NULL,
  `BoilingPoint` varchar(10) DEFAULT NULL,
  `Calcium` double(30,15) DEFAULT NULL,
  `Calories` double(30,15) DEFAULT NULL,
  `CaloriesFromFat` double(30,15) DEFAULT NULL,
  `Cholesterol` double(30,15) DEFAULT NULL,
  `Copper` double(30,15) DEFAULT NULL,
  `CurrentSellingItem` tinyint(1) DEFAULT '0',
  `DateOfFormulation` datetime DEFAULT NULL,
  `Designation` varchar(100) DEFAULT NULL,
  `DietaryFiber` double(30,15) DEFAULT NULL,
  `DeveloperID` int(10) DEFAULT NULL,
  `EmergencyFirstAidProcedure` longtext,
  `EvaporationRate` varchar(10) DEFAULT NULL,
  `ExtinguishingMedia` longtext,
  `FatCalories` double(30,15) DEFAULT NULL,
  `FEMA_NBR` varchar(15) DEFAULT NULL,
  `FinalProductNotCreatedByAbelei` tinyint(1) DEFAULT '0',
  `FlammableLimits` varchar(25) DEFAULT NULL,
  `Flashpoint` varchar(50) DEFAULT NULL,
  `FlavorAndAroma` longtext,
  `Folate` double(30,15) DEFAULT NULL,
  `FolateFolacinFolicAdic` double(30,15) DEFAULT NULL,
  `GeneralDescriptionOfFormulation` varchar(75) DEFAULT NULL,
  `GMO` longtext,
  `Halal` longtext,
  `Hazard` longtext,
  `HazardousComponents` longtext,
  `HazardousDecomposition` longtext,
  `HazardousPolymerization` tinyint(1) DEFAULT NULL,
  `HazardousPolymerizationConditions` longtext,
  `HealthHazards` longtext,
  `Incompatibility` longtext,
  `InsolubleFiber` double(30,15) DEFAULT NULL,
  `Intermediary` tinyint(1) DEFAULT NULL,
  `Iodine` double(30,15) DEFAULT NULL,
  `Iron` double(30,15) DEFAULT NULL,
  `Keywords` varchar(255) DEFAULT NULL,
  `Kosher` longtext,
  `KosherStatus` varchar(50) DEFAULT NULL,
  `LabelDeclaration` longtext,
  `Lactose` double(30,15) DEFAULT NULL,
  `LEL` varchar(15) DEFAULT NULL,
  `Magnesium` double(30,15) DEFAULT NULL,
  `Manganese` double(30,15) DEFAULT NULL,
  `ManufacturingInstructions` longtext,
  `MedicalCondition` longtext,
  `MeltingPoint` varchar(10) DEFAULT NULL,
  `MonounsaturatedFat` double(30,15) DEFAULT NULL,
  `MostRecentVendorID` int(10) DEFAULT NULL,
  `Natural_OR_Artificial` varchar(50) DEFAULT NULL,
  `Niacin` double(30,15) DEFAULT NULL,
  `NonFlavorIngredients` longtext,
  `NoteForFormulation` longtext,
  `OldDescriptionDelete` varchar(99) DEFAULT NULL,
  `Organic` tinyint(1) DEFAULT NULL,
  `OtherCarbohydrates` double(30,15) DEFAULT NULL,
  `OtherProtectiveClothing` longtext,
  `Packaging` longtext,
  `PantothenicAcid` double(30,15) DEFAULT NULL,
  `Phosphorus` double(30,15) DEFAULT NULL,
  `PolyunsaturatedFat` double(30,15) DEFAULT NULL,
  `Potassium` double(30,15) DEFAULT NULL,
  `Precautions` longtext,
  `PriceOfMaterial` double(30,15) DEFAULT NULL,
  `ProductType` varchar(10) DEFAULT NULL,
  `ProjectNumber` varchar(6) DEFAULT NULL,
  `ProtectiveGloves` longtext,
  `Protein` double(30,15) DEFAULT NULL,
  `Quality_Sensitive` varchar(1) DEFAULT NULL,
  `QuickScan` varchar(20) DEFAULT NULL,
  `RefractiveIndex` varchar(50) DEFAULT NULL,
  `Riboflavin` double(30,15) DEFAULT NULL,
  `ReplacedBy` varchar(20) DEFAULT NULL,
  `RestrictedAccess` tinyint(1) DEFAULT '0',
  `SaturatedFat` double(30,15) DEFAULT NULL,
  `SaturatedFatCalories` double(30,15) DEFAULT NULL,
  `ShelfLifeInMonths` double(7,2) DEFAULT NULL,
  `SignsAndSymptoms` longtext,
  `Sodium` double(30,15) DEFAULT NULL,
  `SolubilityInWater` longtext,
  `SolubleFiber` double(30,15) DEFAULT NULL,
  `SpecialFirefightingProcedures` longtext,
  `SpecificGravity` double(30,15) DEFAULT NULL,
  `SpecificGravityUnits` varchar(10) DEFAULT NULL,
  `Stability` tinyint(1) DEFAULT NULL,
  `StabilityConditions` longtext,
  `StepsToBeTaken` longtext,
  `StorageAndShelfLife` longtext,
  `SugarAlcohol` double(30,15) DEFAULT NULL,
  `Sugars` double(30,15) DEFAULT NULL,
  `Thiamin` double(30,15) DEFAULT NULL,
  `TotalCarbohydrates` double(30,15) DEFAULT NULL,
  `TotalFat` double(30,15) DEFAULT NULL,
  `TotalSolids` double(30,15) DEFAULT NULL,
  `TransFattyAcids` double(30,15) DEFAULT NULL,
  `UEL` varchar(15) DEFAULT NULL,
  `UnusualFire` longtext,
  `UseLevel` varchar(50) DEFAULT NULL,
  `VaporDensity` varchar(10) DEFAULT NULL,
  `VaporPressure` varchar(10) DEFAULT NULL,
  `VentilatorMechanical` varchar(50) DEFAULT NULL,
  `VentilatorSpecial` varchar(50) DEFAULT NULL,
  `VerifiedYN` tinyint(1) DEFAULT NULL,
  `VitaminA` double(30,15) DEFAULT NULL,
  `VitaminB12` double(30,15) DEFAULT NULL,
  `VitaminB6` double(30,15) DEFAULT NULL,
  `VitaminC` double(30,15) DEFAULT NULL,
  `VitaminD` double(30,15) DEFAULT NULL,
  `VitaminE` double(30,15) DEFAULT NULL,
  `WasteDisposalMethod` longtext,
  `Water` double(30,15) DEFAULT NULL,
  `WeightPerGallon` double(30,15) DEFAULT NULL,
  `WeightPerGallonUnits` varchar(10) DEFAULT NULL,
  `WorkHygienicPractices` longtext,
  `Zinc` double(30,15) DEFAULT NULL,
  `New_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `Delete_This_REC` tinyint(1) DEFAULT '0',
  `Notes` longtext,
  `New_Designation` varchar(100) DEFAULT NULL,
  `OLD_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `OLD_Designation` varchar(100) DEFAULT NULL,
  `OrderTriggerAmount` double(30,15) DEFAULT NULL,
  `MaxTargetAmount` double(30,15) DEFAULT NULL,
  `UnitOfMeasure` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`ProductNumberInternal`),
  KEY `DeveloperID` (`DeveloperID`),
  KEY `Keywords` (`Keywords`),
  KEY `MostRecentVendorID` (`MostRecentVendorID`),
  KEY `New_ProductNumberInternal` (`OLD_ProductNumberInternal`),
  KEY `PantothenicAcid` (`PantothenicAcid`),
  KEY `ProductNumberInternal` (`New_ProductNumberInternal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productpacksize`
--

DROP TABLE IF EXISTS `productpacksize`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productpacksize` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ProductNumberInternal` varchar(20) NOT NULL,
  `PackSize` double(15,5) NOT NULL,
  `UnitOfMeasure` varchar(15) NOT NULL DEFAULT 'lbs',
  `PackagingType` varchar(50) NOT NULL,
  `ProductNumberExternal` varchar(20) DEFAULT NULL,
  `PackIn` varchar(20) NOT NULL COMMENT 'Packin Productnumber',
  `DefaultPksz` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  CONSTRAINT `productpacksize_ibfk_1` FOREIGN KEY (`ProductNumberInternal`) REFERENCES `productmaster` (`ProductNumberInternal`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=335 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productprices`
--

DROP TABLE IF EXISTS `productprices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productprices` (
  `VendorID` int(10) NOT NULL,
  `ProductNumberInternal` varchar(20) NOT NULL,
  `Tier` varchar(1) NOT NULL,
  `DateQuoted` datetime DEFAULT NULL,
  `PriceEffectiveDate` datetime DEFAULT NULL,
  `PricePerPound` decimal(19,4) DEFAULT NULL,
  `Notes` varchar(100) DEFAULT NULL,
  `Volume` varchar(50) DEFAULT NULL,
  `Units` varchar(50) DEFAULT NULL,
  `Kosher` varchar(2) DEFAULT NULL,
  `New_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `Minimums` varchar(50) DEFAULT NULL,
  `Packaging` varchar(50) DEFAULT NULL,
  `MISC` varchar(50) DEFAULT NULL,
  `OLD_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`VendorID`,`ProductNumberInternal`,`Tier`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `Tier` (`Tier`),
  CONSTRAINT `productprices_ibfk_1` FOREIGN KEY (`ProductNumberInternal`) REFERENCES `productmaster` (`ProductNumberInternal`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `project_id` int(5) unsigned zerofill NOT NULL,
  `contact_id` int(11) unsigned DEFAULT NULL,
  `date_created` date DEFAULT NULL,
  `priority` tinyint(1) unsigned DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `salesperson` int(10) unsigned DEFAULT NULL,
  `project_type` tinyint(1) unsigned DEFAULT NULL,
  `parent_id` int(5) unsigned zerofill DEFAULT NULL,
  `application` tinyint(2) unsigned DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `annual_potential` tinyint(1) unsigned DEFAULT NULL,
  `shipping` tinyint(1) unsigned DEFAULT NULL,
  `shipper` tinyint(1) unsigned DEFAULT NULL,
  `shipper_other` varchar(75) DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `shipped_date` date DEFAULT NULL,
  `n_a1` tinyint(1) unsigned DEFAULT NULL,
  `n_a2` tinyint(1) unsigned DEFAULT NULL,
  `form` tinyint(1) unsigned DEFAULT NULL,
  `product_type` tinyint(1) unsigned DEFAULT NULL,
  `kosher` tinyint(1) unsigned DEFAULT NULL,
  `halal` tinyint(1) unsigned DEFAULT NULL,
  `sample_size` tinyint(1) unsigned DEFAULT NULL,
  `sample_size_other` varchar(255) DEFAULT NULL,
  `base_included` tinyint(1) unsigned DEFAULT NULL,
  `target_included` tinyint(1) unsigned DEFAULT NULL,
  `target_level` varchar(255) DEFAULT NULL,
  `target_rmc` tinyint(1) unsigned DEFAULT '1',
  `cost_in_use` varchar(255) DEFAULT NULL,
  `cost_in_use_measure` tinyint(1) unsigned DEFAULT NULL,
  `summary` varchar(30) DEFAULT NULL,
  `comments` text,
  `follow_up` tinyint(1) unsigned DEFAULT '1',
  `project_info_submitted` tinyint(1) unsigned DEFAULT '0',
  `client_info_submitted` tinyint(1) unsigned DEFAULT '0',
  `sample_info_submitted` tinyint(1) unsigned DEFAULT '0',
  `sales_follow_up` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lab_comments` varchar(20) DEFAULT NULL,
  `sent_to_front` datetime DEFAULT NULL,
  PRIMARY KEY (`project_id`),
  KEY `contact_id` (`contact_id`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `customer_contacts` (`contact_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchaseorderdetail`
--

DROP TABLE IF EXISTS `purchaseorderdetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchaseorderdetail` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PurchaseOrderNumber` int(11) unsigned NOT NULL,
  `ProductNumberInternal` varchar(20) NOT NULL,
  `PurchaseOrderSeqNumber` smallint(5) NOT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `PackSize` int(11) DEFAULT NULL,
  `UnitOfMeasure` varchar(5) DEFAULT NULL,
  `UnitPrice` decimal(15,2) DEFAULT NULL,
  `TotalQuantityOrdered` int(11) DEFAULT NULL,
  `TotalQuantityExpected` int(11) DEFAULT NULL,
  `VendorProductCode` varchar(50) DEFAULT NULL,
  `FinalProductNotCreatedByAbelei` tinyint(1) NOT NULL DEFAULT '0',
  `IntermediarySentToVendor` tinyint(1) NOT NULL DEFAULT '0',
  `InventoryMovementTransactionNumber` int(11) unsigned DEFAULT NULL,
  `Status` varchar(1) DEFAULT 'O' COMMENT 'A:accepted, O:open, P:pending, R:rejected',
  PRIMARY KEY (`ID`),
  KEY `VendorProductCode` (`VendorProductCode`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `PurchaseOrderNumber` (`PurchaseOrderNumber`),
  KEY `PurchaseOrderSeqNumber` (`PurchaseOrderSeqNumber`),
  KEY `InventoryMovementTransactionNumber` (`InventoryMovementTransactionNumber`),
  CONSTRAINT `purchaseorderdetail_ibfk_1` FOREIGN KEY (`InventoryMovementTransactionNumber`) REFERENCES `inventorymovements` (`TransactionNumber`) ON UPDATE CASCADE,
  CONSTRAINT `purchaseorderdetail_ibfk_2` FOREIGN KEY (`ProductNumberInternal`) REFERENCES `productmaster` (`ProductNumberInternal`) ON UPDATE CASCADE,
  CONSTRAINT `purchaseorderdetail_ibfk_3` FOREIGN KEY (`PurchaseOrderNumber`) REFERENCES `purchaseordermaster` (`PurchaseOrderNumber`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2072 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchaseordermaster`
--

DROP TABLE IF EXISTS `purchaseordermaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchaseordermaster` (
  `PurchaseOrderNumber` int(11) unsigned NOT NULL,
  `PurchaseOrderType` varchar(8) DEFAULT NULL,
  `VendorID` int(11) unsigned DEFAULT NULL,
  `VendorName` varchar(50) DEFAULT NULL,
  `VendorStreetAddress1` varchar(50) DEFAULT NULL,
  `VendorStreetAddress2` varchar(50) DEFAULT NULL,
  `VendorCity` varchar(50) DEFAULT NULL,
  `VendorState` varchar(2) DEFAULT NULL,
  `VendorZipCode` varchar(15) DEFAULT NULL,
  `VendorMainPhoneNumber` varchar(30) DEFAULT NULL,
  `ShipToID` int(10) DEFAULT NULL,
  `ShipToName` varchar(50) DEFAULT NULL,
  `ShipToStreetAddress1` varchar(50) DEFAULT NULL,
  `ShipToStreetAddress2` varchar(50) DEFAULT NULL,
  `ShipToCity` varchar(50) DEFAULT NULL,
  `ShipToState` varchar(2) DEFAULT NULL,
  `ShipToZipCode` varchar(15) DEFAULT NULL,
  `ShipToMainPhoneNumber` varchar(30) DEFAULT NULL,
  `ShippingAndHandlingCost` decimal(10,2) DEFAULT NULL,
  `PaymentType` varchar(50) DEFAULT NULL,
  `ShippingDate` datetime DEFAULT NULL,
  `DateOrderPlaced` datetime DEFAULT NULL,
  `ConfirmationOrderNumber` varchar(20) DEFAULT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  `VendorSalesRep` varchar(60) DEFAULT NULL,
  `ShipVia` varchar(25) DEFAULT NULL,
  `Notes` longtext,
  PRIMARY KEY (`PurchaseOrderNumber`),
  KEY `ShipToZipCode` (`ShipToZipCode`),
  KEY `ShipToZipCode1` (`VendorZipCode`),
  KEY `VendorID` (`VendorID`),
  KEY `VendorID1` (`ShipToID`),
  CONSTRAINT `purchaseordermaster_ibfk_1` FOREIGN KEY (`VendorID`) REFERENCES `vendors` (`vendor_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `receipts`
--

DROP TABLE IF EXISTS `receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `receipts` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `LotID` int(11) unsigned DEFAULT NULL,
  `PurchaseOrderID` int(11) unsigned DEFAULT NULL,
  `VendorInvoiceNumber` varchar(50) DEFAULT NULL,
  `Quantity` double(15,5) NOT NULL,
  `PackSize` double(15,5) NOT NULL,
  `UnitOfMeasure` varchar(5) NOT NULL,
  `PackagingType` varchar(100) DEFAULT NULL,
  `DateReceived` datetime DEFAULT NULL,
  `EmployeeID` int(10) unsigned DEFAULT NULL,
  `ConditionOfShipment` varchar(45) DEFAULT NULL,
  `C_of_A_attached` tinyint(1) DEFAULT NULL,
  `MSDS_on_file` tinyint(1) DEFAULT NULL,
  `Specifications_on_file` tinyint(1) DEFAULT NULL,
  `Nutrition_on_file` tinyint(1) DEFAULT NULL,
  `Allergen_statement_on_file` tinyint(1) DEFAULT NULL,
  `KosherApproved` tinyint(1) DEFAULT NULL,
  `QualityControlApproval` tinyint(1) DEFAULT NULL,
  `Comments` longtext,
  `Status` varchar(1) DEFAULT NULL COMMENT 'A:Approved, P:Pending, R:Rejected',
  PRIMARY KEY (`ID`),
  KEY `LotID` (`LotID`),
  KEY `PurchaseOrderID` (`PurchaseOrderID`),
  KEY `EmployeeID` (`EmployeeID`),
  CONSTRAINT `receipts_ibfk_2` FOREIGN KEY (`PurchaseOrderID`) REFERENCES `purchaseorderdetail` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `receipts_ibfk_3` FOREIGN KEY (`LotID`) REFERENCES `lots` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `receipts_ibfk_4` FOREIGN KEY (`EmployeeID`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2283 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sample_batchsheets`
--

DROP TABLE IF EXISTS `sample_batchsheets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sample_batchsheets` (
  `sample_batchsheet_number` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_by` varchar(75) NOT NULL,
  `date` date NOT NULL,
  `contact` varchar(75) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(7,2) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `abelei_number` varchar(20) NOT NULL,
  PRIMARY KEY (`sample_batchsheet_number`)
) ENGINE=InnoDB AUTO_INCREMENT=375 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shipping_info`
--

DROP TABLE IF EXISTS `shipping_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_info` (
  `shipping_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(5) unsigned zerofill NOT NULL,
  `first_name` varchar(75) NOT NULL,
  `last_name` varchar(75) NOT NULL,
  `company` varchar(75) DEFAULT NULL,
  `address1` varchar(75) NOT NULL,
  `address2` varchar(75) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `zip` varchar(25) NOT NULL,
  `country` varchar(50) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`shipping_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `shipping_info_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblsystemdefaultsdetail`
--

DROP TABLE IF EXISTS `tblsystemdefaultsdetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblsystemdefaultsdetail` (
  `ItemID` int(10) NOT NULL,
  `ItemDescription` varchar(50) NOT NULL,
  `ItemValue` double(15,5) DEFAULT NULL,
  `ItemValueText` varchar(50) DEFAULT NULL,
  `Sequence` int(10) DEFAULT NULL,
  `Location_On_Site` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ItemID`,`ItemDescription`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblsystemdefaultsmaster`
--

DROP TABLE IF EXISTS `tblsystemdefaultsmaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblsystemdefaultsmaster` (
  `ItemID` int(10) NOT NULL AUTO_INCREMENT,
  `ItemDescription` varchar(50) DEFAULT NULL,
  KEY `ItemID` (`ItemID`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tmpclone`
--

DROP TABLE IF EXISTS `tmpclone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmpclone` (
  `ProductNumberInternal` varchar(20) NOT NULL,
  `AllergenEgg` tinyint(1) DEFAULT NULL,
  `AllergenMilk` tinyint(1) DEFAULT NULL,
  `AllergenPeanut` tinyint(1) DEFAULT NULL,
  `AllergenSeafood` tinyint(1) DEFAULT NULL,
  `AllergenSeed` tinyint(1) DEFAULT NULL,
  `AllergenSoybean` tinyint(1) DEFAULT NULL,
  `AllergenSulfites` tinyint(1) DEFAULT NULL,
  `AllergenTreeNuts` tinyint(1) DEFAULT NULL,
  `AllergenWheat` tinyint(1) DEFAULT NULL,
  `AllergenYellow` tinyint(1) DEFAULT NULL,
  `Appearance` longtext,
  `Ash` double(15,5) DEFAULT NULL,
  `BatchSize` varchar(50) DEFAULT NULL,
  `BatchSizeKg` double(15,5) DEFAULT NULL,
  `Biotin` double(15,5) DEFAULT NULL,
  `BoilingPoint` varchar(10) DEFAULT NULL,
  `Calcium` double(15,5) DEFAULT NULL,
  `Calories` double(15,5) DEFAULT NULL,
  `CaloriesFromFat` double(15,5) DEFAULT NULL,
  `Cholesterol` double(15,5) DEFAULT NULL,
  `Copper` double(15,5) DEFAULT NULL,
  `CurrentSellingItem` tinyint(1) DEFAULT '0',
  `DateOfFormulation` datetime DEFAULT NULL,
  `Designation` varchar(100) DEFAULT NULL,
  `DietaryFiber` double(15,5) DEFAULT NULL,
  `DeveloperID` int(10) DEFAULT NULL,
  `EmergencyFirstAidProcedure` longtext,
  `EvaporationRate` varchar(10) DEFAULT NULL,
  `ExtinguishingMedia` longtext,
  `FatCalories` double(15,5) DEFAULT NULL,
  `FEMA_NBR` varchar(15) DEFAULT NULL,
  `FinalProductNotCreatedByAbelei` tinyint(1) DEFAULT '0',
  `FlammableLimits` varchar(25) DEFAULT NULL,
  `Flashpoint` varchar(50) DEFAULT NULL,
  `FlavorAndAroma` longtext,
  `Folate` double(15,5) DEFAULT NULL,
  `FolateFolacinFolicAdic` double(15,5) DEFAULT NULL,
  `GeneralDescriptionOfFormulation` varchar(75) DEFAULT NULL,
  `GMO` longtext,
  `Halal` longtext,
  `Hazard` longtext,
  `HazardousComponents` longtext,
  `HazardousDecomposition` longtext,
  `HazardousPolymerization` tinyint(1) DEFAULT NULL,
  `HazardousPolymerizationConditions` longtext,
  `HealthHazards` longtext,
  `Incompatibility` longtext,
  `Insoluble Fiber` double(15,5) DEFAULT NULL,
  `Intermediary` tinyint(1) DEFAULT NULL,
  `Iodine` double(15,5) DEFAULT NULL,
  `Iron` double(15,5) DEFAULT NULL,
  `Keywords` varchar(255) DEFAULT NULL,
  `Kosher` longtext,
  `KosherStatus` varchar(50) DEFAULT NULL,
  `LabelDeclaration` longtext,
  `Lactose` double(15,5) DEFAULT NULL,
  `LEL` varchar(15) DEFAULT NULL,
  `Magnesium` double(15,5) DEFAULT NULL,
  `Manganese` double(15,5) DEFAULT NULL,
  `ManufacturingInstructions` longtext,
  `MedicalCondition` longtext,
  `MeltingPoint` varchar(10) DEFAULT NULL,
  `MonounsaturatedFat` double(15,5) DEFAULT NULL,
  `MostRecentVendorID` int(10) DEFAULT NULL,
  `Natural_OR_Artificial` varchar(50) DEFAULT NULL,
  `Niacin` double(15,5) DEFAULT NULL,
  `NonFlavorIngredients` longtext,
  `NoteForFormulation` longtext,
  `OldDescriptionDelete` varchar(99) DEFAULT NULL,
  `Organic` tinyint(1) DEFAULT NULL,
  `OtherCarbohydrates` double(15,5) DEFAULT NULL,
  `OtherProtectiveClothing` longtext,
  `Packaging` longtext,
  `PantothenicAcid` double(15,5) DEFAULT NULL,
  `Phosphorus` double(15,5) DEFAULT NULL,
  `PolyunsaturatedFat` double(15,5) DEFAULT NULL,
  `Potassium` double(15,5) DEFAULT NULL,
  `Precautions` longtext,
  `PriceOfMaterial` double(15,5) DEFAULT NULL,
  `ProductType` varchar(10) DEFAULT NULL,
  `ProjectNumber` varchar(6) DEFAULT NULL,
  `ProtectiveGloves` longtext,
  `Protein` double(15,5) DEFAULT NULL,
  `Quality_Sensitive` varchar(1) DEFAULT NULL,
  `QuickScan` varchar(20) DEFAULT NULL,
  `RefractiveIndex` varchar(50) DEFAULT NULL,
  `Riboflavin` double(15,5) DEFAULT NULL,
  `ReplacedBy` varchar(20) DEFAULT NULL,
  `RestrictedAccess` tinyint(1) DEFAULT '0',
  `SaturatedFat` double(15,5) DEFAULT NULL,
  `SaturatedFatCalories` double(15,5) DEFAULT NULL,
  `ShelfLifeInMonths` double(7,2) DEFAULT NULL,
  `SignsAndSymptoms` longtext,
  `Sodium` double(15,5) DEFAULT NULL,
  `SolubilityInWater` longtext,
  `SolubleFiber` double(15,5) DEFAULT NULL,
  `SpecialFirefightingProcedures` longtext,
  `SpecificGravity` double(15,5) DEFAULT NULL,
  `SpecificGravityUnits` varchar(10) DEFAULT NULL,
  `Stability` tinyint(1) DEFAULT NULL,
  `StabilityConditions` longtext,
  `StepsToBeTaken` longtext,
  `StorageAndShelfLife` longtext,
  `SugarAlcohol` double(15,5) DEFAULT NULL,
  `Sugars` double(15,5) DEFAULT NULL,
  `Thiamin` double(15,5) DEFAULT NULL,
  `TotalCarbohydrates` double(15,5) DEFAULT NULL,
  `TotalFat` double(15,5) DEFAULT NULL,
  `TotalSolids` double(15,5) DEFAULT NULL,
  `TransFattyAcids` double(15,5) DEFAULT NULL,
  `UEL` varchar(15) DEFAULT NULL,
  `UnusualFire` longtext,
  `UseLevel` varchar(50) DEFAULT NULL,
  `VaporDensity` varchar(10) DEFAULT NULL,
  `VaporPressure` varchar(10) DEFAULT NULL,
  `VentilatorMechanical` varchar(50) DEFAULT NULL,
  `VentilatorSpecial` varchar(50) DEFAULT NULL,
  `VerifiedYN` tinyint(1) DEFAULT NULL,
  `VitaminA` double(15,5) DEFAULT NULL,
  `VitaminB12` double(15,5) DEFAULT NULL,
  `VitaminB6` double(15,5) DEFAULT NULL,
  `VitaminC` double(15,5) DEFAULT NULL,
  `VitaminD` double(15,5) DEFAULT NULL,
  `VitaminE` double(15,5) DEFAULT NULL,
  `WasteDisposalMethod` longtext,
  `Water` double(15,5) DEFAULT NULL,
  `WeightPerGallon` double(15,5) DEFAULT NULL,
  `WeightPerGallonUnits` varchar(10) DEFAULT NULL,
  `WorkHygienicPractices` longtext,
  `Zinc` double(15,5) DEFAULT NULL,
  `New_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `Delete_This_REC` tinyint(1) DEFAULT '0',
  `Notes` longtext,
  `New_Designation` varchar(100) DEFAULT NULL,
  `OLD_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `OLD_Designation` varchar(100) DEFAULT NULL,
  `OrderTriggerAmount` double(15,5) DEFAULT NULL,
  `MaxTargetAmount` double(15,5) DEFAULT NULL,
  `UnitOfMeasure` varchar(5) DEFAULT NULL,
  KEY `DeveloperID` (`DeveloperID`),
  KEY `Keywords` (`Keywords`),
  KEY `MostRecentVendorID` (`MostRecentVendorID`),
  KEY `PantothenicAcid` (`PantothenicAcid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tmpformulationclone`
--

DROP TABLE IF EXISTS `tmpformulationclone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmpformulationclone` (
  `ProductNumberInternal` varchar(12) NOT NULL,
  `IngredientSEQ` double(7,2) NOT NULL,
  `IngredientProductNumber` varchar(12) DEFAULT NULL,
  `Percentage` double(15,5) DEFAULT NULL,
  `VendorID` int(10) unsigned DEFAULT NULL,
  `Tier` varchar(1) DEFAULT NULL,
  `New_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `NewIngredientProductNumber` varchar(12) DEFAULT NULL,
  `Delete_This_REC` tinyint(1) NOT NULL,
  `OLD_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `OLDIngredientProductNumber` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`ProductNumberInternal`,`IngredientSEQ`),
  KEY `New_ProductNumberInternal` (`New_ProductNumberInternal`),
  KEY `New_ProductNumberInternal1` (`OLD_ProductNumberInternal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tmpformulationdetailandvendorsimp`
--

DROP TABLE IF EXISTS `tmpformulationdetailandvendorsimp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmpformulationdetailandvendorsimp` (
  `ProductNumberInternal` varchar(20) NOT NULL,
  `PriceSheetNumber` int(10) NOT NULL,
  `IngredientProductNumber` varchar(20) NOT NULL,
  `IngredientSEQ` double(7,2) NOT NULL,
  `VendorID` int(11) unsigned DEFAULT NULL,
  `Tier` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tmpformulationdetailandvendorsimp_2`
--

DROP TABLE IF EXISTS `tmpformulationdetailandvendorsimp_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmpformulationdetailandvendorsimp_2` (
  `ProductNumberInternal` varchar(20) NOT NULL,
  `PriceSheetNumber` int(10) NOT NULL,
  `IngredientProductNumber` varchar(20) NOT NULL,
  `IngredientSEQ` double(7,2) NOT NULL,
  `VendorID` int(11) unsigned DEFAULT NULL,
  `Tier` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL DEFAULT '',
  `last_name` varchar(50) NOT NULL DEFAULT '',
  `user_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL DEFAULT '',
  `pass` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(75) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `login_attempts` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lockout_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `address1` varchar(60) DEFAULT NULL,
  `address2` varchar(60) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_salesperson` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendor_address_phones`
--

DROP TABLE IF EXISTS `vendor_address_phones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor_address_phones` (
  `phone_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `address_id` int(11) unsigned NOT NULL,
  `number` varchar(255) NOT NULL,
  `type` tinyint(4) unsigned DEFAULT '1',
  `number_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`phone_id`),
  KEY `address_id` (`address_id`),
  KEY `type` (`type`),
  CONSTRAINT `vendor_address_phones_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `vendor_addresses` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vendor_address_phones_ibfk_2` FOREIGN KEY (`type`) REFERENCES `phone_types` (`type_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendor_addresses`
--

DROP TABLE IF EXISTS `vendor_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor_addresses` (
  `address_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) unsigned NOT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `notes` text,
  `main_location` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(4) unsigned DEFAULT '1',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ship_to_vendor` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`address_id`),
  KEY `type` (`type`),
  KEY `vendor_id` (`vendor_id`),
  CONSTRAINT `vendor_addresses_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vendor_addresses_ibfk_2` FOREIGN KEY (`type`) REFERENCES `address_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendor_contact_phones`
--

DROP TABLE IF EXISTS `vendor_contact_phones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor_contact_phones` (
  `phone_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) unsigned NOT NULL,
  `number` varchar(255) NOT NULL,
  `type` tinyint(4) unsigned DEFAULT '1',
  `number_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`phone_id`),
  KEY `type` (`type`),
  KEY `contact_id` (`contact_id`),
  CONSTRAINT `vendor_contact_phones_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `vendor_contacts` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vendor_contact_phones_ibfk_2` FOREIGN KEY (`type`) REFERENCES `phone_types` (`type_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendor_contacts`
--

DROP TABLE IF EXISTS `vendor_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor_contacts` (
  `contact_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) unsigned NOT NULL,
  `vendor_address_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `suffix` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `email1` varchar(75) DEFAULT NULL,
  `email2` varchar(75) DEFAULT NULL,
  `notes` text,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`contact_id`),
  KEY `customer_id` (`vendor_id`),
  KEY `vendor_address_id` (`vendor_address_id`),
  CONSTRAINT `vendor_contacts_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vendor_contacts_ibfk_2` FOREIGN KEY (`vendor_address_id`) REFERENCES `vendor_addresses` (`address_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1459 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendorproductcodes`
--

DROP TABLE IF EXISTS `vendorproductcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendorproductcodes` (
  `VendorID` int(11) unsigned NOT NULL,
  `ProductNumberInternal` varchar(20) NOT NULL,
  `VendorProductCode` varchar(50) DEFAULT NULL,
  `New_ProductNumberInternal` varchar(20) DEFAULT NULL,
  `OLD_ProductNumberInternal` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`VendorID`,`ProductNumberInternal`),
  KEY `New_ProductNumberInternal` (`New_ProductNumberInternal`),
  KEY `New_ProductNumberInternal1` (`OLD_ProductNumberInternal`),
  KEY `VendorProductCode` (`VendorProductCode`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  CONSTRAINT `vendorproductcodes_ibfk_1` FOREIGN KEY (`VendorID`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vendorproductcodes_ibfk_2` FOREIGN KEY (`ProductNumberInternal`) REFERENCES `productmaster` (`ProductNumberInternal`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendors`
--

DROP TABLE IF EXISTS `vendors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendors` (
  `vendor_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `notes` text,
  `web_address` varchar(255) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`vendor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2589 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `vwinventory`
--

DROP TABLE IF EXISTS `vwinventory`;
/*!50001 DROP VIEW IF EXISTS `vwinventory`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vwinventory` (
  `ProductNumberInternal` varchar(20),
  `LotID` int(11) unsigned,
  `InventoryCount` double(22,5)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vwmaterialpricing`
--

DROP TABLE IF EXISTS `vwmaterialpricing`;
/*!50001 DROP VIEW IF EXISTS `vwmaterialpricing`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vwmaterialpricing` (
  `VendorID` int(11) unsigned,
  `ProductNumberInternal` varchar(20),
  `VendorProductCode` varchar(50),
  `Tier` varchar(1),
  `DateQuoted` datetime,
  `PriceEffectiveDate` datetime,
  `PricePerPound` decimal(19,4),
  `Notes` varchar(100),
  `Volume` varchar(50),
  `Units` varchar(50),
  `ProductPricesKosher` varchar(2),
  `Minimums` varchar(50),
  `Packaging` varchar(50),
  `Designation` varchar(100),
  `Natural_OR_Artificial` varchar(50),
  `Kosher` longtext,
  `vendor_id` int(11) unsigned,
  `vendor_name` varchar(255)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vwinventory`
--

/*!50001 DROP TABLE IF EXISTS `vwinventory`*/;
/*!50001 DROP VIEW IF EXISTS `vwinventory`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vwinventory` AS select `inventorymovements`.`ProductNumberInternal` AS `ProductNumberInternal`,`inventorymovements`.`LotID` AS `LotID`,sum((`inventorymovements`.`Quantity` * `inventorytransactiontypes`.`InventoryMultiplier`)) AS `InventoryCount` from (`inventorymovements` join `inventorytransactiontypes` on((`inventorymovements`.`TransactionType` = `inventorytransactiontypes`.`TransactionID`))) where (`inventorymovements`.`MovementStatus` = 'C') group by `inventorymovements`.`ProductNumberInternal`,`inventorymovements`.`LotID` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwmaterialpricing`
--

/*!50001 DROP TABLE IF EXISTS `vwmaterialpricing`*/;
/*!50001 DROP VIEW IF EXISTS `vwmaterialpricing`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`abelei`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vwmaterialpricing` AS select `vendorproductcodes`.`VendorID` AS `VendorID`,`vendorproductcodes`.`ProductNumberInternal` AS `ProductNumberInternal`,`vendorproductcodes`.`VendorProductCode` AS `VendorProductCode`,`productprices`.`Tier` AS `Tier`,`productprices`.`DateQuoted` AS `DateQuoted`,`productprices`.`PriceEffectiveDate` AS `PriceEffectiveDate`,`productprices`.`PricePerPound` AS `PricePerPound`,`productprices`.`Notes` AS `Notes`,`productprices`.`Volume` AS `Volume`,`productprices`.`Units` AS `Units`,`productprices`.`Kosher` AS `ProductPricesKosher`,`productprices`.`Minimums` AS `Minimums`,`productprices`.`Packaging` AS `Packaging`,`productmaster`.`Designation` AS `Designation`,`productmaster`.`Natural_OR_Artificial` AS `Natural_OR_Artificial`,`productmaster`.`Kosher` AS `Kosher`,`vendors`.`vendor_id` AS `vendor_id`,`vendors`.`name` AS `vendor_name` from (((`productmaster` join `productprices` on((`productmaster`.`ProductNumberInternal` = `productprices`.`ProductNumberInternal`))) join `vendorproductcodes` on(((`vendorproductcodes`.`ProductNumberInternal` = `productprices`.`ProductNumberInternal`) and (`vendorproductcodes`.`VendorID` = `productprices`.`VendorID`)))) join `vendors` on((`vendors`.`vendor_id` = `vendorproductcodes`.`VendorID`))) order by `vendorproductcodes`.`ProductNumberInternal`,`vendorproductcodes`.`VendorID`,`vendorproductcodes`.`VendorProductCode`,`productprices`.`Tier` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-02-18 10:32:50
