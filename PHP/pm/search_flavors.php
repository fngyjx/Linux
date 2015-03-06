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
$f_id = strtolower(escape_data($_REQUEST["f_id"]));
$project_id = strtolower(escape_data($_REQUEST["project_id"]));
$items = array();

$sql = "SELECT DISTINCT flavor_id, flavor_name, IF(lot_code!='', lot_code, concat(SUBSTRING(project_id,1,2),'-',SUBSTRING(project_id,-3),'-',date_created) ) as lot_code, expiration_date
 FROM flavors LEFT JOIN projects USING(project_id) WHERE ( flavor_id = '".$q."' OR flavor_id = '".$f_id."' )
 AND expiration_date > CURRENT_DATE
ORDER BY flavor_id, flavor_name " . (0 != $limit ? " LIMIT $limit" : "");

$result = mysql_query($sql, $link) or die ( mysql_error() . " Failed Execute SQL:$sql<br />");;
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		echo $row['flavor_id']. " ".$row['flavor_name']. " ".$row['lot_code']. "|".$row['lot_code']."|".date("m/d/Y",strtotime($row['expiration_date']))."\n";
		
	}
} else {
//print_r($_REQUEST);
	echo substr($project_id,0,2)."-".substr($project_id,-3)."-".date("Y-m-d")."|".substr($project_id,0,2)."-".substr($project_id,-3)."-".date("Y-m-d")."|".date("m/d/Y"). "\n";
	echo " \n";
}
?>