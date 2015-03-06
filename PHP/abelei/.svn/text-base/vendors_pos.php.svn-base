<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');
include('search/system_defaults.php');

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( $_REQUEST['pon'] != '' ) {
	$pon = $_REQUEST['pon'];
}

if ( isset($_REQUEST['action']) ) {
	$action = $_REQUEST['action'];
}

if ( isset($_REQUEST['update_seq_no']) ) {
	$update_seq_no = $_REQUEST['update_seq_no'];
}

$states = array("", "AL","AK","AZ","AR","CA","CO","CT","DE","DC","FL","GA","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MA","MI","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT", "VT","VA","WA","WV","WI","WY");

if ( "delete_order" == $action ) {
	$po_id = $_REQUEST[cid];
	// Deleting the inventorymovents record will cascade and delete the associated lots and their associated receipts
	$sql = "DELETE inventorymovements.* FROM inventorymovements 
					LEFT JOIN lots ON inventorymovements.TransactionNumber = lots.InventoryMovementTransactionNumber 
					LEFT JOIN receipts ON lots.ID = receipts.LotID
					LEFT JOIN purchaseorderdetail ON receipts.PurchaseOrderID = purchaseorderdetail.ID
				WHERE receipts.Status IN ('P') AND purchaseorderdetail.PurchaseOrderNumber = $po_id";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// in case there's no inventorymovements records, cycle through and delete lots, then receipts
	$sql = "DELETE lots.* FROM lots 
					LEFT JOIN receipts ON lots.ID = receipts.LotID
					LEFT JOIN purchaseorderdetail ON receipts.PurchaseOrderID = purchaseorderdetail.ID
				WHERE receipts.Status IN ('P') AND purchaseorderdetail.PurchaseOrderNumber = $po_id";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$sql = "DELETE receipts.* FROM receipts 
					LEFT JOIN purchaseorderdetail ON receipts.PurchaseOrderID = purchaseorderdetail.ID
				WHERE receipts.Status IN ('P') AND purchaseorderdetail.PurchaseOrderNumber = $po_id";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// Deleting the purchaseordermaster record will cascade and delete the associated purchaseorderdetail records
	$sql = "DELETE FROM purchaseordermaster WHERE PurchaseOrderNumber = $po_id";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$_SESSION['note'] = "Order successfully deleted<BR>";
	header("location: vendors_pos.php");
	exit();
}

if ( isset($_REQUEST['add_prod']) ) {
	//echo print_r($_POST);
	//die();

	$IngredientProductNumber = $_REQUEST['IngredientProductNumber'];
	$PurchaseOrderSeqNumber = $_REQUEST['PurchaseOrderSeqNumber'];

	// check_field() FUNCTION IN global.php
	check_field($PurchaseOrderSeqNumber, 3, 'SEQ#');
	check_field($IngredientProductNumber, 1, 'Internal#');

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$PurchaseOrderSeqNumber = escape_data($PurchaseOrderSeqNumber);
		$IngredientProductNumber = escape_data($IngredientProductNumber);

		$sql = "SELECT MAX(PurchaseOrderSeqNumber) AS max_seq FROM purchaseorderdetail WHERE PurchaseOrderNumber = $pon";
		$result_count = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row_count = mysql_fetch_array($result_count);
		$max_seq = $row_count['max_seq'];

		$sql = "SELECT * FROM purchaseorderdetail WHERE PurchaseOrderNumber = $pon AND ProductNumberInternal = '$IngredientProductNumber' AND PurchaseOrderSeqNumber = '$PurchaseOrderSeqNumber'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( mysql_num_rows($result) > 0 or $PurchaseOrderSeqNumber < $max_seq ) {
			$sql = "UPDATE purchaseorderdetail SET PurchaseOrderSeqNumber = (PurchaseOrderSeqNumber +1) WHERE PurchaseOrderNumber = $pon AND ProductNumberInternal = '$IngredientProductNumber' AND PurchaseOrderSeqNumber >= '$PurchaseOrderSeqNumber' ORDER BY PurchaseOrderSeqNumber DESC";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		$sql = "SELECT Designation FROM productmaster WHERE ProductNumberInternal = '$IngredientProductNumber'";
		//$sql = "CALL CreatePOdetail( $pon, $PurchaseOrderSeqNumber, '$IngredientProductNumber' )";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		$Description = $row['Designation'];
		$sql = "SELECT VendorProductCode FROM vendorproductcodes WHERE ProductNumberInternal = '$IngredientProductNumber' AND VendorID=(SELECT VendorID FROM purchaseordermaster WHERE PurchaseOrderNumber=$pon LIMIT 1)";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		$vendorproductcode = $row['VendorProductCode'];
		$sql = "INSERT INTO purchaseorderdetail (PurchaseOrderNumber, ProductNumberInternal, PurchaseOrderSeqNumber, Description, UnitPrice, VendorProductCode) VALUES ('$pon', '$IngredientProductNumber', '$PurchaseOrderSeqNumber', '$Description', 0, '$vendorproductcode')";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

		header("location: vendors_pos.php?action=edit&update_seq_no=$PurchaseOrderSeqNumber&pon=$pon");
		exit();
	}

}



if ( isset($_POST['edit_prod']) ) {

	$ProductNumberInternal = $_POST['ProductNumberInternal'];
	$PurchaseOrderSeqNumber = $_POST['PurchaseOrderSeqNumber'];
	$VendorProductCode = $_POST['VendorProductCode'];
	$Quantity = $_POST['Quantity'];
	$PackSize = $_POST['PackSize'];
	$UnitOfMeasure = $_POST['UnitOfMeasure'];
	$UnitPrice = $_POST['UnitPrice'];
	$TotalQuantityOrdered = $_POST['TotalQuantityOrdered'];
	$TotalQuantityExpected = $_POST['TotalQuantityExpected'];
	$Status = $_POST['Status'];

	// check_field() FUNCTION IN global.php
	check_field($Quantity, 3, 'Quantity');
	check_field($PackSize, 3, 'Pack Size');
	check_field($UnitOfMeasure, 1, 'Units');
	check_field($UnitPrice, 3, 'Price');
	check_field($TotalQuantityOrdered, 3, 'Qty Entered');
	check_field($TotalQuantityExpected, 3, 'Qty Expected');

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$VendorProductCode = escape_data($VendorProductCode);
		$Quantity = escape_data($Quantity);
		$PackSize = escape_data($PackSize);
		$UnitOfMeasure = escape_data($UnitOfMeasure);
		$UnitPrice = escape_data($UnitPrice);
		$TotalQuantityOrdered = escape_data($TotalQuantityOrdered);
		$TotalQuantityExpected = escape_data($TotalQuantityExpected);

		$sql = "UPDATE purchaseorderdetail SET " .
		"VendorProductCode = '" . $VendorProductCode . "', " .
		"Quantity = '" . $Quantity . "', " .
		"PackSize = '" . $PackSize . "', " .
		"UnitOfMeasure = '" . $UnitOfMeasure . "', " .
		"UnitPrice = '" . $UnitPrice . "', " .
		"TotalQuantityOrdered = '" . $TotalQuantityOrdered . "', " .
		"TotalQuantityExpected = '" . $TotalQuantityExpected . "', " .
		"Status = '" . $Status . "' " .
		"WHERE ProductNumberInternal = '" . $ProductNumberInternal . "' AND PurchaseOrderSeqNumber = '" . $PurchaseOrderSeqNumber . "' AND PurchaseOrderNumber = " . $pon;
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//$_SESSION['note'] = "Information successfully saved<BR>";
		//echo $sql;
		//die();
		header("location: vendors_pos.php?pon=" . $pon);
		exit();
	}
}



if ( !empty($_POST) and $_REQUEST['action'] != 'search' and !isset($_POST['add_prod']) and !isset($_POST['edit_prod']) ) {

	$PurchaseOrderNumber = $_POST['PurchaseOrderNumber'];
	$PurchaseOrderType = $_POST['PurchaseOrderType'];
	$VendorID = $_POST['VendorID'];
	$VendorName = $_POST['VendorName'];
	$VendorStreetAddress1 = $_POST['VendorStreetAddress1'];
	$VendorStreetAddress2 = $_POST['VendorStreetAddress2'];
	$VendorCity = $_POST['VendorCity'];
	$VendorState = $_POST['VendorState'];
	$VendorZipCode = $_POST['VendorZipCode'];
	$VendorMainPhoneNumber = $_POST['VendorMainPhoneNumber'];
	$ShipToID = $_POST['ShipToID'];
	$ShipToName = $_POST['ShipToName'];
	$ShipToStreetAddress1 = $_POST['ShipToStreetAddress1'];
	$ShipToStreetAddress2 = $_POST['ShipToStreetAddress2'];
	$ShipToCity = $_POST['ShipToCity'];
	$ShipToState = $_POST['ShipToState'];
	$ShipToZipCode = $_POST['ShipToZipCode'];
	$ShipToMainPhoneNumber = $_POST['ShipToMainPhoneNumber'];
	if ( $_POST['ShippingAndHandlingCost'] == '' ) {
		$ShippingAndHandlingCost = 0;
	} else {
		$ShippingAndHandlingCost = $_POST['ShippingAndHandlingCost'];
	}
	$PaymentType = $_POST['PaymentType'];

	if ( $pon == '' ) {
		$sql = "SELECT PurchaseOrderNumber FROM purchaseordermaster WHERE PurchaseOrderNumber = '" . $PurchaseOrderNumber . "'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$match = mysql_num_rows($result);
		if ( $match > 0 ) {
			$error_found = true;
			$error_message .= "P.O.# already exists<BR>";
		}
	}

	$ShippingDate = $_POST['ShippingDate'];
	if ( $ShippingDate != '' ) {
		$date_parts = explode("/", $ShippingDate);
		$NewShippingDate = "'" . $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1] . "'";
		if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
			if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
				$error_found=true;
				$error_message .= "Invalid (" . $ShippingDate . ") date entered<BR>";
			}
		} else {
			$error_found=true;
			$error_message .= "Invalid (" . $ShippingDate . ") date entered<BR>";
		}
	} else {
		$NewShippingDate = "NULL";
	}

	$ConfirmationOrderNumber = $_POST['ConfirmationOrderNumber'];
	$contact_id = $_POST['contact_id'];
	$VendorSalesRep = $_POST['VendorSalesRep'];
	$ShipVia = $_POST['ShipVia'];
	$Notes = $_POST['Notes'];

	// check_field() FUNCTION IN global.php
	check_field($PurchaseOrderNumber, 3, 'P.O.#');
	check_field($PurchaseOrderType, 1, 'Type of P.O.');
	check_field($PaymentType, 1, 'Payment Type');
//	check_field($ConfirmationOrderNumber, 1, 'Order#');
	check_field($VendorName, 1, 'Vendor Name');
	//check_field($VendorSalesRep, 1, 'Sales Rep');
	check_field($VendorStreetAddress1, 1, 'Vendor Address');
	check_field($VendorCity, 1, 'Vendor City');
	check_field($VendorState, 1, 'Vendor State');
	check_field($VendorZipCode, 1, 'Vendor ZIP');
	// check_field($VendorMainPhoneNumber, 1, 'Vendor Phone');
	check_field($ShipToName, 1, 'Shipping ');
	check_field($ShipToStreetAddress1, 1, 'Shipping Address');
	check_field($ShipToCity, 1, 'Shipping City');
	check_field($ShipToState, 1, 'Shipping State');
	check_field($ShipToZipCode, 1, 'Shipping ZIP');
	check_field($ShipToMainPhoneNumber, 1, 'Shipping Phone');


	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$PurchaseOrderNumber = escape_data($PurchaseOrderNumber);
		$PurchaseOrderType = escape_data($PurchaseOrderType);
		$VendorID = escape_data($VendorID);
		$VendorName = escape_data($VendorName);
		$VendorStreetAddress1 = escape_data($VendorStreetAddress1);
		$VendorStreetAddress2 = escape_data($VendorStreetAddress2);
		$VendorCity = escape_data($VendorCity);
		$VendorState = escape_data($VendorState);
		$VendorZipCode = escape_data($VendorZipCode);
		$VendorMainPhoneNumber = escape_data($VendorMainPhoneNumber);
		$ShipToID = escape_data($ShipToID);
		$ShipToName = escape_data($ShipToName);
		$ShipToStreetAddress1 = escape_data($ShipToStreetAddress1);
		$ShipToStreetAddress2 = escape_data($ShipToStreetAddress2);
		$ShipToCity = escape_data($ShipToCity);
		$ShipToState = escape_data($ShipToState);
		$ShipToZipCode = escape_data($ShipToZipCode);
		$ShipToMainPhoneNumber = escape_data($ShipToMainPhoneNumber);
		$ShippingAndHandlingCost = escape_data($ShippingAndHandlingCost);
		$PaymentType = escape_data($PaymentType);
		$ShippingDate = escape_data($ShippingDate);
		$ConfirmationOrderNumber = escape_data($ConfirmationOrderNumber);
		$VendorSalesRep = escape_data($VendorSalesRep);
		$ShipVia = escape_data($ShipVia);
		$Notes = escape_data($Notes);

		if ( $contact_id != '' ) {
			$contact_id_clause = $contact_id;
		} else {
			$contact_id_clause = 'NULL';
		}

		if ( $pon != '' ) {
			$sql = "UPDATE purchaseordermaster SET " .
			"PurchaseOrderNumber = '" . $PurchaseOrderNumber . "', " .
			"PurchaseOrderType = '" . $PurchaseOrderType . "', " .
			"VendorID = " . $VendorID . ", " .
			"VendorName = '" . $VendorName . "', " .
			"VendorStreetAddress1 = '" . $VendorStreetAddress1 . "', " .
			"VendorStreetAddress2 = '" . $VendorStreetAddress2 . "', " .
			"VendorCity = '" . $VendorCity . "', " .
			"VendorState = '" . $VendorState . "', " .
			"VendorZipCode = '" . $VendorZipCode . "', " .
			"VendorMainPhoneNumber = '" . $VendorMainPhoneNumber . "', " .
			"ShipToID = '" . $ShipToID . "', " .
			"ShipToName = '" . $ShipToName . "', " .
			"ShipToStreetAddress1 = '" . $ShipToStreetAddress1 . "', " .
			"ShipToStreetAddress2 = '" . $ShipToStreetAddress2 . "', " .
			"ShipToCity = '" . $ShipToCity . "', " .
			"ShipToState = '" . $ShipToState . "', " .
			"ShipToZipCode = '" . $ShipToZipCode . "', " .
			"ShipToMainPhoneNumber = '" . $ShipToMainPhoneNumber . "', " .
			"ShippingAndHandlingCost = '" . $ShippingAndHandlingCost . "', " .
			"PaymentType = '" . $PaymentType . "', " .
			"ShippingDate = " . $NewShippingDate . ", " .
			"ConfirmationOrderNumber = '" . $ConfirmationOrderNumber . "', " .
			"contact_id = " . $contact_id_clause . ", " .
			"VendorSalesRep = '" . $VendorSalesRep . "', " .
			"ShipVia = '" . $ShipVia . "', " .
			"Notes = '" . $Notes . "' " .
			"WHERE PurchaseOrderNumber = '" . $pon . "'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		} else {
			$sql = "INSERT INTO purchaseordermaster (PurchaseOrderNumber, PurchaseOrderType, VendorID, VendorName, VendorStreetAddress1, VendorStreetAddress2, VendorCity, VendorState, VendorZipCode, VendorMainPhoneNumber, ShipToID, ShipToName, ShipToStreetAddress1, ShipToStreetAddress2, ShipToCity, ShipToState, ShipToZipCode, ShipToMainPhoneNumber, ShippingAndHandlingCost, PaymentType, ShippingDate, DateOrderPlaced, ConfirmationOrderNumber, contact_id, VendorSalesRep, ShipVia, Notes) VALUES ('" . $PurchaseOrderNumber . "', '" . $PurchaseOrderType . "', '" . $VendorID . "', '" . $VendorName . "', '" . $VendorStreetAddress1 . "', '" . $VendorStreetAddress2 . "', '" . $VendorCity . "', '" . $VendorState . "', '" . $VendorZipCode . "', '" . $VendorMainPhoneNumber . "', '" . $ShipToID . "', '" . $ShipToName . "', '" . $ShipToStreetAddress1 . "', '" . $ShipToStreetAddress2 . "', '" . $ShipToCity . "', '" . $ShipToState . "', '" . $ShipToZipCode . "', '" . $ShipToMainPhoneNumber . "', '" . $ShippingAndHandlingCost . "', '" . $PaymentType . "', " . $NewShippingDate . ", '" . date("Y-m-d H:i:s") . "', '" . $ConfirmationOrderNumber . "', " . $contact_id_clause . ", '" . $VendorSalesRep . "', '" . $ShipVia . "', '" . $Notes . "')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$pon = $PurchaseOrderNumber;
		}

		header("location: vendors_pos.php?pon=" . $pon);
		exit();
	}

} elseif ( $pon != '' ) {

	//$sql = "SELECT pom.*, pod.Status FROM purchaseordermaster AS pom LEFT JOIN purchaseorderdetail AS pod USING (PurchaseOrderNumber) WHERE pom.PurchaseOrderNumber = $pon ORDER BY Status DESC";

	$sql = "SELECT pom.*, (SELECT COUNT(PurchaseOrderNumber) FROM purchaseorderdetail WHERE purchaseorderdetail.PurchaseOrderNumber = pom.PurchaseOrderNumber AND (Status = 'O' OR Status = 'P' OR Status = '' OR Status IS NULL)) AS incompletes FROM purchaseordermaster AS pom WHERE pom.PurchaseOrderNumber = $pon";
	
	// LEFT JOIN purchaseorderdetail USING (PurchaseOrderNumber)
	//echo $sql . "<BR>";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$PurchaseOrderType = $row['PurchaseOrderType'];
	$VendorID = $row['VendorID'];
	$VendorName = $row['VendorName'];
	$VendorStreetAddress1 = $row['VendorStreetAddress1'];
	$VendorStreetAddress2 = $row['VendorStreetAddress2'];
	$VendorCity = $row['VendorCity'];
	$VendorState = $row['VendorState'];
	$VendorZipCode = $row['VendorZipCode'];
	$VendorMainPhoneNumber = $row['VendorMainPhoneNumber'];
	$ShipToID = $row['ShipToID'];
	$ShipToName = $row['ShipToName'];
	$ShipToStreetAddress1 = $row['ShipToStreetAddress1'];
	$ShipToStreetAddress2 = $row['ShipToStreetAddress2'];
	$ShipToCity = $row['ShipToCity'];
	$ShipToState = $row['ShipToState'];
	$ShipToZipCode = $row['ShipToZipCode'];
	$ShipToMainPhoneNumber = $row['ShipToMainPhoneNumber'];
	$ShippingAndHandlingCost = $row['ShippingAndHandlingCost'];
	$PaymentType = $row['PaymentType'];

	$ShippingDate = $row['ShippingDate'];

	$DateOrderPlaced = $row['DateOrderPlaced'];
	$ConfirmationOrderNumber = $row['ConfirmationOrderNumber'];
	$contact_id = $row['contact_id'];
	$VendorSalesRep = $row['VendorSalesRep'];
	$ShipVia = $row['ShipVia'];
	$Notes = $row['Notes'];
	$status=$row['incompletes'];

} else {
	if ( !$error_found ) {
		$PurchaseOrderNumber = '';
	}
	$PurchaseOrderType = '';
	$VendorID = '';
	$VendorName = '';
	$VendorStreetAddress1 = '';
	$VendorStreetAddress2 = '';
	$VendorCity = '';
	$VendorState = '';
	$VendorZipCode = '';
	$VendorMainPhoneNumber = '';
	$ShipToID = '1';
	$ShipToName = 'abelei';
	$ShipToStreetAddress1 = '194 Alder Dr.';
	$ShipToStreetAddress2 = '';
	$ShipToCity = 'North Aurora';
	$ShipToState = 'IL';
	$ShipToZipCode = '60542';
	$ShipToMainPhoneNumber = '630-859-1410';
	$ShippingAndHandlingCost = '';
	$PaymentType = '';
	$ShippingDate = '';
	$DateOrderPlaced = '';
	$ConfirmationOrderNumber = '';
	$contact_id = '';
	$VendorSalesRep = '';
	$ShipVia = '';
	$Notes = '';
	$status=1;
}



if ( $action == "delete_product" ) {
	$sql = "DELETE FROM purchaseorderdetail WHERE ProductNumberInternal = '" . $_GET['pni'] . "' AND PurchaseOrderSeqNumber = '" . $_GET['seq'] . "' AND PurchaseOrderNumber = " . $_GET['pon'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$sql = "SELECT * FROM purchaseorderdetail WHERE PurchaseOrderNumber = " . $_GET['pon'] . " ORDER BY PurchaseOrderSeqNumber";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	if ( mysql_num_rows($result) != 0 ) {
		$i = 1;
		while ( $row = mysql_fetch_array($result) ) {
			$sql = "UPDATE purchaseorderdetail SET PurchaseOrderSeqNumber = " . $i . " WHERE PurchaseOrderNumber = '" . $_GET['pon'] . "' AND PurchaseOrderSeqNumber = '" .  $row['PurchaseOrderSeqNumber'] . "'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$i = $i + 1;
		}
	}
	header("location: vendors_pos.php?pon=" . $_GET['pon']);
	exit();
}



if ( isset($_REQUEST['PurchaseOrderNumber']) and $_GET['action'] == 'search' ) {
	$PurchaseOrderNumber = $_REQUEST['PurchaseOrderNumber'];
}
if ( isset($_REQUEST['vendor']) and $_GET['action'] == 'search' ) {
	$vendor = $_REQUEST['vendor'];
} else {
	//$VendorID = '';
}
if ( isset($_REQUEST['VendorID']) and $_GET['action'] == 'search' ) {
	$VendorID = $_REQUEST['VendorID'];
}
if ( isset($_REQUEST['Status']) and $_GET['action'] == 'search' ) {
	$Status = $_REQUEST['Status'];
}
if ( isset($_REQUEST['start_date']) and $_GET['action'] == 'search' ) {
	$start_date = $_REQUEST['start_date'];
}
if ( isset($_REQUEST['end_date']) and $_GET['action'] == 'search' ) {
	$end_date = $_REQUEST['end_date'];
}



$form_status = "";
if ( $_REQUEST['update'] != 1 ) {
	$form_status = "readonly=\"readonly\"";
}


include("inc_header.php");

$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

$shippers = array("Airborne", "Christian Freight", "Common Carrier", "Company Truck", "Customer Pick Up", "DHL", "Fed Ex", "Please Call", "Taxair", "Tri-Air", "United Express", "UPS Ground");
$po_types = array("Material", "Process");
$payment_types = array("Check", "Credit Card");


?>



<script type="text/javascript">

$(function() {
	$('#datepicker1').datepicker({
		changeMonth: true,
		changeYear: true
	});
});

$(function() {
	$('#datepicker2').datepicker({
		changeMonth: true,
		changeYear: true
	});
});

$(function() {
	$('#datepicker3').datepicker({
		changeMonth: true,
		changeYear: true
	});
});

</script>



<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
	unset($note);
} ?>



<?php if ( $pon == '' and $_REQUEST['action'] != 'edit' ) { ?>

<table class="bounding">
<tr valign="top">
<td class="padded">

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
	<FORM ACTION="vendors_pos.php" METHOD="get">
	<INPUT TYPE="hidden" NAME="action" VALUE="search">

		<TR>
			<TD><B>PO#:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="PurchaseOrderNumber" NAME="PurchaseOrderNumber" VALUE="<?php echo $PurchaseOrderNumber;?>" SIZE="20">&nbsp;&nbsp;

			<?php 
			$sql_one = "SELECT DISTINCT purchaseordermaster.PurchaseOrderNumber, VendorName, DateOrderPlaced, Status FROM purchaseordermaster LEFT JOIN purchaseorderdetail USING (PurchaseOrderNumber) WHERE Status = 'O' ORDER BY DateOrderPlaced DESC LIMIT 1";
			//echo $sql . "<BR>";
			$result_one = mysql_query($sql_one, $link) or die (mysql_error()."<br />Couldn't execute query: $sql_one<BR><BR>");
			$c = mysql_num_rows($result_one);
			if ( $c > 0 ) {
				$row_one = mysql_fetch_array($result_one);
				echo "<A HREF=\"vendors_pos.php?pon=" . $row_one['PurchaseOrderNumber'] . "\">" . $row_one['PurchaseOrderNumber'] . "</A>";
				if ( $row_one['DateOrderPlaced'] != '' ) {
					echo  " - " . date("n/j/Y", strtotime($row_one['DateOrderPlaced']));
				}
			} ?>

			</TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Vendor:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="vendor" SIZE="20" VALUE="<?php echo stripslashes($vendor);?>"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

			<TR>
			<TD><B>Date quoted:</B>&nbsp;&nbsp;&nbsp;</TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD>
			<INPUT TYPE="text" SIZE="12" NAME="start_date" id="datepicker2" VALUE="<?php
				if ( $start_date != '' ) {
					echo date("m/d/Y", strtotime($start_date));
				}
				?>">
				to 
			<INPUT TYPE="text" SIZE="12" NAME="end_date" id="datepicker3" VALUE="<?php
				if ( $end_date != '' ) {
					echo date("m/d/Y", strtotime($end_date));
				}
				?>">
			</TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Status:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><SELECT NAME="Status" STYLE="font-size: 7pt">
			<?php if ( $Status == 'A' ) { ?>
				<OPTION VALUE=""></OPTION>
				<OPTION VALUE="O">Open</OPTION>
				<OPTION VALUE="A" SELECTED>Approved</OPTION>
			<?php } elseif ( $Status == 'O' ) { ?>
				<OPTION VALUE=""></OPTION>
				<OPTION VALUE="O" SELECTED>Open</OPTION>
				<OPTION VALUE="A">Approved</OPTION>
			<?php } else { ?>
				<OPTION VALUE=""></OPTION>
				<OPTION VALUE="O">Open</OPTION>
				<OPTION VALUE="A">Approved</OPTION>
			<?php } ?>
			</SELECT></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="12"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3>
			<INPUT style="float:right" TYPE="submit" class="submit_medium" VALUE="Search">
			<INPUT TYPE="button" style="margin:.5em .5em 0 0" class="submit new" VALUE="Add PO" onClick="window.location='vendors_pos.php?action=edit&update=1'">
			<INPUT TYPE="button" style="margin-top:.5em" class="submit" VALUE="Reset" onClick="window.location='vendors_pos.php'">
			</TD>
		</TR></FORM>
	</TABLE><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" ALIGN=CENTER>
	<TR><FORM>
		<TD>
		<INPUT TYPE="button" VALUE="Open Purchase Orders" onClick="popup('reports/inventory_reports_open_purchase_orders.php')" CLASS="submit_normal">
		<!-- <IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"> -->
		</TD>
	</TR></FORM>
</TABLE>
</TD></TR></TABLE>
<BR><BR>

	<?php

	if ( $_REQUEST['action'] != 'search' ) {

		//$sql = "SELECT DISTINCT purchaseordermaster.PurchaseOrderNumber, VendorID, VendorName, DateOrderPlaced, Status, (SELECT COUNT(PurchaseOrderNumber) FROM purchaseorderdetail WHERE purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber) AS detail_count FROM purchaseordermaster LEFT JOIN purchaseorderdetail USING (PurchaseOrderNumber) WHERE Status = 'O' ORDER BY purchaseordermaster.PurchaseOrderNumber";

		//$sql = "SELECT DISTINCT purchaseordermaster.PurchaseOrderNumber, VendorID, VendorName, DateOrderPlaced, Status FROM purchaseordermaster LEFT JOIN purchaseorderdetail USING (PurchaseOrderNumber) WHERE Status = 'O' ORDER BY purchaseordermaster.PurchaseOrderNumber";

		$sql = "SELECT DISTINCT purchaseordermaster.PurchaseOrderNumber, VendorID, VendorName, DateOrderPlaced, (SELECT COUNT(PurchaseOrderNumber) FROM purchaseorderdetail WHERE purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber) AS detail_count, (SELECT COUNT(PurchaseOrderNumber) FROM purchaseorderdetail WHERE purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber AND (Status = 'O' OR Status = 'P' OR Status = '' OR Status IS NULL) OR 
			purchaseordermaster.PurchaseOrderNumber NOT IN (SELECT PurchaseOrderNumber FROM purchaseorderdetail)) AS incompletes,
	(
		SELECT COUNT(PurchaseOrderNumber)  FROM purchaseorderdetail 
		WHERE purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber
	) as li_count
		FROM purchaseordermaster
		LEFT JOIN purchaseorderdetail USING (PurchaseOrderNumber)
		WHERE (SELECT COUNT(PurchaseOrderNumber) FROM purchaseorderdetail WHERE purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber AND (Status = 'O' OR Status = 'P' OR Status = '' OR Status IS NULL)) > 0 OR PurchaseOrderNumber NOT IN (SELECT PurchaseOrderNumber FROM purchaseorderdetail)
		ORDER BY PurchaseOrderNumber";

		//echo $sql . "<BR>";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$count = mysql_num_rows($result);
		$bg = 0; 
		if ( $count > 0 ) { ?>
		<h3>All open POs</h3>
			<TABLE BORDER=0 CELLSPACING="0" CELLPADDING="3">
				<TR VALIGN=BOTTOM>
					<td />
					<TD><B>PO#</B></TD>
					<TD><B>Vendor</B></TD>
					<TD><B>Date</B></TD>
					<TD><B>Status</B></TD>
					<TD></TD>
					<TD></TD>
				</TR>

			<?php 
			$total = 0;
			while ( $row = mysql_fetch_array($result) ) {
				if ( $bg == 1 ) {
					$bgcolor = "#F3E7FD";
					$bg = 0;
				} else {
					$bgcolor = "whitesmoke";
					$bg = 1;
				}
				?>
				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
				<TD>
				<?php
				$sql = "SELECT receipts.ID 
				FROM receipts
					LEFT JOIN purchaseorderdetail ON receipts.PurchaseOrderID = purchaseorderdetail.ID
				WHERE receipts.Status IN ('A','R') AND purchaseorderdetail.PurchaseOrderNumber = $row[PurchaseOrderNumber]";
				$result_delete = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$k = mysql_num_rows($result_delete);
				if ( 0 == $k ) { ?>
					<A HREF="JavaScript:delete_order(<?php echo($row[PurchaseOrderNumber]);?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A>
				<?php } ?>
				</TD>
				<!-- <TD><A HREF="JavaScript:void(0)" onClick="printer_popup('reports/print_formula.php?pni=<?php echo $row['ProductNumberInternal'];?>')"><?php //echo $row['ProductNumberExternal'];?></A></TD> -->
					<TD><A HREF="vendors_pos.php?pon=<?php echo $row['PurchaseOrderNumber'];?>"><?php echo $row['PurchaseOrderNumber'];?></A></TD>
					<TD><?php echo $row['VendorName'];?></TD>
					<TD><?php
					if ( $row['DateOrderPlaced'] != '' ) {
						echo date("n/j/Y", strtotime($row['DateOrderPlaced']));
					}
					?></TD>
						<?php
						if ( $row['incompletes'] > 0 ) {
							if ( 0 == $row[li_count] ) {
								$status="No Items Ordered";
								$color="red";
							} 
							else {
								$status="Open";
								$color="yellow";
							}
						} 
						else {
							$status="Approved";
							$color="grey";
						}
						echo "<TD style=\"background-color:$color\">$status</TD>";
						?>
					<TD><INPUT TYPE="button" VALUE="Print PO" onClick="popup('reports/print_vendor_po.php?pon=<?php echo $row['PurchaseOrderNumber'];?>',800,830)" CLASS="submit_normal"></TD>
					<TD><INPUT TYPE="button" VALUE="E-mail PO" onClick="location.href='vendors_pos.email.php?vid=<?php echo $row['VendorID'];?>&pon=<?php echo $row['PurchaseOrderNumber'];?>'" CLASS="submit_normal"></TD>
				</TR>
			<?php } ?>

			</TABLE><BR>
		<?php
		} else {
			echo "No open purchase orders found";
		}

	}

} ?>



<?php

if ( $_REQUEST['action'] == 'search' ) {

	if ( $start_date != '' and $end_date != '' ) {
		$start_date_parts = explode("/", $start_date);
		$end_date_parts = explode("/", $end_date);
		$mysql_start_date = $start_date_parts[2] . "-" . $start_date_parts[0] . "-" . $start_date_parts[1];
		$mysql_end_date = $end_date_parts[2] . "-" . $end_date_parts[0] . "-" . $end_date_parts[1];
	}

	if ( $PurchaseOrderNumber != '' ) {
		$PurchaseOrderNumber_clause = " AND PurchaseOrderNumber LIKE '%" . $PurchaseOrderNumber . "%'";
	} else {
		$PurchaseOrderNumber_clause = "";
	}
	if ( $vendor != '' ) {
		$vendor_clause = " AND VendorName LIKE '%" . $vendor . "%'";
	} else {
		$vendor_clause = "";
	}
	//if ( $VendorID != '' ) {
	//	$VendorID_clause = " AND VendorID = " . $VendorID;
	//} else {
	//	$VendorID_clause = "";
	//}

	if ( $start_date != '' and $end_date != '' ) {
		$date_filter = " AND ( DateOrderPlaced >= '" . $mysql_start_date . "' AND DateOrderPlaced <= '" . $mysql_end_date . "' )";
	} else {
		$date_filter = "";
	}

	if ( $Status == 'O' ) {
		$Status_clause = 
	" AND ( 
		(
			SELECT COUNT(PurchaseOrderNumber) 
			FROM purchaseorderdetail 
			WHERE purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber AND 
				(Status = 'O' OR Status = 'P' OR Status = '' OR Status IS NULL)
		) > 0 OR 
			purchaseordermaster.PurchaseOrderNumber NOT IN (
			SELECT PurchaseOrderNumber 
			FROM purchaseorderdetail) 
		)";
	} elseif ( $Status == 'A' ) {
		$Status_clause = 
	" AND (
		(
			SELECT COUNT(PurchaseOrderNumber) 
			FROM purchaseorderdetail 
			WHERE purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber AND 
				(Status = 'O' OR Status = 'P' OR Status = '' OR Status IS NULL)
		) = 0 AND 
			purchaseordermaster.PurchaseOrderNumber IN (
			SELECT PurchaseOrderNumber 
			FROM purchaseorderdetail) 
		)";
	} else {
		$Status_clause = "";
	}

	//$sql = "SELECT DISTINCT purchaseordermaster.PurchaseOrderNumber, VendorID, VendorName, DateOrderPlaced, Status, (SELECT COUNT(PurchaseOrderNumber) FROM purchaseorderdetail WHERE purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber) AS detail_count FROM purchaseordermaster LEFT JOIN purchaseorderdetail USING (PurchaseOrderNumber) WHERE 1=1 " . $PurchaseOrderNumber_clause . $vendor_clause . $Status_clause . $date_filter . " ORDER BY PurchaseOrderNumber, Status DESC";

	$sql = "SELECT DISTINCT purchaseordermaster.PurchaseOrderNumber, VendorID, VendorName, DateOrderPlaced, (SELECT COUNT(PurchaseOrderNumber) FROM purchaseorderdetail WHERE purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber) AS detail_count, (SELECT COUNT(PurchaseOrderNumber) FROM purchaseorderdetail WHERE purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber AND (Status = 'O' OR Status = 'P' OR Status = '' OR Status IS NULL)) AS incompletes,
	(
		SELECT COUNT(PurchaseOrderNumber)  FROM purchaseorderdetail 
		WHERE purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber
	) as li_count
	FROM purchaseordermaster
	LEFT JOIN purchaseorderdetail USING (PurchaseOrderNumber)
	WHERE 1=1 " . $PurchaseOrderNumber_clause . $vendor_clause . $Status_clause . $date_filter . "
	ORDER BY PurchaseOrderNumber, Status DESC";

	//echo $sql . "<BR>";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	$bg = 0; 
	if ( $c > 0 ) { ?>
		
		<TABLE BORDER=0 CELLSPACING="0" CELLPADDING="3">
			<TR VALIGN=BOTTOM>
				<td />
				<TD><B>PO#</B></TD>
				<TD><B>Vendor</B></TD>
				<TD><B>Date</B></TD>
				<TD><B>Status</B></TD>
				<TD>&nbsp;</TD>
			</TR>

		<?php 
		$total = 0;
		while ( $row = mysql_fetch_array($result) ) {
			if ( $bg == 1 ) {
				$bgcolor = "#F3E7FD";
				$bg = 0;
			} else {
				$bgcolor = "whitesmoke";
				$bg = 1;
			}
			?>
			<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
				<TD>
				<?php
				$sql = "SELECT receipts.ID 
				FROM receipts
					LEFT JOIN purchaseorderdetail ON receipts.PurchaseOrderID = purchaseorderdetail.ID
				WHERE receipts.Status IN ('A','R') AND purchaseorderdetail.PurchaseOrderNumber = $row[PurchaseOrderNumber]";
				$result_delete = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$k = mysql_num_rows($result_delete);
				if ( 0 == $k ) { ?>
					<A HREF="JavaScript:delete_order(<?php echo($row[PurchaseOrderNumber]);?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A>
				<?php } ?>
				</TD>
				
				<!-- <TD><A HREF="JavaScript:void(0)" onClick="printer_popup('reports/print_formula.php?pni=<?php echo $row['ProductNumberInternal'];?>')"><?php //echo $row['ProductNumberExternal'];?></A></TD> -->
				<TD><A HREF="vendors_pos.php?pon=<?php echo $row['PurchaseOrderNumber'];?>"><?php echo $row['PurchaseOrderNumber'];?></A></TD>
				<TD><?php echo $row['VendorName'];?></TD>
				<TD><?php
				if ( $row['DateOrderPlaced'] != '' ) {
					echo date("n/j/Y", strtotime($row['DateOrderPlaced']));
				}
				?></TD>
						<?php
						if ( $row['incompletes'] > 0 ) {
							$status="Open";
							$color="yellow";
						} 
						else {
							if ( 0 == $row[li_count] ) {
								$status="No Items Ordered";
								$color="red";
							} 
							else {
								$status="Approved";
								$color="grey";
							}
						}
						echo "<TD style=\"background-color:$color\">$status</TD>";
						?>
				<TD><INPUT TYPE="button" VALUE="Print PO" onClick="popup('reports/print_vendor_po.php?pon=<?php echo $row['PurchaseOrderNumber'];?>',800,830)" CLASS="submit_normal"></TD>
			</TR>
		<?php } ?>

		</TABLE><BR>
	<?php
	} else {
		echo "No matches found";
	}

}

?>



<?php if ( $pon != '' or $_REQUEST['action'] == 'edit' ) { ?>

	<FORM NAME="header_info" ACTION="vendors_pos.php" METHOD="post">

	<div style="background-color:<?php echo ($status > 0) ? "yellow":"lawngreen" ?>;width:890px; padding:0.5em 0; margin-bottom:0.5em; font-size:2em ;font-weight:bold; text-align:center"><?php echo ($status > 0 || ''==$status) ? "Open": "All Approved" ?>

	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

	<INPUT TYPE="button" VALUE="Print PO" onClick="popup('reports/print_vendor_po.php?pon=<?php echo $row['PurchaseOrderNumber'];?>',800,830)" CLASS="submit_normal">

	&nbsp;

	<INPUT TYPE="button" VALUE="E-mail PO" onClick="location.href='vendors_pos.email.php?vid=<?php echo $VendorID;?>&pon=<?php echo $row['PurchaseOrderNumber'];?>'" CLASS="submit_normal">

	</div>

	
	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

	<INPUT TYPE="hidden" NAME="pon" VALUE="<?php echo $pon;?>">
	<INPUT TYPE="hidden" NAME="action" VALUE="edit">
	<INPUT TYPE="hidden" NAME="update" VALUE="1">

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
				<TR style="background-color:#FFFF99">
					<TD COLSPAN=2><B>Order Info</B></TD>
				</TR>
				<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="1"></TD></TR>

				<TR>
					<TD STYLE="text-align:right"><NOBR><B>P.O.#:</B></NOBR></TD>
					<TD><INPUT TYPE="text" NAME="PurchaseOrderNumber" VALUE="<?php
					if ( !$error_found ) {
						echo $pon;
					} else {
						echo $PurchaseOrderNumber;
					}
					?>" SIZE="20" <?php echo $form_status;?>></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Date Placed:</B></TD>
					<TD>
					<?php
					if ( $row['DateOrderPlaced'] != '' and $row['DateOrderPlaced'] != '0000-00-00 00:00:00' ) {
						echo date("n/j/Y", strtotime($row['DateOrderPlaced']));
					}
					?></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><NOBR><B>Type of P.O.:</B></NOBR></TD>
					<TD><SELECT NAME="PurchaseOrderType" <?php echo $form_status;?> STYLE="font-size: 7pt">
					<OPTION VALUE=""></OPTION>
					<?php
					foreach ( $po_types as $value ) {
						if ( $value == $PurchaseOrderType ) {
							echo "<OPTION VALUE='" . $value . "' SELECTED>" . $value . "</OPTION>";
						} else {
							echo "<OPTION VALUE='" . $value . "'>" . $value . "</OPTION>";
						}
					}
					?></SELECT></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><NOBR><B>Payment Type:</B></NOBR></TD>
					<TD><SELECT NAME="PaymentType" <?php echo $form_status;?> STYLE="font-size: 7pt">
					<OPTION VALUE=""></OPTION>
					<?php
					foreach ( $payment_types as $value ) {
						if ( $value == $PaymentType ) {
							echo "<OPTION VALUE='" . $value . "' SELECTED>" . $value . "</OPTION>";
						} else {
							echo "<OPTION VALUE='" . $value . "'>" . $value . "</OPTION>";
						}
					}
					?></SELECT></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Order#:</B></TD>
					<TD><INPUT TYPE="text" NAME="ConfirmationOrderNumber" VALUE="<?php echo $ConfirmationOrderNumber;?>" SIZE="20" <?php echo $form_status;?> /></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><NOBR><B>Shipping Date:</B></NOBR></TD>
					<TD><INPUT TYPE="text" SIZE="20" NAME="ShippingDate" id="datepicker1" VALUE="<?php
						if ( $ShippingDate != '' ) {
							echo date("m/d/Y", strtotime($ShippingDate));
						}
						?>" <?php echo $form_status;?>></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Ship Via:</B></TD>
					<TD><SELECT NAME="ShipVia" <?php echo $form_status;?> STYLE="font-size: 7pt">
					<OPTION VALUE=""></OPTION>
					<?php
					foreach ( $shippers as $value ) {
						if ( $value == $ShipVia ) {
							echo "<OPTION VALUE='" . $value . "' SELECTED>" . $value . "</OPTION>";
						} else {
							echo "<OPTION VALUE='" . $value . "'>" . $value . "</OPTION>";
						}
					}
					?>
					</SELECT></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><NOBR><B>Shipping:</B></NOBR></TD>
					<TD>$<INPUT TYPE="text" NAME="ShippingAndHandlingCost" VALUE="<?php echo $ShippingAndHandlingCost;?>" SIZE="7" <?php echo $form_status;?> STYLE="text-align:right"></TD>
				</TR>
			</TABLE>



			</TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD BGCOLOR="#CDCDCD"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="2" HEIGHT="1"></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD>



			<TABLE BORDER="0" CELLPADDING="3" CELLSPACING="0">
				<TR style="background-color:#FFFF99">
					<TD COLSPAN=2><B>Vendor</B></TD>
				</TR>
				<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="1"></TD></TR>

				<TR>
					<td/>
					<td align="left"><select id="VendorLocation" <?php echo $form_status;?> style="width:190px"><option/><?php 
						if ( $VendorID != '' ) {
							$vendor_clause = " AND vendor_addresses.vendor_id = " . $VendorID;
						} else {
							$vendor_clause = "";
						}
						$sql = "SELECT DISTINCT vendor_addresses.address_id, vendor_addresses.*, vendors.vendor_id, vendors.name,vendor_address_phones.number AS phone,vendor_address_phones.type FROM vendors, vendor_addresses LEFT JOIN vendor_address_phones ON (vendor_address_phones.address_id = vendor_addresses.address_id AND vendor_address_phones.type = 2) WHERE vendor_addresses.vendor_id = vendors.vendor_id AND vendors.active=1 AND vendor_addresses.active=1" . $vendor_clause . " ORDER BY name, country DESC, state, city";
						$result_vendors = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
						while ( $row_vendors = mysql_fetch_array($result_vendors) ) {
							echo "<option value=\"$row_vendors[vendor_id]|$row_vendors[name]|$row_vendors[address1]|$row_vendors[address2]|$row_vendors[city]|$row_vendors[state]|$row_vendors[zip]|$row_vendors[phone]\">$row_vendors[name] (".substr($row_vendors['address1'],0,10).")</option>";
						}
						?></select></td>
				</TR>

				<TR>
					<TD STYLE="text-align:right"><B>Name:</B></TD>
					<TD><INPUT TYPE="text" ID="VendorName" NAME="VendorName" SIZE="20" VALUE="<?php echo stripslashes($VendorName);?>" <?php echo $form_status;?>><INPUT TYPE="hidden" ID="VendorID" NAME="VendorID" VALUE="<?php echo $VendorID;?>"></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Address:</B></TD>
					<TD><INPUT TYPE="text" NAME="VendorStreetAddress1" id="VendorStreetAddress1" VALUE="<?php echo $VendorStreetAddress1;?>" SIZE="35" <?php echo $form_status;?> /></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"></TD>
					<TD><INPUT TYPE="text" NAME="VendorStreetAddress2" id="VendorStreetAddress2" VALUE="<?php echo $VendorStreetAddress2;?>" SIZE="35" <?php echo $form_status;?> /></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>City:</B></TD>
					<TD><INPUT TYPE="text" NAME="VendorCity" id="VendorCity" VALUE="<?php echo $VendorCity;?>" SIZE="35" <?php echo $form_status;?> /></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>State:</B></TD>
					<TD><?php if ( $_REQUEST['update'] == 1 ) { ?><select name="VendorState" id="VendorState" ><?php foreach($states as $val){ echo "<option".($VendorState==$val?" selected=\"selected\"":"")." value=\"$val\">$val</option>\n"; } ?></select><?php } else { echo "<b>$VendorState</b>"; } ?></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>ZIP:</B></TD>
					<TD><INPUT TYPE="text" NAME="VendorZipCode" id="VendorZipCode" VALUE="<?php echo $VendorZipCode;?>" SIZE="11" <?php echo $form_status;?> /></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Phone:</B></TD>
					<TD><INPUT TYPE="text" NAME="VendorMainPhoneNumber" id="VendorMainPhoneNumber" VALUE="<?php echo $VendorMainPhoneNumber;?>" SIZE="35" <?php echo $form_status;?> /></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><NOBR><B>Sales Rep:</B></NOBR></TD>
					<TD><NOBR><INPUT TYPE="text" NAME="VendorSalesRep" VALUE="<?php echo $VendorSalesRep;?>" SIZE="20" READONLY="readonly" /><INPUT TYPE="hidden" NAME="contact_id" VALUE="<?php echo $contact_id;?>">
<?php if ($_REQUEST['update'] == 1) { ?>
					<INPUT TYPE="button" VALUE="Update Contact" CLASS="submit" onClick="popup('pop_select_vendor_contact.php?vid=' + $('#VendorID').val())">
<?php } ?>
					</NOBR></TD>
				</TR>
			</TABLE>



			</TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD BGCOLOR="#CDCDCD"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="2" HEIGHT="1"></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD>



			<TABLE BORDER="0" CELLPADDING="3" CELLSPACING="0">
				<TR style="background-color:#FFFF99">
					<TD COLSPAN=2><B>Ship To</B></TD>
				</TR>
				<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="1"></TD></TR>

				<TR>
					<td/>
					<td align="left"><select id="ShipToLocation" <?php echo $form_status;?> style="width:190px"><option/><?php 
						$sql = "SELECT DISTINCT vendor_addresses.address_id, vendor_addresses.*, vendors.name,vendor_address_phones.number AS phone,vendor_address_phones.type FROM vendors, vendor_addresses LEFT JOIN vendor_address_phones ON (vendor_address_phones.address_id = vendor_addresses.address_id AND vendor_address_phones.type = 2) WHERE vendor_addresses.vendor_id = vendors.vendor_id AND ship_to_vendor=1 AND vendors.active=1 AND vendor_addresses.active=1";
						$ship_to_vendors = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
						while ( $row_s2v = mysql_fetch_array($ship_to_vendors) ) {
							echo "<option value=\"$row_s2v[name]|$row_s2v[address1]|$row_s2v[address2]|$row_s2v[city]|$row_s2v[state]|$row_s2v[zip]|$row_s2v[phone]\">$row_s2v[name] (".substr($row_s2v[address1],0,10).")</option>";
						}
						?></select></td>
				</TR>

				<TR>
					<TD STYLE="text-align:right"><B>Name:</B></TD>
					<TD><INPUT TYPE="text" NAME="ShipToName" id="ShipToName" VALUE="<?php echo $ShipToName;?>" SIZE="20" <?php echo $form_status;?> /><INPUT TYPE="hidden" NAME="ShipToID" VALUE="1" /></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Address:</B></TD>
					<TD><INPUT TYPE="text" NAME="ShipToStreetAddress1" id="ShipToStreetAddress1" VALUE="<?php echo $ShipToStreetAddress1;?>" <?php echo $form_status;?> SIZE="35" /></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"></TD>
					<TD><INPUT TYPE="text" NAME="ShipToStreetAddress2" id="ShipToStreetAddress2" VALUE="<?php echo $ShipToStreetAddress2;?>" <?php echo $form_status;?> SIZE="35" /></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>City:</B></TD>
					<TD><INPUT TYPE="text" NAME="ShipToCity" id="ShipToCity" VALUE="<?php echo $ShipToCity;?>" SIZE="35" <?php echo $form_status;?> /></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>State:</B></TD>
					<TD><?php if ( $_REQUEST['update'] == 1 ) { ?><select name="ShipToState" id="ShipToState"><?php foreach($states as $val){ echo "<option".($ShipToState==$val?" selected=\"selected\"":"")." value=\"$val\">$val</option>\n"; } ?></select><?php } else { echo "<b>$ShipToState</b>"; } ?></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>ZIP:</B></TD>
					<TD><INPUT TYPE="text" NAME="ShipToZipCode" id="ShipToZipCode" VALUE="<?php echo $ShipToZipCode;?>" SIZE="11" <?php echo $form_status;?> /></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Phone:</B></TD>
					<TD><INPUT TYPE="text" NAME="ShipToMainPhoneNumber" id="ShipToMainPhoneNumber" VALUE="<?php echo $ShipToMainPhoneNumber;?>" SIZE="35" <?php echo $form_status;?> /></TD>
				</TR>
			</TABLE>



				</TD>
			</TR>

			<TR><TD COLSPAN=9><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="10"></TD></TR>
			<TR><TD COLSPAN=9 BGCOLOR="#CDCDCD"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="2"></TD></TR>
			<TR><TD COLSPAN=9><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="10"></TD></TR>

			<TR>
				<TD COLSPAN=9 VALIGN=TOP><B>Notes:</B><BR><TEXTAREA NAME="Notes" ROWS=4 COLS=80 <?php echo $form_status;?>><?php echo $Notes;?></TEXTAREA></TD>
			</TR>

			<TR><TD COLSPAN=9><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="10"></TD></TR>
			<TR><TD COLSPAN=9 BGCOLOR="#CDCDCD"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="2"></TD></TR>
			<TR><TD COLSPAN=9><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="10"></TD></TR>

			<TR VALIGN=TOP>
				<TD COLSPAN=9 ALIGN=RIGHT>
				<?php if ( $form_status == '' and $_REQUEST['update_seq_no'] == '' ) { ?>
					<INPUT TYPE="submit" VALUE="Save PO" CLASS="submit"> <INPUT TYPE="button" VALUE="Cancel" onClick="window.location='vendors_pos.php?pon=<?php echo $pon;?>'" CLASS="submit">
				<?php } elseif ( $form_status != '' and $_REQUEST['update_seq_no'] == '' ) { ?>
					<INPUT TYPE="button" VALUE="Edit PO" onClick="window.location='vendors_pos.php?pon=<?php echo $pon;?>&update=1'" CLASS="submit">
				<?php } ?>
				</TD>
			</TR>

		</TABLE>

	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</FORM><BR>









	<!-- ADD PRODUCT -->
	<?php if ( "" != $form_status and $_REQUEST['update_seq_no'] == '' ) { ?>
		<?php
		$sql = "SELECT MAX(PurchaseOrderSeqNumber) AS max_seq FROM purchaseorderdetail WHERE PurchaseOrderNumber = '" . $pon . "'";
		$result_count = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row_count = mysql_fetch_array($result_count);
		$max_seq = $row_count['max_seq'];
		$PurchaseOrderSeqNumber = ($max_seq + 1);
		?>
		<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
		<TABLE CELLPADDING=7 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
		<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD ALIGN=CENTER>
		<FORM NAME="add_ingredient" ACTION="vendors_pos.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="action" VALUE="<?php echo $action;?>">
		<INPUT TYPE="hidden" NAME="pon" VALUE="<?php echo $pon;?>">
		<INPUT TYPE="hidden" NAME="add_prod" VALUE="1">

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<TR>
				<TD>
			
				<TABLE ALIGN=RIGHT BORDER=0 CELLSPACING="0" CELLPADDING="2" BORDERCOLOR="#CDCDCD">
		
					<TR VALIGN=BOTTOM>
						<TD><B>Seq#</B></TD>
						<TD><B>Internal#</B></TD>
						<TD>&nbsp;</TD>
						<TD><B>Ingredient</B></TD>
						<TD>&nbsp;</TD>
					</TR>
					<TR>
						<TD><INPUT TYPE="text" NAME="PurchaseOrderSeqNumber" id="PurchaseOrderSeqNumber" VALUE="<?php echo $PurchaseOrderSeqNumber ?>" SIZE="5" STYLE="text-align:right"></TD>
						<TD><INPUT TYPE="text" NAME="IngredientProductNumber" VALUE="<?php echo $IngredientProductNumber ?>" SIZE="12" readonly="readonly"></TD>
						<TD><A HREF="JavaScript:newWindow=openWin('pop_search_product.php?VendorID=<?php echo $VendorID ?>&parent_action=<?php echo $action ?>&pon=<?php echo $pon ?>&posn='+document.getElementById('PurchaseOrderSeqNumber').value,'','width=1000,height=750,toolbar=0,location=0,scrollBars=1,resizable=1,left=30,top=30'); newWindow.focus()"><IMG SRC="images/zoom.png" ALT="search" WIDTH="16" HEIGHT="16" BORDER=0></A></TD>
						<TD><INPUT TYPE="text" NAME="Ingredient" VALUE="" SIZE="25" readonly="readonly"></TD>
						<TD><INPUT TYPE="submit" VALUE="Add Product" CLASS="submit"></TD>
					</TR>
				</TABLE>

		</TD></TR></TABLE></form>

		</TD></TR></TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE>
		<BR>

	<?php } ?>
	<!-- ADD PRODUCT -->








	<?php

	$sql = "SELECT * FROM purchaseorderdetail WHERE PurchaseOrderNumber = '" . $pon . "' ORDER BY PurchaseOrderSeqNumber";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";
	$c = mysql_num_rows($result);
	if ( $c > 0 ) { ?>

		<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
		<TABLE CELLPADDING=7 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
		<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD ALIGN=CENTER>

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<TR>
				<TD>

		<TABLE ALIGN=RIGHT BORDER=1 CELLSPACING="0" CELLPADDING="3" BORDERCOLOR="#CDCDCD" width="1300">
		
			<TR VALIGN=BOTTOM>
				<TD COLSPAN=2>&nbsp;</TD>
				<TD ALIGN=RIGHT><B>Seq#</B></TD>
				<TD ALIGN=RIGHT><B>Internal#</B></TD>
				<TD WIDTH="300"><B>Description</B></TD>
				<TD><B>Vendor#</B></TD>
				<TD ALIGN=RIGHT><NOBR><B>Quantity</B></NOBR></TD>
				<TD ALIGN=RIGHT><NOBR><B>Pack Size</B></NOBR></TD>
				<TD><NOBR><B>Units</B></NOBR></TD>
				<TD ALIGN=RIGHT><NOBR><B>Price</B></NOBR></TD>
				<TD ALIGN=RIGHT><NOBR><B>Total</B></NOBR></TD>
				<TD ALIGN=RIGHT><NOBR><B>Qty Ent</B></NOBR></TD>
				<TD ALIGN=RIGHT><NOBR><B>Qty Exp</B></NOBR></TD>
				<TD><NOBR><B>Status</B></NOBR></TD>
				<TD>&nbsp;</TD>
			</TR>
		
		<?php
		$total = 0;
		while ( $row = mysql_fetch_array($result) ) {
			?>

			<TR>
			<FORM ACTION="vendors_pos.php" METHOD="post">
			<INPUT TYPE="hidden" NAME="action" VALUE="<?php echo $action;?>">
			<INPUT TYPE="hidden" NAME="update_seq_no" VALUE="<?php echo $update_seq_no;?>">
			<INPUT TYPE="hidden" NAME="pon" VALUE="<?php echo $pon;?>">
			<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $row['ProductNumberInternal'];?>">
			<INPUT TYPE="hidden" NAME="PurchaseOrderSeqNumber" VALUE="<?php echo $row['PurchaseOrderSeqNumber'];?>">
			<INPUT TYPE="hidden" NAME="edit_prod" VALUE="1">
				<TD>
				<?php if ( "" != $form_status and $_REQUEST['update_seq_no'] == '' ) { ?>
					<INPUT TYPE="button" VALUE="x" CLASS="submit" onClick="delete_product('<?php echo $row['ProductNumberInternal'];?>', '<?php echo $row['PurchaseOrderSeqNumber'];?>', '<?php echo $pon;?>')">
				<?php } else if ( $row['PurchaseOrderSeqNumber'] == $_REQUEST['update_seq_no'] ) { ?>
					<INPUT TYPE="button" VALUE="Cancel" CLASS="submit" onClick="window.location='vendors_pos.php?&pon=<?php echo $pon;?>'">
				<?php } else { ?>
					&nbsp;
				<?php } ?>
				</TD>
				<TD>
				<?php if ( "" != $form_status ) { ?>
					<?php if ( $_REQUEST['update_seq_no'] == $row['PurchaseOrderSeqNumber'] ) { ?>
						<INPUT TYPE="submit" VALUE="Save" CLASS="submit">
					<?php } else if ( "" == $_REQUEST['update_seq_no'] ){ ?>
						<INPUT TYPE="button" VALUE="Edit" CLASS="submit" onClick="window.location='vendors_pos.php?action=edit&update_seq_no=<?php echo $row['PurchaseOrderSeqNumber'];?>&pon=<?php echo $pon;?>'">
					<?php } ?>
				<?php } else { ?>
					&nbsp;
				<?php } ?>
				</TD>
				<?php if ( $_REQUEST['update_seq_no'] == $row['PurchaseOrderSeqNumber'] ) {
					$ing_form_status = "";
				} else {
					$ing_form_status = "readonly='readonly'";
				} 
				$subtotal = QuantityConvert($row[Quantity]*$row[PackSize], $row[UnitOfMeasure], "lbs") * $row[UnitPrice];
				$total = $total + $subtotal; ?>

				<TD ALIGN=RIGHT><INPUT TYPE="text" NAME="PurchaseOrderSeqNumber" VALUE="<?php echo $row['PurchaseOrderSeqNumber'];?>" SIZE="5" STYLE="text-align:right" readonly='readonly'></TD>
				<TD ALIGN=RIGHT><?php echo $row['ProductNumberInternal'];?></TD>
				<TD><?php echo $row['Description'];?></TD>
				<TD><INPUT TYPE="text" NAME="VendorProductCode" id="VendorProductCode" VALUE="<?php echo $row['VendorProductCode'];?>" SIZE="12" readonly='readonly'></TD>
				<TD><INPUT TYPE="text" NAME="Quantity" <?php echo (""==$ing_form_status ? "id=\"Quantity\" ":"") ?>VALUE="<?php echo $row['Quantity'];?>" SIZE="8" <?php echo $ing_form_status;?> STYLE="text-align:right"></TD>
				<TD><INPUT TYPE="text" NAME="PackSize" <?php echo (""==$ing_form_status ? "id=\"PackSize\" ":"") ?>VALUE="<?php echo $row['PackSize'];?>" SIZE="8" <?php echo $ing_form_status;?> STYLE="text-align:right"></TD>
				<TD><select class="input-box" <?php echo (""==$ing_form_status ? "id=\"UnitOfMeasure\" ":"") ?>name="UnitOfMeasure" <?php echo $ing_form_status;?>><?php printInventoryUnitsOptions($row['UnitOfMeasure']); ?></select></TD>
				<TD><INPUT TYPE="text" NAME="UnitPrice" <?php echo (""==$ing_form_status ? "id=\"UnitPrice\" ":"") ?> VALUE="<?php echo $row['UnitPrice'];?>" SIZE="8" readonly="readonly" STYLE="text-align:right"></TD>
				<TD><INPUT style="text-align:right" TYPE="text" NAME="Total" <?php echo (""==$ing_form_status ? "id=\"Total\" ":"") ?> VALUE="<?php echo "$".number_format($subtotal,2) ?>" SIZE="8" readonly="readonly" STYLE="text-align:right"></TD>
				<TD><INPUT TYPE="text" NAME="TotalQuantityOrdered" <?php echo (""==$ing_form_status ? "id=\"TotalQuantityOrdered\" ":"") ?>VALUE="<?php echo $row['TotalQuantityOrdered'];?>" SIZE="8" <?php echo $ing_form_status;?> STYLE="text-align:right"></TD>
				<TD><INPUT TYPE="text" NAME="TotalQuantityExpected" <?php echo (""==$ing_form_status ? "id=\"TotalQuantityExpected\" ":"") ?>VALUE="<?php echo $row['TotalQuantityExpected'];?>" SIZE="8" <?php echo $ing_form_status;?> STYLE="text-align:right"></TD>
				<TD><SELECT NAME="Status" <?php echo $ing_form_status;?>>
				<?php if ( $row['Status'] == 'A' ) { ?>
					<OPTION VALUE="O">Open</OPTION>
					<OPTION VALUE="A" SELECTED>Approved</OPTION>
				<?php } else { ?>
					<OPTION VALUE="O">Open</OPTION>
					<OPTION VALUE="A">Approved</OPTION>
				<?php } ?>
				</SELECT></TD>

				<TD>
				<?php if ( "" != $form_status and $_REQUEST['update_seq_no'] == '' ) { ?>
					<INPUT TYPE="button" VALUE="Select Price" onClick="popup('pop_select_tier.php?ipn=<?php echo $row['ProductNumberInternal'];?>&seq=<?php echo $row['PurchaseOrderSeqNumber'];?>&pon=<?php echo $pon;?>&vid=<?php echo $VendorID ?>')" CLASS="submit"></TD>
				<?php } else { ?>
					&nbsp;
				<?php } ?>
				</TD>
			</TR></FORM>
<?php		} ?>
		<TR>
			<TD COLSPAN=15><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="1"></TD>
		</TR>
		<TR>
			<TD COLSPAN=10 ALIGN=RIGHT>Shipping:</TD>
			<TD ALIGN=RIGHT><NOBR>$<?php echo number_format($ShippingAndHandlingCost, 2);?></NOBR></TD>
			<TD COLSPAN=4>&nbsp;</TD>
		</TR>
		<TR><FORM>
			<TD COLSPAN=10 ALIGN=RIGHT><B>Total:</B></TD>
			<TD ALIGN=RIGHT><NOBR><B>$<?php echo number_format($total + $ShippingAndHandlingCost, 2);?><INPUT TYPE="hidden" NAME="total1" SIZE="8" VALUE="<?php echo number_format($total + $ShippingAndHandlingCost, 2);?>" STYLE="text-align:right" READONLY></B></NOBR></TD>
			<TD COLSPAN=4>&nbsp;</TD>
		</TR></FORM>

		</TABLE>

		</TD></TR></TABLE>

		</TD></TR></TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE><BR>

	<?php } ?>







	


<?php } ?>



<script type="text/javascript">
<!--
$(document).ready(function(){
	$("#ShipToLocation").change( function() {
		var val = $("#ShipToLocation option:selected").val().split("|");
		$("#ShipToName").val(val[0]);
		$("#ShipToStreetAddress1").val(val[1]);
		$("#ShipToStreetAddress2").val(val[2]);
		$("#ShipToCity").val(val[3]);
		$("#ShipToState").val(val[4]);
		$("#ShipToZipCode").val(val[5]);
		$("#ShipToMainPhoneNumber").val(val[6]);
	});
	$("#VendorLocation").change( function() {
		var val = $("#VendorLocation option:selected").val().split("|");
		$("#VendorID").val(val[0]);
		$("#VendorName").val(val[1]);
		$("#VendorStreetAddress1").val(val[2]);
		$("#VendorStreetAddress2").val(val[3]);
		$("#VendorCity").val(val[4]);
		$("#VendorState").val(val[5]);
		$("#VendorZipCode").val(val[6]);
		$("#VendorMainPhoneNumber").val(val[7]);
	});
	$(":input[readonly]").addClass("readOnly");
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");
	<?php if ($_REQUEST['update'] == 1) { ?>
	$("#vendor").autocomplete("search/vendors.php", {
		cacheLength: 1,
		selectFirst: false,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		width: 350,
		max:50,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#vendor").result(function(event, data, formatted) {
		if (data) {
			$("#VendorID").val(data[1]);
			popup('pop_select_vendor_contact.php?vid=' + $("#VendorID").val());
		}
	});
	<?php } ?>
	$("#Quantity").change(function() {
		if (!isNaN($(this).val()) && !isNaN($("#PackSize").val())) {
			$("#TotalQuantityOrdered").val($(this).val() * $("#PackSize").val());
			$("#TotalQuantityExpected").val($(this).val() * $("#PackSize").val());
			$("#UnitOfMeasure").change();
		}
	});
	$("#PackSize").change(function() {
		if (!isNaN($(this).val()) && !isNaN($("#Quantity").val())) {
			$("#TotalQuantityOrdered").val($(this).val() * $("#Quantity").val());
			$("#TotalQuantityExpected").val($(this).val() * $("#Quantity").val());
			$("#UnitOfMeasure").change();
		}
	});
	$("#UnitOfMeasure").change(function() {
		if (!isNaN($("#PackSize").val()) && !isNaN($("#Quantity").val())) {
			var qlbs = new Number( QuantityConvert( ($("#PackSize").val() * $("#Quantity").val()), $("#UnitOfMeasure").val(), "lbs"));
			if (!isNaN(qlbs)) {
				var total = new Number ($("#UnitPrice").val() * qlbs);
				$("#Total").val("$" + total.toFixed(2));
			}
		}
	});
});

function delete_order(cid) {
	if ( confirm('Are you sure you want to delete purchase order '+ cid + '?') ) {
		document.location.href = "vendors_pos.php?action=delete_order&cid=" + cid;
	}
}

function delete_product(pni, seq, pon) {
	if ( confirm('Are you sure you want to delete this product?') ) {
		document.location.href = "vendors_pos.php?action=delete_product&pni=" + pni + "&seq=" + seq + "&pon=" + pon
	}
}
// -->
</script>



<?php include("inc_footer.php"); ?>