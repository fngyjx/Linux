<?php 

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');

$states = array("", "AL","AK","AZ","AR","CA","CO","CT","DE","DC","FL","GA","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MA","MI","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT", "VT","VA","WA","WV","WI","WY");

if ( isset($_GET['sp']) ) {   // SHIP TO SALESPERSON
	$sql = "SELECT * FROM users WHERE user_id = " . $_GET['sp'];
	$result = mysql_query($sql, $link);
	$row = mysql_fetch_array($result);
	$first_name = $row['first_name'];
	$last_name = $row['last_name'];
	$address1 = $row['address1'];
	$address2 = $row['address2'];
	$city = $row['city'];
	$state = $row['state'];
	$zip = $row['zip'];
	$phone = $row['phone'];
	$sql = "INSERT INTO shipping_info (project_id, first_name, last_name, address1, address2, city, state, zip, phone) VALUES (" . $_SESSION['pid'] . ", '" . $first_name . "', '" . $last_name . "', '" . $address1 . "', '" . $address2 . "', '" . $city . "', '" . $state . "', '" . $zip . "', '" . $phone . "')";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	header("location: project_management_admin.client.php");
	exit();
}



if ( !empty($_POST) ) {

	$shipping_id = $_POST['shipping_id'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$company = $_POST['company'];
	$address1 = $_POST['address1'];
	$address2 = $_POST['address2'];
	$city = $_POST['city'];
	$state = $_POST['state'];
	$zip = $_POST['zip'];
	$country = $_POST['country'];
	$phone = $_POST['phone'];

	// check_field() FUNCTION IN global.php
	check_field($first_name, 1, 'First name');
	check_field($last_name, 1, 'Last name');
	check_field($address1, 1, 'Address');
	check_field($city, 1, 'City');
	check_field($state, 1, 'State');
	check_field($zip, 1, 'Postal code');

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$first_name = escape_data($first_name);
		$last_name = escape_data($last_name);
		$company = escape_data($company);
		$address1 = escape_data($address1);
		$address2 = escape_data($address2);
		$city = escape_data($city);
		$state = escape_data($state);
		$zip = escape_data($zip);
		$country = escape_data($country);
		$phone = escape_data($phone);

		if ( $_POST['shipping_id'] != "" ) {
			$sql = "UPDATE shipping_info " .
			" SET first_name = '" . $first_name . "'," .
			" last_name = '" . $last_name . "'," .
			" company = '" . $company . "'," .
			" address1 = '" . $address1 . "'," .
			" address2 = '" . $address2 . "'," .
			" city = '" . $city . "'," .
			" state = '" . $state . "'," .
			" zip = '" . $zip . "'," .
			" country = '" . $country . "', " .
			" phone = '" . $phone . "'" .
			" WHERE shipping_id = " . $shipping_id;
			mysql_query($sql, $link);
		}
		else {
			$sql = "INSERT INTO shipping_info (project_id, first_name, last_name, company, address1, address2, city, state, zip, country, phone) VALUES (" . $_SESSION['pid'] . ", '" . $first_name . "','" . $last_name . "', '" . $company . "', '" . $address1 . "', '" . $address2 . "', '" . $city . "', '" . $state . "', '" . $zip . "', '" . $country . "', '" . $phone . "')";
			mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		header("location: project_management_admin.client.php");
		exit();

	}


}

else {
	if ( isset($_GET['sid']) ) {
		$sql = "SELECT * FROM shipping_info WHERE shipping_id = " . $_GET['sid'];
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result);
		$shipping_id = $row['shipping_id'];
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		$company = $row['company'];
		$address1 = $row['address1'];
		$address2 = $row['address2'];
		$city = $row['city'];
		$state = $row['state'];
		$zip = $row['zip'];
		$country = $row['country'];
		$phone = $row['phone'];
	}
	else {
		$shipping_id = "";
		$first_name = "";
		$last_name = "";
		$company = "";
		$address1 = "";
		$address2 = "";
		$city = "";
		$state = "";
		$zip = "";
		$country = "";
		$phone = "";
	}
}



include('inc_header.php');

?>



<B CLASS="header">Shipping information</B><BR><BR>


<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR><FORM METHOD="post" ACTION="project_management_admin_shipping_address.php">
		<INPUT TYPE="hidden" NAME="shipping_id" VALUE="<?php echo $shipping_id;?>">
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
		<TD><INPUT TYPE='text' NAME="company" SIZE=26 VALUE="<?php echo stripslashes($company);?>"></TD>
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
		<TD><select name="state" id="state"><?php foreach($states as $val){ echo "<option".($state==$val?" selected=\"selected\"":"")." value=\"$val\">$val</option>\n"; } ?></select></TD>
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



<?php include('inc_footer.php'); ?>