<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

include('../inc_global.php');

//$sql = "SELECT * FROM formulationdetail LEFT JOIN productmaster ON formulationdetail.IngredientProductNumber = productmaster. ProductNumberInternal WHERE formulationdetail.ProductNumberInternal = '" . $_GET['pni'] . "' ORDER BY IngredientSEQ";

$sql = "SELECT * FROM productmaster WHERE ProductNumberInternal = '" . escape_data($_GET[pni]) . "'";
$result_des = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row_des = mysql_fetch_array($result_des);
$ProductDesignation = (("" != $row_des[Natural_OR_Artificial]) ? $row_des[Natural_OR_Artificial]." " : "")."$row_des[Designation]".(("" != $row_des[ProductType]) ? " - ".$row_des[ProductType] : "").(("" != $row_des[Kosher]) ? " - ".$row_des[Kosher] : "");
$notes = $row_des[NoteForFormulation];

$sql = "SELECT formulationdetail.ProductNumberInternal, formulationdetail.IngredientSEQ, formulationdetail.IngredientProductNumber, formulationdetail.Percentage, productmaster.Designation, productmaster.FEMA_NBR, externalproductnumberreference.ProductNumberExternal, Natural_OR_Artificial, ProductType, Kosher
FROM formulationdetail
LEFT JOIN productmaster ON formulationdetail.IngredientProductNumber = productmaster.ProductNumberInternal
LEFT JOIN externalproductnumberreference ON formulationdetail.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
WHERE formulationdetail.ProductNumberInternal = '$_GET[pni]'";
$result = mysql_query($sql, $link) or die (mysql_error());
$row = mysql_fetch_array($result);

$external_number = $row['ProductNumberExternal'];
$internal_number = $row['ProductNumberInternal'];

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

<B>Formulation: Internal #: <?php echo $internal_number;?></B><BR><BR>

<?php echo $ProductDesignation;?> (abelei# <?php echo $external_number;?>)<BR><BR>

<TABLE BORDER="1" WIDTH="100%" CELLSPACING="0" CELLPADDING="3" BORDERCOLOR="#CDCDCD">
	<TR ALIGN=CENTER>
		<TD><B CLASS="black" STYLE="font-size:8pt">Seq#</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Internal#</B></TD>
		<TD ALIGN=LEFT><B CLASS="black" STYLE="font-size:8pt">Ingredient Description</B></TD>
		<TD ALIGN=LEFT><B CLASS="black" STYLE="font-size:8pt">Natural<BR>and artificial</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">%</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">FEMA#</B></TD>
	</TR>

	<?php
	$total = 0;
	$result = mysql_query($sql, $link) or die (mysql_error());
	while ( $row = mysql_fetch_array($result) ) {
	?>
		<TR>

			<?php
			if ( substr($row['IngredientProductNumber'], 0, 1) == 4 ) {
				$td_bgcolor = "#999999";
				$font_color = "color:#FFFFFF;font-weight:bold";
				$colspan = "COLSPAN=4";
			} else {
				$td_bgcolor = "#FFFFFF";
				$font_color = "color: #000000";
				$colspan = "";
			}
			?>

			<TD BGCOLOR="<?php echo $td_bgcolor;?>" ALIGN=RIGHT>
			<?php echo "<SPAN STYLE='" . $font_color . "'>" . $row['IngredientSEQ'] . "</SPAN>"; ?>
			</TD>
			
			<TD BGCOLOR="<?php echo $td_bgcolor;?>" ALIGN=CENTER>
			<?php

			if ( substr($row['IngredientProductNumber'], 0, 1) == 2 ) {
				$sql = "SELECT ProductNumberExternal FROM externalproductnumberreference WHERE ProductNumberInternal = " . $row['IngredientProductNumber'];
				$result_external = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql_external<BR><BR>");
				$row_external = mysql_fetch_array($result_external);
				$abelei_number = " (abelei# " . $row_external[0] . ")";
			}
			else {
				$abelei_number = '';
			}
			
			echo "<SPAN STYLE='" . $font_color . "'>" . $row['IngredientProductNumber'] . "</SPAN>";
			?>
			</TD>

			<TD BGCOLOR="<?php echo $td_bgcolor;?>" <?php echo $colspan;?>><?php
			if ( $row['Designation'] != '' ) {
				echo "<SPAN STYLE='" . $font_color . "'>" . $row['Designation'] . $abelei_number . "</SPAN>";
			} else {
				echo "&nbsp;";
			}
			?></TD>

			<?php
			if ( substr($row['IngredientProductNumber'], 0, 1) != 4 ) {
			?>
				<TD><?php
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
			<?php } ?>
	
		</TR>
		<?php
		$total = $total + $row['Percentage'];
	}
	?>

	<TR>
		<TD COLSPAN=4>&nbsp;</TD>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt"><?php echo number_format($total, 3) ;?></B></TD>
		<TD>&nbsp;</TD>
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