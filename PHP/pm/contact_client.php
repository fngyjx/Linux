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
//print_r($_REQUEST);
$cid=escape_data($_REQUEST[cid]);
if ( empty($cid) ) {
	$_SESSION['note']="Client ID is required";
	echo "<script language='javascript'>window.opener.loaction.reload();window.close();</script>";
	exit();
}

$total_flavors=escape_data($_REQUEST['total_flavors']);
$project_ids="";
$flavor_ids="";
for ( $item=1; $item<=$total_flavors; $item++ ) {
	if ( ! empty($_REQUEST['flavor_'.$item]) ) {
		$selected=explode("_",$_REQUEST['flavor_'.$item]);
		$project_ids.=",'".escape_data($selected[0])."'";
		$flavor_ids.=",'".escape_data($selected[1])."'";
	}
}

$project_ids=substr($project_ids,1);
$flavor_ids=substr($flavor_ids,1);

//Get client e-mail, address info

$sql="SELECT first_name,last_name, company, email FROM clients 
LEFT JOIN companies USING(company_id)
WHERE client_id='".$cid."'";

$result=mysql_query($sql,$link) or die ( mysql_error() ." Failed execute SQL : $sql <br />");
$row=mysql_fetch_array($result);
$email=$row['email'];
$firstName=$row['first_name'];
$lastName=$row['last_name'];
$c_company=$row['company'];

$sql="SELECT date_created, shipped_date, lab_comments,summary,comments, notes ,flavor_id,flavor_name 
FROM projects LEFT JOIN flavors USING(project_id) LEFT JOIN notes USING(project_id)
WHERE project_id in (".$project_ids.") AND flavor_id in (".$flavor_ids.") AND client_id='".$cid."'";

// echo "<br />$sql<br />";
$result=mysql_query($sql,$link) or die(mysql_error(). " Failed execute SQL : $sql <br />");

$message="Dear " . $row['first_name'] . ",<BR><BR>";
$message.="I am sending you the email to follow up following projects:<BR>";

while ( $row = mysql_fetch_array($result) ) {
	$message .= "<I>".$row['flavor_id']." ".$row['flavor_name']. (( $row['shipped_date'] != '') ? " Sample Shipped to you on " .date("m/d/Y",strtotime($row['shipped_date'])) : "" );
	$message .="<BR>";
//	$message .=$row['summary']."<BR>".$row['comments']."<BR>".$row['notes']."<BR>".$row['lab_comments']."<BR>";
}

$message .= "<BR>Please return to me at your earliest available<BR>Thanks<BR><BR>";
$text_message = str_replace("<BR>","\n",$message);
$text_message = str_replace("<I>","\t",$text_message);
//get salesperson info:
$sql="SELECT * FROM users where user_id=".$_SESSION['user_id'];
$result=mysql_query($sql,$link) or die ( mysql_error() ." Failed execute SQL: $sql<br />");
$row=mysql_fetch_array($result);

$signature=$row['first_name']." ".$row['last_name']."<BR>";
$signature.= ( empty($row['address1']) ? "194 Alder Drive" : $row['address1']." ".$row['address2'])."<BR>";
$signature.= ( empty($row['city']) ? "North Aurora, IL 60542 " : $row['city'].",".$row['state']." ".$row['zip'])."<BR>";
$signature.="Phone:". ( empty($phone) ? "(630) 859-1410, (866) 4 Abelei" : $row['phone']) ."<BR>";
$signature.=$row['email'].",www.abelei.com<BR>";
$signature = "With Best Regards,\n\n<B>" . $_SESSION['first_nameCookie'] . " " . $_SESSION['last_nameCookie'] . "</B>\n<B STYLE='color:red'>abelei</B> <B STYLE='color:#730099'>flavors</B>\n194 Alder Drive\nNorth Aurora, IL  60542\n630-859-1410\nFax 630-859-1448\nToll Free 866-422-3534\n<A HREF='http://www.abelei.com'>www.abelei.com</A>";
include('header.php');

?>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>

		<TD>

	<B CLASS="header">E-Mail To Client: <?php echo $firstName." ".$lastName." of ".$c_company ;?></B>
<BR><BR><BR>

<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>

<FORM action="contact_client.email.php" method="post">
<input type="hidden" name="pids" value="<?php echo $project_ids;?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
<TR>
<TD><B>TO:</B></TD>
<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
<TD><INPUT type='text' name="mail_to" value="<?php echo $email;?>" size="90"></TD>
</TR>
<TR><TD COLSPAN="3"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>
<TR>
<TD><B>Subject:</B></TD>
<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
<TD><INPUT type="TEXT" name="mail_subject" value="Abelei Projects Follow Up" size="90"></TD>
</TR>
<TR><TD COLSPAN="3"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>
<TR><TD><B>CC:</B></TD>
<TD><IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1"></TD>
<TD align="left"><INPUT type="TEXT" name="mail_cc" value="<?php echo $row['email'];?>" size="90"></TD>
</TR>
<TR><TD COLSPAN="3"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>
<TR><TD colspan="3" align="left"><B>Message:</B></TD></TR>
<TR><TD colspan='3'><TEXTAREA cols="80" rows="40" name="mail_message"><?php echo $text_message;?></TEXTAREA></TD></TR>
<TR><TD COLSPAN="3"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>
<TR><TD colspan="3" align="left"><B>Signature:</B></TD></TR>
<TR><TD colspan='3'><TEXTAREA cols="80" rows="8" name="mail_signature"><?php echo str_replace("<BR>","\n",$signature);?></TEXTAREA></TD></TR>
<TR><TD COLSPAN="3"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>
<TR><TD colspan='3'><NOBR><input type="submit" value="Submit" name="mail_submit"><input type="reset" value="Cancel"></NOBR></TD></TR>
<TR><TD COLSPAN="3"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="5"></TD></TR>
</TABLE>
</FORM>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>



		</TD>
	</TR>
</TABLE>



<?php include('footer.php'); ?>