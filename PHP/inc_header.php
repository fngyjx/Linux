<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<HEAD>
<TITLE> abelei </TITLE>

<LINK HREF="styles.css" REL="stylesheet" TYPE="text/css">
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">

<?php
$userTypeCookie = isset($_SESSION['userTypeCookie']) ? $_SESSION['userTypeCookie'] : "";
$rights = $userTypeCookie;
$topmenuwidth=10;

// ARRAY OF ARRAY NAMES LISTED BELOW
// ARRAYS FOR ROLLOVER IMAGES AND PAGE NAMES
$arrays=array();
$main_rolls=array();
switch ($rights) {
	case 1: // ADMIN   //"front_desk" => "Front Desk", 
		$arrays = array("project_management" => "Project Managment", "flavors" => "Flavors and Materials", "customers" => "Customers", "vendors" => "Vendors", "inventory" => "Inventory", "admin" => "Admin", "reports" => "Reports");   //"front_desk", 
		$main_rolls = array("project_management", "flavors", "customers", "vendors", "inventory", "reports", "admin", "logout");
		$front_desk_rolls = array("front_desk_labels" => "Labels", "front_desk_bill_of_lading"=>"Bills of Lading", "front_desk_truck_quote"=>"Truck Quote", "front_desk_packing_list"=>"Packing List", "front_desk_supplemental_documents"=>"Supplemental Documents");
		$project_management_rolls = array("project_management_admin"=>"Admin", "project_management_projects"=>"Projects", "project_management_completed"=>"Completed");
		$flavors_rolls = array("flavors_beverage_formulations"=>"Beverages","flavors_formulations"=>"Formulations", "flavors_materials"=>"Materials", "flavors_materials_pricing"=>"Materials Pricing", "flavors_qc_formulas"=>"QC Formulas", "flavors_regulatory_reports"=>"Regulatory Reports");
		$customers_rolls = array("customers_quotes"=>"Quotes", "customers_multiple_quotes"=>"Multiple Quotes", "customers_customer_order_shipping"=>"Orders/Shipping", "customers_batch_sheets"=>"Batch Sheets", "customers_customers"=>"Customers", "customers_contacts"=>"Contacts","customers_beverage"=>"Beverage");
		$customers_beverage_rolls = array("customers_beverage_quotes"=>"Quotes", "customers_beverage_multiple_quotes"=>"Multiple Quotes", "customers_beverage_customer_order_shipping"=>"Orders/Shipping", "customers_beverage_batch_sheets"=>"Batch Sheets", "customers_beverage_customers"=>"Customers", "customers_beverage_contacts"=>"Contacts","customers_ingredient"=>"Ingredient");
		$vendors_rolls = array("vendors_pos"=>"POs", "vendors_receipts"=>"Receipts", "vendors_vendors"=>"Vendors", "vendors_contacts"=>"Contacts","vendors_products"=>"Products","vendors_beverage"=>"Beverage");
		$vendors_beverage_rolls = array("vendors_beverage_pos"=>"POs", "vendors_beverage_receipts"=>"Receipts", "vendors_beverage_vendors"=>"Vendors", "vendors_beverage_contacts"=>"Contacts","vendors_beverage_products"=>"Products","vendors_ingredient"=>"Ingredient");
		$inventory_rolls = array("inventory_inventory_maintenance"=>"Inventory Maintenance");
		$reports_rolls = array("reports_management_reports"=>"Management Reports", "reports_regulatory_reports"=>"Regulatory Reports","reports_inventory_reports"=>"Inventory Reports");
		$admin_rolls = array("admin_users"=>"Users","admin_quote_price"=>"Quote Price");
		$topmenuwidth="634";
		break;
	case 2: // SALES
		$arrays = array("project_management" => "Project Managment", "customers" => "Customers", "vendors" => "Vendors");
		$main_rolls = array("project_management","customers", "logout");
		$project_management_rolls = array("project_management_admin"=>"Admin", "project_management_projects"=>"Projects", "project_management_completed"=>"Completed");
		$customers_rolls = array("customers_customers"=>"Customers", "customers_contacts"=>"Contacts","customers_beverage"=>"Beverage");
		$customers_beverage_rolls = array("customers_beverage_customers"=>"Customers", "customers_beverage_contacts"=>"Contacts","customers_ingredient"=>"Ingredient");
		$topmenuwidth="300";
		break;
	case 3: // LAB
		$arrays = array("project_management" => "Project Managment", "flavors" => "Flavors and Materials", "vendors" => "Vendors", "inventory" => "Inventory", "reports" => "Reports");
		$main_rolls = array("project_management", "flavors", "vendors", "logout");
		$project_management_rolls = array("project_management_admin"=>"Admin", "project_management_completed"=>"Completed");
		$flavors_rolls = array("flavors_beverage_formulations"=>"Beverages","flavors_formulations"=>"Formulations", "flavors_materials"=>"Materials", "flavors_materials_pricing"=>"Materials Pricing", "flavors_qc_formulas"=>"QC Formulas", "flavors_regulatory_reports"=>"Regulatory Reports");
		$vendors_rolls = array("vendors_vendors"=>"Vendors", "vendors_contacts"=>"Contacts","vendors_beverage"=>"Beverage");
		$vendors_beverage_rolls = array("vendors_beverage_vendors"=>"Vendors", "vendors_beverage_contacts"=>"Contacts","vendors_ingredient"=>"Ingredient");
		$topmenuwidth="334";
		break;
	case 4: // FRONT DESK   //"front_desk" => "Front Desk", 
		$arrays = array("project_management" => "Project Managment", "flavors" => "Flavors and Materials", "customers" => "Customers", "reports" => "Reports");   //"front_desk", 
		$main_rolls = array("project_management", "flavors", "customers", "reports", "logout");
		$front_desk_rolls = array("front_desk_labels" => "Labels", "front_desk_bill_of_lading"=>"Bills of Lading", "front_desk_truck_quote"=>"Truck Quote", "front_desk_packing_list"=>"Packing List", "front_desk_supplemental_documents"=>"Supplemental Documents");
		$project_management_rolls = array("project_management_admin"=>"Admin", "project_management_completed"=>"Completed");
		$flavors_rolls = array("flavors_beverage_formulations"=>"Beverages","flavors_formulations"=>"Formulations", "flavors_materials_pricing"=>"Materials Pricing");
		$customers_rolls = array("customers_customer_order_shipping"=>"Orders/Shipping", "customers_customers"=>"Customers", "customers_contacts"=>"Contacts","customers_beverage"=>"Beverage");
		$customers_beverage_rolls = array("customers_beverage_customer_order_shipping"=>"Orders/Shipping", "customers_beverage_customers"=>"Customers", "customers_beverage_contacts"=>"Contacts","customers_ingredient"=>"Ingredient");
		$reports_rolls = array("reports_regulatory_reports"=>"Regulatory Reports");
		$topmenuwidth="534";
		break;
	case 5: // QUALITY CONTROL
		$arrays = array("project_management" => "Project Managment", "flavors" => "Flavors and Materials", "vendors" => "Vendors", "inventory" => "Inventory", "reports" => "Reports");
		$main_rolls = array("project_management", "flavors", "vendors", "inventory", "logout");
		$project_management_rolls = array("project_management_admin"=>"Admin", "project_management_completed"=>"Completed");
		$flavors_rolls = array("flavors_beverage_formulations"=>"Beverages","flavors_formulations"=>"Formulations", "flavors_materials"=>"Materials", "flavors_materials_pricing"=>"Materials Pricing", "flavors_qc_formulas"=>"QC Formulas", "flavors_regulatory_reports"=>"Regulatory Reports");
		$vendors_rolls = array("vendors_pos"=>"POs", "vendors_receipts"=>"Receipts", "vendors_vendors"=>"Vendors", "vendors_contacts"=>"Contacts","vendors_beverage"=>"Beverage");
		$vendors_beverage_rolls = array("vendors_beverage_pos"=>"POs", "vendors_beverage_receipts"=>"Receipts", "vendors_beverage_vendors"=>"Vendors", "vendors_beverage_contacts"=>"Contacts","vendors_ingredient"=>"Ingredient");
		$inventory_rolls = array("inventory_inventory_maintenance"=>"Inventory Maintenance");
		$topmenuwidth="334";
		break;
	case 6: // LAB + LIMITTED QC
		$arrays = array("project_management" => "Project Managment", "flavors" => "Flavors and Materials", "customers" => "Customers");
		$main_rolls = array("project_management", "flavors", "customers", "logout");
		$project_management_rolls = array("project_management_admin"=>"Admin", "project_management_completed"=>"Completed");
		$flavors_rolls = array("flavors_beverage_formulations"=>"Beverages","flavors_formulations"=>"Formulations", "flavors_materials"=>"Materials", "flavors_materials_pricing"=>"Materials Pricing", "flavors_qc_formulas"=>"QC Formulas", "flavors_regulatory_reports"=>"Regulatory Reports");
		$customers_rolls = array("customers_batch_sheets"=>"Batch Sheets","customers_beverage"=>"Beverage");
		$customers_beverage_rolls = array("customers_beverage_batch_sheets"=>"Batch Sheets", "customers_ingredient"=>"Ingredient");
		$topmenuwidth="334";
		break;
}

$dates = array("1","2","3","4","5","6","7","8","9","10","11","12","15","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");
$dates_zero = array("01","02","03","04","05","06","07","08","09","10","11","12","15","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");
$months_numbers = array("01","02","03","04","05","06","07","08","09","10","11","12");
$months_names =  array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

$page_title="";
?>


<SCRIPT TYPE="text/javascript" LANGUAGE="JavaScript">
   <!-- Hide

	<?php

		// CREATE TOP NAV IMAGE STATES IN JAVASCRIPT 
		foreach ( $main_rolls as $value ) {
			echo $value . "Out = new Image\n";
			echo $value . "Out.src = \"images/menu/" . $value . "_out.gif\";\n";
			echo $value . "Over = new Image\n";
			echo $value . "Over.src = \"images/menu/" . $value . "_over.gif\";\n";
			echo $value . "Active = new Image\n";
			echo $value . "Active.src = \"images/menu/" . $value . "_hover.gif\";\n\n";
		}

		// CREATE SUB NAV IMAGE STATES IN JAVASCRIPT 
		foreach ( $arrays as $value => $title) {
			$character_count = strlen($value);
			$beginning_string = substr(str_replace(".comments", "", str_replace(".rmc_management", "", str_replace(".rmc_configuration", "", str_replace(".pricing", "", str_replace(".email", "", str_replace(".header", "", str_replace(".sample", "", str_replace(".client", "", str_replace(".sales", "", str_replace(".edit", "", str_replace(".php", "", basename($_SERVER['PHP_SELF'])))))))))))), 0, $character_count);
			if ( $beginning_string == $value ) {
				foreach ( ${$value . "_rolls"} as $roll => $section) {
					echo $roll . "Out = new Image;\n";
					echo $roll . "Out.src = \"images/submenu/" . $roll . "_out.gif\";\n";
					echo $roll . "Over = new Image\n";
					echo $roll . "Over.src = \"images/submenu/" . $roll . "_over.gif\";\n";
					echo $roll . "Hover = new Image\n";
					echo $roll . "Hover.src = \"images/submenu/" . $roll . "_hover.gif\";\n\n";
				}
			}
		}

	?>

	function openWin( windowURL, windowName, windowFeatures ) { 
		return window.open( windowURL, windowName, windowFeatures ) ; 
	}

   // End -->
</SCRIPT>
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
<link type="text/css" href="js/custom-theme/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
<script type="text/javascript" language="javascript" src="js/autocomplete/jquery.autocomplete.js"></script>
<link rel="stylesheet" href="js/autocomplete/jquery.autocomplete.css" type="text/css" />
<script type="text/javascript" language="javascript" src="js/helpers.js"></script>
<script type="text/javascript" language="javascript" src="js/editable_dropdown.js"></script>

<script type="text/javascript">
<!--
function popup(url, width, height, left, top) {
	if (width === undefined) {
		width = 820;
	}
	if (height === undefined) {
		height = 680;
	}
	if (left === undefined) {
		left  = (screen.width  - width)/2;
	}
	if (top === undefined) {
		top  = (screen.height - height)/2;
	}
	var params = 'width='+width+', height='+height;
	params += ', top='+top+', left='+left;
	params += ', directories=no';
	params += ', location=no';
	params += ', menubar=no';
	params += ', resizable=yes';
	params += ', scrollbars=yes';
	params += ', status=no';
	params += ', toolbar=no';
	var newwin=window.open(url,'_blank', params);
	if (window.focus) {newwin.focus()}
	return false;
}

function change_price(pitem, pni, vid,tier, pplb) {
//signatures: productnumberinternal,vendorid,tier, priceperpound, pitem - pitem is used for elementID
	document.getElementById("price_perpound_"+pitem).style.visibility="visible";
	document.getElementById("price_"+pitem).innerHTML="";
	document.getElementById("update_price_"+pitem).value=pni+"_"+vid+"_"+tier+"_"+pplb+"_"+pitem;
	document.getElementById("submit_price_change").style.visibility="visible";
	document.getElementById("cancel_price_change").style.visibility="visible";
	


}
// -->
</script>

</HEAD>

<BODY>



<!-- TOP MENU -->

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%"><TR><TD BACKGROUND="images/backer.gif">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="800">
	<TR>
		<TD><A HREF="index.php"><IMG SRC="images/abelei_logo.gif" ALT="abelei logo" WIDTH="166" HEIGHT="67" BORDER="0"></A></TD>
		<TD><IMG SRC="images/solid_green_header_bar.gif" ALT="Blank" WIDTH="634" HEIGHT="43" BORDER="0"><BR>

<TABLE WIDTH=<?php echo $topmenuwidth ?> BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>

		<?php
		if ( isset($_SESSION["uLoggedInCookie"]) ) {
			foreach ( $main_rolls as $value ) {
				$character_count = strlen($value);
				$beginning_string = substr(str_replace(".php", "", basename($_SERVER['PHP_SELF'])), 0, $character_count);
				if ( $beginning_string == $value ) {
					$roll_state = "Over";
					$header_name = $value;
				} else {
					$roll_state = "Out";
				}

			//	if ( $value == "admin" ) {
			//		$url = "admin_users.php";
			//	} else {
					$url = $value . ".php";
			//	}

				//$url = $value . ".php";
				echo "<TD><A onFocus='if(this.blur)this.blur()'\n";
				echo "onMouseOver='" . $value . ".src=" . $value . "Active.src'\n";
				echo "onMouseOut='" . $value . ".src=" . $value . $roll_state . ".src'\n";
				echo "HREF='" . $url . "'><IMG SRC='images/menu/" . $value . "_" . strtolower($roll_state) . ".gif' BORDER=0 NAME='" . $value . "' ALT='" . $value . "'></A></TD>\n";
			}
		}
		?>

		<TD><IMG SRC="images/menu/divider.gif" WIDTH=2 HEIGHT=24 ALT="Blank"></TD>
		<TD><IMG SRC="images/menu/green_bar.gif" WIDTH=12 HEIGHT=24 ALT="Blank"></TD>
	</TR>
</TABLE>

		</TD>
	</TR>
</TABLE>
</TD></TR></TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
	<TR>
		<TD BACKGROUND="images/light_purple_rule.gif"><IMG SRC="images/light_purple_rule.gif" ALT="Purple rule" WIDTH="800" HEIGHT="2"></TD>
	</TR>
</TABLE>

<!-- TOP MENU -->



<!-- SUBMENUS WRITTEN OUT BASED ON PAGE NAME -->

<?php

if ( isset($_SESSION["uLoggedInCookie"]) ) {

	foreach ( $arrays as $value => $title) {

		$character_count = strlen($value);
		$beginning_string = substr(str_replace(".comments", "", str_replace(".rmc_management", "", str_replace(".rmc_configuration", "", str_replace(".pricing", "", str_replace(".email", "", str_replace(".header", "", str_replace(".sample", "", str_replace(".client", "", str_replace(".sales", "", str_replace(".edit", "", str_replace(".php", "", basename($_SERVER['PHP_SELF'])))))))))))), 0, $character_count);

		if ( $beginning_string == $value ) {

			echo "<!-- " . strtoupper($value) . " MENU -->\n\n";

			echo "<TABLE BORDER='0' CELLSPACING='0' CELLPADDING='0' WIDTH='100%'><TR><TD BACKGROUND='images/submenu/flavors_right_blank.gif'>\n";
			echo "<TABLE BORDER='0' CELLPADDING='0' CELLSPACING='0'>\n";
			echo "<TR>\n";
			echo "<TD><IMG SRC='images/submenu/flavors_left_blank.gif' WIDTH=164 HEIGHT=24 ALT='Blank'></TD>\n";

			foreach ( ${$value . "_rolls"} as $roll => $section ) {

				if ( str_replace(".comments", "", str_replace(".rmc_management", "", str_replace(".rmc_configuration", "", str_replace(".pricing", "", str_replace(".sample", "", str_replace(".email", "", str_replace(".header", "", str_replace(".client", "", str_replace(".sales", "", str_replace(".edit", "", str_replace(".php", "", basename($_SERVER['PHP_SELF'])))))))))))) == $roll ) {
					$roll_state = "Hover";
					//$section = ucwords(trim(str_replace("_"," ",str_replace($value,"",$roll))));
					$page_title = "<span class=\"main\">$title</span> &raquo; <span class=\"section\">".$section."</span>";
					if ( $section == "Beverages" ) {
						$page_title = "<span class=\"main\">Beverage Formula</span> &raquo; <span class=\"section\">".$section."</span>";
					}
					
				} else {
					$roll_state = "Out";
				}

				if ( $roll == "reports_regulatory_reports" ) {
					$url = "flavors_regulatory_reports.php";
				} else if ( $roll == "vendors_products" ) {
					$url = "flavors_materials_pricing.php";
				}  else {
					$url = $roll . ".php";
				}

				//$url = $roll . ".php";
				echo "<TD><A onFocus='if(this.blur)this.blur()'\n";
				echo "onMouseOver='" . $roll . ".src=" . $roll . "Over.src'\n";
				echo "onMouseOut='" . $roll . ".src=" . $roll . $roll_state . ".src'\n";
				echo "HREF='" . $url . "'><IMG SRC='images/submenu/" . $roll . "_" . strtolower($roll_state) . ".gif' BORDER=0 NAME='" . $roll . "' ALT='" . $roll . "'></A></TD>\n";

				// TWO-LINED MENU SO CLOSE ONE TABLE AND START ANOTHER
				if ( $roll == "inventory_order_tracking" ) {
					echo "<TD><IMG SRC='images/submenu/divider.gif' WIDTH=2 HEIGHT=24 ALT='Blank'></TD>\n";
					echo "</TR>\n";
					echo "</TABLE>\n";
					echo "</TD></TR></TABLE>\n";
					echo "<TABLE BORDER='0' CELLSPACING='0' CELLPADDING='0' WIDTH='100%'>\n";
					echo "<TR>\n";
					echo "<TD WIDTH=164><IMG SRC='images/submenu/flavors_left_blank.gif' WIDTH=164 HEIGHT=2 	ALT='Blank'></TD>\n";
					echo "<TD BACKGROUND='images/submenu/inventory_menu_rule.gif'><IMG SRC='images/submenu/inventory_menu_rule.gif' ALT='Purple rule' WIDTH='800' HEIGHT='2'></TD>\n";
					echo "</TR>\n";
					echo "</TABLE>\n";
					echo "<TABLE BORDER='0' CELLSPACING='0' CELLPADDING='0' WIDTH='100%'><TR><TD BACKGROUND='images/submenu/flavors_right_blank.gif'>\n";
					echo "<TABLE BORDER='0' CELLPADDING='0' CELLSPACING='0'>\n";
					echo "<TR>\n";
					echo "<TD><IMG SRC='images/submenu/flavors_left_blank.gif' WIDTH=164 HEIGHT=24 ALT='Blank'></TD>\n";
				}

			}

			echo "<TD><IMG SRC='images/submenu/divider.gif' WIDTH=2 HEIGHT=24 ALT='Blank'></TD>\n";
			echo "</TR>\n";
			echo "</TABLE>\n";
			echo "</TD></TR></TABLE>\n";

			echo "<TABLE BORDER='0' CELLSPACING='0' CELLPADDING='0' WIDTH='100%'>\n";
			echo "<TR>\n";
			echo "<TD BACKGROUND='images/light_purple_rule.gif'><IMG SRC='images/light_purple_rule.gif' ALT='Purple rule' WIDTH='800' HEIGHT='2'></TD>\n";
			echo "</TR>\n";
			echo "</TABLE>\n";

			echo "\n<!-- " . strtoupper($value) . " MENU -->\n\n";

		}
	}
}

?>

<!-- SUBMENUS WRITTEN OUT BASED ON PAGE NAME -->



<BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="800">
	<TR>
		<TD WIDTH="20"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="20" HEIGHT="1" BORDER="0"></TD>
		<TD WIDTH="780"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="780" HEIGHT="1" BORDER="0">
		<?php echo $page_title ?><BR>


