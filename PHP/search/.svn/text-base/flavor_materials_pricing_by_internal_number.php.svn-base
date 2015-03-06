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

$sql = "SELECT distinct ProductNumberInternal ";
$sql .= "FROM vwmaterialpricing ";
$sql .= "WHERE ( ProductNumberInternal LIKE '%$q%' ) ";
$sql .= "ORDER BY ProductNumberInternal".(0 != $limit ? " LIMIT $limit" : "");

$result = mysql_query($sql, $link) or die (mysql_error() . " Failed execute SQL:$sql<br />");
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		echo $row["ProductNumberInternal"]."\n";
	}
}
?>