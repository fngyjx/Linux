<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN AND FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 4 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( $_REQUEST['pni'] != '' ) {
	$pni = $_REQUEST['pni'];
}

if ( $_REQUEST['amt'] != '' ) {
	$amt = $_REQUEST['amt'];
}

if ( $_REQUEST['seq'] != '' ) {
	$seq = $_REQUEST['seq'];
}

if ( $_REQUEST['order_num'] != '' ) {
	$order_num = $_REQUEST['order_num'];
}

if ( $_REQUEST['rid'] != '' ) {
	$rid = $_REQUEST['rid'];
}

if ( isset($_REQUEST['action']) ) {
	$action = $_REQUEST['action'];
}

if ( isset($_REQUEST['update_prod']) ) {
	$update_prod = $_REQUEST['update_prod'];
}

include('inc_global.php');


$quantity='';
if ( !empty($_POST) and $rid == "" ) {

	$LotIDAndQuantity = $_POST['InventoryPieces'];
	$data_pieces = explode("~", $LotIDAndQuantity);
	$LotID = $data_pieces[0];
	$InventoryCount = $data_pieces[1];
	$quantity = $_POST['quantity'];
	$current_total = $_POST['current_total'];

	// check_field() FUNCTION IN global.php
	check_field($quantity, 3, 'Amount');
	if ( !$error_found ) {
		if ( $quantity > $InventoryCount ) {
			$error_found = true;
			$error_message .= "Amount entered is greater than the inventory on hand<BR>";
		}
	}
	if ( !$error_found ) {
		if ( $quantity < 1 ) {
			$error_found = true;
			$error_message .= "Please enter a postive number for quantity<BR>";
		}
	}
	if ( !$error_found ) {
		if ( $quantity > $amt - $current_total ) {
			$error_found = true;
			$error_message .= "Amount entered is greater than quantity needed<BR>";
		}
	}

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$quantity = escape_data($quantity);

		//$sql = "SELECT TransactionNumber FROM inventorymovements WHERE LotNumber = '" . $LotNumber . "' AND LotSequenceNumber = '" . $LotSequenceNumber . "' AND ProductNumberInternal = '" . $pni . "'";
		//$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//$row = mysql_fetch_array($result);
		//$TransactionNumber = $row['TransactionNumber'];

		$sql = "INSERT INTO inventorymovements (LotID, ProductNumberInternal, TransactionDate, Quantity, TransactionType, Remarks, MovementStatus) VALUES ('$LotID', '$pni', '" . date("Y-m-d H:i:s") . "', '".QuantityConvert($quantity,'lbs','grams')."', '2', '', 'C')";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$TransactionNumber = mysql_insert_id();

		$sql = "INSERT INTO customerorderdetaillotnumbers (CustomerOrderNumber, ProductNumberInternal, CustomerOrderSeqNumber, LotID, InventoryMovementTransactionNumber, QuantityUsedFromThisLot) VALUES ('" . $order_num . "', '" . $pni . "', '" . $seq . "', '" . $LotID . "', '" . $TransactionNumber . "', '" . $quantity . "')";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

		header("location: pop_select_lots.php?pni=" . $pni . "&amt=" . $amt . "&seq=" . $seq . "&order_num=" . $order_num);
		exit();
	
	}



} elseif ( !empty($_POST) ) {

	$quantity = $_POST['quantity'];
	$InventoryMovementTransactionNumber = $_POST['InventoryMovementTransactionNumber'];
	$current_total = $_POST['current_total'];

	$sql = "SELECT InventoryCount FROM vwinventory WHERE ProductNumberInternal = '" . $pni . "' AND InventoryCount > 0";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$InventoryCount = $row['InventoryCount'];

	// check_field() FUNCTION IN global.php
	check_field($quantity, 3, 'Amount');
	if ( !$error_found ) {
		if ( $quantity > $InventoryCount ) {
			$error_found = true;
			$error_message .= "Amount entered is greater than the inventory on hand<BR>";
		}
	}
	if ( !$error_found ) {
		if ( $quantity < 1 ) {
			$error_found = true;
			$error_message .= "Please enter a postive number for quantity<BR>";
		}
	}
	if ( !$error_found ) {
		if ( $quantity > $amt - ($current_total - $quantity) ) {
			$error_found = true;
			$error_message .= "Amount entered is greater than quantity needed<BR>";
			$error_message .= "qty: $quantity<BR>";
			$error_message .= "amt: $amt<BR>";
			$error_message .= "current: $current_total<BR>";
		}
	}
echo "quantity " . $quantity . "<BR>";
echo "amt " . $amt . "<BR>";
echo "current_total " . $current_total . "<BR>";
die();
	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$quantity = escape_data($quantity);

		$sql = "UPDATE customerorderdetaillotnumbers SET " .
		"QuantityUsedFromThisLot = '" . $quantity . "' " .
		"WHERE RecordID = " . $rid;
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

		$sql = "UPDATE inventorymovements SET " .
		"Quantity = '" . $quantity . "' " .
		"WHERE TransactionNumber = " . $InventoryMovementTransactionNumber;
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

		header("location: pop_select_lots.php?pni=" . $pni . "&amt=" . $amt . "&seq=" . $seq . "&order_num=" . $order_num);
		exit();

	}

}


if ( $_GET['action'] == "delete_lot" ) {
	$sql = "DELETE FROM customerorderdetaillotnumbers WHERE RecordID = " . $_GET['RecordID'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	
	$sql = "DELETE FROM inventorymovements WHERE TransactionNumber = " . $_GET['mtn'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	header("location: pop_select_lots.php?pni=" . $_GET['pni'] . "&amt=" . $_GET['amt'] . "&seq=" . $_GET['seq'] . "&order_num=" . $_GET['order_num']);
	exit();
}



include("inc_pop_header.php"); ?>



<B>Assign Lot Numbers</B><BR><BR>



<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
	unset($error_found);
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>



<?php

$sql = "SELECT productmaster.Designation, externalproductnumberreference.ProductNumberExternal FROM productmaster LEFT JOIN externalproductnumberreference USING (ProductNumberInternal) WHERE externalproductnumberreference.ProductNumberInternal = '" . $pni . "'";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);

?>


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
	<TR>
		<TD><B CLASS="black">Product:</B></TD>
		<TD><?php echo $row['Designation'];?> - abelei#<?php echo $row['ProductNumberExternal'];?></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Qty Ordered (lbs):</B></TD>
		<TD><?php echo number_format($amt, 2);?></TD>
	</TR>
</TABLE><BR>



<?php

$sql = "SELECT SUM(QuantityUsedFromThisLot) AS current_total
FROM customerorderdetaillotnumbers, lots
WHERE lots.ID = customerorderdetaillotnumbers.LotID
AND ProductNumberInternal = " . $pni . "
AND CustomerOrderNumber = " . $order_num;
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
$current_total = $row['current_total'];

if ( $current_total < $amt ) {

	$sql = "SELECT *, lots.LotNumber AS LotNumber FROM vwinventory,lots WHERE lots.ID = vwinventory.LotID AND ProductNumberInternal = '" . $pni . "' AND InventoryCount > 0";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	if ( mysql_num_rows($result) > 0 ) {
		//echo $sql;

		?>

		<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
		<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
		<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

		<FORM ACTION="pop_select_lots.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="pni" VALUE="<?php echo $pni;?>">
		<INPUT TYPE="hidden" NAME="amt" VALUE="<?php echo $amt;?>">
		<INPUT TYPE="hidden" NAME="seq" VALUE="<?php echo $seq;?>">
		<INPUT TYPE="hidden" NAME="order_num" VALUE="<?php echo $order_num;?>">
		<INPUT TYPE="hidden" NAME="rid" VALUE="<?php echo $rid;?>">
		<INPUT TYPE="hidden" NAME="current_total" VALUE="<?php echo $current_total;?>">

			<TR>
				<TD><B>Assign Lot#:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="3" HEIGHT="1"></TD>
				<TD><SELECT NAME="InventoryPieces">
				<?php while ( $row = mysql_fetch_array($result) ) { 
					$q_lbs = QuantityConvert($row[InventoryCount],'grams', 'lbs');
					?>
					<OPTION VALUE="<?php echo $row['LotID'] . "~" . $q_lbs;?>">Lot# <?php echo $row['LotNumber'];?> (count: <?php echo number_format($q_lbs, 2);?>)</OPTION>
				<?php } ?>
				</SELECT></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
				<TD><B>Amount (lbs):</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="3" HEIGHT="1"></TD>
				<TD><INPUT TYPE="text" NAME="quantity" VALUE="<?php echo (''==$quantity ? number_format($amt, 2) : '');?>" SIZE="10"></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
				<TD><INPUT TYPE="submit" VALUE="Add" CLASS="submit"></TD>
			</TR></FORM>

		</TABLE>

		</TD></TR></TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE><BR><BR>

	<?php } else { ?>

		<B>No inventory found for this product number</B>

	<?php

	} 

}

?>






<?php

$sql = "SELECT customerorderdetaillotnumbers.*, lots.LotNumber AS LotNumber, DateManufactured
FROM customerorderdetaillotnumbers, lots
WHERE lots.ID = customerorderdetaillotnumbers.LotID
AND ProductNumberInternal = " . $pni . "
AND CustomerOrderNumber = " . $order_num;
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$c = mysql_num_rows($result);
//echo $sql . "<BR>";

if ( $c > 0 ) {

	$bg = 0; ?>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">

		<TR>
			<TD></TD>
			<TD><B>Lot#</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			<TD><B>Seq#</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			<TD><B>Manufactured</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			<TD ALIGN=RIGHT><B>Amount Used (lbs)</B></TD>
		</TR>

		<TR>
			<TD COLSPAN=9><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
		</TR>

		<?php 

		while ( $row = mysql_fetch_array($result) ) {
			if ( $bg == 1 ) {
				$bgcolor = "#F3E7FD";
				$bg = 0;
			} else {
				$bgcolor = "whitesmoke";
				$bg = 1;
			}

				$ing_form_status = "";
			if ( $_REQUEST['update_prod'] != $row['RecordID'] ) {
				$ing_form_status = "readonly=\"readonly\"";
			}

			?>

			<FORM ACTION="pop_select_lots.php" METHOD="post">
			<INPUT TYPE="hidden" NAME="pni" VALUE="<?php echo $pni;?>">
			<INPUT TYPE="hidden" NAME="amt" VALUE="<?php echo $amt;?>">
			<INPUT TYPE="hidden" NAME="seq" VALUE="<?php echo $seq;?>">
			<INPUT TYPE="hidden" NAME="order_num" VALUE="<?php echo $order_num;?>">
			<INPUT TYPE="hidden" NAME="rid" VALUE="<?php echo $row['RecordID'];?>">
			<INPUT TYPE="hidden" NAME="InventoryMovementTransactionNumber" VALUE="<?php echo $row['InventoryMovementTransactionNumber'];?>">
			<INPUT TYPE="hidden" NAME="action" VALUE="<?php echo $action;?>">
			<INPUT TYPE="hidden" NAME="update_prod" VALUE="<?php echo $row['RecordID'];?>">
			<INPUT TYPE="hidden" NAME="current_total" VALUE="<?php echo $current_total;?>">

			<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
				<?php //if ( $_REQUEST['update_prod'] == '' ) { ?>
					<!-- <TD WIDTH="16"><A HREF="JavaScript:location.href='pop_select_lots.php?update_prod=<?php //echo $row['RecordID'];?>&pni=<?php //echo $pni;?>&amt=<?php //echo $amt;?>&seq=<?php //echo $seq;?>&order_num=<?php //echo $order_num;?>'"><IMG SRC="images/pencil.gif" BORDER="0"></A></TD> -->
				<?php //} else { ?>
					<!-- <TD WIDTH="16"><INPUT TYPE="image" SRC="images/pencil.gif" BORDER="0"></TD> -->
				<?php //} ?>
				<TD WIDTH="16"><A HREF="JavaScript:delete_lot('<?php echo $row['RecordID'];?>', '<?php echo $pni;?>', '<?php echo $amt;?>', '<?php echo $seq;?>', '<?php echo $order_num;?>', '<?php echo $row['InventoryMovementTransactionNumber'];?>')"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A></TD>
				<TD><?php echo $row['LotNumber'] ?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=CENTER><?php echo $row['CustomerOrderSeqNumber'] ?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><?php
				if ( $row['DateManufactured'] != '' ) {
					$DateManufactured = date("m/d/Y", strtotime($row['DateManufactured']));
				} else {
					$DateManufactured = '';
				}
				echo $DateManufactured;
				?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><INPUT TYPE="text" NAME="quantity" VALUE="<?php echo number_format($row['QuantityUsedFromThisLot'], 2);?>" SIZE="10" <?php echo $ing_form_status;?> STYLE="text-align:right"> <INPUT TYPE="hidden" VALUE="Save" <?php echo $ing_form_status;?> CLASS="submit"></TD>
			</TR>

			</FORM>

		<?php }
		
		if ( $bg == 1 ) {
			$bgcolor = "#F3E7FD";
			$bg = 0;
		} else {
			$bgcolor = "whitesmoke";
			$bg = 1;
		}
			
		?>

		<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
			<TD COLSPAN=8 ALIGN=RIGHT><B>Total:<INPUT TYPE="text" NAME="quantity" VALUE="<?php echo number_format($current_total, 2);?>" SIZE="8" <?php echo $ing_form_status;?> STYLE="text-align:right;color:#330066"></B></TD>
		</TR>

	</TABLE>

<?php } ?>



<BR><BR>

<FORM><INPUT TYPE="button" VALUE="Close" CLASS="button_pop" onClick="window.close()"></FORM>




<script LANGUAGE=JAVASCRIPT>
 <!-- Hide
$(document).ready(function(){
	$(":input[readonly]").addClass("readOnly");
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");
});

function delete_lot(RecordID, pni, amt, seq, order_num, mtn) {
	if ( confirm('Are you sure you want to delete this item?') ) {
		document.location.href = "pop_select_lots.php?action=delete_lot&RecordID=" + RecordID + "&pni=" + pni + "&amt=" + amt + "&seq=" + seq + "&order_num=" + order_num + "&mtn=" + mtn
	}
}

 // End -->
 
</script>



<?php include("inc_footer.php"); ?>