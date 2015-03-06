<?php

session_start();

include('global.php');
require_ssl();

if ( $_SESSION['userTypeCookie'] != 1 and $_SESSION['userTypeCookie'] != 2 ) {
	header ("Location: login.php?out=1");
	exit;
}



if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}



if ( !empty($_POST) ) {

	if ( $_POST['sales'] != '' ) {
		$sales = $_POST['sales'];
	} else {
		$sales = array();
	}

	$client_id = $_POST['client_id'];
	//$user_id = $_POST['user_id'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$company_id = $_POST['company_id'];
	$company = $_POST['company'];
	$address1 = $_POST['address1'];
	$address2 = $_POST['address2'];
	$city = $_POST['city'];
	$state = $_POST['state'];
	$zip = $_POST['zip'];
	$country = $_POST['country'];
	$phone = $_POST['phone'];
	$fax = $_POST['fax'];
	$email = $_POST['email'];
	$active = $_POST['active'];

	// check_field() FUNCTION IN global.php
	check_field($first_name, 1, 'First name');
	check_field($last_name, 1, 'Last name');
	check_field($company_id, 1, 'Company');
	check_field($address1, 1, 'Address');
	check_field($city, 1, 'City');
	check_field($state, 1, 'State');
	check_field($zip, 1, 'Postal code');
	check_field($country, 1, 'Country');
	check_field($phone, 1, 'Phone');
	check_field($email, 1, 'E-mail');

	if ( $_POST['client_id'] != "" ) {
		$id_check = " AND client_id <> " .  $_POST['client_id'];
	}
	else {
		$id_check = "";
	}

	if ( $email != "" ) {
		$sql = "SELECT * FROM clients WHERE email = '" . $email . "'" . $id_check;
		$result = mysql_query($sql, $link);
		//mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
		if ( $c > 0 ) {
			$error_found = true;
			$error_message .= "E-mail address entered is already in database<BR>";
		}
	}

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$first_name = escape_data($first_name);
		$last_name = escape_data($last_name);
		$address1 = escape_data($address1);
		$address2 = escape_data($address2);
		$city = escape_data($city);
		$state = escape_data($state);
		$zip = escape_data($zip);
		$country = escape_data($country);
		$phone = escape_data($phone);
		$fax = escape_data($fax);
		$email = escape_data($email);

		$cid = $client_id;

		if ( $_POST['client_id'] != "" ) {
			$sql = "UPDATE clients " .
			" SET first_name = '" . $first_name . "'," .
			" last_name = '" . $last_name . "'," .
			" company_id = " . $company_id . "," .
			" address1 = '" . $address1 . "'," .
			" address2 = '" . $address2 . "'," .
			" city = '" . $city . "'," .
			" state = '" . $state . "'," .
			" zip = '" . $zip . "'," .
			" country = '" . $country . "'," .
			" phone = '" . $phone . "'," .
			" fax = '" . $fax . "'," .
			" email = '" . $email . "'," .
			" active = " . $active .
			" WHERE client_id = " . $client_id;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		else {
			$sql = "INSERT INTO clients (first_name, last_name, company_id, address1, address2, city, state, zip, country, phone, fax, email) VALUES ('" . $first_name . "','" . $last_name . "', " . $company_id . ", '" . $address1 . "', '" . $address2 . "', '" . $city . "', '" . $state . "', '" . $zip . "', '" . $country . "', '" . $phone . "', '" . $fax . "', '" . $email . "')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$cid = mysql_insert_id();
		}

		if ( $_SESSION['userTypeCookie'] == 2 ) {
			$sql = "DELETE FROM clients_users WHERE client_id = " . $cid . " AND user_id = " . $_SESSION['user_id'];
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$sql = "INSERT INTO clients_users (client_id, user_id) VALUES (" . $cid . "," . $_SESSION['user_id'] . ")";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		} else {
			$sql = "DELETE FROM clients_users WHERE client_id = " . $cid;
			mysql_query($sql, $link);
			foreach ( $sales as $value ) {
				$sql = "INSERT INTO clients_users (client_id, user_id) VALUES (" . $cid . ", '" . $value . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
		}

		$_SESSION['note'] = "Client information successfully saved<BR>";

		if ( isset($_SESSION['pid']) ) {
			header("location: client_info.php");
			exit();
		} else {
			header("location: choose_client.php");
			exit();
		}

	}

}

else {
	if ( isset($_GET['cid']) ) {
		$sql = "SELECT clients.*, companies.company FROM clients LEFT JOIN companies USING(company_id) WHERE client_id = " . $_GET['cid'];
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result);
		//$user_id = $row['user_id'];
		$client_id = $row['client_id'];
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		$company_id = $row['company_id'];
		$company = $row['company'];
		$address1 = $row['address1'];
		$address2 = $row['address2'];
		$city = $row['city'];
		$state = $row['state'];
		$zip = $row['zip'];
		$country = $row['country'];
		$phone = $row['phone'];
		$fax = $row['fax'];
		$email = $row['email'];
		$active = $row['active'];

		$sales = array();
		$sql = "SELECT * FROM clients_users WHERE client_id = " . $_GET['cid'];
		$result_sales = mysql_query($sql, $link);
		if ( mysql_num_rows($result_sales) > 0 ) {
			$i = 0;
			while ( $row_sales = mysql_fetch_array($result_sales) ) {
				$sales[$i] = $row_sales['user_id'];
				$i++;
			}
		}

	} else if ( $_GET['company_id'] != "" and $_GET['address1'] != "" ) {
		$client_id = "";
		$first_name = "";
		$last_name = "";
		$company_id=escape_data($_GET['company_id']);
		$address1=escape_data($_GET['address1']);
		$address2=escape_data($_GET['address2']);
		$city=escape_data($_GET['city']);
		$state=escape_data($_GET['state']);
		$zip=escape_data($_GET['zip']);
		$country=escape_data($_GET['country']);
		$phone=escape_data($_GET['phone']);
		$fax=escape_data($_GET['fax']);
		$email=escape_data($_GET['email']);
		$active = 1;
		$sales = array();
	}
	else {
		//$user_id = "";
		$client_id = "";
		$first_name = "";
		$last_name = "";
		$company_id = "";
		$company = "";
		$address1 = "";
		$address2 = "";
		$city = "";
		$state = "";
		$zip = "";
		$country = "";
		$phone = "";
		$fax = "";
		$email = "";
		$active = 1;
		$sales = array();
	}
}



if ( $_GET['action'] == "inact" ) {
	$sql = "UPDATE clients SET active = 0 WHERE client_id = " . $_GET['cid'];
	mysql_query($sql, $link);
}



include('header.php');

?>



<B CLASS="header">Clients</B>

<?php 

if ( $client_id != "" ) {
	echo " / <B>Edit</B>";
}

?>

<BR><BR>



<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>



<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<FORM METHOD="post" id="c_form" name="c_form" ACTION="clients.php">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">



	<?php  if ( $_SESSION['userTypeCookie'] == 1 ) { ?>
	
		<TR VALIGN=TOP>
			<TD><B CLASS="black">Assigned salespeople:</B>&nbsp;</TD>
			<TD>

			<?php

			$sql = "SELECT user_type, user_id, first_name, last_name FROM users WHERE user_type < 3 ORDER BY last_name";
			$result = mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

			if ( mysql_num_rows($result) != 0 ) {
				while ( $row = mysql_fetch_array($result) ) {
					if ( in_array($row['user_id'], $sales) ) {
						echo "<INPUT TYPE='checkbox' NAME='sales[]' VALUE=" . $row['user_id'] . " CHECKED>" . $row['first_name'] . " " . $row['last_name'] . "<BR>";
					} else {
						if ( $row['user_type'] == 2 ) {
							echo "<INPUT TYPE='checkbox' NAME='sales[]' VALUE=" . $row['user_id'] . ">" . $row['first_name'] . " " . $row['last_name'] . "<BR>";
						}
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
	
		<!-- <INPUT TYPE='hidden' NAME="user_id" VALUE="<?php //echo $_SESSION['user_id'];?>"> -->
	
	<?php } ?>



		</TD>
	</TR>

	<TR>
		<INPUT TYPE="hidden" NAME="client_id" VALUE="<?php echo $client_id;?>">
		<TD><B CLASS="black">First name:</B></TD>
		<TD><INPUT TYPE='text' NAME="first_name" SIZE=26 VALUE="<?php echo stripslashes($first_name);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Last name:</B></TD>
		<TD><INPUT TYPE='text' NAME="last_name" SIZE=26 VALUE="<?php echo stripslashes($last_name);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Company:</B></TD>
		<TD>

<?php

$sql = "SELECT company_id, company FROM companies ORDER BY company";
$result = mysql_query($sql, $link);

if ( mysql_num_rows($result) > 0 ) { ?> 
	<SELECT NAME="company_id" id="company_id" onChange="setClientAddr()">
		<OPTION VALUE=""></OPTION>
		<?php
		while ( $row = mysql_fetch_array($result) ) {
			if ( $company_id == $row['company_id'] ) {
				echo "<OPTION VALUE='" . $row['company_id'] . "' SELECTED>" . $row['company'] . "</OPTION>";
			} else {
				echo "<OPTION VALUE='" . $row['company_id'] . "'>" . $row['company'] . "</OPTION>";
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
		<TD><B CLASS="black">Address:</B></TD>
		<TD><INPUT TYPE='text' NAME="address1" SIZE=26 VALUE="<?php echo stripslashes($address1);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD></TD>
		<TD><INPUT TYPE='text' NAME="address2" SIZE=26 VALUE="<?php echo stripslashes($address2);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">City:</B></TD>
		<TD><INPUT TYPE='text' NAME="city" SIZE=26 VALUE="<?php echo stripslashes($city);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">State:</B></TD>
		<TD><INPUT TYPE='text' NAME="state" SIZE=4 VALUE="<?php echo stripslashes($state);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Postal code:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE='text' NAME="zip" SIZE=26 VALUE="<?php echo stripslashes($zip);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Country:</B></TD>
		<TD><INPUT TYPE='text' NAME="country" SIZE=26 VALUE="<?php echo stripslashes($country);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Phone:</B></TD>
		<TD><INPUT TYPE='text' NAME="phone" SIZE=26 VALUE="<?php echo stripslashes($phone);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Fax:</B></TD>
		<TD><INPUT TYPE='text' NAME="fax" SIZE=26 VALUE="<?php echo stripslashes($fax);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">E-mail:</B></TD>
		<TD><INPUT TYPE='text' NAME="email" SIZE=26 VALUE="<?php echo stripslashes($email);?>"></TD>
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
		<TD><INPUT TYPE='submit' VALUE="Save"> <INPUT TYPE='button' VALUE="Cancel" onClick="JavaScript:history.go(-1)"></TD>
	</TR>
</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>



<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

function setClientAddr() 
{
   var w = document.c_form.company_id.selectedIndex;
   var company_id = document.c_form.company_id.options[w].value;
   popup("client_addresses.php?company_id="+company_id, 720, 500, 200, 200);
}
 // End -->
</SCRIPT>

<?php include('footer.php'); ?>