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

$q = strtolower(escape_data($_REQUEST["q"]));
$items = array();

$sql = "SELECT DISTINCT productmaster.ProductNumberInternal, productmaster.SpecificGravity, productmaster.SpecificGravityUnits, productmaster.Organic, ";
$sql .= "productmaster.Natural_OR_Artificial, productmaster.Designation, productmaster.ProductType, productmaster.Kosher, externalproductnumberreference.ProductNumberExternal ";
$sql .= "FROM externalproductnumberreference RIGHT JOIN productmaster ON externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal ";
$sql .= "WHERE productmaster.ProductNumberInternal LIKE '$q%' ";
$sql .= "ORDER BY ProductNumberInternal".(0 != $limit ? " LIMIT $limit" : "");

$result = mysql_query($sql, $link);
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
			$description = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "");
		    $description .= $row['Designation'];
		    $description .= ("" != $row['ProductType'] ? " - ".$row['ProductType'] : "");
		    $description .= ("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
		    $description .= ",&nbsp;&nbsp; External#: ".$row["ProductNumberExternal"];		
		
			echo $row["ProductNumberInternal"]."&nbsp;&nbsp;&nbsp;&nbsp;".$description."\n";
	}
}
?>