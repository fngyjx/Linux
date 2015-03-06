<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 and $rights != 6) {
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
<FORM ACTION="flavors_materials_pricing.php" METHOD="post" id="search" NAME="search">
<INPUT TYPE="hidden" id="action" NAME="action" VALUE="search">
	
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD><B>Vendor:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="vendor_name" NAME="vendor_name" VALUE="<?php echo $vendor_name;?>" SIZE="30"></TD>
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
		<TD><INPUT TYPE="text" id="ProductNumberInternal" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	
	<TR>
		<TD><B>Designation:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="Designation" NAME="Designation" VALUE="<?php echo $Designation;?>" SIZE="30"><INPUT TYPE="hidden" NAME="parent_url" VALUE="flavors_materials_pricing.php"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="12"></TD>
	</TR>

	<TR>
		<TD><INPUT type="button" value="Add Vendor Product" onClick="popup('pop_add_product_vendor.php', 400, 600, 60)"></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT style="float:right" VALUE="Search" TYPE="submit" CLASS="submit_medium" ></TD>
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
		$ProductNumberInternal_clause = " AND vwmaterialpricing.ProductNumberInternal LIKE '%" . escape_data($ProductNumberInternal) . "%'";
	} else {
		$ProductNumberInternal_clause = "";
	}
	if ( $Designation != '' ) {
		$Designation_clause = " AND Designation LIKE '%" . escape_data($Designation) . "%'";
	} else {
		$Designation_clause = "";
	}

	$sql = "SELECT vwmaterialpricing.*, productprices.is_deleted
	FROM vwmaterialpricing
	LEFT JOIN productprices
	ON vwmaterialpricing.VendorID = productprices.VendorID AND vwmaterialpricing.ProductNumberInternal = productprices.ProductNumberInternal AND vwmaterialpricing.Tier = productprices.Tier
	WHERE 1=1" . $vendor_name_clause . $VendorProductCode_clause . $ProductNumberInternal_clause . $Designation_clause;

	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql;
	$pitem=0;
	?>
	<FORM action="update_material_pricing.php" method="post" target="_blanck"> 
	<?php
	include("inc_materials_pricing.php");
	?>
	<input type="hidden" name="pitem" value="<?php echo $pitem-1;?>">
	<input type="submit" id="submit_price_change" name="submmit_price_change" value="submit" class="submit" style="visibility:hidden">
	<input type="reset" id="cancel_price_change" name="cancel_price_change" value="cancel" class="submit" style="visibility:hidden" onClick="window.location.reload();">
	</FORM>
	<?php

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

$(document).ready(function(){

	$("#Designation").autocomplete("search/flavor_material_pricing_by_designation.php", {
		cacheLength: 1,
		width: 650,
		max:50,
		scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#Designation").result(function(event, data, formatted) {
		if (data)
			$("#vendor_name").val('');
			$("#Designation").val(data[0]);
			$("#ProductNumberInternal").val(data[1]);
			$("#action").val('search');
			document.search.submit();
	});
	
	$("#vendor_name").autocomplete("search/flavor_material_pricing_by_vendor_name.php", {
		cacheLength: 1,
		width: 650,
		max:50,
		scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#vendor_name").result(function(event, data, formatted) {
		if (data)
			$("#Designation").val('');
			$("#ProductNumberInternal").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
	$("#ProductNumberInternal").autocomplete("search/flavor_materials_pricing_by_internal_number.php", {
		cacheLength: 1,
		width: 650,
		max:50,
		scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#ProductNumberInternal").result(function(event, data, formatted) {
		if (data)
			$("#Designation").val('');
			$("#vendor_name").val('');
			$("#action").val('search');
			document.search.submit();
	});
})

 // End -->
</SCRIPT>


<?php include("inc_footer.php"); ?>