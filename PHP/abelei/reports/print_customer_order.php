<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

// ADMIN AND FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 4 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( $_REQUEST['order_num'] != '' ) {
	$order_num = $_REQUEST['order_num'];
}

include('../inc_global.php');
include('../search/system_defaults.php');



$sql = "SELECT * FROM customerordermaster WHERE OrderNumber = " . $order_num;
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
$OrderNumber = $row['OrderNumber'];
$customer_id = $row['CustomerID'];
$OrderDate = $row['OrderDate'];
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

$RequestedDeliveryDate = $row['RequestedDeliveryDate'];

$sql = "SELECT name FROM customers WHERE customer_id = " . $customer_id;
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
$customer = $row['name'];

$sql = "SELECT first_name, last_name FROM customer_contacts WHERE contact_id = " . $ContactID;
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
$contact_name = $row['first_name'] . " " . $row['last_name'];

$sql = "SELECT number FROM customer_contact_phones WHERE contact_id = " . $ContactID . " AND type = 2 LIMIT 1";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$row = mysql_fetch_array($result);
	$phone = $row['number'];
} else {
	$phone = '';
}

$sql = "SELECT number FROM customer_contact_phones WHERE contact_id = " . $ContactID . " AND type = 3 LIMIT 1";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( mysql_num_rows($result) > 0 ) {
	$row = mysql_fetch_array($result);
	$mobile = $row['number'];
} else {
	$mobile = '';
}

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


$sql = "SELECT first_name, last_name FROM users WHERE user_id = " . $OrderTakenByEmployeeID;
$result_sales = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row_sales = mysql_fetch_array($result_sales);
$OrderTakenBy = $row_sales['first_name'] . ' ' . $row_sales['last_name'];
					
?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>

<TABLE CELLSPACING="0" CELLPADDING="0" WIDTH="680" ALIGN=CENTER><TR VALIGN=TOP><TD>



<TABLE WIDTH="100%" CELLSPACING="0" CELLPADDING="0" BORDER=0>
	<TR VALIGN=MIDDLE>
		<TD WIDTH="60"><IMG SRC="../images/abelei_logo.png" WIDTH="60"></TD>
		<TD WIDTH="10"><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT=1></TD>
		<TD WIDTH="80" VALIGN=MIDDLE STYLE="font-size:8pt"><NOBR>clever able capable</NOBR><BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7"><BR>
		<IMG SRC="../images/abelei_font.png" WIDTH="80"></TD>
		<TD ALIGN=RIGHT><NOBR><B CLASS="header">ORDER REQUEST FORM</B></NOBR></TD>
	</TR>
</TABLE><BR>



<TABLE HEIGHT="770" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD><BR>




<TABLE CELLSPACING="0" CELLPADDING="3" BORDER=0>
	<TR>
		<TD><B CLASS='black'>DATE:</B></TD>
		<TD>&nbsp;</TD>
		<TD><?php
		if ( $OrderDate != '' ) {
			echo date("m/d/Y", strtotime($OrderDate));
		} else { ?>
			&nbsp;
		<?php } ?></TD>
	</TR>
	<TR VALIGN="TOP">
		<TD><NOBR><B CLASS='black'>CUSTOMER:</B></NOBR></TD>
		<TD>&nbsp;</TD>
		<TD><?php echo $customer;?></TD>
	</TR>
	<TR>
		<TD><B CLASS='black'>CONTACT:</B></TD>
		<TD>&nbsp;</TD>
		<TD><?php echo $contact_name;?></TD>
	</TR>
	<TR>
		<TD><B CLASS='black'>BUSINESS PHONE:</B></TD>
		<TD>&nbsp;</TD>
		<TD><?php echo $phone;?></TD>
	</TR>
	<TR>
		<TD><B CLASS='black'>MOBILE:</B></TD>
		<TD>&nbsp;</TD>
		<TD><?php echo $mobile;?></TD>
	</TR>
</TABLE><BR><BR>



	<?php

	$sql = "SELECT customerorderdetail.*, externalproductnumberreference.ProductNumberExternal FROM customerorderdetail INNER JOIN externalproductnumberreference USING (ProductNumberInternal) WHERE CustomerOrderNumber = '" . $order_num . "' ORDER BY CustomerOrderSeqNumber";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	if ( $c > 0 ) { ?>

		<TABLE BORDER=1 CELLSPACING="0" CELLPADDING="3" BORDERCOLOR="#CDCDCD">

			<TR VALIGN=BOTTOM>
				<TD ALIGN=RIGHT><B CLASS='black'>Qty</B></TD>
				<TD ALIGN=RIGHT><B CLASS='black'>Pack Size</B></TD>
				<TD><B CLASS='black'>Units</B></TD>
				<TD><B CLASS='black'>Description</B></TD>
				<TD><B CLASS='black'>Ship Date</B></TD>
				<TD><B CLASS='black'>Billed Date</B></TD>
				<TD ALIGN=RIGHT><B CLASS='black'>Order Qty</B></TD>
				<TD><B CLASS='black'>Cust Code</B></TD>
			</TR>
		
		<?php
		while ( $row = mysql_fetch_array($result) ) {
			$BilledDate = $row['BilledDate'];
			$ShipDate = $row['ShipDate'];
			?>

			<TR VALIGN=MIDDLE>
			
				<TD ALIGN=RIGHT><?php echo number_format($row['Quantity'], 2);?></TD>
				<TD ALIGN=RIGHT><?php echo number_format($row['PackSize'], 2);?></TD>
				<TD><?php echo $row['UnitOfMeasure']; ?></TD>

				<?php
				$sql = "SELECT productmaster.Designation, Natural_OR_Artificial, ProductType, Kosher
				FROM productmaster
				LEFT JOIN externalproductnumberreference USING(ProductNumberInternal)
				WHERE productmaster.ProductNumberInternal = '" . $row['ProductNumberInternal'] . "'";
				$result_des = mysql_query($sql, $link) or die (mysql_error());
				$row_des = mysql_fetch_array($result_des);
				$ProductDesignation = "$row[ProductNumberExternal] - ".("" != $row_des[Natural_OR_Artificial] ? $row_des[Natural_OR_Artificial]." " : "").$row_des[Designation].("" != $row_des[ProductType] ? " - ".$row_des[ProductType] : "").("" != $row_des[Kosher] ? " - ".$row_des[Kosher] : "")." [#$row[ProductNumberInternal]]";
				?>

				<TD WIDTH="360"><IMG SRC="images/spacer" WIDTH=360 HEIGHT=1><BR><?php echo $ProductDesignation;?></TD>

				<TD><?php
					if ( $ShipDate != '' ) {
						echo date("m/d/Y", strtotime($ShipDate));
					} else { ?>
						&nbsp;
					<?php } ?>
				</TD>

				<TD><?php
					if ( $BilledDate != '' ) {
						echo date("m/d/Y", strtotime($BilledDate));
					} else { ?>
						&nbsp;
					<?php } ?>
				</TD>
	
				<TD ALIGN=RIGHT><?php echo number_format($row['TotalQuantityOrdered'], 2);?></TD>
				<TD><?php echo $row['CustomerCodeNumber'];?></TD>

			</TR>

			<?php } ?>

		</TABLE><BR><BR>

	<?php } ?>




<TABLE CELLSPACING="0" CELLPADDING="0" BORDER=0 WIDTH="100%">
	<TR VALIGN=TOP>
		<TD>

		<TABLE CELLSPACING="0" CELLPADDING="0" BORDER=0 WIDTH="46%">
			<TR VALIGN=TOP>
				<TD BGCOLOR="white">

				<B STYLE="color:black;font-size:10pt">BILL TO</B><BR>

				<TABLE CELLPADDING="3" CELLSPACING="0">
					<TR>
						<TD>&nbsp;&nbsp;&nbsp;</TD>
						<TD><NOBR><?php echo $billing_address1;?></NOBR></TD>
					</TR>
					<?php if ( $billing_address2 != '' ) { ?>
						<TR>
							<TD>&nbsp;&nbsp;&nbsp;</TD>
							<TD><NOBR><?php echo $billing_address2;?></NOBR></TD>
						</TR>
					<?php } ?>
					<TR>
						<TD>&nbsp;&nbsp;&nbsp;</TD>
						<TD><NOBR><?php echo $billing_city;?>, <?php echo $billing_state;?> <?php echo $billing_zip;?></NOBR></TD>
					</TR>
				</TABLE><BR><BR>

				<B STYLE="color:black;font-size:10pt">SHIP TO</B><BR>

				<TABLE CELLPADDING="3" CELLSPACING="0" BORDER=0>
					<TR>
						<TD>&nbsp;&nbsp;&nbsp;</TD>
						<TD><NOBR><?php echo $shipping_address1;?></NOBR></TD>
					</TR>
					<?php if ( $shipping_address2 != '' ) { ?>
						<TR>
							<TD>&nbsp;&nbsp;&nbsp;</TD>
							<TD><NOBR><?php echo $shipping_address2;?></NOBR></TD>
						</TR>
					<?php } ?>
					<TR>
						<TD>&nbsp;&nbsp;&nbsp;</TD>
						<TD><NOBR><?php echo $shipping_city;?>, <?php echo $shipping_state;?> <?php echo $shipping_zip;?></NOBR></TD>
					</TR>
				</TABLE>

				</TD>
			</TR>
		</TABLE>

		</TD>
		<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
		<TD>

		<TABLE CELLPADDING="3" CELLSPACING="0" BORDER=0 WIDTH="46%">
			<TR>
				<TD>

				<NOBR><B STYLE="color:black;font-size:10pt">P.O. NUMBER: </B> <?php echo $CustomerPONumber;?></NOBR><BR><BR>

				<?php if ( $C_of_A_Requested == 1 ) { ?>
					<IMG SRC="../images/bulletCheck.png" WIDTH="11" HEIGHT=9> C of A Requested<BR>
				<?php } else { ?>
					<IMG SRC="../images/bulletUnChecked.png" WIDTH="11" HEIGHT=9> C of A Requested<BR>
				<?php } ?><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT=5><BR>

				<?php if ( $MSDS_Requested == 1 ) { ?>
					<IMG SRC="../images/bulletCheck.png" WIDTH="11" HEIGHT=9> MSDS Requested<BR>
				<?php } else { ?>
					<IMG SRC="../images/bulletUnChecked.png" WIDTH="11" HEIGHT=9> MSDS Requested<BR>
				<?php } ?><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT=5><BR>

				<?php if ( $NAFTA_Requested == 1 ) { ?>
					<IMG SRC="../images/bulletCheck.png" WIDTH="11" HEIGHT=9> NAFTA Requested<BR>
				<?php } else { ?>
					<IMG SRC="../images/bulletUnChecked.png" WIDTH="11" HEIGHT=9> NAFTA Requested<BR>
				<?php } ?><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT=5><BR>

				<?php if ( $Hazardous_Info_Requested == 1 ) { ?>
					<NOBR><IMG SRC="../images/bulletCheck.png" WIDTH="11" HEIGHT=9> Hazardous Info Requested</NOBR><BR>
				<?php } else { ?>
					<NOBR><IMG SRC="../images/bulletUnChecked.png" WIDTH="11" HEIGHT=9> Hazardous Info Requested</NOBR><BR>
				<?php } ?><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT=5><BR>

				<?php if ( $Kosher == 1 ) { ?>
					<IMG SRC="../images/bulletCheck.png" WIDTH="11" HEIGHT=9> Kosher<BR>
				<?php } else { ?>
					<IMG SRC="../images/bulletUnChecked.png" WIDTH="11" HEIGHT=9> Kosher<BR>
				<?php } ?>

				</TD>
			</TR>
		</TABLE>

		</TD>
	</TR>
</TABLE><BR><BR>



<TABLE CELLSPACING="0" CELLPADDING="3" BORDER=0>
	<TR>
		<TD><B CLASS='black'>DELIVER BY:</B></TD>
		<TD>&nbsp;</TD>
		<TD><?php
		if ( $RequestedDeliveryDate != '' ) {
			echo date("m/d/Y", strtotime($RequestedDeliveryDate));
		} else { ?>
			&nbsp;
		<?php } ?></TD>
	</TR>
	<TR VALIGN="TOP">
		<TD><NOBR><B CLASS='black'>SPECIAL INSTRUCTIONS:</B></NOBR></TD>
		<TD>&nbsp;</TD>
		<TD><?php echo $SpecialInstructions;?></TD>
	</TR>
	<TR>
		<TD><B CLASS='black'>SHIP VIA:</B></TD>
		<TD>&nbsp;</TD>
		<TD><?php echo $ShipVia;?></TD>
	</TR>
	<TR>
		<TD><B CLASS='black'>ORDER TAKEN BY:</B></TD>
		<TD>&nbsp;</TD>
		<TD><?php echo $OrderTakenBy;?></TD>
	</TR>
</TABLE>

</TD></TR></TABLE>



<BR><BR>
<DIV STYLE="font-size:8pt" ALIGN=CENTER>
194 Alder Drive |
North Aurora, Illinois 60542 |
t-630.859.1410 |
f-630.859.1448 |
<SPAN STYLE="color: blue">toll-free 866-4-abelei</SPAN>
</DIV>

</TD></TR></TABLE>

</BODY>
</HTML>