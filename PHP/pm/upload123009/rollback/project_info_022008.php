<?php

include('global.php');
require_ssl();

if ( !isset($_COOKIE['userTypeCookie']) ) {
	header ("Location: login.php?out=1");
	exit;
}


if ( $_GET['new_id'] ) {
	setCookie("pid", $_GET['new_id']);
	header("location: project_info.php");
	exit();
}



$project_type_array = array("New","Revision","Resample","Other");
$project_type_num = array(1,2,3,4);

$priority_array = array("Low","Medium","High");
$priority_num = array(1,2,3);

$status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
$status_num = array(1,2,3,4,5);

$annual_potential_array = array("Low","Medium","High");
$annual_potential_num = array(1,2,3);

$application_array = array("Baked Good","Beverage","Cereal","Confection","Dairy Product","Pet Food","Nutraceutical","Pharmaceutical","Prepared Food","Snack");
$application_num = array(1,2,3,4,5,6,7,8,9,10);

$sample_size_array = array("1 oz.","2 oz.","4 oz.","8 oz.","Other");
$sample_size_num = array(1,2,3,4,5);

$suggested_level_array = array("Use as desired","Same as target","Other");
$suggested_level_num = array(1,2,3);

$shipper_array = array("UPS","FedEx","DHL","USPS","Other");
$shipper_num = array(1,2,3,4,5);

$shipping_array = array("Next day","2nd day","Ground ","Date appropriate carrier");
$shipping_num = array(1,2,3,4);

$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");



if ( $_GET['stat'] ) {

	if ( $_GET['stat'] == "2" ) {   // STATUS: FROM SALES TO LAB
		$sql = "UPDATE projects SET status = 2 WHERE project_id = " . $_COOKIE['pid'];
		mysql_query($sql, $link);
		header("location: project_info.php");
		exit();
	}

	elseif ( $_GET['stat'] == "3" ) {   // STATUS: FROM LAB TO FRONT DESK
		$sql = "UPDATE projects SET status = 3 WHERE project_id = " . $_COOKIE['pid'];
		mysql_query($sql, $link);
		header("location: project_info.php");
		exit();
	}

	elseif ( $_GET['stat'] == "5" ) {   // STATUS: FROM LAB TO FRONT DESK
		$sql = "UPDATE projects SET status = 5, follow_up = 5 WHERE project_id = " . $_GET['pid'];
		mysql_query($sql, $link);
		setCookie("note", "Project successfully cancelled<BR>");
		header("location: projects.php");
		exit();
	}

	elseif ( $_GET['stat'] == "4" ) {   // STATUS: FROM FRONT DESK TO SHIPPED
		$sql = "UPDATE projects SET status = 4, shipped_date = '" . date("Y-m-d") . "' WHERE project_id = " . $_GET['pid'];
		mysql_query($sql, $link);

		// E-MAIL NOTIFICATION TO CLIENT

		$sql = "SELECT shipping, shipper, shipper_other, tracking_number, clients.first_name AS client_first, clients.email
				AS client_e, users.first_name AS sales_first, users.last_name AS sales_last, users.email AS sales_e
			FROM projects
			LEFT JOIN clients
			USING ( client_id ) 
			LEFT JOIN users ON projects.salesperson = users.user_id
			WHERE project_id  = " . $_GET['pid'];
		$result1 = mysql_query($sql, $link);
		$row1 = mysql_fetch_array($result1);

		// CHECK WHETHER OTHER SHIPPING ADDRESS HAS BEEN ENTERED
		// IF SO, E-MAIL SALESPERSON NOT CLIENT
		$sql = "SELECT * FROM shipping_info WHERE project_id = " . $_GET['pid'];
		$result_shipping = mysql_query($sql, $link);
		$c = mysql_num_rows($result_shipping);

		if ( $c > 0 ) {
			$email = $row1['sales_e'];
		} else {
			$email = $row1['client_e'];
		}

		if ( $row1['shipper'] == 5 ) {
			$shipper = $row1['shipper_other'];
		}
		else {
			$shipper = $shipper_array[$row1['shipper']-1];
		}

		$message = "Hello " . $row1['client_first'] . ",<BR><BR>";  

		$message .= "This is to confirm that <B STYLE='color:red'>abelei</B> has shipped the flavor sample(s) listed in this e-mail message to you this afternoon by " . $shipper . " " . $shipping_array[$row1['shipping']-1] . " and should arrive soon. The tracking number is " . $row1['tracking_number'] . ".<BR><BR>";
  
		$message .= "If you have any questions or concerns, or need additional information on these or other flavors, please reply to 
this message. <BR><BR>";
  
		$message .= "Thank you for your interest in flavors from <B STYLE='color:red'>abelei</B>, <B>the source of good taste</B>.<BR><BR>";


		$message .= "Sincerely,<BR>";
		$message .= "<B>" . $row1['sales_first'] . " " . $row1['sales_last'] . "</B><BR>";
		$message .= "<B STYLE='color:red'>abelei</B><BR>";
		$message .= "194 Alder Drive<BR>";
		$message .= "North Aurora, IL  60542<BR>";
		$message .= "630-859-1410<BR>";
		$message .= "Fax 630-859-1448<BR>";
		$message .= "Toll Free 866-422-3534<BR>";
		$message .= $row1['sales_e'] . "<BR>";
		$message .= "<A HREF='http://www.abelei.com'>www.abelei.com</A><BR><BR><BR>";

		// COMMON CODE SHARED WITH PACKING SLIP (packing_slip.php)
		include('inc_packing_slip.php');

		$mail_message = "<HTML><BODY><TABLE WIDTH=600 BORDER=0><TR><TD STYLE='font-family:verdana, tacoma, sans-serif'>";
		$mail_message .= $message;
		$mail_message .= "</TD></TR></TABLE></BODY></HTML>";
		mail("tgooding@abelei.com,$email", "abelei sample shipped", wordwrap($mail_message,72,"\r\n"), "From: " . $row1['sales_e'] . "\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1");

		// CHANGE "tgooding@abelei.com,moconnell@chicagoit.com" to   $email   AFTER TESTING
		// tgooding@abelei.com,

		header("location: index.php");
		exit();
	}
}



// COPY INFO FROM EXISTING PRJECT TO REVISION OR RESAMPLE
if ( $_POST['revision'] or $_POST['resample'] ) {

	if ( $_POST['revision'] != "" ) {
		$id = $_POST['revision'];
		$type = 2;
	}
	else {
		$id = $_POST['resample'];
		$type = 3;
	}

	$sql = "SELECT project_id FROM projects ORDER BY project_id DESC LIMIT 1";
	$result = mysql_query($sql, $link);
	$row = mysql_fetch_array($result);
	$last_id = $row['project_id'];
	$year = substr($last_id, 0, 2);
	$old_id = substr($last_id, -3);
	if ( $year == date("y") ) {
		$project_id = (date("y") . $old_id) + 1;
	}
	else {
		$project_id = date("y") . "001";
	}

	$sql = "SELECT * FROM projects WHERE project_id = " . $id;
	$result = mysql_query($sql, $link);
	$row = mysql_fetch_array($result);

	$due_date = date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')+14, date('Y')));
	$sql = "INSERT INTO projects (project_id, client_id, date_created, priority, due_date, salesperson, project_type, parent_id, application, status, annual_potential, n_a1, n_a2, form, product_type, kosher, halal, sample_size, sample_size_other, base_included, target_included, target_level, target_rmc, cost_in_use, cost_in_use_measure, summary, comments, project_info_submitted, client_info_submitted, sample_info_submitted) VALUES ("
	. $project_id . ", "
	. "'" . $row['client_id'] . "', "
	. "'" . date("Y-m-d") . "', "
	. "'" . $row['priority'] . "', "
	. "'" . $due_date . "', "
	. "'" . $_COOKIE['user_id'] . "', "
	. "'" . $type . "', "
	. "'" . $id . "', "
	. "'" . $row['application'] . "', "
	. "'1', "
	. "'" . $row['annual_potential'] . "', "
	. "'" . $row['n_a1'] . "', "
	. "'" . $row['n_a2'] . "', "
	. "'" . $row['form'] . "', "
	. "'" . $row['product_type'] . "', "
	. "'" . $row['kosher'] . "', "
	. "'" . $row['halal'] . "', "
	. "'" . $row['sample_size'] . "', "
	. "'" . $row['sample_size_other'] . "', "
	. "'" . $row['base_included'] . "', "
	. "'" . $row['target_included'] . "', "
	. "'" . $row['target_level'] . "', "
	. "'" . $row['target_rmc'] . "', "
	. "'" . $row['cost_in_use'] . "', "
	. "'" . $row['cost_in_use_measure'] . "', "
	. "'" . $row['summary'] . "', "
	. "'" . $row['comments'] . "',"
	. "0,"
	. "1,"
	. "0"
	. ")";
	mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$sql = "SELECT * FROM lab_assignees WHERE project_id = " . $id;
	$result = mysql_query($sql, $link);
	$row = mysql_fetch_array($result);

	$sql = "INSERT INTO lab_assignees (project_id, user_id) VALUES ("
	. $project_id . ", "
	. $row['user_id'] . ")";
	mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$sql = "SELECT * FROM flavors WHERE project_id = " . $id;
	$result = mysql_query($sql, $link);
	$row = mysql_fetch_array($result);

	$sql = "INSERT INTO flavors (flavor_id, project_id, flavor_name, expiration_date, suggested_level, suggested_level_other) VALUES ("
	. "'" . $row['flavor_id'] . "', "
	. $project_id . ", "
	. "'" . $row['flavor_name'] . "', "
	. "'" . $row['expiration_date'] . "', "
	. "'" . $row['suggested_level'] . "', "
	. "'" . $row['suggested_level_other'] . "')";
	mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	setCookie("pid", $project_id);
	header("location: project_info.php");
	exit();

}



if ( isset($_COOKIE['note']) ) {
	$note = $_COOKIE['note'];
	setCookie("note", "" , time()-"100");
}



if ( isset($_COOKIE['pid']) ) {

	if ( !empty($_POST) ) {

		$summary = $_POST['summary'];
		$priority = $_POST['priority'];
		$month = $_POST['month'];
		$day = $_POST['day'];
		$year = $_POST['year'];
		$due_date = $year . "-" . $month . "-" . $day;
		$salesperson = $_POST['salesperson'];
		$project_type = $_POST['project_type'];
		$parent_id = $_POST['parent_id'];
		$application = $_POST['application'];
		$annual_potential = $_POST['annual_potential'];

		// check_field() FUNCTION IN global.php
		// NONE TO CHECK

		if ( !$error_found ) {

			// CHECK DUE DATE ENTERED TO SEE WHETHER IT'S BEEN CHANGED
			$sql = "SELECT due_date FROM projects WHERE project_info_submitted = 1 AND due_date <> '" . $due_date . "' AND project_id = " . $_COOKIE['pid'];
			$result = mysql_query($sql, $link);
			if ( mysql_num_rows($result) > 0 ) {
				$row = mysql_fetch_array($result);
				$old_date = date("m/d/Y", strtotime($row['due_date']));
				$new_date = date("m/d/Y", strtotime($due_date));
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_COOKIE['pid'] . ", " . $_COOKIE['user_id'] . ", 'Due date', '" . $old_date . "', '" . $new_date . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}

			$sql = "UPDATE projects " .
			" SET project_info_submitted = 1," .
			" summary = '" . $summary . "'," .
			" priority = " . $priority . "," .
			" due_date = '" . $due_date . "', " .
			" salesperson = " . $salesperson . ", " .
			" application = " . $application . ", " .
			" annual_potential = " . $annual_potential .
			" WHERE project_id = " . $_COOKIE['pid'];
			mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			//echo $sql;

			setCookie("note", "Information successfully saved<BR>");
			header("location: project_info.php");
			exit();
		}

	}

	else {

		$sql = "SELECT * FROM projects WHERE project_id = " . $_COOKIE['pid'];
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result);
		$summary = $row['summary'];
		$priority = $row['priority'];
		$month = date("m", strtotime($row['due_date']));
		$day = date("d", strtotime($row['due_date']));
		$year = date("Y", strtotime($row['due_date']));
		$salesperson = $row['salesperson'];
		$project_type = $row['project_type'];
		$parent_id = $row['parent_id'];
		$application = $row['application'];
		$status = $row['status'];
		$annual_potential = $row['annual_potential'];
		$shipping = $row['shipping'];

	}
	
}



elseif ( isset($_GET['new']) ) {

	$sql = "SELECT project_id FROM projects ORDER BY project_id DESC LIMIT 1";
	$result = mysql_query($sql, $link);
	$row = mysql_fetch_array($result);
	$last_id = $row['project_id'];
	$year = substr($last_id, 0, 2);
	$old_id = substr($last_id, -3);
	if ( $year == date("y") ) {
		$project_id = (date("y") . $old_id) + 1;
	}
	else {
		$project_id = date("y") . "001";
	}

	$due_date = date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')+14, date('Y')));
	$sql = "INSERT INTO projects (project_id, date_created, priority, due_date, salesperson, project_type, status, shipping, n_a1, form, product_type, kosher, halal, sample_size, base_included, target_included, cost_in_use_measure) "
	. "VALUES (" . $project_id . ", '" . date("Y-m-d") . "', 2, '" . $due_date . "', " . $_COOKIE['user_id'] . ",1,1,4,6,1,1,2,3,2,2,2,1)";
	mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	setCookie("pid", $project_id);
	header("location: project_info.php");
	exit();

}



if ( ($status >= 4 or $_COOKIE['userTypeCookie'] == 3) or ($status > 2 and $_COOKIE['userTypeCookie'] == 2) or $_COOKIE['userTypeCookie'] == 4 ) {
	$form_status = "DISABLED";
}
else {
	$form_status = "";
}



include('header.php');

?>



<?php include('inc_project_header.php'); ?>



<TABLE WIDTH=700 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="sales.src=salesOver.src"
		onMouseOut="sales.src=salesOver.src" 
		HREF="project_info.php"><IMG SRC="images/tabs/sales_over.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Sales info" NAME="sales"></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="client.src=clientOver.src"
		onMouseOut="client.src=clientOut.src" 
		HREF="client_info.php"><IMG SRC="images/tabs/client_out.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Client info" NAME="client"></TD>
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

<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR><FORM METHOD="post" ACTION="project_info.php">
		<TD><B CLASS="black">Summary:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE="text" NAME="summary" SIZE="36" VALUE="<?php echo $summary?>" <?php echo $form_status ?> MAXLENGTH=30></TD>
		<TD></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Due date:</B></TD>
		<TD>
		<SELECT NAME="month" <?php echo $form_status ?>>
			<?php foreach ( $months as $value ) {
				if ( $month == $value ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $value?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $value?></OPTION>
				<?php }
			} ?>
		</SELECT>
		<SELECT NAME="day" <?php echo $form_status ?>>
			<?php foreach ( $days as $value ) {
				if ( $day == $value ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $value?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $value?></OPTION>
				<?php }
			} ?>
		</SELECT>
		<SELECT NAME="year" <?php echo $form_status ?>>
			<?php for ( $n = date("Y")-1; $n <= date("Y") + 1; $n++ ) {
				if ( $year == $n ) { ?>
					<OPTION VALUE="<?php echo $n?>" SELECTED><?php echo $n?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $n?>"><?php echo $n?></OPTION>
				<?php }
			} ?>
		</SELECT></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Priority:</B></TD>
		<TD><SELECT NAME="priority" <?php echo $form_status ?>>
			<?php 
			foreach ( $priority_num as $value ) {
				if ( $value == $priority ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $priority_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $priority_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT></TD>
		<TD></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Salesperson:</B></TD>
		<TD><?php

		if ( $_COOKIE['userTypeCookie'] == 1 and $status < 4 ) {

			$sql = "SELECT user_id, first_name, last_name FROM users WHERE user_type = 2 OR last_name = 'Gooding' ORDER BY user_type, last_name";
			$result = mysql_query($sql, $link);
			$c = mysql_num_rows($result);
			if ( $c != 0 ) { ?>
				<SELECT NAME="salesperson">
				<?php while ( $row = mysql_fetch_array($result) ) {
					if ( $row['user_id'] == $salesperson ) { ?>
						<OPTION VALUE="<?php echo $row['user_id']?>" SELECTED><?php echo $row['first_name'] . " " . $row['last_name'];?></OPTION>
					<?php } else { ?>
						<OPTION VALUE="<?php echo $row['user_id']?>"><?php echo $row['first_name'] . " " . $row['last_name'];?></OPTION>
					<?php }
				} ?>
				</SELECT>
			<?php 
			}

		} else {

			$sql = "SELECT email, first_name, last_name FROM users WHERE user_id = " . $salesperson;
			$result = mysql_query($sql, $link);
			$c = mysql_num_rows($result);
			if ( $c != 0 ) {
				$row = mysql_fetch_array($result);
				echo "<A HREF='mailto:" . $row['email'] . "?subject=Regarding Project# " . $project_id_head . " (" . $company . ")'>" . $row['first_name'] . " " . $row['last_name'] . "</A>";
			}
			?>
			<INPUT TYPE="hidden" NAME="salesperson" VALUE="<?php echo $salesperson?>">
		
		<?php } ?>

		</TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Project type:</B></TD>
		<TD>
			<?php 
			foreach ( $project_type_num as $value ) {
				if ( $value == $project_type ) {
					echo $project_type_array[$value-1];
					if ( $value == 2 or $value == 3 ) {
						echo " <I>(Parent#  " . substr($parent_id, 0, 2) . "-" . substr($parent_id, -3) . ")</I>";
					} //<A HREF='project_info.php?new_id=" . $parent_id . "'>   </A>
				}
			}
			?><INPUT TYPE="hidden" NAME="parent_id" VALUE="<?php echo $parent_id?>"></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Application:</B></TD>
		<TD><SELECT NAME="application" <?php echo $form_status ?>>
			<?php 
			foreach ( $application_num as $value ) {
				if ( $value == $application ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $application_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $application_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT></TD>
		<TD></TD>
	</TR>
	<TR>	
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Annual potential:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><SELECT NAME="annual_potential" <?php echo $form_status ?>> -->
			<?php 
			foreach ( $annual_potential_num as $value ) {
				if ( $value == $annual_potential ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $annual_potential_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $annual_potential_array[$value-1]?></OPTION>
				<?php }
			} ?>
 		</SELECT></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>
	<TR>
		<TD></TD>
		<TD><INPUT TYPE='submit' VALUE="Save" <?php echo $form_status ?>> <INPUT TYPE='button' VALUE="Cancel" onClick="JavaScript:history.go(-1)"></TD>
		<TD></TD>
	</TR>
</TABLE>

		</TD>
	</TR>
</TABLE>


<?php 

$sql = "SELECT * FROM change_log LEFT JOIN users USING(user_id) WHERE project_id = " . $_COOKIE['pid'] . " ORDER BY time_stamp DESC";
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

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR>



<?php include('inc_project_status.php'); ?>

<?php include('footer.php'); ?>