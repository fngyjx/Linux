<?php

// ADMIN and Sales HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 AND $rights != 2 ) {
	header ("Location: login.php?out=1");
	exit;
}

?>		<A HREF="project_management_projects.php">Project Log<!-- &#151;SALES --></A><BR><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"><BR>
		<A HREF="project_management_completed.php">Completed</A><BR><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="21"><BR>

		<A HREF="project_management_admin.sales.php?new=1">Create new project</A><BR><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"><BR>

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<TR><FORM METHOD="post" ACTION="project_management_admin.sales.php">
				<TD>Revise project:</TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD>

				<?php

				$sql = "SELECT * FROM projects LEFT JOIN customer_contacts USING(contact_id) LEFT JOIN customers USING(customer_id) WHERE status = 4 AND salesperson = " . $_SESSION['user_id'] . " ORDER BY project_id" . $filter;
				$result = mysql_query($sql, $link);

				if ( mysql_num_rows($result) > 0 ) {
					?> <SELECT NAME="revision" STYLE="font-size:7pt"> <?php
					while ( $row = mysql_fetch_array($result) ) {
						echo "<OPTION VALUE='" . $row['project_id'] . "'>" . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) . " - " . $row['name'] . "</OPTION>";
					}
					?> </SELECT> <INPUT TYPE="submit" VALUE="Go" STYLE="font-size:7pt"> <?php
				}
				else {
					echo "<I>None available</I>";
				}

				?>

				 </TD>
			</TR></FORM>

			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
			</TR>

			<TR><FORM METHOD="post" ACTION="project_management_admin.sales.php">
				<TD>Resample project:</TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
				<TD>

				<?php

				$sql = "SELECT * FROM projects LEFT JOIN customer_contacts USING(contact_id) LEFT JOIN customers USING(customer_id) WHERE status = 4 AND salesperson = " . $_SESSION['user_id'] . " ORDER BY project_id" . $filter;
				$result = mysql_query($sql, $link);

				if ( mysql_num_rows($result) > 0 ) {
					?> <SELECT NAME="resample" STYLE="font-size:7pt"> <?php
					while ( $row = mysql_fetch_array($result) ) {
						echo "<OPTION VALUE='" . $row['project_id'] . "'>" . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) . " - " . $row['name'] . "</OPTION>";
					}
					?> </SELECT> <INPUT TYPE="submit" VALUE="Go" STYLE="font-size:7pt"> <?php
				}
				else {
					echo "<I>None available</I>";
				}

				?>

				</TD>
			</TR></FORM>
		</TABLE><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="21"><BR>

		<A HREF="customers_contacts.php">Contacts</A><BR><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"><BR>
		<A HREF="customers_customers.php">Customers</A>