<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ONLY ADMIN HAS PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');
include("inc_header.php");

?>

<?php 

	$action = "";
	$report_type = "";
	$po_number = "";
	
	if (isset($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
	}
	if (isset($_REQUEST['report_type'])) {
	$report_type = $_REQUEST['report_type'];
	}
	if (isset($_REQUEST['po_number'])) {
	$po_number = $_REQUEST['po_number'];
	}
?>
	
<TABLE class="bounding">
<TR>
<TD>


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>



		<FORM ACTION="reports_management_reports.php" METHOD="post">
				<INPUT TYPE="hidden" NAME="action" VALUE="select">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">


	<TR>
		<TD align="center" valign="bottom" >
				<INPUT TYPE="button" VALUE="Customer Order Review" onClick="popup('reports/inventory_reports_customer_order_review.php')" CLASS="submit_big">
		</TD>
		</tr>
		<tr><TD align="center" valign="bottom" >
				<INPUT TYPE="submit" class="submit_big" onClick="popup('reports/raw_material_requirements.php')" VALUE="Raw Material Requirements 1 (Batch Sheet)">
		</TD></tr>
		<tr><TD align="center" valign="bottom" >
				<INPUT TYPE="submit" class="submit_big" onClick="popup('reports/raw_material_requirements_part2.php')" VALUE="Raw Material Requirements 2 (Vendor PO)">
		</TD></tr>
	</table>

	</FORM>
	</td>
	</tr>
	</TABLE>
	</TD>
	</TR>
	</TABLE>

<BR><BR>
</TD>
</TR>
</TABLE>


<?php include("inc_footer.php"); ?>