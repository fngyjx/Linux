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

if ( $_REQUEST['psn'] != '' ) {
	$psn = $_REQUEST['psn'];
} elseif ( $_REQUEST['psn_string'] != '' ) {
	$psn_string = $_REQUEST['psn_string'];
} else {
	header ("Location: customers_quotes.php");
	exit;
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
	//if ( $cc != '' ) {
	//	check_field($cc, 2, 'CC');
	//}
	check_field($subject, 1, 'Subject');
	check_field($message, 1, 'Message');



	if ( !$error_found and ($_POST['verified_1'] or $_POST['verified_1_test']) ) {

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

		if ( $_REQUEST['psn_string'] != '' ) {
			$psn_array = explode(",", $_REQUEST['psn_string']);
			$psn_clause = " AND PriceSheetNumber = " . $psn_array[0];
		} else {
			$psn_clause = " AND PriceSheetNumber = " . $psn;
		}

		//$sql = "SELECT * FROM pricesheetmaster WHERE PriceSheetNumber = " . $psn;
		//$result_analysis = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//$row_analysis = mysql_fetch_array($result_analysis);
		//$SellingPrice = number_format(round($row_analysis['SellingPrice'], 2), 2);

		$sql = "SELECT address1, address2, city, state, zip
		FROM customer_addresses
		WHERE address_id = " . $address_id;
		$result_address = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row_address = mysql_fetch_array($result_address);

		$sql = "SELECT users.first_name AS fn, users.last_name AS ln, users.title AS users_title, users.email AS users_email, productmaster.SpecificGravity AS SpecificGravityMaster, pricesheetmaster.*, ProductNumberExternal, Designation, name, DatePriced, customer_contacts.* 
		FROM pricesheetmaster
		LEFT JOIN customers ON pricesheetmaster.CustomerID = customers.customer_id
		LEFT JOIN customer_contacts
		USING ( customer_id ) 
		LEFT JOIN users ON users.user_id = pricesheetmaster.Priced_ByEmployeeID
		INNER JOIN externalproductnumberreference
		USING ( ProductNumberInternal ) 
		INNER JOIN productmaster ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
		WHERE 1=1 " .$psn_clause . " AND customer_contacts.active = 1 AND customer_contacts.active = 1 AND email1 = '" . $email . "'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);

		$Priced_ByEmployeeID = $row['Priced_ByEmployeeID'];
		$IncludePricePerGallonInQuote = $row['IncludePricePerGallonInQuote'];
		//$SpecificGravity = round($row['SpecificGravityMaster'], 2);
		$Terms = $row['Terms'];
		$Packaged_In = $row['Packaged_In'];
		$MinBatch_Units = $row['MinBatch_Units'];
		$FOBLocation = $row['FOBLocation'];

		$html = '<BR><BR><BR>' . date("M j, Y") . '<BR><BR>';

		$html .= $row['first_name'] . ' ' . $row['last_name'] . '<BR>';
		$html .= $row['name'] . '<BR>';
		$html .= $row_address['address1'] . '<BR>';
		if ( $row_address['address2'] != '' ) {
			$html .= $row_address['address2'] . '<BR>';
		}
		$html .= $row_address['city'] . ', ' . $row_address['state'] . ' ' . $row_address['zip'] . '<BR><BR>';

		$html .=  'Dear ' . $row['first_name'] . ':<BR><BR>';
		$html .= '<B STYLE="color:red">abelei</B> <B STYLE="color:#730099">flavors</B> is pleased to provide price quotes on the flavors below.<BR><BR>';
		$html .= '<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="510"><TR><TD WIDTH="80"><B STYLE="text-decoration:underline">Flavor</B></TD><TD WIDTH="270">&nbsp;</TD><TD WIDTH="80"><B STYLE="text-decoration:underline">Price $ / lb.</B></TD>';

		if ( $IncludePricePerGallonInQuote == 1 ) {
			$html .= '<TD><B STYLE="text-decoration:underline">Price $ / gal.</B></TD>';
		}

		$html .= '</TR>';



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

		$html .= 'Unless you specify otherwise, <B STYLE="color:red">abelei</B> <B STYLE="color:#730099">flavors</B> will ship orders by what we consider the most reliable and affordable carriers with respect to your requested arrival date. In these cases freight charges will be prepaid and added to your invoice. If you have a freight carrier and billing procedure that you prefer, please let us know. We will do our very best to accommodate you.<BR><BR>';
		$html .= 'On behalf of my colleagues, I thank you for your interest in <B STYLE="color:red">abelei</B> <B STYLE="color:#730099">flavors</B>, the source of good taste.<BR><BR>';

		if ( $Priced_ByEmployeeID == 10 or $Priced_ByEmployeeID == 11 ) {
			$html .= 'With Best Regards,<BR><BR>';
			$html .= '<IMG SRC="images/signatures/' . $Priced_ByEmployeeID . '.png" HEIGHT=75>';
			$html .= '<BR><BR>';
		} else {
			$html .= 'With Best Regards,<BR><BR><BR><BR><BR>';
		}

		$html .= '<B>' . $row['fn'] . " " . $row['ln'] . '</B>';
		if ( $row['users_title'] != '' ) {
			$html .= ", <I>" . $row['users_title'] . "</I>";
		}
		$html .= "<BR>";
		$html .= '<B STYLE="color:red">abelei</B><BR>';
		$html .= $row['users_email'] . "</B><BR>";
		$html .= 'www.abelei.com';

		$pdf->writeHTML($html, true, 0, true, 0);

		// reset pointer to the last page 
		$pdf->lastPage();

		// --------------------------------------------------------- 

		//Close and output PDF document
		$file = str_replace(" ", "_", $row['name']) . "_pricing_quote_" . date("mdy");
		$pdf->Output('pdfs/' . $file . '.pdf', 'F');

		//============================================================+ 
		// END OF FILE                                                  
		//============================================================+ 

	}



	if ( !$error_found and ($_POST['verified_1'] or $_POST['verified_1_test']) ) {

		$sql = "SELECT email 
		FROM users 
		WHERE user_id = " . $_SESSION['user_id'];
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);

		$from = $row['email'];

		if ( $_POST['verified_1_test'] ) {
			// SEND TO USER FOR TEST
			$to = $from;
		} else {
			// FOR $to, SWITCH TO $email WHEN LIVE!!!
			$to = "moconnell@chicagoit.com";
		}

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
		header("location: customers_quotes.header.php?psn=" . $psn);
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


<?php include("inc_quotes_header.php"); ?>


<?php if ( empty($_POST) ) {
	$subject = "Flavor Quote for " . $row_header['ProductNumberExternal'] . " - " . $row_header['Designation'];
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

	<FORM METHOD="post" ACTION="customers_quotes.email.php">
	<INPUT TYPE="hidden" NAME="psn" VALUE="<?php echo $psn;?>">
	<INPUT TYPE="hidden" NAME="email" VALUE="<?php echo stripslashes($email);?>">
	<INPUT TYPE="hidden" NAME="address_id" VALUE="<?php echo stripslashes($address_id);?>">
	<INPUT TYPE="hidden" NAME="cc" VALUE="<?php echo stripslashes($cc);?>">
	<INPUT TYPE="hidden" NAME="subject" VALUE="<?php echo stripslashes($subject);?>">
	<INPUT TYPE="hidden" NAME="message" VALUE="<?php echo stripslashes($message);?>">
	<INPUT TYPE="hidden" NAME="signature" VALUE="<?php echo stripslashes($signature);?>">
	<?php if ( $psn_string != ''  ) { ?>
		<INPUT TYPE="hidden" NAME="psn_string" VALUE="<?php echo $psn_string;?>">
		<INPUT TYPE="hidden" NAME="customer_id" VALUE="<?php echo $_REQUEST['customer_id'];?>">
	<?php } ?>

		<?php
		if ( $psn_string != ''  ) {
			$customer_id = $_REQUEST['customer_id'];
		} else {
			$customer_id = $row_header['CustomerID'];
		}
		?>

		<TR>
			<TD><B>Contact e-mail:</B></TD>
			<TD>
			<?php
			$sql = "SELECT first_name, last_name, email1 
			FROM customer_contacts 
			WHERE email1 IS NOT NULL AND customer_contacts.active = 1 AND customer_id = " . $customer_id . " ORDER BY last_name";
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
			FROM customer_addresses 
			WHERE customer_id = " . $customer_id . " ORDER BY state, city, zip";
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
			<TD ALIGN=RIGHT><INPUT TYPE="submit" NAME="verified_0" VALUE="Edit message" CLASS='submit'> <INPUT TYPE="submit" NAME="verified_1" VALUE="Send message" CLASS='submit'> <INPUT TYPE="submit" NAME="verified_1_test" VALUE="Send test message" CLASS='submit'></TD>
		</TR></FORM>

	</TABLE>

	</TD></TR></TABLE>
	</TD></TR></TABLE>





<?php } else { ?>



<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
<TABLE CELLPADDING=7 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">

<FORM ACTION="customers_quotes.email.php" METHOD="post">
	<INPUT TYPE="hidden" NAME="psn" VALUE="<?php echo $_REQUEST['psn'];?>">
	<?php if ( $psn_string != ''  ) { ?>
		<INPUT TYPE="hidden" NAME="psn_string" VALUE="<?php echo $psn_string;?>">
		<INPUT TYPE="hidden" NAME="customer_id" VALUE="<?php echo $_REQUEST['customer_id'];?>">
	<?php } ?>

	<?php
	if ( $psn_string != ''  ) {
		$customer_id = $_REQUEST['customer_id'];
	} else {
		$customer_id = $row_header['CustomerID'];
	}
	?>

	<TR>
		<TD><B>Contact e-mail:</B></TD>
		<TD>
		<?php
		$sql = "SELECT first_name, last_name, email1 
		FROM customer_contacts 
		WHERE email1 IS NOT NULL AND customer_contacts.active = 1 AND customer_id = " . $customer_id . " ORDER BY last_name";
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
		FROM customer_addresses 
		WHERE customer_id = " . $customer_id . " ORDER BY state, city, zip";
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
		
		<?php
		if ( $psn_string != ''  ) {
			$qs = "psn_string=" . $psn_string;
		} else {
			$qs = "psn=" . $psn;
		}
		?>
	
		<TD ALIGN=RIGHT><INPUT TYPE='submit' VALUE='Preview message' CLASS='submit'> <INPUT TYPE="button" VALUE="Cancel" onClick="location.href='customers_quotes.header.php?<?php echo $qs;?>'" CLASS="submit"></TD>
	</TR>

</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>

</FORM><RB>



<?php } ?>



<?php include("inc_footer.php"); ?>