<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');
if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

function prep_number($in) { return str_replace(',','',str_replace(' ','',$in)); }
// print_r($_REQUEST);

$bsn = isset($_REQUEST['bsn']) ? $_REQUEST['bsn'] : "";
$amt = isset($_REQUEST['qty']) ? $_REQUEST['qty'] : ""; 
$vendor_id = isset($_REQUEST['vendor_id']) ? $_REQUEST['vendor_id'] : "";
$vendor_name = isset($_REQUEST['vendor_name']) ? $_REQUEST['vendor_name'] : "";

$lot_id = isset($_REQUEST['lot_id']) ? $_REQUEST['lot_id'] : "";
$inv_qty = isset($_REQUEST['inv_qty']) ? $_REQUEST['inv_qty'] : ""; //inv_qty in unitofmeasure
$cstordnm = isset($_REQUEST['CustomerOrderNumber']) ? $_REQUEST['CustomerOrderNumber'] : "";
$cstordsqnm = isset($_REQUEST['CustomerOrderSeqNumber']) ? $_REQUEST['CustomerOrderSeqNumber'] : "";
$cstponm = isset($_REQUEST['CustomerPONumber']) ? $_REQUEST['CustomerPONumber'] : "";
//echo "<br />cstornm=". $cstornm ."<br />". $_REQUEST['CustomerOrderNumber'] ."<br />";
$sql = "SELECT `Intermediary`, `FinalProductNotCreatedByAbelei` FROM `productmaster` WHERE `ProductNumberInternal` = '$pni'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
$intermediary = ( 0 == mysql_result($result,0,0) ) ?  false : true;
$FinalProductNotCreatedByAbelei = ( 0 == mysql_result($result,0,1) ) ?  false : true;

$error_found = false;
$error_message = "";

//get pkg info from bsci or bsciplins
if ( $_REQUEST['PackIn'] != "" ) {
	$sql = "SELECT * FROM batchsheetcustomerinfo WHERE BatchSheetNumber = ". $bsn . " AND PackIn='" . escape_data($_REQUEST['PackIn']) ."'";
} elseif ($_REQUEST['PackInID'] != "" ) {
	$sql = "SELECT * FROM bscustomerinfopackins WHERE PackInID=".escape_data($_REQUEST['PackInID']);
} else {
	die ("Lack of Package information <br />");
}
// echo "<br />". $sql ."<br />";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$c = mysql_num_rows($result);
$i = 0;
if ( $row = mysql_fetch_array($result) ) {
	$packin = escape_data($row[PackIn]);
}

$c = 0;
$lot = $lot_id;
$qty_in = $inv_qty;
		
$qty_out = 0;
if ( $lot != '' and $qty_in != '' and 0 < $qty_in ) {
	$qty = $qty_in;
	$sql = "SELECT SUM(InventoryCount) as count FROM vwinventory WHERE LotID = ".$lot." AND ProductNumberInternal = '".$packin."'";
//	echo "<br />". $sql ."<br />";
	$result_check = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row_check = mysql_fetch_array($result_check);
	// rounding with units needs to be accounted for
	if ( $qty_in != $row_check[count] ) {
		$error_found = true;
		$error_message .= "Inventory Package Quantity in lot does not match with prev. Inventory Package amount $inv_qty , $row_check[count]<BR>";
	} 
	if ( $row_check[count] >= $amt ) {
		$qty_out = $amt;
	} else {
		$qty_out = $row_check[count];
		
	}
	$amt -= $qty_out ;	
			
//	echo "<br /> qty_out=". $qty_out."<br />qty needed = ". $amt ."<br />"; 
	if ( !$error_found and $qty_out > 0 ) {
		$remarks = "Cust PO# " . escape_data($cstponm) . " - " . escape_data($cstordnm). "-". escape_data($cstordsqnm);
	
		$sql = "INSERT INTO inventorymovements ".
		"(LotID, ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate, Remarks) ".
		"VALUES ".
		"(" . escape_data($lot) . ", " . $packin . ", " . escape_data($qty_out) . ", 8, 'C', '" . date("Y-m-d H:i:s") . "', '" . $remarks . "')";
	/*here using $tmpArr[2] - CustomerOrderNumber replaced CustomerCodeNumber that is queried from bsci, if needed, may replace customerordernumber with customercodenumber in keys - jdu */
								
//	echo "<br /> $sql<br />";
	start_transaction($link);	
	if ( ! mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}
	$trans_num = mysql_insert_id();
// echo $sql . "<BR>";
	$sql = "INSERT INTO batchsheetdetailpackaginglotnumbers ".
		"(BatchSheetNumber, CustomerOrderNumber, CustomerOrderSeqNumber, CustomerPONumber, PackagingProductNumber, LotID, InventoryMovementTransactionNumber, QuantityUsedFromThisLot) ".
		"VALUES (" .
		 escape_data($bsn) . ", '" . escape_data($cstordnm) . "', '" . escape_data($cstordsqnm) ."','" . escape_data($cstponm) . "', '" . $packin . "', '" . escape_data($lot) . "', " . escape_data($trans_num) . ", " . escape_data($qty_out) . ")";
//	echo "<br /> $sql <br />";
	if ( ! mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}
							
	end_transaction(1,$link);
				
//	echo $sql . "<BR>";

	} // !error_found
} // if lot != ''
   
//	echo "<br />amt=".$amt ."<br />";
	if ( $amt <= 0.00001 ) {
		$_SESSION['note'] .= "Lots successfully saved<BR>";
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "parent.location.href='pop_select_lots_for_batch_sheet_new.php?bsn=".$bsn."&pni=".$pni."';\n";
		echo "window.close();\n";
		echo "</SCRIPT>\n";
		exit();
	}
 //if post != ''

 ?>

<?php


	$sql_pndscrp = "SELECT Designation FROM productmaster where ProductNumberInternal = '". $packin ."'";
	$result_dscrp = mysql_query($sql_pndscrp,$link) or die (mysql_error() ." Failed execute SQL $sql_pndscrp <br />");
	$row_dscrp = mysql_fetch_array($result_dscrp);
	$sql = "SELECT 
		purchaseorderdetail.PurchaseOrderNumber AS po_no, VendorProductCode, vendors.name, 
		lots.LotNumber, lots.LotSequenceNumber, lots.ID, 
		ROUND(vwinventory.InventoryCount,2) AS InventoryCount 
		FROM vwinventory
		LEFT JOIN lots ON vwinventory.LotID = lots.ID
		LEFT JOIN receipts ON lots.ID = receipts.LotID
		LEFT JOIN purchaseorderdetail ON purchaseorderdetail.ID = receipts.PurchaseOrderID  
		LEFT JOIN purchaseordermaster ON purchaseordermaster.PurchaseOrderNumber = purchaseorderdetail.PurchaseOrderNumber 
		LEFT JOIN vendors ON purchaseordermaster.VendorId = vendors.vendor_id
		WHERE 
		LotNumber IS NOT NULL 
		AND vwinventory.ProductNumberInternal='$packin'  
		AND ROUND(InventoryCount,2) > 0";
//		echo "<br />". $sql . "<br />";
		
		$result_lots = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		// echo "<h3>$sql</h3>";
		if ( mysql_num_rows($result_lots) <= 0 or $amt < 0.00001) {
	
		$_SESSION['note'] .= "Did not find vendor lots for $packin with vendorid= $vendor_id<BR> Or qty needed is $amt <br />";
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "parent.location.href='pop_select_lots_for_batch_sheet_new.php?bsn=1713&pni=$packin';\n";
		echo "window.close();\n";
		echo "</SCRIPT>\n";
		exit();
	
		} else {
			$c = 0;
		?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
		<TR>
			<TD><B CLASS="black">P.O.: </b></td>
			<TD><?php echo $cstponm;?></td>
			<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
			<td><B>Packaging:</B> <?php echo "$packin] - [$row_dscrp[0]]"; ?></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
			<TD><B CLASS="black">Qty Needed:</B></TD>
			<TD><?php $quantity_needed = $amt; echo number_format($quantity_needed, 2);?></TD>
		</TR>
		</TABLE>

		<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
		<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
		<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">
		<TR VALIGN=TOP>
			<TD>&nbsp;</TD>
			<TD  STYLE="font-size: 11pt"><B>Vendor</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
			<TD  STYLE="font-size: 11pt"><B>Lot#</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
			<TD ALIGN=RIGHT  STYLE="font-size: 11pt"><B>Lot Seq#</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
			<TD ALIGN=RIGHT  STYLE="font-size: 11pt"><B>Inventory Count</B></TD>
		</TR>
		<?php $bg=0; while ( $row_lots = mysql_fetch_array($result_lots) ) {
		if ( $bg == 1 ) {
			$bgcolor = "#F3E7FD";
			$bg = 0;
		} else {
			$bgcolor = "whitesmoke";
			$bg = 1;
		}
		$c++;
		$button_href="assign_bsci_lot.php?PackIn=" . $_REQUEST['PackIn'] ."&PackInID=". $_REQUEST[PackInID] .
			"&lot_id=" . $row_lots[ID] . "&bsn=" . $bsn . "&vendor_id=" . $vendor_id .
			"&qty=" . $amt . "&inv_qty=" .$row_lots[InventoryCount] . 
			"&CustomerOrderNumber=". $cstordnm ."&CustomerPONumber=" . $cstponm .
			"&CustomerOrderSeqNumber=" .$cstordsqnm;
			
	//		echo "href = ". $button_href. "<br />";
		?>

		<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
			<TD><INPUT type="button" value="Select"  STYLE="font-size: 8pt"
						onClick="document.location.href='<?php echo $button_href;?>'">
			</TD>
			<TD  STYLE="font-size: 10pt"><?php echo $row_lots['name'];?></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
			<TD STYLE="font-size: 10pt"><?php echo $row_lots['LotNumber'];?></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
			<TD ALIGN=RIGHT STYLE="font-size: 10pt"><?php echo $row_lots['LotSequenceNumber'];?></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
			<TD ALIGN=RIGHT STYLE="font-size: 10pt"><?php echo number_format($row_lots[InventoryCount]);?></TD>
		</TR>
		<?php } ?>
		</TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE><BR>
		<?php 
	}
?>

<script LANGUAGE=JAVASCRIPT>
 <!-- Hide

//function delete_lot(RecordID, pni, amt, seq, order_num, mtn) {
//	if ( confirm('Are you sure you want to delete this item?') ) {
//		document.location.href = "pop_select_lots_for_batch_sheet.php?action=delete_lot&RecordID=" + RecordID + "&pni=" + pni + "&amt=" + amt + "&seq=" + seq + "&order_num=" + order_num + "&mtn=" + mtn
//	}
//}

 // End -->
 
</script>

<BR><BR>
</TD></TABLE>
<?php include("inc_footer.php"); ?>