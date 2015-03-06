<?php

include('inc_ssl_check.php');
if ( !isset($_SESSION) ) { session_start(); }

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ONLY ADMIN AND QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');

// set
$note=""; $lot_id="";$x_id = ""; $x_type=""; $x_date=""; $quantity=""; $remarks=""; $lot=""; $lot_sequnce=""; $pni=""; $description=""; $units=""; $error_message="";
if ( isset($_SESSION[note]) ) { $note = $_SESSION[note]; unset($_SESSION[note]); }
if ( isset($_REQUEST[lot_id]) ) $lot_id = $_REQUEST[lot_id];
if ( isset($_REQUEST[x_id]) ) { $x_id = $_REQUEST[x_id]; }
if(""!=$x_id){
	$sql = "SELECT TransactionDate, Quantity, TransactionType, Remarks, LotNumber, LotSequenceNumber , 
						pm.ProductNumberInternal, Designation, Natural_OR_Artificial as NA, Kosher, ProductType, UnitOfMeasure AS units
					FROM inventorymovements, lots, productmaster AS pm
					WHERE pm.ProductNumberInternal=inventorymovements.ProductNumberInternal AND lots.ID=inventorymovements.LotID AND TransactionNumber=$x_id LIMIT 1";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	if ( 0 < mysql_num_rows($result) ) {
		$row = mysql_fetch_array($result);
		$x_type = $row[TransactionType];
		$units = $row[units];
		$quantity = QuantityConvert($row[Quantity],'grams',$units);
		$remarks = $row[Remarks];
		$pni = $row[ProductNumberInternal];
		$lot = $row[LotNumber];
		$lot_sequence = $row[LotSequenceNumber];
		$description = (""!=$row[NA] ? $row[NA]." " : "").$row[Designation].("" != $row[ProductType] ? " - ".$row[ProductType] : "").("" != $row[Kosher] ? " - ".$row[Kosher] : "");;
		if ( '' != $row[TransactionDate] ) {
			$x_date = date('m/d/Y',strtotime($row[TransactionDate]));
		}
	}
}
else if (""!=$lot_id) {
	$sql = "SELECT LotNumber, LotSequenceNumber , pm.ProductNumberInternal, Designation, Natural_OR_Artificial as NA, Kosher, ProductType, UnitOfMeasure AS units
					FROM inventorymovements, lots, productmaster AS pm
					WHERE pm.ProductNumberInternal=inventorymovements.ProductNumberInternal AND lots.ID=inventorymovements.LotID AND lots.ID=$lot_id LIMIT 1";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	if ( 0 < mysql_num_rows($result) ) {
		$row = mysql_fetch_array($result);
		$x_type = "";
		$units = $row[units];
		$quantity = 0;
		$remarks = "";
		$pni = $row[ProductNumberInternal];
		$lot = $row[LotNumber];
		$lot_sequence = $row[LotSequenceNumber];
		$description = (""!=$row[NA] ? $row[NA]." " : "").$row[Designation].("" != $row[ProductType] ? " - ".$row[ProductType] : "").("" != $row[Kosher] ? " - ".$row[Kosher] : "");;
		$x_date = date('m/d/Y');
	}
}

if ( isset($_REQUEST[x_type]) ) { $x_type = $_REQUEST[x_type]; }
if ( isset($_REQUEST[x_date]) ) { $x_date=escape_data($_REQUEST[x_date]); }
if ( isset($_REQUEST[units]) ) { $remarks = escape_data($_REQUEST[units]); }
if ( isset($_REQUEST[quantity]) ) { $quantity = is_numeric($_REQUEST[quantity]) ? round($_REQUEST[quantity],2) : ""; }
if ( isset($_REQUEST[remarks]) ) { $remarks = escape_data($_REQUEST[remarks]); }

if (""==$x_id && ""==$x_date) { // if new record, set default date to today
	$x_date=date('m/d/Y');
}

if (""==$lot_id && ""==$x_id) {
	$_SESSION['note'] = "Lot or Inventory Movement ID is required";
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
}


if ( !empty($_POST) ) {

// validate
	$x_date_parts = explode("/", $x_date);
	$x_date_submit = (is_numeric($x_date_parts[2]) ? $x_date_parts[2] : 0)."-".(is_numeric($x_date_parts[0]) ? $x_date_parts[0] : 0)."-".(is_numeric($x_date_parts[1]) ? $x_date_parts[1] : 0);
	if (!checkdate((is_numeric($x_date_parts[0]) ? $x_date_parts[0] : 0), (is_numeric($x_date_parts[1]) ? $x_date_parts[1] : 0), (is_numeric($x_date_parts[2]) ? $x_date_parts[2] : 0))) 
		$error_message .= "Transaction date incomplete or invalid.<br/>";
	if ( "lbs"!=$units && "grams" != $units && "kilos"!=$units)
		$error_message .= "Units Error - please edit the materials page for this item and set the units.<br/>";
	if ( ""==$quantity || 0 >= $quantity)
		$error_message .= "Quantity must be numeric and greater than 0 [Error - $quantity].<br/>";
	if ( (3 != $x_type) && (4 != $x_type) && (7 != $x_type) )
		$error_message .= "Invalid Transaction Type. [Error - $x_type]<br/>";
	
	if ( !$error_message ) {

		$quantity_submit = QuantityConvert($quantity,$units,'grams');
		if( "" == $x_id ) { // if it's a new record
			$sql = "SELECT DISTINCT ProductNumberInternal FROM inventorymovements WHERE LotID=$lot_id LIMIT 1";
			$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			if ( 1 == mysql_num_rows($result) ) {
				$row = mysql_fetch_row($result);
				$pni = $row[0];
			} else { 
				$_SESSION['note'] = "There are no other transactions on this lot! Error. - $sql<br/>";
				echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
				echo "window.opener.location.reload()\n";
				echo "window.close()\n";
				echo "</SCRIPT>\n";
				exit;
			}
			$sql = "INSERT INTO inventorymovements (LotID, ProductNumberInternal, TransactionDate, Quantity, TransactionType, Remarks, MovementStatus) VALUES ($lot_id, $pni, '$x_date_submit', $quantity_submit, $x_type, '$remarks', 'C')";
		} else {
			$sql = "UPDATE inventorymovements SET TransactionDate='$x_date_submit', Quantity=$quantity_submit, TransactionType=$x_type, Remarks='$remarks' WHERE TransactionNumber=$x_id";
		}
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

		$_SESSION['note'] = "Movement successfully saved<br/>";

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
<h3>Lot Number and Sequence: <?php echo "$lot - $lot_seq" ?></h3>
<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<FORM METHOD="post" ACTION="pop_add_inventory_movement.php">
<input type="hidden" id="x_id" name="x_id" value="<?php echo $x_id ?>" />
<input type="hidden" id="lot_id" name="lot_id" value="<?php echo $lot_id ?>" />
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD><B CLASS="black">Transaction Type:</B></TD>
		<TD>
			<SELECT id="x_type" NAME="x_type">
					<OPTION VALUE=""></OPTION>
					<OPTION VALUE="3" <?php echo (3 == $x_type ? "SELECTED ": "") ?>>usage for samples</OPTION>
					<OPTION VALUE="4" <?php echo (4 == $x_type ? "SELECTED ": "") ?>>destroyed inventory</OPTION>
					<OPTION VALUE="7" <?php echo (7 == $x_type ? "SELECTED ": "") ?>>inventory adjustment</OPTION>
			</SELECT>
		</TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Date:</B></TD>
		<td><input type="text" name="x_date" id="x_date" value="<?php echo $x_date ?>" /></td>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Quantity (<?php echo $units ?>):</B></TD>
		<TD><input type="hidden" name="units" value="<?php echo $units ?>" /><INPUT TYPE="text" id="quantity" NAME="quantity" SIZE=26 VALUE="<?php
		if ( is_numeric($quantity) ) {
			echo number_format($quantity, 2);
		} else {
			echo $quantity;
		}
		?>" /></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD></TR>

	<TR>
		<TD><B CLASS="black">Remarks:</B></TD>
		<TD><INPUT TYPE="text" id="remarks" NAME="remarks" SIZE=26 VALUE="<?php echo stripslashes($remarks) ?>"></TD>
	</TR>

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
	$(":input#x_date").datepicker();
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
});
function validated() {
	// verify all fields have a value that need one;
	var alertMessage = "";
	if ("" == $("#x_type").val()) {
		alertMessage+="Transaction Type is a required Field\n";
		$("#x_type").attr("style", "border: solid 1px red");
	} else {
		$("#x_type").attr("style", "border: none");
	}
	if ("" == $("#quantity").val() || 0 > $("#quantity").val() ) {
		alertMessage+="Quantity must be 0 or greater.\n";
		$("#quantity").attr("style", "border: solid 1px red");
	} else {
		$("#quantity").attr("style", "border: none");
	}
	if ("" == $("#x_date").val()) {
		alertMessage+="Date is a required Field\n";
		$("#x_date").attr("style", "border: solid 1px red");
	} else {
		$("#x_date").attr("style", "border: none");
	}
	return alertMessage;
}
</script>


<?php include("inc_footer.php"); ?>