<?php

if(!isset($_SESSION)) { session_start(); }  

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	//header ("Location: login.php?out=1");
	exit;
}

function printNorAOptions($active_value = "") {
	printGenericSystemDefaults("Natural_Or_Artificial", $active_value);
}
function printProductTypeOptions($active_value = "") {
	printGenericSystemDefaults("Product Type", $active_value);
}
function printKosherOptions($active_value=  "") {
	printGenericSystemDefaults("Kosher", $active_value);
}
function printInventoryUnitsOptions($active_value = "") {
	printGenericSystemDefaults("UnitsOfMeasure", $active_value);
}
function printInternalInventoryUnitsOptions($pni, $active_value = "") {
	switch (substr($pni,0,1)) {
		case "1":
		case "2":
			echo "<option></option>";
			echo"<option value=\"grams\"" . ( 0 == strcasecmp($active_value, 'grams') ? 'SELECTED' : '') . " >grams</option>";
			echo"<option value=\"kg\"" . ( 0 == strcasecmp($active_value, 'kg') ? 'SELECTED' : '') . " >kg</option>";
			echo"<option value=\"lbs\"" . ( 0 == strcasecmp($active_value, 'lbs') ? 'SELECTED' : '') . " >lbs</option>";
			break;
		case "4" :
		case "6":
		case "7" :
			echo"<option value=\"N/A\" SELECTED >N/A</option>";
			break;
	}
}
function printShipmentConditionOptions($active_value = "") {
	printGenericSystemDefaults("Condition of Shipment", $active_value);
}
function printPackageTypeOptions($active_value = "") {
	printGenericSystemDefaults("Package Types", $active_value);
}
function printVendorPackagingTypeOptions($active_value = "") {
	printGenericSystemDefaults("Vendor Packaging Types", $active_value);
}
function printStorageLocationOptions($active_value = "") {
	global $link;
	$sql="SELECT tblsystemdefaultsdetail.ItemDescription, tblsystemdefaultsdetail.Location_On_Site ";
	$sql.="FROM tblsystemdefaultsdetail ";
	$sql.="INNER JOIN tblsystemdefaultsmaster ON tblsystemdefaultsdetail.ItemID = tblsystemdefaultsmaster.ItemID ";
	$sql.="WHERE tblsystemdefaultsmaster.itemDescription = 'Storage Location' ";
	$sql.="ORDER BY `tblsystemdefaultsdetail`.`Sequence` ASC";
	$result = mysql_query($sql, $link) or die (mysql_error());
	$result_count = mysql_num_rows($result);
	echo "<option></option>";
	if (0 < $result_count)
	{
		while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
			echo"<option value=\"" . $row["ItemDescription"] . "\"" . ( 0 == strcasecmp($active_value, $row["ItemDescription"]) ? 'SELECTED' : '') . " >".$row["ItemDescription"] . " (" . ( 1 == $row["Location_On_Site"] ? 'On Site' : 'Off Site' ) . ")</option>";
		}
	}
}

function printGenericSystemDefaults($default_type, $active_value = "") {
	global $link;
	$sql="SELECT tblsystemdefaultsdetail.ItemDescription ";
	$sql.="FROM tblsystemdefaultsdetail ";
	$sql.="INNER JOIN tblsystemdefaultsmaster ON tblsystemdefaultsdetail.ItemID = tblsystemdefaultsmaster.ItemID ";
	$sql.="WHERE tblsystemdefaultsmaster.itemDescription = '$default_type' ";
	$sql.="ORDER BY `tblsystemdefaultsdetail`.`Sequence` ASC";
	$result = mysql_query($sql, $link) or die (mysql_error());
	$result_count = mysql_num_rows($result);
	echo "<option></option>";
	if (0 < $result_count)
	{
		while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
			echo"<option value=\"" . $row["ItemDescription"] . "\"" . ( 0 == strcasecmp($active_value, $row["ItemDescription"]) ? 'SELECTED' : '') . " >".$row["ItemDescription"]."</option>";
		}
	}
}

function printEmployeeOptions($active_value = "") {
	global $link;
	$sql="SELECT user_id, first_name, last_name ";
	$sql.="FROM users ";
	$sql.="WHERE active=1 AND locked=0 ";
	$sql.="ORDER BY last_name, first_name ASC";
	$result = mysql_query($sql, $link) or die (mysql_error());
	$result_count = mysql_num_rows($result);
	echo "<option></option>";
	if (0 < $result_count)
	{
		while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
			echo"<option value=\"" . $row["user_id"] . "\"" . ($active_value==$row["user_id"] ? 'SELECTED' : '') . " >".$row["first_name"]." ".$row["last_name"]."</option>";
		}
	}
}

?>