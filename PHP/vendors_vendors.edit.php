<?php

include('inc_ssl_check.php');
if ( !isset($_SESSION) ) {session_start(); }

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 5 and $rights != 6 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( isset($_SESSION['subnote']) ) {
	$subnote = $_SESSION['subnote'];
	unset($_SESSION['subnote']);
}

if ( isset($_REQUEST['vid']) ) {
	$vid = $_REQUEST['vid'];
} elseif ( isset($_REQUEST['contact_id']) ) {
	$vid = $_REQUEST['contact_id'];
}

include('inc_global.php');

 if ( $vid != '' and $_REQUEST['remove'] == 1 ) { 
 
		$sql = "SELECT * FROM vendors WHERE vendor_id = " . $vid ." AND active = 1";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		$_SESSION['note'] = "Vendor ". $row['name'] ." has been inacted successfully<BR>";
		
		$sql = "UPDATE vendors set active = 0 WHERE vendor_id = " . $vid;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		echo $sql;

		header("location: vendors_vendors.edit.php");
		exit();
		
}

if ( !empty($_POST) and $_REQUEST['action'] != 'search' ) {

	$name = $_POST['name'];
	$web_address = $_POST['web_address'];
	$notes = $_POST['notes'];

	// check_field() FUNCTION IN global.php
	check_field($name, 1, 'Vendor');

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$name = escape_data($name);
		$web_address = escape_data($web_address);
		$notes = escape_data($notes);
		if ( $vid != "" ) {
			$sql = "UPDATE vendors " .
			" SET name = '" . $name . "'," .
			" web_address = '" . $web_address . "'," .
			" notes = '" . $notes . "'" .
			" WHERE vendor_id = " . $vid;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		else {
			$sql = "INSERT INTO vendors (name, web_address, notes) VALUES ('" . $name . "', '" . $web_address . "', '" . $notes . "')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$vid = mysql_insert_id();
		}

		$_SESSION['note'] = "Vendor information successfully saved<BR>";
		header("location: vendors_vendors.edit.php?vid=" . $vid);
		exit();
	}


}

else {
	if ( $vid != '' ) {
		$sql = "SELECT * FROM vendors WHERE vendor_id = " . $vid ." AND active = 1";
		$result = mysql_query($sql, $link);
		$row = mysql_fetch_array($result) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$name = $row['name'];
		$web_address = $row['web_address'];
		$notes = $row['notes'];
	}
	else {
		$name = "";
		$web_address = "";
		$notes = "";
	}
}


if ( $_GET['action'] == "inact" ) {
	$sql = "UPDATE vendor_addresses SET active = 0 WHERE address_id = " . $_GET['aid'];
	mysql_query($sql, $link);
	header("location: vendors_vendors.edit.php?vid=" . $_GET['vid']);
	exit();
}


if ( $_GET['action'] == "inact_contact" ) {
	$sql = "UPDATE vendor_contacts SET active = 0 WHERE contact_id = " . $_GET['contact_id'];
	mysql_query($sql, $link);
	header("location: vendors_vendors.edit.php?vid=" . $_GET['vid']);
	exit();
}



if ( $_GET['action'] == "delete_prod" ) {
	$sql = "UPDATE productprices SET is_deleted = 1 WHERE VendorID = " . $_GET['VendorID'] . " AND ProductNumberInternal = '" . $_GET['ProductNumberInternal'] . "'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";
	$sql = "DELETE FROM vendorproductcodes WHERE VendorID = " . $_GET['VendorID'] . " AND ProductNumberInternal = '" . $_GET['ProductNumberInternal'] . "' AND VendorProductCode = '" . $_GET['VendorProductCode'] . "'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo $sql . "<BR>";
	//die();
	$_SESSION['note'] = "Product successfully deleted<BR>";
	header("location: vendors_vendors.edit.php?vid=" . $_GET['VendorID']);
	exit();
}



if ( $_GET['action'] == "delete_tier" ) {
	$sql = "UPDATE productprices SET is_deleted = 1 WHERE VendorID = " . $_GET['VendorID'] . " AND ProductNumberInternal = '" . $_GET['ProductNumberInternal'] . "' AND Tier = '" . $_GET['Tier'] . "'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$_SESSION['note'] = "Pricing tier successfully deleted<BR>";
	header("location: vendors_vendors.edit.php?vid=" . $_GET['VendorID']);
	exit();
}


if ( isset($_REQUEST['VendorProductCode']) and $_REQUEST['action'] == 'search' ) {
	$VendorProductCode = $_REQUEST['VendorProductCode'];
}
if ( isset($_REQUEST['ProductNumberInternal']) and $_REQUEST['action'] == 'search' ) {
	$ProductNumberInternal = $_REQUEST['ProductNumberInternal'];
}
if ( isset($_REQUEST['Designation']) and $_REQUEST['action'] == 'search' ) {
	$Designation = $_REQUEST['Designation'];
}


include("inc_header.php");



$form_status = "";
if ( $_REQUEST['update'] != 1 ) {
	$form_status = "readonly=\"readonly\"";
}
	
?>


<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>

<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>

<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>

<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<FORM METHOD="post" ACTION="vendors_vendors.edit.php" NAME="pricing">


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR>
		<INPUT TYPE="hidden" NAME="vid" VALUE="<?php echo $vid;?>">
		<TD><B CLASS="black">Vendor:</B>&nbsp;</TD>
		<TD><INPUT TYPE='text' NAME="name" SIZE=42 VALUE="<?php echo stripslashes($name);?>" <?php echo $form_status;?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>
	<TR>
		<TD><NOBR><B CLASS="black">Web site:</B>&nbsp;</NOBR></TD>
		<TD><INPUT TYPE='text' NAME="web_address" SIZE=42 VALUE="<?php echo stripslashes($web_address);?>" <?php echo $form_status;?>></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD><B CLASS="black">Notes:</B>&nbsp;</TD>
		<TD><TEXTAREA NAME="notes" ROWS="3" COLS="30"<?php echo $form_status;?>><?php echo stripslashes($notes);?></TEXTAREA><INPUT TYPE="hidden" NAME="parent_url" VALUE="vendors_vendors.edit.php"></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="9"></TD>
	</TR>
	<TR>
		<TD></TD>
		<TD>
		<?php if ( $form_status != '' ) { ?>
			<INPUT TYPE="button" VALUE="Edit" CLASS="submit" onClick="window.location='vendors_vendors.edit.php?vid=<?php echo $vid;?>&update=1'">
			<INPUT TYPE="button" VALUE="Inact" CLASS="submit" onClick="window.location='vendors_vendors.edit.php?vid=<?php echo $vid;?>&remove=1'">
		<?php } else { ?>
			<INPUT TYPE='submit' VALUE="Save" CLASS="submit"> <INPUT TYPE='button' VALUE="Cancel" onClick="window.location='vendors_vendors.edit.php?vid=<?php echo $vid;?>'" CLASS="submit">
		<?php } ?>
		</TD>
	</TR>
</TABLE>


</TD></TR></TABLE>
</TD></TR></TABLE>




</TD><TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>

<TD>


<?php if ( $vid != '' and !$main_error and $form_status != '' ) { ?>

	<?php if ( $error_found ) {
		echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
		unset($error_message);
	} ?>

	<?php if ( $subnote ) {
		echo "<B STYLE='color:#990000'>" . $subnote . "</B><BR>";
	} ?>

	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD COLSPAN=2><NOBR><B STYLE="font-size:8pt">Addresses</B> / <A HREF="javascript:void(0)" onClick="popup('pop_add_vendor_address.php?vid=<?php echo $vid;?>')" STYLE="font-size:8pt">Add new address</A></NOBR></TD>
		</TR>
	</TABLE><BR>

	<?php
	$sql = "SELECT * FROM vendor_addresses WHERE vendor_id = " . $vid . " AND active = 1";
	$result_list = mysql_query($sql, $link) or die (mysql_error() ." Failed to execute SQL $sql <br />");
	if ( mysql_num_rows($result_list) > 0 ) {
		$bg = 0; ?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
		<?php
		while ( $row_list = mysql_fetch_array($result_list) ) {
			if ( $bg == 1 ) {
				$bgcolor = "#F3E7FD";
				$bg = 0;
			}
			else {
				$bgcolor = "#DFDFDF";
				$bg = 1;
			} ?>
			<TR BGCOLOR="<?php echo($bgcolor);?>" VALIGN=TOP>
				<TD><A HREF="javascript:void(0)" onClick="popup('pop_add_vendor_address.php?vid=<?php echo $vid;?>&aid=<?php echo $row_list['address_id'];?>')" STYLE="font-size:8pt"><IMG SRC="images/pencil.gif" WIDTH="16" HEIGHT="16" BORDER=0></A></TD>
				<TD><A HREF="JavaScript:inactivate(<?php echo($row_list['address_id'] . "," . $vid);?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A></TD>
				<TD STYLE="font-size:8pt">
				<?php
				if ( $row_list['address1'] != '' ) {
					echo $row_list['address1'];
					echo "<BR>";
				}
				if ( $row_list['address2'] != '' ) {
					echo $row_list['address2'];
					echo "<BR>";
				}
				echo "<NOBR>" . $row_list['city'] . ", " . $row_list['state'] . " " . $row_list['zip'] . " " . $row_list['country'] . "</NOBR><BR>";
				$sql_phone="SELECT distinct if(type=2,'phone','fax') , number FROM vendor_address_phones where address_id = " .$row_list['address_id'] ." AND type in ( 2, 4 )";
				$phone_result = mysql_query($sql_phone,$link) or die (mysql_error() ." Failed to execute SQL $sql_phone <br />");
				while ( $row_phone = mysql_fetch_array($phone_result) ) {
					if ( $row_phone[0] == 'phone' )
						echo "<br /><NOBR>Company Phone#: ". $row_phone['number'] ."</NOBR>";
					if ( $row_phone[0] == 'fax' )
						echo "<br /><NOBR>Company Fax#: ". $row_phone['number'] ."</NOBR>";
				}
				?>
				</TD>
			</TR>
		<?php } ?>
		</TABLE>
	<?php } else {
		echo "<I>No addresses found</I>";
	}
	
	echo "</TD></TR></TABLE>";
	echo "</TD></TR></TABLE>";

}

?>



	</TD><TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>

	<TD>


<?php if ( $vid != '' and !$main_error and $form_status != '' ) { ?>

	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>
	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR VALIGN=TOP><TD>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<!-- <TD COLSPAN=2><NOBR><B STYLE="font-size:8pt">Contacts</B> / <A HREF="javascript:void(0)" onClick="popup('pop_add_vendor_contact.php?vend_id=<?php //echo $vid;?>')" STYLE="font-size:8pt">Add new contact</A></NOBR></TD> -->
			<TD COLSPAN=2><NOBR><B STYLE="font-size:8pt">Contacts</B> / <A HREF="vendors_contacts.edit.php?update=1&vend_id=<?php echo $vid;?>" STYLE="font-size:8pt">Add new contact</A></NOBR></TD>
		</TR>
	</TABLE><BR>

	<?php
	$sql = "SELECT * FROM vendor_contacts
	LEFT JOIN vendor_addresses ON vendor_contacts.vendor_address_id = vendor_addresses.address_id
	JOIN vendors ON vendors.vendor_id = vendor_contacts.vendor_id
	WHERE vendor_contacts.vendor_id = " . $_REQUEST['vid'] . " AND vendor_contacts.active = 1 AND vendors.active=1";
	$result_list = mysql_query($sql, $link);
	if ( mysql_num_rows($result_list) > 0 ) {
		$bg = 0; ?>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
		<?php
		while ( $row_list = mysql_fetch_array($result_list) ) {
			if ( $bg == 1 ) {
				$bgcolor = "#F3E7FD";
				$bg = 0;
			}
			else {
				$bgcolor = "#DFDFDF";
				$bg = 1;
			} ?>
			<TR BGCOLOR="<?php echo($bgcolor);?>" VALIGN=TOP>
				<!-- <TD><A HREF="javascript:void(0)" onClick="popup('pop_add_vendor_contact.php?cid=<?php //echo $row_list['contact_id'];?>&vid=<?php //echo $vid;?>')" STYLE="font-size:8pt"><IMG SRC="images/pencil.gif" WIDTH="16" HEIGHT="16" BORDER=0></A></TD> -->
				<TD><A HREF="vendors_contacts.edit.php?cid=<?php echo $row_list['contact_id'];?>" STYLE="font-size:8pt"><IMG SRC="images/pencil.gif" WIDTH="16" HEIGHT="16" BORDER=0></A></TD>
				<TD><A HREF="JavaScript:inactivate_contact(<?php echo($row_list['contact_id'] . "," . $vid);?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A></TD>
				<TD STYLE="font-size:8pt">
				<?php
				echo "<NOBR><B STYLE='font-size:8pt'>" . $row_list['first_name'] . " " . $row_list['last_name'] . "</B></NOBR><BR>";
				//if ( $row_list['address1'] != '' ) {
				//	echo $row_list['address1'];
				//	echo "<BR>";
				//}
				//if ( $row_list['address2'] != '' ) {
				//	echo $row_list['address2'];
				//	echo "<BR>";
				//}
				//echo "<NOBR>" . $row_list['city'] . ", " . $row_list['state'] . " " . $row_list['zip'] . " " . $row_list['country'] . "</NOBR><BR>";
				if ( $row_list['email1'] != '' ) {
					echo "<A HREF='mailto:" . $row_list['email1'] . "'>" . $row_list['email1'] . "</A><BR>";
				}
				if ( $row_list['email2'] != '' ) {
					echo "<BR><A HREF='mailto:" . $row_list['email2'] . "'>" . $row_list['email2'] . "</A><BR>";
				}
				$sql = "SELECT * FROM vendor_contact_phones
				LEFT JOIN phone_types ON vendor_contact_phones.type = phone_types.type_id
				WHERE vendor_contact_phones.contact_id = " . $row_list['contact_id'];
				$result_phone = mysql_query($sql, $link);
				if ( mysql_num_rows($result_phone) > 0 ) { ?>
					<IMG SRC="images/delete.gif" WIDTH="1" HEIGHT="3" BORDER="0"><BR>
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

					<?php
					while ( $row_phone = mysql_fetch_array($result_phone) ) { ?>
							<TR VALIGN=TOP>
								<TD><NOBR><I STYLE="font-size:8pt"><?php echo $row_phone['description'];?>:</I>&nbsp;&nbsp;</NOBR></TD>
								<TD STYLE="font-size:8pt"><NOBR><A HREF='vendors_contacts.edit.php?cid=<?php echo $row_list['contact_id'];?>&pid=<?php echo $row_phone['phone_id'];?>'><?php echo $row_phone['number'];?></A>&nbsp;&nbsp;</NOBR></TD>
								<TD><NOBR><?php
								if ( $row_phone['number_description'] != '' ) {
									echo "(<I STYLE='font-size:8pt'>" . $row_phone['number_description'] . "</I>)";
								}
								?></NOBR></TD>
							</TR>
					<?php } ?>
					</TABLE>
				<?php } else {
					echo "<I STYLE='font-size:8pt'>No numbers found</I>";
				}
				?>
				</TD>
			</TR>
		<?php } ?>
		</TABLE>
	<?php } else {
		echo "<I STYLE='font-size:8pt'>No contacts found</I>";
	}

	echo "</TD></TR></TABLE>";
	echo "</TD></TR></TABLE>";

}


?>

</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>


<?php if ( $form_status != '' ) { ?>

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
			<TR VALIGN=TOP><FORM>
				<TD><B>Material Pricing</B></TD>
				<TD ALIGN=RIGHT><INPUT TYPE="button" VALUE="Add product" onClick="popup('pop_add_product_vendor.php?VendorID=<?php echo $vid;?>')" CLASS="submit" STYLE="color:#330066;background-color: #DCDCDC"></TD>
			</TR></FORM>
		</TABLE><BR>
	
		<TABLE class="bounding">
		<TR VALIGN=TOP>
		<TD class="padded">
		<FORM ACTION="vendors_vendors.edit.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="action" VALUE="search">
		<INPUT TYPE="hidden" NAME="vid" VALUE="<?php echo $vid;?>">
	
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

			<TR>
				<TD><B>Vendor Product Code:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
				<TD><INPUT TYPE="text" NAME="VendorProductCode" VALUE="<?php echo $VendorProductCode;?>" SIZE="30"></TD>
			</TR>

			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR>
				<TD><B>Material number (internal):</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
				<TD><INPUT TYPE="text" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>" SIZE="30"></TD>
			</TR>

			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>
	
			<TR>
				<TD><B>Designation:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
				<TD><INPUT TYPE="text" NAME="Designation" VALUE="<?php echo $Designation;?>" SIZE="30"><INPUT TYPE="hidden" NAME="parent_url" VALUE="flavors_materials_pricing.php"></TD>
			</TR>

			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="12"></TD>
			</TR>

			<TR>
				<TD COLSPAN="3"><INPUT style="float:right" TYPE="submit" VALUE="Search" CLASS="submit_medium" NAME="submitter"></TD>
			</TR>
		</TABLE>
		</FORM>
		</TD></TR></TABLE><BR>

<?php } ?>



<?php if ( $vid != '' and $_REQUEST['update'] != 1 ) { ?>

	<?php if ( $_REQUEST['action'] == 'search' ) { ?>

		<?php
		
		if ( $VendorProductCode != '' ) {
			$VendorProductCode_clause = " AND VendorProductCode LIKE '%" . escape_data($VendorProductCode) . "%'";
		} else {
			$VendorProductCode_clause = "";
		}
		if ( $ProductNumberInternal != '' ) {
			$ProductNumberInternal_clause = " AND vwmaterialpricing.ProductNumberInternal LIKE '%" . escape_data($ProductNumberInternal) . "%'";
		} else {
			$ProductNumberInternal_clause = "";
		}
		if ( $Designation != '' ) {
			$Designation_clause = " AND Designation LIKE '%" . escape_data($Designation) . "%'";
		} else {
			$Designation_clause = "";
		}

		//$sql = "SELECT * FROM vwmaterialpricing WHERE vendor_id = " . $vid . " ORDER BY DESIGNATION";

		$sql = "SELECT vwmaterialpricing.*, productprices.is_deleted FROM vwmaterialpricing
		LEFT JOIN productprices
		ON vwmaterialpricing.VendorID = productprices.VendorID AND vwmaterialpricing.ProductNumberInternal = productprices.ProductNumberInternal AND vwmaterialpricing.Tier = productprices.Tier
		WHERE ( is_deleted = 0 or is_deleted is null) AND vendor_id = " . $vid . $vendor_name_clause . $VendorProductCode_clause . $ProductNumberInternal_clause . $Designation_clause . " ORDER BY Designation";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//echo $sql;
		include("inc_materials_pricing.php");
	}

}

?>
<BR><BR>











<script type="text/javascript">
<!--
$(document).ready(function(){
	$(":input[readonly]").addClass("readOnly");
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");
});


function inactivate(aid, vid) {
	if ( confirm('Are you sure you want to delete this address?') ) {
		document.location.href = "vendors_vendors.edit.php?action=inact&aid=" + aid + "&vid=" + vid
	}
}

function inactivate_contact(contact_id, vid) {
	if ( confirm('Are you sure you want to delete this contact?') ) {
		document.location.href = "vendors_vendors.edit.php?action=inact_contact&contact_id=" + contact_id + "&vid=" + vid
	}
}


function delete_prod(vid, pni, vpc) {
	if ( confirm('Are you sure you want to delete this poduct?') ) {
		document.location.href = "vendors_vendors.edit.php?action=delete_prod&VendorID=" + vid + "&ProductNumberInternal=" + pni + "&VendorProductCode=" + vpc
	}
}

function delete_tier(vid, pni, tier) {
	if ( confirm('Are you sure you want to delete this pricing tier?') ) {
		document.location.href = "vendors_vendors.edit.php?action=delete_tier&VendorID=" + vid + "&ProductNumberInternal=" + pni + "&Tier=" + tier
	}
}

// -->
</script>



<?php include("inc_footer.php"); ?>