<?php

	$sql = "SELECT project_id, contact_id, project_type, priority, status, date_created, due_date, lab_comments FROM projects WHERE project_id = " . $_SESSION['pid'];
	$result = mysql_query($sql, $link);
	$row = mysql_fetch_array($result);

	$project_id_head = substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3);
	$type_head = $row['project_type'];
	$priority_head = $row['priority'];
	$status_head = $row['status'];
	$date_created_head = date("m/d/Y", strtotime($row['date_created']));
	$due_date_head = date("m/d/Y", strtotime($row['due_date']));
	$contact_id = $row['contact_id'];
	$lab_comments = $row['lab_comments'];
	
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
			<TD><B>Contact:</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			<TD><?php
				if ( $contact_id == "" ) {
					echo "<A HREF='customers_contacts.php?alpha=c&choose=1'>Choose contact</A>";
				}
				else {
					$sql = "SELECT first_name, last_name, name FROM customer_contacts LEFT JOIN customers USING(customer_id) WHERE contact_id = " . $contact_id;
					$result = mysql_query($sql, $link);
					$row = mysql_fetch_array($result);
					echo $row['first_name'] , " " . $row['last_name'] . "<BR>";
					$name = $row['name'];
					echo "<I>" . $name . "</I>";
				}
				?>
			</TD>
		</TR>
	</TABLE>

			</TD>
		</TR>
	</TABLE><BR>



	<?php

	//if ( $_SESSION['userTypeCookie'] != 2 ) {
		//if ( $lab_comments == '' ) { ?>

			<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
			<TABLE CELLPADDING=7 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
			<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>

			<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0>
			<FORM ACTION="project_management_admin.sample.php" METHOD="post">
			<INPUT TYPE="hidden" NAME="uri" VALUE="<?php echo $_SERVER['REQUEST_URI'];?>">

				<TR VALIGN=TOP>
					<TD><B>Comments:</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><INPUT TYPE="text" NAME="comments" SIZE="22" VALUE="<?php echo stripslashes($lab_comments);?>" MAXLENGTH=20> <INPUT TYPE="submit" VALUE="Save"></TD>
				</TR></FORM>

			</TABLE>
	
			</TD></TR></TABLE>
			</TD></TR></TABLE>
			</TD></TR></TABLE>

		<?php //} else { ?>
			<!-- <B>Comments:</B> <?php //echo stripslashes($lab_comments);?><BR> -->
		<?php

		//}
	//}

	?>

	<BR>