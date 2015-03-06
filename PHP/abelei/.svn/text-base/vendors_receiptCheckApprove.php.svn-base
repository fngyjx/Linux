<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	exit;
}

// ADMIN and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');

global $link;

$po_id="";
$quantity="";
$units="";
if ( isset($_REQUEST['po_id']) ) { $po_id=escape_data($_REQUEST['po_id']); }
if ( isset($_REQUEST['quantity']) ) { $quantity=escape_data($_REQUEST['quantity']); }
if ( isset($_REQUEST['units']) ) { $units=escape_data($_REQUEST['units']); }

if (is_numeric($po_id) && is_numeric($quantity) && ("grams"==strtolower($units) || "lbs"==strtolower($units) || "kg"==strtolower($units) ) ) {
	$sql="SELECT QuantityConvert(TotalQuantityExpected, UnitOfMeasure, 'grams') FROM purchaseorderdetail WHERE ID='$po_id'";
	$result = mysql_query($sql, $link) or die (mysql_error());
	$expected = mysql_result($result,0);

	$sql = "SELECT TotalPOAmtReceived ($po_id, 'C')";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$received = mysql_result($result,0);

	$sql="SELECT QuantityConvert($quantity, '$units', 'grams')";
	$result = mysql_query($sql, $link) or die (mysql_error());
	$pending = mysql_result($result,0);
	
	if ( $pending + $received > $expected) {
		echo "WARNING: The TOTAL quantity received is greater than the TOTAL quantity expected! If you approve, the total quantitiy expected will be updated to reflect the new amount received.";
	} 
	else
	if ( $pending + $received < $expected) {
		echo "WARNING: The TOTAL quantity received is less than the TOTAL quantity expected! Only approve if there is another shipment or multiple lots.";
	}
}

?>