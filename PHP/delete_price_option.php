<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) or $_SESSION['userTypeCookie'] != 1 ) {
	header ("Location: login.php?out=1");
	exit;
}

$note="";

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');

if ( ! empty($_REQUEST) ) {
	if ( $_REQUEST['option_id'] ) { // 
		$sql =" DELETE FROM price_quote_options WHERE option_id = '".escape_data($_REQUEST['option_id'])."'";
		//echo "<br /> $sql <br />"; 
		mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
		$_SESSION['note']="The Price item  deleting process is finished";
		echo "<SCRIPT>window.history.back();</SCRIPT>";
		exit();
	}
}

?>
