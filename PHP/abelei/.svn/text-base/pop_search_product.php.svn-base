<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

$screen = $_REQUEST['screen'];
$pne = $_REQUEST['pne'];


include('inc_global.php');

$VendorID = ( isset($_REQUEST['VendorID']) ? "$_REQUEST[VendorID]":"");
$parent_action = ( isset($_REQUEST['parent_action']) ? "$_REQUEST[parent_action]":"");
$pon = ( isset($_REQUEST['pon']) ? "$_REQUEST[pon]":"");
$posn = ( isset($_REQUEST['posn']) ? "$_REQUEST[posn]":"");
$VendorClause = "";

if ( $_REQUEST['pni'] != '' ) {
	if ( $screen == 'ff' ) {
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "window.opener.document.add_ingredient.IngredientProductNumber.value='" . $_REQUEST['pni'] . "'\n";
		echo "window.opener.document.add_ingredient.Ingredient.value='" . $_REQUEST['description'] . "'\n";
		echo "window.close();\n";
		echo "</SCRIPT>\n";
	} else {
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "window.opener.location='vendors_pos.php?action=$parent_action&pon=$pon&add_prod=1&PurchaseOrderSeqNumber=$posn&IngredientProductNumber=$_REQUEST[pni]'\n";
		echo "window.close();\n";
		echo "</SCRIPT>\n";
	}
}


if ( isset($_REQUEST['Designation']) and $_POST['action'] == 'search' ) {
	$Designation = $_REQUEST['Designation'];
}
if ( isset($_REQUEST['ProductNumberExternal']) and $_POST['action'] == 'search' ) {
	$ProductNumberExternal = $_REQUEST['ProductNumberExternal'];
}
if ( isset($_REQUEST['ProductNumberInternal']) and $_POST['action'] == 'search' ) {
	$ProductNumberInternal = $_REQUEST['ProductNumberInternal'];
}
if ( isset($_REQUEST['Keywords']) and $_POST['action'] == 'search' ) {
	$Keywords = $_REQUEST['Keywords'];
}



include("inc_pop_header.php"); ?>



<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<FORM ACTION="pop_search_product.php" METHOD="post">
<INPUT TYPE="hidden" NAME="action" VALUE="search">
<INPUT TYPE="hidden" NAME="parent_action" VALUE="<?php echo $parent_action;?>">
<INPUT TYPE="hidden" NAME="pon" VALUE="<?php echo $pon;?>">
<INPUT TYPE="hidden" NAME="posn" VALUE="<?php echo $posn;?>">
<INPUT TYPE="hidden" NAME="VendorID" VALUE="<?php echo $VendorID;?>">
<INPUT TYPE="hidden" NAME="screen" VALUE="<?php echo $screen;?>">
<INPUT TYPE="hidden" NAME="pne" VALUE="<?php echo $pne;?>">


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<TR>
		<TD><B>Material designation:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" NAME="Designation" VALUE="<?php echo $Designation;?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
	</TR>

	<TR>
		<TD><B>abelei number (external):</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>" SIZE="30" readonly='readonly'></TD>
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
		<TD><B>Keywords:</B></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
		<TD><INPUT TYPE="text" NAME="Keywords" VALUE="<?php echo $Keywords;?>" SIZE="30"></TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="12"></TD>
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

	if ( $Designation != '' ) {
		$Designation_clause = " AND Designation LIKE '%" . $Designation . "%'";
	} else {
		$Designation_clause = "";
	}
	// ADD BACK AFTER DATA IMPORT!!! //
	//if ( $ProductNumberExternal != '' ) {
	//	$ProductNumberExternal_clause = " AND ProductNumberExternal LIKE '%" . $ProductNumberExternal . "%'";
	//} else {
		$ProductNumberExternal_clause = "";
	//}
	// ADD BACK AFTER DATA IMPORT!!! //
	if ( $ProductNumberInternal != '' ) {
		$ProductNumberInternal_clause = " AND pm.ProductNumberInternal LIKE '%" . $ProductNumberInternal . "%'";
	} else {
		$ProductNumberInternal_clause = "";
	}
	if ( $Keywords != '' ) {
		$Keywords_clause = " AND Keywords LIKE '%" . $Keywords . "%'";
	} else {
		$Keywords_clause = "";
	}
if ("" != $VendorID) { 
	$VendorClause = "AND pp.VendorID = $VendorID";
}
	// ADD BACK AFTER DATA IMPORT!!! //
	//  INNER JOIN externalproductnumberreference USING (ProductNumberInternal)
	// ADD BACK AFTER DATA IMPORT!!! //
	// $sql = "SELECT DISTINCT pp.ProductNumberInternal as pni, pm.*, epnr.ProductNumberExternal FROM productprices AS pp, productmaster as pm LEFT JOIN externalproductnumberreference AS epnr ON ( epnr.ProductNumberInternal = pm.ProductNumberInternal ) WHERE pm.ProductNumberInternal = pp.ProductNumberInternal $VendorClause $Designation_clause $ProductNumberExternal_clause  $ProductNumberInternal_clause $Keywords_clause";
		$sql = "SELECT DISTINCT pm.ProductNumberInternal as pni, pm.*, epnr.ProductNumberExternal FROM productmaster as pm LEFT JOIN productprices AS pp ON pp.ProductNumberInternal = pm.ProductNumberInternal LEFT JOIN externalproductnumberreference AS epnr ON ( epnr.ProductNumberInternal = pm.ProductNumberInternal ) WHERE 1 $VendorClause $Designation_clause $ProductNumberExternal_clause  $ProductNumberInternal_clause $Keywords_clause";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	// echo $sql . "<BR>";

	if ( $c > 0 ) {

		$bg = 0; ?>

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" width="850">

			<TR VALIGN=BOTTOM>
				<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>abelei<BR>number<BR>(external)</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>abelei<BR>number<BR>(internal)</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><NOBR><B>Quick scan</B></NOBR></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Specific<BR>gravity</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Units</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Developer</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Description</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Appearance</B></TD>
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
				$designation = $row['Designation'];
				$description = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
			?>

				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD><FORM ACTION="pop_search_product.php" METHOD="post">
					<input type="hidden" name="pni" value="<?php echo $row[ProductNumberInternal] ?>"/>
					<input type="hidden" name="parent_action" value="<?php echo $parent_action ?>"/>
					<input type="hidden" name="pon" value="<?php echo $pon ?>"/>
					<input type="hidden" name="posn" value="<?php echo $posn ?>"/>
					<input type="hidden" name="description" value="<?php echo $description ?>"/>
					<input type="hidden" name="screen" value="<?php echo $screen ?>"/>
					<INPUT TYPE="hidden" NAME="pne" VALUE="<?php echo $pne;?>">
					<INPUT TYPE="submit" VALUE="Select" CLASS="submit" STYLE="font-size:7pt"></form></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['ProductNumberExternal'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['ProductNumberInternal'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['QuickScan'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo number_format($row['SpecificGravity'], 2) ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['SpecificGravityUnits'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php
					if ( $row['DeveloperID'] != '' ) {
						$sql = "SELECT first_name, last_name FROM users WHERE user_id = " . $row['DeveloperID'];
						$result_dev = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
						$row_dev = mysql_fetch_array($result_dev);
						echo "<NOBR>" . $row_dev['first_name'] . ' ' . $row_dev['last_name'] . "</NOBR>";
					}
					?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $description ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['Appearance'] ?></TD>
				</TR>

			<?php } ?>

		</TABLE></FORM>
<?php
		if ("" != $VendorID) { 
			echo "If you don't see the item you're looking for in the above results, it may not linked to this vendor yet. <a href=\"pop_add_product_vendor.php?VendorID=$VendorID\">Click here to add</a>.<br />";
		}
	} else {
		if ("" != $VendorID) { 
			echo "The item you're searching for is not currently on record as being sold by this vendor. <a href=\"pop_add_product_vendor.php?VendorID=$VendorID\">Click here to add</a>.<br />";
		} else {
		echo "No matches found in database<BR>";
		}
	}
}

?>



<?php include("inc_footer.php"); ?>