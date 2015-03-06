<?php

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

if ( isset($_SESSION['inc_note']) ) {
	$inc_note = $_SESSION['inc_note'];
	unset($_SESSION['inc_note']);
}

//if ( $_REQUEST['psn'] != '' ) {
//	$psn = $_REQUEST['psn'];
//} else {
//	header ("Location: customers_quotes.php");
//	exit;
//}

//include('inc_global.php');

$inc_form_status = "";
if ( $_REQUEST['inc_action'] != 'edit' ) {
	$inc_form_status = "DISABLED";
}

if ( $_REQUEST['inc_action'] != '' ) {
	$inc_action = $_REQUEST['inc_action'];
} else {
	$inc_action = "";
}


if ( $_REQUEST['psn_string'] != '' ) {
	$psn_array = explode(",", $_REQUEST['psn_string']);
	$psn_clause = " AND PriceSheetNumber = " . $psn_array[0];
} else {
	$psn_clause = " AND PriceSheetNumber = " . $_REQUEST['psn'];
}


$sql = "SELECT locked FROM pricesheetmaster WHERE 1=1 " . $psn_clause;
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
$locked = $row['locked'];
//echo $locked;
$sql = "SELECT pricesheetmaster.ProductNumberInternal, pricesheetmaster.CustomerID, ProductNumberExternal, productmaster.Designation, Natural_OR_Artificial, productmaster.ProductType, productmaster.Kosher, name, DatePriced FROM pricesheetmaster LEFT JOIN customers ON pricesheetmaster.CustomerID = customers.customer_id INNER JOIN externalproductnumberreference USING(ProductNumberInternal) INNER JOIN productmaster ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal WHERE 1=1 " . $psn_clause;
$result_header = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row_header = mysql_fetch_array($result_header);
$pni = $row_header['ProductNumberInternal'];

$description = ("" != $row_header['Natural_OR_Artificial'] ? $row_header['Natural_OR_Artificial']." " : "").$row_header['Designation'].("" != $row_header['ProductType'] ? " - ".$row_header['ProductType'] : "").("" != $row_header['Kosher'] ? " - ".$row_header['Kosher'] : "");

?>



<LINK HREF="styles.css" REL="stylesheet" TYPE="text/css">

<script>
function confirmation() {
	var answer = confirm("xxx")
		if (answer){
			//SUBMIT FORM HERE
			document.formname.submit;
		}
			else{
			return false;
		}
}

function print_only() {
	document.getElementById("print_only").innerHTML="<input type='hidden' name='reprint' value='1'>";
	document.forms("print_submit").submit();
}

function print_and_mail() {
	document.getElementById("print_only").innerHTML="";
}


</script>

<?php if ( $inc_note ) {
	echo "<B STYLE='color:#990000'>" . $inc_note . "</B><BR><BR>";
} ?>


<FORM NAME="add_ingredient" ACTION="customers_quotes.header.php" METHOD="post">
<INPUT TYPE="hidden" NAME="psn" VALUE="<?php echo $psn;?>">
<INPUT TYPE="hidden" NAME="locked" VALUE="<?php echo $locked;?>">
<INPUT TYPE="hidden" NAME="referrer" VALUE="<?php echo basename($_SERVER['PHP_SELF']);?>">


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
	<TR>
		<TD><NOBR><B>abelei#:</B></NOBR></TD>
		<TD><?php echo $row_header['ProductNumberExternal'];?></TD>
		<TD>&nbsp;&nbsp;&nbsp;</TD>
		<TD><B>Internal#:</B></TD>
		<TD><?php echo $row_header['ProductNumberInternal'];?> &nbsp;&nbsp;&nbsp;<B>PriceSheet ID:</B> <?php echo $psn;?></TD>
		<TD>&nbsp;&nbsp;&nbsp;</TD>
		<?php if ( $locked == 1 ) { ?>
			<TD><NOBR><B STYLE='color:red'>Locked</B>&nbsp; <INPUT TYPE="submit" VALUE="Unlock" CLASS="submit"></NOBR></TD>
		<?php } else { ?>
			<TD><NOBR><B>Unlocked</B>
			<INPUT TYPE="submit" VALUE="Lock" CLASS="submit">
			</NOBR></TD>
		<?php } ?>
		<TD>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE="button" VALUE="Print Price Sheet" onClick="popup('reports/print_price_sheet.php?locked=<?php echo $locked;?>&psn=<?php echo $psn;?>',800,830)" CLASS="submit_normal"></TD>
	</TR>

	<TR>
		<TD><B>Customer:</B>&nbsp;</TD>
		<TD><NOBR><?php echo $row_header['name'];?></NOBR></TD>
		<TD>&nbsp;&nbsp;&nbsp;</TD>
		<TD><B>Description:</B>&nbsp;</TD>
		<TD><NOBR><?php echo $description;?></NOBR></TD>
		<TD>&nbsp;&nbsp;&nbsp;</TD>
		<?php if ( $locked == 1 ) { ?>
			<TD>&nbsp;</TD>
		<?php } else { ?>
			<TD>
			<INPUT TYPE="button" VALUE="Refresh Material Cost Data" CLASS="submit" onClick="location.href='customers_quotes.header.php?psn=<?php echo $psn;?>&referrer=<?php echo basename($_SERVER['PHP_SELF']);?>&pni=<?php echo $row_header['ProductNumberInternal'];?>'">
			</TD>
		<?php } ?>
		<TD>&nbsp;&nbsp;&nbsp;</TD>
		<?php if ( basename($_SERVER['PHP_SELF']) != 'customers_quotes.email.php' ) { ?>
			<TD><INPUT TYPE="button" VALUE="E-mail Price Sheet" onClick="location.href='customers_quotes.email.php?psn=<?php echo $psn;?>'" CLASS="submit_normal"></TD>
		<?php } else { ?>
			<TD>&nbsp;&nbsp;&nbsp;</TD>
		<?php } ?>
		
	</TR></FORM>
</TABLE><BR>



<?php if ( basename($_SERVER['PHP_SELF']) != 'customers_quotes.email.php' ) { ?>


	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR VALIGN=TOP><TD>

	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
	<TABLE CELLPADDING=7 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR VALIGN=TOP><TD>

	<FORM ACTION="/pricing_quote_letter_pdf.php" METHOD="post" TARGET="_blank" id="print_submit" name="print_submit">

	<INPUT TYPE="hidden" NAME="psn" VALUE="<?php echo $_REQUEST['psn'];?>">
	<?php
	if ( "" != $row_header[CustomerID] ) {
		$sql = "SELECT address_id, address1, address2, city, state, zip 
		FROM customer_addresses 
		WHERE customer_id = $row_header[CustomerID] ORDER BY state, city, zip";
		$result_addresses = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$print_price_letter = 0;
		if ( mysql_num_rows($result_addresses) > 0 ) {

			echo "<SELECT NAME='address_id'>";
			while ( $row_addresses = mysql_fetch_array($result_addresses) ) {
				echo "<OPTION VALUE='" . $row_addresses['address_id'] . "'>" . $row_addresses['address1'] . " " . $row_addresses['address2'] . " " . $row_addresses['city'] . ", " . $row_addresses['state'] . " " . $row_addresses['zip'] . "</OPTION>";
			}
			echo "</SELECT><BR>";
			$print_price_letter = 1;
		}

		$sql = "SELECT contact_id, first_name, last_name 
		FROM customer_contacts 
		WHERE customer_id = " . $row_header['CustomerID'] . " AND customer_contacts.active = 1 ORDER BY last_name";
		$result_contacts = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( mysql_num_rows($result_contacts) > 0 ) {
			echo "<NOBR><SELECT NAME='contact_name'>";
			while ( $row_contacts = mysql_fetch_array($result_contacts) ) {
				echo "<OPTION VALUE='" . $row_contacts['contact_id'] . "_" .$row_contacts['first_name'] . "_" . $row_contacts['last_name'] . "'>" . $row_contacts['first_name'] . " " . $row_contacts['last_name'] . "</OPTION>";
			}
			echo "</SELECT> <B>cc:</B> <INPUT TYPE='text' NAME='cc' VALUE='' SIZE='22'><BR>";
			$print_price_letter = 1;
		}
		if ( $print_price_letter ) {
			echo "<div id='print_only' name='print_only'></div>";
			echo "<INPUT TYPE='submit' VALUE='PrintPriceQuoteLetterMailtoContact' CLASS='submit' onClick='print_and_mail();'></NOBR><br />
				  <input type='button' value='Print Price Quote Letter only' class='submit' onClick='print_only();'>";
		}
	}
	?>

	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>

	</FORM>

	</TD>

	<TD><IMG SRC="../images/spacer.gif" WIDTH=30 HEIGHT=1></TD>

	<TD>

	<?php

	$sql = "SELECT *
	FROM price_quote_letters
	LEFT JOIN customer_addresses USING(address_id)
	WHERE pricesheet_number = " . $psn . "
	ORDER BY datetime_sent DESC";
	$result_letters = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	if ( mysql_num_rows($result_letters) > 0 ) {
		echo "<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0>";
		//echo "<TR>";
		//echo "<TD COLSPAN=2><B CLASS='black'>Printed letters</B></TD>";
		//echo "</TR>";
		echo "<TR>";
		echo "<TD><B>Contact</B><br /><NOBR><small>(Click contact link re-print price letter)</small></NOBR></TD>";
		//echo "<TD><B>Address</B></TD>";
		echo "<TD><B>Date Contacted by</B></TD>";
		echo "</TR>";
		while ( $row_letters = mysql_fetch_array($result_letters) ) {
			echo "<TR VALIGN=TOP>";
			echo "<TD><NOBR><A HREF='reports/pricing_quote_letter.php?reprint=1&psn=" . $_REQUEST['psn'] ."&address_id=" . $row_letters['address_id'] . "&contact_name=" . $row_letters['contact_name'] . "' TARGET='_blank' alt='re-print letter'>" . $row_letters['contact_name'] . "</A>&nbsp;&nbsp;</NOBR></TD>";
			//echo "<TD>" . $row_letters['address1'] . " " . $row_letters['address2'] . " " . $row_letters['city'] . ", " . $row_letters['state'] . " " . $row_letters['zip'] . "</TD>";
			echo "<TD>" . date("m/d/Y", strtotime($row_letters['datetime_sent'])) . " ". $row_letters['sent_by']."</TD>";
			echo "</TR>";
		}
		echo "</TABLE>";
	}

	?>

	</TD>

	</TR></TABLE>
	


<?php } ?>

<BR>