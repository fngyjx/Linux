<?php 

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN and QC HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 5 and $rights != 6 ) {
	header ("Location: login.php?out=1");
	exit;
}
include('inc_global.php');
?>
<HEAD>
	<TITLE> abelei labels </TITLE>
	<LINK HREF="styles.css" REL="stylesheet">
	<STYLE TYPE="text/css">
     P.breakhere {page-break-before: always}
	</STYLE>
</HEAD>

<BODY LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" MARGINHEIGHT="0" BGCOLOR="#FFFFFF" onLoad="print()">

<?php
//print_r($_REQUEST);
$size=( isset($_REQUEST['size']) ) ? escape_data($_REQUEST['size']) : "";

if ( $size == "2x4" ) {
	$l_width="288pt"; //4 x 72
	$l_hight="144pt"; //2 x 72
	$l_font="STYLE='font-size:12pt;font-weight:bold'";
} else { //2.25x1.25
	$l_width="162pt"; //2.25 x 72
	$l_hight="90pt"; //2 x 72
	$l_font="STYLE='font-size:7pt;font-weight:bold'";
} 

$date_received=$_REQUEST['date_received'];
$po_number=$_REQUEST['po_number']; //82254&
$po_sequence=$_REQUEST['po_sequence']; //1
$product_number=$_REQUEST['product_number']; //104050&
$description=$_REQUEST['description']; //Artificial+K+Ethyl+Vanillin+-+104050&
$vendor=$_REQUEST['vendor']; //William+E.+Phillips&
$vendor_product_code=$_REQUEST['vendor_product_code']; //N%2FA&
$lot_number=$_REQUEST['lot_number']; //3380601&
$lot_seq_number=$_REQUEST['lot_seq_number']; //1&
$manufacture_date=$_REQUEST['manufacture_date']; //3%2F3%2F2008&
$expiration_date=$_REQUEST['expiration_date']; //3%2F3%2F2013&
$quantity=$_REQUEST['quantity']; //24.00&
$pack_size=$_REQUEST['pack_size']; //25.00&
$measurement_units=$_REQUEST['measurement_units']; //kg&
$qc_date=$_REQUEST['qc_date']; //8%2F29%2F2008
$retain_size=$_REQUEST['retain_size']; //5+g;
$fema_nbr=$_REQUEST['fema_nbr'];

$date = date("m/d/y");

?>
<TABLE CELLPADDING=0 CELLSPACING=0 WIDTH="<?php echo $l_width; ?>" ALIGN=CENTER>
<TR HIGHT="<?php echo $l_hight; ?>"><TD WIDTH="<?php echo $l_width; ?>">
<DIV style='page-break-after: always'>
	<TABLE BORDER='0' WIDTH='100%' CELLSPACING='10pt' CELLPADDING='0pt'>
		<TR>
		<TD WIDTH='<?php echo ($l_width - 0)."pt";?>' ALIGN="LEFT">
			<SPAN <?php echo $l_font; ?>><?php echo $description;?>
		<?php if ($size != "2x4" ) echo "<br /><!--";?></SPAN>
		</TD></TR>
		<TR><TD WIDTH='<?php echo ($l_width - 0)."pt";?>' ALIGN="LEFT">
			<SPAN <?php echo $l_font; ?>><?php if ($size != "2x4" ) echo "-->";?><NOBR><?php echo $product_number; ?> &nbsp;&nbsp;&nbsp;    <?php echo $vendor;?></NOBR>
		<?php if ($size != "2x4" ) echo "<br /><!--";?></SPAN>
		</TD></TR>
		<TR><TD WIDTH='<?php echo ($l_width - 0)."pt";?>' ALIGN="LEFT">
			<SPAN <?php echo $l_font; ?>><?php if ($size != "2x4" ) echo "-->";?><NOBR>Lot: <?php echo $lot_number."-".$lot_seq_number; if ( $size == "2x4" ) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    fema:". $fema_nbr; ?></NOBR>
		<?php if ($size != "2x4" ) echo "<br /><!--";?></SPAN>
		</TD></TR>
		<TR><TD WIDTH='<?php echo ($l_width - 0)."pt";?>' ALIGN="LEFT">	
			<SPAN <?php echo $l_font; ?>><?php if ($size != "2x4" ) echo "-->";?><NOBR><?php echo $date_received;?>&nbsp;&nbsp;&nbsp; <?php if( $po_number ) echo "PO# ". $po_number."-".$po_sequence;?></NOBR>
		<?php if ($size != "2x4" ) echo "<br /><!--";?></SPAN>
		</TD></TR>
		<TR><TD WIDTH='<?php echo ($l_width - 0)."pt";?>' ALIGN="LEFT">	
			<SPAN <?php echo $l_font; ?>><?php if ($size != "2x4" ) echo "-->";?><NOBR><?php echo $expiration_date;?>&nbsp;&nbsp;&nbsp;(<?php echo $quantity;?>X<?php echo $pack_size;?><?php echo $measurement_units;?>)</NOBR></SPAN>
		</TD>
		</TR>
	</TABLE>
	</DIV>
</TD></TR>
</TABLE>	
</BODY>
</HTML>