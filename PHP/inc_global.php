<?php
global $link;
$link = mysql_connect("localhost","abelei","abelei");
mysql_select_db("abelei",$link);
date_default_timezone_set('America/Chicago');

$text_newline = array("\r\n","\n","\r");
$html_newline = "<br />";

function check_field ($data, $case, $field) {

	global $error_found, $error_message;

	$data = trim($data);

	switch($case) {

		case 1:
			// TEXT FIELD
			if ( $data != "" ) {
				return true;
			} else {
				$error_found = true;
				$error_message .= "Please enter a value for '" . $field . "'<BR>";
				return false;
			}
			break;

		case 2:
			// E-MAIL ADDRESS
			$dataA=explode(",",$data); //multiple email addresses seperated with "," comma
			foreach($dataA as $data_e) { 
			$data_length = strlen($data_e);
			if ( $data_length > 70 or !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $data_e) or $data_e == "" ) {
				$error_found = true;
				$error_message .= "Invalid value entered for '" . $field . "'<BR>";
				return false;
			}
			}	
			return true;
			break;

		case 3:
			// NUMBERS, CURRENCY
			if ( !is_numeric($data) ) {
				$error_found = true;
				$error_message .= "Invalid value entered for '" . $field . "'<BR>";
				return false;
			} else {
				return true;
			}
			break;
	
	}

}


function escape_data ($data) {
	global $link;
	if ( get_magic_quotes_gpc() ) {
		$data = stripslashes($data);
	}
	return mysql_real_escape_string ( trim($data), $link );
}
function getFormSafe($text) {
		//$text = str_replace("\'", "'", $text);
		return stripslashes($text);
	}
function formatTxt($str){
	$stripAmp=str_replace ("&","&amp;", $str);
	$stripApos=str_replace ("'","&#x27;", $stripAmp);
	$stripQuote=str_replace ("\"","&quot;", $stripApos);
	$stripLessThan=str_replace ("<","&lt;", $stripQuote);
	$stripGreaterThan=str_replace (">","&gt;", $stripLessThan);
	$stripForwardSlash=str_replace ("/","&#x2F;", $stripGreaterThan);
	return $stripForwardSlash;
}
function QuantityConvert($quantity, $units_from, $units_to) {
//	echo "input-".$quantity."-".$units_from."-".$units_to."<br/>";
	if (is_numeric($quantity) && ("lbs"==$units_from || "kg"==$units_from || "grams"==$units_from ) && ( "lbs"==$units_to || "kg"==$units_to || "grams"==$units_to)) {
		switch ($units_from) {
		case "lbs": 
			if ("grams"==$units_to) {
				return ($quantity * 453.59237);
			} else if ("kg"==$units_to) {
				return ($quantity * .45359237);
			} else { 
				return $quantity;
			}
			break;
		case "grams" : 
			if ("lbs"==$units_to) {
				return ($quantity / 453.59237);
			} else if ("kg"==$units_to) {
				return ($quantity / 1000);
			} else {
				return $quantity;
			}
			break;
		case "kg": 
			if ("grams"==$units_to) {
				return ($quantity * 1000);
			} else if ("lbs"==$units_to) {
				return ($quantity / .45359237);
			} else {
				return $quantity;
			}
			break;
		}
	}
	return $quantity;
}
function start_transaction($link) {
	mysql_query("SET autocommit=0", $link);
	mysql_query("START TRANSACTION",$link);
	return null;
}

function end_transaction($ok_flag, $link) {
    global $link;
	if ($ok_flag)
		mysql_query("COMMIT",$link);
	else 
		mysql_query("ROLLBACK",$link);	
		
	mysql_query("SET autocommit=1", $link);
	
	return null;
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

function create_pqt_pdf_file($psn,$psn_string,$address_id,$email,$contactA, $cc) { //Create Price Quote PDF file for customer
    
    global $link;
	include("set_pdf.php");
    $pdf = set_abelei_pdf("abelei","abelei","abelei","abelei",0);
			
	if ( isset($psn_string) and $psn_string != '' ) {
		$psn_array = explode(",", $psn_string);
		$psn_clause = " AND PriceSheetNumber = " . $psn_array[0];
	} else {
		$psn_clause = " AND PriceSheetNumber = " . $psn;
	}

	//$sql = "SELECT * FROM pricesheetmaster WHERE PriceSheetNumber = " . $psn;
	//$result_analysis = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//$row_analysis = mysql_fetch_array($result_analysis);
	//$SellingPrice = number_format(round($row_analysis['SellingPrice'], 2), 2);
	$row_address="";
	if ( $address_id != "" ) {
	  $sql = "SELECT address1, address2, city, state, zip
		FROM customer_addresses
		WHERE address_id = '" . $address_id."'";
	    $result_address = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	    $row_address = mysql_fetch_array($result_address);
	}
	
	$email_clause = ( $email != "" ) ? " AND email1 = '" . $email . "'" : "";
	$contact_clause="";
	
	if ( isset($contactA) and $contactA != "") {
		$contact_array=explode("_",$contactA);
		$contact_clause=" AND customer_contacts.contact_id='".$contact_array[0]."' ";
	}
	
	$sql = "SELECT users.first_name AS fn, users.last_name AS ln, users.title AS users_title, 
		users.email AS users_email, productmaster.SpecificGravity AS SpecificGravityMaster, 
		pricesheetmaster.*, ProductNumberExternal, Designation, name, DatePriced, customer_contacts.* 
		FROM pricesheetmaster
		LEFT JOIN customers ON pricesheetmaster.CustomerID = customers.customer_id
		LEFT JOIN customer_contacts
		USING ( customer_id ) 
		LEFT JOIN users ON users.user_id = pricesheetmaster.Priced_ByEmployeeID
		INNER JOIN externalproductnumberreference
		USING ( ProductNumberInternal ) 
		INNER JOIN productmaster ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
		WHERE 1=1 " .$psn_clause . " AND customer_contacts.active = 1 AND customer_contacts.active = 1 
		" . $email_clause . $contact_clause;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// echo "<br /> $sql<BR><BR>";
	$row = mysql_fetch_array($result);
	$Priced_ByEmployeeID = $row['Priced_ByEmployeeID'];
	$IncludePricePerGallonInQuote = $row['IncludePricePerGallonInQuote'];
	//$SpecificGravity = round($row['SpecificGravityMaster'], 2);
	$Terms = $row['Terms'];
	$Packaged_In = $row['Packaged_In'];
	$MinBatch_Units = $row['MinBatch_Units'];
	$FOBLocation = $row['FOBLocation'];
	$html = '<BR><BR><BR><BR><BR><BR>' . date("F j, Y") . '<BR><BR>';

	$html .= $row['first_name'] . ' ' . $row['last_name'] . '<BR>';
	$html .= $row['name'] . '<BR>';
	$html .= $row_address['address1'] . '<BR>';
	if ( $row_address['address2'] != '' ) {
		$html .= $row_address['address2'] . '<BR>';
	}
	$html .= $row_address['city'] . ', ' . $row_address['state'] . ' ' . $row_address['zip'] . '<BR><BR>';

	$html .=  'Dear ' . $row['first_name'] . ':<BR><BR>';
	$html .= '<IMG SRC="images/abelei_font.png" HEIGHT=8> <B STYLE="color:#730099">flavors</B> is pleased to provide price quotes on the flavors below.<BR><BR>';
	$html .= '<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="510"><TR><TD WIDTH="80"><B STYLE="text-decoration:underline">Flavor</B></TD><TD WIDTH="270">&nbsp;</TD><TD WIDTH="80"><B STYLE="text-decoration:underline">Price $ / lb.</B></TD>';

	if ( $IncludePricePerGallonInQuote == 1 ) {
		$html .= '<TD><B STYLE="text-decoration:underline">Price $ / gal.</B></TD>';
	}

	$html .= '</TR>';

	if ( isset($psn_string) and $psn_string != '' ) {
		$psn_array = explode(",", $psn_string);
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
		$psn_clause = " AND PriceSheetNumber = " . $psn;
	}
	
	$sql = "SELECT PriceSheetNumber, pricesheetmaster.ProductNumberInternal, DatePriced, SellingPrice, 
		productmaster.SpecificGravity, externalproductnumberreference.ProductNumberExternal, 
		productmaster.Designation, productmaster.Kosher, productmaster.Natural_OR_Artificial, 
		productmaster.ProductType
		FROM pricesheetmaster
		LEFT JOIN externalproductnumberreference
		USING ( ProductNumberInternal ) 
		INNER JOIN productmaster ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
		WHERE 1=1 " . $psn_clause;
	$result_prods = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	while ( $row_prods = mysql_fetch_array($result_prods) ) {

		$ProductDesignation = ("" != $row_prods['Natural_OR_Artificial'] ? $row_prods['Natural_OR_Artificial']." " : "").$row_prods['Designation'].("" != $row_prods['ProductType'] ? " - ".$row_prods['ProductType'] : "").("" != $row_prods['Kosher'] ? " - ".$row_prods['Kosher'] : "");

		$html .= '<TR>';
		$html .= '<TD WIDTH="80">' . $row_prods['ProductNumberExternal'] . '</TD>';
		$html .= '<TD WIDTH="270">' . $ProductDesignation . '</TD>';
		$html .= '<TD WIDTH="80">' . number_format(round($row_prods['SellingPrice'], 2), 2) . '</TD>';
		if ( $IncludePricePerGallonInQuote == 1 ) {
			if ( $row_prods['SpecificGravity'] != 0 and $row_prods['SpecificGravity'] != '' ) {
				$PricePerGallon = number_format(($row_prods['SpecificGravity'] * $row_prods['SellingPrice']) * 8.34, 2);
			} else {
				$PricePerGallon = number_format(round(8.34 * $row_prods['SellingPrice'], 2), 2);
			}
			$html .= '<TD WIDTH="80">' . $PricePerGallon . '</TD>';
		}
		$html .= '</TR>';
	}


	$html .= '</TABLE>';

	$pdf->writeHTML($html, true, 0, true, 0);

	if ( $Packaged_In != '' ) {
		$packed_in_language = ' and packed in ' . str_replace("ail", "ails", $Packaged_In) . "s";
	} else {
		$packed_in_language = '';
	}

	$html = 'The prices above are based on minimums of ' . $MinBatch_Units . '  of flavor shipped F.O.B. ' . $FOBLocation . ' ' . str_replace("ss", "s", $packed_in_language) . '. Our payment terms are ' . $Terms . '.<BR><BR>';

	$html .= 'Unless you specify otherwise, <IMG SRC="images/abelei_font.png" HEIGHT=8> <B STYLE="color:#730099">flavors</B> will ship orders by what we consider the most reliable and affordable carriers with respect to your requested arrival date. In these cases freight charges will be prepaid and added to your invoice. If you have a freight carrier and billing procedure that you prefer, please let us know. We will do our very best to accommodate you.<BR><BR>';
	$html .= 'On behalf of my colleagues, I thank you for your interest in <IMG SRC="images/abelei_font.png" HEIGHT=8> <B STYLE="color:#730099">flavors</B>, the source of good taste.<BR><BR>';

	if ( $Priced_ByEmployeeID == 10 or $Priced_ByEmployeeID == 11 ) {
		$html .= 'With Best Regards,<BR><BR>';
		$html .= '<IMG SRC="images/signatures/' . $Priced_ByEmployeeID . '.png" HEIGHT=45>';
		$html .= '<BR><BR>';
	} else { //use Troy as the default signature
		$html .= 'With Best Regards,<BR><BR>';
		$html .= '<BR>';
		$html .= '<BR><BR>';
	}

	$html .= '<B>' . $row['fn'] . " " . $row['ln'] . '</B>';
	if ( $row['users_title'] != '' ) {
		$html .= ", <I>" . $row['users_title'] . "</I>";
	}
	$html .= "<BR>";
	$html .= '<IMG SRC="images/abelei_font.png" HEIGHT=8> <B STYLE="color:#730099">flavors</B><BR>';
	$html .= $row['users_email'] . "</B><BR>";
	$html .= 'www.abelei.com';
	
	if ( isset($cc) and $cc != '' ) {
		$html .= "<BR><BR>cc: " . $cc;
	}
	$pdf->writeHTML($html, true, 0, true, 0);

	// reset pointer to the last page 
	$pdf->lastPage();

	// --------------------------------------------------------- 

	//Close and output PDF document
	$file = str_replace(" ", "_", $row['name']) . "_pricing_quote_" . date("mdy");
	$pdf->Output('pdfs/' . $file . '.pdf', 'F');

	return "pdfs/".$file.".pdf";
	//============================================================+ 
	// END OF FILE                                                  
	//============================================================+ 
}
?>