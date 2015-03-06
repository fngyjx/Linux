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
	$_SESSION['error_message'] = "Packaging size id is required ";
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
	exit();
} 

$sql = "SELECT ProductNumberInternal FROM productpacksize WHERE id=$pkszid";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
$row=mysql_fetch_array($result);
$pni=$row[0];

$sql = "UPDATE productpacksize set DefaultPksz=0 WHERE ProductNumberInternal=$pni";
echo "<br /> $sql <br />";
mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL: $sql <br />");

$sql = "UPDATE productpacksize set DefaultPksz=1 WHERE id=$pkszid";
echo "<br /> $sql <br />";
mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL: $sql <br />");
	
$_SESSION['note'] = "The Packaging id $pkszid was set as default packaging of $pni";

echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
echo "window.opener.location.reload()\n";
echo "window.close()\n";
echo "</SCRIPT>\n";
exit();
?>
