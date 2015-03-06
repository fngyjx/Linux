<?php
/* raw_material_requirements.php - reports shortage of the material in inventory
	program work flow:
	1. query batch sheet ingredients that were commited but not lot assigned
	2. Find the inventory amounts of the ingredients that were found in step 1
	3. List out the ingredient products that have less amount in inventory than batch amount.
	
	*/
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
<FORM ACTION="raw_material_requirements_new.php" METHOD="post">

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

//if ( !empty($_POST) ) {

	if ( $start_date != '' and $end_date != '' ) {
		$start_date_parts = explode("/", $start_date);
		$end_date_parts = explode("/", $end_date);
		$mysql_start_date = $start_date_parts[2] . "-" . $start_date_parts[0] . "-" . $start_date_parts[1];
		$mysql_end_date = $end_date_parts[2] . "-" . $end_date_parts[0] . "-" . $end_date_parts[1];
		$date_filter = " AND (DueDate >= '" . $mysql_start_date . "' AND DueDate <= '" . $mysql_end_date . "')";
	} else {
		$date_filter = "";
//		$error_found = true;
//		echo "<BR><B>Please enter valid dates for 'Date range'</B><BR><BR><A HREF='JavaScript:history.go(-1)'>Choose new date range ></A>";
//		die();
	}

	if ( !$error_found ) ?>

		<B>Committed Batch Sheets Raw Material Requirements</B><BR><BR>

		<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=1 BORDERCOLOR="#999999">

		<?php

	$sql = "SELECT productmaster.Natural_OR_Artificial, productmaster.Designation, productmaster.ProductType,
	productmaster.Kosher, productmaster.UnitOfMeasure, batchsheetmaster.BatchSheetNumber, ProductNumberExternal,
	productmaster.ProductNumberInternal, productmaster.OrderTriggerAmount,
	sum(QuantityConvert(NetWeight,TotalQuantityUnitType,'grams')*Percentage*0.01/Yield*NumberOfTimesToMake) as NetWeight,
	min(DueDate) as DueDate, CustomerID,
	batchsheetcustomerinfo.CustomerPONumber
	FROM batchsheetmaster
	LEFT JOIN batchsheetdetail USING(BatchSheetNumber)
	LEFT JOIN batchsheetcustomerinfo USING(BatchSheetNumber)
	LEFT JOIN productmaster ON (productmaster.ProductNumberInternal = batchsheetdetail.IngredientProductNumber)
	WHERE productmaster.ProductNumberInternal IS NOT NULL AND productmaster.ProductNumberInternal NOT LIKE ('4%') 
	AND ( CommitedToInventory = 1 AND Manufactured = 0 )
	AND productmaster.ProductNumberInternal not like '10829%' " . $date_filter . "
	GROUP BY productmaster.ProductNumberInternal
	ORDER BY Designation, productmaster.ProductNumberInternal, DueDate";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	print_results($result);
	$sql = "SELECT productmaster.Natural_OR_Artificial, productmaster.Designation, productmaster.ProductType,
	productmaster.Kosher, productmaster.UnitOfMeasure, productmaster.OrderTriggerAmount, batchsheetmaster.BatchSheetNumber, 
	ProductNumberExternal, productmaster.ProductNumberInternal, min(DueDate), CustomerID, 
	batchsheetcustomerinfo.CustomerPONumber, sum(NumberOfPackages) as NumberOfPackages
	FROM batchsheetmaster
	LEFT JOIN batchsheetcustomerinfo USING(BatchSheetNumber)
	LEFT JOIN productmaster ON (productmaster.ProductNumberInternal = batchsheetcustomerinfo.PackIn)
	WHERE productmaster.ProductNumberInternal IS NOT NULL " . $date_filter . "
	AND ( CommitedToInventory = 1 AND Manufactured = 0 )
	GROUP BY productmaster.ProductNumberInternal
	ORDER BY Designation, productmaster.ProductNumberInternal, DueDate";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	print_results($result);
//}
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
	while ( $row = mysql_fetch_array($result) ) {
		//$PercentageComplete = ($c/$i) * 100;
		//$running="Running..". str_repeat("-",$PercentageComplete) ." ". number_format($PercentageComplete,0)."%";
		
		$c++;
		
		$ProductNumberExternal = $row['ProductNumberExternal'];
		$ProductNumberInternal = $row['ProductNumberInternal'];

		$customer_id = $row['CustomerID'];
		$NetWeight = $row['NetWeight'];
		$ordertiggeramount=$row['OrderTiggerAmount'];
		//$TotalQuantityUnitType = $row['TotalQuantityUnitType'];
		$TotalQuantityUnitType = $row['UnitOfMeasure'];
		$CustomerPONumber = $row['CustomerPONumber'];
		
		$gross_weight = $NetWeight;

		if ( $row['DueDate'] != '' ) {
			$DueDate = date("n/j/Y", strtotime($row['DueDate']));
		} else {
			$DueDate = '';
		}

		if ( $customer_id != '' ) {
			$sql = "SELECT name FROM customers WHERE customer_id = " . $customer_id;
			$result_customer = mysql_query($sql, $link);
			$row_customer = mysql_fetch_array($result_customer);
			$customer = $row_customer['name'];
		} else {
			$customer = '';
		}

		$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").
		$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").
		("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");

		$UnitOfMeasure = $row['UnitOfMeasure'];

		if ("6" == substr($ProductNumberInternal,0,1)) {
			$BatchAmount = $row[NumberOfPackages];
		}
		else{
			$BatchAmount = QuantityConvert($gross_weight,'grams',$UnitOfMeasure);
		}

		$sql = "Select DISTINCT ProductTotal(inventorymovements.ProductNumberInternal,'C',NULL) as total, ".
			"COALESCE((".
				"SELECT SUM(QuantityConvert( (TotalQuantityExpected), UnitOfMeasure, 'grams')) ".
				"FROM purchaseorderdetail WHERE ProductNumberInternal = productmaster.ProductNumberInternal AND (`Status` = 'O' OR `Status` = 'P')".
			"),0) as ordered, ".
			"ProductTotal(inventorymovements.ProductNumberInternal,'P',NULL) as committed, ".
			"ProductTotal(inventorymovements.ProductNumberInternal,NULL, NULL) as net, ".
			"productmaster.*, externalproductnumberreference.ProductNumberExternal as external ".
			"FROM productmaster ".
			"LEFT JOIN inventorymovements ON (inventorymovements.ProductNumberInternal = productmaster.ProductNumberInternal) ".
			"LEFT JOIN lots ON (inventorymovements.LotID = lots.ID) ".
			"LEFT JOIN externalproductnumberreference ON (externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal) 
			WHERE productmaster.ProductNumberInternal=" . $ProductNumberInternal;
			
		$result_vend = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( mysql_num_rows($result_vend) > 0 ) {
			$row_inv = mysql_fetch_array($result_vend);
			//$Inventory = round(QuantityConvert(($row_inv['total'] - $row_inv['committed']), "grams", $TotalQuantityUnitType),2);
			if ("6" == substr($ProductNumberInternal,0,1)) {
				$Inventory = $row_inv['total'];
				$AmountOnOrder = $row_inv['ordered'];
				$AmountCommitted = $row_inv['committed'];
				$NetInventory = $row_inv['net'];
			}
			else {
				$Inventory = QuantityConvert(($row_inv['total']), "grams", $TotalQuantityUnitType);
				$AmountOnOrder = QuantityConvert($row_inv['ordered'], "grams", $TotalQuantityUnitType);
				$AmountCommitted = QuantityConvert($row_inv['committed'], "grams", $TotalQuantityUnitType);
				$NetInventory = QuantityConvert($row_inv['net'], "grams", $TotalQuantityUnitType);
			}
		} else {
			$Inventory = 0;
			$AmountOnOrder = 0;
			$AmountCommitted = 0;
			$NetInventory = 0;
			//	$units = "grams";
		}

		$net_inventory = $Inventory + $AmountCommitted;
		
		if ( ($net_inventory + $AmountOnOrder) <= ($ordertriggeramount + 0) ) {
			
		echo "<TR BGCOLOR='black'>";
		echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Int#</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Designation</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white' ALIGN=RIGHT>Qty</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white' ALIGN=RIGHT>Units</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white' ALIGN=RIGHT>Due date</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Customer</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white' ALIGN=RIGHT>PO#</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Final product</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold;color:white'>Storage locations</TD>\n";
		echo "</TR>";

		echo "<TR VALIGN=TOP BGCOLOR='#DFDFDF'>";
		echo "<TD STYLE='font-size:7pt'>";
		echo $ProductNumberInternal;
		echo "</TD>";
		echo "<TD STYLE='font-size:7pt'>";
		echo $ProductDesignation;
		echo "</TD>";
		echo "<TD STYLE='font-size:7pt' ALIGN=RIGHT>" . number_format($BatchAmount, 2) . "</TD>";
		echo "<TD STYLE='font-size:7pt' ALIGN=RIGHT>" . $UnitOfMeasure . "</TD>";
		echo "<TD STYLE='font-size:7pt' ALIGN=RIGHT>" . $DueDate . "</TD>";
		echo "<TD STYLE='font-size:7pt'>" . $customer . "</TD>";
		echo "<TD STYLE='font-size:7pt' ALIGN=RIGHT>" . $CustomerPONumber . "</TD>";
		echo "<TD STYLE='font-size:7pt'>" . $ProductNumberExternal . "</TD>";
		echo "<TD STYLE='font-size:7pt'>";

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
		echo "</TD>";
		echo "</TR>";
			

	
	//	if ( ($ProductNumberInternal != $old_ProductNumberInternal) and $old_ProductNumberInternal != '' ) {
		echo "<TR>";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Total current inventory:</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT>" . number_format($Inventory,2) . "</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT BGCOLOR='#DFDFDF'>". $TotalQuantityUnitType ."</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=5 BGCOLOR='#DFDFDF'>&nbsp;</TD>\n";
		echo "</TR>";
		echo "<TR>";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Amount committed:</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT>" . number_format($AmountCommitted,2) . "</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT BGCOLOR='#DFDFDF'>". $TotalQuantityUnitType ."</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=5 BGCOLOR='#DFDFDF'>&nbsp;</TD>\n";
		echo "</TR>";
		echo "<TR>";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Net inventory:</TD>\n";

		if ( $net_inventory < (0 + $ordertriggeramount) ) {
			$text_color = ";color:red";
		} else {
			$text_color = "";
		}
		echo "<TD STYLE='font-size:7pt;font-weight:bold" . $text_color . "' ALIGN=RIGHT>" . number_format(($net_inventory),2) . "</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=6 BGCOLOR='#DFDFDF'>&nbsp;</TD>\n";
		echo "</TR>";
		
		echo "<TR>";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Amount on order:</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT>" . number_format($AmountOnOrder,2) . "</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' ALIGN=RIGHT BGCOLOR='#DFDFDF'>". $TotalQuantityUnitType ."</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=5 BGCOLOR='#DFDFDF'>&nbsp;</TD>\n";
		echo "</TR>";
		echo "<TR>";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=2 ALIGN=RIGHT>Total inventory with all outstanding PO's:</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold";
		if ( ($net_inventory + $AmountOnOrder) <= ( 0  + $ordertriggeramount) )
			echo $text_color;
		echo "' ALIGN=RIGHT>" . number_format(($net_inventory + $AmountOnOrder),2) ."</TD>\n";
		echo "<TD STYLE='font-size:7pt;font-weight:bold' COLSPAN=6 BGCOLOR='#DFDFDF'>&nbsp;</TD>\n";
		echo "</TR>";

		echo "<TR>";
		echo "<TD COLSPAN=9><IMG SRC='images/spacer.gif' WIDTH='1' HEIGHT='3'></TD>";
		echo "</TR>";
	}
	}
}
?>