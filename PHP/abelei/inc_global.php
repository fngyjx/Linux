<?php

$link = mysql_connect("localhost","abelei","abelei");
mysql_select_db("abelei",$link);

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
			$data_length = strlen($data);
			if ( $data_length > 70 or !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $data) or $data == "" ) {
				$error_found = true;
				$error_message .= "Invalid value entered for '" . $field . "'<BR>";
				return false;
			} else {
				return true;
			}
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
function getFormSafe($text) {
		//$text = str_replace("\'", "'", $text);
		return stripslashes($text);
	}
function formatTxt($str){
	$stripAmp=str_replace ("&","&amp;", $str);
	$stripApos=str_replace ("'","&#x27;", $stripAmp);
	$stripQuote=str_replace ("\"","&quot;", $stripApos);
	$stripLessThan=str_replace ("<","&lt;", $stripQuote);
	$stripGreaterThan=str_replace (">","&gt;", $stripLessThan);
	$stripForwardSlash=str_replace ("/","&#x2F;", $stripGreaterThan);
	return $stripForwardSlash;
}
function QuantityConvert($quantity, $units_from, $units_to) {
//	echo "input-".$quantity."-".$units_from."-".$units_to."<br/>";
	if (is_numeric($quantity) && ("lbs"==$units_from || "kg"==$units_from || "grams"==$units_from ) && ( "lbs"==$units_to || "kg"==$units_to || "grams"==$units_to)) {
		switch ($units_from) {
		case "lbs": 
			if ("grams"==$units_to) {
				return ($quantity * 453.59237);
			} else if ("kg"==$units_to) {
				return ($quantity * .45359237);
			} else { 
				return $quantity;
			}
			break;
		case "grams" : 
			if ("lbs"==$units_to) {
				return ($quantity / 453.59237);
			} else if ("kg"==$units_to) {
				return ($quantity / 1000);
			} else {
				return $quantity;
			}
			break;
		case "kg": 
			if ("grams"==$units_to) {
				return ($quantity * 1000);
			} else if ("lbs"==$units_to) {
				return ($quantity / .45359237);
			} else {
				return $quantity;
			}
			break;
		}
	}
	return null;
}
?>