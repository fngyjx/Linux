<?php
global $link;
$link = mysql_connect("localhost","root","addga0");
// $link = mysql_connect("h50mysql51.secureserver.net","tgooding","Abelei_1410");
mysql_select_db("tgooding",$link);
date_default_timezone_set('America/Chicago'); //won't work on hosting
$abelei_font = "<B STYLE='color:red;font-family:\"Century Gothic\";'>abelei</B>";
$flavor_font = "<B STYLE='color:black;font-family:\"Century Gothic\";'>flavors</B>";
$text_newline = array("\r\n","\n","\r");
$html_newline = "<br />";

$project_type_array = array("New","Revision","Resample","Other");
$project_type_num = array(1,2,3,4);

$priority_array = array("Low","Medium","High");
$priority_num = array(1,2,3);

$status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
$status_num = array(1,2,3,4,5);

$n_a1_array = array("Org-Cert","Org-Comp","Natural","WONF","NI","N&A","Art","WholeFood");
$n_a1_num = array(1,2,3,4,5,6,7,8);

$n_a2_array = array(""," Or Higher","Org-Cert","Org-Comp","Natural","WONF","NI","N&A","Art","WholeFood");
$n_a2_num = array(1,2,3,4,5,6,7,8,9,10);

$form_array = array("Liquid","Powder","Emulsion","Other");
$form_num = array(1,2,3,4);

$product_type_array = array("W.S.","O.S.","Plated","S.D.");
$product_type_num = array(1,2,3,4);

$kosher_array = array("No","Pareve","Dairy","Passover");
$kosher_num = array(1,2,3,4);

$halal_array = array("No","Yes","DNA");
$halal_num = array(1,2,3);

$sample_size_array = array("1 oz.","2 oz.","4 oz.","8 oz.","Other");
$sample_size_num = array(1,2,3,4,5);

$base_included_array = array("Yes","No","En route");
$base_included_num = array(1,2,3);

$target_included_array = array("Yes","No","En route");
$target_included_num = array(1,2,3);

$target_rmc_array = array("DNA","Forthcoming");
$target_rmc_num = array(1,2);

$cost_in_use_measure_array = array("lb.","gallon","kilo");
$cost_in_use_measure_num = array(1,2,3);

$suggested_level_array = array("Use as desired","Same as target","Other");
$suggested_level_num = array(1,2,3);

$annual_potential_array = array("Low","Medium","High");
$annual_potential_num = array(1,2,3);

$application_array = array("Baked Good","Beverage","Cereal","Confection","Dairy Product","Pet Food","Nutraceutical","Pharmaceutical","Prepared Food","Snack");
$application_num = array(1,2,3,4,5,6,7,8,9,10);

$shipper_array = array("UPS","FedEx","DHL","USPS","Other");
$shipper_num = array(1,2,3,4,5);

$shipping_array = array("Next day","2nd day","Ground ","Date appropriate carrier");
$shipping_num = array(1,2,3,4);


$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

function require_ssl() {
//	if( $_SERVER['SERVER_PORT'] == 80) { 
//		header('Location:https://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/'.basename($_SERVER['PHP_SELF'])); 
//		die(); 
//	} 
}

function check_field ($data, $case, $field) {

	global $error_found, $error_message;

	$data = trim($data);

	switch($case) {

		case 1:
			// TEXT FIELD
			if ( $data != "" ) {
				return true;
			} else {
				$error_found = true;
				$error_message .= "Please enter a value for '" . $field . "'<BR>";
				return false;
			}
			break;

		case 2:
			// E-MAIL ADDRESS
			$dataA=explode(array(",",";"),$data) ;// allow multiple email addresses
			foreach ( $dataA as $data_e ) {
			$data_length = strlen($data);
			if ( $data_length > 70 or !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $data) or $data == "" ) {
				$error_found = true;
				$error_message .= "Invalid value entered for '" . $field . "'<BR>";
				return false;
			} 
			}
			return true;
			break;

		case 3:
			// NUMBERS, CURRENCY
			if ( !is_numeric($data) ) {
				$error_found = true;
				$error_message .= "Invalid value entered for '" . $field . "'<BR>";
				return false;
			} else {
				return true;
			}
			break;
	
	}

}



function escape_data ($data) {
	global $link;
	if ( get_magic_quotes_gpc() ) {
		$data = stripslashes($data);
	}
	return mysql_real_escape_string ( trim($data), $link );
}

?>
