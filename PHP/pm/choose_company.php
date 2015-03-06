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



if ( $_GET['set'] ) {
	$sql = "UPDATE clients " .
	" SET company_id = " . $_GET['set'] . 
	" WHERE client_id = " . $_GET['cid'];
	mysql_query($sql, $link);   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	header("location: clients.php?cid=" . $_GET['cid']);
	exit();
}



if ( $_GET['action'] == "inact" ) {
	$sql = "UPDATE companies SET active = 0 WHERE company_id = " . $_GET['cid'];
	mysql_query($sql, $link);
}



include('header.php');

?>



<B CLASS="header">Choose company</B>

<?php 

if ( $_GET['cid'] == "" ) {
	echo " / <B><A HREF='companies.php'>Add new company</A></B>";
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
	<TR VALIGN=TOP><FORM METHOD="post" ACTION="choose_company.php">
		<TD>

<?php if ( $_GET['action'] == "" ) { ?>

	Choose the first initial of a company:<BR><BR>

	<B><A HREF="choose_company.php?alpha=a&cid=<?php echo $_GET['cid'];?>">A</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=b&cid=<?php echo $_GET['cid'];?>">B</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=c&cid=<?php echo $_GET['cid'];?>">C</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=d&cid=<?php echo $_GET['cid'];?>">D</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=e&cid=<?php echo $_GET['cid'];?>">E</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=f&cid=<?php echo $_GET['cid'];?>">F</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=g&cid=<?php echo $_GET['cid'];?>">G</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=h&cid=<?php echo $_GET['cid'];?>">H</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=i&cid=<?php echo $_GET['cid'];?>">I</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=j&cid=<?php echo $_GET['cid'];?>">J</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=k&cid=<?php echo $_GET['cid'];?>">K</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=l&cid=<?php echo $_GET['cid'];?>">L</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=m&cid=<?php echo $_GET['cid'];?>">M</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=n&cid=<?php echo $_GET['cid'];?>">N</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=o&cid=<?php echo $_GET['cid'];?>">O</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=p&cid=<?php echo $_GET['cid'];?>">P</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=q&cid=<?php echo $_GET['cid'];?>">Q</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=r&cid=<?php echo $_GET['cid'];?>">R</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=s&cid=<?php echo $_GET['cid'];?>">S</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=t&cid=<?php echo $_GET['cid'];?>">T</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=u&cid=<?php echo $_GET['cid'];?>">U</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=v&cid=<?php echo $_GET['cid'];?>">V</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=w&cid=<?php echo $_GET['cid'];?>">W</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=x&cid=<?php echo $_GET['cid'];?>">X</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=y&cid=<?php echo $_GET['cid'];?>">Y</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="choose_company.php?alpha=z&cid=<?php echo $_GET['cid'];?>">Z</A></B><BR><BR>

	<?php
	if ( $_GET['alpha'] ) {
		?> 
		<DIV ALIGN=RIGHT><INPUT TYPE='button' VALUE="Cancel" onClick="JavaScript:history.go(-1)"></DIV>
		<?php
		$alpha = $_GET['alpha'];
		//$first_screen = false;
		if ( $_SESSION['userTypeCookie'] == 2 ) {
			$company_clause = " AND user_id = " . $_SESSION['user_id'];
		} else {
			$company_clause = "";
		}
		$sql = "SELECT DISTINCT company_id, company FROM companies LEFT JOIN companies_users USING(company_id) WHERE company LIKE '$alpha%' " . $company_clause . " ORDER BY company";
		$result = mysql_query($sql, $link);

		if ( mysql_num_rows($result) > 0 ) {

			$bg = 0; ?>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">

				<TR VALIGN=TOP>
					<TD><B>Company#</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><B>Company</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				</TR>

				<TR>
					<TD COLSPAN=5><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
				</TR>

				<?php 

				while ( $row = mysql_fetch_array($result) ) {

					if ( $bg == 1 ) {
						$bgcolor = "#FFFFFF";
						$bg = 0;
					}
					else {
						$bgcolor = "#EFEFEF";
						$bg = 1;
					} ?>

					<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
						<TD><?php echo $row['company_id'] ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
						<TD><?php echo $row['company'] ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>

						<TD>
						<?php if ( $_GET['cid'] != "" ) { ?>
							<INPUT TYPE="button" VALUE="Choose" onClick="window.location='choose_company.php?set=<?php echo $row['company_id']?>&cid=<?php echo $_GET['cid'];?>'" STYLE="font-size:7pt">
						<?php } else { ?>
							<INPUT TYPE="button" VALUE="Edit" onClick="window.location='companies.php?cid=<?php echo $row['company_id']?>'" STYLE="font-size:7pt">
						<?php } ?>
						</TD>

						<TD>
						
						</TD>
					</TR>

				<?php } ?>

			</TABLE>

		<?php } else {
			print("No companies in database under \"". strtoupper($alpha) . "\".");
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
		document.location.href = "choose_company.php?action=inact&cid=" + cid
	}
}

 // End -->
</SCRIPT>

<?php include('footer.php'); ?>