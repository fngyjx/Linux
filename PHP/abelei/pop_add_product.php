<?php

include('inc_ssl_check.php');
session_start();

include('inc_global.php');

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB AND QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

$error_message="";

$session_external_number="";
if ( isset($_SESSION['external_number']) ) {
	$session_external_number = $_SESSION['external_number'];
	unset($_SESSION['external_number']);
}
$session_internal_number="";
if ( isset($_SESSION['internal_number']) ) {
	$session_internal_number = $_SESSION['internal_number'];
	unset($_SESSION['internal_number']);
}
$designation_search = "";
if ( isset($_REQUEST['product_designation_search'] ) && !empty($_REQUEST['product_designation_search'])) {
	$html_designation_search = htmlentities($_REQUEST['product_designation_search']);
	$designation_search = escape_data($_REQUEST['product_designation_search']);
}

$record_type="";
if ( isset($_REQUEST['record_type']) ) {
	$record_type = $_REQUEST['record_type'];
}

$action="";
if ( isset($_REQUEST['action']) ) {
	$action = $_REQUEST['action'];
}
/* Initialize */
$external_number = "";
$intermediary = "";
$designation = "";
$quick_scan = "";
$n_or_a = "";
$product_type = "";
$kosher = "";
$organic = "";
$inventory_units = "";
$clone_source = "";

if ( ("clone" == $action) || ("save" == $action) )  {
	/* Set */
	if ( isset($_REQUEST['record_type']) ) { $record_type = $_REQUEST['record_type']; }
	if ( isset($_REQUEST['external_number']) && ("" != $_REQUEST['external_number']) ) { $external_number = escape_data($_REQUEST['external_number']); }
	if ( isset($_REQUEST['intermediary']) && ("" != $_REQUEST['intermediary']) ) { $intermediary = escape_data($_REQUEST['intermediary']); }
	if ( isset($_REQUEST['designation']) && ("" != $_REQUEST['designation']) ) { $designation = escape_data($_REQUEST['designation']); }
	if ( isset($_REQUEST['quick_scan']) && ("" != $_REQUEST['quick_scan']) ) { $quick_scan = escape_data($_REQUEST['quick_scan']); }
	if ( isset($_REQUEST['n_or_a']) && ("" != $_REQUEST['n_or_a']) ) { $n_or_a = escape_data($_REQUEST['n_or_a']); }
	if ( isset($_REQUEST['product_type']) && ("" != $_REQUEST['product_type']) ) { $product_type = escape_data($_REQUEST['product_type']); }
	if ( isset($_REQUEST['kosher']) && ("" != $_REQUEST['kosher']) ) { $kosher = escape_data($_REQUEST['kosher']); }
	if ( isset($_REQUEST['organic']) && ("" != $_REQUEST['organic']) ) { $organic = escape_data($_REQUEST['organic']); }
	if ( isset($_REQUEST['inventory_units']) && ("" != $_REQUEST['inventory_units']) ) { escape_data($inventory_units = $_REQUEST['inventory_units']); }
	if ( isset($_REQUEST['clone']) && ("" != $_REQUEST['clone']) ) { $clone_source = escape_data($_REQUEST['clone']); }
	/* Validate */
	if( validateData( $record_type, $external_number, $intermediary, $designation, $quick_scan, $n_or_a, $product_type, $kosher, $organic, $inventory_units, $error_message) ) {
		$date="";
		$addToInventory=false;
		switch ($record_type) {
			case 'formula':
				$date = date("Y-m-d  H:m:s");
				$type_number = "2";
				$addToInventory=true;
				break;
			case 'raw material':
				$type_number = "1";
				$addToInventory=true;
				break;
			case 'instruction':
				$type_number = "4";
				break;
			case 'process':
				$type_number = "7";
				break;
			case 'packaging':
				$type_number = "6";
				$addToInventory=true;
				break;
		}
		$sql = "SELECT Max(ProductMaster.ProductNumberInternal) AS MaxOfProductNumberInternal FROM ProductMaster WHERE (((ProductMaster.ProductNumberInternal)  Like '$type_number%'))";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$result_count = mysql_num_rows($result);
		$internal_number = "600000";
		if (0 < $result_count) { 
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$internal_number = $row['MaxOfProductNumberInternal'] + 1;
		}
		if ('save' == $action) {
			$sql = "INSERT INTO productmaster (ProductNumberInternal, DateOfFormulation, Designation, Intermediary, Kosher, Natural_OR_Artificial, ProductType, QuickScan, Organic, UnitOfMeasure) VALUES ";
			$sql .= "('$internal_number', ". (""==$date ? 'NULL' : "'$date'") .", '$designation', " . ("on"==$intermediary ? "1" : "0" ) . ", " . (""==$kosher ? 'NULL' : "'$kosher'") . ", ";
			$sql .= (""==$n_or_a ? 'NULL' : "'$n_or_a'") . ", " . (""==$product_type ? 'NULL' : "'$product_type'") . ", " . (""==$quick_scan ? 'NULL' : "'$quick_scan'") . ", " . ("on"==$organic ? "1" : "0" ) . "," . (false==$addToInventory ? 'NULL' : "'$inventory_units'") . ")";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		else if ( ('clone' == $action) && ("" != $clone_source) ){
			/* copy formula to temp table */
			$sql = "INSERT INTO tmpclone SELECT * FROM ProductMaster WHERE ProductNumberInternal='$clone_source'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			/* update temp table copy with new values */
			$sql = "UPDATE tmpclone SET ProductNumberInternal='$internal_number', DateOfFormulation=". (""==$date ? 'NULL' : "'$date'") .", Designation='$designation', ";
			$sql .= "Intermediary= " . ("on"==$intermediary ? "1" : "0" ) . ", " . ("" != $kosher ? "Kosher='$kosher', " : "" );
			$sql .= (""!=$n_or_a ? "Natural_OR_Artificial='$n_or_a', " : "") . (""!=$product_type ? "ProductType='$product_type', " : "");
			$sql .= (""!=$quick_scan ? "QuickScan='$quick_scan', " : "") . "Organic=" . ("on"==$organic ? "1" : "0" )." WHERE ProductNumberInternal='$clone_source'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			/* copy from temp table to productmaster */
			$sql = "INSERT INTO productmaster SELECT * FROM tmpclone WHERE ProductNumberInternal='$internal_number'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			/* delete from temp table */
			$sql = "DELETE FROM tmpClone WHERE ProductNumberInternal='$internal_number'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			/* copy ingredients to temp table */
			$sql = "INSERT INTO tmpformulationclone SELECT * FROM formulationdetail WHERE ProductNumberInternal='$clone_source'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			/* update temp table copy with new values */
			$sql = "UPDATE tmpformulationclone SET ProductNumberInternal='$internal_number' WHERE ProductNumberInternal='$clone_source'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			/* copy from temp table to formulationdetail */
			$sql = "INSERT INTO formulationdetail SELECT * FROM tmpformulationclone WHERE ProductNumberInternal='$internal_number'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			/* delete from temp table */
			$sql = "DELETE FROM tmpformulationclone WHERE ProductNumberInternal='$internal_number'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		if ("formula" == $record_type) {
			$sql = "INSERT INTO ExternalProductNumberReference ( ProductNumberExternal, ProductNumberInternal ) VALUES ('$external_number', '$internal_number')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$_SESSION['external_number'] = $external_number;
			$options="pne=$external_number";
			$location = "window.opener.location";
		}
		else
		{
			$options="ProductNumberInternal=$internal_number";
			$location = "'flavors_materials.php'";
		}
		$_SESSION['internal_number'] = $internal_number;
		$_SESSION['note'] .= "Succesfully saved $designation with internal number $internal_number";
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "var url = $location + '?action=edit&$options'\n";
		echo "window.opener.location.href=url\n";
		echo "window.close()\n";
		echo "</SCRIPT>\n";
	}
	else { 
		//echo "WTF - $error_message"; 
	}
}

function validateData( &$record_type, &$external_number, &$intermediary, &$designation, &$quick_scan, &$n_or_a, &$product_type, &$kosher, &$organic, &$inventory_units, &$error_message = "" ) {
	$valid = true;
	global $link;
	if ("formula"==$record_type)
	{
		if ( "" != $external_number) {
			if (20 < strlen($external_number))
			{
				$error_message="External number must be less than 20 characters";
				$valid = false;
			}
			else
			{
				/* test if duplicate external number*/
				$sql = "SELECT ProductNumberExternal FROM ExternalProductNumberReference WHERE ProductNumberExternal='$external_number'";
				$result = mysql_query($sql, $link);
				$result_count = mysql_num_rows($result);
				if (0 < $result_count) { 
					$error_message="External Number already exists.<br/>";
					$valid =  false; 
				}
			}
		} else { 
			$error_message.="External Number required.<br/>";
			$valid =  false; 
			}
		
		if (20 < strlen($quick_scan) ) { 
			$error_message.="Quickscan must be 20 characters or less.<br/>";
			$valid =  false; 
		}
		if ( "" ==$inventory_units) { 
			$error_message.="Must select inventory unit.<br/>";
			$valid =  false; 
		}
	}
	else if ( ("raw material"==$record_type) || ("packaging"==$record_type) )
	{
		if ( "" ==$inventory_units) { 
			$error_message.="Must select inventory unit.<br/>";
			$valid =  false; 
		}
		$external_number="";
		$intermediary="";
		$quick_scan="";
		if ("packaging"==$record_type) {
			$n_or_a="";
			$product_type="";
			$kosher="";
			$organic="";
		}
	}
	else if ( ("instruction"==$record_type) || ("process"==$record_type) ) { 
		$external_number="";
		$intermediary="";
		$quick_scan="";
		$n_or_a="";
		$product_type="";
		$kosher="";
		$organic="";
	}
	else
	{
		$error_message.="Must select product type.<br/>";
		$valid =  false; 
	}

	if (""==$designation) { 
		$error_message.="Designation cannot be blank.<br/>";
		$valid =  false; 
	}
	else if (100 < strlen($designation) ) { 
		$error_message.="Designation must be 100 characters or less.<br/>";
		$valid =  false; 
	}

	return $valid;
}

include('search/system_defaults.php');
include("inc_pop_header.php");
?>

<script>
	var contacts="";
	$(document).ready(function(){
//	$("div[id]").hide();
//	$("#new_input").hide();
	// $("#product_designation").autocomplete("search/products_by_designation.php", {
		// matchContains: true,
		// mustMatch: true,
		// minChars: 0,
		// width: 350,
		// max:10000,
		// multipleSeparator: "¬",
		// scrollheight: 350
	// });
	$(":submit").click(function() {
		$("#action").val(this.id);
		var alertMessage="";
		switch (this.id)
		{
			case 'save':
				alertMessage = validated();
				if ("" != alertMessage )
				{ 
					alert(alertMessage);
					return false;
				}
				break;
			case 'clone':
				alertMessage = validated();
				if ("" != alertMessage )
				{ 
					alert(alertMessage);
					return false;
				}
				if ( $(":checked[name='clone']").length == 0 )
				{
					alert("A record to clone must be selected");
					return false;
				}
				else {
					if (!confirm("Do you want to add the new product and clone it from formula " + $(":checked[name='clone']").val() + "?")) {
						return false;
					}
				}
				break;
			case 'cancel':
				alert("This will cancel");
				self.close();
				break;
			case 'search':
				if( $(":checked[name='record_type']").length == 0 )
				{
					alert('Please select a record type to search against');
					return false;
				}
				break;
			default:
				alert ("this button not yet supported");
				break;
		}
	});
	$(":radio[name=record_type]").click(function() {
		$("#new_input").show();
		$("#search_results").hide("slow");
		switch (this.value)
		{
			case 'formula':
				$("div:not(.type_formula)[class^='type']").hide("normal");
				$(".type_formula").show("normal");
				break;
			case 'raw material':
				$("div:not(.type_rawMaterial)[class^='type']").hide("normal");
				$(".type_rawMaterial").show("normal");
				break;
			case 'instruction':
				$("div:not(.type_instruction)[class^='type']").hide("normal");
				$(".type_instruction").show("normal");
				break;
			case 'process':
				$("div:not(.type_process)[class^='type']").hide("normal");
				$(".type_process").show("normal");
				break;
			case 'packaging':
				$("div:not(.type_packaging)[class^='type']").hide("normal");
				// $("div[class^='type'][class!=type_packaging]").hide("normal");
				$(".type_packaging").show("normal");
				break;
			default:
				break;
		}
	});
});
function validated() {
	var goodToSave=true;
	var alertMessage="";
	if ( ( ( $("#external_number").val() == "" ) && ( $(":checked[name='record_type']").val() == "formula" ) ) || ($("#external_number").val().length > 20) ){ 
			alertMessage+="External Number cannot be blank or longer than 20 characters.\n";
			$("#external_number_label").attr("style", "border: solid 1px red");
			goodToSave=false;
	}
	// else if ( $(":checked[name='record_type']").val() == "formula" ) {/* make sure they're not submitting a duplicate external number*/
		// $.ajax({
			// type: "POST",
			// url: "search/test_external_number.php",
			// data: "external_number=" + $("#external_number").val(),
			// success: function(msg){
				// if (msg== 'exists') {
					// alert("External Number already exists. Please change.\n");
					// $("#external_number_label").attr("style", "border: solid 1px red");
					// goodToSave=false;
				// }
				// else
				// {
					// alert("good");
				// }
			// }
		// });
	// }
	else {
		$("#external_number_label").attr("style", "border: none 0px");
	}
	if ( "" == $("#designation").val() ) { 
			alertMessage+="Designation field cannot be blank.\n";
			$("#designation_label").attr("style", "border: solid 1px red");
			goodToSave=false;
	}
	else if ( 100 < $("#designation").val().length ) { 
			alertMessage+="Designation field too long. Can be up to 100 characters.\n";
			$("#designation_label").attr("style", "border: solid 1px red");
			goodToSave=false;
	} else {
		$("#designation_label").attr("style", "border: none 0px");
	}
	if( ( $("#quick_scan").val().length > 20) && ( $(":checked[name='record_type']").val() == "formula" ) ) {
		alertMessage+="Quickscan must be 20 characters or less.\n";
		$("#quick_scan_label").attr("style", "border: solid 1px red");
		goodToSave=false;
	}
	else {
		$("#quick_scan_label").attr("style", "border: none 0px");
	}
	/* If formula, Raw Material or Packaging, make sure there's a Unit type for inventory tracking */
	if ( ( $("#inventory_units").val() == "" ) && ( $(":checked[name='record_type']").val() != "instruction" ) && ( $(":checked[name='record_type']").val() != "process" ) ) { 
		alertMessage+="Unit Type must be declared for inventory purposes.\n";
		$("#inventory_units_label").attr("style", "border: solid 1px red");
		goodToSave=false;
	}
	else {
		$("#inventory_units_label").attr("style", "border: none 0px");
	}
	return alertMessage;
	//return goodToSave;
}
</script>
<h1>Add New Product Master Record</h1>
<?php 
//echo "<h2 style=\"color:red\">Action is $action</h2>"; 
?>

<FORM ACTION="pop_add_product.php" METHOD="post">
<INPUT TYPE="hidden" id="action" NAME="action" VALUE="search">

<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD>
		Select the type of record to add:<br/>
		<div style="margin:1em 0 1em 2em; padding:.5em; border:solid 1px">
			<b><label for="radio_formula"><input id="radio_formula" name="record_type" value="formula" type="radio" <?php if ($record_type=='formula') echo "CHECKED"; ?> />Formula</label>
			<label for="radio_raw_material"><input id="radio_raw_material" name="record_type" value="raw material"type="radio"<?php if ($record_type=='raw material') echo "CHECKED"; ?> />Raw Material</label>
			<label for="radio_instruction"><input id="radio_instruction" name="record_type" value="instruction" type="radio" <?php if ($record_type=='instruction') echo "CHECKED"; ?>/>Instruction</label>
			<label for="radio_process"><input id="radio_process" name="record_type" value="process"  type="radio" <?php if ($record_type=='process') echo "CHECKED"; ?> />Process</label>
			<label for="radio_packaging"><input id="radio_packaging" name="record_type" value="packaging" type="radio" <?php if ($record_type=='packaging') echo "CHECKED"; ?> />Packaging</label></b>
		</div>
		<p>Enter characters within the product designation:</p>
		<input type="text" id="product_designation_search" name="product_designation_search" style="width:350px" <?php if ( $html_designation_search !="") echo "VALUE=\"$html_designation_search\""; ?> />
		<INPUT TYPE="submit" class="submit_medium" id="search" name="search" VALUE="View Similar Records" >
		</TD>
	</TR>

</TABLE>

	
</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR>

	<?php
	if ( $record_type != "" )  {
	?>
	<TABLE id="search_results" CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC" ><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>


	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<tbody>
	<tr><td>
<?php

		if ( $_SESSION['userTypeCookie'] == 2 ) {
			$name_clause = " AND user_id = " . $_SESSION['user_id'];
		} else {
			$name_clause = "";
		}
		switch ($record_type) {
			case 'formula':
				$sql = "SELECT ProductMaster.ProductNumberInternal, ProductMaster.Designation, ProductMaster.Natural_OR_Artificial, ProductMaster.Kosher, ExternalProductNumberReference.ProductNumberExternal, ";
				$sql .= "if( Mid( ExternalProductNumberReference.ProductNumberExternal, 1, 2 ) = 'US', ExternalProductNumberReference.ProductNumberExternal, BuildExternalSortKeyField1( ExternalProductNumberReference.ProductNumberExternal) ) AS field1, ";
				$sql .= "if( Mid( ExternalProductNumberReference.ProductNumberExternal, 4, 1 ) = 'a', 0, ExternalProductNumberReference.ProductNumberExternal ) AS Field2, ";
				$sql .= "BuildExternalSortKeyField3( ExternalProductNumberReference.ProductNumberExternal) AS Field3, ";
				$sql .= "BuildExternalSortKeyField4( ExternalProductNumberReference.ProductNumberExternal) AS Field4, ";
				$sql .= "ProductMaster.Organic ";
				$sql .= "FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal ";
				$sql .= "WHERE ( ( ( ( ProductMaster.ProductNumberInternal ) LIKE '2%' ) OR ( ( ProductMaster.ProductNumberInternal ) LIKE '5%' ) ) AND ( ( ProductMaster.Designation ) LIKE '%$designation_search%' ) ) ";
				$sql .= "ORDER BY if( Mid( ExternalProductNumberReference.ProductNumberExternal, 1, 2 ) = 'US', ExternalProductNumberReference.ProductNumberExternal, BuildExternalSortKeyField1( ExternalProductNumberReference.ProductNumberExternal ) ) , ";
				$sql .= "if( Mid( ExternalProductNumberReference.ProductNumberExternal, 4, 1 ) = 'a', 0, ExternalProductNumberReference.ProductNumberExternal ) , ";
				$sql .= "BuildExternalSortKeyField3( ExternalProductNumberReference.ProductNumberExternal), ";
				$sql .= "BuildExternalSortKeyField4( ExternalProductNumberReference.ProductNumberExternal)";
				break;
			case 'raw material':
				$sql = "SELECT ProductMaster.ProductNumberInternal, ProductMaster.Designation, ProductMaster.Natural_OR_Artificial, ProductMaster.Kosher, ExternalProductNumberReference.ProductNumberExternal, ";
				$sql .= "ProductMaster.Organic ";
				$sql .= "FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal ";
				$sql .= "WHERE ( ( ( ( ProductMaster.ProductNumberInternal ) Like '1%' ) OR ( ( ProductMaster.ProductNumberInternal) Like '5%' ) ) And ( ( ProductMaster.Designation ) Like '%$designation_search%' ) ) ";
				$sql .= "ORDER BY ProductMaster.ProductNumberInternal";
				break;
			case 'instruction': 
				$sql = "SELECT ProductMaster.ProductNumberInternal, ProductMaster.Designation, ProductMaster.Natural_OR_Artificial,ProductMaster.Kosher,ExternalProductNumberReference.ProductNumberExternal, ";
				$sql .= "If ( Mid( ExternalProductNumberReference.ProductNumberExternal, 1, 2 ) = 'US', ExternalProductNumberReference.ProductNumberExternal, BuildExternalSortKeyField1 ( ExternalProductNumberReference.ProductNumberExternal ) ) AS field1, ";
				$sql .= "If ( Mid( ExternalProductNumberReference.ProductNumberExternal, 4, 1 ) = 'a', 0, ExternalProductNumberReference.ProductNumberExternal ) AS Field2, ";
				$sql .= "BuildExternalSortKeyField3 ( ExternalProductNumberReference.ProductNumberExternal ) AS Field3, ";
				$sql .= "BuildExternalSortKeyField4 ( ExternalProductNumberReference.ProductNumberExternal ) AS Field4, ";
				$sql .= "ProductMaster.Organic ";
				$sql .= "FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal ";
				$sql .= "WHERE ( ( ( ( ProductMaster.ProductNumberInternal ) Like '4%' ) OR ( ( ProductMaster.ProductNumberInternal ) Like '5%' ) ) And ( ( ProductMaster.Designation ) Like '%$designation_search%' ) ) ";
				$sql .= "ORDER BY If ( Mid( ExternalProductNumberReference.ProductNumberExternal, 1, 2 ) = 'US', ExternalProductNumberReference.ProductNumberExternal, BuildExternalSortKeyField1 ( ExternalProductNumberReference.ProductNumberExternal ) ), ";
				$sql .= "If ( Mid( ExternalProductNumberReference.ProductNumberExternal, 4, 1 ) = 'a', 0, ExternalProductNumberReference.ProductNumberExternal ), ";
				$sql .= "BuildExternalSortKeyField3 ( ExternalProductNumberReference.ProductNumberExternal ), ";
				$sql .= "BuildExternalSortKeyField4 ( ExternalProductNumberReference.ProductNumberExternal )";
				break;
			case 'process': 
				$sql = "SELECT ProductMaster.ProductNumberInternal, ProductMaster.Designation, ProductMaster.Natural_OR_Artificial,ProductMaster.Kosher,ExternalProductNumberReference.ProductNumberExternal, ";
				$sql .= "If ( Mid( ExternalProductNumberReference.ProductNumberExternal, 1, 2 ) = 'US', ExternalProductNumberReference.ProductNumberExternal, BuildExternalSortKeyField1 ( ExternalProductNumberReference.ProductNumberExternal ) ) AS field1, ";
				$sql .= "If ( Mid( ExternalProductNumberReference.ProductNumberExternal, 4, 1 ) = 'a', 0, ExternalProductNumberReference.ProductNumberExternal ) AS Field2, ";
				$sql .= "BuildExternalSortKeyField3 ( ExternalProductNumberReference.ProductNumberExternal ) AS Field3, ";
				$sql .= "BuildExternalSortKeyField4 ( ExternalProductNumberReference.ProductNumberExternal ) AS Field4 ";
				$sql .= "FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal ";
				$sql .= "WHERE ( ( ( ( ProductMaster.ProductNumberInternal ) Like '7%' ) ) And ( ( ProductMaster.Designation ) Like '%$designation_search%' ) ) ";
				$sql .= "ORDER BY If ( Mid( ExternalProductNumberReference.ProductNumberExternal, 1, 2 ) = 'US', ExternalProductNumberReference.ProductNumberExternal, BuildExternalSortKeyField1 ( ExternalProductNumberReference.ProductNumberExternal ) ), ";
				$sql .= "If ( Mid( ExternalProductNumberReference.ProductNumberExternal, 4, 1 ) = 'a', 0, ExternalProductNumberReference.ProductNumberExternal ), ";
				$sql .= "BuildExternalSortKeyField3 ( ExternalProductNumberReference.ProductNumberExternal ), ";
				$sql .= "BuildExternalSortKeyField4 ( ExternalProductNumberReference.ProductNumberExternal )";
				break;
			case 'packaging': 
				$sql = "SELECT ProductMaster.ProductNumberInternal, ProductMaster.Designation, ProductMaster.Natural_OR_Artificial,ProductMaster.Kosher,ExternalProductNumberReference.ProductNumberExternal, ";
				$sql .= "If ( Mid( ExternalProductNumberReference.ProductNumberExternal, 1, 2 ) = 'US', ExternalProductNumberReference.ProductNumberExternal, BuildExternalSortKeyField1 ( ExternalProductNumberReference.ProductNumberExternal ) ) AS field1, ";
				$sql .= "If ( Mid( ExternalProductNumberReference.ProductNumberExternal, 4, 1 ) = 'a', 0, ExternalProductNumberReference.ProductNumberExternal ) AS Field2, ";
				$sql .= "BuildExternalSortKeyField3 ( ExternalProductNumberReference.ProductNumberExternal ) AS Field3, ";
				$sql .= "BuildExternalSortKeyField4 ( ExternalProductNumberReference.ProductNumberExternal ) AS Field4, ";
				$sql .= "ProductMaster.Organic ";
				$sql .= "FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal ";
				$sql .= "WHERE ( ( ( ( ProductMaster.ProductNumberInternal ) Like '6%' ) ) And ( ( ProductMaster.Designation ) Like '%$designation_search%' ) ) ";
				$sql .= "ORDER BY If ( Mid( ExternalProductNumberReference.ProductNumberExternal, 1, 2 ) = 'US', ExternalProductNumberReference.ProductNumberExternal, BuildExternalSortKeyField1 ( ExternalProductNumberReference.ProductNumberExternal ) ), ";
				$sql .= "If ( Mid( ExternalProductNumberReference.ProductNumberExternal, 4, 1 ) = 'a', 0, ExternalProductNumberReference.ProductNumberExternal ), ";
				$sql .= "BuildExternalSortKeyField3 ( ExternalProductNumberReference.ProductNumberExternal ), ";
				$sql .= "BuildExternalSortKeyField4 ( ExternalProductNumberReference.ProductNumberExternal )";
				break;
		}
		$result = mysql_query($sql, $link);

		if ( mysql_num_rows($result) > 0 ) {

			$bg = 0; ?>

			<div <?php if ( $record_type == "formula" ) { ?> id="tableContainerWide" class="tableContainerWide" <?php } else { ?> id="tableContainerNarrow" class="tableContainerNarrow"<?php } ?> >
			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" width="100%" class = "scrollTable">

				<thead class="fixedHeader">
				<TR VALIGN=TOP>
					<TH><B>Internal #</B></TH>
					<TH><B>Designation</B></TH>
					<TH><B>External #</B></TH>
					<TH><B>N or A</B></TH>
					<TH><B>Kosher</B></TH>
					<TH <?php if ( $record_type != "formula" ) { echo ("style=\"border-right:0px\""); } ?> ><B>Organic</B></TH>
					<?php if ( $record_type == "formula" ) { echo ("<TH style=\"border-right:0px\"><INPUT TYPE=\"submit\" id=\"clone\" class=\"submit\" name=\"clone\" VALUE=\"Clone\" STYLE=\"font-size:7pt\"/></TH>"); } ?>
				</TR>
				</thead>
				<tbody class="scrollContent">

				<?php 

				while ( $row = mysql_fetch_array($result) ) {

					if ( $bg == 1 ) {
						$rowClass = "normalRow";
						$bg = 0;
					}
					else {
						$rowClass = "alternateRow";
						$bg = 1;
					} ?>

					<TR class="<?php echo $rowClass ?>" VALIGN=TOP>
						<TD><?php echo ($row['ProductNumberInternal'] ? $row['ProductNumberInternal'] : "&nbsp;") ?></TD>
						<TD><?php echo ($row['Designation'] ? $row['Designation'] : "&nbsp;") ?></TD>
						<TD><?php echo ($row['ProductNumberExternal'] ? $row['ProductNumberExternal'] : "&nbsp;") ?></TD>
						<TD><?php echo ($row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial'] : "&nbsp;") ?></TD>
						<TD><?php echo ($row['Kosher'] ? $row['Kosher'] : "&nbsp;") ?></TD>
						<TD <?php if ( $record_type != "formula" ) { echo ("style=\"border-right:0px\""); } ?> ><?php echo ($row['Organic'] ? $row['Organic'] : "&nbsp;") ?></TD>

						<?php if ( $record_type == "formula" ) { ?>
						<TD style="border-right:0px">
							<input name="clone" value="<?php echo ($row['ProductNumberInternal'] ? $row['ProductNumberInternal'] : "&nbsp;") ?>" type="radio" />
						</TD>
						<?php } ?>

					</TR>

				<?php } ?>

				</tbody>
			</TABLE>
			</DIV>

		<?php } else {
			print("No $record_type records in database match \"$html_designation_search\".");
			// print("<div style=\"border:solid; padding:1em;\">$sql</div><div style=\"border:solid; padding:1em;\">");
			// print_r($result);
			// print("<div>");
		}
?>
	</td></tr>
	</tbody>
	</TABLE>


	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE><br/>
<?php
	}
	
If ("" != $error_message) echo "<div style=\"color:red\">$error_message</div>";
?>
	
	
	
	
	<TABLE id="new_input" CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC" <?php if ($record_type=='') echo "style=\"display:none\""; ?> ><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>


	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<tbody>
	<tr><td>
		<div class="type_formula" <?php if ($record_type!='formula') echo "style=\"display:none\""; ?> >
			<h2>New Formula Record Fields</h2>
		</div>
		<div class="type_rawMaterial" <?php if ($record_type!='raw material') echo "style=\"display:none\""; ?> >
			<h2>New Raw Material Record Fields</h2>
		</div>
		<div class="type_instruction" <?php if ($record_type!='instruction') echo "style=\"display:none\""; ?> >
			<h2>New Instruction Record Fields</h2>
		</div>
		<div class="type_process" <?php if ($record_type!='process') echo "style=\"display:none\""; ?> >
			<h2>New Process Record Fields</h2>
		</div>
		<div class="type_packaging"<?php if ($record_type!='packaging') echo "style=\"display:none\""; ?> >
			<h2>New Packaging Record Fields</h2>
		</div>
		<div class="type_formula" <?php if ($record_type!='formula') echo "style=\"display:none\""; ?> >
			<label class="w6" id="external_number_label" for="external_number">Abelei Number (External): </label><input type="text" class="input-box" id="external_number" name="external_number" style="width:200px" value="<?php echo ("" != $external_number ? $external_number: ""); ?>"/>
		</div>
		<div class="type_formula" <?php if ($record_type!='formula') echo "style=\"display:none\""; ?> >
			<label class="w6" for="intermediary">Intermediary: </label><input type="checkbox" class="input-box" id="intermediary" name="intermediary" <?php echo ("on" == $intermediary ? 'CHECKED' : ''); ?> />
		</div>
		<label class="w6" id="designation_label" for="designation">Designation: </label><input type="text" class="input-box" id="designation" name="designation" style="width:500px"  value="<?php echo ("" != $designation ? $designation : ""); ?>" /><br />
		<div class="type_formula" <?php if ($record_type!='formula') echo "style=\"display:none\""; ?> >
			<label class="w6" id="quick_scan_label" for="quick_scan">QuickScan: </label><input type="text" class="input-box" id="quick_scan" name="quick_scan" style="width:200px" value="<?php echo ("" != $quick_scan ? $quick_scan : ""); ?>" /><br />
		</div>
		<div class="type_formula type_rawMaterial" <?php if (($record_type!='formula') && ($record_type!='raw material' )) echo "style=\"display:none\""; ?> >
			<label class="w6" for="n_or_a">Natural or Artificial: </label><select class="input-box" id="n_or_a" name="n_or_a" >
			<?php 
				printNorAOptions($n_or_a);
			?>
			</select><br />
		</div>
		<div class="type_formula type_rawMaterial" <?php if (($record_type!='formula') && ($record_type!='raw material' )) echo "style=\"display:none\""; ?> >
			<label class="w6" for="product_type">Product Type: </label><select class="input-box" id="product_type" name="product_type">
			<?php 
				printProductTypeOptions($product_type);
			?>
			</select><br />
		</div>
		<div class="type_formula type_rawMaterial" <?php if (($record_type!='formula' ) && ($record_type!='raw material' )) echo "style=\"display:none\""; ?> >
			<label class="w6" for="kosher">Kosher: </label><select class="input-box" id="kosher" name="kosher">
			<?php 
				printKosherOptions($kosher);
			?>
			</select><br />
		</div>
		<div class="type_formula type_rawMaterial" <?php if (($record_type!='formula') && ($record_type!='raw material' )) echo "style=\"display:none\""; ?> >
			<label class="w6" for="organic">Organic: </label><input type="checkbox" class="input-box" id="organic" name="organic" <?php echo ("on" == $organic ? 'CHECKED' : ''); ?> />
		</div>
		
		<label class="w6" id="inventory_units_label" for="inventory_units">Inventory Units: </label><select class="input-box" id="inventory_units" name="inventory_units">
		<?php 
			printInventoryUnitsOptions($inventory_units);
		?>
		</select><br />
	<INPUT TYPE="submit" class="submit_medium" id="save" name="Save" VALUE="Add New Record" >
	<INPUT TYPE="submit" class="submit_medium" id="cancel" name="Cancel" VALUE="Close" >
	</td></tr>
	</tbody>
	</TABLE>

	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>
</FORM>
<BR><BR>


<?php include("inc_footer.php"); ?>