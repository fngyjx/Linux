<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');



if ( $_GET['set'] ) {
	$sql = "UPDATE vendor_contacts " .
	" SET vendor_id = " . $_GET['set'] . 
	" WHERE contact_id = " . $_GET['vid'];
	mysql_query($sql, $link);   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	header("location: vendors_vendors.php?vid=" . $_GET['vid']);
	exit();
}



if ( $_GET['action'] == "inact" ) {
	$sql = "UPDATE vendors SET active = 0 WHERE vendor_id = " . $_GET['vid'];
	mysql_query($sql, $link);
}



include('inc_header.php');

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
	<TR VALIGN=TOP><FORM METHOD="post" ACTION="vendors_vendors.php">
		<TD>

<?php if ( $_GET['action'] == "" ) { ?>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
		<TR>
			<TD>Choose the first initial of a vendor:</TD>
			<TD ALIGN=RIGHT><INPUT style="margin-top:.5em" name="new" id="new" TYPE="button" onclick="window.location='vendors_vendors.edit.php?vid=&update=1'" class="submit new" VALUE="New vendor"></TD>
		</TR>
	</TABLE><BR>

	<B><A HREF="vendors_vendors.php?alpha=a&vid=<?php echo $_GET['vid'];?>">A</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=b&vid=<?php echo $_GET['vid'];?>">B</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=c&vid=<?php echo $_GET['vid'];?>">C</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=d&vid=<?php echo $_GET['vid'];?>">D</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=e&vid=<?php echo $_GET['vid'];?>">E</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=f&vid=<?php echo $_GET['vid'];?>">F</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=g&vid=<?php echo $_GET['vid'];?>">G</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=h&vid=<?php echo $_GET['vid'];?>">H</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=i&vid=<?php echo $_GET['vid'];?>">I</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=j&vid=<?php echo $_GET['vid'];?>">J</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=k&vid=<?php echo $_GET['vid'];?>">K</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=l&vid=<?php echo $_GET['vid'];?>">L</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=m&vid=<?php echo $_GET['vid'];?>">M</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=n&vid=<?php echo $_GET['vid'];?>">N</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=o&vid=<?php echo $_GET['vid'];?>">O</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=p&vid=<?php echo $_GET['vid'];?>">P</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=q&vid=<?php echo $_GET['vid'];?>">Q</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=r&vid=<?php echo $_GET['vid'];?>">R</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=s&vid=<?php echo $_GET['vid'];?>">S</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=t&vid=<?php echo $_GET['vid'];?>">T</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=u&vid=<?php echo $_GET['vid'];?>">U</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=v&vid=<?php echo $_GET['vid'];?>">V</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=w&vid=<?php echo $_GET['vid'];?>">W</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=x&vid=<?php echo $_GET['vid'];?>">X</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=y&vid=<?php echo $_GET['vid'];?>">Y</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="vendors_vendors.php?alpha=z&vid=<?php echo $_GET['vid'];?>">Z</A></B><BR><BR>

	<?php
	if ( $_GET['alpha'] ) {
		?> 
		<DIV ALIGN=RIGHT><INPUT TYPE='button' VALUE="Cancel" onClick="JavaScript:history.go(-1)"></DIV>
		<?php
		$alpha = $_GET['alpha'];
		$sql = "SELECT DISTINCT vendor_id, name FROM vendors WHERE name LIKE '$alpha%' ORDER BY name";
		$result = mysql_query($sql, $link);

		if ( mysql_num_rows($result) > 0 ) {

			$bg = 0; ?>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">

				<TR VALIGN=TOP>
					<TD><B>Vendor#</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><B>Vendor</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				</TR>

				<TR>
					<TD COLSPAN=5><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
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
						<TD><?php echo $row['vendor_id'] ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
						<TD><?php echo $row['name'] ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>

						<TD>
						<?php if ( $_GET['vid'] != "" ) { ?>
							<INPUT TYPE="button" VALUE="Choose" onClick="window.location='vendors_vendors.php?set=<?php echo $row['vendor_id']?>&vid=<?php echo $_GET['vid'];?>'" STYLE="font-size:7pt">
						<?php } else { ?>
							<INPUT TYPE="button" VALUE="Edit" onClick="window.location='vendors_vendors.edit.php?vid=<?php echo $row['vendor_id']?>'" STYLE="font-size:7pt">
						<?php } ?>
						</TD>

						<TD>
						
						</TD>
					</TR>

				<?php } ?>

			</TABLE>

		<?php } else {
			print("No vendors in database under \"". strtoupper($alpha) . "\".");
		}
	}
} ?>

		</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><BR><INPUT TYPE='button' VALUE="Cancel" onClick="window.location='vendors_vendors.php'"></TD>
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

function inactivate(vid) {
	if ( confirm('Are you sure you want to inactivate this contact?') ) {
		document.location.href = "vendors_vendors.php?action=inact&vid=" + vid
	}
}

 // End -->
</SCRIPT>


<?php include("inc_footer.php"); ?>