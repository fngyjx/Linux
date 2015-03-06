<?php

include('../inc_ssl_check.php');
session_start();
$debug = 0;
include('../inc_global.php');

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB AND QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 and $rights != 6) {
	header ("Location: login.php?out=1");
	exit;
}

$error_message="";

if ( ! isset($_REQUEST['pni'] ) ) {
	$_SESSION['note'] = "Internal Product # is required";
	echo "<script>window.opener.location.reload();window.close();</script>";
	exit();
}

$pni=escape_data($_REQUEST['pni']);
$mergtopni = ( empty($_REQUEST['mergtopni'] ) ) ? "" : escape_data($_REQUEST['mergtopni']);

//start_transaction($link);
//Backup first before we remove the product
//BS
$sql="SELECT * FROM batchsheetdetail WHERE IngredientProductNumber='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
$sql="INSERT INTO deleted_batchsheetdetail SELECT * FROM batchsheetdetail WHERE IngredientProductNumber='".$pni."'";
if ( ! mysql_query($sql,$link) ) {
	echo " Failed execute SQL : $sql <br />";
	//end_transaction($sql,$link);
	die();
}
}

$sql="SELECT * FROM batchsheetdetaillotnumbers WHERE IngredientProductNumber='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_batchsheetdetaillotnumbers SELECT * FROM batchsheetdetaillotnumbers WHERE IngredientProductNumber='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}

$sql="SELECT * FROM batchsheetmaster WHERE ProductNumberInternal='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_batchsheetmaster SELECT * FROM batchsheetmaster WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}

//CO
$sql="SELECT * FROM customerorderdetail WHERE ProductNumberInternal='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_customerorderdetail SELECT * FROM customerorderdetail WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}

$sql="SELECT * FROM customerorderdetaillotnumbers WHERE ProductNumberInternal='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_customerorderdetaillotnumbers SELECT * FROM customerorderdetaillotnumbers WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}
//epnref

$sql="SELECT * FROM externalproductnumberreference WHERE ProductNumberInternal='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_externalproductnumberreference SELECT * FROM externalproductnumberreference WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}
//FM

$sql="SELECT * FROM formulationdetail WHERE ProductNumberInternal='".$pni."' OR IngredientProductNumber='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_formulationdetail SELECT * FROM formulationdetail WHERE ProductNumberInternal='".$pni."' OR IngredientProductNumber='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}
//IM

$sql="SELECT * FROM inventorymovements WHERE ProductNumberInternal='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_inventorymovements SELECT * FROM inventorymovements WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}
//PS

$sql="SELECT * FROM pricesheetdetail WHERE IngredientProductNumber ='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_pricesheetdetail SELECT * FROM pricesheetdetail WHERE IngredientProductNumber ='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}

$sql="SELECT * FROM pricesheetmaster WHERE ProductNumberInternal='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_pricesheetmaster SELECT * FROM pricesheetmaster WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}
//PM

$sql="SELECT * FROM productmaster WHERE ProductNumberInternal='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_productmaster SELECT * FROM productmaster WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}
//PK

$sql="SELECT * FROM productpacksize WHERE ProductNumberInternal='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_productpacksize SELECT * FROM productpacksize WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}
//PP
$sql="SELECT * FROM productprices WHERE ProductNumberInternal='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_productprices SELECT * FROM productprices WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}
//PO
$sql="SELECT * FROM purchaseorderdetail WHERE ProductNumberInternal='".$pni."'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$sql="INSERT INTO deleted_purchaseorderdetail SELECT * FROM purchaseorderdetail WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}

if ( $mergtopni != "" ) {
	//BS
	
	$sql="UPDATE batchsheetdetail SET IngredientProductNumber='".$mergtopni."' WHERE IngredientProductNumber='".$pni."'";
	mysql_query($sql,$link);// or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
	$sql="UPDATE batchsheetdetaillotnumbers SET IngredientProductNumber='".$mergtopni."' WHERE IngredientProductNumber='".$pni."'";
	mysql_query($sql,$link);// or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
	$sql="UPDATE batchsheetmaster SET ProductNumberInternal='".$mergtopni ."', ProductNumberExternal=( SELECT ProductNumberExternal 
	FROM externalproductnumberreference WHERE ProductNumberInternal='".$mergtopni."') WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed Execute SQL : %$sql <br />");
	
	//CO
	$sql="UPDATE customerorderdetail SET ProductNumberInternal='".$mergtopni."' WHERE ProductNumberInternal = '".$pni."'";
	mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />" );

	$sql="UPDATE customerorderdetaillotnumbers SET ProductNumberInternal='".$mergtopni."' WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />" );	
	
	//FM
	$sql="UPDATE formulationdetail SET IngredientProductNumber='".$mergtopni."' WHERE IngredientProductNumber = '".$pni."'";
	mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />" );
	$sql="UPDATE formulationdetail SET ProductNumberInternal='".$mergtopni."' WHERE ProductNumberInternal = '".$pni."'";
	mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />" );
	
	//IM
	$sql="UPDATE inventorymovements SET ProductNumberInternal='".$mergtopni."' WHERE ProductNumberInternal = '".$pni."'";
	mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />" );
	
	//PS
	$sql="UPDATE pricesheetdetail SET IngredientProductNumber='".$mergtopni."' WHERE IngredientProductNumber = '".$pni."'";
	mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />" );
	$sql="UPDATE pricesheetmaster SET ProductNumberInternal='".$mergtopni."' WHERE ProductNumberInternal = '".$pni."'";
	mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />" );
	
	//pp
	$sql="UPDATE productprices SET ProductNumberInternal='".$mergtopni."' WHERE ProductNumberInternal = '".$pni."'";
	mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />" );
	
	//PO
	$sql="UPDATE purchaseorderdetail SET ProductNumberInternal='".$mergtopni."' WHERE ProductNumberInternal = '".$pni."'";
	mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />" );

}

//Now delete the pni from database
//BS
$sql = "DELETE FROM batchsheetdetaillotnumbers WHERE IngredientProductNumber='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");
$sql = "DELETE FROM batchsheetdetail WHERE IngredientProductNumber='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");
$sql = "DELETE FROM batchsheetmaster WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//CO
$sql = "DELETE FROM customerorderdetail WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");
$sql = "DELETE FROM customerorderdetaillotnumbers WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//EPNREF
$sql = "DELETE FROM externalproductnumberreference WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//FM
$sql = "DELETE FROM formulationdetail WHERE ProductNumberInternal='".$pni."' OR IngredientProductNumber='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//IM
$sql = "DELETE FROM inventorymovements WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//ps
$sql = "DELETE FROM pricesheetdetail WHERE IngredientProductNumber='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");
$sql = "DELETE FROM pricesheetmaster WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//PM
$sql = "DELETE FROM productmaster WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//PK
$sql = "DELETE FROM productpacksize WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//PP
$sql = "DELETE FROM productprices WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//PO
$sql = "DELETE FROM purchaseorderdetail WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) ;//or die ( mysql_error() . " Failed execute SQL : $sql <br />");

$_SESSION['note'] = "The meterial $pni was removed ". ( $mergtopni == "" ? "" : "with the merge to $mergtopni" ) ;
echo "<script>window.opener.location.reload();window.close();</script>";
exit();

?>