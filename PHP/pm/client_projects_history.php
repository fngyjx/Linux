<?php 
include('global.php');
session_start();

if ( ! $_SESSION['uLoggedInCookie'] AND ! $_COOKIE["uLoggedInCookie"]  ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

// $application_array = array("Baked Good","Beverage","Cereal","Confection","Dairy Product","Pet Food","Nutraceutical","Pharmaceutical","Prepared Food","Snack");
// $application_num = array(1,2,3,4,5,6,7,8,9,10);

// $status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
// $status_num = array(1,2,3,4,5);

// $follow_up_array = array("Hot","Warm","Cold","Won","Lost","Cancelled","");
// $follow_up_num = array(1,2,3,4,5,6,7);

$client_id=escape_data($_REQUEST[cid]);

// print_r($_REQUEST);

if ( empty($client_id) ) {
	$_SESSION['note']="Client ID is required";
	echo "<script>window.opener.loaction.reload();window.close();</script>";
	exit();
}

$sql="SELECT clients.*, company FROM clients
	LEFT JOIN users USING(user_id)
	LEFT JOIN companies USING(company_id)
	WHERE client_id='".$client_id."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL $sql<br />");
$row=mysql_fetch_array($result);
include('header.php');

?>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>

		<TD>

	<B CLASS="header">Project Lists For Client: <?php echo $row['first_name']." ".$row['last_name']." of ".$row['company'] ;?></B>
<BR><BR><BR>

<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>

<?php
 $salesperson="";
 if ( $_SESSION['userTypeCookie'] > 1 )
	$salesperson=" AND salespersion='".$_SESSION['user_id']."' ";
	
	$sql = "SELECT projects.*, notes.notes,notes.date_time, flavors.*
	FROM projects 
	LEFT JOIN notes USING(project_id)
	LEFT JOIN flavors USING(project_id)
	WHERE client_id='".$client_id."' AND shipped_date is not null AND follow_up <5 AND status<>5 ".$salesperson ." ORDER BY date_created DESC";
	
	// echo "<br /> $sql<br />";
	$result_prj = mysql_query($sql, $link) or die ( mysql_error() ." Failed execute SQL : $sql <br />");   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	if ( mysql_num_rows($result_prj) > 0 ) {

		$bg = 0; ?>
		<FORM action="contact_client.php" method="post" target="_blank">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<TR>
				<TD><B>Created</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B>Completed</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B>Project</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD>&nbsp;</TD>
				<TD><B>Flavor</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B>Notes</B></TD>
				<!-- <TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD><B>Status_List</B></TD> -->
				<?php if ( $_SESSION['userTypeCookie'] < 3 ) { ?>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><B>Followed up</B></TD>
				<?php } ?>
			</TR>
			<TR><?php if ( $_SESSION['userTypeCookie'] < 3 ) { ?>
				<TD COLSPAN="14"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
				<?php } else {?>
				<TD COLSPAN="12"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
				<?php } ?>
			</TR>

			<?php 
			$c=0;
			$created="";
			$project_id="";
			$completed="";
			
			while ( $row_prj = mysql_fetch_array($result_prj) ) {
				$c++;
				$next_follow_up_date="";
				
				if ( !empty($row_prj['next_follow_up_date']) ) {
					$next_follow_up_date=date("m/d/Y",strtotime($row_prj['next_follow_up_date']));
				} else 
				if ( !empty($row_prj['shipped_date'])) {
					$next_follow_up_date=mktime(0,0,0,date("m",$row_prj['shipped_date']), date("d",$row_prj['shipped_date'])+14,date("Y",$row_prj['shipped_date']));
					$today=mktime(0,0,0,date("m"),date("d"),date("Y"));
					if ($next_follow_up_date < $today ) 
						$next_follow_up_date = "";
					else 
					    $next_follow_up_date=date("m/d/Y",$next_follow_up_date);
				}
				
				if ( $bg == 1 ) {
					$bgcolor = "#FFFFFF";
					$bg = 0;
				}
				else {
					$bgcolor = "#EFEFEF";
					$bg = 1;
				} ?>

				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN="TOP">
					<TD>
					<?php 
					if ( $created == $row_prj['date_created'] )
						echo "&nbsp;";
					else 
						echo date("m/d/Y", strtotime($row_prj['date_created'])); ?>
					</TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><?php
					if ( $row_prj['sent_to_front'] != '' and $row_prj['status'] > 2 and $completed != $row_prj['sent_to_front'] ) {
						echo date("m/d/Y", strtotime($row_prj['sent_to_front']));
					}
					?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD>
					<?php if ( $project_id != $row_prj['project_id'] ) { ?>
					<A HREF="project_info.php?new_id=<?php echo $row_prj['project_id'] ?>"><?php echo substr($row_prj['project_id'], 0, 2) . "-" . substr($row_prj['project_id'], -3) ?></A>
					<?php } ?>
					</TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><INPUT type='checkbox' value="<?php echo $row_prj['project_id']."_".$row_prj['flavor_id']?>" 
					id="flavor_<?php echo $c;?>" name="flavor_<?php echo $c;?>" onClick="flavor_checked('<?php echo $c;?>')"></TD>
					<TD><?php echo $row_prj['flavor_id']." ".$row_prj['flavor_name'];?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><?php echo $row_prj['lab_comments']."<br />".$row_prj['summary']."<br />".$row['comments']."<br />".$row_prj['notes'];?></TD>
					<!-- <TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD style="border:none; padding:0 0 0 0; cell-spacing:0; text-align:left top;" ><NOBR><iframe id="trgamt_<?php echo $c;?>" name="trgamt_<?php echo $c;?>" width="100px" height="25px" align="left" valign="top" bgcolor="<?php echo $bgcolor;?>" frameborder="0" scrolling="no" src="setProjectFollowUp.php?project_id=<?php echo $row['project_id'];?>&bgcolor=<?php echo $bgcolor ?>"></iframe>
					</NOBR></TD> -->
					<?php if ( $_SESSION['userTypeCookie'] < 3 ) { ?>
						<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
						<TD>
						<?php if ( ($_SESSION['user_id'] == $row_prj['salesperson'] or $_SESSION['user_id'] == '24') and $row_prj['status'] == 4 ) {
									echo $row_prj['follow_up_notes']."<br /><B>Next Follow up Date</B>: ". $next_follow_up_date;?>
									<INPUT TYPE="button" id="followedup_button_<?php echo $item;?>" name="followedup_button_<?php echo $item;?>" VALUE="Update Note" onClick="follow_up('<?php echo $row_prj['project_id'];?>','<?php echo $item;?>')">
						<?php } ?>
						</TD>
					<?php } ?>
				</TR>

			<?php 
				$project_id=$row_prj['project_id'];
				$created=$row_prj['date_created'];
				$completed=$row_prj['sent_to_front'];
			} ?>
		<TR><TD colspan="14" align="right">
			<INPUT type='hidden' name="total_flavors" value='<?php echo $c;?>'>
			<INPUT type='hidden' name="cid" value='<?php echo $client_id;?>'>
			<NOBR><INPUT type="submit" value="Contact Client" id="my_submit" style="display:none"><INPUT type="reset" value="Cancel" id="my_cancel" style="display:none" onClick="reset_flavor('<?php echo $c;?>')"></NOBR>
			</TD></TR>
		</TABLE>
		</FORM>
	<?php } else {
		print("No projects match search criteria<BR><BR>");
	}

 ?>
<SCRIPT language="javascript">
<!-- hide script
function flavor_checked(item) {
	if (document.getElementById("flavor_"+item).checked ) {
		document.getElementById("my_submit").style.display="block";
		document.getElementById("my_cancel").style.display="inline";
	}
}

function reset_flavor(item) {
	for ( var i=1; i<=item; i++ ) {
		document.getElementById("flavor_"+i).checked=false;
	}
	document.getElementById("my_submit").style.display="none";
	document.getElementById("my_cancel").style.display="none";
}

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
</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>



		</TD>
	</TR>
</TABLE>



<?php include('footer.php'); ?>