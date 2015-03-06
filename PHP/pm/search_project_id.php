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

$sql = "SELECT distinct project_id, concat(clients.first_name,' ',clients.last_name) as Client, company ";
$sql .= "FROM projects LEFT JOIN clients USING(client_id) LEFT JOIN companies USING(company_id) ";
$sql .= "WHERE ( project_id LIKE '%$q%' ) ";
$sql .= "ORDER BY project_id".(0 != $limit ? " LIMIT $limit" : "");

$result = mysql_query($sql, $link) or die ( mysql_error() . " Failed Execute SQL:$sql<br />");;
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		echo substr($row['project_id'],0,2) ."-". substr($row['project_id'],-3). " ". $row['Client']."/".$row['company']. "|".$row['project_id']."|".$row["Client"]."|".$row['company']."\n";
	}
}
?>