<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');



if ( isset($_SESSION['pid']) ) {
	if ( !empty($_POST) ) {
		$sql = "INSERT INTO lab_assignees (project_id, user_id) "
		. "VALUES (" . $_SESSION['pid'] . ", '" . $_POST['assignee_id'] . "')";
		mysql_query($sql, $link);
		header("location: project_management_admin.sample.php");
		exit();
	}
}



if ( $_GET['action'] == "del" ) {
	$sql = "DELETE FROM lab_assignees WHERE assignee_id = " . $_GET['aid'];
	mysql_query($sql, $link);
	header("location: project_management_admin.sample.php");
	exit();
}



include('inc_header.php');

?>



<B CLASS="header">Choose assignee</B><BR><BR>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BGCOLOR="#976AC2"><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="5" BGCOLOR="whitesmoke" WIDTH=694><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="10" BGCOLOR="whitesmoke" ALIGN=CENTER WIDTH=684><TR><TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>

		<TD>


<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>

		<TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP><FORM METHOD="post" ACTION="project_management_admin_choose_assignee.php">
		<TD><SELECT NAME="assignee_id">

		<?php

		$sql = "SELECT * FROM users WHERE user_type =3 AND active = 1 ORDER BY last_name";
		$result = mysql_query($sql, $link);

		if ( mysql_num_rows($result) > 0 ) {
			while ( $row = mysql_fetch_array($result) ) {
				echo "<OPTION VALUE='" . $row['user_id'] . "'>" . $row['first_name'] . " " . $row['last_name'] . "</OPTION>";
			}
		}

		?>

		</SELECT> <INPUT TYPE='submit' VALUE="Choose">   <INPUT TYPE='button' VALUE="Cancel" onClick="JavaScript:history.go(-1)"></TD>
	</TR></FORM>
</TABLE>

		</TD>
	</TR>
</TABLE>

		</TD>
	</TR>
</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE>



<?php include('inc_footer.php'); ?>