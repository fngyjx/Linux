<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

include('../inc_global.php');

?>



<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">

	<script type="text/javascript" language="javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/jquery-1.3.2.js"></script>
	<script type="text/javascript" src="/js/jquery-ui-1.7.2.custom.min.js"></script>
	<link type="text/css" href="/js/custom-theme/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
	<script type="text/javascript" language="javascript" src="/js/autocomplete/jquery.autocomplete.js"></script>
	<link rel="stylesheet" href="/js/autocomplete/jquery.autocomplete.css" type="text/css" />
	<script type="text/javascript" language="javascript" src="/js/helpers.js"></script>

</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0"><BR>



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

</script>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="680" ALIGN=CENTER><TR><TD>

<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD WIDTH="60"><IMG SRC="../images/abelei_logo.png" WIDTH="60" BORDER="0"></TD>
		<TD WIDTH="10"><IMG SRC="../images/spacer.gif" WIDTH="10" BORDER="0"></TD>
		<TD WIDTH="80" VALIGN=MIDDLE STYLE="font-size:8pt"><NOBR>clever able capable</NOBR><BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<IMG SRC="../images/abelei_font.png" WIDTH="80" BORDER="0"><!-- <BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<SPAN STYLE="font-size:7pt">US INGREDIENTS</STYLE> --></TD>
		<TD ALIGN=RIGHT STYLE="font-size:8pt">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
<FORM ACTION="raw_material_requirements_part2.php" METHOD="post">

	<TR>
		<TD><B>Date range:</B></TD>
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
		<TD>&nbsp;</TD>
		<TD><INPUT TYPE="submit" VALUE="Submit"></TD>
	</TR>
</FORM>
</TABLE>

		</TD>
	</TR>
</TABLE><BR>



<?php

if ( !empty($_POST) ) {

	if ( $start_date != '' and $end_date != '' ) {
		$start_date_parts = explode("/", $start_date);
		$end_date_parts = explode("/", $end_date);
		$mysql_start_date = $start_date_parts[2] . "-" . $start_date_parts[0] . "-" . $start_date_parts[1];
		$mysql_end_date = $end_date_parts[2] . "-" . $end_date_parts[0] . "-" . $end_date_parts[1];
		$date_filter = " AND (DateOrderPlaced >= '" . $mysql_start_date . "' AND DateOrderPlaced <= '" . $mysql_end_date . "')";
	} else {
		$error_found = true;
		echo "<BR><B>Please enter valid dates for 'Date range'</B><BR><BR><A HREF='JavaScript:history.go(-1)'>Choose new date range ></A>";
		die();
	}

	if ( !$error_found ) ?>

	<B>Committed Batch Sheets Raw Material Requirements, part 2</B><BR><BR>

	<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=1 BORDERCOLOR="#999999">

	<?php

	$c = 0;
	$old_ProductNumberInternal = '';
	$ProductNumberInternal = '';
	$sql = "SELECT DISTINCT vendors.name AS vendor, productmaster.Natural_OR_Artificial, productmaster.Designation, productmaster.ProductType, productmaster.Kosher, productmaster.UnitOfMeasure, ProductNumberExternal, productmaster.ProductNumberInternal, ShippingDate, DateOrderPlaced, purchaseordermaster.PurchaseOrderNumber, purchaseorderdetail.Quantity, purchaseorderdetail.PackSize, purchaseorderdetail.UnitOfMeasure
	FROM purchaseordermaster
	LEFT JOIN purchaseorderdetail
	USING ( PurchaseOrderNumber ) 
	LEFT JOIN productmaster ON productmaster.ProductNumberInternal = purchaseorderdetail.ProductNumberInternal
	LEFT JOIN externalproductnumberreference ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
	LEFT JOIN receipts ON purchaseorderdetail.ID = receipts.PurchaseOrderID
	LEFT JOIN vendors ON purchaseordermaster.VendorID = vendors.vendor_id
	WHERE purchaseorderdetail.ProductNumberInternal IS NOT NULL " . $date_filter . "
	AND (
	receipts.status =  'P'
	OR receipts.status IS NULL
	)
	ORDER BY Designation, productmaster.ProductNumberInternal, ShippingDate";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$i = mysql_num_rows($result);

	if ( $i > 0 ) {	

		while ( $row = mysql_fetch_array($result) ) {
			$c++;

			$ProductNumberExternal = $row['ProductNumberExternal'];
			$ProductNumberInternal = $row['ProductNumberInternal'];
			$vendor = $row['vendor'];
			$PurchaseOrderNumber = $row['PurchaseOrderNumber'];
			$Quantity = $row['Quantity'] * $row['PackSize'];
			$UnitOfMeasure = $row['UnitOfMeasure'];

			if ( $row['DateOrderPlaced'] != '' ) {
				$DateOrderPlaced = date("n/j/Y", strtotime($row['DateOrderPlaced']));
			} else {
				$DateOrderPlaced = '';
			}

			if ( $row['ShippingDate'] != '' ) {
				$ShippingDate = date("n/j/Y", strtotime($row['ShippingDate']));
			} else {
				$ShippingDate = '';
			}

			$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");


			if ( ($ProductNumberInternal != $old_ProductNumberInternal) and $old_ProductNumberInternal != '' ) {
				echo "<TR>";
				echo "<TD COLSPAN=8><IMG SRC='images/spacer.gif' WIDTH='1' HEIGHT='3'></TD>";
				echo "</TR>";
			}

			if ( $ProductNumberInternal != $old_ProductNumberInternal ) {
				echo "<TR BGCOLOR='black'>";
				echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Int#</TD>";
				echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Designation</TD>";
				echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white' ALIGN=RIGHT>Vendor</TD>";
				echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white' ALIGN=RIGHT>Ship date</TD>";
				echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white' ALIGN=RIGHT>Order date</TD>";
				echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>PO#</TD>";
				echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white' ALIGN=RIGHT>Qty</TD>";
				echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Units</TD>";
				echo "</TR>";
			}

			if ( $ProductNumberInternal != $old_ProductNumberInternal ) {
				echo "<TR VALIGN=TOP BGCOLOR='#DFDFDF'>";
			} else {
				echo "<TR VALIGN=TOP>";
			}
			echo "<TD STYLE='font-size:7pt'>";
			if ( $ProductNumberInternal != $old_ProductNumberInternal ) {
				echo $ProductNumberInternal;
			} else {
				echo "&nbsp;";
			}
			"</TD>";
			echo "<TD STYLE='font-size:7pt'>";
			if ( $ProductNumberInternal != $old_ProductNumberInternal ) {
				echo $ProductDesignation;
			} else {
				echo "&nbsp;";
			}
			"</TD>";
			echo "<TD STYLE='font-size:7pt' ALIGN=RIGHT>" . $vendor . "</TD>";
			echo "<TD STYLE='font-size:7pt' ALIGN=RIGHT>" . $ShippingDate . "</TD>";
			echo "<TD STYLE='font-size:7pt' ALIGN=RIGHT>" . $DateOrderPlaced . "</TD>";
			echo "<TD STYLE='font-size:7pt'>" . $PurchaseOrderNumber . "</TD>";
			echo "<TD STYLE='font-size:7pt' ALIGN=RIGHT>" . $Quantity . "</TD>";
			echo "<TD STYLE='font-size:7pt'>" . $UnitOfMeasure . "</TD>";

			echo "</TR>";

			$old_ProductNumberInternal = $row['ProductNumberInternal'];

		}

	} else {
		echo "<I>No records found</I>";
	}

}

?>



</TABLE><BR><BR>

</BODY>
</HTML>