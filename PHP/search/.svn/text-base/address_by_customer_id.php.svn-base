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
if (!isset($_REQUEST["c_id"]))
{
	$_REQUEST["c_id"] = "";
} 

$limit = 0;
if (isset($_REQUEST["limit"]))
{
	if (is_numeric($_REQUEST["limit"]))
	{
		$limit = $_REQUEST["limit"];
	}
} 

$c_id = strtolower(escape_data($_REQUEST["c_id"]));
if ("" != $c_id)
{

	$sql = "SELECT DISTINCT address_id, address1, address2,city,state,zip FROM customer_addresses
	WHERE customer_id=$c_id AND customer_addresses.active = 1 ORDER BY address1 ASC".(0 != $limit ? " LIMIT $limit" : "");
	$result = mysql_query($sql, $link);
	$result_count = mysql_num_rows($result);
	if (0 < $result_count)
	{
		while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
			
			$id = $row['address_id'];
			$address= $row['address1']. " " . $row['address2'] ."- " .$row['city'] . "- " .$row['state'] ." " .$row['zip'];
			echo "$address|$id|".$row['address1']."|".$row['address2']."|".$row['city']."|".$row['state']."|".$row['zip']."\n";
		}
	}
}
else
{
	echo "no contacts selected||||||";
}

?>