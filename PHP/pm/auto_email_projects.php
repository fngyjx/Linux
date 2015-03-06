<?php

include('global.php');
include('auto_email.php');

$fortnight_ago = mktime(0, 0, 0, date("m"), date("d")-14, date("y"));
$fortnight_ago = date("Y-m-d", $fortnight_ago);
$sevennight_ago = mktime(0, 0, 0, date("m"), date("d")-7, date("y"));
$sevennight_ago = date("Y-m-d", $sevennight_ago);
$today=mktime(0,0,0,date("m"), date("d"), date("y"));
$today=date("Y-m-d",$today);

//status=4 shipped		
$sql = "SELECT project_id, shipped_date, summary, concat(users.first_name,' ',users.last_name) as SalerName,users.title,users.phone,
 clients.email as client_email, clients.first_name as ClientName, users.email as saler_email, company, salesperson, 
 flavor_id,flavor_name, flavors.suggested_level_other, flavors.use_in, flavors.other_info, next_follow_up_date
FROM projects
LEFT JOIN users ON projects.salesperson = users.user_id
LEFT JOIN clients
USING ( client_id ) 
LEFT JOIN companies on clients.company_id=companies.company_id
LEFT JOIN flavors USING(project_id)
WHERE status = 4 AND flavor_id <> ''
AND ( shipped_date =  '" . $fortnight_ago . "' OR next_follow_up_date = '".$today."')
Order By salesperson, project_id, flavor_name ";
//echo "<br />$sql<br />";
$result = mysql_query($sql, $link)  or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

if ( mysql_num_rows($result) != 0 ) {
	$salesperson=0;
	$project_id=0;
	$flavors="";
	$signature="";
	$email="";
	$cc="jdu@abelei.com";
	$bcc="";
	$subject="";
	$next_follow_up_date="";
	$message_style="<div style='font-family:\"Century Gothic\", \"Times New Roman\", Arial, Georgia;font-style:normal'>";
	$signature=$abelei_font." ".$flavor_font."<BR>194 Alder Drive<BR>North Aurora, IL  60542<BR>Office: 630-859-1410<BR>Fax: 630-859-1448<BR>Toll Free: 866-422-3534<BR>";
	$message_comm = "<BR><BR>Please let us know your impression of these flavors, or if you need further information or assistance.<BR><BR>Thank you for your interest in flavors from ".$abelei_font.", the source of good taste.<BR><BR> With best regards,<BR><BR>";
	while ( $row = mysql_fetch_array($result) ) {
		if ( $project_id != $row['project_id'] ) {
			if ( $flavors != "") {
				$message = $message_style."Hi " . $clientname  . ",<BR><BR>";
				$message .= "In an effort to provide world-class customer service, ".$abelei_font." ".$flavor_font." is interested in knowing what you thought of the flavors sent ". ( $today == $next_follow_up_date ? "recently" : "a couple of weeks ago").", which are listed below.<BR>";
				$message .= $flavors.$message_comm."<BR><BR>".$salername."<BR>".$title."<BR>".$signature."Cell: ".$phone."<BR>".$email."<BR><A HREF='http://www.abelei.com'>www.abelei.com</A>";
				$message .= "</div>";
				auto_email($email,$message,$subject,$cc, $from,$bcc);
			}
			$clientname=$row['ClientName'];
			$subject="abelei flavor follow-up, " . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) ." ". $row['company'] .", ".$row['summary'];
			$salername=$row['SalerName'];
			$salesperson=$row['salesperson'];
			$email=$row['saler_email']."fail";
			$from=$email;
			$phone=$row['phone'];
			$title=$row['title'];
			$client_email=$row['client_email'];
			$project_id=$row['project_id'];
			$next_follow_up_date=date("Y-m-d", strtotime($row['next_follow_up_date']));
			$flavors="<BR><BR><B>" .$row['flavor_id'] ." " .$row['flavor_name'] ."</B> ". $row['other_info'] .( $row['suggested_level_other'] == "" ? "" : "<BR>&nbsp;&nbsp;&nbsp; - Suggested Start Use Level: ". $row['suggested_level_other']);
		} else {
			$flavors .= "<BR><BR><B>" .$row['flavor_id'] ." " .$row['flavor_name'] ."</B> ". $row['other_info'] . ( $row['suggested_level_other'] == "" ? "" : "<BR>&nbsp;&nbsp;&nbsp; - Suggested Start Use Level: ". $row['suggested_level_other']);
			$project_id=$row['project_id'];
		}
	}
} else {
	auto_email("jdu@abelei.com","Projects follow ups<BR>No follow ups needed to be sent today.<BR>From: noreply@abelei.com\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1","No Auto Mail","","","");
}

?>