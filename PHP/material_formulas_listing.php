<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 and $rights != 6) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

$material = $_REQUEST['pni'];
if ( empty($material) ) {
	$_SESSION['note']= "The Internal# is must be privided";
	echo "<script>window.opener.location.reload();windoe.close();</sctipt>";
	exit();
}

include('inc_global.php');
// print_r($_REQUEST);

$sql="SELECT DISTINCT productmaster.Organic, productmaster.Natural_OR_Artificial, productmaster.Designation, productmaster.ProductType, 
 productmaster.Kosher,externalproductnumberreference.ProductNumberExternal
 FROM productmaster LEFT JOIN externalproductnumberreference ON
 externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal 
 WHERE productmaster.ProductNumberInternal='". $material."'";
 
// echo "<br />$sql<br />";
 $result=mysql_query($sql,$link) or die( mysql_error() . " Failed Execute SQL <br >$sql<br />");
 $row=mysql_fetch_array($result);
 
$materialDescription= ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").
$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "")
. " ".$row['ProductNumberExternal'];

$sql = "SELECT DISTINCT productmaster.ProductNumberInternal, productmaster.UnitOfMeasure,
 productmaster.Organic, productmaster.Natural_OR_Artificial, productmaster.Designation, productmaster.ProductType, 
 productmaster.Kosher,externalproductnumberreference.ProductNumberExternal, productmaster.NoteForFormulation, formulationdetail.IngredientSEQ,
 formulationdetail.Percentage
 FROM productmaster LEFT JOIN externalproductnumberreference ON
 externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal 
 LEFT JOIN formulationdetail ON productmaster.ProductNumberInternal=formulationdetail.ProductNumberInternal
 WHERE formulationdetail.IngredientProductNumber='".$material."'
 GROUP BY productmaster.ProductNUmberInternal
 ORDER BY productmaster.Designation, productmaster.ProductNumberInternal";
 
 $result=mysql_query($sql,$link) or die ( mysql_error(). " Failed Execute SQL: $sql <br />");
 
 include("inc_header.php");
 ?>
 
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

<h5> Formulas That Use Material <B><?php echo $material . " ( ". html_entity_decode($materialDescription) . " ) ";?></B></h5>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" width="100%">
	<TR VALIGN=BOTTOM>
	<TD>&nbsp;</TD>
	<TD><B>Internal Number</B></TD>
	<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
	<TD><B>Abelei Number (External)</B></TD>
	<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
	<TD><B>Description</B></TD>
	<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
	<TD><B>Seq #<br />(In Total Ingredients):</B></TD>
	<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
	<TD><B>%age:</B></TD>
	<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
	<TD><B>Formula<br />Notes</B></TD>
</TR>

<TR>
	<TD COLSPAN='16'><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
</TR>

<?php 
	$i = 1;
	while ( $row = mysql_fetch_array($result) ) {
	$description = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
	$sql="SELECT MAX(IngredientSEQ) FROM formulationdetail WHERE ProductNumberInternal='".$row['ProductNumberInternal']."'";
	$result_total=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
	$row_total=mysql_fetch_array($result_total);
	if ( $bg == 1 ) {
		$bgcolor = "#F3E7FD";
		$bg = 0;
	} else {
		$bgcolor = "whitesmoke";
		$bg = 1;
	} ?>

	<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
	<TD>
	<A href="flavors_formulations.php?action=edit&pne=<?php echo $row['ProductNumberExternal']; ?>" target="_blank"><IMG src="images/zoom.png"></A>
	</TD>
	<TD style="font-size:9px;"><?php echo $row['ProductNumberInternal'] ?></TD>
	<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
	<TD style="font-size:9px;"><?php echo $row['ProductNumberExternal'] ?></TD>
	<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
	<TD style="font-size:9px;"><NOBR><?php echo $description ?></NOBR></TD>
	<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
	<TD><?php echo number_format($row['IngredientSEQ'],0)."(".number_format($row_total[0],0).")";?></TD>
	<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
	<TD style="font-size:9px;"><?php echo number_format($row['Percentage'],2) ?></TD>
	<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
	<TD style="border-bottom:1px solid black;"><?php echo $row['NoteForFormulation'];?>&nbsp;</TD>
	</TR>
<?php } ?>
	</TABLE>
		
<?php include("inc_footer.php"); ?>