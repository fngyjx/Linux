<?php 

include('global.php');
session_start();

if ( ! $_SESSION['uLoggedInCookie'] AND ! $_COOKIE["uLoggedInCookie"] ) {
	 header ("Location: login.php?out=1");
	 exit;
}

if ( isset($_SESSION['pid']) ) {
	unset($_SESSION['pid']);
	header("location: projects_history.php");
	exit();
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( $_GET['archives'] == 1 or $_POST['archives'] == 1 ) {
	$archives = 1;
} else {
	$archives = '';
}

$application_array = array("Baked Good","Beverage","Cereal","Confection","Dairy Product","Pet Food","Nutraceutical","Pharmaceutical","Prepared Food","Snack");
$application_num = array(1,2,3,4,5,6,7,8,9,10);

$status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
$status_num = array(1,2,3,4,5);

$follow_up_array = array("Hot","Warm","Cold","Won","Lost","Cancelled","");
$follow_up_num = array(1,2,3,4,5,6,7);

include('header.php');

?>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>

		<TD>

<B CLASS="header"></B>

<B CLASS="header">Completed</B> / <B><A HREF="projects_history.php?archives=1">Archives</A></B>

<BR><BR><BR>



<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<FORM METHOD="post" name="search" id="search" ACTION="projects_history_search.php" target="_blank">
	<INPUT TYPE="hidden" NAME="action" VALUE="search">
	<INPUT TYPE="hidden" NAME="archives" VALUE="<?php echo $archives;?>">

	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B>Project#:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT name="search_project_id" id="search_project_id" size="10"</TD>
	</TR>
	
	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>
	
	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B>Client:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT name="search_client" id="search_client" size="50">
		<INPUT type="hidden" id="client_id" name="client_id" value=""></TD>
	</TR>
	
	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>
	
	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B>Salesperson:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><SELECT NAME="salesperson">
		<?php
		//add Ron in the lists - Dec082009 jdu
		if ( $_SESSION['userTypeCookie'] == 2 ) {
			$sql = "SELECT * FROM users WHERE user_id = " . $_SESSION['user_id'];
		} else {
			echo "<OPTION VALUE=''></OPTION>";
			$sql = "SELECT * FROM users WHERE (user_type = 2 OR last_name = 'Gooding' OR last_name = 'Arb') AND active=1 ORDER BY user_type, last_name";
		}
		$result = mysql_query($sql, $link);
		if ( mysql_num_rows($result) > 0 ) {
			while ( $row = mysql_fetch_array($result) ) {
				if ( $_POST['salesperson'] == $row['user_id'] or $_GET['salesperson'] == $row['user_id'] ) {
					echo "<OPTION VALUE='" . $row['user_id'] . "' SELECTED>" . $row['first_name'] . " " . $row['last_name'] . "</OPTION>";
				} else {
					echo "<OPTION VALUE='" . $row['user_id'] . "'>" . $row['first_name'] . " " . $row['last_name'] . "</OPTION>";
				}
			}
		}
		?>
		</SELECT></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B>Company:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT NAME="company_id" id="company_id" type="hidden" value="">
			<INPUT name="company" id="search_company" type="text" size="50">
		</TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B>Flavor:</B></TD>
		<TD><IMG SRC="/images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT type="text" name="search_flavor" id="search_flavor" value="" size="50">
			<input type="hidden" name="flavor_id" id="flavor_id">
			<input type="hidden" name="flavor_name" id="flavor_name">
		</TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B>Application:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><SELECT NAME="application" id="application">
		<OPTION VALUE=''></OPTION>
			<?php 
			foreach ( $application_num as $value ) {
				if ( $value == $_POST['application'] ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $application_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $application_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT></TD>
	</TR>
	
	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>
	
	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B>Completed Date:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><NOBR>From: &nbsp;<INPUT type="text" name="date_from" id="date_from" SIZE="12"> &nbsp;&nbsp;To:&nbsp;<INPUT type="text" name="date_to" id="date_to" SIZE="12"></NOBR>
		</TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT TYPE="submit" VALUE="Search"> <INPUT TYPE="button" VALUE="Reset" onClick="window.location='projects_history.php'"></TD>
	</TR></FORM>

</TABLE>


</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>
</TD></TR></TABLE>

<?php include('footer.php'); ?>
<script type="text/javascript">
<!--
$(document).ready(function(){
	
	$("#search_project_id").autocomplete("search_project_id.php", {
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
	$("#search_project_id").result(function(event, data, formatted) {
		if (data) {
			$("#search_project_id").val(data[1]);
//			document.search.submit();
		}
	});
	$("#search_project_id").keypress(function(e) {
		if (e.which >= 32) {
//			$("#search_client").val('');
//			$("#company_id").val('');
//			$("#flavor_id").val('');
//			$("#application").val('');
		}
	});
	$("#search_client").autocomplete("search_client.php", {
		cacheLength: 1,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max: 50,
		multipleSeparator: "¬",
		scrollHeight: 350,
		selectFirst: false
	});
	$("#search_client").result(function(event, data, formatted) {
		if (data) {
			$("#search_client").val(data[0]);
			$("#client_id").val(data[1]);
//			document.search.submit();
		}
	});
	$("#search_client").keypress(function(e) {
		if (e.which >= 32) {
//			$("#search_project_id").val('');
//			$("#company_id").val('');
//			$("#flavor_id").val('');
//			$("#application").val('');
		}
	});
	$("#search_company").autocomplete("search_company.php", {
		cacheLength: 1,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 365,
		max: 50,
		multipleSeparator: "¬",
		scrollHeight: 350,
		selectFirst: false
	});
	$("#search_company").result(function(event, data, formatted) {
		if (data) {
			$("#search_company").val(data[0]);
			$("#company_id").val(data[1]);
//			document.search.submit();
		}
	});
	$("#search_company").keypress(function(e) {
		if (e.which >= 32) {
//			$("#search_project_id").val('');
//			$("#company_id").val('');
//			$("#flavor_id").val('');
//			$("#application").val('');
		}
	});
	
	$("#search_flavor").autocomplete("search_flavor.php", {
		cacheLength: 1,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 365,
		max: 50,
		multipleSeparator: "¬",
		scrollHeight: 350,
		selectFirst: false
	});
	$("#search_flavor").result(function(event, data, formatted) {
		if (data) {
			$("#search_flavor").val(data[0]);
			$("#flavor_id").val(data[1]);
			$("#flavor_name").val(data[2]);
//			document.search.submit();
		}
	});
	$("#search_flavor").keypress(function(e) {
		if (e.which >= 32) {
//			$("#search_project_id").val('');
//			$("#company_id").val('');
//			$("#flavor_id").val('');
//			$("#application").val('');
		}
	});
});

$(function() {
	$('#date_from').datepicker({
		 buttonImageOnly: true ,
		changeMonth: true,
		changeYear: true
	});
	$('#date_to').datepicker({
		 buttonImageOnly: true ,
		changeMonth: true,
		changeYear: true
	});
});

-->
</script>