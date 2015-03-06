<?php

include('inc_ssl_check.php');
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

//BS
$sql="SELECT  BatchSheetNumber,IngredientProductNumber,IngredientSEQ, IngredientNumberExternal, IngredientDesignation
FROM deleted_batchsheetdetail WHERE IngredientProductNumber='".$pni."'";
$result = mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
if ( mysql_num_rows($result) > 0 ) { 
	while ( $row = mysql_fetch_array($result) ) {
		$sql="UPDATE batchsheetdetail SET IngredientProductNumber='".$row['IngredientProductNumber']."',
			IngredientNumberExternal='".$row['IngredientNumberExternal']."' ,
			IngredientDesignation='".$row['IngredientDesignation'] ."' WHERE BatchSheetNumber='".$row['BatchSheetNumber']."' AND IngredientSEQ='".$row['IngredientSEQ']."'";
		if ( ! mysql_query($sql,$link) ) {
			echo mysql_error() . " Failed Execute SQL $sql <br />";
			die();
		}
	}
	$sql="INSERT INTO batchsheetdetail SELECT * FROM deleted_batchsheetdetail 
	  WHERE IngredientProductNumber='".$pni."' AND BatchSheetNumber not in ( 
	  SELECT BatchSheetNumber FROM batchsheetdetail WHERE IngredientProductNumber='".$pni."' )";
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL $sql <br />";
		die();
	}
}
	
$sql="SELECT  BatchSheetNumber,IngredientProductNumber,IngredientSEQ FROM deleted_batchsheetdetaillotnumbers WHERE IngredientProductNumber='".$pni."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
if ( mysql_num_rows($result) > 0 ) { 
	while ( $row = mysql_fetch_array($result) ) {
		$sql="UPDATE batchsheetdetaillotnumbers SET IngredientProductNumber='".$row['IngredientProductNumber']."'
		 WHERE BatchSheetNumber='".$row['BatchSheetNumber']."' AND IngredientSEQ='".$row['IngredientSEQ']."'";
		if ( ! mysql_query($sql,$link) ) {
			echo mysql_error() . " Failed Execute SQL $sql <br />";
			die();
		}
	}
	$sql="INSERT INTO batchsheetdetaillotnumbers SELECT * FROM deleted_batchsheetdetaillotnumbers 
	  WHERE IngredientProductNumber='".$pni."' AND BatchSheetNumber not in ( 
	  SELECT BatchSheetNumber FROM batchsheetdetaillotnumbers WHERE IngredientProductNumber='".$pni."' )";
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL $sql <br />";
		die();
	}
}

$sql="SELECT BatchSheetNumber,ProductNumberInternal,ProductNumberExternal,ProductDesignation FROM deleted_batchsheetmaster WHERE ProductNumberInternal='".$pni."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
if ( mysql_num_rows($result) > 0 ) { 
	while ( $row = mysql_fetch_array($result) ) {
		$sql="UPDATE batchsheetmaster SET ProductNumberInternal='".$row['ProductNumberInternal']."',
		ProductNumberExternal='".$row['ProductNumberExternal']."', ProductDesignation='".$row['ProductDesignation']."' 
		 WHERE BatchSheetNumber='".$row['BatchSheetNumber']."'";
		if ( ! mysql_query($sql,$link) ) {
			echo mysql_error() . " Failed Execute SQL $sql <br />";
			die();
		}
	}
	$sql="INSERT INTO batchsheetmaster SELECT * FROM deleted_batchsheetmaster 
	  WHERE ProductNumberInternal='".$pni."' AND BatchSheetNumber not in ( 
	  SELECT BatchSheetNumber FROM batchsheetmaster WHERE ProductNumberInternal='".$pni."' )";
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL $sql <br />";
		die();
	}
}

//CO
$sql="SELECT CUstomerOrderNumber,ProductNumberInternal,CustomerOrderSeqNumber FROM deleted_customerorderdetail WHERE ProductNumberInternal='".$pni."'";
$result = mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
if ( mysql_num_rows($result) > 0 ) { 
//	start_transaction($link); I may not use transaction here because second data change look up 1st result from database
	while ( $row = mysql_fetch_array($result) ) {
		$sql="UPDATE customerorderdetail SET ProductNumberInternal='".$row['ProductNumberInternal']."'
		 WHERE CustomerOrderNumber='".$row['CustomerOrderNumber']."' AND CustomerOrderSeqNumber='".$row['CustomerOrderSeqNumber']."'";
		if ( ! mysql_query($sql,$link) ) {
			echo mysql_error() . " Failed Execute SQL $sql <br />";
//			end_transaction(0,$link);
			die();
		}
	}
	$sql="INSERT INTO customerorderdetail SELECT * FROM deleted_customerorderdetail 
	  WHERE ProductNumberInternal='".$pni."' AND CustomerOrderNumber not in ( 
	  SELECT CustomerOrderNumber FROM customerorderdetail WHERE ProductNumberInternal='".$pni."' )";
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL $sql <br />";
//		end_transaction(0,$link);
		die();
	}
//	end_transaction(1,$link);
}

$sql="SELECT CUstomerOrderNumber,ProductNumberInternal,CustomerOrderSeqNumber FROM  deleted_customerorderdetaillotnumbers WHERE ProductNumberInternal='".$pni."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
if ( mysql_num_rows($result) > 0 ) { 
	while ( $row = mysql_fetch_array($result) ) {
		$sql="UPDATE customerorderdetaillotnumbers SET ProductNumberInternal='".$row['ProductNumberInternal']."'
		 WHERE CustomerOrderNumber='".$row['CustomerOrderNumber']."' AND CustomerOrderSeqNumber='".$row['CustomerOrderSeqNumber']."'";
		if ( ! mysql_query($sql,$link) ) {
			echo mysql_error() . " Failed Execute SQL $sql <br />";
			die();
		}
	}
	$sql="INSERT INTO customerorderdetaillotnumbers SELECT * FROM deleted_customerorderdetaillotnumbers 
	  WHERE ProductNumberInternal='".$pni."' AND CustomerOrderNumber not in ( 
	  SELECT CustomerOrderNumber FROM customerorderdetail WHERE ProductNumberInternal='".$pni."' )";
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL $sql <br />";
		die();
	}
}

//PM
$sql="SELECT * FROM productmaster WHERE ProductNumberInternal='".$pni."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
if ( mysql_num_rows($result) == 0 ) {
	$sql="INSERT INTO productmaster SELECT * FROM deleted_productmaster WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}
//epnref
$sql="SELECT * from externalproductnumberreference WHERE ProductNumberInternal='".$pni."'";
$result=mysql_query($sql,$link) or die (mysql_error() . " Failed to execute SQL : $sql <br />");
if ( mysql_num_rows($result) == 0 ) {
	$sql="INSERT INTO externalproductnumberreference SELECT * FROM deleted_externalproductnumberreference WHERE ProductNumberInternal='".$pni."'";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
}

//FM

$sql="SELECT ProductNumberInternal,IngredientSEQ, IngredientProductNumber FROM deleted_formulationdetail  WHERE IngredientProductNumber='".$pni."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
if ( mysql_num_rows($result) > 0 ) { 
	while ( $row = mysql_fetch_array($result) ) {
		$sql="UPDATE formulationdetail SET IngredientProductNumber='".$pni."'
		 WHERE ProductNumberInternal='".$row['ProductNumberInternal']."' AND IngredientSEQ='".$row['IngredientSEQ']."'";
		if ( ! mysql_query($sql,$link) ) {
			echo mysql_error() . " Failed Execute SQL $sql <br />";
			die();
		}
	}
	$sql="INSERT INTO formulationdetail SELECT * FROM deleted_formulationdetail 
	  WHERE IngredientProductNumber='".$pni."' AND ProductNumberInternal not in ( 
	  SELECT ProductNumberInternal FROM formulationdetail WHERE IngredientProductNumber='".$pni."' )";
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL $sql <br />";
		die();
	}
}

$sql="SELECT ProductNumberInternal,IngredientSEQ, IngredientProductNumber FROM deleted_formulationdetail  WHERE ProductNumberInternal='".$pni."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
if ( mysql_num_rows($result) > 0 ) { 
	$sql="INSERT INTO formulationdetail SELECT * FROM deleted_formulationdetail 
	  WHERE ProductNumberInternal='".$pni."'";
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL $sql <br />";

		die();
	}

}
//IM
$sql="SELECT TransactionNumber FROM deleted_inventorymovements  WHERE ProductNumberInternal='".$pni."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

if ( mysql_num_rows($result) > 0 ) { 
	start_transaction($link);
	while ( $row = mysql_fetch_array($result) ) {
		$sql="UPDATE inventorymovements SET ProductNumberInternal='".pni."'
		 WHERE TransactionNumber='".$row['TransactionNumber']."'";
		if ( ! mysql_query($sql,$link) ) {
			echo mysql_error() . " Failed Execute SQL $sql <br />";
			end_transaction(0,$link);
			die();
		}
	}
	$sql="INSERT INTO inventorymovements SELECT * FROM deleted_inventorymovements
	  WHERE ProductNumberInternal='".$pni."' AND TransactionNumber not in ( 
	  SELECT TransactionNumber FROM inventorymovements WHERE ProductNumberInternal='".$pni."' )";
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL $sql <br />";
		end_transaction(0,$link);
		die();
	}
	end_transaction(1,$link);
}

//PS

$sql="SELECT PriceSheetNumber,IngredientProductnumber,IngredientSEQ,IngredientDesignation FROM deleted_pricesheetdetail WHERE IngredientProductNumber ='".$pni."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

if ( mysql_num_rows($result) > 0 ) { 
	start_transaction($link);
	while ( $row = mysql_fetch_array($result) ) {
		$sql="UPDATE pricesheetdetail SET IngredientProductNumber='".pni."', IngredientDesignation='".$row['IngredientDesignation']."'
		 WHERE IngredientProductNumber='".$row['IngredientProductNumber']."' AND IngredientSEQ='".$row['IngredientSEQ']."'";
		if ( ! mysql_query($sql,$link) ) {
			echo mysql_error() . " Failed Execute SQL $sql <br />";
			end_transaction(0,$link);
			die();
		}
	}
	$sql="INSERT INTO pricesheetdetail SELECT * FROM deleted_pricesheetdetail 
	  WHERE IngredientProductNumber='".$pni."' AND PriceSheetNumber not in ( 
	  SELECT PriceSheetNumber FROM pricesheetdetail WHERE IngredientProductNumber='".$pni."' )";
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL $sql <br />";
		end_transaction(0,$link);
		die();
	}
	end_transaction(1,$link);
}

$sql="SELECT PriceSheetNumber,ProductNumberInternal,ProductDesignation FROM deleted_pricesheetmaster WHERE ProductNumberInternal='".$pni."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
if ( mysql_num_rows($result) > 0 ) { 
	start_transaction($link);
	while ( $row = mysql_fetch_array($result) ) {
		$sql="UPDATE pricesheetmaster SET ProductNumberInternal='".pni."', ProductDesignation='".$row['ProductDesignation']."'
		 WHERE PriceSheetNumber='".$row['PriceSheetNumber']."'";
		if ( ! mysql_query($sql,$link) ) {
			echo mysql_error() . " Failed Execute SQL $sql <br />";
			end_transaction(0,$link);
			die();
		}
	}
	$sql="INSERT INTO pricesheetmaster SELECT * FROM deleted_pricesheetmaster 
	  WHERE ProductNumberInternal='".$pni."' AND PriceSheetNumber not in ( 
	  SELECT PriceSheetNumber FROM pricesheetmaster WHERE ProductNumberInternal='".$pni."' )";
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL $sql <br />";
		end_transaction(0,$link);
		die();
	}
	end_transaction(1,$link);
}


//PK
$sql="INSERT INTO productpacksize SELECT * FROM deleted_productpacksize WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//PP
$sql="SELECT VendorID, Tier FROM deleted_productprices WHERE ProductNumberInternal='".$pni."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

if ( mysql_num_rows($result) > 0 ) { 
	start_transaction($link);
	while ( $row = mysql_fetch_array($result) ) {
		$sql="UPDATE productprices SET ProductNumberInternal='".pni."'
		 WHERE VendorID='".$row['VendorID']."' AND Tier='".$row['Tier']."'";
		if ( ! mysql_query($sql,$link) ) {
			echo mysql_error() . " Failed Execute SQL $sql <br />";
			end_transaction(0,$link);
			die();
		}
	}
	$sql="INSERT INTO productprices SELECT * FROM deleted_productprices 
	  WHERE ProductNumber='".$pni."' AND VendorID not in ( 
	  SELECT VendorID FROM productprices WHERE ProductNumberInternal='".$pni."' )";
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL $sql <br />";
		end_transaction(0,$link);
		die();
	}
	end_transaction(1,$link);
}


//PO
$sql="SELECT ID FROM deleted_purchaseorderdetail WHERE ProductNumberInternal='".$pni."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
if ( mysql_num_rows($result) > 0 ) { 
	start_transaction($link);
	while ( $row = mysql_fetch_array($result) ) {
		$sql="UPDATE purchaseorderdetail SET ProductNumberInternal='".pni."'
		 WHERE ID='".$row['ID']."'";
		if ( ! mysql_query($sql,$link) ) {
			echo mysql_error() . " Failed Execute SQL $sql <br />";
			end_transaction(0,$link);
			die();
		}
	}
	$sql="INSERT INTO purchaseorderdetail SELECT * FROM deleted_purchaseorderdetail 
	  WHERE ProductNumberInternal='".$pni."' AND ID not in ( 
	  SELECT ID FROM pricesheetdetail WHERE ProductNumberInternal='".$pni."' )";
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL $sql <br />";
		end_transaction(0,$link);
		die();
	}
	end_transaction(1,$link);
}

//Now delete the pni from backup database
//BS
$sql = "DELETE FROM deleted_batchsheetdetaillotnumbers WHERE IngredientProductNumber='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
$sql = "DELETE FROM deleted_batchsheetdetail WHERE IngredientProductNumber='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
$sql = "DELETE FROM deleted_batchsheetmaster WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//CO
$sql = "DELETE FROM deleted_customerorderdetail WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
$sql = "DELETE FROM deleted_customerorderdetaillotnumbers WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//EPNREF
$sql = "DELETE FROM deleted_externalproductnumberreference WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//FM
$sql = "DELETE FROM deleted_formulationdetail WHERE ProductNumberInternal='".$pni."' OR IngredientProductNumber='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//IM
$sql = "DELETE FROM deleted_inventorymovements WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//ps
$sql = "DELETE FROM deleted_pricesheetdetail WHERE IngredientProductNumber='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
$sql = "DELETE FROM deleted_pricesheetmaster WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//PM
$sql = "DELETE FROM deleted_productmaster WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//PK
$sql = "DELETE FROM deleted_productpacksize WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//PP
$sql = "DELETE FROM deleted_productprices WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

//PO
$sql = "DELETE FROM deleted_purchaseorderdetail WHERE ProductNumberInternal='".$pni."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");

$_SESSION['note'] = "The meterial $pni was recovered ";
echo "<script>window.opener.location.reload();window.close();</script>";
exit();

?>