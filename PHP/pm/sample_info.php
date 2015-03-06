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

if ( isset($_POST['uri']) ) {
	//if ( $_POST['comments'] == '' ) {
	//	header("location: " . $_POST['uri']);
	//	exit();
	//}
	//else {
		$comments = escape_data(addslashes($_POST['comments']));

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
	//}		
}



if ( isset($_SESSION['pid']) ) {

	if ( !empty($_POST) ) {

		$n_a1 = escape_data($_POST['n_a1']);
		$n_a2 = escape_data($_POST['n_a2']);
		$form = escape_data($_POST['form']);
		$form_2 = escape_data($_POST['form_2']);
		$product_type = escape_data($_POST['product_type']);
		$product_type_2 = escape_data($_POST['product_type_2']);
		$kosher = escape_data($_POST['kosher']);
		$halal = escape_data($_POST['halal']);
		$sample_size = escape_data($_POST['sample_size']);
		$sample_size_other = escape_data($_POST['sample_size_other']);
		$base_included = escape_data($_POST['base_included']);
		$target_included = escape_data($_POST['target_included']);
		$target_level =escape_data($_POST['target_level']);
		$target_rmc = escape_data($_POST['target_rmc']);
		$cost_in_use = escape_data($_POST['cost_in_use']);
		$cost_in_use_measure = escape_data($_POST['cost_in_use_measure']);
		$comments = escape_data($_POST['comments']);
		$status = escape_data($_POST['status']);

		$old_na1 = escape_data($_POST['old_na1']);
		$old_na2 = escape_data($_POST['old_na2']);
		$old_form = escape_data($_POST['old_form']);
		$old_product_type = escape_data($_POST['old_product_type']);
		$old_form_2 = escape_data($_POST['old_form_2']);
		$old_product_type_2 = escape_data($_POST['old_product_type_2']);
		$old_kosher = escape_data($_POST['old_kosher']);
		$old_halal = escape_data($_POST['old_halal']);
		$old_sample_size = escape_data($_POST['old_sample_size']);
		$old_sample_size_other = escape_data($_POST['old_sample_size_other']);
		$old_base_included = escape_data($_POST['old_base_included']);
		$old_target_included = escape_data($_POST['old_target_included']);
		$old_target_level = escape_data($_POST['old_target_level']);
		
		// check_field() FUNCTION IN global.php
		check_field($target_level, 1, 'Target use level');
		check_field($cost_in_use, 1, 'Target RMC cost');
		check_field($comments, 1, 'Project details');

		if ( !$error_found ) {

			if ( $old_na1 != $n_a1 and $old_na1 != "") {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'NorA (Prefered)', '" . $n_a1_array[$old_na1-1] . "', '" . $n_a1_array[$n_a1-1] . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
			
			if ( $old_na2 != $n_a2 and $old_na2 != "") {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'NorA (Optional)', '" . $n_a2_array[$old_na2-1] . "', '" . $n_a2_array[$n_a2-1] . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
			
			if ( $old_form != $form and $old_form != "" ) {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Form (Prefered)', '" . $form_array[$old_form-1] . "', '" . $form_array[$form-1] . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}

			if ( $old_product_type != $product_type and $old_product_type != "" ) {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Product Type (Prefered)', '" . $product_type_array[$old_product_type-1] . "', '" . $product_type_array[$product_type-1] . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
			
			if ( $old_form_2 != $form_2 and $old_form_2 != "" ) {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Form (Otional)', '" . $form_array[$old_form_2-1] . "', '" . $form_array[$form_2-1] . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
			
			if ( $old_product_type_2 = $product_type_2 and $old_product_type_2 != "" ) {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Product Type (Optional)', '" . $product_type_array[$old_product_type_2-1] . "', '" . $product_type_array[$product_type_2-1] . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
			
			if ( $old_kosher != $kosher and $old_kosher != "" ) {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Kosher', '" . $kosher_array[$old_kosher-1] . "', '" . $kosher_array[$kosher-1] . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
			
			if ( $old_halal != $halal and $old_halal != "") {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Halal', '" . $halal_array[$old_halal-1] . "', '" . $halal_array[$halal-1] . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
			
			if ( $old_sample_size != $sample_size and $old_sample_size != "" ) {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Sample Size', '" . $sample_size_array[$old_sample_size-1] . "', '" . $sample_size_array[$sample_size-1] . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
			
			if ( $old_sample_size_other != $sample_size_other and $old_sample_size_other != "") {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Sample Size Other', '" . $old_sample_size_other . "', '" . $sample_size_other . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
			
			if ( $old_base_included != $base_included and $old_base_included != "") {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Base Included', '" . $base_included_array[$old_base_included-1] . "', '" . $base_included_array[$base_included-1] . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
			
			if ( $old_target_included != $target_included and $old_target_included != "") {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Target Included', '" . $target_included_array[$old_target_included-1] . "', '" . $target_included_array[$target_included-1] . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
			
			if ( $old_target_level != $target_level and $old_target_level != "") {
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Target Use Level', '" . $old_target_level . "', '" . $target_level . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}
			
				
			// CHECK COMMENTS ENTERED TO SEE WHETHER IT'S BEEN CHANGED
			$sql = "SELECT comments FROM projects WHERE sample_info_submitted = 1 AND comments <> '" . $comments . "' AND project_id = " . $_SESSION['pid'];
			$result = mysql_query($sql, $link);
			if ( mysql_num_rows($result) > 0 ) {
				$old_comments=$row[0];
				$sql = "INSERT INTO change_log (project_id, user_id, field_name, old_value, new_value, time_stamp) VALUE (" . $_SESSION['pid'] . ", " . $_SESSION['user_id'] . ", 'Project details', '" . $old_comments . "', '" . $comments . "', '" . date("Y-m-d H:i:s") . "')";
				mysql_query($sql, $link)   or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			}

			$sql = "UPDATE projects " .
				" SET sample_info_submitted = 1," .
				" n_a1 = '" . $n_a1 . "'," .
				" n_a2 = '" . $n_a2 . "', " .
				" form = '" . $form . "', " .
				" form_2 = '" . $form_2 . "', " .
				" product_type = '" . $product_type . "', " .
				" product_type_2 = '" . $product_type_2 . "', " .
				" kosher = '" . $kosher . "', " .
				" halal = '" . $halal . "', " .
				" sample_size = '" . $sample_size . "', " .
				" sample_size_other = '" . $sample_size_other . "', " .
				" base_included = '" . $base_included . "', " .
				" target_included = '" . $target_included . "', " .
				" target_level = '" . $target_level . "', " .
				" target_rmc = '" . $target_rmc . "', " .
				" cost_in_use = '" . $cost_in_use . "', " .
				" cost_in_use_measure = '" . $cost_in_use_measure . "', " .
				" comments = '" . $comments . "'" .
				" WHERE project_id = " . $_SESSION['pid'];
				mysql_query($sql, $link) or die ( mysql_error() . " Failed execute query: $sql <br />");
				$_SESSION['note'] = "Information successfully saved<BR>";
				header("location: sample_info.php");
				exit();
			}

		// } // POSTED DATA

	}

	else {

		$sql = "SELECT * FROM projects WHERE project_id = " . $_SESSION['pid'];
	//	echo "<br />$sql<br />";
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
		$form_2 = $row['form_2'];
		$product_type = $row['product_type'];
		$product_type_2 = $row['product_type_2'];
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



if ( $status >= 4 or ($status > 2 and ( $_SESSION['userTypeCookie'] == 2 or $_SESSION['userTypeCookie'] == 3 )) or $_SESSION['userTypeCookie'] == 4 ) {
	$form_status = "DISABLED";
}
else {
	$form_status = "";
}



if ( $_GET['action'] == "del" ) {
	$sql = "DELETE FROM flavors WHERE flavor_id = '" . $_GET['fid'] . "' AND project_id = " . $_GET['pid'];
	mysql_query($sql, $link);
}

include('header.php');

?>



<?php include('inc_project_header.php') ?>



<TABLE WIDTH=700 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="sales.src=salesOver.src"
		onMouseOut="sales.src=salesOut.src" 
		HREF="project_info.php"><IMG SRC="images/tabs/sales_out.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Sales info" NAME="sales"></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="client.src=clientOver.src"
		onMouseOut="client.src=clientOut.src" 
		HREF="client_info.php"><IMG SRC="images/tabs/client_out.gif" WIDTH=101 HEIGHT=18 BORDER=0 ALT="Client info" NAME="client"></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="sample.src=sampleOver.src"
		onMouseOut="sample.src=sampleOver.src" 
		HREF="sample_info.php"><IMG SRC="images/tabs/sample_over.gif" WIDTH=106 HEIGHT=18 BORDER=0 ALT="Sample info" NAME="sample"></TD>
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
					echo "<A HREF='mailto:" . $row['email'] . "?subject=Regarding Project: " . $project_id_head . " (" . $company . ") ". $summary."'>" . $lab . "</A>";
				}
				else {
					echo "<A HREF='mailto:" . $row['email'] . "?subject=Regarding Project: " . $project_id_head . " (" . $company . ") ". $summary."'>" . $lab . "</A>";
				}
				$i++;
				if ( $i < $c ) {
					echo "&nbsp;&nbsp;";
				}
			}
		}
		?></TD>
		<TD>
		<?php //if ( ($status == 2 and $_SESSION['userTypeCookie'] == 3) or ($status < 4 and $_SESSION['userTypeCookie'] == 1) ) { ?>
			<INPUT TYPE="button" VALUE="Add assignee" onClick="window.location='choose_assignee.php'" STYLE="font-size:7pt;">
		<?php //} ?>
		</TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>

	<TR VALIGN=TOP><FORM METHOD="post" ACTION="sample_info.php">
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
					echo "<TR><TD><A HREF=\"JavaScript:delete_flavor('" . $row['flavor_id'] . "'," . $row['project_id'] . ")\"><IMG SRC='images/delete.gif' WIDTH='16' HEIGHT='16' BORDER='0' ALIGN=TOP></A> <A HREF='enter_flavor.php?fid=" . $row['flavor_id'] . "&proj_id=" . $row['project_id'] ."'>" . $row['flavor_id'] , " - " . $row['flavor_name'] . "</A></TD><TD>&nbsp;&nbsp;</TD><TD><I STYLE='font-size:7pt;color:#333333'>Expires: " . date("m/d/Y", strtotime($row['expiration_date'])) . "</I></TD><TD>&nbsp;&nbsp;</TD><TD><I STYLE='font-size:7pt;color:#333333'>Level: " . $row['suggested_level_other'] . " " . $row['use_in'] . "</I></TD><TD>&nbsp;&nbsp;</TD></TR>";
				//}
			}
			echo "</TABLE>";
		}
		if ( $c < 7 ) {
		?>
			</TD><TD>
			<?php if ( $status < 4 and ($_SESSION['userTypeCookie'] != 2) ) {?>
				<INPUT TYPE="button" VALUE="Add flavor" onClick="window.location='enter_flavor.php'" STYLE="font-size:7pt">
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
		<INPUT type="hidden" name="old_na1" value="<?php echo $n_a1;?>">
		<INPUT type="hidden" name="old_na2" value="<?php echo $n_a2;?>">
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
		<TD>
		<INPUT type="hidden" name="old_form" value="<?php echo $form;?>">
		<INPUT type="hidden" name="old_form_2" value="<?php echo $form_2;?>">
		<SELECT NAME="form" <?php echo $form_status ?>>
			<?php 
			foreach ( $form_num as $value ) {
				if ( $value == $form ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $form_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $form_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT>
		<SELECT NAME="form_2" <?php echo $form_status ?>>
			<OPTION VALUE="0"></OPTION>
			<?php 
			foreach ( $form_num as $value ) {
				if ( $value == $form_2 ) { ?>
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
		<TD>
		<INPUT type="hidden" name="old_product_type" value="<?php echo $product_type;?>">
		<INPUT type="hidden" name="old_product_type_2" value="<?php echo $product_type_2;?>">
		<SELECT NAME="product_type" <?php echo $form_status ?>>
			<?php 
			foreach ( $product_type_num as $value ) {
				if ( $value == $product_type ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $product_type_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $product_type_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT>
		<SELECT NAME="product_type_2" <?php echo $form_status ?>>
		<OPTION value="0"></OPTION>
			<?php 
			foreach ( $product_type_num as $value ) {
				if ( $value == $product_type_2 ) { ?>
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
		<TD>
		<INPUT type="hidden" name="old_kosher" value="<?php echo $kosher;?>">
		<SELECT NAME="kosher" <?php echo $form_status ?>>
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
		<TD>
		<INPUT type="hidden" name="old_halal" value="<?php echo $halal;?>">
		<SELECT NAME="halal" <?php echo $form_status ?>>
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
		<TD>
		<INPUT type="hidden" name="old_sample_size" value="<?php echo $sample_size;?>">
		<SELECT NAME="sample_size" <?php echo $form_status ?>>
			<?php 
			foreach ( $sample_size_num as $value ) {
				if ( $value == $sample_size ) { ?>
					<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $sample_size_array[$value-1]?></OPTION>
				<?php } else { ?>
					<OPTION VALUE="<?php echo $value?>"><?php echo $sample_size_array[$value-1]?></OPTION>
				<?php }
			} ?>
		</SELECT> &nbsp;<I>if "Other":</I> <INPUT TYPE="text" NAME="sample_size_other" SIZE="10" VALUE="<?php echo $sample_size_other?>" <?php echo $form_status ?>></TD><TD>
			<?php if ( $status < 4 and ($_SESSION['userTypeCookie'] == 33) ) {   // disabled cs lab can make any change now  or $_SESSION['userTypeCookie'] == 1?>
				<INPUT TYPE="button" VALUE="Change" onClick="window.location='enter_sample_size.php'" STYLE="font-size:7pt">
			<?php } ?>
			</TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Base included:</B></TD>
		<TD>
		<INPUT type="hidden" name="old_base_included" value="<?php echo $base_included;?>">
		<SELECT NAME="base_included" <?php echo $form_status ?>>
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
		<TD>
		<INPUT type="hidden" name="old_target_included" value="<?php echo $target_included;?>">
		<SELECT NAME="target_included" <?php echo $form_status ?>>
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
		<TD>
		<INPUT type="hidden" name="old_target_level" value="<?php echo $target_level;?>">
		<INPUT TYPE="text" NAME="target_level" SIZE="24" VALUE="<?php echo ($target_level == "" ? 0 : $target_level); ?>" <?php echo $form_status ?>></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD>
	</TR>



<?php if ( $_SESSION['userTypeCookie'] < 3 ) { ?>

		<TR>
			<TD><B CLASS="black">Target RMC:</B></TD>
			<TD>
			<INPUT type="hidden" name="old_target_rmc" value="<?php echo $target_rmc;?>">
			<SELECT NAME="target_rmc" <?php echo $form_status ?>>
				<?php 
				foreach ( $target_rmc_num as $value ) {
					if ( $value == $target_rmc ) { ?>
						<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $target_rmc_array[$value-1]?></OPTION>
					<?php } else { ?>
						<OPTION VALUE="<?php echo $value?>"><?php echo $target_rmc_array[$value-1]?></OPTION>
					<?php }
				} ?>
			</SELECT> $<INPUT TYPE="text" NAME="cost_in_use" SIZE="12" VALUE="<?php echo ( empty($cost_in_use) ? "0.00" : $cost_in_use) ?>" <?php echo $form_status ?>>
			<INPUT type="hidden" name="old_cost_in_use" value="<?php echo $cost_in_use;?>">
			<INPUT type="hidden" name="old_cost_in_use_measure" value="<?php echo $cost_in_use_measure;?>">
			<SELECT NAME="cost_in_use_measure" <?php echo $form_status ?>>
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
		<TD><B CLASS="black">Project<BR>details and<BR>comments:</B>&nbsp;&nbsp;&nbsp;<br /><BR><BR>
		<input type="button" value="Print/View" onClick="popup('print_project_comments.php?pid=<?php echo $project_id;?>')"</TD>
		<TD>
		<?php
		// ONLY ALLOW SALESPERSON TO SAVE COMMENTS IF PROJECT'S ALREADY IN LAB ($status == 2 and
		if (  $_SESSION['userTypeCookie'] == 2 or $_SESSION['userTypeCookie'] == 1 ) { ?>
			<TEXTAREA NAME="comments" ROWS="14" COLS="55" <?php echo $form_status ?>><?php echo $comments ?></TEXTAREA>
		<?php //} elseif ( $form_status == '' ) { ?>
			<!-- <TEXTAREA NAME="comments" ROWS="14" COLS="70"><?php //echo $comments ?></TEXTAREA> -->
		<?php } else { ?>
			<?php echo "<SPAN STYLE='line-height:14pt'>" . str_replace($text_newline,$html_newline,$comments) . "</SPAN><br />----------" ?>
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

function delete_lab(aid) {
	if ( confirm('Are you sure you want to delete this assignee?') ) {
		document.location.href = "choose_assignee.php?action=del&aid=" + aid
	}
}

function delete_flavor(fid,pid) {
	if ( confirm('Are you sure you want to delete this flavor?') ) {
		document.location.href = "sample_info.php?action=del&fid=" + fid + "&pid=" + pid
	}
}

 // End -->
</SCRIPT>



<?php include('inc_project_status.php'); ?>

<?php
$sql="SELECT * FROM project_files WHERE project_id='".$_SESSION['pid']."' and status<>0";
$result=mysql_query($sql,$link) or die( mysql_error() . " Filed Execute SQL: $sql<br />");
$cnt=mysql_num_rows($result);
$height=100+$cnt*30;
?>

<iframe src="inc_project_attach_file.php?pid=<?php echo $_SESSION['pid'];?>" width="700px" height="<?php echo $height;?>px"
             scrolling="no" frameborder="0"></iframe>

<?php //include("inc_project_log.php");?>

<?php include('footer.php'); ?>