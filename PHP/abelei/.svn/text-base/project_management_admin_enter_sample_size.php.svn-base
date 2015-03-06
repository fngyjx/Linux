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



$sample_size_array = array("1 oz.","2 oz.","4 oz.","8 oz.","Other");
$sample_size_num = array(1,2,3,4,5);

$project_type_array = array("New","Revision","Resample","Other");
$project_type_num = array(1,2,3,4);

$priority_array = array("Low","Medium","High");
$priority_num = array(1,2,3);

$status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
$status_num = array(1,2,3,4,5);

$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");



if ( isset($_SESSION['pid']) ) {

	if ( !empty($_POST) ) {

		$sample_size = $_POST['sample_size'];
		$sample_size_other = $_POST['sample_size_other'];

		if ( !$error_found ) {

			// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
			$sample_size_other = escape_data($sample_size_other);

			// CHECK sample_size ENTERED TO SEE WHETHER IT'S BEEN CHANGED
			$sql = "SELECT sample_size FROM projects WHERE sample_info_submitted = 1 AND sample_size <> '" . $sample_size . "' AND project_id = " . $_SESSION['pid'];
			$result = mysql_query($sql, $link);
			if ( mysql_num_rows($result) > 0 ) {
				$row = mysql_fetch_array($result);
				$old_size = $sample_size_array[$row['sample_size']-1];
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Sample size', '" . $old_size . "', '" . $sample_size_array[$sample_size-1] . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}

			// CHECK sample_size_other ENTERED TO SEE WHETHER IT'S BEEN CHANGED
			$sql = "SELECT sample_size_other FROM projects WHERE sample_info_submitted = 1 AND sample_size_other <> '" . $sample_size_other . "' AND project_id = " . $_SESSION['pid'];
			$result = mysql_query($sql, $link);
			if ( mysql_num_rows($result) > 0 ) {
				$row = mysql_fetch_array($result);
				$old_sample_size_other = $row['sample_size_other'];
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Other sample size', '" . $old_sample_size_other . "', '" . $sample_size_other . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}

			$sql = "UPDATE projects " .
			" SET sample_size = " . $sample_size . ", " .
			" sample_size_other = '" . $sample_size_other . "' " .
			" WHERE project_id = " . $_SESSION['pid'];
			mysql_query($sql, $link);
			$_SESSION['note'] = "Information successfully saved<BR>";
			header("location: project_management_admin.sample.php");
			exit();

		}

	}

	else {

		$sql = "SELECT sample_size, sample_size_other FROM projects WHERE project_id = " . $_SESSION['pid'];
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result);

		if ( $row['sample_size'] == "" ) {
			$sample_size = 2;
		}
		else {
			$sample_size = $row['sample_size'];
		}
		$sample_size_other = $row['sample_size_other'];

	}

}



include('inc_header.php');

?>



<B CLASS="header">Project requisition &#150; Edit sample size</B><BR><BR>



<?php include('inc_project_header.php') ?>



<TABLE WIDTH=700 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="sales.src=salesOver.src"
		onMouseOut="sales.src=salesOut.src" 
		HREF="project_info.php"><IMG SRC="images/tabs/sales_out.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Sales info" NAME="sales"></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="client.src=clientOver.src"
		onMouseOut="client.src=clientOut.src" 
		HREF="client_info.php"><IMG SRC="images/tabs/client_out.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Contact info" NAME="contact"></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="sample.src=sampleOver.src"
		onMouseOut="sample.src=sampleOver.src" 
		HREF="sample_info.php"><IMG SRC="images/tabs/sample_over.gif" WIDTH=106 HEIGHT=18 BORDER=0 ALT="Sample info" NAME="sample"></TD>
		<TD><IMG SRC="images/tabs/blank.gif" WIDTH="392" HEIGHT="18" ALT="Blank"></TD>
	</TR>
	<TR><TD COLSPAN=4><IMG SRC="images/tabs/tab_rule.gif" WIDTH="700" HEIGHT="8"></TD></TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BGCOLOR="#976AC2"><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="5" BGCOLOR="whitesmoke" WIDTH=694><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="10" BGCOLOR="whitesmoke" ALIGN=CENTER WIDTH=684><TR><TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>

<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<FORM METHOD="post" ACTION="project_management_admin_enter_sample_size.php">
	<INPUT TYPE="hidden" NAME="proj_id" VALUE="<?php echo $_SESSION['pid']; ?>">

	<TR>
		<TD><B CLASS="black">Sample size:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><SELECT NAME="sample_size">
			<?php 
			foreach ( $sample_size_num as $value ) {
				if ( $value == $sample_size ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $sample_size_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $sample_size_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT> &nbsp;&nbsp;&nbsp;<I>if "Other":</I> <INPUT TYPE="text" NAME="sample_size_other" SIZE="10" VALUE="<?php echo $sample_size_other?>"></TD>
	</TR>

	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="11"></TD>
	</TR>

	<TR>
		<TD></TD>
		<TD><INPUT TYPE="submit" VALUE="Save"> <INPUT TYPE="button" VALUE="Cancel" onClick="JavaScript:history.go(-1)"></TD>
	</TR></FORM>

</TABLE>

		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE>



<?php include('inc_footer.php'); ?>