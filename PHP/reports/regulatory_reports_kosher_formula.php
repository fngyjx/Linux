<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

include('../inc_global.php');

$sql = "SELECT productmaster.Designation, productmaster.Kosher, externalproductnumberreference.ProductNumberExternal, productmaster.Natural_OR_Artificial, ProductType
FROM productmaster
LEFT JOIN externalproductnumberreference ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
WHERE productmaster.ProductNumberInternal = " . $_GET['pni'];
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);

$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
$external_number = $row['ProductNumberExternal'];
$internal_number = $row['ProductNumberInternal'];



$sql = "SELECT (SELECT notes FROM productmaster WHERE ProductNumberInternal =  '" . $_GET['pni'] . "') AS notes, formulationdetail.ProductNumberInternal, formulationdetail.IngredientSEQ, formulationdetail.IngredientProductNumber, productmaster.Designation, productmaster.Kosher, externalproductnumberreference.ProductNumberExternal, productmaster.Natural_OR_Artificial, ProductType, vwmaterialpricing.vendor_name
FROM formulationdetail
LEFT JOIN productmaster ON formulationdetail.IngredientProductNumber = productmaster.ProductNumberInternal
LEFT JOIN externalproductnumberreference ON formulationdetail.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
LEFT JOIN vwmaterialpricing ON formulationdetail.VendorID = vwmaterialpricing.VendorID AND formulationdetail.Tier = vwmaterialpricing.Tier AND formulationdetail.IngredientProductNumber = vwmaterialpricing.ProductNumberInternal
WHERE formulationdetail.ProductNumberInternal = " . $_GET['pni'] . " ORDER BY IngredientSEQ";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
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

<B>Kosher Submission</B><BR>
<B>Confidential Information</B><BR><BR>

<?php echo $ProductDesignation;?> (abelei# <?php echo $external_number;?>)<BR><BR>

<TABLE BORDER="1" WIDTH="100%" CELLSPACING="0" CELLPADDING="3" BORDERCOLOR="#CDCDCD">
	<TR ALIGN=CENTER>
		<TD><B CLASS="black" STYLE="font-size:8pt">Ingredient</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Kosher</B></TD>
		<TD ALIGN=LEFT><B CLASS="black" STYLE="font-size:8pt">Natural<BR>and artificial</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Vendor</B></TD>
	</TR>

	<?php
	$total = 0;
	$result = mysql_query($sql, $link) or die (mysql_error());
	while ( $row = mysql_fetch_array($result) ) {
		if ( substr($row['IngredientProductNumber'], 0, 1) != 4 ) {
			?>
			<TR>
				<TD><?php

				if ( substr($row['IngredientProductNumber'], 0, 1) == 2 ) {
					$sql = "SELECT ProductNumberExternal FROM externalproductnumberreference WHERE ProductNumberInternal = " . 	$row['IngredientProductNumber'];
					$result_external = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql_external<BR><BR>");
					$row_external = mysql_fetch_array($result_external);
					$abelei_number = " (abelei# " . $row_external[0] . ")";
				}
				else {
					$abelei_number = '';
				}

				echo $row['Designation'] . $abelei_number;
				?></TD>
				<TD><?php
				if ( $row['Kosher'] != '' ) {
					echo $row['Kosher'];
				} else {
					echo "&nbsp;";
				}?></TD>

				<TD><?php
				if ( $row['Natural_OR_Artificial'] != '' ) {
					echo $row['Natural_OR_Artificial'];
				} else {
					echo "&nbsp;";
				}
				?></TD>

				<TD><?php
				if ( $row['vendor_name'] != '' ) {
					echo $row['vendor_name'];
				} else {
					echo "&nbsp;";
				}?></TD>
			</TR>
			<?php
		}
	}
	?>

</TABLE><BR>


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