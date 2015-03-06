<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

if ( $_REQUEST['pon'] != '' ) {
	$pon = $_REQUEST['pon'];
}

include('../inc_global.php');
include('../search/system_defaults.php');

	$sql = "SELECT * FROM purchaseordermaster WHERE PurchaseOrderNumber = " . $pon;
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

	if ( $row['ShippingDate'] != '' ) {
		$ShippingDate = date("m/d/Y", strtotime($row['ShippingDate']));
	} else {
		$ShippingDate = '';
	}

	$DateOrderPlaced = $row['DateOrderPlaced'];
	$ConfirmationOrderNumber = $row['ConfirmationOrderNumber'];
	$contact_id = $row['contact_id'];
	$VendorSalesRep = $row['VendorSalesRep'];
	$ShipVia = $row['ShipVia'];
	$Notes = $row['Notes'];

?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">

<STYLE>

H2.top
{
position:relative;
font: 9pt verdana, tacoma, geneva, arial, sans-serif;
font-weight:bold;
color: #330066;
top:8px;
background-color: #FFFFFF;
display: inline;
left:12px;
}

</STYLE>

</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="680" ALIGN=CENTER><TR VALIGN=TOP><TD>



<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=MIDDLE>
		<TD WIDTH="60"><IMG SRC="../images/abelei_logo.png" WIDTH="60" BORDER="0"></TD>
		<TD WIDTH="10"><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT=1 BORDER="0"></TD>
		<TD WIDTH="80" VALIGN=MIDDLE STYLE="font-size:8pt"><NOBR>clever able capable</NOBR><BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<IMG SRC="../images/abelei_font.png" WIDTH="80" BORDER="0"></TD>
		<TD ALIGN=CENTER><NOBR><B CLASS="header">PURCHASE ORDER</B></NOBR></TD>
		<TD WIDTH="20"><IMG SRC="../images/spacer.gif" WIDTH="20" HEIGHT=1 BORDER="0"></TD>
		<TD ALIGN=RIGHT WIDTH=150><NOBR><B CLASS="header">#<?php echo $pon;?></B></NOBR></TD>
	</TR>
</TABLE><BR>



<TABLE BORDER="0" HEIGHT="770" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>


	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD BGCOLOR="white">

			<H2 CLASS="top">&nbsp;Vendor&nbsp;</H2>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" BGCOLOR="#CDCDCD"><TR><TD>
			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="10" BGCOLOR="white"><TR><TD>
			<TABLE BORDER="0" CELLPADDING="3" CELLSPACING="0" WIDTH=330>
				<TR>
					<TD STYLE="text-align:right"><B>Name:</B></TD>
					<TD><?php echo stripslashes($VendorName);?></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Address:</B></TD>
					<TD><?php echo $VendorStreetAddress1;?></TD>
				</TR>
				<?php if ( $VendorStreetAddress2 != '' ) { ?>
					<TR>
						<TD STYLE="text-align:right"></TD>
						<TD><?php echo $VendorStreetAddress2;?></TD>
					</TR>
				<?php } ?>
				<TR>
					<TD STYLE="text-align:right"><B>City:</B></TD>
					<TD><?php echo $VendorCity;?>, <?php echo $VendorState;?> <?php echo $VendorZipCode;?>
					</TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Phone:</B></TD>
					<TD><?php echo $VendorMainPhoneNumber;?></TD>
				</TR>
			</TABLE>
			</TD></TR></TABLE>
			</TD></TR></TABLE>

			</TD>
			<TD WIDTH=20 BGCOLOR="white"><IMG SRC="../images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
			<TD>

			<H2 CLASS="top">&nbsp;Ship To&nbsp;</H2>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" BGCOLOR="#CDCDCD"><TR><TD>
			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="10" BGCOLOR="white"><TR><TD>

			<TABLE BORDER="0" CELLPADDING="3" CELLSPACING="0" WIDTH=330>
				<TR>
					<TD STYLE="text-align:right"><B>Name:</B></TD>
					<TD><?php echo $ShipToName;?></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Address:</B></TD>
					<TD><?php echo $ShipToStreetAddress1;?></TD>
				</TR>
				<?php if ( $VendorStreetAddress2 != '' ) { ?>
					<TR>
						<TD STYLE="text-align:right"></TD>
						<TD><?php echo $ShipToStreetAddress2;?></TD>
					</TR>
				<?php } ?>
				<TR>
					<TD STYLE="text-align:right"><B>City:</B></TD>
					<TD><?php echo $ShipToCity;?>, <?php echo $ShipToState;?> <?php echo $ShipToZipCode;?></TD>
				</TR>
				<TR>
					<TD STYLE="text-align:right"><B>Phone:</B></TD>
					<TD><?php echo $ShipToMainPhoneNumber;?></TD>
				</TR>
			</TABLE>
			</TD></TR></TABLE>
			</TD></TR></TABLE>

				</TD>
			</TR>
		</TABLE><BR><BR>



	<?php

	$sql = "SELECT purchaseorderdetail . *, productmaster.Designation, productmaster.Natural_OR_Artificial, productmaster.Kosher
	FROM purchaseorderdetail
	LEFT JOIN productmaster
	USING ( ProductNumberInternal ) 
	WHERE PurchaseOrderNumber = '" . $pon . "'
	ORDER BY PurchaseOrderSeqNumber";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	if ( $c > 0 ) { ?>

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
			<TR>
				<TD>

		<TABLE ALIGN=RIGHT BORDER=1 CELLSPACING="0" CELLPADDING="3" BORDERCOLOR="#CDCDCD" WIDTH="100%">
		
			<TR VALIGN=BOTTOM>
				<TD ALIGN=CENTER><NOBR><B>Qty</B></NOBR></TD>
				<TD ALIGN=CENTER><NOBR><B>Pack size</B></NOBR></TD>
				<TD ALIGN=CENTER><NOBR><B>Units</B></NOBR></TD>
				<TD><B>Description</B></TD>
				<TD ALIGN=RIGHT><NOBR><B>Price</B></NOBR></TD>
				<TD ALIGN=RIGHT><NOBR><B>Total</B></NOBR></TD>
			</TR>
		
		<?php
		$total = 0;
		while ( $row = mysql_fetch_array($result) ) {
			$subtotal = QuantityConvert($row['TotalQuantityExpected'], $row['UnitOfMeasure'], "lbs") * $row['UnitPrice'];
			$total = $total + $subtotal;
			?>
			<TR>
				<TD ALIGN=CENTER><?php echo $row['Quantity'];?></TD>
				<TD ALIGN=CENTER><?php echo $row['PackSize'];?></TD>
				<TD ALIGN=CENTER><?php echo $row['UnitOfMeasure'];?></TD>
				<TD>#<?php
				if ( $row['Kosher'] != '' ) {
					$kosher_info = $row['Kosher'] . " ";
				} else {
					$kosher_info = "";
				}
				echo $row['VendorProductCode'] . " " . $row['Natural_OR_Artificial'] . " " . $kosher_info . $row['Designation'] . " - " . $row['ProductNumberInternal'];
				?></TD>
				<TD ALIGN=RIGHT><?php echo $row['UnitPrice'];?></TD>
				<TD ALIGN=RIGHT><?php echo "$".number_format($subtotal,2) ?></TD>
			</TR>
		<?php 
		}
		?>

		<TR>
			<TD COLSPAN=4 ROWSPAN=3>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="5">
	<TR>
		<TD>

		<H2 CLASS="top">&nbsp;Payment details&nbsp;</H2>

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" BGCOLOR="#CDCDCD"><TR><TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="10" BGCOLOR="#FFFFFF" WIDTH="150"><TR>
			<TD><?php echo $PaymentType;?></TD>	
		</TR></TABLE>
		</TD></TR></TABLE>

		</TD>

		<TD>

		<H2 CLASS="top">&nbsp;Shipping date&nbsp;</H2>

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" BGCOLOR="#CDCDCD"><TR><TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="10" BGCOLOR="#FFFFFF" WIDTH="150"><TR>
			<TD><?php echo $ShippingDate;?></TD>	
		</TR></TABLE>
		</TD></TR></TABLE>

		</TD>
	</TR>
</TABLE>

			</TD>
			<TD ALIGN=RIGHT>Sub total:</TD>
			<TD ALIGN=RIGHT><NOBR>$<?php echo number_format($total, 2);?></NOBR></TD>
		</TR>

		<TR>
			<TD ALIGN=RIGHT>Shipping & handling:</TD>
			<TD ALIGN=RIGHT><NOBR>$<?php echo number_format($ShippingAndHandlingCost, 2);?></NOBR></TD>
		</TR>

		<TR>
			<TD ALIGN=RIGHT><B>Total:</B></TD>
			<TD ALIGN=RIGHT><NOBR><B>$<?php echo number_format($total + $ShippingAndHandlingCost, 2);?></B></NOBR></TD>
		</TR>

		</TABLE>

		</TD></TR></TABLE><BR>

	<?php } ?>


<BR><HR NOSHADE SIZE=4 COLOR="#CDCDCD"><BR>


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR>
		<TD><B>Date:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT=1 BORDER="0"></TD>
		<TD><?php echo date("n/j/Y", strtotime($DateOrderPlaced));?></TD>
	</TR>
	<TR>
		<TD><B>Order#:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT=1 BORDER="0"></TD>
		<TD><?php echo $ConfirmationOrderNumber;?></TD>
	</TR>
	<TR>
		<TD><B>Sales rep:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT=1 BORDER="0"></TD>
		<TD><?php echo $VendorSalesRep;?></TD>
	</TR>
	<TR>
		<TD><B>Ship via:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT=1 BORDER="0"></TD>
		<TD><?php echo $ShipVia;?></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><BR><B>Notes/remarks</B><BR>
		<?php echo $Notes;?>
		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE>


<BR><BR>
<SPAN STYLE="font-size:8pt">
194 Alder Drive |
North Aurora, Illinois 60542 |
t-630.859.1410 |
f-630.859.1448 |
<SPAN STYLE="color: blue">toll-free 866-4-abelei</SPAN>
</SPAN>

</TD></TR></TABLE>

</BODY>
</HTML>