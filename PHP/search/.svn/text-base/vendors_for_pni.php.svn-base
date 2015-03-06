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

$limit = "";
if (isset($_REQUEST[limit]))
{
	if (is_numeric($_REQUEST[limit]))
	{
		$limit = "LIMIT $_REQUEST[limit]";
	}
}

$q = isset($_REQUEST[q]) ? strtolower($_REQUEST[q]) : "";
$pni = isset($_REQUEST[pni]) ? $_REQUEST[pni] : "";

$sql = "SELECT 
			DISTINCT productprices.VendorID, vendors.name 
			FROM productprices 
				LEFT JOIN vendors ON productprices.VendorID = vendors.vendor_id 
			WHERE name LIKE ('%$q%') AND 
				productprices.ProductNumberInternal LIKE ('%$pni%') 
			ORDER BY name ASC $limit";
$result = mysql_query($sql, $link);
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		echo "$row[name]|$row[VendorID]\n";
	}
}
?>