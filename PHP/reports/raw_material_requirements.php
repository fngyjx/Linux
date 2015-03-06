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
<FORM ACTION="raw_material_requirements.php" METHOD="post">

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

// if ( !empty($_POST) ) {

	if ( $start_date != '' and $end_date != '' ) {
		$start_date_parts = explode("/", $start_date);
		$end_date_parts = explode("/", $end_date);
		$mysql_start_date = $start_date_parts[2] . "-" . $start_date_parts[0] . "-" . $start_date_parts[1];
		$mysql_end_date = $end_date_parts[2] . "-" . $end_date_parts[0] . "-" . $end_date_parts[1];
		$date_filter = " AND (DueDate >= '" . $mysql_start_date . "' AND DueDate <= '" . $mysql_end_date . "')";
	} else if ( $start_date != '' ) {
		$start_date_parts = explode("/", $start_date);
		$mysql_start_date = $start_date_parts[2] . "-" . $start_date_parts[0] . "-" . $start_date_parts[1];
		$date_filter = " AND DueDate >= '" . $mysql_start_date . "'";
	}
	else { 
		$date_filter = "";
	}

	if ( !$error_found ) ?>

		<B>Committed Batch Sheets Raw Material Requirements</B>&nbsp;&nbsp;(<?php echo $mysql_start_date ."-" .$mysql_end_date;?>)<BR><BR>

		<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=1 BORDERCOLOR="#999999">

		<?php

	$sql = "SELECT DISTINCT productmaster.Natural_OR_Artificial, productmaster.Designation, productmaster.ProductType, productmaster.Kosher, 
	productmaster.UnitOfMeasure, batchsheetmaster.BatchSheetNumber, DueDate, ProductNumberExternal, 
	productmaster.ProductNumberInternal, TotalQuantityUnitType, Percentage, NetWeight, Yield, 
	CustomerID, batchsheetcustomerinfo.CustomerOrderNumber, OrderTriggerAmount,NumberOfTimesToMake
	FROM batchsheetmaster
	INNER JOIN batchsheetdetail USING(BatchSheetNumber)
	LEFT JOIN batchsheetcustomerinfo USING(BatchSheetNumber)
	LEFT JOIN inventorymovements ON inventorymovements.TransactionNumber=batchsheetdetail.InventoryTransactionNumber
	INNER JOIN productmaster ON (productmaster.ProductNumberInternal = batchsheetdetail.IngredientProductNumber)
	WHERE productmaster.ProductNumberInternal IS NOT NULL AND productmaster.ProductNumberInternal NOT LIKE ('4%') 
	  AND productmaster.ProductNumberInternal NOT LIKE ('6%') 
	  AND Manufactured=0 AND NetWeight>0 AND NetWeight is not null AND Yield >0 AND Yield is not null
	  AND NumberOfTimesToMake > 0 
	  AND inventorymovements.movementstatus='P' AND (CustomerOrderNumber is not null or TransactionNumber is not null)
	  AND productmaster.ProductNumberInternal not like '10829%' " . $date_filter . "
	ORDER BY productmaster.Designation, ProductNumberInternal, NetWeight*Percentage*NumberOfTimesToMake DESC, DueDate";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	print_results($result);
	$sql = "SELECT BatchSheetNumber, PackInID FROM batchsheetcustomerinfo WHERE PackInID is not null";
	$result = mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL $sql <br />"); 
	$packinids="";
	$batchsheetnumbers="";
	$pmpackins="";
	if ( mysql_num_rows($result) > 0 ) { 
		while ( $row = mysql_fetch_array($result) ) {
			$packinids .= $row['PackInID'].",";
			$batchsheetnumbers .= $row['BatchSheetNumber'] . ",";
		}
	}
	$numberofpackages="NumberOfPackages";
	$joinbscustomerinfopackins="";
	$pmToPackins = "";
	if ( $packinids != "" ) {
		$packinids=substr($packinids, 0, -1);
		$batchsheetnumbers=substr($batchsheetnumbers,0,-1);
		$numberofpackages="if (batchsheetcustomerinfo.NumberOfPackages is null, bscustomerinfopackins.NumberOfPackages, batchsheetcustomerinfo.NumberOfPackages) as NumberOfPackages";
		$joinbscustomerinfopackins=" LEFT JOIN bscustomerinfopackins ON (bscustomerinfopackins.PackInID in (".$packinids." ) AND batchsheetcustomerinfo.BatchSheetNumber in (".$batchsheetnumbers.")) ";
		$pmToPackins = " OR	productmaster.ProductNumberInternal = bscustomerinfopackins.PackIn ";
	}	
	
	$sql = "SELECT productmaster.Natural_OR_Artificial, productmaster.Designation, productmaster.ProductType, productmaster.Kosher, 
	  productmaster.UnitOfMeasure, batchsheetmaster.BatchSheetNumber, DueDate, ProductNumberExternal,NumberOfTimesToMake,
	  productmaster.ProductNumberInternal, CustomerID, batchsheetcustomerinfo.CustomerOrderNumber, 
	  batchsheetcustomerinfo.CustomerOrderSeqNumber, ". $numberofpackages .",
	  OrderTriggerAmount, NetWeight, Yield
	FROM batchsheetmaster
	INNER JOIN batchsheetcustomerinfo USING(BatchSheetNumber) ". $joinbscustomerinfopackins ." 	
	INNER JOIN productmaster ON ( productmaster.ProductNumberInternal = batchsheetcustomerinfo.PackIn ".$pmToPackins ." )
	WHERE productmaster.ProductNumberInternal IS NOT NULL AND Manufactured=0 
	ORDER BY Designation, productmaster.ProductNumberInternal,NumberOfPackages DESC, DueDate";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	print_results($result); 

?>
</TD></TR></TABLE><BR><BR>

</BODY>
</HTML>
<?php
function print_results($result) {
	global $link;
	$old_ProductNumberInternal = '';
	$ProductNumberInternal = '';
	$c = 0;
	$i = mysql_num_rows($result);
	$totalbsamt=0;
	while ( $row = mysql_fetch_array($result) ) {
		$c++;
	//echo "<br /> $c <br />";
		$ProductNumberExternal = $row['ProductNumberExternal'];
		$ProductNumberInternal = $row['ProductNumberInternal'];
		
		if ( ($ProductNumberInternal != $old_ProductNumberInternal) and $old_ProductNumberInternal != '' and $totalbsamt > 0) {
			getInventory($old_ProductNumberInternal,$UnitOfMeasure, $Inventory,$AmountOnOrder,$AmountCommitted,$NetInventory,$totalbsamt);
		
			echo "<TR>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Total current inventory:</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT>" . number_format($Inventory,2) . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT BGCOLOR='#DFDFDF'>" . $UnitOfMeasure . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=5 BGCOLOR='#DFDFDF'>&nbsp;</TD>";
			echo "</TR>";
			echo "<TR>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Amount committed:</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT>" . number_format($AmountCommitted,2) . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=6 BGCOLOR='#DFDFDF'>&nbsp;</TD>";
			echo "</TR>";
			echo "<TR>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Net inventory:</TD>";
			//$net_inventory = $Inventory + $AmountCommitted;
			if ( $NetInventory < 0 ) {
				$text_color = ";color:red";
			} else {
				$text_color = "";
			}
			echo "<TD STYLE='font-size:7pt;font-weight:bold" . $text_color . "' ALIGN=RIGHT>" . number_format(($NetInventory),2) . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=6 BGCOLOR='#DFDFDF'>&nbsp;</TD>";
			echo "</TR>";
			echo "<TR>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Amount on order:</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT>" . number_format($AmountOnOrder,2) . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT BGCOLOR='#DFDFDF'>" . $UnitOfMeasure . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=5 BGCOLOR='#DFDFDF'>&nbsp;</TD>";
			echo "</TR>";
			echo "<TR>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Total inventory with all outstanding PO's:</TD>";
			$outstandinginv=($Inventory + $AmountOnOrder - $totalbsamt);
			
			if ( $outstandinginv <= $ordertiggeramt ) {
				$text_color = ";color:red";
			} else {
				$text_color = "";
			}
			
			echo "<TD STYLE='font-size:7pt;font-weight:bold" .$text_color ."' ALIGN=RIGHT>" . number_format($outstandinginv,2) . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=6 BGCOLOR='#DFDFDF'>&nbsp;</TD>";
			echo "</TR>";
			echo "<TR>";
			echo "<TD COLSPAN=9><IMG SRC='images/spacer.gif' WIDTH='1' HEIGHT='3'></TD>";
			echo "</TR>";
			$totalbsamt=0;
		}

		$ordertiggeramt= $row['OrderTriggerAmount'] == "" ? 0 : $row['OrderTriggerAmount'] ;
		$customer_id = $row['CustomerID'];
		$NetWeight = $row['NetWeight'];
	    
		$CustomerOrderNumber = $row['CustomerOrderNumber'];
		$Yield = $row['Yield'];

		if ( $NetWeight != 0 and $Yield != 0 ) {
			$gross_weight = $NetWeight/$Yield;
		} else {
			$gross_weight = $NetWeight;
		}

		if ( $row['DueDate'] != '' ) {
			$DueDate = date("n/j/Y", strtotime($row['DueDate']));
		} else {
			$DueDate = '';
		}

		//if ( $customer_id != '' ) {
		//	$sql = "SELECT name FROM customers WHERE customer_id = " . $customer_id;
		//	$result_customer = mysql_query($sql, $link);
		//	$row_customer = mysql_fetch_array($result_customer);
		$customer = "";//$row_customer['name'];
		$CustomerPONumber="";
		//} else 
		if ( $CustomerOrderNumber != "" ) {
			$sql = "SELECT distinct name, CustomerPONumber FROM customerordermaster 
				INNER JOIN customers ON customers.customer_id=customerordermaster.CustomerID
				WHERE OrderNumber = '" .$CustomerOrderNumber ."'";
			$result_cstinfo=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
			$row_cstinfo=mysql_fetch_array($result_cstinfo);
			
			$customer = $row_cstinfo['name'];
			$CustomerPONumber = $row_cstinfo['CustomerPONumber'];
			
		}

		$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");

		$UnitOfMeasure = $row['UnitOfMeasure'];

		if ("6" == substr($ProductNumberInternal,0,1)) {
			$BatchAmount = $row['NumberOfPackages'];// * $row['NumberOfTimesToMake'];
			$totalbsamt += $BatchAmount;
			$TotalQuantityUnitType = "pcs";
		}
		else{
			$BatchAmount = $gross_weight * ($row['Percentage']*0.01) * $row['NumberOfTimesToMake'];
			$TotalQuantityUnitType = $row['TotalQuantityUnitType'];
			$totalbsamt += QuantityConvert($BatchAmount,$TotalQuantityUnitType,'grams');
			//echo "<br /> totalbsamt= $totalbsamt <br />";
		}
	   // echo "batchamount = $BatchAmount<br />";
		if ( $BatchAmount == 0 ) {
		//	echo "<br /> $old_ProductNumberInternal, ";
			$old_ProductNumberInternal = $row['ProductNumberInternal'];
		//	echo "$old_ProductNumberInternal <br />";
			continue;
		}
		
		if ( $BatchAmount < 0.01 ) {
			$BatchAmount=QuantityConvert($BatchAmount,$TotalQuantityUnitType,'grams');
			$TotalQuantityUnitType='grams';
		}
		
		if ( $ProductNumberInternal != $old_ProductNumberInternal ) {
			echo "<TR BGCOLOR='black'>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Int#</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Designation</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white' ALIGN=RIGHT>Qty</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white' ALIGN=RIGHT>Units</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white' ALIGN=RIGHT>Due date</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Customer</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white' ALIGN=RIGHT>PO#</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Final product</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Storage locations</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>BatchSheet#</TD>";
			echo "</TR>";
		}

		if ( $ProductNumberInternal != $old_ProductNumberInternal ) {
			echo "<TR VALIGN=TOP BGCOLOR='#DFDFDF'>";
		} else {
			echo "<TR VALIGN=TOP>";
		}
		echo "<TD STYLE='font-size:7pt'>";
		if ( $ProductNumberInternal != $old_ProductNumberInternal ) {
			echo $ProductNumberInternal;
		}
		echo "</TD>";
		echo "<TD STYLE='font-size:7pt'>";
		if ( $ProductNumberInternal != $old_ProductNumberInternal ) {
			echo $ProductDesignation;
		}
		echo "</TD>";
		echo "<TD STYLE='font-size:7pt' ALIGN=RIGHT>" . number_format($BatchAmount, 2) . "</TD>";
		echo "<TD STYLE='font-size:7pt' ALIGN=RIGHT>" . $TotalQuantityUnitType . "</TD>";
		echo "<TD STYLE='font-size:7pt' ALIGN=RIGHT>" . $DueDate . "</TD>";
		echo "<TD STYLE='font-size:7pt'>" . $customer . "</TD>";
		echo "<TD STYLE='font-size:7pt' ALIGN=RIGHT>" . $CustomerPONumber . "</TD>";
		echo "<TD STYLE='font-size:7pt'>" . $ProductNumberExternal . "</TD>";
		echo "<TD STYLE='font-size:7pt'>";
		
		if ( $ProductNumberInternal != $old_ProductNumberInternal ) {
			$sql  = "SELECT lots.StorageLocation as storage_location";
			$sql .= " FROM inventorymovements LEFT JOIN lots ON (inventorymovements.LotID = lots.ID) ";
			if ( "2" != substr($ProductNumberInternal,0,1) ) { // if not a flavor
				$sql .= "LEFT JOIN receipts ON ( receipts.LotID  = inventorymovements.LotID ) ".
				"LEFT JOIN purchaseorderdetail ON ( purchaseorderdetail.ID = receipts.PurchaseOrderID ) ".
				"LEFT JOIN purchaseordermaster ON ( purchaseordermaster.PurchaseOrderNumber = purchaseorderdetail.PurchaseOrderNumber ) ".
				"LEFT JOIN vendors ON ( vendors.vendor_id = purchaseordermaster.VendorID ) ".
				"LEFT JOIN vendorproductcodes ON ( vendorproductcodes.VendorID=purchaseordermaster.VendorID AND vendorproductcodes.ProductNumberInternal=inventorymovements.ProductNumberInternal )";
			}
			$sql .= "WHERE inventorymovements.ProductNumberInternal=$ProductNumberInternal AND NOT inventorymovements.LotID IS NULL AND MovementStatus='C'";
			$result_locations = mysql_query($sql, $link);
			$locs[] = '';
			$locations = '';
			while ( $row_locations = mysql_fetch_array($result_locations) ) {
				if ( trim($row_locations['storage_location']) != '' ) {
					if ( !in_array($row_locations['storage_location'], $locs) ) {
						$locs[] = $row_locations['storage_location'];
					}
				}
			}
			$locations = join("; ", $locs);
			$locs = '';
			echo substr($locations, 2);
		} else {
			echo "&nbsp;";
		}

		echo "</TD>";
		echo "<TD><A href='../customers_batch_sheets.php?bsn=".$row['BatchSheetNumber']."' target='_blank'>".$row['BatchSheetNumber']."</TD>";
		echo "</TR>";

		$old_ProductNumberInternal = $row['ProductNumberInternal'];

		if ( $i == $c ) {
			getInventory($ProductNumberInternal,$UnitOfMeasure, $Inventory,$AmountOnOrder,$AmountCommitted,$NetInventory,$totalbsamt);
			echo "<TR>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Total current inventory:</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT>" . number_format($Inventory,2) . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT BGCOLOR='#DFDFDF'>"  . $UnitOfMeasure . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=5 BGCOLOR='#DFDFDF'>&nbsp;</TD>";
			echo "</TR>";
			echo "<TR>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Amount committed:</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT>" . number_format($AmountCommitted,2) . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=6 BGCOLOR='#DFDFDF'>&nbsp;</TD>";
			echo "</TR>";
			echo "<TR>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Net inventory:</TD>";
			$net_inventory = $Inventory + $AmountCommitted;
			if ( $NetInventory < 0 ) {
				$text_color = ";color:red";
			} else {
				$text_color = "";
			}
			echo "<TD STYLE='font-size:7pt;font-weight:bold" . $text_color . "' ALIGN=RIGHT>" . number_format(($NetInventory),2) . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=6 BGCOLOR='#DFDFDF'>&nbsp;</TD>";
			echo "</TR>";
			echo "<TR>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Amount on order:</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT>" . number_format($AmountOnOrder,2) . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT BGCOLOR='#DFDFDF'>"  . $UnitOfMeasure ." </TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=5 BGCOLOR='#DFDFDF'>&nbsp;</TD>";
			echo "</TR>";
			echo "<TR>";
			$outstandinginv=($Inventory + $AmountOnOrder - $totalbsamt);
			// echo " net=$net_inventory  outstandinginv = $outstandinginv ordered=$AmountOnOrder  TotalBSAmt=$totalbsamt AmountCommited = $AmountCommitted <br />";
			if ( $outstandinginv <= $ordertiggeramt ) {
				$text_color = ";color:red";
			} else {
				$text_color = "";
			}
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Total inventory with all outstanding PO's:</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold". $text_color."' ALIGN=RIGHT>" . number_format($outstandinginv,2) . "</TD>";
			echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=6 BGCOLOR='#DFDFDF'>&nbsp;</TD>";
			echo "</TR>";
		}
	}
}

function getInventory($ProductNumberInternal,$UnitOfMeasure, &$Inventory,&$AmountOnOrder,&$AmountCommitted,&$NetInventory,&$totalbsamt) {
	global $link;
		$sql = "Select DISTINCT ProductTotal(inventorymovements.ProductNumberInternal,'C',NULL) as total, ".
			"COALESCE((".
				"SELECT SUM(QuantityConvert( (TotalQuantityExpected), UnitOfMeasure, 'grams')) ".
				"FROM purchaseorderdetail WHERE ProductNumberInternal = productmaster.ProductNumberInternal AND (`Status` = 'O' OR `Status` = 'P')".
			"),0) as ordered, ".
			"ProductTotal(inventorymovements.ProductNumberInternal,'P',NULL) as committed, ".
			"ProductTotal(inventorymovements.ProductNumberInternal,NULL, NULL) as net ".
			"FROM productmaster ".
			"INNER JOIN inventorymovements ON (inventorymovements.ProductNumberInternal = productmaster.ProductNumberInternal) ".
			"WHERE productmaster.ProductNumberInternal=" . $ProductNumberInternal;
			//echo "<br /> $sql <br />";
		$result_vend = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( mysql_num_rows($result_vend) > 0 ) {
			$row_inv = mysql_fetch_array($result_vend);
			if ("6" == substr($ProductNumberInternal,0,1)) {
					$Inventory = $row_inv['total'];
					$AmountOnOrder = $row_inv['ordered'];
					$AmountCommitted = $row_inv['committed'];
					$NetInventory = $row_inv['net'];
				
				}
				else {
					$Inventory = QuantityConvert(($row_inv['total']), "grams", $UnitOfMeasure);
					$AmountOnOrder = QuantityConvert($row_inv['ordered'], "grams", $UnitOfMeasure);
					$AmountCommitted = QuantityConvert($row_inv['committed'], "grams", $UnitOfMeasure);
					$NetInventory = QuantityConvert($row_inv['net'], "grams", $UnitOfMeasure);
					$totalbsamt = QuantityConvert($totalbsamt,'grams',$UnitOfMeasure);
				
				}
				
			} else {
				$Inventory = 0;
				$AmountOnOrder = 0;
				$AmountCommitted = 0;
				$NetInventory = 0;
				$totalbsamt = QuantityConvert($totalbsamt,'grams',$UnitOfMeasure);
			}
				
}
?>