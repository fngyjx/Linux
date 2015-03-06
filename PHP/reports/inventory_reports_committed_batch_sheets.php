<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

include('../inc_global.php');

$sql = "SELECT Natural_OR_Artificial, Designation, ProductType, productmaster.Kosher, productmaster.ProductNumberInternal FROM productmaster WHERE productmaster.ProductNumberInternal = " . $_GET['pni'];
$result = mysql_query($sql, $link) or die (mysql_error());
$row = mysql_fetch_array($result);

$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
//$external_number = $row['ProductNumberExternal'];
$internal_number = $row['ProductNumberInternal'];

$sql = "SELECT SUM(InventoryCount) AS count FROM vwinventory WHERE InventoryCount > 0 AND ProductNumberInternal = " . $_GET['pni'];
$result = mysql_query($sql, $link) or die (mysql_error());
$row = mysql_fetch_array($result);
$current = $row['count'];

$sql = "SELECT SUM( TotalQuantityExpected ) AS count, UnitOfMeasure
FROM purchaseordermaster
LEFT JOIN purchaseorderdetail
USING ( PurchaseOrderNumber ) 
WHERE (
ShippingDate = '0000-00-00 00:00:00'
OR ShippingDate IS NULL
)
AND purchaseorderdetail.ProductNumberInternal = " . $_GET['pni'] . "
GROUP BY UnitOfMeasure";
$result = mysql_query($sql, $link) or die (mysql_error());
$row = mysql_fetch_array($result);
$on_order = $row['count'];
$UnitOfMeasure = $row['UnitOfMeasure'];

$sql = "SELECT SUM(inventorymovements.Quantity) AS count
FROM batchsheetmaster
LEFT JOIN batchsheetdetail
USING ( BatchSheetNumber ) 
LEFT JOIN inventorymovements ON batchsheetdetail.InventoryTransactionNumber = inventorymovements.TransactionNumber
WHERE CommitedToInventory = 1
AND Manufactured = 0
AND batchsheetdetail.IngredientProductNumber = " . $_GET['pni'];
$result = mysql_query($sql, $link) or die (mysql_error());
$row = mysql_fetch_array($result);
$committed = $row['count'];

?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0"><BR>





<?php //if ( $c > 0 ) {



	?>

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

<B CLASS="header">Committed Batch Sheets/Open Purchase Orders</B><BR><BR>

<B><?php echo $ProductDesignation;?></B><BR><BR>



<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3">
	<TR VALIGN=BOTTOM ALIGN=CENTER>
		<TD><B CLASS="black" STYLE="font-size:8pt">Product#</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Current<BR>Inventory</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Total Amt<BR>Committed</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Total Amt<BR>on Order</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Net<BR>Inventory</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Units</B></TD>
	</TR>

	<TR ALIGN=CENTER>
		<TD><?php echo $internal_number;?></TD>
		<TD><?php echo number_format($current, 2);?></TD>
		<TD><?php echo number_format($committed, 2);?></TD>
		<TD><?php echo number_format($on_order, 2);?></TD>
		<TD><?php echo number_format($current + $on_order - $committed, 2);?></TD>
		<TD><?php echo $UnitOfMeasure;?></TD>
	</TR>

</TABLE><BR>



<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3">
	<TR ALIGN=CENTER BGCOLOR="#DFDFDF">
		<TD COLSPAN=5><B>Committed Batch Sheets</B></TD>
	</TR>
	<TR ALIGN=CENTER>
		<TD><B CLASS="black" STYLE="font-size:8pt">Qty</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Due Date</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Customer Name</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Customer PO#</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Final Product</B></TD>
	</TR>

	<!-- LOOP $row['xxx'] HERE -->
	<?php
	
	$sql = "SELECT DISTINCT Quantity, DueDate, name, CustomerPONumber, ProductNumberExternal
	FROM batchsheetmaster
	LEFT JOIN batchsheetdetail USING ( BatchSheetNumber ) 
	LEFT JOIN inventorymovements ON batchsheetdetail.InventoryTransactionNumber = inventorymovements.TransactionNumber
	LEFT JOIN batchsheetcustomerinfo USING ( BatchSheetNumber ) 
	LEFT JOIN customers ON batchsheetmaster.CustomerID = customers.customer_id
	WHERE CommitedToInventory = 1
	AND Manufactured = 0
	AND batchsheetdetail.IngredientProductNumber = " . $_GET['pni'];
	$result = mysql_query($sql, $link) or die (mysql_error());
	while ( $row = mysql_fetch_array($result) ) { ?>

		<TR>
			<TD ALIGN=RIGHT><?php echo number_format($row['Quantity'], 2);?></TD>
			<TD ALIGN=RIGHT><?php echo date("m/d/Y", strtotime($row["DueDate"]));?></TD>
			<TD><?php echo $row['name'];?></TD>
			<TD ALIGN=RIGHT><?php echo $row['CustomerPONumber'];?></TD>
			<TD ALIGN=RIGHT><?php echo $row['ProductNumberExternal'];?></TD>
		</TR>

	<?php } ?>

</TABLE><BR>



<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3">
	<TR ALIGN=CENTER BGCOLOR="#DFDFDF">
		<TD COLSPAN=10><B>Open Purchase Orders</B></TD>
	</TR>
	<TR ALIGN=CENTER>
		<TD><B CLASS="black" STYLE="font-size:8pt">Ship Date</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Order Date</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Vendor Name</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">PO#</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Qty</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Pack Size</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Units</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Total Qty<BR>Ordered</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Total Qty<BR>Expected</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Total Qty<BR>Received</B></TD>
	</TR>


	<?php
	$sql = "SELECT purchaseordermaster.VendorName, purchaseordermaster.DateOrderPlaced, purchaseordermaster.PurchaseOrderNumber, purchaseorderdetail.*
	FROM purchaseordermaster LEFT JOIN purchaseorderdetail USING ( PurchaseOrderNumber ) 
	WHERE (
	ShippingDate = '0000-00-00 00:00:00'
	OR ShippingDate IS NULL
	)
	AND purchaseorderdetail.ProductNumberInternal = " . $_GET['pni'];
	$result = mysql_query($sql, $link) or die (mysql_error());
	while ( $row = mysql_fetch_array($result) ) { ?>
		<TR>
			<TD ALIGN=RIGHT>&nbsp;</TD>
			<TD ALIGN=RIGHT><?php echo date("m/d/Y", strtotime($row["DateOrderPlaced"]));?></TD>
			<TD><?php echo $row['VendorName'];?></TD>
			<TD><?php echo $row['PurchaseOrderNumber'];?></TD>
			<TD ALIGN=RIGHT><?php echo $row['Quantity'];?></TD>
			<TD ALIGN=RIGHT><?php echo $row['PackSize'];?></TD>
			<TD><?php echo $row['UnitOfMeasure'];?></TD>
			<TD ALIGN=RIGHT><?php echo $row['TotalQuantityOrdered'];?></TD>
			<TD ALIGN=RIGHT><?php echo $row['TotalQuantityExpected'];?></TD>
			<TD>&nbsp;</TD>
		</TR>
	<?php } ?>

</TABLE>



		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE>



<?php //} ?>



</BODY>
</HTML>