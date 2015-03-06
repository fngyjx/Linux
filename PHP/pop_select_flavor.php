<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN AND FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 4 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');



if ( $_REQUEST['pni'] != '' ) {
	$sql="SELECT PackSize,UnitOfMeasure FROM productpacksize WHERE ProductNumberInternal='".escape_data($_REQUEST['pni'])."' AND DefaultPksz=1";
	$result=mysql_query($sql,$link) or die ( mysql_error() ." Failed Execute SQL : $sql <br />");
	$row=mysql_fetch_array($result);
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.document.add_prod.ProductNumberExternal.value='" . $_REQUEST['pne'] . "'\n";
	echo "window.opener.document.add_prod.PackSize.value='" . $row['PackSize'] . "'\n";
	echo "window.opener.document.add_prod.UnitOfMeasure.value='" . $row['UnitOfMeasure'] . "'\n";
	echo "window.opener.document.add_prod.ProductNumberInternal.value='" . $_REQUEST['pni'] . "'\n";
	echo "window.opener.document.add_prod.LbsPerPail.value='" . $_REQUEST['ppp'] . "'\n";
	echo "window.opener.document.add_prod.LbsPerDrum.value='" . $_REQUEST['ppd'] . "'\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
}



if ( isset($_REQUEST['ProductNumberExternal']) and $_POST['action'] == 'search' ) {
	$tmpArr = explode("&", $_REQUEST['ProductNumberExternal']);
	$ProductNumberExternal = $tmpArr[0];
}



include("inc_pop_header.php"); ?>



<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<FORM ACTION="pop_select_flavor.php" METHOD="post">
<INPUT TYPE="hidden" NAME="action" VALUE="search">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD><B>abelei number (external):</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" id="external_number_search" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>

	<TR>
		<TD></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD>

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<TR>
				<TD><INPUT TYPE="submit" VALUE="Search >" CLASS="submit"></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
				<TD><INPUT TYPE="button" VALUE="Cancel" CLASS="button_pop" onClick="window.close()"></TD>
			</TR>
		</TABLE>
		</FORM>

		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>



<?php

if ( $_POST['action'] == 'search' ) {

	if ( $ProductNumberExternal != '' ) {
		$ProductNumberExternal_clause = " AND ProductNumberExternal LIKE '%" . $ProductNumberExternal . "%'";
	} else {
		$ProductNumberExternal_clause = "";
	}

	$sql = "SELECT ProductNumberExternal, productmaster.ProductNumberInternal, Designation, SpecificGravity FROM productmaster INNER JOIN externalproductnumberreference USING (ProductNumberInternal) WHERE 1=1 " . $ProductNumberExternal_clause;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	//echo $sql . "<BR>";

	if ( $c > 0 ) {

		$bg = 0; ?>

		<FORM><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">

			<TR VALIGN=BOTTOM>
				<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>abelei<BR>number<BR>(external)</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>abelei<BR>number<BR>(internal)</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Description</B></TD>
			</TR>

			<TR>
				<TD COLSPAN=9><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
			</TR>

			<?php 

			while ( $row = mysql_fetch_array($result) ) {
				if ( $bg == 1 ) {
					$bgcolor = "#F3E7FD";
					$bg = 0;
				} else {
					$bgcolor = "whitesmoke";
					$bg = 1;
				} 
				$lbspergal = $row[SpecificGravity]*8.34;
				$lbsperpail = $lbspergal * 5;
				$lbsperdrum = $lbspergal * 55;
				?>

				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD><INPUT TYPE="button" VALUE="Select" CLASS="submit" onClick="window.location='pop_select_flavor.php?pni=<?php echo $row['ProductNumberInternal']?>&pne=<?php echo $row['ProductNumberExternal']?>&des=<?php echo addslashes($row['Designation'])?>&ppp=<?php echo $lbsperpail?>&ppd=<?php echo $lbsperdrum?>'" STYLE="font-size:7pt"></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['ProductNumberExternal'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['ProductNumberInternal'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['Designation'] ?></TD>
				</TR>

			<?php } ?>

		</TABLE></FORM>

	<?php } else {
		echo "No matches found in database<BR>";
	}
}

?>



<script LANGUAGE=JAVASCRIPT>
 <!-- Hide
$(document).ready(function(){

	$("#external_number_search").autocomplete("search/product_master_formulas_by_external_number.php", {
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 650,
		max:1000,
		multipleSeparator: "¬",
		scrollheight: 350
	});
		$("#external_number_search").result(function(event, data, formatted) {
		if (data)
			$("#designation_search").val('');
			$("#internal_number_search").val('');
			$("#keyword_search").val('');
			$("#action").val('search');
			document.search.submit();
	});
	
});

 // End -->
 
</script>



<?php include("inc_footer.php"); ?>