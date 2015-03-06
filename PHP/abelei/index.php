<?php

include('inc_ssl_check.php');
session_start();
include('inc_global.php');

$note=""; $error_found=false;
$userTypeCookie = isset($_SESSION['userTypeCookie']) ? $_SESSION['userTypeCookie'] : "";
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "";
// REFRESH PAGE EVERY TWO MINUTES IF LAB OR FRONT DESK'S LOGGED IN
if ( $userTypeCookie > 2 ) {
	header('Refresh: 120');
}



if ( isset($_GET['instruction']) ) {

	// Set instruction variable and clear login variables	
	if ( $_GET['instruction'] == "incorrect_u_p" ) {
		$note = "Incorrect username or password";
		unset($_SESSION["uNameCookie"]);
		unset($_SESSION["uLoggedInCookie"]);
	}
	elseif ( $_GET['instruction'] == "wologin" ) {
		$note = "Please log in";
		unset($_SESSION["uNameCookie"]);
		unset($_SESSION["uLoggedInCookie"]);
	}
	elseif ( $_GET['instruction'] == "error" ) {
		$note = "An error occurred.  Please contact technical support if this error persists.";
		unset($_SESSION["uNameCookie"]);
		unset($_SESSION["uLoggedInCookie"]);
	}
	elseif ( $_GET['instruction'] == "lockout" ) {
		$note = "Your account has been locked out. Please contact technical support.";
		unset($_SESSION["uNameCookie"]);
		unset($_SESSION["uLoggedInCookie"]);
	}
	elseif ( $_GET['instruction'] == "log_out" ) {
		$note = "Logged Out";
		unset($_SESSION["uNameCookie"]);
		unset($_SESSION["uLoggedInCookie"]);
		$_SESSION["uLoggedOutCookie"] = true;
	}
	elseif ( $_GET['instruction'] == "inactive" ) {
		$note = "Your account is inactive. Please contact technical support.";
		unset($_SESSION["uNameCookie"]);
		unset($_SESSION["uLoggedInCookie"]);
		$_SESSION["uLoggedOutCookie"] = true;
	}
}



if ( isset($_SESSION['pid']) ) {
	unset($_SESSION['pid']);
	header("location: index.php");
	exit();
}

$status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
$status_num = array(1,2,3,4,5);

$application_array = array("Baked Good","Beverage","Cereal","Confection","Dairy Product","Pet Food","Nutraceutical","Pharmaceutical","Prepared Food","Snack");
$application_num = array(1,2,3,4,5,6,7,8,9,10);

$shipper_array = array("UPS","FedEx","DHL","USPS","Other");
$shipper_num = array(1,2,3,4,5);



include('inc_header.php');

?>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>

<?php

if ( ""==$user_id ) { ?>

	<B CLASS="header">Login</B><BR><BR>

	<?php if ( $note != '' ) {
		echo "<B STYLE='color:#FF0000'>" . $note . "</B><BR><BR>";
	} ?>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<FORM ACTION="login.php" METHOD="post">
		<TR VALIGN=MIDDLE>
			<TD>Username:</TD>
			<TD><INPUT TYPE="text" NAME="email" SIZE="30"></TD>
		</TR>
		<TR>
			<TD COLSPAN=2 HEIGHT="9">&nbsp;</TD>
		</TR>
		<TR>
			<TD>Password:&nbsp;&nbsp;&nbsp;</TD>
			<TD><INPUT TYPE="password" NAME="pass" SIZE="30"></TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
		</TR>
		<TR>
			<TD>&nbsp;</TD>
			<TD><INPUT TYPE="submit" VALUE="Login"></TD>
		</TR></FORM>
	</TABLE>



<?php
} else {
?>



	<?php if ( $error_found ) {
		echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
	} ?>



	<?php if ( $_SESSION['userTypeCookie'] == 1 ) { ?>
		<B CLASS="header">Admin home</B><BR><BR><BR>
		<?php include('inc_admin.php');
	}

	elseif ( $_SESSION['userTypeCookie'] == 2 ) { ?>
		<B CLASS="header">Sales home</B><BR><BR><BR>
		<?php include('inc_sales.php');
	}

	elseif ( $_SESSION['userTypeCookie'] == 3 ) { ?>
		<B CLASS="header">Lab home</B><BR><BR><BR>
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

}
?>



		</TD>
	</TR>
</TABLE>



<?php 
include('inc_footer.php');?>