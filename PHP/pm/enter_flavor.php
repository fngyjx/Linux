<?php

session_start();

include('global.php');
require_ssl();



if ( isset($_SESSION['pid']) ) {

	if ( !empty($_POST) ) {

		$flavor_name = $_POST['flavor_name'];
		$flavor_id = $_POST['flavor_id'];
		$lot_code = escape_data($_POST['lot_code']);
		$proj_id = $_POST['proj_id'];
		$old_flavor_id = $_POST['old_flavor_id'];
		$suggested_level_other = $_POST['suggested_level_other'];
		$use_in = $_POST['use_in'];
		$other_info = $_POST['other_info'];
		$expiration_dateA=explode("/",$_POST['expiration_date']);
		$month = $expiration_dateA[0];
		$day = $expiration_dateA[1];
		$year = $expiration_dateA[2];
		$expiration_date = $year . "-" . $month . "-" . $day;

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
			// if a new flavor, add it in to flavor_distinct table
			$sql="SELECT * FROM flavor_distinct WHERE flavor_id='".$flavor_id."'";
			$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL: $sql<br />");
			if ( mysql_num_rows($result) == 0 ) {
				$sql="INSERT INTO flavor_distinct VALUES('".$flavor_id."','".$flavor_name."')";
				mysql_query($sql,$link) or die (mysql_error() . " Failed execute SQL: $sql <br />");
			}
			if ( $_POST['proj_id'] == "" ) {
				$sql = "INSERT INTO flavors (flavor_id, project_id, flavor_name, lot_code, expiration_date, suggested_level_other, use_in, other_info) "
				. "VALUES ('" . $flavor_id . "', " . $_SESSION['pid'] . ", '" . $flavor_name . "','".$lot_code."', '" . $expiration_date . "', '" . $suggested_level_other . "', '" . $use_in . "', '" . $other_info . "')";
				mysql_query($sql, $link);   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			} else {
				$sql = "UPDATE flavors SET flavor_id = '" . $flavor_id . "', project_id = " . $_SESSION['pid'] . ", flavor_name = '" . $flavor_name . "', lot_code = '".$lot_code."', expiration_date = '" . $expiration_date . "', suggested_level_other = '" . $suggested_level_other . "', use_in = '" . $use_in . "', other_info = '" . $other_info . "' WHERE flavor_id = '" . $old_flavor_id . "' AND project_id = " . $_SESSION['pid'];
				mysql_query($sql, $link);   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}

			header("location: sample_info.php");
			exit();

		}

	}

	elseif ( isset($_GET['fid']) and isset($_GET['proj_id']) ) {
		$sql = "SELECT * FROM flavors WHERE flavor_id = '" . $_GET['fid'] . "' AND project_id = " . $_GET['proj_id'];
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result);
		$flavor_id = $row['flavor_id'];
		$flavor_name = $row['flavor_name'];
		$lot_code = $row['lot_code'];
		$old_flavor_id = $_GET['fid'];
		$proj_id = $_GET['proj_id'];
		$suggested_level_other = $row['suggested_level_other'];
		$use_in = $row['use_in'];
		$other_info = $row['other_info'];
		$month = date("m", strtotime($row['expiration_date']));
		$day = date("d", strtotime($row['expiration_date']));
		$year = date("Y", strtotime($row['expiration_date']));
		$expiration_date=$row['expiration_date'];
	}

	else {
		$flavor_id = "";
		$flavor_name = "";
		$lot_code = "";
		$proj_id = "";
		$old_flavor_id = "";
		$suggested_level_other = "";
		$use_in = "";
		$other_info = "";
		$six_months_out = date("Y-m-d", mktime(0, 0, 0, date("m")+6, date("d"), date("Y")));
		$month = date("m", strtotime($six_months_out));
		$day = date("d", strtotime($six_months_out));
		$year = date("Y", strtotime($six_months_out));
		$expiration_date=$six_months_out;
	}
	
}

include('header.php');

?>



<B CLASS="header">Project requisition &#150; Add flavor</B><BR><BR>



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
		HREF="client_info.php"><IMG SRC="images/tabs/client_out.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Client info" NAME="client"></TD>
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

	<TR><FORM METHOD="post" ACTION="enter_flavor.php">
		<INPUT TYPE="hidden" NAME="proj_id" VALUE="<?php echo $proj_id; ?>">
		<INPUT TYPE="hidden" NAME="old_flavor_id" VALUE="<?php echo $old_flavor_id; ?>">
		<TD><B CLASS="black">Flavor#:</B></TD>
		<TD><INPUT TYPE="text" NAME="flavor_id" id="flavor_id" SIZE="30" VALUE="<?php echo $flavor_id ?>"></TD>
	</TR>

	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Flavor name:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE="text" NAME="flavor_name" id="flavor_name" SIZE="30" VALUE="<?php echo $flavor_name ?>"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Sample Lot Code:</B></TD>
		<TD><input type="text" name="lot_code" id="lot_code" value="<?php echo $lot_code;?>" size="25">
		</TD>
		<TD></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Expiration date:</B></TD>
		<TD><input type="text" name="expiration_date" id="expiration_date" value="<?php echo date("m/d/Y",strtotime($expiration_date))?>" size="15">
		</TD>
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

<?php include('footer.php'); ?>


<script language="javascript">
<!-- 
$(document).ready(function(){
	
	$("#flavor_id").autocomplete("search_flavor.php", {
		cacheLength: 1,
		width: 365,
		max: 50,
		scroll: true,
		scrollHeight: 350,
		multipleSeparator: "¬",
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		selectFirst: false
	});
	$("#flavor_id").result(function(event, data, formatted) {
		if (data) {
			$("#flavor_id").val(data[1]);
			$("#flavor_name").val(data[2]);
			$("#lot_code").val(data[1]);
//			document.search.submit();
		}
	});
	
	$("#lot_code").autocomplete("search_flavors.php", {
		cacheLength: 1,
		width: 365,
		max: 50,
		scroll: true,
		scrollHeight: 350,
		multipleSeparator: "¬",
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		selectFirst: false,
		extraParams: { f_id:$("#flavor_id").val(), project_id:<?php echo $_SESSION['pid'];?> }
	});
	$("#lot_code").result(function(event, data, formatted) {
		if (data) {
			$("#lot_code").val(data[1]);
			$("#expiration_date").val(data[2]);
//			document.search.submit();
		}
	});
});

$(function() {
	$('#expiration_date').datepicker({
		//buttonImageOnly: true ,
		changeMonth: true,
		changeYear: true
	});
});

-->
</script>