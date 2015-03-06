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
	header("location: projects.php");
	exit();
}



if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
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

<B CLASS="header">Active</B> / <B><A HREF="projects_history.php">Completed</A></B><BR><BR><BR>


<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


<?php

if ( !$_GET['sortby'] ) {
	$filter = " ORDER BY due_date";
}
else {
	if ( $_GET['sortby'] == "p" ) {
		$filter = " ORDER BY project_id DESC";
	}
	elseif ( $_GET['sortby'] == "c" ) {
		$filter = " ORDER BY company";
	}
	elseif ( $_GET['sortby'] == "sp" ) {
		$filter = " ORDER BY first_name";
	}
	elseif ( $_GET['sortby'] == "a" ) {
		$filter = " ORDER BY lab_first";
	}
	elseif ( $_GET['sortby'] == "s" ) {
		$filter = " ORDER BY status";
	}
	elseif ( $_GET['sortby'] == "cp" ) {
		$filter = " ORDER BY sent_to_front DESC";
	}
	elseif ( $_GET['sortby'] == "d" ) {
		$filter = " ORDER BY  due_date";
	}
	elseif ( $_GET['sortby'] == "dc" ) {
		$filter = " ORDER BY date_created DESC";
	}
}

if ( $_GET['sortby'] == "a" ) {
	$lab_filter = " LEFT JOIN lab_assignees USING(project_id) ";
	$lab_fields = "";
}
else {
	$lab_filter = '';
	$lab_fields = '';
}

if ( $_SESSION['userTypeCookie'] == 1 ) {
	$sales_filter = " WHERE (status < 4 OR status IS NULL)";
}
else {
	$sales_filter = " WHERE (status < 4 OR status IS NULL) AND salesperson = " . $_SESSION['user_id'];
}

$sql = "SELECT project_id, date_created, company, parent_id, project_type, status, due_date, summary, users.last_name, users.first_name, sent_to_front, ( SELECT first_name
FROM lab_assignees
LEFT JOIN users
USING ( user_id ) 
WHERE lab_assignees.project_id = projects.project_id
ORDER BY assignee_id
LIMIT 1
) AS  'lab_first'
FROM projects
LEFT JOIN clients
USING ( client_id ) 
LEFT JOIN companies
USING ( company_id ) 
LEFT JOIN users ON projects.salesperson = users.user_id " . $sales_filter . $filter;
$result = mysql_query($sql, $link);

if ( mysql_num_rows($result) > 0 ) {

	$bg = 0; ?>
	
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">
		<TR>
			<TD><B><A HREF="projects.php?sortby=p">Project#</A></B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B><A HREF="projects.php?sortby=dc">Created</A></B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B><A HREF="projects.php?sortby=c">Company</A></B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B><A HREF="projects.php?sortby=sp">Sales</A></B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B><A HREF="projects.php?sortby=a">Assigns</A></B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B><A HREF="projects.php?sortby=s">Status</A></B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B><A HREF="projects.php?sortby=cp">Completed</A></B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B><A HREF="projects.php?sortby=d">Due</A></B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B>Summary</B></TD>
			<TD COLSPAN=2></TD>
		</TR>
		<TR>
			<TD COLSPAN=17><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<?php

		$x = mysql_num_rows($result);
		$z = 0;

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

				<TD><A HREF="project_info.php?new_id=<?php echo $row['project_id'] ?>"><?php echo substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) ?></A></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><?php echo date("m/d/Y", strtotime($row['date_created'])) ?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><?php
				echo $row['company'];
				if ( $row["project_type"] == 2 ) {
					echo " <I STYLE='color:#999999'>(Revision of " . substr($row['parent_id'], 0, 2) . "-" . substr($row['parent_id'], -3) . ")</I>";
				} elseif ( $row["project_type"] == 3 ) {
					echo " <I STYLE='color:#999999'>(Resample of " . substr($row['parent_id'], 0, 2) . "-" . substr($row['parent_id'], -3) . ")</I>";
				}
				?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><?php
				echo "<A HREF='mailto:" . $row['email'] . "?subject=Regarding Project# " . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) . " (" . $row['company'] . ")'>" . strtoupper(substr($row['first_name'],0,1) . substr($row['last_name'],0,1)) . "</A>";
				?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>

				<TD><?php
				$sql = "SELECT * FROM lab_assignees LEFT JOIN users USING(user_id) WHERE project_id = " . $row['project_id'];
				$result_lab = mysql_query($sql, $link);
				$c = mysql_num_rows($result_lab);
				if ( $c == 0 ) {
					echo "<I>None</I>";
				}
				else {
					$i = 0;
					while ( $row_lab = mysql_fetch_array($result_lab) ) {
						if ( $row_lab['last_name'] == 'Tang' ) {
							$lab = "Tang";
						} else {
							$lab = strtoupper(substr($row_lab['first_name'],0,1) . substr($row_lab['last_name'],0,1));
						}
						echo "<A HREF='mailto:" . $row_lab['email'] . "?subject=Regarding Project# " . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) . " (" . $row['company'] . ")'>" . $lab . "</A>";
						$i++;
						if ( $i < $c ) {
							echo "; ";
						}
					}
				}
				?></TD>

				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>

				<TD><NOBR><?php
				foreach ( $status_num as $value ) {
					if ( $value == $row['status'] ) {
						echo $status_array[$value-1];
					}
				} ?></NOBR></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><?php
				if ( $row['sent_to_front'] != '' and $row['status'] > 2 ) {
					echo date("m/d/Y", strtotime($row['sent_to_front']));
				}
				?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><?php echo date("m/d/Y", strtotime($row['due_date'])) ?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><?php echo $row['summary'];?></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD><FORM>
				<TD><?php
				if ( $_SESSION['userTypeCookie'] == 1 ) { ?>
					<INPUT TYPE="button" VALUE="Cancel" onClick="cancel_project(<?php echo $row['project_id'];?>)" STYLE="font-size:7pt">
				<?php } ?></TD></FORM>
			</TR>

		<?php } ?>

	</TABLE><BR>

<?php } else {
	print("No open projects<BR>");
} ?>


		</TD>
	</TR>
</TABLE>



<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

function cancel_project(pid) {
	if ( confirm('Are you sure you want to cancal this project?') ) {
		document.location.href = "project_info.php?stat=5&pid=" + pid
	}
}

 // End -->
</SCRIPT>



<?php include('footer.php'); ?>