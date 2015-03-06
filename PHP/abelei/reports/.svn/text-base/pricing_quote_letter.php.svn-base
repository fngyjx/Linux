<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

include('../inc_global.php');

?>



<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="600" ALIGN=CENTER><TR VALIGN=TOP><TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0"> <!--  WIDTH="100%"-->
	<TR VALIGN=MIDDLE>
		<TD WIDTH="120" ALIGN=CENTER><IMG SRC="../images/abelei_logo.png" WIDTH="120" BORDER="0"><!-- </TD>
		<TD WIDTH="10"><IMG SRC="../images/spacer.gif" WIDTH="10" HEIGHT=1 BORDER="0"></TD>
		<TD WIDTH="80" VALIGN=MIDDLE STYLE="font-size:8pt"><NOBR>clever able capable</NOBR><BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"> -->
		<BR><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT=7 BORDER="0"><BR>
		<IMG SRC="../images/abelei_font.png" WIDTH="80" BORDER="0"></TD>
	</TR>
</TABLE><BR><BR>

<TABLE BORDER="0" HEIGHT="650" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>



<?php

if ( $_REQUEST['psn_string'] != '' ) {
	$psn_array = explode(",", $_REQUEST['psn_string']);
	$psn_clause = " AND PriceSheetNumber = " . $psn_array[0];
} else {
	$psn_clause = " AND PriceSheetNumber = " . $_REQUEST['psn'];
}


// RECORD WHO LETTER WAS PRINTED FOR
// ADDED 10/5/2009
if ( $_REQUEST['psn'] != '' and $_REQUEST['reprint'] != 1 ) {
	$sql = "INSERT into price_quote_letters (pricesheet_number, address_id, contact_name) VALUES (" . $_REQUEST['psn'] . ", " . $_REQUEST['address_id'] . ", '" . $_REQUEST['contact_name'] . "')";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
}


$sql = "SELECT users.first_name, users.last_name, users.title, users.email, productmaster.SpecificGravity AS SpecificGravityMaster, pricesheetmaster.*, ProductNumberExternal, Designation, name, DatePriced, customer_addresses.* 
FROM pricesheetmaster
LEFT JOIN customers ON pricesheetmaster.CustomerID = customers.customer_id
LEFT JOIN customer_addresses
USING ( customer_id ) 
LEFT JOIN users ON users.user_id = pricesheetmaster.Priced_ByEmployeeID
INNER JOIN externalproductnumberreference
USING ( ProductNumberInternal ) 
INNER JOIN productmaster ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
WHERE 1=1 " .$psn_clause . " AND address_id = " . $_REQUEST['address_id'];
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$row = mysql_fetch_array($result);
//echo $sql . "<BR>";

//$SellingPrice = number_format(round($row['SellingPrice'], 2), 2);
$IncludePricePerGallonInQuote = $row['IncludePricePerGallonInQuote'];
//$SalesPersonEmployeeID = $row['SalesPersonEmployeeID'];
//$SpecificGravity = round($row['SpecificGravityMaster'], 2);
$Priced_ByEmployeeID = $row['Priced_ByEmployeeID'];
//$ProcessType = $row['ProcessType'];
$Terms = $row['Terms'];
$Packaged_In = $row['Packaged_In'];
//$MinBatch = number_format($row['MinBatch'], 2);
$MinBatch_Units = $row['MinBatch_Units'];
$FOBLocation = $row['FOBLocation'];
//$DatePriced = $row['DatePriced'];

$message = date("M j, Y") . "<BR><BR>";

$message .= $_REQUEST['contact_name'] . "<BR>";
$message .= $row['name'] . "<BR>";
$message .= $row['address1'] . "<BR>";
if ( $row['address2'] != '' ) {
	$message .= $row['address2'] . "<BR>";
}
$message .= $row['city'] . ", " . $row['state'] . " " . $row['zip'] . "<BR><BR>";

$first_name_parts = explode(" " , $_REQUEST['contact_name']);
$message .=  "Dear " . $first_name_parts[0] . ":<BR><BR>";

$message .= "<B STYLE='color:red'>abelei</B> is pleased to provide price quotes on the flavors below.<BR><BR>";

$message .= "<TABLE BORDER='0' CELLSPACING='0' CELLPADDING='0'><TR>";
$message .= "<TD><B STYLE='text-decoration:underline'>Flavor</B></TD>";
$message .= "<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
$message .= "<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
$message .= "<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
$message .= "<TD ALIGN=RIGHT><B STYLE='text-decoration:underline'>Price $ / lb.</B></TD>";
if ( $IncludePricePerGallonInQuote == 1 ) {
	$message .= "<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
	$message .= "<TD><B STYLE='text-decoration:underline'>Price $ / gal.</B></TD>";
}
$message .= "</TR>";


if ( $_REQUEST['psn_string'] != '' ) {
	$psn_array = explode(",", $_REQUEST['psn_string']);
	$psn_clause = " AND (";
	$i = 0;
	foreach ( $psn_array as $psn ) {
		if ( $i != 0 ) {
			$psn_clause .= " OR PriceSheetNumber = " . $psn;
		} else {
			$psn_clause .= " PriceSheetNumber = " . $psn;
		}
		$i++;
	}
	$psn_clause .= ") ";
} else {
	$psn_clause = " AND PriceSheetNumber = " . $_REQUEST['psn'];
}
	
$sql = "SELECT PriceSheetNumber, pricesheetmaster.ProductNumberInternal, DatePriced, SellingPrice, productmaster.SpecificGravity, externalproductnumberreference.ProductNumberExternal, productmaster.Designation, productmaster.Kosher, productmaster.Natural_OR_Artificial, productmaster.ProductType
FROM pricesheetmaster
LEFT JOIN externalproductnumberreference
USING ( ProductNumberInternal ) 
INNER JOIN productmaster ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
WHERE 1=1 " . $psn_clause;
$result_prods = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
while ( $row_prods = mysql_fetch_array($result_prods) ) {

	$ProductDesignation = ("" != $row_prods['Natural_OR_Artificial'] ? $row_prods['Natural_OR_Artificial']." " : "").$row_prods['Designation'].("" != $row_prods['ProductType'] ? " - ".$row_prods['ProductType'] : "").("" != $row_prods['Kosher'] ? " - ".$row_prods['Kosher'] : "");

	$message .= "<TR>";
	$message .= "<TD>" . $row_prods['ProductNumberExternal'] . "</TD>";
	$message .= "<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
	$message .= "<TD>" . $ProductDesignation . "</TD>";
	$message .= "<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
	$message .= "<TD ALIGN=RIGHT>" . number_format(round($row_prods['SellingPrice'], 2), 2) . "</TD>";

	if ( $IncludePricePerGallonInQuote == 1 ) {
		$message .= "<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
		if ( $row_prods['SpecificGravity'] != 0 and $row_prods['SpecificGravity'] != '' ) {
			$PricePerGallon = number_format(($row_prods['SpecificGravity'] * $row_prods['SellingPrice']) * 8.34, 2);
		} else {
			$PricePerGallon = number_format(round(8.34 * $row_prods['SellingPrice'], 2), 2);
		}
		$message .= "<TD>" . $PricePerGallon . "</TD>";
	}

	$message .= "</TR>";

}



$message .= "</TABLE><BR>";

if ( $Packaged_In != '' ) {
	$packed_in_language = ' and packed in ' . str_replace("ail", "ails", $Packaged_In) . "s";
} else {
	$packed_in_language = '';
}

$message .= "The prices above are based on minimums of " . $MinBatch_Units . "  of flavor shipped F.O.B. " . $FOBLocation . " " . str_replace("ss", "s", $packed_in_language) . ". Our payment terms are " . $Terms . ".<BR><BR>";

$message .= "Unless you specify otherwise, <B STYLE='color:red'>abelei</B> will ship orders by what we consider the most reliable and affordable carriers with respect to your requested arrival date. In these cases freight charges will be prepaid and added to your invoice. If you have a freight carrier and billing procedure that you prefer, please let us know. We will do our very best to accommodate you.<BR><BR>";

$message .= "On behalf of my colleagues, I thank you for your interest in <B STYLE='color:red'>abelei</B> flavors, the source of good taste.<BR><BR>";

if ( $Priced_ByEmployeeID == 10 or $Priced_ByEmployeeID == 11 ) {
	$message .= "With Best Regards,<BR><BR>";
	$message .= "<IMG SRC='../images/signatures/" . $Priced_ByEmployeeID . ".png' HEIGHT=75>";
	$message .= "<BR><BR>";
} else {
	$message .= "With Best Regards,<BR><BR><BR><BR><BR>";
}

$message .= "<B>" . $row['first_name'] . " " . $row['last_name'] . "</B>";

if ( $row['title'] != '' ) {
	$message .= ", <I>" . $row['title'] . "</I>";
}
$message .= "<BR>";
$message .= "<B STYLE='color:red'>abelei</B><BR>";
$message .= $row['email'] . "</B><BR>";
$message .= "www.abelei.com<BR><BR>";

if ( $_REQUEST['cc'] != '' ) {
	$message .= "cc: " . $_REQUEST['cc'];
}

echo $message;

?>



</TD></TR></TABLE>


<SPAN STYLE="font-size:8pt">
194 Alder Drive |
North Aurora, Illinois 60542 |
t-630.859.1410 |
f-630.859.1448 |
<SPAN STYLE="color: blue">toll-free 866-4-abelei</SPAN>
</SPAN>

</TD></TR></TABLE>

<?php if ( $_REQUEST['psn'] != '' and $_REQUEST['reprint'] != 1 ) { ?>

	<SCRIPT>
		window.opener.location.reload();
	</SCRIPT>

<?php } ?>

</BODY>
</HTML>