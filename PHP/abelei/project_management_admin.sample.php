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



$project_type_array = array("New","Revision","Resample","Other");
$project_type_num = array(1,2,3,4);

$priority_array = array("Low","Medium","High");
$priority_num = array(1,2,3);

$status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
$status_num = array(1,2,3,4,5);

$n_a1_array = array("Org-Cert","Org-Comp","Natural","WONF","NI","N&A","Art");
$n_a1_num = array(1,2,3,4,5,6,7);

$n_a2_array = array(""," Or Higher","Org-Cert","Org-Comp","Natural","WONF","NI","N&A","Art");
$n_a2_num = array(1,2,3,4,5,6,7,8,9);

$form_array = array("Liquid","Powder","Emulsion","Other");
$form_num = array(1,2,3,4);

$product_type_array = array("W.S.","O.S.","Plated","S.D.");
$product_type_num = array(1,2,3,4);

$kosher_array = array("No","Pareve","Dairy","Passover");
$kosher_num = array(1,2,3,4);

$halal_array = array("No","Yes","DNA");
$halal_num = array(1,2,3);

$sample_size_array = array("1 oz.","2 oz.","4 oz.","8 oz.","Other");
$sample_size_num = array(1,2,3,4,5);

$base_included_array = array("Yes","No","En route");
$base_included_num = array(1,2,3);

$target_included_array = array("Yes","No","En route");
$target_included_num = array(1,2,3);

$target_rmc_array = array("DNA","Forthcoming");
$target_rmc_num = array(1,2);

$cost_in_use_measure_array = array("lb.","gallon","kilo");
$cost_in_use_measure_num = array(1,2,3);

$suggested_level_array = array("Use as desired","Same as target","Other");
$suggested_level_num = array(1,2,3);

$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");



if ( isset($_POST['uri']) ) {

	$comments = escape_data($_POST['comments']);

	// CHECK lab_comments ENTERED TO SEE WHETHER IT'S BEEN CHANGED
	$sql = "SELECT lab_comments FROM projects WHERE lab_comments <> '" . $comments . "' AND project_id = " . $_SESSION['pid'];
	$result = mysql_query($sql, $link);
	if ( mysql_num_rows($result) > 0 ) {
		$row = mysql_fetch_array($result);
		$old_lab_comments = $row['lab_comments'];
		$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Comments', '" . $old_lab_comments . "', '" . $comments . "', '" . date("Y-m-d H:i:s") . "')";
		mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	}

	$sql = "UPDATE projects " .
	" SET lab_comments = '" . $comments . "'" .
	" WHERE project_id = " . $_SESSION['pid'];
	mysql_query($sql, $link);
	$_SESSION['note'] = "Comments successfully saved<BR>";
	header("location: " . $_POST['uri']);
	exit();

}



if ( isset($_SESSION['pid']) ) {

	if ( !empty($_POST) ) {

		$n_a1 = $_POST['n_a1'];
		$n_a2 = $_POST['n_a2'];
		$form = $_POST['form'];
		$product_type = $_POST['product_type'];
		$kosher = $_POST['kosher'];
		$halal = $_POST['halal'];
		$sample_size = $_POST['sample_size'];
		$sample_size_other = $_POST['sample_size_other'];
		$base_included = $_POST['base_included'];
		$target_included = $_POST['target_included'];
		$target_level = $_POST['target_level'];
		$target_rmc = $_POST['target_rmc'];
		$cost_in_use = $_POST['cost_in_use'];
		$cost_in_use_measure = $_POST['cost_in_use_measure'];
		$comments = $_POST['comments'];
		$status = $_POST['status'];

		// check_field() FUNCTION IN global.php
		check_field($target_level, 1, 'Target use level');
		check_field($cost_in_use, 1, 'Target RMC cost');
		check_field($comments, 1, 'Project details');

		if ( !$error_found ) {

			// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
			$target_level = escape_data($target_level);
			$cost_in_use = escape_data($cost_in_use);
			$sample_size_other = escape_data($sample_size_other);
			$comments = escape_data($comments);

			// CHECK COMMENTS ENTERED TO SEE WHETHER IT'S BEEN CHANGED
			$sql = "SELECT comments FROM projects WHERE sample_info_submitted = 1 AND comments <> '" . $comments . "' AND project_id = " . $_SESSION['pid'];
			$result = mysql_query($sql, $link);
			if ( mysql_num_rows($result) > 0 ) {
				$row = mysql_fetch_array($result);
				$old_comments = $row['comments'];
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Project details', '" . $old_comments . "', '" . $comments . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}

			$sql = "UPDATE projects " .
				" SET sample_info_submitted = 1," .
				" n_a1 = " . $n_a1 . "," .
				" n_a2 = " . $n_a2 . ", " .
				" form = " . $form . ", " .
				" product_type = " . $product_type . ", " .
				" kosher = " . $kosher . ", " .
				" halal = " . $halal . ", " .
				" sample_size = " . $sample_size . ", " .
				" sample_size_other = '" . $sample_size_other . "', " .
				" base_included = " . $base_included . ", " .
				" target_included = " . $target_included . ", " .
				" target_level = '" . $target_level . "', " .
				" target_rmc = " . $target_rmc . ", " .
				" cost_in_use = '" . $cost_in_use . "', " .
				" cost_in_use_measure = " . $cost_in_use_measure . ", " .
				" comments = '" . $comments . "'" .
				" WHERE project_id = " . $_SESSION['pid'];
			mysql_query($sql, $link);

			$_SESSION['note'] = "Information successfully saved<BR>";
			header("location: project_management_admin.sample.php");
			exit();
		}

	}

	else {

		$sql = "SELECT * FROM projects WHERE project_id = " . $_SESSION['pid'];
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result);
		if ( $row['n_a1'] == "" ) {
			$n_a1 = 6;
		}
		else {
			$n_a1 = $row['n_a1'];
		}
		$n_a2 = $row['n_a2'];
		$form = $row['form'];
		$product_type = $row['product_type'];
		if ( $row['kosher'] == "" ) {
			$kosher = 2;
		}
		else {
			$kosher = $row['kosher'];
		}
		if ( $row['halal'] == "" ) {
			$halal = 3;
		}
		else {
			$halal = $row['halal'];
		}
		if ( $row['recommended_use'] == "" ) {
			$recommended_use = 1;
		}
		else {
			$recommended_use = $row['recommended_use'];
		}
		$recommended_use_other = $row['recommended_use_other'];
		if ( $row['sample_size'] == "" ) {
			$sample_size = 2;
		}
		else {
			$sample_size = $row['sample_size'];
		}
		$sample_size_other = $row['sample_size_other'];
		if ( $row['base_included'] == "" ) {
			$base_included = 2;
		}
		else {
			$base_included = $row['base_included'];
		}
		if ( $row['target_included'] == "" ) {
			$target_included = 2;
		}
		else {
			$target_included = $row['target_included'];
		}
		$target_level = $row['target_level'];
		$target_rmc = $row['target_rmc'];
		$cost_in_use = $row['cost_in_use'];
		$cost_in_use_measure = $row['cost_in_use_measure'];
		$status = $row['status'];
		$comments = $row['comments'];

	}

}



$form_status = "";
if ( ($status >= 4 or $_SESSION['userTypeCookie'] == 3) or ($status > 2 and $_SESSION['userTypeCookie'] == 2) or $_SESSION['userTypeCookie'] == 4 ) {
	$form_status = "readonly=\"readonly\"";
}



if ( $_GET['action'] == "del" ) {
	$sql = "DELETE FROM flavors WHERE flavor_id = '" . $_GET['fid'] . "' AND project_id = " . $_GET['pid'];
	mysql_query($sql, $link);
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

<TABLE WIDTH=700 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="sales.src=salesOver.src"
		onMouseOut="sales.src=salesOut.src" 
		HREF="project_management_admin.sales.php"><IMG SRC="images/tabs/sales_out.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Sales info" NAME="sales"></a></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="client.src=clientOver.src"
		onMouseOut="client.src=clientOut.src" 
		HREF="project_management_admin.client.php"><IMG SRC="images/tabs/client_out.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Contact info" NAME="client"></a></TD>
		<TD><IMG SRC="images/tabs/sample_over.gif" WIDTH=106 HEIGHT=18 BORDER=0 ALT="Sample info" NAME="sample"></TD>
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

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Lab assignees:</B></TD>
		<TD><?php
		$sql = "SELECT * FROM lab_assignees LEFT JOIN users USING(user_id) WHERE project_id = " . $_SESSION['pid'];
		$result = mysql_query($sql, $link);
		$c = mysql_num_rows($result);
		if ( $c == 0 ) {
			echo "<I>None yet</I>";
		}
		else {
			$i = 0;
			while ( $row = mysql_fetch_array($result) ) {
				if ( $row['last_name'] == 'Tang' ) {
					$lab = "Tang";
				} else {
					$lab = strtoupper(substr($row['first_name'],0,1) . substr($row['last_name'],0,1));
				}
				if ( $status < 4 ) {
					echo "<A HREF='JavaScript:delete_lab(" . $row['assignee_id'] . ")'><IMG SRC='images/delete.gif' WIDTH='16' HEIGHT='16' BORDER='0' ALIGN=TOP></A>";
					echo "<A HREF='mailto:" . $row['email'] . "?subject=Regarding Project# " . $project_id_head . " (" . $name . ")'>" . $lab . "</A>";
				}
				else {
					echo "<A HREF='mailto:" . $row['email'] . "?subject=Regarding Project# " . $project_id_head . " (" . $name . ")'>" . $lab . "</A>";
				}
				$i++;
				if ( $i < $c ) {
					echo "&nbsp;&nbsp;";
				}
			}
		}
		?></TD>
		<TD>
			<INPUT TYPE="button" VALUE="Add assignee" onClick="window.location='project_management_admin_choose_assignee.php'" STYLE="font-size:7pt;">
		</TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>

	<TR VALIGN=TOP><FORM METHOD="post" ACTION="project_management_admin.sample.php">
		<INPUT TYPE="hidden" NAME="status" VALUE="<?php echo $status;?>">
		<TD><B CLASS="black">Flavor(s):</B></TD>
		<TD VALIGN=MIDDLE><?php
		$sql = "SELECT * FROM flavors WHERE project_id = " . $_SESSION['pid'] . " ORDER BY flavor_name";
		$result = mysql_query($sql, $link);
		$c = mysql_num_rows($result);
		if ( $c == 0 ) {
			echo "<I>None yet</I>";
		}
		else {
			//$i = 0;
			echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>";
			while ( $row = mysql_fetch_array($result) ) {
				// TROY HAD MIKE ALLOW EDITS AND DELETIONS AT ANY TIME; 6/12/2008
				//if ( $_SESSION['userTypeCookie'] == 2 or $status >= 4 ) {
				//	echo "<TR><TD>" . $row['flavor_id'] , " - " . $row['flavor_name'] . "</TD><TD>&nbsp;&nbsp;</TD><TD><I STYLE='font-size:7pt;color:#333333'>Expires " . date("m/d/Y", strtotime($row['expiration_date'])) . "</I></TD><TD>&nbsp;&nbsp;</TD><TD><I STYLE='font-size:7pt;color:#333333'>Level: " . $row['suggested_level_other'] . " " . $row['use_in'] . "</I></TD><TD>&nbsp;&nbsp;</TD></TR>";
				//}
				//else {
					echo "<TR><TD><A HREF=\"JavaScript:delete_flavor('" . $row['flavor_id'] . "'," . $row['project_id'] . ")\"><IMG SRC='images/delete.gif' WIDTH='16' HEIGHT='16' BORDER='0' ALIGN=TOP></A> <A HREF='project_management_admin_enter_flavor.php?fid=" . $row['flavor_id'] . "&proj_id=" . $row['project_id'] ."'>" . $row['flavor_id'] , " - " . $row['flavor_name'] . "</A></TD><TD>&nbsp;&nbsp;</TD><TD><I STYLE='font-size:7pt;color:#333333'>Expires: " . date("m/d/Y", strtotime($row['expiration_date'])) . "</I></TD><TD>&nbsp;&nbsp;</TD><TD><I STYLE='font-size:7pt;color:#333333'>Level: " . $row['suggested_level_other'] . " " . $row['use_in'] . "</I></TD><TD>&nbsp;&nbsp;</TD></TR>";
				//}
			}
			echo "</TABLE>";
		}
		if ( $c < 7 ) {
		?>
			</TD><TD>
			<?php if ( $status < 4 and ($_SESSION['userTypeCookie'] != 2) ) {?>
				<INPUT TYPE="button" VALUE="Add flavor" onClick="window.location='project_management_admin_enter_flavor.php'" STYLE="font-size:7pt">
			<?php } ?>
			</TD>
		<?php } 
		else { ?>
			</TD><TD></TD>
		<?php } ?>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">N_or_A:</B></TD>
		<TD>
		<SELECT NAME="n_a1" <?php echo $form_status ?>>
			<?php 
			foreach ( $n_a1_num as $value ) {
				if ( $value == $n_a1 ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $n_a1_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $n_a1_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT> <SELECT NAME="n_a2" <?php echo $form_status ?>>
			<?php 
			foreach ( $n_a2_num as $value ) {
				if ( $value == $n_a2 ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $n_a2_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $n_a2_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Form:</B></TD>
		<TD><SELECT NAME="form" <?php echo $form_status ?>>
			<?php 
			foreach ( $form_num as $value ) {
				if ( $value == $form ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $form_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $form_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Product type:</B></TD>
		<TD><SELECT NAME="product_type" <?php echo $form_status ?>>
			<?php 
			foreach ( $product_type_num as $value ) {
				if ( $value == $product_type ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $product_type_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $product_type_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Kosher:</B></TD>
		<TD><SELECT NAME="kosher" <?php echo $form_status ?>>
			<?php 
			foreach ( $kosher_num as $value ) {
				if ( $value == $kosher ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $kosher_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $kosher_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Halal:</B></TD>
		<TD><SELECT NAME="halal" <?php echo $form_status ?>>
			<?php 
			foreach ( $halal_num as $value ) {
				if ( $value == $halal ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $halal_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $halal_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Sample size:</B></TD>
		<TD><SELECT NAME="sample_size" <?php echo $form_status ?>>
			<?php 
			foreach ( $sample_size_num as $value ) {
				if ( $value == $sample_size ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $sample_size_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $sample_size_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT> &nbsp;<I>if "Other":</I> <INPUT TYPE="text" NAME="sample_size_other" SIZE="10" VALUE="<?php echo $sample_size_other?>" <?php echo $form_status ?>></TD><TD>
			<?php if ( $status < 4 and ($_SESSION['userTypeCookie'] == 3) ) {   //  or $_SESSION['userTypeCookie'] == 1?>
				<INPUT TYPE="button" VALUE="Change" onClick="window.location='project_management_admin_enter_sample_size.php'" STYLE="font-size:7pt">
			<?php } ?>
			</TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Base included:</B></TD>
		<TD><SELECT NAME="base_included" <?php echo $form_status ?>>
			<?php 
			foreach ( $base_included_num as $value ) {
				if ( $value == $base_included ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $base_included_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $base_included_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Target included:</B></TD>
		<TD><SELECT NAME="target_included" <?php echo $form_status ?>>
			<?php 
			foreach ( $target_included_num as $value ) {
				if ( $value == $target_included ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $target_included_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $target_included_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><NOBR><B CLASS="black">Target use level:</B>&nbsp;&nbsp;&nbsp;</NOBR></TD>
		<TD><INPUT TYPE="text" NAME="target_level" SIZE="24" VALUE="<?php echo $target_level; ?>" <?php echo $form_status ?>></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>



<?php if ( $_SESSION['userTypeCookie'] < 3 ) { ?>

		<TR>
			<TD><B CLASS="black">Target RMC:</B></TD>
			<TD><SELECT NAME="target_rmc" <?php echo $form_status ?>>
				<?php 
				foreach ( $target_rmc_num as $value ) {
					if ( $value == $target_rmc ) { ?>
						<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $target_rmc_array[$value-1]?></OPTION>
					<?php } else { ?>
						<OPTION VALUE="<?php echo $value?>"><?php echo $target_rmc_array[$value-1]?></OPTION>
					<?php }
				} ?>
			</SELECT> $<INPUT TYPE="text" NAME="cost_in_use" SIZE="12" VALUE="<?php echo $cost_in_use?>" <?php echo $form_status ?>> <SELECT NAME="cost_in_use_measure" <?php echo $form_status ?>>
				<?php 
				foreach ( $cost_in_use_measure_num as $value ) {
					if ( $value == $cost_in_use_measure ) { ?>
						<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $cost_in_use_measure_array[$value-1]?></OPTION>
					<?php } else { ?>
						<OPTION VALUE="<?php echo $value?>"><?php echo $cost_in_use_measure_array[$value-1]?></OPTION>
					<?php }
				} ?>
			</SELECT></TD>
			<TD></TD>
		</TR>
		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
		</TR>

<?php } else { ?>

	<INPUT TYPE="hidden" NAME="target_rmc" VALUE="<?php echo $target_rmc?>">
	<INPUT TYPE="hidden" NAME="cost_in_use" VALUE="<?php echo $cost_in_use?>">
	<INPUT TYPE="hidden" NAME="cost_in_use_measure" VALUE="<?php echo $cost_in_use_measure?>">

<?php } ?>

	<TR VALIGN=TOP>
		<TD><B CLASS="black">Project<BR>details and<BR>comments:</B>&nbsp;&nbsp;&nbsp;</TD>
		<TD>
		<?php
		// ONLY ALLOW SALESPERSON TO SAVE COMMENTS IF PROJECT'S ALREADY IN LAB
		if ( ($status == 2 and $_SESSION['userTypeCookie'] == 2) or $form_status == '' ) { ?>
			<TEXTAREA NAME="comments" ROWS="14" COLS="55" <?php //echo $form_status ?>><?php echo $comments ?></TEXTAREA>
		<?php //} elseif ( $form_status == '' ) { ?>
			<!-- <TEXTAREA NAME="comments" ROWS="14" COLS="70"><?php //echo $comments ?></TEXTAREA> -->
		<?php } else { ?>
			<?php echo "<SPAN STYLE='line-height:14pt'>" . $comments . "</SPAN>" ?>
		<?php } ?></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>

	<TR>
		<TD></TD>
		<TD>
		<?php
		// ONLY ALLOW SALESPERSON TO SAVE COMMENTS IF PROJECT'S ALREADY IN LAB
		if ( $status == 2 and $_SESSION['userTypeCookie'] == 2 ) { ?>
			<INPUT TYPE='submit' VALUE="Save" <?php //echo $form_status ?>>
		<?php } else { ?>
			<INPUT TYPE='submit' VALUE="Save" <?php echo $form_status ?>>
		<?php } ?>
			 <INPUT TYPE='button' VALUE="Cancel" onClick="JavaScript:history.go(-1)"></TD>
		<TD></TD>
	</TR></FORM>
</TABLE>

		</TD>
	</TR>
</TABLE>



</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR>



<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide
$(document).ready(function(){
	$(":input[readonly]").addClass("readOnly");
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");
});
function delete_lab(aid) {
	if ( confirm('Are you sure you want to delete this assignee?') ) {
		document.location.href = "project_management_admin_choose_assignee.php?action=del&aid=" + aid
	}
}

function delete_flavor(fid,pid) {
	if ( confirm('Are you sure you want to delete this flavor?') ) {
		document.location.href = "project_management_admin.sample.php?action=del&fid=" + fid + "&pid=" + pid
	}
}

 // End -->
</SCRIPT>



<?php include('inc_project_status.php'); ?>


<?php include("inc_footer.php"); ?>