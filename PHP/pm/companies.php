<?php

session_start();

include('global.php');
require_ssl();

 if ( $_SESSION['userTypeCookie'] != 1 and $_SESSION['userTypeCookie'] != 2 ) {
	header ("Location: login.php?out=1");
	exit;
}


if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}



if ( !empty($_POST) ) {

	if ( $_POST['sales'] != '' ) {
		$sales = $_POST['sales'];
	} else {
		$sales = array();
	}

	$company_id = $_POST['company_id'];
	$company = $_POST['company'];

	// check_field() FUNCTION IN global.php
	check_field($company, 1, 'Company');

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$company = escape_data($company);
		$cid = $company_id;
		if ( $_POST['company_id'] != "" ) {
			$sql = "UPDATE companies " .
			" SET company = '" . $company . "'" .
			" WHERE company_id = " . $company_id;
			mysql_query($sql, $link);
		}
		else {
			$sql = "INSERT INTO companies (company) VALUES ('" . $company . "')";
			mysql_query($sql, $link);   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$cid = mysql_insert_id();
		}

		if ( $_SESSION['userTypeCookie'] == 2 ) {
			$sql = "DELETE FROM companies_users WHERE company_id = " . $cid . " AND user_id = " . $_SESSION['user_id'];
			mysql_query($sql, $link);
			$sql = "INSERT INTO companies_users (company_id, user_id) VALUES (" . $cid . "," . $_SESSION['user_id'] . ")";
			mysql_query($sql, $link);
		} else {
			$sql = "DELETE FROM companies_users WHERE company_id = " . $cid;
			mysql_query($sql, $link);
			foreach ( $sales as $value ) {
				$sql = "INSERT INTO companies_users (company_id, user_id) VALUES (" . $cid . ", '" . $value . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
		}

		$_SESSION['note'] = "Company information successfully saved<BR>";
		header("location: choose_company.php");
		exit();
	}


}

else {
	if ( isset($_GET['cid']) ) {
		$sql = "SELECT * FROM companies WHERE company_id = " . $_GET['cid'];
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result);
		$company_id = $row['company_id'];
		$company = $row['company'];

		$sales = array();
		$sql = "SELECT * FROM companies_users WHERE company_id = " . $_GET['cid'];
		$result_sales = mysql_query($sql, $link);
		if ( mysql_num_rows($result_sales) > 0 ) {
			$i = 0;
			while ( $row_sales = mysql_fetch_array($result_sales) ) {
				$sales[$i] = $row_sales['user_id'];
				$i++;
			}
		}

	}
	else {
		$company_id = "";
		$company = "";
		$sales = array();
	}
}



include('header.php');

?>



<B CLASS="header">Companies</B>

<?php 

if ( $company_id != "" ) {
	echo " / <B>Edit</B>";
}

?>

<BR><BR>


<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<FORM METHOD="post" ACTION="companies.php">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR>
		<INPUT TYPE="hidden" NAME="company_id" VALUE="<?php echo $company_id;?>">
		<TD><B CLASS="black">Company:</B>&nbsp;</TD>
		<TD><INPUT TYPE='text' NAME="company" SIZE=42 VALUE="<?php echo stripslashes($company);?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>

	<?php  if ( $_SESSION['userTypeCookie'] == 1 ) { ?>

		<TR VALIGN=TOP>
			<TD><B CLASS="black">Assigned salespeople:</B>&nbsp;</TD>
			<TD>

			<?php

			$sql = "SELECT user_type, user_id, first_name, last_name FROM users WHERE user_type < 3 ORDER BY last_name";
			$result = mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

			if ( mysql_num_rows($result) != 0 ) {
				while ( $row = mysql_fetch_array($result) ) {
					if ( in_array($row['user_id'], $sales) ) {
						echo "<INPUT TYPE='checkbox' NAME='sales[]' VALUE=" . $row['user_id'] . " CHECKED>" . $row['first_name'] . " " . $row['last_name'] . "<BR>";
					} else {
						if ( $row['user_type'] == 2 ) {
							echo "<INPUT TYPE='checkbox' NAME='sales[]' VALUE=" . $row['user_id'] . ">" . $row['first_name'] . " " . $row['last_name'] . "<BR>";
						}
					}
				}
	 		}

			?>

			</TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
		</TR>

	<?php } ?>

	<TR>
		<TD></TD>
		<TD><INPUT TYPE='submit' VALUE="Save"> <INPUT TYPE='button' VALUE="Cancel" onClick="JavaScript:history.go(-1)"></TD>
	</TR>
</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>



<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

function inactivate(uid) {
	if ( confirm('Are you sure you want to inactivate this user?') ) {
		document.location.href = "companies.php?action=inact&uid=" + uid
	}
}

 // End -->
</SCRIPT>

<?php include('footer.php'); ?>