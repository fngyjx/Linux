<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN AND FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 4 ) {
	header ("Location: login.php?out=1");
	exit;
}

// TRACK BatchSheetNumber FOR POs
if ( $_REQUEST['bsn'] != '' and $_REQUEST['pne'] != '' ) {
	$_SESSION['bsn'] = $_GET['bsn'];
} else {
	unset($_SESSION['bsn']);
}

if ( isset($_REQUEST['bsn']) ) {
	$bsn = $_REQUEST['bsn'];
} elseif ( isset($_SESSION['bsn']) ) {
	$bsn = $_SESSION['bsn'];
}

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

include('inc_global.php');
include('search/system_defaults.php');

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( $_REQUEST['order_num'] != '' ) {
	$order_num = $_REQUEST['order_num'];
}

$action = "";
if ( isset($_REQUEST['action']) ) {
	$action = $_REQUEST['action'];
}

if ( isset($_REQUEST['update_prod']) ) {
	$update_prod = $_REQUEST['update_prod'];
}


// FOR USE IN SHIPPING FORM BELOW
if ( $_REQUEST['update'] == 1 ) {
	$combo_status = "CLASS='comboBox'";
} else {
	$combo_status = "";
}

$states = array("", "AL","AK","AZ","AR","CA","CO","CT","DE","DC","FL","GA","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MA","MI","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT", "VT","VA","WA","WV","WI","WY");



if ( $_GET['action'] == "delete" ) {
	$sql = "DELETE FROM customerordermaster WHERE OrderNumber = " . $_GET['cid'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$sql = "DELETE FROM customerorderdetail WHERE CustomerOrderNumber = " . $_GET['cid'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$_SESSION['note'] = "Order successfully deleted<BR>";
	header("location: customers_customer_order_shipping.php");
	exit();
}



if ( $_GET['type'] == 'bill' or $_GET['type'] == 'ship' ) {
	$date_parts = explode("/", $_GET['date']);
	$formatted_date = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
	if ( $_GET['type'] == 'bill' ) {
		$db_field = "BilledDate";
	} else {
		$db_field = "ShipDate";
	}
	$sql = "UPDATE customerorderdetail SET " .
	$db_field . " = '" . $formatted_date . "' " .
	"WHERE ProductNumberInternal = '" . $_GET['pni'] . "' AND CustomerOrderSeqNumber = '" . $_GET['seq'] . "' AND CustomerOrderNumber = " . $_GET['order_num'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	header("location: customers_customer_order_shipping.php?order_num=" . $_GET['order_num']);
	exit();
}



if ( isset($_POST['add_prod']) ) {
	//echo print_r($_POST);
	//die();

	$CustomerOrderSeqNumber = $_POST['CustomerOrderSeqNumber'];
	$ProductNumberExternal = $_POST['ProductNumberExternal'];
	$ProductNumberInternal = $_POST['ProductNumberInternal'];
	//$Description = $_POST['Description'];
	$Quantity = $_POST['Quantity'];
	$PackSize = $_POST['PackSize'];
	$UnitOfMeasure = $_POST['UnitOfMeasure'];
	$TotalQuantityOrdered = $Quantity* $PackSize;
	$CustomerCodeNumber = $_POST['CustomerCodeNumber'];

	$BilledDate = $_POST['BilledDate'];
	if ( $BilledDate != '' ) {
		$date_parts = explode("/", $BilledDate);
		$NewBilledDate = "'" . $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1] . "'";
		if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
			if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
				$error_found=true;
				$error_message .= "Invalid (" . $BilledDate . ") date entered<BR>";
			}
		} else {
			$error_found=true;
			$error_message .= "Invalid (" . $BilledDate . ") date entered<BR>";
		}
	} else {
		$NewBilledDate = "NULL";
	}

	// check_field() FUNCTION IN global.php
	check_field($CustomerOrderSeqNumber, 3, 'SEQ#');
	check_field($ProductNumberInternal, 1, 'abelei#');
	if ( $Quantity != '' ) {
		check_field($Quantity, 3, 'Qty');
	} else {
		$Quantity = 0;
	}
	if ( $PackSize != '' ) {
		check_field($PackSize, 3, 'Pack Size');
	} else {
		$PackSize = 0;
	}
	if ( $TotalQuantityOrdered != '' ) {
		check_field($TotalQuantityOrdered, 3, 'Qty Ordered');
	} else {
		$TotalQuantityOrdered = 0;
	}

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$CustomerOrderSeqNumber = escape_data($CustomerOrderSeqNumber);
		$ProductNumberInternal = escape_data($ProductNumberInternal);
		//$Description = escape_data($Description);
		$Quantity = escape_data($Quantity);
		$PackSize = escape_data($PackSize);
		$UnitOfMeasure = escape_data($UnitOfMeasure);
		$TotalQuantityOrdered = escape_data($TotalQuantityOrdered);
		$CustomerCodeNumber = escape_data($CustomerCodeNumber);

		$sql = "SELECT MAX(CustomerOrderSeqNumber) AS max_seq FROM customerorderdetail WHERE CustomerOrderNumber = " . $order_num;
		$result_count = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row_count = mysql_fetch_array($result_count);
		$max_seq = $row_count['max_seq'];

		$sql = "SELECT * FROM customerorderdetail WHERE CustomerOrderNumber = " . $order_num . " AND ProductNumberInternal = '" . $ProductNumberInternal . "' AND CustomerOrderSeqNumber = '" . $CustomerOrderSeqNumber . "'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( mysql_num_rows($result) > 0 or $CustomerOrderSeqNumber < $max_seq ) {
			$sql = "UPDATE customerorderdetail SET CustomerOrderSeqNumber = (CustomerOrderSeqNumber +1) WHERE CustomerOrderNumber = " . $order_num . " AND ProductNumberInternal = '" . $ProductNumberInternal . "' AND CustomerOrderSeqNumber >= '" . $CustomerOrderSeqNumber . "' ORDER BY CustomerOrderSeqNumber DESC";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		$sql = "INSERT INTO customerorderdetail (CustomerOrderNumber, ProductNumberInternal, CustomerOrderSeqNumber, Quantity, PackSize, UnitOfMeasure, TotalQuantityOrdered, CustomerCodeNumber, BilledDate) VALUES ('" . $order_num . "', '" . $ProductNumberInternal . "', '" . $CustomerOrderSeqNumber . "', '" . $Quantity . "', '" . $PackSize . "', '" . $UnitOfMeasure . "', '" . $TotalQuantityOrdered . "', '" . $CustomerCodeNumber . "', " . $NewBilledDate . ")";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

		header("location: customers_customer_order_shipping.php?order_num=" . $order_num);
		exit();
	}

}



if ( isset($_POST['edit_prod']) ) {

	$CustomerOrderSeqNumber = $_POST['CustomerOrderSeqNumber'];
	$ProductNumberExternal = $_POST['ProductNumberExternal'];
	$ProductNumberInternal = $_POST['ProductNumberInternal'];
	//$Description = $_POST['Description'];
	$Quantity = $_POST['Quantity'];
	$PackSize = $_POST['PackSize'];
	$UnitOfMeasure = $_POST['UnitOfMeasure'];
	$TotalQuantityOrdered = $_POST['TotalQuantityOrdered'];
	$CustomerCodeNumber = $_POST['CustomerCodeNumber'];

	// check_field() FUNCTION IN global.php
	check_field($CustomerOrderSeqNumber, 3, 'SEQ#');
	check_field($ProductNumberInternal, 1, 'abelei#');
	//check_field($Description, 1, 'Description');
	check_field($Quantity, 3, 'Qty');
	check_field($PackSize, 3, 'Pack Size');
	check_field($UnitOfMeasure, 1, 'Units');
	check_field($TotalQuantityOrdered, 3, 'Qty Ordered');

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		//$Description = escape_data($Description);
		$Quantity = escape_data($Quantity);
		$PackSize = escape_data($PackSize);
		$UnitOfMeasure = escape_data($UnitOfMeasure);
		$TotalQuantityOrdered = escape_data($TotalQuantityOrdered);
		$CustomerCodeNumber = escape_data($CustomerCodeNumber);

		$sql = "UPDATE customerorderdetail SET " .
		//"Description = '" . $Description . "', " .
		"Quantity = '" . $Quantity . "', " .
		"PackSize = '" . $PackSize . "', " .
		"UnitOfMeasure = '" . $UnitOfMeasure . "', " .
		"TotalQuantityOrdered = '" . $TotalQuantityOrdered . "', " .
		"CustomerCodeNumber = '" . $CustomerCodeNumber . "' " .
		"WHERE ProductNumberInternal = '" . $ProductNumberInternal . "' AND CustomerOrderSeqNumber = '" . $CustomerOrderSeqNumber . "' AND CustomerOrderNumber = " . $order_num;
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//$_SESSION['note'] = "Information successfully saved<BR>";
		//echo $sql;
		//die();
		header("location: customers_customer_order_shipping.php?order_num=" . $order_num);
		exit();
	}
}



if ( !empty($_POST) and $action != 'search' and !isset($_POST['add_prod']) and !isset($_POST['edit_prod']) ) {

	//foreach (array_keys($_POST) as $key) { 
	//	$$key = $_POST[$key]; 
	//	print "$key is ${$key}<br />"; 
	//}

	$OrderNumber = $_POST['OrderNumber'];
	$OrderDate = $_POST['OrderDate'];
	$customer = $_POST['customer'];
	$customer_id = $_POST['customer_id'];
	$contact_name = $_POST['contact_name'];
	$ContactID = $_POST['contact_id'];
	$CustomerPONumber = $_POST['CustomerPONumber'];
	$C_of_A_Requested = $_POST['C_of_A_Requested'];
	$MSDS_Requested = $_POST['MSDS_Requested'];
	$NAFTA_Requested = $_POST['NAFTA_Requested'];
	$Hazardous_Info_Requested = $_POST['Hazardous_Info_Requested'];
	$Kosher = $_POST['Kosher'];
	if ( $C_of_A_Requested != 1 ) {
		$C_of_A_Requested = 0;
	}
	if ( $MSDS_Requested != 1 ) {
		$MSDS_Requested = 0;
	}
	if ( $NAFTA_Requested != 1 ) {
		$NAFTA_Requested = 0;
	}
	if ( $Hazardous_Info_Requested != 1 ) {
		$Hazardous_Info_Requested = 0;
	}
	if ( $Kosher != 1 ) {
		$Kosher = 0;
	}
	$SpecialInstructions = $_POST['SpecialInstructions'];
	$ShipVia = $_POST['ShipViaHidden'];
	$OrderTakenByEmployeeID = $_POST['OrderTakenByEmployeeID'];

	// ADDED 08/27/2009
	// ALLOWS A NEW ADDRESS TO BE ADDED ON THE FLY WITHOUT GOING TO THE CUSTOMER SCREEN
	$billing_address1 = $_POST['billing_address1'];
	$billing_address2 = $_POST['billing_address2'];
	$billing_city = $_POST['billing_city'];
	$billing_state = $_POST['billing_state'];
	$billing_zip = $_POST['billing_zip'];
	$sql = "SELECT * FROM customer_addresses WHERE address1 = '" . $billing_address1. "' AND address2 = '" . $billing_address2. "' AND city = '" . $billing_city. "' AND state = '" . $billing_state. "' AND zip = '" . $billing_zip. "'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	if ( mysql_num_rows($result) > 0 ) {
		$BillToLocationID = $_POST['billing_id'];
	} else {
		$sql = "INSERT INTO customer_addresses (customer_id, address1, address2, city, state, zip) VALUES ('" . $customer_id . "', '" . $billing_address1 . "', '" . $billing_address2 . "', '" . $billing_city . "', '" . $billing_state . "', '" . $billing_zip . "')";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$BillToLocationID = mysql_insert_id();
	}

	// ADDED 08/27/2009
	// ALLOWS A NEW ADDRESS TO BE ADDED ON THE FLY WITHOUT GOING TO THE CUSTOMER SCREEN
	$shipping_address1 = $_POST['shipping_address1'];
	$shipping_address2 = $_POST['shipping_address2'];
	$shipping_city = $_POST['shipping_city'];
	$shipping_state = $_POST['shipping_state'];
	$shipping_zip = $_POST['shipping_zip'];
	$sql = "SELECT * FROM customer_addresses WHERE address1 = '" . $shipping_address1. "' AND address2 = '" . $shipping_address2. "' AND city = '" . $shipping_city. "' AND state = '" . $shipping_state. "' AND zip = '" . $shipping_zip. "'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	if ( mysql_num_rows($result) > 0 ) {
		$ShipToLocationID = $_POST['shipping_id'];
	} else {
		$sql = "INSERT INTO customer_addresses (customer_id, address1, address2, city, state, zip) VALUES ('" . $customer_id . "', '" . $shipping_address1 . "', '" . $shipping_address2 . "', '" . $shipping_city . "', '" . $shipping_state . "', '" . $shipping_zip . "')";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$ShipToLocationID = mysql_insert_id();
	}

	// check_field() FUNCTION IN global.php
	check_field($customer_id, 1, 'Customer');
	check_field($ContactID, 1, 'Contact');
	check_field($CustomerPONumber, 1, 'P.O. Number');

	$RequestedDeliveryDate = $_POST['RequestedDeliveryDate'];
	$date_parts = explode("/", $RequestedDeliveryDate);
	$NewRequestedDeliveryDate = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
	if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
		if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
			$error_found=true;
			$error_message .= "Invalid (" . $RequestedDeliveryDate . ") date entered<BR>";
		}
	} else {
		$error_found=true;
		$error_message .= "Invalid (" . $RequestedDeliveryDate . ") date entered<BR>";
	}

	check_field($ShipVia, 1, 'Ship Via');
	if ( $ShipVia != '' and strlen($ShipVia) > 25 ) {
		$error_found=true;
		$error_message .= "'Ship Via' must be 25 characters or less<BR>";
	}
	check_field($OrderTakenByEmployeeID, 1, 'Taken By');
	check_field($BillToLocationID, 1, 'Bill to');
	check_field($ShipToLocationID, 1, 'Ship to');

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$CustomerPONumber = escape_data($CustomerPONumber);
		$SpecialInstructions = escape_data($SpecialInstructions);
		$ShipVia = escape_data($ShipVia);

		if ( $order_num != '' ) {
			$sql = "UPDATE customerordermaster SET " .
			"CustomerID = '" . $customer_id . "', " .
			"ContactID = '" . $ContactID . "', " .
			"BillToLocationID = '" . $BillToLocationID . "', " .
			"ShipToLocationID = '" . $ShipToLocationID . "', " .
			"CustomerPONumber = '" . $CustomerPONumber . "', " .
			"C_of_A_Requested = '" . $C_of_A_Requested . "', " .
			"MSDS_Requested = '" . $MSDS_Requested . "', " .
			"NAFTA_Requested = '" . $NAFTA_Requested . "', " .
			"Hazardous_Info_Requested = '" . $Hazardous_Info_Requested . "', " .
			"Kosher = '" . $Kosher . "', " .
			"SpecialInstructions = '" . $SpecialInstructions . "', " .
			(""!=$RequestedDeliveryDate ? "RequestedDeliveryDate = '$NewRequestedDeliveryDate', " : "").
			"ShipVia = '" . $ShipVia . "', " .
			"OrderTakenByEmployeeID = '" . $OrderTakenByEmployeeID . "' " .
			"WHERE OrderNumber = '" . $order_num . "'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		} else {
			$sql = "INSERT INTO customerordermaster (CustomerID, OrderDate, ContactID, BillToLocationID, ShipToLocationID, CustomerPONumber, C_of_A_Requested, MSDS_Requested, NAFTA_Requested, Hazardous_Info_Requested, Kosher, SpecialInstructions, ".(""!=$RequestedDeliveryDate ? "RequestedDeliveryDate, ": "")."ShipVia, OrderTakenByEmployeeID) VALUES ('" . $customer_id . "', '" . date("Y-m-d H:i:s") . "', '" . $ContactID . "', '" . $BillToLocationID . "', '" . $ShipToLocationID . "', '" . $CustomerPONumber . "', '" . $C_of_A_Requested . "', '" . $MSDS_Requested . "', '" . $NAFTA_Requested . "', '" . $Hazardous_Info_Requested . "', '" . $Kosher . "', '" . $SpecialInstructions . "', ".(""!=$RequestedDeliveryDate ? "'$NewRequestedDeliveryDate', ": ""). "'" . $ShipVia . "', '" . $OrderTakenByEmployeeID . "')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$order_num = mysql_insert_id();
		}

		header("location: customers_customer_order_shipping.php?order_num=" . $order_num);
		exit();
	}

} elseif ( $order_num != '' ) {

	$sql = "SELECT * FROM customerordermaster WHERE OrderNumber = " . $order_num;
	// LEFT JOIN customerorderdetail USING (OrderNumber)
	//echo $sql . "<BR>";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$OrderNumber = $row['OrderNumber'];
	$customer_id = $row['CustomerID'];
	$OrderDate =  date("n/j/Y", strtotime($row['OrderDate']));
	$ContactID = $row['ContactID'];
	$BillToLocationID = $row['BillToLocationID'];
	$ShipToLocationID = $row['ShipToLocationID'];
	$CustomerPONumber = $row['CustomerPONumber'];
	$C_of_A_Requested = $row['C_of_A_Requested'];
	$MSDS_Requested = $row['MSDS_Requested'];
	$NAFTA_Requested = $row['NAFTA_Requested'];
	$Hazardous_Info_Requested = $row['Hazardous_Info_Requested'];
	$Kosher = $row['Kosher'];
	$SpecialInstructions = $row['SpecialInstructions'];
	$ShipVia = $row['ShipVia'];
	$OrderTakenByEmployeeID = $row['OrderTakenByEmployeeID'];

	$RequestedDeliveryDate = date("m/d/Y", strtotime($row['RequestedDeliveryDate']));

	$sql = "SELECT name FROM customers WHERE customer_id = " . $customer_id;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$customer = $row['name'];

	$sql = "SELECT first_name, last_name FROM customer_contacts WHERE contact_id = " . $ContactID;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$contact_name = $row['first_name'] . " " . $row['last_name'];

	$sql = "SELECT * FROM customer_addresses WHERE address_id = " . $BillToLocationID;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$billing_id = $row['address_id'];
	$billing_address1 = $row['address1'];
	$billing_address2 = $row['address2'];
	$billing_city = $row['city'];
	$billing_state = $row['state'];
	$billing_zip = $row['zip'];

	$sql = "SELECT * FROM customer_addresses WHERE address_id = " . $ShipToLocationID;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$shipping_id = $row['address_id'];
	$shipping_address1 = $row['address1'];
	$shipping_address2 = $row['address2'];
	$shipping_city = $row['city'];
	$shipping_state = $row['state'];
	$shipping_zip = $row['zip'];

	if ( $billing_id == $shipping_id ) {
		$billing_same = 1;
	}

} else {
	$customer = '';
	$contact_name = '';
	$OrderNumber = '<I>TBD</I>';
	$customer_id = '';
	$OrderDate = '<I>TBD</I>';
	$ContactID = '';
	$BillToLocationID = '';
	$ShipToLocationID = '';
	$CustomerPONumbe = '';
	$C_of_A_Requested = '';
	$MSDS_Requested = '';
	$NAFTA_Requested = '';
	$Hazardous_Info_Requested = '';
	$Kosher = '';
	$SpecialInstructions = '';
	$ShipVia = '';
	$OrderTakenByEmployeeID = '';
	$RequestedDeliveryDate = '';
	$billing_same = '';
}



if ( $action == "delete_product" ) {
	$sql = "DELETE FROM customerorderdetail WHERE ProductNumberInternal = '" . $_GET['pni'] . "' AND CustomerOrderSeqNumber = '" . $_GET['seq'] . "' AND CustomerOrderNumber = " . $_GET['on'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$sql = "SELECT * FROM customerorderdetail WHERE CustomerOrderNumber = " . $_GET['on'] . " ORDER BY CustomerOrderSeqNumber";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	if ( mysql_num_rows($result) != 0 ) {
		$i = 1;
		while ( $row = mysql_fetch_array($result) ) {
			$sql = "UPDATE customerorderdetail SET CustomerOrderSeqNumber = " . $i . " WHERE CustomerOrderNumber = '" . $_GET['on'] . "' AND CustomerOrderSeqNumber = '" .  $row['CustomerOrderSeqNumber'] . "'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$i = $i + 1;
		}
	}
	header("location: customers_customer_order_shipping.php?order_num=" . $_GET['on']);
	exit();
}



if ( isset($_REQUEST['OrderNum']) and $action == 'search' ) {
	$OrderNum = $_REQUEST['OrderNum'];
}
if ( isset($_REQUEST['CustomerPONumber']) and $action == 'search' ) {
	$CustomerPONumber = $_REQUEST['CustomerPONumber'];
}
//if ( isset($_REQUEST['CustomerID']) and $action == 'search' ) {
//	$CustomerID = $_REQUEST['CustomerID'];
//}
if ( isset($_REQUEST['customer_id']) and $action == 'search' ) {
	$customer_id = $_REQUEST['customer_id'];
}
if ( isset($_REQUEST['abelei_num']) and $action == 'search' ) {
	$abelei_num = $_REQUEST['abelei_num'];
}
if ( isset($_REQUEST['shipping_status']) and $action == 'search' ) {
	$shipping_status = $_REQUEST['shipping_status'];
}
if ( isset($_REQUEST['batch_sheet_created']) and $action == 'search' ) {
	$batch_sheet_created = $_REQUEST['batch_sheet_created'];
}
if ( isset($_REQUEST['customer']) and $action == 'search' ) {
	$customer = $_REQUEST['customer'];
}
if ( isset($_REQUEST['pne']) and isset($_REQUEST['bsn']) and $action == 'search' ) {
	$pne = $_REQUEST['pne'];
}
if ( isset($_REQUEST['pne']) and $action == 'search' ) {
	$pne = $_REQUEST['pne'];
}


if ( $_REQUEST['update'] != 1 ) {
	$form_status = "readonly=\"readonly\"";
} else {
	$form_status = "";
}



$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

$shippers = array("", "Airborne", "Christian Freight", "Common Carrier", "Company Truck", "Customer Pick Up", "DHL", "Fed Ex", "Please Call", "Taxair", "Tri-Air", "United Express", "UPS Ground");


include("inc_header.php");

?>



<script type="text/javascript">

$(document).ready(function(){
	$(":input[readonly]").addClass("readOnly");
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");
	$("#customer[readonly!=readonly]").autocomplete("search/customers_by_name.php", {
		cacheLength: 1,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max:50,
		multipleSeparator: "¬",
		scrollheight: 350,
		selectFirst: false
	});
	$("#customer[readonly!=readonly]").change(function() {
		if ("" == $("#customer").val())
		{
			$("#contact_name").val("");
			$("#contact_id").val("");
			$("#contactdiv").html("");
			$("#contact_name").unautocomplete();
		}
	});
	$("#customer[readonly!=readonly]").result(function(event, data, formatted) {
		if (data) {
			$("#contact_name").val("");
			$("#contact_id").val("");
			$("#contactdiv").html("");
			$("#contact_name").unautocomplete();
			$("#customer_id").val(data[1]);
	$("#contact_name[readonly!=readonly]").autocomplete("search/contacts_by_customer_id.php", {
		cacheLength: 1,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max:50,
		multipleSeparator: "¬",
		scrollheight: 350,
		selectFirst: false,
		extraParams: { c_id: function() { return $("#customer_id").val(); } }
	});
	$("#contact_name[readonly!=readonly]").result(function(event, data, formatted) {
		if (data)
		{
			$("#contact_name").val(data[0]);
			$("#contact_id").val(data[1]);
			$("#contactdiv").html(data[2]);
		}
	});
	// result_string=search('search/contacts_by_customer_id',data[1]);
			// update_id('update/contacts_by_customer_id','contactspan',data[1]);
		}
	});

	$("#contact_name[readonly!=readonly]").change(function() {
		if ("" == $("#customer").val())
		{
			$("input#contact_name").flushCache();
			$("input#contact_name").search();
			$("#contact_name").val("");
			$("#contact_id").val("");
			$("#contactdiv").html("");
		}
		if ("" == $("#contact_name").val())
		{
			$("#contact_id").val("");
			$("#contactdiv").html("");
		}
	});
	$(":submit").click(function() {
	});
	
		$("#Quantity").change(function() {
		if (!isNaN($(this).val()) && !isNaN($("#PackSize").val())) {
			$("#TotalQuantityOrdered").val($(this).val() * $("#PackSize").val());
		}
	});
	$("#PackSize").change(function() {
		if (!isNaN($(this).val()) && !isNaN($("#Quantity").val())) {
			$("#TotalQuantityOrdered").val($(this).val() * $("#Quantity").val());
		}
	});
});
function validate() {
	switch (document.getElementById("action").value)
	{
		case 'delete':
			var answer = confirm("Delete this order?")
			if (answer) { return true; } else { return false; }
			break;
		default:
			break;
	}
}


// O'CONNELL CHANGED client_id TO customer_id SINCE WE NO LONGER TIE CLIENTS TO ADDRESSES
// 08/27/2009
function checkID(address_type) {
	if ( document.header_info.customer_id.value != '' ) {
		popup("pop_select_customer_address.php?type=" + address_type + "&cid=" + document.header_info.customer_id.value)
	} else {
		alert("Please choose a Customer and Contact before selecting an address");
	}
}


function useBillingAddress() {
	if ( document.header_info.billing_same.checked == false ) {
		document.header_info.shipping_id.value = ""
		document.header_info.shipping_address1.value = ""
		document.header_info.shipping_address2.value = ""
		document.header_info.shipping_city.value = ""
		document.header_info.shipping_state.value = ""
		document.header_info.shipping_zip.value = ""
	} else {
		document.header_info.shipping_id.value = document.header_info.billing_id.value
		document.header_info.shipping_address1.value = document.header_info.billing_address1.value
		document.header_info.shipping_address2.value = document.header_info.billing_address2.value
		document.header_info.shipping_city.value = document.header_info.billing_city.value
		document.header_info.shipping_state.value = document.header_info.billing_state.value
		document.header_info.shipping_zip.value = document.header_info.billing_zip.value
	}
}


function delete_product(pni, seq, on) {
	if ( confirm('Are you sure you want to delete this item?') ) {
		document.location.href = "customers_customer_order_shipping.php?action=delete_product&pni=" + pni + "&seq=" + seq + "&on=" + on
	}
}

</script>



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
$(function() {
	$('#datepicker4').datepicker({
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
} ?>



<?php if ( $order_num == '' and $action != 'edit' ) { ?>

<table class="bounding">
<tr valign="top">
<td class="padded">

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<FORM ACTION="customers_customer_order_shipping.php" METHOD="get">
	<INPUT TYPE="hidden" NAME="action" VALUE="search" />
	<INPUT TYPE="hidden" NAME="pne" VALUE="<?php echo $pne;?>">
	<INPUT TYPE="hidden" NAME="bsn" VALUE="<?php echo $bsn;?>">
	<input type="hidden" name="readonly" value="<?php echo (""==$form_status ? "false" : "true")?>" />

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Order ID:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="OrderNum" VALUE="<?php echo $OrderNum;?>" SIZE="26"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Customer PO#:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="CustomerPONumber" VALUE="<?php echo $CustomerPONumber;?>" SIZE="26"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Customer:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" ID="customer" NAME="customer" VALUE="<?php echo $customer?>" SIZE=26>
			<INPUT TYPE="hidden" ID="customer_id" NAME="customer_id" VALUE="<?php echo $customer_id;?>"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>abelei#:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="abelei_num" VALUE="<?php echo $abelei_num;?>" SIZE="26"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Status:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><SELECT NAME="shipping_status" STYLE="font-size: 7pt">
					<?php
						if ( $shipping_status == 'Complete' ) { ?>
							<OPTION VALUE=""></OPTION>
							<OPTION VALUE="Complete" SELECTED>Complete</OPTION>
							<OPTION VALUE="Incomplete">Incomplete</OPTION>
						<?php } elseif ( $shipping_status == 'Incomplete' ) { ?>
							<OPTION VALUE=""></OPTION>
							<OPTION VALUE="Complete">Complete</OPTION>
							<OPTION VALUE="Incomplete" SELECTED>Incomplete</OPTION>
						<?php } else { ?>
							<OPTION VALUE="" SELECTED></OPTION>
							<OPTION VALUE="Complete">Complete</OPTION>
							<OPTION VALUE="Incomplete">Incomplete</OPTION>
						<?php }
					?>
				</SELECT></TD>
		</TR>
		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Batch sheet created:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><SELECT NAME="batch_sheet_created" STYLE="font-size: 7pt">
					<?php
						if ( $batch_sheet_created == 'Yes' ) { ?>
							<OPTION VALUE=""></OPTION>
							<OPTION VALUE="Yes" SELECTED>Yes</OPTION>
							<OPTION VALUE="No">No</OPTION>
						<?php } elseif ( $batch_sheet_created == 'No' ) { ?>
							<OPTION VALUE=""></OPTION>
							<OPTION VALUE="Yes">Yes</OPTION>
							<OPTION VALUE="No" SELECTED>No</OPTION>
						<?php } else { ?>
							<OPTION VALUE="" SELECTED></OPTION>
							<OPTION VALUE="Yes">Yes</OPTION>
							<OPTION VALUE="No">No</OPTION>
						<?php }
					?>
				</SELECT></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

			<TR>
			<TD><B>Order Date:</B>&nbsp;&nbsp;&nbsp;</TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD>
			<INPUT TYPE="text" SIZE="12" NAME="start_date" id="datepicker1" VALUE="<?php
				if ( $start_date != '' ) {
					echo date("m/d/Y", strtotime($start_date));
				}
				?>">
				to 
			<INPUT TYPE="text" SIZE="12" NAME="end_date" id="datepicker2" VALUE="<?php
				if ( $end_date != '' ) {
					echo date("m/d/Y", strtotime($end_date));
				}
				?>">
			</TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="12"></TD>
		</TR>

		<TR>
					<TD colspan=3><INPUT style="float:right" TYPE="submit" class="submit_medium" VALUE="Search"><INPUT style="margin-top:.5em;float:left" TYPE="button" class="submit new" VALUE="New Customer Order" onClick="window.location='customers_customer_order_shipping.php?action=edit&update=1'"></TD>
		</TR>
	</FORM>
	</TABLE><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" ALIGN=CENTER>
	<TR><FORM>
		<TD>
		<INPUT TYPE="button" VALUE="Customer Order Review" onClick="popup('reports/inventory_reports_customer_order_review.php')" CLASS="submit_normal">
		<!-- <IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"> -->
		</TD>
	</TR></FORM>
</TABLE>

</TD></TR></TABLE>
<BR><BR>

<?php } ?>



<?php

if ( $action == 'search' ) {

	if ( $start_date != '' or $end_date != '' ) {
		if (''==$start_date)
			$start_date = '01/01/1900';
		if (''==$end_date)
			$end_date = date('m/d/Y');
		$start_date_parts = explode("/", $start_date);
		$end_date_parts = explode("/", $end_date);
		$mysql_start_date = $start_date_parts[2] . "-" . $start_date_parts[0] . "-" . $start_date_parts[1];
		$mysql_end_date = $end_date_parts[2] . "-" . $end_date_parts[0] . "-" . $end_date_parts[1];
		$date_filter = " AND (OrderDate >= '" . $mysql_start_date . "' AND OrderDate <= '" . $mysql_end_date . "')";
	} else {
		$date_filter = "";
	}

	if ( $OrderNum != '' ) {
		$OrderNumber_clause = " AND OrderNumber LIKE ('%$OrderNum%')";
	} else {
		$OrderNumber_clause = "";
	}
	// if ( $customer_id != '' ) {
		// $CustomerID_clause = " AND CustomerID = " . $customer_id;
	// } else {
		// $CustomerID_clause = "";
	// }
	if ( $customer != '' ) {
		$CustomerName_clause = " AND name LIKE ('%" . escape_data($customer) . "%')";
	} else {
		$CustomerName_clause = "";
	}
	if ( $CustomerPONumber != '' ) {
		$CustomerPONumber_clause = " AND CustomerPONumber LIKE ('%$CustomerPONumber%')";
	} else {
		$CustomerPONumber_clause = "";
	}
	if ( $shipping_status == 'Complete' ) {
		$status_clause = 
		" AND ( 
			( SELECT COUNT(CustomerOrderNumber) 
				FROM customerorderdetail 
				WHERE OrderNumber = CustomerOrderNumber 
					AND ShipDate IS NULL) = 0 
			AND (SELECT COUNT(CustomerOrderNumber) 
				FROM customerorderdetail 
				WHERE OrderNumber = CustomerOrderNumber) <> 0 )";
	} elseif ( $shipping_status == 'Incomplete' ) {
		$status_clause = 
		" AND ( 
			( SELECT COUNT(CustomerOrderNumber) 
				FROM customerorderdetail 
				WHERE OrderNumber = CustomerOrderNumber 
					AND ShipDate IS NULL) <> 0 
			OR ( SELECT COUNT(CustomerOrderNumber) 
				FROM customerorderdetail 
				WHERE OrderNumber = CustomerOrderNumber ) = 0 )";
	} else {
		$status_clause = "";
	}
//batchsheetcustomerinfo   CustomerPONumber
	if ( $batch_sheet_created == 'No' ) {
		$batch_sheet_clause = " AND (SELECT COUNT(CustomerPONumber) FROM batchsheetcustomerinfo WHERE customerordermaster.CustomerPONumber = batchsheetcustomerinfo.CustomerPONumber) = 0";
	} elseif ( $batch_sheet_created == 'Yes' ) {
		$batch_sheet_clause = " AND (SELECT COUNT(CustomerPONumber) FROM batchsheetcustomerinfo WHERE customerordermaster.CustomerPONumber = batchsheetcustomerinfo.CustomerPONumber) <> 0";
	} else {
		$batch_sheet_clause = "";
	}

	if ( $abelei_num != '' ) {
		$abelei_num_clause = " AND EXISTS (SELECT ProductNumberExternal
		FROM customerorderdetail
		LEFT JOIN externalproductnumberreference ON customerorderdetail.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
		WHERE externalproductnumberreference.ProductNumberExternal LIKE ('%$abelei_num%') AND CustomerOrderNumber = OrderNumber)";
	} else {
		$abelei_num_clause = "";
	}

	if ( $bsn != '' ) {
		if ( $pne != '' ) {
			$sql = "SELECT epnr.ProductNumberInternal, pm.Intermediary, pm.FinalProductNotCreatedByAbelei
					FROM externalproductnumberreference AS epnr LEFT JOIN productmaster AS pm ON pm.ProductNumberInternal = epnr.ProductNumberInternal
					WHERE ProductNumberExternal = '$pne'";
			$result_pni = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$row_pni = mysql_fetch_array($result_pni);
			// if not intermediary nor completed outside of abelei
			if ( 0 == $row_pni[Intermediary] ) {
				$pni_clause = " AND ProductNumberInternal = $row_pni[ProductNumberInternal]";
			} else 
			//if  ( 0 == $row_pni[Intermediary] ) 
			{
				$sql = "SELECT DISTINCT fd.ProductNumberInternal, epnr.ProductNumberExternal 
						FROM formulationdetail AS fd 
							LEFT JOIN externalproductnumberreference AS epnr 
								ON (fd.ProductNumberInternal = epnr.ProductNumberInternal) 
						WHERE IngredientProductNumber='$row_pni[ProductNumberInternal]'";
				$result_ipni = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$row_ipni = mysql_fetch_array($result_ipni);
				$pni_clause = " AND ProductNumberInternal IN ($row_ipni[ProductNumberInternal]";
				while ($row_ipni = mysql_fetch_array($result_ipni)) {
					$pni_clause .= ", ".$row_ipni[ProductNumberInternal];
				}
				$pni_clause .= ")";
			}
		} else {
			$pni_clause = "";
		}

		$sql = 
		"SELECT (
				SELECT COUNT(CustomerOrderNumber) 
				FROM customerorderdetail 
				WHERE OrderNumber = CustomerOrderNumber AND 
					ShipDate IS NULL) 
			AS shipping_count, (
					SELECT COUNT(CustomerOrderNumber) 
					FROM customerorderdetail 
					WHERE OrderNumber = CustomerOrderNumber)
			AS li_count, (
				SELECT COUNT(CustomerPONumber) 
				FROM batchsheetcustomerinfo 
				WHERE customerordermaster.CustomerPONumber = batchsheetcustomerinfo.CustomerPONumber) 
			AS po_count, OrderNumber, CustomerID, CustomerPONumber, OrderDate, RequestedDeliveryDate, 
			customer_id, name, customerorderdetail.ProductNumberInternal, 
			customerorderdetail.CustomerOrderSeqNumber, customerorderdetail.CustomerCodeNumber 
		FROM customerordermaster
			LEFT JOIN customerorderdetail ON customerordermaster.OrderNumber = customerorderdetail.CustomerOrderNumber
			LEFT JOIN customers ON customerordermaster.CustomerID = customers.customer_id
		WHERE 1=1 " . $pni_clause . $abelei_num_clause . $OrderNumber_clause . $CustomerName_clause . $CustomerPONumber_clause . $date_filter . $status_clause . $batch_sheet_clause . "
		ORDER BY OrderNumber";
	} else {
		$sql = 
			"SELECT (
					SELECT COUNT(CustomerOrderNumber) 
					FROM customerorderdetail 
					WHERE OrderNumber = CustomerOrderNumber AND ShipDate IS NULL)
				AS shipping_count, (
					SELECT COUNT(CustomerOrderNumber) 
					FROM customerorderdetail 
					WHERE OrderNumber = CustomerOrderNumber)
				AS li_count, (
					SELECT COUNT(CustomerPONumber) 
					FROM batchsheetcustomerinfo 
					WHERE customerordermaster.CustomerPONumber = batchsheetcustomerinfo.CustomerPONumber) 
				AS po_count, OrderNumber, CustomerID, CustomerPONumber, OrderDate, RequestedDeliveryDate, customer_id, name 
			FROM customerordermaster 
				LEFT JOIN customers ON customerordermaster.CustomerID = customers.customer_id 
			WHERE 1=1 " . $abelei_num_clause . $OrderNumber_clause . $CustomerName_clause . $CustomerPONumber_clause . $date_filter . $status_clause . $batch_sheet_clause . "
			ORDER BY OrderNumber";
	}

	//echo $sql . "<BR>";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	$bg = 0; 
	if ( $c > 0 ) { ?>
		
		<TABLE BORDER=0 CELLSPACING="0" CELLPADDING="3">
			<TR VALIGN=BOTTOM>
				<TD></TD>
				<TD><B>Customer</B></TD>
				<TD ALIGN=CENTER><B>Order#</B></TD>
				<TD><B>PO#</B></TD>

				<?php if ( $bsn != '' ) { ?>
					<TD><B>Cust Code</B></TD>
					<TD ALIGN=CENTER><B>Order Seq#</B></TD>
					<TD ALIGN=CENTER><B>Product# Internal</B></TD>
				<?php } ?>

				<TD><B>Order Date</B></TD>
				<td><b>Due Date</b></td>
				<td><b>Status</b></td>
				<td><b>Batch sheet created</b></td>
				<?php if ( $bsn != '' ) { ?>
					<TD></TD>
				<?php } ?>
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
				$sql = "SELECT OrderNumber 
				FROM customerordermaster
				LEFT JOIN customerorderdetail ON customerordermaster.OrderNumber = customerorderdetail.CustomerOrderNumber
				WHERE ShipDate IS NULL
				AND BilledDate IS NULL
				AND customerordermaster.OrderNumber = " . $row['OrderNumber'] . "
				AND (SELECT COUNT(CustomerPONumber) FROM batchsheetcustomerinfo WHERE customerordermaster.CustomerPONumber = batchsheetcustomerinfo.CustomerPONumber) = 0";
				$result_delete = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$k = mysql_num_rows($result_delete);
				if ( $k > 0 ) { ?>
					<A HREF="JavaScript:delete_order(<?php echo($row['OrderNumber']);?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A>
				<?php } ?>
				</TD>

				<TD><A HREF="customers_customer_order_shipping.php?order_num=<?php echo $row['OrderNumber'];?>"><?php echo $row['name'];?></A></TD>
				<TD ALIGN=CENTER><A HREF="customers_customer_order_shipping.php?order_num=<?php echo $row['OrderNumber'];?>"><?php echo $row['OrderNumber'];?></A></TD>
				<TD><A HREF="customers_customer_order_shipping.php?order_num=<?php echo $row['OrderNumber'];?>"><?php echo $row['CustomerPONumber'];?></A></TD>

				<?php if ( $bsn != '' ) { ?>
					<TD><?php echo $row['CustomerCodeNumber'];?></TD>
					<TD ALIGN=CENTER><?php echo $row['CustomerOrderSeqNumber'];?></TD>
					<TD ALIGN=CENTER><?php echo $row['ProductNumberInternal'];?></TD>
				<?php } ?>

				<TD><?php
				if ( $row['OrderDate'] != '' ) {
					echo date("n/j/Y", strtotime($row['OrderDate']));
				}
				?></TD>
				<TD><?php
				if ( $row['RequestedDeliveryDate'] != '' ) {
					echo date("n/j/Y", strtotime($row['RequestedDeliveryDate']));
				}
				?></TD>
				<TD><?php
				if ( 0 < $row [li_count] && 0 == $row['shipping_count'] ) {
					echo "Complete";
				} else {
					echo "Incomplete";
				}
				?></TD>
				<TD ALIGN=CENTER><?php
				if ( $row['po_count'] == 0 ) {
					echo "No";
				} else {
					echo "Yes";
				}
				?></TD>
				<?php if ( $bsn != '' ) { ?>
					<FORM><TD>
					<INPUT TYPE="button" CLASS="submit" NAME="Choose" VALUE="Choose for Batch" onClick="popup('pop_select_customer_order.php?action=edit&bsn=<?php echo $bsn;?>&con=<?php echo $row['OrderNumber'];?>&seq=<?php echo $row['CustomerOrderSeqNumber'];?>&pni=<?php echo $row['ProductNumberInternal'];?>',700,830)">
					</TD></FORM>
				<?php } ?>
				<FORM><TD>
				<INPUT TYPE="button" CLASS="submit" VALUE="Print Order" onClick="popup('reports/print_customer_order.php?order_num=<?php echo $row['OrderNumber'];?>',820,830)">
				</TD></FORM>
			</TR>
		<?php } ?>

		</TABLE><BR>
	<?php
	} else {
		echo "No matches found";
	}

}

?>









<?php if ( $order_num != '' or $action == 'edit' ) { ?>

	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

	<FORM NAME="header_info" ACTION="customers_customer_order_shipping.php" METHOD="post" onSubmit="return updateHiddenField(this)">
	<INPUT TYPE="hidden" NAME="order_num" VALUE="<?php echo $order_num;?>">
	<INPUT TYPE="hidden" NAME="action" VALUE="edit">
	<INPUT TYPE="hidden" NAME="update" VALUE="1">

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
				<TR STYLE="background-color:#FFFF99">
					<TD COLSPAN=2><B>Order Info</B></TD>
				</TR>
				<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="1"></TD></TR>

				<TR>
					<TD STYLE="text-align:right"><NOBR><B>Order#:</B></NOBR></TD>
					<TD><?php echo $OrderNumber?></TD>
					<INPUT TYPE="hidden" NAME="OrderNumber" VALUE="<?php echo $OrderNumber;?>">
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Order Date:</B></TD>
					<TD><?php echo $OrderDate; ?></TD>
					<INPUT TYPE="hidden" NAME="OrderDate" VALUE="<?php echo $OrderDate;?>">
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Customer:</B></TD>
					<TD><INPUT TYPE="text" ID="customer" NAME="customer" VALUE="<?php echo $customer;?>" SIZE=26 <?php echo $form_status;?>>
					<INPUT TYPE="hidden" ID="customer_id" NAME="customer_id" VALUE="<?php echo $customer_id;?>"></TD>
				</TR>
				<TR VALIGN=TOP>
					<TD STYLE="text-align:right"><B>Contact:</B></TD>
					<TD><INPUT TYPE="text" ID="contact_name" NAME="contact_name" SIZE=26 VALUE="<?php echo $contact_name;?>" <?php echo $form_status;?>>
					<INPUT TYPE="hidden" ID="contact_id" NAME="contact_id" VALUE="<?php echo $ContactID;?>">
					<DIV ID="contactdiv"></div></td>
				</tr>

				<TR>
					<TD STYLE="text-align:right"><NOBR><B>P.O. Number:</B></NOBR></TD>
					<TD><INPUT TYPE="text" NAME="CustomerPONumber" VALUE="<?php echo $CustomerPONumber?>" SIZE="26" <?php echo $form_status;?>></TD>
				</TR>

				<TR>
					<TD STYLE="text-align:right"><B>Ship Via:</B></TD>
					<TD><SELECT NAME="ShipVia" <?php echo $combo_status;?> <?php echo $form_status;?> STYLE="width:160px">
					<?php
					$c = 0;
					$db_value_found = false;
					foreach ( $shippers as $value ) {
						if ( $value == $ShipVia ) {
							echo "<OPTION VALUE='" . $value . "' SELECTED>" . $value . "</OPTION>";
							$db_value_found = true;
						} else {
							echo "<OPTION VALUE='" . $value . "'>" . $value . "</OPTION>";
						}
						$c++;
					}
					if ( $db_value_found == false ) {
						echo "<OPTION VALUE=\"" . $ShipVia . "\" SELECTED>" . $ShipVia . "</OPTION>";
					}
					?>
					</SELECT></NOBR>
					<INPUT TYPE="hidden" NAME="ShipViaHidden" VALUE="">
		
					</TD>
				</TR>

				<TR VALIGN=MIDDLE>
					<TD STYLE="text-align:right"><B>Deliver By:</B></TD>
					<TD><INPUT TYPE="text" SIZE="12" NAME="RequestedDeliveryDate" id="datepicker3" VALUE="<?php
					if ( $RequestedDeliveryDate != '' ) {
						echo date("m/d/Y", strtotime($RequestedDeliveryDate));
					}
					?>" <?php echo $form_status;?>></TD>
				</TR>

				<TR>
					<TD STYLE="text-align:right"><NOBR><B>Taken By:</B></NOBR></TD>
					<TD><SELECT NAME="OrderTakenByEmployeeID" <?php echo $form_status;?> STYLE="font-size: 7pt">
					<OPTION VALUE=""></OPTION>
					<?php
					$sql = "SELECT user_id, first_name, last_name FROM users WHERE active = 1 ORDER BY last_name";
					$result_sales = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					while ( $row_sales = mysql_fetch_array($result_sales) ) {
						if ( $OrderTakenByEmployeeID == $row_sales['user_id'] ) {
							echo "<OPTION VALUE='$row_sales[user_id]' SELECTED>$row_sales[first_name] $row_sales[last_name]</OPTION>";
						} else {
							echo "<OPTION VALUE='$row_sales[user_id]'>$row_sales[first_name] $row_sales[last_name]</OPTION>";
						}
					}
					?></SELECT></TD>
				</TR>

			</TABLE>



			</TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD BGCOLOR="#CDCDCD"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="2" HEIGHT="1"></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD>



			<TABLE BORDER="0" CELLPADDING="3" CELLSPACING="0">
				<TR STYLE="background-color:#FFFF99">
					<TD COLSPAN=2><B>Requests</B></TD>
				</TR>
				<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="1"></TD></TR>

				<TR>
					<TD>

					<?php if ( $C_of_A_Requested == 1 ) { ?>
						<INPUT TYPE="checkbox" NAME="C_of_A_Requested" ID="C_of_A_Requested" VALUE="1" CHECKED <?php echo $form_status;?>/><label for="C_of_A_Requested">C of A Requested</label><BR>
					<?php } else { ?>
						<INPUT TYPE="checkbox" NAME="C_of_A_Requested" ID="C_of_A_Requested" VALUE="1" <?php echo $form_status;?>/><label for="C_of_A_Requested">C of A Requested</label><BR>
					<?php } ?>

					<?php if ( $MSDS_Requested == 1 ) { ?>
						<INPUT TYPE="checkbox" NAME="MSDS_Requested" ID="MSDS_Requested" VALUE="1" CHECKED <?php echo $form_status;?>/><label for="MSDS_Requested">MSDS Requested</label><BR>
					<?php } else { ?>
						<INPUT TYPE="checkbox" NAME="MSDS_Requested" ID="MSDS_Requested" VALUE="1" <?php echo $form_status;?>/><label for="MSDS_Requested">MSDS Requested</label><BR>
					<?php } ?>

					<?php if ( $NAFTA_Requested == 1 ) { ?>
						<INPUT TYPE="checkbox" NAME="NAFTA_Requested" ID="NAFTA_Requested" VALUE="1" CHECKED <?php echo $form_status;?>/><label for="NAFTA_Requested">NAFTA Requested</label><BR>
					<?php } else { ?>
						<INPUT TYPE="checkbox" NAME="NAFTA_Requested" ID="NAFTA_Requested" VALUE="1" <?php echo $form_status;?>/><label for="NAFTA_Requested">NAFTA Requested</label><BR>
					<?php } ?>

					<?php if ( $Hazardous_Info_Requested == 1 ) { ?>
						<NOBR><INPUT TYPE="checkbox" NAME="Hazardous_Info_Requested" ID="Hazardous_Info_Requested" VALUE="1" CHECKED <?php echo $form_status;?>/><label for="Hazardous_Info_Requested">Hazardous Info Requested</label></NOBR><BR>
					<?php } else { ?>
						<NOBR><INPUT TYPE="checkbox" NAME="Hazardous_Info_Requested" ID="Hazardous_Info_Requested" VALUE="1" <?php echo $form_status;?>/><label for="Hazardous_Info_Requested">Hazardous Info Requested</label></NOBR><BR>
					<?php } ?>

					<?php if ( $Kosher == 1 ) { ?>
						<INPUT TYPE="checkbox" NAME="Kosher" ID="Kosher" VALUE="1" CHECKED <?php echo $form_status;?>/><label for="Kosher">Kosher</label><BR>
					<?php } else { ?>
						<INPUT TYPE="checkbox" NAME="Kosher" ID="Kosher" VALUE="1" <?php echo $form_status;?>/><label for="Kosher">Kosher</label><BR>
					<?php } ?>

					</TD>
				</TR>
			</TABLE>



			</TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD BGCOLOR="#CDCDCD"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="2" HEIGHT="1"></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD>



			<TABLE BORDER="0" CELLPADDING="3" CELLSPACING="0">
				<TR STYLE="background-color:#FFFF99">
					<TD COLSPAN=2><B>Bill To</B></TD>
				</TR>
				<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="1"></TD></TR>
				<?php if (""==$form_status) { ?>
				<TR>
					<TD COLSPAN=2 ALIGN=RIGHT><INPUT TYPE="button" VALUE="Choose Address" CLASS="submit" onClick="checkID('b')" <?php echo $form_status;?>></TD>
				</TR><?php } ?>
				<INPUT TYPE="hidden" NAME="billing_id" VALUE="<?php echo $BillToLocationID;?>">

				<TR>
					<TD STYLE="text-align:right"><B>Address 1:</B></TD>
					<TD><INPUT TYPE="text" NAME="billing_address1" VALUE="<?php echo $billing_address1;?>" SIZE="26" <?php echo $form_status;?>></TD>
					<!-- Shelley would also like an option for "new" so she can add an address on the fly here -->
					<!--    Ideally it would not interrupt flow too much - if it takes her to a new page to input new address we should record the current state -->
					<!--    in session variables to repopulate this screen when a use returns after adding a new address -->
				</TR>
				<TR>
					<TD STYLE="text-align:right"><NOBR><B>Address 2:</B></NOBR></TD>
					<TD><INPUT TYPE="text" NAME="billing_address2" VALUE="<?php echo $billing_address2;?>" SIZE="26" <?php echo $form_status;?>></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>City:</B></TD>
					<TD><INPUT TYPE="text" NAME="billing_city" VALUE="<?php echo $billing_city;?>" SIZE="26" <?php echo $form_status;?>></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>State:</B></TD>
					<TD><select name="billing_state" id="billing_state" <?php echo $form_status;?>><?php foreach($states as $val){ echo "<option".($billing_state==$val?" selected=\"selected\"":"")." value=\"$val\">$val</option>\n"; } ?></select></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>ZIP:</B></TD>
					<TD><INPUT TYPE="text" NAME="billing_zip" VALUE="<?php echo $billing_zip;?>" SIZE="15" <?php echo $form_status;?>></TD>
				</TR>
			</TABLE>



			</TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD BGCOLOR="#CDCDCD"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="2" HEIGHT="1"></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD>



			<TABLE BORDER="0" CELLPADDING="3" CELLSPACING="0">
				<TR STYLE="background-color:#FFFF99">
					<TD COLSPAN=2><B>Ship To</B></TD>
				</TR>
				<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="1"></TD></TR>
				<?php if (""==$form_status) { ?>
				<TR>
					<TD COLSPAN=2 ALIGN=RIGHT><INPUT TYPE="button" VALUE="Choose Address" CLASS="submit" onClick="checkID('s')" <?php echo $form_status;?>></TD>
				</TR>

				<TR>
					<TD COLSPAN=2 ALIGN=RIGHT>
					<?php if ( $billing_same == 1 ) { ?>
						<INPUT TYPE="checkbox" NAME="billing_same" id="billing_same" VALUE="1" onClick="useBillingAddress()" CHECKED /> <I STYLE="font-size:8pt"><label for="billing_same">Same As Bill To</label></I>
					<?php } else { ?>
						<INPUT TYPE="checkbox" NAME="billing_same" id="billing_same" VALUE="1" onClick="useBillingAddress()" /> <I STYLE="font-size:8pt"><label for="billing_same">Same As Bill To</label></I>
					<?php } ?>
					</TD>
				</TR>
				<?php } ?>
				<INPUT TYPE="hidden" NAME="shipping_id" VALUE="<?php echo $ShipToLocationID;?>">
					
				<TR>
					<TD STYLE="text-align:right"><B>Address 1:</B></TD>
					<TD><INPUT TYPE="text" NAME="shipping_address1" VALUE="<?php echo $shipping_address1;?>" <?php echo $form_status;?> SIZE="26"></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><NOBR><B>Address 2:</B></NOBR></TD>
					<TD><INPUT TYPE="text" NAME="shipping_address2" VALUE="<?php echo $shipping_address2;?>" <?php echo $form_status;?> SIZE="26"></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>City:</B></TD>
					<TD><INPUT TYPE="text" NAME="shipping_city" VALUE="<?php echo $shipping_city;?>" SIZE="26" <?php echo $form_status;?>></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>State:</B></TD>
					<TD><select name="shipping_state" id="shipping_state" <?php echo $form_status;?>><?php foreach($states as $val){ echo "<option".($shipping_state==$val?" selected=\"selected\"":"")." value=\"$val\">$val</option>\n"; } ?></select></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>ZIP:</B></TD>
					<TD><INPUT TYPE="text" NAME="shipping_zip" VALUE="<?php echo $shipping_zip;?>" SIZE="15" <?php echo $form_status;?>></TD>
				</TR>
			</TABLE>

				</TD>
			</TR>



			<TR><TD COLSPAN=15><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="10"></TD></TR>
			<TR><TD COLSPAN=15 BGCOLOR="#CDCDCD"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="2"></TD></TR>
			<TR><TD COLSPAN=15><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="10"></TD></TR>

			<TR>
				<TD COLSPAN=15 VALIGN=TOP><B>Special Instructions:</B><BR><TEXTAREA NAME="SpecialInstructions" ROWS=4 COLS=80 <?php echo $form_status;?>><?php echo $SpecialInstructions;?></TEXTAREA></TD>
			</TR>

			<TR><TD COLSPAN=15><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="10"></TD></TR>
			<TR><TD COLSPAN=15 BGCOLOR="#CDCDCD"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="2"></TD></TR>
			<TR><TD COLSPAN=15><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="10"></TD></TR>

			<TR VALIGN=TOP>
				<TD COLSPAN=15 ALIGN=RIGHT>
				<?php if ( $form_status == '' and $_REQUEST['update_ing'] == '' ) { ?>
					<INPUT TYPE="submit" VALUE="Save" CLASS="submit"> 
					<INPUT TYPE="button" VALUE="Cancel" onClick="window.location='customers_customer_order_shipping.php?order_num=<?php echo $order_num;?>'" CLASS="submit">
				<?php } elseif ( $form_status != '' and $_REQUEST['update_ing'] == '' ) { ?>
					<INPUT TYPE="button" VALUE="Edit" onClick="window.location='customers_customer_order_shipping.php?order_num=<?php echo $order_num;?>&update=1'" CLASS="submit"> 
					<INPUT TYPE="button" VALUE="Cancel" onClick="window.location='customers_customer_order_shipping.php'" CLASS="submit">
				<?php } ?>
				</TD>
			</TR>

		</TABLE>

	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</FORM><BR>






	<!-- ADD PRODUCT -->
	<?php if ( $form_status != '' and $_REQUEST['update_prod'] == '' ) { ?>
		<?php
		$sql = "SELECT MAX(CustomerOrderSeqNumber) AS max_seq FROM customerorderdetail WHERE CustomerOrderNumber = '" . $order_num . "'";
		$result_count = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row_count = mysql_fetch_array($result_count);
		$max_seq = $row_count['max_seq'];
		$CustomerOrderSeqNumber = ($max_seq + 1);
		?>
		<!-- 
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
		<TABLE CELLPADDING=7 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
 -->
		<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#FFFFFF"><TR><TD ALIGN=CENTER>
		<FORM NAME="add_prod" ACTION="customers_customer_order_shipping.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="action" VALUE="<?php echo $action;?>">
		<INPUT TYPE="hidden" NAME="order_num" VALUE="<?php echo $order_num;?>">
		<INPUT TYPE="hidden" NAME="add_prod" VALUE="1">
		<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>">

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<TR>
				<TD>
			
				<TABLE ALIGN=RIGHT BORDER=0 CELLSPACING="0" CELLPADDING="3" BORDERCOLOR="#CDCDCD">
		
					<TR VALIGN=BOTTOM>
						<TD><B>Seq#</B></TD>
						<TD><B>abelei#</B></TD>
						<TD>&nbsp;</TD>
						<!-- <TD><B>Description</B></TD> -->
						<td><b>lbs / pail</b></td>
						<td><b>lbs / drum</b></td>
						<TD><B>Qty</B></TD>
						<TD><B>Pack Size</B></TD>
						<TD><B>Units</B></TD>
						<TD><B>Qty Ordered</B></TD>
						<TD><B>Cust Code</B></TD>
						<TD><B>Billed Date</B></TD>
						<TD>&nbsp;</TD>
					</TR>
					<TR>
						<TD><INPUT TYPE="text" NAME="CustomerOrderSeqNumber" VALUE="<?php echo $CustomerOrderSeqNumber;?>" SIZE="5" STYLE="text-align:right"></TD>
						<TD><INPUT TYPE="text" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>" SIZE="12" READONLY></TD>
						<TD><A HREF="JavaScript:newWindow=openWin('pop_select_flavor.php','','width=800,height=600,toolbar=0,location=0,scrollBars=1,resizable=1,left=30,top=30'); newWindow.focus()"><IMG SRC="images/zoom.png" ALT="search" WIDTH="16" HEIGHT="16" BORDER=0></A></TD>

						<!-- <TD><INPUT TYPE="text" NAME="Description" VALUE="<?php //echo $Description;?>" SIZE="26"></TD> -->

						<TD><INPUT TYPE="text" NAME="LbsPerPail" ID="LbsPerPail" VALUE="" SIZE="10" readonly='readonly'></TD>
						<TD><INPUT TYPE="text" NAME="LbsPerDrum" ID="LbsPerDrum" VALUE="" SIZE="10" readonly='readonly'></TD>
						<TD><INPUT TYPE="text" NAME="Quantity" ID="Quantity" VALUE="<?php echo $Quantity;?>" SIZE="10"></TD>
						<TD><INPUT TYPE="text" NAME="PackSize" ID="PackSize" VALUE="<?php echo $PackSize;?>" SIZE="10"></TD>
						<TD><!-- <INPUT TYPE="text" NAME="UnitOfMeasure" VALUE="<?php echo $UnitOfMeasure;?>" SIZE="10"> -->
						<select class="input-box" name="UnitOfMeasure">
						<option value="grams">grams</option>
						<option value="kg">kg</option>
						<option value="lbs">lbs</option>
						</select></TD>
						<TD><INPUT TYPE="text" NAME="TotalQuantityOrdered" ID="TotalQuantityOrdered" VALUE="<?php echo $TotalQuantityOrdered;?>" SIZE="10"></TD>
						<TD><INPUT TYPE="text" NAME="CustomerCodeNumber" VALUE="<?php echo $CustomerCodeNumber;?>" SIZE="10"></TD>
						<TD><INPUT TYPE="text" SIZE="12" NAME="BilledDate" id="datepicker4" VALUE="<?php
						if ( $BilledDate != '' ) {
							echo date("m/d/Y", strtotime($BilledDate));
						}
						?>"></TD>

						<TD><INPUT TYPE="submit" VALUE="Add" CLASS="submit"></TD>
					</TR>
				</TABLE>

		</TD></TR></TABLE>

		</TD></TR></TABLE>
		<!-- 
</TD></TR></TABLE>
		</TD></TR></TABLE>
 -->
		</FORM>

	<?php } ?>
	<!-- ADD PRODUCT -->






	<?php

	$sql = "SELECT customerorderdetail.*, externalproductnumberreference.ProductNumberExternal FROM customerorderdetail INNER JOIN externalproductnumberreference USING (ProductNumberInternal) WHERE CustomerOrderNumber = '" . $order_num . "' ORDER BY CustomerOrderSeqNumber";
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

		<TABLE ALIGN=RIGHT BORDER=1 CELLSPACING="0" CELLPADDING="3" BORDERCOLOR="#CDCDCD">

			<TR VALIGN=BOTTOM>
				<TD COLSPAN=2>&nbsp;</TD>
				<TD><B>Seq#</B></TD>
				<TD><B>abelei#</B></TD>
				<TD><B>Description</B></TD>
				<TD ALIGN=RIGHT><B>Qty</B></TD>
				<TD ALIGN=RIGHT><B>Pack Size</B></TD>
				<TD><B>Units</B></TD>
				<TD ALIGN=RIGHT><B>Qty Ordered</B></TD>
				<!-- <TD><B>Vendor</B></TD>
				<TD><B>Cost per lb.</B></TD>
				<TD><B>Ext. cost</B></TD> -->
				<TD><B>Cust Code</B></TD>
				<TD WIDTH=100><IMG SRC="images/spacer.gif" WIDTH=100 HEIGHT=1><BR><B>Shipped</B></TD>
				<TD WIDTH=100><IMG SRC="images/spacer.gif" WIDTH=100 HEIGHT=1><BR><B>Billed Date</B></TD>
				<TD>&nbsp;</TD>
			</TR>
		
		<?php
		$x = 5;
		while ( $row = mysql_fetch_array($result) ) {

			if ( $update_prod == '' ) {

				echo "<script type='text/javascript'>\n";
				echo "$(function() {\n";
					echo "$('#datepicker" . $x . "').datepicker({\n";
						echo "showOn: 'button', buttonImage: 'images/calendar.gif', buttonImageOnly: true,\n";
						echo "changeMonth: true,\n";
						echo "changeYear: true\n";
					echo "});\n";
				echo "});\n";
				echo "</script>\n";
				$current_div1 = "datepicker" . $x;
				$x++;

				echo "<script type='text/javascript'>\n";
				echo "$(function() {\n";
					echo "$('#datepicker" . $x . "').datepicker({\n";
						echo "showOn: 'button', buttonImage: 'images/calendar.gif', buttonImageOnly: true,\n";
						echo "changeMonth: true,\n";
						echo "changeYear: true\n";
					echo "});\n";
				echo "});\n";
				echo "</script>\n";
				$current_div2 = "datepicker" . $x;
				$x++;

			}

			$BilledDate = $row['BilledDate'];
			$ShipDate = $row['ShipDate'];

 			if ( $row['ShipDate'] == '' ) {
 				$shipped = false;
 			} else {
 				$shipped = true;
 			}

			?>

			<TR VALIGN=MIDDLE>
			<FORM NAME="line_items" ACTION="customers_customer_order_shipping.php" METHOD="post">
			<INPUT TYPE="hidden" NAME="action" VALUE="<?php echo $action;?>">
			<INPUT TYPE="hidden" NAME="update_prod" VALUE="<?php echo $update_prod;?>">
			<INPUT TYPE="hidden" NAME="order_num" VALUE="<?php echo $order_num;?>">
			<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $row['ProductNumberInternal'];?>">
			<INPUT TYPE="hidden" NAME="CustomerOrderSeqNumber" VALUE="<?php echo $row['CustomerOrderSeqNumber'];?>">
			<INPUT TYPE="hidden" NAME="edit_prod" VALUE="1">
				<TD>
				<?php if ( $form_status != '' and $_REQUEST['update_prod'] == '' and !$shipped ) { ?>
					<INPUT TYPE="button" VALUE="x" CLASS="submit" onClick="delete_product('<?php echo $row['ProductNumberInternal'];?>', '<?php echo $row['CustomerOrderSeqNumber'];?>', '<?php echo $order_num;?>')">
				<?php } else { ?>
					&nbsp;
				<?php } ?>
				</TD>
				<TD>
				<?php if ( $form_status != '' and !$shipped ) { ?>
					<?php if ( $_REQUEST['update_prod'] == $row['ProductNumberInternal'] ) { ?>
						<NOBR><INPUT TYPE="submit" VALUE="Save" CLASS="submit"> <INPUT TYPE="button" VALUE="Cancel" onClick="location.href='customers_customer_order_shipping.php?order_num=<?php echo $order_num;?>'" CLASS="submit"></NOBR>
					<?php } else { ?>
						<INPUT TYPE="button" VALUE="Edit" CLASS="submit" onClick="window.location='customers_customer_order_shipping.php?action=edit&update_prod=<?php echo $row['ProductNumberInternal'];?>&order_num=<?php echo $order_num;?>'">
					<?php } ?>
				<?php } else { ?>
					&nbsp;
				<?php } ?>
				</TD>
				<?php if ( $_REQUEST['update_prod'] == $row['ProductNumberInternal'] and !$shipped ) {
					$prod_form_status = "";
				} else {
					$prod_form_status = "readonly='readonly'";
				} ?>

				<TD><INPUT TYPE="text" NAME="CustomerOrderSeqNumber" VALUE="<?php echo $row['CustomerOrderSeqNumber'];?>" SIZE="5" STYLE="text-align:right" readonly='readonly'></TD>
				<TD><?php echo $row['ProductNumberExternal'];?></TD>

				<?php
				
				$sql = "SELECT productmaster.Designation, Natural_OR_Artificial, ProductType, Kosher
				FROM productmaster
				LEFT JOIN externalproductnumberreference USING(ProductNumberInternal)
				WHERE productmaster.ProductNumberInternal = '" . $row['ProductNumberInternal'] . "'";
				$result_des = mysql_query($sql, $link) or die (mysql_error());
				$row_des = mysql_fetch_array($result_des);

				$ProductDesignation = ("" != $row_des['Natural_OR_Artificial'] ? $row_des['Natural_OR_Artificial']." " : "").$row_des['Designation'].("" != $row_des['ProductType'] ? " - ".$row_des['ProductType'] : "").("" != $row_des['Kosher'] ? " - ".$row_des['Kosher'] : "");

				?>

				<TD WIDTH="360"><IMG SRC="images/spacer" WIDTH=360 HEIGHT=1><BR>
				<?php echo $ProductDesignation;?>
				</TD>
			
				<TD ALIGN=RIGHT><INPUT TYPE="text" NAME="Quantity" <?php echo (""==$prod_form_status ? "id=\"Quantity\" ":"") ?>VALUE="<?php echo number_format($row['Quantity'], 2);?>" SIZE="7" <?php echo $prod_form_status;?> STYLE="text-align:right"></TD>
				<TD ALIGN=RIGHT><INPUT TYPE="text" NAME="PackSize" <?php echo (""==$prod_form_status ? "id=\"PackSize\" ":"") ?>VALUE="<?php echo number_format($row['PackSize'], 2);?>" SIZE="7" <?php echo $prod_form_status;?> STYLE="text-align:right"></TD>
				<TD><select class="input-box" <?php echo (""==$prod_form_status ? "id=\"UnitOfMeasure\" ":"") ?>name="UnitOfMeasure" <?php echo $prod_form_status;?>><?php printInventoryUnitsOptions($row['UnitOfMeasure']); ?></select></TD>
				<TD ALIGN=RIGHT><INPUT TYPE="text" NAME="TotalQuantityOrdered" <?php echo (""==$prod_form_status ? "id=\"TotalQuantityOrdered\" ":"") ?>VALUE="<?php echo number_format($row['TotalQuantityOrdered'], 2);?>" SIZE="10" readonly='readonly' STYLE="text-align:right"></TD>

				<TD><INPUT TYPE="text" NAME="CustomerCodeNumber" VALUE="<?php echo $row['CustomerCodeNumber'];?>" SIZE="10" <?php echo $prod_form_status;?>></TD>

				<?php
				if ( $_REQUEST['update_prod'] != '' ) {
					$datepicker_status = "DISABLED";
				} else {
					$datepicker_status = "";
				}
				?>

				<TD><INPUT TYPE="text" SIZE="12" NAME="ShipDate" id="<?php echo $current_div1;?>" VALUE="<?php
					if ( $ShipDate != '' ) {
						echo date("m/d/Y", strtotime($ShipDate));
					}
					?>" onChange="updateDate('<?php echo $current_div1;?>', 'ship', <?php echo $row['ProductNumberInternal'];?>, <?php echo $order_num;?>, <?php echo $row['CustomerOrderSeqNumber'];?>)" <?php echo $datepicker_status;?>><DIV></DIV></TD>

				<TD><INPUT TYPE="text" SIZE="12" NAME="BilledDate" id="<?php echo $current_div2;?>" VALUE="<?php
					if ( $BilledDate != '' ) {
						echo date("m/d/Y", strtotime($BilledDate));
					}
					?>" onChange="updateDate('<?php echo $current_div2;?>', 'bill', <?php echo $row['ProductNumberInternal'];?>, <?php echo $order_num;?>, <?php echo $row['CustomerOrderSeqNumber'];?>)" <?php echo $datepicker_status;?>></TD>
				<TD>

				<?php if ( $form_status != '' and $_REQUEST['update_prod'] == '' and $shipped ) { ?>
					<INPUT TYPE="button" VALUE="Lot#(s)" onClick="popup('pop_select_lots.php?pni=<?php echo $row['ProductNumberInternal'];?>&amt=<?php echo $row['TotalQuantityOrdered'];?>&seq=<?php echo $row['CustomerOrderSeqNumber'];?>&order_num=<?php echo $order_num;?>')" CLASS="submit"></TD>
				<?php } else { ?>
					&nbsp;
				<?php } ?>
				</TD>
				<!-- 
<TD>
				<?php //if ( "" != $form_status and $_REQUEST['update_prod'] == '' ) { ?>
					<INPUT TYPE="button" VALUE="Ship" onClick="popup('#')" CLASS="submit"></TD>
				<?php //} else { ?>
					&nbsp;
				<?php //} ?>
				</TD>
 -->
			</TR></FORM>

			<?php } ?>

		</TABLE>
		

		</TD></TR></TABLE>
		</TD></TR></TABLE>

		</TD></TR></TABLE>
		</TD></TR></TABLE><BR>

		<FORM>
		<INPUT TYPE="button" CLASS="submit" VALUE="Print Order" onClick="popup('reports/print_customer_order.php?order_num=<?php echo $order_num;?>',820,830)">
		</FORM>

		</TD></TR></TABLE><BR>

	<?php } ?>




<?php } ?>


<SCRIPT TYPE="text/javascript" src="combo_box/comboBox.js"></SCRIPT>


<?php
// LOAD DB VALUES ON TOP OF DROP-DOWN MENUS onLoad
if ( $_REQUEST['update'] == 1 ) {
?>
	<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>
	
	addLoadEvent(loadIDs);
	function loadIDs() {
		var ship_via = document.getElementById('txtShipVia');
		ship_via.value="<?php echo $ShipVia;?>";
	}

	</SCRIPT>
<?php } ?>

<SCRIPT TYPE="text/javascript">

// ON FORM SUBMIT, PASS HIDDEN INPUT VALUES TO PHP PROCESSING SCRIPT
function updateHiddenField(header_info) {
	var ship_via = document.getElementById('txtShipVia');
	header_info.ShipViaHidden.value = ship_via.value;
}

</SCRIPT>



<SCRIPT TYPE="text/javascript">

function updateDate(current_div, type, pni, con, seq) {
	//document.line_items
	var whichDiv = document.getElementById(current_div);
	document.location.href = "customers_customer_order_shipping.php?order_num=" + con + "&type=" +  type + "&date=" +  whichDiv.value + "&pni=" +  pni + "&seq=" + seq;
}

function delete_order(cid) {
	if ( confirm('Are you sure you want to delete this order?') ) {
		document.location.href = "customers_customer_order_shipping.php?action=delete&cid=" + cid;
	}
}

</SCRIPT>



<?php include("inc_footer.php"); ?>