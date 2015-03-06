<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB, Front Desk AND QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}



if ( $_REQUEST['action'] == "weight" ) {
	if ( !is_numeric($_REQUEST['weight']) ) {
		$_SESSION['note'] = "Please enter a numeric value for 'Weight estimate'<BR>";
		header ("Location: flavors_formulations.php?action=edit&pne=" . $_REQUEST['pne']);
		exit;
	} else {
		$_SESSION['weight'] = $_REQUEST['weight'];
		header ("Location: flavors_formulations.php?action=edit&pne=" . $_REQUEST['pne']);
		exit;
	}
}

if ( $_SESSION['weight'] != '' and is_numeric($_SESSION['weight']) ) {
	$weight = $_SESSION['weight'];
} else {
	$weight = 1;
}



$error_found="";
$Designation="";
$ProductNumberExternal="";
$ProductNumberInternal="";
$Keywords="";
$note="";
if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

$pne="";
$edit=false;
if ( isset($_SESSION['external_number']) ) {
	$pne = $_SESSION['external_number'];
	$edit = true;
}
if (isset($_REQUEST['pne']) ) {
	$pne = $_REQUEST['pne'];
}

$action="";
if (isset($_REQUEST['action']) )
{
	$action=$_REQUEST['action'];
}
if ( $action == 'edit' ) 
{
	$edit = true;
}

include('inc_global.php');
include('search/system_defaults.php');



if ( $_GET["dir"] == "u" ) {

	$sql = "UPDATE formulationdetail SET IngredientSEQ = 101 WHERE IngredientSEQ = " . $_GET["seq"] . " AND ProductNumberInternal = " . $_REQUEST['pni'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";

	$sql = "UPDATE formulationdetail SET IngredientSEQ = 102 WHERE IngredientSEQ = " . ($_GET["seq"] - 1) . " AND ProductNumberInternal = " . $_REQUEST['pni'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";

	$sql = "UPDATE formulationdetail SET IngredientSEQ = " .  ($_GET["seq"] - 1) . " WHERE IngredientSEQ = 101 AND ProductNumberInternal = " . $_REQUEST['pni'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";

	$sql = "UPDATE formulationdetail SET IngredientSEQ = " .  $_GET["seq"] . " WHERE IngredientSEQ = 102 AND ProductNumberInternal = " . $_REQUEST['pni'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";

	header ("Location: flavors_formulations.php?action=edit&pne=" . $_REQUEST['pne']);
	exit;

}

if ( $_GET["dir"] == "d" ) {

	$sql = "UPDATE formulationdetail SET IngredientSEQ = 101 WHERE IngredientSEQ = " . $_GET["seq"] . " AND ProductNumberInternal = " . $_REQUEST['pni'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";

	$sql = "UPDATE formulationdetail SET IngredientSEQ = 102 WHERE IngredientSEQ = " . ($_GET["seq"] + 1) . " AND ProductNumberInternal = " . $_REQUEST['pni'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";

	$sql = "UPDATE formulationdetail SET IngredientSEQ = " .  ($_GET["seq"] + 1) . " WHERE IngredientSEQ = 101 AND ProductNumberInternal = " . $_REQUEST['pni'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";

	$sql = "UPDATE formulationdetail SET IngredientSEQ = " .  $_GET["seq"] . " WHERE IngredientSEQ = 102 AND ProductNumberInternal = " . $_REQUEST['pni'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";

	header ("Location: flavors_formulations.php?action=edit&pne=" . $_REQUEST['pne']);
	exit;

}



if ( $action == "delete_formula" ) {
	$error_message = "";
	$sql = "SELECT `ProductNumberInternal` FROM externalproductnumberreference WHERE `ProductNumberExternal`='$pne'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$pni = $row[ProductNumberInternal];
	
	// if there's no inventory movements associated with the formula
	$sql = "SELECT COUNT(*) FROM inventorymovements AS im WHERE `ProductNumberInternal`='$pni'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	if (0 < $row[0])
		$error_message = "Inventory lots exist in the system for this formula, therefore the formula cannot be deleted.<br/>";

	// and if there's no batch sheets or customer orders
	$sql = "SELECT COUNT(*) FROM `batchsheetmaster` AS bsm WHERE bsm.`ProductNumberInternal`='$pni'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	if (0 < $row[0])
		$error_message .= "Batch sheets exist in the system for this formula, therefore the formula cannot be deleted.<br/>";

	$sql = "SELECT COUNT(*) FROM `customerorderdetail` WHERE `ProductNumberInternal`='$pni'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	if (0 < $row[0])
		$error_message .= "Customer orders exist in the system for this formula, therefore the formula cannot be deleted.<br/>";

	// then delete the formula
	$sql = "DELETE FROM `productmaster` WHERE `ProductNumberInternal`='$pni'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	if ('' == $error_message) {
		header("location: flavors_formulations.php");
		$_SESSION[note] = "Flavor Successfully deleted";
		exit();
	}
	else {
		$edit = true;
		$action="edit";
	}
}


if ( $edit and isset($_POST['add_ing']) ) {

	$ProductNumberInternal = $_POST['ProductNumberInternal'];
	$Ingredient = $_POST['Ingredient'];
	$IngredientSEQ = $_POST['IngredientSEQ'];
	$IngredientProductNumber =  $_POST['IngredientProductNumber'];
	$Percentage = $_POST['Percentage'];
	$normalize = $_POST['normalize'];

	// check_field() FUNCTION IN global.php
	check_field($IngredientProductNumber, 1, 'Internal#');
	check_field($IngredientSEQ, 3, 'SEQ#');
	check_field($Percentage, 3, 'Percentage');

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$IngredientSEQ = escape_data(ceil($IngredientSEQ));
		$Percentage = escape_data($Percentage);

		$sql = "SELECT MAX(IngredientSEQ) AS max_seq FROM formulationdetail WHERE ProductNumberInternal = '" . $ProductNumberInternal . "'";
		$result_count = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row_count = mysql_fetch_array($result_count);
		$max_seq = $row_count['max_seq'];

		$sql = "SELECT * FROM formulationdetail WHERE ProductNumberInternal = '" . $ProductNumberInternal . "' AND IngredientSEQ = '" . $IngredientSEQ . "'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( mysql_num_rows($result) > 0 or $IngredientSEQ < $max_seq ) {
			$sql = "UPDATE formulationdetail SET IngredientSEQ = (IngredientSEQ +1) WHERE ProductNumberInternal = '" . $ProductNumberInternal . "' AND IngredientSEQ >= '" . $IngredientSEQ . "' ORDER BY IngredientSEQ DESC";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		// PREPOPULATE TIER AND VENDOR
		// ADDED 09/16/2009
		$sql = "SELECT vwMaterialPricing.*
		FROM vwMaterialPricing
		LEFT JOIN productprices
		ON vwMaterialPricing.VendorID = productprices.VendorID AND vwMaterialPricing.ProductNumberInternal = productprices.ProductNumberInternal AND vwMaterialPricing.Tier = productprices.Tier
		WHERE is_deleted = 0 AND vwMaterialPricing.ProductNumberInternal = " . $IngredientProductNumber . " LIMIT 1";
		$result_tier = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//echo $sql;
		if ( mysql_num_rows($result_tier) > 0 ) {
			$row_tier = mysql_fetch_array($result_tier);
			$VendorID = "'" . $row_tier['VendorID'] . "'";
			$Tier = "'" . $row_tier['Tier'] . "'";
		} else {
			$VendorID = "NULL";
			$Tier = "NULL";
		}

		$sql = "INSERT INTO formulationdetail (ProductNumberInternal, IngredientSEQ, IngredientProductNumber, Percentage, VendorID, Tier) VALUES ('" . $ProductNumberInternal . "', '" . $IngredientSEQ . "', '" . $IngredientProductNumber . "', '" . $Percentage . "', " . $VendorID . ", " . $Tier . ")";
		//die("<BR><BR>$sql");
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

		if ( $normalize == 1 ) {
			$sql = "SELECT SUM(Percentage) AS CurrentPercentTotal FROM formulationdetail WHERE ProductNumberInternal = " . $ProductNumberInternal;
			$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$row = mysql_fetch_array($result);
			$CurrentPercentTotal = $row['CurrentPercentTotal'];
			$TargetPercent = 100;
			$AdjustmentPercent = $TargetPercent/$CurrentPercentTotal;
			$sql = "UPDATE formulationdetail SET Percentage = (Percentage * " . $AdjustmentPercent . ") WHERE ProductNumberInternal = '" . $_POST["ProductNumberInternal"] . "'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		header("location: flavors_formulations.php?action=edit&pne=" . $pne);
		exit();
	}
}
				


if ( $edit and isset($_POST['normalize']) ) {
	$sql = "SELECT SUM(Percentage) AS CurrentPercentTotal FROM formulationdetail WHERE ProductNumberInternal = " . $_POST["ProductNumberInternal"];
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$CurrentPercentTotal = $row['CurrentPercentTotal'];
	$TargetPercent = 100;
	$AdjustmentPercent = $TargetPercent/$CurrentPercentTotal;
	$sql = "UPDATE formulationdetail SET Percentage = (Percentage * " . $AdjustmentPercent . ") WHERE ProductNumberInternal = '" . $_POST["ProductNumberInternal"] . "'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	header("location: flavors_formulations.php?action=edit&pne=" . $pne);
	exit();
}
			


if ( isset($_POST['edit_header']) ) {

	$ProductNumberInternal = $_POST['ProductNumberInternal'];
	$ProductNumberExternal = $_POST['ProductNumberExternal'];
	$Appearance = $_POST['Appearance'];
	$DeveloperID = $_POST['DeveloperID'];
	$Intermediary = $_POST['Intermediary'];
	$QuickScan = $_POST['QuickScan'];
	$SpecificGravity = $_POST['SpecificGravity'];
	$SpecificGravityUnits = $_POST['SpecificGravityUnits'];
	$NoteForFormulation = $_POST['NoteForFormulation'];
	$ProductDesignation = $_POST['ProductDesignation'];
	$update = 1;

	// check_field() FUNCTION IN global.php
	check_field($SpecificGravity, 3, 'Specific gravity');

	// check for duplicate External number if changing the external number
	$sql = "SELECT * FROM externalproductnumberreference WHERE ProductNumberInternal = '$ProductNumberInternal'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	if ($row['ProductNumberExternal'] != $ProductNumberExternal) {
		$sql = "SELECT * FROM externalproductnumberreference WHERE ProductNumberExternal = '$ProductNumberExternal'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$count = mysql_num_rows($result);
		if (0 < $count) {
			$error_found=true;
			$error_message .= "External Number already exists. Try different external number.";
		}
	}

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$Appearance = escape_data($Appearance);
		$DeveloperID = escape_data($DeveloperID);
		$Intermediary = ("Intermediary"==$Intermediary ? 1 : 0);
		$QuickScan = escape_data($QuickScan);
		$SpecificGravity = escape_data($SpecificGravity);
		$SpecificGravityUnits = escape_data($SpecificGravityUnits);
		$NoteForFormulation = escape_data($NoteForFormulation);
		
		if ( $ProductNumberInternal != "" ) {
			$sql = "UPDATE productmaster " .
			" SET Appearance = '" . $Appearance . "'," .
			" DeveloperID = '" . $DeveloperID . "'," .
			" Intermediary = '" . $Intermediary . "'," .
			" QuickScan = '" . $QuickScan . "'," .
			" SpecificGravity = '" . $SpecificGravity . "'," .
			" SpecificGravityUnits = '" . $SpecificGravityUnits . "'," .
			" NoteForFormulation = '" . $NoteForFormulation . "'" .
			" WHERE ProductNumberInternal = '" . $ProductNumberInternal . "'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		//else {
		//	$sql = "INSERT INTO customer_contacts (title, first_name, last_name, suffix, job_title, department, customer_id, address1, address2, city, state, zip, country, email1, email2) VALUES ('" . $title . "','" . $first_name . "','" . $last_name . "','" . $suffix . "','" . $job_title . "','" . $department . "', " . $customer_id . ", '" . $address1 . "', '" . $address2 . "', '" . $city . "', '" . $state . "', '" . $zip . "', '" . $country . "', '" . $email1 . "', '" . $email2 . "','" . $NoteForFormulation . "', '" . $NoteForFormulation . "')";
		//	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//	$cid = mysql_insert_id();
		//}
		//die("$sql");
		//$_SESSION['note'] = "Information successfully saved<BR>";
		header("location: flavors_formulations.php?action=edit&pne=" . $pne);
		exit();

	}

}
elseif ( $edit ) {
	$sql = "SELECT * FROM productmaster INNER JOIN externalproductnumberreference USING (ProductNumberInternal) WHERE ProductNumberExternal = '" . escape_data($pne) . "'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	$row = mysql_fetch_array($result);
	//echo $sql . "<BR>";
	$ProductNumberInternal = $row['ProductNumberInternal'];
	$ProductNumberExternal = $row['ProductNumberExternal'];
	$QuickScan = $row['QuickScan'];
	$SpecificGravity = $row['SpecificGravity'];
	$SpecificGravityUnits = $row['SpecificGravityUnits'];
	$DeveloperID = $row['DeveloperID'];
	//$Description = $row['Description'];
	$NoteForFormulation = $row['NoteForFormulation'];
	$Appearance = $row['Appearance'];
	$Intermediary = (1==$row['Intermediary'] ? true : false);
	
	$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");

}



if ( $edit and isset($_POST['edit_ing']) ) {

	$ProductNumberInternal = $_POST['ProductNumberInternal'];
	$IngredientSEQ = $_POST['IngredientSEQ'];
	$IngredientSEQNew = $_POST['IngredientSEQNew'];
	$Percentage = $_POST['PercentageNew'];
	$update_ing = $_POST['update_ing'];
	if ( $Percentage == '' ) {
		$Percentage = 0;
	}

	// check_field() FUNCTION IN global.php
	check_field($IngredientSEQNew, 3, 'SEQ#');
	check_field($Percentage, 3, 'Percentage');

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$Percentage = escape_data($Percentage);
		$sql = "UPDATE formulationdetail " .
		" SET `Percentage` = " . $Percentage . ", " .
		" IngredientSEQ = " . $IngredientSEQNew .
		" WHERE ProductNumberInternal = '" . $ProductNumberInternal . "' AND IngredientSEQ = " . $IngredientSEQ;
		//die("$sql");
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//$_SESSION['note'] = "Information successfully saved<BR>";
		header("location: flavors_formulations.php?action=edit&pne=" . $pne);
		exit();
	}

}



if ( $action == "delete_ingredient" ) {
	$sql = "DELETE FROM formulationdetail WHERE ProductNumberInternal = '" . $_GET['pni'] . "' AND IngredientSEQ = '" . $_GET['seq'] . "'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$sql = "SELECT * FROM formulationdetail WHERE ProductNumberInternal = '" . $_GET["pni"] . "' ORDER BY IngredientSEQ";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	if ( mysql_num_rows($result) != 0 ) {
		$i = 1;
		while ( $row = mysql_fetch_array($result) ) {
			$sql = "UPDATE formulationdetail SET IngredientSEQ = " . $i . " WHERE ProductNumberInternal = '" . $_GET['pni'] . "' AND IngredientSEQ = '" .  $row['IngredientSEQ'] . "'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$i = $i + 1;
		}
	}
	header("location: flavors_formulations.php?action=edit&pne=" . $_GET['pne']);
	exit();
}

if ( isset($_REQUEST['Designation']) and $action == 'search' ) {
	$Designation = $_REQUEST['Designation'];
}
if ( isset($_REQUEST['ProductNumberExternal']) and $action == 'search' ) {
	$ProductNumberExternal = $_REQUEST['ProductNumberExternal'];
}
if ( isset($_REQUEST['ProductNumberInternal']) and $action == 'search' ) {
	$ProductNumberInternal = $_REQUEST['ProductNumberInternal'];
}
if ( isset($_REQUEST['Keywords']) and $action == 'search' ) {
	$Keywords = $_REQUEST['Keywords'];
}



include("inc_header.php");

?>





<script LANGUAGE=JAVASCRIPT>
 <!-- Hide
$(document).ready(function(){
	$(":input[readonly]").addClass("readOnly");
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");

	$(":submit").click(function() {
		$("#action").val(this.name);
		switch (this.name)
		{
			case 'new':
				popup('pop_add_product.php',800,900);
				return false;
				break;
			default:
				//alert ("this button not yet supported");
				break;
		}
	});
	
	$("#delete_formula").click(function() {
		if ( confirm('Are you sure you want to delete this formula? You may only delete formulae that have no associated inventory (i.e. no associated orders or batch sheets)') ) {
			document.location.href = "flavors_formulations.php?action=delete_formula&pne=<?php echo $pne ?>";
		} else 
			return false;
	});

	$("#designation_search").autocomplete("search/product_master_formulas_by_designation.php", {
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#designation_search").result(function(event, data, formatted) {
		if (data)
			$("#external_number_search").val('');
			$("#internal_number_search").val('');
			$("#keyword_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
	$("#external_number_search").autocomplete("search/product_master_formulas_by_external_number.php", {
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
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
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#internal_number_search").result(function(event, data, formatted) {
		if (data)
			$("#designation_search").val('');
			$("#external_number_search").val('');
			$("#keyword_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
	$("#keyword_search").autocomplete("search/product_master_formulas_by_keyword.php", {
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#keyword_search").result(function(event, data, formatted) {
		if (data)
			$("#designation_search").val('');
			$("#external_number_search").val('');
			$("#internal_number_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
});

function delete_ingredient(pni, seq, pne) {
	if ( confirm('Are you sure you want to delete this ingredient?') ) {
		document.location.href = "flavors_formulations.php?action=delete_ingredient&pni=" + pni + "&seq=" + seq + "&pne=" + pne;
	}
}

function validate() {
	switch (document.getElementById("action").value)
	{
		case 'delete':
			var answer = confirm("Delete this order?")
			if (answer) { return true; } else { return false; }
			break;
		default:
			break;
	}
}

 // End -->
 
</script>



<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
	//unset($error_found);
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
	//unset($note);
} ?>




<?php if ( $action == 'search' or $action != 'edit' ) { ?>

<table class="bounding">
<tr valign="top">
<td class="padded">
	<FORM id="search" name="search" ACTION="flavors_formulations.php" METHOD="get">
	<INPUT TYPE="hidden" NAME="action" VALUE="search">

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

		<TR>
			<TD><B>Material designation:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="designation_search" NAME="Designation" VALUE="<?php echo $Designation;?>" SIZE="30"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Abelei number (external):</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="external_number_search" NAME="ProductNumberExternal" VALUE="<?php
			if ( $action != '' ) {
				echo $ProductNumberExternal;
			}
			?>" SIZE="30"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Material number (internal):</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="internal_number_search" NAME="ProductNumberInternal" VALUE="<?php
			if ( $action != '' ) {
				echo $ProductNumberInternal;
			}
			?>" SIZE="30"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Keywords:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="keyword_search" NAME="Keywords" VALUE="<?php echo $Keywords;?>" SIZE="30"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="12"></TD>
		</TR>

		<TR>
			<TD colspan=3 ALIGN=left>
				<INPUT style="float:right" name="search" id="search" TYPE="submit" class="submit_medium" VALUE="Search"><INPUT style="margin-top:.5em" name="new" id="new" TYPE="submit" class="submit new" VALUE="New Flavor">
			</TD>
		</TR>
	</TABLE>
</FORM>
</TD></TR></TABLE>
<BR><BR>

<?php

}



if ( $action == 'search' ) {

	$clause = "";

	if ( $Designation != '' ) {
		$clause = " AND ( ( ProductMaster.Designation ) LIKE '%" . str_replace("'","''",$Designation) . "%' )";
	} else
	if ( $ProductNumberExternal != '' ) {
		$clause = " AND ( ( ExternalProductNumberReference.ProductNumberExternal ) LIKE '%" . str_replace("'","''",$ProductNumberExternal) . "%' )";
	} else 
	if ( $ProductNumberInternal != '' ) {
		$clause = " AND ( ( ProductMaster.ProductNumberInternal ) LIKE '%" . str_replace("'","''",$ProductNumberInternal) . "%' )";
	} else 
	if ( $Keywords != '' ) {
		$clause = " AND ( ( ProductMaster.Keywords ) LIKE '%" . str_replace("'","''",$Keywords) . "%' )";
	} 
	$sql = "SELECT ProductMaster.ProductNumberInternal, ProductMaster.QuickScan, ProductMaster.SpecificGravity, ProductMaster.SpecificGravityUnits, ProductMaster.DeveloperID, ";
	$sql .= "ProductMaster.Natural_OR_Artificial, ProductMaster.Designation, ProductMaster.ProductType, ProductMaster.Kosher, ProductMaster.Appearance, ExternalProductNumberReference.ProductNumberExternal ";
	$sql .= "FROM ExternalProductNumberReference LEFT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal ";
	$sql .= "WHERE ( ( ( ( ProductMaster.ProductNumberInternal ) LIKE '2%' ) OR ( ( ProductMaster.ProductNumberInternal ) LIKE '5%' ) ) $clause ) ";
	$sql .= "ORDER BY if( Mid( ExternalProductNumberReference.ProductNumberExternal, 1, 2 ) = 'US', ExternalProductNumberReference.ProductNumberExternal, BuildExternalSortKeyField1( ExternalProductNumberReference.ProductNumberExternal ) ) , ";
	$sql .= "if( Mid( ExternalProductNumberReference.ProductNumberExternal, 4, 1 ) = 'a', 0, ExternalProductNumberReference.ProductNumberExternal ) , ";
	$sql .= "BuildExternalSortKeyField3( ExternalProductNumberReference.ProductNumberExternal), ";
	$sql .= "BuildExternalSortKeyField4( ExternalProductNumberReference.ProductNumberExternal)";
	
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	//echo $sql . "<BR>";

	if ( $c > 0 ) {
		$bg = 0; ?>

		<FORM>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" style="margin-bottom:3em">

			<TR VALIGN=BOTTOM>
				<TD><B>Abelei# (external)</B></TD>
				<TD><B>Quick Scan</B></TD>
				<TD><B>Developer</B></TD>
				<TD WIDTH=140><IMG SRC="images/spacer.gif" WIDTH="140" HEIGHT="1"><BR><B>Description</B></TD>
				<TD><B>N or A</B></TD>
				<TD><B>Type</B></TD>
				<TD ALIGN=CENTER><B>Kosher</B></TD>
				<TD><B>Internal#</B></TD>
				<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			</TR>

			<?php 

			while ( $row = mysql_fetch_array($result) ) {

				$description = $row['Designation'];
				$n_a = $row['Natural_OR_Artificial'];
				$prod_type = $row['ProductType'];
				if ( $row['Kosher'] == "K" ) {
					$kosher = "Yes";
				} else {
					$kosher = "No";
				}

				if ( $bg == 1 ) {
					$bgcolor = "#F3E7FD";
					$bg = 0;
				} else {
					$bgcolor = "whitesmoke";
					$bg = 1;
				} ?>

				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD><?php echo $row['ProductNumberExternal'] ?></TD>
					<TD><?php echo $row['QuickScan'] ?></TD>
					<TD><?php
					if ("" != $row['DeveloperID']) {
						$sql = "SELECT first_name, last_name FROM users WHERE user_id = " . $row['DeveloperID'];
						$result_dev = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
						$row_dev = mysql_fetch_array($result_dev);
						echo "<NOBR>" . $row_dev['first_name'] . ' ' . $row_dev['last_name'] . "</NOBR>";
					}
					?></TD>
					<TD><NOBR><?php echo $description ?></NOBR></TD>
					<TD><?php echo $n_a;?></TD>
					<TD><?php echo $prod_type;?></TD>
					<TD ALIGN=CENTER><?php echo $kosher;?></TD>
					<TD><?php echo $row['ProductNumberInternal'] ?></TD>
					<TD><INPUT TYPE="button" VALUE="View" CLASS="submit" onClick='window.location="flavors_formulations.php?action=edit&pne=<?php echo urlencode($row['ProductNumberExternal'])?>"' STYLE="font-size:7pt"></TD>
				</TR>

			<?php } ?>

		</TABLE>
		</FORM>

	<?php } else {
		echo "No matches found in database<BR>";
	}
}








if ( $action == "edit" ) {

	$form_status = "";
	if ( $_REQUEST['update'] != 1 and $update != 1 ) {
		$form_status = "readonly=\"readonly\"";
	}

	?>

	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

	<FORM ACTION="flavors_formulations.php" METHOD="post">
	<INPUT TYPE="hidden" NAME="action" VALUE="<?php echo $action;?>">
	<INPUT TYPE="hidden" NAME="pne" VALUE="<?php echo $pne;?>">
	<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>">
	<INPUT TYPE="hidden" NAME="edit_header" VALUE="1">


	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

		<TR>
			<TD style="text-align:right"><NOBR><B>abelei# (external):</B></NOBR></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
			<TD><?php echo $ProductNumberExternal;?><!-- <INPUT TYPE="text" NAME="ProductNumberExternal" VALUE="<?php //echo $ProductNumberExternal;?>" SIZE="30" STYLE="width: 110px" <?php //echo $form_status;?>> --></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR>
			<TD style="text-align:right"><NOBR><B>Internal#:</B></NOBR></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
			<TD><?php echo $ProductNumberInternal;?></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR VALIGN=TOP>
			<TD style="text-align:right"><B>Quick scan:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
			<TD>
			<?php if ( $form_status != '' ) { ?>
				<?php echo $QuickScan;?>
			<?PHP } else { ?>
				<INPUT TYPE="text" NAME="QuickScan" VALUE="<?php echo $QuickScan;?>" SIZE="30" STYLE="width: 110px">
			<?php } ?>
			</TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR VALIGN=TOP>
			<TD style="text-align:right"><B>Specific gravity:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
			<TD>
			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
				<TR>

					<TD>
					<?php if ( $form_status != '' ) { ?>
						<?php echo number_format($row[SpecificGravity], 2) ?>
					<?php } else { ?>
						<INPUT TYPE="text" NAME="SpecificGravity" VALUE="<?php echo number_format($row[SpecificGravity], 2);?>" SIZE="30" STYLE="width: 90px" <?php echo $form_status;?>>
					<?php } ?>
					 (g/ml)
					</TD>
	
					<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="15" HEIGHT="1"></TD>
<?php
					/* <TD><B>Units:</B></TD>
					// <TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
					//<TD><select id="SpecificGravityUnits" name="SpecificGravityUnits" <?php echo $form_status;?>><?php printInventoryUnitsOptions($SpecificGravityUnits); ?></select></TD>
*/?>				</TR>
			</TABLE>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR>
			<TD style="text-align:right"><B>Developer:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
			<TD COLSPAN=3><SELECT NAME="DeveloperID" <?php echo $form_status;?>>
			<?php
			$sql = "SELECT user_id, first_name, last_name FROM users WHERE user_type = 3";
			$result_dev = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			while ( $row_dev = mysql_fetch_array($result_dev) ) {
				if ( $DeveloperID == $row_dev['user_id'] ) {
					echo "<OPTION VALUE='" . $row_dev['user_id'] . "' SELECTED>" . $row_dev['first_name'] . ' ' . $row_dev['last_name'] . "</OPTION>";
				} else {
					echo "<OPTION VALUE='" . $row_dev['user_id'] . "'>" . $row_dev['first_name'] . ' ' . $row_dev['last_name'] . "</OPTION>";
				}
			}
			?>
			</SELECT></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR VALIGN=TOP>
			<td style="text-align:right"><B>Intermediary:</B></td>
			<td><img src="images/spacer.gif" alt="spacer" width="5" height="1"></td>
			<td><input type="checkbox" name="Intermediary" value="Intermediary" <?php echo (true==$Intermediary ? "checked=\"checked\"" : "") ?> <?php echo $form_status;?>></td>
		</TR>

	</TABLE>

			</TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="20" HEIGHT="1"></TD>
			<TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

		<TR>
			<TD><B>Description:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
			<TD><?php echo $ProductDesignation;?><!-- <INPUT TYPE="text" NAME="Description" VALUE="<?php //echo $Description;?>" CLASS="text" <?php //echo $form_status;?>> --></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR VALIGN=TOP>
			<TD style="text-align:right"><B>Notes:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
			<TD WIDTH=350>
			<?php if ( $form_status != '' ) { ?>
				<?php echo $NoteForFormulation;?>
			<?php } else { ?>
				<TEXTAREA NAME="NoteForFormulation" STYLE="width:350px;height:120px"><?php echo $NoteForFormulation;?></TEXTAREA>
			<?php } ?>
			</TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Appearance:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
			<TD WIDTH=350>
			<?php if ( $form_status != '' ) { ?>
				<?php echo $Appearance;?>
			<?php } else { ?>
				<TEXTAREA NAME="Appearance" CLASS="textarea"><?php echo $Appearance;?></TEXTAREA>
			<?php } ?>
			</TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR><TD COLSPAN=3 ALIGN=RIGHT>
		<?php if ( $_REQUEST['update_ing'] == '' ) { ?>
			<?php if ( $form_status != '' ) { ?>
				<INPUT TYPE="button" VALUE="Edit" CLASS="submit" onClick='window.location="flavors_formulations.php?action=edit&update=1&pne=<?php echo urlencode($pne);?>"'>
			<?php } else { ?>
				<INPUT TYPE="submit" VALUE="Save" CLASS="submit"> <INPUT TYPE="button" VALUE="Cancel" CLASS="submit" onClick='window.location="flavors_formulations.php?action=edit&pne=<?php echo urlencode($pne);?>"'>
			<?php } ?>
		<?php } ?>
		</TD></TR>

	</TABLE>

			</TD>
		</TR>
	</TABLE>

	</TABLE>

		
	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</FORM><BR>



	<!-- ADD INGREDIENT -->
	<?php if ( "" != $form_status  and $_REQUEST['update_ing'] == '' ) { ?>
		<?php
		$sql = "SELECT MAX(IngredientSEQ) AS max_seq FROM formulationdetail WHERE ProductNumberInternal = '" . $ProductNumberInternal . "'";
		$result_count = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row_count = mysql_fetch_array($result_count);
		$max_seq = $row_count['max_seq'];
		$IngredientSEQ = ($max_seq + 1) . ".00";
		?>
<!-- 
		<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
		<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
		<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD ALIGN=CENTER>
 -->
		<FORM NAME="add_ingredient" ACTION="flavors_formulations.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="action" VALUE="<?php echo $action;?>">
		<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>">
		<INPUT TYPE="hidden" NAME="pne" VALUE="<?php echo $pne;?>">
		<INPUT TYPE="hidden" NAME="add_ing" VALUE="1">

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<TR>
				<TD>
			
				<TABLE ALIGN=RIGHT BORDER=0 CELLSPACING="0" CELLPADDING="2" BORDERCOLOR="#CDCDCD">

					<TR VALIGN=BOTTOM>
						<TD><B>Seq#</B></TD>
						<TD><B>Internal#</B></TD>
						<TD>&nbsp;</TD>
						<TD><B>Ingredient</B></TD>
						<TD><B>Percentage</B></TD>
						<TD><B>Normalize</B></TD>
						<TD>&nbsp;</TD>
					</TR>
					<TR>
						<TD><INPUT TYPE="text" NAME="IngredientSEQ" VALUE="<?php echo $IngredientSEQ;?>" SIZE="5" STYLE="text-align:right"></TD>
						<TD><INPUT TYPE="text" NAME="IngredientProductNumber" VALUE="<?php echo $IngredientProductNumber;?>" SIZE="12" ></TD>
						<TD><A HREF="JavaScript:newWindow=openWin('pop_search_product.php?screen=ff&pne=<?php echo $pne;?>','','width=800,height=600,toolbar=0,location=0,scrollBars=1,resizable=1,left=30,top=30'); newWindow.focus()"><IMG SRC="images/zoom.png" ALT="search" WIDTH="16" HEIGHT="16" BORDER=0></A></TD>
						<TD><INPUT TYPE="text" NAME="Ingredient" VALUE="<?php echo $Ingredient;?>" SIZE="25" readonly='readonly'></TD>
						<TD><INPUT TYPE="text" NAME="Percentage" VALUE="<?php echo $Percentage;?>" SIZE="12"></TD>
						<TD ALIGN=CENTER><INPUT TYPE="checkbox" NAME="normalize" VALUE="1"></TD>
						<TD><INPUT TYPE="submit" VALUE="Add Ingredient" CLASS="submit"></TD>
					</TR></FORM>
				</TABLE>

		</TD></TR></TABLE>

<!-- 
		</TD></TR></TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE>
 -->

	<?php } ?>
	<!-- ADD INGREDIENT -->


	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR VALIGN=TOP><TD>

	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD ALIGN=CENTER>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD>

	<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
		<TR><FORM ACTION="flavors_formulations.php" METHOD="post">
			<TD><B>Formula</B></TD>
			<TD ALIGN=RIGHT>
			<?php if ( "" != $form_status and $_REQUEST['update_ing'] == '' ) { ?>
				<INPUT TYPE="hidden" NAME="action" VALUE="<?php echo $action;?>">
				<INPUT TYPE="hidden" NAME="pne" VALUE="<?php echo $pne;?>">
				<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>">
				<INPUT TYPE="hidden" NAME="normalize" VALUE="1">
				<INPUT TYPE="submit" VALUE="Normalize" CLASS="submit">
			<?php } ?>
			</TD>
		</TR></FORM>
	</TABLE><BR>

	<TABLE ALIGN=RIGHT BORDER=1 CELLSPACING="0" CELLPADDING="1" BORDERCOLOR="#CDCDCD">
		
		<TR VALIGN=BOTTOM>
			<TD COLSPAN=8>&nbsp;</TD>
			<!-- <TD ALIGN=CENTER COLSPAN=3 BGCOLOR="#9966CC"><B CLASS="white">Best fit estimate</B></TD> -->
			<TD ALIGN=CENTER COLSPAN=4 BGCOLOR="#7766CC"><B CLASS="white">Selected Vendor/Tier</B></TD>
			<!-- <TD ALIGN=CENTER COLSPAN=3 BGCOLOR="#5566CC"><B CLASS="white">Default order size</B></TD> -->
			<TD COLSPAN=4>&nbsp;</TD>
		</TR>
		
		<TR VALIGN=BOTTOM>
			<TD COLSPAN=3>&nbsp;</TD>
			<TD><B>Seq#</B></TD>
			<TD><B>Internal#</B></TD>
			<TD><B>Ingredient</B></TD>
			<TD><B>Natural<BR>or artificial</B></TD>
			<TD ALIGN=RIGHT><B>Percentage</B></TD>

<!-- 
			<TD BGCOLOR="#DFDFDF"><B>Vendor</B></TD>
			<TD ALIGN=RIGHT BGCOLOR="#DFDFDF"><NOBR><B>Cost per lb.</B></NOBR></TD>
			<TD ALIGN=RIGHT BGCOLOR="#DFDFDF"><NOBR><B>Ext. cost</B></NOBR></TD>
 -->

			<TD BGCOLOR="#BCBCBC"><B>Vendor</B></TD>
			<TD ALIGN=RIGHT BGCOLOR="#BCBCBC"><NOBR><B>Cost per lb.</B></NOBR></TD>
			<TD ALIGN=RIGHT BGCOLOR="#BCBCBC"><NOBR><B>Ext. cost</B></NOBR></TD>
			<TD ALIGN=RIGHT BGCOLOR="#BCBCBC"><B>Effective</B></TD>

			<TD ALIGN=RIGHT><NOBR><B>Lowest<BR>Ext Cost</B></NOBR></TD>
			<TD ALIGN=RIGHT><NOBR><B>Highest<BR>Ext Cost</B></NOBR></TD>
			<TD ALIGN=RIGHT><NOBR><B>Last ordered<BR>price/size</B></NOBR></TD>
			<TD>&nbsp;</TD>

<!-- 
			<TD BGCOLOR="#DFDFDF"><B>Vendor</B></TD>
			<TD ALIGN=RIGHT BGCOLOR="#DFDFDF"><NOBR><B>Cost per lb.</B></NOBR></TD>
			<TD ALIGN=RIGHT BGCOLOR="#DFDFDF"><NOBR><B>Ext. cost</B></NOBR></TD>
 -->

		</TR>

	<?php

	//( SELECT MIN( PricePerPound ) 
	//	FROM productprices 
	//	WHERE productprices.ProductNumberInternal = pm.ProductNumberInternal
	//	AND Volume <= $weight 
	//) AS EstimatedPricePerPound,
	//(
	//	SELECT vendors.name FROM productprices, vendors 
	//		WHERE productprices.ProductNumberInternal = pm.ProductNumberInternal 
	//		AND productprices.VendorID=vendors.vendor_id 
	//		AND Volume <= $weight 
	//		AND PricePerPound = EstimatedPricePerPound
	//) AS vendor,

	$sql = "SELECT formulationdetail.*, pm.*, 
	(
	SELECT MIN(PricePerPound ) 
	FROM productprices
	WHERE productprices.ProductNumberInternal = pm.ProductNumberInternal LIMIT 1
	) AS LeastEstimatedPricePerPound,
	(
	SELECT vendors.name
	FROM productprices, vendors
	WHERE productprices.ProductNumberInternal = pm.ProductNumberInternal
	AND productprices.VendorID = vendors.vendor_id
	AND PricePerPound = LeastEstimatedPricePerPound LIMIT 1
	) AS LeastVendor, 
	(
	SELECT MAX(PricePerPound ) 
	FROM productprices
	WHERE productprices.ProductNumberInternal = pm.ProductNumberInternal LIMIT 1
	) AS MaxEstimatedPricePerPound,
	(
	SELECT vendors.name
	FROM productprices, vendors
	WHERE productprices.ProductNumberInternal = pm.ProductNumberInternal
	AND productprices.VendorID = vendors.vendor_id
	AND PricePerPound = MaxEstimatedPricePerPound LIMIT 1
	) AS MaxVendor,
	(
	SELECT UnitPrice FROM purchaseordermaster LEFT JOIN purchaseorderdetail USING(PurchaseOrderNumber) WHERE ProductNumberInternal = pm.ProductNumberInternal ORDER BY DateOrderPlaced DESC LIMIT 1
	) AS LastUnitPrice,
	(
	SELECT UnitOfMeasure FROM purchaseordermaster Left join purchaseorderdetail USING(PurchaseOrderNumber) WHERE ProductNumberInternal = pm.ProductNumberInternal ORDER BY DateOrderPlaced DESC LIMIT 1
	) AS LastUnitOfMeasure,
	(
	SELECT TotalQuantityOrdered FROM purchaseordermaster Left join purchaseorderdetail USING(PurchaseOrderNumber) WHERE ProductNumberInternal = pm.ProductNumberInternal ORDER BY DateOrderPlaced DESC LIMIT 1
	) AS LastTotalQuantityOrdered 
	FROM formulationdetail
	LEFT JOIN productmaster pm ON formulationdetail.IngredientProductNumber = pm.ProductNumberInternal
	WHERE formulationdetail.ProductNumberInternal = '" . escape_data($ProductNumberInternal) . "'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";
	$TotalEstimatedPricePerPound = 0;
	$TotalSelectedPricePerPound = 0;
	$TotalLeastEstimatedPricePerPound = 0;
	$total2 = 0;
	$totalLeast = 0;
	$totalMax = 0;
	$c = mysql_num_rows($result);
	$z = 0;

	if ( $c > 0 ) {
		$total = 0;
		while ( $row = mysql_fetch_array($result) ) {
			$z = $z + 1;
			?>

			<TR>
			<FORM ACTION="flavors_formulations.php" METHOD="post">
			<INPUT TYPE="hidden" NAME="action" VALUE="<?php echo $action;?>">
			<INPUT TYPE="hidden" NAME="pne" VALUE="<?php echo $pne;?>">
			<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>">
			<INPUT TYPE="hidden" NAME="IngredientSEQ" VALUE="<?php echo $row['IngredientSEQ'];?>">
			<INPUT TYPE="hidden" NAME="edit_ing" VALUE="1">
			<INPUT TYPE="hidden" NAME="update_ing" VALUE="<?php echo $row['IngredientProductNumber'];?>">
				<TD>
				<?php if ( "" != $form_status and $_REQUEST['update_ing'] == '' and $update_ing == '' ) { ?>
					<INPUT TYPE="button" VALUE="x" CLASS="submit" onClick="delete_ingredient('<?php echo $ProductNumberInternal;?>', '<?php echo $row['IngredientSEQ'];?>', '<?php echo $pne;?>')">
				<?php } else { ?>
					&nbsp;
				<?php } ?>
				</TD>

				<TD>
				<?php if ( $form_status != '' and $_REQUEST['update_ing'] == '' ) { ?>

					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">
						<TR>
							<TD WIDTH="16">
							<?php if ( $z > 1 ) { ?>
								<A HREF="flavors_formulations.php?pni=<?php echo $ProductNumberInternal;?>&seq=<?php echo $row[IngredientSEQ];?>&pne=<?php echo $pne;?>&dir=u"><IMG SRC="images/arrow_up.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A>
							<?php } else { ?>
								<IMG SRC="images/spacer.gif" WIDTH="16" HEIGHT="16">
							<?php } ?>
							</TD>
							<TD WIDTH="16">
							<?php if ( $z != $c ) { ?>
								<A HREF="flavors_formulations.php?pni=<?php echo $ProductNumberInternal;?>&seq=<?php echo $row[IngredientSEQ];?>&pne=<?php echo $pne;?>&dir=d"><IMG SRC="images/arrow_down.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A>
							<?php } else { ?>
								<IMG SRC="images/spacer.gif" WIDTH="16" HEIGHT="16">
							<?php } ?>
							</TD>
						</TR>
					</TABLE>

				<?php } ?>
				</TD>

				<TD><NOBR>
				<?php if ( "" != $form_status ) { ?>
					<?php if ( $_REQUEST['update_ing'] == $row['IngredientProductNumber'] or $update_ing == $row['IngredientProductNumber'] ) { ?>
						<INPUT TYPE="submit" VALUE="Save" CLASS="submit"> <INPUT TYPE="button" VALUE="Cancel" CLASS="submit" onClick="window.location='flavors_formulations.php?action=edit&pne=<?php echo urlencode($ProductNumberExternal);?>'">
					<?php } else { ?>
						<INPUT TYPE="button" VALUE="Edit" CLASS="submit" onClick="window.location='flavors_formulations.php?action=edit&update_ing=<?php echo $row['IngredientProductNumber'];?>&pne=<?php echo urlencode($ProductNumberExternal);?>'">
					<?php } ?>
				<?php } else { ?>
					&nbsp;
				<?php } ?>
				</NOBR></TD>
				<?php if ( $_REQUEST['update_ing'] == $row['IngredientProductNumber'] or $update_ing == $row['IngredientProductNumber'] ) {
					$ing_form_status = "";
				} else {
					$ing_form_status = "readonly='readonly'";
				} ?>

				<?php
				if ( substr($row['IngredientProductNumber'], 0, 1) == 4 ) {
					$td_bgcolor = "#999999";
					$font_color = "color:#FFFFFF;font-weight:bold";
					$water_colspan = "";
					$colspan = "COLSPAN=10";
				} elseif ( $row['IngredientProductNumber'] == "108290" ) {  // WATER
					$td_bgcolor = "#F3E7FD";
					$font_color = "";
					$water_colspan = "COLSPAN=7";
					$colspan = "";
				} else {
					$td_bgcolor = "#F3E7FD";
					$font_color = "color: #000000";
					$water_colspan = "";
					$colspan = "";
				}
				?>

				<TD ALIGN=RIGHT BGCOLOR="<?php echo $td_bgcolor;?>">
				<?php
				//echo "<SPAN STYLE='" . $font_color . "'>" . $row['IngredientSEQ'] . "</SPAN>";
				?>
				<INPUT TYPE="text" NAME="IngredientSEQNew" VALUE="<?php echo $row['IngredientSEQ'];?>" SIZE="5" STYLE="text-align:right"   <?php echo $ing_form_status;?>>
				</TD>

				<TD BGCOLOR="<?php echo $td_bgcolor;?>">
				<?php
				echo "<SPAN STYLE='" . $font_color . "'><A HREF='flavors_materials.php?action=edit&ProductNumberInternal=" . $row['IngredientProductNumber'] . "'>" . $row['IngredientProductNumber'] . "</A></SPAN>";
				?>
				<!-- <INPUT TYPE="text" NAME="IngredientProductNumber" VALUE="<?php //echo $row['IngredientProductNumber'];?>" SIZE="8" readonly='readonly'> -->
				</TD>

				<TD BGCOLOR="<?php echo $td_bgcolor;?>" <?php echo $colspan;?>>
				<?php

				if ( substr($row['IngredientProductNumber'], 0, 1) == 2 ) {
					$sql = "SELECT ProductNumberExternal FROM externalproductnumberreference WHERE ProductNumberInternal = " . $row['IngredientProductNumber'];
					$result_external = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql_external<BR><BR>");
					$row_external = mysql_fetch_array($result_external);
					$abelei_number = " (abelei# " . $row_external[0] . ")";
				}
				else {
					$abelei_number = '';
				}

				echo "<NOBR><SPAN STYLE='" . $font_color . "'>" . $row['Designation'] . $abelei_number . "</SPAN>&nbsp;</NOBR>";
				?>
				<!-- <INPUT TYPE="text" NAME="Designation" VALUE="<?php //echo $row['Designation'];?>" SIZE="20" readonly='readonly'> -->
				</TD>

				<?php
				if ( substr($row['IngredientProductNumber'], 0, 1) != 4 ) {
				?>

					<TD><?php echo $row['Natural_OR_Artificial'];?>&nbsp;
					<!-- <INPUT TYPE="text" NAME="Natural_OR_Artificial" VALUE="<?php //echo $row['Natural_OR_Artificial'];?>" SIZE="14" readonly='readonly'> -->
					</TD>

					<TD ALIGN=RIGHT><INPUT TYPE="text" NAME="PercentageNew" VALUE="<?php echo number_format($row['Percentage'], 3);?>" SIZE="6" STYLE="text-align:right" <?php echo $ing_form_status;?>></TD>

					<?php

					$vendor_price_effective_date = '';

					if ( $row['VendorID'] != '' and $row['Tier'] != '' ) {
						$sql= "SELECT Tier, PricePerPound, name, PriceEffectiveDate 
						FROM vwmaterialpricing
						LEFT JOIN vendors ON vwmaterialpricing.VendorID = vendors.vendor_id
						WHERE ProductNumberInternal = " . $row['IngredientProductNumber'] . " AND VendorID = '" . $row['VendorID'] . "' AND Tier = '" . $row['Tier'] . "'";
						//echo $sql . "<BR><BR>";
						$result_selected = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
						$row_selected = mysql_fetch_array($result_selected);
						$vendor_name = $row_selected['name'];
						$vendor_price_per_pound = $row_selected['PricePerPound'];
						$vendor_tier = $row_selected['Tier'];
						$vendor_price_effective_date = $row_selected[PriceEffectiveDate];
					} elseif ( $row['IngredientProductNumber'] == "108290" ) {
						$vendor_name = "&nbsp;";
					} else {
						$vendor_name = "<NOBR><I>None yet</I></NOBR>";
						$vendor_price_per_pound = 0;
						$vendor_tier = "&nbsp;";
					}
					?>

					<TD <?php echo $water_colspan;?> BGCOLOR="#BCBCBC"><?php echo $vendor_name; ?></TD>

					<?php  if ( $row['IngredientProductNumber'] != "108290" ) { ?>

						<TD ALIGN=RIGHT BGCOLOR="#BCBCBC"><?php
						if ( is_numeric($vendor_price_per_pound) ) {
							echo number_format($vendor_price_per_pound, 2);
						} else {
							echo "&nbsp;";
						}
						?>
						</TD>

						<TD ALIGN=RIGHT BGCOLOR="#BCBCBC"><?php
						if ( is_numeric($vendor_price_per_pound) ) {
							$selected_line_item = $vendor_price_per_pound*$row['Percentage'];
							echo number_format($selected_line_item/100, 2);
							$TotalSelectedPricePerPound = $TotalSelectedPricePerPound + $selected_line_item;
						} else {
							echo "&nbsp;";
						}
						?>
						</TD>

						<TD ALIGN=RIGHT BGCOLOR="#BCBCBC"><?php
						if ( is_numeric($vendor_price_per_pound) ) {
							if ( $vendor_price_effective_date != '' ) {
								echo date('m/d/Y',strtotime($vendor_price_effective_date));
							} else {
								echo "<NOBR><I>None yet</I></NOBR>";
							}
						} else {
							echo "&nbsp;";
						}
						?>
						</TD>

						<TD ALIGN=RIGHT><?php
						if ( is_numeric($row['LeastEstimatedPricePerPound']) ) {
							$line_item = $row['LeastEstimatedPricePerPound']*$row['Percentage'];
							echo number_format($line_item/100, 2);
							$TotalLeastEstimatedPricePerPound = $TotalLeastEstimatedPricePerPound + $line_item;
						} else {
							echo "&nbsp;";
						}
						?>
						</TD>

						<TD ALIGN=RIGHT><?php
						if ( is_numeric($row['MaxEstimatedPricePerPound']) ) {
							$max_line = $row['MaxEstimatedPricePerPound']*$row['Percentage'];
							echo number_format($max_line/100, 2);
							$totalMax = $totalMax + $max_line;
						} else {
							echo "&nbsp;";
						}
						?>
						</TD>

						<TD ALIGN=RIGHT><?php
						if ( $row['LastUnitPrice'] != '' ) {
							echo $row['LastUnitPrice'] . "/" . $row['LastUnitOfMeasure'];
						} else {
							echo "&nbsp;";
						}
						?></TD>

						<TD><INPUT TYPE="button" VALUE="Select Vendor/Tier" onClick="popup('pop_select_tier.php?ff=1&seq=<?php echo $row['IngredientSEQ'];?>&ipn=<?php echo $row['IngredientProductNumber'];?>&pni=<?php echo $ProductNumberInternal;?>', 960, 800)" CLASS="submit"></TD>

					<?php }?>

				<?php } ?>

				</TR></FORM>

			<?php
			$total = $total + $row['Percentage'];
		} ?>
		<TR>
			<TD COLSPAN=15><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="1"></TD>
		</TR>
		<TR>
			<TD COLSPAN=6 ALIGN=RIGHT><B>Totals:</B></TD>
			<TD>&nbsp;</TD>
			<TD ALIGN=RIGHT><INPUT TYPE="text" NAME="total1" SIZE="9" VALUE="<?php echo number_format($total, 3);?>" STYLE="text-align:right" READONLY></TD>

<!-- 
			<TD>&nbsp;</TD>
			<TD ALIGN=RIGHT><B><?php //echo number_format($total2, 2);?></B></TD>
			<TD ALIGN=RIGHT><B><?php //echo number_format($TotalEstimatedPricePerPound, 2);?></B></TD>
 -->

			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD ALIGN=RIGHT><B><?php echo number_format($TotalSelectedPricePerPound/100, 2);?></B></TD>
			<TD ALIGN=RIGHT><B><?php echo number_format($TotalLeastEstimatedPricePerPound/100, 2);?></B></TD>
			<TD ALIGN=RIGHT><B><?php echo number_format($totalMax/100, 2);?></B></TD>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			

<!-- 
			<TD>&nbsp;</TD>
			<TD ALIGN=RIGHT><B><?php //echo number_format(999, 2);?></B></TD>
			<TD ALIGN=RIGHT><B><?php //echo number_format(888, 2);?></B></TD>
 -->

			<!-- <TD COLSPAN=4>&nbsp;</TD> -->
		</TR>
	<?php } ?>

	</TABLE>

	</TD></TR><FORM></TABLE>

	<?php if ( "" != $form_status and $_REQUEST['update_ing'] == '' ) { ?>

		<IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="12"><BR>
		<INPUT TYPE="button" VALUE="Print Formula" CLASS="submit" onClick="printer_popup('reports/print_formula.php?pni=<?php echo $ProductNumberInternal;?>')" STYLE="margin-right:10px; margin-left:10px;">
		<INPUT TYPE="button" VALUE="Print Formula w/ Vendors" CLASS="submit" onClick="printer_popup('reports/print_formula_vendor.php?pni=<?php echo $ProductNumberInternal;?>')"  STYLE="margin-right:10px; margin-left:10px;">
		<INPUT TYPE="button" VALUE="Print Sample Batch Sheet" CLASS="submit" onClick="printer_popup('pop_sample_batch_sheet.php?pne=<?php echo $pne;?>')"  STYLE="margin-right:10px; margin-left:10px;">
		<INPUT TYPE="button" VALUE="Delete Formula" ID="delete_formula" CLASS="submit" STYLE="margin-right:10px; margin-left:10px; background-color:red;">

	<?php } ?>

	</TD></TR></FORM></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>



<!-- 

	</TD>

	<TD>&nbsp;&nbsp;&nbsp;</TD>

	<TD>

	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD ALIGN=CENTER>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD>


	<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
		<TR><FORM ACTION="flavors_formulations.php?action=weight" METHOD="post">
			<TD>
			<B><NOBR>Weight estimate (lbs)</NOBR></B><BR><BR>
			<?php //if ( "" != $form_status and $_REQUEST['update_ing'] == '' ) { ?>
				<INPUT TYPE="hidden" NAME="pne" VALUE="<?php //echo $pne;?>">
				<NOBR><INPUT TYPE="text" NAME="weight" VALUE="<?php //echo $weight;?>" SIZE="8">
				<INPUT TYPE="submit" VALUE="Save" CLASS="submit"></NOBR>
			<?php //} ?>
			</TD>
		</TR></FORM>
	</TABLE>

	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>
 -->

	</TD></TR></TABLE><BR>

<?php } ?>



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
</script>



<?php include("inc_footer.php"); ?>