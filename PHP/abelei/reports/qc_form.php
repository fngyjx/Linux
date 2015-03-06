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



//if ("" != $bsn) {
	$sql = "SELECT batchsheetmaster.*, customers.name FROM batchsheetmaster
	LEFT JOIN customers ON batchsheetmaster.CustomerID = customers.customer_id
	WHERE BatchSheetNumber = " . $bsn;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$ProductNumberExternal = $row['ProductNumberExternal'];
	$ProductNumberInternal = $row['ProductNumberInternal'];
	$ProductDesignation = $row['ProductDesignation'];
	$customer = $row['name'];
	$ID = $row['LotID'];

	$sql = "SELECT SpecificGravity, SpecificGravityUnits, RefractiveIndex FROM productmaster WHERE ProductNumberInternal = " . $ProductNumberInternal;
	$result_gravity = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row_gravity = mysql_fetch_array($result_gravity);
	$SpecificGravity = $row_gravity['SpecificGravity'];
	$SpecificGravityUnits = $row_gravity['SpecificGravityUnits'];
	$RefractiveIndex = $row_gravity['RefractiveIndex'];

//} else { 
//	$sql = "SELECT receipts.*, purchaseordermaster.VendorName, productmaster.* FROM receipts 
//	LEFT JOIN purchaseorderdetail ON (purchaseorderdetail.ID = receipts.PurchaseOrderID) 
//	LEFT JOIN purchaseordermaster ON (purchaseordermaster.PurchaseOrderNumber = purchaseorderdetail.PurchaseOrderNumber) 
//	LEFT JOIN productmaster ON (productmaster.ProductNumberInternal = purchaseorderdetail.ProductNumberInternal) 
//	WHERE receipts.ID = " . $receipt_id;
//	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
//	$row = mysql_fetch_array($result);
//	$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
//	$supplier = $row['VendorName'];
//	$ID = $row['LotID'];
//}

$sql = "SELECT * FROM lots WHERE ID=$ID";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
$LotSequenceNumber = $row['LotSequenceNumber'];
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
<B STYLE="color:black;font-size:10pt">Outbound - Finished Goods Quality Control</B><BR><BR>

<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0><TR VALIGN=TOP><TD>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Product:</B></TD>
		<TD><?php echo $ProductDesignation.(""!=$bsn ? "- abelei# $ProductNumberExternal":"");?></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Manufacturer:</B></TD>
		<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
			<TR VALIGN=TOP>
				<TD>abelei</TD>
				<TD><IMG SRC="../images/spacer.gif" WIDTH="30" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B CLASS="black">Sample Size:</B> <?php echo stripslashes($SizeOfRetainTaken);?></TD>
			</TR>
		</TABLE>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black"><?php echo (""!=$bsn ? "Customer":"Supplier"); ?>:</B></TD>
		<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
			<TR VALIGN=TOP>
				<TD><?php echo (""!=$bsn ? "$customer":"$supplier"); ?></TD>
				<TD><IMG SRC="../images/spacer.gif" WIDTH="30" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B CLASS="black">P.O.(s):</B> <?php
				$pos = '';
				$sql = "SELECT CustomerPONumber FROM batchsheetcustomerinfo WHERE BatchSheetNumber = " . $bsn;
				$result_po = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				while ( $row_po = mysql_fetch_array($result_po) ) {
					$pos[] = $row_po['CustomerPONumber'];
				}
				 $pos_joined = join("; ", $pos);
				 echo $pos_joined;
				?></TD>
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
				<TD><!-- THIS APPARENTLY IS NOT IN batchsheetmaster YET --></TD>
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
		<?php if ( $QCCofAStandardAvailable == 0 ) {
			echo "No";
		} elseif ( $QCCofAStandardAvailable == 1 ) {
			echo "Yes";
		} ?>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#DFDFDF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>
	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="3"></TD></TR>

<!-- 
	<TR>
		<TD><B CLASS="black">C of A Available?:</B></TD>
		<TD>
		<?php //if ( $QCCofAAvailable == 0 ) {
			//echo "No";
		//} elseif ( $QCCofAAvailable == 1 ) {
			//echo "Yes";
		//} ?>
		</TD>
	</TR>
 -->



	<TR VALIGN=TOP>
		<TD><B CLASS="black">Std Specific Gravity:</B></TD>
		<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
			<TR VALIGN=TOP>
				<TD><NOBR><?php echo number_format($SpecificGravity, 2) . " " . $SpecificGravityUnits;?><NOBR></TD>
				<TD><IMG SRC="../images/spacer.gif" WIDTH="30" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B CLASS="black">Actual Specific Gravity:</B> <?php echo stripslashes($QCActualSpecificGravity);?></TD>
			</TR>
		</TABLE>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="6"></TD></TR>
	<TR><TD COLSPAN=2 BGCOLOR="#EFEFEF"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="1"></TD></TR>

	<TR>
		<TD COLSPAN=2 BGCOLOR="#EFEFEF">&nbsp;<B CLASS="black">Finished Goods Characteristics</B></TD>
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
		<?php if ( $QCMicrobiologicalReportNeeded == 0 ) {
			echo "No";
		} elseif ( $QCMicrobiologicalReportNeeded == 1 ) {
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
		<?php if ( $QCMicrobiologicalReportMeetsSpecs == 0 ) {
			echo "No";
		} elseif ( $QCMicrobiologicalReportMeetsSpecs == 1 ) {
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
		<?php if ( $QCProductMeetsAllSpecs == 0 ) {
			echo "No";
		} elseif ( $QCProductMeetsAllSpecs == 1 ) {
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
		<TD><B CLASS="black">Refractive Index:</B></TD>
		<TD><?php echo stripslashes($RefractiveIndex);?></TD>
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
				$sql = "SELECT user_id, first_name, last_name FROM users WHERE user_type = 3 AND active = 1 ORDER BY last_name";
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