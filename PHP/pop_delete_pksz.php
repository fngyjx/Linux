<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 6 ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');

$error_found="";

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( isset($_SESSION[error_message]) ) {
	$error_message = $_SESSION[error_message];
	$error_found=true;
	unset($_SESSION['error_message']);
}

$pkszid=isset($_REQUEST['pkszid'])? $_REQUEST['pkszid'] : '';

if ($pkszid == "" )  {
	$_SESSION['error_message'] = "Packaging size id is required for deleting the entry";
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
	exit();
} 

$sql = "SELECT DefaultPksz FROM productpacksize WHERE id=$pkszid";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
$row=mysql_fetch_array($result);
$default=$row[0];
if ( $default != 0 ) {
	$note .= "Warning: The deleted packaging size is the one in default";
}
$sql = "DELETE FROM productpacksize WHERE id=$pkszid";
echo "<br /> $sql <br />";
mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL: $sql <br />");
$note .= "The Packaging ID $pkszid was deleted";
	
$_SESSION['note'] = $note;
echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
echo "window.history.back()\n";
echo "//window.close()\n";
echo "</SCRIPT>\n";
exit();
?>
