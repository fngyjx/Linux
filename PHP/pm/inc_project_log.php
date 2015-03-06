<?php 
//inc projects log info
$sql = "SELECT * FROM change_log LEFT JOIN users USING(user_id) WHERE project_id = " . $_SESSION['pid'] . " ORDER BY time_stamp DESC";
$result = mysql_query($sql, $link);

if ( mysql_num_rows($result) > 0 ) { ?>

	<BR><HR NOSHADE COLOR="#976AC2" SIZE="3"><BR>

	<B CLASS="red">Change log</B><BR><BR>

	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0>

		<TR>
			<TD><B>Name</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B>Field</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B>Old value</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B>New value</B></TD>
			<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
			<TD><B>Date/time</B></TD>
		</TR>

	<?php
	$bg = 0;
	while ( $row = mysql_fetch_array($result) ) {
		if ( $bg == 1 ) {
			$bgcolor = "#FFFFFF";
			$bg = 0;
		}
		else {
			$bgcolor = "#DFDFDF";
			$bg = 1;
		}
		echo "<TR BGCOLOR='" . $bgcolor . "' VALIGN=TOP>";
		echo "<TD><NOBR>" . $row['first_name'] . " " . $row['last_name'] . "</NOBR></TD>";
		echo "<TD></TD>";
		echo "<TD><NOBR>" . $row['field_name'] . "</NOBR></TD>";
		echo "<TD></TD>";
		echo "<TD>" . $row['old_value'] . "</TD>";
		echo "<TD></TD>";
		echo "<TD>" . $row['new_value'] . "</TD>";
		echo "<TD></TD>";
		echo "<TD>" . date("m/d/Y H:i:s", strtotime($row['time_stamp'])) . "</TD>";
		echo "</TR>";
	} ?>

	</TABLE>

<?php } ?>
