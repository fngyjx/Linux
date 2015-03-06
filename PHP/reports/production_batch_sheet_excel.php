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
 
$bsn=( isset($_REQUEST['bsn'] ) ) ? escape_data($_REQUEST['bsn']) : "";

if ( $bsn == "" ) {
	echo "Please provide a valid batch sheet number <br />";
	exit();
}

$note = $_SESSION['note'];
if ( $note != "" )
	echo $note;
	
$sql = "SELECT batchsheetmaster.*, lots.QualityControlEmployeeID, lots.DateManufactured, lots.ExpirationDate, lots.QualityControlDate FROM batchsheetmaster
LEFT JOIN lots ON batchsheetmaster.LotID = lots.ID
WHERE BatchSheetNumber = $_REQUEST[bsn]";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
//echo $sql . "<BR>";

$LotID = $row['LotID'];
$ProductNumberExternal = $row['ProductNumberExternal'];
$ProductNumberInternal = $row['ProductNumberInternal'];
$ProductDesignation = $row['ProductDesignation'];
$NetWeight = $row['NetWeight'];
$TotalQuantityUnitType = $row['TotalQuantityUnitType'];
$Column1UnitType = $row['Column1UnitType'];
$Column2UnitType = $row['Column2UnitType'];
$Yield = $row['Yield'];
$NumberOfTimesToMake = $row['NumberOfTimesToMake'];
$Allergen = $row['Allergen'];
$Kosher = $row['Kosher'];
$Vessel = $row['Vessel'];
$ScaleNumber = $row['ScaleNumber'];
$MadeBy = $row['MadeBy'];
$Filtered = $row['Filtered'];
$QualityControlEmployeeID = $row['QualityControlEmployeeID'];
$CommitedToInventory = $row['CommitedToInventory'];
$Manufactured = $row['Manufactured'];
$InventoryMovementRemarks = $row['InventoryMovementRemarks'];
//$abeleiLotNumber = $row['abeleiLotNumber'];
//$LotSequenceNumber = $row['LotSequenceNumber'];
$Notes = $row['Notes'];


if ( $NetWeight != 0 and $Yield != 0 ) {
	$gross_weight = ($NetWeight/$Yield);
} else {
	$gross_weight = 0.00;
}

if ( $row['DueDate'] != '' ) {
	$DueDate = date("n/j/Y", strtotime($row['DueDate']));
} else {
	$DueDate = '';
}

if ( $row['DateManufactured'] != '' ) {
	$DateManufactured = date("n/j/Y", strtotime($row['DateManufactured']));
} else {
	$DateManufactured = '';
}

if ( $row['ExpirationDate'] != '' ) {
	$ExpirationDate = date("n/j/Y", strtotime($row['ExpirationDate']));
} else {
	$ExpirationDate = '';
}

if ( $row['QualityControlDate'] != '' ) {
	$QualityControlDate = date("n/j/Y", strtotime($row['QualityControlDate']));
} else {
	$QualityControlDate = 'None entered yet';
}

if ( $QualityControlEmployeeID != '' and $QualityControlEmployeeID != 0 ) {
	$sql = "SELECT first_name, last_name FROM users WHERE user_id = " . $QualityControlEmployeeID;
	$result = mysql_query($sql, $link);
	if ( mysql_num_rows($result) > 0 ) {
		while ( $row = mysql_fetch_array($result) ) {
			$QualityControlEmployee = $row['first_name'] . " " . $row['last_name'];
		}
	}
} else {
	$QualityControlEmployee = 'None entered yet';
}

///include ("BatchsheetMasterSql.php");
include ("production_batch_sheet_excel.inc.php");

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
echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) * 0.00098 * 0.00098) . " MB\r\n";

// Echo done
echo date('H:i:s') . " Done writing file.\r\n";

echo "<script text/javascript>";
echo "document.location.href('$save_file_name');";
echo "</script>";

//unlink($save_file_name);
//exit();