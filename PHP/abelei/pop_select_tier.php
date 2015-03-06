<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB AND QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}


include('inc_global.php');



if ( $_REQUEST['action'] == "select_for_po" ) {
	$sql = "UPDATE purchaseorderdetail SET " .
	"UnitPrice = '" . $_GET['price'] . "' " .
	"WHERE ProductNumberInternal = '" . $_GET['ipn'] . "' AND PurchaseOrderSeqNumber = '" . $_GET['seq'] . "' AND PurchaseOrderNumber = " . $_GET['pon'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//$_SESSION['note'] = "Information successfully saved<BR>";
	//echo $sql;
	//die();

	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
}



if ( $_REQUEST['action'] == "select_for_ff" ) {

	$sql = "SELECT * FROM productprices WHERE VendorID = " . $_GET['vid'] . " AND ProductNumberInternal = " . $_GET['ipn'] . " AND Tier = '" . $_GET['tier'] . "'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);

	$sql = "UPDATE formulationdetail SET " .
	" VendorID = '" . $row['VendorID'] . "', " .
	" Tier = '" . $row['Tier'] . "'" .
	" WHERE ProductNumberInternal = '" . $_GET['pni'] . "' AND IngredientSEQ = '" . $_GET['seq'] . "'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";

}



if ( $_REQUEST['action'] == "select" ) {

	$sql = "SELECT * FROM productprices WHERE VendorID = " . $_GET['vid'] . " AND ProductNumberInternal = " . $_GET['ipn'] . " AND Tier = '" . $_GET['tier'] . "'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);

	$sql = "UPDATE pricesheetdetail SET " .
	" Price = '" . $row['PricePerPound'] . "', " .
	" PriceEffectiveDate = '" . $row['PriceEffectiveDate'] . "', " .
	" VendorID = '" . $row['VendorID'] . "', " .
	" Tier = '" . $row['Tier'] . "'" .
	" WHERE PriceSheetNumber = " . $_GET['psn'] . " AND IngredientProductNumber = '" . $_GET['ipn'] . "' AND IngredientSEQ = '" . $_GET['seq'] . "'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";

}

include("inc_pop_header.php");

?>


<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
	<TR VALIGN=TOP>
		<TD>

		<?php

		$sql = "SELECT vwMaterialPricing.*, productprices.is_deleted, productmaster.Designation, productmaster.Natural_OR_Artificial, productmaster.ProductType, productmaster.Kosher
		FROM vwMaterialPricing
		LEFT JOIN productprices
		ON vwMaterialPricing.VendorID = productprices.VendorID AND vwMaterialPricing.ProductNumberInternal = productprices.ProductNumberInternal AND vwMaterialPricing.Tier = productprices.Tier
		LEFT JOIN productmaster ON productmaster.ProductNumberInternal = vwMaterialPricing.ProductNumberInternal
		WHERE is_deleted = 0 AND vwMaterialPricing.ProductNumberInternal = " . $_GET['ipn'] . ("" != $_GET['vid'] ? " AND vendor_id = " . $_GET['vid'] : "");
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//echo $sql;

		if ( mysql_num_rows($result) > 0 ) {
			$header_shown = "";
			$vendor_code_shown = "";
			$VendorID_shown = "";
			while ( $row = mysql_fetch_array($result) ) {

				$description = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");

				if ( ($VendorID_shown != $row['VendorID'] and $VendorID_shown != "")) { ?>
					</TABLE><BR>
					</TD></TR></TABLE>
				<?php }

				if ( $header_shown != $row['ProductNumberInternal'] ) { ?>
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" BGCOLOR="#976AC2" WIDTH="100%">
						<TR>
							<TD>
							<B CLASS="white">
							Internal Product#: <?php echo $row['ProductNumberInternal'];?>&nbsp;&nbsp;&nbsp;
							(<?php echo $description;?>)
							</B>
							</TD>
						</TR>
					</TABLE><BR>
				<?php }
				if ( $VendorID_shown != $row['VendorID'] or ( $VendorID_shown == $row['VendorID'] and $header_shown != $row['ProductNumberInternal'] ) ) { ?>
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%"><TR><TD>
					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BGCOLOR="#7AB829" WIDTH="100%">
						<TR>
							<TD><B CLASS="white">Vendor Product#: <?php echo $row['VendorProductCode'];?>
							&nbsp;&nbsp;&nbsp;
							Vendor: <?php echo $row['vendor_name'];?></B></TD>
						</TR>
					</TABLE>
					</TD></TR></TABLE>

					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%"><TR><TD>
					<TABLE BORDER=1 CELLSPACING="0" CELLPADDING="2" BORDERCOLOR="#CDCDCD" WIDTH="100%">

					<TR BGCOLOR="#FFFFCC">
						<TD>&nbsp;</TD>
						<TD>&nbsp;</TD>
						<TD ALIGN=CENTER WIDTH=30><IMG SRC="images/spacer_long" WIDTH=30 HEIGHT=1><BR><B CLASS="black">Tier</B></TD>
						<TD ALIGN=RIGHT WIDTH=65><IMG SRC="images/spacer_long" WIDTH=65 HEIGHT=1><BR><B CLASS="black">$ per lb</B></TD>
						<TD ALIGN=RIGHT WIDTH=90><IMG SRC="images/spacer_long" WIDTH=90 HEIGHT=1><BR><B CLASS="black">Effective</B></TD>
						<TD WIDTH=90><IMG SRC="images/spacer_long" WIDTH=90 HEIGHT=1><BR><B CLASS="black">Volume</B></TD>
						<TD ALIGN=CENTER WIDTH=60><IMG SRC="images/spacer_long" WIDTH=60 HEIGHT=1><BR><B CLASS="black">Mins</B></TD>
						<TD WIDTH=90><IMG SRC="images/spacer_long" WIDTH=90 HEIGHT=1><BR><B CLASS="black">Packaging</B></TD>
						<TD ALIGN=RIGHT WIDTH=90><IMG SRC="images/spacer_long" WIDTH=90 HEIGHT=1><BR><B CLASS="black">Quoted</B></TD>
						<TD WIDTH=270 VALIGN=TOP><B CLASS="black"><IMG SRC="images/spacer_long" WIDTH=270 HEIGHT=1><BR>Notes</B></TD>
					</TR>

				<?php } ?>

					<TR><FORM>
						<TD>
						<?php if ( isset($_GET['pon']) ) { ?>
							<INPUT TYPE="button" VALUE="Select" onClick="window.location='pop_select_tier.php?action=select_for_po&price=<?php echo $row['PricePerPound'];?>&ipn=<?php echo $_GET['ipn'];?>&seq=<?php echo $_GET['seq'];?>&pon=<?php echo $_GET['pon'];?>'" CLASS="submit">
						<?php } elseif ( isset($_GET['ff']) ) { ?>
							<INPUT TYPE="button" VALUE="Select" onClick="window.location='pop_select_tier.php?action=select_for_ff&vid=<?php echo $row['VendorID'];?>&tier=<?php echo $row['Tier'];?>&ipn=<?php echo $_GET['ipn'];?>&pni=<?php echo $_GET['pni'];?>&seq=<?php echo $_GET['seq'];?>&ppp=<?php echo $row['PricePerPound'];?>'" CLASS="submit">
						<?php } else { ?>
							<INPUT TYPE="button" VALUE="Select" onClick="window.location='pop_select_tier.php?action=select&vid=<?php echo $row['VendorID'];?>&tier=<?php echo $row['Tier'];?>&ipn=<?php echo $_GET['ipn'];?>&seq=<?php echo $_GET['seq'];?>&psn=<?php echo $_GET['psn'];?>'" CLASS="submit">
						<?php } ?>
						</TD>
						<TD><INPUT TYPE="button" VALUE="Edit Price Tier" onClick="popup('pop_add_price_tier.php?VendorID=<?php echo $row['VendorID'];?>&ProductNumberInternal=<?php echo $row['ProductNumberInternal'];?>&Tier=<?php echo $row['Tier'];?>', 920, 700, (screen.width  - width)/2, (screen.height - height)/2, 'popper')" CLASS="submit"></TD></FORM>
						<TD ALIGN=CENTER><?php echo $row['Tier'];?>&nbsp;</TD>
						<TD ALIGN=RIGHT><?php echo number_format($row['PricePerPound'], 2);?>&nbsp;</TD>
						<TD ALIGN=RIGHT><?php
						if ( $row['PriceEffectiveDate'] != '' ) {
							echo date("n/j/Y", strtotime($row['PriceEffectiveDate']));
						}
						?>&nbsp;</TD>
						<TD><?php echo $row['Volume'];?>&nbsp;</TD>
						<TD ALIGN=CENTER><?php echo $row['Minimums'];?>&nbsp;</TD>
						<TD><?php echo $row['Packaging'];?>&nbsp;</TD>
						<TD ALIGN=RIGHT><?php
						if ( $row['DateQuoted'] != '' ) {
							echo date("n/j/Y", strtotime($row['DateQuoted']));
						}
						?>&nbsp;</TD>
						<TD><?php echo $row['Notes'];?>&nbsp;</TD>
					</TR>

				<?php
				$header_shown = $row['ProductNumberInternal'];
				$VendorID_shown = $row['VendorID'];
				$vendor_code_shown = $row['VendorProductCode'];
			}
		} else {
			echo "No matches found";
		}

		echo "</TABLE><BR>";
		echo "</TD></TR></TABLE><BR>";

		?>

		</TD>
	</TR>
</TABLE><br/><br/>



<?php include("inc_footer.php"); ?>