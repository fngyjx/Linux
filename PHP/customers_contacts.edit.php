<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, SALES AND FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 2 and $rights != 4 ) {
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

if ( $_REQUEST['action'] == 'edit' ) {
	$form_status = "";
	$check_radio = "";
} else {
	$form_status = " readonly=\"readonly\"";
	$check_radio = " DISABLED";
}

if ( isset($_REQUEST['cid']) ) {
	$cid = $_REQUEST['cid'];
} elseif ( isset($_REQUEST['contact_id']) ) {
	$cid = $_REQUEST['contact_id'];
}

if ( isset($_REQUEST['cust_id']) ) {
	$cust_id = $_REQUEST['cust_id'];
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
			$sql = "UPDATE customer_contact_phones " .
			" SET number_description = '" . $number_description . "'," .
			" number = '" . $number . "'," .
			" type = " . $type .
			" WHERE phone_id = " . $pid;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		} else {
			$sql = "INSERT INTO customer_contact_phones (contact_id, number_description, number, type) VALUES (" . $cid . ",'" . $number_description . "', '" . $number . "', " . $type . ")";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		$_SESSION['subnote'] = "Information successfully saved<BR>";
		header ("Location: customers_contacts.edit.php?cid=" . $cid);
		exit;
	} else {
		$sub_errors = true;
	}

}



if ( !empty($_POST) and !$sub_errors ) { // MAIN FORM

	if ( $_POST['sales'] != '' ) {
		$sales = $_POST['sales'];
	} else {
		$sales = array();
	}

	$customer_pieces = explode("|", $_POST['customer_and_address']);
	$customer_id = $customer_pieces[0];
	$address_id = ( empty($customer_pieces[1]) ) ? "NULL" : $customer_pieces[1];

	$user_id = $_POST['user_id'];
	$title_ = $_POST['title_'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$suffix = $_POST['suffix'];
	$job_title = $_POST['job_title'];
	$department = $_POST['department'];
	//$name = $_POST['customer'];
	//$address1 = $_POST['address1'];
	//$address2 = $_POST['address2'];
	//$city = $_POST['city'];
	//$state = $_POST['state'];
	//$zip = $_POST['zip'];
	//$country = $_POST['country'];
	$email1 = $_POST['email1'];
	$email2 = $_POST['email2'];
	$notes = $_POST['notes'];
	$active = $_POST['active'];
	
	// check_field() FUNCTION IN global.php
	check_field($first_name, 1, 'First name');
	check_field($last_name, 1, 'Last name');
	check_field($customer_id, 1, 'Customer');
	//check_field($address1, 1, 'Address');
	//check_field($city, 1, 'City');
	//check_field($state, 1, 'State');
	//check_field($zip, 1, 'Postal code');
	//check_field($country, 1, 'Country');
//	check_field($email1, 1, 'E-mail 1');

	if ( $cid != "" ) {
		$id_check = " AND contact_id <> " .  $cid;
	}
	else {
		$id_check = "";
	}

	//if ( $email1 != "" ) {
	//	$sql = "SELECT * FROM customer_contacts WHERE ((email1 = '" . escape_data($email1) . "') OR (email2 = '" . escape_data($email1) . "' ))" . $id_check;
	//	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//	$c = mysql_num_rows($result);
	//	if ( $c > 0 ) {
	//		$error_found = true;
	//		$error_message .= "E-mail address 1 entered is already in database<BR>";
	//	}
	//}

	//if ( $email2 != "" ) {
	//	$sql = "SELECT * FROM customer_contacts WHERE ((email1 = '" . escape_data($email2) . "') OR (email2 = '" . escape_data($email2) . "' ))" . $id_check;
	//	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//	$c = mysql_num_rows($result);
	//	if ( $c > 0 ) {
	//		$error_found = true;
	//		$error_message .= "E-mail address 2 entered is already in database<BR>";
	//	}
	//}

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$title_ = escape_data($title_);
		$first_name = escape_data($first_name);
		$last_name = escape_data($last_name);
		$suffix = escape_data($suffix);
		$job_title = escape_data($job_title);
		$department = escape_data($department);
		//$address1 = escape_data($address1);
		//$address2 = escape_data($address2);
		//$city = escape_data($city);
		//$state = escape_data($state);
		//$zip = escape_data($zip);
		//$country = escape_data($country);
		$email1 = escape_data($email1);
		$email2 = escape_data($email2);
		$notes = escape_data($notes);

		if ( $cid != "" ) {
			$sql = "UPDATE customer_contacts " .
			" SET title = '" . $title_ . "'," .
			" address_id = " . $address_id . "," .
			" first_name = '" . $first_name . "'," .
			" last_name = '" . $last_name . "'," .
			" suffix = '" . $suffix . "'," .
			" job_title = '" . $job_title . "'," .
			" department = '" . $department . "'," .
			" customer_id = " . $customer_id . "," .
			//" address1 = '" . $address1 . "'," .
			//" address2 = '" . $address2 . "'," .
			//" city = '" . $city . "'," .
			//" state = '" . $state . "'," .
			//" zip = '" . $zip . "'," .
			//" country = '" . $country . "'," .
			" email1 = '" . $email1 . "'," .
			" email2 = '" . $email2 . "'," .
			" notes = '" . $notes . "'," .
			" active = " . $active .
			" WHERE contact_id = " . $cid;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		else {
			$sql = "INSERT INTO customer_contacts (address_id, title, first_name, last_name, suffix, job_title, department, customer_id, email1, email2, notes, active) VALUES (" . $address_id . ", '" . $title_ . "','" . $first_name . "','" . $last_name . "','" . $suffix . "','" . $job_title . "','" . $department . "', " . $customer_id . ", '" . $email1 . "', '" . $email2 . "','" . $notes . "', " . $active . ")";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$cid = mysql_insert_id();
		}

		if ( $_SESSION['userTypeCookie'] == 2 ) {
			$sql = "DELETE FROM contacts_users WHERE contact_id = " . $cid . " AND user_id = " . $_SESSION['user_id'];
			mysql_query($sql, $link);
			$sql = "INSERT INTO contacts_users (contact_id, user_id) VALUES (" . $cid . "," . $_SESSION['user_id'] . ")";
			mysql_query($sql, $link);
		} else {
			$sql = "DELETE FROM contacts_users WHERE contact_id = " . $cid;
			mysql_query($sql, $link);
			foreach ( $sales as $value ) {
				$sql = "INSERT INTO contacts_users (contact_id, user_id) VALUES (" . $cid . ", '" . $value . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
		}

		$_SESSION['note'] = "Contact information successfully saved<BR>";

		//if ( isset($_SESSION['pid']) ) {
		//	header("location: project_management_admin.client.php");
		//	exit();
		//} else {
			header("location: customers_contacts.edit.php?cid=" . $cid);
			exit();
		//}

	}

}

else {
	if ( $cid != '' ) {
		$sql = "SELECT customer_contacts.*, customers.name FROM customer_contacts LEFT JOIN customers USING(customer_id) WHERE contact_id = " . $cid;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		$address_id = $row['address_id'];
		$user_id = $row['user_id'];
		$cid = $row['contact_id'];
		$title_ = $row['title'];
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		$suffix = $row['suffix'];
		$job_title = $row['job_title'];
		$department = $row['department'];
		$customer_id = $row['customer_id'];
		//$name = $row['name'];
		//$address1 = $row['address1'];
		//$address2 = $row['address2'];
		//$city = $row['city'];
		//$state = $row['state'];
		//$zip = $row['zip'];
		//$country = $row['country'];
		$email1 = $row['email1'];
		$email2 = $row['email2'];
		$notes = $row['notes'];
		$active = $row['active'];

		$sales = array();
		$sql = "SELECT * FROM contacts_users WHERE contact_id = " . $cid;
		$result_sales = mysql_query($sql, $link);
		if ( mysql_num_rows($result_sales) > 0 ) {
			$i = 0;
			while ( $row_sales = mysql_fetch_array($result_sales) ) {
				$sales[$i] = $row_sales['user_id'];
				$i++;
			}
		}

	}
	else {
		$address_id = "";
		$user_id = "";
		$cid = "";
		$title_ = '';
		$first_name = "";
		$last_name = "";
		$suffix = '';
		$job_title = '';
		$department = '';
		if ( isset($_GET['cust_id']) ) {
			$customer_id = $_GET['cust_id'];
		} else {
			$customer_id = "";
		}
		//$name = "";
		//$address1 = "";
		//$address2 = "";
		//$city = "";
		//$state = "";
		//$zip = "";
		//$country = "";
		$email1 = "";
		$email2 = "";
		$active = 1;
		$notes = '';
		$sales = array();
	}
}


if ( $_GET['action'] == "inact" ) {
	$sql = "DELETE from customer_contact_phones WHERE phone_id = " . $_GET['pid'];
	mysql_query($sql, $link);
	header("location: customers_contacts.edit.php?cid=" . $_GET['cid']);
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


<?php if ( $customer_id != "" ) { ?>
	<A HREF="customers_customers.edit.php?cid=<?php echo $customer_id;?>">Customer info</A><BR><BR>
<?php } ?>


<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0><TR VALIGN=TOP><TD>

<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<FORM METHOD="post" ACTION="customers_contacts.edit.php">
<INPUT TYPE="hidden" NAME="cid" VALUE="<?php echo $cid;?>">
<INPUT TYPE="hidden" NAME="customer_id" VALUE="<?php echo $customer_id;?>">
<INPUT TYPE="hidden" NAME="action" VALUE="edit">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<?php  if ( $_SESSION['userTypeCookie'] == 1 ) { ?>
	
		<TR VALIGN=TOP>
			<TD><B CLASS="black">Assigned salespeople:</B>&nbsp;</TD>
			<TD>

			<?php

			$sql = "SELECT user_type, user_id, first_name, last_name FROM users WHERE is_salesperson = 1 ORDER BY last_name";
			$result = mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

			if ( mysql_num_rows($result) != 0 ) {
				while ( $row = mysql_fetch_array($result) ) {
					if ( in_array($row['user_id'], $sales) ) {
						echo "<INPUT TYPE='checkbox' NAME='sales[]' VALUE=" . $row['user_id'] . " CHECKED " . $check_radio . ">" . $row['first_name'] . " " . $row['last_name'] . "<BR>";
					} else {
						//if ( $row['user_type'] == 2 ) {
							echo "<INPUT TYPE='checkbox' NAME='sales[]' VALUE=" . $row['user_id'] . $check_radio . ">" . $row['first_name'] . " " . $row['last_name'] . "<BR>";
						//}
					}
				}
	 		}

			?>

			</TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>

	<?php } else { ?>
	
		<INPUT TYPE='hidden' NAME="user_id" VALUE="<?php echo $_SESSION['user_id'];?>">
	
	<?php } ?>

		</TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Title:</B></TD>
		<TD><INPUT TYPE='text' NAME="title_" SIZE=26 VALUE="<?php echo stripslashes($title_);?>" MAXLENGTH=100 <?php echo $form_status ?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">First name:</B></TD>
		<TD><INPUT TYPE='text' NAME="first_name" SIZE=26 VALUE="<?php echo stripslashes($first_name);?>" MAXLENGTH=100 <?php echo $form_status ?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Last name:</B></TD>
		<TD><INPUT TYPE='text' NAME="last_name" SIZE=26 VALUE="<?php echo stripslashes($last_name);?>" MAXLENGTH=100 <?php echo $form_status ?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Suffix:</B></TD>
		<TD><INPUT TYPE='text' NAME="suffix" SIZE=26 VALUE="<?php echo stripslashes($suffix);?>" MAXLENGTH=100 <?php echo $form_status ?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Job title:</B></TD>
		<TD><INPUT TYPE='text' NAME="job_title" SIZE=26 VALUE="<?php echo stripslashes($job_title);?>" MAXLENGTH=100 <?php echo $form_status ?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Department:</B></TD>
		<TD><INPUT TYPE='text' NAME="department" SIZE=26 VALUE="<?php echo stripslashes($department);?>" MAXLENGTH=100 <?php echo $form_status ?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Customer:</B></TD>

		<TD><SELECT NAME="customer_and_address" STYLE="font-size:7pt" <?php echo $form_status == "" ? "" : "DISABLED";?> >
		<?php
		if ( $_REQUEST['cust_id'] != '' ) {
			$filter = " WHERE customers.customer_id = " . $customer_id;
		} else {
			$filter = "";
			echo "<OPTION VALUE=''></OPTION>";
		}
		$sql = "SELECT name, customers.customer_id, customer_addresses.address_id, address1, address2, city, state
		FROM customers
		LEFT JOIN customer_addresses
		USING (customer_id) 
		" . $filter . "
		ORDER BY name";
		$result_customers = mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( mysql_num_rows($result_customers) != 0 ) {
			while ( $row_customers = mysql_fetch_array($result_customers) ) {
				if ( $customer_id == $row_customers['customer_id'] ) {
					echo "<OPTION VALUE='" . $row_customers['customer_id'] . "|" . $row_customers['address_id'] . "' SELECTED>" . $row_customers['name'] . " (" . $row_customers['city'] . ", " . $row_customers['state'] . ")</OPTION>";
				} else {
					echo "<OPTION VALUE='" . $row_customers['customer_id'] . "|" . $row_customers['address_id'] . "'>" . $row_customers['name'] . " (" . $row_customers['city'] . ", " . $row_customers['state'] . ")</OPTION>";
				}
			}
	 	}
		?>
		</SELECT></TD>

		<!-- <TD><input type="text" id="customer" name="customer" VALUE="<?php //echo stripslashes($name);?>" <?php //echo $form_status ?> />
		<input type="hidden" id="customer_id" name="customer_id" VALUE="<?php //echo stripslashes($customer_id);?>" /></TD> -->
	</TR>
<!--	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Address:</B></TD>
		<TD><INPUT TYPE='text' NAME="address1" SIZE=26 VALUE="<?php //echo stripslashes($address1);?>" MAXLENGTH=100 <?php //echo $form_status ?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD></TD>
		<TD><INPUT TYPE='text' NAME="address2" SIZE=26 VALUE="<?php //echo stripslashes($address2);?>" MAXLENGTH=100 <?php //echo $form_status ?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">City:</B></TD>
		<TD><INPUT TYPE='text' NAME="city" SIZE=26 VALUE="<?php //echo stripslashes($city);?>" MAXLENGTH=100 <?php //echo $form_status ?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">State:</B></TD>
		<TD><select name="state" id="state" <?php //echo $form_status ?>><?php //foreach($states as $val){ echo "<option".($state==$val?" selected=\"selected\"":"")." value=\"$val\">$val</option>\n"; } ?></select></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Postal code:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE='text' NAME="zip" SIZE=26 VALUE="<?php //echo stripslashes($zip);?>" MAXLENGTH=100 <?php //echo $form_status ?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Country:</B></TD>
		<TD><INPUT TYPE='text' NAME="country" SIZE=26 VALUE="<?php //echo stripslashes($country);?>" MAXLENGTH=100 <?php //echo $form_status ?>></TD>
	</TR> -->
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">E-mail 1:</B></TD>
		<TD><INPUT TYPE='text' NAME="email1" SIZE=26 VALUE="<?php echo stripslashes($email1);?>" MAXLENGTH=75 <?php echo $form_status ?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">E-mail 2:</B></TD>
		<TD><INPUT TYPE='text' NAME="email2" SIZE=26 VALUE="<?php echo stripslashes($email2);?>" MAXLENGTH=75 <?php echo $form_status ?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Notes:</B>&nbsp;</TD>
		<TD><TEXTAREA NAME="notes" ROWS="3" COLS="30" <?php echo $form_status ?>><?php echo stripslashes($notes);?></TEXTAREA></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Active:</B></TD>
		<TD>
		<?php if ( $active == "" or $active == "1" ) {
			print("<INPUT TYPE='radio' NAME='active' VALUE='1' CHECKED " . $check_radio . ">Yes ");
			print("<INPUT TYPE='radio' NAME='active' VALUE='0' " . $check_radio . ">No");
		} else {
			print("<INPUT TYPE='radio' NAME='active' VALUE='1' " . $check_radio . ">Yes ");
			print("<INPUT TYPE='radio' NAME='active' VALUE='0' CHECKED " . $check_radio . ">No");
		} ?>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>
	<TR>
		<TD></TD>
		<TD>
		<?php if ( $form_status == '' ) { ?>
			<INPUT TYPE='submit' VALUE="Save"> <INPUT TYPE='button' VALUE="Cancel" onClick="history.go(-1)">
		<?php } else { ?>
			<INPUT TYPE="button" NAME="Edit" VALUE="Edit" onClick="location.href='customers_contacts.edit.php?action=edit&cid=<?php echo $cid ?>'">
		<?php } ?>
		</TD>
	</TR></FORM>
</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE>









<?php if ( $cid != '' and !$main_error and $form_status != '' ) { ?>

	<?php

	if ( isset($pid) ) {
		$sql = "SELECT * FROM customer_contact_phones WHERE phone_id = " . $pid;
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
	<FORM METHOD="post" ACTION="customers_contacts.edit.php">
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
				echo "<INPUT TYPE='button' VALUE='Cancel' onClick=\"location.href='customers_contacts.edit.php?cid=" . $cid . "'\">";
			}
			?></NOBR></TD>
		</TR>

	</TABLE><BR>

	<?php
	if ( $pid == '' ) {
		$sql = "SELECT * FROM customer_contact_phones LEFT JOIN phone_types ON customer_contact_phones.type = phone_types.type_id 	WHERE contact_id = " . $cid . " ORDER BY type_id";
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
					<TD><A HREF="customers_contacts.edit.php?cid=<?php echo $cid;?>&pid=<?php echo $row['phone_id'];?>"><IMG SRC="images/pencil.gif" WIDTH="16" HEIGHT="16" BORDER=0></A></TD>
					<TD><A HREF="JavaScript:inactivate(<?php echo($row['phone_id'] . "," . $cid);?>)"><IMG SRC="images/delete.gif" WIDTH="16" 	HEIGHT="16" BORDER="0"></A></TD>
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
	}
	
	?></FORM>
	
	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>

<?php } ?>



</TD></TR></TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>



<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

function inactivate(pid, cid) {
	if ( confirm('Are you sure you want to delete this number?') ) {
		document.location.href = "customers_contacts.edit.php?action=inact&pid=" + pid + "&cid=" + cid
	}
}

 // End -->
</SCRIPT>


<script>
	var contacts="";
	$(document).ready(function(){
	
	$("#customer").autocomplete("search/customers_by_name.php", {
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		width: 350,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#customer").result(function(event, data, formatted) {
		if (data)
			$("#customer_id").val(data[1]);
			//contacts=search('search/contacts_by_customer_id',data[1]);
			//update_id('update/contacts_by_customer_id','contactspan',data[1]);
	});
	// $("#contact_name").autocomplete(contacts, {
		// matchContains: true,
		// mustMatch: true,
		// minChars: 0,
		// width: 350,
		// multipleSeparator: "¬",
		// scrollheight: 350
	// });
});
</script>


<?php include("inc_footer.php"); ?>