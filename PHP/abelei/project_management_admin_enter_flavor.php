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

		$flavor_name = $_POST['flavor_name'];
		$flavor_id = $_POST['flavor_id'];
		$proj_id = $_POST['proj_id'];
		$old_flavor_id = $_POST['old_flavor_id'];
		$suggested_level_other = $_POST['suggested_level_other'];
		$use_in = $_POST['use_in'];
		$other_info = $_POST['other_info'];

		$expiration_date = $_POST['expiration_date'];
		$date_parts = explode("/", $expiration_date);
		$new_expiration_date = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
		if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
			if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
				$error_found=true;
				$error_message .= "Invalid (" . $expiration_date . ") date entered<BR>";
			}
		} else {
			$error_found=true;
			$error_message .= "Invalid (" . $expiration_date . ") date entered<BR>";
		}

		// check_field() FUNCTION IN global.php
		check_field($flavor_name, 1, 'Flavor name');
		check_field($flavor_id, 1, 'Flavor#');

		if ( !$error_found ) {

			// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
			$flavor_name = escape_data($flavor_name);
			$flavor_id = escape_data($flavor_id);
			$suggested_level_other = escape_data($suggested_level_other);
			$use_in = escape_data($use_in);
			$other_info = escape_data($other_info);

			if ( $_POST['proj_id'] == "" ) {
				$sql = "INSERT INTO flavors (flavor_id, project_id, flavor_name, expiration_date, suggested_level_other, use_in, other_info) "
				. "VALUES ('" . $flavor_id . "', " . $_SESSION['pid'] . ", '" . $flavor_name . "', '" . $new_expiration_date . "', '" . $suggested_level_other . "', '" . $use_in . "', '" . $other_info . "')";
				mysql_query($sql, $link);   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			} else {
				$sql = "UPDATE flavors SET flavor_id = '" . $flavor_id . "', project_id = " . $_SESSION['pid'] . ", flavor_name = '" . $flavor_name . "', expiration_date = '" . $new_expiration_date . "', suggested_level_other = '" . $suggested_level_other . "', use_in = '" . $use_in . "', other_info = '" . $other_info . "' WHERE flavor_id = '" . $old_flavor_id . "' AND project_id = " . $_SESSION['pid'];
				mysql_query($sql, $link);   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}

			header("location: project_management_admin.sample.php");
			exit();

		}

	}

	elseif ( isset($_GET['fid']) and isset($_GET['proj_id']) ) {
		$sql = "SELECT * FROM flavors WHERE flavor_id = '" . $_GET['fid'] . "' AND project_id = " . $_GET['proj_id'];
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result);
		$flavor_id = $row['flavor_id'];
		$flavor_name = $row['flavor_name'];
		$old_flavor_id = $_GET['fid'];
		$proj_id = $_GET['proj_id'];
		$suggested_level_other = $row['suggested_level_other'];
		$use_in = $row['use_in'];
		$other_info = $row['other_info'];
		$expiration_date = $row['expiration_date'];
	}

	else {
		$flavor_id = "";
		$flavor_name = "";
		$proj_id = "";
		$old_flavor_id = "";
		$suggested_level_other = "";
		$use_in = "";
		$other_info = "";
		$six_months_out = date("Y-m-d", mktime(0, 0, 0, date("m")+6, date("d"), date("Y")));
		$expiration_date = date("n/j/Y", strtotime($six_months_out));
	}
	
}



$project_type_array = array("New","Revision","Resample","Other");
$project_type_num = array(1,2,3,4);

$priority_array = array("Low","Medium","High");
$priority_num = array(1,2,3);

$status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
$status_num = array(1,2,3,4,5);

//$suggested_level_array = array("Use as desired","Same as target","Other");
//$suggested_level_num = array(1,2,3);

$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");


include('inc_header.php');

?>



<script type="text/javascript">
$(function() {
	$('#datepicker1').datepicker({
		changeMonth: true,
		changeYear: true
	});
});
</script>



<B CLASS="header">Project requisition &#150; Add flavor</B><BR><BR>



<?php include('inc_project_header.php') ?>



<TABLE WIDTH=700 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="sales.src=salesOver.src"
		onMouseOut="sales.src=salesOut.src" 
		HREF="project_management_admin.sales.php"><IMG SRC="images/tabs/sales_out.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Sales info" NAME="sales"></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="client.src=clientOver.src"
		onMouseOut="client.src=clientOut.src" 
		HREF="project_management_admin.client.php"><IMG SRC="images/tabs/client_out.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Contact info" NAME="contact"></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="sample.src=sampleOver.src"
		onMouseOut="sample.src=sampleOver.src" 
		HREF="project_management_admin.sample.php"><IMG SRC="images/tabs/sample_over.gif" WIDTH=106 HEIGHT=18 BORDER=0 ALT="Sample info" NAME="sample"></TD>
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

	<TR><FORM NAME="flavors_form" METHOD="post" ACTION="project_management_admin_enter_flavor.php">
		<INPUT TYPE="hidden" NAME="proj_id" VALUE="<?php echo $proj_id; ?>">
		<INPUT TYPE="hidden" NAME="old_flavor_id" VALUE="<?php echo $old_flavor_id; ?>">
		<TD><B CLASS="black">Flavor#:</B></TD>
		<TD><INPUT TYPE="text" NAME="flavor_id" SIZE="30" VALUE="<?php echo $flavor_id ?>"></TD>
	</TR>

	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Flavor name:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE="text" NAME="flavor_name" SIZE="30" VALUE="<?php echo $flavor_name ?>"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Expiration date:</B></TD>
		<TD><INPUT TYPE="text" SIZE="30" NAME="expiration_date" id="datepicker1" VALUE="<?php
		if ( $expiration_date != '' ) {
			echo date("m/d/Y", strtotime($expiration_date));
		}
		?>"></TD>
		<TD></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Suggested starting use level:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE="text" NAME="suggested_level_other" SIZE="30" VALUE="<?php echo $suggested_level_other?>" <?php echo $form_status ?> MAXLENGTH=30></TD>
		<TD></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Use in:</B></TD>
		<TD><INPUT TYPE="text" NAME="use_in" SIZE="30" VALUE="<?php echo $use_in?>" <?php echo $form_status ?> MAXLENGTH=40></TD>
		<TD></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Other info:</B></TD>
		<TD><INPUT TYPE="text" NAME="other_info" SIZE="30" VALUE="<?php echo $other_info?>" <?php echo $form_status ?> MAXLENGTH=40></TD>
		<TD></TD>
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