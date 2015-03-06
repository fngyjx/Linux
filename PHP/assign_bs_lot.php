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
//print_r($_REQUEST);

$bsn = isset($_REQUEST['bsn']) ? $_REQUEST['bsn'] : "";
$pni = isset($_REQUEST['pni']) ? $_REQUEST['pni'] : "";
$pni_seq = isset($_REQUEST['pni_seq']) ? $_REQUEST['pni_seq'] : "";
$amt = isset($_REQUEST['qty']) ? $_REQUEST['qty'] : ""; //in gram
$vendor_id = isset($_REQUEST['vendor_id']) ? $_REQUEST['vendor_id'] : "";
$vendor_name = isset($_REQUEST['vendor_name']) ? $_REQUEST['vendor_name'] : "";
$lot_id = isset($_REQUEST['lot_id']) ? $_REQUEST['lot_id'] : "";
$inv_qty = isset($_REQUEST['inv_qty']) ? $_REQUEST['inv_qty'] : ""; //inv_qty in unitofmeasure
$UnitOfMeasure = isset($_REQUEST['UnitOfMeasure']) ? $_REQUEST['UnitOfMeasure'] : "";

$sql = "SELECT `Intermediary`, `FinalProductNotCreatedByAbelei` FROM `productmaster` WHERE `ProductNumberInternal` = '$pni'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
$intermediary = ( 0 == mysql_result($result,0,0) ) ?  false : true;
$FinalProductNotCreatedByAbelei = ( 0 == mysql_result($result,0,1) ) ?  false : true;

$error_found = false;
$error_message = "";

if ( !empty($_REQUEST) && $lot_id != "" ) {
//this block is checkking whether assigned lot amont = needed amount, maybe deleted later
	$sql = "SELECT batchsheetdetail.*, productmaster.Designation, inventorymovements.Quantity
	FROM batchsheetdetail
	LEFT JOIN inventorymovements ON batchsheetdetail.InventoryTransactionNumber = inventorymovements.TransactionNumber
	LEFT JOIN productmaster ON batchsheetdetail.IngredientProductNumber = productmaster.ProductNumberInternal
	WHERE batchsheetdetail.BatchSheetNumber = '$bsn' AND IngredientProductNumber = '$pni' and IngredientSEQ = '$pni_seq'
	ORDER BY IngredientSEQ";
//	echo "<br />". $sql ."<br />";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	if ( $c > 0 ) {
		$total = 0;
		$i = 0;
		if ( $row = mysql_fetch_array($result) ) {
			if ( '10829' == substr($row[IngredientProductNumber],0,5) ) // Ignore Water
				continue;
			if ( 4 == substr($row[IngredientProductNumber], 0, 1) ) // Ignore instructions
				continue;
			if ( 0 >= $row[Quantity] ) //Ignore if not enough quantity
				continue;
			$bg = 0;
			$i++;
			$c = 0;
			$lot = $lot_id;
			$qty_in = $inv_qty;
			$units = $UnitOfMeasure;
			$qty_out = 0;
			if ( $lot != '' and $qty_in != '' and 0 < $qty_in ) {
				$qty = QuantityConvert($qty_in,$units,'grams');
				$sql = "SELECT SUM(InventoryCount) as count FROM vwinventory WHERE LotID = $lot AND ProductNumberInternal = $row[IngredientProductNumber]";
				//echo "<br />". $sql ."<br />";
				$result_check = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$row_check = mysql_fetch_array($result_check);
				// rounding with units needs to be accounted for
				if ( $qty_in != round(QuantityConvert($row_check[count],'grams',$units),2) ) {
					$error_found = true;
					$error_message .= "Inventory Quantity in lot does not match with prev. Inventory amount $inv_qty , $row_check[count]<BR>";
				} 
				if ( $row_check[count] >= $amt ) {
					$qty_out = $amt;
				} else {
					$qty_out = $row_check[count];
					
				}
				$amt -= $qty_out ;	
				}
//	echo "<br /> qty_out=". $qty_out."<br />qty needed = ". $amt ."<br />"; 
	if ( !$error_found and $qty_out > 0 ) {

		$sql = "SELECT `ProductNumberExternal` , `ProductNumberInternal`
				FROM `batchsheetmaster`
				WHERE `BatchSheetNumber` = $bsn";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$pne = mysql_result($result,0,0);
		
		$sql = "SELECT DISTINCT bsci.CustomerPONumber, customers.name
				FROM batchsheetcustomerinfo AS bsci
					LEFT JOIN customerorderdetail AS c ON c.CustomerOrderNumber = bsci.CustomerOrderNumber AND 
						c.CustomerOrderSeqNumber = bsci.CustomerOrderSeqNumber AND bsci.BatchSheetNumber = $bsn
					LEFT JOIN customerordermaster ON c.CustomerOrderNumber = customerordermaster.OrderNumber
					LEFT JOIN customers ON customers.customer_id = customerordermaster.CustomerID
				WHERE bsci.BatchSheetNumber = $bsn";

		$result_cust = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$remarks = "";
		while ( $row_cust = mysql_fetch_array($result_cust) ) {
			$remarks .= "; $row_cust[name] PO $row_cust[CustomerPONumber] - $pne";
		}
		$remarks = substr($remarks,2);
		
		$sql = sprintf( "INSERT INTO inventorymovements 
			(LotID, ProductNumberInternal, Quantity, 
			TransactionType, MovementStatus, TransactionDate, Remarks) 
			VALUES (%s, %s, %s, 8, 'C', '%s', '%s')", 
				escape_data($lot), escape_data($pni), 
				escape_data($qty_out), date("Y-m-d H:i:s"), mysql_real_escape_string($remarks));
			start_transaction($link);
									
			if ( ! mysql_query($sql, $link) ) {
				echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
				end_transaction(0,$link);
				die;
			}
			$trans_num = mysql_insert_id();
//echo $sql . "<BR>";
			$sql = "INSERT INTO batchsheetdetaillotnumbers (BatchSheetNumber, IngredientProductNumber, IngredientSeq, LotID, InventoryMovementTransactionNumber, QuantityUsedFromThisLot) 
			       VALUES (" . escape_data($bsn) . ", " . escape_data($pni) . ", '" . escape_data($pni_seq) . "', " . escape_data($lot) . ", " . escape_data($trans_num) . ", " . escape_data($qty_out) . ")";
									
			if ( ! mysql_query($sql, $link) ) {
				echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
				end_transaction(0,$link);
				die;
			}
									
			end_transaction(1,$link);
//echo $sql . "<BR>";

	} // !error_found
	} // if row
}   //if c>0
 //echo $amt ."<br />";
	if ( number_format(QuantityConvert($amt,'grams',$UnitOfMeasure),2) == "0.00" ) {
		$_SESSION['note'] .= "Lots successfully saved<BR>";
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "parent.location.href='pop_select_lots_for_batch_sheet_new.php?bsn=".$bsn."&pni=". $pni ."';\n";
		echo "window.close();\n";
		echo "</SCRIPT>\n";
		exit();
	}
} //if post != ''

 ?>

<?php
	$sql = 
		"SELECT lots.LotNumber, lots.LotSequenceNumber, lots.ID, lots.DateManufactured,lots.qualitycontroldate, ROUND( QuantityConvert(vwinventory.InventoryCount,'grams','$UnitOfMeasure'),2) AS InventoryCount, 
		vendors.name, tsdd.Location_On_Site, lots.StorageLocation 
		FROM vwinventory, lots, vendors, tblsystemdefaultsdetail AS tsdd 
		WHERE lots.ID = vwinventory.LotID AND vendors.vendor_id = lots.VendorId AND 
		tsdd.ItemDescription = lots.StorageLocation AND
		LotNumber IS NOT NULL AND vwinventory.ProductNumberInternal = '$pni'
		AND vendors.vendor_id = $vendor_id
		AND ROUND(InventoryCount,2) > 0 ORDER BY lots.DateManufactured,lots.qualitycontroldate,tsdd.Sequence ASC, InventoryCount ASC";
	$result_lots = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
// echo  "<h3>$sql</h3>";
	if ( mysql_num_rows($result_lots) <= 0 or $amt < 0.00001) {
	
		$_SESSION['note'] .= "Did not find vendor lots for $pni with vendorid= $vendor_id<BR> Or qty needed is $amt <br />";
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "parent.location.href='pop_select_lots_for_batch_sheet_new.php?bsn=".$bsn."&pni=".$pni . "';\n";
		echo "window.close();\n";
		echo "</SCRIPT>\n";
		exit();
	
	} else {
		$c = 0;
	?>

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
			<TR>
				<TD STYLE="font-size: 11pt"><B CLASS="black">Vendor:</B><?php echo $vendor_name;?></TD>
				<TD STYLE="font-size: 11pt">[Internal#<?php echo $pni;?>]<input type="hidden" id="units_<?php echo $pni;?>" name="units_<?php echo $pni;?>" value="<?php echo $UnitOfMeasure;?>" /></TD>
				<TD STYLE="font-size: 11pt"><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
				<TD STYLE="font-size: 11pt"><B CLASS="black">Qty Needed:</B></TD>
				<TD STYLE="font-size: 11pt"><?php $quantity_needed = QuantityConvert($amt,'grams',$UnitOfMeasure); echo number_format($quantity_needed, 2)." $UnitOfMeasure"; ?></TD>
			</TR>
		</TABLE>

			<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
			<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
			<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">


				<TR VALIGN=TOP>
					<TD>&nbsp;</TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD STYLE="font-size: 11pt"><B>Lot#</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT STYLE="font-size: 11pt"><B>Lot Seq#</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT STYLE="font-size: 11pt"><B>Location</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT STYLE="font-size: 11pt"><B>On Site</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT STYLE="font-size: 11pt"><B>Inventory Count</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT STYLE="font-size: 11pt"><B>MFR Date</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT STYLE="font-size: 11pt"><B>QC Date</B></TD>
					
					<TD>&nbsp;</TD>
				</TR>

				<?php while ( $row_lots = mysql_fetch_array($result_lots) ) {

					if ( $bg == 1 ) {
						$bgcolor = "#F3E7FD";
						$bg = 0;
					} 
					else {
						$bgcolor = "whitesmoke";
						$bg = 1;
					}
					$c++;
					if ( isset($_POST["qty_".$pni."_".$c]) ) 
					{
						$subQuantity = prep_number($_POST["qty_".$pni."_".$c]);
					}
					else
					{
						if ($quantity_needed > $row_lots[InventoryCount]) 
						{
							$subQuantity = $row_lots[InventoryCount];
							$quantity_needed-=$row_lots[InventoryCount];
						}
						else
						{
							$subQuantity = $quantity_needed;
							$quantity_needed=0;
						}
					}
					$button_href ="assign_bs_lot.php?lot_id=" . 
					$row_lots[ID] ."&pni=". $pni ."&pni_seq=". $pni_seq ."&bsn=" . $bsn .
					"&UnitOfMeasure=". $UnitOfMeasure ."&vendor_id=" . $vendor_id .
					"&qty=" . $amt . "&inv_qty=" .$row_lots[InventoryCount];
					//echo "<br />" . $button_href ."<br />";
					?>

					<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<FORM action="assign_bs_lot.php" method="post">
						<TD STYLE="font-size: 11pt"><INPUT type="button" STYLE="font-size: 8pt" value="Select" 
						onClick="document.location.href='<?php echo $button_href;?>'"></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD STYLE="font-size: 10pt"><?php echo $row_lots[LotNumber];?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD ALIGN=RIGHT STYLE="font-size: 10pt"><?php echo $row_lots[LotSequenceNumber];?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD ALIGN=MIDDLE STYLE="font-size: 10pt"><?php echo $row_lots[StorageLocation];?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD ALIGN=MIDDLE STYLE="font-size: 10pt"><?php echo (1==$row_lots[Location_On_Site] ? 'Y' :'N');?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD ALIGN=RIGHT STYLE="font-size: 10pt"><?php echo number_format($row_lots[InventoryCount],2);?>
						<IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="1">
						<INPUT TYPE="hidden" NAME="lot_id" VALUE="<?php echo $row_lots[ID];?>" SIZE="8" STYLE="text-align:right">
						<INPUT TYPE="hidden" name="pni" value="<?php echo $pni;?>">
						<INPUT TYPE="hidden" name="bsn" value="<?php echo $bsn;?>">
						<INPUT TYPE="hidden" name="UnitOfMeasure" value="<?php echo $UnitOfMeasure;?>">
						<INPUT TYPE="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>">
						<INPUT TYPE="hidden" name="qty" value="<?php echo $amt;?>">
						<INPUT TYPE="hidden" name="inv_qty" value="<?php echo $row_lots[InventoryCount];?>">
						<?php echo $UnitOfMeasure; ?>
						</TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD ALIGN=RIGHT STYLE="font-size: 10pt"><?php echo $row_lots[DateManufactured];?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD ALIGN=RIGHT STYLE="font-size: 10pt"><?php echo $row_lots[qualitycontroldate];?></TD>
					<FORM>
					</TR>
				<?php } ?>

			</TABLE>

			</TD></TR></TABLE>
			</TD></TR></TABLE>
			</TD></TR></TABLE><BR>
			

	<?php } 
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