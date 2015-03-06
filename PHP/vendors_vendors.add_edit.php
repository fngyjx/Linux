<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 5 and $rights != 6) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( isset($_SESSION['subnote']) ) {
	$subnote = $_SESSION['subnote'];
	unset($_SESSION['subnote']);
}

if ( isset($_REQUEST['vid']) ) {
	$vid = $_REQUEST['vid'];
}

if ( isset($_REQUEST['aid']) ) {
	$aid = $_REQUEST['aid'];
}

if ( isset($_REQUEST['pid']) ) {
	$pid = $_REQUEST['pid'];
}

include('inc_global.php');

$states = array("", "AL","AK","AZ","AR","CA","CO","CT","DE","DC","FL","GA","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MA","MI","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT", "VT","VA","WA","WV","WI","WY");

if ( isset($_POST['subform']) ) { // NUMBERS FORM

	$sub_errors = false;

	$number_description = $_POST['number_description'];
	$number = $_POST['number'];
	$type = $_POST['type'];

	// check_field() FUNCTION IN global.php
	check_field($number, 1, 'Number');

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$number_description = escape_data($number_description);
		$number = escape_data($number);
		if ( $pid != "" ) {
			$sql = "UPDATE vendor_address_phones " .
			" SET number_description = '" . $number_description . "'," .
			" number = '" . $number . "'," .
			" type = " . $type .
			" WHERE phone_id = " . $pid;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		} else {
			$sql = "INSERT INTO vendor_address_phones (address_id, number_description, number, type) VALUES (" . $aid . ",'" . $number_description . "', '" . $number . "', " . $type . ")";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		$_SESSION['subnote'] = "Information successfully saved<BR>";
		header("location: vendors_vendors.add_edit.php?vid=" . $vid . "&aid=" . $aid);
		exit;
	} else {
		$sub_errors = true;
	}

}





if ( !empty($_POST) and !$sub_errors ) { // MAIN FORM

	$address1 = $_POST['address1'];
	$address2 = $_POST['address2'];
	$city = $_POST['city'];
	$state = $_POST['state'];
	$zip = $_POST['zip'];
	$country = $_POST['country'];
	$notes = $_POST['notes'];

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
			$sql = "UPDATE vendor_addresses " .
			" SET address1 = '" . $address1 . "'," .
			" address2 = '" . $address2 . "'," .
			" city = '" . $city . "'," .
			" state = '" . $state . "'," .
			" zip = '" . $zip . "'," .
			" country = '" . $country . "'," .
			" notes = '" . $notes . "'" .
			" WHERE address_id = " . $aid;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		else {
			$sql = "INSERT INTO vendor_addresses (vendor_id, address1, address2, city, state, zip, country, notes) VALUES (" . $vid . ", '" . $address1 . "', '" . $address2 . "', '" . $city . "', '" . $state . "', '" . $zip . "', '" . $country . "', '" . $notes . "')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		$_SESSION['note'] = "Address information successfully saved<BR>";
		header("location: vendors_vendors.edit.php?vid=" . $vid);
		exit();

	}

}

else {
	if ( $aid != '' ) {
		$sql = "SELECT * FROM vendor_addresses WHERE address_id = " . $aid;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		$address1 = $row['address1'];
		$address2 = $row['address2'];
		$city = $row['city'];
		$state = $row['state'];
		$zip = $row['zip'];
		$country = $row['country'];
		$notes = $row['notes'];
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


if ( $_GET['action'] == "inact" ) {
	$sql = "DELETE from vendor_address_phones WHERE phone_id = " . $_GET['pid'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	header("location: vendors_vendors.add_edit.php?vid=" . $_GET['vid'] . "&aid=" . $aid);
	exit();
}


include("inc_header.php");

?>

<?php if ( $error_found and $type == '' ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0><TR VALIGN=TOP><TD>



<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<FORM METHOD="post" ACTION="vendors_vendors.add_edit.php">
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
		<TD></TD>
		<TD><INPUT TYPE='submit' VALUE="Save"> <INPUT TYPE='button' VALUE="Cancel" onClick="window.location='vendors_vendors.edit.php?vid=<?php echo $vid;?>'"></TD>
	</TR></FORM>
</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE>










<?php if ( isset($aid) and !$main_error ) { ?>

	<?php

	if ( isset($pid) ) {
		$sql = "SELECT * FROM vendor_address_phones WHERE phone_id = " . $pid;
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result);
		$pid = $row['phone_id'];
		$number_description = $row['number_description'];
		$number = $row['number'];
		$type = $row['type'];
	} else {
		$pid = '';
		$number_description = '';
		$number = '';
		$type = '';
	}

	?>

	<TD>
	<TD><IMG SRC="images/spacer.gif" WIDTH="50" HEIGHT="1"></TD>
	<TD>

	<?php if ( $error_found ) {
		echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
		unset($error_message);
	} ?>

	<?php if ( $subnote ) {
		echo "<B STYLE='color:#990000'>" . $subnote . "</B><BR>";
	} ?>

	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
	<FORM METHOD="post" ACTION="vendors_vendors.add_edit.php">
	<INPUT TYPE="hidden" NAME="pid" VALUE="<?php echo $pid;?>">
	<INPUT TYPE="hidden" NAME="aid" VALUE="<?php echo $aid;?>">
	<INPUT TYPE="hidden" NAME="vid" VALUE="<?php echo $vid;?>">

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

		<TR>
			<TD COLSPAN=2><B STYLE="font-size:8pt">Numbers (phone, fax, etc.)</B></TD>
		</TR>
		<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="11"></TD></TR>
		<TR>
			<TD><B CLASS="black" STYLE="font-size:8pt">Description:</B>&nbsp;&nbsp;</TD>
			<TD><INPUT TYPE='text' NAME="number_description" SIZE=21 VALUE="<?php echo stripslashes($number_description);?>"></TD>
		</TR>
		<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>
		<TR>
			<TD><B CLASS="black" STYLE="font-size:8pt">Number:</B></TD>
			<TD><INPUT TYPE='text' NAME="number" SIZE=21 VALUE="<?php echo stripslashes($number);?>"></TD>
		</TR>
		<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>
		<TR>
			<TD><B CLASS="black" STYLE="font-size:8pt">Type:</B></TD>
			<TD>
			<SELECT NAME="type" STYLE="font-size:7pt">
				<?php
				$sql = "SELECT * FROM phone_types ORDER BY type_id";
				$result = mysql_query($sql, $link);
				if ( mysql_num_rows($result) > 0 ) {
					while ( $row = mysql_fetch_array($result) ) {
						if ( $row['type_id'] == $type ) {
							echo "<OPTION VALUE='" . $row['type_id'] . "' SELECTED>" . $row['description'] . "</OPTION>";
						} else {
							echo "<OPTION VALUE='" . $row['type_id'] . "'>" . $row['description'] . "</OPTION>";
						}
					}
				} ?>
			</SELECT> <INPUT TYPE='submit' NAME="subform" VALUE="Save >"></TD>
		</TR>

	</TABLE><BR>

	<?php
	$sql = "SELECT * FROM vendor_address_phones LEFT JOIN phone_types ON vendor_address_phones.type = phone_types.type_id WHERE address_id = " . $aid . " ORDER BY type_id";
	$result = mysql_query($sql, $link);
	if ( mysql_num_rows($result) > 0 ) {
		$bg = 0; ?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
		<?php
		while ( $row = mysql_fetch_array($result) ) {

			if ( $bg == 1 ) {
				$bgcolor = "#F3E7FD";
				$bg = 0;
			}
			else {
				$bgcolor = "#DFDFDF";
				$bg = 1;
			} ?>
				<TR BGCOLOR="<?php echo($bgcolor);?>" VALIGN=TOP>
					<TD><A HREF="vendors_vendors.add_edit.php?vid=<?php echo $vid;?>&pid=<?php echo $row['phone_id'];?>&aid=<?php echo $aid;?>"><IMG SRC="images/pencil.gif" WIDTH="16" HEIGHT="16" BORDER=0></A></TD>
					<TD><A HREF="JavaScript:inactivate(<?php echo($row['phone_id'] . "," . $vid . "," . $aid);?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A></TD>
					<TD><B STYLE="font-size:8pt"><?php echo $row['description'];?>:</B> </TD>
					<TD STYLE="font-size:8pt"><NOBR><?php echo $row['number'];?></NOBR></TD>
					<TD><?php
					if ( $row['number_description'] != '' ) {
						echo "(<I STYLE='font-size:8pt'>" . $row['number_description'] . "</I>)";
					}
					?></TD>
				</TR>
		<?php } ?>
		</TABLE>
	<?php } else {
		echo "<I STYLE='font-size:8pt'>No numbers found</I>";
	}

	?>

	</TD></FORM></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>

<?php } ?>






</TD></TR></TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE>










<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

function inactivate(pid, vid, aid) {
	if ( confirm('Are you sure you want to delete this number?') ) {
		document.location.href = "vendors_vendors.add_edit.php?action=inact&pid=" + pid + "&vid=" + vid + "&aid=" + aid
	}
}

 // End -->
</SCRIPT>


<?php include("inc_footer.php"); ?>