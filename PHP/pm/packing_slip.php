<?php

session_start();

include('global.php');
require_ssl();

if ( !isset($_SESSION['userTypeCookie']) ) {
	header ("Location: login.php?out=1");
	exit;
}


if ( isset($_SESSION['pid']) ) {
	unset($_SESSION['pid']);
	header("location: packing_slip.php");
	exit();
}



if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

$suggested_level_array = array("Use as desired","Same as target","Other");
$suggested_level_num = array(1,2,3);

$sample_size_array = array("1 oz.","2 oz.","4 oz.","8 oz.","Other");
$sample_size_num = array(1,2,3,4,5);

$shipper_array = array("UPS","FedEx","DHL","USPS","Other");
$shipper_num = array(1,2,3,4,5);

$shipping_array = array("Next day","2nd day","Ground ","Date appropriate carrier");
$shipping_num = array(1,2,3,4);

?>



<HEAD>
	<TITLE> abelei packing slip </TITLE>
	<LINK HREF="styles.css" REL="stylesheet">
	<LINK HREF="ps.css" REL="stylesheet" media="print">

</HEAD>

<BODY CLASS="PackingSlip" BGCOLOR="#FFFFFF" onLoad="print()">

<?php

// COMMON CODE SHARED WITH E-MAIL RECEIPT TO CLIENT (project_info.php)
include('inc_packing_slip.php');

echo $message;

?>

</BODY>
</HTML>