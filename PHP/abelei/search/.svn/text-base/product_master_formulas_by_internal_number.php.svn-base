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

$sql = "SELECT ProductMaster.ProductNumberInternal ";
$sql .= "FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal ";
$sql .= "WHERE ( ( ( ( ProductMaster.ProductNumberInternal ) LIKE '2%' ) OR ( ( ProductMaster.ProductNumberInternal ) LIKE '5%' ) ) AND ( ( ProductMaster.ProductNumberInternal ) LIKE '%$q%' ) ) ";
$sql .= "ORDER BY if( Mid( ExternalProductNumberReference.ProductNumberExternal, 1, 2 ) = 'US', ExternalProductNumberReference.ProductNumberExternal, BuildExternalSortKeyField1( ExternalProductNumberReference.ProductNumberExternal ) ) , ";
$sql .= "if( Mid( ExternalProductNumberReference.ProductNumberExternal, 4, 1 ) = 'a', 0, ExternalProductNumberReference.ProductNumberExternal ) , ";
$sql .= "BuildExternalSortKeyField3( ExternalProductNumberReference.ProductNumberExternal), ";
$sql .= "BuildExternalSortKeyField4( ExternalProductNumberReference.ProductNumberExternal)".(0 != $limit ? " LIMIT $limit" : "");

$result = mysql_query($sql, $link);
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		echo $row["ProductNumberInternal"]."\n";
	}
}
?>