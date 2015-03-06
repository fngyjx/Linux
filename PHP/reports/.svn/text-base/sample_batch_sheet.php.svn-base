<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

include('../inc_global.php');

$sql = "SELECT * FROM sample_batchsheets WHERE sample_batchsheet_number = " . $_GET['sbsn'];
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
$created_by = $row['created_by'];
$date = date("n/d/Y", strtotime($row['Date'] . " 00:00:00"));
$contact_name = $row['contact'];
$customer_id = $row['customer_id'];
$amount = $row['amount'];
$unit = $row['unit'];
$pne = $row['abelei_number'];

$sql = "SELECT name FROM customers WHERE customer_id = " . $customer_id;
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
$customer = $row['name'];

//$sql = "SELECT first_name, last_name FROM customer_contacts WHERE contact_id = " . $contact_id;
//$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
//$row = mysql_fetch_array($result);
//$contact_name = $row['first_name'] . " " . $row['last_name'];

$sql = "SELECT ProductNumberInternal FROM externalproductnumberreference WHERE ProductNumberExternal = '" . escape_data($pne) . "'";
$result_pni = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row_pni = mysql_fetch_array($result_pni);
$pni = $row_pni['ProductNumberInternal'];

$sql = "SELECT * FROM productmaster WHERE ProductNumberInternal = '" . escape_data($pni) . "'";
$result_des = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row_des = mysql_fetch_array($result_des);
$ProductDesignation = ("" != $row_des['Natural_OR_Artificial'] ? $row_des['Natural_OR_Artificial']." " : "").$row_des['Designation'].("" != $row_des['ProductType'] ? " - ".$row_des['ProductType'] : "").("" != $row_des['Kosher'] ? " - ".$row_des['Kosher'] : "");
$specific_gravity = $row_des[SpecificGravity];
$NoteForFormulation = $row_des['NoteForFormulation'];

$sql = "SELECT formulationdetail.ProductNumberInternal, formulationdetail.IngredientSEQ, formulationdetail.IngredientProductNumber, formulationdetail.Percentage, productmaster.Kosher, productmaster.Organic, productmaster.Designation, productmaster.FEMA_NBR, Natural_OR_Artificial, ProductType, Kosher
FROM formulationdetail
LEFT JOIN productmaster ON formulationdetail.IngredientProductNumber = productmaster.ProductNumberInternal
WHERE formulationdetail.ProductNumberInternal = '" . $pni . "'";
$result = mysql_query($sql, $link) or die (mysql_error());
$row = mysql_fetch_array($result);



//	$NetWeight = $row['NetWeight'];
//	$Column1UnitType = $row['Column1UnitType'];
//	$Column2UnitType = $row['Column2UnitType'];

	$TotalQuantityUnitType = "lbs";
	$Yield = 0.98;

	if ("gal" == $unit) {
		$amount *= 8.35 * $specific_gravity;
		$unit = "lbs";
	}
	
	$lbs_weight = QuantityConvert($amount,$unit,'lbs');
	$gross_weight = ( empty($lbs_weight) || empty($Yield) ) ? 0 : $lbs_weight/($Yield/100)/100;



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

<B CLASS="header">Sample Batch Sheet</B><BR><BR>

<!-- Internal #: <?php //echo $pni;?><BR> -->
<?php echo $ProductDesignation;?> (abelei# <?php echo $pne;?>)<BR><BR>


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
	<TR>
		<TD><B CLASS="black">Created by:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT="1" BORDER="0"></TD>
		<TD><?php echo $created_by;?></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Date:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT="1" BORDER="0"></TD>
		<TD><?php echo $date;?></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Customer:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT="1" BORDER="0"></TD>
		<TD><?php echo $customer;?></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Contact:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT="1" BORDER="0"></TD>
		<TD><?php echo $contact_name;?></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Amount:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT="1" BORDER="0"></TD>
		<TD><?php echo $amount;?></TD>
	</TR>
	<TR>
		<TD><B CLASS="black">Unit:</B></TD>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT="1" BORDER="0"></TD>
		<TD><?php echo $unit;?></TD>
	</TR>
</TABLE><BR>



<TABLE BORDER="1" WIDTH="100%" CELLSPACING="0" CELLPADDING="3" BORDERCOLOR="#CDCDCD">
	<TR ALIGN=CENTER>
		<TD><B CLASS="black" STYLE="font-size:8pt">Seq#</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Internal#</B></TD>
		<TD ALIGN=LEFT><B CLASS="black" STYLE="font-size:8pt">Ingredient Description</B></TD>
		<TD ALIGN=LEFT><B CLASS="black" STYLE="font-size:8pt">Natural<BR>and artificial</B></TD>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">%</B></TD>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Amt (lbs)</B></TD>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Amt (grams)</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">FEMA#</B></TD>
	</TR>

	<?php
	$total = 0;
	$result = mysql_query($sql, $link) or die (mysql_error());
	$lbs_total = 0;
	$grams_total = 0;
	while ( $row = mysql_fetch_array($result) ) {
	?>
		<TR>

			<?php
			if ( substr($row['IngredientProductNumber'], 0, 1) == 4 ) {
				$td_bgcolor = "#999999";
				$font_color = "color:#FFFFFF;font-weight:bold";
				$colspan = "COLSPAN=6";
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
			
			echo "<SPAN STYLE='" . $font_color . "'>" . $row['IngredientProductNumber'] . $abelei_number . "</SPAN>";
			?>
			</TD>

			<TD BGCOLOR="<?php echo $td_bgcolor;?>" <?php echo $colspan;?>><?php

			if ( $row['Kosher'] != '' ) {
				$kosher_info = $row['Kosher'] . " ";
			} else {
				$kosher_info = "";
			}

			if ( $row['Organic'] != 0 ) {
				$organic_info = "Organic ";
			} else {
				$organic_info = "";
			}

			if ( $row['Designation'] != '' ) {
				echo "<SPAN STYLE='" . $font_color . "'>" . $organic_info . $kosher_info . $row['Designation'] . "</SPAN>";
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

				<?php
				$BatchAmtLbs = ($row['Percentage']/100) * $gross_weight;
				$BatchAmtG = QuantityConvert($gross_weight*($row['Percentage']/100),$TotalQuantityUnitType,"grams");
				$lbs_total = $lbs_total + ($row['Percentage']/100) * $gross_weight;
				$grams_total = $grams_total + QuantityConvert($gross_weight*($row['Percentage']/100),$TotalQuantityUnitType,"grams");
				?>

				<TD ALIGN=RIGHT><?php echo number_format($BatchAmtLbs, 2); ?></TD>
				<TD ALIGN=RIGHT><?php echo number_format($BatchAmtG, 2); ?></TD>
	
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
		<TD ALIGN=RIGHT><?php echo number_format($lbs_total, 2) ;?></TD>
		<TD ALIGN=RIGHT><?php echo number_format($grams_total, 2) ;?></TD>
		<TD>&nbsp;</TD>
	</TR>
</TABLE><BR>

<B CLASS='black'>Notes:</B> <?php echo $NoteForFormulation;?>

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