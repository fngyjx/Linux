<?php
require_once "Mail.php";

$from = "Sandra Sender <jdu@abelei.com>";
$to = "Ramona Recipient <fngyjx@gmail.com>";
$subject = "Hi!";
$body = "Hi,\n\nHow are you?";

$host = "smtpout.secureserver.net";
$username = "jdu@abelei.com";
$password = "itguy09";

$headers = array ('From' => $from,
  'To' => $to,
  'Subject' => $subject);
$smtp = Mail::factory('smtp',
  array ('host' => $host,
    'auth' => true,
    'username' => $username,
    'password' => $password));

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
  echo("<p>" . $mail->getMessage() . "</p>");
 } else {
  echo("<p>Message successfully sent!</p>");
 }
?>
