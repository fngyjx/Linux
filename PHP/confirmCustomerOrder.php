<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN AND FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 4 ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');
include('inc_customers_order_pdf.php');

print_r($_REQUEST);

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( $_REQUEST['order_number'] != '' ) {
	$order_number = escape_data($_REQUEST['order_number']);
} else {
	$note .= " Contact Phone or FAX Number is must to have. <br />";
	echo "<SCRIPT>window.opener.location.reload();</SCRIPT>";
	exit;
}

if ( isset($_REQUEST['contact_phone_fax']) ) {
	$contact_phone_id = escape_data($_REQUEST['contact_phone_fax']);
} else {
	$note .= " Contact Phone or FAX Number is must to have. <br />";
	echo "<SCRIPT>window.opener.location.reload();window.close();</SCRIPT>";
	exit;
}

$sql = "SELECT first_name, last_name  
		FROM users 
		WHERE user_id = " . $_SESSION['user_id'];
$result = mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL: $sql <br />");

$row_user=mysql_fetch_array($result);

$sql = "SELECT customer_id, email, first_name,last_name,phone_id,title,number, phone_types.description
FROM customer_contacts LEFT JOIN customer_contact_phones
USING(contact_id)
LEFT JOIN customer_contact_email USING(contact_id) 
LEFT JOIN phone_types ON customer_contact_phones.type=phone_types.type_id
WHERE customer_contact_phones.phone_id='".$contact_phone_id ."'";
echo "<br /> $sql <br />";
$results = mysql_query($sql,$link) or die (mysql_error() . " Faied Execute SQL : $sql <br />");
$row = mysql_fetch_array($results);

$attachment=create_cstordcnfrm_pdf_file($order_number,$row['customer_id'],"",$contact_phone_id);

$sql = "UPDATE customerordermaster set ConfirmedToCustomer=1 , ConfirmFile='".$attachment."', ConfirmedBy ='Confirmed to Customer via ". $row['description'] . " number " . $row['number'] . " to " . $row['first_name'] . " "
	. $row['last_name'] . " " . $row['title'] . " By " . $row_user['first_name'] . " " .$row_user['last_name'] ." on " . date("F j, Y")."' WHERE OrderNumber=$order_number";

//echo "<br /> $sql <br />";

mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");

echo "<SCRIPT>window.opener.location.reload();window.open('','_self'); window.close();</SCRIPT>";
exit;


