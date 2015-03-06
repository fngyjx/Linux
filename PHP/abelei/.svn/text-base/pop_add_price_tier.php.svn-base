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

// ADMIN, LAB and FRONT DESK and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 ) {
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

if ( isset($_REQUEST['ProductNumberInternal']) ) {
	$ProductNumberInternal = $_REQUEST['ProductNumberInternal'];
}

if ( isset($_REQUEST['Tier']) and $_REQUEST['Tier'] != '' ) {
	$Tier = $_REQUEST['Tier'];
}

if ( isset($_REQUEST['add_tier']) and $_REQUEST['add_tier'] != '' ) {
	$add_tier = $_REQUEST['add_tier'];
}

include('inc_global.php');

if ( !empty($_POST) ) {

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
	if ( $Tier != "" ) {
		check_field($Tier, 1, 'Tier');
	} else {
		$error_found=true;
		$error_message.="Teir required<br/>";
	}
	check_field($PricePerPound, 3, 'Price Per lb');

	$pos1 = strpos($Tier, "'");
	$pos2 = strpos($Tier, '"');
	if ( $pos1 !== false or $pos2 !== false ) {
		$error_found = true;
		$error_message .= "'Tier' cannot contain an apostrophe or quote<BR>";
	}

	if ( $add_tier == 1 and ($pos1 === false or $pos2 === false) and ""!=$Tier ) {
		$sql = "SELECT Tier FROM productprices WHERE VendorID = " . escape_data($VendorID) . " AND ProductNumberInternal = '" . escape_data($ProductNumberInternal) . "' AND Tier = '" . escape_data($Tier) . "'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
		if ( $c > 0 ) {
			$error_found = true;
			$error_message .= "Tier entered is already in database<BR>";
		}
	}

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$VendorID = escape_data($VendorID);
		$ProductNumberInternal = escape_data($ProductNumberInternal);
		$Tier = escape_data($Tier);
		$PricePerPound = escape_data($PricePerPound);
		$PriceEffectiveDate = escape_data($PriceEffectiveDate);
		$Volume = escape_data($Volume);
		$Minimums = escape_data($Minimums);
		$Packaging = escape_data($Packaging);
		$Notes = escape_data($Notes);

		if ( $add_tier != 1 ) {
			$sql = "UPDATE productprices " .
			" SET PricePerPound = '" . $PricePerPound . "'," .
			" PriceEffectiveDate = '" . $NewPriceEffectiveDate . "'," .
			" Volume = '" . $Volume . "'," .
			" Minimums = '" . $Minimums . "'," .
			" Packaging = '" . $Packaging . "'," .
			" Notes = '" . $Notes . "'" .
			" WHERE VendorID = " . $VendorID . " AND ProductNumberInternal = '" . $ProductNumberInternal . "' AND Tier = '" . $Tier . "'";
			//die("$sql");
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		else {
			$sql = "INSERT INTO productprices (Tier, VendorID, ProductNumberInternal, PricePerPound, PriceEffectiveDate, Volume, Minimums, Packaging, DateQuoted, Notes) VALUES ('" . $Tier . "', '" . $VendorID . "', '" . $ProductNumberInternal . "', '" . $PricePerPound . "', '" . $NewPriceEffectiveDate . "', '" . $Volume . "', '" . $Minimums . "', '" . $Packaging . "', '" . date("Y-m-d H:i:s") . "', '" . $Notes . "')";
			//die("$sql");
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		$_SESSION['note'] = "Pricing information successfully saved<BR>";

		$base_page = explode("?", $referer);

		?>

		<!-- IE FIX FOR flavors_materials_pricing.php PAGE NOT REFRESHING -->
		<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>
			info = navigator.userAgent;
			if ( (info.indexOf('Explorer') >= 0 || info.indexOf('MSIE') >= 0) ) {
				ie = 1;
			} else {
				ie = 0;
			}
			if ( ie == 1 && ( opener.document.forms['pricing'].parent_url.value == 'flavors_materials_pricing.php' ) ) {
				opener.document.forms['pricing'].submit();
				window.close();
			} else if ( ie == 1 ) {
				window.opener.location.reload();
				window.close();
			}
		</SCRIPT>


		<?php

 		if ( $base_page[0] == "flavors_materials_pricing.php" or $base_page[0] == "vendors_vendors.edit.php" ) {
			echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
			echo "opener.document.forms['pricing'].submit();\n";
			echo "window.close();\n";
			echo "</SCRIPT>\n";
		} elseif ( $base_page[0] == "data_vendors.edit.php" or $base_page[0] = "customers_quotes.rmc_management.php" ) {
			echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
			echo "window.opener.location.reload()\n";
			echo "window.close();\n";
			echo "</SCRIPT>\n";
		}

	}

}

else {
	if ( $Tier != '' ) {
		$sql = "SELECT * FROM productprices WHERE VendorID = " . $VendorID . " AND ProductNumberInternal = '" . $ProductNumberInternal . "' AND Tier = '" . $Tier . "'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		$PricePerPound = $row['PricePerPound'];
		$PriceEffectiveDate = date("m/d/Y", strtotime($row['PriceEffectiveDate']));
		$Volume = $row['Volume'];
		$Minimums = $row['Minimums'];
		$Packaging = $row['Packaging'];
		$Notes = $row['Notes'];
	}
	else {
		$PricePerPound = '';
		$PriceEffectiveDate = date("m/d/Y");
		$Volume = '';
		$Minimums = '';
		$Packaging = '';
		$Notes = '';
	}
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



<script type="text/javascript">
$(function() {
	$('#datepicker').datepicker({
		changeMonth: true,
		changeYear: true
	});
});
</script>



<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
<FORM NAME="popper_form" METHOD="post" ACTION="pop_add_price_tier.php">
<INPUT TYPE="hidden" NAME="VendorID" VALUE="<?php echo $VendorID;?>">
<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>">
<INPUT TYPE="hidden" NAME="add_tier" VALUE="<?php echo $add_tier;?>">
<INPUT TYPE="hidden" NAME="referer" VALUE="<?php echo $referer;?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<?php if ( $Tier == '' or $error_found ) { ?>

		<TR>
			<TD><B CLASS="black">Tier:</B></TD>
			<TD><INPUT TYPE='text' NAME="Tier" SIZE=5 VALUE="<?php echo stripslashes($Tier);?>" MAXLENGTH=1></TD>
		</TR>

		<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<?php } else { ?>
		<INPUT TYPE="hidden" NAME="Tier" VALUE="<?php echo $Tier;?>" MAXLENGTH=1>
	<?php } ?>

	<TR>
		<TD><B CLASS="black">Price Per Pound:</B></TD>
		<TD><INPUT TYPE='text' NAME="PricePerPound" SIZE=26 VALUE="<?php echo stripslashes($PricePerPound);?>"></TD>
	</TR>

	<TR><TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>

	<TR>
		<TD><B CLASS="black">Price Effective Date:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE="text" SIZE="12" NAME="PriceEffectiveDate" id="datepicker" VALUE="<?php echo date("m/d/Y", strtotime($PriceEffectiveDate));?>"></TD>
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
		<TD><INPUT TYPE='submit' VALUE="Save"> <INPUT TYPE='button' VALUE="Cancel" onClick="window.close()"></TD>
	</TR></FORM>
</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><br/><br/>



<?php include("inc_footer.php"); ?>