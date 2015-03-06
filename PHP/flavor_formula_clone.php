<?php

$debug = 0;
if ( $debug == 0 ) {
include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB, Front Desk AND QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 and $rights != 6 ) {
	header ("Location: login.php?out=1");
	exit;
}
}

include('inc_global.php');

if ( isset($_REQUEST['pne']) )
	$productNumberExternal = $_REQUEST['pne'];
elseif ( isset($_GET['pne']) ) 
	$productNumberExternal = $_GET['pne'];

if ( $debug )
	$productNumberExternal='144a115K';
if ( strlen($productNumberExternal) <=0 ) {
	die ("The clone feature needs at least abelei# provided <br />");
}

if ( isset($_REQUEST['ProductNumberInternal']) )
	$productNumberInternal = $_REQUEST['ProductNumberInternal'];
elseif ( isset($_GET['ProductNumberInternal']) ) 
	$productNumberInternal = $_GET['ProductNumberInternal'];

if ( $debug ) {
	$productNumberInternal='211995';
}

if ( ! $productNumberInternal ) {
	die ("The clone feature needs product internal number provided <br />");
}

if (isset($_POST['action']) && ( $_POST['action'] == 1 or $_POST['action'] == "save")) {
	
include("inc_pop_header.php");

$sql = "SELECT ProductNumberInternal from externalproductnumberreference where ProductNumberExternal = '" . $productNumberExternal ."'";
$results = mysql_query($sql,$link);
$result_count = mysql_num_rows($results);
if ( $result_count != 1 ) {
	die ("The Abelei# $productNumberExternal is linked to multiple internal product numbers: $result_count <b /> $sql. The clone cannot be done<br />");
}
$row = mysql_fetch_array($results);
$clone_source = $row['ProductNumberInternal'];

$clone_like = substr($clone_source,0,1);

$sql = "SELECT Max(ProductNumberInternal) MaxOfProductNumberInternal FROM productmaster where ProductNumberInternal like '$clone_like%'";
//echo "$sql <br />";
$result = mysql_query($sql,$link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$result_count = mysql_num_rows($result);

if (0 < $result_count) { 
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$internal_number = $row['MaxOfProductNumberInternal'] + 1;
}

start_transaction($link);

$sql = "INSERT INTO tmpclone SELECT * FROM productmaster WHERE ProductNumberInternal='$clone_source'";

if ( ! mysql_query($sql, $link) ) {
	echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
	end_transaction(0,$link);
	die;
}

/* update temp table copy with new values */
$sql = "UPDATE tmpclone SET ProductNumberInternal='$internal_number', DateOfFormulation=now() ";

if ( ! mysql_query($sql, $link) ){
	echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
	end_transaction(0,$link);
	die;
}	
//end_transaction(1,$link);
/* copy from temp table to productmaster */
$sql = "INSERT INTO productmaster (SELECT distinct * FROM tmpclone WHERE ProductNumberInternal='$internal_number')";
//echo $sql."<br />";
if (! mysql_query($sql, $link) ) {
	echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
	end_transaction(0,$link);
	die;
}


/* delete from temp table */
$sql = "DELETE FROM tmpclone WHERE ProductNumberInternal='$internal_number'";
if (! mysql_query($sql, $link) ) {
	echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
	end_transaction(0,$link);
	die;
}
			/* copy ingredients to temp table */
$sql = "INSERT INTO tmpformulationclone (SELECT * FROM formulationdetail WHERE ProductNumberInternal='$clone_source')";
//echo $sql ."<br />";
if (! mysql_query($sql, $link) ) {
	echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
	end_transaction(0,$link);
	die;
}

/* update temp table copy with new values */
$sql = "UPDATE tmpformulationclone SET ProductNumberInternal='$internal_number' WHERE ProductNumberInternal='$clone_source'";
if (! mysql_query($sql, $link) ) {
	echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
	end_transaction(0,$link);
	die;
}
/* copy from temp table to formulationdetail */
$sql = "INSERT INTO formulationdetail SELECT * FROM tmpformulationclone WHERE ProductNumberInternal='$internal_number'";
//echo $sql . "<br />";
if (! mysql_query($sql, $link) ) {
	echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
	end_transaction(0,$link);
	die;
}
/* delete from temp table */
$sql = "DELETE FROM tmpformulationclone WHERE ProductNumberInternal='$internal_number'";
if (! mysql_query($sql, $link) ) {
	echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
	end_transaction(0,$link);
	die;
}

	$intermediary = ( $_POST['intermediary'] ) ? 1 : 0;

	$designation = ( strlen($_POST['designation']) >0 ) ? escape_data($_POST['designation']) : "NULL";

	$quick_scan = ( strlen($_POST['quick_scan']) >0 ) ? escape_data($_POST['quick_scan']) : "NULL";

	$product_type = ( strlen($_POST['product_type']) > 0 ) ? escape_data($_POST['product_type']) : "NULL";

	$kosher = ( strlen($_POST['kosher']) > 0 ) ? escape_data($_POST['kosher']) : "NULL";

	$organic = ( $_POST['organic'] > 0 ) ? 1 : 0;

	$inventory_units = ( strlen($_POST['inventory_units']) > 0 ) ? escape_data($_POST['inventory_units']) : "NULL";

	$naturalOrArtificial = ( strlen($_POST['n_or_a']) > 0 ) ? escape_data($_POST['n_or_a']) : "NULL";

$sql = "UPDATE productmaster set intermediary='". $intermediary ."',".
	" UnitOfMeasure='". $inventory_units ."',".
	" Organic='".$organic . "',".
	" designation='". $designation ."',".
	" QuickScan='". $quick_scan ."',".
	" ProductType='". $product_type ."',".
	" Kosher='". $kosher ."',".
	" Natural_OR_Artificial = '" . $naturalOrArtificial . "'".
	" where ProductNumberInternal='". $internal_number . "'";
//echo $sql . "<br />";
	if ( ! mysql_query($sql,$link)) {
		echo mysql_error()."<br />Couldn't execute query: $sql <br /><br />";
		end_transaction(0,$link);
		die;
	}

	if ( isset($_POST['external_number']) and strlen($_POST['external_number']) > 0 ) {
		$external_number = escape_data($_POST['external_number']);
	
		$sql = "INSERT INTO externalproductnumberreference ".
		" VALUES ( '" . $external_number . "','". $internal_number ."')";
		//echo $sql . "<br />";
		if ( ! mysql_query($sql,$link)) {
			echo mysql_error()."<br />Couldn't execute query: $sql <br /><br />";
			end_transaction(0,$link);
			die;
		}
	}
	
	end_transaction(1,$link);

	$_SESSION['internal_number'] = $internal_number;
	$_SESSION['note'] .= "Succesfully saved $designation with internal number $internal_number";
	$_SESSION['external_number'] = $external_number;
	$options="pne=$external_number";
		
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "var url = 'flavors_formulations.php' + '?action=edit&$options'\n";
		echo "window.opener.location.href=url\n";
		echo "window.close()\n";
		echo "</SCRIPT>\n";
			
} else {
	
include('search/system_defaults.php');
include("inc_pop_header.php");
?>

<script>
	var contacts="";
	$(document).ready(function(){

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
			case 'cancel':
				alert("This will cancel");
				self.close();
				break;
			default:
				alert ("this button not yet supported");
				break;
		}
	});

});

function validated() {
	var goodToSave=true;
	var alertMessage="";
	if ( ( $("#external_number").val() == "" ) || ($("#external_number").val().length > 20) ){ 
			alertMessage+="External Number cannot be blank or longer than 20 characters.\n";
			$("#external_number_label").attr("style", "border: solid 1px red");
			goodToSave=false;
	}
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
	if( ( $("#quick_scan").val().length > 50) && ( $(":checked[name='record_type']").val() == "formula" ) ) {
		alertMessage+="Quickscan must be 50 characters or less.\n";
		$("#quick_scan_label").attr("style", "border: solid 1px red");
		goodToSave=false;
	}
	else {
		$("#quick_scan_label").attr("style", "border: none 0px");
	}
	/* If formula, Raw Material or Packaging, make sure there's a Unit type for inventory tracking */
	if ( ( $("#inventory_units").val() == "" ) ) { 
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
<h4>Add New Product Master Record Clone From Abelei# <?php echo $productNumberExternal . " Internal#  : " . $productNumberInternal; ?></h4>

	<form action="flavor_formula_clone.php" method="post">
	<input type="hidden" name="action" id="action" value="1">
	<input type="hidden" name="pne" id="pne" value="<?php echo $productNumberExternal; ?>">
	<input type="hidden" name="ProductNumberInternal" id="ProductNumberInternal" value="<?php echo $productNumberInternal;?>">
	<TABLE id="new_input" CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>


	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<tbody>
	<tr> <td colspan="2">
		<h5>New Formula Record Fields</h5>
		</td>
	</tr><tr><td>
		<label class="w6" id="external_number_label" for="external_number">Abelei Number (External): </label>
		</td>
		<td>
		<input type="text" class="input-box" id="external_number" name="external_number" style="width:200px" value=""/>
		</td></tr>
	<tr><td>
		<label class="w6" for="intermediary">Intermediary: </label>
		</td><td>
		<input type="checkbox" class="input-box" id="intermediary" name="intermediary" value="1" />
		</td></tr>
	<tr><td>
		<label class="w6" id="designation_label" for="designation">Designation: </label>
	</td><td>
		<input type="text" class="input-box" id="designation" name="designation" style="width:500px"  value="" /><br />
	</td></tr>
	<tr><td>
		<label class="w6" id="quick_scan_label" for="quick_scan">QuickScan: </label>
		</td><td>
		<input type="text" class="input-box" id="quick_scan" name="quick_scan" maxlength="45" style="width:310px" value="" /><br />
		</td></tr>
	<tr><td>
		<label class="w6" for="n_or_a">Natural or Artificial: </label>
		</td><td>
		<select class="input-box" id="n_or_a" name="n_or_a" >
			<?php 
				$n_or_a = "";
				printNorAOptions($n_or_a);
			?>
			</select>
		</td></tr>
		<tr><td>
			<label class="w6" for="product_type">Product Type: </label>
			</td><td>
			<select class="input-box" id="product_type" name="product_type">
			<?php 
				$product_type = "";
				printProductTypeOptions($product_type);
			?>
			</select><br />
			</td></tr>
		<tr><td>
			<label class="w6" for="kosher">Kosher: </label>
			</td><td>
			<select class="input-box" id="kosher" name="kosher">
			<?php 
				$kosher="";
				printKosherOptions($kosher);
			?>
			</select>
			</td></tr>
		<tr><td>

			<label class="w6" for="organic">Organic: </label>
			</td><td>
			<input type="checkbox" class="input-box" id="organic" name="organic" value="1" />
			</td></tr>
			<tr><td>
		<label class="w6" id="inventory_units_label" for="inventory_units">Inventory Units: </label>
		</td><td>
		<select class="input-box" id="inventory_units" name="inventory_units">
		<?php 
			$inventory_units = "";
			printInventoryUnitsOptions($inventory_units);
		?>
		</select>
		</td></tr>
		<tr><td colspan="2"><NOBR>
		
	<INPUT TYPE="submit" class="submit_medium" id="save" name="save" VALUE="Add New Record" STYLE="font-size:7pt">
	<INPUT TYPE="submit" class="submit_medium" id="cancel" name="Cancel" VALUE="Close" STYLE="font-size:7pt">
	</NOBR>
	</td></tr>
	</tbody>
	</TABLE>

	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</form>
	
<?php } include("inc_footer.php"); ?>