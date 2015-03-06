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

if ( isset($_REQUEST['bsn']) ) {
	$bsn = $_REQUEST['bsn'];
}

if ( isset($_REQUEST['receipt_id']) ) {
	$receipt_id = $_REQUEST['receipt_id'];
}

include('inc_global.php');



if ( !empty($_POST) ) { // MAIN FORM

	$ID = $_POST['ID'];
	$QCLotNumberofStandard = $_POST['QCLotNumberofStandard'];
	$QCCofAAvailable = (isset($_POST['QCCofAAvailable']) ? $_POST['QCCofAAvailable'] : "");
	$QCCofAStandardAvailable = $_POST['QCCofAStandardAvailable'];
	$SizeOfRetainTaken = $_POST['SizeOfRetainTaken'];
	$QCPackagingTypeAndSize = (isset($_POST['QCPackagingTypeAndSize']) ? $_POST['QCPackagingTypeAndSize'] : "");
	$QCActualSpecificGravity = (isset($_POST['QCActualSpecificGravity']) ? $_POST['QCActualSpecificGravity'] : "");
	$QCColor = $_POST['QCColor'];
	$QCOdor = $_POST['QCOdor'];
	$QCGranulation = $_POST['QCGranulation'];
	$QCBrix = $_POST['QCBrix'];
	$QCMoisture = $_POST['QCMoisture'];
	$QCMethodForOrganolepticEvaluation = $_POST['QCMethodForOrganolepticEvaluation'];
	$QCOrganolepticOberservations = $_POST['QCOrganolepticOberservations'];
	$QCMicrobiologicalReportNeeded = $_POST['QCMicrobiologicalReportNeeded'];
	// $QCMicrobiologicalReportDate = $_POST['QCMicrobiologicalReportDate'];
	$QCMicrobiologicalReportMeetsSpecs = $_POST['QCMicrobiologicalReportMeetsSpecs'];
	$QCMicrobiologicalReportDoesNotMeetSpecs = $_POST['QCMicrobiologicalReportDoesNotMeetSpecs'];
	$QCProductMeetsAllSpecs = $_POST['QCProductMeetsAllSpecs'];
	$QCComments = $_POST['QCComments'];
	$QualityControlEmployeeID = $_POST['QualityControlEmployeeID'];

	$QCDateOfStandard = $_POST['QCDateOfStandard'];
	$QCMicrobiologicalReportDate = $_POST['QCMicrobiologicalReportDate'];
	$QualityControlDate = $_POST['QualityControlDate'];

	if ( $QCDateOfStandard != '' ) {
		$date_parts = explode("/", $QCDateOfStandard);
		$QCDateOfStandard = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
		if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
			if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
				$error_found=true;
				$error_message .= "Invalid (" . $QCDateOfStandard . ") date entered<BR>";
			}
		} else {
			$error_found=true;
			$error_message .= "Invalid (" . $QCDateOfStandard . ") date entered<BR>";
		}
	}

	if ( $QCMicrobiologicalReportDate != '' ) {
		$date_parts = explode("/", $QCMicrobiologicalReportDate);
		$QCMicrobiologicalReportDate = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
		if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
			if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
				$error_found=true;
				$error_message .= "Invalid (" . $QCMicrobiologicalReportDate . ") date entered<BR>";
			}
		} else {
			$error_found=true;
			$error_message .= "Invalid (" . $QCMicrobiologicalReportDate . ") date entered<BR>";
		}
	}

	if ( $QualityControlDate != '' ) {
		$date_parts = explode("/", $QualityControlDate);
		$QualityControlDate = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
		if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
			if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
				$error_found=true;
				$error_message .= "Invalid (" . $QualityControlDate . ") date entered<BR>";
			}
		} else {
			$error_found=true;
			$error_message .= "Invalid (" . $QualityControlDate . ") date entered<BR>";
		}
	}



	// check_field() FUNCTION IN global.php
	//check_field($xxx, 1, 'xxx');

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$QCDateOfStandard = escape_data($QCDateOfStandard);
		$QCLotNumberofStandard = escape_data($QCLotNumberofStandard);
		$QCCofAAvailable = escape_data($QCCofAAvailable);
		$QCCofAStandardAvailable = escape_data($QCCofAStandardAvailable);
		$SizeOfRetainTaken = escape_data($SizeOfRetainTaken);
		$QCPackagingTypeAndSize = escape_data($QCPackagingTypeAndSize);
		$QCActualSpecificGravity = escape_data($QCActualSpecificGravity);
		$QCColor = escape_data($QCColor);
		$QCOdor = escape_data($QCOdor);
		$QCGranulation = escape_data($QCGranulation);
		$QCBrix = escape_data($QCBrix);
		$QCMoisture = escape_data($QCMoisture);
		$QCMethodForOrganolepticEvaluation = escape_data($QCMethodForOrganolepticEvaluation);
		$QCOrganolepticOberservations = escape_data($QCOrganolepticOberservations);
		$QCMicrobiologicalReportNeeded = escape_data($QCMicrobiologicalReportNeeded);
		$QCMicrobiologicalReportDate = escape_data($QCMicrobiologicalReportDate);
		$QCMicrobiologicalReportMeetsSpecs = escape_data($QCMicrobiologicalReportMeetsSpecs);
		$QCMicrobiologicalReportDoesNotMeetSpecs = escape_data($QCMicrobiologicalReportDoesNotMeetSpecs);
		$QCProductMeetsAllSpecs = escape_data($QCProductMeetsAllSpecs);
		$QualityControlDate = escape_data($QualityControlDate);
		$QCComments = escape_data($QCComments);

		$sql = "UPDATE lots SET " .
		"QCDateOfStandard=" . ("" != $QCDateOfStandard ? "'$QCDateOfStandard'" : "NULL") . ", " .
		"QCLotNumberofStandard=" . ("" != $QCLotNumberofStandard ? "'$QCLotNumberofStandard'" : "NULL") . ", " .
		"QCCofAStandardAvailable=" . ("" != $QCCofAStandardAvailable ? "'$QCCofAStandardAvailable'" : "NULL") . ", " .
		"QCCofAAvailable=" . ("" != $QCCofAAvailable ? "'$QCCofAAvailable'" : "NULL") . ", " .
		"SizeOfRetainTaken=" . ("" != $SizeOfRetainTaken ? "'$SizeOfRetainTaken'" : "NULL") . ", " .
		"QCPackagingTypeAndSize=" . ("" != $QCPackagingTypeAndSize ? "'$QCPackagingTypeAndSize'" : "NULL") . ", " .
		"QCActualSpecificGravity=" . ("" != $QCActualSpecificGravity ? "'$QCActualSpecificGravity'" : "NULL") . ", " .
		"QCColor=" . ("" != $QCColor ? "'$QCColor'" : "NULL") . ", " .
		"QCOdor=" . ("" != $QCOdor ? "'$QCOdor'" : "NULL") . ", " .
		"QCGranulation=" . ("" != $QCGranulation ? "'$QCGranulation'" : "NULL") . ", " .
		"QCBrix=" . ("" != $QCBrix ? "'$QCBrix'" : "NULL") . ", " .
		"QCMoisture=" . ("" != $QCMoisture ? "'$QCMoisture'" : "NULL") . ", " .
		"QCMethodForOrganolepticEvaluation=" . ("" != $QCMethodForOrganolepticEvaluation ? "'$QCMethodForOrganolepticEvaluation'" : "NULL") . 	", " .
		"QCOrganolepticOberservations=" . ("" != $QCOrganolepticOberservations ? "'$QCOrganolepticOberservations'" : "NULL") . ", " .
		"QCMicrobiologicalReportNeeded=" . ("" != $QCMicrobiologicalReportNeeded ? "'$QCMicrobiologicalReportNeeded'" : "NULL") . ", " .
		"QCMicrobiologicalReportDate=" . ("" != $QCMicrobiologicalReportDate ? "'$QCMicrobiologicalReportDate'" : "NULL") . ", " .
		"QCMicrobiologicalReportMeetsSpecs=" . ("" != $QCMicrobiologicalReportMeetsSpecs ? "'$QCMicrobiologicalReportMeetsSpecs'" : "NULL") . "," .
		"QCMicrobiologicalReportDoesNotMeetSpecs=" . ("" != $QCMicrobiologicalReportDoesNotMeetSpecs ? "'$QCMicrobiologicalReportDoesNotMeetSpecs'" : "NULL") . ", " .
		"QCProductMeetsAllSpecs=" . ("" != $QCProductMeetsAllSpecs ? "'$QCProductMeetsAllSpecs'" : "NULL") . ", " .
		"QCComments=" . ("" != $QCComments ? "'$QCComments'" : "NULL") . ", " .
		"QualityControlEmployeeID=" . ("" != $QualityControlEmployeeID ? "'$QualityControlEmployeeID'" : "NULL") . ", " .
		"QualityControlDate=" . ("" != $QualityControlDate ? "'$QualityControlDate'" : "NULL") .
		" WHERE ID = " . $ID;
//		print "<br />$sql<br />";
//		return;
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$_SESSION['note'] = "QC information successfully saved<BR>"; //."<h3>$sql</h3><h2>".$_POST['date_value_hidden3']."</h2>";

		if (""!=$receipt_id) {
			$callback = "var url = window.opener.location + '?action=view&record_id=$receipt_id';\nwindow.opener.location.href=url;\n";
		} else if (""!=$bsn) {
			$callback = "var url = window.opener.location + '?action=edit&bsn=$bsn';\nwindow.opener.location.href=url;\n";
		} else {
			$callback = "window.opener.location.reload();\n";
		}
		
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo $callback;
		echo "window.close();\n";
		echo "</SCRIPT>\n";

	}

}
else if ("" != $bsn && ""!=$receipt_id)
{
	echo "Error - trying to edit a batch sheet lot AND and receipt lot";
	include("inc_footer.php");
	exit;
}
else if ("" != $bsn)
{
	$sql = "SELECT batchsheetmaster.*, customers.name FROM batchsheetmaster
	LEFT JOIN customers ON batchsheetmaster.CustomerID = customers.customer_id
	WHERE BatchSheetNumber = " . $bsn;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$ProductNumberExternal = $row['ProductNumberExternal'];
	$ProductDesignation = $row['ProductDesignation'];
	$customer = $row['name'];
	$ID = $row['LotID'];
}
else 
{ 
	$sql = "SELECT receipts.*, purchaseordermaster.VendorName, productmaster.* FROM receipts 
	LEFT JOIN purchaseorderdetail ON (purchaseorderdetail.ID = receipts.PurchaseOrderID) 
	LEFT JOIN purchaseordermaster ON (purchaseordermaster.PurchaseOrderNumber = purchaseorderdetail.PurchaseOrderNumber) 
	LEFT JOIN productmaster ON (productmaster.ProductNumberInternal = purchaseorderdetail.ProductNumberInternal) 
	WHERE receipts.ID = " . $receipt_id;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
	$supplier = $row['VendorName'];
	$ID = $row['LotID'];
}

$sql = "SELECT * FROM lots WHERE ID=$ID";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
$QCLotNumberofStandard = $row['QCLotNumberofStandard'];
$QCCofAAvailable = $row['QCCofAAvailable'];
$QCCofAStandardAvailable = $row['QCCofAStandardAvailable'];
$SizeOfRetainTaken = $row['SizeOfRetainTaken'];
$QCPackagingTypeAndSize = $row['QCPackagingTypeAndSize'];
$QCActualSpecificGravity = $row['QCActualSpecificGravity'];
$QCColor = $row['QCColor'];
$QCOdor = $row['QCOdor'];
$QCGranulation = $row['QCGranulation'];
$QCBrix = $row['QCBrix'];
$QCMoisture = $row['QCMoisture'];
$QCMethodForOrganolepticEvaluation = $row['QCMethodForOrganolepticEvaluation'];
$QCOrganolepticOberservations = $row['QCOrganolepticOberservations'];
$QCMicrobiologicalReportNeeded = $row['QCMicrobiologicalReportNeeded'];
$QCMicrobiologicalReportMeetsSpecs = $row['QCMicrobiologicalReportMeetsSpecs'];
$QCMicrobiologicalReportDoesNotMeetSpecs = $row['QCMicrobiologicalReportDoesNotMeetSpecs'];
$QCProductMeetsAllSpecs = $row['QCProductMeetsAllSpecs'];
$QCComments = $row['QCComments'];
$QualityControlEmployeeID = $row['QualityControlEmployeeID'];

$QCDateOfStandard = $row['QCDateOfStandard'];
$QCMicrobiologicalReportDate = $row['QCMicrobiologicalReportDate'];
$QualityControlDate = $row['QualityControlDate'];

$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

include("inc_pop_header.php");

?>

<?php if ( $error_found and $type == '' ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>



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



<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0><TR VALIGN=TOP><TD>



<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<FORM NAME="popper" METHOD="post" ACTION="pop_qc_input_form.php">
<INPUT TYPE="hidden" NAME="bsn" VALUE="<?php echo $bsn;?>">
<INPUT TYPE="hidden" NAME="receipt_id" VALUE="<?php echo $receipt_id;?>">
<INPUT TYPE="hidden" NAME="customer" VALUE="<?php echo $customer;?>">
<INPUT TYPE="hidden" NAME="supplier" VALUE="<?php echo $supplier;?>">
<INPUT TYPE="hidden" NAME="ID" VALUE="<?php echo $ID;?>">
<INPUT TYPE="hidden" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>">
<INPUT TYPE="hidden" NAME="ProductDesignation" VALUE="<?php echo $ProductDesignation;?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Product:</B></TD>
		<TD><?php echo $ProductDesignation.(""!=$bsn ? "- abelei# $ProductNumberExternal":"");?></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black"><?php echo (""!=$bsn ? "Customer":"Supplier"); ?>:</B></TD>
		<TD><?php echo (""!=$bsn ? "$customer":"$supplier"); ?></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Date of Standard:</B></TD>
		<TD><INPUT TYPE="text" SIZE="26" NAME="QCDateOfStandard" id="datepicker1" VALUE="<?php
		if ( $QCDateOfStandard != '' ) {
			echo date("m/d/Y", strtotime($QCDateOfStandard));
		}
		?>"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Lot# of Standard:</B></TD>
		<TD><INPUT TYPE='text' NAME="QCLotNumberofStandard" SIZE=26 VALUE="<?php echo stripslashes($QCLotNumberofStandard);?>"></TD>
	</TR>
<?php if (""==$bsn) {?>

	<TR>
		<TD><B CLASS="black">C of A Standard Available?:</B></TD>
		<TD><SELECT NAME="QCCofAStandardAvailable" STYLE="font-size: 7pt">
		<?php if ( $QCCofAStandardAvailable == 0 ) { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0" SELECTED>No</OPTION>
			<OPTION VALUE="1">Yes</OPTION>
		<?php } elseif ( $QCCofAStandardAvailable == 1 ) { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0">No</OPTION>
			<OPTION VALUE="1" SELECTED>Yes</OPTION>
		<?php } else { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0">No</OPTION>
			<OPTION VALUE="1">Yes</OPTION>
		<?php } ?>
		</SELECT></TD>
	</TR>
<?php }?>

	<TR>
		<TD><B CLASS="black">C of A Available?:</B></TD>
		<TD><SELECT NAME="QCCofAAvailable" STYLE="font-size: 7pt">
		<?php if ( $QCCofAAvailable == 0 ) { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0" SELECTED>No</OPTION>
			<OPTION VALUE="1">Yes</OPTION>
		<?php } elseif ( $QCCofAAvailable == 1 ) { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0">No</OPTION>
			<OPTION VALUE="1" SELECTED>Yes</OPTION>
		<?php } else { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0">No</OPTION>
			<OPTION VALUE="1">Yes</OPTION>
		<?php } ?>
		</SELECT></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Sample Size:</B></TD>
		<TD><INPUT TYPE='text' NAME="SizeOfRetainTaken" SIZE=26 VALUE="<?php echo stripslashes($SizeOfRetainTaken);?>"></TD>
	</TR>
	<?php if (""!=$bsn) {?>

	<TR>
		<TD><B CLASS="black">Packaging Type and Size:</B></TD>
		<TD><INPUT TYPE='text' NAME="QCPackagingTypeAndSize" SIZE=26 VALUE="<?php echo stripslashes($QCPackagingTypeAndSize);?>"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Actual Specific Gravity:</B></TD>
		<TD><INPUT TYPE='text' NAME="QCActualSpecificGravity" SIZE=26 VALUE="<?php echo stripslashes($QCActualSpecificGravity);?>"></TD>
	</TR>
<?php } ?>
	<TR>
		<TD><B CLASS="black">Color:</B></TD>
		<TD><INPUT TYPE='text' NAME="QCColor" SIZE=26 VALUE="<?php echo stripslashes($QCColor);?>"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Odor:</B></TD>
		<TD><INPUT TYPE='text' NAME="QCOdor" SIZE=26 VALUE="<?php echo stripslashes($QCOdor);?>"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Granulation:</B></TD>
		<TD><INPUT TYPE='text' NAME="QCGranulation" SIZE=26 VALUE="<?php echo stripslashes($QCGranulation);?>"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Brix:</B></TD>
		<TD><INPUT TYPE='text' NAME="QCBrix" SIZE=26 VALUE="<?php echo stripslashes($QCBrix);?>"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Moisture:</B></TD>
		<TD><INPUT TYPE='text' NAME="QCMoisture" SIZE=26 VALUE="<?php echo stripslashes($QCMoisture);?>"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Method for Organoleptic Evaluation:</B></TD>
		<TD><TEXTAREA NAME="QCMethodForOrganolepticEvaluation" ROWS="3" COLS="22"><?php echo stripslashes($QCMethodForOrganolepticEvaluation);?></TEXTAREA></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Organoleptic Oberservations:</B></TD>
		<TD><TEXTAREA NAME="QCOrganolepticOberservations" ROWS="3" COLS="22"><?php echo stripslashes($QCOrganolepticOberservations);?></TEXTAREA></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Microbiological Report Needed:</B></TD>
		<TD><SELECT NAME="QCMicrobiologicalReportNeeded" STYLE="font-size: 7pt">
		<?php if ( $QCMicrobiologicalReportNeeded == 0 ) { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0" SELECTED>No</OPTION>
			<OPTION VALUE="1">Yes</OPTION>
		<?php } elseif ( $QCMicrobiologicalReportNeeded == 1 ) { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0">No</OPTION>
			<OPTION VALUE="1" SELECTED>Yes</OPTION>
		<?php } else { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0">No</OPTION>
			<OPTION VALUE="1">Yes</OPTION>
		<?php } ?>
		</SELECT></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">If yes, Report Date:</B></TD>
		<TD><INPUT TYPE="text" SIZE="26" NAME="QCMicrobiologicalReportDate" id="datepicker2" VALUE="<?php
		if ( $QCMicrobiologicalReportDate != '' ) {
			echo date("m/d/Y", strtotime($QCMicrobiologicalReportDate));
		}
		?>"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Report Meets Specs:</B></TD>
		<TD><SELECT NAME="QCMicrobiologicalReportMeetsSpecs" STYLE="font-size: 7pt">
		<?php if ( $QCMicrobiologicalReportMeetsSpecs == 0 ) { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0" SELECTED>No</OPTION>
			<OPTION VALUE="1">Yes</OPTION>
		<?php } elseif ( $QCMicrobiologicalReportMeetsSpecs == 1 ) { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0">No</OPTION>
			<OPTION VALUE="1" SELECTED>Yes</OPTION>
		<?php } else { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0">No</OPTION>
			<OPTION VALUE="1">Yes</OPTION>
		<?php } ?>
		</SELECT></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">If no, what does not meet spec<BR>
		and what action was taken?:</B></TD>
		<TD><TEXTAREA NAME="QCMicrobiologicalReportDoesNotMeetSpecs" ROWS="3" COLS="22"><?php echo stripslashes($QCMicrobiologicalReportDoesNotMeetSpecs);?></TEXTAREA></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Product Meets All Specifications?:</B></TD>
		<TD><SELECT NAME="QCProductMeetsAllSpecs" STYLE="font-size: 7pt">
		<?php if ( $QCProductMeetsAllSpecs == 0 ) { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0" SELECTED>No</OPTION>
			<OPTION VALUE="1">Yes</OPTION>
		<?php } elseif ( $QCProductMeetsAllSpecs == 1 ) { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0">No</OPTION>
			<OPTION VALUE="1" SELECTED>Yes</OPTION>
		<?php } else { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="0">No</OPTION>
			<OPTION VALUE="1">Yes</OPTION>
		<?php } ?>
		</SELECT></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Comments:</B></TD>
		<TD><TEXTAREA NAME="QCComments" ROWS="3" COLS="22"><?php echo stripslashes($QCComments);?></TEXTAREA></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">QC Date:</B></TD>
		<TD><INPUT TYPE="text" SIZE="26" NAME="QualityControlDate" id="datepicker3" VALUE="<?php
		if ( $QualityControlDate != '' ) {
			echo date("m/d/Y", strtotime($QualityControlDate));
		}
		?>"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">QC Performed by:</B></TD>
		<TD><SELECT NAME="QualityControlEmployeeID" STYLE="font-size: 7pt">
		<OPTION VALUE=""></OPTION>
		<?php
		$sql = "SELECT user_id, first_name, last_name FROM users WHERE (user_type = 3 OR user_type = 5 OR user_type = 6) AND active = 1 ORDER BY last_name";
		$result = mysql_query($sql, $link);
		if ( mysql_num_rows($result) > 0 ) {
			while ( $row = mysql_fetch_array($result) ) {
				if ( $QualityControlEmployeeID == $row['user_id'] ) {
					echo "<OPTION VALUE='" . $row['user_id'] . "' SELECTED>" . $row['first_name'] . " " . $row['last_name'] . "</OPTION>";
				} else {
					echo "<OPTION VALUE='" . $row['user_id'] . "'>" . $row['first_name'] . " " . $row['last_name'] . "</OPTION>";
				}
			}
		}
		?>
		</SELECT></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD></TR>

	<TR>
		<TD></TD>
		<TD><INPUT TYPE='submit' VALUE="Save" CLASS="submit"> <INPUT TYPE='button' VALUE="Cancel" onClick="window.close()" CLASS="submit"></TD>
	</TR></FORM>
</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE>



<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

 // End -->
</SCRIPT>


<?php include("inc_footer.php"); ?>