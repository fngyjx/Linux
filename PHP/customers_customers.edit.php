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

if ( isset($_REQUEST['cid']) ) {
	$cid = $_REQUEST['cid'];
} elseif ( isset($_REQUEST['contact_id']) ) {
	$cid = $_REQUEST['contact_id'];
}

include('inc_global.php');



if ( !empty($_POST) and isset($_POST['main_form']) ) {
	$sql = "UPDATE customer_addresses SET main_location = 0 WHERE customer_id = " . $_POST['customer_id'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$sql = "UPDATE customer_addresses SET main_location = 1 WHERE address_id = " . $_POST['main_location'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$_SESSION['subnote'] = "Main location information updated<BR>";
	header("location: customers_customers.edit.php?cid=" . $_POST['customer_id']);
	exit();
}



if ( !empty($_POST) ) {

	if ( $_POST['sales'] != '' ) {
		$sales = $_POST['sales'];
	} else {
		$sales = array();
	}

	$customer_id = $_POST['customer_id'];
	$name = $_POST['name'];
	$web_address = $_POST['web_address'];
	$notes = $_POST['notes'];


	// check_field() FUNCTION IN global.php
	check_field($name, 1, 'Customer');

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$name = escape_data($name);
		$cid = $customer_id;
		if ( $_POST['customer_id'] != "" ) {
			$sql = "UPDATE customers " .
			" SET name = '" . $name . "'," .
			" web_address = '" . $web_address . "'," .
			" notes = '" . $notes . "'" .
			" WHERE customer_id = " . $customer_id;
			mysql_query($sql, $link);
		}
		else {
			$sql = "INSERT INTO customers (name, web_address, notes) VALUES ('" . $name . "', '" . $web_address . "', '" . $notes . "')";
			mysql_query($sql, $link);   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$cid = mysql_insert_id();
		}

		if ( $_SESSION['userTypeCookie'] == 2 ) {
			$sql = "DELETE FROM customers_users WHERE customer_id = " . $cid . " AND user_id = " . $_SESSION['user_id'];
			mysql_query($sql, $link);
			$sql = "INSERT INTO customers_users (customer_id, user_id) VALUES (" . $cid . "," . $_SESSION['user_id'] . ")";
			mysql_query($sql, $link);
		} else {
			$sql = "DELETE FROM customers_users WHERE customer_id = " . $cid;
			mysql_query($sql, $link);
			foreach ( $sales as $value ) {
				$sql = "INSERT INTO customers_users (customer_id, user_id) VALUES (" . $cid . ", '" . $value . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
		}

		$_SESSION['note'] = "Customer information successfully saved<BR>";
		header("location: customers_customers.php");
		exit();
	}


}

else {
	if ( isset($_GET['cid']) ) {
		$sql = "SELECT * FROM customers WHERE active = 1 AND customer_id = " . $_GET['cid'];
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result);
		$customer_id = $row['customer_id'];
		$name = $row['name'];
		$web_address = $row['web_address'];
		$notes = $row['notes'];
		
		if ( mysql_num_rows($result) > 0 ) {
		$sales = array();
		$sql = "SELECT * FROM customers_users WHERE customer_id = " . $_GET['cid'];
		$result_sales = mysql_query($sql, $link);
		if ( mysql_num_rows($result_sales) > 0 ) {
			$i = 0;
			while ( $row_sales = mysql_fetch_array($result_sales) ) {
				$sales[$i] = $row_sales['user_id'];
				$i++;
			}
		}
		}

	}
	else {
		$customer_id = "";
		$name = "";
		$web_address = "";
		$notes = "";
		$sales = array();
	}
}


if ( $_GET['action'] == "inact" ) {
	$sql = "UPDATE customer_addresses SET active = 0 WHERE address_id = " . $_GET['aid'];
	mysql_query($sql, $link);
	header("location: customers_customers.edit.php?cid=" . $_GET['cid']);
	exit();
}


if ( $_GET['action'] == "inact_contact" ) {
	$sql = "UPDATE customer_contacts SET active = 0 WHERE contact_id = " . $_GET['contact_id'];
	mysql_query($sql, $link);
	header("location: customers_customers.edit.php?cid=" . $_GET['cid']);
	exit();
}



include("inc_header.php");

?>


<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0><TR VALIGN=TOP><TD>

<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<FORM METHOD="post" ACTION="customers_customers.edit.php">


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR>
		<INPUT TYPE="hidden" NAME="customer_id" VALUE="<?php echo $customer_id;?>">
		<TD><B CLASS="black">Customer:</B>&nbsp;</TD>
		<TD><INPUT TYPE='text' NAME="name" SIZE=42 VALUE="<?php echo stripslashes($name);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>

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
						echo "<INPUT TYPE='checkbox' NAME='sales[]' VALUE=" . $row['user_id'] . " CHECKED>" . $row['first_name'] . " " . $row['last_name'] . "<BR>";
					} else {
						//if ( $row['user_type'] == 2 ) {
							echo "<INPUT TYPE='checkbox' NAME='sales[]' VALUE=" . $row['user_id'] . ">" . $row['first_name'] . " " . $row['last_name'] . "<BR>";
						//}
					}
				}
	 		}

			?>

			</TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
		</TR>

	<?php } ?>

	<TR>
		<TD><B CLASS="black">Web site:</B>&nbsp;</TD>
		<TD><INPUT TYPE='text' NAME="web_address" SIZE=42 VALUE="<?php echo stripslashes($web_address);?>"></TD>
	</TR>

	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>

	<TR>
		<TD VALIGN=TOP><B CLASS="black">Notes:</B>&nbsp;</TD>
		<TD><TEXTAREA NAME="notes" ROWS="8" COLS="40"><?php echo stripslashes($notes);?></TEXTAREA></TD>
	</TR>

	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>

	<TR>
		<TD></TD>
		<TD><INPUT TYPE='submit' VALUE="Save"> <INPUT TYPE='button' VALUE="Cancel" onClick="window.location='customers_customers.php'"></TD>
	</TR></FORM>
</TABLE>


</TD></TR></TABLE>
</TD></TR></TABLE>

</TD></TR></TABLE>


</TD><TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1">






<TD>

<?php if ( isset($cid) and !$main_error ) { ?>

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

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD COLSPAN=2><B STYLE="font-size:8pt">Addresses</B> / <A HREF="customers_customers.add_edit.php?cid=<?php echo $cid;?>" STYLE="font-size:8pt">Add new address</A></TD>
		</TR>
	</TABLE>

	<?php
	$sql = "SELECT * FROM customer_addresses WHERE customer_id = " . $_GET['cid'] . " AND active = 1";
	$result_list = mysql_query($sql, $link);
	if ( mysql_num_rows($result_list) > 0 ) {
		$bg = 0; ?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
			<TR>
				<TD COLSPAN=3></TD>
				<TD ALIGN=CENTER><B STYLE="font-size:7pt">Main</B></TD>
			</TR>
			<FORM METHOD="post" ACTION="customers_customers.edit.php">
			<INPUT TYPE="hidden" NAME="customer_id" VALUE="<?php echo $customer_id;?>">
			<INPUT TYPE="hidden" NAME="main_form" VALUE="1">
		<?php
		while ( $row_list = mysql_fetch_array($result_list) ) {
			if ( $bg == 1 ) {
				$bgcolor = "#F3E7FD";
				$bg = 0;
			}
			else {
				$bgcolor = "#DFDFDF";
				$bg = 1;
			} ?>
			<TR BGCOLOR="<?php echo($bgcolor);?>" VALIGN=TOP>
				<TD><A HREF="customers_customers.add_edit.php?cid=<?php echo $cid;?>&aid=<?php echo $row_list['address_id'];?>"><IMG SRC="images/pencil.gif" WIDTH="16" HEIGHT="16" BORDER=0></A></TD>
				<TD><A HREF="JavaScript:inactivate(<?php echo($row_list['address_id'] . "," . $cid);?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A></TD>
				<TD STYLE="font-size:8pt">
				<?php
				if ( $row_list['address1'] != '' ) {
					echo $row_list['address1'];
					echo "<BR>";
				}
				if ( $row_list['address2'] != '' ) {
					echo $row_list['address2'];
					echo "<BR>";
				}
				echo $row_list['city'] . ", " . $row_list['state'] . " " . $row_list['zip'] . " " . $row_list['country'] . "<br />";
				
				$sql_phone="SELECT * FROM customer_address_phones JOIN phone_types on phone_types.type_id=customer_address_phones.type where address_id = ". $row_list['address_id']; 
				$phone_result = mysql_query($sql_phone,$link) or die (mysql_error()." Failed to execute $sql_phone <br />");
					while ( $row_phone = mysql_fetch_array($phone_result) ) {
						echo "<NOBR>" . $row_phone['description'] .":".$row_phone['number'] ."</NOBR><br />";
					}
				?>
				</TD>
				<?php
				if ( $row_list['main_location'] == 1 ) {
					$field_status = "CHECKED";
				} else {
					$field_status = "";
				}
				?>
				<TD ALIGN=CENTER><INPUT TYPE="radio" NAME="main_location" VALUE="<?php echo$row_list['address_id'];?>"  onClick="this.form.action='customers_customers.edit.php';this.form.submit()" <?php echo $field_status;?>></TD>
			</TR>
		<?php } ?>
		</FORM></TABLE>
	<?php } else {
		echo "<BR><I STYLE='font-size:8pt'>No addresses found</I>";
	}

}

?>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR>























<?php if ( isset($cid) and !$main_error ) { ?>

	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD COLSPAN=2><B STYLE="font-size:8pt">Contacts</B> / <A HREF="customers_contacts.edit.php?action=edit&cust_id=<?php echo $cid;?>" STYLE="font-size:8pt">Add new contact</A></TD>
		</TR>
	</TABLE><BR>

	<?php
	$sql = "SELECT * FROM customer_contacts WHERE customer_id = " . $_GET['cid'] . " AND active = 1";
	$result_list = mysql_query($sql, $link);
	if ( mysql_num_rows($result_list) > 0 ) {
		$bg = 0; ?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
		<?php
		while ( $row_list = mysql_fetch_array($result_list) ) {
			if ( $bg == 1 ) {
				$bgcolor = "#F3E7FD";
				$bg = 0;
			}
			else {
				$bgcolor = "#DFDFDF";
				$bg = 1;
			} ?>
			<TR BGCOLOR="<?php echo($bgcolor);?>" VALIGN=TOP>
				<TD><A HREF="customers_contacts.edit.php?cid=<?php echo $row_list['contact_id'];?>"><IMG SRC="images/pencil.gif" WIDTH="16" HEIGHT="16" BORDER=0></A></TD>
				<TD><A HREF="JavaScript:inactivate_contact(<?php echo($row_list['contact_id'] . "," . $cid);?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A></TD>
				<TD STYLE="font-size:8pt">
				<?php
				echo "<B STYLE='font-size:8pt'>" . $row_list['first_name'] . " " . $row_list['last_name'] . "</B><BR>";
				//if ( $row_list['address1'] != '' ) {
				//	echo $row_list['address1'];
				//	echo "<BR>";
				//}
				//if ( $row_list['address2'] != '' ) {
				//	echo $row_list['address2'];
				//	echo "<BR>";
				//}
				//echo $row_list['city'] . ", " . $row_list['state'] . " " . $row_list['zip'] . " " . $row_list['country'];
				if ( $row_list['email1'] != '' ) {
					echo "<A HREF='mailto:" . $row_list['email1'] . "'>" . $row_list['email1'] . "</A><BR>";
				}
				if ( $row_list['email2'] != '' ) {
					echo "<BR><A HREF='mailto:" . $row_list['email2'] . "'>" . $row_list['email2'] . "</A><BR>";
				}
				$sql = "SELECT * FROM customer_contact_phones
				LEFT JOIN phone_types ON customer_contact_phones.type = phone_types.type_id
				WHERE customer_contact_phones.contact_id = " . $row_list['contact_id'];
				$result_phone = mysql_query($sql, $link);
				if ( mysql_num_rows($result_phone) > 0 ) { ?>
					<IMG SRC="images/delete.gif" WIDTH="1" HEIGHT="3" BORDER="0"><BR>
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
					<?php
					while ( $row_phone = mysql_fetch_array($result_phone) ) { ?>
							<TR VALIGN=TOP>
								<TD><NOBR><I STYLE="font-size:8pt"><?php echo $row_phone['description'];?>:</I>&nbsp;&nbsp;</NOBR></TD>
								<TD STYLE="font-size:8pt"><NOBR><?php echo $row_phone['number'];?>&nbsp;&nbsp;</NOBR></TD>
								<TD><NOBR><?php
								if ( $row_phone['number_description'] != '' ) {
									echo "(<I STYLE='font-size:8pt'>" . $row_phone['number_description'] . "</I>)";
								}
								?></NOBR></TD>
							</TR>
					<?php } ?>
					</TABLE>
				<?php } else {
					echo "<I STYLE='font-size:8pt'>No numbers found</I>";
				}
				?>

				</TD>
				<?php
				if ( $row_list['main_location'] == 1 ) {
					$field_status = "CHECKED";
				} else {
					$field_status = "";
				}
				?>
			</TR>
		<?php } ?>
		</TABLE>
	<?php } else {
		echo "<I STYLE='font-size:8pt'>No contacts found</I>";
	}

?>

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

function inactivate(aid, cid) {
	if ( confirm('Are you sure you want to delete this address?') ) {
		document.location.href = "customers_customers.edit.php?action=inact&aid=" + aid + "&cid=" + cid
	}
}

function inactivate_contact(contact_id, cid) {
	if ( confirm('Are you sure you want to delete this contact?') ) {
		document.location.href = "customers_customers.edit.php?action=inact_contact&contact_id=" + contact_id + "&cid=" + cid
	}
}

 // End -->
</SCRIPT>


<?php include("inc_footer.php"); ?>