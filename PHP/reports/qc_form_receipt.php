<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
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

include('../inc_global.php');


	$sql = "SELECT receipts.*, purchaseordermaster.VendorName, purchaseordermaster.PurchaseOrderNumber, productmaster.* FROM receipts 
	LEFT JOIN purchaseorderdetail ON (purchaseorderdetail.ID = receipts.PurchaseOrderID) 
	LEFT JOIN purchaseordermaster ON (purchaseordermaster.PurchaseOrderNumber = purchaseorderdetail.PurchaseOrderNumber) 
	LEFT JOIN productmaster ON (productmaster.ProductNumberInternal = purchaseorderdetail.ProductNumberInternal) 
	WHERE receipts.ID = " . $receipt_id;
	$result_receipts = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row_receipts = mysql_fetch_array($result_receipts);
	$ProductDesignation = ("" != $row_receipts['Natural_OR_Artificial'] ? $row_receipts['Natural_OR_Artificial']." " : "").$row_receipts['Designation'].("" != $row_receipts['ProductType'] ? " - ".$row_receipts['ProductType'] : "").("" != $row_receipts['Kosher'] ? " - ".$row_receipts['Kosher'] : "");
	$supplier = $row_receipts['VendorName'];
	$po_number=$row_receipts['PurchaseOrderNumber'];
	$ID = $row_receipts['LotID'];
	$ProductNumberInternal=$row_receipts['ProductNumberInternal'];

$sql = "SELECT * FROM lots WHERE ID=$ID";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
$LotNumber = $row['LotNumber'];
$LotSequenceNumber = $row['LotSequenceNumber'];
$QCLotNumberofStandard = $row['QCLotNumberofStandard'];
$QCCofAAvailable = $row['QCCofAAvailable'];
$QCCofAStandardAvailable = $row['QCCofAStandardAvailable'];
$SizeOfRetainTaken = $row['SizeOfRetainTaken'];
$QCPackagingTypeAndSize = ( $row['QCPackagingTypeAndSize'] == "" ) ? $row_receipts['PackagingType']. " " .$row_receipts['PackSize'] :  $row['QCPackagingTypeAndSize'];
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



if ( $row['DateManufactured'] != '' ) {
	$DateManufactured = date("n/j/Y", strtotime($row['DateManufactured']));
} else {
	$DateManufactured = '';
}

if ( $row['QCDateOfStandard'] != '' ) {
	$date_value1 = date("n/j/Y", strtotime($row['QCDateOfStandard']));
} else {
	$date_value1 = '';
}

if ( $row['QCMicrobiologicalReportDate'] != '' ) {
	$date_value2 = date("n/j/Y", strtotime($row['QCMicrobiologicalReportDate']));
} else {
	$date_value2 = '';
}

if ( $row['QualityControlDate'] != '' ) {
	$date_value3 = date("n/j/Y", strtotime($row['QualityControlDate']));
} else {
	$date_value3 = '';
}


$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="680" ALIGN=CENTER><TR><TD>

<B STYLE="color:red;font-size:10pt">abelei</B>, <B STYLE="color:black;font-size:10pt">Inc.</B><BR>
<B STYLE="color:black;font-size:10pt">Inbound - Raw Material Quality Control</B><BR><BR>

<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0><TR VALIGN=TOP><TD>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Product: <?php echo $ProductNumberInternal; ?></B></TD>
		<TD><?php echo $ProductDesignation.(""!=$bsn ? "- abelei# $ProductNumberExternal":"");?></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Supplier:</B></TD>
		<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
			<TR VALIGN=TOP>
				<TD><?php echo $supplier ?></TD>
			</TR>
		</TABLE>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">abelei P.O.:</B></TD>
		<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
			<TR VALIGN=TOP>
				<TD><?php echo $po_number ?></TD>
			</TR>
		</TABLE>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR>
		<TD><B CLASS="black">Packaging Type and Size:</B></TD>
		<TD><?php echo stripslashes($QCPackagingTypeAndSize);?></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Lot#:</B></TD>
		<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
			<TR VALIGN=TOP>
				<TD><?php echo $LotNumber;?></TD>
				<TD><IMG SRC="../images/spacer.gif" WIDTH="30" HEIGHT="1"></TD>
				<TD><B CLASS="black">Seq#:</B> <?php echo stripslashes($LotSequenceNumber);?></TD>
				<TD><IMG SRC="../images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B CLASS="black">Date of Mfg:</B> <?php echo $DateManufactured;?></TD>
			</TR>
		</TABLE>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR>
		<TD><B CLASS="black">C of A Available?:</B></TD>
		<TD>
		<?php if ( strtoupper($QCCofAAvailable) == 'N' or $QCCofAAvailable == 0 ) {
			echo "No";
		} elseif ( strtoupper($QCCofAAvailable) == 'Y' or $QCCofAAvailable == 1 ) {
			echo "Yes";
		} ?>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Date of Standard:</B></TD>
		<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
			<TR VALIGN=TOP>
				<TD><NOBR><?php echo $date_value1;?><NOBR></TD>
				<TD><IMG SRC="../images/spacer.gif" WIDTH="30" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B CLASS="black">Lot# of Standard:</B> <?php echo stripslashes($QCLotNumberofStandard);?></TD>
			</TR>
		</TABLE>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR>
		<TD><B CLASS="black">C of A Standard Available?:</B></TD>
		<TD>
		<?php if ( strtoupper($QCCofAStandardAvailable) == 'N' or $QCCofAStandardAvailable == 0 ) {
			echo "No";
		} elseif ( strtoupper($QCCofAStandardAvailable) == 'Y' or $QCCofAStandardAvailable == 1 ) {
			echo "Yes";
		} ?>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>


	<TR>
		<TD COLSPAN=2 BGCOLOR="#EFEFEF">&nbsp;<B CLASS="black">Raw Material Sample Characteristics</B></TD>
	</TR>

	<TR><TD COLSPAN=2 BGCOLOR="#EFEFEF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="6"></TD></TR>

	<TR>
		<TD><B CLASS="black">Color:</B></TD>
		<TD><?php echo stripslashes($QCColor);?></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR>
		<TD><B CLASS="black">Odor:</B></TD>
		<TD><?php echo stripslashes($QCOdor);?></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR>
		<TD><B CLASS="black">Granulation:</B></TD>
		<TD><?php echo stripslashes($QCGranulation);?></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR>
		<TD><B CLASS="black">Brix:</B></TD>
		<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
			<TR VALIGN=TOP>
				<TD><?php echo stripslashes($QCBrix);?></TD>
				<TD><IMG SRC="../images/spacer.gif" WIDTH="30" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B CLASS="black">Moisture:</B> <?php echo stripslashes($QCMoisture);?></TD>
			</TR>
		</TABLE>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR VALIGN=TOP>
		<TD><NOBR><B CLASS="black">Method for Organoleptic Evaluation:</B>&nbsp;&nbsp;&nbsp;</NOBR></TD>
		<TD><?php echo stripslashes($QCMethodForOrganolepticEvaluation);?></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Organoleptic Oberservations:</B></TD>
		<TD><?php echo stripslashes($QCOrganolepticOberservations);?></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR>
		<TD><B CLASS="black">Microbiological Report Needed:</B></TD>
		<TD>
		<?php if ( $QCMicrobiologicalReportNeeded == 0 or strtoupper($QCMicrobiologicalReportNeeded) == 'N' ) {
			echo "No";
		} elseif ( $QCMicrobiologicalReportNeeded == 1 or strtoupper($QCMicrobiologicalReportNeeded) == 'Y' ) {
			echo "Yes";
		} ?>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR>
		<TD><B CLASS="black">If yes, Report Date:</B></TD>
		<TD><NOBR><?php echo $date_value2;?><NOBR></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR>
		<TD><B CLASS="black">Report Meets Specs:</B></TD>
		<TD>
		<?php if ( $QCMicrobiologicalReportMeetsSpecs == 0 or strtoupper($QCMicrobiologicalReportMeetsSpecs) == 'N' ) {
			echo "No";
		} elseif ( $QCMicrobiologicalReportMeetsSpecs == 1 or strtoupper($QCMicrobiologicalReportMeetsSpecs) == 'Y' ) {
			echo "Yes";
		} ?>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">If no, what does not meet spec<BR>
		and what action was taken?:</B></TD>
		<TD><?php echo stripslashes($QCMicrobiologicalReportDoesNotMeetSpecs);?></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR>
		<TD><B CLASS="black">Product Meets All Specifications?:</B></TD>
		<TD>
		<?php if ( strtoupper($QCProductMeetsAllSpecs) == 'N' or $QCProductMeetsAllSpecs == 0 ) {
			echo "No";
		} elseif ( strtoupper($QCProductMeetsAllSpecs) == 'Y' or $QCProductMeetsAllSpecs == 1 ) {
			echo "Yes";
		} ?>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Comments:</B></TD>
		<TD><?php echo stripslashes($QCComments);?></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Evaluated by:</B></TD>
		<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
			<TR VALIGN=TOP>
				<TD>
				<?php
				$sql = "SELECT user_id, first_name, last_name FROM users WHERE ( user_type = 3 AND active = 1 ) OR user_id='$QualityControlEmployeeID' ORDER BY last_name";
				$result = mysql_query($sql, $link);
				if ( mysql_num_rows($result) > 0 ) {
					while ( $row = mysql_fetch_array($result) ) {
						if ( $QualityControlEmployeeID == $row['user_id'] ) {
							echo $row['first_name'] . " " . $row['last_name'];
						}
					}
				}
				?></TD>
				<TD><IMG SRC="../images/spacer.gif" WIDTH="30" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B CLASS="black">Date:</B> <?php echo $date_value3;?></TD>
			</TR>
		</TABLE>
		</TD>
	</TR>

</TABLE>



</TD></TR></TABLE>

</BODY>
</HTML>