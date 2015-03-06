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

$sql = "SELECT distinct client_id, concat(clients.first_name,' ',clients.last_name) as Client, company ";
$sql .= "FROM clients LEFT JOIN companies USING(company_id) ";
$sql .= "WHERE ( clients.first_name LIKE '%$q%' or clients.last_name LIKE '%$q%' ) ";
$sql .= "ORDER BY clients.first_name, clients.last_name ".(0 != $limit ? " LIMIT $limit" : "");

$result = mysql_query($sql, $link) or die ( mysql_error() . " Failed Execute SQL:$sql<br />");;
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		echo $row['Client']."/".$row['company']. "|".$row['client_id']."|".$row["Client"]."|".$row['company']."\n";
	}
}
?>