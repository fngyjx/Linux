<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $_SESSION['user_id'] != '4' and $_SESSION['user_id'] != '5' and $_SESSION['user_id'] != '6' and $_SESSION['user_id'] != '35' ) {
	$_SESSION['note'] = "Login do not have the right to access the page<br />";
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');

$error_found="";
$Designation="";
$note="";

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( isset($_SESSION[error_message]) ) {
	$error_message = $_SESSION[error_message];
	$error_found=true;
	unset($_SESSION['error_message']);
}

$pitem = isset($_REQUEST['pitem']) ? $_REQUEST['pitem'] : '';

//print_r($_REQUEST);

if ($pitem == "" )  {
	$_SESSION['error_message'] = "number of updated items is empty";
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.open('','_self');\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
	exit();
} 

if ( !empty($_POST) ) {
	for ( $i = 0; $i <= $pitem; $i++) {
		$update_price_item="update_price_".$i; 
		$update_items = isset($_REQUEST[$update_price_item]) ? $_REQUEST[$update_price_item] : "";
		if ( $update_items == "" )
			continue;
		$update_items_array = explode("_", $update_items);
		$pni = $update_items_array[0];
		$vendor_id = $update_items_array[1];
		$tier = $update_items_array[2];
		$org_price_perpound = $update_items_array[3];
		
		$update_price_perpound = isset($_REQUEST["price_perpound_".$i]) ? $_REQUEST["price_perpound_".$i] : '';
		if ( $update_price_perpound == '' or $update_price_perpound < 0 ) {
			$_SESSION['error_message'] = "price value has to greater than 0";
			echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
			echo "window.opener.location.reload();\n";
			echo "window.open('','_self');\n";
			echo "window.close();\n";
			echo "</SCRIPT>\n";
			exit();
		} 
		
		if ( $pni != '' and $vendor_id != "" and $tier != "" and $update_price_perpound > 0) {
			$sql="UPDATE productprices set PricePerPound='". $update_price_perpound ."', PriceEffectiveDate=NOW() WHERE
			ProductNumberInternal='".$pni."' AND VendorID = '".$vendor_id ."' AND Tier = '".$tier . "'";
			mysql_query($sql,$link) or die(mysql_error() ." Failed Execute SQL : $sql <br />");
		}
	}
}

$_SESSION['error_message'] = "Product Price(s) updated successfully";
echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";

echo "window.open('','_self');\n";
echo "window.opener.location.reload();\n";
echo "window.close()\n";
echo "</SCRIPT>\n";
exit();

include("inc_footer.php"); ?>