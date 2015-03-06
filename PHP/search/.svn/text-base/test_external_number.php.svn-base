<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	//header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('../inc_global.php');
if (!isset($_REQUEST["external_number"]))
{
	echo "ERROR";
}
else
{
	$q = $_REQUEST["external_number"];

	$sql = "SELECT ProductNumberExternal FROM ExternalProductNumberReference WHERE ProductNumberExternal='$q'";
	$result = mysql_query($sql, $link);
	$result_count = mysql_num_rows($result);
	if (0 < $result_count)
	{
		echo "exists";
	}
	else
	{
		echo "does not exist";
	}
}
?>