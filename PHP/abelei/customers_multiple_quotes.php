<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN HAS PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

$action = "";
if ( isset($_REQUEST['action']) ) {
	$action = $_REQUEST['action'];
}

if ( isset($_REQUEST['customer_id']) ) {
	$customer_id = $_REQUEST['customer_id'];
}

include('inc_global.php');


if ( $action == 'generate_message' ) {

	if ( $_POST['psns'] == '' ) {
		$error_found = true;
		$error_message .= "Please select price quotes<BR>";
	}

	if ( !$error_found ) {
	
		$sql = "INSERT INTO multiple_price_quotes (customer_id, created_by, date_sent) VALUES (" . $customer_id . ", " . $_SESSION['user_id'] . ", '" . date("Y-m-d H:i:s") . "')";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$qid = mysql_insert_id();

		$psn_string = '';
		$c = 0;

		foreach ( $_POST['psns'] as $psn ) {
			$c++;
			$sql = "SELECT PriceSheetNumber, pricesheetmaster.ProductNumberInternal, DatePriced, SellingPrice, productmaster.Designation, productmaster.Kosher, productmaster.Natural_OR_Artificial, productmaster.ProductType, externalproductnumberreference.ProductNumberExternal
			FROM pricesheetmaster 
			LEFT JOIN productmaster
			USING ( ProductNumberInternal ) 
			LEFT JOIN externalproductnumberreference ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
			WHERE PriceSheetNumber = " . $psn;
			$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$row = mysql_fetch_array($result);
			$price = $row['SellingPrice'];
			$internal_number = $row['ProductNumberInternal'];
			$external_number = $row['ProductNumberExternal'];
			$designation = $row['Designation'];

			$sql = "INSERT INTO multiple_price_quotes_items (quote_id, price_sheet, internal_number, external_number, designation, price) VALUES (" . $qid . ", " . $psn . ", '" . $internal_number . "', '" . $external_number . "', '" . $designation . "', " . $price . ")";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

			if ( $c < count($_POST['psns']) ) {
				$psn_string .= $psn . ",";
			} else {
				$psn_string .= $psn;
			}

		}

		header("location: customers_multiple_quotes.php?customer_id=" . $customer_id . "&psn_string=" . $psn_string);
		exit();

	}

}

		
include("inc_header.php");

?>



<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>



<?php if ( $action == '' and $customer_id == '' ) { ?>

	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD ALIGN=CENTER>
	<FORM ACTION="customers_multiple_quotes.php" METHOD="post">

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">

		<TR>
			<TD COLSPAN=2><B>Select customer for multiple quotes</B><BR><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="10"><BR></TD>
		</TR>

		<TR VALIGN=TOP>
			<TD><B CLASS="black">Customer:</B></TD>
			<TD><INPUT TYPE="text" ID="customer" NAME="name" SIZE=30 VALUE="<?php echo stripslashes($name);?>">
			<INPUT TYPE="hidden" ID="customer_id" NAME="customer_id" VALUE="<?php echo stripslashes($customer_id);?>"><BR><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="3"><BR></TD>
		</TR>

		<TR>
			<TD></TD>
			<TD><INPUT TYPE="button" VALUE="Cancel" CLASS="submit" onClick="window.location='customers_quotes.php'"> <INPUT TYPE="submit" VALUE="Continue >" CLASS="submit"></TD>
		</TR>

	</TABLE>

	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</FORM><BR>

<?php } ?>



<?php if ( $customer_id != '' ) {

	if ( $_GET['psn_string'] != '' ) {
		$psn_array = explode(",", $_GET['psn_string']);
		$psn_clause = " AND (";
		$i = 0;
		foreach ( $psn_array as $psn ) {
			if ( $i != 0 ) {
				$psn_clause .= " OR PriceSheetNumber = " . $psn;
			} else {
				$psn_clause .= " PriceSheetNumber = " . $psn;
			}
			$i++;
		}
		$psn_clause .= ") ";
		$step_two = true;
	} else {
		$psn_clause = "";
		$step_two = false;
	}

	$sql = "SELECT PriceSheetNumber, pricesheetmaster.ProductNumberInternal, DatePriced, SellingPrice, productmaster.Designation, productmaster.Kosher, productmaster.Natural_OR_Artificial, productmaster.ProductType, externalproductnumberreference.ProductNumberExternal
	FROM pricesheetmaster 
	LEFT JOIN productmaster
	USING ( ProductNumberInternal ) 
	LEFT JOIN externalproductnumberreference ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
	WHERE CustomerID = " . $customer_id . $psn_clause . "
	ORDER BY DatePriced DESC";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	if ( $c > 0 ) {

		$bg = 0; 
		?>
		<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>
			<TR VALIGN=BOTTOM>
			<FORM ACTION="customers_multiple_quotes.php" METHOD="post">
			<INPUT TYPE="hidden" NAME="action" VALUE="generate_message">
			<INPUT TYPE="hidden" NAME="customer_id" VALUE="<?php echo $customer_id;?>">
				<?php if ( $_GET['psn_string'] == '' ) { ?>
					<TD></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="2" HEIGHT="1"></TD>
				<?php } ?>
				<TD><B>Date priced</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Price</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>abelei#</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Designation</B></TD>
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

			$designation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
			//$internal_number = $row['ProductNumberInternal'];

			$PriceSheetNumber = $row['PriceSheetNumber'];
			if ( $row['DatePriced'] != '' ) {
				$DatePriced = date("m/d/Y", strtotime($row['DatePriced']));
			} else {
				$DatePriced = '';
			}
			$SellingPrice = $row['SellingPrice'];
			$external_number = $row['ProductNumberExternal'];
			//$designation = $row['Designation'];

			?>

			<TR BGCOLOR="<?php echo $bgcolor;?>">
				<?php if ( $_GET['psn_string'] == '' ) { ?>
					<TD><INPUT NAME="psns[]" VALUE="<?php echo $PriceSheetNumber;?>" TYPE="checkbox"></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="2" HEIGHT="1"></TD>
				<?php } ?>
				<TD><?php echo $DatePriced;?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><?php echo $SellingPrice;?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><?php echo $external_number;?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><?php echo $designation;?></TD>
			</TR>

			<?php } ?>

			<?php if ( $_GET['psn_string'] == '' ) { ?>

				<TR>
					<TD COLSPAN=9 ALIGN=RIGHT><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"><BR><INPUT TYPE="button" VALUE="Cancel" CLASS="submit" onClick="window.location='customers_quotes.php'"> <INPUT TYPE="submit" VALUE="Continue >" CLASS="submit"></TD>
				</TR>

			<?php } else { ?>

				<TR>
					<TD COLSPAN=7><BR><INPUT TYPE="button" VALUE="E-mail Price Sheet" onClick="location.href='customers_quotes.email.php?customer_id=<?php echo $customer_id;?>&psn_string=<?php echo $_REQUEST['psn_string'];?>'" CLASS="submit_normal"></TD>
				</TR></FORM>

				<TR>
					<TD COLSPAN=7><BR>

					<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
					<TABLE CELLPADDING=7 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR VALIGN=TOP><TD>

					<FORM ACTION="reports/pricing_quote_letter.php" METHOD="post" TARGET="_blank">

					<INPUT TYPE="hidden" NAME="psn_string" VALUE="<?php echo $_REQUEST['psn_string'];?>">

					<?php
					$sql = "SELECT address_id, address1, address2, city, state, zip 
					FROM customer_addresses 
					WHERE customer_id = " . $customer_id . " ORDER BY state, city, zip";
					$result_addresses = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					if ( mysql_num_rows($result_addresses) > 0 ) {

						echo "<SELECT NAME='address_id'>";
						while ( $row_addresses = mysql_fetch_array($result_addresses) ) {
							echo "<OPTION VALUE='" . $row_addresses['address_id'] . "'>" . $row_addresses['address1'] . " " . $row_addresses['address2'] . " " . $row_addresses['city'] . ", " . $row_addresses['state'] . " " . $row_addresses['zip'] . "</OPTION>";
						}
						echo "</SELECT><BR>";

						$sql = "SELECT first_name, last_name 
						FROM customer_contacts 
						WHERE customer_id = " . $customer_id . " AND customer_contacts.active = 1 ORDER BY last_name";
						$result_contacts = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
						if ( mysql_num_rows($result_contacts) > 0 ) {
							echo "<NOBR><SELECT NAME='contact_name'>";
							while ( $row_contacts = mysql_fetch_array($result_contacts) ) {
								echo "<OPTION VALUE='" . $row_contacts['first_name'] . " " . $row_contacts['last_name'] . "'>" . $row_contacts['first_name'] . " " . $row_contacts['last_name'] . "</OPTION>";
							}
							echo "</SELECT> <B>cc:</B> <INPUT TYPE='text' NAME='cc' VALUE='' SIZE='22'><BR>";
						}

						echo "<INPUT TYPE='submit' VALUE='Print price quote letter' CLASS='submit'></NOBR>";
					} ?>

					</TD></TR></TABLE>
					</TD></TR></TABLE>
					</TD></TR></TABLE>

					</TD>
				</TR>
				


	
		<?php } ?>

		</FORM></TABLE>

	<?php

	} else {
		echo "<I>No price quotes available for this customer</I>";
	}

} ?>




















<script>
	$(document).ready(function(){

	$("#customer").autocomplete("search/customers_by_name.php", {
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#customer").result(function(event, data, formatted) {
		if (data)
			document.getElementById("customer_id").value = data[1];
	});
	$("#ProductNumberExternal").autocomplete("search/external_product_numbers.php", {
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#ProductNumberInternal").autocomplete("search/internal_product_numbers.php", {
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#designation").autocomplete("search/designations.php", {
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#new").click(function() {
		window.location = 'customers_quotes.php?action=add_quote';
	});
});
</script>

<BR><BR>

<?php include("inc_footer.php"); ?>