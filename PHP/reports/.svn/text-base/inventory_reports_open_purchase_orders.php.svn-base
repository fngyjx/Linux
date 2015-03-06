<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

include('../inc_global.php');

?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>






<?php

$sql = "SELECT * FROM purchaseordermaster WHERE (ShippingDate = '0000-00-00 00:00:00' OR ShippingDate IS NULL )";
$result = mysql_query($sql, $link) or die (mysql_error());
$c = mysql_num_rows($result);
$i = 1;
while ( $row = mysql_fetch_array($result) ) {

	if ( $i < $c ) {
		echo "<DIV style='page-break-after: always'>";
	}

	$sql = "SELECT purchaseorderdetail.*, Natural_OR_Artificial, Designation, ProductType, productmaster.Kosher, externalproductnumberreference.ProductNumberExternal
	FROM purchaseordermaster
	LEFT JOIN purchaseorderdetail
	USING ( PurchaseOrderNumber )
	LEFT JOIN productmaster ON purchaseorderdetail.ProductNumberInternal = productmaster.ProductNumberInternal
	LEFT JOIN externalproductnumberreference ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
	WHERE PurchaseOrderNumber = " . $row['PurchaseOrderNumber'];
	$result_details = mysql_query($sql, $link) or die (mysql_error());

	?>




	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="680" ALIGN=CENTER><TR><TD>

	

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD WIDTH="60"><IMG SRC="../images/abelei_logo.png" WIDTH="60" BORDER="0"></TD>
			<TD WIDTH="10"><IMG SRC="../images/spacer.gif" WIDTH="10" BORDER="0"></TD>
			<TD WIDTH="80" VALIGN=MIDDLE STYLE="font-size:8pt"><NOBR>clever able capable</NOBR><BR>
			<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
			<IMG SRC="../images/abelei_font.png" WIDTH="80" BORDER="0"><!-- <BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<SPAN STYLE="font-size:7pt">US INGREDIENTS</STYLE> --></TD>
			<TD ALIGN=RIGHT STYLE="font-size:8pt"><?php //echo date("l, F j, Y")?></TD>
		</TR>
	</TABLE><BR><BR>

	

	<TABLE BORDER="0" WIDTH="680" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD>

	<B CLASS="header">Open Purchase Orders</B><BR><BR>

	
	<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD><B>Vendor: <?php echo $row['VendorName'];?></B></TD>
			<TD ALIGN=RIGHT>PO#: <?php echo $row['PurchaseOrderNumber'];?></TD>
		</TR>
	</TABLE><BR>

	
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" BGCOLOR="#000000"><TR><TD>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BGCOLOR="white">
		<TR>
			<TD>

	<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="3">
		<TR>
			<TD ALIGN=RIGHT><B CLASS="black">Date Order Placed:</B></TD>
			<TD><?php echo date("m/d/Y", strtotime($row["DateOrderPlaced"]));?></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><B CLASS="black">Shipping Date:</B></TD>
			<TD>&nbsp;</TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><B CLASS="black">Confirmation Order#:</B></TD>
			<TD><?php echo $row['ConfirmationOrderNumber'];?></TD>
		</TR>
	</TABLE>

			</TD>
			<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
			<TD>

	<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="3">
		<TR>
			<TD ALIGN=RIGHT><B CLASS="black">Ship Via:</B></TD>
			<TD><?php echo $row['ShipVia'];?></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><B CLASS="black">Sales Rep:</B></TD>
			<TD><?php echo $row['VendorSalesRep'];?></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><B CLASS="black">Phone#:</B></TD>
			<TD><?php echo $row['ShipToMainPhoneNumber'];?></TD>
		</TR>
	</TABLE>

			</TD>
		</TR>
	</TABLE>
	</TD></TR></TABLE><BR>

	<B CLASS="black">Notes:</B> <?php ;?><BR><BR>

	
	<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3">
		<TR ALIGN=CENTER>
			<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Qty</B></TD>
			<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Pack Size</B></TD>
			<TD><B CLASS="black" STYLE="font-size:8pt">Units</B></TD>
			<TD><B CLASS="black" STYLE="font-size:8pt">Description</B></TD>
			<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Unit Price</B></TD>
			<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Total</B></TD>
			<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Total<BR>Ordered</B></TD>
			<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Total<BR>Expected</B></TD>
			<TD><B CLASS="black" STYLE="font-size:8pt">Received</B></TD>
			<TD><B CLASS="black" STYLE="font-size:8pt">Status</B></TD>
		</TR>

		<?php
		$subtotal = 0;
		while ( $row_details = mysql_fetch_array($result_details) ) { ?>
			<TR>
				<TD ALIGN=RIGHT><?php echo $row_details['Quantity'];?></TD>
				<TD ALIGN=RIGHT><?php echo $row_details['PackSize'];?></TD>
				<TD><?php echo $row_details['UnitOfMeasure'];?></TD>
				<TD><?php echo $row_details['Description'];?></TD>
				<TD ALIGN=RIGHT>$<?php echo number_format($row_details['UnitPrice'], 2);?></TD>
				<?php $subtotal = $row_details['TotalQuantityExpected'] * $row_details['UnitPrice'];?>
				<TD ALIGN=RIGHT>$<?php echo number_format($subtotal, 2);?></TD>
				<TD ALIGN=RIGHT><?php echo $row_details['TotalQuantityOrdered'];?></TD>
				<TD ALIGN=RIGHT><?php echo $row_details['TotalQuantityExpected'];?></TD>
				<TD><?php //echo $row['xxx'];?></TD>
				<TD><?php echo $row['Status'];?></TD>
			</TR>
			<?php
			
		}
		?>

	</TABLE><BR>

	

	<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3">
		<TR ALIGN=CENTER>
			<TD><B CLASS="black" STYLE="font-size:8pt">Shipping & Handling:</B></TD>
			<TD ALIGN=RIGHT>$<?php echo number_format($row['ShippingAndHandlingCost'], 2)?></TD>
		</TR>

		<TR>
			<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Total:</B></TD>
			<TD ALIGN=RIGHT>$<?php echo number_format($subtotal + $row['ShippingAndHandlingCost'], 2)?></TD>
		</TR>

	</TABLE>

	

			</TD>
		</TR>
	</TABLE>

	</TD></TR></TABLE><BR><BR>



	<?php

	if ( $i < $c ) {
		echo "</DIV><BR>";
	}
	$i++;


}
?>






</BODY>
</HTML>