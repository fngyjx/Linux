<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and QC HAVE PERMISSIONS
// FRONT DESK CAN SEE SOME REPORTS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 ) {
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


include("inc_header.php");

if ( !empty($_POST) and !empty($_POST['print']) ) {
	if ( $_POST['report_type'] == 'm' ) {
		$url = "reports/regulatory_reports_msds.php?pni=" . $_POST['pni'];
	} elseif ( $_POST['report_type'] == 'c' ) {
	} elseif ( $_POST['report_type'] == 's' ) {
	} elseif ( $_POST['report_type'] == 'n' ) {
		$url = "reports/regulatory_reports_nutritionals.php?pni=" . $_POST['pni'];
	} elseif ( $_POST['report_type'] == 'k' ) {
		$url = "reports/regulatory_reports_kosher_formula.php?pni=" . $_POST['pni'];
	} elseif ( $_POST['report_type'] == 'o' ) {
		$url = "reports/regulatory_reports_organic_formula.php?pni=" . $_POST['pni'];
	} elseif ( $_POST['report_type'] == 'a' ) {
	}
	
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "popup('" . $url . "', 800, 700)\n";
	echo "</SCRIPT>\n";

}
/*
//onChange="printer_popup('reports/print_formula.php?pni=<?php echo $row['ProductNumberInternal'];

//					<OPTION VALUE="m">MSDS</OPTION>
//					<OPTION VALUE="c">Certificate of Analysis</OPTION>
//					<OPTION VALUE="s">Specifications</OPTION>
//					<OPTION VALUE="n">Nutritionals</OPTION>
//					<OPTION VALUE="k">Kosher</OPTION>
//					<OPTION VALUE="o">Organic</OPTION>
//					<OPTION VALUE="a">Allergens</OPTION>


//$report_initials = array("m","c","s","n","k","o","a");
//$report_types = array("MSDS", "Certificate of Analysis", "Specifications", "Nutritionals", "Kosher", "Organic", "Allergens");
*/

if ( $rights == 4 ) {
	$report_initials = array("k","o");
	$report_types = array("Kosher", "Organic");
} else {
	$report_initials = array("m","n","k","o");
	$report_types = array("MSDS", "Nutritionals", "Kosher", "Organic");
}

?>



<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>

<table class="bounding">
<tr valign="top">
<td class="padded">
<FORM ACTION="flavors_regulatory_reports.php" METHOD="post">
<INPUT TYPE="hidden" NAME="action" VALUE="search">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR>
			<TD><B>Abelei number (external):</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="external_number_search" NAME="ProductNumberExternal" VALUE="<?php echo str_replace("'","",$ProductNumberExternal);?>" SIZE="30"></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
	</tr>
	<tr>
		<TD COLSPAN="4"><INPUT style="float:right" TYPE="submit" class="submit_medium" VALUE="Search"></TD>
	</TR>
	<tr>
		<td colspan="4">
			<button style="margin:10px 0 10px 0" onClick="popup('reports/regulatory_reports_all_kosher_formulas.php')" CLASS="submit_normal" >Print All Kosher Formulas</button><br/>
			<button onClick="popup('reports/regulatory_reports_all_organic_formulas.php')" CLASS="submit_normal" >Print All Organic Formulas</button>
		</TD>
	</TR>
</TABLE>
</FORM>

</TD></TR></TABLE>
<BR><BR>



<?php

if ( $_REQUEST['action'] == 'search' ) {

	if ( $ProductNumberExternal != '' ) {
		$pne_clause = " AND externalproductnumberreference.ProductNumberExternal LIKE '%" . str_replace("'","",$ProductNumberExternal) . "%'";
	}

	$sql = "SELECT productmaster.ProductNumberInternal, productmaster.Designation, externalproductnumberreference.ProductNumberExternal
	FROM externalproductnumberreference LEFT JOIN productmaster USING(ProductNumberInternal)
	WHERE 1=1 " . $pne_clause . " ORDER BY externalproductnumberreference.ProductNumberExternal, productmaster.ProductNumberInternal";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	$bg = 0; 
	if ( $c > 0 ) { ?>
		
		<TABLE BORDER=0 CELLSPACING="0" CELLPADDING="3">

			<TR VALIGN=BOTTOM>
				<TD><B>abelei#</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="3" HEIGHT="1"></TD>
				<TD><B>Internal#</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="3" HEIGHT="1"></TD>
				<TD><B>Description</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="3" HEIGHT="1"></TD>
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
			<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=MIDDLE>
			<FORM ACTION="flavors_regulatory_reports.php" METHOD="post">
			<INPUT TYPE="hidden" NAME="action" VALUE="search">
			<INPUT TYPE="hidden" NAME="print" VALUE="1">
			<INPUT TYPE="hidden" NAME="ProductNumberExternal" VALUE="<?php echo $row['ProductNumberExternal'];?>">
				<TD><?php echo $row['ProductNumberExternal'];?></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="3" HEIGHT="1"></TD>
				<TD><?php echo $row['ProductNumberInternal'];?></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="3" HEIGHT="1"></TD>
				<TD><?php echo $row['Designation'];?></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="3" HEIGHT="1"></TD>
				<TD><SELECT NAME="report_type" CLASS="select">
				<?php
				$i = 0;
				foreach ( $report_types as $value ) {
					if ( $_POST['report_type'] == $report_initials[$i] ) {
						echo "<OPTION VALUE='" . $report_initials[$i] . "' SELECTED>" . $value . "</OPTION>";
					} else {
						echo "<OPTION VALUE='" . $report_initials[$i] . "'>" . $value . "</OPTION>";
					}
					$i++;
				}
				?>
				</SELECT>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="3" HEIGHT="1"></TD>
				<TD><INPUT TYPE="submit" VALUE="Print report" CLASS="submit"></TD>
				
				<INPUT TYPE="hidden" NAME="pni" VALUE="<?php echo $row['ProductNumberInternal'];?>"></TD>
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
	var width  = 720;
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