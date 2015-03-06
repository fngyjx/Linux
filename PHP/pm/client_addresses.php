<?php

session_start();

include('global.php');
require_ssl();

if ( $_SESSION['userTypeCookie'] != 1 and $_SESSION['userTypeCookie'] != 2 ) {
	header ("Location: login.php?out=1");
	exit;
}
include('header.php');
if ( $_REQUEST['company_id'] != '' ) {
	$sql="SELECT distinct company_id, address1,address2,city,state,zip,country,phone,fax, email FROM clients 
	WHERE active=1 AND company_id='".escape_data($_REQUEST['company_id'])."' GROUP BY company_id, address1,address2,city,state,zip,country,phone,fax";
	$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL: $sql<br />");
	if ( mysql_num_rows($result) < 1) {
		echo "<script language='javascript'>window.close();</script>";
		exit;
	} 
//	if ( mysql_num_rows($result) == 1) { //set address
//		$row=mysql_fetch_array($result);
//		echo "<script laguage='javascript'>hide window.opener.loaction.href='clients.php?company_id=".$row['company_id']."&address1=".$row['address1']."&address2=".$row['address2']."&city=".$row['city']."&state=".$row['state']."&zip=".$row['zip']."&country=".$row['country']."&phone=".$row['phone']."&fax=".$row['fax']."&email=".$row['email']."'; window.close();</script>";
//		exit();
//	}
//list the addresses of the company for seleting
	$i=0;

?>

<B CLASS="header"><?php echo $row['company'];?> Existng Contact Addresses</B>

<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<FORM>
<INPUT type="hidden" name="select_address" id="select_address" value="">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
<TR VALIGN=TOP>
	<TD>&nbsp;</TD>
	<TD><B CLASS="black">Address:</B>&nbsp;</TD>
	<TD><B CLASS="black">Phone:</B>&nbsp;</TD>
	<TD><B CLASS="black">Fax:</B>&nbsp;</TD>
	<TD><B CLASS="black">domain:</B>&nbsp;</TD>
</TR>	
<?php while( $row = mysql_fetch_array($result) ) {
	$i++;
?>
<TR><TD colspan='5'><IMG src="images/spacer.gif" height="10"></TD></TR>
<TR>
	<TD>
	<INPUT type="button" value="Select"  onClick="setClientAddr('<?php echo "company_id=".$row['company_id']."&address1=".$row['address1']."&address2=".$row['address2']."&city=".$row['city']."&state=".$row['state']."&zip=".$row['zip']."&country=".$row['country'];?>')"></TD>
	<TD><?php echo $row['address1']." ".$row['address2']. "<br />".$row['city'].", ".$row['state']." ".$row['zip']."<br />".$row['country'];?></TD>
	<TD><INPUT type="checkbox" value="<?php echo $row['phone']?>" name="c_phone_<?php echo $i?>" id="c_phone_<?php echo $i;?>" onClick="AddPhone('<?php echo $i;?>')">&nbsp;<?php echo $row['phone'];?></TD>
	<TD><INPUT type="checkbox" value="<?php echo $row['fax']?>" name="c_fax_<?php echo $i?>" id="c_fax_<?php echo $i;?>" onClick="AddFax('<?php echo $i;?>')">&nbsp;<?php echo $row['fax'];?></TD>
	<?php $domainA=explode("@",$row['email']);?>
	<TD><INPUT type="checkbox" value="<?php echo $row['email']?>" name="c_email_<?php echo $i?>" id="c_email_<?php echo $i;?>" onClick="AddEmail('<?php echo $i;?>')">&nbsp;<?php echo $domainA[1];?></TD>
</TR>
<?php	} 
}	?>

</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE>


<SCRIPT LANGUAGE="JAVASCRIPT">
 <!-- Hide

function setClientAddr(url) 
{
   var addr = document.getElementById("select_address");
   var phoneFax=addr.value;
   window.opener.location.href="clients.php?"+url+phoneFax;
   window.close();
}
function AddPhone(item) 
{
var addr = document.getElementById("select_address");
var phoneFax=addr.value;
	if ( document.getElementById("c_phone_"+item).checked ) {
		var phone=document.getElementById("c_phone_"+item).value;
		addr.value=phoneFax+"&phone="+phone;
	} else { //remove the phone from url
		phoneFax.replace(/&phone=[0-9\-()\+]*/,"");
		addr.value=phoneFax;
	}
}
function AddFax(item) 
{
var addr = document.getElementById("select_address");
var phoneFax=addr.value;

var fax=document.getElementById("c_fax_" + item);
//alert("phone fax value "+phoneFax);
//alert("checked fax value" + fax.value);
	if ( fax.checked ) {
		addr.value=phoneFax+"&fax="+fax.value;
	} else {
		var newPhoneFax=phoneFax.replace(/&fax=[0-9\-()\+]*/,"");
		//alert("phone fax value changed " + newPhoneFax)
		addr.value=newPhoneFax;
	}
}
function AddEmail(item) 
{
   var addr = document.getElementById("select_address");
   var phoneFax=addr.value;
   var email=document.getElementById("c_email_"+item);
   if ( email.checked ) {
		addr.value=phoneFax+"&email="+email.value;
	} else {
		phoneFax.replace(/&email=[@\.a-z0-9\-()\+]*/i,"");
		addr.value=phoneFax;
	}
}
 // End -->
</SCRIPT>

<?php include('footer.php'); ?>