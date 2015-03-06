<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

include('inc_global.php');

$productNumberInternal = escape_data($_GET[pni]);

if ( strlen($productNumberInternal) < 1 ) {
	die ("Please provide a product number <b/> ");
}
$sql = "SELECT Natural_OR_Artificial, Designation, ProductType, Kosher, NoteForFormulation FROM productmaster WHERE ProductNumberInternal = '" .$productNumberInternal. "'";
//echo $sql."<br />";
$result_des = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row_des = mysql_fetch_array($result_des);
$ProductDesignation = (("" != $row_des[Natural_OR_Artificial]) ? $row_des[Natural_OR_Artificial]." " : "")."$row_des[Designation]".(("" != $row_des[ProductType]) ? " - ".$row_des[ProductType] : "").(("" != $row_des[Kosher]) ? " - ".$row_des[Kosher] : "");
$notes = $row_des[NoteForFormulation];

$sql = "SELECT ProductNumberExternal from externalproductnumberreference where ProductNumberInternal='". $productNumberInternal ."'";
//echo $sql." <br />";
$result_pne = mysql_query($sql,$link) or die (mysql_error()."<br />Could not execute query: $sql<br /><br />");
$row_pne = mysql_fetch_array($result_pne);

$sql = "SELECT formulationdetail.ProductNumberInternal, formulationdetail.IngredientSEQ, formulationdetail.IngredientProductNumber,
formulationdetail.Percentage, pm.Natural_OR_Artificial, pm.Designation, pm.ProductType, pm.Kosher, pm.NoteForFormulation, 
pm.FEMA_NBR, externalproductnumberreference.ProductNumberExternal
FROM formulationdetail
LEFT JOIN productmaster as pm ON formulationdetail.IngredientProductNumber = pm.ProductNumberInternal
LEFT JOIN externalproductnumberreference ON formulationdetail.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
WHERE formulationdetail.IngredientProductNumber = '$productNumberInternal'";

//echo $sql."<br />";
?>


<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0""><BR>

<?php

$result = mysql_query($sql, $link) or die (mysql_error()."failed on SQL $sql<br />");
$row = mysql_fetch_array($result);

$external_number = $row['ProductNumberExternal'];
$internal_number = $row['ProductNumberInternal'];

?>
<h4 align="center">Formulas That Use Product#: <?php echo $productNumberInternal." Abelei#: ".$row_pne['ProductNumberExternal']; ?></h4>
<h5 align="center"><?php echo $ProductDesignation; ?></h5>
<h6 align="center"><?php echo ($notes);?></h6>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="680" ALIGN=CENTER><TR><TD>

<TABLE style="border-top-style:solid; border-right-style:none;border-bottom-style:solid;border-left-style:none" WIDTH="100%" CELLSPACING="0" CELLPADDING="3">
	<TR ALIGN=CENTER style="border-bottom-style:solid">
		<TD><B CLASS="black" STYLE="font-size:8pt">Formula#:</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Seq#:</B></TD>
		<TD ALIGN=LEFT><B CLASS="black" STYLE="font-size:8pt">Ingredient Description</B></TD>
		<TD ALIGN=LEFT><B CLASS="black" STYLE="font-size:8pt">NorA</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">%age</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">FEMA#</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Formula Notes:</TD>
	</TR>

	<?php
	$total = 0;
	$result = mysql_query($sql, $link) or die (mysql_error());
	while ( $row = mysql_fetch_array($result) ) {
	?>
		<TR>
			<TD ALIGN=CENTER>
			<?php echo $row['ProductNumberExternal'];?>
			</TD>

			<TD Align="left">
			<?php echo $row['IngredientSEQ'] ?>
			</TD>
			

			<TD><?php
			if ( $row['Designation'] != '' ) {
				echo $row['Designation'] ."(Intl#:".$row['ProductNumberInternal'].")";
			} else {
				echo "&nbsp;";
			}
			?></TD>

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
			<TD><?php
				if ( $row['NoteForFormulation'] != '' ) {
					echo $row['NoteForFormulation'];
				} else {
					echo "&nbsp;";
				}
			?></TD>
			<?php } ?>
	
		</TR>
		<?php

	?>

</TABLE><BR>

</TD></TR></TABLE>

</BODY>
</HTML>