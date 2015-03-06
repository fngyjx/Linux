<?php

$link = mysql_connect("localhost","moconnell","zW0Lv5_090");
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

?>