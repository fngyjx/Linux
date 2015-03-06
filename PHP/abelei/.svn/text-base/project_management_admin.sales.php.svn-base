<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');




if ( $_GET['new_id'] ) {
	$_SESSION['pid'] = $_GET['new_id'];
	header("location: project_management_admin.sales.php");
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
		$sql = "UPDATE projects SET status = 2 WHERE project_id = " . $_SESSION['pid'];
		mysql_query($sql, $link);
		header("location: project_management_admin.sales.php");
		exit();
	}

	elseif ( $_GET['stat'] == "3" ) {   // STATUS: FROM LAB TO FRONT DESK
		$sql = "UPDATE projects SET status = 3, sent_to_front = '" . date("Y-m-d H:i:s") . "' WHERE project_id = " . $_SESSION['pid'];
		mysql_query($sql, $link);

		// E-MAIL NOTIFICATION TO SALESPERSON AND FRONT DESK

		$message = "Hello,<BR><BR>";  

		$message .= "The samples for this project are listed below and are now ready to be shipped. Please click REPLY and let me know when they need to be in your customer's hands. You only need to respond either: \"Next Day\" (Air), in \"2-Days\", or \"Later\" (Ground), unless you specifically need FedEx or UPS. If so, say so.<BR><BR>";
  
		$message .= "If you need to contact your customer, you have until 1:30 PM Central Time tomorrow afternoon to let me know. If you do not reply to me by then, these samples will be shipped by UPS Ground.<BR><BR>";
  
		$message .= "List of flavors to be shipped:<BR><BR>";


		$sql = "SELECT name, sample_size, sample_size_other, users.email
		FROM projects
		LEFT JOIN customer_contacts
		USING ( contact_id ) 
		LEFT JOIN customers
		USING ( customer_id ) 
		LEFT JOIN users ON projects.salesperson = users.user_id
		WHERE project_id = " . $_SESSION['pid'];
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result)or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$name = $row['name'];
		$email = $row['email'];
		$sample_size = $row['sample_size'];
		$sample_size_other = $row['sample_size_other'];
		if ( $sample_size == 5 ) {
			$sample_size = $sample_size_other;
		} else {
			$sample_size = $sample_size_array[$sample_size-1];
		}

		$sql = "SELECT * FROM flavors WHERE project_id = " . $_SESSION['pid'] . " ORDER BY flavor_name";
		$result = mysql_query($sql, $link);
		$c = mysql_num_rows($result);
		if ( $c == 0 ) {
			$message .= "<I>None</I>";
		}
		else {
			while ( $row = mysql_fetch_array($result) ) {
				$message .= $sample_size . ", " . $row['flavor_id'] . " " . $row['flavor_name'] . ", " . $row['suggested_level_other'] . " " . $row['use_in'] . "<BR>";
			}
		}


		$message .= "<BR>Thanks,<BR><BR>";
		$message .= "Christina<BR><BR>";

		$mail_message = "<HTML><BODY><TABLE WIDTH=600 BORDER=0><TR><TD STYLE='font-family:verdana, tacoma, sans-serif'>";
		$mail_message .= $message;
		$mail_message .= "</TD></TR></TABLE></BODY></HTML>";

		$from = "cpeters@abelei.com";   //$row1['sales_e'];
		$to = $email;   //"$email";
		$cc = "cpeters@abelei.com";   // "moconnell@chicagoit.com"
		//$bcc = "moconnell@chicagoit.com";   // "moconnell@chicagoit.com"

		// PEAR MAIL PACKAGES
		require_once ('Mail.php');
		require_once ('Mail/mime.php');
		$text = 'Message requires an HTML-compatible e-mail program.';
		$crlf = "\n";
		$mime = new Mail_Mime($crlf);

		// Set the email body
		$mime->setTXTBody($text);
		$mime->setHTMLBody($mail_message);

		// Set the headers
		$mime->setFrom("$from");
		$mime->addCc("$cc");
		//$mime->addBcc("$bcc");
		$mime->setSubject($_SESSION['pid'] . " - " . $name . ", Shipping Decision Needed");

		// Get the formatted code
		$body = $mime->get();
		$headers = $mime->headers();

		// Invoke the Mail class' factory() method
		$mail =& Mail::factory('mail');

		// Send the email
		$mail->send($to, $headers, $body);

		header("location: project_management_admin.sales.php");
		exit();
	}

	elseif ( $_GET['stat'] == "5" ) {   // STATUS: FROM LAB TO FRONT DESK
		$sql = "UPDATE projects SET status = 5, follow_up = 5 WHERE project_id = " . $_GET['pid'];
		mysql_query($sql, $link);
		$_SESSION['note'] = "Project successfully cancelled<BR>";
		header("location: project_management_projects.php");
		exit();
	}

	elseif ( $_GET['stat'] == "4" ) {   // STATUS: FROM FRONT DESK TO SHIPPED
		$sql = "UPDATE projects SET status = 4, shipped_date = '" . date("Y-m-d") . "' WHERE project_id = " . $_GET['pid'];
		mysql_query($sql, $link);

		// E-MAIL NOTIFICATION TO CLIENT

		$sql = "SELECT shipping, shipper, shipper_other, tracking_number, customer_contacts.first_name AS client_first, customer_contacts.email
			AS client_e, users.first_name AS sales_first, users.last_name AS sales_last, users.email AS sales_e
			FROM projects
			LEFT JOIN customer_contacts
			USING ( contact_id ) 
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


		// COMMON CODE SHARED WITH PACKING SLIP (project_management_front_desk_packing_slip.php)
		include('inc_packing_slip.php');


		$mail_message = "<HTML><BODY><TABLE WIDTH=600 BORDER=0><TR><TD STYLE='font-family:verdana, tacoma, sans-serif'>";
		$mail_message .= $message;
		$mail_message .= "</TD></TR></TABLE></BODY></HTML>";

		$from = "info@abelei.com";   //$row1['sales_e'];
		$to = $row1['sales_e'];   //"$email";
		$bcc = "tgooding@abelei.com";   // "tgooding@abelei.com,$from,moconnell@chicagoit.com"

		// PEAR MAIL PACKAGES
		require_once ('Mail.php');
		require_once ('Mail/mime.php');
		$text = 'Message requires an HTML-compatible e-mail program.';
		$crlf = "\n";
		$mime = new Mail_Mime($crlf);

		// Set the email body
		$mime->setTXTBody($text);
		$mime->setHTMLBody($mail_message);

		// Set the headers
		$mime->setFrom("$from");
		$mime->addBcc("$bcc");
		$mime->setSubject('abelei sample shipped');

		// Get the formatted code
		$body = $mime->get();
		$headers = $mime->headers();

		// Invoke the Mail class' factory() method
		$mail =& Mail::factory('mail');

		// Send the email
		$mail->send($to, $headers, $body);

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
	$sql = "INSERT INTO projects (project_id, contact_id, date_created, priority, due_date, salesperson, project_type, parent_id, application, status, annual_potential, n_a1, n_a2, form, product_type, kosher, halal, sample_size, sample_size_other, base_included, target_included, target_level, target_rmc, cost_in_use, cost_in_use_measure, summary, comments, project_info_submitted, client_info_submitted, sample_info_submitted) VALUES ("
	. $project_id . ", "
	. "'" . $row['contact_id'] . "', "
	. "'" . date("Y-m-d") . "', "
	. "'" . $row['priority'] . "', "
	. "'" . $due_date . "', "
	. "'" . $_SESSION['user_id'] . "', "
	. "'" . $type . "', "
	. "'" . $id . "', "
	. "'" . $row['application'] . "', "
	. "'1', "
	. "'" . escape_data($row['annual_potential']) . "', "
	. "'" . $row['n_a1'] . "', "
	. "'" . $row['n_a2'] . "', "
	. "'" . $row['form'] . "', "
	. "'" . $row['product_type'] . "', "
	. "'" . $row['kosher'] . "', "
	. "'" . $row['halal'] . "', "
	. "'" . $row['sample_size'] . "', "
	. "'" . escape_data($row['sample_size_other']) . "', "
	. "'" . $row['base_included'] . "', "
	. "'" . $row['target_included'] . "', "
	. "'" . escape_data($row['target_level']) . "', "
	. "'" . $row['target_rmc'] . "', "
	. "'" . escape_data($row['cost_in_use']) . "', "
	. "'" . $row['cost_in_use_measure'] . "', "
	. "'" . escape_data($row['summary']) . "', "
	. "'" . escape_data($row['comments']) . "',"
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

	$_SESSION['pid'] = $project_id;
	header("location: project_management_admin.sales.php");
	exit();

}



if ( isset($_SESSION['pid']) ) {

	if ( !empty($_POST) ) {

		$summary = $_POST['summary'];
		$priority = $_POST['priority'];

		$due_date = $_POST['due_date'];
		$date_parts = explode("/", $due_date);
		$new_due_date = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
		if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
			if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
				$error_found=true;
				$error_message .= "Invalid (" . $due_date . ") date entered<BR>";
			}
		} else {
			$error_found=true;
			$error_message .= "Invalid (" . $due_date . ") date entered<BR>";
		}

		$salesperson = $_POST['salesperson'];
		$project_type = $_POST['project_type'];
		$parent_id = $_POST['parent_id'];
		$application = $_POST['application'];
		$annual_potential = $_POST['annual_potential'];

		// check_field() FUNCTION IN global.php
		// NONE TO CHECK

		if ( !$error_found ) {

			// CHECK DUE DATE ENTERED TO SEE WHETHER IT'S BEEN CHANGED
			$sql = "SELECT due_date FROM projects WHERE project_info_submitted = 1 AND due_date <> '" . $new_due_date . "' AND project_id = " . $_SESSION['pid'];
			$result = mysql_query($sql, $link);
			if ( mysql_num_rows($result) > 0 ) {
				$row = mysql_fetch_array($result);
				$old_date = date("m/d/Y", strtotime($row['due_date']));
				$new_date = date("m/d/Y", strtotime($new_due_date));
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Due date', '" . $old_date . "', '" . $new_date . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}

			$sql = "UPDATE projects " .
			" SET project_info_submitted = 1," .
			" summary = '" . $summary . "'," .
			" priority = " . $priority . "," .
			" due_date = '" . $new_due_date . "', " .
			" salesperson = " . $salesperson . ", " .
			" application = " . $application . ", " .
			" annual_potential = " . $annual_potential .
			" WHERE project_id = " . $_SESSION['pid'];
			mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			//echo $sql;

			$_SESSION['note'] = "Information successfully saved<BR>";
			header("location: project_management_admin.sales.php");
			exit();
		}

	}

	else {

		$sql = "SELECT * FROM projects WHERE project_id = " . $_SESSION['pid'];
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result);
		$summary = $row['summary'];
		$priority = $row['priority'];
		$due_date = $row['due_date'];
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
	. "VALUES (" . $project_id . ", '" . date("Y-m-d") . "', 2, '" . $due_date . "', " . $_SESSION['user_id'] . ",1,1,4,6,1,1,2,3,2,2,2,1)";
	mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$_SESSION['pid'] = $project_id;
	header("location: project_management_admin.sales.php");
	exit();

}



$form_status = "";
if ( ($status >= 4 or $_SESSION['userTypeCookie'] == 3) or ($status > 2 and $_SESSION['userTypeCookie'] == 2) or $_SESSION['userTypeCookie'] == 4 ) {
	$form_status = "readonly=\"readonly\"";
}





include("inc_header.php");
include('inc_project_header.php');

?>


<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

salesOut = new Image
salesOut.src = "images/tabs/sales_out.gif"
salesOver = new Image
salesOver.src = "images/tabs/sales_over.gif"

clientOut = new Image
clientOut.src = "images/tabs/client_out.gif"
clientOver = new Image
clientOver.src = "images/tabs/client_over.gif"

sampleOut = new Image
sampleOut.src = "images/tabs/sample_out.gif"
sampleOver = new Image
sampleOver.src = "images/tabs/sample_over.gif"

 // End -->
</SCRIPT>


<script type="text/javascript">
$(function() {
	$('#datepicker1').datepicker({
		changeMonth: true,
		changeYear: true
	});
});
</script>


<TABLE WIDTH=700 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><IMG SRC="images/tabs/sales_over.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Sales info" NAME="sales"></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="client.src=clientOver.src"
		onMouseOut="client.src=clientOut.src" 
		HREF="project_management_admin.client.php"><IMG SRC="images/tabs/client_out.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Contact info" NAME="client"></a></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="sample.src=sampleOver.src"
		onMouseOut="sample.src=sampleOut.src" 
		HREF="project_management_admin.sample.php"><IMG SRC="images/tabs/sample_out.gif" WIDTH=106 HEIGHT=18 BORDER=0 ALT="Sample info" NAME="sample"></a></TD>
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

	<TR><FORM NAME="popper" METHOD="post" ACTION="project_management_admin.sales.php">
		<TD><B CLASS="black">Summary:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD><INPUT TYPE="text" NAME="summary" SIZE="36" VALUE="<?php echo $summary?>" <?php echo $form_status ?> MAXLENGTH=30></TD>
		<TD></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
	</TR>

	<TR>
		<TD><B CLASS="black">Due date:</B></TD>
		<TD><INPUT TYPE="text" SIZE="26" NAME="due_date" id="datepicker1" VALUE="<?php
		if ( $due_date != '' ) {
			echo date("m/d/Y", strtotime($due_date));
		}
		?>"></TD>
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

		if ( $_SESSION['userTypeCookie'] == 1 and $status < 4 ) {

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
				echo "<A HREF='mailto:" . $row['email'] . "?subject=Regarding Project# " . $project_id_head . " (" . $name . ")'>" . $row['first_name'] . " " . $row['last_name'] . "</A>";
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
					} //<A HREF='project_management_admin.sales.php?new_id=" . $parent_id . "'>   </A>
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

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR>
<script type="text/javascript">
$(document).ready(function(){
	$(":input[readonly]").addClass("readOnly");
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");
});
</script>


<?php include('inc_project_status.php'); ?>


<?php include("inc_footer.php"); ?>