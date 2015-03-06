<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ONLY ADMIN AND QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 5 and $rights != 6 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');

// print_r($_REQUEST);

$productnumberinternal = ( isset($_REQUEST['productnumberinternal']) ) ? $_REQUEST['productnumberinternal'] : '';

if ( isset($_POST['ordertriggeramount'] ) ) {
	$ordertriggeramount=str_replace(",","",escape_data($_POST['ordertriggeramount']));
	$orgordertriggeramount=str_replace(",","",escape_data($_POST['orgordertriggeramount']));
	if ( $ordertriggeramount > 0 and $ordertriggeramount != $orgordertriggeramount ) { 
		$sql = "UPDATE productmaster set OrderTriggerAmount='".$ordertriggeramount."' WHERE ProductNumberInternal='".$productnumberinternal."'";
		mysql_query($sql,$link) or die ( mysql_error() ." Failed execute SQL : $sql <br/>");
	}
}

$sql="select OrderTriggerAmount FROM productmaster WHERE ProductNumberInternal='" .escape_data($productnumberinternal)."'";
$result=mysql_query($sql,$link) or die( mysql_error() . " Faied execute SQL : $sql <br />");
$row=mysql_fetch_array($result);

?>
<HTML>
<HEAD><TITLE>SET ORDER TRIGGER AMOUNT</TITLE></HEAD>
<body style="margin:0px 0px 0px 0px">
<FORM action="setOrderTriggerAmt.php" method="post" cellpadding="0" cellspacing="0" valign="top" align="left">
<INPUT style="padding:0 0 0 0;align:top left;cellspacing:0" type="text" name="ordertriggeramount" value="<?php echo $row['OrderTriggerAmount'] == "" ? 0 : number_format($row['OrderTriggerAmount'],2);?>" onChange="document.submit();" size="8">
<INPUT type="hidden" name="productnumberinternal" value="<?php echo $productnumberinternal;?>">
<INPUT type="hidden" name="orgordertriggeramount" value="<?php echo $row['OrderTriggerAmount'] == "" ? 0 : number_format($row['OrderTriggerAmount'],2);?>">
</FORM>
</body>
</html>
