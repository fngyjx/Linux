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

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( isset($_SESSION['subnote']) ) {
	$subnote = $_SESSION['subnote'];
	unset($_SESSION['subnote']);
}

if ( isset($_REQUEST['cid']) ) {
	$cid = $_REQUEST['cid'];
}

if ( isset($_REQUEST['pid']) ) {
	$pid = $_REQUEST['pid'];
}


$form_status = "";
$radio_check = "";
if ( $_REQUEST['update'] != 1 ) {
	$form_status = "readonly=\"readonly\"";
	$radio_check = "DISABLED";
}


include('inc_global.php');






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
			$sql = "UPDATE vendor_contact_phones " .
			" SET number_description = '" . $number_description . "'," .
			" number = '" . $number . "'," .
			" type = " . $type .
			" WHERE phone_id = " . $pid;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		} else {
			$sql = "INSERT INTO vendor_contact_phones (contact_id, number_description, number, type) VALUES (" . $cid . ",'" . $number_description . "', '" . $number . "', " . $type . ")";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		$_SESSION['subnote'] = "Information successfully saved<BR>";
		header ("Location: vendors_contacts.edit.php?cid=" . $cid);
		exit;
	} else {
		$sub_errors = true;
	}

}





if ( !empty($_POST) and !$sub_errors ) { // MAIN FORM

	$user_id = $_POST['user_id'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$vendor_id = $_POST['vendor_id'];
	$name = $_POST['name'];
	$email1 = $_POST['email1'];
	$email2 = $_POST['email2'];
	$active = $_POST['active'];

	// check_field() FUNCTION IN global.php
	check_field($first_name, 1, 'First name');
	check_field($last_name, 1, 'Last name');
	check_field($vendor_id, 1, 'Vendor');
	//check_field($email1, 1, 'E-mail 1');

	if ( $cid != "" ) {
		$id_check = " AND contact_id <> " .  $cid;
	}
	else {
		$id_check = "";
	}

	//if ( $email1 != "" ) {
	//	$sql = "SELECT * FROM vendor_contacts WHERE ((email1 = '" . escape_data($email1) . "') OR (email2 = '" . escape_data($email1) . "' ))" . $id_check;
	//	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//	$c = mysql_num_rows($result);
	//	if ( $c > 0 ) {
	//		$error_found = true;
	//		$error_message .= "E-mail address 1 entered is already in database<BR>";
	//	}
	//}

	//if ( $email2 != "" ) {
	//	$sql = "SELECT * FROM vendor_contacts WHERE ((email1 = '" . escape_data($email2) . "') OR (email2 = '" . escape_data($email2) . "' ))" . $id_check;
	//	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//	$c = mysql_num_rows($result);
	//	if ( $c > 0 ) {
	//		$error_found = true;
	//		$error_message .= "E-mail address 2 entered is already in database<BR>";
	//	}
	//}

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$first_name = escape_data($first_name);
		$last_name = escape_data($last_name);
		$email1 = escape_data($email1);
		$email2 = escape_data($email2);

		$vendor_values = explode("~", $vendor_id);
		$vendor_id = $vendor_values[0];
		$address_id = $vendor_values[1];

		if ( $cid != "" ) {
			$sql = "UPDATE vendor_contacts " .
			" SET first_name = '" . $first_name . "'," .
			" last_name = '" . $last_name . "'," .
			" vendor_id = " . $vendor_id . "," .
			" vendor_address_id = " . $address_id . "," .
			" email1 = '" . $email1 . "'," .
			" email2 = '" . $email2 . "'," .
			" active = " . $active .
			" WHERE contact_id = " . $cid;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		else {
			$sql = "INSERT INTO vendor_contacts (first_name, last_name, vendor_id, vendor_address_id, email1, email2) VALUES ('" . $first_name . "','" . $last_name . "', " . $vendor_id . ", " . $address_id . ", '" . $email1 . "', '" . $email2 . "')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$cid = mysql_insert_id();
		}

		$_SESSION['note'] = "Contact information successfully saved<BR>";
		header("location: vendors_contacts.edit.php?cid=" . $cid);
		exit();

	}

}

else {
	if ( $cid != '' ) {
		$sql = "SELECT vendor_contacts.*, vendors.name FROM vendor_contacts LEFT JOIN vendors USING(vendor_id) WHERE contact_id = " . $cid;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		$cid = $row['contact_id'];
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		$vendor_id = $row['vendor_id'];
		$vendor_address_id = $row['vendor_address_id'];
		$name = $row['name'];
		$email1 = $row['email1'];
		$email2 = $row['email2'];
		$active = $row['active'];

	}
	else {
		$cid = "";
		$first_name = "";
		$last_name = "";
		$vendor_id = "";
		$vendor_address_id = "";
		$name = "";
		$email1 = "";
		$email2 = "";
		$active = 1;
	}
}


if ( $_GET['action'] == "inact" ) {
	$sql = "DELETE from vendor_contact_phones WHERE phone_id = " . $_GET['pid'];
	mysql_query($sql, $link);
	header("location: vendors_contacts.edit.php?cid=" . $_GET['cid']);
	exit();
}


include("inc_header.php");

?>

<?php if ( $error_found and $type == '' ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
	unset($error_message);
	unset($error_found);
	$main_error = true;
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


<?php if ( $vendor_id != "" ) { ?>
	<A HREF="vendors_vendors.edit.php?vid=<?php echo $vendor_id;?>">Vendor info</A><BR><BR>
<?php } ?>


<?php if ( $_REQUEST['cid'] == '' or $_REQUEST['pid'] == '' ) { ?>

	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0><TR VALIGN=TOP><TD>
	
	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
	<FORM METHOD="post" ACTION="vendors_contacts.edit.php">
	
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	
		<TR>
			<INPUT TYPE="hidden" NAME="cid" VALUE="<?php echo $cid;?>">
			<TD><NOBR><B CLASS="black">First name:</B>&nbsp;&nbsp;&nbsp;</NOBR></TD>
			<TD><INPUT TYPE='text' NAME="first_name" SIZE=26 VALUE="<?php echo stripslashes($first_name);?>" <?php echo $form_status;?>></TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD><NOBR><B CLASS="black">Last name:</B></NOBR></TD>
			<TD><INPUT TYPE='text' NAME="last_name" SIZE=26 VALUE="<?php echo stripslashes($last_name);?>" <?php echo $form_status;?>></TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD><B CLASS="black">Vendor:</B></TD>
			<TD>
	
	<?php
	
	$sql = "SELECT vendors.vendor_id, address_id, name, vendor_addresses.address1, vendor_addresses.city, vendor_addresses.state 
	FROM vendors
	LEFT JOIN vendor_contacts
	USING (vendor_id)
	LEFT JOIN vendor_addresses
	USING (vendor_id)
	WHERE address_id IS NOT NULL
	ORDER BY name, city";
	$result = mysql_query($sql, $link);
	//echo $sql;
	
	if ( mysql_num_rows($result) > 0 ) { ?> 
		<SELECT NAME="vendor_id" <?php echo $radio_check;?>>
			<OPTION VALUE=""></OPTION>
			<?php
			while ( $row = mysql_fetch_array($result) ) {
				if ( $vendor_id == $row['vendor_id'] or $_REQUEST['vend_id'] == $row['vendor_id'] )  {   //  and $vendor_address_id == $row['address_id']
					echo "<OPTION VALUE='" . $row['vendor_id'] . "~" . $row['address_id'] . "' SELECTED>" . $row['name'] . " (" . $row['address1'] . " " . $row['city'] . ", " . $row['state'] . ")</OPTION>";
				} else {
					echo "<OPTION VALUE='" . $row['vendor_id'] . "~" . $row['address_id'] . "'>" . $row['name'] . " (" . $row['address1'] . " " . $row['city'] . ", " . $row['state'] . ")</OPTION>";
				}
			} ?>
		</SELECT>
	<?php }
	else {
		echo "<I>None available</I>";
	}
	
	?>
	
			</TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD><B CLASS="black">E-mail 1:</B></TD>
			<TD><INPUT TYPE='text' NAME="email1" SIZE=26 VALUE="<?php echo stripslashes($email1);?>" MAXLENGTH=75 <?php echo $form_status;?>></TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD><B CLASS="black">E-mail 2:</B></TD>
			<TD><INPUT TYPE='text' NAME="email2" SIZE=26 VALUE="<?php echo stripslashes($email2);?>" MAXLENGTH=75 <?php echo $form_status;?>></TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD><B CLASS="black">Active:</B></TD>
			<TD>
			<?php if ( $active == "" or $active == "1" ) {
				print("<INPUT TYPE='radio' NAME='active' VALUE='1' CHECKED " . $radio_check . ">Yes ");
				print("<INPUT TYPE='radio' NAME='active' VALUE='0' " . $radio_check . ">No");
			} else {
				print("<INPUT TYPE='radio' NAME='active' VALUE='1' " . $radio_check . ">Yes ");
				print("<INPUT TYPE='radio' NAME='active' VALUE='0' CHECKED " . $radio_check . ">No");
			} ?>
			</TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
		</TR>
		<TR>
			<TD></TD>
			<TD>
			<?php if ( $form_status != '' ) { ?>
				<INPUT TYPE="button" VALUE="Edit" CLASS="submit" onClick="window.location='vendors_contacts.edit.php?cid=<?php echo $cid;?>&update=1'">
			<?php } else { ?>
				<INPUT TYPE='submit' VALUE="Save" CLASS="submit"> <INPUT TYPE='button' VALUE="Cancel" onClick="window.location='vendors_contacts.edit.php?cid=<?php echo $cid;?>'" CLASS="submit">
			<?php } ?>
			</TD>
		</TR></FORM>
	</TABLE>

	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>

<?php } ?>



<?php if ( $cid != '' and !$main_error and $_REQUEST['update'] != 1 ) { ?>

	<?php

	if ( isset($pid) ) {
		$sql = "SELECT * FROM vendor_contact_phones WHERE phone_id = " . $pid;
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



<?php if ( ($_REQUEST['cid'] == '' or $_REQUEST['pid'] == '') ) { ?>

	<TD>
	<TD><IMG SRC="images/spacer.gif" WIDTH="50" HEIGHT="1"></TD>
	<TD>

<?php } ?>



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
	<FORM METHOD="post" ACTION="vendors_contacts.edit.php">
	<INPUT TYPE="hidden" NAME="pid" VALUE="<?php echo $pid;?>">
	<INPUT TYPE="hidden" NAME="cid" VALUE="<?php echo $cid;?>">

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
			<TD><NOBR>
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
			</SELECT> <INPUT TYPE='submit' NAME="subform" VALUE="Save >">
			<?php
			if ( $pid != '' ) {
				echo "<INPUT TYPE='button' VALUE='Cancel' onClick=\"location.href='vendors_contacts.edit.php?cid=" . $cid . "'\">";
			}
			?>
			</NOBR></TD>
		</TR>

	</TABLE><BR>

	<?php
	if ( $pid == '' ) {
		$sql = "SELECT * FROM vendor_contact_phones
		LEFT JOIN phone_types ON vendor_contact_phones.type = phone_types.type_id
		WHERE contact_id = " . $cid . " ORDER BY type_id";
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
						<TD><A HREF="vendors_contacts.edit.php?cid=<?php echo $cid;?>&pid=<?php echo $row['phone_id'];?>"><IMG SRC="images/pencil.gif" WIDTH="16" HEIGHT="16" BORDER=0></A></TD>
						<TD><A HREF="JavaScript:inactivate(<?php echo($row['phone_id'] . "," . $cid);?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A></TD>
						<TD><B STYLE="font-size:8pt"><?php echo $row['description'];?>:</B> </TD>
						<TD STYLE="font-size:8pt"><NOBR><?php echo $row['number'];?></NOBR></TD>
						<TD><NOBR><?php
						if ( $row['number_description'] != '' ) {
							echo "(<I STYLE='font-size:8pt'>" . $row['number_description'] . "</I>)";
						}
						?></NOBR></TD>
					</TR>
			<?php } ?>
			</TABLE>
		<?php } else {
			echo "<I STYLE='font-size:8pt'>No numbers found</I>";
		}
	}

	?></FORM>

	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>

<?php } ?>



</TD></TR></TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE>



<?php if ( $_REQUEST['cid'] == '' or $_REQUEST['pid'] == '' ) { ?>
	</TD></TR></TABLE>
<?php } ?>







<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

function inactivate(pid, cid) {
	if ( confirm('Are you sure you want to delete this number?') ) {
		document.location.href = "vendors_contacts.edit.php?action=inact&pid=" + pid + "&cid=" + cid
	}
}

 // End -->
</SCRIPT>


<?php include("inc_footer.php"); ?>