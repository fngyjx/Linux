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
<FORM ACTION="inventory_reports_movements_list.php" METHOD="post">
	<INPUT TYPE="hidden" NAME="pni" VALUE="<?php echo $_REQUEST['pni'];?>">

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
		$date_filter = " AND (TransactionDate >= '" . $mysql_start_date . "' AND TransactionDate <= '" . $mysql_end_date . "')";
	} else {
		$error_found = true;
		echo "<BR><B>Please enter valid dates for 'Date range'</B><BR><BR><A HREF='JavaScript:history.go(-1)'>Choose new date range ></A>";
		die();
	}

	if ( !$error_found ) ?>

		<B CLASS="header">Inventory Movements List</B><BR><BR>

		<?php

		$sql = "SELECT Natural_OR_Artificial, Designation, ProductType, Kosher FROM productmaster WHERE ProductNumberInternal = " . $_REQUEST['pni'];
		$result = mysql_query($sql, $link) or die (mysql_error());
		$row = mysql_fetch_array($result);

		$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");

		?>

		<B CLASS="black">Product#: <?php echo $_REQUEST['pni'];?></B><BR>
		<B CLASS="black">Description: <?php echo $ProductDesignation;?></B><BR><BR>

		<?php

		$sql = "SELECT UnitOfMeasure FROM productmaster WHERE ProductNumberInternal = " . $_REQUEST['pni'];
		$result = mysql_query($sql, $link) or die (mysql_error());
		$row = mysql_fetch_array($result);
		$units = $row['UnitOfMeasure'];
		$sql = "SELECT (
		SELECT VendorId
		FROM purchaseordermaster
		WHERE PurchaseOrderNumber = ( 
		SELECT PurchaseOrderNumber
		FROM purchaseorderdetail
		WHERE ID = ( 
		SELECT PurchaseOrderID
		FROM receipts
		WHERE LotID = LotID
		LIMIT 1 ) 
		LIMIT 1 )
		) AS VendorID, (
		SELECT name
		FROM vendors
		WHERE vendor_id = VendorID
		) AS vendor_name, lots.ID, lots.LotNumber, lots.LotSequenceNumber
		FROM lots
		LEFT JOIN inventorymovements ON lots.ID = inventorymovements.LotID
		LEFT JOIN receipts USING ( LotID ) 
		WHERE LotNumber IS NOT NULL 
		AND LotSequenceNumber IS NOT NULL 
		AND ProductNumberInternal = " . $_REQUEST['pni'] . "
		AND InventoryMovementTransactionNumber IS NOT NULL" . $date_filter;
		$result = mysql_query($sql, $link) or die (mysql_error());

		$old_ID = "";
		//$old_LotNumber = "";
		//$old_LotSequenceNumber = "";

		while ( $row = mysql_fetch_array($result) ) {

			//if ( $old_LotSequenceNumber != $row['LotSequenceNumber'] ) {
			//if ( $old_LotSequenceNumber != $row['LotSequenceNumber'] and $old_LotNumber != $row['LotNumber'] ) {
			if ( $old_ID != $row['ID'] ) { ?>
			
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
					<TR>
						<TD>Lot#:</TD>
						<TD><?php echo $row['LotNumber'];?></TD>
						<TD>&nbsp;&nbsp;</TD>
						<TD>Seq#:</TD>
						<TD><?php echo $row['LotSequenceNumber'];?></TD>
						<TD>&nbsp;&nbsp;</TD>
						<TD>Vendor:</TD>
						<TD><?php echo $row['vendor_name'];?></TD>
						<TD>&nbsp;&nbsp;</TD>
						<TD>Beginning Amt:</TD>
						<TD><?php echo "0.00 lbs";?></TD>
					</TR>
					<!-- <TR>
						<TD COLSPAN=11><I>Inventory Movements:</I></TD>
					</TR> -->
				</TABLE>

				<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3">
					<TR ALIGN=CENTER BGCOLOR="#DFDFDF">
						<TD COLSPAN=4><B CLASS="black" STYLE="font-size:8pt">Inventory Movements</B></TD>
					</TR>
					<TR ALIGN=CENTER BGCOLOR="#FFFFFF">
						<TD><B CLASS="black" STYLE="font-size:8pt">Date</B></TD>
						<TD><B CLASS="black" STYLE="font-size:8pt">Description</B></TD>
						<TD><B CLASS="black" STYLE="font-size:8pt">Remarks</B></TD>
						<TD ALIGN=RIGHT><NOBR><B CLASS="black" STYLE="font-size:8pt">Qty (lbs)</B></NOBR></TD>
					</TR>
				
					<?php
					$sql = "SELECT TransactionDate, TransactionDescription, Remarks, Quantity, InventoryMultiplier
					FROM inventorymovements
					LEFT JOIN inventorytransactiontypes ON inventorymovements.TransactionType = inventorytransactiontypes.TransactionID
					WHERE LotID = " . $row['ID'] . $date_filter;
					$result_items = mysql_query($sql, $link) or die (mysql_error());
					while ( $row_items = mysql_fetch_array($result_items) ) { ?>
						<TR>
							<TD><?php
							if ( $row_items['TransactionDate'] != '' ) {
								echo date("n/j/Y", strtotime($row_items['TransactionDate']));
							}
							?></TD>
							<TD><?php echo $row_items['TransactionDescription'];?></TD>
							<TD><?php echo $row_items['Remarks'];?></TD>
							<TD ALIGN=RIGHT><NOBR>
							<?php
							$quantity = number_format(QuantityConvert($row_items['Quantity'] * $row_items['InventoryMultiplier'], "grams", "lbs"), 2);
							//if ( $units == 'grams' ) {
							//	$quantity = number_format(QuantityConvert($row_items['Quantity'], "lbs", "grams"), 2);
							//} elseif ( $units == 'lbs' ) {
							//	$quantity = number_format($row_items['Quantity'], 2);
							//} elseif ( $units == 'kg' ) {
							//	$quantity = number_format(QuantityConvert($row_items['Quantity'], "lbs", "kg"), 2);
							//}
							echo $quantity;
							//echo number_format($row_items['Quantity'], 2) . " " . $units;
							?></NOBR></TD>
						</TR>
					<?php } ?>
				
						<TR>
							<TD COLSPAN=3 ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Current Inventory:</B></TD>
							<TD ALIGN=RIGHT><?php
							$sql = "SELECT SUM(InventoryCount) AS count FROM vwinventory WHERE LotID = " . $row['ID'];   //  InventoryCount > 0 AND
							$result_count = mysql_query($sql, $link) or die (mysql_error());
							$row_count = mysql_fetch_array($result_count);		
							$quantity = number_format(QuantityConvert($row_count['count'], "grams", "lbs"), 2);
							//if ( $units == 'grams' ) {
							//	$quantity = number_format($row_count['count'], 2);
							//} elseif ( $units == 'lbs' ) {
							//	$quantity = number_format(QuantityConvert($row_count['count'], "grams", "lbs"), 2);
							//} elseif ( $units == 'kg' ) {
							//	$quantity = number_format(QuantityConvert($row_count['count'], "grams", "kg"), 2);
							//}
							echo $quantity;	
							?></TD>
						</TR>
				</TABLE><BR>

			<?php } ?>

			<?php
			$old_ID = $row['ID'];
			//$old_LotNumber = $row['LotNumber'];
			//$old_LotSequenceNumber = $row['LotSequenceNumber'];
		}
		?>

				</TD>
			</TR>
		</TABLE>

<?php } ?>




</TD></TR></TABLE><BR><BR>

</BODY>
</HTML>