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

$form_status = "";
if ( $_REQUEST['action'] != 'edit' ) {
	$form_status = "readonly=\"readonly\"";
}

if ( $_REQUEST['action'] != '' ) {
	$action = $_REQUEST['action'];
} else {
	$action = "";
}





if ( !empty($_GET) and $_REQUEST['referrer'] != '' ) {

	$sql = "SELECT IngredientProductNumber, IngredientSEQ, productprices.PricePerPound
	FROM pricesheetmaster
	LEFT JOIN pricesheetdetail USING(PriceSheetNumber)
	LEFT JOIN productprices ON pricesheetdetail.IngredientProductNumber = productprices.ProductNumberInternal
	AND pricesheetdetail.VendorID = productprices.VendorID
	AND pricesheetdetail.Tier = productprices.Tier
	WHERE pricesheetmaster.ProductNumberInternal = " . $_GET['pni'] . " AND PriceSheetNumber = " . $_REQUEST['psn'] . "
	ORDER BY IngredientSEQ";
	//echo $sql . "<BR>";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	while ( $row = mysql_fetch_array($result) ) {

		if ( is_numeric($row['PricePerPound']) ) {
			$price = "'" . number_format($row['PricePerPound'], 2) . "'";
		} else {
			$price = "NULL";
		}

		$sql = "UPDATE pricesheetdetail SET Price = " . str_replace(",", "", $price) . " WHERE PriceSheetNumber = " . $_REQUEST['psn'] . " AND IngredientProductNumber = '" . $row['IngredientProductNumber'] . "' AND IngredientSEQ = '" . $row['IngredientSEQ'] . "'";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//echo $sql . "<BR>";
	}

	header("location: " . $_GET['referrer'] . "?psn=" . $psn);
	exit();

}

			

if ( !empty($_POST) and $_REQUEST['referrer'] != '' ) {
	$locked = $_POST['locked'];
	if ( $locked == 1 ) {
		$locked = 0;
	} else {
		$locked = 1;
	}
	if ( !$error_found ) {
		$sql = "UPDATE pricesheetmaster SET locked = '" . $locked . "'"
		. " WHERE PriceSheetNumber = " . $psn;
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		header("location: " . $_POST['referrer'] . "?psn=" . $psn);
		exit();
	}
}



if ( !empty($_POST) ) {
	//echo print_r($_POST);
	//die();

	$SalesPersonEmployeeID = $_POST['SalesPersonEmployeeID'];
	$SpecificGravity = $_POST['SpecificGravity'];
	$Lbs_Per_Gallon = $_POST['Lbs_Per_Gallon'];
	$Priced_ByEmployeeID = $_POST['Priced_ByEmployeeID'];
	//$ProcessType = $_POST['ProcessType'];
	$Terms = $_POST['Terms'];
	$Packaged_In = $_POST['Packaged_In'];
	$MinBatch = $_POST['MinBatch'];
	$MinBatch_Units = $_POST['MinBatch_Units'];
	$FOBLocation = $_POST['FOBLocation'];
	$IncludePricePerGallonInQuote = $_POST['IncludePricePerGallonInQuote'];
	//$Notes = $_POST['Notes'];

	if ( $IncludePricePerGallonInQuote != 1 ) {
		$IncludePricePerGallonInQuote = 0;
	}

	$DatePriced = $_POST['DatePriced'];
	$date_parts = explode("/", $DatePriced);
	$NewDatePriced = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];

	if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
		if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
			$error_found=true;
			$error_message .= "Invalid (" . $DatePriced . ") date entered";
		}
	} else {
		$error_found=true;
		$error_message .= "Invalid (" . $DatePriced . ") date entered";
	}

	// check_field() FUNCTION IN global.php
	check_field($SalesPersonEmployeeID, 1, 'Salesperson');
	//check_field($SpecificGravity, 3, 'Specific Gravity');
	check_field($Priced_ByEmployeeID, 1, 'Priced by');
	check_field($MinBatch, 3, 'Min Order');

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$SalesPersonEmployeeID = escape_data($SalesPersonEmployeeID);
		//$SpecificGravity = escape_data($SpecificGravity);
		$Priced_ByEmployeeID = escape_data($Priced_ByEmployeeID);
		//$ProcessType = escape_data($ProcessType);
		$Terms = escape_data($Terms);
		$Packaged_In = escape_data($Packaged_In);
		$MinBatch = escape_data($MinBatch);
		$FOBLocation = escape_data($FOBLocation);
		//$Notes = escape_data($Notes);

		if ( $SpecificGravity != 0 ) {
			$Lbs_Per_Gallon = $SpecificGravity * 8.34;
		} else {
			$Lbs_Per_Gallon = 8.34;
		}

		$sql = "UPDATE pricesheetmaster SET "
		. " SalesPersonEmployeeID = '" . $SalesPersonEmployeeID . "', "
		. " IncludePricePerGallonInQuote = '" . $IncludePricePerGallonInQuote . "', "
		. " Lbs_Per_Gallon = '" . $Lbs_Per_Gallon . "', "
		. " Priced_ByEmployeeID = '" . $Priced_ByEmployeeID . "', "
		//. " ProcessType = '" . $ProcessType . "', "
		. " Terms = '" . $Terms . "', "
		. " Packaged_In = '" . $Packaged_In . "', "
		. " MinBatch = '" . $MinBatch . "', "
		. " MinBatch_Units = '" . $MinBatch_Units . "', "
		. " FOBLocation = '" . $FOBLocation . "', "
		. " DatePriced = '" . $NewDatePriced . "'"
		. " WHERE PriceSheetNumber = " . $psn;
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//$_SESSION['note'] = "Information successfully saved<BR>";
		if ( $_REQUEST['continue'] == "Save" ) {
			header("location: customers_quotes.header.php?action=edit&psn=" . $psn);
			exit();
		} else {
			header("location: customers_quotes.header.php?psn=" . $psn);
			exit();
		}
	}

} else {
	$sql = "SELECT pricesheetmaster.*, productmaster.SpecificGravity AS SpecificGravityMaster FROM pricesheetmaster LEFT JOIN productmaster USING (ProductNumberInternal) WHERE PriceSheetNumber = " . $psn;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$SalesPersonEmployeeID = $row['SalesPersonEmployeeID'];
	$Lbs_Per_Gallon = $row['Lbs_Per_Gallon'];
	$SpecificGravity = $row['SpecificGravityMaster'];
	$Priced_ByEmployeeID = $row['Priced_ByEmployeeID'];
	//$ProcessType = $row['ProcessType'];
	$Terms = $row['Terms'];
	$Packaged_In = $row['Packaged_In'];
	$MinBatch = number_format($row['MinBatch'], 2);
	$MinBatch_Units = $row['MinBatch_Units'];
	$FOBLocation = $row['FOBLocation'];
	$DatePriced = $row['DatePriced'];
	$IncludePricePerGallonInQuote = $row['IncludePricePerGallonInQuote'];
	//$Notes = $row['Notes'];
}



$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

$min_units = array("one, 5-Gallon pail", "100 pounds", "300 pounds");


//$process_types = array("L-ALC", "LIQUID", "PLATED", "RESALE", "SD+Blend", "SD+P", "SPRAY DRY");
//$packaging = array("0.5 Gallon Jug", "1 Gallon Jug", "100 Lb. Fiber Drums", "200 Lb. Fiber Drums", "25 Lb. Pail", "30 Lb. Boxes", "35 Lb. Boxes", "75 Lb. Drums", "5-Gallon Pail W/Reicke Spout", "5-Gallon Pails", "50 Lb. Boxes", "55 Lb. Poly-Lined Box", "55 Gallon Drums");

$process_types = array();
$sql = "SELECT tblsystemdefaultsdetail.ItemDescription 
FROM tblsystemdefaultsmaster
LEFT JOIN tblsystemdefaultsdetail
USING (ItemID) 
WHERE ItemID = 1";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$i = 0;
while ( $row = mysql_fetch_array($result) ) {
	$process_types[$i] = $row['ItemDescription'];
	$i++;
}

$packaging = array();
$sql = "SELECT tblsystemdefaultsdetail.ItemDescription 
FROM tblsystemdefaultsmaster
LEFT JOIN tblsystemdefaultsdetail
USING (ItemID) 
WHERE ItemID = 10";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$i = 0;
while ( $row = mysql_fetch_array($result) ) {
	$packaging[$i] = $row['ItemDescription'];
	$i++;
}

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


<script type="text/javascript">
$(function() {
	$('#datepicker').datepicker({
		changeMonth: true,
		changeYear: true
	});
});
</script>


<?php include("inc_quotes_header.php"); ?>


<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


<TABLE WIDTH=450 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="header.src=headerOver.src"
		onMouseOut="header.src=headerOver.src" 
		HREF="customers_quotes.header.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/header_over.gif" WIDTH=74 HEIGHT=18 BORDER=0 NAME="header"></A></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="rmc_management.src=rmc_managementOver.src"
		onMouseOut="rmc_management.src=rmc_managementOut.src" 
		HREF="customers_quotes.rmc_management.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/rmc_management_out.gif" WIDTH=143 HEIGHT=18 BORDER=0 NAME="rmc_management"></A></TD>
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

<FORM NAME="add_ingredient" ACTION="customers_quotes.header.php" METHOD="post">
<INPUT TYPE="hidden" NAME="psn" VALUE="<?php echo $psn;?>">
<INPUT TYPE="hidden" NAME="action" VALUE="<?php echo $action;?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
	<TR VALIGN=TOP>
		<TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
	<TR>
		<TD><B>Salesperson:</B></TD>
		<TD><SELECT NAME="SalesPersonEmployeeID" <?php echo $form_status;?>>
		<OPTION VALUE=""></OPTION>
		<?php
		$sql = "SELECT user_id, first_name, last_name FROM users WHERE user_type < 3";
		$result_sales = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		while ( $row_sales = mysql_fetch_array($result_sales) ) {
			if ( $SalesPersonEmployeeID == $row_sales['user_id'] ) {
				echo "<OPTION VALUE='" . $row_sales['user_id'] . "' SELECTED>" . $row_sales['first_name'] . ' ' . $row_sales['last_name'] . "</OPTION>";
			} else {
				echo "<OPTION VALUE='" . $row_sales['user_id'] . "'>" . $row_sales['first_name'] . ' ' . $row_sales['last_name'] . "</OPTION>";
			}
		}
		?></SELECT></TD>
		<TD>&nbsp;&nbsp;&nbsp;</TD>
		<TD><NOBR><B>Specific Gravity:</B></NOBR></TD>
		<TD><INPUT TYPE="text" NAME="SpecificGravity" VALUE="<?php echo number_format($SpecificGravity, 2);?>" SIZE=30 READONLY><INPUT NAME="Lbs_Per_Gallon" TYPE="hidden" VALUE="<?php echo $Lbs_Per_Gallon;?>"></TD>
	</TR>
	<TR>
		<TD><B>Priced by:</B></TD>
		<TD><SELECT NAME="Priced_ByEmployeeID" <?php echo $form_status;?>>
		<OPTION VALUE=""></OPTION>
		<?php
		$sql = "SELECT user_id, first_name, last_name FROM users WHERE user_type < 3";
		$result_priced = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		while ( $row_priced = mysql_fetch_array($result_priced) ) {
			if ( $Priced_ByEmployeeID == $row_priced['user_id'] ) {
				echo "<OPTION VALUE='" . $row_priced['user_id'] . "' SELECTED>" . $row_priced['first_name'] . ' ' . $row_priced['last_name'] . "</OPTION>";
			} else {
				echo "<OPTION VALUE='" . $row_priced['user_id'] . "'>" . $row_priced['first_name'] . ' ' . $row_priced['last_name'] . "</OPTION>";
			}
		}
		?></SELECT></TD>
		<TD>&nbsp;&nbsp;&nbsp;</TD>
		<TD><B>Packaged in:</B></TD>
		<TD><SELECT NAME="Packaged_In" <?php echo $form_status;?>>
		<OPTION VALUE=""></OPTION>
		<?php
		foreach ( $packaging as $value ) {
			if ( $value == $Packaged_In ) {
				echo "<OPTION VALUE='" . $value . "' SELECTED>" . $value . "</OPTION>";
			} else {
				echo "<OPTION VALUE='" . $value . "'>" . $value . "</OPTION>";
			}
		}
		?></SELECT></TD>
	</TR>
	<TR>
		<TD><B>Terms:</B></TD>
		<TD><INPUT TYPE="text" NAME="Terms" VALUE="<?php echo $Terms;?>" SIZE=30 <?php echo $form_status;?>></TD>
		<TD>&nbsp;&nbsp;&nbsp;</TD>
		<TD><B>FOB Location:</B></TD>
		<TD><INPUT TYPE="text" NAME="FOBLocation" VALUE="<?php echo $FOBLocation;?>" SIZE=30 <?php echo $form_status;?>></TD>
	</TR>
	<TR>
		<TD><B>Min Order:</B></TD>
		<TD><NOBR><INPUT TYPE="text" NAME="MinBatch" VALUE="<?php echo $MinBatch;?>" SIZE=14 <?php echo $form_status;?>> <!-- <I><?php //echo $MinBatch_Units;?></I> --> 
		<SELECT NAME="MinBatch_Units" <?php echo $form_status;?>>
		<?php
		foreach ( $min_units as $value ) {
			if ( $value == $MinBatch_Units ) {
				echo "<OPTION VALUE='" . $value . "' SELECTED>" . $value . "</OPTION>";
			} else {
				echo "<OPTION VALUE='" . $value . "'>" . $value . "</OPTION>";
			}
		}
		?></SELECT></NOBR>
		</TD>
		<TD>&nbsp;&nbsp;&nbsp;</TD>
		<TD><NOBR><B>Date Priced:</B>&nbsp;</NOBR></TD>
		<TD><INPUT TYPE="text" SIZE="12" NAME="DatePriced" id="datepicker" VALUE="<?php echo date("m/d/Y", strtotime($DatePriced));?>" <?php echo $form_status;?>></TD>
	</TR>
	<TR>
		<TD></TD>
		<TD>
		<?php if ( $IncludePricePerGallonInQuote == 1 ) { ?>
			<INPUT TYPE="checkbox" NAME="IncludePricePerGallonInQuote" VALUE="1" <?php echo $form_status;?> CHECKED> <B>Include Price Per Gal in Quote</B>
		<?php } else { ?>
			<INPUT TYPE="checkbox" NAME="IncludePricePerGallonInQuote" VALUE="1" <?php echo $form_status;?>> <B>Include Price Per Gal in Quote</B>
		<?php } ?>
		</TD>
		<TD>&nbsp;&nbsp;&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
	</TR>
	<!-- <TR VALIGN=TOP>
		<TD><B>Comments:</B></TD>
		<TD COLSPAN=4><TEXTAREA NAME="Notes" ROWS=4 COLS=28 <?php //echo $form_status;?>><?php //echo $Notes;?></TEXTAREA></TD>
	</TR> -->
	<TR>
		<TD></TD>
		<TD COLSPAN=4>
		<?php if ( $locked != 1 ) { ?>
			<?php if ( $form_status != '' ) { ?>
				<INPUT TYPE="button" VALUE="Edit" CLASS="submit" onClick="window.location='customers_quotes.header.php?action=edit&psn=<?php echo $psn;?>'">
			<?php } else { ?>
				<INPUT TYPE="submit" NAME="continue" VALUE="Save" CLASS="submit"> <INPUT TYPE="submit" VALUE="Save and done" CLASS="submit"> <INPUT TYPE="button" VALUE="Cancel" CLASS="submit" onClick="window.location='customers_quotes.header.php?psn=<?php echo $psn;?>'">
			<?php } ?>
		<?php } ?>
		</TD>
	</TR>
</TABLE>

		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE></FORM><RB>



<script>
var contacts="";
$(document).ready(function(){
	$(":input[readonly]").addClass("readOnly");
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");
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
</script>



<?php include("inc_footer.php"); ?>