<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	//header ("Location: login.php?out=1");
	exit;
}

include('../inc_global.php');
if (!isset($_REQUEST["c_id"]))
{
	$_REQUEST["c_id"] = "";
} 
$c_id = strtolower(escape_data($_REQUEST["c_id"]));

// if (!isset($_REQUEST["q"]))
// {
	// $_REQUEST["q"] = "";
// } 

// $limit = 0;
// if (isset($_REQUEST["limit"]))
// {
	// if (is_numeric($_REQUEST["limit"]))
	// {
		// $limit = $_REQUEST["limit"];
	// }
// } 

//$q = strtolower(escape_data($_REQUEST["q"]));

$sql = "SELECT purchaseorderdetail.ID, purchaseorderdetail.PurchaseOrderNumber, purchaseorderdetail.PurchaseOrderSeqNumber, purchaseorderdetail.ProductNumberInternal, productmaster.Designation, ";
$sql .= "vendors.name, purchaseorderdetail.Quantity, purchaseorderdetail.PackSize, purchaseorderdetail.UnitOfMeasure, purchaseorderdetail.UnitPrice, ";
$sql .= "purchaseorderdetail.TotalQuantityOrdered, vendors.vendor_id, purchaseorderdetail.VendorProductCode, ";
$sql .= "@total_received_grams := (TotalPOAmtReceived( `purchaseorderdetail`.`ID`, NULL )) AS total_received_grams, ";
$sql .= "@total_received_native := (QuantityConvert(@total_received_grams, 'grams', `purchaseorderdetail`.UnitOfMeasure)) AS total_received_native, ";
$sql .= "@total_grams := (QuantityConvert(`purchaseorderdetail`.`TotalQuantityOrdered`, `purchaseorderdetail`.UnitOfMeasure, 'grams')) AS total_grams,";
$sql .= "QuantityConvert((@total_grams - @total_received_grams), 'grams', `purchaseorderdetail`.UnitOfMeasure) / purchaseorderdetail.PackSize AS OpenQuantity_native, ";
$sql .= "@total_grams - @total_received_grams AS OpenQuantity_grams ";
$sql .= "FROM `purchaseorderdetail` LEFT JOIN `purchaseordermaster` ON (`purchaseorderdetail`.`PurchaseOrderNumber` = `purchaseordermaster`.`PurchaseOrderNumber`) ";
$sql .= "LEFT JOIN `productmaster` ON (`purchaseorderdetail`.`ProductNumberInternal` = `productmaster`.`ProductNumberInternal`) ";
$sql .= "LEFT JOIN `vendors` ON (`purchaseordermaster`.`VendorId`=`vendors`.`vendor_id`) ";
$sql .= "WHERE (`purchaseorderdetail`.`ProductNumberInternal` NOT LIKE '7%' AND `purchaseorderdetail`.`IntermediarySentToVendor` = 0 AND `purchaseorderdetail`.`Status` = 'O') ";
if ("" != $c_id) { $sql .= " OR purchaseorderdetail.ID=$c_id "; }
$sql .= "HAVING total_received_grams < total_grams";
if ("" != $c_id) { $sql .= " OR purchaseorderdetail.ID=$c_id "; }
// $sql .= "ORDER BY purchaseorderdetail.PurchaseOrderNumber ASC";

$result = mysql_query($sql, $link);
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
//	print_r($row); 
		echo "PO:".$row["PurchaseOrderNumber"]."(".$row["PurchaseOrderSeqNumber"].")"." - Prod#:".$row["ProductNumberInternal"]." - Desig.:".$row["Designation"]." - Vendor:".$row["name"]." - Quantity:".$row["Quantity"]." - Pack Size:".$row["PackSize"]." - Units:".$row["UnitOfMeasure"]." - Unit Price:".$row["UnitPrice"]." - Tot. Ordered:".$row["TotalQuantityOrdered"]." - Tot. Received:".$row['total_received_native']."|".$row["PurchaseOrderNumber"]."|".$row["PurchaseOrderSeqNumber"]."|".$row["ProductNumberInternal"]."|".$row["Designation"]."|".$row["vendor_id"]."|".$row["name"]."|".$row["VendorProductCode"]."|".$row["OpenQuantity_native"]."|".$row["PackSize"]."|".$row["UnitOfMeasure"]."|".$row["ID"]."\n";
		//echo "<p>Quantity / Pack Size / Units:".$row['Quantity']." / ".$row['PackSize']." / ".$row['UnitOfMeasure']."<br/>total_ordered_native:".$row['TotalQuantityOrdered']."<br/>total_ordered_grams:".$row['total_grams']."<br/>total_received_native:".$row['total_received_native']."<br/>total_received_grams:".$row['total_received_grams']."<br/>OpenQuantity_native:".$row['OpenQuantity_native']."<br/>OpenQuantity_grams:".$row['OpenQuantity_grams']."<br/></p>";
	}
}
?>