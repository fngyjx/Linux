<?php

include('inc_ssl_check.php');
if ( !isset($_SESSION) ) { session_start(); }

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

include('inc_global.php');
include('search/system_defaults.php');

$pni = isset($_REQUEST[pni]) ? $_REQUEST[pni] : "";

if (""==$pni) {
	$_SESSION['note'] = "Product Number is Required";
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
}

$sql = "SELECT Designation, Natural_OR_Artificial as NA, Kosher, ProductType, UnitOfMeasure AS units
				FROM productmaster
				WHERE ProductNumberInternal=$pni LIMIT 1";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
if ( 0 < mysql_num_rows($result) ) {
	$row = mysql_fetch_array($result);
	$units = $row[units];
	$description = (""!=$row[NA] ? $row[NA]." " : "").$row[Designation].("" != $row[ProductType] ? " - ".$row[ProductType] : "").("" != $row[Kosher] ? " - ".$row[Kosher] : "");;
} else {
	$_SESSION['note'] = "Invalid Product Number ($pni)";
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
}

// set
$note=""; $error_message="";

$vendor_id = isset($_REQUEST[vendor_id]) ? escape_data($_REQUEST[vendor_id]) : "";
$vendor = isset($_REQUEST[vendor]) ? escape_data($_REQUEST[vendor]) : "";
$lot_no = isset($_REQUEST[lot_no]) ? escape_data($_REQUEST[lot_no]) : "";
$date_man = isset($_REQUEST[date_man]) ? escape_data($_REQUEST[date_man]) : "";
$date_exp = isset($_REQUEST[date_exp]) ? escape_data($_REQUEST[date_exp]) : "";
$quantity = isset($_REQUEST[quantity]) && is_numeric($_REQUEST[quantity]) ? $_REQUEST[quantity] : "";
$pack_size = isset($_REQUEST[pack_size]) && is_numeric($_REQUEST[pack_size]) ? $_REQUEST[pack_size] : "";
$units = isset($_REQUEST[units]) ? escape_data($_REQUEST[units]) : $units;
$pack_in = isset($_REQUEST[pack_in]) ? escape_data($_REQUEST[pack_in]) : "";
$storage_location = isset($_REQUEST[storage_location]) ? escape_data($_REQUEST[storage_location]) : "";

if ( isset($_SESSION[note]) ) { $note = $_SESSION[note]; unset($_SESSION[note]); }

if ( !empty($_POST) ) {

// validate
	if ("" != $date_man) {
		$date_man_parts = explode("/", $date_man);
		$date_man_submit = "'".(is_numeric($date_man_parts[2]) ? $date_man_parts[2] : 0)."-".(is_numeric($date_man_parts[0]) ? $date_man_parts[0] : 0)."-".(is_numeric($date_man_parts[1]) ? $date_man_parts[1] : 0)."'";
		if (!checkdate((is_numeric($date_man_parts[0]) ? $date_man_parts[0] : 0), (is_numeric($date_man_parts[1]) ? $date_man_parts[1] : 0), (is_numeric($date_man_parts[2]) ? $date_man_parts[2] : 0))) 
			$error_message .= "Transaction date invalid.<br/>";
	} 
	else {
		$date_man_submit="NULL";
	}

	if ("" != $date_exp) {
		$date_exp_parts = explode("/", $date_exp);
		$date_exp_submit = "'".(is_numeric($date_exp_parts[2]) ? $date_exp_parts[2] : 0)."-".(is_numeric($date_exp_parts[0]) ? $date_exp_parts[0] : 0)."-".(is_numeric($date_exp_parts[1]) ? $date_exp_parts[1] : 0)."'";
		if (!checkdate((is_numeric($date_exp_parts[0]) ? $date_exp_parts[0] : 0), (is_numeric($date_exp_parts[1]) ? $date_exp_parts[1] : 0), (is_numeric($date_exp_parts[2]) ? $date_exp_parts[2] : 0))) 
			$error_message .= "Transaction date invalid.<br/>";
	}
	else {
		$date_exp_submit="NULL";
	}

	if ( "lbs"!=$units && "grams" != $units && "kilos"!=$units)
		$error_message .= "Units Error - please edit the materials page for this item and set the units.<br/>";
	if ( ""==$quantity || 0 >= $quantity)
		$error_message .= "Quantity must be numeric and greater than 0 [Error - $quantity].<br/>";
	if ( ""==$pack_size || 0 >= $pack_size)
		$error_message .= "Pack Size must be numeric and greater than 0 [Error - $quantity].<br/>";
	
	if ("" == $lot_no) {
		$error_message .= "Lot Number is a required field";
	}
	else 
	{
		$lot_seq_no = getNextLotSequenceNumber($lot_no);
	}

	if ( !$error_message ) {
	
	$total = QuantityConvert($quantity*$pack_size, $units, 'grams');

	// Insert new lot, inventorymovement, and receipt
	$sql = "INSERT INTO lots 
					(LotNumber, LotSequenceNumber, DateManufactured, ExpirationDate, StorageLocation, VendorID) 
				VALUES 
					('$lot_no', $lot_seq_no, $date_man_submit, $date_exp_submit, '$storage_location', $vendor_id)";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// echo "<h3>$sql</h3>";

	$sql = "SELECT LAST_INSERT_ID()";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$lot_id = mysql_result($result, 0, 0);
	// echo "<h3>$sql</h3>";

	$sql = "INSERT INTO inventorymovements 
					(LotID, ProductNumberInternal, TransactionDate, Quantity, TransactionType, Remarks, MovementStatus) 
				VALUES 
					($lot_id, $pni, '".date('Y-m-d')."', $total, 1, '$vendor - Moved From Lab', 'C')";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// echo "<h3>$sql</h3>";

	$sql = "UPDATE lots SET InventoryMovementTransactionNumber= LAST_INSERT_ID() WHERE ID='" . $lot_id ."'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// echo "<h3>$sql</h3>";

	$sql = "INSERT INTO receipts 
					(LotID, Quantity, PackSize, UnitOfMeasure, PackagingType, EmployeeID, Status) 
				VALUES 
					($lot_id, $quantity, $pack_size, '$units', '$pack_in', $_SESSION[user_id], 'A')";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// echo "<h3>$sql</h3>";

		$_SESSION['note'] = "Inventory successfully moved from the lab<br/>";

		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "window.opener.location.reload()\n";
		echo "window.close()\n";
		echo "</SCRIPT>\n";

	}

}

include("inc_pop_header.php");

?>

<?php if ( "" != $error_message ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} 

$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

?>
<h3><?php echo "$pni - $description" ?></h3>
<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<FORM METHOD="post" ACTION="pop_add_lab_sample_to_inventory.php">
<input type="hidden" name="pni" id="pni" value="<?php echo $pni ?>" />
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD align="right"><B CLASS="black">Vendor:</B><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="5"></TD>
		<td><input type="hidden" name="vendor_id" id="vendor_id" value="<?php echo $vendor_id?>"/><input type="text" name="vendor" id="vendor" value="<?php echo $vendor ?>" /></td>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD align="right"><B CLASS="black">Lot Number:</B><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="5"></TD>
		<td><input type="text" name="lot_no" id="lot_no" value="<?php echo $lot_no ?>" /></td>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD align="right"><B CLASS="black">Manufacture Date:</B><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="5"></TD>
		<td><input type="text" name="date_man" id="date_man" value="<?php echo $date_man ?>" /></td>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD align="right"><B CLASS="black">Expiration Date:</B><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="5"></TD>
		<td><input type="text" name="date_exp" id="date_exp" value="<?php echo $date_exp ?>" /></td>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD align="right"><B CLASS="black">Quantity:</B><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="5"></TD>
		<TD><INPUT TYPE="text" id="quantity" NAME="quantity" SIZE="26" VALUE="<?php
		if ( is_numeric($quantity) ) {
			echo number_format($quantity, 2);
		} else {
			echo ($quantity);
		}
		?>" /></TD>
	</TR>

	<TR>
		<TD align="right"><B CLASS="black">Pack Size:</B><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="5"></TD>
		<td><input type="text" name="pack_size" id="pack_size" SIZE="26" VALUE="<?php
		if ( is_numeric($pack_size) ) {
			echo number_format($pack_size, 2);
		} else {
			echo ($pack_size);
		}
		?>" /></TD>
	</TR>
	
	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>
	<?php if ( substr($pni,0,1) != '6') { ?>
	<TR>
		<TD align="right"><B CLASS="black">Units:</B><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="5"></TD>
		<td><select name="units" id="units"><?php printInventoryUnitsOptions($units) ?></select></td>
	</TR>


    <?php } else { ?>
    	<TR>
		<TD align="right"><B CLASS="black">Units:</B><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="5"></TD>
		<td><select name="units" id="units" disabled="disabled">
				<option value=\"N/A\" SELECTED>N/A</option>
			</select></td>
		</TR>

 	<?php } ?>

 	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD align="right"><B CLASS="black">Pack In:</B><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="5"></TD>
		<td><select name="pack_in" id="pack_in" ><?php printVendorPackagingTypeOptions($pack_in); ?></select></td>
	</TR>
 	
	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>
    
	<TR>
		<TD align="right"><B CLASS="black">Storage Location:</B><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="5"></TD>
		<td><select name="storage_location" id="storage_location" ><?php printStorageLocationOptions($storage_location); ?></select></td>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD></TD>
		<TD><INPUT TYPE="submit" name="save" VALUE="Save"> <INPUT TYPE="button" VALUE="Cancel" onClick="window.close()"></TD>
	</TR></FORM>
</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><br/><br/>

<script type="text/javascript">

$(document).ready(function(){
	$(":input#date_man").datepicker();
	$(":input#date_exp").datepicker({changeYear: true});
	$(":submit").click(function() {
		$("#action").val(this.name);
		switch (this.name)
		{
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
	$("#vendor").autocomplete("search/vendors_for_pni.php", {
		cacheLength: 1,
		selectFirst: false,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		width: 350,
		max:50,
		multipleSeparator: "¬",
		scrollheight: 350,
		extraParams: { pni:<?php echo $pni ?> }
	});
	$("#vendor").result(function(event, data, formatted) {
		if (data)
			$("#vendor_id").val(data[1]);
	});

});
function validated() {
	// verify all fields have a value that need one;
	var alertMessage = "";
	if ("" == $("#vendor").val()) {
		alertMessage+="Vendor is a required Field\n";
		$("#vendor").attr("style", "border: solid 1px red");
	} else {
		$("#vendor").attr("style", "border: none");
	}
	if ("" == $("#quantity").val()) {
		alertMessage+="Quantity is a required Field\n";
		$("#quantity").attr("style", "border: solid 1px red");
	} else {
		$("#quantity").attr("style", "border: none");
	}
	if ("" == $("#lot_no").val()) {
		alertMessage+="Lot Number is a required Field\n";
		$("#lot_no").attr("style", "border: solid 1px red");
	} else {
		$("#lot_no").attr("style", "border: none");
	}
	if ("" == $("#units").val()) {
		alertMessage+="Units is a required Field\n";
		$("#units").attr("style", "border: solid 1px red");
	} else {
		$("#units").attr("style", "border: none");
	}
	if ("" == $("#storage_location").val()) {
		alertMessage+="Storage Location is a required Field\n";
		$("#storage_location").attr("style", "border: solid 1px red");
	} else {
		$("#storage_location").attr("style", "border: none");
	}
	if ("" == $("#pack_in").val()) {
		alertMessage+="Pack In is a required Field\n";
		$("#pack_in").attr("style", "border: solid 1px red");
	} else {
		$("#pack_in").attr("style", "border: none");
	}
	if ("" == $("#pack_size").val()) {
		alertMessage+="Pack Size is a required Field\n";
		$("#pack_size").attr("style", "border: solid 1px red");
	} else {
		$("#pack_size").attr("style", "border: none");
	}
	return alertMessage;
}
</script>


<?php 
include("inc_footer.php"); 

function getNextLotSequenceNumber($lot_number) {
	global $link;
	$sql  ="SELECT LotSequenceNumber 
			FROM lots 
			WHERE LotNumber='$lot_number' 
			ORDER BY LotSequenceNumber DESC LIMIT 1";
	$result = mysql_query($sql, $link) or 
		die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$result_count = mysql_num_rows($result);
	if (0 == $result_count) {
		return(1);
	}
	else {
		return(mysql_result($result,0,0) + 1);
	}
}
?>