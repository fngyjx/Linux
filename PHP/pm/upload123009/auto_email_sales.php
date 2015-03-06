<?php

include('global.php');

$fortnight_ago = mktime(0, 0, 0, date("m"), date("d")-14, date("y"));
$fortnight_ago = date("Y-m-d", $fortnight_ago);
		
$sql = "SELECT project_id, shipped_date, summary, comments, users.first_name, users.email, company
FROM projects
LEFT JOIN users ON projects.salesperson = users.user_id
LEFT JOIN clients
USING ( client_id ) 
LEFT JOIN companies
USING ( company_id ) 
WHERE status = 4
AND shipped_date <=  '" . $fortnight_ago . "'
AND sales_follow_up = 0";

$result = mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

if ( mysql_num_rows($result) != 0 ) {
	while ( $row = mysql_fetch_array($result) ) {

		$message = "Hi " . $row['first_name'] . ",<BR><BR>";
		$message .= "This is an automated e-mail. Records show that you have not followed up on <B>Project# " . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) . "</B> for <B>" . $row['company'] . "</B>.<BR><BR>";
		$message .= "<B>Summary</B>: " .  $row['summary'] . "<BR><BR>";
		$message .= "<B>Comments</B>: " .  $row['comments'] . "<BR><BR>";
		$message .= "If you've already followed up, please login and mark this on the projects web site. Otherwise, follow up with the client and then mark as followed up on the site.<BR><BR>";
		mail($row['email'].",shenderson@abelei.com,jdu@abelei.com","Sales follow up notification",wordwrap($message,72,"\r\n"),"From: noreply@abelei.com\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1");
	}
} else {
	mail("shenderson@abelei.com,jdu@abelei.com","Sales follow ups","No follow ups needed to be sent today.","From: noreply@abelei.com\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1");
}

?>