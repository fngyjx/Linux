<?php

if ( !$_GET['sortby'] ) {
	$filter = " ORDER BY status ASC, due_date";
}
else {
	if ( $_GET['sortby'] == "p" ) {
		$filter = " ORDER BY project_id DESC";
	}
	elseif ( $_GET['sortby'] == "c" ) {
		$filter = " ORDER BY company";
	}
	elseif ( $_GET['sortby'] == "cp" ) {
		$filter = " ORDER BY sent_to_front DESC";
	}
	elseif ( $_GET['sortby'] == "d" ) {
		$filter = " ORDER BY  due_date";
	}
	elseif ( $_GET['sortby'] == "s" ) {
		$filter = " ORDER BY shipped_date DESC";
	}
}

// SEARCH ARCHIVES OR WITHIN THE PAST 60 DAYS
$fourteen_days_ago = mktime(0, 0, 0, date("m"), date("d")-14, date("y"));
$search_start = date("Y-m-d", $fourteen_days_ago);
if ( $archives == 1 ) {
	//$date_filter =  " sent_to_front < '" . $search_start . "'";
	$date_filter =  " sent_to_front < '" . $search_start . "' OR sent_to_front IS NULL";
} else {
	$date_filter =  " sent_to_front >= '" . $search_start . "'";
}
	
$sql = "SELECT * FROM projects LEFT JOIN clients USING(client_id) LEFT JOIN companies USING(company_id) WHERE " . $date_filter . " AND (status > 2 AND status < 5) " . $filter;

//$sql = "SELECT project_id, date_created, company, parent_id, project_type, status, due_date, summary, users.last_name, users.first_name, sent_to_front FROM projects LEFT JOIN clients USING(client_id) LEFT JOIN companies USING(company_id) LEFT JOIN users ON projects.salesperson = users.user_id " . $sales_filter . $filter;
$result = mysql_query($sql, $link);

if ( mysql_num_rows($result) > 0 ) {

	if ( $_SESSION['userTypeCookie'] == 1 ) {
		$page = "front_desk_admin.php";
	} else {
		$page = "index.php";
	}

	$bg = 0; ?>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">
				<TR>
					<TD><B><A HREF="<?php echo $page;?>?sortby=p&archives=<?php echo $archives;?>">Project#</A></B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><B><A HREF="<?php echo $page;?>?sortby=c&archives=<?php echo $archives;?>">Company</A></B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><B><A HREF="<?php echo $page;?>?sortby=cp&archives=<?php echo $archives;?>">Completed</A></B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><B><A HREF="<?php echo $page;?>?sortby=d&archives=<?php echo $archives;?>">Due</A></B></TD>
					<TD COLSPAN="6"><IMG SRC="images/spacer.gif" WIDTH="5" HEIGHT="1"></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><B><A HREF="<?php echo $page;?>?sortby=s&archives=<?php echo $archives;?>">Shipped</A></B></TD>
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
						<TD><A HREF="project_info.php?new_id=<?php echo $row['project_id'] ?>"><?php echo substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) ?></A></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
						<TD><?php echo $row['company'] ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
						<TD><?php
						if ( $row['sent_to_front'] != '' ) {
							echo date("m/d/Y", strtotime($row['sent_to_front']));
						}?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
						<TD><?php echo date("m/d/Y", strtotime($row['due_date'])) ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="1"></TD><FORM ACTION="packing_slip.php?pid=<?php echo $row['project_id'] ?>" METHOD="post" TARGET="_blank">
						<TD><INPUT TYPE="submit" VALUE="Print packing slip" STYLE="font-size:7pt"></TD></FORM>

						<TD><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="1"></TD><FORM>
						<TD>
						<?php
						$sql = "SELECT * FROM flavors WHERE project_id = " . $row['project_id'];
						$result_flavors = mysql_query($sql, $link);
						$c = mysql_num_rows($result_flavors);
						if ( $c > 0 ) { ?>
							<INPUT TYPE="button" VALUE="Print labels" onClick="window.open('sample_labels.php?pid=<?php echo $row['project_id'];?>','','')" STYLE="font-size:7pt">
						<?php } else { ?>
							<I>No flavors</I>
						<?php } ?>
						</TD></FORM>

						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD><FORM>
						<TD>
						<?php if ( $row['tracking_number'] != "" ) {

							foreach ( $shipper_num as $value ) {
								if ( $value == $row['shipper'] ) {
									if ( $row['shipper'] == 5 ) {
										echo $row['shipper_other'] . "#:<BR>";
									}
									else {
										echo $shipper_array[$value-1] . "#:<BR>";
									}
								}
							}

							echo $row['tracking_number'];
							$can_submit = "";

						} else { ?>
							<INPUT TYPE="button" VALUE="Enter shipping info" onClick="window.location='shipping.php?pid=<?php echo $row['project_id'] ?>'" STYLE="font-size:7pt">
						<?php 
							$can_submit = "DISABLED";
						} ?>
						</TD></FORM>
						<TD><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="1"></TD><FORM>
						<TD>
						<?php if ( $row['status'] == 4 ) { ?>
							Shipped<BR>
							<?php echo date("m/d/Y", strtotime($row['shipped_date'])) ?>
						<?php } else { ?>
							<INPUT TYPE="button" VALUE="Mark as shipped" onClick="window.location='project_info.php?stat=4&pid=<?php echo $row['project_id'];?>'" <?php echo $can_submit ?> STYLE="font-size:7pt">
						<?php } ?>
						</TD></FORM>
					</TR>

				<?php } ?>

			</TABLE>

		<?php } else {
			if ( $archives == 1 ) {
				print("No archives yet<BR><BR>");
			} else {
				print("No pending shipments<BR><BR>");
			}
		} ?>