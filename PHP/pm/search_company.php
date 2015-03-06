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

if ( $_SESSION['userTypeCookie'] == 2 ) {
	$company_clause = " WHERE user_id = " . $_SESSION['user_id'];
	$sql = "SELECT DISTINCT company, company_id FROM companies LEFT JOIN companies_users USING(company_id) " . $company_clause . "
		AND company LIKE '%$q%' ORDER BY company" . (0 != $limit ? " LIMIT $limit" : "");
} else {
		$sql = "SELECT DISTINCT company, company_id FROM companies WHERE company LIKE '%$q%' ORDER BY company" . (0 != $limit ? " LIMIT $limit" : "");
}

$result = mysql_query($sql, $link) or die ( mysql_error() . " Failed Execute SQL:$sql<br />");;
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		echo $row['company']. "|".$row['company_id']."\n";
	}
}
?>