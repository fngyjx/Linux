<?php
session_start();

include('global.php');
require_ssl();

if ( !isset($_SESSION['userTypeCookie']) ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

// print_r($_REQUEST);
$error_found=false;
if ( !empty($_POST) ) {

	$pids=escape_data($_REQUEST['pids']);
    $email = escape_data($_REQUEST['mail_to']);
	$cc = escape_data($_POST['mail_cc']);
	$subject = escape_data($_POST['mail_subject']);
	$message = $_POST['mail_message'];
	$signature = escape_data($_POST['mail_signature']);

	// check_field() FUNCTION IN global.php
	check_field($email, 2, 'Contact e-mail');
	if ( $cc != '' ) {
		check_field($cc, 2, 'CC');
	}
	check_field($subject, 1, 'Subject');
	check_field($message, 1, 'Message');

    $attachment = ( isset($_REQUEST['attachment']) ) ? escape_data($_REQUEST['attachment']) : "";
         
//end of pdf file

	if ( !$error_found ) {
	   
       	$sql = "SELECT email 
		FROM users 
		WHERE user_id = " . $_SESSION['user_id'];
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		// echo "<br />$sql<br />";
		$row = mysql_fetch_array($result);

		$from = $row['email'];

		// FOR $to, SWITCH TO $email WHEN LIVE!!!
		$to = $email;
        				
		$text = 'Message requires an HTML-compatible e-mail program.\n';
			
		// PEAR MAIL PACKAGES
		require_once('Mail.php');
		require_once('Mail/mime.php');
		$crlf = "\n";
		$mime = new Mail_Mime($crlf);

		// Set the email body
		$mime->setTXTBody($text);
		
		$new_line=array("\r\n","\\r\\n","\n","\\n");
		echo "<BR />$message<BR />";
		$message=str_replace($new_line,"<BR>",$message);
		$mime->setHTMLBody( str_replace($new_line, "<BR>", $message . "<BR><BR><BR>" . $signature));
		if ( ! empty($attachment ) ) 
			$mime->addAttachment($attachment,'application/octet-stream');

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
		$mail_send = $mail->send($to, $headers, $body);
		$now=date("m/d/Y H:M:S");
		if ( $mail_send ) {
			$note = "Message successfully sent<BR>";
			$sql="UPDATE projects set follow_up_notes = 'Sent Contact E-mail to ".$email." on ". $now. "' where project_id in (".str_replace("\'","'",$pids).")";
			mysql_query($sql,$link) or die ( mysql_error() . " Failed to execute SQL: $sql <br />");
		} else {
			$note = "Message failed to be sent<BR>";
		}
        $_SESSION['note'] = $note;
		echo "<script language='javascript'>window.close();</script>";
		exit();
	} else 
		echo "error found<br />";

} else {
	echo "<script language='javascript'>window.close();</script>";
	exit();
}
?>