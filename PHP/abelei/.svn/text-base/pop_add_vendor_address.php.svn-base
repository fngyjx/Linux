<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION[note]) ) {
	$note = $_SESSION[note];
	unset($_SESSION[note]);
}

if ( isset($_REQUEST[vid]) ) {
	$vid = $_REQUEST[vid];
}

if ( isset($_REQUEST[aid]) ) {
	$aid = $_REQUEST[aid];
}

include('inc_global.php');

$states = array("", "AL","AK","AZ","AR","CA","CO","CT","DE","DC","FL","GA","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MA","MI","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT", "VT","VA","WA","WV","WI","WY");

if ( !empty($_POST) ) {

	$address1 = $_POST[address1];
	$address2 = $_POST[address2];
	$city = $_POST[city];
	$state = $_POST[state];
	$zip = $_POST[zip];
	$country = $_POST[country];
	$notes = $_POST[notes];
	$ship_to_vendor = 1==$_POST[ship_to_vendor] ? 1:0;

	// check_field() FUNCTION IN global.php
	check_field($address1, 1, 'Address');
	check_field($city, 1, 'City');
	check_field($state, 1, 'State');
	check_field($zip, 1, 'Postal code');
	check_field($country, 1, 'Country');

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$address1 = escape_data($address1);
		$address2 = escape_data($address2);
		$city = escape_data($city);
		$state = escape_data($state);
		$zip = escape_data($zip);
		$country = escape_data($country);
		$notes = escape_data($notes);

		if ( $aid != "" ) {
			$sql = "UPDATE vendor_addresses 
			SET address1 = '$address1',
				address2 = '$address2',
				city = '$city',
				state = '$state',
				zip = '$zip',
				country = '$country',
				notes = '$notes',
				ship_to_vendor = $ship_to_vendor
			WHERE address_id = $aid";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		else {
			$sql = "INSERT INTO vendor_addresses (vendor_id, address1, address2, city, state, zip, country, notes, ship_to_vendor) VALUES ($vid, '$address1', '$address2', '$city', '$state', '$zip', '$country', '$notes',$ship_to_vendor)";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		$_SESSION['note'] = "Address information successfully saved<BR>";
		//exit();

		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "window.opener.location.reload()\n";
		echo "window.close()\n";
		echo "</SCRIPT>\n";

	}

}

else {
	if ( $aid != '' ) {
		$sql = "SELECT * FROM vendor_addresses WHERE address_id = $aid";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		$address1 = $row[address1];
		$address2 = $row[address2];
		$city = $row[city];
		$state = $row[state];
		$zip = $row[zip];
		$country = $row[country];
		$notes = $row[notes];
		$ship_to_vendor = $row[ship_to_vendor];
	}
	else {
		$address1 = "";
		$address2 = "";
		$city = "";
		$state = "";
		$zip = "";
		$country = "";
		$notes = "";
	}
}

include("inc_pop_header.php");

?>

<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>$error_message</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>$note</B><BR>";
} ?>



<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<FORM METHOD="post" ACTION="pop_add_vendor_address.php">
<INPUT TYPE="hidden" NAME="aid" VALUE="<?php echo $aid;?>">
<INPUT TYPE="hidden" NAME="vid" VALUE="<?php echo $vid;?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">


	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Address:</B></TD>
		<TD><INPUT TYPE='text' NAME="address1" SIZE=26 VALUE="<?php echo stripslashes($address1);?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD></TD>
		<TD><INPUT TYPE='text' NAME="address2" SIZE=26 VALUE="<?php echo stripslashes($address2);?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">City:</B></TD>
		<TD><INPUT TYPE='text' NAME="city" SIZE=26 VALUE="<?php echo stripslashes($city);?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">State:</B></TD>
		<TD><select name="state" id="state"><?php foreach($states as $val){ echo "<option".($state==$val?" selected=\"selected\"":"")." value=\"$val\">$val</option>\n"; } ?></select></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Postal code:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE='text' NAME="zip" SIZE=26 VALUE="<?php echo stripslashes($zip);?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Country:</B></TD>
		<TD><INPUT TYPE='text' NAME="country" SIZE=26 VALUE="<?php echo stripslashes($country);?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Notes:</B></TD>
		<TD><TEXTAREA NAME="notes" ROWS="3" COLS="30"><?php echo stripslashes($notes);?></TEXTAREA></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD></TR>

	<TR>
		<TD/>
		<TD><input type="checkbox" NAME="ship_to_vendor" id="ship_to_vendor" <?php echo (1==$ship_to_vendor ? "checked=\"checked\"" : "") ?> value="1" /> <label for="ship_to_vendor"><B CLASS="black">display in vendor PO "Ship To" drop down</B></label></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD></TR>

	<TR>
		<TD></TD>
		<TD><INPUT TYPE='submit' VALUE="Save"> <INPUT TYPE='button' VALUE="Cancel" onClick="window.close()"></TD>
	</TR></FORM>
</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>



<?php include("inc_footer.php"); ?>