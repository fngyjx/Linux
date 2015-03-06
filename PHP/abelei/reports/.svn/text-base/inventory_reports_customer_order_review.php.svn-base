<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

//SELECT * 
//FROM customerordermaster
//LEFT JOIN customerorderdetail ON customerordermaster.OrderNumber = customerorderdetail.CustomerOrderNumber
//WHERE CustomerPONumber = '4500470957'

include('../inc_global.php');
$where_clause = "2009-09-16";
$sql = "SELECT DISTINCT customerordermaster.OrderNumber, RequestedDeliveryDate, customerordermaster.CustomerPONumber, customerorderdetail.CustomerOrderSeqNumber, customerorderdetail.ProductNumberInternal, Quantity, PackSize, customerorderdetail.UnitOfMeasure, Natural_OR_Artificial, Designation, ProductType, productmaster.Kosher, externalproductnumberreference.ProductNumberExternal, name, CONCAT( customer_contacts.first_name,  ' ', customer_contacts.last_name ) AS contact_name, CONCAT( address1,  ' ', address2, ' ', city, ' ', state ) AS ship_to_address, bsm.BatchSheetNumber, bsm.CommitedToInventory AS committed, bsm.Manufactured AS manufactured
FROM customerordermaster
LEFT JOIN customerorderdetail ON customerordermaster.OrderNumber = customerorderdetail.CustomerOrderNumber
LEFT JOIN productmaster ON customerorderdetail.ProductNumberInternal = productmaster.ProductNumberInternal
LEFT JOIN externalproductnumberreference ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
LEFT JOIN customers ON customerordermaster.CustomerID = customers.customer_id
LEFT JOIN customer_contacts ON customerordermaster.ContactID = customer_contacts.contact_id 
LEFT JOIN batchsheetcustomerinfo AS bsci ON bsci.CustomerOrderNumber = customerordermaster.OrderNumber AND  bsci.CustomerOrderSeqNumber = customerorderdetail.CustomerOrderSeqNumber
LEFT JOIN batchsheetmaster AS bsm ON bsm.BatchSheetNumber = bsci.BatchSheetNumber AND bsm.ProductNumberInternal = customerorderdetail.ProductNumberInternal
WHERE (ShipDate IS NULL OR ShipDate = '') ORDER BY RequestedDeliveryDate ASC, customerordermaster.OrderNumber";
//  GROUP BY customerordermaster.OrderNumber
$result = mysql_query($sql, $link) or die (mysql_error());

?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="790" ALIGN=CENTER><TR><TD>



<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD WIDTH="60"><IMG SRC="../images/abelei_logo.png" WIDTH="60" BORDER="0"></TD>
		<TD WIDTH="10"><IMG SRC="../images/spacer.gif" WIDTH="10" BORDER="0"></TD>
		<TD WIDTH="80" VALIGN=MIDDLE STYLE="font-size:8pt"><NOBR>clever able capable</NOBR><BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<IMG SRC="../images/abelei_font.png" WIDTH="80" BORDER="0"><!-- <BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<SPAN STYLE="font-size:7pt">US INGREDIENTS</STYLE> --></TD>
		<TD ALIGN=RIGHT STYLE="font-size:8pt"><?php echo date("l, F j, Y")?></TD>
	</TR>
</TABLE><BR><BR>



<TABLE BORDER="0" WIDTH="790" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>

<B CLASS="header">Customer Order Review</B><BR><BR>



<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="2">
	<TR VALIGN=BOTTOM>
		<TD><B CLASS="black" STYLE="font-size:8pt">Due Date</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Product</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Customer Name</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Customer PO#</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Contact Name</B></TD>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Qty</B></TD>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Pack Size</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Units</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Batch<br/>Status</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Location</B></TD>
	</TR>

	<?php
	$current_date = '';
	$current_con = '';
	$current_pni = '';
	$current_cosq = '';
	while ( $row = mysql_fetch_array($result) ) {
		if ( ($current_con != $row["OrderNumber"]) or ( $current_con == $row["OrderNumber"] and $current_pni != $row["ProductNumberExternal"] and $current_cosq != $row["CustomerOrderSeqNumber"]) ) {
			$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
			?>
			<TR>
				<TD><?php
				if ( $current_date != $row["RequestedDeliveryDate"] ) {
					echo date("m/d/Y", strtotime($row["RequestedDeliveryDate"]));
				}
				?>&nbsp;</TD>
				<TD><?php echo $row["ProductNumberExternal"] . " " . $ProductDesignation;?>&nbsp;</TD>
				<TD><?php echo $row['name'];?>&nbsp;</TD>
				<TD><?php echo $row['CustomerPONumber'];?>&nbsp;</TD>
				<TD><?php echo $row['contact_name'];?>&nbsp;</TD>
				<TD ALIGN=RIGHT>&nbsp;<?php echo number_format($row['Quantity'], 2);?></TD>
				<TD ALIGN=RIGHT>&nbsp;<?php echo number_format($row['PackSize'], 2);?></TD>
				<TD><?php echo $row['UnitOfMeasure'];?>&nbsp;</TD>
				<TD><?php 
					if (0 != $row[manufactured] )
						$status = "Mfg'd";
					else
					if (0 != $row[committed] )
						$status = "Comt'd";
					else
						$status = "NONE";
					echo $status;
				?>&nbsp;</TD>
				<TD><?php echo $row['ship_to_address'];?>&nbsp;</TD>
			</TR>
			<?php
		}
		$current_date = $row["RequestedDeliveryDate"];
		$current_con = $row["OrderNumber"];
		$current_pni = $row["ProductNumberExternal"];
		$current_cosq = $row["CustomerOrderSeqNumber"];
	} ?>

</TABLE>



		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE><BR><BR>
</BODY>
</HTML>