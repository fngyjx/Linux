<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

include('../inc_global.php');

$sql = "SELECT productmaster.*, externalproductnumberreference.ProductNumberExternal
FROM productmaster LEFT JOIN externalproductnumberreference ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
WHERE productmaster.ProductNumberInternal = " . $_GET['pni'];
$result = mysql_query($sql, $link) or die (mysql_error());
$row = mysql_fetch_array($result);

$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
$external_number = $row['ProductNumberExternal'];
$internal_number = $row['ProductNumberInternal'];

$field_names = array("Calories", "CaloriesFromFat", "TotalFat", "SaturatedFat", "PolyunsaturatedFat", "MonounsaturatedFat", "Cholesterol", "Sodium", "Potassium", "TotalCarbohydrates", "DietaryFiber", "SolubleFiber", "InsolubleFiber", "Sugars", "SugarAlcohol", "OtherCarbohydrates", "Protein", "VitaminA", "VitaminC", "Calcium", "Iron", "VitaminD", "VitaminE", "Thiamin", "Riboflavin", "Niacin", "VitaminB6", "Folate", "VitaminB12", "Biotin", "PantothenicAcid", "Phosphorus", "Iodine", "Magnesium", "Zinc", "Copper");
$units = array("cal", "cal", "g", "g", "g", "g", "mg", "mg", "mg", "g", "g", "g", "g", "g", "g", "g", "g", "IU", "mg", "mg", "mg", "IU", "IU", "mg", "mg", "mg", "mg", "mcg", "mcg", "mcg", "mg", "mg", "mcg", "mg", "mg", "mcg");

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
		<TD ALIGN=RIGHT STYLE="font-size:8pt">Date generated: <?php echo date("l, F j, Y")?></TD>
	</TR>
</TABLE><BR>



<TABLE BORDER="0" WIDTH="680" HEIGHT="750" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>

<B>Nutrition Spreadsheet for:</B> <?php echo $ProductDesignation . " (abelei# " . $external_number . ")";?><BR><BR>

Serving Size 1 Container (100g)<BR>
Servings Per Container: 1<BR><BR>

<TABLE BORDER="1" WIDTH="100%" CELLSPACING="0" CELLPADDING="1">
	<TR ALIGN=CENTER>
		<TD><B CLASS="black" STYLE="font-size:8pt">Nutrient</B></TD>
		<TD ALIGN=CENTER><B CLASS="black" STYLE="font-size:8pt">Units</B></TD>
		<TD ALIGN=CENTER><B CLASS="black" STYLE="font-size:8pt">ADV</B></TD>
		<TD ALIGN=CENTER><B CLASS="black" STYLE="font-size:8pt">Calculated<BR>(per serving)</B></TD>
		<TD ALIGN=CENTER><B CLASS="black" STYLE="font-size:8pt">Rounded<BR>(per serving)</B></TD>
		<TD ALIGN=CENTER><B CLASS="black" STYLE="font-size:8pt">% ADV</B></TD>
		<TD ALIGN=CENTER><B CLASS="black" STYLE="font-size:8pt">Calculated<BR>(per 100.00g)</B></TD>
	</TR>

	<?php
	$i = 0;
	foreach ( $field_names as $value ) { ?>
		<TR>
			<TD><?php echo $value;?>&nbsp;</TD>
			<TD ALIGN=CENTER><?php echo $units[$i];?>&nbsp;</TD>
			<TD ALIGN=RIGHT>
			<?php
			if ( is_numeric($row[$value]) ) {
				if ( $i == 2 or $i ==3 or $i == 6 or $i == 7 ) {
					echo "<";
				}
				echo number_format($row[$value], 1);
			}
			?>&nbsp;
			</TD>
			<TD ALIGN=RIGHT>0.00</TD>
			<TD ALIGN=RIGHT>0.0</TD>
			<TD ALIGN=RIGHT>0</TD>
			<TD ALIGN=RIGHT>0.00</TD>
		</TR>
		<?php
		$i++;
	} ?>

	<TR>
		<TD>TotalSolids</TD>
		<TD ALIGN=CENTER>%</TD>
		<TD ALIGN=RIGHT>100.0</TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD ALIGN=RIGHT>0.0</TD>
	</TR>
</TABLE>



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