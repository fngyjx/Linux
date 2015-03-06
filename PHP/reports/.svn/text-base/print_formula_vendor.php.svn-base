<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

//if ( $_SESSION['weight'] != '' and is_numeric($_SESSION['weight']) ) {
//	$weight = $_SESSION['weight'];
//} else {
//	$weight = 1;
//}

include('../inc_global.php');

$sql = "SELECT externalproductnumberreference.ProductNumberExternal
FROM externalproductnumberreference 
WHERE ProductNumberInternal = " . $_GET['pni'];
$result_ext = mysql_query($sql, $link) or die (mysql_error());
$row_ext = mysql_fetch_array($result_ext);
$external_number = $row_ext['ProductNumberExternal'];
$internal_number = escape_data($_GET['pni']);


$sql = "SELECT * FROM productmaster WHERE ProductNumberInternal = '$internal_number'";
$result_des = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row_des = mysql_fetch_array($result_des);
$ProductDesignation = (("" != $row_des[Natural_OR_Artificial]) ? $row_des[Natural_OR_Artificial]." " : "")."$row_des[Designation]".(("" != $row_des[ProductType]) ? " - ".$row_des[ProductType] : "").(("" != $row_des[Kosher]) ? " - ".$row_des[Kosher] : "");
$notes = $row_des[NoteForFormulation];


//$sql = "SELECT formulationdetail.* , pm.* , ( SELECT MIN( PricePerPound ) 
//	FROM productprices LEFT JOIN vendors ON ( productprices.VendorID=vendors.vendor_id) 
//	WHERE productprices.ProductNumberInternal = pm.ProductNumberInternal
//	AND Volume <= " . $weight . " ) AS EstimatedPricePerPound,
//	( SELECT vendors.name FROM productprices, vendors WHERE productprices.ProductNumberInternal = pm.ProductNumberInternal AND productprices.VendorID=vendors.vendor_id AND Volume <= 1000 AND PricePerPound = EstimatedPricePerPound ) AS vendor, externalproductnumberreference.ProductNumberExternal
//	FROM formulationdetail
//	LEFT JOIN productmaster pm ON formulationdetail.IngredientProductNumber = pm.ProductNumberInternal
//	LEFT JOIN externalproductnumberreference ON formulationdetail.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
//	WHERE formulationdetail.ProductNumberInternal = '" . $_GET['pni'] . "'";


$sql = "SELECT formulationdetail.*, pm.*, 
	(
	SELECT MIN(PricePerPound ) 
	FROM productprices
	WHERE productprices.ProductNumberInternal = pm.ProductNumberInternal LIMIT 1
	) AS LeastEstimatedPricePerPound,
	(
	SELECT vendors.name
	FROM productprices, vendors
	WHERE productprices.ProductNumberInternal = pm.ProductNumberInternal
	AND productprices.VendorID = vendors.vendor_id
	AND PricePerPound = LeastEstimatedPricePerPound LIMIT 1
	) AS LeastVendor, 
	(
	SELECT MAX(PricePerPound ) 
	FROM productprices
	WHERE productprices.ProductNumberInternal = pm.ProductNumberInternal LIMIT 1
	) AS MaxEstimatedPricePerPound,
	(
	SELECT vendors.name
	FROM productprices, vendors
	WHERE productprices.ProductNumberInternal = pm.ProductNumberInternal
	AND productprices.VendorID = vendors.vendor_id
	AND PricePerPound = MaxEstimatedPricePerPound LIMIT 1
	) AS MaxVendor,
	(
	SELECT UnitPrice FROM purchaseordermaster LEFT JOIN purchaseorderdetail USING(PurchaseOrderNumber) WHERE ProductNumberInternal = pm.ProductNumberInternal ORDER BY DateOrderPlaced DESC LIMIT 1
	) AS LastUnitPrice,
	(
	SELECT UnitOfMeasure FROM purchaseordermaster Left join purchaseorderdetail USING(PurchaseOrderNumber) WHERE ProductNumberInternal = pm.ProductNumberInternal ORDER BY DateOrderPlaced DESC LIMIT 1
	) AS LastUnitOfMeasure,
	(
	SELECT TotalQuantityOrdered FROM purchaseordermaster Left join purchaseorderdetail USING(PurchaseOrderNumber) WHERE ProductNumberInternal = pm.ProductNumberInternal ORDER BY DateOrderPlaced DESC LIMIT 1
	) AS LastTotalQuantityOrdered 
	FROM formulationdetail
	LEFT JOIN productmaster pm ON formulationdetail.IngredientProductNumber = pm.ProductNumberInternal
	WHERE formulationdetail.ProductNumberInternal = '$internal_number'";


//$sql = "SELECT (SELECT notes FROM productmaster WHERE ProductNumberInternal =  '" . $_GET['pni'] . "') AS notes, formulationdetail.ProductNumberInternal, formulationdetail.IngredientSEQ, formulationdetail.IngredientProductNumber, formulationdetail.Percentage, productmaster.Designation, productmaster.FEMA_NBR, externalproductnumberreference.ProductNumberExternal, name, Natural_OR_Artificial, ProductType, Kosher
//FROM formulationdetail
//LEFT JOIN productmaster ON formulationdetail.IngredientProductNumber = productmaster.ProductNumberInternal
//LEFT JOIN externalproductnumberreference ON formulationdetail.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
//LEFT JOIN vendorproductcodes ON vendorproductcodes.ProductNumberInternal = formulationdetail.IngredientProductNumber
//LEFT JOIN vendors ON vendorproductcodes.VendorID = vendors.vendor_id
//WHERE formulationdetail.ProductNumberInternal = '" . $_GET['pni'] . "'";
$result = mysql_query($sql, $link) or die (mysql_error());
$row = mysql_fetch_array($result);

?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="680" ALIGN=CENTER><TR><TD>



<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD WIDTH="60"><IMG SRC="../images/abelei_logo.png" WIDTH="60" BORDER="0"></TD>
		<TD WIDTH="10"><IMG SRC="../images/spacer.gif" WIDTH="10" BORDER="0"></TD>
		<TD WIDTH="80" VALIGN=MIDDLE STYLE="font-size:8pt"><NOBR>clever able capable</NOBR><BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<IMG SRC="../images/abelei_font.png" WIDTH="80" BORDER="0"><!-- <BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<SPAN STYLE="font-size:7pt">US INGREDIENTS</STYLE> --></TD>
		<TD ALIGN=RIGHT STYLE="font-size:8pt">Date printed: <?php echo date("l, F j, Y")?></TD>
	</TR>
</TABLE><BR><BR>



<TABLE BORDER="0" HEIGHT="750" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>

<B>Formulation: Internal#: <?php echo $internal_number;?></B><BR><BR>

<!--<B CLASS="black">Weight estimate for report: <?php //echo number_format($weight, 2);?></B><BR><BR>-->

<?php echo $ProductDesignation;?> (abelei# <?php echo $external_number;?>)<BR><BR>

<TABLE BORDER="1" WIDTH="100%" CELLSPACING="0" CELLPADDING="3" BORDERCOLOR="#CDCDCD">
	<TR ALIGN=CENTER>
		<TD><B CLASS="black" STYLE="font-size:8pt">Seq#</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Internal#</B></TD>
		<TD WIDTH=220 ALIGN=LEFT><B CLASS="black" STYLE="font-size:8pt">Ingredient Description</B></TD>
		<TD ALIGN=LEFT><B CLASS="black" STYLE="font-size:8pt">Natural<BR>and artificial</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">%</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">FEMA#</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Vendor</B></TD>
		<!-- <TD ALIGN=RIGHT STYLE="font-size:8pt"><NOBR><B CLASS="black" STYLE="font-size:8pt">Cost per lb.</B></NOBR></TD>
		<TD ALIGN=RIGHT STYLE="font-size:8pt"><NOBR><B CLASS="black" STYLE="font-size:8pt">Ext. cost</B></NOBR></TD> -->
	</TR>

	<?php
	$total = 0;
	$OldIngredientProductNumber = '';
	$result = mysql_query($sql, $link) or die (mysql_error());
	while ( $row = mysql_fetch_array($result) ) {
	?>
		<TR>

			<?php
			if ( substr($row['IngredientProductNumber'], 0, 1) == 4 ) {
				$td_bgcolor = "#999999";
				$font_color = "color:#FFFFFF;font-weight:bold";
				$colspan = "COLSPAN=7";
			} else {
				$td_bgcolor = "#FFFFFF";
				$font_color = "color: #000000";
				$colspan = "";
			}
			?>

			<TD BGCOLOR="<?php echo $td_bgcolor;?>" ALIGN=RIGHT><?php
			echo "<SPAN STYLE='" . $font_color . "'>" . $row['IngredientSEQ'] . "</SPAN>";
			?></TD>

			<TD BGCOLOR="<?php echo $td_bgcolor;?>" ALIGN=CENTER><?php

			if ( substr($row['IngredientProductNumber'], 0, 1) == 2 ) {
				$sql = "SELECT ProductNumberExternal FROM externalproductnumberreference WHERE ProductNumberInternal = " . $row['IngredientProductNumber'];
				$result_external = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql_external<BR><BR>");
				$row_external = mysql_fetch_array($result_external);
				$abelei_number = " (abelei# " . $row_external[0] . ")";
			}
			else {
				$abelei_number = '';
			}

			echo "<SPAN STYLE='" . $font_color . "'>" . $row['IngredientProductNumber'] . $abelei_number . "</SPAN>";
			?></TD>

			<TD BGCOLOR="<?php echo $td_bgcolor;?>" WIDTH=220 <?php echo $colspan;?>><IMG SRC="/images/spacer.gif" WIDTH="220" HEIGHT="1"><BR>
			<?php
			if ( $row['Designation'] != '' ) {
				echo "<SPAN STYLE='" . $font_color . "'>" . $row['Designation'] . "</SPAN>";
			} else {
				echo "&nbsp;";
			}
			?></TD>

			<?php
			if ( substr($row['IngredientProductNumber'], 0, 1) != 4 ) {
			?>

				<TD BGCOLOR="<?php echo $td_bgcolor;?>"><?php
				if ( $row['Natural_OR_Artificial'] != '' ) {
					echo $row['Natural_OR_Artificial'];
				} else {
					echo "&nbsp;";
				}
				?></TD>

				<TD ALIGN=RIGHT><?php echo number_format($row['Percentage'], 3);?></TD>

				<TD><?php
				if ( $row['FEMA_NBR'] != '' ) {
					echo $row['FEMA_NBR'];
				} else {
					echo "&nbsp;";
				}
				?></TD>

				<?php
				if ( $row['VendorID'] != '' and $row['Tier'] != '' ) {	
					$sql= "SELECT Tier, PricePerPound, name 
					FROM vwmaterialpricing
					LEFT JOIN vendors ON vwmaterialpricing.VendorID = vendors.vendor_id
					WHERE ProductNumberInternal = " . $row['IngredientProductNumber'] . " AND VendorID = '" . $row['VendorID'] . "' AND Tier = '" . $row['Tier'] . "'";
					$result_selected = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					$row_selected = mysql_fetch_array($result_selected);
					$vendor_name = $row_selected['name'];
					$vendor_price_per_pound = $row_selected['PricePerPound'];
					$vendor_tier = $row_selected['Tier'];
				} else {
					$vendor_name = "<NOBR><I>None yet</I></NOBR>";
					$vendor_price_per_pound = 0;
					$vendor_tier = "&nbsp;";
				}
				?>
				<TD><?php echo $vendor_name; ?></TD>
				<!-- <TD ALIGN=RIGHT><?php
				//if ( is_numeric($vendor_price_per_pound) ) {
				//	echo number_format($vendor_price_per_pound, 2);
				//}
				?>
				</TD> -->
				<!-- <TD ALIGN=RIGHT><?php
				//if ( is_numeric($vendor_price_per_pound) ) {
				//	$selected_line_item = $vendor_price_per_pound*$row['Percentage'];
				//	echo number_format($selected_line_item/100, 2);
				//	$TotalSelectedPricePerPound = $TotalSelectedPricePerPound + $selected_line_item;
				//}
				?>
				</TD> -->
			<?php } ?>

		</TR>
		<?php
		if ( $OldIngredientProductNumber != $row['IngredientProductNumber'] ) {
			$total = $total + $row['Percentage'];
		}
		$OldIngredientProductNumber = $row['IngredientProductNumber'];
	}
	?>


	<TR>
		<TD COLSPAN=4>&nbsp;</TD>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt"><?php echo number_format($total, 3) ;?></B></TD>
		<TD COLSPAN=2>&nbsp;</TD>
		<!-- <TD ALIGN=RIGHT>&nbsp;</TD>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt"><?php //echo number_format($TotalSelectedPricePerPound/100, 2);?></B></TD> -->
	</TR>


</TABLE><BR>

<B>Notes: <BR><BR>

<?php echo $notes;?></B>



		</TD>
	</TR>
</TABLE>

<BR><BR>
<SPAN STYLE="font-size:8pt">
194 Alder Drive |
North Aurora, Illinois 60542 |
t-630.859.1410 |
f-630.859.1448 |
<SPAN STYLE="color: blue">toll-free 866-4-abelei</SPAN>
</SPAN>

</TD></TR></TABLE>

</BODY>
</HTML>