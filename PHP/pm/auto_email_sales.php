<?php

include('global.php');
include('auto_email.php');

$fortnight_ago = mktime(0, 0, 0, date("m"), date("d")-14, date("y"));
$fortnight_ago = date("Y-m-d", $fortnight_ago);
$sevennight_ago = mktime(0, 0, 0, date("m"), date("d")-7, date("y"));
$sevennight_ago = date("Y-m-d", $sevennight_ago);
//status=4 shipped		
$sql = "SELECT project_id, shipped_date, summary, comments, users.first_name as SalerName,
 concat(clients.first_name,' ',clients.last_name) as ClientName, users.email, company, salesperson, flavor_id,flavor_name
FROM projects
LEFT JOIN users ON projects.salesperson = users.user_id
LEFT JOIN clients
USING ( client_id ) 
LEFT JOIN companies on clients.company_id=companies.company_id
LEFT JOIN flavors USING(project_id)
WHERE status = 4 
AND shipped_date <=  '" . $sevennight_ago . "' AND shipped_date >= '2009-10-19'
AND sales_follow_up = 0
Order By salesperson,shipped_date DESC, flavor_name ";

$result = mysql_query($sql, $link);   //or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

if ( mysql_num_rows($result) != 0 ) {
	$salesperson=0;
	$flavors="";
	$signature="<BR><BR>IT auto-email facility<BR>";
	$email="";
	$cc="jdu@abelei.com";
	$subject="Courtesy Projects Follow Up Reminder";
	$message_comm = "<BR><BR>If you've already followed up, please login and mark this on the projects web site. Otherwise, follow up with the client and then mark as followed up on the site.<BR><BR>";
	while ( $row = mysql_fetch_array($result) ) {
		if ( $salesperson != $row['salesperson'] ) {
			if ( $flavors != "") {
				$message = "Hi " . $salername  . ",<BR><BR>";
				$message .= "This is an automated e-mail as a courtesy reminder. Records show that you have not followed up on following projects that their samples had been sent one week ago.<BR>";
				$message .= $flavors.$message_comm.$signature;
				auto_email($email,$message,$subject,$cc);
			}
			$salername=$row['SalerName'];
			$salesperson=$row['salesperson'];
			$email=$row['email'];
			$project_id=$row['project_id'];
			$flavors="<BR><BR><B>Project#</B>" . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) ." for <B>" . $row['company'] . "</B><BR><I>" .$row['flavor_id'] ." " .$row['flavor_name'] ." shipped on ". date("m/d/Y", strtotime($row['shipped_date']));
		} else {
			if ( $project_id != $row['project_id'] ) {
				$flavors .= "<BR><BR><B>Project#</B>". substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) ." for <B>" . $row['company'] . "</B><BR><I>" .$row['flavor_id'] ." " .$row['flavor_name'] ." shipped on ". date("m/d/Y", strtotime($row['shipped_date']));
				$project_id=$row['project_id'];
			}
			else 
				$flavors .= "<BR><I>" .$row['flavor_id'] ." " .$row['flavor_name'] ." shipped on ". date("m/d/Y", strtotime($row['shipped_date']));
		}
	}
} else {
	auto_email("jdu@abelei.com","Sales follow ups<BR>No follow ups needed to be sent today.<BR>From: noreply@abelei.com\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1","No Auto Mail","");
}

?>