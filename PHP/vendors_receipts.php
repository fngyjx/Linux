<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 5 and $rights != 6 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');
include("inc_header.php");
include('search/system_defaults.php');

//print_r($_REQUEST);
//init
$error_message="";

$record_id=""; $po_id="";

$Vendor=""; $Designation="";
$ProductNumberExternal="";
$ProductNumberInternal="";
$Keywords="";
$receipt_search="";
$lot_number="";
$lot_sequence="";

$receipts_pending="";
$receipts_qc_approved="";
$receipts_rejected="";
$employee_id="";
$invoice_number="";
$date_received="";
$date_received_submit="";
$package_type="";
$product_number="";
$description="";
$vendor_id = "";$vendor="";
$vendor_product_code="";
$manufacture_date="";
$manufacture_date_submit="";
$expiration_date="";
$expiration_date_submit="";
$quantity="";
$pack_size="";
$measurement_units="";
$allergen_egg="";
$allergen_milk="";
$allergen_peanut="";
$allergen_seafood="";
$allergen_seed="";
$allergen_soybean="";
$allergen_sulfites="";
$allergen_tree_nuts="";
$allergen_wheat="";
$allergen_yellow="";
$package_type="";
$shipment_condition="";
$storage_location="";
$location_on_site="";
$has_c_of_a="";
$nutrition_on_file="";
$msds_on_file="";
$allergen_on_file="";
$specifications_on_file="";
$kosher_approved="";
$qc_date="";
$qc_date_submit=""; $qc_employee_id="";
$retain_size="";
$comments="";
$status="";

function validate() {
	global $po_id, $lot_number, $lot_sequence, $employee_id, $invoice_number, $date_received, 
	$date_received_submit, $package_type, $product_number, $vendor_id, $vendor_product_code, $manufacture_date,
	$manufacture_date_submit, $expiration_date, $expiration_date_submit, $quantity, $pack_size, 
	$measurement_units, $allergen_egg, $allergen_milk, $allergen_peanut, $allergen_seafood, $allergen_seed,
	$allergen_soybean, $allergen_sulfites, $allergen_tree_nuts, $allergen_wheat, $allergen_yellow, $package_type,
	$shipment_condition, $storage_location, $has_c_of_a, $nutrition_on_file, $msds_on_file, $allergen_on_file,
	$specifications_on_file, $kosher_approved,$qc_date, $qc_date_submit, $qc_employee_id, $retain_size,
	$comments, $status, $link, $record_id;
	
	$errr_message="";
	
	$allergen_egg="0";	$allergen_milk="0";	$allergen_peanut="0";	$allergen_seafood="0";	$allergen_seed="0";	$allergen_soybean="0";	$allergen_sulfites="0";	$allergen_tree_nuts="0";	$allergen_wheat="0";	$allergen_yellow="0";
	
	if ( isset($_REQUEST['allergen_egg']) ) { $allergen_egg=("on"==$_REQUEST['allergen_egg'] ? "1": "0"); }
	if ( isset($_REQUEST['allergen_milk']) ) { $allergen_milk=("on"==$_REQUEST['allergen_milk']? "1": "0"); }
	if ( isset($_REQUEST['allergen_peanut']) ) { $allergen_peanut=("on"==$_REQUEST['allergen_peanut']? "1": "0"); }
	if ( isset($_REQUEST['allergen_seafood']) ) { $allergen_seafood=("on"==$_REQUEST['allergen_seafood']? "1": "0"); }
	if ( isset($_REQUEST['allergen_seed']) ) { $allergen_seed=("on"==$_REQUEST['allergen_seed']? "1": "0"); }
	if ( isset($_REQUEST['allergen_soybean']) ) { $allergen_soybean=("on"==$_REQUEST['allergen_soybean']? "1": "0"); }
	if ( isset($_REQUEST['allergen_sulfites']) ) { $allergen_sulfites=("on"==$_REQUEST['allergen_sulfites']? "1": "0"); }
	if ( isset($_REQUEST['allergen_tree_nuts']) ) { $allergen_tree_nuts=("on"==$_REQUEST['allergen_tree_nuts']? "1": "0"); }
	if ( isset($_REQUEST['allergen_wheat']) ) { $allergen_wheat=("on"==$_REQUEST['allergen_wheat']? "1": "0"); }
	if ( isset($_REQUEST['allergen_yellow']) ) { $allergen_yellow=("on"==$_REQUEST['allergen_yellow']? "1": "0"); }

	$has_c_of_a="0";	$nutrition_on_file="0";	$msds_on_file="0";	$allergen_on_file="0";	$specifications_on_file="0";	$kosher_approved="0";
	
	if ( isset($_REQUEST['has_c_of_a']) ) { $has_c_of_a=("on"==$_REQUEST['has_c_of_a'] ? "1": "0"); }
	if ( isset($_REQUEST['nutrition_on_file']) ) { $nutrition_on_file=("on"==$_REQUEST['nutrition_on_file'] ? "1": "0"); }
	if ( isset($_REQUEST['msds_on_file']) ) {$msds_on_file=("on"==$_REQUEST['msds_on_file'] ? "1": "0"); }
	if ( isset($_REQUEST['allergen_on_file']) ) { $allergen_on_file=("on"==$_REQUEST['allergen_on_file']);}
	if ( isset($_REQUEST['specifications_on_file']) ) { $specifications_on_file=("on"==$_REQUEST['specifications_on_file'] ? "1": "0"); }
	if ( isset($_REQUEST['kosher_approved']) ) { $kosher_approved=("on"==$_REQUEST['kosher_approved'] ? "1": "0"); }

	if ( isset($_REQUEST['status']) ) { $status=escape_data($_REQUEST['status']); }
	if ( isset($_REQUEST['employee_id']) ) { $employee_id=escape_data($_REQUEST['employee_id']); }
	if ( isset($_REQUEST['invoice_number']) ) { $invoice_number=escape_data($_REQUEST['invoice_number']); }
	// if ( isset($_REQUEST['received_day']) ) { $received_day=escape_data($_REQUEST['received_day']); }
	// if ( isset($_REQUEST['received_month']) ) { $received_month=escape_data($_REQUEST['received_month']); }
	// if ( isset($_REQUEST['received_year']) ) { $received_year=escape_data($_REQUEST['received_year']); }
	if ( isset($_REQUEST['date_received']) ) { $date_received=escape_data($_REQUEST['date_received']); }
	// if ( is_numeric($received_month) AND is_numeric($received_day) AND is_numeric($received_year) AND checkdate($received_month, $received_day, $received_year) ) { $date_received = $received_year . "-" . $received_month . "-" . $received_day; }
	if ( isset($_REQUEST['lot_number']) ) { $lot_number=$_REQUEST['lot_number']; }
	// if ( isset($_REQUEST['lot_sequence']) ) { $lot_sequence=$_REQUEST['lot_sequence']; }
	// if ( isset($_REQUEST['manufacture_day']) ) { $manufacture_day=escape_data($_REQUEST['manufacture_day']); }
	// if ( isset($_REQUEST['manufacture_month']) ) { $manufacture_month=escape_data($_REQUEST['manufacture_month']); }
	// if ( isset($_REQUEST['manufacture_year']) ) { $manufacture_year=escape_data($_REQUEST['manufacture_year']); }
	if ( isset($_REQUEST['manufacture_date']) ) { $manufacture_date=escape_data($_REQUEST['manufacture_date']); }
	// if ( is_numeric($manufacture_month) AND is_numeric($manufacture_day) AND is_numeric($manufacture_year) AND checkdate($manufacture_month, $manufacture_day, $manufacture_year) ) { $manufacture_date = $manufacture_year . "-" . $manufacture_month . "-" . $manufacture_day; }
	// if ( isset($_REQUEST['expiration_day']) ) { $expiration_day=escape_data($_REQUEST['expiration_day']); }
	// if ( isset($_REQUEST['expiration_month']) ) { $expiration_month=escape_data($_REQUEST['expiration_month']); }
	// if ( isset($_REQUEST['expiration_year']) ) { $expiration_year=escape_data($_REQUEST['expiration_year']); }
	if ( isset($_REQUEST['expiration_date']) ) { $expiration_date=escape_data($_REQUEST['expiration_date']); }
	// if ( is_numeric($expiration_month) AND is_numeric($expiration_day) AND is_numeric($expiration_year) AND checkdate($expiration_month, $expiration_day, $expiration_year) ) { $expiration_date = $expiration_year . "-" . $expiration_month . "-" . $expiration_day; }
	if ( isset($_REQUEST['quantity']) ) { $quantity=escape_data($_REQUEST['quantity']); }
	if ( isset($_REQUEST['pack_size']) ) { $pack_size=escape_data($_REQUEST['pack_size']); }
	if ( isset($_REQUEST['measurement_units']) ) { $measurement_units=escape_data($_REQUEST['measurement_units']); }
	if ( isset($_REQUEST['package_type']) ) { $package_type=escape_data($_REQUEST['package_type']); }
	if ( isset($_REQUEST['shipment_condition']) ) { $shipment_condition=escape_data($_REQUEST['shipment_condition']); }
	if ( isset($_REQUEST['storage_location']) ) { $storage_location=escape_data($_REQUEST['storage_location']); }
/*	if ( isset($_REQUEST['qc_day']) ) { $qc_day=escape_data($_REQUEST['qc_day']); }
	if ( isset($_REQUEST['qc_month']) ) { $qc_month=escape_data($_REQUEST['qc_month']); }
	if ( isset($_REQUEST['qc_year']) ) { $qc_year=escape_data($_REQUEST['qc_year']); }*/
	if ( isset($_REQUEST['qc_date']) ) { $qc_date=escape_data($_REQUEST['qc_date']); }
	// if ( is_numeric($qc_month) AND is_numeric($qc_day) AND is_numeric($qc_year) AND checkdate($qc_month, $qc_day, $qc_year) ) { $qc_date = $qc_year . "-" . $qc_month . "-" . $qc_day; }
	if ( isset($_REQUEST['qc_employee_id']) )  { $qc_employee_id=escape_data($_REQUEST['qc_employee_id']); }
	if ( isset($_REQUEST['retain_size']) )  { $retain_size=escape_data($_REQUEST['retain_size']); }
	if ( isset($_REQUEST['comments']) ) { $comments=escape_data($_REQUEST['comments']); }

	// Validate Data
	if ("P" != $status && "A" != $status && "R" != $status) { $error_message .= "Bad receipt status<br/>"; }
	if ("" != $employee_id) {
		$sql="SELECT COUNT(*) FROM users WHERE user_id=$employee_id AND active=1 AND locked=0 ";
		$result = mysql_query($sql, $link) or die (mysql_error());
		if (1 != mysql_num_rows($result)) { $error_message .= "Invalid Employee<br/>"; }
	}
	if ("" != $invoice_number && 50 < strlen($invoice_number)) { $error_message .= "Invoice number too long. Must be less than 50 characters<br.>"; }
	$received_date_parts = explode("/", $date_received);
	$date_received_submit = (is_numeric($received_date_parts[2]) ? $received_date_parts[2] : 0)."-".(is_numeric($received_date_parts[0]) ? $received_date_parts[0] : 0)."-".(is_numeric($received_date_parts[1]) ? $received_date_parts[1] : 0);
	if (!checkdate((is_numeric($received_date_parts[0]) ? $received_date_parts[0] : 0), (is_numeric($received_date_parts[1]) ? $received_date_parts[1] : 0), (is_numeric($received_date_parts[2]) ? $received_date_parts[2] : 0))) {
		$error_message .= "Date received incomplete or invalid.<br/>";
	}
	if (0 == strlen(trim($po_id))) {
		$error_message.="Purchase Order is a required field.<br/>"; 
	} else {
		// print_r($_REQUEST);
		// echo "<h1>PO ID = '$po_id' or ".strlen(trim($po_id))."</h1>";
		$sql  = "SELECT VendorID, Description, VendorProductCode, ProductNumberInternal From purchaseorderdetail ";
		$sql .= "LEFT JOIN purchaseordermaster ON ( purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber ) ";
		$sql .= "WHERE purchaseorderdetail.ID=$po_id";
		$result=mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$result_count = mysql_num_rows($result);
		if (1==$result_count) {
			$row = mysql_fetch_row($result);
			$vendor_id = $row[0];
			$vendor_product_code = $row[2];
			$product_number = $row[3];
		} 
		else if (0==$result_count) { $error_message.="P.O. does not exist. Please try again with different P.O.<br/>"; }
		else if (1 < $result_count) { $error_message.="P.O. not unique. Please contact your database administrator.<br/>"; }
	}
	if ( "" == $lot_number ) { 
		$error_message .= "Lot Number is a required field.<br/>"; 
	} 
	else {
		$lot_good=true;
		if (30 < strlen($lot_number) ) { $error_message .= "Lot Number cannot be longer than 30 characters.<br/>"; $lot_good=false; }
		// if ( !(is_numeric($lot_sequence)) ) { $error_message .= "Lot Number Sequence must be a number.<br/>"; $lot_good=false; }
		// else if ( 10 < strlen($lot_sequence)) { $error_message .= "Lot Number Sequence cannot be longer than 10 digits.<br/>"; $lot_good=false; }
		if ($lot_good) {
			if (""==$record_id) { // if new record
				$lot_sequence = getNextLotSequenceNumber($lot_number);
			}
			else {
				$sql_test = "SELECT LotNumber, LotSequenceNumber
								FROM lots, receipts
								WHERE lots.ID = receipts.LotID
									AND receipts.ID =$record_id";
				$result = mysql_query($sql_test, $link) or 
					die (mysql_error()."<br />Couldn't execute query: $sql_test<BR><BR>");
				if (mysql_result($result,0,0) != $lot_number) { // if updating
					$lot_sequence = getNextLotSequenceNumber($lot_number);
				} 
				else {
					$lot_sequence = mysql_result($result,0,1);
				}
			}
		}
	}
	if ('' != $manufacture_date) {
		$manufacture_date_parts = explode("/", $manufacture_date);
		$manufacture_date_submit = 
			(is_numeric($manufacture_date_parts[2]) ? $manufacture_date_parts[2] : 0)."-".
			(is_numeric($manufacture_date_parts[0]) ? $manufacture_date_parts[0] : 0)."-".
			(is_numeric($manufacture_date_parts[1]) ? $manufacture_date_parts[1] : 0);
		if (!checkdate(
				(is_numeric($manufacture_date_parts[0]) ? $manufacture_date_parts[0] : 0), 
				(is_numeric($manufacture_date_parts[1]) ? $manufacture_date_parts[1] : 0), 
				(is_numeric($manufacture_date_parts[2]) ? $manufacture_date_parts[2] : 0)
			)
		) {
			$error_message .= "Date manufactured \"$manufacture_date\" invalid.<br/>";
		}
	}
	if ('' != $expiration_date) {
		$expiration_date_parts = explode("/", $expiration_date);
		$expiration_date_submit = 
			(is_numeric($expiration_date_parts[2]) ? $expiration_date_parts[2] : 0)."-".
			(is_numeric($expiration_date_parts[0]) ? $expiration_date_parts[0] : 0)."-".
			(is_numeric($expiration_date_parts[1]) ? $expiration_date_parts[1] : 0);
		if (!checkdate(
				(is_numeric($expiration_date_parts[0]) ? $expiration_date_parts[0] : 0), 
				(is_numeric($expiration_date_parts[1]) ? $expiration_date_parts[1] : 0), 
				(is_numeric($expiration_date_parts[2]) ? $expiration_date_parts[2] : 0)
			)
		) {
			$error_message .= "Expiration date \"$expiration_date\" invalid.<br/>";
		}
	}
	if ( "" == $quantity ) { 
		$error_message .= "Quantity is a required field.<br/>"; 
	} else if ( !(is_numeric($quantity)) && 0 > $quantity ) { 
		$error_message .= "Quantity must be  greater than zero.<br/>"; 
	}
	if ( "" == $pack_size ) { 
		$error_message .= "Pack Size is a required field.<br/>"; 
	} else if ( !(is_numeric($pack_size)) && 0 > $pack_size ) { 
		$error_message .= "Pack Size must be  greater than zero.<br/>"; 
	}
	if ( ("grams" != $measurement_units) && ("kg" != $measurement_units) && ("lbs" != $measurement_units) 
	     && ("N/A" != $measurement_units) ) {
		$error_message .= "Unit of Measure is a required field. - $measurement_units<br/>";
	}
	function test_system_default_value($value,$type) {
		global $link;
		$sql="SELECT COUNT(*) FROM tblsystemdefaultsdetail ";
		$sql.="INNER JOIN tblsystemdefaultsmaster ON (tblsystemdefaultsdetail.ItemID = tblsystemdefaultsmaster.ItemID) ";
		$sql.="WHERE tblsystemdefaultsmaster.itemDescription = '$type' AND tblsystemdefaultsdetail.ItemDescription='$value'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( 1 == mysql_result($result,0) ) { return (true); }
		return (false);
	}
	if ("" != $package_type && !(test_system_default_value($package_type, 'Vendor Packaging Types')) ) { $error_message .= "Storage Location invalid<br/>"; }
	if ("" != $shipment_condition && !(test_system_default_value($shipment_condition, 'Condition of Shipment')) ) { $error_message .= "Storage Location invalid<br/>"; }
	if ("" != $storage_location && !(test_system_default_value($storage_location, 'Storage Location')) ) { $error_message .= "Storage Location invalid<br/>"; }
	if (""!=$qc_date) {
		$qc_date_parts = explode("/", $qc_date);
		$qc_date_submit = (is_numeric($qc_date_parts[2]) ? $qc_date_parts[2] : 0)."-".(is_numeric($qc_date_parts[0]) ? $qc_date_parts[0] : 0)."-".(is_numeric($qc_date_parts[1]) ? $qc_date_parts[1] : 0);
		if (!checkdate((is_numeric($qc_date_parts[0]) ? $qc_date_parts[0] : 0), (is_numeric($qc_date_parts[1]) ? $qc_date_parts[1] : 0), (is_numeric($qc_date_parts[2]) ? $qc_date_parts[2] : 0))) {
			$error_message .= "QC Date incomplete or invalid.<br/>";
		}
	}
	if ("" != $qc_employee_id) {
		$sql="SELECT COUNT(*) FROM users WHERE user_id=$qc_employee_id AND active=1 AND locked=0 ";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if (1 != mysql_num_rows($result)) { $error_message .= "Invalid QC Employee<br/>"; }
	}
	if ("" != $retain_size && 10 < strlen($retain_size) ) { $error_message .= "Retain size cannot be longer than 10 characters.<br/>"; }
	if ("" != $comments && 4294967296 < strlen($comments)) { $error_message .= "Comments cannot be longer than 4.2 billion characters.<br/>"; }
	return ($error_message);
}

$action="";
if ( isset($_REQUEST['action']) ) { $action=$_REQUEST['action']; }
if ( isset($_REQUEST['record_id']) ) { $record_id=$_REQUEST['record_id']; }
if (isset($_REQUEST['po_id'])) { $po_id=$_REQUEST['po_id']; }

if ("search" == $action || "edit" == $action)
{
	if ( isset($_REQUEST['receipts_pending']) ) { $receipts_pending=$_REQUEST['receipts_pending']; }
	if ( isset($_REQUEST['receipts_qc_approved']) ) { $receipts_qc_approved=$_REQUEST['receipts_qc_approved']; }
	if ( isset($_REQUEST['receipts_rejected']) ) { $receipts_rejected=$_REQUEST['receipts_rejected']; }

	if ( ("search"==$action ) && (""==$receipts_pending) && (""==$receipts_qc_approved) && (""==$receipts_rejected) ) {
		$error_message = "You must select a receipt type to search";
	}
	if ( isset($_REQUEST['Vendor']) ) { $Vendor=$_REQUEST['Vendor']; }
	if ( isset($_REQUEST['Designation']) ) { $Designation=$_REQUEST['Designation']; }
	if ( isset($_REQUEST['ProductNumberExternal']) ) { 
		$tmpArr = explode("&", $_REQUEST['ProductNumberExternal']);
		$ProductNumberExternal=$tmpArr[0]; }
	if ( isset($_REQUEST['ProductNumberInternal']) ) { 
		$tmpArr = explode("&", $_REQUEST['ProductNumberInternal']);
		$ProductNumberInternal=$tmpArr[0]; }
	if ( isset($_REQUEST['Keywords']) ) { $Keywords=$_REQUEST['Keywords']; }
	if ( isset($_REQUEST['receipt_search']) ) { $receipt_search=$_REQUEST['receipt_search']; }
	if ( isset($_REQUEST['po_search']) ) { $PONUmber=$_REQUEST['po_search']; }
	if ("search"==$action && ""==$receipt_search)
	{
		$record_id="";
	}
}
else if ("save" == $action)
{
	$error_message=validate();
	
	//If no errors, process data
	if ("" == $error_message) {
		//if new receipt
		if (""==$record_id) {
			// Save minimal amount
			$sql = "CALL CreatePendingReceipt($po_id,$quantity,$pack_size,'$measurement_units',@new_receipt)";
			// echo "$sql <br />";
			//$sql = "INSERT INTO receipts ( LotID, PurchaseOrderID, Quantity, PackSize, UnitOfMeasure, Status ) VALUES ( $lots_id, $po_id, $quantity, $pack_size, units, 'P' )";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$sql = "SELECT @new_receipt;";
			$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$result_count = mysql_num_rows($result);
			if (1==$result_count) {
				$row = mysql_fetch_row($result);
				$record_id = $row[0];
			}
			// and process as "Pending"
			$status='P';
		}

		// process based on receipt status
		switch ($status) {
			case "R" : //status "Rejected"
				$sql  = "UPDATE receipts SET `Comments`='$comments' WHERE `ID`='$record_id'";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				break;
			case "A": //status "Approved"
				$sql  = "UPDATE receipts SET `VendorInvoiceNumber`='$invoice_number', `PackagingType`='$package_type', `ConditionOfShipment`='$shipment_condition'
								`Comments`='$comments' WHERE `ID`='$record_id'";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$sql  = "UPDATE lots, receipts SET lots.StorageLocation='$storage_location' lots.QCPackagingTypeAndSize='$package_type' WHERE receipts.LotID=lots.ID AND receipts.ID='$record_id'";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				break;
			case "P": //status "Pending"
				$sql  = "SELECT PurchaseOrderID FROM receipts WHERE `ID`='$record_id'";
				// echo "$sql <br />";
				$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				if ($po_id != mysql_result($result,0)) {
					// if you changed the associated PO, Update the Old PO
					$old_po = mysql_result($result,0);
					$sql  = "SELECT QuantityConvert(TotalQuantityExpected, UnitOfMeasure, 'grams') FROM purchaseorderdetail WHERE ID='$old_po'";
					$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					$expected = mysql_result($result,0);
					
					$sql = "SELECT TotalPOAmtReceived ($old_po, NULL)";
					$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					$received = mysql_result($result,0);
					if ($received >= $expected) {
						$sql  = "UPDATE purchaseorderdetail SET Status='P' WHERE ID='$old_po'";
					} else {
						$sql  = "UPDATE purchaseorderdetail SET Status='O' WHERE ID='$old_po'";
					}
					mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					echo "<h1>New PO is $po_id. I updated previous PO - $sql</h1>";
				}

				//Update inventory movements - as inventory number was scrued up, use LotId instead - jdu 20100516
				$sql  = "SELECT QuantityConvert( Quantity, 'grams', '$measurement_units' ) AS Quantity, ProductNumberInternal FROM inventorymovements WHERE LotID = ( ";
				$sql .= "SELECT LotID from receipts WHERE ID='$record_id' LIMIT 1) and TransactionType=1";
				$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				// if quantity or product has changed
				if (1 == mysql_num_rows($result)){
					$row_temp = mysql_fetch_array($result);
					if ( ($quantity * $pack_size) != $row_temp['Quantity'] || $product_number != $row_temp['ProductNumberInternal'] ) {
						$sql  = "UPDATE inventorymovements SET ProductNumberInternal=$product_number, Quantity=QuantityConvert(".$quantity*$pack_size.",'$measurement_units','grams') ";
						$sql .= "WHERE LotID = ( SELECT LotID FROM receipts WHERE ID='$record_id' LIMIT 1) AND  TransactionType=1";
						mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					}
				}

				//Update receipts
				$sql  = "UPDATE receipts SET ";
				$sql .= "PurchaseOrderID=$po_id, VendorInvoiceNumber=".("" != $invoice_number ? "'$invoice_number'" : "NULL").", ";
				$sql .= "Quantity=".("" != $quantity ? "'$quantity'" : "NULL").", ";
				$sql .= "PackSize=".("" != $pack_size ? "'$pack_size'" : "NULL").", ";
				$sql .= "UnitOfMeasure=".("" != $measurement_units ? "'$measurement_units'" : "NULL").", ";
				$sql .= "EmployeeID=".("" != $employee_id ? "'$employee_id'" : "NULL").", ";
				$sql .= "ConditionOfShipment=".("" != $shipment_condition ? "'$shipment_condition'" : "NULL").", ";
				$sql .= "DateReceived=".("" != $date_received_submit ? "'$date_received_submit'" : "NULL").", ";
				$sql .= "PackagingType=".("" != $package_type ? "'$package_type'" : "NULL").", ";
				$sql .= "Comments=".("" != $comments ? "'$comments'" : "NULL").", ";
				$sql .= "C_of_A_attached=$has_c_of_a, ";
				$sql .= "MSDS_on_file=$msds_on_file, ";
				$sql .= "Specifications_on_file=$specifications_on_file, ";
				$sql .= "Nutrition_on_file=$nutrition_on_file, ";
				$sql .= "Allergen_statement_on_file=$allergen_on_file, ";
				$sql .= "KosherApproved=$kosher_approved ";
				$sql .= "WHERE `ID`='$record_id'";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

				//Update lots
				$sql  = "UPDATE lots SET ";
				$sql .= "LotNumber=".("" != $lot_number ? "'$lot_number'" : "NULL").", ";
				$sql .= "LotSequenceNumber=".("" != $lot_sequence ? "'$lot_sequence'" : "NULL").", ";
				$sql .= "DateManufactured=".("" != $manufacture_date_submit ? "'$manufacture_date_submit'" : "NULL").", ";
				$sql .= "ExpirationDate=".("" != $expiration_date_submit ? "'$expiration_date_submit'" : "NULL").", ";
				$sql .= "SizeOfRetainTaken=".("" != $retain_size ? "'$retain_size'" : "NULL").", ";
				$sql .= "QualityControlDate =".("" != $qc_date_submit ? "'$qc_date_submit'" : "NULL").", ";
				$sql .= "QualityControlEmployeeID=".("" != $qc_employee_id ? "'$qc_employee_id'" : "NULL").", ";
				$sql .= "StorageLocation=".("" != $storage_location ? "'$storage_location'" : "'Warehouse'").", ";
				$sql .= "QCPackagingTypeAndSize=".("" != $package_type ? "'$package_type'" : "NULL").", ";
				$sql .= "VendorID=".("" != $vendor_id ? "'$vendor_id'" : "'NULL'")." ";
				$sql .= "WHERE `ID`= ( SELECT LotID FROM receipts WHERE `ID`='$record_id')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

				//update purchaseorderdetail
				$sql  = "SELECT QuantityConvert(TotalQuantityExpected, UnitOfMeasure, 'grams') FROM purchaseorderdetail WHERE ID='$po_id'";
				$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$expected = mysql_result($result,0);
				
				$sql = "SELECT TotalPOAmtReceived ($po_id, NULL)";
				$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$received = mysql_result($result,0);
				if ($received >= $expected) {
					$sql  = "UPDATE purchaseorderdetail SET Status='P' WHERE ID='$po_id'";
				} else {
					$sql  = "UPDATE purchaseorderdetail SET Status='O' WHERE ID='$po_id'";
				}
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

				//update product master
				$sql  = "UPDATE productmaster SET ";
				$sql .= "`AllergenEgg`=$allergen_egg, `AllergenMilk`=$allergen_milk, `AllergenPeanut`=$allergen_peanut, `AllergenSeafood`=$allergen_seafood, `AllergenSeed`=$allergen_seed, `AllergenSoybean`=$allergen_soybean, ";
				$sql .= "`AllergenSulfites`=$allergen_sulfites, `AllergenTreeNuts`=$allergen_tree_nuts, `AllergenWheat`=$allergen_wheat, `AllergenYellow`=$allergen_yellow ";
				$sql .= "WHERE `ProductNumberInternal`='$product_number'";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				break;
			}
		$note.="<p>Successfully saved receipt.</p>";
		$action="view";
	}
	else { $action="edit"; }
} 
else if ("approve"==$action) 
{
	$error_message=validate();
	if ("" == $date_received_submit) {
		$error_message .= "Received date is required to approve this receipt.<br/>";
	}
	if ("" == $qc_date) {
		$error_message .= "QC date is required to approve this receipt.<br/>";
	}
	if ("" == $qc_employee_id) {
		$error_message .= "QC Performed By is required to approve this receipt.<br/>";
	}
	if ("" == $error_message) {
		// Update PO expected quantity if we need to
		$sql="SELECT QuantityConvert(TotalQuantityExpected, UnitOfMeasure, 'grams') FROM purchaseorderdetail WHERE ID='$po_id'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$expected = mysql_result($result,0);

		$sql = "SELECT TotalPOAmtReceived ($po_id, 'C')";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$received = mysql_result($result,0);

		$sql="SELECT QuantityConvert(".$quantity*$pack_size.", '$measurement_units', 'grams')";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$pending = mysql_result($result,0);
		
		if ( $pending + $received > $expected) {
			$totalQE = $pending + $received;
			$sql  = "UPDATE purchaseorderdetail SET TotalQuantityExpected = QuantityConvert($totalQE, 'grams', UnitOfMeasure) WHERE ID='$po_id'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		
		// update status on receipts, inventorymovements, and purchaseorderdetails
		
		$sql  = "UPDATE receipts SET Status = 'A' WHERE ID='$record_id'";
		if ( ! mysql_query($sql, $link) ) {
			echo  mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			die;
		}

		$sql  = "SELECT name FROM vendors WHERE vendor_id = (SELECT VendorId FROM purchaseordermaster WHERE PurchaseOrderNumber = (SELECT PurchaseOrderNumber FROM purchaseorderdetail WHERE ID = '$po_id' LIMIT 1) LIMIT 1)";
		if ( ! ($result = mysql_query($sql, $link) ) ){
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		}
		
		$vendor = mysql_result($result,0);

		$sql  = "UPDATE inventorymovements SET TransactionDate = ".("" != $date_received_submit ? "'$date_received_submit'" : "NULL").", ";
		$sql .= "MovementStatus = 'C', Remarks = '$vendor - Invoice # $invoice_number' ";
		$sql .= "WHERE LotID = ( SELECT LotID from receipts WHERE ID='$record_id' LIMIT 1) AND TransactionType=1";
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			die;
		}

		$sql  = "SELECT QuantityConvert(TotalQuantityExpected, UnitOfMeasure, 'grams') FROM purchaseorderdetail WHERE ID='$po_id'";
		if ( !($result = mysql_query($sql, $link)) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			die;
		}
		$expected = mysql_result($result,0);
		
		$sql = "SELECT TotalPOAmtReceived ($po_id, NULL)";
		if ( ! ($result = mysql_query($sql, $link) ) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			die;
		}
		$received = mysql_result($result,0);
		// when received ammount diff from PO amount, Yes clicked, PO pending for more come in
		// if No clicked, the PO will be cloed because we don't expect any further shipping in 
		$endOfPO = ( $_REQUEST['ApproveEndPO'] != "" ) ? escape_data($_REQUEST['ApproveEndPO']) : "";
		// echo "<br />Approved to end of the PO ? = " . $endOfPO . "<br />";
		// echo "<br /> expected = " .$expected." received = " . $received."<br />";
		// $onthewayamount=(($expected - $received) > 0 ) ? ($expected - $received) : 0 ;
		if ($received >= $expected or $endOfPO == "YES") {
			$sql  = "UPDATE purchaseorderdetail SET Status='A' WHERE ID='$po_id'";
		} else {
			$sql  = "UPDATE purchaseorderdetail SET Status='P' WHERE ID='$po_id'";
		}
		// echo "<br />endofpo=$endOfPO <br /> onthewayamount=$onthewayamount<br /> $sql <br />";
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			die;
		}
		
		// update productmaster
		$sql  = "UPDATE productmaster SET productmaster.MostRecentVendorID = $vendor_id  WHERE ProductNumberInternal = '$product_number'";
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			die;
		}
		
		$action="view";
	}
	else { 
		$action="edit"; 
	}
}
else if ("reject" == $action) 
{
	$error_message=validate();
	if ("" == $qc_date) {
		$error_message .= "QC date is required to reject this receipt.<br/>";
	}
	if ("" == $qc_employee_id) {
		$error_message .= "QC Performed By is required to reject this receipt.<br/>";
	}
	if ("" == $error_message) {
		// Update inventorymovements record to type = rejected
		$sql  = "UPDATE inventorymovements SET TransactionType=10, TransactionDate = ".("" != $date_received_submit ? "'$date_received_submit'" : "NULL").", ";
		$sql .= "MovementStatus = 'C', Remarks = '$vendor - Invoice # $invoice_number' ";
		$sql .= "WHERE TransactionNumber = ( SELECT InventoryMovementTransactionNumber FROM lots WHERE ID = ( ";
		$sql .= "SELECT LotID from receipts WHERE ID='$record_id' LIMIT 1) LIMIT 1) LIMIT 1";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

		// Update purchaseorderdetail to rejected
		$sql  = "UPDATE purchaseorderdetail SET Status='R' WHERE ID=$po_id";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

		// Update receipt to rejected
		$sql  = "UPDATE receipts SET Status = 'R' WHERE ID='$record_id'";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$status="R";
	}
	else { 
		$action="edit"; 
	}
}
else if ("delete" == $action) 
{
	// delete lots record, and inventorymovements record
	if ("" != $record_id) {
		$sql = "SELECT DISTINCT TransactionType FROM inventorymovements ";
		$sql .= "WHERE `LotID`= ( SELECT LotID FROM receipts WHERE `ID`='$record_id') AND NOT (MovementStatus='P' AND TransactionType=1)";
		$result=mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if (0 < mysql_num_rows($result))
		{
			$error_message .= "This lot has been committed to inventory and there are potentially already transactions against it. Cannot delete.";
			$action="edit";
		}
		else
		{
			$sql  = "CALL DeletePendingReceipt($record_id)";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

				$sql  = "SELECT QuantityConvert(TotalQuantityExpected, UnitOfMeasure, 'grams') FROM purchaseorderdetail WHERE ID='$po_id'";
				$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$expected = mysql_result($result,0);
				
				$sql = "SELECT TotalPOAmtReceived ($po_id, NULL)";
				$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$received = mysql_result($result,0);
				if ($received >= $expected) {
					$sql  = "UPDATE purchaseorderdetail SET Status='P' WHERE ID='$po_id'";
				} else {
					$sql  = "UPDATE purchaseorderdetail SET Status='O' WHERE ID='$po_id'";
				}
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

				$note.= "receipt deleted.";
			$action="search";
		}
	} else {
		$error_message .="Bad LotID!";
		$action = "edit";
	}
}
?>


<FORM id="search" name="search" ACTION="vendors_receipts.php" METHOD="get">
<INPUT TYPE="hidden" id="action" NAME="action" VALUE="search">
<input type="hidden" id="record_id" name="record_id" value="<?php echo $record_id ?>"/>

<?php if (""==$action || "search"==$action) { ?>

<TABLE class="bounding"><TR valign="top"><TD class="padded">


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD colspan=7 >
		<div style="margin-bottom:.5em; padding:.5em; border:solid 1px">
		<span style="margin-right:1em">Select receipt type(s):</span>
			<strong>
				<input type="checkbox" class="input-box"  id="receipts_pending" name="receipts_pending" <?php if ("on"==$receipts_pending) echo "CHECKED" ?> />
				<label for="receipts_pending" style="padding-right:2em;" >Pending</label>
				<input type="checkbox" class="input-box"  id="receipts_qc_approved" name="receipts_qc_approved" <?php if ("on"==$receipts_qc_approved) echo "CHECKED" ?> />
				<label for="receipts_qc_approved" style="padding-right:2em;" >QC Approved</label>
				<input type="checkbox" class="input-box"  id="receipts_rejected" name="receipts_rejected" <?php if ("on"==$receipts_rejected) echo "CHECKED"; ?> />
				<label for="receipts_rejected">Rejected</label>
			</strong>
		</div>
		</TD>
	</TR>
	
	<TR>
		<TD><B>Vendor:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="vendor_search" NAME="Vendor" VALUE="<?php echo formatTxt(getFormSafe($Vendor))?>" SIZE="30"></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><B>Designation:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="designation_search" NAME="Designation" VALUE="<?php echo formatTxt(getFormSafe($Designation))?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=7><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>

	<TR>
		<TD><B>Abelei number (external):</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="external_number_search" NAME="ProductNumberExternal" VALUE="<?php echo formatTxt(getFormSafe($ProductNumberExternal))?>" SIZE="30"></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><B>Material number (internal):</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="internal_number_search" NAME="ProductNumberInternal" VALUE="<?php echo formatTxt(getFormSafe($ProductNumberInternal))?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=7><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>

	<TR>
		<TD><B>Keywords:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="keyword_search" NAME="Keywords" VALUE="<?php echo formatTxt(getFormSafe($Keywords))?>" SIZE="30"></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><B>Receipts</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
		<TD><input type="text" id="receipt_search" name="receipt_search" VALUE="<?php echo formatTxt(getFormSafe($receipt_search))?>" size="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=7><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>

	<TR>
		<TD><B>P.O#:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="po_search" NAME="po_search" VALUE="<?php echo formatTxt(getFormSafe($PONumber))?>" SIZE="30"></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD>&nbsp;</TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
		<TD>&nbsp;</TD>
	</TR>
	
	<TR>
		<TD COLSPAN=7><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>

	<TR>
		<TD style="text-align:left;" colspan=7 >
			<INPUT style="float:right" name="search" id="search" TYPE="submit" class="submit_medium" VALUE="Search">
			<INPUT style="margin-top:.5em" name="new" id="new" TYPE="submit" class="submit new" VALUE="New Receipt">
		</TD>
	</TR>

</TABLE>

</TD></TR>
</TABLE><BR>

<?php
}

if ("" != $error_message) {
	echo "<div style=\"color:red; border:solid 2px red; padding:1em\">$error_message</div>";
}
if ("" != $note) {
	echo "<div>$note</div>";
}

if ( "search"== $action && "" == $error_message ) {
	$clause = "";
	if ( ("on"==$receipts_pending) || ("on"==$receipts_qc_approved) || ("on"==$receipts_rejected) )
	{
		$clause = "(";
		if ("on"==$receipts_pending) {
			$clause .= "receipts.Status='P' "; //status "Pending"
		}
		if ("on"==$receipts_qc_approved) {
			if ("on"==$receipts_pending) { $clause .="OR "; }
			$clause .= "receipts.Status='A' "; //status "Approved"
		}
		if ("on"==$receipts_rejected) {
			if ( "on"==$receipts_pending || "on"==$receipts_qc_approved ) { $clause .="OR "; }
			$clause .= "receipts.Status='R' "; //status "Rejected"
		}
		$clause .= ")";
	}
	if ( $Vendor != '' ) {
		$clause .= ( "" != $clause ? " AND " : "" )."( vendors.name LIKE '%" . str_replace("'","''",$Vendor) . "%' )";
	} else
	if ( $Designation != '' ) {
		$clause .= ( "" != $clause ? " AND " : "" )."( purchaseorderdetail.Description LIKE '%" . str_replace("'","''",$Designation) . "%' )";
	} else
	if ( $ProductNumberExternal != '' ) {
		$clause .= ( "" != $clause ? " AND " : "" )."( externalproductnumberreference.ProductNumberExternal LIKE '%" . str_replace("'","''",$ProductNumberExternal) . "%' )";
	} else 
	if ( $ProductNumberInternal != '' ) {
		$clause .= ( "" != $clause ? " AND " : "" )."( productmaster.ProductNumberInternal LIKE '%" . str_replace("'","''",$ProductNumberInternal) . "%' )";
	} else 
	if ( $Keywords != '' ) {
		$clause .= ( "" != $clause ? " AND " : "" )."( productmaster.Keywords LIKE '%" . str_replace("'","''",$Keywords) . "%' )";
	} else 
	if ( $lot_number != "" && $lot_sequence != '' ) {
		$clause .= ( "" != $clause ? " AND " : "" )."( lots.LotNumber = '$lot_number' AND lots.LotSequenceNumber = '$lot_sequence' )";
	} 
	if ("" != $_REQUEST['record_id']) { $clause = "receipts.ID = ".$_REQUEST['record_id']; }
	
	if ( "" != $_REQUEST['po_search'] ) {
		$PONumber = escape_data($_REQUEST['po_search']);
		$clause .= " AND receipts.PurchaseOrderID in ( SELECT ID FROM purchaseorderdetail where PurchaseOrderNumber = '$PONumber') ";
	}
	
$sql  = "SELECT receipts.ID AS record_id, purchaseorderdetail.PurchaseOrderNumber as PurchaseOrderNumber, receipts.DateReceived as DateReceived, purchaseorderdetail.ProductNumberInternal as ProductNumberInternal, ";
$sql .= "purchaseorderdetail.Description as Description, vendors.name as Vendor, lots.LotNumber as LotNumber,  lots.LotSequenceNumber as LotSequenceNumber, ";
$sql .= "receipts.Quantity as Quantity, receipts.PackSize as PackSize, receipts.UnitOfMeasure as UnitOfMeasure, receipts.Status as Status FROM receipts ";
$sql .= "LEFT JOIN lots ON ( receipts.LotID = lots.ID ) ";
$sql .= "LEFT JOIN purchaseorderdetail ON ( receipts.PurchaseOrderID = purchaseorderdetail.ID ) ";
$sql .= "LEFT JOIN purchaseordermaster ON ( purchaseorderdetail.PurchaseOrderNumber = purchaseordermaster.PurchaseOrderNumber ) ";
$sql .= "LEFT JOIN productmaster ON (purchaseorderdetail.ProductNumberInternal = productmaster.ProductNumberInternal) ";
$sql .= "LEFT JOIN vendors ON (purchaseordermaster.VendorId=vendors.vendor_id) ";
$sql .= ( "" != $ProductNumberExternal ? "LEFT JOIN externalproductnumberreference ON ( externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal ) " : "");
$sql .= ( "" != $clause ? "WHERE $clause " : "");
$sql .= "ORDER BY purchaseorderdetail.PurchaseOrderNumber";
	
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
//	echo $sql . "<BR>";

	if ( $c > 0 ) {
		$bg = 0; ?>

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<TR VALIGN=BOTTOM>
				<TD><NOBR><B>P.O. #</B></NOBR></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Received</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><NOBR><B>Internal #</B></NOBR></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Description</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Vendor</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><NOBR><B>Lot #</B></NOBR></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><NOBR><B>Seq #</B></NOBR></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B>Qty</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B>Pack Size</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Units</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Status</B></TD>
				<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			</TR>

			<TR>
				<TD COLSPAN=9><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
			</TR>

			<?php 
			while ( $row = mysql_fetch_array($result) ) {
				if ( $bg == 1 ) {
					$bgcolor = "#F3E7FD";
					$bg = 0;
				} else {
					$bgcolor = "whitesmoke";
					$bg = 1;
				} 
				$status = $row['Status'];
				switch ($status) {
					case "P":
						$status_verbose="Pending";
						break;
					case "A":
						$status_verbose="Approved";
						break;
					case "R":
						$status_verbose="Rejected";
						break;
				}
				?>
 
				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD><?php echo $row['PurchaseOrderNumber'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php
					if ( $row['DateReceived'] != '' ) {
						echo date("m/d/Y", strtotime($row['DateReceived']));
					} else {
						echo "&nbsp;";
					}
					?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['ProductNumberInternal'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD WIDTH=300><IMG SRC="images/spacer_long" WIDTH=300 HEIGHT=1 BORDER=0><BR><?php echo $row['Description'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><NOBR><?php echo $row['Vendor'] ?></NOBR></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['LotNumber'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><?php echo $row['LotSequenceNumber'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><?php echo number_format($row['Quantity'], 2) ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><?php echo number_format($row['PackSize'], 2) ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['UnitOfMeasure'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD style="background-color:<?php 
					if ("P" == $status) { echo "yellow"; } //status "Pending"
					else if ("A" == $status) { echo "#7AB829"; }  //status "Approved"
					else { echo "pink"; }
					?>"><?php echo $status_verbose ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><INPUT TYPE="button" VALUE="View" CLASS="submit" onClick="window.location='vendors_receipts.php?action=view&record_id=<?php echo $row['record_id']?>'" STYLE="font-size:7pt"></TD>
				</TR>

			<?php } ?>

		</TABLE>

	<?php } else {
		echo "No receipts match your search criteria.<BR>";
	}
}

 if ( "search" != $action && "" != $action ) {
	if ("new" != $action) {
		//populate variables with record
		if (""!=$record_id ) {

			$sql  = "SELECT purchaseorderdetail.ID as ID, purchaseorderdetail.PurchaseOrderNumber as PurchaseOrderNumber, purchaseorderdetail.PurchaseOrderSeqNumber as PurchaseOrderSeqNumber, ";
			$sql .= "purchaseorderdetail.ProductNumberInternal as ProductNumberInternal, purchaseorderdetail.Description as Description, vendors.vendor_id as VendorID, vendors.name as Vendor, ";
			$sql .= "tblsystemdefaultsdetail.Location_On_Site as Location_On_Site, productmaster.AllergenEgg, productmaster.AllergenMilk, productmaster.AllergenPeanut, productmaster.AllergenSeafood, ";
			$sql .= "productmaster.AllergenSeed, productmaster.AllergenSoybean, productmaster.AllergenSulfites, productmaster.AllergenTreeNuts, productmaster.AllergenWheat, productmaster.AllergenYellow, productmaster.FEMA_NBR, ";
			$sql .= "receipts.DateReceived as DateReceived, lots.LotNumber as LotNumber, lots.LotSequenceNumber as LotSequenceNumber, ";
			$sql .= "receipts.Quantity as Quantity, receipts.PackSize as PackSize, receipts.UnitOfMeasure as UnitOfMeasure, receipts.Status as Status, ";
			$sql .= "receipts.EmployeeID, receipts.VendorInvoiceNumber, purchaseorderdetail.VendorProductCode, lots.DateManufactured, lots.ExpirationDate, receipts.PackagingType, receipts.ConditionOfShipment, lots.StorageLocation, ";
			$sql .= "receipts.C_of_A_attached, receipts.Nutrition_on_file, receipts.MSDS_on_file, receipts.Allergen_statement_on_file, receipts.Specifications_on_file, receipts.KosherApproved, lots.QualityControlDate, lots.QualityControlEmployeeID, lots.SizeOfRetainTaken, receipts.Comments ";
			$sql .= "FROM receipts ";
			$sql .= "LEFT JOIN lots ON (receipts.LotId = lots.ID) ";
			$sql .= "LEFT JOIN purchaseorderdetail ON ( receipts.PurchaseOrderID = purchaseorderdetail.ID) ";
			$sql .= "LEFT JOIN purchaseordermaster ON ( purchaseordermaster.PurchaseOrderNumber = purchaseorderdetail.PurchaseOrderNumber) ";
			$sql .= "LEFT JOIN productmaster ON (purchaseorderdetail.ProductNumberInternal = productmaster.ProductNumberInternal) ";
			$sql .= "LEFT JOIN vendors ON (purchaseordermaster.VendorID=vendors.vendor_id) ";
			$sql .= "LEFT JOIN tblsystemdefaultsdetail ON ( tblsystemdefaultsdetail.ItemID= 20 AND lots.StorageLocation = tblsystemdefaultsdetail.ItemDescription) ";
			$sql .= "WHERE `receipts`.`ID`='$record_id'";
			$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$c = mysql_num_rows($result);

			if ( 1 == $c  ) {
				$row = mysql_fetch_array($result);
				$employee_id=$row['EmployeeID'];
				$invoice_number=$row['VendorInvoiceNumber'];
				$date_received = !empty($row[DateReceived]) ? date("n/j/Y",strtotime($row[DateReceived])) : "";
				$po_id=$row['ID'];
				$po_number=$row['PurchaseOrderNumber'];
				$po_sequence=$row['PurchaseOrderSeqNumber'];
				$description=$row['Description'];
				$product_number=$row['ProductNumberInternal'];
				$vendorID=$row['VendorID'];
				$vendor=$row['Vendor'];
				$vendor_product_code=$row['VendorProductCode'];
				$lot_number=$row['LotNumber'];
				$lot_sequence=$row['LotSequenceNumber'];
				$manufacture_date = !empty($row[DateManufactured]) ? date("n/j/Y",strtotime($row[DateManufactured])) : "";
				$expiration_date = !empty($row[ExpirationDate]) ? date("n/j/Y",strtotime($row[ExpirationDate])) : "";
				$quantity=$row['Quantity'];
				$pack_size=$row['PackSize'];
				$measurement_units=$row['UnitOfMeasure'];
				$package_type=$row['PackagingType'];
				$shipment_condition=$row['ConditionOfShipment'];
				$storage_location=$row['StorageLocation'];
				$location_on_site=$row['Location_On_Site'];
				$has_c_of_a=$row['C_of_A_attached'];
				$nutrition_on_file=$row['Nutrition_on_file'];
				$msds_on_file=$row['MSDS_on_file'];
				$allergen_on_file=$row['Allergen_statement_on_file'];
				$specifications_on_file=$row['Specifications_on_file'];
				$kosher_approved=$row['KosherApproved'];
				$qc_date = !empty($row[QualityControlDate]) ? date("n/j/Y",strtotime($row[QualityControlDate])) : "";
				$qc_employee_id=$row['QualityControlEmployeeID'];
				$retain_size=$row['SizeOfRetainTaken'];
				$comments=$row['Comments'];
				$status=$row['Status'];
				$allergen_egg=$row['AllergenEgg'];
				$allergen_milk=$row['AllergenMilk'];
				$allergen_peanut=$row['AllergenPeanut'];
				$allergen_seafood=$row['AllergenSeafood'];
				$allergen_seed=$row['AllergenSeed'];
				$allergen_soybean=$row['AllergenSoybean'];
				$allergen_sulfites=$row['AllergenSulfites'];
				$allergen_tree_nuts=$row['AllergenTreeNuts'];
				$allergen_wheat=$row['AllergenWheat'];
				$allergen_yellow=$row['AllergenYellow'];
				$FEMA_NBR=$row['FEMA_NBR'];
			}
			else
			{
				echo "<h2>Error: Receipt not found.</h2><h3>$sql</h3>";
				include ('footer.php');
				exit();
			}
		}
		else
		{
			if(""==$error_message) {
				echo "<h2>Error: No record chosen.</h2>";
				include ('footer.php');
				exit();
			} else {
			}
		}
	}
	else {
		//initialize blank variables
		$employee_id="";
		$invoice_number="";
		$date_received="";
		$po_number="";
		$product_number="";
		$description="";
		$vendor="";
		$vendor_product_code="";
		$lot_number="";
		$lot_sequence="";
		$manufacture_date="";
		$expiration_date="";
		$quantity="";
		$pack_size="";
		$measurement_units="";
		$allergen_egg="";
		$allergen_milk="";
		$allergen_peanut="";
		$allergen_seafood="";
		$allergen_seed="";
		$allergen_soybean="";
		$allergen_sulfites="";
		$allergen_tree_nuts="";
		$allergen_wheat="";
		$allergen_yellow="";
		$FEMA_NBR="";
		$package_type="";
		$shipment_condition="";
		$storage_location="";
		$location_on_site="";
		$has_c_of_a="";
		$nutrition_on_file="";
		$msds_on_file="";
		$allergen_on_file="";
		$specifications_on_file="";
		$kosher_approved="";
		$qc_date="";
		$qc_employee_id="";
		$retain_size="";
		$comments="";
		$status="P"; //status "Pending"
	}
	//initialize readonly flag variables all to true
	$readonly_employee_id=true;
	$readonly_invoice_number=true;
	$readonly_date_received=true;
	$readonly_po_number=true;
	$readonly_lot_number=true;
	$readonly_lot_sequence=true;
	$readonly_manufacture_date=true;
	$readonly_expiration_date=true;
	$readonly_quantity=true;
	$readonly_pack_size=true;
	$readonly_measurement_units=true;
	$readonly_allergen_egg=true;
	$readonly_allergen_milk=true;
	$readonly_allergen_peanut=true;
	$readonly_allergen_seafood=true;
	$readonly_allergen_seed=true;
	$readonly_allergen_soybean=true;
	$readonly_allergen_sulfites=true;
	$readonly_allergen_tree_nuts=true;
	$readonly_allergen_wheat=true;
	$readonly_allergen_yellow=true;
	$readonly_package_type=true;
	$readonly_shipment_condition=true;
	$readonly_storage_location=true;
	$readonly_has_c_of_a=true;
	$readonly_nutrition_on_file=true;
	$readonly_msds_on_file=true;
	$readonly_allergen_on_file=true;
	$readonly_specifications_on_file=true;
	$readonly_kosher_approved=true;
	$readonly_qc_date=true;
	$readonly_qc_employee_id=true;
	$readonly_retain_size=true;
	$readonly_comments=true;
	if ("edit"==$action || "new"==$action)
	{
		if ("P"==$status) { //status "Pending"
			$readonly_employee_id=false;
			$readonly_invoice_number=false;
			$readonly_date_received=false;
			$readonly_po_number=false;
			$readonly_lot_number=false;
			$readonly_lot_sequence=false;
			$readonly_manufacture_date=false;
			$readonly_expiration_date=false;
			$readonly_quantity=false;
			$readonly_pack_size=false;
			$readonly_measurement_units=false;
			$readonly_allergen_egg=false;
			$readonly_allergen_milk=false;
			$readonly_allergen_peanut=false;
			$readonly_allergen_seafood=false;
			$readonly_allergen_seed=false;
			$readonly_allergen_soybean=false;
			$readonly_allergen_sulfites=false;
			$readonly_allergen_tree_nuts=false;
			$readonly_allergen_wheat=false;
			$readonly_allergen_yellow=false;
			$readonly_package_type=false;
			$readonly_shipment_condition=false;
			$readonly_storage_location=false;
			$readonly_has_c_of_a=false;
			$readonly_nutrition_on_file=false;
			$readonly_msds_on_file=false;
			$readonly_allergen_on_file=false;
			$readonly_specifications_on_file=false;
			$readonly_kosher_approved=false;
			$readonly_qc_date=false;
			$readonly_qc_employee_id=false;
			$readonly_retain_size=false;
		} 
		else
		if ("A"==$status) { //status "Approved"
			$readonly_invoice_number=false;
			$readonly_package_type=false;
			$readonly_shipment_condition=false;
			$readonly_storage_location=false;
		}

		$readonly_comments=false;
	}
switch ($status) {
	case "P": $status_verbose="Pending"; break;
	case "A": $status_verbose="Approved"; break;
	case "R": $status_verbose="Rejected"; break;
}
$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

?>
<TABLE class="bounding">
<TR><TD>
 <div class="status_header" style="text-align:center; background-color:<?php 
if ("P" == $status) { echo "yellow"; } //status "Pending"
else if ("A" == $status) { echo "#7AB829"; }  //status "Approved"
else { echo "pink"; }
?>">
	<?php echo $status_verbose ?><input type="hidden" id="status" name="status" value="<?php echo $status ?>"/>
	<INPUT TYPE="hidden" ID="fema_nbr" NAME="fema_nbr" VALUE="<?php echo $FEMA_NBR ?>">
</div>
</TD></TR>
<TR>
<TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">

	<TR>
	<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
		<TD valign="top">
		<TABLE WIDTH="400" BORDER="1" CELLPADDING="1" CELLSPACING="1" RULES="NONE" FRAME="RHS">
			<TR>
				<TD style="text-align:right; width:120px" >Employee: </TD><TD><SELECT NAME="employee_id" CLASS="select" <?php echo ($readonly_employee_id ? " readonly='readonly' ": "\"") ?> ><?php printEmployeeOptions($employee_id); ?></SELECT></TD>
			</TR>
			<TR>
				<TD style="text-align:right;" >Invoice #: </TD><TD><INPUT TYPE="text" NAME="invoice_number" VALUE="<?php echo formatTxt(getFormSafe($invoice_number)) ?>" <?php echo ($readonly_invoice_number ? "readonly='readonly' ": "") ?>SIZE="30" /></TD>
			</TR>
			<TR>
				<TD style="text-align:right;" >Date Received: </TD><TD>
						<INPUT TYPE="text" ID="date_received" NAME="date_received" VALUE="<?php echo $date_received ?>" <?php echo (("edit"==$action && "P"==$status) || "new"==$action) ? "" : "READONLY=\"READONLY\""; ?>SIZE="15" />
						</TD>
			</TR>
			<TR>
				<TD style="text-align:right;" >P.O.#: </TD>
				<TD>
					<input type="hidden" id="po_id" name="po_id" value="<?php echo $po_id; ?>" />
					<input readonly="readonly" size="10" type="text" id="po_number" name="po_number" value="<?php echo $po_number ?>" />
					<input readonly="readonly" size="2" type="text" id="po_sequence" name="po_sequence" value="<?php echo $po_sequence ?>" />
<?php if ( ("edit"==$action || "new"==$action) && "P"==$status) { ?>
					<input type="text" size="40" id="po_number_search" NAME="po_number_search" />
<?php } ?>
				</TD>
			</TR>
			<tr>
			<td colspan=2>
			<table style="border-spacing:0px; border:1px solid #9966CC; width:450px; margin-right:15px;">
			<TR>
				<TD style="text-align:right; width:120px;" >Product Number: </TD><TD><INPUT TYPE="text" id="product_number" NAME="product_number" VALUE="<?php echo $product_number; ?>" SIZE="30" READONLY /></TD>
			</TR>
			<TR>
				<TD style="text-align:right;" >Description: </TD><TD><INPUT TYPE="text" id="description" NAME="description" VALUE="<?php echo $description; ?>" SIZE="60" READONLY /></TD>
			</TR>
			<TR>
				<TD style="text-align:right;" >Vendor: </TD><TD><INPUT TYPE="text" id="vendor" NAME="vendor" VALUE="<?php echo $vendor; ?>" SIZE="30" READONLY /><INPUT TYPE="hidden" id="vendor_id" NAME="vendor_id" VALUE="<?php echo $vendor_id; ?>" /></TD>
			</TR>
			<TR>
				<TD style="text-align:right;" >Vendor Product Code: </TD><TD><INPUT TYPE="text" id="vendor_product_code" NAME="vendor_product_code" VALUE="<?php echo $vendor_product_code; ?>" SIZE="30" READONLY /></TD>
			</TR>
			</table>
			</td>
			</tr>
			<TR>
				<TD style="text-align:right">Lot #: </TD>
				<TD>
					<INPUT TYPE="text" id="lot_number" NAME="lot_number" VALUE="<?php echo formatTxt(getFormSafe($lot_number)); ?>"<?php echo ($readonly_lot_number? " READONLY": ""); ?> SIZE="24" />
					Sequence #: <INPUT TYPE="test" id="lot_sequence" NAME="lot_sequence" VALUE="<?php echo formatTxt(getFormSafe($lot_sequence)); ?> " <?php echo ($readonly_lot_sequence? " READONLY": ""); ?> SIZE="5" />
				
				</TD>
			</TR>
			<TR>
				<TD style="text-align:right;" >Date of Manufacture: </TD>
				<TD>
					<INPUT TYPE="text" NAME="manufacture_date" id="manufacture_date" VALUE="<?php echo $manufacture_date ?>" <?php echo (("edit"==$action && "P"==$status) || "new"==$action) ? "" : "READONLY=\"READONLY\""; ?>SIZE="15" />
				</TD>
			</TR>
			<TR>
				<TD style="text-align:right;" >Expiration Date: </TD>
				<TD>
					<INPUT TYPE="text" NAME="expiration_date" id="expiration_date" VALUE="<?php echo $expiration_date ?>" <?php echo (("edit"==$action && "P"==$status) || "new"==$action) ? "" : "READONLY=\"READONLY\""; ?>SIZE="15" />
				</TD>
			</TR>
			<TR>
				<TD style="text-align:right;" >Quantity: </TD><TD><INPUT TYPE="text" id="quantity" NAME="quantity" VALUE="<?php echo (""!=$quantity && is_numeric($quantity) ? number_format(formatTxt(getFormSafe($quantity)),2) : "") ?>" <?php echo ($readonly_quantity ? "READONLY ": "") ?>SIZE="30" /></TD>
			</TR>
		<TR>
			<TD style="text-align:right">Pack Size: </TD><TD><INPUT TYPE="text" id="pack_size" NAME="pack_size" VALUE="<?php echo (""!=$pack_size && is_numeric($pack_size) ? number_format(formatTxt(getFormSafe($pack_size)),2) : "") ?>" <?php echo ($readonly_pack_size ? "READONLY ": "") ?>SIZE="30" /></TD>
		</TR>
		<TR>
			<TD style="text-align:right">Unit of Measure: </TD><TD><?php
			if ($readonly_measurement_units) { 
					echo "<input type=\"hidden\" id=\"measurement_units\" NAME=\"measurement_units\" value=\"$measurement_units\">";
					echo "<SELECT CLASS=\"select\" readonly=\"readonly\">";
					printInventoryUnitsOptions($measurement_units);
					echo "</SELECT>";
				} else {
					echo "<SELECT id=\"measurement_units\" NAME=\"measurement_units\" CLASS=\"select\" >";
					printInventoryUnitsOptions($measurement_units);
					echo "</SELECT>";
				}?></TD>
		</TR>
			</TABLE>
		</TD>
		<TD>
		<TABLE WIDTH="450" BORDER="0" CELLPADDING="1" CELLSPACING="1">
		<tr><td align="center" style="padding-left:1em;">
			<table>
			<th align="center" style="background-color:yellow" colspan="6">Allergens</th>
			<tr>
				<td align="right"><label for="allergen_egg">Egg </label></td><td><INPUT TYPE="checkbox" id="allergen_egg" NAME="allergen_egg" <?php echo ("1"==$allergen_egg ? "CHECKED ": ""); echo ($readonly_allergen_egg ? "READONLY ": "") ?> /></td>
				<td align="right"><label for="allergen_milk">Milk </label></td><td><INPUT TYPE="checkbox" id="allergen_milk" NAME="allergen_milk" <?php echo ("1"==$allergen_milk ? "CHECKED ": ""); echo ($readonly_allergen_milk ? "READONLY ": "") ?> /></td>
				<td align="right"><label for="allergen_peanut">Peanut </label></td><td><INPUT TYPE="checkbox" id="allergen_peanut" NAME="allergen_peanut"<?php echo ("1"==$allergen_peanut ? "CHECKED ": ""); echo ($readonly_allergen_peanut ? "READONLY ": "") ?>  /></td>
			</tr><tr>
				<td align="right"><label for="allergen_seafood">Seafood </label></td><td><INPUT TYPE="checkbox" id="allergen_seafood" NAME="allergen_seafood" <?php echo ("1"==$allergen_seafood ? "CHECKED ": ""); echo ($readonly_allergen_seafood ? "READONLY ": "") ?> /></td>
				<td align="right"><label for="allergen_seed">Seed </label></td><td><INPUT TYPE="checkbox" id="allergen_seed" NAME="allergen_seed" <?php echo ("1"==$allergen_seed ? "CHECKED ": ""); echo ($readonly_allergen_seed ? "READONLY ": "") ?> /></td>
				<td align="right"><label for="allergen_soybean">Soybean </label></td><td><INPUT TYPE="checkbox" id="allergen_soybean" NAME="allergen_soybean" <?php echo ("1"==$allergen_soybean ? "CHECKED ": ""); echo ($readonly_allergen_soybean ? "READONLY ": "") ?> /></td>
			</tr><tr>
				<td align="right"><label for="allergen_sulfites">Sulfites </label></td><td><INPUT TYPE="checkbox" id="allergen_sulfites" NAME="allergen_sulfites" <?php echo ("1"==$allergen_sulfites ? "CHECKED ": ""); echo ($readonly_allergen_sulfites ? "READONLY ": "") ?> /></td>
				<td align="right"><label for="allergen_tree_nuts">Tree Nuts </label></td><td><INPUT TYPE="checkbox" id="allergen_tree_nuts" NAME="allergen_tree_nuts" <?php echo ("1"==$allergen_tree_nuts ? "CHECKED ": ""); echo ($readonly_allergen_tree_nuts ? "READONLY ": "") ?> /></td>
				<td align="right"><label for="allergen_wheat">Wheat </label></td><td><INPUT TYPE="checkbox" id="allergen_wheat" NAME="allergen_wheat" <?php echo ("1"==$allergen_wheat ? "CHECKED ": ""); echo ($readonly_allergen_wheat ? "READONLY ": "") ?> /></td>
			</tr><tr>
				<td align="right"><label for="allergen_yellow">Yellow </label></td><td><INPUT TYPE="checkbox" id="allergen_yellow" NAME="allergen_yellow" <?php echo ("1"==$allergen_yellow ? "CHECKED ": ""); echo ($readonly_allergen_yellow ? "READONLY ": "") ?> /></td>
			</tr>
			</table>
			<table>
			<TR>
				<TD style="text-align:right">Type of Packaging: </TD><TD><SELECT NAME="package_type" CLASS="select" <?php echo ($readonly_package_type ? "READONLY ": "") ?>><?php printVendorPackagingTypeOptions($package_type); ?></SELECT></TD>
			</TR>
			<TR>
				<TD style="text-align:right">Condition of Shipment: </TD><TD><SELECT NAME="shipment_condition" CLASS="select" <?php echo ($readonly_shipment_condition ? "READONLY ": "") ?>><?php printShipmentConditionOptions($shipment_condition); ?></SELECT></TD>
			</TR>
			<TR>
				<TD style="text-align:right">Storage Location: </TD><TD><SELECT NAME="storage_location" CLASS="select" <?php echo ($readonly_storage_location ? "READONLY ": "") ?> ><?php printStorageLocationOptions($storage_location); ?></SELECT></TD>
			</TR>
			<TR>
				<TD align="center" colspan="2">
				<table>
			<tr>
				<td align="right"><label for="has_c_of_a">C. of A. Attached: </label></td><td><INPUT TYPE="checkbox" id="has_c_of_a" NAME="has_c_of_a" <?php echo ("1"==$has_c_of_a ? "CHECKED ": ""); echo ($readonly_has_c_of_a ? "READONLY ": "") ?> /></td>
				<td align="right"><label for="nutrition_on_file">Nutrition on File: </label></td><td><INPUT TYPE="checkbox" id="nutrition_on_file" NAME="nutrition_on_file" <?php echo ("1"==$nutrition_on_file ? "CHECKED ": ""); echo ($readonly_nutrition_on_file ? "READONLY ": "") ?> /></td>
			</tr><tr>
				<td align="right"><label for="msds_on_file">MSDS on File: </label></td><td><INPUT TYPE="checkbox" id="msds_on_file" NAME="msds_on_file" <?php echo ("1"==$msds_on_file ? "CHECKED ": ""); echo ($readonly_msds_on_file ? "READONLY ": "") ?> /></td>
				<td align="right"><label for="allergen_on_file">Allergen stmt on File:</label></td><td><INPUT TYPE="checkbox" id="allergen_on_file" NAME="allergen_on_file" <?php echo ("1"==$allergen_on_file ? "CHECKED ": ""); echo ($readonly_allergen_on_file ? "READONLY ": "") ?> /></td>
			</tr><tr>
				<td align="right"><label for="specifications_on_file">Specifications on File: </label></td><td><INPUT TYPE="checkbox" id="specifications_on_file" NAME="specifications_on_file" <?php echo ("1"==$specifications_on_file ? "CHECKED ": ""); echo ($readonly_specifications_on_file ? "READONLY ": "") ?> /></td>
				<td align="right"><label for="kosher_approved">Kosher Approved: </label></td><td><INPUT TYPE="checkbox" id="kosher_approved" NAME="kosher_approved" <?php echo ("1"==$kosher_approved ? "CHECKED ": ""); echo ($readonly_kosher_approved ? "READONLY ": "") ?> /></td>
			</tr>
			</table>
			</TD>
			</TR>
			<TR>
				<TD align="right">QC Date: </TD>
				<TD align="left"><INPUT TYPE="text" id="qc_date" NAME="qc_date"  VALUE="<?php echo $qc_date ?>" <?php echo ("edit"==$action && "P"==$status) ? "" : "READONLY=\"READONLY\"" ?> SIZE="15" />
			</TD>
			</TR>
			<TR>
				<TD align="right">QC Performed By: </TD><TD align="left"><?php 
				if ($readonly_qc_employee_id) { 
					echo "<input type=\"hidden\" id=\"qc_employee_id\" NAME=\"qc_employee_id\" value=\"$qc_employee_id\">";
					echo "<SELECT CLASS=\"select\" readonly=\"reasonly\">";
					printEmployeeOptions($qc_employee_id);
					echo "</SELECT>";
				} else {
					echo "<SELECT id=\"qc_employee_id\" NAME=\"qc_employee_id\" CLASS=\"select\" >";
					printEmployeeOptions($qc_employee_id);
					echo "</SELECT>";
				}?></TD>
			</TR>
			<TR>
				<TD align="right">Size of Retain Taken: </TD><TD align="left"><INPUT TYPE="text" NAME="retain_size" id="retain_size" VALUE="<?php echo formatTxt(getFormSafe($retain_size)) ?>" <?php echo ($readonly_retain_size ? "READONLY ": "") ?>SIZE="30" /></TD>
			</TR>
			</table>
		</tr></td>
			</table>
		</TD>
		</TR>
		<tr>
		<td colspan=2>
			<table><tr><td style="text-align:right;width:120px" valign="top">Comments: </td><td><textarea name="comments" <?php echo ($readonly_comments ? "READONLY ": "") ?> cols="80" rosw="6"><?php echo formatTxt(getFormSafe($comments)) ?></textarea></td></tr></table>
		</td>
		</tr>
	<TR>
		<TD align="center" valign="bottom" colspan="2">
				<br /><br />
<?php
	if ("edit" == $action || "new"==$action) { ?>
			<input TYPE="submit" class="submit_medium"" id="cancel_edit" name="cancel_edit" VALUE="Cancel" />
			<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
			<INPUT TYPE="submit" class="submit_medium"" id="save" name="save" VALUE="Save" />
			<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
<!--
			<INPUT TYPE="submit" class="submit_medium"" id="cancel" name="cancel" VALUE="Cancel">
			<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
-->
<?php }
	else { ?>
			<INPUT TYPE="button" id="search" name="search" VALUE="Search" CLASS="submit" onClick="window.location='vendors_receipts.php'" />
			<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
			<INPUT TYPE="submit" class="submit" id="edit" name="edit" VALUE="Edit">
			<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
<?php if ("R"==$status) { //status "Rejected" ?>
			<INPUT TYPE="submit" class="submit" VALUE="Rejected Receipts Report">
			<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
<?php }
		if ("P"==$status) { //status "Pending" ?>
			<INPUT TYPE="submit" class="submit"  id="delete" name="delete" VALUE="Delete">
			<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
			<INPUT name="new" id="new" TYPE="submit" class="submit" VALUE="New Receipt">
			<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
<?php }
		?>
			<INPUT TYPE="button" class="submit" id="qc_input" name="qc_input"  VALUE="QC Input Form" onClick="popup('pop_qc_input_form.php?receipt_id=<?php echo $record_id;?>',700,830)" />
			<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
			<INPUT TYPE="button" class="submit"  id="qc_report" name="qc_report" VALUE="QC Report" onClick="popup('reports/qc_form_receipt.php?receipt_id=<?php echo $record_id;?>',700,830)">
			<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
<?php 
		if ("P"==$status && "new"!=$action) { //status "Pending" ?>
			<INPUT TYPE="submit" class="submit" id="approve" name="approve" VALUE="Approve">
			<INPUT TYPE="hidden" id="ApproveEndPO" name="ApproveEndPO">
			<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
			<INPUT TYPE="submit" class="submit" id="reject" name="reject"  VALUE="Reject">
			<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
			<INPUT TYPE="submit" class="submit" id="print_pending_2x1" name="print_pending_2x1"  VALUE="2.25 X 1.25 Label">
<?php }
		if ("A"==$status) { //status "Approved" ?>
			<INPUT TYPE="submit" class="submit"  id="print_approved_2x4" name="print_approved_2x4" VALUE="2 X 4 Label"> 
<?php }
	} ?>
		</TD>
	</TR>
	</TABLE>
	</TD>
	</TR>
	</TABLE>
<BR><BR>
</TD>
</TR>
</TABLE>
<br>
<BR>
<br>
<BR>
<br>
<BR>
<?php 
}
?>
</FORM>

<BR><BR>

<script type="text/javascript">
<!---
$(document).ready(function(){
	$(":input[readonly]").addClass("readOnly");
<?php if (("edit"==$action && "P"==$status) || "new"==$action) { ?>
	$(":input#date_received").datepicker();
	$(":input#manufacture_date").datepicker();
	$(":input#expiration_date").datepicker({changeYear: true});
	$(":input#qc_date").datepicker();
<?php } ?>
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");
	if ( !($("#receipts_pending").attr("checked")) && !($("#receipts_qc_approved").attr("checked")) && !($("#receipts_rejected").attr("checked")) ) {
		$("#receipts_pending").click();
	}
	
	$(":submit").click(function() {
		$("#action").val(this.name);
		var label_params;
		switch (this.name)
		{
			case 'cancel_edit':
				window.location = "vendors_receipts.php?action=view&record_id=<?php echo $record_id ?>";
				return false;
				break;
			case 'search':
				break;
			case 'new':
				break;
			case 'save':
				alertMessage = validated();
				if ("" != alertMessage )
				{ 
					alert(alertMessage);
					return false;
				}
				break;
			case 'approve':
				alertMessage = validated();
				alertMessage += validate2('approve');
				if ("" != alertMessage )
				{ 
					alert(alertMessage);
					return false;
				}
				else {
					if (!confirm("Do you want to accept this receipt? Once accepted, it will be added to inventory.")) {
						return false;
					}
				}
				var quantity = $("#quantity").val() * $("#pack_size").val();
				var confirmText = $.ajax({
					url: "vendors_receiptCheckApprove.php",
					data: "po_id="+$("#po_id").val()+"&quantity="+quantity+"&units="+$("#measurement_units").val(),
					async: false
				}).responseText;
				if ("" != confirmText ) {
					var app_opt = callAlert(confirmText,"ApprovePOWarning"); 
					if ( app_opt == 2 )
						return false;
					else if ( app_opt == 6 ) { //Yes
						document.getElementById('ApproveEndPO').value = "NO";
					} else { //app_opt == 7 no acepts this amount and close the PO
					//	alert("Approve to close the PO? " + document.getElementById('ApproveEndPO').value);
						document.getElementById('ApproveEndPO').value = "YES";
					}
				}
				break;
			case 'reject':
				alertMessage = validated();
				alertMessage += validate2('reject');
				if ("" != alertMessage )
				{ 
					alert(alertMessage);
					return false;
				}
				else {
					if (!confirm("Do you want to reject this receipt?")) {
						return false;
					}
				}
				break;
			case 'delete':
				if (!confirm("Do you want to delete this record?")) {
					return false;
				}
				break;
			case 'print_approved_2x4':
				label_params="date_received="+$("#date_received").val()+"&po_number="+$("#po_number").val()+"&po_sequence="
				+$("#po_sequence").val()+"&product_number="+$("#product_number").val()+"&description="+
				encodeURIComponent($("#description").val())+
				"&vendor="+$("#vendor").val()+"&vendor_product_code="+$("#vendor_product_code").val()+"&lot_number="+
				$("#lot_number").val()+"&lot_seq_number="+$("#lot_sequence").val()+"&manufacture_date="+$("#manufacture_date").val()
				+"&expiration_date="+$("#expiration_date").val()+"&quantity="+$("#quantity").val()+"&pack_size="+
				$("#pack_size").val()+"&measurement_units="+$("#measurement_units").val()+"&qc_date="+$("#qc_date").val()+
				"&retain_size="+$("#retain_size").val()+"&fema_nbr="+$("#fema_nbr").val();
			
				window.location = "vendors_receipts_print_labels.php?size=2x4&"+label_params;
				return false;
				break;
			case 'print_pending_2x1':
				label_params="date_received="+$("#date_received").val()+"&po_number="+$("#po_number").val()+"&po_sequence="
				+$("#po_sequence").val()+"&product_number="+$("#product_number").val()+"&description="+
				encodeURIComponent($("#description").val())+
				"&vendor="+$("#vendor").val()+"&vendor_product_code="+$("#vendor_product_code").val()+"&lot_number="+
				$("#lot_number").val()+"&lot_seq_number="+$("#lot_sequence").val()+"&manufacture_date="+$("#manufacture_date").val()
				+"&expiration_date="+$("#expiration_date").val()+"&quantity="+$("#quantity").val()+"&pack_size="+
				$("#pack_size").val()+"&measurement_units="+$("#measurement_units").val()+"&qc_date="+$("#qc_date").val()+
				"&retain_size="+$("#retain_size").val();
				window.location = "vendors_receipts_print_labels.php?size=2.25x1.25&"+label_params;
				return false;
				break;
			default:
				alert ("this button not yet supported");
				break;
		}
	});
	$("#vendor_search").autocomplete("search/vendors.php", {
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#vendor_search").result(function(event, data, formatted) {
		if (data)
			$("#designation_search").val('');
			$("#external_number_search").val('');
			$("#internal_number_search").val('');
			$("#keyword_search").val('');
			$("#receipt_search").val('');
			$("#action").val('search');
			document.search.submit();
	});

	$("#designation_search").autocomplete("search/designations.php", {
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max:1000,
		scrollheight: 350,
		multipleSeparator: "¬",
		extraParams: { moreinfo:"true"}
	});
	$("#designation_search").result(function(event, data, formatted) {
		if (data)
			$("#vendor_search").val('');
			$("#external_number_search").val('');
			$("#internal_number_search").val('');
			$("#keyword_search").val('');
			$("#receipt_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
	$("#external_number_search").autocomplete("search/external_product_numbers.php", {
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 650,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#external_number_search").result(function(event, data, formatted) {
		if (data)
			$("#vendor_search").val('');
			$("#designation_search").val('');
			$("#internal_number_search").val('');
			$("#keyword_search").val('');
			$("#receipt_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
	$("#internal_number_search").autocomplete("search/internal_product_numbers.php", {
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 650,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#internal_number_search").result(function(event, data, formatted) {
		if (data)
			$("#vendor_search").val('');
			$("#designation_search").val('');
			$("#external_number_search").val('');
			$("#keyword_search").val('');
			$("#receipt_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
	$("#keyword_search").autocomplete("search/keywords.php", {
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#keyword_search").result(function(event, data, formatted) {
		if (data)
			$("#vendor_search").val('');
			$("#designation_search").val('');
			$("#external_number_search").val('');
			$("#internal_number_search").val('');
			$("#receipt_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	$("#receipt_search").autocomplete("search/receipts.php", {
		cacheLength: 1,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 1000,
		max:50,
		multipleSeparator: "¬",
		scroll: true,
		scrollHeight: 350,
		extraParams: { Pending:$("#receipts_pending").val(), Approved:$("#receipts_qc_approved").val(), Rejected:$("#receipts_rejected").val()  }
	});
	$("#receipt_search").result(function(event, data, formatted) {
		if (data)
			$("#vendor_search").val('');
			$("#designation_search").val('');
			$("#external_number_search").val('');
			$("#internal_number_search").val('');
			$("#keyword_search").val('');
			$("#record_id").val(data[1]);
			document.search.submit();
	});
	$("#po_number_search").autocomplete("search/open_purchase_orders.php", {
		cacheLength: 1,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		width: 1000,
		max: 50,
		selectFirst: false,
		multipleSeparator: "¬",
		scroll: true,
		scrollHeight: 350,
		extraParams: { c_id:$("#po_id").val() }
	});
	$("#po_number_search").result(function(event, data, formatted) {
		if (data) {
			$("#po_number").val(data[1]);
			$("#po_sequence").val(data[2]);
			$("#product_number").val(data[3]);
			$("#description").val(data[4]);
			$("#vendor_id").val(data[5]);
			$("#vendor").val(data[6]);
			$("#vendor_product_code").val(data[7]);
			$("#quantity").val(data[8]);
			$("#pack_size").val(data[9]);
			$("#measurement_units").val(data[10]);
			$("#po_id").val(data[11]);
		}
	});
	$("#po_number_search").change(function() {
		if (""==$("#po_number_search").val())
			$("#po_id").val("");
			$("#po_number").val("");
			$("#po_sequence").val("");
			$("#product_number").val("");
			$("#description").val("");
			$("#vendor_id").val("");
			$("#vendor").val("");
			$("#vendor_product_code").val("");
			$("#quantity").val("");
			$("#pack_size").val("");
			$("#measurement_units").val("");
	});
});
function validated() {
	// verify all fields have a value that need one;
	var alertMessage = "";
	if ("" == $("#date_received").val()) {
		alertMessage+="Date Received is a required Field\n";
		$("#date_received").attr("style", "border: solid 1px red");
	} else {
		$("#date_received").attr("style", "border: none");
	}
	if ("" == $("#po_id").val()) {
		alertMessage+="Purchase Order is a required Field\n";
		$("#po_number").attr("style", "border: solid 1px red");
		$("#po_sequence").attr("style", "border: solid 1px red");
		$("#po_number_search").attr("style", "border: solid 1px red");
	} else {
		$("#po_number").attr("style", "border: none");
		$("#po_sequence").attr("style", "border: none");
		$("#po_number_search").attr("style", "border: none");
	}
	if ("" == $("#lot_number").val()) {
		alertMessage+="Lot Number is a required Field\n";
		$("#lot_number").attr("style", "border: solid 1px red");
	} else {
		$("#lot_number").attr("style", "border: none");
	}
	if ("" == $("#lot_sequence").val()) {
		alertMessage+="Lot Sequence Number is a required Field\n";
		$("#lot_sequence").attr("style", "border: solid 1px red");
	} else {
		$("#lot_sequence").attr("style", "border: none");
	}
	if ("" == $("#quantity").val()) {
		alertMessage+="Quantity is a required Field\n";
		$("#quantity").attr("style", "border: solid 1px red");
	} else {
		$("#quantity").attr("style", "border: none");
	}
	if ("" == $("#pack_size").val()) {
		alertMessage+="Pack Size is a required Field\n";
		$("#pack_size").attr("style", "border: solid 1px red");
	} else {
		$("#pack_size").attr("style", "border: none");
	}
	if ("" == $("#measurement_units").val()) {
		alertMessage+="Unit of Measure is a required Field\n";
		$("#measurement_units").attr("style", "border: solid 1px red");
	} else {
		$("#measurement_units").attr("style", "border: none");
	}
	return alertMessage;
}
function validate2(action) {
	// verify all fields have a value that need one;
	var alertMessage = "";
	if ("" == $("#qc_date").val()) {
		alertMessage+="QC Date is required to " + action + " a receipt.\n";
		$("#qc_date").attr("style", "border: solid 1px red");
	} else {
		$("#qc_date").attr("style", "border: none");
	}
	if ("" == $("#qc_employee_id").val()) {
		alertMessage+="QC Performed By is required to " + action + " a receipt.\n";
		$("#qc_employee_id").attr("style", "border: solid 1px red");
	} else {
		$("#qc_employee_id").attr("style", "border: none");
	}

	return alertMessage;
}

//-->
</script>

<Script Language=JavaScript>
<!--
	var isChoice = 0;
	
	function callAlert(Msg,Title){
		
		txt = Msg;
		caption = Title;
		vbMsg(txt,caption)
		//alert(isChoice);
		return isChoice;
		//yes, isChoice = 6, no isChoice=7, cancel isChoice=2
		
	}
-->
</Script>

<Script Language=VBScript>
<!--
	Function vbMsg(isTxt,isCaption)
	
	testVal = MsgBox(isTxt,3,isCaption)
	isChoice = testVal

	End Function
-->
</Script>
<?php
include("inc_footer.php");

function getNextLotSequenceNumber($lot_number) {
	global $link;
	$sql  ="SELECT LotSequenceNumber 
			FROM lots 
			WHERE LotNumber='$lot_number' 
			ORDER BY LotSequenceNumber DESC LIMIT 1";
	$result = mysql_query($sql, $link) or 
		die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$result_count = mysql_num_rows($result);
	if (0 == $result_count) {
		return(1);
	}
	else {
		return(mysql_result($result,0,0) + 1);
	}
}
 
 ?>