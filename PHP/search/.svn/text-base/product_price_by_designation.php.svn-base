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

$VendorID = ( isset($_REQUEST["VendorID"]) ) ?  $_REQUEST["VendorID"] : "";

$vend_clause=( $VendorID == "" ) ? "" : " AND vendors.vendor_id = '$VendorID' ";

$q = strtolower(escape_data($_REQUEST["q"]));
$items = array();

$sql = "SELECT DISTINCT pm.ProductNumberInternal as pni,pm.Designation, epnr.ProductNumberExternal, pp.*, name
	FROM productmaster as pm LEFT JOIN productprices AS pp ON pp.ProductNumberInternal = pm.ProductNumberInternal 
	LEFT JOIN externalproductnumberreference AS epnr ON ( epnr.ProductNumberInternal = pm.ProductNumberInternal )
	LEFT JOIN vendors on pp.VendorID=vendors.vendor_id
	WHERE pm.Designation LIKE '%$q%' AND pp.is_deleted = 0 and vendors.name NOT LIKE '%Abelei%' ". $vend_clause;

$result = mysql_query($sql, $link);
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		$dsp_msg=$row["Designation"];
		$dsp_msg .= "&nbsp;&nbsp;Ext#:".$row["ProductNumberExternal"];
		$dsp_msg .= "&nbsp;&nbsp;Vendor:".$row["name"];
		$dsp_msg .= "&nbsp;&nbsp;Kosher:".$row["Kosher"];
		$dsp_msg .= "&nbsp;&nbsp;Price:".$row["PricePerPound"];
		$dsp_msg .= "&nbsp;&nbsp;Volume:".$row["Volume"];
		$dsp_msg .= "\n";
		echo $dsp_msg;
	}
}
?>