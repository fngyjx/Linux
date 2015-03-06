<?php 

session_start();

include('global.php');
require_ssl();

if ( !isset($_SESSION['userTypeCookie']) ) {
	header ("Location: login.php?out=1");
	exit;
}


if ( isset($_SESSION['pid']) ) {
	unset($_SESSION['pid']);
	header("location: projects_lab.php");
	exit();
}



$application_array = array("Baked Good","Beverage","Cereal","Confection","Dairy Product","Pet Food","Nutraceutical","Pharmaceutical","Prepared Food","Snack");
$application_num = array(1,2,3,4,5,6,7,8,9,10);

$status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
$status_num = array(1,2,3,4,5);



include('header.php');

?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>

		<TD>

<B CLASS="header">Active</B><BR><BR>

<?php

$sql = "SELECT * FROM projects LEFT JOIN clients USING(client_id) LEFT JOIN companies USING(company_id) WHERE status = 3";
$result = mysql_query($sql, $link);

if ( mysql_num_rows($result) > 0 ) {

	$bg = 0; ?>
	
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">
		<TR>
			<TD></TD>
			<TD><B>Project#</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="15" HEIGHT="1"></TD>
			<TD><B>Company</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="15" HEIGHT="1"></TD>
			<TD><B>Application</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="15" HEIGHT="1"></TD>
			<TD><B>Status</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="15" HEIGHT="1"></TD>
			<TD><B>Due</B></TD>
		</TR>
		<TR>
			<TD COLSPAN=6><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<?php 

		while ( $row = mysql_fetch_array($result) ) {
			if ( $bg == 1 ) {
				$bgcolor = "#FFFFFF";
				$bg = 0;
			}
			else {
				$bgcolor = "#EFEFEF";
				$bg = 1;
			} ?>

			<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
				<TD>
				</TD>
				<TD><A HREF="project_info.php?new_id=<?php echo $row['project_id'] ?>"><?php echo substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) ?></A></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="15" HEIGHT="1"></TD>
				<TD><?php echo $row['company'] ?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="15" HEIGHT="1"></TD>
				<TD><?php
				foreach ( $application_num as $value ) {
					if ( $value == $row['application'] ) {
						echo $application_array[$value-1];
					}
				} ?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="15" HEIGHT="1"></TD>
				<TD><?php
				foreach ( $status_num as $value ) {
					if ( $value == $row['status'] ) {
						echo $status_array[$value-1];
					}
				} ?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="15" HEIGHT="1"></TD>
				<TD><?php echo date("m/d/Y", strtotime($row['due_date'])) ?></TD>
			</TR>

		<?php } ?>

		<TR>
			<TD COLSPAN=6><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="1"></TD>
		</TR>
	</TABLE>

<?php } else {
	print("No open projects");
} ?>



		</TD>
	</TR>
</TABLE>



<?php include('footer.php'); ?>