<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB AND QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 5 and $rights != 6) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');
include('search/system_defaults.php');


if ( isset($_REQUEST['sbsn']) ) {
	$sbsn = $_REQUEST['sbsn'];
}

if ( isset($_REQUEST['pne']) ) {
	$pne = $_REQUEST['pne'];
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

$action = "";
if ( isset($_REQUEST['action']) ) {
	$action = $_REQUEST['action'];
}



if ( !empty($_POST) ) {

	$created_by = $_POST['created_by'];
	$date = date("Y-m-d", strtotime($_POST['date'] . " 00:00:00"));
	$customer = $_POST['customer'];
	//$contact_id = $_POST['contact_id'];
	$contact_name = $_POST['contact_name'];
	$customer_id = $_POST['customer_id'];
	$amount = $_POST['amount'];
	$unit = $_POST['unit'];
	$abelei_number = $_POST['abelei_number'];

	// check_field() FUNCTION IN global.php
	check_field($customer_id, 1, 'Customer');
	//check_field($contact_id, 1, 'Contact');
	check_field($amount, 3, 'amount');

	if ( !$error_found ) {
		if ( $unit == "gal" ) {
			if ( $amount > 1 ) {
				$error_found = true;
				$error_message .= "Amount must be 1 gallon or less<BR>";
			}
		} elseif ( $unit == "lbs" ) {
			if ( $amount > 8.35 ) {
				$error_found = true;
				$error_message .= "Amount must be 8.35 lbs or less<BR>";
			}
		} elseif ( $unit == "grams" ) {
			if ( $amount > 3787 ) {
				$error_found = true;
				$error_message .= "Amount must be 3,787 grams or less<BR>";
			}
		} else {
			if ( $amount > 3.79 ) {
				$error_found = true;
				$error_message .= "Amount must be 3.79 kilograms or less<BR>";
			}
		}
	}

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$created_by = escape_data($created_by);
		$amount = escape_data($amount);
		$abelei_number = escape_data($abelei_number);

		if ( $contact_name == '' ) {
			$contact_name = "NULL";
		} else {
			$contact_name = "'" . escape_data($contact_name) . "'";
		}
	
		if ( $sbsn != '' ) {
			$sql = "UPDATE sample_batchsheets SET " .
			"contact = " . $contact_name . ", " .
			"customer_id = '" . $customer_id . "', " .
			"amount = '" . $amount . "', " .
			"unit = '" . $unit . "', " .
			"abelei_number = '" . $abelei_number . "' " .
			"WHERE sample_batchsheet_number = " . $sbsn;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		} else {
			$sql = "INSERT INTO sample_batchsheets (created_by, date, contact, customer_id, amount, unit, abelei_number) VALUES ('" . $created_by . "', '" . $date . "', " . $contact_name . ", '" . $customer_id . "', '" . $amount . "', '" . $unit . "', '" . $abelei_number . "')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$sbsn = mysql_insert_id();
		}

		header("location: reports/sample_batch_sheet.php?sbsn=" . $sbsn);
		exit();

	} else {
		$date = date("m/d/Y", strtotime($date . " 00:00:00"));
	}

} elseif ( $sbsn != '' ) {

	$sql = "SELECT * FROM sample_batchsheets WHERE sample_batchsheet_number = " . $sbsn;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$created_by = $row['created_by'];
	$date = date("m/d/Y", strtotime($row['Date']));
	$contact_name = $row['contact'];
	$customer_id = $row['customer_id'];
	$amount = $row['amount'];
	$unit = $row['unit'];

	$sql = "SELECT name FROM customers WHERE customer_id = " . $customer_id;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$customer = $row['name'];

//	$sql = "SELECT first_name, last_name FROM customer_contacts WHERE contact_id = " . $contact_id;
//	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
//	$row = mysql_fetch_array($result);
//	$contact_name = $row['first_name'] . " " . $row['last_name'];

} else {
	$created_by = $_SESSION['first_nameCookie'] . " " . $_SESSION['last_nameCookie'];
	$date = date("m/d/Y");
	$customer = '';
//	$contact_id = '';
	$contact_name = '';
	$customer_id = '';
	$amount = '';
	$unit = '';
	$abelei_number = $pne;
}



//if ( $_REQUEST['update'] != 1 ) {
//	$form_status = "readonly=\"readonly\"";
//} else {
	$form_status = "";
//}



$units = array("gal"=>"Gallon", "lbs"=>"Lbs", "grams"=>"Grams", "kilos"=>"Kilograms");

include("inc_pop_header.php");

?>



<script type="text/javascript">

$(document).ready(function(){
	$(":input[readonly]").addClass("readOnly");
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");
	$("#customer[readonly!=readonly]").autocomplete("search/customers_by_name.php", {
		cacheLength: 1,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max:50,
		multipleSeparator: "¬",
		scrollheight: 350,
		selectFirst: false
	});
	$("#customer[readonly!=readonly]").change(function() {
		if ("" == $("#customer").val())
		{
			$("#contact_name").val("");
			//$("#contact_id").val("");
			$("#contactdiv").html("");
			$("#contact_name").unautocomplete();
		}
	});
	$("#customer[readonly!=readonly]").result(function(event, data, formatted) {
		if (data) {
			$("#contact_name").val("");
			//$("#contact_id").val("");
			$("#contactdiv").html("");
			$("#contact_name").unautocomplete();
			$("#customer_id").val(data[1]);
	$("#contact_name[readonly!=readonly]").autocomplete("search/contacts_by_customer_id.php", {
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		width: 350,
		multipleSeparator: "¬",
		scrollheight: 350,
		extraParams: { c_id: function() { return $("#customer_id").val(); } }
	});
	$("#contact_name[readonly!=readonly]").result(function(event, data, formatted) {
		if (data)
		{
			$("#contact_name").val(data[0]);
			//$("#contact_id").val(data[1]);
			$("#contactdiv").html(data[2]);
		}
	});
	// result_string=search('search/contacts_by_customer_id',data[1]);
			// update_id('update/contacts_by_customer_id','contactspan',data[1]);
		}
	});

	$("#contact_name[readonly!=readonly]").change(function() {
		if ("" == $("#customer").val())
		{
			$("input#contact_name").flushCache();
			$("input#contact_name").search();
			$("#contact_name").val("");
			//$("#contact_id").val("");
			$("#contactdiv").html("");
		}
		if ("" == $("#contact_name").val())
		{
			//$("#contact_id").val("");
			$("#contactdiv").html("");
		}
	});
	$(":submit").click(function() {
	});
	
		$("#Quantity").change(function() {
		if (!isNaN($(this).val()) && !isNaN($("#PackSize").val())) {
			$("#TotalQuantityOrdered").val($(this).val() * $("#PackSize").val());
		}
	});
	$("#PackSize").change(function() {
		if (!isNaN($(this).val()) && !isNaN($("#Quantity").val())) {
			$("#TotalQuantityOrdered").val($(this).val() * $("#Quantity").val());
		}
	});
});
function validate() {
	switch (document.getElementById("action").value)
	{
		case 'delete':
			var answer = confirm("Delete this order?")
			if (answer) { return true; } else { return false; }
			break;
		default:
			break;
	}
}

</script>



<?php //if ( $order_num != '' or $action == 'edit' ) { ?>

	<?php if ( $error_found ) {
		echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
		unset($error_found);
	} ?>

	<?php if ( $note ) {
		echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
	} ?>

	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

	<FORM NAME="header_info" ACTION="pop_sample_batch_sheet.php" METHOD="post">
	<INPUT TYPE="hidden" NAME="abelei_number" VALUE="<?php echo $abelei_number;?>">

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">

				<TR>
					<TD><B>Created by:</B></TD>
					<TD><?php echo $created_by; ?><INPUT TYPE="hidden" NAME="created_by" VALUE="<?php echo $created_by;?>"></TD>
				</TR>

				<TR>
					<TD><B>Date:</B></TD>
					<TD><?php echo $date; ?><INPUT TYPE="hidden" NAME="Date" VALUE="<?php echo $date;?>"></TD>
				</TR>

				<TR>
					<TD><B>Customer:</B></TD>
					<TD><INPUT TYPE="text" ID="customer" NAME="customer" VALUE="<?php echo $customer;?>" SIZE=26 <?php echo $form_status;?>>
					<INPUT TYPE="hidden" ID="customer_id" NAME="customer_id" VALUE="<?php echo $customer_id;?>"></TD>
				</TR>

				<TR>
					<TD><B>Contact:</B></TD><!-- ID="contact_name" -->
					<TD><INPUT TYPE="text" NAME="contact_name" SIZE=26 VALUE="<?php echo $contact_name;?>" <?php echo $form_status;?>><!-- <INPUT TYPE="hidden" ID="contact_id" NAME="contact_id" VALUE="<?php //echo $contact_id;?>"> -->
					</TD>
				</TR>

				<TR VALIGN=TOP>
					<TD><B>Amount:</B></TD>
					<TD><INPUT TYPE="text" NAME="amount" VALUE="<?php echo $amount;?>" SIZE="20" <?php echo $form_status;?>></TD>
				</TR>

				<TR VALIGN=TOP>
					<TD><B>Units:</B></TD>
					<TD><SELECT NAME="unit" STYLE="font-size: 7pt" <?php echo $form_status;?>>
					<?php
					foreach ( $units as $key => $value ) {
						if ( $value == $unit ) {
							echo "<OPTION VALUE='$key' SELECTED>$value</OPTION>";
						} else {
							echo "<OPTION VALUE='$key'>$value</OPTION>";
						}
					}
					?>
					</SELECT></TD>
				</TR>

			</TABLE>

			<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="10"></TD></TR>
			<TR><TD COLSPAN=2 BGCOLOR="#CDCDCD"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="2"></TD></TR>
			<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="10"></TD></TR>

			<TR VALIGN=TOP>
				<TD COLSPAN=2 ALIGN=RIGHT>
					<INPUT TYPE="submit" VALUE="Print Batch Sheet" CLASS="submit"> 
					<INPUT TYPE="button" VALUE="Cancel" onClick="window.close()" CLASS="submit">
				</TD>
			</TR>

		</TABLE>

	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</FORM><BR>



<?php //} ?>

<SCRIPT TYPE="text/javascript">

function updateDate(current_div, type, pni, con, seq) {
	//document.line_items
	var whichDiv = document.getElementById(current_div);
	document.location.href = "pop_sample_batch_sheet.php?order_num=" + con + "&type=" +  type + "&date=" +  whichDiv.value + "&pni=" +  pni + "&seq=" + seq;
}

</SCRIPT>

<?php include("inc_footer.php"); ?>