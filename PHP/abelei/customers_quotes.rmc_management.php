<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN HAS PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( $_REQUEST['psn'] != '' ) {
	$psn = $_REQUEST['psn'];
} else {
	header ("Location: customers_quotes.php");
	exit;
}

include('inc_global.php');



if ( $_GET['action'] == "delete_tier" ) {
	$sql = "UPDATE productprices SET is_deleted = 1 WHERE VendorID = " . $_GET['VendorID'] . " AND ProductNumberInternal = '" . $_GET['ProductNumberInternal'] . "' AND Tier = '" . $_GET['Tier'] . "'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$_SESSION['note'] = "Pricing tier successfully deleted<BR>";
	header("location: customers_quotes.rmc_management.php?psn=" . $_GET['psn']);
	exit();
}



if ( $_REQUEST['action'] == 'add' and isset($_POST['add_quote']) ) {
	//echo print_r($_POST);
	//die();
	$ProductNumberInternal = $_POST['ProductNumberInternal'];
	$customer_id = $_POST['customer_id'];

	// check_field() FUNCTION IN global.php
	check_field($ProductNumberInternal, 1, 'Product Number Internal');
	check_field($customer_id, 1, 'Customer');

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$ProductNumberInternal = escape_data($ProductNumberInternal);
		$sql = "INSERT into pricesheetmaster (ProductNumberInternal, CustomerID, DatePriced) VALUES ('" . $ProductNumberInternal . "', '" . $customer_id . "', '" . date("Y-m-d") . "')";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//$_SESSION['note'] = "Information successfully saved<BR>";
		$psn = mysql_insert_id();
		header("location: customers_quotes.php?action=edit&psn=" . $psn);
		exit();
	}
}

$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

$sql = "SELECT pricesheetmaster.ProductNumberInternal, ProductNumberExternal, Designation, name, DatePriced FROM pricesheetmaster LEFT JOIN customers ON pricesheetmaster.CustomerID = customers.customer_id INNER JOIN externalproductnumberreference USING(ProductNumberInternal) INNER JOIN productmaster ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal WHERE PriceSheetNumber = " . $_REQUEST['psn'];
$result_header = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row_header = mysql_fetch_array($result_header);
$pni = $row_header['ProductNumberInternal'];

include("inc_header.php");

?>



<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

headerOut = new Image
headerOut.src = "images/tabs/quoting_flavors/header_out.gif"
headerOver = new Image
headerOver.src = "images/tabs/quoting_flavors/header_over.gif"

rmc_managementOut = new Image
rmc_managementOut.src = "images/tabs/quoting_flavors/rmc_management_out.gif"
rmc_managementOver = new Image
rmc_managementOver.src = "images/tabs/quoting_flavors/rmc_management_over.gif"

rmc_configurationOut = new Image
rmc_configurationOut.src = "images/tabs/quoting_flavors/rmc_configuration_out.gif"
rmc_configurationOver = new Image
rmc_configurationOver.src = "images/tabs/quoting_flavors/rmc_configuration_over.gif"

pricingOut = new Image
pricingOut.src = "images/tabs/quoting_flavors/pricing_out.gif"
pricingOver = new Image
pricingOver.src = "images/tabs/quoting_flavors/pricing_over.gif"

commentsOut = new Image
commentsOut.src = "images/tabs/quoting_flavors/comments_out.gif"
commentsOver = new Image
commentsOver.src = "images/tabs/quoting_flavors/comments_over.gif"

 // End -->
</SCRIPT>



<?php include("inc_quotes_header.php"); ?>

<!-- <IFRAME SRC="inc_quotes_header.php?psn=<?php //echo $psn;?>" WIDTH="900" HEIGHT="70" FRAMEBORDER="0"></IFRAME><BR> -->



<TABLE WIDTH=450 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="header.src=headerOver.src"
		onMouseOut="header.src=headerOut.src" 
		HREF="customers_quotes.header.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/header_out.gif" WIDTH=74 HEIGHT=18 BORDER=0 NAME="header"></A></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="rmc_management.src=rmc_managementOver.src"
		onMouseOut="rmc_management.src=rmc_managementOver.src" 
		HREF="customers_quotes.rmc_management.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/rmc_management_over.gif" WIDTH=143 HEIGHT=18 BORDER=0 NAME="rmc_management"></A></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="rmc_configuration.src=rmc_configurationOver.src"
		onMouseOut="rmc_configuration.src=rmc_configurationOut.src" 
		HREF="customers_quotes.rmc_configuration.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/rmc_configuration_out.gif" WIDTH=156 HEIGHT=18 BORDER=0 NAME="rmc_configuration"></A></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="pricing.src=pricingOver.src"
		onMouseOut="pricing.src=pricingOut.src" 
		HREF="customers_quotes.pricing.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/pricing_out.gif" WIDTH=77 HEIGHT=18 BORDER=0 NAME="pricing"></A></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="comments.src=commentsOver.src"
		onMouseOut="comments.src=commentsOut.src" 
		HREF="customers_quotes.comments.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/comments_out.gif" WIDTH=98 HEIGHT=18 BORDER=0 NAME="comments"></A></TD>
		<TD><IMG SRC="images/tabs/blank.gif" WIDTH="152" HEIGHT="18" ALT="Blank"></TD>
	</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
	<TR>
		<TD BACKGROUND="images/tabs/tab_rule.gif"><IMG SRC="images/tabs/tab_rule.gif" WIDTH="100%" HEIGHT="8"></TD>
	</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BGCOLOR="#976AC2" WIDTH="100%"><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="5" BGCOLOR="whitesmoke" WIDTH="100%"><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="10" BGCOLOR="whitesmoke" ALIGN=CENTER WIDTH="100%"><TR><TD>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
	<TR VALIGN=TOP>
		<TD>

<?php



$sql_outer = "SELECT IngredientProductNumber FROM formulationdetail LEFT JOIN productmaster ON formulationdetail.IngredientProductNumber = productmaster.ProductNumberInternal WHERE formulationdetail.ProductNumberInternal = " . $pni;
$result_outer = mysql_query($sql_outer, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$c = 0;
	$x = 0;
	$header_shown = "";
	$vendor_code_shown = "";
	$VendorID_shown = $row['VendorID'];
	$outer_loop_write = 0;

	while ( $row_outer = mysql_fetch_array($result_outer) ) {

		$ProductNumberInternal_clause = " AND vwMaterialPricing.ProductNumberInternal = " . $row_outer['IngredientProductNumber'];

		$sql = "SELECT vwMaterialPricing.*, productprices.is_deleted
		FROM vwMaterialPricing
		LEFT JOIN productprices
		ON vwMaterialPricing.VendorID = productprices.VendorID AND vwMaterialPricing.ProductNumberInternal = productprices.ProductNumberInternal AND vwMaterialPricing.Tier = productprices.Tier
		WHERE 1=1 " . $ProductNumberInternal_clause;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//echo $sql;

		if ( mysql_num_rows($result) > 0 ) {

			?>
			
			<?php
			include("inc_materials_pricing.php");


		} else {
			if ( $c == 1 ) {
				echo "</TABLE></TD></TR></TABLE><BR>";
			}
			$c = 0;
			$outer_loop_write = 1;
			$sql_sub = "SELECT ProductNumberInternal, Designation, Natural_OR_Artificial, Kosher FROM productmaster WHERE ProductNumberInternal = " . $row_outer['IngredientProductNumber'];
			$result_sub = mysql_query($sql_sub, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			if ( mysql_num_rows($result_sub) > 0 ) {
				$row_sub = mysql_fetch_array($result_sub);
				?>

				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" BGCOLOR="#976AC2" WIDTH="100%">
					<TR>
						<TD><B CLASS="white">Internal Product#: <?php echo $row_sub['ProductNumberInternal'];?>
						&nbsp;&nbsp;&nbsp;
						Designation: <?php echo $row_sub['Designation'];?>
						&nbsp;&nbsp;&nbsp;
						Nat/Art: <?php echo $row_sub['Natural_OR_Artificial'];?>
						&nbsp;&nbsp;&nbsp;
						Kosher: <?php echo $row_sub['Kosher'];?></B></TD>
					</TR>
				</TABLE><BR>

				<FORM><INPUT TYPE="button" VALUE="Add vendor" onClick="popup('pop_add_product_vendor.php?pni=<?php echo $row_sub['ProductNumberInternal'];?>')" CLASS="submit"></FORM><BR>

			<?php
			}
		}

	}


?>

		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><br/><br/>









<script>
	var contacts="";
	$(document).ready(function(){
	
	$("#customer").autocomplete("search/customers_by_name.php", {
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		width: 350,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#customer").result(function(event, data, formatted) {
		if (data)
			document.getElementById("customer_id").value = data[1];
			contacts=search('search/contacts_by_customer_id',data[1]);
			update_id('update/contacts_by_customer_id','contactspan',data[1]);
	});
	$("#ProductNumberInternal").autocomplete("search/internal_product_numbers.php", {
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		width: 350,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#designation").autocomplete("search/designations.php", {
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		width: 350,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$(":submit").click(function() {
	});
});



function delete_tier(vid, pni, tier, psn) {
	if ( confirm('Are you sure you want to delete this pricing tier?') ) {
		document.location.href = "customers_quotes.rmc_management.php?action=delete_tier&VendorID=" + vid + "&ProductNumberInternal=" + pni + "&Tier=" + tier + "&psn=" + psn
	}
}

</script>



<?php include("inc_footer.php"); ?>