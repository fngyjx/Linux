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

$div_tag_i = 0;
function prep_number($in) { return str_replace(',','',str_replace(' ','',$in)); }
$bsn = isset($_REQUEST['bsn']) ? $_REQUEST['bsn'] : "";
$pni = isset($_REQUEST['pni']) ? $_REQUEST['pni'] : "";
$amt = isset($_REQUEST['amt']) ? $_REQUEST['amt'] : "";
$seq = isset($_REQUEST['seq']) ? $_REQUEST['seq'] : "";
$order_num = isset($_REQUEST['order_num']) ? $_REQUEST['order_num'] : "";

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$update_prod = isset($_REQUEST['update_prod']) ? $_REQUEST['update_prod'] : "";
$lot_number = isset($_REQUEST['lot_number']) ? escape_data(trim($_REQUEST['lot_number'])) : "";
$lot_sequence_number = isset($_REQUEST['lot_sequence_number']) ? escape_data(trim($_REQUEST['lot_sequence_number'])) : "1";

$sql = "SELECT `Intermediary`, `FinalProductNotCreatedByAbelei` FROM `productmaster` WHERE `ProductNumberInternal` = 
(SELECT ProductNumberInternal FROM batchsheetmaster WHERE BatchSheetNumber = '$bsn')";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
$intermediary = ( 0 == mysql_result($result,0,0) ) ?  false : true;
$FinalProductNotCreatedByAbelei = ( 0 == mysql_result($result,0,1) ) ?  false : true;

$sql = "SELECT count(*) from batchsheetdetail where subbatchsheetnumber='" .$bsn."'";
$result = mysql_query($sql,$link) or die (mysql_error() . " Failed Execute SQL : $sql <br />");
$key_batchsheet = ( 0 == mysql_result($result,0,0) ) ?  false : true;
$error_found = false;
$error_message = "";

if ( !empty($_POST) and $action == "save" ) {
    // final check - ingredient lot assignment check

	$sql = "SELECT batchsheetdetail.*, productmaster.Designation, inventorymovements.Quantity, batchsheetmaster.TotalQuantityUnitType
	FROM batchsheetdetail
	LEFT JOIN inventorymovements ON batchsheetdetail.InventoryTransactionNumber = inventorymovements.TransactionNumber
	LEFT JOIN productmaster ON batchsheetdetail.IngredientProductNumber = productmaster.ProductNumberInternal
	LEFT JOIN batchsheetmaster USING (batchsheetnumber)
	WHERE batchsheetdetail.BatchSheetNumber = $bsn 
	ORDER BY IngredientSEQ";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	if ( $c > 0 ) {
		$total = 0;
		$i = 0;
		while ( $row = mysql_fetch_array($result) ) {
			if ( '10829' != substr($row[IngredientProductNumber],0,5) and 4 != substr($row[IngredientProductNumber], 0, 1) and 0 < $row[Quantity] ) {
				$bg = 0;
				$i++;
				$c = 0;
				$sql_lots = "SELECT * FROM batchsheetdetaillotnumbers 
					WHERE BatchSheetNumber = $bsn AND
					IngredientProductNumber = $row[IngredientProductNumber] AND
					IngredientSEQ = $row[IngredientSEQ]";
				//echo "<br />". $sql_lots ."<br /> need quantity=" . $row['Quantity'];
				$lots_result=mysql_query($sql_lots,$link) or die (mysql_error() ." Failed execute SQL $sql_lots <br />");
				$qty_done = 0;
				while ( $row_lots = mysql_fetch_array($lots_result) ) {
					$qty_done += $row_lots['QuantityUsedFromThisLot'];
				}
				
				$round_value = 1;
				if ( $row['TotalQuantityUnitType'] == "lbs" )
					$round_value=425;
				elseif ( $row['TotalQuantityUnitType'] == "kg" )
					$round_value=1000;
				
				if ( abs($row['Quantity'] - $qty_done)/$round_value >= 0.01 ) {
					$error_found = true;
					$error_message="Quantity in assigned lots is not match the one needed in batchsheet, please give it a check:<br />
					needed = $row[Quantity] , assigned $qty_done for Ingredient $row[IngredientProductNumber] <br />";
				}
			}
		}
	}
	//check packaging
	$sql = "SELECT * FROM batchsheetcustomerinfo where BatchSheetNumber = $bsn";
	$result=mysql_query($sql,$link) or die ( mysql_error() ." Failed Execute SQL $sql <br />");
//	echo "<br />" . $sql ."<br />";
	while ( $row = mysql_fetch_array($result) ) {
		if ( $row['PackIn'] != "" ) {
			$sql_lots = "SELECT * FROM batchsheetdetailpackaginglotnumbers 
			where BatchSheetNumber = '$bsn' AND PackagingProductNumber = '$row[PackIn]'
			AND CustomerPONumber = '$row[CustomerPONumber]'";
		//	echo "<br />" .$sql_lots ."<br /> needed amount=" . $row['NumberOfPackages'];
			$result_lots = mysql_query($sql_lots,$link) or die ( mysql_error() . " Failed execute SQL $sql_lots <br />");
			$qty_done = 0;
		  if ( mysql_num_rows($result_lots) > 0) {	//allow 0 package
			while ( $row_lots = mysql_fetch_array($result_lots) ){
				$qty_done += $row_lots['QuantityUsedFromThisLot'];
			//	echo "qty_done " .$qty_done ."<br />";
			}
			if ( abs($qty_done - $row['NumberOfPackages']) > 0.001 ) {
				$error_found = true;
				$error_message="The Packaging count in assigned lot is not match with the amount in batchsheet<br /> $qty_done , needed $row[NumberOfPackages] <br />";
			}
		  }
		} elseif ( $row['PackInID'] != "" ) {
			$sql_pkin = "SELECT * FROM bscustomerinfopackins WHERE PackInID in (". $row[PackInID] . ")";
			$result_pkin = mysql_query($sql_pkin,$link) or die (mysql_erro() . " Failed to EXECUTE SQL $sql_pkin <br />");
			//echo "<br />". $sql_pkin ."<br />";
			while ( $row_pkin = mysql_fetch_array($result_pkin) ) {

				$sql_lots = "SELECT * FROM batchsheetdetailpackaginglotnumbers 
				where BatchSheetNumber = '$bsn' AND PackagingProductNumber = '$row_pkin[PackIn]'";
			//	echo "<br />" .$sql_lots ."<br />";
				$result_lots = mysql_query($sql_lots,$link) or die ( mysql_error() . " Failed execute SQL $sql_lots <br />");
				$qty_done = 0;
   			  if ( mysql_num_rows($result_lot) > 0 ) {	
				while ( $row_lots = mysql_fetch_array($result_lots) ){
					$qty_done += $row_lots['QuantityUsedFromThisLot'];
				}
				if ( abs($qty_done - ( $row_pkin['NumberOfPackages'] + 0 )) > 0.001 ) {
					$error_found = true;
					$error_message= $row_pkin['PackIn'] . " The Packaging count in assigned lot is not match with the amount in batchsheet<br /> $qty_done , needed $row[NumberOfPackages] <br />";
				}
			  }
			}
		 }
	}
 	// Check Lot number and seq to make sure they're unique
	if (""==$lot_sequence_number || ""==$lot_number) { 
		$error_found=true; $error_message .= "Lot number and lot sequence number must both be set";
	} else 
	// Check Lot number and seq to make sure they're in bounds
	if (30<strlen($lot_number)) { 
		$error_found=true; $error_message .= "Lot number must be less than 12 characters.";
	} else 
	if (!is_numeric($lot_sequence_number)) { 
		$error_found=true; $error_message .= "Lot sequence number must be numeric.";
	} else 
	{
		$sql = "SELECT ID FROM lots WHERE LotNumber = '$lot_number' AND LotSequenceNumber = '$lot_sequence_number'";
		$lotresult = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($lotresult);
		if (0<$c) {
			$error_found=true; $error_message .= "Lot number $lot_number $lot_sequence_number already exists. Please update these values.";
		}
	}

	if ( !$error_found ) {
		$run_deletes = true;
		if ( !$error_found and $run_deletes ) {

				$sql = "DELETE im.* FROM inventorymovements AS im, batchsheetdetail AS bsd WHERE im.TransactionNumber = bsd.InventoryTransactionNumber AND bsd.BatchSheetNumber = " . $bsn;
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				echo "<p>$sql</p>";
						
				// $sql = "UPDATE batchsheetdetail SET InventoryTransactionNumber = NULL WHERE BatchSheetNumber = " . $bsn;
				// mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
						
				$sql = "DELETE im.* FROM inventorymovements AS im, batchsheetcustomerinfo AS bsci WHERE im.TransactionNumber = bsci.InventoryTransactionNumber AND bsci.BatchSheetNumber = " . $bsn;
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				echo "<p>$sql</p>";
				
				$sql = "SELECT PackInID from batchsheetcustomerinfo where PackInID is not null AND
				PackInID <> '' AND BatchSheetNumber = $bsn";
				echo "<br />". $sql ."<br />";
				$result=mysql_query($sql,$link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$row = mysql_fetch_array($result) ;
				if ( $row[0] != "" ) { //has pckinids
					$sql = "DELETE im.* FROM inventorymovements AS im, bscustomerinfopackins AS bscipkin WHERE im.TransactionNumber = bscipkin.InventoryMovementTransactionNumber AND bscipkin.PackInID in (" . $row[0] .")";
					mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				//	echo "<p>$sql</p>";
				}
				// $sql = "UPDATE batchsheetcustomerinfo SET InventoryTransactionNumber = NULL WHERE BatchSheetNumber = " . $bsn;
				// mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
//update to insert batchsheetmaster lot info						
				$sql = "SELECT c.`name`, bsci.`CustomerOrderNumber`, bsci.`CustomerOrderSeqNumber`, bsci.`CustomerPONumber` FROM batchsheetcustomerinfo as bsci, customers AS c, customerordermaster AS com WHERE bsci.`CustomerOrderNumber`=com.`OrderNumber` AND com.`CustomerID`=c.`customer_id` AND bsci.`BatchSheetNumber`=$bsn";
				$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				while ($row=mysql_fetch_array($result)) {
					$remark .= " - Cust. ".mysql_real_escape_string($row[name])."; PO: $row[CustomerOrderNumber]; Cust. PO: $row[CustomerPONumber]";
				}
			
				$sql = "UPDATE inventorymovements AS im, batchsheetmaster AS bsm SET im.MovementStatus = 'C', im.Remarks='Batch Sheet #$bsn for".mysql_real_escape_string($remark)."', im.LotID=bsm.LotID WHERE im.TransactionNumber=bsm.InventoryMovementTransactionNumber AND bsm.BatchSheetNumber =$bsn";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				echo "<p>$sql</p>";
				
			
				$sql = "UPDATE lots, batchsheetmaster AS bsm SET LotNumber='$lot_number', LotSequenceNumber='$lot_sequence_number', lots.InventoryMovementTransactionNumber = bsm.InventoryMovementTransactionNumber, StorageLocation='Warehouse', lots.VendorID=2382 WHERE lots.ID=bsm.LotID AND bsm.BatchSheetNumber =$bsn";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			//	echo "<p>$sql</p>";

				$sql = "UPDATE batchsheetmaster SET Manufactured = 1 WHERE BatchSheetNumber = " . $bsn;
				//, abeleiLotNumber=$formulaLotNumber
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			//	echo "<p>$sql</p>";

			}

		$_SESSION['note'] .= "Lots successfully saved<BR>";
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "window.opener.location.reload();\n";
		echo "window.close()\n";
		echo "</SCRIPT>\n";
		exit();
	
	} else {
		echo "<br />" . $error_message;
	}

}

if ( !empty($_POST) and $action == "cancel" ) {

	start_transaction($link);
	$sql = "DELETE im.* FROM inventorymovements AS im, batchsheetdetaillotnumbers AS bsdlt
	WHERE im.TransactionNumber = bsdlt.InventoryMovementTransactionNumber AND bsdlt.BatchSheetNumber = " . $bsn;
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL: $sql <br /> ";
		end_transaction(0,$link);
		die;
	}
	
	$sql = "DELETE FROM batchsheetdetaillotnumbers where BatchSheetNumber = " . $bsn;
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed execute SQL : $sql <br />";
		end_transaction(0,$link);
		die;
	}
	
		$sql = "DELETE im.* FROM inventorymovements AS im, batchsheetdetailpackaginglotnumbers AS bsdplt
	WHERE im.TransactionNumber = bsdplt.InventoryMovementTransactionNumber AND bsdplt.BatchSheetNumber = " . $bsn;
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed Execute SQL: $sql <br /> ";
		end_transaction(0,$link);
		die;
	}
	
	$sql = "DELETE FROM batchsheetdetailpackaginglotnumbers where BatchSheetNumber = " . $bsn;
	if ( ! mysql_query($sql,$link) ) {
		echo mysql_error() . " Failed execute SQL : $sql <br />";
		end_transaction(0,$link);
		die;
	}
	
	end_transaction(1,$link);
	
	$_SESSION['note'] .= "Lots assignment was cancelled<BR>";
	
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload();\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
	exit();
}

include("inc_pop_header.php"); ?>

<B>Assign Lot Numbers</B><BR><BR>

<?php

$all_assigned=true;
$sql = "SELECT bsd.*, pm.Designation, im.Quantity, pm.UnitOfMeasure, bsm.TotalQuantityUnitType
	FROM batchsheetdetail AS bsd, inventorymovements AS im, productmaster AS pm, 
	batchsheetmaster as bsm
	WHERE bsd.InventoryTransactionNumber = im.TransactionNumber AND 
	bsd.IngredientProductNumber = pm.ProductNumberInternal AND
	bsm.BatchSheetNumber = bsd.BatchSheetNumber AND 
	bsd.BatchSheetNumber = $bsn 
	ORDER BY IngredientSEQ";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
 //echo "$c rows result - $sql";

if ( $c > 0 ) {
	$total = 0;
	$i = 0;
	$bg = 0;
?>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" width="100%">
	<TR><TD colspan="4"><B CLASS="black">Ingredient:</B></TD></TR>
	<?php
	while ( $row = mysql_fetch_array($result) ) {
	// ignore water, instructions, and ingredients whose quantity is less than 0
		if ( '10829' == substr($row['IngredientProductNumber'],0,5) or '4' == substr($row['IngredientProductNumber'], 0, 1))
			continue;
		if ( $bg == 0 ) {
			$bgcolor="";
			$bg=1;
		} else {
			$bgcolor="whitesmoke";
			$bg=0;
		}
		$i++;
		// get assigned lots for the ingredient
		$sql = "SELECT bsdlt.*, lots.LotNumber,lots.LotSequenceNumber,vendors.name 
			FROM batchsheetdetaillotnumbers as bsdlt, lots,vendors
			WHERE bsdlt.BatchSheetNumber = $bsn AND
				lots.ID = bsdlt.LotID AND
				vendors.vendor_id = lots.VendorID
				AND IngredientProductNumber = $row[IngredientProductNumber]
				AND IngredientSEQ = $row[IngredientSEQ]";
		//echo "<br /> $i ". $sql ."<br />";
		$qty_done= 0;
		$lot_assigned = "";
		$result_done=mysql_query($sql,$link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		while ( $row_done = mysql_fetch_array($result_done) ) {
				$qty_done += $row_done['QuantityUsedFromThisLot'];
				$lot_assigned .= "&nbsp;&nbsp;&nbsp;&nbsp;<NOBR>Vendor:". $row_done['name'] .";&nbsp;Lot#:". $row_done['LotNumber'] .";&nbsp;Amount:". number_format(QuantityConvert($row_done['QuantityUsedFromThisLot'],'grams',$row['UnitOfMeasure']),2) ."</NOBR><br />";
				//echo $lot_assigned;
		}
		$quantity_needed_grams=$row['Quantity'] - $qty_done;
		$quantity_needed = QuantityConvert($quantity_needed_grams,'grams',$row['UnitOfMeasure']);
		if ( $quantity_needed < 0.01 and $qty_done == 0 ) {
			$quantity_needed = $quantity_needed_grams;
			$units = 'grams';
		} else {
			$units = $row['UnitOfMeasure'];
		}
	//	echo "quantity needed = $quantity_needed <br /> $quantity_needed_grams <br />";
		if ( $quantity_needed >= 0.01 ) {		
			$all_assigned=false;

			$sql = "SELECT vendors.vendor_id, vwinventory.Productnumberinternal, count(LotID) AS LotCnt,
			sum(ROUND( QuantityConvert(vwinventory.InventoryCount,'grams','$units'),2)) AS InventoryCount,
			vendors.name FROM vwinventory join lots on lots.ID=vwinventory.LotID
			JOIN vendors on lots.VendorID=vendors.vendor_id 
			WHERE productnumberinternal='$row[IngredientProductNumber]'
			AND InventoryCount > 0.001
			AND vendors.active=1 GROUP BY vendors.name ORDER BY vendors.name";
			$result_vendor = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//	echo "<br />$sql<br />";
		    ?>
			
				<TR bgcolor="<?php echo $bgcolor;?>">
					<TD><b><?php echo $row['IngredientProductNumber'];?></b> - [<?php echo $row['Designation'];?>]</TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
					<TD><b>Qty Needed:</b></TD>
					<TD><?php echo number_format($quantity_needed, 2)." ". $units; ?></TD>
				</TR>
			
			<?php
			if ( mysql_num_rows($result_vendor) > 0 ) {
			$c = 0;
			

				while( $row_vendor = mysql_fetch_array($result_vendor) ) { 
				  if( $bg == 0 ) {
					$bgcolor = "";
					$bg = 1;
				  } else {
					$bgcolor = "whitesmoke";
					$bg = 0;
				  }
				
			?>
					<TR bgcolor="<?php echo $bgcolor;?>"><TD colspan='4' align='left'><NOBR>&nbsp;&nbsp;&nbsp;&nbsp;
					<A HREF="assign_bs_lot.php?pni=<?php echo $row['IngredientProductNumber'];?>&pni_seq=<?php echo $row['IngredientSEQ'];?>&bsn=<?php echo $bsn; ?>&vendor_id=<?php echo $row_vendor['vendor_id']; ?>&UnitOfMeasure=<?php echo $units;?>&qty=<?php echo $quantity_needed_grams;?>&vendor_name=<?php echo $row_vendor['name'];?>"
					onMouseover="document.ckbut<?php echo $i;?>.src='images/select.png'" onClick="SetIfram('ifrm_<?php echo $div_tag_i;?>'); document.ckbut<?php echo $i;?>.src='images/bulletCheck.png'"
					 target="infrm_assign_lot"><IMG SRC="images/select.png" NAME="ckbut<?php echo $i;?>" WIDTH="12" HEIGHT="12" BORDER="0" ALT="..."></A>
					<?php echo $row_vendor['name'] . ", Inv. Amnt.: " .
					$row_vendor['InventoryCount'] . " <small>".$units."</small> in ". 
					$row_vendor['LotCnt']." Lots<br />" ;
					$i++;
					echo "</NOBR></td></tr>";
				}
				
			?>
			<?php 
				echo "<TR><TD colspan='4'>&nbsp;&nbsp;&nbsp;&nbsp;<div name='ifrm_".$div_tag_i ."' id='ifrm_". $div_tag_i ."'></div>";
				echo "</TD></TR>";
				$div_tag_i++;
				
			} else {
				echo "<TR><TD colspan='4' align='left'>&nbsp;&nbsp;&nbsp;&nbsp;Cannot find Inventory</TD></TR>";
				
			}
		} else if ( $qty_done > 0 ) { // proven the needed = 0
		
		
		?>
						<!--<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3"> -->
			<TR>
				<TD><?php echo $row[IngredientProductNumber];?> - [<?php echo $row[Designation];?>]</TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
				<TD>Qty Needed:</TD>
				<TD><?php $quantity_needed = QuantityConvert($row[Quantity],'grams',$units); echo number_format($quantity_needed, 2)." $units"; ?></TD>
			</TR>
		<?php
			 echo "<TR><TD colspan='3' align='left'>". $lot_assigned ."&nbsp;&nbsp;&nbsp;&nbsp;Total Assigned Amount:" . number_format(QuantityConvert($qty_done,'grams',$units),2). $units. "</TD>";
		?>
			<TD><input type="button" class="submit" value="Re Assign Lot" onClick="window.location='edit_bs_lot.php?bsn=<?php echo $bsn;?>&pni=<?php
			echo $row['IngredientProductNumber'];?>&pni_seq=<?php echo $row['IngredientSEQ'];?>'"></TD></TR>
				
<?php		}
	}
	echo "</TABLE>";
}
//now get packagins
	
$sql = "SELECT * FROM batchsheetcustomerinfo WHERE BatchSheetNumber = ".$bsn;
	//echo "<br />".$sql."<br />";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$c = mysql_num_rows($result);
$bg = 0; 
if ( $c > 0 ) {
	$found_packs = true;
	$total = 0;
	$i = 0;
	$bg = 0;
?>
	<br />
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
	<TR><TD><B CLASS="black">P.O.: </b></td>
	<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
	<td colspan='4'><B>Packaging:</B></td></TR>
	<?php
	while ( $row = mysql_fetch_array($result) ) { 
		$i++;
		if ( $bg == 0 ) {
			$bgcolor="";
			$bg=1;
		} else {
			$bgcolor = "whitesmoke";
			$bg = 0;
		}
		if ( $row['PackIn'] != "" ) {
		
			$sql = "SELECT bspklt.*, vendors.name,lots.LotNumber,lots.LotSequenceNumber
				FROM batchsheetdetailpackaginglotnumbers as bspklt, lots,vendors 
				WHERE BatchSheetNumber = '$bsn' AND
				lots.ID=bspklt.LotID AND
				vendors.vendor_id=lots.VendorID
				AND PackagingProductNumber = '$row[PackIn]'
				AND CustomerPONumber = '$row[CustomerPONumber]'";
			//	echo "<br />". $sql ."<br />";
			$qty_done= 0;
			$lot_assigned="";
			$result_done=mysql_query($sql,$link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			while ( $row_done = mysql_fetch_array($result_done) ) {
				$qty_done += $row_done['QuantityUsedFromThisLot'];
				$lot_assigned .= "&nbsp;&nbsp;&nbsp;&nbsp;<NOBR>Vendor:". $row_done['name'] .";&nbsp;Lot#:". $row_done['LotNumber'] .";&nbsp;Amount:". number_format($row_done['QuantityUsedFromThisLot'],0) ."</NOBR><br />";
			}
		
			$quantity_needed = $row[NumberOfPackages] - $qty_done;
	
			if ( $quantity_needed > 0.01 ) {
				$sql_pndscrp = "SELECT Designation FROM productmaster where ProductNumberInternal = '". $row['PackIn'] ."'";
				$result_dscrp = mysql_query($sql_pndscrp,$link) or die (mysql_error() ." Failed execute SQL $sql_pndscrp <br />");
				$row_dscrp = mysql_fetch_array($result_dscrp);
				$sql = "SELECT vendors.vendor_id,
					vendors.name, count(lots.LotNumber) AS LotCnt, 
					sum(ROUND(vwinventory.InventoryCount,2)) AS InventoryCount 
				FROM vwinventory
					LEFT JOIN lots ON vwinventory.LotID = lots.ID
					LEFT JOIN receipts ON lots.ID = receipts.LotID
					LEFT JOIN purchaseorderdetail ON purchaseorderdetail.ID = receipts.PurchaseOrderID  
					LEFT JOIN purchaseordermaster ON purchaseordermaster.PurchaseOrderNumber = purchaseorderdetail.PurchaseOrderNumber 
					LEFT JOIN vendors ON purchaseordermaster.VendorId = vendors.vendor_id
				WHERE 
					LotNumber IS NOT NULL 
					AND vwinventory.ProductNumberInternal = '$row[PackIn]'  
					AND ROUND(InventoryCount,2) > 0.0001
					GROUP BY name
					ORDER BY Name";
				//	echo "<br />". $sql . "<br />";
					$result_lots = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
						// echo "<h3>$sql</h3>";

				?>
					<TR bgcolor="<?php echo $bgcolor;?>">
						<TD><b><?php echo $row[CustomerPONumber];?></b></td>
						<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
						<TD><?php echo "$row[PackIn] - [$row_dscrp[0]]"; ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
						<TD><b>Qty Needed:</b></TD>
						<TD><?php echo number_format($quantity_needed, 2);?></TD>
					</TR>
					
					<?php
				if ( mysql_num_rows($result_lots) > 0 ) {
						$c = 0;
						$all_assigned=false;
					?>
					
					<?php 
					
					while( $row_lots = mysql_fetch_array($result_lots) ) { 
						if ( $bg == 0 ) {
							$bgcolor="";
							$bg=1;
						} else {
							$bgcolor = "whitesmoke";
							$bg = 0;
						}
					?>
						<TR bgcolor="<?php echo $bgcolor;?>"><TD colspan='6'>&nbsp;&nbsp;&nbsp;&nbsp;
						<A HREF="assign_bsci_lot.php?PackIn=<?php echo $row['PackIn'];?>&bsn=<?php echo $bsn;?>&vendor_id=<?php echo $row_pkin['vendor_id'];?>&qty=<?php echo $quantity_needed;?>&CustomerPONumber=<?php echo $row[CustomerPONumber];?>&CustomerOrderNumber=<?php echo $row[CustomerOrderNumber];?>&CustomerOrderSeqNumber=<?php echo $row[CustomerOrderSeqNumber];?>"
						onMouseover="document.pockbut<?php echo $i;?>.src='images/select.png'" onClick="SetIfram('ifrm_<?php echo $div_tag_i;?>'); document.pockbut<?php echo $i?>.src='images/bulletCheck.png'"
						target="infrm_assign_lot"><IMG SRC="images/select.png" NAME="pockbut<?php echo $i;?>" WIDTH="12" HEIGHT="12" BORDER="0" ALT="..."></A>
					<?php
					echo $row_lots['name']. ", Inv Amnt " .	$row_lots['InventoryCount'] . " in " . $row_lots['LotCnt'] ." Lots<br />";
					  $i++;
					  echo "</TD></TR>";
					}
					echo "<TR><TD colspan='6'>&nbsp;&nbsp;&nbsp;&nbsp;<div name='ifrm_".$div_tag_i ."' id='ifrm_". $div_tag_i ."'></div>";
					echo "</TD></TR>";
					$div_tag_i++;
				} else {
					echo "<TR><TD colspan='6'>&nbsp;&nbsp;&nbsp;&nbspCannot find Inventory</TD></TR>";
				}
			} else {
				?>
					<TR>
						<TD><?php echo $row[CustomerPONumber];?></td>
						<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
						<TD><?php echo "$row[PackIn] - [$row_dscrp[0]]"; ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
						<TD>Qty Needed:</TD>
						<TD><?php echo number_format($row[NumberOfPackages], 2);?></TD>
					</TR>
					<TR><TD colspan='5'><?php echo $lot_assigned; ?><br />Total Assigned Amount:<?php echo $qty_done;?></TD>
					<TD><INPUT TYPE="button" class="submit" value="Re Assign Lot" onClick="window.location='edit_bsci_lot.php?bsn=<?php echo $bsn;?>&pni=<?php echo $pni;?>&packin=<?php
					echo $row['PackIn'];?>&cstordnm=<?php echo $row['CustomerOrderNumber'];?>&cstordsqnm=<?php echo $row['CustomerOrderSeqNumber']?>'"></TD></TR>
					<TR>
				<?php
				}
		} elseif ( $row['PackInID'] != "" ) {
			
			$sql_pkin = "SELECT *, pm.Designation FROM bscustomerinfopackins AS bscipk, productmaster AS pm WHERE pm.ProductNUmberInternal = bscipk.PackIn AND PackInID in (". $row['PackInID'] .")";
			//echo "<br />". $sql_pkin ."<br />";
			$pkin_result = mysql_query($sql_pkin,$link) or (mysql_error()."<br />Couldn't execute query: $sql_pkin<BR><BR>");
			while ( $row_pkin = mysql_fetch_array($pkin_result) ) {
				$sql = "SELECT bspklt.* , vendors.name, lots.LotNumber, lots.LotSequenceNumber
				FROM batchsheetdetailpackaginglotnumbers as bspklt , lots, vendors
				WHERE BatchSheetNumber = $bsn AND
				lots.ID=bspklt.LotID AND
				vendors.vendor_id=lots.VendorID
				AND PackagingProductNumber = $row_pkin[PackIn]";
				//echo "<br />". $sql ."<br />";
				$qty_done= 0;
				$lot_assigned="";
				$result_done=mysql_query($sql,$link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				while ( $row_done = mysql_fetch_array($result_done) ) {
					$qty_done += $row_done['QuantityUsedFromThisLot'];
					$lot_assigned .= "&nbsp;&nbsp;&nbsp;&nbsp;<NOBR>Vendor:". $row_done['name'] .";&nbsp;Lot#:". $row_done['LotNumber'] .";&nbsp;Amount:". number_format($row_done['QuantityUsedFromThisLot']) ."</NOBR><br />";
				}
				$quantity_needed = $row_pkin[NumberOfPackages] - $qty_done;	
				if ( $quantity_needed > 0.01 ) {
					$sql = "SELECT 
					vendors.vendor_id, vendors.name, 
					count(lots.LotNumber) AS LotCnt,  
					sum(ROUND(vwinventory.InventoryCount,2)) AS InventoryCount 
					FROM vwinventory
					LEFT JOIN lots ON vwinventory.LotID = lots.ID
					LEFT JOIN receipts ON lots.ID = receipts.LotID
					LEFT JOIN purchaseorderdetail ON purchaseorderdetail.ID = receipts.PurchaseOrderID  
					LEFT JOIN purchaseordermaster ON purchaseordermaster.PurchaseOrderNumber = purchaseorderdetail.PurchaseOrderNumber 
					LEFT JOIN vendors ON purchaseordermaster.VendorId = vendors.vendor_id
					WHERE 
					LotNumber IS NOT NULL 
					AND vwinventory.ProductNumberInternal = $row_pkin[PackIn]
					AND ROUND(InventoryCount,2) > 0
					GROUP BY name ORDER BY Name";
				//	echo "<br />". $sql . "<br />";
					$result_lots = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
						// echo "<h3>$sql</h3>";
				
						
				?>		
					<TR>
						<TD><b><?php echo $row[CustomerPONumber];?></b></td>
						<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
						<TD><?php echo $row_pkin[PackIn] ." - [".$row_pkin['Designation'] ."]"; ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
						<TD><b>Qty Needed:</b></TD>
						<TD><?php echo number_format($quantity_needed, 2);?></TD>
					</TR>
					
				<?php 
					if ( mysql_num_rows($result_lots) > 0 ) {
						$all_assigned=false;
						$c = 0;
					?>

					<?php 
						while( $row_lots = mysql_fetch_array($result_lots) ) {

						?>
							<TR><TD colspan='6'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<A HREF="assign_bsci_lot.php?PackInID=<?php echo $row_pkin['PackInID'];?>&bsn=<?php echo $bsn;?>&vendor_id=<?php echo $row_lots['vendor_id'];?>&qty=<?php echo $quantity_needed;?>&CustomerPONumber=<?php echo $row[CustomerPONumber];?>&CustomerOrderNumber=<?php echo $row[CustomerOrderNumber];?>&CustomerOrderSeqNumber=<?php echo $row[CustomerOrderSeqNumber];?>"
							onMouseover="document.pockbut<?php echo $i;?>.src='images/bulletUnChecked.png'" onClick="SetIfram('ifrm_<?php echo $div_tag_i;?>'); document.pockbut<?php echo $i;?>.src='images/bulletCheck.png'"
							target="infrm_assign_lot"><IMG SRC="images/bulletUnChecked.png" NAME="pockbut<?php echo $i;?>" WIDTH="12" HEIGHT="12" BORDER="0" ALT="..."></A>
					
						<?php 
							echo $row_lots['name']. ", Inv Amnt " . $row_lots['InventoryCount'] . " in " . $row_lots['LotCnt'] ." Lots<br />";
							$i++;
							echo "</TD></TR>";
						}
						echo "<TR><TD colspan='6'><div name='ifrm_".$div_tag_i ."' id='ifrm_". $div_tag_i ."'></div>";
						echo "</TD></TR>";
						$div_tag_i++;
			    	} //in row_lot >0
					else {
					   echo "<TR><TD colspan='6'>&nbsp;&nbsp;&nbsp;&nbspCannot find Inventory</TD></TR>";
					}
				} else {
				?>
					<TR>
						<TD><?php echo $row[CustomerPONumber];?></td>
						<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
						<TD><?php echo $row_pkin[PackIn] ." - [".$row_pkin['Designation'] ."]"; ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
						<TD>Qty Needed:</TD>
						<TD><?php echo number_format($row_pkin[NumberOfPackages], 2);?></TD>
					</TR>
					<TR><TD colspan='6'><?php echo $lot_assigned; ?><br />&nbsp;&nbsp;&nbsp;&nbsp;Total Assigned Amount:<?php echo $qty_done;?></TD></TR> 				
				
				<?php
				
				
				}
			} //while row_pkin
		} //pkinid
	} //whilebsci
		echo "</TABLE>";
} //if find pkg

?>

<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( !empty($note) ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>
<?php 
if ( ( $found_packs or $intermediary or $FinalProductNotCreatedByAbelei or $key_batchsheet ) and $all_assigned ) { //enter bsm lot# and lotseq# to save the lots assignement ?>

<FORM action="pop_select_lots_for_batch_sheet_new.php" method="post">
<input type="hidden" name="bsn" value="<?php echo $bsn;?>">
<input type="hidden" name="pni" value="<?php echo $pni;?>">
<input type="hidden" name="action" value="save">
<br/>
<h4>Now type in Final Product Lot# and Click on Save to finish lots assignment</h4>
<p><b>Final Product Lot #</b> <input type="text" name="lot_number" value="<?php echo formatTxt(getFormSafe($lot_number)) ?>" /> 
<b>Seq. #</b> <input type="text" name="lot_sequence_number" value="<?php echo formatTxt(getFormSafe($lot_sequence_number)) ?>" /></p>

<BR>

<INPUT TYPE="submit" VALUE="Save" CLASS="button_pop">
</FORM>

<FORM action="pop_select_lots_for_batch_sheet_new.php" method="post">
<input type="hidden" name="bsn" value="<?php echo $bsn;?>">
<input type="hidden" name="pni" value="<?php echo $pni;?>">
<input type="hidden" name="action" value="cancel">
<br/>
<INPUT TYPE="submit" VALUE="Cancel" CLASS="button_pop">
</FORM>

<?php } else { ?>
<FORM action="pop_select_lots_for_batch_sheet_new.php" method="post">
<input type="hidden" name="bsn" value="<?php echo $bsn;?>">
<input type="hidden" name="pni" value="<?php echo $pni;?>">
<input type="hidden" name="action" value="cancel">
<br/>
<INPUT TYPE="submit" VALUE="Cancel" CLASS="button_pop">
</FORM>


<?php } ?>

&nbsp;&nbsp;&nbsp;

<script LANGUAGE=JAVASCRIPT>
 <!-- Hide
 function SetIfram(pni) {

	for (var i=0; i<40; i++) {
		var divElm = document.getElementById('ifrm_'+i);
		if ( divElm ) {
			document.getElementById('ifrm_'+i).innerHTML = "";
		}
	}
	document.getElementById(pni).innerHTML="<IFRAME name='infrm_assign_lot' width='100%' height='300' frameborder='0' scrolling='auto'></IFRAME>";
}
//function delete_lot(RecordID, pni, amt, seq, order_num, mtn) {
//	if ( confirm('Are you sure you want to delete this item?') ) {
//		document.location.href = "pop_select_lots_for_batch_sheet.php?action=delete_lot&RecordID=" + RecordID + "&pni=" + pni + "&amt=" + amt + "&seq=" + seq + "&order_num=" + order_num + "&mtn=" + mtn
//	}
//}

 // End -->
 
</script>

<BR><BR>

<?php include("inc_footer.php"); ?>