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
		onMouseOut="rmc_management.src=rmc_managementOut.src" 
		HREF="customers_quotes.rmc_management.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/rmc_management_out.gif" WIDTH=143 HEIGHT=18 BORDER=0 NAME="rmc_management"></A></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="rmc_configuration.src=rmc_configurationOver.src"
		onMouseOut="rmc_configuration.src=rmc_configurationOver.src" 
		HREF="customers_quotes.rmc_configuration.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/rmc_configuration_over.gif" WIDTH=156 HEIGHT=18 BORDER=0 NAME="rmc_configuration"></A></TD>
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

$sql = "SELECT pricesheetdetail.*, productmaster.Designation, Natural_OR_Artificial, productmaster.ProductType, productmaster.Kosher, productprices.Volume, productprices.Minimums, productprices.DateQuoted, vendors.name AS vendor, vendors.vendor_id
FROM pricesheetmaster
LEFT JOIN pricesheetdetail
USING (PriceSheetNumber) 
LEFT JOIN productprices ON pricesheetdetail.IngredientProductNumber = productprices.ProductNumberInternal
AND pricesheetdetail.VendorID = productprices.VendorID
AND pricesheetdetail.Tier = productprices.Tier
LEFT JOIN productmaster ON productmaster.ProductNumberInternal = pricesheetdetail.IngredientProductNumber
LEFT JOIN vendors ON vendors.vendor_id = pricesheetdetail.VendorID
WHERE PriceSheetNumber = " . $psn . " ORDER BY IngredientSEQ";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$c = mysql_num_rows($result);
if ( $c > 0 ) {
	$bg = 0; ?>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">

		<TR>
			<TD COLSPAN=14 HEIGHT="28" VALIGN=TOP><B CLASS="black">Vendor/Tier Configuration</B></TD>
		</TR>

		<TR>
			<TD><B>Seq#</B></TD>
			<TD><B>Internal#</B></TD>
			<TD><B>Ingedient</B></TD>
			<TD ALIGN=RIGHT><B>%</B></TD>
			<TD><B>Vendor</B></TD>
			<TD ALIGN=CENTER><B>Tier</B></TD>
			<TD ALIGN=RIGHT><B>$/lb</B></TD>
			<TD ALIGN=RIGHT><NOBR><B>Ext. $</B></NOBR></TD>
			<TD><NOBR>&nbsp;&nbsp;<B>Vol</B></NOBR></TD>
			<TD><B>Min</B></TD>
			<TD><B>Effective</B></TD>
			<TD><B>Quoted</B></TD>
			<TD></TD>
		</TR>

	<?php 

	$total_extended_cost = 0;
	while ( $row = mysql_fetch_array($result) ) {
		if ( $bg == 1 ) {
			$bgcolor = "#F3E7FD";
			$bg = 0;
		} else {
			$bgcolor = "whitesmoke";
			$bg = 1;
		}

		$description = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");

		if ( $locked == 1 ) {
			$price_per_pound = $row['Price'];
		} else {
			if ( $row['vendor_id'] != '' ) {
				$sql = "SELECT PricePerPound FROM productprices WHERE ProductNumberInternal = '" . $row['IngredientProductNumber'] . "' AND VendorID = " . $row['vendor_id'] . " AND Tier = '" . $row['Tier'] . "'";
				$result_price = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$row_price = mysql_fetch_array($result_price);
				$price_per_pound = $row_price['PricePerPound'];
			} else {
				$price_per_pound = 0;
			}
		}

		if ( $_REQUEST['action'] != 'edit' ) {
			$form_status = "readonly=\"readonly\"";
		} elseif ( $_REQUEST['ipn'] == $row['IngredientProductNumber'] and $_REQUEST['seq'] == $row['IngredientSEQ'] ) {
			$form_status = "";
		} else {
			$form_status = "readonly=\"readonly\"";
		}

		if ( $row['PriceEffectiveDate'] != '' ) {
			$date_split = explode(" ", $row['PriceEffectiveDate']);
			$date_parts = explode("-", $date_split[0]);
			$start_month = $date_parts[1];
			$start_day = $date_parts[2];
			$start_year = $date_parts[0];
		}

		?>

		<FORM ACTION="customers_quotes.rmc_configuration.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="psn" VALUE="<?php echo $psn;?>">
		<INPUT TYPE="hidden" NAME="action" VALUE="edit">

		<TR BGCOLOR="<?php echo $bgcolor ?>">
			<TD STYLE="font-size:8pt">
			<?php echo $row['IngredientSEQ'];?>
			<INPUT TYPE="hidden" NAME="IngredientSEQ" VALUE="<?php echo $row['IngredientSEQ'];?>">
			</TD>
			<TD STYLE="font-size:8pt">
			<?php echo $row['IngredientProductNumber'];?>
			<INPUT TYPE="hidden" NAME="IngredientProductNumber" VALUE="<?php echo $row['IngredientProductNumber'];?>">
			</TD>
			<?php 
			$pne = '';
			if ( '2' == substr($row[IngredientProductNumber],0,1) ) {
				$sql = "SELECT ProductNumberExternal FROM externalproductnumberreference WHERE ProductNumberInternal = '$row[IngredientProductNumber]' LIMIT 1";
				$result_pne = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				$pne = " (abelei#: " . mysql_result($result_pne,0,0) . ")";
			} else {
				$pne = '';
			}
			?>
			<TD STYLE="font-size:8pt"><NOBR><?php echo $description . $pne;?></NOBR></TD>
			<TD STYLE="font-size:8pt" ALIGN=RIGHT><?php echo number_format($row['Percentage'], 2);?></TD>
			<TD STYLE="font-size:8pt">
			<?php if ( $_REQUEST['action'] != 'edit' ) { ?>
				<NOBR><?php echo $row['vendor'];?></NOBR>
			<?php //} elseif ( $_REQUEST['ipn'] == $row['IngredientProductNumber'] and $_REQUEST['seq'] == $row['IngredientSEQ'] ) { ?>
				<!-- <INPUT TYPE="text" ID="vendor" NAME="vendor" SIZE=16 VALUE="<?php //echo $row['vendor'];?>" <?php //echo $form_status;?>><INPUT TYPE="hidden" ID="VendorID" NAME="VendorID" VALUE="<?php //echo $VendorID;?>"> -->
			<?php } else { ?>
				<NOBR><?php echo $row['vendor'];?></NOBR>
			<?php } ?>
			</TD>

			<TD ALIGN=CENTER><?php echo $row['Tier'];?>
			<!-- <INPUT TYPE="text" NAME="Tier" VALUE="<?php //echo $row['Tier'];?>" SIZE=2 <?php //echo $form_status;?>> -->
			</TD>

			<TD ALIGN=RIGHT><?php echo number_format($price_per_pound, 2);?>
			<!-- <INPUT TYPE="text" NAME="Price" VALUE="<?php //echo number_format($price_per_pound, 2);?>" SIZE=7 <?php //echo $form_status;?> STYLE="text-align:right"> -->
			</TD>

			<TD ALIGN=RIGHT>
			<?php
			$price = $price_per_pound * $row['Percentage'];
			echo number_format($price/100, 2);
			$total_extended_cost = $total_extended_cost + $price;
			?>
			</TD>

			<TD><NOBR>&nbsp;&nbsp;<?php echo $row['Volume'];?></NOBR>
			<!-- <INPUT TYPE="text" NAME="Volume" VALUE="<?php //echo $row['Volume'];?>" SIZE=7 <?php //echo $form_status;?>> -->
			</TD>

			<TD><INPUT TYPE="text" NAME="Minimums" VALUE="<?php echo $row['Minimums'];?>" SIZE=3 <?php echo $form_status;?>></TD>
			<TD STYLE="font-size:8pt">

			<?php if ( $_REQUEST['action'] != 'edit' ) {
				if ( $row['PriceEffectiveDate'] != '' ) {
					echo date("n/j/Y", strtotime($row['PriceEffectiveDate']));
				} else {
					echo "<I>Unspecified</I>";
				}
			} elseif ( $_REQUEST['ipn'] == $row['IngredientProductNumber'] and $_REQUEST['seq'] == $row['IngredientSEQ'] ) { ?>
				<NOBR><SELECT NAME="start_month">
					<?php foreach ( $months as $value ) {
						if ( $start_month == $value ) { ?>
							<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $value?></OPTION>
						<?php } else { ?>
							<OPTION VALUE="<?php echo $value?>"><?php echo $value?></OPTION>
						<?php }
					} ?>
				</SELECT><SELECT NAME="start_day">
					<?php foreach ( $days as $value ) {
						if ( $start_day == $value ) { ?>
							<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $value?></OPTION>
						<?php } else { ?>
							<OPTION VALUE="<?php echo $value?>"><?php echo $value?></OPTION>
						<?php }
					} ?>
				</SELECT><SELECT NAME="start_year">
					<?php for ( $n = date("Y") - 1; $n <= date("Y") + 3; $n++ ) {
						if ( $start_year == $n ) {
							$start_year_found = true; ?>
							<OPTION VALUE="<?php echo $n?>" SELECTED><?php echo $n?></OPTION>
						<?php } elseif ( date("Y") == $n and !$start_year_found ) { ?>
							<OPTION VALUE="<?php echo $n?>" SELECTED><?php echo $n?></OPTION>
						<?php } else { ?>
							<OPTION VALUE="<?php echo $n?>"><?php echo $n?></OPTION>
						<?php }
					} ?>
				</SELECT></NOBR>
			<?php } else {
				if ( $row['PriceEffectiveDate'] != '' ) {
					echo date("n/j/Y", strtotime($row['PriceEffectiveDate']));
				} else {
					echo "<I>Unspecified</I>";
				}
			} ?>

			</TD>
			<TD STYLE="font-size:8pt"><?php
			if ( $row['DateQuoted'] != '' ) {
				echo date("n/j/Y", strtotime($row['DateQuoted']));
			} else {
				echo "<I>Unspecified</I>";
			}
			?></TD>
			<TD STYLE="font-size:8pt">
			<?php if ( $locked != 1 ) { ?>
				<INPUT TYPE="button" VALUE="Select" onClick="popup('pop_select_tier.php?seq=<?php echo $row['IngredientSEQ'];?>&ipn=<?php echo $row['IngredientProductNumber'];?>&psn=<?php echo $psn;?>')" CLASS="submit">
			<?php } ?>
			</TD>
		</TR>
		</FORM>

	<?php } ?>

		<TR>
			<TD COLSPAN=7 ALIGN=RIGHT><B>Total:</B></TD>
			<TD ALIGN=RIGHT><B>$<?php echo number_format($total_extended_cost/100, 2);?></B></TD>
			<TD COLSPAN=5>&nbsp;</TD>
		</TR>

	</TABLE>

<?php } else {
	echo "No matches found in database<BR><BR>";
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
	$(":input[readonly]").addClass("readOnly");
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");
	$("#vendor").autocomplete("search/vendors.php", {
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		width: 350,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#vendor").result(function(event, data, formatted) {
		if (data)
			document.getElementById("VendorID").value = data[1];
	});
	$(":submit").click(function() {
	});
});
</script>



<?php include("inc_footer.php"); ?>