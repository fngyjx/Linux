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
$bsn = isset($_REQUEST['bsn']) ? $_REQUEST['bsn'] : "";
$pni = isset($_REQUEST['pni']) ? $_REQUEST['pni'] : "";
$amt = isset($_REQUEST['amt']) ? $_REQUEST['amt'] : "";
$seq = isset($_REQUEST['seq']) ? $_REQUEST['seq'] : "";
$order_num = isset($_REQUEST['order_num']) ? $_REQUEST['order_num'] : "";
$rid = isset($_REQUEST['rid']) ? $_REQUEST['rid'] : "";
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$update_prod = isset($_REQUEST['update_prod']) ? $_REQUEST['update_prod'] : "";
$lot_number = isset($_REQUEST['lot_number']) ? escape_data(trim($_REQUEST['lot_number'])) : "";
$lot_sequence_number = isset($_REQUEST['lot_sequence_number']) ? escape_data(trim($_REQUEST['lot_sequence_number'])) : "1";

$sql = "SELECT `Intermediary`, `FinalProductNotCreatedByAbelei` FROM `productmaster` WHERE `ProductNumberInternal` = '$pni'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
$intermediary = ( 0 == mysql_result($result,0,0) ) ?  false : true;
$FinalProductNotCreatedByAbelei = ( 0 == mysql_result($result,0,1) ) ?  false : true;

$error_found = false;
$error_message = "";

if ( !empty($_POST) and $rid == "" ) {

//echo "<TABLE BORDER=1 CELLPADDING=1 CELLSPACING=0>";
//foreach (array_keys($_POST) as $key) { 
//	$$key = $_POST[$key];
//	print "<TR><TD><B STYLE='color:#666666'>$key</B></TD><TD><B STYLE='color:red'>${$key}</B></TD></TR>"; 
//}
//echo "</TABLE><BR>";
//die();


	$sql = "SELECT batchsheetdetail.*, productmaster.Designation, vendors.name, inventorymovements.Quantity
	FROM batchsheetdetail
	LEFT JOIN inventorymovements ON batchsheetdetail.InventoryTransactionNumber = inventorymovements.TransactionNumber
	LEFT JOIN productmaster ON batchsheetdetail.IngredientProductNumber = productmaster.ProductNumberInternal
	LEFT JOIN vendors ON vendors.vendor_id = batchsheetdetail.VendorID
	WHERE batchsheetdetail.BatchSheetNumber = $bsn 
	ORDER BY IngredientSEQ";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	if ( $c > 0 ) {
		$total = 0;
		$i = 0;
		while ( $row = mysql_fetch_array($result) ) {
			if ( '10829' == substr($row[IngredientProductNumber],0,5) ) // Ignore Water
				continue;
			if ( 4 == substr($row[IngredientProductNumber], 0, 1) ) // Ignore instructions
				continue;
			if ( 0 >= $row[Quantity] ) //Ignore if not enough quantity
				continue;
			$bg = 0;
			$i++;
			$ingredient_amount = 0;
			$c = 0;
			foreach (array_keys($_POST) as $key) {
				$$key = $_POST[$key];
				// print "$key = ${$key}"; 
				if ( substr($key, 4, 6) == $row[IngredientProductNumber] ) {
					$c++;
					$lot = $_POST["lot_$row[IngredientProductNumber]_".floor($row[IngredientSEQ])."_$c"];
					$qty_in = round(prep_number($_POST["qty_$row[IngredientProductNumber]_".floor($row[IngredientSEQ])."_$c"]),2);
					$units = $_POST["units_$row[IngredientProductNumber]"];
					if ( $lot != '' and $qty_in != '' and 0 < $qty_in ) {
						$qty = QuantityConvert($qty_in,$units,'grams');
						$sql = "SELECT SUM(InventoryCount) as count FROM vwinventory WHERE LotID = $lot AND ProductNumberInternal = $row[IngredientProductNumber]";
						$result_check = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
						$row_check = mysql_fetch_array($result_check);
						// rounding with units needs to be accounted for
						if ( $qty_in > round(QuantityConvert($row_check[count],'grams',$units),2) ) {
							$error_found = true;
							$error_message .= "Quantity entered for lot is greater than what's on hand for ingredient $row[IngredientProductNumber].<BR>";
						} 
						$ingredient_amount = $ingredient_amount + $qty;
					}
				}
			}
			if ( $ingredient_amount == 0 or !is_numeric($ingredient_amount) ) {
				$error_found = true;
				$error_message .= "Please enter a numeric value for ingredient $row[IngredientProductNumber] ($ingredient_amount | $qty) - \$_POST[\"qty_$row[IngredientProductNumber]_".floor($row[IngredientSEQ])."_$c\"]).<BR>";
			} elseif ( round(QuantityConvert($ingredient_amount,'grams',$units),2) != round(QuantityConvert($row[Quantity],'grams',$units), 2) ) {
				$error_found = true;
				$error_message .= "The total amount for ingredient $row[IngredientProductNumber] must be " . number_format( QuantityConvert($row[Quantity],'grams',$units), 2) . " $units.<BR>";
			}
		}
		//check the packaging 
		$sql = "SELECT bsci.*, pm.Designation FROM batchsheetcustomerinfo AS bsci, productmaster AS pm WHERE pm.ProductNumberInternal=bsci.PackIn AND BatchSheetNumber = $bsn";
		// echo "<br />". $sql ."<br >";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
		$bg = 0; 
		if ( $c > 0 ) {
			$found_packs = true;
			$total = 0;
			$i = 0;
			while ( $row = mysql_fetch_array($result) ) { 
				$i++;
				$packaging_amount = 0;
				$c=0;
				foreach (array_keys($_POST) as $key) {
					$$key = $_POST[$key];
					if ( substr($key, 4, 6) == $row[PackIn] ) {
						$c++;
						$lot = $_POST["lot_$row[PackIn]_$row[CustomerOrderNumber]_$row[CustomerPONumber]_$c"];
						$qty = prep_number($_POST["qty_$row[PackIn]_$row[CustomerOrderNumber]_$row[CustomerPONumber]_$c"]);
						if ( $lot != '' and $qty != '' and 0 < $qty ) {
							$sql = "SELECT SUM(InventoryCount) as count FROM vwinventory WHERE LotID = $lot AND ProductNumberInternal = $row[PackIn]";
							$result_check = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
							$row_check = mysql_fetch_array($result_check);
							if ( $qty > round($row_check['count'],2) ) {
								$error_found = true;
								$error_message .= "Quantity entered for lot is greater than what's on hand for packaging $row[PackIn] for PO $row[CustomerPONumber]<BR>";
							}
							$packaging_amount = $packaging_amount + $qty;
						}
					}
				}
				if ( $packaging_amount < 0 or !is_numeric($packaging_amount) ) {
					$error_found = true;
					$error_message .= "Please enter a numeric value for packaging $row[PackIn] for PO $row[CustomerPONumber]<BR>";
				} elseif ( round($packaging_amount,2) != round($row[NumberOfPackages], 2) ) {
					$error_found = true;
					$error_message .= "The total amount for packaging $row[PackIn] for PO $row[CustomerPONumber] must be ".number_format($row[NumberOfPackages], 2) . "<BR>";
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
	}

	if ( !$error_found ) {

		$sql = "SELECT `ProductNumberExternal` , `ProductNumberInternal`
				FROM `batchsheetmaster`
				WHERE `BatchSheetNumber` = $bsn";
		$result_cust = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$pne = mysql_result($result_cust,0,0);
		
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
		
		$sql = "SELECT bsd.*, pm.Designation, im.Quantity
		FROM batchsheetdetail AS bsd
			LEFT JOIN inventorymovements AS im ON bsd.InventoryTransactionNumber = im.TransactionNumber
			LEFT JOIN productmaster AS pm ON bsd.IngredientProductNumber = pm.ProductNumberInternal
		WHERE bsd.BatchSheetNumber = $bsn
		ORDER BY bsd.IngredientSEQ";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
// echo $c." - ".$sql . "<BR><h3>";
// print_r($_POST); echo "</h3>";
		if ( $c > 0 ) {
			$total = 0;
			$i = 0;
			$run_deletes = false;
			while ( $row = mysql_fetch_array($result) ) {
				if ( '10829' != sbstr($row[IngredientProductNumber],0,5) and 4 != substr($row[IngredientProductNumber], 0, 1) and 0 < $row[Quantity] ) {
					$bg = 0;
					$i++;
					$c = 0;
					foreach (array_keys($_POST) as $key) { 
						$$key = $_POST[$key];
						if ( substr($key, 4, 6) == $row[IngredientProductNumber] ) {
							$c++;
							$lot = $_POST["lot_$row[IngredientProductNumber]_".floor($row[IngredientSEQ])."_$c"];
							$qty_in = round(prep_number($_POST["qty_$row[IngredientProductNumber]_".floor($row[IngredientSEQ])."_$c"]),2);
							$units = $_POST["units_$row[IngredientProductNumber]"];
							$qty = QuantityConvert($qty_in,$units,'grams');
							if ( '' != $lot and '' != $qty_in and 0 < $qty_in ) {
								$run_deletes = true;
								if ( substr($row[IngredientProductNumber], 0, 1) != 6 ) {   // INGREDIENT

								// If amounts match up in lbs or kilos but not grams, set grams about to equal remainder
								$sql = "SELECT SUM(InventoryCount) as count FROM vwinventory WHERE LotID = $lot AND ProductNumberInternal = $row[IngredientProductNumber]";
								$result_check = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
								$row_check = mysql_fetch_array($result_check);
								if ( $qty_in == round(QuantityConvert($row_check[count],'grams',$units),2) and $qty != $row_check[count] ) {
									$qty=$row_check[count];
								}

//								$sql = "SELECT purchaseorderdetail.PurchaseOrderNumber, VendorProductCode, vendors.name
//										FROM vwinventory
//										LEFT JOIN lots ON vwinventory.LotID = lots.ID
//										LEFT JOIN receipts ON lots.ID = receipts.LotID
//										LEFT JOIN purchaseorderdetail ON purchaseorderdetail.ID = receipts.PurchaseOrderID
//										LEFT JOIN purchaseordermaster ON purchaseordermaster.PurchaseOrderNumber = purchaseorderdetail.PurchaseOrderNumber
//										LEFT JOIN vendors ON purchaseordermaster.VendorId = vendors.vendor_id
//										WHERE lots.ID = $lot";
//										$result_remarks = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
//										$row_remarks = mysql_fetch_array($result_remarks);

									$sql = sprintf( "INSERT INTO inventorymovements 
												(LotID, ProductNumberInternal, Quantity, 
												TransactionType, MovementStatus, TransactionDate, Remarks) 
											VALUES (%s, %s, %s, 8, 'C', '%s', '%s')", 
												escape_data($lot), escape_data($row[IngredientProductNumber]), 
												escape_data($qty), date("Y-m-d H:i:s"), mysql_real_escape_string($remarks));
									start_transaction($link);
									
									if ( ! mysql_query($sql, $link) ) {
										echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
										end_transaction(0,$link);
										die;
									}
									$trans_num = mysql_insert_id();
// echo $sql . "<BR>";

									$sql = "INSERT INTO batchsheetdetaillotnumbers (BatchSheetNumber, IngredientProductNumber, IngredientSeq, LotID, InventoryMovementTransactionNumber, QuantityUsedFromThisLot) VALUES (" . escape_data($bsn) . ", " . escape_data($row[IngredientProductNumber]) . ", '" . escape_data($row[IngredientSEQ]) . "', " . escape_data($lot) . ", " . escape_data($trans_num) . ", " . escape_data($qty) . ")";
									
									if ( ! mysql_query($sql, $link) ) {
										echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
										end_transaction(0,$link);
										die;
									}
									
									end_transaction(1,$link);
// echo $sql . "<BR>";

								} // else echo "<h2>Boom 4".substr($row['IngredientProductNumber'], 0, 1)." == 6</h2>";
							} // else echo "<h2>Boom 3- $lot == '' or $qty == '' or $qty < 0</h2>";
						} // else echo "<h2>Boom 2 Key($key) - ".substr($key, 4, 6)." != $row[IngredientProductNumber] </h2>";
					}   // FOR LOOP FOR EACH POST VARIABLE
				} // else echo "<h2>Boom 1( '10829' == substr($row[IngredientProductNumber],0,5) or 4 == substr($row[IngredientProductNumber], 0, 1) or 0 >= $row[Quantity] ) </h2>";  // SHOW INGREDIENT IF NOT INSTRUCTIONS OR WATER
			}   // WHILE LOOPING THROUGH INGREDIENTS
		// And then loop through packaging.
		
		$bg = 0; 
		$total = 0;
		$i = 0;
		$packaging_amount = 0;
		$c=0;
		foreach (array_keys($_POST) as $key) {
	
	//		echo "POST['".$key."']=".$_POST[$key]."<br />";
			$tmpArr = explode("_",$key);
	//		foreach($tmpArr as $tmpvalue) {
	//			echo "Values from tmparray $tmpvalue <br />";
	//		}
			if ( substr($tmpArr[1],0,1) == '6') 
			{
	//			echo "I found the package - $_POST[$key] and $key";
				$found_packs = true;
				$i++;
				if ( $tmpArr[0] == 'lot' ) {
	//				echo "I found Package's lot $_POST[$key] and $key";
					$lot = $_POST[$key];
					$qty_key = str_replace("lot","qty",$key);
	//				echo "<br /> qty_key = $qty_key <br />";
					$qty = prep_number($_POST["$qty_key"]);
					if ( $lot != '' and $qty > 0 ) {
						$run_deletes = true;
						$sql = "INSERT INTO inventorymovements ".
							"(LotID, ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate, Remarks) ".
							"VALUES ".
							"(" . escape_data($lot) . ", " . escape_data($tmpArr[1]) . ", " . escape_data($qty) . ", 8, 'C', '" . date("Y-m-d H:i:s") . "', 'Cust PO# " . escape_data($tmpArr[3]) . " - " . escape_data($tmpArr[2]) . "')";
					/*here using $tmpArr[2] - CustomerOrderNumber replaced CustomerCodeNumber that is queried from bsci, if needed, may replace customerordernumber with customercodenumber in keys - jdu */
								
		//				echo "<br /> $sql<br />";
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
							"VALUES ".
							"(" . escape_data($bsn) . ", '" . escape_data($tmpArr[2]) . "', '" . escape_data($tmpArr[4]) . "', '" . escape_data($tmpArr[3]) . "', '" . escape_data($tmpArr[1]) . "', " . escape_data($lot) . ", " . escape_data($trans_num) . ", " . escape_data($qty) . ")";
		//				echo "<br /> $sql <br />";
						if ( ! mysql_query($sql, $link) ) {
							echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
							end_transaction(0,$link);
							die;
						}
							
						end_transaction(1,$link);
// echo $sql . "<BR>";

					}
				} 
			}
		}

// die();

		if ( !$error_found and $run_deletes ) {

				$sql = "DELETE im.* FROM inventorymovements AS im, batchsheetdetail AS bsd WHERE im.TransactionNumber = bsd.InventoryTransactionNumber AND bsd.BatchSheetNumber = " . $bsn;
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				// echo "<p>$sql</p>";
						
				// $sql = "UPDATE batchsheetdetail SET InventoryTransactionNumber = NULL WHERE BatchSheetNumber = " . $bsn;
				// mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
						
				$sql = "DELETE im.* FROM inventorymovements AS im, batchsheetcustomerinfo AS bsci WHERE im.TransactionNumber = bsci.InventoryTransactionNumber AND bsci.BatchSheetNumber = " . $bsn;
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				// echo "<p>$sql</p>";
						
				// $sql = "UPDATE batchsheetcustomerinfo SET InventoryTransactionNumber = NULL WHERE BatchSheetNumber = " . $bsn;
				// mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				
				$sql = "SELECT c.`name`, bsci.`CustomerOrderNumber`, bsci.`CustomerOrderSeqNumber`, bsci.`CustomerPONumber` FROM batchsheetcustomerinfo as bsci, customers AS c, customerordermaster AS com WHERE bsci.`CustomerOrderNumber`=com.`OrderNumber` AND com.`CustomerID`=c.`customer_id` AND bsci.`BatchSheetNumber`=$bsn";
				$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				while ($row=mysql_fetch_array($result)) {
					$remark .= " - Cust. ".mysql_real_escape_string($row[name])."; PO: $row[CustomerOrderNumber]; Cust. PO: $row[CustomerPONumber]";
				}
				
				$sql = "UPDATE inventorymovements AS im, batchsheetmaster AS bsm SET im.MovementStatus = 'C', im.Remarks='Batch Sheet #$bsn for".mysql_real_escape_string($remark)."', im.LotID=bsm.LotID WHERE im.TransactionNumber=bsm.InventoryMovementTransactionNumber AND bsm.BatchSheetNumber =$bsn";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				//echo "<p>$sql</p>";
						
				$sql = "UPDATE lots, batchsheetmaster AS bsm SET LotNumber='$lot_number', LotSequenceNumber='$lot_sequence_number', lots.InventoryMovementTransactionNumber = bsm.InventoryMovementTransactionNumber, StorageLocation='Warehouse', lots.VendorID=2382 WHERE lots.ID=bsm.LotID AND bsm.BatchSheetNumber =$bsn";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				//echo "<p>$sql</p>";

				$sql = "UPDATE batchsheetmaster SET Manufactured = 1 WHERE BatchSheetNumber = " . $bsn;
				//, abeleiLotNumber=$formulaLotNumber
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				//echo "<p>$sql</p>";

			}
		}   // IF INGREDIENTS ARE FOUND

		$_SESSION['note'] .= "Lots successfully saved<BR>";
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "window.opener.location.reload()\n";
		echo "window.close()\n";
		echo "</SCRIPT>\n";
		exit();
	
	}

}

include("inc_pop_header.php"); ?>





<B>Assign Lot Numbers</B><BR><BR>



<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


<FORM ACTION="pop_select_lots_for_batch_sheet.php" METHOD="post">
<INPUT TYPE="hidden" NAME="bsn" VALUE="<?php echo $bsn;?>">
<INPUT TYPE="hidden" NAME="pni" VALUE="<?php echo $pni;?>">

<?php

$sql = "SELECT bsd.*, pm.Designation, pm.UnitOfMeasure, im.Quantity, bsm.TotalQuantityUnitType
	FROM batchsheetdetail AS bsd, inventorymovements AS im, productmaster AS pm, 
	batchsheetmaster as bsm
	WHERE bsd.InventoryTransactionNumber = im.TransactionNumber AND bsd.IngredientProductNumber = pm.ProductNumberInternal AND
	bsm.BatchSheetNumber = bsd.BatchSheetNumber AND 
	bsd.BatchSheetNumber = $bsn 
	ORDER BY IngredientSEQ";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$c = mysql_num_rows($result);
// echo "$c rows result - $sql";

if ( $c > 0 ) {
	$total = 0;
	$i = 0;
	while ( $row = mysql_fetch_array($result) ) {
		// ignore water, instructions, and ingredients whose quantity is less than 0
		if ( '10829' == substr($row[IngredientProductNumber],0,5) or 4 == substr($row[IngredientProductNumber], 0, 1))
			continue;
		if ( 0 >= $row[Quantity] ) {
			echo "<h3>Not enough inventory on hand for $row[IngredientProductNumber] - [$row[Designation]]; Inventory = $row[Quantity]</h3>";
			continue;
		}
		$bg = 0;
		$i++;

			$sql = 
			"SELECT lots.LotNumber, lots.LotSequenceNumber, lots.ID, ROUND( QuantityConvert(vwinventory.InventoryCount,'grams','$row[UnitOfMeasure]'),2) AS InventoryCount, 
					vendors.name, tsdd.Location_On_Site, lots.StorageLocation 
			FROM vwinventory, lots, vendors, tblsystemdefaultsdetail AS tsdd 
			WHERE lots.ID = vwinventory.LotID AND vendors.vendor_id = lots.VendorId AND 
				tsdd.ItemDescription = lots.StorageLocation AND
				LotNumber IS NOT NULL AND vwinventory.ProductNumberInternal = '$row[IngredientProductNumber]' AND ROUND(InventoryCount,2) > 0 ORDER BY tsdd.Sequence ASC, InventoryCount ASC";
			$result_lots = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			// echo "<h3>$sql</h3>";
			if ( mysql_num_rows($result_lots) > 0 ) {
			$c = 0;
			?>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
				<TR>
					<TD><B CLASS="black">Ingredient:</B></TD>
					<TD><?php echo $row[IngredientProductNumber];?> - [<?php echo $row[Designation];?>] <input type="hidden" id="units_<?php echo $row[IngredientProductNumber];?>" name="units_<?php echo $row[IngredientProductNumber];?>" value="<?php echo $row[UnitOfMeasure];?>" /></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
					<TD><B CLASS="black">Qty Needed:</B></TD>
					<TD><?php $quantity_needed = QuantityConvert($row[Quantity],'grams',$row[UnitOfMeasure]); echo number_format($quantity_needed, 2)." $row[UnitOfMeasure]"; ?></TD>
				</TR>
			</TABLE>

			<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
			<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
			<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">


				<TR VALIGN=TOP>
					<TD><B>Vendor</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD><B>Lot#</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><B>Lot Seq#</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><B>Location</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><B>On Site</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><B>Inventory Count</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><B>Quantity (<?php echo $row[UnitOfMeasure] ?>)</B></TD>
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
					if ( isset($_POST["qty_$row[IngredientProductNumber]_".floor($row[IngredientSEQ])."_$c"]) ) 
					{
						$subQuantity = prep_number($_POST["qty_$row[IngredientProductNumber]_".floor($row[IngredientSEQ])."_$c"]);
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

					?>

					<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
						<TD><?php echo $row_lots[name];?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD><?php echo $row_lots[LotNumber];?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD ALIGN=RIGHT><?php echo $row_lots[LotSequenceNumber];?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD ALIGN=MIDDLE><?php echo $row_lots[StorageLocation];?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD ALIGN=MIDDLE><?php echo (1==$row_lots[Location_On_Site] ? 'Y' :'N');?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD ALIGN=RIGHT><?php echo number_format($row_lots[InventoryCount],2);?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
						<TD>
						<INPUT TYPE="hidden" NAME="<?php echo "lot_$row[IngredientProductNumber]_".floor($row[IngredientSEQ])."_$c";?>" VALUE="<?php echo $row_lots[ID];?>" SIZE="8" STYLE="text-align:right">
						<INPUT TYPE="text" NAME="<?php echo "qty_$row[IngredientProductNumber]_".floor($row[IngredientSEQ])."_$c";?>" VALUE="<?php echo number_format($subQuantity,2) ?>" SIZE="15" STYLE="text-align:right">
						<?php echo $row[UnitOfMeasure]; ?>
						</TD>
					</TR>
				<?php } ?>

			</TABLE>

			</TD></TR></TABLE>
			</TD></TR></TABLE>
			</TD></TR></TABLE><BR>
			

			<?php } else { ?>

				<h3>No inventory found for product number <?php echo $row[IngredientProductNumber] ?>.</h3>

			<?php } ?>

		<?php
	}
}
if (!$intermediary || $FinalProductNotCreatedByAbelei) {
	//get packaging data
	$sql = "SELECT bsci.*, pm.Designation FROM batchsheetcustomerinfo AS bsci, productmaster AS pm WHERE pm.ProductNumberInternal=bsci.PackIn AND BatchSheetNumber = $bsn";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	$bg = 0; 
	if ( $c > 0 ) {
		$found_packs = true;
		$total = 0;
		$i = 0;
		while ( $row = mysql_fetch_array($result) ) { 
			$i++;
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
					AND vwinventory.ProductNumberInternal = '$row[PackIn]' 
					AND ROUND(InventoryCount,2) > 0";
			$result_lots = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			// echo "<h3>$sql</h3>";
			if ( mysql_num_rows($result_lots) > 0 ) {
				$c = 0;

				?>
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
						<TR>
							<TD><B CLASS="black">P.O.: </b></td>
							<TD><?php echo $row[CustomerPONumber];?></td>
							<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
							<td><B>Packaging:</B> <?php echo "$row[PackIn] - [$row[Designation]]"; ?></TD>
							<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
							<TD><B CLASS="black">Qty Needed:</B></TD>
							<TD><?php $quantity_needed = $row[NumberOfPackages]; echo number_format($quantity_needed, 2);?></TD>
						</TR>
					</TABLE>

					<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
					<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
					<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">


						<TR VALIGN=TOP>
							<TD><B>Vendor</B></TD>
							<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
							<TD><B>Lot#</B></TD>
							<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
							<TD ALIGN=RIGHT><B>Lot Seq#</B></TD>
							<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
							<TD ALIGN=RIGHT><B>Inventory Count</B></TD>
							<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
							<TD ALIGN=RIGHT><B>Quantity </B></TD>
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
							if ( isset($_POST["qty_" . $row['IngredientProductNumber'] . "_" .$row[CustomerPONumber] . "_" . $c]) ) 
							{
								$subQuantity = prep_number($_POST["qty_" . $row['IngredientProductNumber'] . "_" .$row[CustomerPONumber] . "_" . $c]);
							}
							else
							{
								$lot_qty = $row_lots['InventoryCount'] - $prev_qty_needed_arr[$row_lots['LotNumber']];
								if ( $quantity_needed > $lot_qty ) 
								{
									$subQuantity = $lot_qty;
									$quantity_needed-= $lot_qty;
								}
								else
								{
									$subQuantity = $quantity_needed;
									$quantity_needed=0;
								}
							}

							$prev_qty_needed_arr[$row_lots['LotNumber']] = $subQuantity;
							
							?>

							<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
								<TD><?php echo $row_lots['name'];?></TD>
								<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
								<TD><?php echo $row_lots['LotNumber'];?></TD>
								<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
								<TD ALIGN=RIGHT><?php echo $row_lots['LotSequenceNumber'];?></TD>
								<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
								<TD ALIGN=RIGHT><?php echo number_format($lot_qty);?></TD>
								<TD><IMG SRC="images/spacer.gif" WIDTH="3" HEIGHT="1"></TD>
								<TD>
								<INPUT TYPE="hidden" NAME="<?php echo "lot_$row[PackIn]_$row[CustomerOrderNumber]_$row[CustomerPONumber]_$c";?>" VALUE="<?php echo $row_lots[ID];?>" SIZE="8" STYLE="text-align:right">
								<INPUT TYPE="text" NAME="<?php echo "qty_$row[PackIn]_$row[CustomerOrderNumber]_$row[CustomerPONumber]_$c";?>" VALUE="<?php echo number_format($subQuantity,2) ?>" SIZE="15" STYLE="text-align:right">
								</TD>
							</TR>
						<?php } ?>
				</TABLE>

					</TD></TR></TABLE>
					</TD></TR></TABLE>
					</TD></TR></TABLE><BR><?php
			} else echo "No Inventory for packaging $row[PackIn] found";
		}
	} else echo "No packaging found";
}
?>







<p><b>Final Product Lot #</b> <input type="text" name="lot_number" value="<?php echo formatTxt(getFormSafe($lot_number)) ?>" /> 
<b>Seq. #</b> <input type="text" name="lot_sequence_number" value="<?php echo formatTxt(getFormSafe($lot_sequence_number)) ?>" /></p>

<BR>

<INPUT TYPE="submit" VALUE="Save" CLASS="button_pop">

&nbsp;&nbsp;&nbsp;

<INPUT TYPE="button" VALUE="Close" CLASS="button_pop" onClick="window.close()"></FORM>




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

<?php include("inc_footer.php"); ?>