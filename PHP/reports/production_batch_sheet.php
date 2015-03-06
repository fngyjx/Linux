<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

include('../inc_global.php');

$sql = "SELECT batchsheetmaster.*, lots.QualityControlEmployeeID, lots.DateManufactured, lots.ExpirationDate, lots.QualityControlDate FROM batchsheetmaster
LEFT JOIN lots ON batchsheetmaster.LotID = lots.ID
WHERE BatchSheetNumber = $_REQUEST[bsn]";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
//echo $sql . "<BR>";
$LotID = $row['LotID'];
$ProductNumberExternal = $row['ProductNumberExternal'];
$ProductNumberInternal = $row['ProductNumberInternal'];
$ProductDesignation = $row['ProductDesignation'];
$NetWeight = $row['NetWeight'];
$TotalQuantityUnitType = $row['TotalQuantityUnitType'];
$Column1UnitType = $row['Column1UnitType'];
$Column2UnitType = $row['Column2UnitType'];
$Yield = $row['Yield'];
$NumberOfTimesToMake = $row['NumberOfTimesToMake'];
$Allergen = $row['Allergen'];
$Kosher = $row['Kosher'];
$Vessel = $row['Vessel'];
$ScaleNumber = $row['ScaleNumber'];
$MadeBy = $row['MadeBy'];
$Filtered = $row['Filtered'];
$QualityControlEmployeeID = $row['QualityControlEmployeeID'];
$CommitedToInventory = $row['CommitedToInventory'];
$Manufactured = $row['Manufactured'];
$InventoryMovementRemarks = $row['InventoryMovementRemarks'];
//$abeleiLotNumber = $row['abeleiLotNumber'];
//$LotSequenceNumber = $row['LotSequenceNumber'];
$Notes = $row['Notes'];


if ( $NetWeight != 0 and $Yield != 0 ) {
	$gross_weight = ($NetWeight/($Yield/100)/100);
} else {
	$gross_weight = 0.00;
}

if ( $row['DueDate'] != '' ) {
	$DueDate = date("n/j/Y", strtotime($row['DueDate']));
} else {
	$DueDate = '';
}

if ( $row['DateManufactured'] != '' ) {
	$DateManufactured = date("n/j/Y", strtotime($row['DateManufactured']));
} else {
	$DateManufactured = '';
}

if ( $row['ExpirationDate'] != '' ) {
	$ExpirationDate = date("n/j/Y", strtotime($row['ExpirationDate']));
} else {
	$ExpirationDate = '';
}

if ( $row['QualityControlDate'] != '' ) {
	$QualityControlDate = date("n/j/Y", strtotime($row['QualityControlDate']));
} else {
	$QualityControlDate = '<I>None entered yet</I>';
}

if ( $QualityControlEmployeeID != '' and $QualityControlEmployeeID != 0 ) {
	$sql = "SELECT first_name, last_name FROM users WHERE user_id = " . $QualityControlEmployeeID;
	$result = mysql_query($sql, $link);
	if ( mysql_num_rows($result) > 0 ) {
		while ( $row = mysql_fetch_array($result) ) {
			$QualityControlEmployee = $row['first_name'] . " " . $row['last_name'];
		}
	}
} else {
	$QualityControlEmployee = '<I>None entered yet</I>';
}
?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="96%" ALIGN=CENTER>
<TR><TD align="center"><B>Production Batch Sheet</B> <?php echo "Batch sheet#: " . $_REQUEST['bsn'] ;?></TD></TR>
<TR><TD>
<table border='1'><tr style="border-style:none">
<td align="left" style="border-style:none">
<?php echo "<NOBR><b>Formula for: </b>" . $ProductDesignation . "</NOBR></td><td style='border-style:none'><NOBR><b>abelei#:</b>  " . $ProductNumberExternal . "</NOBR></td>";
echo "<td style='border-style:none'><NOBR><b>Internal#:</b> " . $ProductNumberInternal . "</NOBR></td><td style='border-style:none'>";
echo "<NOBR><b>Scale#:</b> " . $ScaleNumber . "<?NOBR></td></tr>"; 
echo "<tr><td style='border-style:none'><NOBR><b>Gross weight:</b> " . number_format($gross_weight, 2) . " " . $TotalQuantityUnitType . "&nbsp;&#151;";
echo "<b>Net weight:</b> " . number_format($NetWeight,2)  . " " . $TotalQuantityUnitType . "</NOBR></td><td style='border-style:none'>";
echo "<NOBR><b>Allergen:</b> " . $Allergen . "</NOBR></td><td style='border-style:none'>";
echo "<NOBR><b>Vessel:</b> " . $Vessel . "</NOBR></td><td style='border-style:none'>";
if ( $NumberOfTimesToMake > 1 ) {
	$plural = " times";
} else {
	$plural = " time";
}
echo "<NOBR><b>Make:</b> " . $NumberOfTimesToMake . $plural . "</NOBR></td></tr>";
$sql = "SELECT bsci.*, name FROM batchsheetcustomerinfo AS bsci, customerordermaster AS com, customers AS c 
WHERE bsci.CustomerOrderNumber = com. OrderNumber AND com.CustomerID = c.customer_id AND bsci.BatchSheetNumber = $_REQUEST[bsn]";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
echo "<tr><td colspan=\"4\"><NOBR>";
while ( $row = mysql_fetch_array($result) ) {
	echo "Customer: $row[name] &#151; Cust. PO:  $row[CustomerPONumber] &nbsp;&nbsp;";
}
echo "</NOBR></td></tr>"

?>
</table></TD></TR>
<TR><TD>


<?php


?><TABLE CELLPADDING=3 CELLSPACING=0 BORDER=1>

<TR>
<TD><B STYLE="font-size:8pt">Seq#</B></TD>
<TD ALIGN=RIGHT><B STYLE="font-size:8pt">Internal#</B></TD>
<TD><B STYLE="font-size:8pt">Ingredient</B></TD>
<TD><B STYLE="font-size:8pt">Vendor</B></TD>
<TD><B STYLE="font-size:8pt">%age</B></TD>
<TD ALIGN=RIGHT><B STYLE="font-size:8pt">Amount</B></TD>

<?php if ( $TotalQuantityUnitType == "lbs" and $Column1UnitType == "lbs" and $Column2UnitType == "lbs" ) { ?>
	<TD ALIGN=RIGHT><B STYLE="font-size:8pt">Amount</B></TD>
<?php } else { ?>
	<TD ALIGN=RIGHT><B STYLE="font-size:8pt">Amount</B></TD>
<?php } ?>

<TD><B STYLE="font-size:8pt"><nobr>Raw Material Lot#</nobr></B></TD>
<TD><B STYLE="font-size:8pt">FEMA#</B></TD>
 <!-- <TD>&nbsp;</TD> -->
</TR>



<?php

$sql="SELECT l.LotNumber AS abeleiLotNumber, l.LotSequenceNumber AS LotSequenceNumber FROM `inventorymovements` AS im, `batchsheetmaster` AS bsm, `lots` AS l ".
"WHERE bsm.`InventoryMovementTransactionNumber` = im.`TransactionNumber` AND l.id=im.LotID AND bsm.`BatchSheetNumber` = " . $_REQUEST['bsn'];
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if (0 <  mysql_num_rows($result)) {
	$row = mysql_fetch_array($result);
	$abeleiLotNumber = $row[abeleiLotNumber];
	$LotSequenceNumber = $row[LotSequenceNumber];
} 
else {
	$abeleiLotNumber = "";
	$LotSequenceNumber = "";
}
	
$sql = "SELECT batchsheetdetail. *, productmaster.FEMA_NBR, productmaster.Natural_OR_Artificial, productmaster.Organic, productmaster.Kosher, productmaster.Designation, productmaster.ProductType, vendors.name, Quantity, externalproductnumberreference.ProductNumberExternal AS pne
FROM batchsheetdetail
LEFT JOIN inventorymovements ON batchsheetdetail.InventoryTransactionNumber = inventorymovements.TransactionNumber
LEFT JOIN productmaster ON batchsheetdetail.IngredientProductNumber = productmaster.ProductNumberInternal
LEFT JOIN vendors ON vendors.vendor_id = batchsheetdetail.VendorID
LEFT JOIN externalproductnumberreference ON batchsheetdetail.IngredientProductNumber = externalproductnumberreference.ProductNumberInternal 
WHERE batchsheetdetail.BatchSheetNumber = " . $_REQUEST['bsn'] . "
ORDER BY IngredientSEQ";

	
	
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$c = mysql_num_rows($result);
if ( $c > 0 ) {
	$total = 0;
	$i = 0;
	while ( $row = mysql_fetch_array($result)) {
		$ipn = $row['IngredientProductNumber'];
		?>

		<TR VALIGN=TOP>

			<TD class="batch_sheet_padded" ><?php echo number_format($row['IngredientSEQ'],0);?></TD>
			<TD class="batch_sheet_padded" ALIGN=RIGHT><?php echo $row['IngredientProductNumber'];?></TD>

			<?php

			if ( $row['pne'] != '' ) {
				$abelei_num_string = " (abelei# " . $row['pne'] . ")";
			} else {
				$abelei_num_string = "";
			}

			$i++;
			
			$description = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");

			if ( substr($row['IngredientProductNumber'], 0, 1) == 4 ) {
				$bgcolor = "BGCOLOR='#FFFF99'";
				$colspan = "COLSPAN=7";
				//$ingredient_string = "<B CLASS='white'>" . $description . $abelei_num_string . "</B>";
				$ingredient_string = $description . $abelei_num_string;
			} else {
				$bgcolor = "";
				$colspan = "";
				$ingredient_string = $description . " - " . $row['IngredientProductNumber'] . $abelei_num_string;
			}

				
			?>

			<TD class="batch_sheet_padded" <?php echo $bgcolor;?> <?php echo $colspan;?>><?php echo $ingredient_string;?></TD>
			
			<?php $percentage = number_format($row['Percentage'],2);?>

			<?php
	//		$sql = "SELECT ".
	//			"ROUND(ProductTotal(productmaster.ProductNumberInternal,'C',NULL),2) as total, ".
	//			"ROUND(ProductTotal(productmaster.ProductNumberInternal,'P',1),2) as ordered, ".
	//			"ROUND(ProductTotal(productmaster.ProductNumberInternal,'P',8),2) as committed, ".
	//			"ROUND(ProductTotal(productmaster.ProductNumberInternal,NULL, NULL),2) as net ".
	//			"FROM productmaster ".
	//			"WHERE productmaster.ProductNumberInternal=$row[IngredientProductNumber]";
			
			$sql = "Select DISTINCT ProductTotal(inventorymovements.ProductNumberInternal,'C',NULL) as total, ".
				"COALESCE((".
				"SELECT SUM(QuantityConvert( (TotalQuantityExpected), UnitOfMeasure, 'grams')) ".
				"FROM purchaseorderdetail WHERE ProductNumberInternal = productmaster.ProductNumberInternal ".
				" AND (`Status` = 'O' OR `Status` = 'P')".
				"),0) as ordered, ".
				"ProductTotal(inventorymovements.ProductNumberInternal,'P',NULL) as committed, ".
				"ProductTotal(inventorymovements.ProductNumberInternal,NULL, NULL) as net, ".
				"productmaster.*, externalproductnumberreference.ProductNumberExternal as external ".
				"FROM productmaster ".
				"LEFT JOIN inventorymovements ON (inventorymovements.ProductNumberInternal = productmaster.ProductNumberInternal) ".
				"LEFT JOIN receipts ON ( receipts.LotID = inventorymovements.LotID ) ".
				"LEFT JOIN lots ON (inventorymovements.LotID = lots.ID) ".
				"LEFT JOIN externalproductnumberreference ON (externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal) ".
				"LEFT JOIN vwmaterialpricing ON (vwmaterialpricing.ProductNumberInternal = productmaster.ProductNumberInternal)
				WHERE productmaster.ProductNumberInternal=" . $row[IngredientProductNumber];

			$result_vend = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			// echo $sql . "<BR>";
			if ( mysql_num_rows($result_vend) > 0 ) {
				$row_inv = mysql_fetch_array($result_vend);
				$YorN="Y";
				$InvG = $row_inv[total];
				$AmtOrdG = $row_inv[ordered];
				$AmtComG = $row_inv[committed];
				$InvLbs = QuantityConvert($InvG, "grams", "lbs");
				$AmtOrdLbs = QuantityConvert($AmtOrdG, "grams", "lbs");
				$AmtComLbs = QuantityConvert($AmtComG, "grams", "lbs");
				$BatchAmtLbs = QuantityConvert($gross_weight*($row[Percentage]/100),$TotalQuantityUnitType,"lbs");
				$BatchAmtG = QuantityConvert($gross_weight*($row[Percentage]/100),$TotalQuantityUnitType,"grams");

				if ( $InvG < $BatchAmtG AND "10829"!=substr($row['IngredientProductNumber'],0,5) AND '4'!=substr($row['IngredientProductNumber'],0,1) ) // make exception for water (10829?) and instructions
				{ 
					$YorN = "N";
					$insufficient_inventory = true;
				}
			} else {
				$BatchAmtLbs = 0;
				$BatchAmtG = 0;
			}

			if ( $row['FEMA_NBR'] != '' ) {
				$fema = $row['FEMA_NBR'];
			} else {
				$fema = "&nbsp;";
			}

			if ( substr($row['IngredientProductNumber'], 0, 1) != 4 ) {

				$sql = "SELECT batchsheetdetaillotnumbers.IngredientProductNumber, vendors.name, CONCAT(lots.LotNumber,' - ',lots.LotSequenceNumber) AS ID, QuantityUsedFromThisLot
				FROM batchsheetdetaillotnumbers
				LEFT JOIN lots ON batchsheetdetaillotnumbers.LotID = lots.ID
				LEFT JOIN vendors ON lots.VendorId = vendors.vendor_id
				WHERE BatchSheetNumber = " . $_REQUEST['bsn'] . "
				AND batchsheetdetaillotnumbers.IngredientProductNumber = " . $ipn . "
				AND batchsheetdetaillotnumbers.IngredientSEQ = ". $row['IngredientSEQ'] ." 
				ORDER BY QuantityUsedFromThisLot";
					
				$result_vend = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				if ( mysql_num_rows($result_vend) > 0 ) {
					$ID = '';
					$current_id = "";
					$total_quantity = 0.0;
					
					while ( $row_vend = mysql_fetch_array($result_vend) ) {
						$vendor_name = $row_vend['name'];
						if ( '' == $vendor_name  ) {
							if ('2' == substr($row['IngredientProductNumber'], 0, 1)) {
								$vendor_name = 'Abelei';
							} else {
								$vendor_name = '<I>None entered</I>';
							}
						}
						
						if ( $NumberOfTimesToMake > 1 ) {
							$quantity = $row_vend['QuantityUsedFromThisLot'] / $NumberOfTimesToMake;
						} else {
							$quantity = $row_vend['QuantityUsedFromThisLot'];
						}
						
						$total_quantity += $quantity;
						
						if ( $current_id != $row_vend['IngredientProductNumber'] ) {
							echo "<TD class='batch_sheet_padded' >" . $vendor_name . "</TD>";
							echo "<TD class='batch_sheet_padded' ALIGN=RIGHT>" . $percentage . "</TD>";
							echo "<TD class='batch_sheet_padded' ALIGN=RIGHT><NOBR>" . number_format(QuantityConvert($quantity,'grams','lbs'), 2) . "<small>lbs</small></NOBR></TD>";

							if ( $TotalQuantityUnitType == "lbs" and $Column1UnitType == "lbs" and $Column2UnitType == "lbs" ) {
								echo "<TD class='batch_sheet_padded' ALIGN=RIGHT><NOBR>" . number_format(QuantityConvert($quantity,'grams','lbs'), 2) . "<small>lbs</small></NOBR></TD>";
							} else {
								echo "<TD class='batch_sheet_padded' ALIGN=RIGHT><NOBR>" . number_format($quantity, 2) . "<small>grams</small></NOBR></TD>";
							}

							echo "<TD class='batch_sheet_padded' ALIGN=RIGHT>" . $row_vend['ID'] . "</TD>";
							echo "<TD class='batch_sheet_padded' ALIGN=RIGHT>" . $fema . "</TD>";
						//	echo "<TD class='batch_sheet_padded' WIDTH=16>&nbsp;</TD>";
						} else {
							echo "</TR><TR VALIGN=TOP>";
							echo "<TD class='batch_sheet_padded' COLSPAN=3>&nbsp;</TD>";
							echo "<TD class='batch_sheet_padded' >" . $vendor_name . "</TD>";
							echo "<TD class='batch_sheet_padded' ALIGN=RIGHT>" . $percentage . "</TD>";
							echo "<TD class='batch_sheet_padded' ALIGN=RIGHT><NOBR>" . number_format(QuantityConvert($quantity,'grams','lbs'), 2) . "<small>lbs</small></NOBR></TD>";

							if ( $TotalQuantityUnitType == "lbs" and $Column1UnitType == "lbs" and $Column2UnitType == "lbs" ) {
								echo "<TD class='batch_sheet_padded' ALIGN=RIGHT><NOBR>" . number_format(QuantityConvert($quantity,'grams','lbs'), 2) . "<small>lbs</small></NOBR></TD>";
							} else {
								echo "<TD class='batch_sheet_padded' ALIGN=RIGHT><NOBR>" . number_format($quantity, 2) . "<small>grams</small></NOBR></TD>";
							}

							echo "<TD class='batch_sheet_padded' ALIGN=RIGHT>" . $row_vend['ID'] . "</TD>";
							echo "<TD class='batch_sheet_padded' ALIGN=RIGHT>" . $fema . "</TD>";
							// echo "<TD class='batch_sheet_padded' WIDTH=16>&nbsp;</TD>";
							echo "</TR>";
						}
						$ID = $row_vend['ID'];
						$current_id = $row_vend['IngredientProductNumber'];
					}
					$BatchAmtLbs = QuantityConvert($total_quantity,"grams","lbs");
					$BatchAmtG = $total_quantity;
				} else {
					$BatchAmtLbs = QuantityConvert($gross_weight*($row[Percentage]/100),$TotalQuantityUnitType,"lbs");
					$BatchAmtG   = QuantityConvert($gross_weight*($row[Percentage]/100),$TotalQuantityUnitType,"grams");
					echo "<TD class='batch_sheet_padded' >$row[name]</TD>";
					//$lb_total = ($gross_weight * $row['Percentage'])/100;
					echo "<TD class='batch_sheet_padded' ALIGN=RIGHT>" . number_format($row['Percentage'], 2) . "</TD>";
					echo "<TD class='batch_sheet_padded' ALIGN=RIGHT><NOBR>" . number_format($BatchAmtLbs, 2) . "<small>lbs</small></NOBR></TD>";

					if ( $TotalQuantityUnitType == "lbs" and $Column1UnitType == "lbs" and $Column2UnitType == "lbs" ) {
						echo "<TD class='batch_sheet_padded' ALIGN=RIGHT><NOBR>" . number_format($BatchAmtLbs, 2) . "<small>lbs</small></NOBR></TD>";
					} else {
						echo "<TD class='batch_sheet_padded' ALIGN=RIGHT><NOBR>" . number_format($BatchAmtG, 2) . "<small>grams</small></NOBR></TD>";
					}

					echo "<TD class='batch_sheet_padded' COLSPAN=2>&nbsp;</TD>";
				//	echo "<TD class='batch_sheet_padded' WIDTH=16>&nbsp;</TD>";
				}
			} else {
				//Fecho "<TD WIDTH=16 >&nbsp;</TD>";
			}
			
			$total_BatchLbs += $BatchAmtLbs;
			$total_BatchG += $BatchAmtG;
			
		?>

	</TR>

	<?php
	}

}

echo "<TR><TD style=\"border-style:none; font-size:7pt\" colspan='5'>Required assuming: " . $Yield*100 .
 " % yield :</td><td style=\"border-style:none; font-size:7pt\">".number_format(QuantityConvert($gross_weight,$TotalQuantityUnitType,$Column1UnitType),2).
 " $Column1UnitType</td><td style=\"border-style:none; font-size:7pt\">".number_format(QuantityConvert($gross_weight,$TotalQuantityUnitType,$Column2UnitType),2).
 " $Column2UnitType</td><td colspan='2' style=\"border-style:none; font-size:7pt\">&nbsp;</td></TR>";

?></TABLE>

<?php $sql = "SELECT DISTINCT bsdpln.*, bsci.*, customerordermaster.RequestedDeliveryDate, c.CustomerCodeNumber AS ccn, c.Quantity, c.PackSize, c.TotalQuantityOrdered, name
FROM batchsheetcustomerinfo AS bsci
LEFT JOIN customerorderdetail AS c
ON c.CustomerOrderNumber = bsci.CustomerOrderNumber
AND c.CustomerOrderSeqNumber = bsci.CustomerOrderSeqNumber
AND bsci.BatchSheetNumber = " . $_REQUEST['bsn'] . "
LEFT JOIN batchsheetdetailpackaginglotnumbers AS bsdpln
ON bsci.BatchSheetNumber = bsdpln.BatchSheetNumber
AND c.CustomerOrderNumber = bsdpln.CustomerOrderNumber
AND c.CustomerOrderSeqNumber = bsdpln.CustomerOrderSeqNumber
AND bsdpln.BatchSheetNumber = " . $_REQUEST['bsn'] . "
LEFT JOIN customerordermaster ON c.CustomerOrderNumber = customerordermaster.OrderNumber
LEFT JOIN customers ON customers.customer_id = customerordermaster.CustomerID
WHERE bsci.BatchSheetNumber = " . $_REQUEST['bsn'];
//echo $sql;
							
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$c = mysql_num_rows($result);
if ( $c > 0 ) { ?>

<TABLE BORDER=1 CELLSPACING="0" CELLPADDING="3">
	<TR VALIGN=BOTTOM>
		<TD><B STYLE="font-size:8pt">PO#</B></TD>
		<TD><B STYLE="font-size:8pt">Cust Code</B></TD>
		<TD><B STYLE="font-size:8pt">Pack in</B></TD>
		<TD><B STYLE="font-size:8pt">#Packs</B></TD>
		<TD><B STYLE="font-size:8pt">Lot#</B></TD>
	</TR>
	<?php
	$total_packs = 0;
	while ( $row = mysql_fetch_array($result) ) {
	  if ( $row['RecordID'] != "" and $row['QuantityUsedFromThisLot'] >0 ) {
	  		$total_packs = $total_packs + $row['QuantityUsedFromThisLot'];
		?>
		<TR VALIGN=TOP>
			<TD STYLE="font-size:8pt"><?php echo $row['CustomerPONumber'];?>&nbsp;</TD>
			<TD STYLE="font-size:8pt"><?php echo $row['CustomerCodeNumber'];?>&nbsp;</TD>
			<TD STYLE="font-size:8pt"><?php
				$sub_sql = "SELECT ProductNumberInternal, Designation FROM productmaster WHERE ProductNumberInternal LIKE '6%'";
				$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
				while ( $sub_row = mysql_fetch_array($sub_result) ) {
					if ( $row["PackagingProductNumber"] == $sub_row["ProductNumberInternal"] ) {
						echo $sub_row["Designation"];
					}
				}
				?>&nbsp;</TD>
			<TD STYLE="font-size:8pt"><?php echo $row['QuantityUsedFromThisLot'];?>&nbsp;</TD>
			<TD STYLE="font-size:8pt"><?php echo $row['LotID'];?>&nbsp;</TD>
		</TR>
	<?php
	  } else
	  if ( $row['PackIn'] != "" and $row_pkin['NumberOfPackages'] > 0 ){
		$total_packs = $total_packs + $row['NumberOfPackages'];
		?>
		<TR VALIGN=TOP>
			<TD STYLE="font-size:8pt"><?php echo $row['CustomerPONumber'];?>&nbsp;</TD>
			<TD STYLE="font-size:8pt"><?php echo $row['CustomerCodeNumber'];?>&nbsp;</TD>
			<TD STYLE="font-size:8pt"><?php
				$sub_sql = "SELECT ProductNumberInternal, Designation FROM productmaster WHERE ProductNumberInternal LIKE '6%'";
				$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
				while ( $sub_row = mysql_fetch_array($sub_result) ) {
					if ( $row["PackIn"] == $sub_row["ProductNumberInternal"] ) {
						echo $sub_row["Designation"];
					}
				}
				?>&nbsp;</TD>
			<TD STYLE="font-size:8pt"><?php echo $row['NumberOfPackages'];?>&nbsp;</TD>
			<TD STYLE="font-size:8pt"><?php echo $row['LotID'];?>&nbsp;</TD>
		</TR>
	<?php
	  } // row PAckIn != 0
	  else if ( $row['PackInID'] != "" and $row_pkin['NumberOfPackages'] >0 ) {
	  $sql_pkin="SELECT * FROM bscustomerinfopackins where PackInID in (". $row['PackInID'] .")";
	  //echo "<br />". $sql_pkin ."<br />";
	  $pkin_result = mysql_query($sql_pkin,$link) or die (mysql_error()."<br />Couldn't execute query: $sql_pkin<BR><BR>");
	  while ( $row_pkin = mysql_fetch_array($pkin_result) ) {
	   $total_packs = $total_packs + $row_pkin['NumberOfPackages'];
		?>
		<TR VALIGN=TOP>
			<TD STYLE="font-size:8pt"><?php echo $row['CustomerPONumber'];?>&nbsp;</TD>
			<TD STYLE="font-size:8pt"><?php echo $row['CustomerCodeNumber'];?>&nbsp;</TD>
			<TD STYLE="font-size:8pt"><?php
				$sub_sql = "SELECT ProductNumberInternal, Designation FROM productmaster WHERE ProductNumberInternal LIKE '6%'";
				$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
				while ( $sub_row = mysql_fetch_array($sub_result) ) {
					if ( $row_pkin['PackIn'] == $sub_row['ProductNumberInternal'] ) {
						echo $sub_row["Designation"];
					}
				}
				?>&nbsp;</TD>
			<TD STYLE="font-size:8pt"><?php echo $row_pkin['NumberOfPackages'];?>&nbsp;</TD>
			<TD STYLE="font-size:8pt"><?php echo $row_pkin['LotID'];?>&nbsp;</TD>
		</TR>
	<?php
	   } //while row_pkin
	  } //row packinid != ""
	}
	?>

		<TR VALIGN=BOTTOM>
			<TD COLSPAN=3 ALIGN=RIGHT><B STYLE="font-size:8pt">Total quantity needed:</B></TD>
			<TD COLSPAN=2><?php echo $total_packs;?></TD>
		</TR>

	</TABLE>
	<?php
} else {
	echo "<I STYLE='font-size:8pt'>No orders associated with this batch sheet</I>";
}
?>

<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0>
	<TR VALIGN=TOP>
		<TD>

<?php

echo "QC date: " . $QualityControlDate . "<BR>";
echo "QC performed by: " . $QualityControlEmployee . "<BR><BR>";
echo "Notes: " . $Notes;

?>

</TD>
<TD><IMG SRC="../images/spacer.gif" WIDTH=20 HEIGHT=1></TD>
<TD>

<?php

echo "<nobr>Manufactured: " . $DateManufactured . "</nobr><BR>";
echo "<nobr>Made by: " . $MadeBy . "</nobr><BR>";
if ( $Filtered != 1 ) {
	$Filtered = "No";
} else {
	$Filtered = "Yes";
}
echo "<nobr>Filtered: " . $Filtered . "</nobr><BR>";
echo "<nobr>Kosher: " . $Kosher . "</nobr><BR>";
echo "<nobr>Lot#: " . $abeleiLotNumber . " - " .$LotSequenceNumber . "</nobr><BR><BR>";

?>

		</TD>
	</TR>
</TABLE>

<?php




echo "Due date: " . $DueDate . "<BR><BR>";

//SELECT bsci.CustomerPONumber, cod.CustomerCodeNumber, bsci.NumberOfPackages, LotID, pm.Designation
//FROM batchsheetcustomerinfo AS bsci, productmaster AS pm, customerorderdetail AS cod, batchsheetdetailpackaginglotnumbers AS bsdpln
//WHERE pm.ProductNumberInternal = bsci.PackIn AND 
//	cod.CustomerOrderNumber = bsci.CustomerOrderNumber AND cod.CustomerOrderSeqNumber = bsci.CustomerOrderSeqNumber AND 
//	bsci.BatchSheetNumber = bsdpln.BatchSheetNumber AND bsci.CustomerOrderNumber = bsdpln.CustomerOrderNumber AND bsci.CustomerOrderSeqNumber = bsdpln.CustomerOrderSeqNumber AND 
//	bsci.BatchSheetNumber = " . $_REQUEST['bsn'];

?>

</TD></TR></TABLE>

</TD></TR></TABLE><BR><BR>

</BODY>
</HTML>