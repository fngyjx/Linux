<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

include('../inc_global.php');

$formula_name = "Colin Artifical Colin Formula";

$sql = "SELECT Natural_OR_Artificial, Designation, ProductType, Kosher, externalproductnumberreference.ProductNumberExternal
FROM externalproductnumberreference
LEFT JOIN productmaster ON externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal
WHERE Organic = 1";
$result = mysql_query($sql, $link) or die (mysql_error() . " $sql");

?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="680" ALIGN=CENTER><TR><TD>



<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
	<TR>
		<TD WIDTH="60"><IMG SRC="../images/abelei_logo.png" WIDTH="60" BORDER="0"></TD>
		<TD WIDTH="10"><IMG SRC="../images/spacer.gif" WIDTH="10" BORDER="0"></TD>
		<TD WIDTH="80" VALIGN=MIDDLE STYLE="font-size:8pt"><NOBR>clever able capable</NOBR><BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<IMG SRC="../images/abelei_font.png" WIDTH="80" BORDER="0"><!-- <BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<SPAN STYLE="font-size:7pt">US INGREDIENTS</STYLE> --></TD>
		<TD ALIGN=CENTER><B>Organic Formulas<BR>Confidential Information</B></TD>
	</TR>
</TABLE><BR><BR>



<TABLE BORDER="0" WIDTH="680" HEIGHT="750" CELLSPACING="0" CELLPADDING="3">
	<TR VALIGN=TOP>
		<TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

	<?php while ( $row = mysql_fetch_array($result) ) {
		$external_number = $row['ProductNumberExternal'];
		$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
		?>
		<TR>
			<TD><?php echo $external_number;?></TD>
			<TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
			<TD><?php echo $ProductDesignation;?></TD>
		</TR>
	<?php } ?>

</TABLE>



		</TD>
	</TR>
</TABLE>

<!-- 
<BR><BR>
<SPAN STYLE="font-size:8pt">
194 Alder Drive |
North Aurora, Illinois 60542 |
t-630.859.1410 |
f-630.859.1448 |
<SPAN STYLE="color: blue">toll-free 866-4-abelei</SPAN>
</SPAN>
 -->

</TD></TR></TABLE>

</BODY>
</HTML>