<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN AND FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 4 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( isset($_REQUEST['cid']) ) {
	$cid = $_REQUEST['cid'];
}

if ( isset($_REQUEST['type']) ) {
	$type = $_REQUEST['type'];
}

include('inc_global.php');



if ( !empty($_POST) ) {
	if ( $type == 's' ) {
		$address_type = "shipping";
	} else {
		$address_type = "billing";
	}
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	$sql = "SELECT * FROM customer_addresses WHERE address_id = " . $cid;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	echo "window.opener.document.header_info." . $address_type . "_id.value='" . $cid . "'\n";
	echo "window.opener.document.header_info." . $address_type . "_address1.value='" . $row['address1'] . "'\n";
	echo "window.opener.document.header_info." . $address_type . "_address2.value='" . $row['address2'] . "'\n";
	echo "window.opener.document.header_info." . $address_type . "_city.value='" . $row['city'] . "'\n";
	echo "window.opener.document.header_info." . $address_type . "_state.value='" . $row['state'] . "'\n";
	echo "window.opener.document.header_info." . $address_type . "_zip.value='" . $row['zip'] . "'\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
	//die();
}



include("inc_pop_header.php");

?>

<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


	<?php
	//$sql = "SELECT customer_addresses.*, customer_contacts.first_name, customer_contacts.last_name FROM customer_addresses LEFT JOIN customer_contacts ON (customer_contacts.customer_id=customer_addresses.customer_id) WHERE customer_contacts.contact_id = " . $cid . " AND customer_contacts.active = 1 ORDER BY last_name";
	$sql = "SELECT * 
	FROM customer_addresses
	WHERE customer_id = " . $cid . "
	AND active =1
	ORDER BY state, city, zip";
	$result_list = mysql_query($sql, $link);
	if ( mysql_num_rows($result_list) > 0 ) { ?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="4">
		<?php
		while ( $row_list = mysql_fetch_array($result_list) ) { ?>
			<TR VALIGN=TOP>
				<FORM METHOD="post" ACTION="pop_select_customer_address.php">
				<INPUT TYPE="hidden" NAME="type" VALUE="<?php echo $type;?>">
				<INPUT TYPE="hidden" NAME="cid" VALUE="<?php echo $row_list['address_id'];?>">
				<TD><INPUT TYPE="image" SRC="images/select.png" BORDER="0"><INPUT TYPE="hidden" NAME="vid" VALUE="<?php echo $vid;?>"></TD>
				<TD>
				<?php
				//echo "<B>" . $row_list['first_name'] . " " . $row_list['last_name'] . "</B><BR>";
				if ( $row_list['address1'] != '' ) {
					echo $row_list['address1'];
					echo "<BR>";
				}
				if ( $row_list['address2'] != '' ) {
					echo $row_list['address2'];
					echo "<BR>";
				}
				echo $row_list['city'] . ", " . $row_list['state'] . " " . $row_list['zip'] . " " . $row_list['country'];
				?>
				</TD>
			</TR></FORM>
		<?php } ?>
		</TABLE>
	<?php } else {
		echo "<I>No contacts found</I>";
	}

?>

<BR>



<?php include("inc_footer.php"); ?>