<?php 

include_once('inc_ssl_check.php');
session_start();
include_once('inc_global.php');

if ( isset($_GET['out']) ) {
	$_SESSION = array(); // Destroy the variables.
	session_destroy(); // Destroy the session itself.
	setcookie ('PHPSESSID', '', time()-300, '/', '', 0); // Destroy the cookie.
	header ("Location: index.php");
	exit();
}

$uName = escape_data($_POST['email']);
$uPassword = escape_data($_POST['pass']);

// Verify that the $uName is logging in or already logged in
if ( !isset($_SESSION['uNameCookie']) ) {
	$new_log = true;
	if ( $uName == "" or $uPassword == "" ) {
		session_unset();
		session_destroy();
		header ("Location: index.php?instruction=wologin1&u=" . $_POST['email'] . "&p=" . $_POST['pass']);
		exit;
	}
}

else {
	// if a new $uName is logging in then get new variables else continue
	if ( !empty($_POST) ) {
		$new_log = true;
	}
	elseif ( !isset($_SESSION['uLoggedInCookie']) ) {
		session_unset();
		session_destroy();
		header ("Location: index.php?instruction=wologin2");
		exit;
	}
	else {
		$uName = $uNameCookie;
		$new_log = false;
	}
}

// if a new user then verify name and password else skip
if ( $new_log == true ) {

	$err = 0;
	
	// sql statement which will determine if $uName exists 
	$sql = "SELECT * FROM users WHERE email = '" . $uName . "'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);

	$iCount = mysql_num_rows($result);

	// if the $uName doesn't exist then send back to login page
	if ( $iCount == 0 ) {
		mysql_close ($link);
		header ("Location: index.php?instruction=incorrect_u_p&sp=1");
		exit;
	}

	// Locked out return to index	
	if ( $row[locked] == 1 ) {
		mysql_close ($link);
		header ("Location: index.php?instruction=lockout");
		exit;
	}

	// inactive out return to index	
	if ( $row[active] == 0 ) {
		mysql_close ($link);
		header ("Location: index.php?instruction=inactive");
		exit;
	}

	// If the password doesn't match then send back to login page
	if ( $row['pass'] != $uPassword ) {
		$FailedLoginAttempts = $row['login_attempts'];
		
		if ( $FailedLoginAttempts >= 4 ) {
			$sql = "UPDATE users SET login_attempts = " . ($FailedLoginAttempts + 1) . ", locked = 1, lockout_date = '" . date("Y-m-d H:i:s") . "' WHERE email = '" . $uName . "'";
			mysql_query($sql, $link);
			mysql_close ($link);
			unset($_SESSION['user_id']);
			header ("Location: index.php?instruction=lockout");
			exit;
		}
		else {
			$sql = "UPDATE users SET login_attempts = " . ($FailedLoginAttempts + 1) .  " WHERE email = '" . $uName . "'";
			mysql_query($sql, $link);
			mysql_close ($link);
			unset($_SESSION['user_id']);
			header ("Location: index.php?instruction=incorrect_u_p&sp=2&p1=" . $row['password'] . "&p2=" . $uPassword);
			exit;
		}
		
	}

	// set the user session variables

	if ( $err == 0 ) {
		
		$_SESSION['user_id'] = $row['user_id'];
		$_SESSION['first_nameCookie'] = $row['first_name'];
		$_SESSION['last_nameCookie'] = $row['last_name'];
		$_SESSION['userTypeCookie'] = $row['user_type'];
		$_SESSION['uNameCookie'] = $row['email'];
		$_SESSION['uLoggedInCookie'] = true;

		$sql =  "UPDATE users SET last_login = '" . date("Y-m-d H:i:s") . "', login_attempts = 0 WHERE email = '" . $uName . "'";
		mysql_query($sql, $link);
		mysql_close ($link);
	}

	else {
		mysql_close ($link);
		unset($_SESSION['user_id']);
		header ("Location: index.php?instruction=error");
		exit;
	}	

}

if ( $row['user_type'] == 1 or $row['user_type'] == 2 ) {   // ADMIN AND SALES
	header ("Location: project_management_admin.php");
} elseif ( $row['user_type'] == 3 or $row['user_type'] == 5 or $row['user_type'] == 6) {   // LAB AND QC
	header ("Location: project_management_projects.php");
} else {   // FRONT DESK
	header ("Location: project_management_front_desk.php");
}

exit;

?>