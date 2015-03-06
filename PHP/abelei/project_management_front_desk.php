<?php

include('inc_ssl_check.php');
session_start();

if ( $_SESSION['userTypeCookie'] != 1 and $_SESSION['userTypeCookie'] != 4 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');

// REFRESH PAGE EVERY TWO MINUTES FOR ADMIN
if ( $_SESSION['userTypeCookie'] == 1 ) {
	header('Refresh: 120');
}



if ( isset($_SESSION['pid']) ) {
	unset($_SESSION['pid']);
	header("location: project_management_front_desk.php");
	exit();
}



if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}



$status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
$status_num = array(1,2,3,4,5);

$application_array = array("Baked Good","Beverage","Cereal","Confection","Dairy Product","Pet Food","Nutraceutical","Pharmaceutical","Prepared Food","Snack");
$application_num = array(1,2,3,4,5,6,7,8,9,10);

$shipper_array = array("UPS","FedEx","DHL","USPS","Other");
$shipper_num = array(1,2,3,4,5);



include("inc_header.php");



?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>

		<TD>


<?php if ( $_GET['archives'] == 1 ) {
	$archives = 1; ?>
	<B CLASS="header">Front desk archives</B> / <B><A HREF="project_management_front_desk.php">Current front desk projects</A>
<?php } else {
	$archives = ''; ?>
	<B CLASS="header">Front desk home</B> / <B><A HREF="project_management_front_desk.php?archives=1">Archives</A>
<?php } ?>
		
		
<BR><BR><BR>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>

<?php include('inc_front_desk.php'); ?>

		</TD>
	</TR>
</TABLE>



<?php include("inc_footer.php"); ?>