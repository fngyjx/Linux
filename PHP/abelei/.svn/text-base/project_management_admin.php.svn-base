<?php

include('inc_ssl_check.php');
session_start();
include('inc_global.php');

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// REFRESH PAGE EVERY TWO MINUTES IF LAB OR FRONT DESK'S LOGGED IN
if ( $_SESSION['userTypeCookie'] > 2 ) {
	header('Refresh: 120');
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include("inc_header.php");

?>




	<?php if ( $_SESSION['userTypeCookie'] == 1 ) { ?>
		<?php include('inc_admin.php');
	}

	elseif ( $_SESSION['userTypeCookie'] == 2 ) { ?>
		<?php include('inc_sales.php');
	}

	elseif ( $_SESSION['userTypeCookie'] == 3 ) { ?>
		<?php include('inc_lab.php');
	}

	elseif ( $_SESSION['userTypeCookie'] == 4 ) {

		if ( $_GET['archives'] == 1 ) {
			$archives = 1; ?>
			<B CLASS="header">Front desk archives</B> / <B><A HREF="index.php">Current projects</A>
		<?php } else {
			$archives = ''; ?>
			<B CLASS="header">Front desk home</B> / <B><A HREF="index.php?archives=1">Archives</A>
		<?php } ?>
 
 		<BR><BR><BR>
		<?php if ( $note ) {
			echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
		} ?>
		<?php include('inc_front_desk.php'); ?><BR><BR>
		<B CLASS="header">In the lab</B><BR><BR><BR>
		<?php include('inc_lab.php');
	}

?>

<?php include("inc_footer.php"); ?>