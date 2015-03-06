<?php

include('inc_ssl_check.php');
session_start();

$debug = 0;

if($debug == 0 ) {
if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ONLY ADMIN AND QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 5 and $rights != 6 ) {
	header ("Location: login.php?out=1");
	exit;
}
}
$note="";
if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');
include("inc_header.php");

?>

<?php 

//foreach ( array_keys($_REQUEST) as $key ) {
//	echo "REQUEST['" .$key ."']=" . $_REQUEST[$key] .'<br />';
//}

$action = "";
$ProductNumberInternal = "";
$ProductNumberExternal = "";
$Vendor = "";
$Designation = "";
$Keywords = "";
$lot_id = "";
$lot_number="";
$lot_sequence="";
$error_message="";

if ( isset($_REQUEST['search'])) 
	$submit_request = "search";
elseif ( isset($_REQUEST['lists']))
	$submit_request = "lists";
else 
	$submit_request = "";

	
if (isset($_REQUEST['action'])) { $action = $_REQUEST['action']; }
if (isset($_REQUEST['ProductNumberInternal'])) {
	$tmpArr = explode("&",$_REQUEST['ProductNumberInternal']);
	$ProductNumberInternal = $tmpArr[0]; }
if (isset($_REQUEST['ProductNumberExternal'])) { 
	$tmpArr = explode("&", $_REQUEST['ProductNumberExternal']);
	$ProductNumberExternal = $tmpArr[0]; }
if (isset($_REQUEST['Vendor'])) { $Vendor = $_REQUEST['Vendor']; }
if (isset($_REQUEST['Designation'])) { $Designation = $_REQUEST['Designation']; }
if (isset($_REQUEST['Keywords'])) { $Keywords = $_REQUEST['Keywords']; }
if (isset($_REQUEST['lot_id'])) { $lot_id = $_REQUEST['lot_id']; }


function product_header($ProductNumberInternal,&$unitOfMeasure) {
	global $link;
	$sql  = "SELECT productmaster.*, ".
		"ProductTotal(productmaster.ProductNumberInternal,'C',NULL) as total, ".
		"ProductTotal(productmaster.ProductNumberInternal,'P',NULL) as committed, ".
		"ProductTotal(productmaster.ProductNumberInternal,NULL, NULL) as net ".
		"FROM productmaster ".
		"LEFT JOIN externalproductnumberreference ON (externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal) ".
		"WHERE productmaster.ProductNumberInternal=$ProductNumberInternal";
		
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR></tbody></table>");
	$row = mysql_fetch_array($result);
	$description = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
	$fema_nbr=$row['FEMA_NBR'];
	$unitOfMeasure = $row['UnitOfMeasure'];
	
	$sql_ord = "SELECT SUM(TotalQuantityExpected) as ordered,UnitOfMeasure from purchaseorderdetail 
				JOIN purchaseordermaster USING (PurchaseOrderNumber) 
				WHERE ProductNumberInternal='". $ProductNumberInternal ."'" .
				"AND status in ('P','O') AND ( purchaseordermaster.PurchaseOrderType = 'Material' OR purchaseordermaster.PurchaseOrderType IS NULL) ";
	$result_ord = mysql_query($sql_ord,$link) or die(mysql_error() ." Failed to execute SQL: $sql_ord <br />");
	$row_ord=mysql_fetch_array($result_ord);
		$total_g=$row['total']; 
		$total_css = 0>$total_g ? ";color:red;font-weight:bold;":"";
		$ordered_g=QuantityConvert($row_ord['ordered'],$row_ord['UnitOfMeasure'],'grams');
		$ordered_css = 0>$ordered_g ? ";color:red;font-weight:bold;":"";
		$committed_g=$row['committed'];
		$committed_css = 0>$committed_g ? ";color:blue;font-weight:bold;":"";
		$net_g=$row['net'];
		$net_css = QuantityConvert($row['OrderTriggerAmount'],$row['UnitOfMeasure'],'grams')>$net_g ? ";color:red;font-weight:bold;":"";
	if ( "6" != substr($ProductNumberInternal,0,1)) {
		$total_lbs=QuantityConvert($total_g,'grams','lbs');
		$ordered_lbs=QuantityConvert($ordered_g,'grams','lbs');
		$committed_lbs=QuantityConvert($committed_g,'grams','lbs');
		$net_lbs=QuantityConvert($net_g,'grams','lbs');
		$total_kg=QuantityConvert($total_g,'grams','kg');
		$ordered_kg=QuantityConvert($ordered_g,'grams','kg');
		$committed_kg=QuantityConvert($committed_g,'grams','kg');
		$net_kg=QuantityConvert($net_g,'grams','kg');
	} 
?>
	<TABLE CELLSPACING="0" CELLPADDING="0" style="border:none;width:850px">
		<thead style="text-align:center">
			<th style="border:solid 1px; width:200px"><B>Description</B></th>
			<th style="border:solid 1px; border-left:none; width:80px" ><B>Internal #</B></th>
			<th style="border:solid 1px; border-left:none;width:80px" ><B>FEMA #</B></th>
			<th style="border:solid 1px; border-left:none;width:140px" ><B>Order Trigger Amt.</B></th>
<?php if ( "6" == substr($ProductNumberInternal,0,1)) { ?>
			<th style="border:solid 1px; border-left:none;width:85px;" ><B>Total All Lots (ps)</B></th>
			<th style="border:solid 1px; border-left:none;width:85px;" ><B>On Order (ps)</B></th>
			<th style="border:solid 1px; border-left:none;width:85px;" ><B>Pending Committed (ps)</B></th>
			<th style="border:solid 1px; border-left:none;width:90px;" ><B>Net Amount (ps)</B></th>

<?php } elseif ( $unitOfMeasure == "grams") { ?>
			<th style="border:solid 1px; border-left:none;width:85px;" ><B>Total All Lots (g)</B></th>
			<th style="border:solid 1px; border-left:none;width:85px;" ><B>On Order (g)</B></th>
			<th style="border:solid 1px; border-left:none;width:85px;" ><B>Pending Committed (g)</B></th>
			<th style="border:solid 1px; border-left:none;width:90px;" ><B>Net Amount (g)</B></th>
<?php } elseif ( $unitOfMeasure == "lbs") { ?>
			<th style="border:solid 1px; border-left:none;width:85px;" ><B>Total All Lots (lbs)</B></th>
			<th style="border:solid 1px; border-left:none;width:85px;" ><B>On Order (lbs)</B></th>
			<th style="border:solid 1px; border-left:none;width:85px;" ><B>Pending Committed (lbs)</B></th>
			<th style="border:solid 1px; border-left:none;width:90px;" ><B>Net Amount (lbs)</B></th>
<?php } elseif ( $unitOfMeasure == "kg") { ?>
			<th style="border:solid 1px; border-left:none;width:85px;" ><B>Total All Lots (kg)</B></th>
			<th style="border:solid 1px; border-left:none;width:85px;" ><B>On Order (kg)</B></th>
			<th style="border:solid 1px; border-left:none;width:85px;" ><B>Pending Committed (kg)</B></th>
			<th style="border:solid 1px; border-left:none;width:90px;" ><B>Net Amount (kg)</B></th>			
<?php } ?>
		</thead>
		<tbody>
			<TR BGCOLOR="#F3E7FD" VALIGN=TOP>
				<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;"><?php echo $description ?></TD>
				<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;" ><?php echo $ProductNumberInternal ?></TD>
				<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;" ><?php echo $fema_nbr ?></TD>
				<TD style="border-right:solid 1px black; padding:0 0 0 0;width:140px;text-align:left top;"><NOBR><iframe id="trgamt_<?php echo $i;?>" name="trgamt_<?php echo $i;?>" width="55px" height="25px" align="left" valign="top" frameborder="0" scrolling="no" src="setOrderTriggerAmt.php?productnumberinternal=<?php echo $ProductNumberInternal;?>"></iframe>&nbsp; <?php echo $row['UnitOfMeasure'];?></NOBR></TD>
<?php if ( "6" == substr($ProductNumberInternal,0,1)) { ?>

				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $total_css ?>"><?php echo number_format($total_g,2) ?></TD>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $ordered_css ?>"><?php echo number_format($ordered_g,2) ?></TD>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $committed_css ?>"><?php echo number_format($committed_g,2) ?></TD>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $net_css ?>"><?php echo number_format($net_g,2) ?></TD>
<?php } elseif ( $unitOfMeasure == "grams") { ?>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $total_css ?>"><?php echo number_format($total_g,2) ?></TD>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $ordered_css ?>"><?php echo number_format($ordered_g,2) ?></TD>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $committed_css ?>"><?php echo number_format($committed_g,2) ?></TD>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $net_css ?>"><?php echo number_format($net_g,2) ?></TD>
<?php } elseif ( $unitOfMeasure == "lbs") { ?>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $total_css ?>"><?php echo number_format($total_lbs,2) ?></TD>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $ordered_css ?>"><?php echo number_format($ordered_lbs,2) ?></TD>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $committed_css ?>"><?php echo number_format($committed_lbs,2) ?></TD>
				<TD style="padding:0 .5em 0 .5em;text-align:right<?php echo $net_css ?>"><?php echo number_format($net_lbs,2) ?></TD>
<?php } elseif ( $unitOfMeasure == "kg") { ?>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $total_css ?>"><?php echo number_format($total_kg,2) ?></TD>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $ordered_css ?>"><?php echo number_format($ordered_kg,2) ?></TD>
				<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $committed_css ?>"><?php echo number_format($committed_kg,2) ?></TD>
				<TD style="padding:0 .5em 0 .5em;text-align:right<?php echo $net_css ?>"><?php echo number_format($net_kg,2) ?></TD>
<?php } ?>
			</TR>
		</tbody>
	</table>
<?php 
}
?>

<FORM id="search" name="search" ACTION="inventory_inventory_maintenance.php" METHOD="get">
<INPUT TYPE="hidden" id="action" NAME="action" VALUE="search">
<input type="hidden" id="lot_number" name="lot_number" value="<?php echo $lot_number ?>"/><input type="hidden" id="lot_sequence" name="lot_sequence" value="<?php echo $lot_sequence ?>"/>

<?php 

if ((""==$action || "search"==$action || $action == "lists") ) { ?>

<TABLE class="bounding"><TR><TD class="padded">


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD><B>Product number (internal):</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="internal_number_search" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>" SIZE="30"></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><B>Abelei number (external):</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="external_number_search" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=7><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>

	<TR>
		<TD><B>Vendor:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="vendor_search" NAME="Vendor" VALUE="<?php echo $Vendor;?>" SIZE="30"></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><B>Designation:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="designation_search" NAME="Designation" VALUE="<?php echo $Designation;?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=7><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>

	<TR>
		<TD><B>Keywords:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="keyword_search" NAME="Keywords" VALUE="<?php echo $Keywords;?>" SIZE="30"></TD>
		<TD  colspan=2 rowspan=2><INPUT style="float:right" name="search" id="search" TYPE="submit" class="submit_medium" VALUE="Search"></td>
		<TD  colspan=2 rowspan=2><INPUT style="float:right" name="lists" id="lists" TYPE="submit" class="submit_medium" VALUE="List All"></td>
	</TR>

	<TR>
		<TD COLSPAN=7><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="23"></TD>
	</TR>

</TABLE>


</TABLE>




</TABLE>

</TD></TR>
</TABLE><BR>


<?php
	
} 

if ("" != $error_message) {
	echo "<div style=\"color:red; border:solid 2px red; padding:1em\">$error_message</div>";
}
if ("" != $note) {
	echo "<div>$note</div>";
}
if ("" == $error_message) {
	if ( "search"== $action || "search" == $submit_request ) {
		$clause = "";
		if ( $Vendor != '' ) {
			$clause .= ( "" != $clause ? " AND " : "" )."( vendors.name LIKE '%$Vendor%' )";
		} 
		if ( $Designation != '' ) {
			$clause .= ( "" != $clause ? " AND " : "" )."( productmaster.Designation LIKE '%$Designation%' )";
		} 
		if ( $ProductNumberExternal != '' ) {
			$clause .= ( "" != $clause ? " AND " : "" )."( externalproductnumberreference.ProductNumberExternal LIKE '$ProductNumberExternal' )";
		}  
		if ( $ProductNumberInternal != '' ) {
			$clause .= ( "" != $clause ? " AND " : "" )."( inventorymovements.ProductNumberInternal LIKE '%$ProductNumberInternal%' )";
		}  
		if ( $Keywords != '' ) {
			$clause .= ( "" != $clause ? " AND " : "" )."( productmaster.Keywords LIKE '%$Keywords%' )";
		}

		if ( strlen($clause) == 0) {
			echo "you have to provide at least one value of searching fields <br />";
			exit();
		}
		$sql  = "SELECT DISTINCT productmaster.ProductNumberInternal as internal, ".
			"ProductTotal(inventorymovements.ProductNumberInternal,'C',NULL) as total, ".
			"COALESCE((".
				"SELECT SUM(QuantityConvert( (TotalQuantityExpected), UnitOfMeasure, 'grams')) ".
				"FROM purchaseorderdetail 
				LEFT JOIN purchaseordermaster USING (PurchaseOrderNumber)
				WHERE ProductNumberInternal = productmaster.ProductNumberInternal 
				AND (`Status` = 'O' OR `Status` = 'P' or `status` is null) AND ( purchaseordermaster.PurchaseOrderType = 'Material' OR purchaseordermaster.PurchaseOrderType IS NULL) ".
			"),0) as ordered, ".
			"ProductTotal(inventorymovements.ProductNumberInternal,'P',NULL) as committed, ".
			"ProductTotal(inventorymovements.ProductNumberInternal,NULL, NULL) as net, ".
			"productmaster.*, externalproductnumberreference.ProductNumberExternal as external ".
			"FROM productmaster ".
			"LEFT JOIN inventorymovements ON (inventorymovements.ProductNumberInternal = productmaster.ProductNumberInternal) ".
			"LEFT JOIN receipts ON ( receipts.LotID = inventorymovements.LotID ) ".
			"LEFT JOIN lots ON (inventorymovements.LotID = lots.ID) ".
			"LEFT JOIN externalproductnumberreference ON (externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal) ".
			"LEFT JOIN vendors ON (vendors.vendor_id=lots.VendorID) ".
			( "" != $clause ? " WHERE $clause " : "" ).
			"GROUP BY productmaster.ProductNumberInternal ORDER BY productmaster.Designation , productmaster.ProductNumberInternal";
			
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
		// echo "<h5>$sql</h5>";

		if ( $c > 0 ) {
			$bg = 0; 
?>
		<TABLE CELLSPACING="0" CELLPADDING="0" style="border:none; width:900px">
			<thead style="text-align:center">
				<th style="border:solid 1px; width:80px" ><B><nobr>Internal #</nobr></B></th>
				<th style="border:solid 1px; border-left:none;width:100px;" ><B><nobr>External #</nobr></B></th>
				<th style="border:solid 1px; border-left:none;width:325px;" ><B>Description</B></th>
				<th style="border:solid 1px; border-left:none;width:85px;" ><B>Order Trigger Amt.</B></th>
				<th style="border:solid 1px; border-left:none;width:95px;" ><B>Amt on Hand</B></th>
				<th style="border:solid 1px; border-left:none;width:85px;" ><B>Amt on Order</B></th>
				<th style="border:solid 1px; border-left:none;width:90px;" ><B>Amt Committed</B></th>
				<th style="border:solid 1px; border-left:none;width:90px;" ><B>Net Amount</B></th>
				<th style="width:40px;" colspan="2">&nbsp;</th>
			</thead>
			<tbody>
			<TR>
				<TD COLSPAN=11><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
			</TR>

<?php 
			$i=0;
			while ( $row = mysql_fetch_array($result) ) {
				if ( $bg == 1 ) {
					$bgcolor = "#F3E7FD";
					$bg = 0;
				} else {
					$bgcolor = "whitesmoke";
					$bg = 1;
				} 
			//	$subsql  = "SELECT DISTINCT vendors.name FROM productprices LEFT JOIN vendors ON (productprices.VendorID = vendors.vendor_id) WHERE productprices.ProductNumberInternal = '".$row['internal']."' ORDER BY vendors.name ASC";
			//	$subresult = mysql_query($subsql, $link) or die (mysql_error()."<br />Couldn't execute query: $subsql<BR><BR>");
			//	$vendor_name="";
			//	while ( $subrow = mysql_fetch_array($subresult) ) {
			//		$vendor_name .= $subrow['name'].", ";
			//	}
			//	if ("" != $vendor_name) { $vendor_name = substr($vendor_name,0, strlen($vendor_name)-2); } 
				$total_g=$row['total']; 
				$total_css = 0>$total_g ? ";color:red;font-weight:bold;":"";
				$ordered_g=$row['ordered'];
				$ordered_css = 0>$ordered_g ? ";color:red;font-weight:bold;":"";
				$committed_g=$row['committed'];
				$committed_css = 0>$committed_g ? ";color:blue;font-weight:bold;":"";
				$net_g=$row['net'];
				
				$unitOfMeasure = $row['UnitOfMeasure'];
				$net_css = QuantityConvert($row['OrderTriggerAmount'],$unitOfMeasure,'grams')>$net_g ? ";color:red;font-weight:bold;":"";
								
				if ($unitOfMeasure == "grams") {
					$total_mrk = "<small>g</small>";
					$ordered_mrk = "<small>g</small>";
					$committed_mrk = "<small>g</small>";
					$net_mrk = "<small>g</small>";
				} elseif ( $unitOfMeasure == "lbs") {
					$total_g=QuantityConvert($total_g,'grams','lbs');
					$total_mrk = "<small>lb</small>";
					$ordered_g=QuantityConvert($ordered_g,'grams','lbs');
					$ordered_mrk = "<small>lb</small>";
					$committed_g=QuantityConvert($committed_g,'grams','lbs');
					$committed_mrk = "<small>lb</small>";
					$net_g=QuantityConvert($net_g,'grams','lbs');
					$net_mrk = "<small>lb</small>";
				} elseif ( $unitOfMeasure == "kg") {
					$total_g=QuantityConvert($total_g,'grams','kg');
					$total_mrk = "<small>kg</small>";
					$ordered_g=QuantityConvert($ordered_g,'grams','kg');
					$ordered_mrk = "<small>kg</small>";
					$committed_g=QuantityConvert($committed_g,'grams','kg');
					$committed_mrk = "<small>kg</small>";
					$net_g=QuantityConvert($net_g,'grams','kg');
					$net_mrk = "<small>kg</small>";
				} elseif ( $unitOfMeasure == "" or $unitOfMeasure == "n/a" or $unitOfMeasure == "N/A") {
					$total_mrk = "<small>ps</small>";
					$ordered_mrk = "<small>ps</small>";
					$committed_mrk = "<small>ps</small>";
					$net_mrk = "<small>ps</small>";
				}
?>
 
				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;" ><?php echo $row['internal']; ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;" ><?php echo $row['external']; ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;"><?php echo ((25 < strlen($row['Designation'])) ? trim(substr($row['Designation'],0,22))."&hellip;": $row['Designation']).
					(("" != $row['Natural_OR_Artificial']) ? " - " . $row['Natural_OR_Artificial']." " : "").
					(("" != $row['ProductType']) ? " - ".$row['ProductType'] : "").(("" != $row['Kosher'] ) ? " - ".$row['Kosher'] : "")  ?></TD>
					<TD style="border-right:solid 1px black; padding:0 0 0 0;cellspacing:0;width:180px;text-align:left top;<?php echo $total_css ?>"><NOBR><iframe id="trgamt_<?php echo $i;?>" name="trgamt_<?php echo $i;?>" width="55px" height="25px" align="left" valign="top" frameborder="0" scrolling="no" src="setOrderTriggerAmt.php?productnumberinternal=<?php echo $row['internal'];?>"></iframe><?php echo $total_mrk;?></NOBR></TD>
					<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $total_css ?>"><?php echo number_format($total_g,2).$total_mrk; ?></TD>
					<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $ordered_css ?>"><?php echo number_format($ordered_g,2).$ordered_mrk; ?></TD>
					<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $committed_css ?>"><?php echo number_format($committed_g,2).$committed_mrk; ?></TD>
					<TD style="padding:0 .5em 0 .5em;text-align:right<?php echo $net_css ?>;border-right:1px solid"><?php echo number_format($net_g,2).$net_mrk; ?></TD>

					<TD><INPUT TYPE="button" VALUE="View Product" CLASS="submit" onClick="window.location='inventory_inventory_maintenance.php?action=view_product&ProductNumberInternal=<?php echo $row['internal']?>'" STYLE="font-size:7pt"></TD>
					<TD><INPUT TYPE="button" VALUE="Add Inventory From Lab" CLASS="submit" onClick="popup('pop_add_lab_sample_to_inventory.php?pni=<?php echo $row[internal]?>',500,500)" STYLE="font-size:7pt"></TD>
				</TR>
		<?php
				$i++;
			} 
?>
			</tbody>
		</TABLE>

<?php
		} else {
			echo "No reciepts match your search criteria.<BR>";
		}
	} elseif ( ("lists"==$action) || ("lists" == $submit_request) ) {
		set_time_limit(120);
				$clause = "";
		if ( $Vendor != '' ) {
			$clause .= ( "" != $clause ? " AND " : "" )."( vendors.name LIKE '%$Vendor%' )";
		} 
		if ( $Designation != '' ) {
			$clause .= ( "" != $clause ? " AND " : "" )."( productmaster.Designation LIKE '%$Designation%' )";
		} 
		if ( $ProductNumberExternal != '' ) {
			$clause .= ( "" != $clause ? " AND " : "" )."( externalproductnumberreference.ProductNumberExternal LIKE '$ProductNumberExternal' )";
		}  
		if ( $ProductNumberInternal != '' ) {
			$clause .= ( "" != $clause ? " AND " : "" )."( inventorymovements.ProductNumberInternal LIKE '%$ProductNumberInternal%' )";
		}  
		if ( $Keywords != '' ) {
			$clause .= ( "" != $clause ? " AND " : "" )."( productmaster.Keywords LIKE '%$Keywords%' )";
		}

		
			$sql = "Select distinct productmaster.ProductNumberInternal as internal, lots.LotNumber, receipts.DateReceived, ".
			"ProductTotal(inventorymovements.ProductNumberInternal,'C',NULL) as total, ".
			"COALESCE((".
				"SELECT SUM(QuantityConvert( (TotalQuantityExpected), UnitOfMeasure, 'grams')) ".
				"FROM purchaseorderdetail 
				LEFT JOIN purchaseordermaster USING (PurchaseOrderNumber)
				WHERE ProductNumberInternal = productmaster.ProductNumberInternal 
				AND (`Status` = 'O' OR `Status` = 'P' OR `Status` is null) AND ( purchaseordermaster.PurchaseOrderType = 'Material' OR purchaseordermaster.PurchaseOrderType IS NULL) ".
			"),0) as ordered, ".
			"ProductTotal(inventorymovements.ProductNumberInternal,'P',NULL) as committed, ".
			"ProductTotal(inventorymovements.ProductNumberInternal,NULL, NULL) as net, ".
			"productmaster.*, externalproductnumberreference.ProductNumberExternal as external ".
			"FROM productmaster ".
			"LEFT JOIN inventorymovements ON (inventorymovements.ProductNumberInternal = productmaster.ProductNumberInternal) ".
			"LEFT JOIN receipts ON ( receipts.LotID = inventorymovements.LotID ) ".
			"LEFT JOIN lots ON (inventorymovements.LotID = lots.ID) ".
			"LEFT JOIN externalproductnumberreference ON (externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal) ".
			"LEFT JOIN vendors ON (vendors.vendor_id=lots.VendorID) ".
			( "" != $clause ? " WHERE $clause " : "" ).
			"GROUP BY productmaster.ProductNumberInternal ORDER BY productmaster.Designation, productmaster.ProductNumberInternal";
			// echo "<br />". $sql ."<br />";
			
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
		// echo "<h2>$sql</h2>";

		if ( $c > 0 ) {
			$bg = 0; 
?>
		<TABLE CELLSPACING="0" CELLPADDING="0" style="border:none; width:900px">
			<thead style="text-align:center">
				<th style="border:solid 1px; width:80px" ><B><nobr>Internal #</nobr></B></th>
				<th style="border:solid 1px; border-left:none;width:100px" ><B><nobr>External #</nobr></B></th>
				<th style="border:solid 1px; border-left:none;width:325px" ><B>Description</B></th>
				<th style="border:solid 1px; border-left:none;width:85px" ><B>Order Trigger Amt.</B></th>
				<th style="border:solid 1px; border-left:none;width:95px" ><B>Amt on Hand</B></th>
				<th style="border:solid 1px; border-left:none;width:85px" ><B>Amt on Order</B></th>
				<th style="border:solid 1px; border-left:none;width:90px" ><B>Amt Committed</B></th>
				<th style="border:solid 1px; border-left:none;width:90px" ><B>Net Amount</B></th>
				<th style="width:40px;" colspan="2">&nbsp;</th>
			</thead>
			<tbody>
			<TR>
				<TD COLSPAN=11><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
			</TR>

<?php 
			$i=0;
			while ( $row = mysql_fetch_array($result) ) {
				if ( $bg == 1 ) {
					$bgcolor = "#F3E7FD";
					$bg = 0;
				} else {
					$bgcolor = "whitesmoke";
					$bg = 1;
				} 
				$i++;
				//$subsql  = "SELECT DISTINCT vendors.name FROM productprices LEFT JOIN vendors ON (productprices.VendorID = vendors.vendor_id) WHERE productprices.ProductNumberInternal = '".$row['internal']."' ORDER BY vendors.name ASC";
				//$subresult = mysql_query($subsql, $link) or die (mysql_error()."<br />Couldn't execute query: $subsql<BR><BR>");
				//$vendor_name="";
				//while ( $subrow = mysql_fetch_array($subresult) ) {
				//	$vendor_name .= $subrow['name'].", ";
				//}
				//if ("" != $vendor_name) { $vendor_name = substr($vendor_name,0, strlen($vendor_name)-2); } 
				$total_g=$row['total']; 
				$total_css = 0>$total_g ? ";color:red;font-weight:bold;":"";
				$ordered_g=$row['ordered'];
				$ordered_css = 0>$ordered_g ? ";color:red;font-weight:bold;":"";
				$committed_g=$row['committed'];
				$committed_css = 0>$committed_g ? ";color:blue;font-weight:bold;":"";
				$net_g=$row['net'];
				
				$unitOfMeasure = $row['UnitOfMeasure'];
				$net_css = QuantityConvert($row['OrderTriggerAmount'],$unitOfMeasure,'grams')>$net_g ? ";color:red;font-weight:bold;":"";
							
				if ($unitOfMeasure == "grams") {
					$total_mrk = "<small>g</small>";
					$ordered_mrk = "<small>g</small>";
					$committed_mrk = "<small>g</small>";
					$net_mrk = "<small>g</small>";
				} elseif ( $unitOfMeasure == "lbs") {
					$total_g=QuantityConvert($total_g,'grams','lbs');
					$total_mrk = "<small>lb</small>";
					$ordered_g=QuantityConvert($ordered_g,'grams','lbs');
					$ordered_mrk = "<small>lb</small>";
					$committed_g=QuantityConvert($committed_g,'grams','lbs');
					$committed_mrk = "<small>lb</small>";
					$net_g=QuantityConvert($net_g,'grams','lbs');
					$net_mrk = "<small>lb</small>";
				} elseif ( $unitOfMeasure == "kg") {
					$total_g=QuantityConvert($total_g,'grams','kg');
					$total_mrk = "<small>kg</small>";
					$ordered_g=QuantityConvert($ordered_g,'grams','kg');
					$ordered_mrk = "<small>kg</small>";
					$committed_g=QuantityConvert($committed_g,'grams','kg');
					$committed_mrk = "<small>kg</small>";
					$net_g=QuantityConvert($net_g,'grams','kg');
					$net_mrk = "<small>kg</small>";
				} elseif ( $unitOfMeasure == "" or $unitOfMeasure == "n/a" or $unitOfMeasure == "N/A") {
					$total_mrk = "<small>ps</small>";
					$ordered_mrk = "<small>ps</small>";
					$committed_mrk = "<small>ps</small>";
					$net_mrk = "<small>ps</small>";
				}
?>
<?php 			if ( $i%20 == 0 ) { ?>
			<tr style="text-align:center">
				<th style="border:solid 1px; width:80px" ><B><nobr>Internal #</nobr></B></th>
				<th style="border:solid 1px; border-left:none;width:100px" ><B><nobr>External #</nobr></B></th>
				<th style="border:solid 1px; border-left:none;width:325px" ><B>Description</B></th>
				<th style="border:solid 1px; border-left:none;width:85px" ><B>Order Trigger Amt.</B></th>
				<th style="border:solid 1px; border-left:none;width:95px" ><B>Amt on Hand</B></th>
				<th style="border:solid 1px; border-left:none;width:85px" ><B>Amt on Order</B></th>
				<th style="border:solid 1px; border-left:none;width:90px" ><B>Amt Committed</B></th>
				<th style="border:solid 1px; border-left:none;width:90px" ><B>Net Amount</B></th>
				<th style="width:40px;" colspan="2">&nbsp;</th>
			</tr>

<?php 			} ?>
				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;" ><?php echo $row['internal'] ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;" ><?php echo $row['external'] ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;"><?php echo ((25 < strlen($row['Designation'])) ? trim(substr($row['Designation'],0,22))."&hellip;": $row['Designation']).
					(("" != $row['Natural_OR_Artificial']) ? " - ". $row['Natural_OR_Artificial']." " : "").
					(("" != $row['ProductType']) ? " - ".$row['ProductType'] : "").
					(("" != $row['Kosher'] ) ? " - ".$row['Kosher'] : "")  ?></TD>
					<TD style="border-right:solid 1px black; padding:0 0 0 0;cellspacing:0;width:180px;text-align:left top;<?php echo $total_css ?>"><NOBR><iframe id="trgamt_<?php echo $i;?>" name="trgamt_<?php echo $i;?>" width="55px" height="25px" align="left" valign="top" frameborder="0" scrolling="no" src="setOrderTriggerAmt.php?productnumberinternal=<?php echo $row['internal'];?>"></iframe><?php echo $total_mrk;?></NOBR></TD>
					<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $total_css ?>"><?php echo number_format($total_g,2).$total_mrk; ?></TD>
					<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $ordered_css ?>"><?php echo number_format($ordered_g,2).$ordered_mrk; ?></TD>
					<TD style="border-right:solid 1px black; padding:0 .5em 0 .5em;text-align:right<?php echo $committed_css ?>"><?php echo number_format($committed_g,2).$committed_mrk; ?></TD>
					<TD style="padding:0 .5em 0 .5em;text-align:right<?php echo $net_css ?>;border-right:1px solid"><?php echo number_format($net_g,2).$net_mrk; ?></TD>

					<TD><INPUT TYPE="button" VALUE="View Product" CLASS="submit" onClick="window.open('inventory_inventory_maintenance.php?action=view_product&ProductNumberInternal=<?php echo $row['internal']?>','view_p_window', 'location=yes, menubar=yes,resizable=yes, scrollbars=yes' )" STYLE="font-size:7pt"></TD>
					<TD><INPUT TYPE="button" VALUE="Add Inventory From Lab" CLASS="submit" onClick="popup('pop_add_lab_sample_to_inventory.php?pni=<?php echo $row[internal]?>',500,500)" STYLE="font-size:7pt"></TD>
				</TR>

<?php
			} 
?>
			</tbody>
		</TABLE>

<?php
		} else {
			echo "No reciepts match your search criteria.<BR>";
		}
	}
	else
	if ("view_product"==$action){
		echo "<h3>Product Detail</h3>";
		product_header($ProductNumberInternal,$unitOfMeasure);
		if ( "2" != substr($ProductNumberInternal,0,1) )
			$tb_width="800px";
		else
			$tb_width="370px";
?>
		<table CELLSPACING="0" CELLPADDING="0" style="width:<?php echo $tb_width;?>; border: medium none ; padding: 1em 0pt 0pt;" >
			<thead style="text-align:center">
				<th style="border: 1px solid ; padding:0.5em; width:140px;" ><B>Lot Number and Sequence</B></th>
<?php
		$colspan = 5;
		if ( "2" != substr($ProductNumberInternal,0,1) ) { // if not a flavor
			$colspan=8;
		?>
				<th style="border: solid 1px; border-left:none; padding:0.5em; width: 170px;" ><B>Vendor</B></th>
				<th style="border: solid 1px; border-left:none; padding:0.5em; width: 170px;" ><B>Vendor Product Code</B></th>
				<th style="border: solid 1px; border-left:none; padding:0.5em; width: 90px;" ><B>Storage Location</B></th>
<?php
		}

		if ( "6" == substr($ProductNumberInternal,0,1) ) { // if containers
?>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 65px;" ><B>Beginning Amt (ps)</B></th>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 65px;" ><B>Current Amt (ps)</B></th>
<?php	} elseif ( $unitOfMeasure == "grams") { ?>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 65px;" ><B>Beginning Amt (g)</B></th>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 65px;" ><B>Current Amt (g)</B></th>
<?php	} elseif ( $unitOfMeasure == "lbs") { ?>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 65px;" ><B>Beginning Amt (lb)</B></th>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 65px;" ><B>Current Amt (lb)</B></th>
<?php	} elseif ( $unitOfMeasure == "kg") { ?>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 65px;" ><B>Beginning Amt (kg)</B></th>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 65px;" ><B>Current Amt (kg)</B></th>
<?php } ?>
				<th style="width:100px;">&nbsp;</th>
				<th style="width:100px;">&nbsp;</th>

			</thead>
			<tbody>

<?php
		$sql  = "Select distinct inventorymovements.LotID as lot_id, 
					CONCAT(lots.LotNumber,' - ',lots.LotSequenceNumber) as lot, 
					LotTotal(inventorymovements.LotID,'C',1) as beginning, 
					LotTotal(inventorymovements.LotID,'C',NULL) as current,
					vendors.name as vendor, 
					vendorproductcodes.VendorProductCode as vendor_product_code, 
					lots.StorageLocation as storage_location 
				FROM inventorymovements 
					LEFT JOIN lots ON (inventorymovements.LotID = lots.ID) 
					LEFT JOIN vendors ON ( vendors.vendor_id = lots.VendorID ) 
					LEFT JOIN vendorproductcodes ON 
						( vendorproductcodes.VendorID=lots.VendorID AND 
						vendorproductcodes.ProductNumberInternal=inventorymovements.ProductNumberInternal ) 
				WHERE inventorymovements.ProductNumberInternal=$ProductNumberInternal AND 
					NOT inventorymovements.LotID IS NULL AND MovementStatus in ('C','P')";
		//echo "<p>" .$sql . "</p>";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR></tbody></table>");
		while ( $row = mysql_fetch_array($result) ) {
			if ( $bg == 1 ) {
				$bgcolor = "#F3E7FD";
				$bg = 0;
			} else {
				$bgcolor = "whitesmoke";
				$bg = 1;
			}
?>
				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;"><?php echo $row['lot'] ?></TD>
<?php
			if ( "2" != substr($ProductNumberInternal,0,1) ) { // if not a flavor
?>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;" ><?php echo $row['vendor'] ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;" ><?php echo $row['vendor_product_code'] ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;"><?php echo $row['storage_location'] ?></TD>
<?php
			} ?>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;text-align:right"><?php echo number_format(QuantityConvert($row['beginning'],'grams',$unitOfMeasure),2) ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;text-align:right"><?php echo number_format(QuantityConvert($row['current'],'grams',$unitOfMeasure),2) ?></TD>

					<TD><INPUT TYPE="button" VALUE="View Movements" CLASS="submit" onClick="window.location='inventory_inventory_maintenance.php?action=view_lot_movements&lot_id=<?php echo $row['lot_id']?>&ProductNumberInternal=<?php echo $ProductNumberInternal ?>'" STYLE="font-size:7pt"></TD>
					<TD><INPUT TYPE="button" id="add movement" VALUE="Add Movement" CLASS="submit" onClick="popup('pop_add_inventory_movement.php?lot_id=<?php echo $row['lot_id']?>',400,400);" STYLE="font-size:7pt"></TD>
				</TR>
<?php
		}
		echo "<TR><TD colspan='". $colspan . "'>&nbsp;</TD></TR>";
		echo "</tbody></table>"; 
		// get pending committed orders
?>

<?php 
		if ( "6" == substr($ProductNumberInternal,0,1)) {
			$sql = "SELECT ".
			"BatchSheetNumber,bsci.CustomerOrderNumber, bsci.CustomerOrderSeqNumber, bsci.NumberOfPackages as pending, 
			'N/A' as TotalQuantityUnitType, NumberOfTimesToMake
			From batchsheetcustomerinfo as bsci
			LEFT JOIN inventorymovements as im ON im.TransactionNumber=bsci.InventoryTransactionNumber 
			LEFT JOIN batchsheetmaster as bsm USING (BatchSheetNumber) 
			where bsci.PackIn = '$ProductNumberInternal' and im.ProductNumberInternal=$ProductNumberInternal ".
			" AND im.MovementStatus = 'P' AND bsci.PackIn = '$ProductNumberInternal'";
		} else {
			$sql = "SELECT DISTINCT ProductTotal($ProductNumberInternal,'P',NULL) as pending, ".
			"bsm.BatchSheetNumber,bsm.ProductNumberExternal, bsm.NetWeight, bsm.TotalQuantityUnitType,".
			" bsd.Percentage, bsm.Yield, bsm.DueDate, NumberOfTimesToMake ".
			"FROM batchsheetmaster as bsm ".
			"LEFT JOIN batchsheetdetail as bsd USING (BatchSheetNumber) ".
			"LEFT JOIN inventorymovements as im ON im.TransactionNumber = bsm.InventoryMovementTransactionNumber ".
			"WHERE bsd.IngredientProductNumber=$ProductNumberInternal ".
			" AND im.MovementStatus = 'P'";
		}
		
		// echo $sql ."<BR />";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR></tbody></table>");
		$c = mysql_num_rows($result);
		if ( $c > 0 ) { ?>
			<TR><TD colspan="11">
			<h5>Pending Committed Amount for Internal Product : <?php echo $ProductNumberInternal; ?></h5>		
			<table>
			<thead style="text-align:center">
				<th style="border: 1px solid ; padding:0.5em; width:10em;" ><B>Batch Sheet Number</B></th>

				<th style="border: 1px solid ; padding:0.5em; width:10em;" ><B>Due Date</B></th>
				<th style="border: 1px solid ; padding:0.5em; width:10em;" ><B>Pending Committed Amount</B></th>
			<?php if ( "6" == substr($ProductNumberInternal,0,1)) { ?>
				<th style="border: 1px solid ; padding:0.5em; width:10em;" ><B>Customer Order Number</B></th>
			<?php } else { ?>
				<th style="border: 1px solid ; padding:0.5em; width:10em;" ><B>Abelei # (External)</B></th>
			<?php } ?>
				<th>&nbsp;</th>
			</thead>
			<tbody>
<?php				
			while ( $row = mysql_fetch_array($result) ) { 
				if ( "6" == substr($ProductNumberInternal,0,1)) {
					$pending_amt = $row['pending'];
				} else {
					$pending_amt = $row['NetWeight']*$row[Percentage]*0.01*$row['NumberOfTimesToMake']/$row[Yield];
				}
			
			?>
		<TR>
			<TD><?php echo $row[BatchSheetNumber] ."&nbsp;".$row[commitedToInventory]."&nbsp;".$row[Manufactured]."&nbsp;".$row[MovementStatus];?></TD>
			<TD><?php echo substr($row[DueDate],0,10);?></TD>
			<?php if ( "6" == substr($ProductNumberInternal,0,1)) { ?>
			<TD><?php echo "-".number_format($pending_amt,2)."<small>ps</small>";?></TD>
			<TD><?php echo $row[CustomerOrderNumber]."_".$row[CustomerOrderSeqNumber];?></TD>
			<?php } else { ?>
			<TD><?php echo "-".number_format(QuantityConvert($pending_amt,$row[TotalQuantityUnitType],$unitOfMeasure),2)."<small>".$unitOfMeasure."</small>";?></TD>
			<TD><?php echo $row[ProductNumberExternal];?></TD>
			<?php } ?>

			<TD><INPUT TYPE="button" VALUE="View Batch Sheet" CLASS="submit" onClick="popup('customers_batch_sheets.php?bsn=<?php echo $row['BatchSheetNumber']?>')" STYLE="font-size:7pt"></TD>
			
		</TR>
			
<?php	}
		echo "<TR><TD colspan='5'></TD></TR></tbody></table>"; 
		
		}
	}
	else
	if ( "view_lot_movements" == $action ) {
		echo "<h3>Lot Detail</h3>";
		product_header($ProductNumberInternal);
?>
		<div>
		<table CELLSPACING="0" CELLPADDING="0" style="width:60em; border: medium none ; padding: 1em 0pt 0pt;" >
			<thead style="text-align:center">
				<th style="border: 1px solid ; padding:0.5em; width:10em;" ><B>Lot Number and Sequence</B></th>
<?php
		if ( "2" != substr($ProductNumberInternal,0,1) ) { // if not a flavor
?>
				<th style="border: solid 1px; border-left:none; padding:0.5em; width: 19em;" ><B>Vendor</B></th>
				<th style="border: solid 1px; border-left:none; padding:0.5em; width: 19em;" ><B>Vendor Product Code</B></th>
				<th style="border: solid 1px; border-left:none; padding:0.5em; width: 9em;" ><B>Storage Location</B></th>
<?php
		}
		if ( "6" != substr($ProductNumberInternal,0,1) ) { 
?>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 6.5em;" ><B>Beginning Amt (g)</B></th>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 6.5em;" ><B>Current Amt (g)</B></th>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 6.5em;" ><B>Beginning Amt (lbs)</B></th>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 6.5em;" ><B>Current Amt (lbs)</B></th>
<?php } else { ?>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 6.5em;" ><B>Beginning Amt (ps)</B></th>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 6.5em;" ><B>Current Amt (ps)</B></th>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 6.5em;" >&nbsp;</th>
				<th style="border:solid 1px; border-left:none; padding:0.5em; width: 6.5em;" >&nbsp;</th>

<?php } ?>
			</thead>
			<tbody>

<?php
		$sql  = "Select DISTINCT inventorymovements.LotID as lot_id, 
					CONCAT(lots.LotNumber,' - ',lots.LotSequenceNumber) as lot, 
					LotTotal(inventorymovements.LotID,'C',1) as beginning, 
					LotTotal(inventorymovements.LotID,'C',NULL) as current, 
					vendors.name as vendor, 
					vendorproductcodes.VendorProductCode as vendor_product_code, 
					lots.StorageLocation as storage_location
				FROM inventorymovements 
					LEFT JOIN lots ON (inventorymovements.LotID = lots.ID) 
					LEFT JOIN vendors ON ( vendors.vendor_id = lots.VendorID ) 
					LEFT JOIN vendorproductcodes ON 
						( vendorproductcodes.VendorID=lots.VendorID AND 
						vendorproductcodes.ProductNumberInternal=inventorymovements.ProductNumberInternal )
				WHERE inventorymovements.LotID=$lot_id AND 
					NOT inventorymovements.LotID IS NULL AND inventorymovements.ProductNumberInternal=$ProductNumberInternal";
		//echo $sql . "<b />";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR></tbody></table>");
		$bg=0;
		while ( $row = mysql_fetch_array($result) ) {
			if ( $bg == 1 ) {
				$bgcolor = "#F3E7FD";
				$bg = 0;
			} else {
				$bgcolor = "whitesmoke";
				$bg = 1;
			}
?>
				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;"><?php echo $row['lot'] ?></TD>
<?php
			if ( "2" != substr($ProductNumberInternal,0,1) ) { // if not a flavor
?>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;" ><?php echo $row['vendor'] ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;" ><?php echo $row['vendor_product_code'] ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;"><?php echo $row['storage_location'] ?></TD>
<?php
			}
			if ( "6" != substr($ProductNumberInternal,0,1) ) {
?>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;text-align:right"><?php echo number_format($row['beginning'],2) ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;text-align:right"><?php echo number_format($row['current'],2) ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;text-align:right"><?php echo number_format(QuantityConvert($row['beginning'],'grams','lbs'),2) ?></TD>
					<TD style="padding:0 .5em 0 .5em;text-align:right"><?php echo number_format(QuantityConvert($row['current'],'grams','lbs'),2) ?></TD>
<?php 		} else { ?>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;text-align:right"><?php echo number_format($row['beginning'],2) ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;text-align:right"><?php echo number_format($row['current'],2) ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;text-align:right">&nbsp;</TD>
					<TD style="padding:0 .5em 0 .5em;text-align:right">&nbsp;</TD>
<?php 		} ?>
					
				</TR>
<?php
		}
?>
			</tbody>
			</table>
			<INPUT TYPE="button" VALUE="Add Movement" CLASS="submit_medium" style="float:right; margin-top: 1em;" onClick="popup('pop_add_inventory_movement.php?lot_id=<?php echo $lot_id ?>',500,400);">
		</div>
		<table CELLSPACING="0" CELLPADDING="0" style="width:70em; border: medium none ; padding: 1em 0pt 0pt;" >
			<thead style="text-align:center">
				<th style="border: 1px solid ; padding:0.5em; width:10em;" ><B>Transaction Type</B></th>
				<th style="border: solid 1px; border-left:none; padding:0.5em; width: 19em;" ><B>Remarks</B></th>
				<th style="border: solid 1px; border-left:none; padding:0.5em; width: 4em;" ><B>Date</B></th>
				<th style="border: solid 1px; border-left:none; padding:0.5em; width: 3em;" ><B>Quantity (g)</B></th>
				<th style="border: solid 1px; border-left:none; padding:0.5em; width: 3em;" ><B>Quantity (lbs)</B></th>
				<th style="border: solid 1px; border-left:none; padding:0.5em; width: 3em;" ><B>BatchSheet / PO#</B></th>
				<th style="width:70px;" colspan="2" />
			</thead>
			<tbody>

<?php
		$sql  = "SELECT inventorymovements.TransactionNumber as x_id, inventorymovements.TransactionType as x_type, inventorytransactiontypes.TransactionDescription, inventorymovements.TransactionDate, ".
			"inventorymovements.Quantity*inventorytransactiontypes.InventoryMultiplier as quantity, inventorymovements.Remarks, ".
			"batchsheetdetaillotnumbers.BatchSheetNumber, purchaseorderdetail.PurchaseOrderNumber ".
			"FROM inventorymovements LEFT JOIN inventorytransactiontypes ON (inventorymovements.TransactionType = inventorytransactiontypes.TransactionID) ".
			" LEFT JOIN batchsheetdetaillotnumbers ON batchsheetdetaillotnumbers.InventoryMovementTransactionNumber = inventorymovements.TransactionNumber ".
			" LEFT JOIN receipts ON receipts.LotId = inventorymovements.LotID ".
			" LEFT JOIN purchaseorderdetail ON purchaseorderdetail.ID=receipts.PurchaseOrderID ".
			"WHERE inventorymovements.LotID=$lot_id AND MovementStatus='C' ORDER BY TransactionDate ASC";
	//	echo $sql. "<br />";	
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR></tbody></table>");
		while ( $row = mysql_fetch_array($result) ) {
			if ( $bg == 1 ) {
				$bgcolor = "#F3E7FD";
				$bg = 0;
			} else {
				$bgcolor = "whitesmoke";
				$bg = 1;
			}
			$x_type=$row['x_type'];
?>
				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;"><?php echo $row[TransactionDescription]; ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;"><?php echo $row[Remarks]; ?></TD>
					<TD style="border-right:solid 1px; padding:0 .5em 0 .5em;text-align:right" ><?php echo date('m/d/Y',strtotime($row[TransactionDate])); ?></TD>
					<TD style="padding:0 .5em 0 .5em;text-align:right" ><?php echo number_format($row[quantity],2); ?></TD>
					<TD style="padding:0 .5em 0 .5em;text-align:right" ><?php echo number_format(QuantityConvert($row[quantity],'grams','lbs'),2); ?></TD>
				<?php if(empty($row[BatchSheetNumber])) { ?>
					<TD style="padding:0 .5em 0 .5em;text-align:right" ><?php echo "PO#:".$row[PurchaseOrderNumber];?></TD>
				<?php } else { ?>
					<TD style="padding:0 .5em 0 .5em;text-align:right" ><?php echo "BS#:".$row[BatchSheetNumber];?></TD>
				<?php } ?>
					<TD><?php 
						if (3 == $x_type || 4 == $x_type || 7 == $x_type) {
							echo "<INPUT TYPE=\"button\" id=\"edit movement\" VALUE=\"Edit Movement\" CLASS=\"submit\" onClick=\"popup('pop_add_inventory_movement.php?x_id=".$row['x_id']."',500,400);\" STYLE=\"font-size:7pt\">" ;
						}
						else {
							echo "<span style=\"color:gray\">cannot edit</span>";
						}
					?></TD>
					<?php if (empty($row[BatchSheetNumber])) { ?>
					<TD><INPUT type="button" class="submit" id="view_po" value="View PO" onClick="popup('vendors_pos.php?pon=<?php echo $row[PurchaseOrderNumber];?>')" style="font-size:7pt"></TD>
					<?php } else { ?> 
					<TD><INPUT type="button" class="submit" id="view_batch" value="View BatchSheet" onClick="popup('customers_batch_sheets.php?bsn=<?php echo $row[BatchSheetNumber];?>')" style="font-size:7pt"></TD>
					<?php } ?>
					</TR>
<?php
		}
		echo "<TR><TD colspan='8'>&nbsp;</TD></TR></tbody></table>";
	}
}

?>
<script type="text/javascript">

$(document).ready(function(){

	$(":submit").click(function() {
		$("#action").val(this.name);
		switch (this.name)
		{
			case 'search':
				break;
			case 'save':
				alertMessage = validated();
				if ("" != alertMessage )
				{ 
					alert(alertMessage);
					return false;
				}
				break;
			default:
				//alert ("this button not yet supported");
				break;
		}
	});

	$("#vendor_search").autocomplete("search/vendors.php", {
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#vendor_search").result(function(event, data, formatted) {
		if (data)
			$("#action").val('search');
			document.search.submit();
	});

	$("#designation_search").autocomplete("search/designations.php", {
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false,
		extraParams: { moreinfo:"true"}
	});
	$("#designation_search").result(function(event, data, formatted) {
		if (data)
			$("#external_number_search").val('');
			$("#internal_number_search").val('');
			$("#keyword_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
	$("#external_number_search").autocomplete("search/external_product_numbers.php", {
		cacheLength: 1,
		width: 650,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false	});
	$("#external_number_search").result(function(event, data, formatted) {
		if (data)
			$("#designation_search").val('');
			$("#internal_number_search").val('');
			$("#keyword_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
	$("#internal_number_search").autocomplete("search/internal_product_numbers.php", {
		cacheLength: 1,
		width: 650,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false	});
	$("#internal_number_search").result(function(event, data, formatted) {
		if (data)
			$("#designation_search").val('');
			$("#external_number_search").val('');
			$("#keyword_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
	$("#keyword_search").autocomplete("search/keywords.php", {
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
  });
	$("#keyword_search").result(function(event, data, formatted) {
		if (data)
			$("#designation_search").val('');
			$("#external_number_search").val('');
			$("#internal_number_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
});
function validated() {
	// verify all fields have a value that need one;
	var alertMessage = "";
	if ("" == $("#po_id").val()) {
		alertMessage+="Purchase Order is a required Field\n";
		$("#po_number").attr("style", "border: solid 1px red");
		$("#po_sequence").attr("style", "border: solid 1px red");
		$("#po_number_search").attr("style", "border: solid 1px red");
	} else {
		$("#po_number").attr("style", "border: none");
		$("#po_sequence").attr("style", "border: none");
		$("#po_number_search").attr("style", "border: none");
	}
	if ("" == $("#lot_number").val()) {
		alertMessage+="Lot Number is a required Field\n";
		$("#lot_number").attr("style", "border: solid 1px red");
	} else {
		$("#lot_number").attr("style", "border: none");
	}
	if ("" == $("#lot_sequence").val()) {
		alertMessage+="Lot Sequence Number is a required Field\n";
		$("#lot_sequence").attr("style", "border: solid 1px red");
	} else {
		$("#lot_sequence").attr("style", "border: none");
	}
	if ("" == $("#quantity").val()) {
		alertMessage+="Quantity is a required Field\n";
		$("#quantity").attr("style", "border: solid 1px red");
	} else {
		$("#quantity").attr("style", "border: none");
	}
	if ("" == $("#pack_size").val()) {
		alertMessage+="Pack Size is a required Field\n";
		$("#pack_size").attr("style", "border: solid 1px red");
	} else {
		$("#pack_size").attr("style", "border: none");
	}
	if ("" == $("#measurement_units").val()) {
		alertMessage+="Unit of Measure is a required Field\n";
		$("#measurement_units").attr("style", "border: solid 1px red");
	} else {
		$("#measurement_units").attr("style", "border: none");
	}
	return alertMessage;
}

function setTrgamt(itrt,prdnminternal,trgamt) {
	var myID="trgamt_"+itrt;
	alert("myID="+myID+"\n");
	var myElement = document.getElementById(myID);
	$("#setTrgAmt_"+itrt).attr("onClick","");
	$("#setTrgAmt_"+itrt).val('');
	$("#setTrgAmt_0").val('');
	$("#setTrgAmt_1").val('');
	alert("innerHTML = "+"<!--<form action=\"setPrdTrgAmt.php\" method=\"get\"><input type=\"hidden\" name=\"ProductNumberInternal\" value=\""+prdnminternal+"\"><input type=\"text\" name=\"trgamt\" value=\""+trgamt+"\"><input type=\"submit\" value=\"submit\"><input type=\"reset\" value=\"cancel\"></form>-->");
	myElement.innerHTML="<!-- <form action=\"setPrdTrgAmt.php\" method=\"get\">\n<input type=\"hidden\" name=\"ProductNumberInternal\" value=\""+prdnminternal+"\">\n<input type=\"text\" name=\"trgamt\" value=\""+trgamt+"\"><input type=\"submit\" value=\"submit\"><input type=\"reset\" value=\"cancel\"></form> -->";
	document.getElementById("setTrgAmt_"+itrt).removeAttribute("onClick");
	document.getElementById("setTrgAmt_"+itrt).innerHTML="";
	//document.getElementById("setTrgAmt_"+itrt).innerHTML="<form action=\"setPrdTrgAmt.php\" method=\"get\">\n<input type=\"hidden\" name=\"ProductNumberInternal\" value=\""+prdnminternal+"\">\n<input type=\"text\" name=\"trgamt\" value=\""+trgamt+"\"><input type=\"submit\" value=\"submit\"><input type=\"reset\" value=\"cancel\"></form>";
}

</script>

<?php
include("inc_footer.php"); 
 ?>