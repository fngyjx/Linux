<?php

	$sql = "SELECT project_id, client_id, project_type, priority, status, date_created, due_date, lab_comments,summary
	FROM projects WHERE project_id = " . $_SESSION['pid'];
	$result = mysql_query($sql, $link);
	$row = mysql_fetch_array($result);

	$project_id=$row['project_id'];
	$project_id_head = substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3);
	$type_head = $row['project_type'];
	$priority_head = $row['priority'];
	$status_head = $row['status'];
	$date_created_head = date("m/d/Y", strtotime($row['date_created']));
	$due_date_head = date("m/d/Y", strtotime($row['due_date']));
	$client_id = $row['client_id'];
	$lab_comments = $row['lab_comments'];
	$summary=$row['summary'];
	?>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD><B>Project#:</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			<TD><NOBR><?php echo $project_id_head ?></NOBR></TD>
		</TR>
		<TR VALIGN=TOP>
			<TD><B>Type:</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			<TD><?php
			foreach ( $project_type_num as $value ) {
				if ( $value == $type_head ) {
					echo $project_type_array[$value-1];
				}
			} 
			?></TD>
		</TR>
	</TABLE>

		</TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
		<TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD><B>Priority:</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			<TD><?php
			foreach ( $priority_num as $value ) {
				if ( $value == $priority_head ) {
					echo $priority_array[$value-1];
				}
			} 
			?></TD>
		</TR>
		<TR VALIGN=TOP>
			<TD><B>Status:</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			<TD><?php
			foreach ( $status_num as $value ) {
				if ( $value == $status_head ) {
					echo $status_array[$value-1];
				}
			} 
			?></TD>
		</TR>
	</TABLE>

		</TD>

		<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
		<TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD><B>Created:</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			<TD><?php echo $date_created_head ?></TD>
		</TR>
		<TR VALIGN=TOP>
			<TD><NOBR><B>Date due:</B></NOBR></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			<TD><NOBR><?php echo $due_date_head ?></NOBR></TD>
		</TR>
	</TABLE>

		</TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="20" HEIGHT="1"></TD>
		<TD VALIGN=TOP>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD><B>Client:</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			<TD><?php
				if ( $client_id == "" ) {
					echo "<A HREF='choose_client.php?alpha=c&choose=1'>Choose client</A>";
				}
				else {
					$sql = "SELECT first_name, last_name, company FROM clients LEFT JOIN companies USING(company_id) WHERE client_id = " . $client_id;
					$result = mysql_query($sql, $link);
					$row = mysql_fetch_array($result);
					echo $row['first_name'] , " " . $row['last_name'] . "<BR>";
					$company = $row['company'];
					echo "<I>" . $company . "</I>";
				}
				?>
			</TD>
		</TR>
	</TABLE>
	</TD>
</TR><TR>	
<TD VALIGN=TOP colspan="7">
	<?php if ( isset($print_diabled) and $print_diabled != "" ) { ?>
	<hr />
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD><B>Lab Notes:</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			<TD><?php echo stripslashes($lab_comments);?>
			</TD>
		</TR>
	</TABLE>
	<hr />
<?php } ?>
			</TD>
		</TR>
	</TABLE>



	<?php

	//if ( $_SESSION['userTypeCookie'] != 2 ) {
		if ( $print_diabled == '' or ! isset($print_diabled) ) { ?>
<BR>
			<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
			<TABLE CELLPADDING=7 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
			<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>

			<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0>
			<FORM ACTION="sample_info.php" METHOD="post">
			<INPUT TYPE="hidden" NAME="uri" VALUE="<?php echo $_SERVER['REQUEST_URI'];?>">

				<TR VALIGN=TOP>
					<TD><B>Lab Notes:</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><INPUT TYPE="text" NAME="comments" SIZE="85" VALUE="<?php echo stripslashes($lab_comments);?>" MAXLENGTH="255"> <INPUT TYPE="submit" VALUE="Save"></TD>
				</TR></FORM>

			</TABLE>
	
			</TD></TR></TABLE>
			</TD></TR></TABLE>
			</TD></TR></TABLE>
	<BR>
		<?php //} else { ?>
			<!-- <B>Comments:</B> <?php //echo stripslashes($lab_comments);?><BR> -->
		<?php

		//}
	}

	?>

