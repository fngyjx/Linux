		<?php

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN and LAB HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 6) {
	header ("Location: login.php?out=1");
	exit;
}

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
			elseif ( $_GET['sortby'] == "d" ) {
				$filter = " ORDER BY  due_date";
			}
			elseif ( $_GET['sortby'] == "sp" ) {
				$filter = " ORDER BY first_name";
			}
			elseif ( $_GET['sortby'] == "a" ) {
				$filter = " ORDER BY lab_first";
			}
			elseif ( $_GET['sortby'] == "dc" ) {
				$filter = " ORDER BY date_created DESC";
			}
			elseif ( $_GET['sortby'] == "cp" ) {
				$filter = " ORDER BY date_created DESC";
			}
		}

		$sql = "SELECT date_created, project_id, name, sent_to_front, status, due_date, summary, lab_comments, users.first_name, users.last_name, users.email, ( SELECT first_name
		FROM lab_assignees
		LEFT JOIN users
		USING ( user_id ) 
		WHERE lab_assignees.project_id = projects.project_id
		ORDER BY assignee_id
		LIMIT 1
		) AS  'lab_first'
		FROM projects
		LEFT JOIN customer_contacts
		USING ( contact_id ) 
		LEFT JOIN customers
		USING ( customer_id ) 
		LEFT JOIN users ON projects.salesperson = users.user_id WHERE status = 2 " . $filter;

		//$sql = "SELECT * FROM projects LEFT JOIN customer_contacts USING(client_id) LEFT JOIN customers USING(customer_id) WHERE status = 2" . $filter;
		$result = mysql_query($sql, $link);

		if ( mysql_num_rows($result) > 0 ) {

			$bg = 0; ?>
	
			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">
				<TR>
					<TD><B><A HREF="index.php?sortby=dc&archives=<?php echo $archives;?>">Created</A></B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><B><A HREF="index.php?sortby=p&archives=<?php echo $archives;?>">Project#</A></B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><B><A HREF="index.php?sortby=c&archives=<?php echo $archives;?>">Customer</A></B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><B><A HREF="index.php?sortby=cp&archives=<?php echo $archives;?>">Completed</A></B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><B><A HREF="index.php?sortby=d&archives=<?php echo $archives;?>">Due</A></B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><B>Summary</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><B><A HREF="index.php?sortby=sp&archives=<?php echo $archives;?>">Sales</A></B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><B><A HREF="index.php?sortby=a&archives=<?php echo $archives;?>">Assigns</A></B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
					<TD><B>Comments</B></TD>
				</TR>
				<TR>
					<TD COLSPAN=12><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
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
						<TD><A HREF="project_management_admin.sales.php?new_id=<?php echo $row['project_id'] ?>"><?php echo substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) ?></A></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
						<TD><?php echo $row['name'] ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
						<TD><?php
						if ( $row['sent_to_front'] != '' and $row['status'] > 2 ) {
							echo date("m/d/Y", strtotime($row['sent_to_front']));
						}
						?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
						<TD><?php echo date("m/d/Y", strtotime($row['due_date'])) ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
						<TD><?php echo $row['summary']; ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
						<TD><?php
						echo "<A HREF='mailto:" . $row['email'] . "?subject=Regarding Project# " . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) . " (" . $row['name'] . ")'>" . strtoupper(substr($row['first_name'],0,1) . substr($row['last_name'],0,1)) . "</A>";
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
								echo "<A HREF='mailto:" . $row_lab['email'] . "?subject=Regarding Project# " . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) . " (" . $row['name'] . ")'>" . $lab . "</A>";
								$i++;
								if ( $i < $c ) {
									echo "; ";
								}
							}
						}
						?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
						<TD><?php echo $row['lab_comments']; ?></TD>
					</TR>

				<?php } ?>

			</TABLE>

		<?php } else {
			print("No open projects");
		} ?>