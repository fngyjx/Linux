<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN HAS PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');

?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<STYLE TYPE="text/css" TITLE="text/css">
		body {
			margin: 0;
		}
	</STYLE>
	<LINK HREF="styles.css" REL="stylesheet" TYPE="text/css">
</HEAD>

<BODY>

<?php

if ( $_REQUEST['locked'] == 0 ) {
	$sql = "SELECT IngredientProductNumber, VendorID, Tier, Percentage
	FROM pricesheetdetail
	WHERE PriceSheetNumber = " . $_GET['psn'] . " ORDER BY IngredientSEQ";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$Tot_Raw_Material_Costs = 0;
	while ( $row = mysql_fetch_array($result) ) {
		if ( $row['VendorID'] != '' ) {
			$sql = "SELECT PricePerPound FROM productprices WHERE ProductNumberInternal = '" . $row['IngredientProductNumber'] . "' AND VendorID = " . $row['VendorID'] . " AND Tier = '" . $row['Tier'] . "'";
			$result_price = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$row_price = mysql_fetch_array($result_price);
			$Tot_Raw_Material_Costs = $Tot_Raw_Material_Costs + (($row_price['PricePerPound'] * $row['Percentage']) / 100);
		} else {
			$Tot_Raw_Material_Costs = $Tot_Raw_Material_Costs + 0;
		}
	}
} else {
	$sql = "SELECT SUM(((Price * Percentage)/100)) AS total_price FROM pricesheetdetail WHERE PriceSheetNumber = " . $_GET['psn'];
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$Tot_Raw_Material_Costs = $row[total_price];
}
// adjust raw material price by 102% to account for lmanufacturing oss
$Tot_Raw_Material_Costs = round( $Tot_Raw_Material_Costs*1.02, 2 );

$sql = "SELECT * FROM pricesheetmaster WHERE PriceSheetNumber = " . $_GET['psn'];
$result_analysis = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row_analysis = mysql_fetch_array($result_analysis);

$SellingPrice = number_format(round($row_analysis['SellingPrice'], 2), 2);
$Cost_in_Use_Level = number_format($row_analysis['Cost_In_Use'], 2);

$Tot_Manufacturing_Costs = number_format(round($row_analysis['SprayDriedCost'] + $row_analysis['RibbonBlendingCost'] + $row_analysis['LiquidProcessingCost'], 2), 2);
$Total_Costs = number_format(round($Tot_Raw_Material_Costs + $Tot_Manufacturing_Costs + $row_analysis['PackagingCost'] + $row_analysis['ShippingCost'] + $row_analysis['ManualAdjustment'], 2), 2);

$Material_Cost_Per_gallon = number_format(round($row_analysis['Lbs_Per_Gallon'] * $Tot_Raw_Material_Costs, 2), 2);
$Material_COST_Per_Kilo = number_format(round(($Tot_Raw_Material_Costs * 2.2046), 2), 2);
$Material_COST_Per_Liter = number_format(round(($Material_Cost_Per_gallon * 0.2642), 2), 2);

$Tot_Manufacturing_Costs_Per_Gal = number_format(round($row_analysis['Lbs_Per_Gallon'] * $Tot_Manufacturing_Costs, 2), 2);
$Tot_Manufacturing_Costs_Per_Kg = number_format(round(($Tot_Manufacturing_Costs * 2.2046), 2), 2);
$Tot_Manufacturing_Costs_Per_Liter = number_format(round(($Tot_Manufacturing_Costs_Per_Gal * 0.2642), 2), 2);

$Tot_Packaging_Costs_per_Gal = number_format(round($row_analysis['Lbs_Per_Gallon'] * $row_analysis['PackagingCost'], 2), 2);
$Tot_Packaging_Costs_per_Kg = number_format(round(($row_analysis['PackagingCost'] * 2.2046), 2), 2);
$Tot_Packaging_Costs_per_Liter = number_format(round(($Tot_Packaging_Costs_per_Gal * 0.2642), 2), 2);

$Tot_Shipping_Costs_Per_Gal = number_format(round($row_analysis['Lbs_Per_Gallon'] * $row_analysis['ShippingCost'], 2), 2);
$Tot_Shipping_Costs_Per_Kg = number_format(round(($row_analysis['ShippingCost'] * 2.2046), 2), 2);
$Tot_Shipping_Costs_Per_Liter = number_format(round(($Tot_Shipping_Costs_Per_Gal * 0.2642), 2), 2);

$Tot_Manual_Adjustment_Costs_Per_Gal = number_format(round($row_analysis['Lbs_Per_Gallon'] * $row_analysis['ManualAdjustment'], 2), 2);
$Tot_Manual_Adjustment_Costs_Per_KG = number_format(round(($row_analysis['ManualAdjustment'] * 2.2046), 2), 2);
$Tot_Manual_Adjustment_Costs_Per_Liter = number_format(round(($Tot_Manual_Adjustment_Costs_Per_Gal * 0.2642), 2), 2);

$Total_Costs_per_Gal = number_format(round($Material_Cost_Per_gallon + $Tot_Manufacturing_Costs_Per_Gal + $Tot_Packaging_Costs_per_Gal + $Tot_Shipping_Costs_Per_Gal + $Tot_Manual_Adjustment_Costs_Per_Gal, 2), 2);

$Total_Costs_per_KG = number_format(round($Material_COST_Per_Kilo + $Tot_Manufacturing_Costs_Per_Kg + $Tot_Packaging_Costs_per_Kg + $Tot_Shipping_Costs_Per_Kg + $Tot_Manual_Adjustment_Costs_Per_KG, 2), 2);

$Total_Costs_per_Liter = number_format(round($Material_COST_Per_Liter + $Tot_Manufacturing_Costs_Per_Liter + $Tot_Packaging_Costs_per_Liter + $Tot_Shipping_Costs_Per_Liter + $Tot_Manual_Adjustment_Costs_Per_Liter, 2), 2);

$SellingPrice_Per_Gal = number_format(round($row_analysis['Lbs_Per_Gallon'] * $SellingPrice, 2), 2);
$SellingPrice_Per_KG = number_format(round($SellingPrice * 2.2046, 2), 2);
$SellingPrice_Per_Liter = number_format(round($SellingPrice_Per_Gal * 0.2642, 2), 2);

$PriceMM70 = number_format(round($Total_Costs / 0.3, 2), 2);
$PriceMM60 = number_format(round($Total_Costs / 0.4, 2), 2);
$PriceMM50 = number_format(round($Total_Costs / 0.5, 2), 2);
$PriceMM40 = number_format(round($Total_Costs / 0.6, 2), 2);
$PriceMM30 = number_format(round($Total_Costs / 0.7, 2), 2);

$Material_Margin_Per_Pound = number_format(round($SellingPrice - $Tot_Raw_Material_Costs, 2), 2);
$Material_Margin_Per_gal = number_format(round($SellingPrice_Per_Gal - $Material_Cost_Per_gallon, 2), 2);
$Material_Margin_Per_KG = number_format(round($SellingPrice_Per_KG - $Material_COST_Per_Kilo, 2), 2);
$Material_Margin_Per_Liter = number_format(round($SellingPrice_Per_Liter - $Material_COST_Per_Liter, 2), 2);

if ( $SellingPrice != 0 ) {
	$Material_Margin_Percentage = number_format((($SellingPrice - $Tot_Raw_Material_Costs) / $SellingPrice * 100), 2);
} else {
	$Material_Margin_Percentage = number_format(0, 2);
}

$Operating_Margin_Per_Pound = number_format(round($SellingPrice - $Total_Costs, 2), 2);
$Operating_Margin_Per_Gal = number_format(round($SellingPrice_Per_Gal - $Total_Costs_per_Gal, 2), 2);
$Operating_Margin_Per_KG = number_format(round($SellingPrice_Per_KG - $Total_Costs_per_KG, 2), 2);
$Operating_Margin_Per_Liter = number_format(round($SellingPrice_Per_Liter - $Total_Costs_per_Liter, 2), 2);

if ( $SellingPrice != 0 ) {
	$Operating_Margin_Percentage = number_format((($SellingPrice - $Total_Costs) / $SellingPrice * 100), 2);
} else {
	$Operating_Margin_Percentage = number_format(0, 2);
}

$U_P_per_lb = number_format(round($SellingPrice * ( $row_analysis['Cost_In_Use'] * 0.01), 2), 2);
$U_P_per_KG = number_format(round($SellingPrice_Per_KG * ( $row_analysis['Cost_In_Use'] * 0.01), 2), 2);
$U_P_per_Gal = number_format(round($SellingPrice_Per_Gal * ( $row_analysis['Cost_In_Use'] * 0.01), 2), 2);
$U_P_per_Liter = number_format(round($SellingPrice_Per_Liter * ( $row_analysis['Cost_In_Use'] * 0.01), 2), 2);

?>

<TABLE BORDER=1 CELLSPACING="0" CELLPADDING="2" BORDERCOLOR="#CDCDCD" BGCOLOR="white">
	<TR ALIGN=RIGHT>
		<TD BGCOLOR="#DFDFDF">&nbsp;</TD>
		<TD><B CLASS="black">$/lb</B></TD>
		<TD><B CLASS="black">$/Gal</B></TD>
		<TD><B CLASS="black">$/kg</B></TD>
		<TD><B CLASS="black">$/Ltr</B></TD>
		<TD><B CLASS="black">lbs/Gal</B></TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD><B CLASS="black">Raw Material Cost:</B></TD>
		<TD>$<?php echo number_format($Tot_Raw_Material_Costs, 2);?></TD>
		<TD>$<?php echo $Material_Cost_Per_gallon;?></TD>
		<TD>$<?php echo $Material_COST_Per_Kilo;?></TD>
		<TD>$<?php echo $Material_COST_Per_Liter;?></TD>
		<TD><?php echo number_format($row_analysis['Lbs_Per_Gallon'], 2);?></TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD><NOBR><B CLASS="black">Manufacturing Costs:</B></NOBR></TD>
		<TD>$<?php echo $Tot_Manufacturing_Costs;?></TD>
		<TD>$<?php echo $Tot_Manufacturing_Costs_Per_Gal;?></TD>
		<TD>$<?php echo $Tot_Manufacturing_Costs_Per_Kg;?></TD>
		<TD>$<?php echo $Tot_Manufacturing_Costs_Per_Liter;?></TD>
		<TD BGCOLOR="#DFDFDF">&nbsp;</TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD><B CLASS="black">Packaging Costs:</B></TD>
		<TD>$<?php echo number_format(round($row_analysis['PackagingCost'], 2), 2);?></TD>
		<TD>$<?php echo $Tot_Packaging_Costs_per_Gal;?></TD>
		<TD>$<?php echo $Tot_Packaging_Costs_per_Kg;?></TD>
		<TD>$<?php echo $Tot_Packaging_Costs_per_Liter;?></TD>
		<TD BGCOLOR="#DFDFDF">&nbsp;</TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD><B CLASS="black">Shipping Costs:</B></TD>
		<TD>$<?php echo number_format(round($row_analysis['ShippingCost'], 2), 2);?></TD>
		<TD>$<?php echo $Tot_Shipping_Costs_Per_Gal;?></TD>
		<TD>$<?php echo $Tot_Shipping_Costs_Per_Kg;?></TD>
		<TD>$<?php echo $Tot_Shipping_Costs_Per_Liter;?></TD>
		<TD BGCOLOR="#DFDFDF">&nbsp;</TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD><B CLASS="black">Adjustment:</B></TD>
		<TD>$<?php echo number_format(round($row_analysis['ManualAdjustment'], 2), 2);?></TD>
		<TD>$<?php echo $Tot_Manual_Adjustment_Costs_Per_Gal;?></TD>
		<TD>$<?php echo $Tot_Manual_Adjustment_Costs_Per_KG;?></TD>
		<TD>$<?php echo $Tot_Manual_Adjustment_Costs_Per_Liter;?></TD>
		<TD BGCOLOR="#DFDFDF">&nbsp;</TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD><B CLASS="black">Total Costs:</B></TD>
		<TD>$<?php echo $Total_Costs;?></TD>
		<TD>$<?php echo $Total_Costs_per_Gal;?></TD>
		<TD>$<?php echo $Total_Costs_per_KG;?></TD>
		<TD>$<?php echo $Total_Costs_per_Liter;?></TD>
		<TD BGCOLOR="#DFDFDF">&nbsp;</TD>
	</TR>
</TABLE><BR>

<TABLE BORDER=1 CELLSPACING="0" CELLPADDING="2" BORDERCOLOR="#CDCDCD" BGCOLOR="white">
	<TR ALIGN=RIGHT>
		<TD BGCOLOR="#DFDFDF">&nbsp;</TD>
		<TD><B CLASS="black">$/lb</B></TD>
		<TD><B CLASS="black">$/Gal</B></TD>
		<TD><B CLASS="black">$/kg</B></TD>
		<TD><B CLASS="black">$/Ltr</B></TD>
		<TD><B CLASS="black">%</B></TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD><B CLASS="black">Selling Price:</B></TD>
		<TD>$<?php echo $SellingPrice;?></TD>
		<TD>$<?php echo $SellingPrice_Per_Gal;?></TD>
		<TD>$<?php echo $SellingPrice_Per_KG;?></TD>
		<TD>$<?php echo $SellingPrice_Per_Liter;?></TD>
		<TD BGCOLOR="#DFDFDF">&nbsp;</TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD><B CLASS="black">Material Margin:</B></TD>
		<TD>$<?php echo $Material_Margin_Per_Pound;?></TD>
		<TD>$<?php echo $Material_Margin_Per_gal;?></TD>
		<TD>$<?php echo $Material_Margin_Per_KG;?></TD>
		<TD>$<?php echo $Material_Margin_Per_Liter;?></TD>
		<TD><?php echo $Material_Margin_Percentage;?>%</TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD><NOBR><B CLASS="black">Operating Margin:</B></NOBR></TD>
		<TD>$<?php echo $Operating_Margin_Per_Pound;?></TD>
		<TD>$<?php echo $Operating_Margin_Per_Gal;?></TD>
		<TD>$<?php echo $Operating_Margin_Per_KG;?></TD>
		<TD>$<?php echo $Operating_Margin_Per_Liter;?></TD>
		<TD><?php echo $Operating_Margin_Percentage;?>%</TD>
	</TR>
</TABLE><BR>

<TABLE BORDER=1 CELLSPACING="0" CELLPADDING="2" BORDERCOLOR="#CDCDCD" BGCOLOR="white">
	<TR ALIGN=RIGHT>
		<TD BGCOLOR="#DFDFDF">&nbsp;</TD>
		<TD><B CLASS="black">Use Level</B></TD>
		<TD><B CLASS="black">$/lb</B></TD>
		<TD><B CLASS="black">$/Gal</B></TD>
		<TD><B CLASS="black">$/kg</B></TD>
		<TD><B CLASS="black">$/Ltr</B></TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD><B CLASS="black">Cost-in-Use:</B></TD>
		<TD><?php echo $Cost_in_Use_Level;?>%</TD>
		<TD>$<?php echo $U_P_per_lb;?></TD>
		<TD>$<?php echo $U_P_per_Gal;?></TD>
		<TD>$<?php echo $U_P_per_KG;?></TD>
		<TD>$<?php echo $U_P_per_Liter;?></TD>
	</TR>
</TABLE><BR>

<TABLE BORDER=1 CELLSPACING="0" CELLPADDING="2" BORDERCOLOR="#CDCDCD" BGCOLOR="white">
	<TR ALIGN=RIGHT>
		<TD BGCOLOR="#DFDFDF">&nbsp;</TD>
		<TD><B CLASS="black">70%</B></TD>
		<TD><B CLASS="black">60%</B></TD>
		<TD><B CLASS="black">50%</B></TD>
		<TD><B CLASS="black">40%</B></TD>
		<TD><B CLASS="black">30%</B></TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD><NOBR><B CLASS="black">Price/Operating Margin:</B></NOBR></TD>
		<TD>$<?php echo $PriceMM70;?></TD>
		<TD>$<?php echo $PriceMM60;?></TD>
		<TD>$<?php echo $PriceMM50;?></TD>
		<TD>$<?php echo $PriceMM40;?></TD>
		<TD>$<?php echo $PriceMM30;?></TD>
	</TR>
</TABLE>

</BODY>
</HTML>