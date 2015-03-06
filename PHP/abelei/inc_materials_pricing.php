<?php

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

	//echo $sql;
	//echo $base_page[0];

	$base_page = explode(".", basename($_SERVER['PHP_SELF']));

	if ( $base_page[0] == "customers_quotes" ) {
		$tdcolor = "whitesmoke";
		$width1 = 1;
		$width2 = 20;
	} else {
		$tdcolor = "#FFFFFF";
		$width1 = 1;
		$width2 = 20;
	}

	if ( mysql_num_rows($result) > 0 ) {

		while ( $row = mysql_fetch_array($result) ) {

			if ( ($header_shown != $row['ProductNumberInternal'] and $outer_loop_write != 1 and $c != 0) or ($VendorID_shown != '' and $VendorID_shown != $row['VendorID']) ) {
				echo "</TABLE>";
				echo "</TD></TR></TABLE><BR>";
			}

		 	$c = 1;

			if ( $header_shown != $row['ProductNumberInternal'] ) { ?>
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" WIDTH="100%" BGCOLOR="<?php echo $tdcolor;?>">
					<TR>
						<TD WIDTH="<?php echo $width1;?>" BGCOLOR="<?php echo $tdcolor;?>"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="<?php echo $width1;?>" HEIGHT="1"></TD>
						<TD BGCOLOR="#976AC2"><B CLASS="white">Internal Product#: <?php echo $row['ProductNumberInternal'];?>
						&nbsp;&nbsp;&nbsp;
						Designation: <?php echo $row['Designation'];?>
						&nbsp;&nbsp;&nbsp;
						Nat/Art: <?php echo $row['Natural_OR_Artificial'];?>
						&nbsp;&nbsp;&nbsp;
						Kosher: <?php echo $row['Kosher'];?></B></TD>
						<TD WIDTH="<?php echo $width1;?>" BGCOLOR="<?php echo $tdcolor;?>"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="<?php echo $width1;?>" HEIGHT="1"></TD>
					</TR>
				</TABLE><BR>
			<?php 
			}

			if ( $VendorID_shown != $row['VendorID'] or ( $VendorID_shown == $row['VendorID'] and $header_shown != $row['ProductNumberInternal'] ) or ($base_page[0] != "customers_quotes" and $header_shown != $row['ProductNumberInternal']) ) { ?>
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%"><TR>
				<TD WIDTH="<?php echo $width2;?>" BGCOLOR="<?php echo $tdcolor;?>"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="<?php echo $width2;?>" HEIGHT="1"></TD><TD>
				<?php
				// ALLOW VENDOR TO BE ADDED FROM PRICE QUOTE PAGE
				if ( $base_page[0] == "flavors_quotes" and $header_shown != $row['ProductNumberInternal'] ) { ?>
					<FORM><INPUT TYPE="button" VALUE="Add vendor" onClick="popup('pop_add_product_vendor.php?pni=<?php echo $row['ProductNumberInternal'];?>')" CLASS="submit"></FORM>
				<?php } ?>

					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BGCOLOR="#7AB829" WIDTH="100%">
						<TR>
							<TD><B CLASS="white">Vendor Product#: <?php echo $row['VendorProductCode'];?>
							&nbsp;&nbsp;&nbsp;
							Vendor: <A HREF="vendors_vendors.edit.php?vid=<?php echo $row['vendor_id'];?>"><?php echo $row['vendor_name'];?></A></B></TD>
							<TD ALIGN=RIGHT>
							<INPUT TYPE="button" VALUE="Add price tier" onClick="popup('pop_add_price_tier.php?add_tier=1&VendorID=<?php echo $row['VendorID'];?>&ProductNumberInternal=<?php echo $row['ProductNumberInternal'];?>')" CLASS="submit" STYLE="color:#330066;background-color: #DCDCDC">
							<INPUT TYPE="button" VALUE="Edit" onClick="popup('pop_edit_material_vendor.php?add_prod=0&VendorID=<?php echo $row['VendorID'];?>&ProductNumberInternal=<?php echo $row['ProductNumberInternal'];?>&VendorProductCode=<?php echo $row['VendorProductCode'];?>')" CLASS="submit" STYLE="color:#330066;background-color: #DCDCDC">
							<INPUT TYPE="button" VALUE="Delete" onClick="delete_prod('<?php echo $row['VendorID'];?>', '<?php echo $row['ProductNumberInternal'];?>', '<?php echo $row['VendorProductCode'];?>')" CLASS="submit" STYLE="color:#330066;background-color: #DCDCDC">
							 </TD>
						</TR>
					</TABLE>

				</TD></TR></TABLE>

				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="92%"><TR>
				<TD WIDTH="<?php echo $width2;?>" BGCOLOR="<?php echo $tdcolor;?>"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="<?php echo $width2;?>" HEIGHT="1"></TD><TD>
				<TABLE BORDER=1 CELLSPACING="0" CELLPADDING="2" BORDERCOLOR="#CDCDCD" WIDTH="94%">

					<TR BGCOLOR="#FFFFCC">
						<TD COLSPAN=2>&nbsp;</TD>
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

					<?php if ( $row['is_deleted'] != 1 ) {
						$font_style = "";
					} else {
						$font_style = "color:#999999;font-style:italic";
					} ?>

				<TR BGCOLOR="#FFFFFF">
					<?php if ( $row['is_deleted'] != 1 ) { ?>
						<TD WIDTH="16"><A HREF="javascript:void(0)" onClick="popup('pop_add_price_tier.php?VendorID=<?php echo $row['VendorID'];?>&ProductNumberInternal=<?php echo $row['ProductNumberInternal'];?>&Tier=<?php echo $row['Tier'];?>')" STYLE="font-size:8pt"><IMG SRC="images/pencil.gif" WIDTH="16" HEIGHT="16" BORDER=0></A></TD>
					<?php } else { ?>
						<TD WIDTH="16"><IMG SRC="images/spacer.gif" WIDTH="16" HEIGHT="16" BORDER=0></TD>
					<?php } ?>

					<?php if ( $base_page[0] != "customers_quotes" ) { ?>

						<?php if ( $row['is_deleted'] != 1 ) { ?>
							<TD WIDTH="16"><A HREF="JavaScript:delete_tier('<?php echo $row['VendorID'];?>', '<?php echo $row['ProductNumberInternal'];?>', '<?php echo $row['Tier'];?>')"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A></TD>
						<?php } else { ?>
							<TD WIDTH="16"><IMG SRC="images/spacer.gif" WIDTH="16" HEIGHT="16" BORDER=0></TD>
						<?php } ?>

					<?php } else { ?>

						<?php if ( $row['is_deleted'] != 1 ) { ?>
							<TD WIDTH="16"><A HREF="JavaScript:delete_tier('<?php echo $row['VendorID'];?>', '<?php echo $row['ProductNumberInternal'];?>', '<?php echo $row['Tier'];?>', '<?php echo $_REQUEST['psn'];?>')"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A></TD>
						<?php } else { ?>
							<TD WIDTH="16"><IMG SRC="images/spacer.gif" WIDTH="16" HEIGHT="16" BORDER=0></TD>
						<?php } ?>

					<?php } ?>

					<TD ALIGN=CENTER><SPAN STYLE="<?php echo $font_style;?>"><?php echo $row['Tier'];?>&nbsp;</SPAN></TD>
					<TD ALIGN=RIGHT><SPAN STYLE="<?php echo $font_style;?>"><?php echo number_format($row['PricePerPound'], 2);?>&nbsp;</SPAN></TD>
					<TD ALIGN=RIGHT><SPAN STYLE="<?php echo $font_style;?>"><?php
					if ( $row['PriceEffectiveDate'] != '' ) {
						echo date("n/j/Y", strtotime($row['PriceEffectiveDate']));
					}
					?>&nbsp;</SPAN></TD>
					<TD><SPAN STYLE="<?php echo $font_style;?>"><?php echo $row['Volume'];?>&nbsp;</SPAN></TD>
					<TD ALIGN=CENTER><?php echo $row['Minimums'];?>&nbsp;</TD>
					<TD><SPAN STYLE="<?php echo $font_style;?>"><?php echo $row['Packaging'];?>&nbsp;</SPAN></TD>
					<TD ALIGN=RIGHT><SPAN STYLE="<?php echo $font_style;?>"><?php
					if ( $row['DateQuoted'] != '' ) {
						echo date("n/j/Y", strtotime($row['DateQuoted']));
					}
					?>&nbsp;</TD>
					<TD><SPAN STYLE="<?php echo $font_style;?>"><?php echo $row['Notes'];?>&nbsp;</SPAN></TD>
				</TR>

			<?php
			$header_shown = $row['ProductNumberInternal'];
			$VendorID_shown = $row['VendorID'];
			$outer_loop_write = 0;
		}
	} else {
		echo "No matches found";
	}

?>