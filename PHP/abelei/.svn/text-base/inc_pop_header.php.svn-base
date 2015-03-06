<?php

if(!isset($_SESSION)) { session_start(); }  

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}


$dates = array("1","2","3","4","5","6","7","8","9","10","11","12","15","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");
$dates_zero = array("01","02","03","04","05","06","07","08","09","10","11","12","15","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");
$months_numbers = array("01","02","03","04","05","06","07","08","09","10","11","12");
$months_names =  array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<HEAD>
<TITLE> abelei </TITLE>

<LINK HREF="styles.css" REL="stylesheet" TYPE="text/css">
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">

<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
<link type="text/css" href="js/custom-theme/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
<script type="text/javascript" language="javascript" src="js/autocomplete/jquery.autocomplete.js"></script>
<link rel="stylesheet" href="js/autocomplete/jquery.autocomplete.css" type="text/css" />
<script type="text/javascript" language="javascript" src="js/helpers.js"></script>

<SCRIPT TYPE="text/javascript" LANGUAGE="JavaScript">
<!--
	function openWin( windowURL, windowName, windowFeatures ) { 
		return window.open( windowURL, windowName, windowFeatures ) ; 
	}
//-->
</SCRIPT>

<script type="text/javascript">
<!--
function popup(url, width, height, left, top, pop_name) {
	if (width === undefined) {
		width = 820;
	}
	if (height === undefined) {
		height = 680;
	}
	if (left === undefined) {
		left  = (screen.width  - width)/2;
	}
	if (top === undefined) {
		top  = (screen.height - height)/2;
	}
	if (pop_name === undefined) {
		pop_name = 'pop_window';
	}
	var params = 'width='+width+', height='+height;
	params += ', top='+top+', left='+left;
	params += ', directories=no';
	params += ', location=no';
	params += ', menubar=no';
	params += ', resizable=yes';
	params += ', scrollbars=yes';
	params += ', status=no';
	params += ', toolbar=no';
	newwin=window.open(url, pop_name, params);
	if (window.focus) {newwin.focus()}
	return false;
}
// -->
</script>

</HEAD>

<BODY>



<!-- TOP MENU -->

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%"><TR><TD BACKGROUND="images/backer.gif">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR>
		<TD><IMG SRC="images/abelei_logo.gif" ALT="abelei logo" WIDTH="166" HEIGHT="67" BORDER="0"></TD>
		<TD HEIGHT="24">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR>
		<TD HEIGHT="43"><IMG SRC="images/solid_green_header_bar.gif" ALT="Blank" WIDTH="34" HEIGHT="43" BORDER="0"></TD>
	</TR>
	<TR>
		<TD HEIGHT="24">&nbsp;&nbsp;
		<?php //if ( basename($_SERVER['PHP_SELF']) == 'pop_search_product.php' ) { ?>
			<!-- <B CLASS="bigwhite">Search for product number</B> -->
		<?php //} else { ?>
			<!-- <B CLASS="bigwhite">Search for price quote</B> -->
		<?php //} ?>
		<?php echo "<B CLASS='bigwhite'>" . ucwords(str_replace(".php", "", str_replace("pop", "", str_replace("_", " ", basename($_SERVER['PHP_SELF']))))) . "</B>";?>
		</TD>
	</TR>
</TABLE>

		</TD>
	</TR>
</TABLE>
</TD></TR></TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
	<TR>
		<TD BACKGROUND="images/light_purple_rule.gif"><IMG SRC="images/light_purple_rule.gif" ALT="Purple rule" WIDTH="500" HEIGHT="2"></TD>
	</TR>
</TABLE>

<!-- TOP MENU -->



<BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR>
		<TD WIDTH="20"><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="20" HEIGHT="1" BORDER="0"></TD>
		<TD><IMG SRC="images/spacer.gif" ALT="spacer" HEIGHT="1" BORDER="0">
		<B><?php //echo ucwords(str_replace("_", " ", $header_name));?></B><BR>


