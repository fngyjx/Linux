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


$sql = "SELECT DISTINCT ".
 " Designation, ".
 "Natural_OR_Artificial, ".
 "ProductType, ".
 "Kosher, ".
 "pm.ProductNumberInternal, ".
 "Currentsellingitem, ".
 "ReplacedBy ".
 "FROM productmaster as pm ".
 " INNER JOIN vendorproductcodes as vpcd on pm.ProductNumberInternal = vpcd.ProductNumberInternal ".
 "WHERE ( ( ( ( pm.ProductNumberInternal ) LIKE '1%' ) OR ".
		" ( ( pm.ProductNumberInternal ) LIKE '2%' ) OR ".
		" ( ( pm.ProductNumberInternal ) LIKE '5%' ) ) ".
		" AND ( ( Designation ) LIKE '%$q%' ) ) ".
 "ORDER BY pm.ProductNumberInternal ";
if ($debug) 
	echo $sql;
$result = mysql_query($sql, $link);
$result_count = mysql_num_rows($result);

if (0 < $result_count)
{
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		$dsp_msg=$row["Designation"];
		$dsp_msg .= "&nbsp;&nbsp;Ext#:".$row["ProductNumberExternal"];
		$dsp_msg .= "&nbsp;&nbsp;NorA:".$row["Natural_OR_Artificial"];
		$dsp_msg .= "&nbsp;&nbsp;Kosher:".$row["Kosher"];
		$dsp_msg .= "&nbsp;&nbsp;PrdTyp:".$row["ProductType"];
		if ( $row['Currentsellingitem'] == 1) 
		    $dsp_msg .= "&nbsp;&nbsp;Current Selling";
		$dsp_msg .= "&nbsp;&nbsp;Int#:".$row["ProductNumberInternal"];
		if ( !empty($row["ReplacedBy"]))
		   $dsp_msg .= "&nbsp;&nbsp;Rplcd by:".$row["ReplacedBy"];
		$dsp_msg .= "\n";
		echo $dsp_msg;
	}
}
?>