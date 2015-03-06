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


$sql = "SELECT DISTINCT externalproductnumberreference.ProductNumberExternal,";
$sql .= " productmaster.Designation, ";
$sql .= "productmaster.Natural_OR_Artificial, ";
$sql .= "productmaster.ProductType, ";
$sql .= "productmaster.Kosher, ";
$sql .= "productmaster.ProductNumberInternal, ";
$sql .= "productmaster.Currentsellingitem, ";
$sql .= "productmaster.ReplacedBy ";
$sql .= "FROM externalproductnumberreference RIGHT JOIN productmaster ON externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal ";
$sql .= "WHERE ( ( ( ( productmaster.ProductNumberInternal ) LIKE '2%' ) OR ( ( productmaster.ProductNumberInternal ) LIKE '5%' ) ) AND ( ( productmaster.Designation ) LIKE '%$q%' ) ) AND ProductNumberExternal != ''";
$sql .= "ORDER BY if( Mid( externalproductnumberreference.ProductNumberExternal, 1, 2 ) = 'US', externalproductnumberreference.ProductNumberExternal, BuildExternalSortKeyField1( externalproductnumberreference.ProductNumberExternal ) ) , ";
$sql .= "if( Mid( externalproductnumberreference.ProductNumberExternal, 4, 1 ) = 'a', 0, externalproductnumberreference.ProductNumberExternal ) , ";
$sql .= "BuildExternalSortKeyField3( externalproductnumberreference.ProductNumberExternal), ";
$sql .= "BuildExternalSortKeyField4( externalproductnumberreference.ProductNumberExternal)".(0 != $limit ? " LIMIT $limit" : "");

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