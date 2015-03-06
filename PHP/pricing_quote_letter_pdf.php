<?php
include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');
?>



<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="/styles.css" REL="stylesheet">

</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="600" ALIGN=CENTER><TR VALIGN=TOP><TD>

<?php

$contactNameA=explode("_",$_REQUEST['contact_name']);
$contact_id=$contactNameA[0];
$contact_fnm=$contactNameA[1];
$contact_lnm=$contactNameA[2];
$cc=$_REQUEST['cc']; 

// RECORD WHO LETTER WAS PRINTED FOR
// ADDED 10/5/2009
if ( $_REQUEST['psn'] != '' and $_REQUEST['reprint'] != 1 ) {
	$sql = "INSERT into price_quote_letters (pricesheet_number, address_id, contact_name, sent_by) 
	VALUES (" . $_REQUEST['psn'] . ", '" . $_REQUEST['address_id'] . "', '" . $contact_fnm ." ". $contact_lnm . "','".$_SESSION['first_nameCookie']." ".$_SESSION['last_nameCookie']." by letter')";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
}
$address_id= ( ! empty($_REQUEST['address_id']) ) ? escape_data( $_REQUEST['address_id']) : "";
$pdf_letter=create_pqt_pdf_file($_REQUEST['psn'],$_REQUEST['psn_string'],$address_id,"", $_REQUEST['contact_name'],$cc);

?>
<SCRIPT>
	window.location.href="<?php echo $pdf_letter;?>";
</SCRIPT>
<?php if ( $_REQUEST['psn'] != '' and $_REQUEST['reprint'] != 1 ) { ?>

	<SCRIPT>
		window.opener.location.reload();
	</SCRIPT>

<?php } ?>

</BODY>
</HTML>