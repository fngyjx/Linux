<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) or $_SESSION['userTypeCookie'] != 1 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');



$types = array("Admin","Sales","Quality Control","Lab","Front desk", "Lab and Limited QC");
$types_num = array(1,2,5,3,4,6);

if ( !empty($_POST) ) {

	$user_id_nonsession = $_POST['user_id_nonsession'];
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$user_type = $_POST['user_type'];
	$email = trim($_POST['email']);
	$pass = trim($_POST['pass']);
	$is_salesperson = $_POST['is_salesperson'];
	$active = $_POST['active'];
	$locked = $_POST['locked'];


	// check_field() FUNCTION IN global.php
	check_field($first_name, 1, 'First name');
	check_field($last_name, 1, 'Last name');
	check_field($email, 1, 'E-mail');


	if ( $_POST['user_id_nonsession'] != "" ) {
		$id_check = " AND user_id <> " .  $_POST['user_id_nonsession'];
	}
	else {
		$id_check = "";
	}

	if ( $email != "" ) {
		$sql = "SELECT * FROM users WHERE email = '" . escape_data($email) . "'" . $id_check;
		$result = mysql_query($sql, $link);
		//mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
		if ( $c > 0 ) {
			$error_found = true;
			$error_message .= "E-mail address entered is already in use<BR>";
		}
	}

	check_field($pass, 1, 'Password');
	if ( strlen($pass) < 6 ) {
		$error_found = true;
		$error_message .= "'Password' must be at least six characters, DEV<BR>";
	}

	$pos = strpos($pass, "'");
	if ($pos !== false) {
		$error_found = true;
		$error_message .= "'Password' cannot contain an apostrophe or quote<BR>";
	}

	$pos = strpos($pass, '"');
	if ($pos !== false) {
		$error_found = true;
		$error_message .= "'Password' cannot contain an apostrophe or quote<BR>";
	}

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$first_name = escape_data($first_name);
		$last_name = escape_data($last_name);
		$email = escape_data($email);
		$pass = escape_data($pass);

		if ( $_POST['user_id_nonsession'] != "" ) {
			$sql = "UPDATE users " .
			" SET first_name = '" . $first_name . "'," .
			" last_name = '" . $last_name . "', " .
			" email = '" . $email . "', " .
			" pass = '" . $pass . "', " .
			" is_salesperson = " . $is_salesperson . ", " .
			" active = " . $active . ", " .
			" user_type = " . $user_type . ", " .
			" locked = " . $locked .
			" WHERE user_id = " . $user_id_nonsession;
			mysql_query($sql, $link);
		}
		else {
			$sql = "INSERT INTO users (first_name, last_name, email, pass, is_salesperson, active, user_type, locked) VALUES ('" . $first_name . "','" . $last_name . "', '" . $email . "', '" . $pass . "', " . $is_salesperson . ", " . $active . ", " . $user_type . ", " . $locked . ")";
			mysql_query($sql, $link);// or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		header("location: admin_users.php");
		exit();
	}


}

else {

	if ( isset($_GET['user_id']) ) {
		$sql = "SELECT * FROM users WHERE user_id = " . $_GET['user_id'];
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result);
		$user_id_nonsession = $row['user_id'];
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		$user_type = $row['user_type'];
		$email = $row['email'];
		$pass = $row['pass'];
		$is_salesperson = $row['is_salesperson'];
		$active = $row['active'];
		$locked = $row['locked'];
	}
	else {
		$user_id_nonsession = "";
		$first_name = "";
		$last_name = "";
		$user_type = "";
		$email = "";
		$pass = "";
		$is_salesperson = "";
		$active = "";
		$locked = "";
	}

}



if ( $_GET['action'] == "inact" ) {
	$sql = "UPDATE users SET active = 0 WHERE user_id = " . $_GET['uid'];
	mysql_query($sql, $link);
}



include("inc_header.php"); ?>



<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>


<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0>
<FORM ACTION="admin_users.php" METHOD="post">
<INPUT TYPE="hidden" NAME="user_id_nonsession" VALUE="<?php echo $user_id_nonsession;?>">

	<TR VALIGN=TOP>
		<TD><B>First name:</B></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" NAME="first_name" SIZE="30" VALUE="<?php echo stripslashes($first_name);?>"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B>Last name:</B></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" NAME="last_name" SIZE="30" VALUE="<?php echo stripslashes($last_name);?>"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B>E-mail:</B></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" NAME="email" SIZE="30" VALUE="<?php echo stripslashes($email);?>"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B>Password:</B></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="password" NAME="pass" SIZE=30 VALUE="<?php echo stripslashes($pass);?>"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B>User type:</B></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD><SELECT NAME="user_type">
			<?php
			$i = 0;
			foreach ( $types_num as $value ) {
				if ( $value == $user_type ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $types[$i]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $types[$i]?></OPTION>
				<?php }
				$i++;
			} ?>
		</SELECT></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B>Salesperson:</B></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD>
		<?php if ( $is_salesperson == "" or $is_salesperson == "0" ) {
			print("<INPUT TYPE='radio' NAME='is_salesperson' VALUE='1'>Yes ");
			print("<INPUT TYPE='radio' NAME='is_salesperson' VALUE='0' CHECKED>No");
		} else {
			print("<INPUT TYPE='radio' NAME='is_salesperson' VALUE='1' CHECKED>Yes ");
			print("<INPUT TYPE='radio' NAME='is_salesperson' VALUE='0'>No");
		} ?>
		</TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B>Active:</B></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD>
		<?php if ( $active == "" or $active == "1" ) {
			print("<INPUT TYPE='radio' NAME='active' VALUE='1' CHECKED>Yes ");
			print("<INPUT TYPE='radio' NAME='active' VALUE='0'>No");
		} else {
			print("<INPUT TYPE='radio' NAME='active' VALUE='1'>Yes ");
			print("<INPUT TYPE='radio' NAME='active' VALUE='0' CHECKED>No");
		} ?>
		</TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B>Locked out:</B></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD>
		<?php if ( $locked == "1" ) {
			print("<INPUT TYPE='radio' NAME='locked' VALUE='1' CHECKED>Yes ");
			print("<INPUT TYPE='radio' NAME='locked' VALUE='0'>No");
		} else {
			print("<INPUT TYPE='radio' NAME='locked' VALUE='1'>Yes ");
			print("<INPUT TYPE='radio' NAME='locked' VALUE='0' CHECKED>No");
		} ?>
		</TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD></TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="submit" VALUE="Save"> <INPUT TYPE="button" VALUE="Cancel" onClick="JavaScript:history.go(-1)"></TD>
	</TR></FORM>

</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>



<?php if ( !isset($_GET['user_id']) and $user_id_nonsession == "" ) { ?>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">

		<TR>
			<TD>&nbsp;</TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="1"></TD>
			<TD><B>Name</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B>Type</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD ALIGN=CENTER><B>Sales</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD ALIGN=CENTER><B>Active</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B>Last login</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD ALIGN=CENTER><B>Locked</B></TD>
		</TR>

	<?php

	$sql = "SELECT * FROM users ORDER BY user_type, last_name";
	$result = mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	if ( mysql_num_rows($result) != 0 ) {

		$bg = 0;
					
		$c = mysql_num_rows($result);
		while ( $row = mysql_fetch_array($result) ) {

			if ( $bg == 1 ) {
				$bgcolor = "#F3E7FD";
				$bg = 0;
			}
			else {
				$bgcolor = "whitesmoke";
				$bg = 1;
			} ?>

		<TR BGCOLOR="<?php echo($bgcolor);?>" VALIGN=TOP>
			<TD><?php if ( $row['active'] == 1 ) { ?>
				<A HREF="JavaScript:inactivate(<?php echo($row['user_id']);?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A>
			<?php } ?></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="1"></TD>
			<TD><NOBR><A HREF="admin_users.php?action=edit&user_id=<?php echo $row['user_id'];?>"><?php echo $row['first_name'] . " " . $row['last_name'] ;?></A></NOBR></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><?php
			$i = 0;
			foreach ( $types_num as $value ) {
				if ( $value == $row['user_type'] ) {
					echo $types[$i];
				}
				$i++;
			} 
			?></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD ALIGN=CENTER><?php
			if ( $row['is_salesperson'] == 1 ) {
				print("Yes");
			}
			else {
				print("No");
			}
			?></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD ALIGN=CENTER><?php
			if ( $row['active'] == 1 ) {
				print("Yes");
			}
			else {
				print("No");
			}
			?></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><?php
			if ( $row['last_login'] > "0000-00-00 00:00:00" ) {
				$login_datetime = explode(" ", $row[last_login]);
				$login_date = explode("-", $login_datetime[0]);
				$login_time = $login_datetime[1];
				print(date("m/d/Y", mktime(0, 0, 0, $login_date[1], $login_date[2], $login_date[0])) . " " . $login_time);
			}
			else {
				print("<I>None</I>");
			}
			?></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD ALIGN=CENTER><?php
			if ( $row['locked'] == 1 ) {
				print("Yes");
			}
			else {
				print("No");
			}
			?></TD>
		</TR>

	<?php
	 	}
	}

	?>

	</TABLE>

<?php } ?>

<BR><BR>

<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

function inactivate(uid) {
	if ( confirm('Are you sure you want to inactivate this user?') ) {
		document.location.href = "admin_users.php?action=inact&uid=" + uid
	}
}

 // End -->
</SCRIPT>





<?php include("inc_footer.php"); ?>