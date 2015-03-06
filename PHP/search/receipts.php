<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	//header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('../inc_global.php');
if (!isset($_REQUEST["q"]))
{
	$_REQUEST["q"] = "";
} 

$limit = 0;
if (isset($_REQUEST["limit"]))
{
	if (is_numeric($_REQUEST["limit"]))
	{
		$limit = $_REQUEST["limit"];
	}
} 

$Pending="";
if (isset($_REQUEST["Pending"]))
{
	$Pending=$_REQUEST["Pending"];
} 
$Approved="";
if (isset($_REQUEST["Approved"]))
{
	$Approved=$_REQUEST["Approved"];
} 
$Rejected="";
if (isset($_REQUEST["Rejected"]))
{
	$Rejected=$_REQUEST["Rejected"];
} 

$q = strtolower(escape_data($_REQUEST["q"]));

$clause="";
if ("on"==$Pending) {
	$clause .= "receipts.Status='P' ";
}
if ("on"==$Approved) {
	if ("on"==$Pending) { $clause .="OR "; }
	$clause .= "receipts.Status='A' ";
}
if ("on"==$Rejected) {
	if ( "on"==$Pending || "on"==$Approved ) { $clause .="OR ";}
	$clause .= "receipts.Status='R' ";
}

$sql  = "SELECT `receipts`.`ID` as RID,`receipts`.`PurchaseOrderID`, `receipts`.`DateReceived`, purchaseorderdetail.*, productmaster.*, purchaseordermaster.*, vendors.* ".
	"FROM receipts LEFT JOIN purchaseorderdetail ON (receipts.PurchaseOrderID=purchaseorderdetail.ID) ".
	"LEFT JOIN productmaster ON (purchaseorderdetail.ProductNumberInternal = productmaster.ProductNumberInternal) ".
	"LEFT JOIN purchaseordermaster ON (purchaseordermaster.PurchaseOrderNumber=purchaseorderdetail.PurchaseOrderNumber) ".
	"LEFT JOIN vendors ON (vendors.vendor_id=purchaseordermaster.VendorID) ".
	"WHERE (Designation LIKE '%$q%'  OR name LIKE '%$q%') ".
	( "" != $clause ?  " AND ( $clause ) " : "" )."ORDER BY purchaseorderdetail.PurchaseOrderNumber".(0 != $limit ? " LIMIT $limit" : "");

//echo $sql;
$result = mysql_query($sql, $link);
$result_count = mysql_num_rows($result);
if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
	// echo "<p>".print_r($row)."</p>";
			echo "PO:".$row["PurchaseOrderNumber"]." DateReceived: ".$row["DateReceived"]." - Desig.:".$row["Designation"]." - Vendor:".$row["name"]." - Prod#:".$row["ProductNumberInternal"]." - Lot#:".$row["LotNumber"]." - Lot Seq#:".$row["LotSequenceNumber"]." - Quantity:".$row["Quantity"]." - Pack Size:".$row["PackSize"]." - Units:".$row["UnitOfMeasure"]."|".$row["RID"]."\n";
	}
}
?>