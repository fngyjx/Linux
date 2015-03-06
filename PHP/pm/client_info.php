<?php 

session_start();

include('global.php');
require_ssl();

if ( !isset($_SESSION['userTypeCookie']) ) {
	header ("Location: login.php?out=1");
	exit;
}


if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}



if ( isset($_SESSION['pid']) ) {

	if ( !empty($_POST) ) {

		$notes = $_POST['notes'];

		// check_field() FUNCTION IN global.php
		check_field($notes, 1, 'Note');

		if ( !$error_found ) {
			// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
			$notes = escape_data($notes);
			$sql = "INSERT INTO notes (project_id, user_id, notes, date_time) "
			. "VALUES (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", '" . $notes . "', '" . date("Y-m-d H:i:s") . "')";
			mysql_query($sql, $link);
			header("location: client_info.php");
			exit();
		}

	}
	
}



if ( $_GET['action'] == "del" ) {
	$sql = "DELETE FROM shipping_info WHERE shipping_id = " . $_GET['sid'];
	mysql_query($sql, $link);
}



if ( ($status >= 4 or $_SESSION['userTypeCookie'] == 3) or ($status > 1 and $_SESSION['userTypeCookie'] == 2) or $_SESSION['userTypeCookie'] == 4 ) {
	$form_status = "DISABLED";
}
else {
	$form_status = "";
}



$project_type_array = array("New","Revision","Resample","Other");
$project_type_num = array(1,2,3,4);

$priority_array = array("Low","Medium","High");
$priority_num = array(1,2,3);

$status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
$status_num = array(1,2,3,4,5);



include('header.php');

include('inc_project_header.php'); ?>



<TABLE WIDTH=700 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="sales.src=salesOver.src"
		onMouseOut="sales.src=salesOut.src" 
		HREF="project_info.php"><IMG SRC="images/tabs/sales_out.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Sales info" NAME="sales"></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="client.src=clientOver.src"
		onMouseOut="client.src=clientOver.src" 
		HREF="client_info.php"><IMG SRC="images/tabs/client_over.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Client info" NAME="client"></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="sample.src=sampleOver.src"
		onMouseOut="sample.src=sampleOut.src" 
		HREF="sample_info.php"><IMG SRC="images/tabs/sample_out.gif" WIDTH=106 HEIGHT=18 BORDER=0 ALT="Sample info" NAME="sample"></TD>
		<TD><IMG SRC="images/tabs/blank.gif" WIDTH="392" HEIGHT="18" ALT="Blank"></TD>
	</TR>
	<TR><TD COLSPAN=4><IMG SRC="images/tabs/tab_rule.gif" WIDTH="700" HEIGHT="8"></TD></TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BGCOLOR="#976AC2"><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="5" BGCOLOR="whitesmoke" WIDTH=694><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="10" BGCOLOR="whitesmoke" ALIGN=CENTER WIDTH=684><TR><TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>

		<TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP><!-- <FORM METHOD="post" ACTION="client_info.php"> -->
		<TD>

<?php

$sql = "SELECT client_id, salesperson FROM projects WHERE project_id = " . $_SESSION['pid'];
$result = mysql_query($sql, $link);
$row = mysql_fetch_array($result);
$client_id = $row['client_id'];
$salesperson = $row['salesperson'];

if ( $client_id == "" ) {
	echo "<A HREF='choose_client.php?choose=1'>Choose client</A>";
}
else {

	$sql = "SELECT * FROM clients INNER JOIN clients_users USING(client_id) WHERE client_id = " . $client_id . " AND clients_users.user_id = " . $_SESSION['user_id'];
	//echo $sql;
	$result = mysql_query($sql, $link); // or die(mysql_error . " SQL: $sql");
	$row = mysql_fetch_array($result);
	if ( mysql_num_rows($result) > 0 ) {
		$allow_changes = true;
	} else {
		$allow_changes = false;
	}

	$sql = "SELECT * FROM clients LEFT JOIN companies USING(company_id) WHERE client_id = " . $client_id;
	$result = mysql_query($sql, $link);
	$row = mysql_fetch_array($result);

	?>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
		<TR VALIGN=TOP>
			<TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD><B CLASS="black">Name:</B>&nbsp;&nbsp;&nbsp;</TD>
			<TD>
			<?php if ( ($_SESSION['userTypeCookie'] == 1 or ( $_SESSION['userTypeCookie'] == 2 and $allow_changes == true )) and $status_head < 3 ) { ?>
				<A HREF="clients.php?cid=<?php echo $client_id;?>"><?php echo stripslashes($row['first_name']) . " " . stripslashes($row['last_name']) ?></A>
			<?php } else {
				echo stripslashes($row['first_name']) . " " . stripslashes($row['last_name']);
			} ?>
			</TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD><B CLASS="black">Company:</B>&nbsp;&nbsp;&nbsp;</TD>
			<TD><?php echo stripslashes($row['company']) ?></TD>
		</TR>

	<?php if ( $_SESSION['userTypeCookie'] == 1 or $_SESSION['userTypeCookie'] == 4 or ( $_SESSION['userTypeCookie'] == 2 and $allow_changes == true ) ) { ?>

		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD><B CLASS="black">Address:</B></TD>
			<TD><?php echo stripslashes($row['address1']) ?></TD>
		</TR>
		<?php if ( $row['address2'] != "" ) { ?>
			<TR>
				<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
			</TR>
			<TR>
				<TD></TD>
				<TD><?php echo stripslashes($row['address2']) ?></TD>
			</TR>
		<?php } ?>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD></TD>
			<TD><?php echo stripslashes($row['city']) . ", " . stripslashes($row['state']) . "  " . stripslashes($row['zip']) ?></TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD></TD>
			<TD><?php echo stripslashes($row['country']) ?></TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD><B CLASS="black">Phone:</B></TD>
			<TD><?php echo stripslashes($row['phone']) ?></TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD><B CLASS="black">Fax:</B></TD>
			<TD><?php echo stripslashes($row['fax']) ?></TD>
		</TR>
		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
		</TR>
		<TR>
			<TD><B CLASS="black">E-mail:</B></TD>
			<TD><?php echo "<A HREF='mailto:" . stripslashes($row['email']) . "'>" . stripslashes($row['email']) . "</A>" ?></TD>
		</TR>

	<?php if ( $_SESSION['userTypeCookie'] < 3 and $status_head < 3 ) { ?>

		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="15"></TD>
		</TR>
		<TR><FORM>
			<TD COLSPAN=2><INPUT TYPE="button" VALUE="Choose new client" onClick="window.location='choose_client.php?del=1&choose=1'" <?php echo $form_status ?>></TD>
		</TR></FORM>

	<?php } ?>

<?php } ?>

</TABLE>

		</TD>
		<TD><IMG SRC="images/spacer.gif" WIDTH="70" HEIGHT="1"></TD>
		<FORM><TD ALIGN=RIGHT>

		<?php 

		$sql = "SELECT * FROM shipping_info WHERE project_id = " . $_SESSION['pid'];
		$result_shipping = mysql_query($sql, $link);
		$c = mysql_num_rows($result_shipping);

		if ( $c == 0 ) {
		
			if ( $_SESSION['userTypeCookie'] == 1 or ( $_SESSION['userTypeCookie'] == 2 and $allow_changes == true ) ) { ?>
				<INPUT TYPE="button" VALUE="Ship to different address" onClick="window.location='shipping_address.php'" STYLE="font-size:7pt"> 
				<INPUT TYPE="button" VALUE="Ship to salesperson" onClick="window.location='shipping_address.php?sp=<?php echo $salesperson;?>'" STYLE="font-size:7pt">
			<?php
			}

		} else {

			$row_shipping = mysql_fetch_array($result_shipping);
			$shipping_id = $row_shipping['shipping_id'];
			$first_name = $row_shipping['first_name'];
			$last_name = $row_shipping['last_name'];
			$company = $row_shipping['company'];
			$address1 = $row_shipping['address1'];
			$address2 = $row_shipping['address2'];
			$city = $row_shipping['city'];
			$state = $row_shipping['state'];
			$zip = $row_shipping['zip'];
			$country = $row_shipping['country'];
			$phone = $row_shipping['phone'];
		
			?>

			<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
			<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
			<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD ALIGN=RIGHT>

			<?php 
			if ( $_SESSION['userTypeCookie'] == 1 or ( $_SESSION['userTypeCookie'] == 2 and $allow_changes == true ) ) { ?>
				<INPUT TYPE="button" VALUE="Edit" onClick="window.location='shipping_address.php?sid=<?php echo $shipping_id;?>'" STYLE="font-size:7pt"> 
				<INPUT TYPE="button" VALUE="Delete" onClick="delete_address(<?php echo $shipping_id;?>)" STYLE="font-size:7pt"><BR><BR>
				<?php
			}
			
			echo "<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0><TR VALIGN=TOP>";
			echo "<TD><B STYLE='font-size:8pt'>Ship to:</B></TD><TD>&nbsp;&nbsp;</TD><TD><SPAN STYLE='font-size:8pt'>";
			echo $first_name . " " . $last_name . "<BR>";
			if ( $company != "" ) {
				echo $company . "<BR>";
			}
			echo $address1 . "  " . $address2 . "<BR>";
			echo $city . ",  " . $state . "  " . $zip . "  " . $country . "<BR>";
			if ( $phone != "" ) {
				echo $phone;
			}

			?>

			</TD></TR></TABLE>

			</TD></TR></TABLE>
			</TD></TR></TABLE>
			</TD></TR></TABLE>

			<?php

		} ?>

		</TD></FORM>
	</TR>
</TABLE>



<?php if ( $_SESSION['userTypeCookie'] == 1 or ( $_SESSION['userTypeCookie'] == 2 and $allow_changes == true ) ) { ?>

	<BR><HR NOSHADE COLOR="#976AC2" SIZE="3"><BR>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD>

	<?php if ( $error_found ) {
		echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
	} ?>

	<?php if ( $note ) {
		echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
	} ?>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP><FORM METHOD="post" ACTION="client_info.php">
			<TD><B CLASS="black">Note:</B>&nbsp;&nbsp;&nbsp;</TD>
			<TD><TEXTAREA NAME="notes" ROWS="2" COLS="50" <?php echo $form_status ?>><?php echo $notes ?></TEXTAREA></TD>
			<TD>&nbsp;&nbsp;</TD>
			<TD><INPUT TYPE='submit' VALUE="Add note" <?php echo $form_status ?>></TD>
		</TR></FORM>
	</TABLE><BR><BR>

	<?php

	$sql = "SELECT * FROM notes LEFT JOIN users USING(user_id) WHERE project_id = " . $_SESSION['pid'] . " ORDER BY date_time";
	$result = mysql_query($sql, $link);
	if ( mysql_num_rows($result) > 0 ) { ?>
	
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">
			<TR>
				<TD><B>Date</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="15" HEIGHT="1"></TD>
				<TD><B>Submitted by</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="15" HEIGHT="1"></TD>
				<TD><B>Note</B></TD>
			</TR>
			<TR>
				<TD COLSPAN=5><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<?php 
			$bg = 0;
			while ( $row = mysql_fetch_array($result) ) {
					if ( $bg == 1 ) {
						$bgcolor = "#FFFFFF";
						$bg = 0;
					}
					else {
						$bgcolor = "#EFEFEF";
						$bg = 1;
					} ?>

				<TR BGCOLOR="<?php echo $bgcolor; ?>" VALIGN=TOP>
					<TD><?php echo date("m/d/Y H:i:s", strtotime($row['date_time'])); ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="15" HEIGHT="1"></TD>
					<TD><?php echo $row['first_name'] . " " . $row['last_name']; ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="15" HEIGHT="1"></TD>
					<TD><?php echo $row['notes']; ?></TD>
				</TR>

			<?php } ?>

		</TABLE>

	<?php } else {
		print("<I>No notes entered yet</I>");
	} ?>

			</TD>
		</TR>
	</TABLE>

			</TD>
		</TR>
	</TABLE>

<?php } ?>



<?php } ?>



		</TD>
	</TR>
</TABLE>

		</TD>
	</TR>
</TABLE>

		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR>



<?php include('inc_project_status.php'); ?>



<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

function delete_address(sid) {
	if ( confirm('Are you sure you want to delete this address?') ) {
		document.location.href = "client_info.php?action=del&sid=" + sid
	}
}

 // End -->
</SCRIPT>



<?php include('footer.php'); ?>