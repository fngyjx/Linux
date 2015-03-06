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
if ( $_GET['archives'] == 1 or $_POST['archives'] == 1 ) {
	$archives = 1;
} else {
	$archives = '';
}



if ( $_GET['fup'] == "1" ) {   // SALESPERSON HAS FOLLOWED UP
	$sql = "UPDATE projects SET sales_follow_up = 1 WHERE project_id = " . $_GET['pid'];
	mysql_query($sql, $link);
	$_SESSION['note'] = "Project follow up successfully saved<BR>";
	header("location: project_management_completed.php");
	exit();
}
	
	

if ( !empty($_POST['follow_up']) ) {
	$sql = "UPDATE projects SET follow_up = " . $_POST['follow_up'] . " WHERE project_id = " . $_POST['project_id'];
	mysql_query($sql, $link);
	$note = "Follow up successfully saved<BR>";
}



$application_array = array("Baked Good","Beverage","Cereal","Confection","Dairy Product","Pet Food","Nutraceutical","Pharmaceutical","Prepared Food","Snack");
$application_num = array(1,2,3,4,5,6,7,8,9,10);

$status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
$status_num = array(1,2,3,4,5);

$follow_up_array = array("Hot","Warm","Cold","Won","Lost","Cancelled","");
$follow_up_num = array(1,2,3,4,5,6,7);


include("inc_header.php");

?>




<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>

		<TD>


<?php if ( $archives == 1 ) { ?>
	<B CLASS="header">Archives</B> / <B><A HREF="project_management_completed.php">Most recent completed</A></B>
<?php } else { ?>
	<B CLASS="header">Completed</B> / <B><A HREF="project_management_completed.php?archives=1">Archives</A></B>
<?php }

//if ( $_SESSION['userTypeCookie'] == 1 or $_SESSION['userTypeCookie'] == 2 ) { ?>
	<!-- / <B><A HREF="projects.php">Active</A></B> -->
<?php //} ?>

<BR><BR><BR>



<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
	<FORM METHOD="post" ACTION="project_management_completed.php">
	<INPUT TYPE="hidden" NAME="search" VALUE="search">
	<INPUT TYPE="hidden" NAME="archives" VALUE="<?php echo $archives;?>">

	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B>Salesperson:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><SELECT NAME="salesperson">
		<?php
		if ( $_SESSION['userTypeCookie'] == 2 ) {
			$sql = "SELECT * FROM users WHERE user_id = " . $_SESSION['user_id'];
		} else {
			echo "<OPTION VALUE=''></OPTION>";
			$sql = "SELECT * FROM users WHERE user_type = 2 OR last_name = 'Gooding' ORDER BY user_type, last_name";
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
		<TD ALIGN=RIGHT><B>Customer:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><SELECT NAME="customer_id">
		<OPTION VALUE=''></OPTION>
		<?php
		if ( $_SESSION['userTypeCookie'] == 2 ) {
			$name_clause = " WHERE user_id = " . $_SESSION['user_id'];
			$sql = "SELECT DISTINCT name, customer_id FROM customers LEFT JOIN customers_users USING(customer_id) " . $name_clause . " ORDER BY name";
		} else {
			$sql = "SELECT DISTINCT name, customer_id FROM customers ORDER BY name";
		}
		$result = mysql_query($sql, $link);
		if ( mysql_num_rows($result) > 0 ) {
			while ( $row = mysql_fetch_array($result) ) {
				if ( $_POST['customer_id'] == $row['customer_id'] or $_GET['customer_id'] == $row['customer_id'] ) {
					echo "<OPTION VALUE='" . $row['customer_id'] . "' SELECTED>" . $row['name'] . "</OPTION>";
				} else {
					echo "<OPTION VALUE='" . $row['customer_id'] . "'>" . $row['name'] . "</OPTION>";
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
		<TD ALIGN=RIGHT><B>Flavor:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><SELECT NAME="flavor_id">
		<OPTION VALUE=''></OPTION>
		<?php
		$sql = "SELECT DISTINCT flavor_id, flavor_name FROM flavors ORDER BY flavor_id, flavor_name";
		$result = mysql_query($sql, $link);
		if ( mysql_num_rows($result) > 0 ) {
			while ( $row = mysql_fetch_array($result) ) {
				if ( $_POST['flavor_id'] == $row['flavor_id'] or $_GET['flavor_id'] == $row['flavor_id'] ) {
					echo "<OPTION VALUE='" . $row['flavor_id'] . "' SELECTED>" . $row['flavor_id'] . " " . $row['flavor_name'] . "</OPTION>";
				} else {
					echo "<OPTION VALUE='" . $row['flavor_id'] . "'>" . $row['flavor_id'] . " " . $row['flavor_name'] . "</OPTION>";
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
		<TD ALIGN=RIGHT><B>Application:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><SELECT NAME="application">
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
		<TD></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT TYPE="submit" VALUE="Search"> <INPUT TYPE="button" VALUE="Reset" onClick="window.location='project_management_completed.php'"></TD>
	</TR></FORM>

</TABLE>


</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>



<?php

if ( $_POST['salesperson'] != "" ) {
	$sales_filter = " AND salesperson = " . $_POST['salesperson'];
	$sales = $_POST['salesperson'];
} elseif ( $_GET['salesperson'] != "" ) {
	$sales_filter = " AND salesperson = " . $_GET['salesperson'];
	$sales = $_GET['salesperson'];
} else {
	$sales_filter = "";
	$sales = "";
}

if ( $_POST['customer_id'] != "" ) {
	$name_filter = " AND customer_id = " . $_POST['customer_id'];
	$name = $_POST['customer_id'];
} elseif ( $_GET['customer_id'] != "" ) {
	$name_filter = " AND customer_id = " . $_GET['customer_id'];
	$name = $_GET['customer_id'];
} else {
	$name_filter = "";
	$name = "";
}

if ( $_POST['flavor_id'] != "" ) {
	$flavor_clause = " LEFT JOIN flavors USING(project_id)";
	$flavor_filter = " AND flavor_id = '" . $_POST['flavor_id'] . "'";
	$flavor = $_POST['flavor_id'];
} elseif ( $_GET['flavor_id'] != "" ) {
	$flavor_clause = " LEFT JOIN flavors USING(project_id)";
	$flavor_filter = " AND flavor_id = '" . $_GET['flavor_id'] . "'";
	$flavor = $_GET['flavor_id'];
} else {
	$flavor_clause = "";
	$flavor_filter = "";
	$flavor = "";
}

if ( $_POST['application'] != "" ) {
	$application_filter = " AND application = " . $_POST['application'];
	$application = $_POST['application'];
} elseif ( $_GET['application'] != "" ) {
	$application_filter = " AND application = " . $_GET['application'];
	$application = $_GET['application'];
} else {
	$application_filter = "";
	$application = "";
}


if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
}



//if ( !empty($_POST) or ( $_GET['salesperson'] != "" or $_GET['customer_id'] != "" or $_GET['flavor_id'] != "" or $_GET['application'] != "" ) ) {
if ( !empty($_POST) or ( $_GET['salesperson'] != "" or $_GET['customer_id'] != "" or $_GET['flavor_id'] != "" or $_GET['application'] != "" or $_GET['sortby'] != "" ) ) {

	if ( !$_GET['sortby'] ) {
		$filter = " ORDER BY due_date";
	}
	else {
		if ( $_GET['sortby'] == "p" ) {
			$filter = " ORDER BY project_id DESC";
		}
		elseif ( $_GET['sortby'] == "c" ) {
			$filter = " ORDER BY name";
		}
		elseif ( $_GET['sortby'] == "sp" ) {
			$filter = " ORDER BY first_name";
		}
		elseif ( $_GET['sortby'] == "a" ) {
			$filter = " ORDER BY lab_first";
		}
		elseif ( $_GET['sortby'] == "s" ) {
			$filter = " ORDER BY follow_up";
		}
		elseif ( $_GET['sortby'] == "d" ) {
			$filter = " ORDER BY  due_date";
		}
		elseif ( $_GET['sortby'] == "dc" ) {
			$filter = " ORDER BY sent_to_front DESC";
		}
		elseif ( $_GET['sortby'] == "cp" ) {
			$filter = " ORDER BY date_created DESC";
		}
	}


	// SEARCH ARCHIVES OR WITHIN THE PAST 60 DAYS
	$sixty_days_ago = mktime(0, 0, 0, date("m"), date("d")-60, date("y"));
	$search_start = date("Y-m-d", $sixty_days_ago);
	if ( $archives == 1 ) {
		$date_filter =  " date_created < '" . $search_start . "'";
	} else {
		$date_filter =  " date_created >= '" . $search_start . "'";
	}



	$sql = "SELECT project_id, date_created, name, parent_id, project_type, status, due_date, summary, users.last_name, users.first_name, lab_comments,		
	sent_to_front, salesperson, shipped_date, follow_up, sales_follow_up, ( SELECT first_name
	FROM lab_assignees
	LEFT JOIN users
	USING ( user_id ) 
	WHERE lab_assignees.project_id = projects.project_id
	ORDER BY assignee_id
	LIMIT 1
	) AS  'lab_first' 
	FROM projects 
	LEFT JOIN customer_contacts USING(contact_id) 
	LEFT JOIN customers USING(customer_id)
	LEFT JOIN users ON projects.salesperson = users.user_id " . $flavor_clause . " WHERE " . $date_filter . " AND status >= 4 " . $sales_filter . $name_filter . $flavor_filter . $application_filter . $filter;
	$result = mysql_query($sql, $link);   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	if ( mysql_num_rows($result) > 0 ) {

		$bg = 0; ?>
	
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">
			<TR>
				<TD><B><A HREF="project_management_completed.php?sortby=dc&salesperson=<?php echo $sales;?>&customer_id=<?php echo $name;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Created</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B><A HREF="project_management_completed.php?sortby=cp&salesperson=<?php echo $sales;?>&customer_id=<?php echo $name;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Completed</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B><A HREF="project_management_completed.php?sortby=p&salesperson=<?php echo $sales;?>&customer_id=<?php echo $name;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Project#</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B><A HREF="project_management_completed.php?sortby=c&salesperson=<?php echo $sales;?>&customer_id=<?php echo $name;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Customer</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B><A HREF="project_management_completed.php?sortby=sp&salesperson=<?php echo $sales;?>&customer_id=<?php echo $name;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Sales</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B><A HREF="project_management_completed.php?sortby=a&salesperson=<?php echo $sales;?>&customer_id=<?php echo $name;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Assigns</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B>Summary</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<!-- <TD><B>Shipped</B></TD> -->
<!-- 				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD> -->
				<TD><B><A HREF="project_management_completed.php?sortby=s&salesperson=<?php echo $sales;?>&customer_id=<?php echo $name;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Status</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B>Comments</B></TD>
				<?php if ( $_SESSION['userTypeCookie'] < 3 ) { ?>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><B>Followed up</B></TD>
				<?php } ?>
			</TR>
			<TR>
				<TD COLSPAN=15><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
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
					<TD><?php echo date("m/d/Y", strtotime($row['date_created'])) ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><?php
					if ( $row['sent_to_front'] != '' and $row['status'] > 2 ) {
						echo date("m/d/Y", strtotime($row['sent_to_front']));
					}
					?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><A HREF="project_management_admin.sales.php?new_id=<?php echo $row['project_id'] ?>"><?php echo substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) ?></A></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><!-- <A HREF="project_management_admin.sales.php?new_id=<?php //echo $row['project_id'] ?>"> --><?php echo $row['name'] ?><!-- </A> --></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD>
					<?php
					$sql = "SELECT first_name, last_name, email FROM users WHERE user_id = " . $row['salesperson'];
					$result_sales = mysql_query($sql, $link);
					$c = mysql_num_rows($result_sales);   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					if ( $c != 0 ) {
						$row_sales = mysql_fetch_array($result_sales);
						echo "<A HREF='mailto:" . $row_sales['email'] . "?subject=Regarding Project# " . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) . " (" . $row['name'] . ")'>" . strtoupper(substr($row_sales['first_name'],0,1) . substr($row_sales['last_name'],0,1)) . "</A>";
					}
					?>
					</TD>
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
							echo "<A HREF='mailto:" . $row_lab['email'] . "?subject=Regarding Project# " . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) . " (" . $row['name'] . ")'>" . $lab . "</A>";
							$i++;
							if ( $i < $c ) {
								echo "; ";
							}
						}
					}
					?></TD>

					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>

					<TD><?php echo $row['summary']?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<!-- <TD> --><?php
 					//if ( $row['status'] == 5 ) {
 					//	echo "<I>Cancelled</I>";
					//} else {
					//	echo date("m/d/Y", strtotime($row['shipped_date']));
					//}
					?><!-- </TD> -->
					<!-- <TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD> -->
					<FORM ACTION="project_management_completed.php" METHOD="post">
					<TD>
					<INPUT TYPE="hidden" NAME="project_id" VALUE="<?php echo $row['project_id'];?>">
					<INPUT TYPE="hidden" NAME="salesperson" VALUE="<?php echo $_POST['salesperson'];?>">
					<INPUT TYPE="hidden" NAME="customer_id" VALUE="<?php echo $_POST['customer_id'];?>">
					<INPUT TYPE="hidden" NAME="flavor_id" VALUE="<?php echo $_POST['flavor_id'];?>">
					<INPUT TYPE="hidden" NAME="application" VALUE="<?php echo $_POST['application'];?>">
					<INPUT TYPE="hidden" NAME="archives" VALUE="<?php echo $archives;?>">
					<?php if ( $_SESSION['userTypeCookie'] == 1 or $_SESSION['userTypeCookie'] == 2 ) { ?>
						<SELECT NAME="follow_up" onChange="submit()" STYLE="font-size:7pt">
						<?php
						foreach ( $follow_up_num as $value ) {
							if ( $value == $row['follow_up'] ) { ?>
								<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $follow_up_array[$value-1];?></OPTION>
							<?php } else { ?>
								<OPTION VALUE="<?php echo $value?>"><?php echo $follow_up_array[$value-1];?></OPTION>
							<?php }
						} ?>
						</SELECT>
					<?php } else {
						echo $follow_up_array[$row['follow_up']-1];
					}
					?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><?php echo $row['lab_comments']?></TD>
					<?php if ( $_SESSION['userTypeCookie'] < 3 ) { ?>
						<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
						<TD>
						<?php if ( ($_SESSION['user_id'] == $row['salesperson']) and $row['status'] == 4 and $row['sales_follow_up'] == 0 ) { ?>
							<INPUT TYPE="button" VALUE="Followed up" onClick="window.location='project_management_completed.php?fup=1&pid=<?php echo $row['project_id'];?>'">
						<?php } elseif ( $row['status'] == 4 and $row['sales_follow_up'] == 0 ) { ?>
							<I><NOBR>No follow up</NOBR></I>
						<?php } elseif ( $row['status'] == 4 and $row['sales_follow_up'] == 1 ) { ?>
							<I><NOBR>Followed up</NOBR></I>
						<?php } else { ?>
							<I>N/A</I>
						<?php } ?>
						</TD></FORM>
					<?php } ?>
				</TR>

			<?php } ?>

		</TABLE>

	<?php } else {
		if ( $archives == 1 ) {
			print("No archives yet<BR><BR>");
		} else {
			print("No projects match search criteria<BR><BR>");
		}
	}

} ?>



		</TD>
	</TR>
</TABLE><BR><BR>




<?php include("inc_footer.php"); ?>