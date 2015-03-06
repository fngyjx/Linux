<?php

session_start();

include('global.php');
require_ssl();


if ( !$_SESSION['userTypeCookie'] ) {
	header ("Location: login.php?out=1");
	exit;
}


if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}



if ( !empty($_POST) ) {

	$shipper = $_POST['shipper'];
	$shipper_other = trim($_POST['shipper_other']);
	$shipping = $_POST['shipping'];
	$tracking_number = trim($_POST['tracking_number']);
	$pid = $_POST['pid'];

	// check_field() FUNCTION IN global.php
	if ( $shipper == 5 ) {
		check_field($shipper_other, 1, 'Other shipping company');
	}
	check_field($tracking_number, 1, 'Tracking number');

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$shipper_other = escape_data($shipper_other);
		$tracking_number = escape_data($tracking_number);

		$sql = "UPDATE projects " .
		" SET shipping = " . $shipping . "," .
		" shipper = " . $shipper . ", " .
		" shipper_other = '" . $shipper_other . "', " .
		" tracking_number = '" . $tracking_number . "' " .
		" WHERE project_id = " . $pid;
		mysql_query($sql, $link);
//echo $sql;
		$_SESSION['note'] = "Shipping information successfully saved<BR>";
		header("location: index.php");
		exit();
	}


}

else {

	$sql = "SELECT shipper, shipper_other, shipping, tracking_number FROM projects WHERE project_id = " . $_GET['pid'];
	$result = mysql_query($sql, $link);
	$row = mysql_fetch_array($result);
	$shipper = $row['shipper'];
	$shipper_other = $row['shipper_other'];
	$shipping = $row['shipping'];
	$tracking_number = $row['tracking_number'];

	$pid = $_GET['pid'];

}



$shipper_array = array("UPS","FedEx","DHL","USPS","Other");
$shipper_num = array(1,2,3,4,5);

$shipping_array = array("Next day","2nd day","Ground ","Date appropriate carrier");
$shipping_num = array(1,2,3,4);



include('header.php');

?>





<B CLASS="header">Shipping details</B><BR><BR>



<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>

<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>


<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0>
<FORM ACTION="shipping.php" METHOD="post">
<INPUT TYPE="hidden" NAME="pid" VALUE="<?php echo $pid;?>">

	<TR VALIGN=TOP>
		<TD><B>Shipping company:</B></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD><SELECT NAME="shipper">
			<?php 
			foreach ( $shipper_num as $value ) {
				if ( $value == $shipper ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $shipper_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $shipper_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT> &nbsp;<I>if "Other":</I> <INPUT TYPE="text" NAME="shipper_other" SIZE="20" VALUE="<?php echo $shipper_other?>" <?php echo $form_status ?>></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B>Shipping method:</B></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD><SELECT NAME="shipping">
			<?php 
			foreach ( $shipping_num as $value ) {
				if ( $value == $shipping ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $shipping_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $shipping_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B>Tracking number:</B></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" NAME="tracking_number" SIZE="30" VALUE="<?php echo stripslashes($tracking_number);?>"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="submit" VALUE="Save"> <INPUT TYPE="button" VALUE="Cancel" onClick="JavaScript:history.go(-1)"></TD>
	</TR></FORM>

</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>



<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

function inactivate(uid) {
	if ( confirm('Are you sure you want to inactivate this user?') ) {
		document.location.href = "shipping.php?action=inact&uid=" + uid
	}
}

 // End -->
</SCRIPT>

<?php include('footer.php'); ?>