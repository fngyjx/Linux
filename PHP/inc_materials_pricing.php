<?php

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 and $rights != 6 ) {
	header ("Location: login.php?out=1");
	exit;
}

	//echo $sql;
	//echo $base_page[0];

	$base_page = explode(".", basename($_SERVER['PHP_SELF']));
	//print_r($base_page); //e.g. customers_quotes.rmc_management.php [0]=>customers_quotes, [1]=>rmc_management,[2]=>php

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

		 	

			if ( $header_shown != $row['ProductNumberInternal'] ) {  ?>
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" WIDTH="100%" BGCOLOR="<?php echo $tdcolor;?>">
					<TR>
						<TD WIDTH="<?php echo $width1;?>" BGCOLOR="<?php echo $tdcolor;?>"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="<?php echo $width1;?>" HEIGHT="1"></TD>
						<TD><B>Internal Product#:</B> <?php echo $row['ProductNumberInternal'];?></TD>
						<TD><B>Designation:</B> <?php echo $row['Designation'];?></TD>
						<TD><B>Nat/Art:</B> <?php echo $row['Natural_OR_Artificial'];?></TD>
						<TD><B>Kosher:</B> <?php echo $row['Kosher'];?></TD>
						<TD WIDTH="<?php echo $width1;?>" BGCOLOR="<?php echo $tdcolor;?>"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="<?php echo $width1;?>" HEIGHT="1"></TD>
					</TR>
				</TABLE>
			<?php 
				
			}

			if ( $VendorID_shown != $row['VendorID'] or ( $VendorID_shown == $row['VendorID'] and $header_shown != $row['ProductNumberInternal'] )
				or $header_shown != $row['ProductNumberInternal'] ) { 
				$c = 1;
			?>
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%"><TR>
				<TD WIDTH="<?php echo $width2;?>" BGCOLOR="<?php echo $tdcolor;?>"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="<?php echo $width2;?>" HEIGHT="1"></TD><TD>
				<?php
				// ALLOW VENDOR TO BE ADDED FROM PRICE QUOTE PAGE
				if ( $base_page[0] == "flavors_quotes" and $header_shown != $row['ProductNumberInternal'] ) { ?>
					<INPUT TYPE="button" VALUE="Add vendor" onClick="popup('pop_add_product_vendor.php?pni=<?php echo $row['ProductNumberInternal'];?>')" CLASS="submit">
				<?php } ?>

					<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BACKGROUND="images/backer.gif" WIDTH="100%">
						<TR>
							<TD><B CLASS="white">Vendor Product#: <?php echo $row['VendorProductCode'];?>
							&nbsp;&nbsp;&nbsp;
							Vendor: <A HREF="vendors_vendors.edit.php?vid=<?php echo $row['vendor_id'];?>"><?php echo $row['vendor_name'];?></A></B></TD>
							<TD ALIGN=RIGHT>
							<INPUT TYPE="button" VALUE="Add price tier" onClick="popup('pop_add_price_tier.php?add_tier=1&VendorID=<?php echo $row['VendorID'];?>&ProductNumberInternal=<?php echo $row['ProductNumberInternal'];?>')" CLASS="submit" STYLE="color:#330066;background-color: #DCDCDC">
							<INPUT TYPE="button" VALUE="Edit" onClick="popup('pop_edit_material_vendor.php?add_prod=0&VendorID=<?php echo $row['VendorID'];?>&ProductNumberInternal=<?php echo $row['ProductNumberInternal'];?>&VendorProductCode=<?php echo base64_encode($row['VendorProductCode']);?>')" CLASS="submit" STYLE="color:#330066;background-color: #DCDCDC">
							<INPUT TYPE="button" VALUE="Delete" onClick="delete_prod('<?php echo $row['VendorID'];?>', '<?php echo $row['ProductNumberInternal'];?>', '<?php echo $row['VendorProductCode'];?>')" CLASS="submit" STYLE="color:#330066;background-color: #DCDCDC">
							 </TD>
						</TR>
					</TABLE>

				</TD></TR></TABLE>

				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%"><TR>
				<TD WIDTH="<?php echo $width2;?>" BGCOLOR="<?php echo $tdcolor;?>"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="<?php echo $width2;?>" HEIGHT="1"></TD><TD>
				<TABLE BORDER=1 CELLSPACING="0" CELLPADDING="2" BORDERCOLOR="#CDCDCD" WIDTH="100%">

					<TR BGCOLOR="#FFFFCC">
						<TD COLSPAN=2>&nbsp;</TD>
						<TD ALIGN=CENTER WIDTH=30><IMG SRC="images/spacer_long" WIDTH=30 HEIGHT=1><BR><B CLASS="black">Tier</B></TD>
						<TD ALIGN=RIGHT WIDTH=60><IMG SRC="images/spacer_long" WIDTH=60 HEIGHT=1><BR><B CLASS="black">$ per lb</B></TD>
						<TD ALIGN=RIGHT WIDTH=70><IMG SRC="images/spacer_long" WIDTH=70 HEIGHT=1><BR><B CLASS="black">Effective</B></TD>
						<TD WIDTH=60><IMG SRC="images/spacer_long" WIDTH=60 HEIGHT=1><BR><B CLASS="black">Volume</B></TD>
						<TD ALIGN=CENTER WIDTH=60><IMG SRC="images/spacer_long" WIDTH=60 HEIGHT=1><BR><B CLASS="black">Mins</B></TD>
						<TD WIDTH=60><IMG SRC="images/spacer_long" WIDTH=60 HEIGHT=1><BR><B CLASS="black">Packaging</B></TD>
						<TD ALIGN=RIGHT WIDTH=70><IMG SRC="images/spacer_long" WIDTH=70 HEIGHT=1><BR><B CLASS="black">Quoted</B></TD>
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
						<TD WIDTH="12"><A HREF="javascript:void(0)" onClick="popup('pop_add_price_tier.php?VendorID=<?php echo $row['VendorID'];?>&ProductNumberInternal=<?php echo $row['ProductNumberInternal'];?>&Tier=<?php echo $row['Tier'];?>')" STYLE="font-size:8pt"><IMG SRC="images/pencil.gif" WIDTH="12" HEIGHT="12" BORDER=0></A></TD>
					<?php } else { ?>
						<TD WIDTH="12"><IMG SRC="images/spacer.gif" WIDTH="14" HEIGHT="14" BORDER=0></TD>
					<?php } ?>

					<?php if ( $base_page[0] != "customers_quotes" ) { ?>

						<?php if ( $row['is_deleted'] != 1 ) { ?>
							<TD WIDTH="12"><A HREF="JavaScript:delete_tier('<?php echo $row['VendorID'];?>', '<?php echo $row['ProductNumberInternal'];?>', '<?php echo $row['Tier'];?>')"><IMG SRC="images/delete.gif" WIDTH="12" HEIGHT="12" BORDER="0"></A></TD>
						<?php } else { ?>
							<TD WIDTH="12"><IMG SRC="images/spacer.gif" WIDTH="14" HEIGHT="14" BORDER=0></TD>
						<?php } ?>

					<?php } else { ?>

						<?php if ( $row['is_deleted'] != 1 ) { ?>
							<TD WIDTH="12"><A HREF="JavaScript:delete_tier('<?php echo $row['VendorID'];?>', '<?php echo $row['ProductNumberInternal'];?>', '<?php echo $row['Tier'];?>', '<?php echo $_REQUEST['psn'];?>')"><IMG SRC="images/delete.gif" WIDTH="12" HEIGHT="12" BORDER="0"></A></TD>
						<?php } else { ?>
							<TD WIDTH="12"><IMG SRC="images/spacer.gif" WIDTH="12" HEIGHT="12" BORDER=0></TD>
						<?php } ?>

					<?php } ?>

					<TD ALIGN=CENTER><SPAN STYLE="<?php echo $font_style;?>"><?php echo $row['Tier'];?>&nbsp;</SPAN></TD>
					<TD ALIGN=LEFT><SPAN STYLE="<?php echo $font_style;?>" id="price_<?php echo $pitem;?>" 
						onClick="change_price('<?php echo $pitem;?>','<?php echo $row['ProductNumberInternal'];?>','<?php echo $row[VendorID];?>','<?php echo $row['Tier'];?>','<?php echo $row['PricePerPound'];?>')">
						<?php echo number_format($row['PricePerPound'], 2);?>&nbsp;</SPAN>
						<INPUT TYPE="hidden" id="update_price_<?php echo $pitem;?>" NAME="update_price_<?php echo $pitem;?>" VALUE="">
						<INPUT TYPE="TEXT" id="price_perpound_<?php echo $pitem;?>" NAME="price_perpound_<?php echo $pitem;?>" VALUE="<?php echo number_format($row['PricePerPound'], 2);?>" STYLE="visibility:hidden"></TD>
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
			$pitem++;
		}
		echo "</TABLE></TD></TR></TABLE><BR>";
	} else {
		echo "No matches found";
	}

?>