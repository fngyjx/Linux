<?php

if ( $_REQUEST['referer'] == '' ) {
	$referer = basename($_SERVER['HTTP_REFERER']);
} elseif ( $_POST['referer'] != '' ) {
	$referer = $_POST['referer'];
}

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN HAS PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 and $rights != 6 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( isset($_REQUEST['VendorID']) ) {
	$VendorID = $_REQUEST['VendorID'];
}

if ( isset($_REQUEST['pni']) ) {
	$ProductNumberInternal = $_REQUEST['pni'];
}
$VendorName="";

if ( isset($_REQUEST['VendorName']) ) {
	$VendorName = $_REQUEST['VendorName'];
}

include('inc_global.php');



if ( !empty($_POST) ) {

	$vendor = $_POST['vendor'];
	$ProductNumberInternal = $_POST['ProductNumberInternal'];
	$VendorProductCode = $_POST['VendorProductCode'];
	$Tier = $_POST['Tier'];
	$PricePerPound = $_POST['PricePerPound'];

	$PriceEffectiveDate = $_POST['PriceEffectiveDate'];
	$date_parts = explode("/", $PriceEffectiveDate);
	$NewPriceEffectiveDate = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];

	if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
		if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
			$error_found=true;
			$error_message .= "Invalid (" . $PriceEffectiveDate . ") date entered<BR>";
		}
	} else {
		$error_found=true;
		$error_message .= "Invalid (" . $PriceEffectiveDate . ") date entered<BR>";
	}

	$Volume = $_POST['Volume'];
	$Minimums = $_POST['Minimums'];
	$Packaging = $_POST['Packaging'];
	$Notes = $_POST['Notes'];

	// check_field() FUNCTION IN global.php
	if ( $VendorID == '' and $vendor == '' ) {
		$error_found = true;
		$error_message .= "Please specify a vendor<BR>";
	}
	check_field($VendorProductCode, 1, 'Vendor Product Code');
	check_field($ProductNumberInternal, 1, 'Product Number Internal');
	check_field($PricePerPound, 1, 'Price Per lb');
	check_field($Tier, 1, 'Tier');

	if ( $VendorID != '' ) {
		$sql = "SELECT * FROM vendorproductcodes WHERE VendorID = " . $VendorID . " AND ProductNumberInternal = '" . $ProductNumberInternal . "' AND VendorProductCode = '" . $VendorProductCode . "'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
		if ( $c > 0 ) {
			$error_found = true;
			$error_message .= "Vendor Product Code is already in database<BR>";
		}
	}

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$VendorID = escape_data($VendorID);
		$ProductNumberInternal = escape_data($ProductNumberInternal);
		$VendorProductCode = escape_data($VendorProductCode);
		$Tier = escape_data($Tier);
		$PricePerPound = escape_data($PricePerPound);
		$NewPriceEffectiveDate = escape_data($NewPriceEffectiveDate);
		$Volume = escape_data($Volume);
		$Minimums = escape_data($Minimums);
		$Packaging = escape_data($Packaging);
		$Notes = escape_data($Notes);

		//if ( $add_tier != 1 ) {
		//	$sql = "UPDATE productprices " .
		//	" SET PricePerPound = '" . $PricePerPound . "'," .
		//	" PriceEffectiveDate = '" . $PriceEffectiveDate . "'," .
		//	" Volume = '" . $Volume . "'," .
		//	" Minimums = '" . $Minimums . "'," .
		//	" Packaging = '" . $Packaging . "'," .
		//	" Notes = '" . $Notes . "'" .
		//	" WHERE VendorID = " . $VendorID . " AND ProductNumberInternal = '" . $ProductNumberInternal . "' AND Tier = '" . $Tier . "'";
		//	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//}
		//else {
			$sql = "INSERT INTO vendorproductcodes (VendorID, VendorProductCode, ProductNumberInternal) VALUES ('" . $VendorID . "', '" . $VendorProductCode . "', '" . $ProductNumberInternal . "')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$sql = "INSERT INTO productprices (Tier, VendorID, ProductNumberInternal, PricePerPound, PriceEffectiveDate, Volume, Minimums, Packaging, DateQuoted, Notes) VALUES ('" . $Tier . "', '" . $VendorID . "', '" . $ProductNumberInternal . "', '" . $PricePerPound . "', '" . $NewPriceEffectiveDate . "', '" . $Volume . "', '" . $Minimums . "', '" . $Packaging . "', '" . date("Y-m-d H:i:s") . "', '" . $Notes . "')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//}

		$_SESSION['note'] = "Product information successfully saved<BR>";

		$base_page = explode("?", $referer);
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		if ( $base_page[0] == "flavors_materials_pricing.php" ) {
			echo "window.opener.document.forms[0].submit()\n";
		} else {
			echo "window.opener.location.reload()\n";
		}
		echo "window.close()\n";
		echo "</SCRIPT>\n";

	}

} else {
	$PriceEffectiveDate = date("Y-m-d");
}



$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

include("inc_pop_header.php");

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
<FORM NAME="popper_form" METHOD="post" ACTION="pop_add_product_vendor.php">
<INPUT TYPE="hidden" NAME="referer" VALUE="<?php echo $referer;?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">


	<?php if ( $VendorID == '' or $vendor != '' ) { 
		$internalnumber_readonly="";
	?>

		<TR>
			<TD><B CLASS="black">Vendor:</B></TD>
			<TD><INPUT TYPE="text" ID="vendor" NAME="vendor" SIZE=26 VALUE="<?php echo stripslashes($vendor);?>">
			<INPUT TYPE="hidden" ID="VendorID" NAME="VendorID" VALUE="<?php echo stripslashes($VendorID);?>"></TD>
		</TR>

		<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<?php } else { 
		if ( isset($_REQUEST['pni']) )
			$internalnumber_readonly="READONLY='readonly'";
	?>
		<TR>
			<TD><B CLASS="black">Vendor:</B></TD>
			<TD><?php echo stripslashes($VendorName);?>
		</TR>
		<INPUT TYPE="hidden" NAME="VendorID" VALUE="<?php echo $VendorID;?>">

	<?php } ?>


	<TR>
		<TD><B CLASS="black">Vendor Product Code:</B></TD>
		<TD><INPUT TYPE='text' id="VendorProductCode" NAME="VendorProductCode" SIZE=26 VALUE="<?php echo stripslashes($VendorProductCode);?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Product search:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE='text' id="product_search" NAME="product_search" SIZE=26 VALUE="" <?php echo $internalnumber_readonly;?>></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Product Number Internal:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE='text' id="ProductNumberInternal" NAME="ProductNumberInternal" SIZE=26 READONLY="readonly" VALUE="<?php echo $ProductNumberInternal ?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>
	<TR>
		<TD><B CLASS="black">Product Designation:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE='text' id="ProductDesignation" NAME="ProductDesignation" SIZE=26 READONLY="readonly" VALUE="<?php echo $ProductDesignation ?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Tier:</B></TD>
		<TD><INPUT TYPE='text' id="Tier" NAME="Tier" SIZE=5 VALUE="<?php echo stripslashes($Tier);?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Price Per Pound:</B></TD>
		<TD><INPUT TYPE='text' id="PricePerPound" NAME="PricePerPound" SIZE=26 VALUE="<?php echo stripslashes($PricePerPound);?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Price Effective Date:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE="text" SIZE="26" NAME="PriceEffectiveDate" id="datepicker1" VALUE="<?php
		if ( $PriceEffectiveDate != '' ) {
			echo date("m/d/Y", strtotime($PriceEffectiveDate));
		}
		?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Volume:</B></TD>
		<TD><INPUT TYPE='text' NAME="Volume" SIZE=26 VALUE="<?php echo stripslashes($Volume);?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Minimums:</B></TD>
		<TD><INPUT TYPE='text' NAME="Minimums" SIZE=26 VALUE="<?php echo stripslashes($Minimums);?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Packaging:</B></TD>
		<TD><INPUT TYPE='text' NAME="Packaging" SIZE=26 VALUE="<?php echo stripslashes($Packaging);?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Notes:</B></TD>
		<TD><TEXTAREA NAME="Notes" ROWS="3" COLS="22"><?php echo stripslashes($Notes);?></TEXTAREA></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD></TR>

	<TR>
		<TD></TD>
		<TD><INPUT TYPE='submit' id="save" name="save" VALUE="Save"> <INPUT TYPE='submit' id="cancel" name="cancel" VALUE="Cancel"></TD>
	</TR></FORM>
</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>


<script type="text/javascript">

$(document).ready(function(){
	
	$(":submit").click(function() {
		$("#action").val(this.name);
		switch (this.name)
		{
			case 'cancel':
				window.close();
				break;
			case 'save':
				alertMessage = validated();
				if ("" != alertMessage )
				{ 
					alert(alertMessage);
					return false;
				}
				break;
			default:
				//alert ("this button not yet supported");
				break;
		}
	});
	
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

	$("#product_search").autocomplete("search/raw_internal_product_numbers.php", {
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		width: 350,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350,
		limit:100
	});
	$("#product_search").result(function(event, data, formatted) {
		if (data)
			$("#ProductNumberInternal").val(data[1]);
			$("#ProductDesignation").val(data[2]);
	});
	$("#product_search").change(function() {
		if (""==$("#product_search").val()) {
			$("#ProductNumberInternal").val('');
			$("#ProductDesignation").val('');
		}
	});
});
function validated() {
	// verify all fields have a value that need one;
	var alertMessage = "";
	if ("" == $("#vendor").val()) {
		alertMessage+="Vendor is a required Field\n";
		$("#vendor").attr("style", "border: solid 1px red");
	} else {
		$("#vendor").attr("style", "border: none");
	}
	if ("" == $("#VendorProductCode").val()) {
		alertMessage+="Vendor Product Code is a required Field\n";
		$("#VendorProductCode").attr("style", "border: solid 1px red");
	} else {
		$("#VendorProductCode").attr("style", "border: none");
	}
	if ("" == $("#ProductNumberInternal").val()) {
		alertMessage+="Product Number is a required Field\n";
		$("#product_search").attr("style", "border: solid 1px red");
		$("#ProductNumberInternal").attr("style", "border: solid 1px red");
		$("#ProductDesignation").attr("style", "border: solid 1px red");
	} else {
		$("#product_search").attr("style", "border: none");
		$("#ProductNumberInternal").attr("style", "border: none");
		$("#ProductDesignation").attr("style", "border: none");
	}
	if ("" == $("#Tier").val()) {
		alertMessage+="Tier is a required Field\n";
		$("#Tier").attr("style", "border: solid 1px red");
	} else {
		$("#Tier").attr("style", "border: none");
	}
	if ("" == $("#PricePerPound").val()) {
		alertMessage+="Price Per Pound is a required Field\n";
		$("#PricePerPound").attr("style", "border: solid 1px red");
	} else {
		$("#PricePerPound").attr("style", "border: none");
	}
	return alertMessage;
}

</script>



<script type="text/javascript">
$(function() {
	$('#datepicker1').datepicker({
		changeMonth: true,
		changeYear: true
	});
});
</script>



<?php include("inc_footer.php"); ?>