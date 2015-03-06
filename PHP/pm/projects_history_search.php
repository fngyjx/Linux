<?php 

include('global.php');
require_ssl();
session_start();

if ( ! $_SESSION['uLoggedInCookie'] AND ! $_COOKIE["uLoggedInCookie"]  ) {
	header ("Location: login.php?out=1");
	exit;
}


if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
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

<B CLASS="header">Completed</B>

<BR><BR><BR>

<?php
//print_r($_REQUEST);
//print_r($_SESSION);
if ( $_REQUEST['search_project_id'] != "" ) {
	$pid_filter = " AND project_id = " . $_POST['search_project_id'];
	$pid = $_POST['project_id'];
} else {
	$pid_filter = "";
	$pid = "";
}

if ( $_REQUEST['search_client'] != "" and  $_REQUEST['client_id'] != "" ) {
	$cid_filter = " AND client_id = " . $_POST['client_id'];
	$cid = $_POST['client_id'];
} else {
	$cid_filter = "";
	$cid = "";
}

if ( $_REQUEST['salesperson'] != "" ) {
	$sales_filter = " AND salesperson = " . $_POST['salesperson'];
	$sales = $_POST['salesperson'];
} else {
	$sales_filter = "";
	$sales = "";
}

if ( $_REQUEST['company_id'] != "" ) {
	$company_filter = " AND company_id = " . $_POST['company_id'];
	$company = $_POST['company_id'];
} else {
	$company_filter = "";
	$company = "";
}

if ( $_REQUEST['flavor_id'] != "" ) {
	$flavor_clause = " LEFT JOIN flavors USING(project_id)";
	$flavor_filter = " AND flavor_id = '" . $_POST['flavor_id'] . "'";
	$flavor = $_POST['flavor_id'];
}  else {
	$flavor_clause = "";
	$flavor_filter = "";
	$flavor = "";
}

if ( $_REQUEST['application'] != "" ) {
	$application_filter = " AND application = " . $_POST['application'];
	$application = $_POST['application'];
}  else {
	$application_filter = "";
	$application = "";
}

if ( $_REQUEST['date_from'] != "" ) {
	$date_fromA=explode("/", escape_data($_POST['date_from']));
	$date_from = mktime(0,0,0,$date_fromA[0],$date_fromA[1],$date_fromA[2]);
	$date_from = date("Y-m-d",$date_from);
	$date_filter = " AND shipped_date >= '" . $date_from ."'";
}  else {
	$date_filter = "";
}

if ( $_REQUEST['date_to'] != "" ) {
	$date_toA=explode("/", escape_data($_POST['date_to']));
	$date_to = mktime(0,0,0,$date_toA[0],$date_toA[1],$date_toA[2]);
	$date_to = date("Y-m-d",$date_to);
	$date_filter .= " AND shipped_date <= '" . $date_to ."'";
}  

if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
}

//if ( !empty($_POST) or ( $_GET['salesperson'] != "" or $_GET['company_id'] != "" or $_GET['flavor_id'] != "" or $_GET['application'] != "" ) ) {
if ( !empty($_POST) or ( $_GET['project_id'] != "" or $_GET['client_id'] != "" or $_GET['salesperson'] != "" or $_GET['company_id'] != "" or $_GET['flavor_id'] != "" or $_GET['application'] != "" or $_GET['sortby'] != "" ) ) {
	
	if ( !$_GET['sortby'] ) {
		$filter = " ORDER BY due_date";
	}
	else {
		if ( $_GET['sortby'] == "p" ) {
			$filter = " ORDER BY project_id DESC";
		}
		elseif ( $_GET['sortby'] == "c" ) {
			$filter = " ORDER BY Name, company";
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

	$sql = "SELECT project_id, client_id, date_created, company, concat(clients.first_name,' ',clients.last_name) as Name, 
	parent_id, project_type, status, due_date, summary, lab_comments,		
	sent_to_front, salesperson, shipped_date, follow_up, sales_follow_up, follow_up_notes,next_follow_up_date
	FROM projects 
	LEFT JOIN clients USING(client_id) 
	LEFT JOIN companies USING(company_id)
	" . $flavor_clause . " WHERE status >= 4 " . $pid_filter . $cid_filter . $sales_filter . $company_filter . $flavor_filter . $application_filter . $date_filter . $filter;
//	echo "<br /> $sql<br />";
	$result = mysql_query($sql, $link) or die ( mysql_error() ." Failed execute SQL : $sql <br />");   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	if ( mysql_num_rows($result) > 0 ) {

		$bg = 0; ?>
	
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<TR>
				<TD><B><A HREF="projects_history.php?sortby=dc&salesperson=<?php echo $sales;?>&company_id=<?php echo $company;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Created</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B><A HREF="projects_history.php?sortby=cp&salesperson=<?php echo $sales;?>&company_id=<?php echo $company;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Completed</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B><A HREF="projects_history.php?sortby=p&salesperson=<?php echo $sales;?>&company_id=<?php echo $company;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Project#</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B><A HREF="projects_history.php?sortby=c&salesperson=<?php echo $sales;?>&company_id=<?php echo $company;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Clients<br />/Company</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B><A HREF="projects_history.php?sortby=sp&salesperson=<?php echo $sales;?>&company_id=<?php echo $company;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Sales</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B><A HREF="projects_history.php?sortby=a&salesperson=<?php echo $sales;?>&company_id=<?php echo $company;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Assigns</A></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B>Summary</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B><A HREF="projects_history.php?sortby=s&salesperson=<?php echo $sales;?>&company_id=<?php echo $company;?>&flavor_id=<?php echo $flavor;?>&application=<?php echo $application;?>&archives=<?php echo $archives;?>">Status_List</A></B></TD>
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
			$item=0;
			while ( $row = mysql_fetch_array($result) ) {
				$item++;
				if ( $bg == 1 ) {
					$bgcolor = "#FFFFFF";
					$bg = 0;
				}
				else {
					$bgcolor = "#EFEFEF";
					$bg = 1;
				} ?>

				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN="TOP">
					<TD><?php echo date("m/d/Y", strtotime($row['date_created'])) ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><?php
					if ( $row['sent_to_front'] != '' and $row['status'] > 2 ) {
						echo date("m/d/Y", strtotime($row['sent_to_front']));
					}
					?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><A HREF="project_info.php?new_id=<?php echo $row['project_id'] ?>"><?php echo substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) ?></A></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><A HREF="client_projects_history.php?cid=<?php echo $row['client_id'];?>"><?php echo $row['Name']."</A><br />/".$row['company'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD>
					<?php
					$sql = "SELECT first_name, last_name, email FROM users WHERE user_id = " . $row['salesperson'];
					$result_sales = mysql_query($sql, $link);
					$c = mysql_num_rows($result_sales);   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					if ( $c != 0 ) {
						$row_sales = mysql_fetch_array($result_sales);
						echo "<A HREF='mailto:" . $row_sales['email'] . "?subject=Regarding Project# " . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) . " (" . $row['company'] . ")'>" . strtoupper(substr($row_sales['first_name'],0,1) . substr($row_sales['last_name'],0,1)) . "</A>";
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
							echo "<A HREF='mailto:" . $row_lab['email'] . "?subject=Regarding Project# " . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) . " (" . $row['company'] . ")'>" . $lab . "</A>";
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
					<TD>
					<SELECT id="follow_up" name="follow_up">
					<?php foreach ( $follow_up_num as $value ) {
						if( $value == $row['follow_up'] )
							echo "<OPTION value='". $value ."' SELECTED>".$follow_up_array[$value - 1]."</OPTION>";
						else
							echo "<OPTION value='". $value ."'>".$follow_up_array[$value - 1]."</OPTION>";
					}
					?>
					</SELECT>
					</TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><?php echo $row['lab_comments']?></TD>
					<?php if ( $_SESSION['userTypeCookie'] < 3 ) { ?>
						<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
						<TD style="border-bottom:solid 1px black">
						<?php if ( ( $_SESSION['user_id'] == $row['salesperson'] or $_SESSION['user_id'] == 24 ) and $row['status'] == 4 ) { 
							$next_follow_up_date="";
							if ( $row['next_follow_up_date'] != 0 ) {
								$next_follow_up_date=date("m/d/Y",strtotime($row['next_follow_up_date']));
							} else if ( $row['shipped_date'] != 0 ) {
							   		$next_follow_up_date=mktime(0,0,0,date("m",$row['shipped_date']), date("d",$row['shipped_date'])+14,date("Y",$row['shipped_date']));
									$today=mktime(0,0,0,date("m"),date("d"),date("Y"));
									if ($next_follow_up_date < $today ) 
										$next_follow_up_date = "";
									else 
										$next_follow_up_date=date("m/d/Y",$next_follow_up_date);
							} 
							echo $row['follow_up_notes']. ( $next_follow_up_date == "" ? "" : "<br /><B>Next Follow up Date</B>: ". $next_follow_up_date) ;
						?>
							<br /><INPUT TYPE="button" id="followedup_button_<?php echo $item;?>" name="followedup_button_<?php echo $item;?>" VALUE="Update Note" onClick="follow_up('<?php echo $row['project_id'];?>','<?php echo $item;?>')">
						<?php } ?> 
						</TD>
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
</TABLE>
<SCRIPT language="javascript">
<!-- hide
function follow_up(prjId,item) {
//	document.getElementById("follow_up_"+item).innerHTML="<iframe src='FollowUpProject.php?pid="+prjId+"' width='400px' height='150px'></iframe>";
	var params = 'width=400, height=300';
	params += ', top=300, left=300';
	params += ', directories=no';
	params += ', location=no';
	params += ', menubar=no';
	params += ', resizable=no';
	params += ', scrollbars=no';
	params += ', status=no';
	params += ', toolbar=no';
	var url="FollowUpProject.php?pid="+prjId;
	var newwin=window.open(url,'win_follwoup', params);
	if (window.focus) {newwin.focus()}

}
-->
</SCRIPT>

<?php include('footer.php'); ?>