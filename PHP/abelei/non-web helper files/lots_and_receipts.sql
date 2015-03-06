-- phpMyAdmin SQL Dump
-- version 2.10.0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 05, 2009 at 07:33 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `abelei`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `lots_and_receipts`
-- 

CREATE TABLE `lots_and_receipts` (
  `LotNumber` varchar(11) NOT NULL,
  `LotSequenceNumber` int(10) NOT NULL,
  `ProductNumberInternal` varchar(20) default NULL,
  `PurchaseOrderSeqNumber` smallint(5) default NULL,
  `PackSize` double(15,5) default NULL,
  `VendorID` int(10) default NULL,
  `VendorProductCode` varchar(50) default NULL,
  `VendorInvoiceNumber` varchar(50) default NULL,
  `DateManufactured` datetime default NULL,
  `ExpirationDate` datetime default NULL,
  `PackagingType` varchar(100) default NULL,
  `DateReceived` datetime default NULL,
  `UnitOfMeasure` varchar(5) default NULL,
  `Quantity` double(15,5) default NULL,
  `InventoryMovementTransactionNumber` int(10) default NULL,
  `EmployeeID` int(10) default NULL,
  `PurchaseOrderNumber` int(10) default NULL,
  `ConditionOfShipment` varchar(15) default NULL,
  `StorageLocation` varchar(15) default NULL,
  `Location_On_Site` tinyint(1) NOT NULL default '0',
  `C_of_A_attached` tinyint(1) NOT NULL default '0',
  `MSDS_on_file` tinyint(1) NOT NULL default '0',
  `Specifications_on_file` tinyint(1) NOT NULL default '0',
  `Nutrition_on_file` tinyint(1) NOT NULL default '0',
  `Allergen_statement_on_file` tinyint(1) NOT NULL default '0',
  `KosherApproved` tinyint(1) NOT NULL default '0',
  `QualityControlApproval` tinyint(1) NOT NULL default '0',
  `SizeOfRetainTaken` varchar(10) default NULL,
  `Comments` longtext,
  `QualityControlDate` datetime default NULL,
  `QualityControlEmployeeID` int(10) default NULL,
  `QCCofAAvailable` varchar(1) default NULL,
  `QCDateOfStandard` datetime default NULL,
  `QCLotNumberofStandard` varchar(10) default NULL,
  `QCCofAStandardAvailable` varchar(1) default NULL,
  `QCColor` varchar(100) default NULL,
  `QCOdor` varchar(100) default NULL,
  `QCGranulation` varchar(100) default NULL,
  `QCBrix` varchar(100) default NULL,
  `QCMethodForOrganolepticEvaluation` longtext,
  `QCOrganolepticOberservations` longtext,
  `QCMicrobiologicalReportNeeded` varchar(1) default NULL,
  `QCMicrobiologicalReportDate` datetime default NULL,
  `QCMicrobiologicalReportMeetsSpecs` varchar(1) default NULL,
  `QCMicrobiologicalReportDoesNotMeetSpecs` longtext,
  `QCProductMeetsAllSpecs` varchar(1) default NULL,
  `QCComments` longtext,
  `QCMoisture` varchar(100) default NULL,
  `Status` varchar(15) NOT NULL default 'Pending' COMMENT 'values: Pending, Approved, Rejected',
  PRIMARY KEY  (`LotNumber`,`LotSequenceNumber`),
  KEY `EmployeeID` (`EmployeeID`),
  KEY `LotSequenceNumber` (`LotSequenceNumber`),
  KEY `NumberOfUnits` (`Quantity`),
  KEY `ProductNumberInternal` (`ProductNumberInternal`),
  KEY `QualityControlEmployeeID` (`QualityControlEmployeeID`),
  KEY `VendorID` (`VendorID`),
  KEY `VendorProductCode` (`VendorProductCode`),
  KEY `Status` (`Status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `lots_and_receipts`
-- 

INSERT INTO `lots_and_receipts` (`LotNumber`, `LotSequenceNumber`, `ProductNumberInternal`, `PurchaseOrderSeqNumber`, `PackSize`, `VendorID`, `VendorProductCode`, `VendorInvoiceNumber`, `DateManufactured`, `ExpirationDate`, `PackagingType`, `DateReceived`, `UnitOfMeasure`, `Quantity`, `InventoryMovementTransactionNumber`, `EmployeeID`, `PurchaseOrderNumber`, `ConditionOfShipment`, `StorageLocation`, `Location_On_Site`, `C_of_A_attached`, `MSDS_on_file`, `Specifications_on_file`, `Nutrition_on_file`, `Allergen_statement_on_file`, `KosherApproved`, `QualityControlApproval`, `SizeOfRetainTaken`, `Comments`, `QualityControlDate`, `QualityControlEmployeeID`, `QCCofAAvailable`, `QCDateOfStandard`, `QCLotNumberofStandard`, `QCCofAStandardAvailable`, `QCColor`, `QCOdor`, `QCGranulation`, `QCBrix`, `QCMethodForOrganolepticEvaluation`, `QCOrganolepticOberservations`, `QCMicrobiologicalReportNeeded`, `QCMicrobiologicalReportDate`, `QCMicrobiologicalReportMeetsSpecs`, `QCMicrobiologicalReportDoesNotMeetSpecs`, `QCProductMeetsAllSpecs`, `QCComments`, `QCMoisture`, `Status`) VALUES 
('ds', 1, '104500', 1, 20.00000, 2576, 'N/A', 'VendInvNo', '1990-03-01 17:00:04', '2022-03-01 17:00:12', '500 gram container', '2009-02-25 11:24:30', 'kg', 1.00000, 0, 1, 123456789, 'broken boxes', 'Oxy Dry', 1, 1, 1, 1, 1, 1, 1, 1, 'RetSizeWoo', 'Long Comment includfing\r\nLine Breaks\r\nand crap', '1984-03-03 00:00:01', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Rejected'),
('ds', 2, '104500', 31, 230.00000, 1, '', 'UPDATE MY INV', '1990-03-03 00:00:00', '2022-03-04 00:00:00', '50 gram container', '2009-02-27 00:00:00', 'lbs', 11.00000, 0, 4, 123456789, 'Good', 'Warehouse', 1, 1, 1, 1, 1, 1, 1, 1, 'Renny', 'Big Ole monkey', '1984-03-02 00:00:00', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pending'),
('ds', 3, '104500', 1, 20.00000, 2576, 'N/A', 'Updated Invoice', '1990-03-01 17:00:04', '2022-03-01 17:00:12', '18 kilo pail', '2009-02-25 11:24:30', 'kg', 1.00000, 0, 1, 123456789, 'Good', 'Oxy Dry', 0, 1, 1, 1, 1, 1, 1, 1, 'RetSizeWoo', 'Updizated Kizomment', '1984-03-03 00:00:01', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Approved');
-- Functions
-- 

CREATE DEFINER=`csheaff`@`%` FUNCTION `BuildExternalSortKeyField1`(ExternalNumber VARCHAR(20)) RETURNS varchar(20) CHARSET latin1
BEGIN
	DECLARE r VARCHAR(20);
	
	If Mid(ExternalNumber, 1, 2) = "US" THEN 
		SET r = ExternalNumber;
	Else
	   SET r = Mid(ExternalNumber, 1, 3);
	End If;
	RETURN(r);
	END

CREATE DEFINER=`csheaff`@`%` FUNCTION `BuildExternalSortKeyField3`(ExternalNumber VARCHAR(20)) RETURNS int(11)
BEGIN
	DECLARE r INT;
	DECLARE intx INT;
	
	If Left(ExternalNumber, 2) = "US" Then
	   SET r = 0;
	ELSE
		SET intx = 5;
		WHILE IsNumeric( MID(ExternalNumber,intx,1)) DO
			If IsNumeric( MID(ExternalNumber,intx,1)) Then
			   SET r = CONCAT( r, Mid(ExternalNumber, intx, 1) );
			   SET intx = intx + 1;
			End If;
		END WHILE;
	END IF;
	
	RETURN(r);
	End

CREATE DEFINER=`csheaff`@`%` FUNCTION `BuildExternalSortKeyField4`(ExternalNumber VARCHAR(20)) RETURNS varchar(20) CHARSET latin1
BEGIN
	DECLARE intx INT;
	DECLARE r VARCHAR(20);
	
	If Left(ExternalNumber, 2) = "US" Then
	   SET r = "";
	ELSE
		SET intx = 5;
		WHILE IsNumeric( MID(ExternalNumber,intx,1)) DO
			If IsNumeric( MID(ExternalNumber,intx,1)) Then
			   SET intx = intx + 1;
			End If;
		END WHILE;
		SET r = Mid(ExternalNumber, intx, 5);
	END IF;
	
	RETURN(r);
End

CREATE DEFINER=`csheaff`@`%` FUNCTION `IsNumeric`(myVal VARCHAR(1024)) RETURNS tinyint(1)
    DETERMINISTIC
RETURN myVal REGEXP '^(-|\\+)?([0-9]+\\.[0-9]*|[0-9]*\\.[0-9]+|[0-9]+)$'

CREATE DEFINER=`csheaff`@`%` FUNCTION `TotalAmtReceived`(PONo int(10), POSeqNo smallint(5), InternalNo varchar(20)) RETURNS double(15,5)
BEGIN
   DECLARE dblAmtReceived double(15,5);
   SELECT Sum(Quantity*PackSize) INTO dblAmtReceived FROM lots_and_receipts WHERE `Status`='Approved' AND `PurchaseOrderNumber`=PONo And `PurchaseOrderSeqNumber`=POSeqNo AND `ProductNumberInternal`=InternalNo LIMIT 1;
   SET dblAmtReceived=COALESCE(dblAmtReceived,0);
   RETURN(dblAmtReceived);
End

