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



$pt_dropdown_numbers = array("0.40", "0.40", "0.40", "0.40", "2.00", "0.35", "0.30", "0.39", "3.10", "1.10", "0.00");
$pt_dropdown_text = array("Liquid - PG", "Liquid - Water", "Liquid - VegOil", "Liquid - MCT", "Liquid - Ethyl Alcohol", "Plated - Abelei", "Plated - Dutch", "Plated - Oxy", "Spray Dry - HBT", "Spray Dry - QIC", "Resale");



if ( !empty($_POST) ) {
	//echo print_r($_POST);
	//die();

//foreach (array_keys($_POST) as $key) { 
//	$$key = $_POST[$key];
//	print "$key is ${$key}<br />"; 
//}
//die();

	$SellingPrice = $_POST['SellingPrice'];
	$ProcessType = $_POST['ProcessType'];
	$Cost_In_Use = $_POST['Cost_In_Use'];
	$SprayDriedCost = $_POST['SprayDriedCostHidden'];
	$RibbonBlendingCost = $_POST['RibbonBlendingCostHidden'];
	$LiquidProcessingCost = $_POST['LiquidProcessingCostHidden'];
	$PackagingCost = $_POST['PackagingCostHidden'];
	$ShippingCost = $_POST['ShippingCostHidden'];
	$ManualAdjustment = $_POST['ManualAdjustment'];
	$ManualAdjustmentType = $_POST['ManualAdjustmentType'];
	$Packaged_In = $_POST['Packaged_In'];
	$MinBatch_Units = $_POST['MinBatch_Units'];

	// check_field() FUNCTION IN global.php
	check_field($SellingPrice, 3, 'Selling Price');
	check_field($ProcessType, 1, 'Process Type');
	check_field($Cost_In_Use, 3, 'Use Level');
	check_field($SprayDriedCost, 3, 'Spray Dried');
	check_field($RibbonBlendingCost, 3, 'Ribbon Blending');
	check_field($LiquidProcessingCost, 3, 'Liquid Processing');
	check_field($PackagingCost, 3, 'Packaging');
	check_field($ShippingCost, 3, 'Shipping');
	check_field($ManualAdjustment, 3, 'Adjustment');

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$SprayDriedCost = escape_data($SprayDriedCost);
		$RibbonBlendingCost = escape_data($RibbonBlendingCost);
		$LiquidProcessingCost = escape_data($LiquidProcessingCost);
		$PackagingCost = escape_data($PackagingCost);
		$ShippingCost = escape_data($ShippingCost);
		$SellingPrice = escape_data($SellingPrice);
		$ManualAdjustment = escape_data($ManualAdjustment);
		$Cost_In_Use = escape_data($Cost_In_Use);

		$sql = "UPDATE pricesheetmaster SET "
		. " SprayDriedCost = '" . $SprayDriedCost . "', "
		. " ProcessType = '" . $ProcessType . "', "
		. " RibbonBlendingCost = '" . $RibbonBlendingCost . "', "
		. " LiquidProcessingCost = '" . $LiquidProcessingCost . "', "
		. " PackagingCost = '" . $PackagingCost . "', "
		. " ShippingCost = '" . $ShippingCost . "', "
		. " SellingPrice = '" . $SellingPrice . "', "
		. " ManualAdjustment = '" . $ManualAdjustment . "', "
		. " Cost_In_Use = '" . $Cost_In_Use . "', "
		. " ManualAdjustmentType = '" . $ManualAdjustmentType . "', "
		. " Packaged_In = '" . $Packaged_In . "', "
		. " MinBatch_Units = '" . $MinBatch_Units . "'"
		. " WHERE PriceSheetNumber = " . $psn;
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//$_SESSION['note'] = "Information successfully saved<BR>";
		if ( $_POST['save_refresh'] ) {
			header("location: customers_quotes.pricing.php?action=edit&psn=" . $psn);
		} else {
			header("location: customers_quotes.pricing.php?psn=" . $psn);
		}
		exit();
	}

} else {
	$sql = "SELECT * FROM pricesheetmaster WHERE PriceSheetNumber = " . $psn;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);

	if ( $row['ProcessType'] == '' ) {
		$ProcessTypeCost = '0.40';
		$ProcessType = 'Liquid - PG';
	} else {
		$i = 0;
		foreach ( $pt_dropdown_text as $value ) {
			if ( $row['ProcessType'] == $value ) {
				$ProcessTypeCost = number_format($pt_dropdown_numbers[$i], 2);
				$i++;
			}
		}
		$ProcessType = $row['ProcessType'];
	}

	if ( $row['SprayDriedCost'] == '' ) {
		$SprayDriedCost = '1.67';
	} else {
		$SprayDriedCost = number_format($row['SprayDriedCost'], 2);
	}
	if ( $row['RibbonBlendingCost'] == '' ) {
		$RibbonBlendingCost = '0.50';
	} else {
		$RibbonBlendingCost = number_format($row['RibbonBlendingCost'], 2);
	}
	if ( $row['LiquidProcessingCost'] == '' ) {
		$LiquidProcessingCost = '0.40';
	} else {
		$LiquidProcessingCost = number_format($row['LiquidProcessingCost'], 2);
	}
	if ( $row['PackagingCost'] == '' ) {
		$PackagingCost = '0.10';
	} else {
		$PackagingCost = number_format($row['PackagingCost'], 2);
	}
	if ( $row['ShippingCost'] == '' ) {
		$ShippingCost = '0.05';
	} else {
		$ShippingCost = number_format($row['ShippingCost'], 2);
	}
	if ( $row['SellingPrice'] == '' ) {
		$SellingPrice = 0;
	} else {
		$SellingPrice = $row['SellingPrice'];
	}
	if ( $row['ManualAdjustment'] == '' ) {
		$ManualAdjustment = 0;
	} else {
		$ManualAdjustment = $row['ManualAdjustment'];
	}
	if ( $row['Cost_In_Use'] == '' ) {
		$Cost_In_Use = 0;
	} else {
		$Cost_In_Use = $row['Cost_In_Use'];
	}
	if ( $row['ManualAdjustmentType'] == '' ) {
		$ManualAdjustmentType = 'Spray Dry Loss';
	} else {
		$ManualAdjustmentType = $row['ManualAdjustmentType'];
	}

	$Packaged_In = $row['Packaged_In'];
	$MinBatch_Units = $row['MinBatch_Units'];

}

$form_status = "";
$field_size = 12;

if ( $_REQUEST['action'] != 'edit' ) {
	$form_status = "readonly=\"readonly\"";
	$field_size = 8;
}


// FOR USE IN PRICING FORM BELOW
if ( $_REQUEST['action'] == 'edit' ) {
	$combo_status = "CLASS='comboBox'";
} else {
	$combo_status = "";
}

$sd_dropdown_numbers = array("", "1.67", "1.55", "1.42");
$sd_dropdown_text = array("", "300 - 1,499", "1,500 - 3,499", "3,500+");

$rb_dropdown_numbers = array("", "1,000 or less", "1,001 - 1,999", "2,000 - 4,999", "5,000 - 9,999", "10,000+");
$rb_dropdown_text = array("", "0.50", "0.40", "0.28", "0.24", "0.20");

$lp_dropdown_numbers = array("", "0.40", "2.00");
$lp_dropdown_text = array("", "Non-Alc", "Alcohol");

$pk_dropdown_numbers = array("", "0.10");
//$pk_dropdown_text = array("", "0.10");

$sh_dropdown_numbers = array("", "0.05");
//$sh_dropdown_text = array("", "0.05");

include("inc_header.php");

?>

<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide
$(document).ready(function(){
	$(":input[readonly]").addClass("readOnly");
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");
});
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
		onMouseOut="rmc_configuration.src=rmc_configurationOut.src" 
		HREF="customers_quotes.rmc_configuration.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/rmc_configuration_out.gif" WIDTH=156 HEIGHT=18 BORDER=0 NAME="rmc_configuration"></A></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="pricing.src=pricingOver.src"
		onMouseOut="pricing.src=pricingOver.src" 
		HREF="customers_quotes.pricing.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/pricing_over.gif" WIDTH=77 HEIGHT=18 BORDER=0 NAME="pricing"></A></TD>
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





<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">

	<TR VALIGN=TOP>

		<TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BGCOLOR="#976AC2"><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="5" BGCOLOR="white"><TR><TD>
<FORM NAME="pricing" ACTION="customers_quotes.pricing.php" METHOD="post" onSubmit="return updateHiddenField(this)">
<INPUT TYPE="hidden" NAME="psn" VALUE="<?php echo $psn;?>">
<INPUT TYPE="hidden" NAME="action" VALUE="edit">
<INPUT TYPE="hidden" ID="Packaged_In" NAME="Packaged_In" VALUE="<?php echo $Packaged_In;?>">
<INPUT TYPE="hidden" ID="MinBatch_Units" NAME="MinBatch_Units" VALUE="<?php echo $MinBatch_Units;?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
	<TR>
		<TD ALIGN=RIGHT><B>Selling Price:</B></TD>
		<TD>$<INPUT TYPE="text" NAME="SellingPrice" VALUE="<?php echo number_format($SellingPrice, 2);?>" SIZE="<?php echo $field_size;?>" STYLE="text-align:left" <?php echo $form_status;?>></TD>
	</TR>

	<TR>
		<TD ALIGN=RIGHT><B>Process Type:</B></TD>
		<TD ALIGN=RIGHT><NOBR><SELECT NAME="ProcessType" <?php echo $form_status;?> onChange="SelectProcessType_updated(this.form.ProcessType)">
		<?php
		$c = 0;
		foreach ( $pt_dropdown_numbers as $value ) {
			if ( $pt_dropdown_text[$c] == $ProcessType ) {
				echo "<OPTION VALUE='" . $pt_dropdown_text[$c] . "' SELECTED>" . $pt_dropdown_text[$c] . " (" . $value . ")</OPTION>";
			} else {
				echo "<OPTION VALUE='" . $pt_dropdown_text[$c] . "'>" . $pt_dropdown_text[$c] . " (" . $value . ")</OPTION>";
			}
			$c++;
		}
		?>
		</SELECT></NOBR>
		</TD>
	</TR>

	<TR>
		<TD ALIGN=RIGHT><B>Use Level:</B></TD>
		<TD><SPAN STYLE="color:white">$</SPAN><INPUT TYPE="text" NAME="Cost_In_Use" VALUE="<?php echo number_format($Cost_In_Use, 2);?>" SIZE="<?php echo $field_size;?>" STYLE="text-align:left" <?php echo $form_status;?>>%</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><B>Spray Dried:</B></TD>
		<TD><NOBR>$<SELECT NAME="SprayDriedCost" <?php echo $combo_status;?> <?php echo $form_status;?> STYLE="width:155px">
		<?php
		$c = 0;
		$db_value_found = false;
		foreach ( $sd_dropdown_numbers as $value ) {
			if ( $_REQUEST['action'] == 'edit' ) {
				$select_value = $sd_dropdown_text[$c] . " (" . $value . ")";
			} else {
				$select_value = $value;
			}
			if ( $value == $SprayDriedCost ) {
				echo "<OPTION VALUE='" . $value . "' SELECTED>" . str_replace(" ()", "", $select_value) . "</OPTION>";
				$db_value_found = true;
			} else {
				echo "<OPTION VALUE='" . $value . "'>" . str_replace(" ()", "", $select_value) . "</OPTION>";
			}
			$c++;
		}
		if ( $db_value_found == false ) {
			echo "<OPTION VALUE='" . $SprayDriedCost . "' SELECTED>" . $SprayDriedCost . "</OPTION>";
		}
		?>
		</SELECT></NOBR>
		<INPUT TYPE="hidden" NAME="SprayDriedCostHidden" VALUE="">
		</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><B>Ribbon Blending:</B></TD>
		<TD><NOBR>$<SELECT NAME="RibbonBlendingCost" <?php echo $combo_status;?> <?php echo $form_status;?> STYLE="width:155px">
		<?php
		$c = 0;
		$db_value_found = false;
		foreach ( $rb_dropdown_numbers as $value ) {
			if ( $_REQUEST['action'] == 'edit' ) {
				$select_value = $rb_dropdown_text[$c] . " (" . $value . ")";
			} else {
				$select_value = $value;
			}
			if ( $value == $RibbonBlendingCost ) {
				echo "<OPTION VALUE='" . $value . "' SELECTED>" . str_replace(" ()", "", $select_value) . "</OPTION>";
				$db_value_found = true;
			} else {
				echo "<OPTION VALUE='" . $value . "'>" . str_replace(" ()", "", $select_value) . "</OPTION>";
			}
			$c++;
		}
		if ( $db_value_found == false ) {
			echo "<OPTION VALUE='" . $RibbonBlendingCost . "' SELECTED>" . $RibbonBlendingCost . "</OPTION>";
		}
		?>
		</SELECT></NOBR>
		<INPUT TYPE="hidden" NAME="RibbonBlendingCostHidden" VALUE="">
		</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><NOBR><B>Liquid Processing:</B></NOBR></TD>
		<TD><NOBR>$<SELECT NAME="LiquidProcessingCost" <?php echo $combo_status;?> <?php echo $form_status;?> STYLE="width:155px">
		<?php
		$c = 0;
		$db_value_found = false;
		foreach ( $lp_dropdown_numbers as $value ) {
			if ( $_REQUEST['action'] == 'edit' ) {
				$select_value = $lp_dropdown_text[$c] . " (" . $value . ")";
			} else {
				$select_value = $value;
			}
			if ( $value == $LiquidProcessingCost ) {
				echo "<OPTION VALUE='" . $value . "' SELECTED>" . str_replace(" ()", "", $select_value) . "</OPTION>";
				$db_value_found = true;
			} else {
				echo "<OPTION VALUE='" . $value . "'>" . str_replace(" ()", "", $select_value) . "</OPTION>";
			}
			$c++;
		}
		if ( $db_value_found == false ) {
			echo "<OPTION VALUE='" . $LiquidProcessingCost . "' SELECTED>" . $LiquidProcessingCost . "</OPTION>";
		}
		?>
		</SELECT></NOBR>
		<INPUT TYPE="hidden" NAME="LiquidProcessingCostHidden" VALUE="">
		</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><B>Packaging:</B></TD>
		<TD><NOBR>$<SELECT NAME="PackagingCost" <?php echo $combo_status;?> <?php echo $form_status;?> STYLE="width:155px">
		<?php
		$c = 0;
		$db_value_found = false;
		foreach ( $pk_dropdown_numbers as $value ) {
			//if ( $_REQUEST['action'] == 'edit' ) {
			//	$select_value = $pk_dropdown_text[$c]; /// . " (" . $value . ")";
			//} else {
				$select_value = $value;
			//}
			if ( $value == $PackagingCost ) {
				echo "<OPTION VALUE='" . $value . "' SELECTED>" . str_replace(" ()", "", $select_value) . "</OPTION>";
				$db_value_found = true;
			} else {
				echo "<OPTION VALUE='" . $value . "'>" . str_replace(" ()", "", $select_value) . "</OPTION>";
			}
			$c++;
		}
		if ( $db_value_found == false ) {
			echo "<OPTION VALUE='" . $PackagingCost . "' SELECTED>" . $PackagingCost . "</OPTION>";
		}
		?>
		</SELECT></NOBR>
		<INPUT TYPE="hidden" NAME="PackagingCostHidden" VALUE="">
		</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><B>Shipping:</B></TD>
		<TD><NOBR>$<SELECT NAME="ShippingCost" <?php echo $combo_status;?> <?php echo $form_status;?> STYLE="width:155px">
		<?php
		$c = 0;
		$db_value_found = false;
		foreach ( $sh_dropdown_numbers as $value ) {
			//if ( $_REQUEST['action'] == 'edit' ) {
			//	$select_value = $sh_dropdown_text[$c];   // . " (" . $value . ")";
			//} else {
				$select_value = $value;
			//}
			if ( $value == $ShippingCost ) {
				echo "<OPTION VALUE='" . $value . "' SELECTED>" . str_replace(" ()", "", $select_value) . "</OPTION>";
				$db_value_found = true;
			} else {
				echo "<OPTION VALUE='" . $value . "'>" . str_replace(" ()", "", $select_value) . "</OPTION>";
			}
			$c++;
		}
		if ( $db_value_found == false ) {
			echo "<OPTION VALUE='" . $ShippingCost . "' SELECTED>" . $ShippingCost . "</OPTION>";
		}
		?>
		</SELECT></NOBR>
		<INPUT TYPE="hidden" NAME="ShippingCostHidden" VALUE="">
		</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><B>Adjustment:</B></TD>
		<TD>$<INPUT TYPE="text" NAME="ManualAdjustment" VALUE="<?php echo number_format($ManualAdjustment, 2);?>" SIZE="<?php echo $field_size;?>" STYLE="text-align:left" <?php echo $form_status;?>></TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><B>Adjustment type:</B></TD>
		<TD><SELECT NAME="ManualAdjustmentType" <?php echo $form_status;?>>
		<?php if ( $ManualAdjustmentType == 'Spray Dry Loss' ) { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="Spray Dry Loss" SELECTED>Spray Dry Loss</OPTION>
			<OPTION VALUE="Other, see Comments">Other, see Comments</OPTION>
		<?php } elseif ( $ManualAdjustmentType == 'Other, see Comments' ) { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="Spray Dry Loss">Spray Dry Loss</OPTION>
			<OPTION VALUE="Other, see Comments" SELECTED>Other, see Comments</OPTION>
		<?php } else { ?>
			<OPTION VALUE=""></OPTION>
			<OPTION VALUE="Spray Dry Loss">Spray Dry Loss</OPTION>
			<OPTION VALUE="Other, see Comments">Other, see Comments</OPTION>
		<?php } ?>
		</SELECT></TD>
	</TR>
	<TR>
		<TD></TD>
		<TD>
		<?php if ( $locked != 1 ) { ?>
			<?php if ( $_REQUEST['action'] != 'edit' ) { ?>
				<INPUT TYPE="button" VALUE="Edit" onClick="window.location='customers_quotes.pricing.php?action=edit&psn=<?php echo $psn;?>'" CLASS="submit">
			<?php } else { ?>
				<INPUT TYPE="submit" VALUE="Save and done" CLASS="submit"><BR><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"><BR><INPUT NAME="save_refresh" TYPE="submit" VALUE="Save and refresh" CLASS="submit"><BR><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"><BR><INPUT TYPE="button" VALUE="Cancel" onClick="window.location='customers_quotes.pricing.php?psn=<?php echo $psn;?>'" CLASS="submit">
			<?php } ?>
		<?php } ?>
		</TD>
	</TR>
</TABLE></FORM>
</TD></TR></TABLE>
</TD></TR></TABLE>

<BR>

<?php
$sql = "SELECT pricesheetmaster.*, productmaster.SpecificGravity AS SpecificGravityMaster FROM pricesheetmaster LEFT JOIN productmaster USING (ProductNumberInternal)WHERE PriceSheetNumber = " . $psn;
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
$Notes = $row['Notes'];
if ( $Notes != '' ) {
	echo "<B>Comments:</B> " . $Notes . "<BR><BR>";
}
?>

		</TD>

		<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>

		<TD valign="top" WIDTH="100%"><IFRAME SRC="inc_pricing_analysis.php?psn=<?php echo $psn;?>&locked=<?php echo $locked;?>" WIDTH="480" HEIGHT="420" FRAMEBORDER="0" /></IFRAME></TD>

	</TR>
</TABLE>







		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><br/><br/>



<SCRIPT TYPE="text/javascript" src="combo_box/comboBox.js"></SCRIPT>


<?php
// LOAD DB VALUES ON TOP OF DROP-DOWN MENUS onLoad
if ( $_REQUEST['action'] == 'edit' ) {
?>
	<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>
	
	addLoadEvent(loadIDs);

	function loadIDs() {
		var sdc = document.getElementById('txtSprayDriedCost');
		var rbc = document.getElementById('txtRibbonBlendingCost');
		var lpc = document.getElementById('txtLiquidProcessingCost');
		var pc = document.getElementById('txtPackagingCost');
		var sc = document.getElementById('txtShippingCost');

		sdc.value='<?php echo $SprayDriedCost;?>';
		rbc.value='<?php echo $RibbonBlendingCost;?>';
		lpc.value='<?php echo $LiquidProcessingCost;?>';
		pc.value='<?php echo $PackagingCost;?>';
		sc.value='<?php echo $ShippingCost;?>';
	}

	</SCRIPT>
<?php } ?>


<SCRIPT TYPE="text/javascript">

function SelectProcessType_updated(field_value) {

	process_type = field_value.options[field_value.selectedIndex].value;

	var valCost = 0;
	//var SelectProcessType[2];
	var SelectProcessType_specific = 0;

	var sdc = document.getElementById('txtSprayDriedCost');
	var rbc = document.getElementById('txtRibbonBlendingCost');
	var lpc = document.getElementById('txtLiquidProcessingCost');
	var pack = document.getElementById('Packaged_In');
	var min = document.getElementById('MinBatch_Units');
 
	SelectProcessType = process_type.split(" - ");
	SelectProcessType_specific = SelectProcessType[1];
 
	// Default to 0
	rbc.value = 0;
	sdc.value = 0;
	LiquidProcessingCost = 0;
	ManualAdjustmentType = null;
		
	if ( SelectProcessType[0] == "Liquid" ) {
  
		/****ATTENTION***
		Process Category Default Values....if new items are added please verify this default can still apply to all
		*/

		pack.value = "5-Gallon Pail w/reicke spout";
		min.value = "one, 5-Gallon pail";
 
		switch ( SelectProcessType_specific ) {
			case 'Ethyl Alcohol':
				lpc.value = 2.00;
				break;
			default:
				lpc.value = 0.40;
				break;
		}

	} else if ( SelectProcessType[0] == "Plated" ) {

		/****ATTENTION***
		Process Category Default Values....if new items are added please verify this default can still apply to all
		*/

		pack.value = "50 lb. Boxes";

		switch ( SelectProcessType_specific ) {
			case 'Abelei':
				rbc.value = 0.35;
				min.value = "100 pounds";
				break;
			case 'Dutch':
				rbc.value = 0.30;
				min.value = "100 pounds";
				break;
			case 'Oxy':
				rbc.value = 0.39;
				alert("There was no default 'Min Order' found for the process type!");
				break;
			default:
				alert("Warning: Has the ribbon cost been assigned correctly based on your change? Please report to technical support.");
				alert("There was no default 'Min Order' found for the process type!");
				break;
		}

	} else if ( SelectProcessType[0] == "Spray Dry" ) {

		// Only one that has ManualAdjustmentType
		ManualAdjustmentType = "Spray Dry Loss";

		/****ATTENTION***
		Process Category Default Values....if new items are added please verify this default can still apply to all
		*/

		pack.value = "50 lb. Boxes";

		switch ( SelectProcessType_specific ) {
			case 'HBT':
				sdc.value = 3.10;
				min.value = "300 pounds";
				break;
			case 'QIC':
				sdc.value = 1.10;
				min.value = "300 pounds";
				break;
			default:
				alert("Warning: Has the Spray Dried Cost been assigned correctly based on your change? Please report to technical support.");
				alert("There was no default 'Min Order' found for the process type!");
				break;
		}

	} else {
		alert("Warning: No associated defaults found. Values have been defaulted to 0, null or otherwise left unchanged.");
	}

	//alert(sdc.value);
	//alert(rbc.value);
	//alert(lpc.value);
	//alert(pack.value);
	//alert(min.value);

}



// ON FORM SUBMIT, PASS HIDDEN INPUT VALUES TO PHP PROCESSING SCRIPT
function updateHiddenField(pricingForm) {

	var sdc = document.getElementById('txtSprayDriedCost');
	var rbc = document.getElementById('txtRibbonBlendingCost');
	var lpc = document.getElementById('txtLiquidProcessingCost');
	var pc = document.getElementById('txtPackagingCost');
	var sc = document.getElementById('txtShippingCost');

	pricingForm.SprayDriedCostHidden.value = sdc.value;
	pricingForm.RibbonBlendingCostHidden.value = rbc.value;
	pricingForm.LiquidProcessingCostHidden.value = lpc.value;
	pricingForm.PackagingCostHidden.value = pc.value;
	pricingForm.ShippingCostHidden.value = sc.value;

}

</SCRIPT>


<?php include("inc_footer.php"); ?>