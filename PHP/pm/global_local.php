<?php
global $link;
$link = mysql_connect("localhost","root","Oct!(@))9");
mysql_select_db("tgooding",$link);
$abelei_font="<B STYLE='color:red;font-family:\"Century Gothic\";'>abelei</B>";
$flavor_font="<B STYLE='color:#730099;font-family:\"Century Gothic\";'>flavors</B>";
$text_newline=array("\r\n","\n","\r");
$html_newline="<br />";

function require_ssl() {

	// FROM http://support.jodohost.com/showthread.php?t=7334
	//if( $_SERVER['SERVER_PORT'] == 80) { 
	//	header('Location:https://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/'.basename($_SERVER['PHP_SELF'])); 
	//	die(); 
	//} 

// CODE BELOW CAUSED ENDLESS LOOP OF REDIRECTS FOR SOME REASON  2/19/08

/* 	if ( !isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on' ) { */
/* 		$HTTP_HOST = $_SERVER['HTTP_HOST']; */
/* 		if (substr($HTTP_HOST,0,3)!="www") */
/* 		{ */
/* 			$HTTP_HOST = "www." . $HTTP_HOST; */
/* 		}    		 */
/* 		header ('Location: https://' . $HTTP_HOST . $_SERVER['REQUEST_URI']); */
/* 		exit(); */
/* 	} */

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
			$dataA=explode(",",$data) ;// allow multiple email addresses
			foreach ( $dataA as $data_e ) {
			$data_length = strlen($data_e);
			if ( $data_length > 70 or !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $data_e) or $data_e == "" ) {
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