<?php

//print product label - print_label.php

include("inc_global.php");
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


function create_pon_pdf_file($pon,$link) {
    global $link;
    include("set_pdf.php");
    $pdf = set_pdf("abelei","abelei","abelei","abelei");
    $sql = "SELECT * FROM purchaseordermaster WHERE PurchaseOrderNumber = " . $pon;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$PurchaseOrderType = $row['PurchaseOrderType'];
	$VendorID = $row['VendorID'];
	$VendorName = $row['VendorName'];
	$VendorStreetAddress1 = $row['VendorStreetAddress1'];
	$VendorStreetAddress2 = $row['VendorStreetAddress2'];
	$VendorCity = $row['VendorCity'];
	$VendorState = $row['VendorState'];
	$VendorZipCode = $row['VendorZipCode'];
	$VendorMainPhoneNumber = $row['VendorMainPhoneNumber'];
	$ShipToID = $row['ShipToID'];
	$ShipToName = $row['ShipToName'];
	$ShipToStreetAddress1 = $row['ShipToStreetAddress1'];
    $ShipToStreetAddress2 = $row['ShipToStreetAddress2'];
	$ShipToCity = $row['ShipToCity'];
	$ShipToState = $row['ShipToState'];
	$ShipToZipCode = $row['ShipToZipCode'];
	$ShipToMainPhoneNumber = $row['ShipToMainPhoneNumber'];
	$ShippingAndHandlingCost = $row['ShippingAndHandlingCost'];
	$PaymentType = $row['PaymentType'];

	if ( $row['ShippingDate'] != '' ) {
		$ShippingDate = date("m/d/Y", strtotime($row['ShippingDate']));
	} else {
		$ShippingDate = '';
	}

	$DateOrderPlaced = $row['DateOrderPlaced'];
	$ConfirmationOrderNumber = $row['ConfirmationOrderNumber'];
	$contact_id = $row['contact_id'];
	$VendorSalesRep = $row['VendorSalesRep'];
	$ShipVia = $row['ShipVia'];
	$Notes = $row['Notes'];

	$html = '<BR><H2>PURCHASE ORDER #' . $pon . '</H2><BR>';

	$html .= '<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">';
	$html .= '<TR VALIGN=TOP>';
	$html .= '<TD>';
	$html .= '<H2>Vendor</H2>';
	$html .= '<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BGCOLOR="white">';
	$html .= '<TR>';
	$html .= '<TD STYLE="text-align:right" WIDTH=60><B>Name:</B></TD>';
	$html .= '<TD WIDTH=195>' . stripslashes($VendorName) . '</TD>';
	$html .= '</TR>';
	$html .= '<TR>';
	$html .= '<TD STYLE="text-align:right" WIDTH=60><B>Address:</B></TD>';
	$html .= '<TD WIDTH=195>' . $VendorStreetAddress1 . '</TD>';
	$html .= '</TR>';
	if ( $VendorStreetAddress2 != "" ) {
		$html .= '<TR>';
    	$html .= '<TD STYLE="text-align:right" WIDTH=60>&nbsp;</TD>';
		$html .= '<TD WIDTH=195>' . $VendorStreetAddress2 . '</TD>';
		$html .= '</TR>';
	}
	$html .= '<TR>';
	$html .= '<TD STYLE="text-align:right" WIDTH=60><B>City:</B></TD>';
	$html .= '<TD WIDTH=195>' . $VendorCity . ', ' . $VendorState . ' ' . $VendorZipCode;
	$html .= '</TD>';
	$html .= '</TR>';
	$html .= '<TR>';
	$html .= '<TD STYLE="text-align:right" WIDTH=60><B>Phone:</B></TD>';
	$html .= '<TD WIDTH=195>' . $VendorMainPhoneNumber . '</TD>';
	$html .= '</TR>';
	$html .= '</TABLE>';
	$html .= '</TD>';
	$html .= '<TD>';
	$html .= '<H2>Ship To</H2>';
	$html .= '<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BGCOLOR="white">';
	$html .= '<TR>';
	$html .= '<TD STYLE="text-align:right" WIDTH=60><B>Name:</B></TD>';
	$html .= '<TD WIDTH=195>' . $ShipToName . '</TD>';
	$html .= '</TR>';
	$html .= '<TR>';
	$html .= '<TD STYLE="text-align:right" WIDTH=60><B>Address:</B></TD>';
	$html .= '<TD WIDTH=195>' . $ShipToStreetAddress1 . '</TD>';
	$html .= '</TR>';
	if ( $VendorStreetAddress2 != '' ) {
		$html .= '<TR>';
		$html .= '<TD STYLE="text-align:right" WIDTH=60>&nbsp;</TD>';
		$html .= '<TD WIDTH=195>' . $ShipToStreetAddress2 . '</TD>';
		$html .= '</TR>';
	}
	$html .= '<TR>';
	$html .= '<TD STYLE="text-align:right" WIDTH=60><B>City:</B></TD>';
	$html .= '<TD WIDTH=195>' . $ShipToCity . ', ' . $ShipToState . ' ' . $ShipToZipCode . '</TD>';
	$html .= '</TR>';
	$html .= '<TR>';
	$html .= '<TD STYLE="text-align:right" WIDTH=60><B>Phone:</B></TD>';
	$html .= '<TD WIDTH=195>' . $ShipToMainPhoneNumber . '</TD>';
	$html .= '</TR>';
	$html .= '</TABLE>';
	$html .= '</TD>';
	$html .= '</TR>';
	$html .= '</TABLE><BR>';

	$pdf->writeHTML($html, true, 0, true, 0);

	$sql = "SELECT purchaseorderdetail . *, productmaster.Designation, productmaster.Natural_OR_Artificial, productmaster.Kosher
    	FROM purchaseorderdetail
		LEFT JOIN productmaster
		USING ( ProductNumberInternal ) 
		WHERE PurchaseOrderNumber = '" . $pon . "'
		ORDER BY PurchaseOrderSeqNumber";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	if ( $c > 0 ) {

		//$html = '<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="0">';
		//$html .= '<TR>';
		//$html .= '<TD>';

		$html = '<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3" BORDERCOLOR="#CDCDCD">';
	
		$html .= '<TR ALIGN="CENTER">';
		$html .= '<TD WIDTH="25"><B>Qty</B></TD>';
		$html .= '<TD WIDTH="65"><B>Pack size</B></TD>';
		$html .= '<TD WIDTH="35"><B>Units</B></TD>';
		$html .= '<TD WIDTH="235"><B>Description</B></TD>';
		$html .= '<TD WIDTH="100" STYLE="text-align:right"><B>Price</B></TD>';
		$html .= '<TD WIDTH="50" STYLE="text-align:right"><B>Total</B></TD>';
		$html .= '</TR>';

		$total = 0;
		while ( $row = mysql_fetch_array($result) ) {
			$subtotal = QuantityConvert($row['TotalQuantityExpected'], $row['UnitOfMeasure'], "lbs") * $row['UnitPrice'];
			$total = $total + $subtotal;
			$html .= '<TR ALIGN="CENTER">';
			$html .= '<TD WIDTH="25">' . $row['Quantity'] . '</TD>';
			$html .= '<TD WIDTH="65">' . $row['PackSize'] . '</TD>';
			$html .= '<TD WIDTH="35">' . $row['UnitOfMeasure'] . '</TD>';
			if ( $row['Kosher'] != '' ) {
				$kosher_info = $row['Kosher'] . " ";
			} else {
				$kosher_info = "";
			}
			$html .= '<TD WIDTH="235">#' . $row['VendorProductCode'] . ' ' . $row['Natural_OR_Artificial'] . ' ' . $kosher_info . $row['Designation'] . ' - ' . $row['ProductNumberInternal'] . '</TD>';
			//$html .= '<TD WIDTH="235">' . $row['Description'] . '</TD>';
			$html .= '<TD WIDTH="100" STYLE="text-align:right">' . $row['UnitPrice'] . '</TD>';
			$html .= '<TD WIDTH="50" STYLE="text-align:right">$' . number_format($subtotal, 2) . '</TD>';
			$html .= '</TR>';
		}

		$html .= '<TR ALIGN="CENTER">';
		$html .= '<TD WIDTH="360" ROWSPAN="3" COLSPAN="4">';
		$html .= '<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" BGCOLOR="#FFFFFF">';
		$html .= '<TR>';
		$html .= '<TD ALIGN="RIGHT" WIDTH="90">&nbsp;</TD>';
		$html .= '<TD ALIGN="LEFT" WIDTH="60">&nbsp;</TD>';
		$html .= '</TR>';
		$html .= '<TR>';
		$html .= '<TD ALIGN="RIGHT" WIDTH="90"><B>Payment details:</B></TD>';
		$html .= '<TD ALIGN="LEFT" WIDTH="60">' . $PaymentType . '</TD>';
		$html .= '</TR>';
		$html .= '<TR>';
		$html .= '<TD ALIGN="RIGHT" WIDTH="90"><B>Shipping date:</B></TD>';
		$html .= '<TD ALIGN="LEFT" WIDTH="60">' . $ShippingDate . '</TD>';
		$html .= '</TR>';
		$html .= '</TABLE>';
		$html .= '</TD>';
		$html .= '<TD WIDTH="100" STYLE="text-align:right">Sub total:</TD>';
		$html .= '<TD WIDTH="50" STYLE="text-align:right">$' . number_format($total, 2) . '</TD>';
		$html .= '</TR>';
		$html .= '<TR ALIGN="CENTER">';
		$html .= '<TD WIDTH="100" STYLE="text-align:right">Shipping & handling:</TD>';
		$html .= '<TD WIDTH="50" STYLE="text-align:right">$' . number_format($ShippingAndHandlingCost, 2) . '</TD>';
		$html .= '</TR>';

		$html .= '<TR ALIGN="CENTER">';
		$html .= '<TD WIDTH="100" STYLE="text-align:right"><B>Total:</B></TD>';
		$html .= '<TD WIDTH="50" STYLE="text-align:right"><B>$' . number_format($total + $ShippingAndHandlingCost, 2) . '</B></TD>';
		$html .= '</TR>';

		$html .= '</TABLE>';

	}

	$pdf->writeHTML($html, true, 0, true, 0);

	$html = '<BR><HR NOSHADE SIZE=4 COLOR="#CDCDCD"><BR>';
	$html .= '<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">';
	$html .= '<TR>';
	$html .= '<TD WIDTH="60"><B>Date:</B></TD>';
	$html .= '<TD WIDTH="130">' . date("n/j/Y", strtotime($DateOrderPlaced)) . '</TD>';
	$html .= '</TR>';
	$html .= '<TR>';
	$html .= '<TD WIDTH="60"><B>Order#:</B></TD>';
	$html .= '<TD WIDTH="130">' . $ConfirmationOrderNumber . '</TD>';
	$html .= '</TR>';
	$html .= '<TR>';
	$html .= '<TD WIDTH="60"><B>Sales rep:</B></TD>';
	$html .= '<TD WIDTH="130">' . $VendorSalesRep . '</TD>';
	$html .= '</TR>';
	$html .= '<TR>';
	$html .= '<TD WIDTH="60"><B>Ship via:</B></TD>';
	$html .= '<TD WIDTH="130">' . $ShipVia . '</TD>';
	$html .= '</TR>';
	$html .= '<TR>';
	$html .= '<TD COLSPAN=2>&nbsp;</TD>';
	$html .= '</TR>';
	$html .= '<TR>';
	$html .= '<TD COLSPAN=2><B>Notes/remarks: </B>' . $Notes . '</TD>';
	$html .= '</TR>';
	$html .= '</TABLE>';

	$pdf->writeHTML($html, true, 0, true, 0);

// reset pointer to the last page 
	$pdf->lastPage();

	// --------------------------------------------------------- 
//Close and output PDF document
	$file = uniqid();
	$file .= "-po";
	$pdf->Output('pdfs/' . $file . '.pdf', 'F');
        
    return "pdfs/".$file.".pdf";

//============================================================+ 
// END OF FILE                                                  
//===========================================================+ 
}