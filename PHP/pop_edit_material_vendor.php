<?php

include('inc_ssl_check.php');
if ( !isset($_SESSION) ) { session_start(); }

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 and $rights != 6 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( $_REQUEST['referer'] == '' ) {
	$referer = basename($_SERVER['HTTP_REFERER']);
} elseif ( $_POST['referer'] != '' ) {
	$referer = $_POST['referer'];
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( isset($_REQUEST['VendorID']) ) {
	$VendorID = $_REQUEST['VendorID'];
}

if ( isset($_REQUEST['ProductNumberInternal']) ) {
	$ProductNumberInternal = $_REQUEST['ProductNumberInternal'];
}

if ( isset($_REQUEST['VendorProductCode']) ) {
	$VendorProductCode = base64_decode($_REQUEST['VendorProductCode']);
}

if ( isset($_GET['VendorProductCode']) ) {
	$NewVendorProductCode = base64_decode($_GET['VendorProductCode']);
} else {
	$NewVendorProductCode = $_POST['NewVendorProductCode'];
}

if ( isset($_REQUEST['add_prod']) ) {
	$add_tier = $_REQUEST['add_prod'];
}



include('inc_global.php');
print_r($_REQUEST);
if ( !empty($_POST) ) {

	// check_field() FUNCTION IN global.php
	check_field($VendorID, 1, 'Vendor');
	check_field($ProductNumberInternal, 1, 'Product Number Internal');
	check_field($NewVendorProductCode, 1, 'Vendor Product Code');

	$pos1 = strpos($NewVendorProductCode, "'");
	$pos2 = strpos($NewVendorProductCode, '"');
	if ( $pos1 !== false or $pos2 !== false ) {
		$error_found = true;
		$error_message .= "'Vendor Product Code' cannot contain an apostrophe or quote<BR>";
	}

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$ProductNumberInternal = escape_data($ProductNumberInternal);
		$VendorProductCode = escape_data($VendorProductCode);

		if ( $add_tier == 0 ) {
			$sql = "UPDATE vendorproductcodes " .
			" SET VendorID = '" . $VendorID . "'," .
			" ProductNumberInternal = '" . $ProductNumberInternal . "'," .
			" VendorProductCode = '" . $NewVendorProductCode . "'" .
			" WHERE VendorID = " . $VendorID . " AND ProductNumberInternal = '" . $ProductNumberInternal . "' AND VendorProductCode = '" . $VendorProductCode . "'";
	//		echo "<br />$sql<br />";
	//			exit();
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		else {
			$sql = "INSERT INTO vendorproductcodes (VendorID, ProductNumberInternal, VendorProductCode) VALUES ('" . $VendorID . "', '" . $ProductNumberInternal . "', '" . $NewVendorProductCode . "')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		$_SESSION['note'] = "Vendor information successfully saved<BR>";

		$base_page = explode("?", $referer);
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		if ( $base_page[0] == "data_vendors.edit.php" or $base_page[0] == "vendors_vendors.edit.php" ) {
			echo "window.opener.location.reload()\n";
		} elseif ( $base_page[0] == "flavors_materials_pricing.php" ) {
			//echo "window.opener.document.forms['search'].submit()\n";
			echo "window.opener.location.reload()\n";
		} else {
			echo "window.opener.location.reload()\n";
		}
		echo "window.close()\n";
		echo "</SCRIPT>\n";

	}

}

include("inc_pop_header.php");


if ( $add_tier == 0 ) {
	$form_field = "READONLY";
} else {
	$form_field = "";
}

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
<FORM METHOD="post" ACTION="pop_edit_material_vendor.php">
<INPUT TYPE="hidden" NAME="VendorID" VALUE="<?php echo $VendorID;?>">
<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>">
<INPUT TYPE="hidden" NAME="VendorProductCode" VALUE="<?php echo base64_encode($VendorProductCode);?>">
<INPUT TYPE="hidden" NAME="add_prod" VALUE="<?php echo $add_prod;?>">
<INPUT TYPE="hidden" NAME="referer" VALUE="<?php echo $referer;?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD><B CLASS="black">Vendor:</B></TD>
		<TD><?php
		$sql = "SELECT vendor_id, name FROM vendors ORDER BY name";
		$result = mysql_query($sql, $link);
		if ( mysql_num_rows($result) > 0 ) { ?> 
			<SELECT NAME="VendorID">
				<?php if ( $add_tier != 0 ) { ?>
					<OPTION VALUE=""></OPTION>
				<?php }
				while ( $row = mysql_fetch_array($result) ) {
					if ( $VendorID == $row['vendor_id'] ) {
						echo "<OPTION VALUE='" . $row['vendor_id'] . "' SELECTED>" . $row['name'] . "</OPTION>";
					} elseif ( $add_tier != 0 ) {
						echo "<OPTION VALUE='" . $row['vendor_id'] . "'>" . $row['name'] . "</OPTION>";
					}
				} ?>
			</SELECT>
		<?php }
		else {
			echo "<I>None available</I>";
		}
		?></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Product Number Internal:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE='text' NAME="ProductNumberInternal" SIZE=26 VALUE="<?php echo stripslashes($ProductNumberInternal);?>" <?php echo $form_field;?>></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Vendor Product Code:</B></TD>
		<TD><INPUT TYPE='text' NAME="NewVendorProductCode" SIZE=26 VALUE="<?php echo stripslashes($NewVendorProductCode);?>" MAXLENGTH=50></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD></TR>

	<TR>
		<TD></TD>
		<TD><INPUT TYPE='submit' VALUE="Save"> <INPUT TYPE='button' VALUE="Cancel" onClick="window.close()"></TD>
	</TR></FORM>
</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><br/><br/>



<?php include("inc_footer.php"); ?>