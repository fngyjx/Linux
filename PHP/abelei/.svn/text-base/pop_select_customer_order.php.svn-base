<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 4 ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');

$found_packs = false;
$error_found="";
$Designation="";
$ProductNumberExternal="";
$ProductNumberInternal="";
$Keywords="";
$note="";

if ( isset($_SESSION[note]) ) {
	$note = $_SESSION[note];
	unset($_SESSION[note]);
}

if ( isset($_SESSION[error_message]) ) {
	$error_message = $_SESSION[error_message];
	$error_found=true;
	unset($_SESSION[error_message]);
}

$intermediary = false;
$edit = false;

$pne = isset($_REQUEST[external_number]) ? $_REQUEST[external_number] : '';
$pne = isset($_REQUEST[pne]) ? $_REQUEST[pne] : $pne;
$action = isset($_REQUEST[action]) ? $_REQUEST[action] : '';

if ( 'edit' == $action ) $edit = true;

$bsn = isset($_REQUEST['bsn']) ? $_REQUEST[bsn] : '';
if ( '' != $bsn ) unset($_SESSION[bsn]);

$LotID = isset($_REQUEST[LotID]) ? $_REQUEST[LotID] : '';

// FOR TRACKING POs FROM ORDERS PAGE
$con = isset($_REQUEST[con]) ? $_REQUEST[con] : '';
$pni = isset($_REQUEST[pni]) ? $_REQUEST[pni] : '';
$seq = isset($_REQUEST[seq]) ? $_REQUEST[seq] : '';

$cpo='';
$ccn ='';
// print_r($_REQUEST);
if ( '' != $con ) {
	$sql = "SELECT `CustomerPONumber` FROM `customerordermaster` WHERE `OrderNumber` = '$con'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$cpo = $row[0];
	if ( '' != $pni && ''!= $seq ) {
		$sql = "SELECT `CustomerCodeNumber` FROM `customerorderdetail` WHERE `CustomerOrderNumber` = '$con' AND ProductNumberInternal = '$pni' AND CustomerOrderSeqNumber = '$seq'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
		$row = mysql_fetch_array($result);
		$ccn = $row[0];
	}
}

$ccn = isset($_REQUEST[ccn]) ? $_REQUEST[ccn] : $ccn;
$seq = isset($_REQUEST[CustomerOrderSeqNumber]) ? $_REQUEST[CustomerOrderSeqNumber] : $seq;
$con = isset($_REQUEST[CustomerOrderNumber]) ? $_REQUEST[CustomerOrderNumber] : $con;

$edit_po = isset($_REQUEST[edit_po]) && 1 == $_REQUEST[edit_po] ? true : false;
$save_po = !empty($_POST[save_po]);

$sql = "SELECT pm.`Intermediary`, pm.`FinalProductNotCreatedByAbelei` FROM `productmaster` as pm, batchsheetmaster as bsm WHERE pm.`ProductNumberInternal` = bsm.`ProductNumberInternal` AND bsm.`BatchSheetNumber`=$bsn";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
$row = mysql_fetch_array($result);
$intermediary = ( 0 != $row[Intermediary] ) ? true : false;
$FinalProductNotCreatedByAbelei = ( 0 != $row[FinalProductNotCreatedByAbelei] ) ? true : false;
if ($intermediary and !$FinalProductNotCreatedByAbelei) { // if is intermediary and the final product is made in house
	$save_po = true;
}
// $note = "$FinalProductNotCreatedByAbelei and $intermediary and $edit_po";
if ( $intermediary and !$FinalProductNotCreatedByAbelei and $edit_po ) {
	$_SESSION[error_message] = "Cannot assign Pack In to intermediary flavors where the final product is created by abelei.";
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
	exit();
} else
if ( !empty($_POST) and $edit_po ) {
	$PackIn = $_POST['PackIn'];
	$NumberOfPackages = $_POST['NumberOfPackages'];

	// check_field() FUNCTION IN global.php
	check_field($PackIn, 1, 'Pack in');
	check_field($NumberOfPackages, 3, '# of Packages');
	if (0>= $NumberOfPackages) {
		$error_found=true;
		$error_message = "Number of packs must be positive<BR>";
	}

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$NumberOfPackages = escape_data($NumberOfPackages);
		
		if ( $bsn != "" ) {
			$sql = "UPDATE batchsheetcustomerinfo SET " .
			" PackIn = '" . $PackIn . "', " .
			" NumberOfPackages = '" . $NumberOfPackages . "'" .
			" WHERE BatchSheetNumber = " . $bsn . " AND CustomerOrderNumber = " . $con . " AND CustomerOrderSeqNumber = " . $seq;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "window.opener.location.reload()\n";
		echo "window.close()\n";
		echo "</SCRIPT>\n";

	}

} else echo "baddy";



if ( $save_po ) {
$note .="<h3>save PO</h3>";

	$ProductNumberExternal = $_POST['ProductNumberExternal'];
	$ProductDesignation = $_POST['ProductDesignation'];

	if ( !$intermediary or $FinalProductNotCreatedByAbelei ) {
		$note .="<h3>FPNCBA</h3>";
		$cpo = $_POST['CustomerPONumber'];
		$ccn = $_POST['CustomerCodeNumber'];
	}
	else {
		$note .="<h3>!FPNCBA</h3>";
	}
	$PackIn = $_POST['PackIn'];
	$NumberOfPackages = $_POST['NumberOfPackages'];

	// check_field() FUNCTION IN global.php
	if ( !$intermediary or $FinalProductNotCreatedByAbelei ) {
			$note .="<h3>3</h3>";
		check_field($PackIn, 1, 'Pack in');
		check_field($NumberOfPackages, 3, '# of Packages');
		
		if ( '' == $PackIn) {
			$error_found = true;
			$error_message .= "No Pack In selected.'<BR>";
		}
		if (0>=$NumberOfPackages) {
			$error_found = true;
			$error_message .= "Please enter a positive value for '# of Packages'<BR>";
		}
	}
	else {
		$note .="<h3>4</h3>";
	}

	$sql = "SELECT count(*) FROM batchsheetcustomerinfo WHERE BatchSheetNumber='$bsn' AND CustomerOrderNumber='$con' AND CustomerOrderSeqNumber='$seq'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	if (0 < $row[0]) {
		$_SESSION[error_message] = "This Customer Order Has already been assigned.";
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "window.opener.location.href='customers_batch_sheets.php?action=edit&bsn=$bsn'\n";
		echo "window.close()\n";
		echo "</SCRIPT>\n";
	}
	if ( !$error_found ) {

	// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
	$NumberOfPackages = escape_data($NumberOfPackages);
	
	$sql = "INSERT INTO batchsheetcustomerinfo 
		(BatchSheetNumber, CustomerOrderNumber, CustomerOrderSeqNumber, CustomerPONumber, CustomerCodeNumber, PackIn, NumberOfPackages) 
		VALUES ($bsn, $con, $seq, '$cpo', ". (!$intermediary ? "'$ccn'" : "NULL" ) .", ". ( (!$intermediary or $FinalProductNotCreatedByAbelei ) ? "'$PackIn', $NumberOfPackages" : "NULL, NULL") .")";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// $_SESSION[note] = $sql;
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.href='customers_batch_sheets.php?action=edit&bsn=$bsn'\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";

	}

}


include("inc_pop_header.php");

?>

<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>











		<FORM ACTION="pop_select_customer_order.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="action" VALUE="edit">
		<INPUT TYPE="hidden" NAME="bsn" VALUE="<?php echo $bsn;?>">
		<INPUT TYPE="hidden" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>">
		<INPUT TYPE="hidden" NAME="ProductDesignation" VALUE="<?php echo $ProductDesignation;?>">
		<INPUT TYPE="hidden" NAME="con" VALUE="<?php echo $con;?>">

		<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC" WIDTH="100%"><TR><TD>
		<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD" WIDTH="100%"><TR><TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">



			<?php if ( $con != '' and !$edit_po ) {

			$sql = "SELECT * FROM customerordermaster LEFT JOIN customerorderdetail ON customerordermaster.OrderNumber = customerorderdetail.CustomerOrderNumber WHERE CustomerOrderNumber = " . $con . " AND ProductNumberInternal = " . $pni . " AND CustomerOrderSeqNumber = " . $seq;
			// echo "<tr><td>$sql</td></tr>";
			$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$row = mysql_fetch_array($result);

			?>

				<INPUT TYPE="hidden" NAME="con" VALUE="<?php echo $con;?>">
				<INPUT TYPE="hidden" NAME="pni" VALUE="<?php echo $pni;?>">
				<INPUT TYPE="hidden" NAME="seq" VALUE="<?php echo $seq;?>">
				<INPUT TYPE="hidden" NAME="CustomerPONumber" VALUE="<?php echo $row['CustomerPONumber'];?>">
				<INPUT TYPE="hidden" NAME="CustomerCodeNumber" VALUE="<?php echo $row['CustomerCodeNumber'];?>">
				<INPUT TYPE="hidden" NAME="ccn" VALUE="<?php echo $row['CustomerCodeNumber'];?>">

				<TR>
					<TD>
						
						<TABLE BORDER=0 CELLSPACING="0" CELLPADDING="3">
							<TR VALIGN=BOTTOM>
								<TD><B STYLE="font-size:8pt">PO#</B></TD>
								<TD><B STYLE="font-size:8pt">Cust Code</B></TD>
								<TD><B STYLE="font-size:8pt">Pack in</B></TD>
								<TD><B STYLE="font-size:8pt">#Packs</B></TD>
							</TR>

							<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
								<TD STYLE="font-size:8pt"><?php echo $row['CustomerPONumber'];?></TD>
								<TD STYLE="font-size:8pt"><?php echo $row['CustomerCodeNumber'];?></TD>
								<TD STYLE="font-size:8pt">
								<SELECT NAME="PackIn" STYLE="font-size: 7pt"><option/>
									<?php
									$sub_sql = "SELECT `ProductNumberInternal` , `Designation` FROM `productmaster` WHERE `ProductNumberInternal` LIKE '6%'";
									$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
									while ( $sub_row = mysql_fetch_array($sub_result) ) {
											echo "<OPTION VALUE='$sub_row[ProductNumberInternal]' ".($row[PackIn] == $sub_row[ProductNumberInternal] ? "SELECTED":"").">$sub_row[Designation]</OPTION>";
									}
									?>
								</SELECT></TD>
								<TD STYLE="font-size:8pt"><INPUT TYPE="text" NAME="NumberOfPackages" SIZE=4></TD>
								<TD><INPUT TYPE="submit" NAME="save_po" VALUE="Save" CLASS="submit"></TD>
							</TR>
				
						</TABLE>
		
					</TD>
				</TR>

			<?php } else { ?>
			
				<INPUT TYPE="hidden" NAME="edit_po" VALUE="1">

				<TR>
					<TD>

					<?php
					$sql = "SELECT *, c.CustomerCodeNumber AS ccn FROM batchsheetcustomerinfo AS bsci
					LEFT JOIN customerorderdetail AS c ON c.CustomerOrderNumber = bsci.CustomerOrderNumber AND c.CustomerOrderSeqNumber = bsci.CustomerOrderSeqNumber
					WHERE c.CustomerOrderNumber = '$con' AND c.CustomerOrderSeqNumber = '$seq' AND bsci.BatchSheetNumber  = '$bsn'";
					$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					$c = mysql_num_rows($result);
					$bg = 0; 
					if ( $c > 0 ) {

					$found_packs = true;

					?>
						
						<TABLE BORDER=0 CELLSPACING="0" CELLPADDING="3">
							<TR VALIGN=BOTTOM>
								<TD><B STYLE="font-size:8pt">PO#</B></TD>
								<TD><B STYLE="font-size:8pt">Cust Code</B></TD>
								<TD><B STYLE="font-size:8pt">Pack in</B></TD>
								<TD><B STYLE="font-size:8pt">#Packs</B></TD>
								<TD></TD>
							</TR>

						<?php
						while ( $row = mysql_fetch_array($result) ) {
							if ( $bg == 1 ) {
								$bgcolor = "#F3E7FD";
								$bg = 0;
							} else {
								$bgcolor = "whitesmoke";
								$bg = 1;
							}
							?>
							<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
								<INPUT TYPE="hidden" NAME="update_po" VALUE="<?php echo $row['CustomerCodeNumber'];?>">
								<INPUT TYPE="hidden" NAME="CustomerOrderNumber" VALUE="<?php echo $row['CustomerOrderNumber'];?>">
								<INPUT TYPE="hidden" NAME="CustomerOrderSeqNumber" VALUE="<?php echo $row['CustomerOrderSeqNumber'];?>">
								
								<?php //if ( $_REQUEST['ccn'] == $row['ccn'] ) {
									$po_form_status = "";
								//} else {
								//	$po_form_status = "readonly='readonly'";
								//} ?>

								<TD STYLE="font-size:8pt"><?php echo $row['CustomerPONumber'];?></TD>
								<TD STYLE="font-size:8pt"><?php echo $row['ccn'];?></TD>
								<TD STYLE="font-size:8pt"><SELECT NAME="PackIn" STYLE="font-size: 7pt" <?php echo $po_form_status;?>><option/>
									<?php
									$sub_sql = "SELECT `ProductNumberInternal` , `Designation` FROM `productmaster` WHERE `ProductNumberInternal` LIKE '6%'";
									$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
									while ( $sub_row = mysql_fetch_array($sub_result) ) {
											echo "<OPTION VALUE='$sub_row[ProductNumberInternal]' ".($row[PackIn] == $sub_row[ProductNumberInternal] ? "SELECTED":"").">$sub_row[Designation]</OPTION>";
									}
									?>
								</SELECT></TD>
								<TD STYLE="font-size:8pt"><INPUT TYPE="text" NAME="NumberOfPackages" VALUE="<?php echo $row['NumberOfPackages'];?>" SIZE=4 <?php echo $po_form_status;?>></TD>

								<TD>
									<?php //if ( $_REQUEST['ccn'] == $row['ccn'] ) { ?>
										<INPUT TYPE="submit" VALUE="Save" CLASS="submit"> <INPUT TYPE="button" VALUE="Cancel" CLASS="submit" onClick="window.close()">
									<?php //} else { ?>
										<!-- <INPUT TYPE="button" VALUE="Edit" CLASS="submit" onClick="window.location='pop_select_customer_order.php?action=edit&edit_po=1&bsn=<?php //echo $row['BatchSheetNumber'];?>&ccn=<?php //echo $row['CustomerCodeNumber'];?>'"> -->
									<?php //} ?>
								</TD>

							</TR>
						<?php } ?>
	
						</TABLE>
					<?php
					} else {
						echo "<I STYLE='font-size:8pt'>No customer orders recorded yet</I>";
					}
					?>
		
					</TD>
				</TR>

			<?php } ?>



		</TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE>













<BR>



<?php include("inc_footer.php"); ?>