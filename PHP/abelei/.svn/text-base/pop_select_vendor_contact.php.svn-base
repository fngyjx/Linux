<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( isset($_REQUEST['vid']) ) {
	$vid = $_REQUEST['vid'];
}

if ( isset($_REQUEST['cid']) ) {
	$cid = $_REQUEST['cid'];
}

include('inc_global.php');

if ( !empty($_POST) ) {

	//$_SESSION['note'] = "Contact information successfully saved<BR>";
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";

	//$sql = "SELECT * FROM vendor_contacts LEFT JOIN vendor_addresses ON vendor_contacts.vendor_address_id = vendor_addresses.address_id WHERE contact_id = " . $_REQUEST['cid'];
	$sql = "SELECT * FROM vendor_contacts WHERE contact_id = " . $_REQUEST['cid'];
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	echo "window.opener.document.header_info.VendorSalesRep.value='" . $row['first_name'] . " " . $row['last_name'] . "'\n";
	echo "window.opener.document.header_info.contact_id.value='" . $row['contact_id'] . "'\n";
	//echo "window.opener.document.header_info.VendorStreetAddress1.value='" . $row['address1'] . "'\n";
	//echo "window.opener.document.header_info.VendorStreetAddress2.value='" . $row['address2'] . "'\n";
	//echo "window.opener.document.header_info.VendorCity.value='" . $row['city'] . "'\n";
	//echo "window.opener.document.header_info.VendorState.value='" . $row['state'] . "'\n";
	//echo "window.opener.document.header_info.VendorZipCode.value='" . $row['zip'] . "'\n";
	
	$sql = "SELECT * FROM vendor_contact_phones WHERE contact_id = " . $row['contact_id'] . " AND type = 2 LIMIT 1";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	if ( mysql_num_rows($result) > 0 ) {
		$row = mysql_fetch_array($result);
		echo "window.opener.document.header_info.VendorMainPhoneNumber.value='" . $row['number'] . "'\n";
	}

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
	//$sql = "SELECT * FROM vendor_contacts LEFT JOIN vendor_addresses ON vendor_contacts.vendor_address_id = vendor_addresses.address_id WHERE vendor_contacts.vendor_id = " . $_GET['vid'] . " AND vendor_contacts.active = 1 ORDER BY last_name";
	$sql = "SELECT * FROM vendor_contacts WHERE vendor_id = " . $_GET['vid'] . " AND active = 1 ORDER BY last_name";
	$result_list = mysql_query($sql, $link);
	if ( mysql_num_rows($result_list) > 0 ) { ?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="4">
		<?php
		while ( $row_list = mysql_fetch_array($result_list) ) { ?>
			<TR VALIGN=TOP>
				<FORM METHOD="post" ACTION="pop_select_vendor_contact.php">
				<INPUT TYPE="hidden" NAME="vid" VALUE="<?php echo $vid;?>">
				<TD><INPUT TYPE="image" SRC="images/select.png" BORDER="0"><INPUT TYPE="hidden" NAME="cid" VALUE="<?php echo $row_list['contact_id'];?>"></TD>
				<TD>
				<?php
				echo "<B>" . $row_list['first_name'] . " " . $row_list['last_name'] . "</B><BR>";
				//if ( $row_list['address1'] != '' ) {
				//	echo $row_list['address1'];
				//	echo "<BR>";
				//}
				//if ( $row_list['address2'] != '' ) {
				//	echo $row_list['address2'];
				//	echo "<BR>";
				//}
				//echo $row_list['city'] . ", " . $row_list['state'] . " " . $row_list['zip'] . " " . $row_list['country'];
				?>
				</TD>
			</TR></FORM>
		<?php } ?>
		</TABLE>
	<?php } else { ?>
		<I>No contacts found - </I><input type="button" onClick="document.location.href='pop_add_vendor_contact.php?vend_id=<?php echo $vid ?>'" value="Add new contact"  class="submit_medium" />
<?php	} ?>

<BR>



<?php include("inc_footer.php"); ?>