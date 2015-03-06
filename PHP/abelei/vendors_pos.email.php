<?php

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

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( $_REQUEST['pon'] != '' ) {
	$pon = $_REQUEST['pon'];
} else {
	header ("Location: vendors_pos.php");
	exit;
}

if ( $_REQUEST['vid'] != '' ) {
	$vid = $_REQUEST['vid'];
}

include('inc_global.php');

$form_status = "";
if ( $_REQUEST['action'] != 'edit' ) {
	$form_status = "readonly=\"readonly\"";
}

if ( $_REQUEST['action'] != '' ) {
	$action = $_REQUEST['action'];
} else {
	$action = "";
}



if ( !empty($_POST) ) {

	$email = $_POST['email'];
	$address_id = $_POST['address_id'];
	$cc = $_POST['cc'];
	$subject = $_POST['subject'];
	$message = $_POST['message'];
	$signature = $_POST['signature'];

	// check_field() FUNCTION IN global.php
	check_field($email, 2, 'Contact e-mail');
	if ( $cc != '' ) {
		check_field($cc, 2, 'CC');
	}
	check_field($subject, 1, 'Subject');
	check_field($message, 1, 'Message');



	if ( !$error_found and $_POST['verified_1'] ) {

		//============================================================+ 
		// File name   : example_006.php 
		// Begin       : 2008-03-04 
		// Last Update : 2009-03-18 
		//  
		// Description : Example 006 for TCPDF class 
		//               WriteHTML and RTL support 
		//  
		// Author: Nicola Asuni 
		//  
		// (c) Copyright: 
		//               Nicola Asuni 
		//               Tecnick.com s.r.l. 
		//               Via Della Pace, 11 
		//               09044 Quartucciu (CA) 
		//               ITALY 
		//               www.tecnick.com 
		//               info@tecnick.com 
		//============================================================+ 

		/** 
		 * Creates an example PDF TEST document using TCPDF 
		 * @package com.tecnick.tcpdf 
		 * @abstract TCPDF - Example: WriteHTML and RTL support 
		 * @author Nicola Asuni 
		 * @copyright 2004-2009 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com 
		 * @link http://tcpdf.org 
		 * @license http://www.gnu.org/copyleft/lesser.html LGPL 
		 * @since 2008-03-04 
		 */ 

		require_once('tcpdf/config/lang/eng.php');
		require_once('tcpdf/tcpdf.php');

		// Extend the TCPDF class to create custom Header and Footer 
		class MYPDF extends TCPDF { 
		    //Page header 
		    public function Header() { 
		        // Logo
		        // function Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false) {
		        $this->Image('tcpdf/images/abelei_logo.png', 15, 8, 20);
		        // Set font 
		        $this->SetFont('helvetica', 'B', 10);
		        // Move to the right 
		        //$this->Cell(80);
		        // Title
		        // LINE BREAK
		        $this->Cell(0, 0, '', 0, 1);
		        // LINE BREAK
		        $this->Cell(0, 0, '', 0, 1);
		        // function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false) {
		        $this->Cell(0, 0, 'clever able capable', 0, 0, 'C', 2, 0, 0, 1);
		        $this->Image('tcpdf/images/abelei_font.png', 0, 19, 18, 0, 0, 0, 'C', 0, 300, 'C');
		        // Line break 
		        $this->Ln(20);
		    } 
		     
		    // Page footer 
		    public function Footer() { 
		        // Position at 1.5 cm from bottom 
		        $this->SetY(-20);
		        // Set font 
		        $this->SetFont('helvetica', 8);
		        // Page number 
		        $this->Cell(0, 10, '194 Alder Drive | North Aurora, Illinois 60542 | t-630.859.1410 | f-630.859.1448 | toll-free 866-4-abelei', 0, 0, 'C');
		        //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
		    } 
		} 

		// create new PDF document 
		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  

		// set document information 
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('abelei');
		$pdf->SetTitle('abelei');
		$pdf->SetSubject('abelei');
		$pdf->SetKeywords('abelei');

		// set default header data 
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

		// set header and footer fonts 
		//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins 
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks 
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor 
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  

		//set some language-dependent strings 
		$pdf->setLanguageArray($l);  

		// --------------------------------------------------------- 

		// set font 
		$pdf->SetFont('helvetica', '', 10);

		// add a page 
		$pdf->AddPage();

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
		$file = $VendorName . "-po";
		$pdf->Output('pdfs/' . $file . '.pdf', 'F');

		//============================================================+ 
		// END OF FILE                                                  
		//============================================================+ 

	}



	if ( !$error_found and $_POST['verified_1'] ) {

		$sql = "SELECT email 
		FROM users 
		WHERE user_id = " . $_SESSION['user_id'];
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);

		$from = $row['email'];

		// FOR $to, SWITCH TO $email WHEN LIVE!!!
		$to = "moconnell@chicagoit.com";

		$text = 'Message requires an HTML-compatible e-mail program.';
			
		// PEAR MAIL PACKAGES
		require_once('Mail.php');
		require_once('Mail/mime.php');
		$crlf = "\n";
		$mime = new Mail_Mime($crlf);

		// Set the email body
		$mime->setTXTBody($text);
		$mime->setHTMLBody( str_replace("\n", "<BR>", $message . "<BR><BR><BR>" . $signature));
		$mime->addAttachment('pdfs/' . $file . '.pdf','application/octet-stream');

		// Set the headers
		$mime->setFrom("$from");
		if ( $cc != '' ) {
			$mime->addCC("$cc");
		}
		$mime->setSubject("$subject");

		// Get the formatted code
		$body = $mime->get();
		$headers = $mime->headers();

		// Invoke the Mail class' factory() method
		$mail =& Mail::factory('mail');

		// Send the email
		$mail->send($to, $headers, $body);

		unlink("pdfs\\" . $file . ".pdf");
		$_SESSION['note'] = "Message successfully sent<BR>";
		header("location: vendors_pos.php");
		exit();
	}

} else {
	$message = '';
	$sql = "SELECT email 
	FROM users 
	WHERE user_id = " . $_SESSION['user_id'];
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$cc = $row['email'];
	$signature = "With Best Regards,\n\n<B>" . $_SESSION['first_nameCookie'] . " " . $_SESSION['last_nameCookie'] . "</B>\n<B STYLE='color:red'>abelei</B> <B STYLE='color:#730099'>flavors</B>\n194 Alder Drive\nNorth Aurora, IL  60542\n630-859-1410\nFax 630-859-1448\nToll Free 866-422-3534\n<A HREF='http://www.abelei.com'>www.abelei.com</A>";
}





include("inc_header.php");

?>


<?php if ( empty($_POST) ) {
	$subject = "Purchase Order from abelei flavors";
} ?>


<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>








<?php if ( !$error_found and !empty($_POST) and !$_POST['verified_0']) { ?>

	<B>Please verify your message</B><BR><BR>

	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
	<TABLE CELLPADDING=7 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="5"><TR VALIGN=TOP><TD>

	<FORM METHOD="post" ACTION="vendors_pos.email.php">
	<INPUT TYPE="hidden" NAME="pon" VALUE="<?php echo $pon;?>">
	<INPUT TYPE="hidden" NAME="vid" VALUE="<?php echo $vid;?>">
	<INPUT TYPE="hidden" NAME="email" VALUE="<?php echo stripslashes($email);?>">
	<INPUT TYPE="hidden" NAME="address_id" VALUE="<?php echo stripslashes($address_id);?>">
	<INPUT TYPE="hidden" NAME="cc" VALUE="<?php echo stripslashes($cc);?>">
	<INPUT TYPE="hidden" NAME="subject" VALUE="<?php echo stripslashes($subject);?>">
	<INPUT TYPE="hidden" NAME="message" VALUE="<?php echo stripslashes($message);?>">
	<INPUT TYPE="hidden" NAME="signature" VALUE="<?php echo stripslashes($signature);?>">

		<TR>
			<TD><B>Contact e-mail:</B></TD>
			<TD>
			<?php
			$sql = "SELECT first_name, last_name, email1 
			FROM vendor_contacts 
			WHERE email1 IS NOT NULL AND vendor_contacts.active = 1 AND vendor_id = " . $vid . " ORDER BY last_name";
			$result_contacts = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			while ( $row_contacts = mysql_fetch_array($result_contacts) ) {
				if ( $row_contacts['email1'] == $email ) {
					echo $row_contacts['first_name'] . " " . $row_contacts['last_name'] . " (" . $row_contacts['email1'] . ")";
				}
			}
			?>
			</TD>
		</TR>

		<TR>
			<TD><B>Contact address (for PDF):</B></TD>
			<TD>
			<?php
			$sql = "SELECT address_id, address1, address2, city, state, zip 
			FROM vendor_addresses 
			WHERE vendor_id = " . $vid . " ORDER BY state, city, zip";
			$result_addresses = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			while ( $row_addresses = mysql_fetch_array($result_addresses) ) {
				if ( $row_addresses['address_id'] == $address_id ) {
					echo $row_addresses['address1'] . " " . $row_addresses['address2'] . " " . $row_addresses['city'] . ", " . $row_addresses['state'] . " " . $row_addresses['zip'];
				}
			}
			?>
			</TD>
		</TR>

		<TR>
			<TD><B>CC:</B></TD>
			<TD><?php echo $cc;?></TD>
		</TR>

		<TR>
			<TD><B>Subject:</B></TD>
			<TD><?php echo $subject;?></TD>
		</TR>

		<TR>
			<TD COLSPAN=2><IMG SRC="images/spacer.gif" WIDTH=1 HEIGHT=7></TD>
		</TR>

		<TR VALIGN=TOP>
			<TD><B>Message:</B></TD>
			<TD BGCOLOR="white" WIDTH=400><?php echo str_replace("\n", "<BR>", $message . "<BR><BR><BR>" . $signature);?></TD>
		</TR>

		<TR>
			<TD></TD>
			<TD ALIGN=RIGHT><INPUT TYPE="submit" NAME="verified_0" VALUE="Edit message" CLASS='submit'> <INPUT TYPE="submit" NAME="verified_1" VALUE="Send message" CLASS='submit'></TD>
		</TR></FORM>

	</TABLE>

	</TD></TR></TABLE>
	</TD></TR></TABLE>





<?php } else { ?>



<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
<TABLE CELLPADDING=7 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">

<FORM ACTION="vendors_pos.email.php" METHOD="post">
	<INPUT TYPE="hidden" NAME="pon" VALUE="<?php echo $_REQUEST['pon'];?>">
	<INPUT TYPE="hidden" NAME="vid" VALUE="<?php echo $vid;?>">

	<TR>
		<TD><B>Contact e-mail:</B></TD>
		<TD>
		<?php
		$sql = "SELECT first_name, last_name, email1 
		FROM vendor_contacts 
		WHERE email1 IS NOT NULL AND vendor_contacts.active = 1 AND vendor_id = " . $vid . " ORDER BY last_name";
		$result_contacts = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( mysql_num_rows($result_contacts) > 0 ) {
			echo "<NOBR><SELECT NAME='email'>";
			while ( $row_contacts = mysql_fetch_array($result_contacts) ) {
				if ( $row_contacts['email1'] == $email ) {
					echo "<OPTION VALUE='" . $row_contacts['email1'] . "' SELECTED>" . $row_contacts['first_name'] . " " . $row_contacts['last_name'] . " (" . $row_contacts['email1'] . ")</OPTION>";
				} else {
					echo "<OPTION VALUE='" . $row_contacts['email1'] . "'>" . $row_contacts['first_name'] . " " . $row_contacts['last_name'] . " (" . $row_contacts['email1'] . ")</OPTION>";
				}
			}
			echo "</SELECT><BR>";
		}
		?>
		</TD>
	</TR>

	<TR>
		<TD><B>Contact address (for PDF):</B></TD>
		<TD>
		<?php
		$sql = "SELECT address_id, address1, address2, city, state, zip 
		FROM vendor_addresses 
		WHERE vendor_id = " . $vid . " ORDER BY state, city, zip";
		$result_addresses = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( mysql_num_rows($result_addresses) > 0 ) {
			echo "<SELECT NAME='address_id'>";
			while ( $row_addresses = mysql_fetch_array($result_addresses) ) {
				if ( $row_addresses['address_id'] == $address_id ) {
					echo "<OPTION VALUE='" . $row_addresses['address_id'] . "' SELECTED>" . $row_addresses['address1'] . " " . $row_addresses['address2'] . " " . $row_addresses['city'] . ", " . $row_addresses['state'] . " " . $row_addresses['zip'] . "</OPTION>";
				} else {
					echo "<OPTION VALUE='" . $row_addresses['address_id'] . "'>" . $row_addresses['address1'] . " " . $row_addresses['address2'] . " " . $row_addresses['city'] . ", " . $row_addresses['state'] . " " . $row_addresses['zip'] . "</OPTION>";
				}
			}
			echo "</SELECT><BR>";
		}
		?>
		</TD>
	</TR>

	<TR>
		<TD><B>CC:</B></TD>
		<TD><INPUT TYPE='text' NAME='cc' VALUE='<?php echo $cc;?>' STYLE='width:350px'></TD>
	</TR>

	<TR>
		<TD><B>Subject:</B></TD>
		<TD><INPUT TYPE='text' NAME='subject' VALUE='<?php echo $subject;?>' STYLE="width:350px"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B>Message:</B></TD>
		<TD><TEXTAREA NAME="message" ROWS="8" COLS="22" STYLE="width:350px"><?php echo $message;?></TEXTAREA></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD><B>Signature:</B></TD>
		<TD><TEXTAREA NAME="signature" ROWS="11" COLS="22" STYLE="width:350px"><?php echo $signature;?></TEXTAREA></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD>&nbsp;</TD>
		<TD ALIGN=RIGHT><INPUT TYPE='submit' VALUE='Preview message' CLASS='submit'> <INPUT TYPE="button" VALUE="Cancel" onClick="location.href='vendors_pos.php?pon=<?php echo $pon;?>'" CLASS="submit"></TD>
	</TR>

</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>

</FORM><RB>



<?php } ?>



<?php include("inc_footer.php"); ?>