<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	//header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('global.php');

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

$sql = "SELECT DISTINCT flavor_id, flavor_name FROM flavor_distinct WHERE flavor_id LIKE '%$q%' OR flavor_name LIKE '%$q%'
	ORDER BY flavor_id, flavor_name " . (0 != $limit ? " LIMIT $limit" : "");

$result = mysql_query($sql, $link) or die ( mysql_error() . " Failed Execute SQL:$sql<br />");;
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		echo $row['flavor_id']. " ".$row['flavor_name']. "|".$row['flavor_id']."|".$row['flavor_name']."\n";
		
	}
}
?>