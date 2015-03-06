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

	
	if ( isset($_REQUEST['other_email'])) {
       $email = $_REQUEST['other_email'];
    } else {
	   $email = $_POST['email'];
    }
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
	//Create PDF attachement
	if ( !$error_found and !isset($_REQUEST['attachment']) 
		and !isset($_POST['verified_1_test']) 
		and !isset($_POST['verified_1']) 
		and !isset($_POST['verified_0'])) {
		$attachment=create_pqt_pdf_file($psn,$_REQUEST['psn_string'],$address_id,$email,"",$cc);
	} else {
		$attachment=escape_data($_REQUEST['attachment']);
	}

	if ( !$error_found and ($_POST['verified_1'] or $_POST['verified_1_test']) ) { //send mail
	
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
			$to = "$email";
		}

		$text = 'Message requires an HTML-compatible e-mail program.';
			
		// PEAR MAIL PACKAGES
		require_once('Mail.php');
		require_once('Mail/mime.php');
		$crlf = "\n";
		$mime = new Mail_Mime($crlf);

		// Set the email body
		$mime->setTXTBody($text);
		$mime->setHTMLBody( str_replace(array("\r\n","\n","\r"), "<BR>", $message . "<BR><BR><BR>" . $signature));
		$mime->addAttachment($attachment,'application/octet-stream');
		$bcc="";
		if ( $_POST['verified_1'] ) {
			$bcc = "jdu@abelei.com,shenderson@abelei.com";
		}
		// Set the headers
		$mime->setFrom("$from");
		if ( $cc != '' ) {
			$mime->addCc("$cc");
		}
		
		if ( $bcc != "" ) {
			$mime->addBcc("$bcc");
		}
		$mime->setSubject("$subject");

		// Get the formatted code
		$body = $mime->get();
		$headers = $mime->headers();

		// Invoke the Mail class' factory() method
		$host = "smtpout.secureserver.net";
		$port = "80";
		$username = "jdu@abelei.com";
		$userpasswd = "itguy09";
		//use smtp to provent mail failure on source lookup mail servers
		$mail=& Mail::factory('smtp',
			array('host' => $host,
				'port' => $port,
				'auth' => true,
				'username' => $username,
				'password' => $userpasswd));
		// Send the email
		$mail->send("$to", $headers, $body);

		unlink($attachment);
		$_SESSION['note'] = "Message successfully sent<BR>";
		
		//store the information 
		if ( $_POST['verified_1'] ) { //not record test message 
			$sql = "INSERT into price_quote_letters (pricesheet_number, address_id, contact_name, sent_by) 
			VALUES (" . $_REQUEST['psn'] . ", '" . $_REQUEST['address_id'] . "', '" . $email . "','".$_SESSION['first_nameCookie']." ".$_SESSION['last_nameCookie']." by email')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		} 
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
<script type="text/javascript">
<!-- hide js 
 function enable_Other_Mailadd_Field(menu, fieldName) {
    
    if (!document.getElementById(fieldName)) return;
    
    var f = fieldName.toLowerCase();
    
    if (menu.options[menu.selectedIndex].value.toLowerCase() == f) {
      document.getElementById(fieldName).innerHTML = "<INPUT TYPE='text' id='other_email' name='other_email' size='50' style='font-size:12px;' value=''></INPUT>";
      menu.disabled = true;
      menu.style.display = 'none';
    } else {
      document.getElementById(f).disabled = true;
      menu.disabled = false;
      menu.style.display = '';
    }
  }
  
-->
</script>

<?php if ( empty($_POST) ) {
	$subject = "Flavor Quote for " . $row_header['ProductNumberExternal'] . " - " . $row_header['Designation'];
} ?>


<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


<?php if ( !$error_found and !empty($_POST) and !isset($_POST['verified_0'])) { ?>

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
	<INPUT TYPE="hidden" NAME="attachment" VALUE="<?php echo $attachment ;?>">
	<?php if ( $psn_string != ''  ) { ?>
		<INPUT TYPE="hidden" NAME="psn_string" VALUE="<?php echo $psn_string;?>">
		<INPUT TYPE="hidden" NAME="customer_id" VALUE="<?php echo $_REQUEST['customer_id'];?>">
	<?php 
		$customer_id=$_REQUEST['customer_id'];
	} else {
			$customer_id = $row_header['CustomerID'];
	}
		?>

		<TR>
			<TD><B>Contact e-mail:</B></TD>
			<TD>
			<?php
			$contact_found=false;
			$sql = "SELECT first_name, last_name, email1 
			FROM customer_contacts 
			WHERE email1 IS NOT NULL AND customer_contacts.active = 1 AND customer_id = " . $customer_id . " ORDER BY last_name";
			$result_contacts = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query ln 237: $sql<BR><BR>");
			while ( $row_contacts = mysql_fetch_array($result_contacts) ) {
				if ( $row_contacts['email1'] == $email ) {
					echo $row_contacts['first_name'] . " " . $row_contacts['last_name'] . " (" . $row_contacts['email1'] . ")";
					$contact_found=true;
				}
			}
			if ( ! $contact_found )
				echo $email;
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
			<TD ALIGN=RIGHT>
				<INPUT TYPE="button" VALUE="View Attachment" CLASS='submit' onClick="popup('<?php echo $attachment; ?>')"> 
				<INPUT TYPE="submit" NAME="verified_0" VALUE="Edit message" CLASS='submit'>
				<INPUT TYPE="submit" NAME="verified_1" VALUE="Send message" CLASS='submit'>
				<INPUT TYPE="submit" NAME="verified_1_test" VALUE="Send test message" CLASS='submit'></TD>
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
	<?php if ( '' != $_REQUEST['verified_0'] ) { 
        echo "<INPUT TYPE='hidden' NAME='attachment' VALUE='". $attachment ."'>";
    } ?>
	<?php if ( $psn_string != ''  ) { ?>
		<INPUT TYPE="hidden" NAME="psn_string" VALUE="<?php echo $psn_string;?>">
		<INPUT TYPE="hidden" NAME="customer_id" VALUE="<?php echo $_REQUEST['customer_id'];?>">
	<?php } ?>

	<?php

	if ( $psn_string != '' ) {
		$customer_id = $_REQUEST['customer_id'];
	} else {
		$customer_id = $row_header['CustomerID'];
	}
	?>
	
	<TR>
		<TD><B>Contact e-mail:</B></TD>
		<TD>
		<div id="other_mail_addr" name="other_mail_addr"></div>
		<?php
      if ( isset($_REQUEST['email']) or isset($_REQUEST['other_email'])) {
          // echo $email;
           echo "<INPUT TYPE='text' name='other_email' value='". $email ."' size='50' style='font-size:12px;'>";
      } else {
		$sql = "SELECT first_name, last_name, email1 
		FROM customer_contacts 
		WHERE email1 IS NOT NULL AND customer_contacts.active = 1 AND customer_id = " . $customer_id . " ORDER BY last_name";
		$result_contacts = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: ln 338 $sql<BR><BR>");
		if ( mysql_num_rows($result_contacts) > 0 ) {
			echo "<NOBR><SELECT NAME='email' id='email' onChange=\"enable_Other_Mailadd_Field(this,'Other_Mail_Addr')\">";
			while ( $row_contacts = mysql_fetch_array($result_contacts) ) {
				if ( $row_contacts['email1'] == $email ) {
					echo "<OPTION VALUE='" . $row_contacts['email1'] . "' SELECTED>" . $row_contacts['first_name'] . " " . $row_contacts['last_name'] . " (" . $row_contacts['email1'] . ")</OPTION>";
				} else {
					echo "<OPTION VALUE='" . $row_contacts['email1'] . "'>" . $row_contacts['first_name'] . " " . $row_contacts['last_name'] . " (" . $row_contacts['email1'] . ")</OPTION>";
				}
			}
			echo "<OPTION VALUE='other_mail_addr'>Other_Mail_Addr</OPTION>";
			echo "</SELECT><BR>";
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