<?php

function auto_email($to,$message,$subject,$cc,$from,$bcc) {

		if($from == "")
			$from = "noreply@abelei.com";
		
		if ( $bcc == "" )
			$bcc = "jdu@abelei.com";

		// PEAR MAIL PACKAGES
		require_once ('Mail.php');
		require_once ('Mail/mime.php');
		$text = 'Message requires an HTML-compatible e-mail program.';
		$crlf = "\n";
		$mime = new Mail_Mime($crlf);

		// Set the email body
		$mime->setTXTBody($text);
		$mime->setHTMLBody($message);

		// Set the headers
		$mime->setFrom("$from");
		$mime->addBcc("$bcc");
		if ( $cc != "" )
			$mime->addCc("$cc");
		$mime->setSubject($subject);

		// Get the formatted code
		$body = $mime->get();
		$headers = $mime->headers();

		// Invoke the Mail class' factory() method
		$mail =& Mail::factory('mail');

		// Send the email
		$mail->send($to, $headers, $body);

}
?>