<?php

include('global.php');

		$sql = "SELECT shipping, shipper, shipper_other, tracking_number, clients.first_name AS client_first, clients.email
				AS client_e, users.first_name AS sales_first, users.last_name AS sales_last, users.email AS sales_e
			FROM projects
			LEFT JOIN clients
			USING ( client_id ) 
			LEFT JOIN users ON projects.salesperson = users.user_id
			WHERE project_id  = 08121";
		$result1 = mysql_query($sql, $link);
		$row1 = mysql_fetch_array($result1);

		// CHECK WHETHER OTHER SHIPPING ADDRESS HAS BEEN ENTERED
		// IF SO, E-MAIL SALESPERSON NOT CLIENT
		$sql = "SELECT * FROM shipping_info WHERE project_id = 08121";
		$result_shipping = mysql_query($sql, $link);
		$c = mysql_num_rows($result_shipping);

		if ( $c > 0 ) {
			$email = $row1['sales_e'];
		} else {
			$email = $row1['client_e'];
		}

		if ( $row1['shipper'] == 5 ) {
			$shipper = $row1['shipper_other'];
		}
		else {
			$shipper = $shipper_array[$row1['shipper']-1];
		}

		$message = "Hello " . $row1['client_first'] . ",<BR><BR>";  

		$message .= "This is to confirm that <B STYLE='color:red'>abelei</B> has shipped the flavor sample(s) listed in this e-mail message to you this afternoon by " . $shipper . " " . $shipping_array[$row1['shipping']-1] . " and should arrive soon. The tracking number is " . $row1['tracking_number'] . ".<BR><BR>";
  
		$message .= "If you have any questions or concerns, or need additional information on these or other flavors, please reply to 
this message. <BR><BR>";
  
		$message .= "Thank you for your interest in flavors from <B STYLE='color:red'>abelei</B>, <B>the source of good taste</B>.<BR><BR>";


		$message .= "Sincerely,<BR>";
		$message .= "<B>" . $row1['sales_first'] . " " . $row1['sales_last'] . "</B><BR>";
		$message .= "<B STYLE='color:red'>abelei</B><BR>";
		$message .= "194 Alder Drive<BR>";
		$message .= "North Aurora, IL  60542<BR>";
		$message .= "630-859-1410<BR>";
		$message .= "Fax 630-859-1448<BR>";
		$message .= "Toll Free 866-422-3534<BR>";
		$message .= $row1['sales_e'] . "<BR>";
		$message .= "<A HREF='http://www.abelei.com'>www.abelei.com</A><BR><BR><BR>";

		// COMMON CODE SHARED WITH PACKING SLIP (packing_slip.php)
		include('inc_packing_slip_TT.php');


		$mail_message = "<HTML><BODY><TABLE WIDTH=600 BORDER=0><TR><TD STYLE='font-family:verdana, tacoma, sans-serif'>";
		$mail_message .= $message;
		$mail_message .= "</TD></TR></TABLE></BODY></HTML>";
		//mail("tgooding@abelei.com,$email", "abelei sample shipped", wordwrap($mail_message,72,"\r\n"), "From: " . $row1['sales_e'] . "\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1");



		$from = $row1['sales_e'];
		$to = "shenderson@abelei.com,jdu@abelei.com";
		$bcc = "shenderson@abelei.com,jdu@abelei.com";

		// PEAR MAIL PACKAGES
		require_once ('Mail.php');
		require_once ('Mail/mime.php');
		$text = 'Message requires an HTML-compatible e-mail program.';
		$crlf = "\n";
		$mime = new Mail_Mime($crlf);

		// Set the email body
		$mime->setTXTBody($text);
		$mime->setHTMLBody($mail_message);

		// Set the headers
		$mime->setFrom("$from");
		$mime->addBcc("$bcc");
		$mime->setSubject('abelei sample shipped');

		// Get the formatted code
		$body = $mime->get();
		$headers = $mime->headers();

		// Invoke the Mail class' factory() method
		$mail =& Mail::factory('mail');

		// Send the email
		$mail->send($to, $headers, $body);


		//mail("moconnell@chicagoit.com", "abelei sample shipped", wordwrap($mail_message,72,"\r\n"), "From: " . $row1['sales_e'] . "\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1");
		//echo("tgooding@abelei.com,$email");

		// CHANGE "tgooding@abelei.com,moconnell@chicagoit.com" to   $email   AFTER TESTING
		// tgooding@abelei.com,

?>