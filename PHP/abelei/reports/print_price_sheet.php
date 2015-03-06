<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

if ( $_REQUEST['psn'] != '' ) {
	$psn = $_REQUEST['psn'];
}

include('../inc_global.php');
include('../search/system_defaults.php');

$sql = "SELECT pricesheetmaster.ProductDesignation, pricesheetmaster.ProcessType, pricesheetmaster.DatePriced, pricesheetmaster.FOBLocation, pricesheetmaster.Terms, pricesheetmaster.Notes, pricesheetmaster.Packaged_In, ProductNumberExternal, productmaster.Designation, customers.name, (SELECT CONCAT( first_name,  ' ', last_name ) FROM users WHERE user_id = SalesPersonEmployeeID) AS sales_name, (SELECT CONCAT( first_name,  ' ', last_name ) FROM users WHERE user_id = Priced_ByEmployeeID) AS priced_name
FROM pricesheetmaster
LEFT JOIN productmaster USING (ProductNumberInternal)
INNER JOIN externalproductnumberreference USING(ProductNumberInternal)
LEFT JOIN customers ON pricesheetmaster.CustomerID = customers.customer_id
WHERE PriceSheetNumber = " . $psn;
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);

$ProductDesignation = $row['ProductDesignation'];
$FOBLocation = $row['FOBLocation'];
$Terms = $row['Terms'];
$Notes = $row['Notes'];
$ProcessType = $row['ProcessType'];
$Packaged_In = $row['Packaged_In'];
$ProductNumberExternal = $row['ProductNumberExternal'];
$Designation = $row['Designation'];
$name = $row['name'];
$sales_name = $row['sales_name'];
$priced_name = $row['priced_name'];
if ( $row['DatePriced'] != '' ) {
	$DatePriced = date("m/d/Y", strtotime($row['DatePriced']));
} else {
	$DatePriced = '';
}
	
?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="680" ALIGN=CENTER><TR VALIGN=TOP><TD>



<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD WIDTH="60"><IMG SRC="../images/abelei_logo.png" WIDTH="60" BORDER="0"></TD>
		<TD WIDTH="10"><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT=1 BORDER="0"></TD>
		<TD WIDTH="80" VALIGN=MIDDLE STYLE="font-size:8pt"><NOBR>clever able capable</NOBR><BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<IMG SRC="../images/abelei_font.png" WIDTH="80" BORDER="0"></TD>
		<TD ALIGN=CENTER><BR><B CLASS="header">
		<NOBR>PRICE INFORMATION SHEET</NOBR><BR>
		FLAVORS - <?php echo $ProcessType;?></B>
		</TD>
		<TD ALIGN=RIGHT><BR><NOBR>Price Sheet# <?php echo $psn;?></NOBR></TD>
	</TR>
</TABLE><BR><BR>



<TABLE BORDER="0" HEIGHT="770" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
	<TR>
		<TD><B>Date priced:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="20" HEIGHT=1></TD>
		<TD><?php echo $DatePriced;?></TD>
	</TR>
	<TR>
		<TD><B>Product#:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="20" HEIGHT=1></TD>
		<TD><?php echo $ProductNumberExternal;?></TD>
	</TR>
	<TR>
		<TD><B>Product name:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="20" HEIGHT=1></TD>
		<TD><?php echo $ProductDesignation;?></TD>
	</TR>
	<TR>
		<TD><B>Customer:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="20" HEIGHT=1></TD>
		<TD><?php echo $name;?></TD>
	</TR>
	<TR>
		<TD><B>Salesperson:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="20" HEIGHT=1></TD>
		<TD><?php echo $sales_name;?></TD>
	</TR>
</TABLE><BR>



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
	$Tot_Raw_Material_Costs = $row['total_price'];
}

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
	$Material_Margin_Percentage = number_format(round(($SellingPrice - $Tot_Raw_Material_Costs) / $SellingPrice, 2), 2);
} else {
	$Material_Margin_Percentage = number_format(0, 2);
}

$Operating_Margin_Per_Pound = number_format(round($SellingPrice - $Total_Costs, 2), 2);
$Operating_Margin_Per_Gal = number_format(round($SellingPrice_Per_Gal - $Total_Costs_per_Gal, 2), 2);
$Operating_Margin_Per_KG = number_format(round($SellingPrice_Per_KG - $Total_Costs_per_KG, 2), 2);
$Operating_Margin_Per_Liter = number_format(round($SellingPrice_Per_Liter - $Total_Costs_per_Liter, 2), 2);

if ( $SellingPrice != 0 ) {
	$Operating_Margin_Percentage = number_format(round(($SellingPrice - $Total_Costs) / $SellingPrice, 2), 2);
} else {
	$Operating_Margin_Percentage = number_format(0, 2);
}

$U_P_per_lb = number_format(round($SellingPrice * ( $row_analysis['Cost_In_Use'] * 0.01), 2), 2);
$U_P_per_KG = number_format(round($SellingPrice_Per_KG * ( $row_analysis['Cost_In_Use'] * 0.01), 2), 2);
$U_P_per_Gal = number_format(round($SellingPrice_Per_Gal * ( $row_analysis['Cost_In_Use'] * 0.01), 2), 2);
$U_P_per_Liter = number_format(round($SellingPrice_Per_Liter * ( $row_analysis['Cost_In_Use'] * 0.01), 2), 2);

?>

<TABLE BORDER=0 CELLSPACING="0" CELLPADDING="4" BORDERCOLOR="#CDCDCD" BGCOLOR="white">
	<TR ALIGN=RIGHT>
		<TD>&nbsp;</TD>
		<TD><B STYLE="text-decoration:underline">$/lb</B></TD>
		<TD><B STYLE="text-decoration:underline">$/Gal</B></TD>
		<TD><B STYLE="text-decoration:underline">$/kg</B></TD>
		<TD><B STYLE="text-decoration:underline">$/Ltr</B></TD>
		<TD><B STYLE="text-decoration:underline">lbs/Gal</B></TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD ALIGN=LEFT><B>Raw Material Cost:</B></TD>
		<TD>$<?php echo number_format($Tot_Raw_Material_Costs, 2);?></TD>
		<TD>$<?php echo $Material_Cost_Per_gallon;?></TD>
		<TD>$<?php echo $Material_COST_Per_Kilo;?></TD>
		<TD>$<?php echo $Material_COST_Per_Liter;?></TD>
		<TD><?php echo number_format($row_analysis['Lbs_Per_Gallon'], 2);?></TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD ALIGN=LEFT><NOBR><B>Manufacturing Costs:</B></NOBR></TD>
		<TD>$<?php echo $Tot_Manufacturing_Costs;?></TD>
		<TD>$<?php echo $Tot_Manufacturing_Costs_Per_Gal;?></TD>
		<TD>$<?php echo $Tot_Manufacturing_Costs_Per_Kg;?></TD>
		<TD>$<?php echo $Tot_Manufacturing_Costs_Per_Liter;?></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD ALIGN=LEFT><B>Packaging Costs:</B></TD>
		<TD>$<?php echo number_format(round($row_analysis['PackagingCost'], 2), 2);?></TD>
		<TD>$<?php echo $Tot_Packaging_Costs_per_Gal;?></TD>
		<TD>$<?php echo $Tot_Packaging_Costs_per_Kg;?></TD>
		<TD>$<?php echo $Tot_Packaging_Costs_per_Liter;?></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD ALIGN=LEFT><B>Shipping Costs:</B></TD>
		<TD>$<?php echo number_format(round($row_analysis['ShippingCost'], 2), 2);?></TD>
		<TD>$<?php echo $Tot_Shipping_Costs_Per_Gal;?></TD>
		<TD>$<?php echo $Tot_Shipping_Costs_Per_Kg;?></TD>
		<TD>$<?php echo $Tot_Shipping_Costs_Per_Liter;?></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD ALIGN=LEFT><B>Adjustment:</B></TD>
		<TD>$<?php echo number_format(round($row_analysis['ManualAdjustment'], 2), 2);?></TD>
		<TD>$<?php echo $Tot_Manual_Adjustment_Costs_Per_Gal;?></TD>
		<TD>$<?php echo $Tot_Manual_Adjustment_Costs_Per_KG;?></TD>
		<TD>$<?php echo $Tot_Manual_Adjustment_Costs_Per_Liter;?></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD ALIGN=LEFT><B>Total Costs:</B></TD>
		<TD>$<?php echo $Total_Costs;?></TD>
		<TD>$<?php echo $Total_Costs_per_Gal;?></TD>
		<TD>$<?php echo $Total_Costs_per_KG;?></TD>
		<TD>$<?php echo $Total_Costs_per_Liter;?></TD>
		<TD>&nbsp;</TD>
	</TR>

	<TR>
		<TD COLSPAN=6>&nbsp;</TD>
	</TR>

	<TR ALIGN=RIGHT>
		<TD>&nbsp;</TD>
		<TD><B STYLE="text-decoration:underline">$/lb</B></TD>
		<TD><B STYLE="text-decoration:underline">$/Gal</B></TD>
		<TD><B STYLE="text-decoration:underline">$/kg</B></TD>
		<TD><B STYLE="text-decoration:underline">$/Ltr</B></TD>
		<TD><B STYLE="text-decoration:underline">%</B></TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD ALIGN=LEFT><B>Selling Price:</B></TD>
		<TD>$<?php echo $SellingPrice;?></TD>
		<TD>$<?php echo $SellingPrice_Per_Gal;?></TD>
		<TD>$<?php echo $SellingPrice_Per_KG;?></TD>
		<TD>$<?php echo $SellingPrice_Per_Liter;?></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD ALIGN=LEFT><B>Material Margin:</B></TD>
		<TD>$<?php echo $Material_Margin_Per_Pound;?></TD>
		<TD>$<?php echo $Material_Margin_Per_gal;?></TD>
		<TD>$<?php echo $Material_Margin_Per_KG;?></TD>
		<TD>$<?php echo $Material_Margin_Per_Liter;?></TD>
		<TD><?php echo $Material_Margin_Percentage;?>%</TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD ALIGN=LEFT><NOBR><B>Operating Margin:</B></NOBR></TD>
		<TD>$<?php echo $Operating_Margin_Per_Pound;?></TD>
		<TD>$<?php echo $Operating_Margin_Per_Gal;?></TD>
		<TD>$<?php echo $Operating_Margin_Per_KG;?></TD>
		<TD>$<?php echo $Operating_Margin_Per_Liter;?></TD>
		<TD><?php echo $Operating_Margin_Percentage;?>%</TD>
	</TR>

	<TR>
		<TD COLSPAN=6>&nbsp;</TD>
	</TR>

	<TR ALIGN=RIGHT>
		<TD>&nbsp;</TD>
		<TD><B STYLE="text-decoration:underline">Use Level</B></TD>
		<TD><B STYLE="text-decoration:underline">$/lb</B></TD>
		<TD><B STYLE="text-decoration:underline">$/Gal</B></TD>
		<TD><B STYLE="text-decoration:underline">$/kg</B></TD>
		<TD><B STYLE="text-decoration:underline">$/Ltr</B></TD>
	</TR>
	<TR ALIGN=RIGHT>
		<TD ALIGN=LEFT><B>Cost-in-Use:</B></TD>
		<TD><?php echo $Cost_in_Use_Level;?>%</TD>
		<TD>$<?php echo $U_P_per_lb;?></TD>
		<TD>$<?php echo $U_P_per_Gal;?></TD>
		<TD>$<?php echo $U_P_per_KG;?></TD>
		<TD>$<?php echo $U_P_per_Liter;?></TD>
	</TR>
</TABLE><BR>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
	<TR>
		<TD><NOBR><B>F.O.B. location:</B></NOBR></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="20" HEIGHT=1></TD>
		<TD><?php echo $FOBLocation;?></TD>
	</TR>
	<TR>
		<TD><B>Terms:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="20" HEIGHT=1></TD>
		<TD><?php echo $Terms;?></TD>
	</TR>
	<TR>
		<TD><B>Packaged in:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="20" HEIGHT=1></TD>
		<TD><?php echo $Packaged_In;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD><B>Comments:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="20" HEIGHT=1></TD>
		<TD><?php echo $Notes;?></TD>
	</TR>
</TABLE><BR>

<DIV ALIGN=CENTER><B>Priced by:</B> <?php echo $priced_name;?></DIV>

</TD></TR></TABLE>


<!-- <BR><BR>
<SPAN STYLE="font-size:8pt">
194 Alder Drive |
North Aurora, Illinois 60542 |
t-630.859.1410 |
f-630.859.1448 |
<SPAN STYLE="color: blue">toll-free 866-4-abelei</SPAN>
</SPAN> -->

</TD></TR></TABLE>

</BODY>
</HTML>