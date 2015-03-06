<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, SALES AND FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 2 and $rights != 4 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');



if ( $_GET['set'] ) {
	$sql = "UPDATE customer_contacts " .
	" SET customer_id = " . $_GET['set'] . 
	" WHERE contact_id = " . $_GET['cid'];
	mysql_query($sql, $link);   // or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	header("location: customers_customers.php?cid=" . $_GET['cid']);
	exit();
}



if ( $_GET['action'] == "inact" ) {
	$sql = "UPDATE customers SET active = 0 WHERE customer_id = " . $_GET['cid'];
	mysql_query($sql, $link);
}



include('inc_header.php');

?>



<table class="bounding">
<tr valign="top">
<td class="padded">

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
	<TR VALIGN=TOP><FORM METHOD="post" ACTION="customers_customers.php">
		<TD>

<?php if ( $_GET['action'] == "" ) { ?>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
		<TR>
			<TD>Choose the first initial of a customer:</TD>
		</TR>
	</TABLE><BR>

	<B><A HREF="customers_customers.php?alpha=a&cid=<?php echo $_GET['cid'];?>">A</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=b&cid=<?php echo $_GET['cid'];?>">B</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=c&cid=<?php echo $_GET['cid'];?>">C</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=d&cid=<?php echo $_GET['cid'];?>">D</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=e&cid=<?php echo $_GET['cid'];?>">E</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=f&cid=<?php echo $_GET['cid'];?>">F</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=g&cid=<?php echo $_GET['cid'];?>">G</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=h&cid=<?php echo $_GET['cid'];?>">H</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=i&cid=<?php echo $_GET['cid'];?>">I</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=j&cid=<?php echo $_GET['cid'];?>">J</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=k&cid=<?php echo $_GET['cid'];?>">K</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=l&cid=<?php echo $_GET['cid'];?>">L</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=m&cid=<?php echo $_GET['cid'];?>">M</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=n&cid=<?php echo $_GET['cid'];?>">N</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=o&cid=<?php echo $_GET['cid'];?>">O</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=p&cid=<?php echo $_GET['cid'];?>">P</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=q&cid=<?php echo $_GET['cid'];?>">Q</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=r&cid=<?php echo $_GET['cid'];?>">R</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=s&cid=<?php echo $_GET['cid'];?>">S</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=t&cid=<?php echo $_GET['cid'];?>">T</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=u&cid=<?php echo $_GET['cid'];?>">U</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=v&cid=<?php echo $_GET['cid'];?>">V</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=w&cid=<?php echo $_GET['cid'];?>">W</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=x&cid=<?php echo $_GET['cid'];?>">X</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=y&cid=<?php echo $_GET['cid'];?>">Y</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=z&cid=<?php echo $_GET['cid'];?>">Z</A></B> <SPAN STYLE="color:#999999">|</SPAN> 
	<B><A HREF="customers_customers.php?alpha=1&cid=<?php echo $_GET['cid'];?>">1-9</A></B><BR><BR>

	<?php
	if ( $_GET['alpha'] ) {
		?> 
		<DIV ALIGN=RIGHT><INPUT TYPE='button' VALUE="Cancel" onClick="JavaScript:history.go(-1)"></DIV>
		<?php
		$alpha = $_GET['alpha'];
		//$first_screen = false;
		if ( $_SESSION['userTypeCookie'] == 2 ) {
			$name_clause = " AND user_id = " . $_SESSION['user_id'];
		} else {
			$name_clause = "";
		}
		if ( $alpha == 1 ) {
			$alpha_clause = " (name LIKE '1%' OR name LIKE '2%' OR name LIKE '3%' OR name LIKE '4%' OR name LIKE '5%' OR name LIKE '6%' OR name LIKE '7%' OR name LIKE '8%' OR name LIKE '9%')";
		} else {
			$alpha_clause = " name LIKE '$alpha%' ";
		}
		$sql = "SELECT DISTINCT customer_id, name FROM customers LEFT JOIN customers_users USING(customer_id) WHERE " . $alpha_clause . $name_clause . " ORDER BY name";
		$result = mysql_query($sql, $link);

		if ( mysql_num_rows($result) > 0 ) {

			$bg = 0; ?>

			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">

				<TR VALIGN=TOP>
					<TD><B>Customer#</B></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><B>Customer</B></TD>
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
						<TD><?php echo $row['customer_id'] ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
						<TD><?php echo $row['name'] ?></TD>
						<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>

						<TD>
						<?php if ( $_GET['cid'] != "" ) { ?>
							<INPUT TYPE="button" VALUE="Choose" onClick="window.location='customers_customers.php?set=<?php echo $row['customer_id']?>&cid=<?php echo $_GET['cid'];?>'" STYLE="font-size:7pt">
						<?php } else { ?>
							<INPUT TYPE="button" VALUE="Edit" onClick="window.location='customers_customers.edit.php?cid=<?php echo $row['customer_id']?>'" STYLE="font-size:7pt">
						<?php } ?>
						</TD>

						<TD>
						
						</TD>
					</TR>

				<?php } ?>

			</TABLE>

		<?php } else {
			print("No customers in database under \"". strtoupper($alpha) . "\".");
		}
	}
} ?>

		</TD>
	</TR>
	<TR>
			<TD><BR>
				<button type="button" style="float:left" class="submit new" onClick="window.location='customers_customers.edit.php'">New Customer</button>
				<INPUT style="float:right" TYPE='button' class="submit" VALUE="Cancel" onClick="window.location='customers_customers.php'">
			</TD>
	</TR>
	</FORM>
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
		document.location.href = "customers_customers.php?action=inact&cid=" + cid
	}
}

 // End -->
</SCRIPT>


<?php include("inc_footer.php"); ?>