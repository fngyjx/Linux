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

	if ( $_GET['set'] ) {
			$sql = "UPDATE projects " .
			" SET client_info_submitted = 1," .
			" client_id = " . $_GET['set'] . 
			" WHERE project_id = " . $_SESSION['pid'];
			mysql_query($sql, $link);
			header("location: client_info.php");
			exit();
	}
	
}



if ( $_GET['action'] == "inact" ) {
	$sql = "UPDATE clients SET active = 0 WHERE client_id = " . $_GET['cid'];
	mysql_query($sql, $link);
}



include('header.php');

?>



<B CLASS="header">Choose client</B>

<?php 

if ( $_GET['choose'] == "" ) {
	echo " / <B><A HREF='clients.php'>Add new client</A></B>";
}

?>

<BR><BR><BR>



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

		<TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP><FORM METHOD="post" ACTION="choose_client.php">
		<TD>

<?php if ( $_GET['action'] == "" ) { ?>

	Choose the initial of a client's last name:<BR><BR>

	<B><A HREF="choose_client.php?alpha=a&choose=<?php echo $_GET['choose'];?>">A</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=b&choose=<?php echo $_GET['choose'];?>">B</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=c&choose=<?php echo $_GET['choose'];?>">C</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=d&choose=<?php echo $_GET['choose'];?>">D</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=e&choose=<?php echo $_GET['choose'];?>">E</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=f&choose=<?php echo $_GET['choose'];?>">F</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=g&choose=<?php echo $_GET['choose'];?>">G</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=h&choose=<?php echo $_GET['choose'];?>">H</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=i&choose=<?php echo $_GET['choose'];?>">I</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=j&choose=<?php echo $_GET['choose'];?>">J</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=k&choose=<?php echo $_GET['choose'];?>">K</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=l&choose=<?php echo $_GET['choose'];?>">L</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=m&choose=<?php echo $_GET['choose'];?>">M</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=n&choose=<?php echo $_GET['choose'];?>">N</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=o&choose=<?php echo $_GET['choose'];?>">O</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=p&choose=<?php echo $_GET['choose'];?>">P</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=q&choose=<?php echo $_GET['choose'];?>">Q</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=r&choose=<?php echo $_GET['choose'];?>">R</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=s&choose=<?php echo $_GET['choose'];?>">S</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=t&choose=<?php echo $_GET['choose'];?>">T</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=u&choose=<?php echo $_GET['choose'];?>">U</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=v&choose=<?php echo $_GET['choose'];?>">V</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=w&choose=<?php echo $_GET['choose'];?>">W</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=x&choose=<?php echo $_GET['choose'];?>">X</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=y&choose=<?php echo $_GET['choose'];?>">Y</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_client.php?alpha=z&choose=<?php echo $_GET['choose'];?>">Z</A></B><BR><BR>

	<?php
	if ( $_GET['alpha'] ) {
		?> 
		<DIV ALIGN=RIGHT><INPUT TYPE='button' VALUE="Cancel" onClick="JavaScript:history.go(-1)"></DIV>
		<?php
		$alpha = $_GET['alpha'];

		if ( $_GET['choose'] == "" ) {
			$active_filter = "";
		} else {
			$active_filter = " AND clients.active = 1";
		}

		if ( $_SESSION['userTypeCookie'] == 2 ) {
			$sales_filter = " AND clients_users.user_id = " . $_SESSION['user_id'];
		} else {
			$sales_filter = "";
		}

		$sql = "SELECT DISTINCT client_id, clients.company_id, company, first_name, last_name, clients.active FROM clients LEFT JOIN companies USING(company_id) LEFT JOIN clients_users USING(client_id) WHERE last_name LIKE '$alpha%' " . $active_filter . $sales_filter . " ORDER BY last_name";
		$result = mysql_query($sql, $link);

		if ( mysql_num_rows($result) > 0 ) {

			$bg = 0; ?>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">

				<TR VALIGN=TOP>
					<TD><B>Client#</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><B>Name</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><B>Company</B></TD>
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
						$bgcolor = "#FFFFFF";
						$bg = 0;
					}
					else {
						$bgcolor = "#DFDFDF";
						$bg = 1;
					} ?>

					<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
						<TD><?php echo $row['client_id'] ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
						<TD><NOBR><?php echo $row['last_name'] . ", " . $row['first_name'] ?></NOBR></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
						<TD><?php echo $row['company'] ?></TD>
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
							<INPUT TYPE="button" VALUE="Select" onClick="window.location='choose_client.php?set=<?php echo $row['client_id']?>&choose=<?php echo $_GET['choose'];?>'" STYLE="font-size:7pt">
						<?php } else { ?>
							<INPUT TYPE="button" VALUE="Edit" onClick="window.location='clients.php?cid=<?php echo $row['client_id']?>'" STYLE="font-size:7pt">
						<?php } ?>
						</TD>
					</TR>

				<?php } ?>

			</TABLE>

		<?php } else {
			print("No clients in database under \"". strtoupper($alpha) . "\".");
		}
	}
} ?>



		</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><BR><INPUT TYPE='button' VALUE="Cancel" onClick="JavaScript:history.go(-1)"></TD>
	</TR></FORM>
</TABLE>

		</TD>
	</TR>
</TABLE>

		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE>



<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

function inactivate(cid) {
	if ( confirm('Are you sure you want to inactivate this client?') ) {
		document.location.href = "choose_client.php?action=inact&cid=" + cid
	}
}

 // End -->
</SCRIPT>

<?php include('footer.php'); ?>