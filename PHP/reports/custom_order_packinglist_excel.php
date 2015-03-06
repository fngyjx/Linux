<?php
/**
 * production_batch_sheet_excel.php
 *
 * Copyright (C) 2009 ZhongqiuDu.com
 *
 * This is the substitue php tool of production_batch_sheet.php
 * It generate the batch sheet in excel file with landscape as the default orientation.
 * It is triggered by click on the Print button on BatchSheet page (customer_batch_sheets.php)
 * It also be used as a stand along tool to generate excel file of batch sheet
 * 
 * e.g.http://abelei.com/reports/production_batch_sheet_excel?bsn=123456789
 
 */
include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

include("../inc_global.php");
 
$order_num=( isset($_REQUEST['order_num'] ) ) ? escape_data($_REQUEST['order_num']) : "";

if ( $order_num == "" ) {
	echo "Please provide a valid customer order number <br />";
	exit();
}

$note = $_SESSION['note'];
if ( $note != "" )
	echo $note;
	
$sql = "SELECT distinct cod.CustomerCodeNumber, cod.Quantity, cod.PackSize, cod.UnitOfMeasure, cod.description, 
 cod.ProductNumberInternal,  bm.ProductNumberExternal,bm.ProductDesignation,bm.Yield,
customerordermaster.CustomerID,customerordermaster.BillToLocationID,customerordermaster.ShipToLocationID, customerordermaster.CustomerPONumber,customerordermaster.Kosher,
bsci.PackIn,bsci.NumberOfPackages,bsci.PackInID,
concat(lots.LotNumber, '-', lots.LotSequenceNumber) as lotNumber, lots.DateManufactured,lots.ExpirationDate,
pm.Organic,pm.Halal,pm.Hazard,pm.designation,pm.Natural_OR_Artificial
FROM customerorderdetail as cod
LEFT JOIN customerorderdetaillotnumbers as codlot ON cod.CustomerOrderNumber=codlot.CustomerOrderNumber and
cod.ProductNumberInternal=codlot.ProductNumberInternal and cod.CustomerOrderSeqNumber=codlot.CustomerOrderSeqNumber
LEFT JOIN lots on codlot.LotID=lots.ID 
LEFT JOIN batchsheetmaster as bm ON bm.ProductNumberInternal=cod.ProductnumberInternal
LEFT JOIN customerordermaster on customerordermaster.OrderNumber=cod.CustomerOrderNumber
JOIN batchsheetcustomerinfo as bsci ON bsci.BatchSheetNumber=bm.BatchSheetNumber AND
  bsci.CustomerOrderNumber=cod.CustomerOrderNumber AND
  bsci.CustomerOrderSeqNumber=cod.CustomerOrderSeqNumber
LEFT JOIN productmaster as pm on pm.ProductNumberInternal = cod.ProductNumberInternal
WHERE cod.CustomerOrderNumber = " . $order_num;

$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
//echo $sql . "<BR>";

$lotNumber = $row['lotNumber'];
$ProductNumberExternal = $row['ProductNumberExternal'];
$ProductNumberInternal = $row['ProductNumberInternal'];
$ProductDesignation = $row['ProductDesignation'];
$PackIn = $row['PackIn'];
$UnitOfMeasure = $row['UnitOfMeasure'];
$Description = ( $row['description'] == "" ? $ProductDesignation : $row['description'] );

$mkDate = date('n-j-y',strtotime($row['DateManufactured']));
$expDate = date('n-j-y',strtotime($row['ExpirationDate']));

$sql="SELECT name, concat(address1,' ',address2) as address, concat(city,', ',state,' ',zip) as city
	FROM customers,customer_addresses WHERE customers.customer_id=customer_addresses.customer_id AND customers.customer_id=".$row['CustomerID'] ." AND customer_addresses.address_id=".$row['ShipToLocationID'];
$result_shipto=mysql_query($sql,$link) or die(mysql_error() . " Failed execute SQL: $sql<br />");
$row_shipto=mysql_fetch_array($result_shipto);
$shipToAddr=$row_shipto['address'];
$shipToCity=$row_shipto['city'];
$shipToName=$row_shipto['name'];

$sql="SELECT concat(address1,' ',address2) as address, concat(city,', ',state,' ',zip) as city
	FROM customer_addresses WHERE customer_addresses.address_id=".$row['BillToLocationID'];

$result_billto=mysql_query($sql,$link) or die(mysql_error() . " Failed execute SQL: $sql<br />");
$row_billto=mysql_fetch_array($result_billto);
$billToAddr=$row_billto['address'];
$billToCity=$row_billto['city'];
$billToName=$shipToName;

///include ("BatchsheetMasterSql.php");
include ("custom_orderpackinglist_excel.inc.php");

/** PHPExcel_IOFactory */
require_once '../PHPExcel/IOFactory.php';

// Save Excel 2007 file
echo date('H:i:s') . " Write to Excel5 format\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$save_file_name="bs_" . $bsn .".xls";
//remove the file if it exists
unlink($save_file_name);

$objWriter->save($save_file_name);

// Echo memory peak usage
echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

// Echo done
echo date('H:i:s') . " Done writing file.\r\n";

echo "<script text/javascript>";
echo "document.location.href('$save_file_name');";
echo "</script>";

//unlink($save_file_name);
//exit();