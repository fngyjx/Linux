<?php
/* This piece of code dose nothing but clear the assigned lot to give the user chance to assgin lot again */
include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');
if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

$bsn = isset($_REQUEST['bsn']) ? $_REQUEST['bsn'] : "";
$pni = isset($_REQUEST['pni']) ? $_REQUEST['pni'] : "";
$packin = isset($_REQUEST['packin']) ? $_REQUEST['packin'] : "";
$cstordsqnm = isset($_REQUEST['cstordsqnm']) ? $_REQUEST['cstordsqnm'] : "";
$cstordnm = isset($_REQUEST['cstordnm']) ? $_REQUEST['cstordnm'] : "";

$note = "";
if ( $bsn == "" ) 
	$note = "<red>Batch Sheet Number is required</red><br />";
if ( $packin == "" )
	$note .= "<red>Pack In Product Number is required</red><br />";
if ( $cstordnm == "" )
	$note .= "<red>Cuatomer Order Number is required</red><br />";
	
if ( $cstordsqnm == "" )
	$note .= "<red>Cuatomer Order Sequence Number is required</red><br />";
	
if ( $note == "" ) {

	$sql = "SELECT * from batchsheetdetailpackaginglotnumbers WHERE 
	BatchSheetNumber = '$bsn' AND
	PackagingProductNumber = '$packin' AND
	CustomerOrderNumber='$cstordnm' AND
	CustomerOrderSeqNumber='$cstordsqnm'";
	echo "<br /> $sql <br />";
	
	$result=mysql_query($sql,$link) or die ( mysql_error() . "Failed execute SQL : $sql <br />");
	if (mysql_num_rows($result) > 0 ) {
		while ( $row_lot = mysql_fetch_array($result) ) {
			$sql_im = "DELETE FROM inventorymovements WHERE TransactionNumber = '".$row_lot['InventoryMovementTransactionNumber'] ."'";
			mysql_query($sql_im,$link) or die ( mysql_error() . " Failed execute SQL : $sql_im <br />");
			echo "<br /> $sql_im <br />";
		}	
		$sql = str_replace("SELECT *", "DELETE ", $sql);
		mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
		$note = "";
	}
}

$_SESSION['note'] = $note;
echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
echo "parent.location.href='pop_select_lots_for_batch_sheet_new.php?bsn=".$bsn."&pni=".$pni . "';\n";
echo "</SCRIPT>\n";
exit();
	
include("inc_footer.php"); ?>