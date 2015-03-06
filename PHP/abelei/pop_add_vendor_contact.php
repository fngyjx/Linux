<<<<<<< .mine
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

if ( isset($_REQUEST['vid']) ) {
	$vid = $_REQUEST['vid'];
} elseif ( isset($_REQUEST['vend_id']) ) {
	$vid = $_REQUEST['vend_id'];
}

if ( isset($_REQUEST['cid']) ) {
	$cid = $_REQUEST['cid'];
}

include('inc_global.php');

if ( !empty($_POST) ) {

	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	//$address_id = $_POST['address_id'];
	$name = $_POST['name'];
	$email1 = $_POST['email1'];
	$email2 = $_POST['email2'];
	$active = $_POST['active'];

	// check_field() FUNCTION IN global.php
	check_field($first_name, 1, 'First name');
	check_field($last_name, 1, 'Last name');
	//check_field($address_id, 1, 'Address');
	//check_field($email1, 1, 'E-mail 1');

	if ( $cid != "" ) {
		$id_check = " AND contact_id <> " .  $cid;
	}
	else {
		$id_check = "";
	}

/*	if ( $email1 != "" ) {
		$sql = "SELECT * FROM vendor_contacts WHERE ((email1 = '" . $email1 . "') OR (email2 = '" . $email1 . "' ))" . $id_check;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
		if ( $c > 0 ) {
			$error_found = true;
			$error_message .= "E-mail address 1 entered is already in database<BR>";
		}
	}

	if ( $email2 != "" ) {
		$sql = "SELECT * FROM vendor_contacts WHERE ((email1 = '" . $email2 . "') OR (email2 = '" . $email2 . "' ))" . $id_check;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
		if ( $c > 0 ) {
			$error_found = true;
			$error_message .= "E-mail address 2 entered is already in database<BR>";
		}
	}
  */

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$first_name = escape_data($first_name);
		$last_name = escape_data($last_name);
		$email1 = escape_data($email1);
		$email2 = escape_data($email2);

		// $vendor_values = explode("~", $vendor_id);
		// $vendor_id = $vendor_values[0];
		//$address_id = $address_id;

		if ( $cid != "" ) {
			$sql = "UPDATE vendor_contacts " .
			" SET first_name = '" . $first_name . "'," .
			" last_name = '" . $last_name . "'," .
			" vendor_id = " . $vid . "," .
			//" vendor_address_id = " . $address_id . "," .
			" email1 = '" . $email1 . "'," .
			" email2 = '" . $email2 . "'," .
			" active = " . $active .
			" WHERE contact_id = " . $cid;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		else {
			$sql = "INSERT INTO vendor_contacts (first_name, last_name, vendor_id, vendor_address_id, email1, email2, active) VALUES ('" . $first_name . "','" . $last_name . "', " . $vid . ", NULL, '" . $email1 . "', '" . $email2 . "', 1)";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		$_SESSION['note'] = "Contact information successfully saved<BR>";
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "window.opener.location.reload()\n";
		echo "window.close()\n";
		echo "</SCRIPT>\n";

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
		$vid = $row['vendor_id'];
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
		if ( isset($_GET['vend_id']) ) {
			$vid = $_GET['vend_id'];
		} else {
			$vid = "";
		}
		$vendor_address_id = $row['vendor_address_id'];
		$name = "";
		$email1 = "";
		$email2 = "";
		$active = 1;
	}
}

include("inc_pop_header.php");

?>

<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>



<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<FORM METHOD="post" ACTION="pop_add_vendor_contact.php">
<INPUT TYPE="hidden" NAME="vid" VALUE="<?php echo $vid;?>">
<INPUT TYPE="hidden" NAME="cid" VALUE="<?php echo $cid;?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD><B CLASS="black">First name:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE='text' NAME="first_name" SIZE=26 VALUE="<?php echo stripslashes($first_name);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Last name:</B></TD>
		<TD><INPUT TYPE='text' NAME="last_name" SIZE=26 VALUE="<?php echo stripslashes($last_name);?>"></TD>
	</TR>
<!--	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Address:</B></TD>
		<TD> -->

<?php

//$sql = "SELECT vendor_id, address_id, name, address1, city, state 
//FROM vendors
//LEFT JOIN vendor_addresses
//USING (vendor_id)
//WHERE address_id IS NOT NULL
//AND vendor_id = " . $vid . "
//ORDER BY name, city";
//$result = mysql_query($sql, $link);

//if ( mysql_num_rows($result) > 0 ) { ?> 
	<!-- <SELECT NAME="address_id"> -->
		<?php
		//while ( $row = mysql_fetch_array($result) ) {
		//	if ( $vid == $row['vendor_id'] and $vendor_address_id == $row['address_id'] ) {
		//		echo "<OPTION VALUE='" . $row['address_id'] . "' SELECTED>" . $row['name'] . " (" . $row['address1'] . " " . $row['city'] . ", " . $row['state'] . ")</OPTION>";
		//	} else {
		//		echo "<OPTION VALUE='" . $row['address_id'] . "'>" . $row['name'] . " (" . $row['address1'] . " " . $row['city'] . ", " . $row['state'] . ")</OPTION>";
		//	}
		//} ?>
	<!--</SELECT> -->
<?php //}
//else {
//	echo "<I>None available</I><input type=\"button\" onClick=\"document.location.href='pop_add_vendor_address.php?vid=$vid'\" value=\"Add new address\"  class=\"submit_medium\" />";
//}

?>

<!--		</TD>
	</TR> -->
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">E-mail 1:</B></TD>
		<TD><INPUT TYPE='text' NAME="email1" SIZE=26 VALUE="<?php echo stripslashes($email1);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">E-mail 2:</B></TD>
		<TD><INPUT TYPE='text' NAME="email2" SIZE=26 VALUE="<?php echo stripslashes($email2);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Active:</B></TD>
		<TD>
		<?php if ( $active == "" or $active == "1" ) {
			print("<INPUT TYPE='radio' NAME='active' VALUE='1' CHECKED>Yes ");
			print("<INPUT TYPE='radio' NAME='active' VALUE='0'>No");
		} else {
			print("<INPUT TYPE='radio' NAME='active' VALUE='1'>Yes ");
			print("<INPUT TYPE='radio' NAME='active' VALUE='0' CHECKED>No");
		} ?>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>
	<TR>
		<TD></TD>
		<TD><INPUT TYPE='submit' VALUE="Save"> <INPUT TYPE='button' VALUE="Cancel" onClick="window.close()"></TD>
	</TR></FORM>
</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><br/><br/>



=======
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

if ( isset($_REQUEST['vid']) ) {
	$vid = $_REQUEST['vid'];
} elseif ( isset($_REQUEST['vend_id']) ) {
	$vid = $_REQUEST['vend_id'];
}

if ( isset($_REQUEST['cid']) ) {
	$cid = $_REQUEST['cid'];
}

include('inc_global.php');

if ( !empty($_POST) ) {

	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	//$address_id = $_POST['address_id'];
	$name = $_POST['name'];
	$email1 = $_POST['email1'];
	$email2 = $_POST['email2'];
	$active = $_POST['active'];

	// check_field() FUNCTION IN global.php
	check_field($first_name, 1, 'First name');
	check_field($last_name, 1, 'Last name');
	//check_field($address_id, 1, 'Address');
	//check_field($email1, 1, 'E-mail 1');

	if ( $cid != "" ) {
		$id_check = " AND contact_id <> " .  $cid;
	}
	else {
		$id_check = "";
	}

/*	if ( $email1 != "" ) {
		$sql = "SELECT * FROM vendor_contacts WHERE ((email1 = '" . $email1 . "') OR (email2 = '" . $email1 . "' ))" . $id_check;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
		if ( $c > 0 ) {
			$error_found = true;
			$error_message .= "E-mail address 1 entered is already in database<BR>";
		}
	}

	if ( $email2 != "" ) {
		$sql = "SELECT * FROM vendor_contacts WHERE ((email1 = '" . $email2 . "') OR (email2 = '" . $email2 . "' ))" . $id_check;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
		if ( $c > 0 ) {
			$error_found = true;
			$error_message .= "E-mail address 2 entered is already in database<BR>";
		}
	}
  */

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$first_name = escape_data($first_name);
		$last_name = escape_data($last_name);
		$email1 = escape_data($email1);
		$email2 = escape_data($email2);

		// $vendor_values = explode("~", $vendor_id);
		// $vendor_id = $vendor_values[0];
		//$address_id = $address_id;

		if ( $cid != "" ) {
			$sql = "UPDATE vendor_contacts " .
			" SET first_name = '" . $first_name . "'," .
			" last_name = '" . $last_name . "'," .
			" vendor_id = " . $vid . "," .
			//" vendor_address_id = " . $address_id . "," .
			" email1 = '" . $email1 . "'," .
			" email2 = '" . $email2 . "'," .
			" active = " . $active .
			" WHERE contact_id = " . $cid;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		else {
			$sql = "INSERT INTO vendor_contacts (first_name, last_name, vendor_id, vendor_address_id, email1, email2, active) VALUES ('" . $first_name . "','" . $last_name . "', " . $vid . ", NULL, '" . $email1 . "', '" . $email2 . "', 1)";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		$_SESSION['note'] = "Contact information successfully saved<BR>";
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "window.opener.location.reload()\n";
		echo "window.close()\n";
		echo "</SCRIPT>\n";

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
		$vid = $row['vendor_id'];
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
		if ( isset($_GET['vend_id']) ) {
			$vid = $_GET['vend_id'];
		} else {
			$vid = "";
		}
		$vendor_address_id = $row['vendor_address_id'];
		$name = "";
		$email1 = "";
		$email2 = "";
		$active = 1;
	}
}

include("inc_pop_header.php");

?>

<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>



<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<FORM METHOD="post" ACTION="pop_add_vendor_contact.php">
<INPUT TYPE="hidden" NAME="vid" VALUE="<?php echo $vid;?>">
<INPUT TYPE="hidden" NAME="cid" VALUE="<?php echo $cid;?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD><B CLASS="black">First name:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE='text' NAME="first_name" SIZE=26 VALUE="<?php echo stripslashes($first_name);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Last name:</B></TD>
		<TD><INPUT TYPE='text' NAME="last_name" SIZE=26 VALUE="<?php echo stripslashes($last_name);?>"></TD>
	</TR>
<!--	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Address:</B></TD>
		<TD> -->

<?php

//$sql = "SELECT vendor_id, address_id, name, address1, city, state 
//FROM vendors
//LEFT JOIN vendor_addresses
//USING (vendor_id)
//WHERE address_id IS NOT NULL
//AND vendor_id = " . $vid . "
//ORDER BY name, city";
//$result = mysql_query($sql, $link);

//if ( mysql_num_rows($result) > 0 ) { ?> 
	<!-- <SELECT NAME="address_id"> -->
		<?php
		//while ( $row = mysql_fetch_array($result) ) {
		//	if ( $vid == $row['vendor_id'] and $vendor_address_id == $row['address_id'] ) {
		//		echo "<OPTION VALUE='" . $row['address_id'] . "' SELECTED>" . $row['name'] . " (" . $row['address1'] . " " . $row['city'] . ", " . $row['state'] . ")</OPTION>";
		//	} else {
		//		echo "<OPTION VALUE='" . $row['address_id'] . "'>" . $row['name'] . " (" . $row['address1'] . " " . $row['city'] . ", " . $row['state'] . ")</OPTION>";
		//	}
		//} ?>
	<!--</SELECT> -->
<?php //}
//else {
//	echo "<I>None available</I><input type=\"button\" onClick=\"document.location.href='pop_add_vendor_address.php?vid=$vid'\" value=\"Add new address\"  class=\"submit_medium\" />";
//}

?>

<!--		</TD>
	</TR> -->
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">E-mail 1:</B></TD>
		<TD><INPUT TYPE='text' NAME="email1" SIZE=26 VALUE="<?php echo stripslashes($email1);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">E-mail 2:</B></TD>
		<TD><INPUT TYPE='text' NAME="email2" SIZE=26 VALUE="<?php echo stripslashes($email2);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Active:</B></TD>
		<TD>
		<?php if ( $active == "" or $active == "1" ) {
			print("<INPUT TYPE='radio' NAME='active' VALUE='1' CHECKED>Yes ");
			print("<INPUT TYPE='radio' NAME='active' VALUE='0'>No");
		} else {
			print("<INPUT TYPE='radio' NAME='active' VALUE='1'>Yes ");
			print("<INPUT TYPE='radio' NAME='active' VALUE='0' CHECKED>No");
		} ?>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>
	<TR>
		<TD></TD>
		<TD><INPUT TYPE='submit' VALUE="Save"> <INPUT TYPE='button' VALUE="Cancel" onClick="window.close()"></TD>
	</TR></FORM>
</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><br/><br/>



>>>>>>> .r243
<?php include("inc_footer.php"); ?>