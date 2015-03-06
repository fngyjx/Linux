<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN AND FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 6 ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');

print_r($_REQUEST);

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( $_REQUEST['ipn'] != '' ) {
	$ipn = escape_data($_REQUEST['ipn']);
} else {
	$note .= " Productnumber is must to have. <br />";
	echo "<SCRIPT>window.opener.location.reload();window.close90;</SCRIPT>";
	exit;
}

if ( $_REQUEST['iseq'] != '' ) {
	$iseq = escape_data($_REQUEST['iseq']);
} else {
	$note .= " Productnumber is must to have. <br />";
	echo "<SCRIPT>window.opener.location.reload();window.close90;</SCRIPT>";
	exit;
}

if ( isset($_REQUEST['bsn']) ) {
	$bsn = escape_data($_REQUEST['bsn']);
} else {
	$note .= " Batch Sheet number is must to have. <br />";
	echo "<SCRIPT>window.opener.location.reload();window.close();</SCRIPT>";
	exit;
}

if ( isset($_REQUEST['key']) ) {
	$key = escape_data($_REQUEST['key']);
} else {
	$note .= "Key Batch Sheet is must to have. <br />";
	echo "<SCRIPT>window.opener.location.reload();window.close();</SCRIPT>";
	exit;
}
// add key to the batchsheet
$sql = "UPDATE batchsheetdetail SET SubBatchSheetNumber='".$key."' WHERE BatchSheetNumber='" .$bsn . "' AND IngredientProductNumber='" .$ipn ."' AND IngredientSEQ='". $iseq."'";
mysql_query($sql,$link) or die (mysql_error() . " Failed Execute : $sql <br />");

//add batchsheet key's amount to the key if the key's amount is not able to cover the batchsheet needs
$sql = "SELECT NetWeight, TotalQuantity, TotalQuantityUnitType FROM batchsheetmaster WHERE BatchSheetNumber='".$key."'";
$result_key=mysql_query($sql,$link) or die ( mysql_error() ." Failed to execute SQL : $sql <br />");
if ( mysql_num_rows($result_key) != 1 ) {
	die ( "Something is wrong in the results of SQL : $sql <br />");
}

$row_key=mysql_fetch_array($result_key);
$sql = "SELECT sum(QuantityConvert(batchsheetmaster.NetWeight,TotalQuantityUnitType,'grams')*Percentage*0.01*NumberOfTimesToMake/batchsheetmaster.Yield) FROM batchsheetdetail 
	LEFT JOIN batchsheetmaster USING(BatchSheetNumber)
	WHERE IngredientProductNumber='".$ipn."' AND batchsheetdetail.subbatchsheetnumber='". $key ."'";

$result_bsamt=mysql_query($sql,$link) or die ( mysql_error() ." Faile Execute SQL : $sql <br />");
$row_bsamt=mysql_fetch_array($result_bsamt);
echo "batchamt sql: $sql <br />";
if ( (QuantityConvert($row_bsamt[0],'grams',$row_key['TotalQuantityUnitType'] ) - $row_key['NetWeight']) > 0.01 ) {
	$sql="UPDATE batchsheetmaster SET NetWeight = '". QuantityConvert($row_bsamt[0],'grams',$row_key['TotalQuantityUnitType'] ) ."'
		WHERE BatchSheetNumber='".$key."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
}
	
echo "<SCRIPT>window.opener.location.reload();window.open('','_self'); window.close();</SCRIPT>";
exit();


