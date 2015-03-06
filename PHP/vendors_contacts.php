<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 5 and $rights != 6) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');


if ( $_GET['action'] == "inact" ) {
	$sql = "UPDATE vendor_contacts SET active = 0 WHERE contact_id = " . $_GET['cid'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
}


include("inc_header.php");

?>



<TABLE class="bounding"><TR valign="top"><TD class="padded">


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
	<TR VALIGN=TOP>
		<TD>

<?php if ( $_GET['action'] == "" or $_GET['action'] == "search" ) { ?>

	<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
		<TR VALIGN=TOP>
			<TD>

<TABLE class="bounding"><TR valign="top"><TD class="padded">
	<FORM ACTION="vendors_contacts.php" METHOD="post">
	<INPUT TYPE="hidden" NAME="action" VALUE="search">

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

		<TR>
			<TD><B>Vendor:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" ID="customer" NAME="customer" SIZE=30 VALUE="<?php echo stripslashes($_POST['customer']);?>"></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR>
			<TD><B>Contact's first name:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="first" SIZE=30 VALUE="<?php echo stripslashes($_POST['first']);?>"></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR>
			<TD><B>Contact's last name:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="last" SIZE=30 VALUE="<?php echo stripslashes($_POST['last']);?>"></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="12"></TD></TR>

		<TR>
			<TD colspan=3>
				<INPUT style="float:right" TYPE="submit" CLASS="submit_medium" VALUE="Search">
				<input type="button" style="margin:.5em .5em 0 0" class="submit new" value="New contact" onclick="window.location='vendors_contacts.edit.php?update=1'" />
				<INPUT TYPE="button" style="margin-top:.5em" CLASS="submit" VALUE="Reset" onClick="window.location='vendors_contacts.php'">
			</TD>
		</TR>
	</TABLE>

	</TD></TR></TABLE>
			</TD>
		</TR>
	</TABLE><BR><BR>



	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
		<TR>
			<TD>Choose the initial of a contact's last name:</TD>
		</TR>
	</TABLE><BR>

	<B><A HREF="vendors_contacts.php?alpha=a&choose=<?php echo $_GET['choose'];?>">A</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=b&choose=<?php echo $_GET['choose'];?>">B</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=c&choose=<?php echo $_GET['choose'];?>">C</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=d&choose=<?php echo $_GET['choose'];?>">D</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=e&choose=<?php echo $_GET['choose'];?>">E</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=f&choose=<?php echo $_GET['choose'];?>">F</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=g&choose=<?php echo $_GET['choose'];?>">G</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=h&choose=<?php echo $_GET['choose'];?>">H</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=i&choose=<?php echo $_GET['choose'];?>">I</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=j&choose=<?php echo $_GET['choose'];?>">J</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=k&choose=<?php echo $_GET['choose'];?>">K</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=l&choose=<?php echo $_GET['choose'];?>">L</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=m&choose=<?php echo $_GET['choose'];?>">M</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=n&choose=<?php echo $_GET['choose'];?>">N</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=o&choose=<?php echo $_GET['choose'];?>">O</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=p&choose=<?php echo $_GET['choose'];?>">P</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=q&choose=<?php echo $_GET['choose'];?>">Q</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=r&choose=<?php echo $_GET['choose'];?>">R</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=s&choose=<?php echo $_GET['choose'];?>">S</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=t&choose=<?php echo $_GET['choose'];?>">T</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=u&choose=<?php echo $_GET['choose'];?>">U</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=v&choose=<?php echo $_GET['choose'];?>">V</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=w&choose=<?php echo $_GET['choose'];?>">W</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=x&choose=<?php echo $_GET['choose'];?>">X</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=y&choose=<?php echo $_GET['choose'];?>">Y</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_contacts.php?alpha=z&choose=<?php echo $_GET['choose'];?>">Z</A></B><BR><BR>

	<?php if ( $_GET['alpha'] or $_POST['action'] == "search" ) { ?>

		<DIV ALIGN=RIGHT><INPUT TYPE='button' style="sbumit" VALUE="Cancel" onClick="JavaScript:history.go(-1)"></DIV>

		<?php
		$alpha = $_GET['alpha'];

		//if ( $_GET['choose'] == "" ) {
		//	$active_filter = "";
		//} else {
			$active_filter = " AND vendor_contacts.active = 1";
		//}

		if ( $_POST['first'] != '' or $_POST['last'] != '' or $_POST['customer'] != '' ) {

			if ( $_POST['customer'] == "" ) {
				$id_filter = " 1=1";
			} else {
				$id_filter = " (name LIKE '%" . escape_data($_POST['customer']) . "%')";
			}

			if ( $_POST['first'] == "" ) {
				$first_filter = "";
			} else {
				$first_filter = " AND (first_name LIKE '%" . escape_data($_POST['first']) . "%')";
			}

			if ( $_POST['last'] == "" ) {
				$last_filter = "";
			} else {
				$last_filter = " AND (last_name LIKE '%" . escape_data($_POST['last']) . "%')";
			}

			$sql = "SELECT DISTINCT contact_id, vendor_contacts.vendor_id, name, first_name, last_name, vendor_contacts.active FROM vendor_contacts JOIN vendors USING(vendor_id) WHERE " . $id_filter . $first_filter . $last_filter . " AND vendors.active=1 ORDER BY last_name";

		} else {
			$sql = "SELECT DISTINCT contact_id, vendor_contacts.vendor_id, name, first_name, last_name, vendor_contacts.active FROM vendor_contacts JOIN vendors USING(vendor_id) WHERE last_name LIKE '$alpha%' " . $active_filter . " AND vendors.active=1 ORDER BY last_name";
		}

		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( mysql_num_rows($result) > 0 ) {

			$bg = 0; ?>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">

				<TR VALIGN=TOP>
					<TD><B>Contact#</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><B>Name</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><B>Vendor</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><B>Active</B></TD>
					<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				</TR>

				<TR>
					<TD COLSPAN=9><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
				</TR>

				<?php 

				while ( $row = mysql_fetch_array($result) ) {

					if ( $bg == 1 ) {
						$bgcolor = "#F3E7FD";
						$bg = 0;
					}
					else {
						$bgcolor = "whitesmoke";
						$bg = 1;
					} ?>

					<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
						<TD><?php echo $row['contact_id'] ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
						<TD><NOBR><?php echo $row['last_name'] . ", " . $row['first_name'] ?></NOBR></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
						<TD><?php echo $row['name'] ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
						<TD ALIGN=CENTER><?php
						if ( $row['active'] == 1 ) {
							print("Yes");
						}
						else {
							print("No");
						}
						?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
						<TD>
						<?php if ( $_GET['choose'] == "1" ) { ?>
							<INPUT TYPE="button" style="submit" VALUE="Select" onClick="window.location='vendors_contacts.php?set=<?php echo $row['contact_id']?>&choose=<?php echo $_GET['choose'];?>'" STYLE="font-size:7pt">
						<?php } else { ?>
							<INPUT TYPE="button" style="submit" VALUE="Edit" onClick="window.location='vendors_contacts.edit.php?cid=<?php echo $row['contact_id']?>'" STYLE="font-size:7pt">
						<?php } ?>
						</TD>
					</TR>

				<?php } ?>

			</TABLE>

		<?php } else {
			print("No contacts found");
		}
	}
} ?>



		</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><BR><INPUT TYPE='button' class="submit"  VALUE="Cancel" onClick="JavaScript:history.go(-1)"></TD>
	</TR></FORM>
</TABLE>

		</TD>
	</TR>
</TABLE>

		</TD>
	</TR>
</TABLE>


</TD></TR></TABLE>
<BR><BR>


<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

function inactivate(cid) {
	if ( confirm('Are you sure you want to inactivate this contact?') ) {
		document.location.href = "vendors_contacts.php?action=inact&cid=" + cid
	}
}

 // End -->
</SCRIPT>


<?php include("inc_footer.php"); ?>