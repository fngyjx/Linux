<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN and LAB HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');


if ( isset($_REQUEST['ProductNumberExternal']) and $_REQUEST['action'] == 'search' ) {
	$ProductNumberExternal = $_REQUEST['ProductNumberExternal'];
}
if ( isset($_REQUEST['ProductNumberInternal']) and $_REQUEST['action'] == 'search' ) {
	$ProductNumberInternal = $_REQUEST['ProductNumberInternal'];
}


include("inc_header.php");

?>



<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>

<table class="bounding">
<tr valign="top">
<td class="padded">
<FORM ACTION="flavors_qc_formulas.php" METHOD="post">
<INPUT TYPE="hidden" NAME="action" VALUE="search">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD><B>Abelei number (external):</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="external_number_search" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>

	<TR>
		<TD><B>Material number (internal):</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="internal_number_search" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="12"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><INPUT style="float:right" TYPE="submit" class="submit_medium" VALUE="Search"></TD>
	</TR>
</TABLE></FORM>

</td></tr></table>
<BR><BR>



<?php

if ( $_REQUEST['action'] == 'search' ) {

	if ( $ProductNumberExternal != '' ) {
		$pne_clause = " AND externalproductnumberreference.ProductNumberExternal LIKE '%" . $ProductNumberExternal . "%'";
	}
	if ( $ProductNumberInternal != '' ) {
		$pni_clause = " AND productmaster.ProductNumberInternal LIKE '%" . $ProductNumberInternal . "%'";
	}

	$sql = "SELECT productmaster.ProductNumberInternal, productmaster.Designation, externalproductnumberreference.ProductNumberExternal
	FROM externalproductnumberreference LEFT JOIN productmaster USING(ProductNumberInternal)
	WHERE 1=1 " . $pne_clause . $pni_clause . " ORDER BY externalproductnumberreference.ProductNumberExternal, productmaster.ProductNumberInternal";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	$bg = 0; 
	if ( $c > 0 ) { ?>
		
		<TABLE BORDER=0 CELLSPACING="0" CELLPADDING="3">
			<TR VALIGN=BOTTOM>
				<TD><B>abelei#</B></TD>
				<TD><B>Internal#</B></TD>
				<TD><B>Description</B></TD>
				<TD></TD>
				<TD></TD>
				<TD></TD>
			</TR>

		<?php 
		$total = 0;
		while ( $row = mysql_fetch_array($result) ) {
			if ( $bg == 1 ) {
				$bgcolor = "#F3E7FD";
				$bg = 0;
			} else {
				$bgcolor = "whitesmoke";
				$bg = 1;
			}
			?>
			<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP><FORM>
				<TD><?php echo $row['ProductNumberExternal'];?></TD>
				<TD><?php echo $row['ProductNumberInternal'];?></TD>
				<TD><?php echo $row['Designation'];?></TD>
				<TD><INPUT TYPE="button" VALUE="Formula" onClick="printer_popup('reports/print_formula.php?pni=<?php echo $row['ProductNumberInternal'];?>')" CLASS="submit"></TD>
				<TD><INPUT TYPE="button" VALUE="Formula w/ Vendors" onClick="printer_popup('reports/print_formula_vendor.php?pni=<?php echo $row['ProductNumberInternal'];?>')" CLASS="submit"></TD>
				<TD><INPUT TYPE="button" VALUE="Kosher w/ Vendors" onClick="printer_popup('reports/regulatory_reports_kosher_formula.php?pni=<?php echo $row['ProductNumberInternal'];?>')" CLASS="submit"></TD>
				<TD><INPUT TYPE="button" VALUE="Organic w/ Vendors" onClick="printer_popup('reports/regulatory_reports_organic_formula.php?pni=<?php echo $row['ProductNumberInternal'];?>')" CLASS="submit"></TD>
			</TR></FORM>
		<?php } ?>

		</TABLE><BR>
	<?php
	} else {
		echo "No matches found";
	}

}

?>



<script LANGUAGE=JAVASCRIPT>
 <!-- Hide
 
 function printer_popup(url) {
	var width  = 820;
	var height = 700;
	var left   = (screen.width  - width)/2;
	var top    = ((screen.height - height)/2) + 50;
	var params = 'width='+width+', height='+height;
	params += ', top='+top+', left='+left;
	params += ', directories=no';
	params += ', location=no';
	params += ', menubar=no';
	params += ', resizable=yes';
	params += ', scrollbars=yes';
	params += ', status=no';
	params += ', toolbar=no';
	newwin=window.open(url,'windowname5', params);
	if (window.focus) {
		newwin.focus()
	}
	return false;
}

$(document).ready(function(){
	
//	$(":submit").click(function() {
//		$("#action").val(this.name);
//		switch (this.name)
//		{
//			case 'New':
//				popup('pop_add_product.php',800,900);
//				return false;
//				break;
//			case '':
//				break;
//			default:
//				//alert ("this button not yet supported");
//				break;
//		}
//	});

//	$("#designation_search").autocomplete("search/product_master_formulas_by_designation.php", {
//		matchContains: true,
//		mustMatch: false,
//		minChars: 0,
//		width: 350,
//		max:1000,
//		multipleSeparator: "¬",
//		scrollheight: 350
//	});
//	$("#designation_search").result(function(event, data, formatted) {
//		if (data)
//			$("#external_number_search").val('');
//			$("#internal_number_search").val('');
//			$("#keyword_search").val('');
//			$("#action").val('search');
//			document.search.submit();
//	});
	
	$("#external_number_search").autocomplete("search/product_master_formulas_by_external_number.php", {
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350
	});
		$("#external_number_search").result(function(event, data, formatted) {
		if (data)
			$("#designation_search").val('');
			$("#internal_number_search").val('');
			$("#keyword_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
	$("#internal_number_search").autocomplete("search/product_master_formulas_by_internal_number.php", {
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#internal_number_search").result(function(event, data, formatted) {
		if (data)
			$("#designation_search").val('');
			$("#external_number_search").val('');
			$("#keyword_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
//	$("#keyword_search").autocomplete("search/product_master_formulas_by_keyword.php", {
//		matchContains: true,
//		mustMatch: false,
//		minChars: 0,
//		width: 350,
//		max:1000,
//		multipleSeparator: "¬",
//		scrollheight: 350
//	});
//	$("#keyword_search").result(function(event, data, formatted) {
//		if (data)
//			$("#designation_search").val('');
//			$("#external_number_search").val('');
//			$("#internal_number_search").val('');
//			$("#action").val('search');
//			document.search.submit();
//	});
	
});

 // End -->
 
</script>





<?php include("inc_footer.php"); ?>