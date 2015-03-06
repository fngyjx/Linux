<?php

include('inc_ssl_check.php');
session_start();

/if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

/ ADMIN and FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 4 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');
include("inc_header.php");

?>



<?php include("inc_footer.php"); ?>