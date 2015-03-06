<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}


if ( $_GET['action'] == "delete_tier" ) {
	$sql = "UPDATE productprices SET is_deleted = 1 WHERE VendorID = " . $_GET['VendorID'] . " AND ProductNumberInternal = '" . $_GET['ProductNumberInternal'] . "' AND Tier = '" . $_GET['Tier'] . "'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$_SESSION['note'] = "Pricing tier successfully deleted<BR>";
	header("location: flavors_materials_pricing.php?action=search&ProductNumberInternal=" . $_GET['ProductNumberInternal']);
	exit();
}


if ( $_GET['action'] == "delete_prod" ) {
	$sql = "UPDATE productprices SET is_deleted = 1 WHERE VendorID = " . $_GET['VendorID'] . " AND ProductNumberInternal = '" . $_GET['ProductNumberInternal'] . "'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";
	$sql = "DELETE FROM vendorproductcodes WHERE VendorID = " . $_GET['VendorID'] . " AND ProductNumberInternal = '" . $_GET['ProductNumberInternal'] . "' AND VendorProductCode = '" . $_GET['VendorProductCode'] . "'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";
	//die();
	$_SESSION['note'] = "Product successfully deleted<BR>";
	header("location: flavors_materials_pricing.php?action=search&ProductNumberInternal=" . $_GET['ProductNumberInternal']);
	exit();
}


if ( isset($_REQUEST['vendor_name']) and $_REQUEST['action'] == 'search' ) {
	$vendor_name = $_REQUEST['vendor_name'];
}
if ( isset($_REQUEST['VendorProductCode']) and $_REQUEST['action'] == 'search' ) {
	$VendorProductCode = $_REQUEST['VendorProductCode'];
}
if ( isset($_REQUEST['ProductNumberInternal']) and $_REQUEST['action'] == 'search' ) {
	$ProductNumberInternal = $_REQUEST['ProductNumberInternal'];
}
if ( isset($_REQUEST['Designation']) and $_REQUEST['action'] == 'search' ) {
	$Designation = $_REQUEST['Designation'];
}


include("inc_header.php");

?>



<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>



<!-- 
<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>
 -->

<TABLE class="bounding">
<TR VALIGN=TOP>
<TD class="padded">
<FORM ACTION="flavors_materials_pricing.php" METHOD="post" NAME="pricing">
<INPUT TYPE="hidden" NAME="action" VALUE="search">
	
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD><B>Vendor:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" NAME="vendor_name" VALUE="<?php echo $vendor_name;?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>

	<TR>
		<TD><B>Vendor Product Code:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" NAME="VendorProductCode" VALUE="<?php echo $VendorProductCode;?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>

	<TR>
		<TD><B>Material number (internal):</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	
	<TR>
		<TD><B>Designation:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" NAME="Designation" VALUE="<?php echo $Designation;?>" SIZE="30"><INPUT TYPE="hidden" NAME="parent_url" VALUE="flavors_materials_pricing.php"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="12"></TD>
	</TR>

	<TR>
		<TD colspan="3"><INPUT style="float:right" TYPE="submit" VALUE="Search" CLASS="submit_medium" NAME="submitter"></TD>
	</TR>
</TABLE>
</FORM>
</TD></TR></TABLE><BR><BR>

<!-- 
		</TD>
		<TD ALIGN=RIGHT><INPUT TYPE="button" VALUE="New Formula" CLASS="submit" onClick="window.location='flavors_formulations.php'"></TD>
	</TR>
</TABLE>
 -->



<?php

if ( $_REQUEST['action'] == 'search' ) {

	if ( $vendor_name != '' ) {
		$vendor_name_clause = " AND vendor_name LIKE '%" . escape_data($vendor_name) . "%'";
	} else {
		$vendor_name_clause = "";
	}
	if ( $VendorProductCode != '' ) {
		$VendorProductCode_clause = " AND VendorProductCode LIKE '%" . escape_data($VendorProductCode) . "%'";
	} else {
		$VendorProductCode_clause = "";
	}
	if ( $ProductNumberInternal != '' ) {
		$ProductNumberInternal_clause = " AND vwMaterialPricing.ProductNumberInternal LIKE '%" . escape_data($ProductNumberInternal) . "%'";
	} else {
		$ProductNumberInternal_clause = "";
	}
	if ( $Designation != '' ) {
		$Designation_clause = " AND Designation LIKE '%" . escape_data($Designation) . "%'";
	} else {
		$Designation_clause = "";
	}

	$sql = "SELECT vwMaterialPricing.*, productprices.is_deleted
	FROM vwMaterialPricing
	LEFT JOIN productprices
	ON vwMaterialPricing.VendorID = productprices.VendorID AND vwMaterialPricing.ProductNumberInternal = productprices.ProductNumberInternal AND vwMaterialPricing.Tier = productprices.Tier
	WHERE 1=1" . $vendor_name_clause . $VendorProductCode_clause . $ProductNumberInternal_clause . $Designation_clause;

	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql;

	include("inc_materials_pricing.php");

}

?>


</TABLE><BR><BR>



<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide
 
function delete_tier(vid, pni, tier) {
	if ( confirm('Are you sure you want to delete this pricing tier?') ) {
		document.location.href = "flavors_materials_pricing.php?action=delete_tier&VendorID=" + vid + "&ProductNumberInternal=" + pni + "&Tier=" + tier
	}
}

function delete_prod(vid, pni, vpc) {
	if ( confirm('Are you sure you want to delete this poduct?') ) {
		document.location.href = "flavors_materials_pricing.php?action=delete_prod&VendorID=" + vid + "&ProductNumberInternal=" + pni + "&VendorProductCode=" + vpc
	}
}

 // End -->
</SCRIPT>


<?php include("inc_footer.php"); ?>