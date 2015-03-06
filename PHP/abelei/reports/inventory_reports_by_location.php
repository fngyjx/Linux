<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

include('../inc_global.php');
//tblsystemdefaultsdetail
$sql = "SELECT Natural_OR_Artificial, Designation, ProductType, Kosher FROM productmaster WHERE ProductNumberInternal = " . $_GET['pni'];
$result = mysql_query($sql, $link) or die (mysql_error());
$row = mysql_fetch_array($result);

$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");

?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0"><BR>

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
		<TD ALIGN=RIGHT STYLE="font-size:8pt"><?php echo date("l, F j, Y")?></TD>
	</TR>
</TABLE><BR><BR>



<TABLE BORDER="0" WIDTH="680" HEIGHT="750" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>

<B CLASS="header">Current Inventory by Location</B><BR><BR>

<B CLASS="black"><?php echo $ProductDesignation;?> - <?php echo $_GET['pni'];?></B><BR><BR>



<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3">
	<TR ALIGN=CENTER>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Storage Location</B></TD>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Amount</B></TD>
		<TD>&nbsp;</TD>
	</TR>

	<?php
	$sql = "SELECT lots.ID, vwinventory.ProductNumberInternal, vwinventory.InventoryCount, lots.StorageLocation, receipts.UnitOfMeasure, receipts.PackSize, receipts.Quantity
	FROM receipts
	LEFT JOIN lots ON receipts.LotID = lots.ID
	LEFT JOIN vwinventory ON lots.ID = vwinventory.LotID
	WHERE vwinventory.LotID IS NOT NULL AND ProductNumberInternal = " . $_GET['pni'];
	$result = mysql_query($sql, $link) or die (mysql_error());

	$sum = 0;
	$total = 0;
	while ( $row = mysql_fetch_array($result) ) { ?>
		<TR>
			<TD ALIGN=RIGHT><?php echo $row['StorageLocation'];?></TD>
			<TD ALIGN=RIGHT><?php
			$sum = $row['InventoryCount'];   //$row['Quantity'] * $row['PackSize'];
			echo number_format($sum, 2);
			?></TD>
			<TD>&nbsp;</TD>
		</TR>

		<?php
		$total = $total + $sum;
		$units = $row['UnitOfMeasure'];
	}
	?>

	<TR>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Total:</B></TD>
		<TD ALIGN=RIGHT><?php echo number_format($total, 2);?></TD>
		<TD><?php echo $units;?></TD>
	</TR>

</TABLE><BR>



		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE>

</BODY>
</HTML>