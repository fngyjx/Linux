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

$sql = "SELECT productmaster.ProductNumberInternal, productmaster.Keywords ";
$sql .= "FROM externalproductnumberreference RIGHT JOIN productmaster ON externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal ";
$sql .= "WHERE ( ( ( ( productmaster.ProductNumberInternal ) LIKE '2%' ) OR ( ( productmaster.ProductNumberInternal ) LIKE '5%' ) ) AND ( ( productmaster.Keywords ) LIKE '%$q%' ) ) ";
$sql .= "ORDER BY if( Mid( externalproductnumberreference.ProductNumberExternal, 1, 2 ) = 'US', externalproductnumberreference.ProductNumberExternal, BuildExternalSortKeyField1( externalproductnumberreference.ProductNumberExternal ) ) , ";
$sql .= "if( Mid( externalproductnumberreference.ProductNumberExternal, 4, 1 ) = 'a', 0, externalproductnumberreference.ProductNumberExternal ) , ";
$sql .= "BuildExternalSortKeyField3( externalproductnumberreference.ProductNumberExternal), ";
$sql .= "BuildExternalSortKeyField4( externalproductnumberreference.ProductNumberExternal)".(0 != $limit ? " LIMIT $limit" : "");

$result = mysql_query($sql, $link);
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
			echo $row["Keywords"]."|".$row["ProductNumberInternal"]."\n";
	}
}
?>