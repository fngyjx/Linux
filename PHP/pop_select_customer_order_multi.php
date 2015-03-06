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

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( isset($_SESSION['error_message']) ) {
	$error_message = $_SESSION['error_message'];
	$error_found=true;
	unset($_SESSION['error_message']);
}

$intermediary = false;
$edit = false;

$pne = isset($_REQUEST['external_number']) ? $_REQUEST['external_number'] : '';
$pne = isset($_REQUEST['pne']) ? $_REQUEST['pne'] : $pne;
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ( $action == "submit" or $action == "cancel" or $action == "submit_pkin") { //done

			echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
			echo "window.opener.location.reload()\n";
			echo "window.close()\n";
			echo "</SCRIPT>\n";
}

$bsn = isset($_REQUEST['bsn']) ? $_REQUEST['bsn'] : '';
if ( '' != $bsn ) unset($_SESSION['bsn']);

$LotID = isset($_REQUEST['LotID']) ? $_REQUEST['LotID'] : '';

// FOR TRACKING POs FROM ORDERS PAGE
$con = isset($_REQUEST['con']) ? $_REQUEST['con'] : '';
$pni = isset($_REQUEST['pni']) ? $_REQUEST['pni'] : '';
$seq = isset($_REQUEST['seq']) ? $_REQUEST['seq'] : '';

$cpo='';
$ccn='';

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

$ccn = isset($_REQUEST['ccn']) ? $_REQUEST['ccn'] : $ccn;
$seq = isset($_REQUEST['CustomerOrderSeqNumber']) ? $_REQUEST['CustomerOrderSeqNumber'] : $seq;
$con = isset($_REQUEST['CustomerOrderNumber']) ? $_REQUEST['CustomerOrderNumber'] : $con;

$edit_po = isset($_REQUEST['edit_po']) && 1 == $_REQUEST['edit_po'] ? true : false;
$save_po = !empty($_POST['save_po']);

$sql = "SELECT pm.`Intermediary`, pm.`FinalProductNotCreatedByAbelei` FROM `productmaster` as pm, batchsheetmaster as bsm WHERE pm.`ProductNumberInternal` = bsm.`ProductNumberInternal` AND bsm.`BatchSheetNumber`=$bsn";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
$row = mysql_fetch_array($result);
$intermediary = ( 0 != $row['Intermediary'] ) ? true : false;
$FinalProductNotCreatedByAbelei = ( 0 != $row['FinalProductNotCreatedByAbelei'] ) ? true : false;

if ($intermediary and !$FinalProductNotCreatedByAbelei) { // if is intermediary and the final product is made in house
	$save_po = true;
}
// note = "$FinalProductNotCreatedByAbelei and $intermediary and $edit_po should be stopped doing it in the beginning, thus $edit jdu";
// it is not true, for intermediary product, we also need packings - jdu
/* if ( $intermediary and !$FinalProductNotCreatedByAbelei and $edit ) {
	$_SESSION['error_message'] = "Cannot assign Pack In to intermediary flavors where the final product is created by abelei.";
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
	exit();
} else */

if ( !empty($_POST) and $action == "edit_pkin" ) { //edit PackIn with or without PackInID
	
	$PackInID = isset($_REQUEST['PackInId']) ? $_REQUEST['PackInId'] : '';
	if ($PackInID == "" ) {
	  $PackIn = $_POST['PackIn_edit'] ;
	  $NumberOfPackages = $_POST['NumberOfPackages_edit'];
	} else {
	  $pkid_arr = explode("_",$PackInID);
	  $i = $pkid_arr[1];
	  $PackInID = $pkid_arr[0];
	  $PackIn = $_POST['PackIn_edit_'.$i] ;
	  $NumberOfPackages = $_POST['NumberOfPackages_edit_'.$i];
	} 

	// check_field() FUNCTION IN global.php
	check_field($PackIn, 1, 'Pack in');
	check_field($NumberOfPackages, 3, '# of Packages');

	if (0> $NumberOfPackages) {
		$error_found=true;
		$error_message = "Number of packs must be positive<BR>";
	}

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$NumberOfPackages = escape_data($NumberOfPackages);
		if ( $PackInID != "" ) { //if packinid exist, update bscipkins
		  if ( $NumberOfPackages >= 0 ) {
			$sql = "UPDATE bscustomerinfopackins SET PackIn = '" . $PackIn .
			"', NumberOfPackages = '" .$NumberOfPackages ."' " .
			" where PackInID = " . $PackInID; 
		  } else { //delete this packin
			$sql_delete = "DELETE FROM bscustomerinfopackins WHERE PackInID = " . $PackInID; 
			mysql_query($sql_delete,$link) or die ( mysql_error(). "<br />Failed execute SQL $sql_delete <br />");
			$sql_pkin="SELECT PackInID FROM batchsheetcustomerinfo WHERE BatchsheetNumber=$bsn
				AND CustomerOrderNumber=$con AND CustomerOrderSeqNumber = $seq";
			$result=mysql_query($sql_pkin,$link) or die ( mysql_error(). "<br />Failed execute SQL $sql_pkin <br />");
			$row=mysql_fetch_array($result);
			$niddle=array(",".$PackInID,$PackInID.",");
			$new_packinids=str_replace($niddle,"",$row[0]);
			$sql = "UPDATE  batchsheetcustomerinfo SET PackInID = '". $new_packinids. "' " .
				" where BatchSheetNumber = " . $bsn . 
				" AND CustomerOrderNumber = " . $con . 
				" AND CustomerOrderSeqNumber = " . $seq;
			//echo "<br /> $sql <br />";
		  }
		//	echo "<br />" .$sql . "<br />";
			
		  mysql_query($sql,$link) or die ( mysql_error(). "<br />Failed execute SQL ln 157: $sql <br />");
		} else { // else, edit PckIn 
			$sql = "UPDATE  batchsheetcustomerinfo SET PackIn = '". $PackIn . "',".
				   "NumberOfPackages = '" .$NumberOfPackages . "' " .
				" where BatchSheetNumber = " . $bsn . 
				" AND CustomerOrderNumber = " . $con . 
				" AND CustomerOrderSeqNumber = " . $seq;
		//	echo "<br />". $sql ."<br />";
			mysql_query($sql,$link) or die ( mysql_error(). "<br />Failed execute SQL ln165: $sql <br />");
		}
		$_SESSION['note'] = "Edit was success <br />" . $sql;
	} //!$error_found
} //$_POST not empty and $action = "edit_pkin" 

if ( !empty($_POST) and $action == "add_pkin" ) { //add pkin to existing bsci and bscipkins 
	$PackInID = isset($_REQUEST['PackInID']) ? $_REQUEST['PackInID'] : '';
	if ($PackInID == "" ) {
	  $PackIn = $_POST['PackIn_add'] ;
	  $NumberOfPackages = $_POST['NumberOfPackages_add'];
	} 
	// check_field() FUNCTION IN global.php
	check_field($PackIn, 1, 'Pack in');
	check_field($NumberOfPackages, 3, '# of Packages');

	if (0> $NumberOfPackages) {
		$error_found=true;
		$error_message = "Number of packs must be positive<BR>";
	}

	if ( !$error_found ) {
	$sql = "SELECT PackIn,PackInID,NumberOfPackages from batchsheetcustomerinfo ".
		" where BatchSheetNumber = " . $bsn . 
		" AND CustomerOrderNumber = " . $con . 
		" AND CustomerOrderSeqNumber = " . $seq;
	//echo "<br />" . $sql ."<br />";
	
	$result = mysql_query($sql,$link) or die ( mysql_error(). "<br />Failed execute SQL $sql <br />");
	$c = mysql_num_rows($result);	
		
	//	echo "\$c = ". $c ."<br />";
	$row = mysql_fetch_array($result);
	if ( $c < 1 ) { //create bsci 
		echo "Something is wrong should not be add_packins<br />";
		die;
	} else { // move PckIn to bscipkins and add new Pckin to bscipkins
	 start_transaction($link);
  if ( "" != $row[PackIn] ) {
//mv PackIn to PackInID batchsheetcustomerinfo
	$sql = "INSERT INTO bscustomerinfopackins (PackIn, NumberOfPackages ) ".
		" SELECT PackIn,NumberOfPackages  from batchsheetcustomerinfo". 
		" where BatchSheetNumber = " . $bsn . 
		" AND CustomerOrderNumber = " . $con . 
		" AND CustomerOrderSeqNumber = " . $seq;
		//echo "<br />" . $sql ."<br />";		
		if ( ! mysql_query($sql,$link) ) {
			echo mysql_error() . "<br />Failed excecute Query $sql <br />";
			end_transaction(0,$link);
			die;
		}

		$insert_id = mysql_insert_id();
				
		$sql = "INSERT INTO bscustomerinfopackins (PackIn, NumberOfPackages ) ".
				" VALUES ('" .$PackIn ."','". $NumberOfPackages . "')";
		//echo "<br />" .$sql ."<br />";
		
		if (!mysql_query($sql,$link)) {
			echo (mysql_error() . "<br />Failed excecute Query $sql <br />");
			end_transaction(0,$link);
			die;
		}
				
		$insert_id = $insert_id .",". mysql_insert_id();
				
		$sql = "UPDATE batchsheetcustomerinfo SET " .
				" PackIn = '" . NULL . "', " .
				" PackInID = '". $insert_id . "',".
				" NumberOfPackages = NULL" .
				" WHERE BatchSheetNumber = " . $bsn . " AND CustomerOrderNumber = " . $con . " AND CustomerOrderSeqNumber = " . $seq;
	//	echo "<br />" .$sql ."<br />";
		if ( !mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}
	} elseif ( "" != $row[PackInID] ) { //more than 2 packins
				
		$sql = "INSERT INTO bscustomerinfopackins (PackIn, NumberOfPackages ) ".
			" VALUES ('" .$PackIn ."','". $NumberOfPackages . "')";
	//	echo "<br />" .$sql ."<br />";
		if ( !mysql_query($sql,$link) ) {
			echo mysql_error() . "<br />Failed excecute Query $sql <br />";
			end_transaction(0,$link);
			die;
		}
				
		$insert_id = $row[PackInID] .",". mysql_insert_id();
		$sql = "UPDATE batchsheetcustomerinfo SET " .
			" PackIn = '" . NULL . "', " .
			" PackInId = '". $insert_id . "',".
			" NumberOfPackages = NULL" .
			" WHERE BatchSheetNumber = " . $bsn . " AND CustomerOrderNumber = " . $con . " AND CustomerOrderSeqNumber = " . $seq;
	//	echo "<br />" .$sql ."<br />";
		if ( !mysql_query($sql,$link) ) {
			echo mysql_error() . "<br />Failed excecute Query $sql <br />";
			end_transaction(0,$link);
			die;
		}
	} //row['PackInId']
	end_transaction(1,$link);
	$_SESSION['note'] = "Adding Packin was success <br />";
 } // add_pkin = true
 } //no error found
} //action add_pkin

$show_cancel=false;

if ( empty($_POST) and ( $action == "edit" or $action == "edit_po" ) )  { //new bs_po multipackin
	$show_cancel = false; //for multiple Packin Assignment, cancel may miss guiding, thus don't use it.
}

if ( $action == "save_po" ) { //new bsci_po
	$sql = "INSERT INTO batchsheetcustomerinfo ".
	    "(BatchSheetNumber, CustomerOrderNumber, CustomerOrderSeqNumber, CustomerPONumber, CustomerCodeNumber, PackIn, PackInID, NumberOfPackages) ". 
		"VALUES (".$bsn.",". $con.",". $seq.", '".$cpo."', '". $ccn."', '". $PackIn."', '".$PackInID."', ".$NumberOfPackages.")";
	echo "<br />". $sql . "<br />";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$edit_po = true;
} // if save_po

include("inc_pop_header.php");

?>

<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>

<FORM ACTION="pop_select_customer_order_multi.php" METHOD="post">
	<INPUT TYPE="hidden" NAME="action" id="action" VALUE="edit">
	<INPUT TYPE="hidden" NAME="bsn" VALUE="<?php echo $bsn;?>">
	<INPUT TYPE="hidden" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>">
	<INPUT TYPE="hidden" NAME="ProductDesignation" VALUE="<?php echo $ProductDesignation;?>">
	<INPUT TYPE="hidden" NAME="con" VALUE="<?php echo $con;?>">

	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC" WIDTH="100%"><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD" WIDTH="100%"><TR><TD>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
	<?php if ( $con != '' and !$edit_po ) {
		$sql = "SELECT * FROM customerordermaster LEFT JOIN customerorderdetail ON customerordermaster.OrderNumber = customerorderdetail.CustomerOrderNumber WHERE CustomerOrderNumber = " . $con . " AND ProductNumberInternal = " . $pni . " AND CustomerOrderSeqNumber = " . $seq;
		echo "<tr><td>$sql</td></tr>";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
	
		$seq = $row['CustomerOrderSeqNumber'];
		
			$sql = "SELECT * from batchsheetcustoerinfo where BatchSheetNumber = " . $bsn . " AND CustomerOrderNumber = " . $con ." AND CustomerOrderSeqNumber = ". $seq; 
            $bsci_result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			$c = mysql_num_rows($bsci_result);
			
			?>

				<INPUT TYPE="hidden" NAME="con" VALUE="<?php echo $con;?>">
				<INPUT TYPE="hidden" NAME="pni" VALUE="<?php echo $pni;?>">
				<INPUT TYPE="hidden" NAME="seq" VALUE="<?php echo $seq ;?>">
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
							<?php if ( $c == 0 ) { ?>
							<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
								<TD STYLE="font-size:8pt"><?php echo $row['CustomerPONumber'];?></TD>
								<TD STYLE="font-size:8pt"><?php echo $row['CustomerCodeNumber'];?></TD>
								<TD STYLE="font-size:8pt">
								<SELECT NAME="PackIn" STYLE="font-size: 8pt"><option/>
									<?php
									$sub_sql = "SELECT `ProductNumberInternal` , `Designation` FROM `productmaster` WHERE `ProductNumberInternal` LIKE '6%'";
									$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
									while ( $sub_row = mysql_fetch_array($sub_result) ) {
											echo "<OPTION VALUE='" . $sub_row['ProductNumberInternal'] ."'> ". $sub_row[Designation] . "</OPTION>";
									}
									?>
								</SELECT></TD>
								<TD STYLE="font-size:8pt"><INPUT TYPE="text" NAME="NumberOfPackages" SIZE=4></TD>
								<TD><INPUT TYPE="submit" NAME="save_po" id="save_po" VALUE="Save" CLASS="submit"></TD>
							</TR>
							<?php //new pkin 
							} else {
								echo "<INPUT TYPE=\"hidden\" NAME=\"edit_po\" VALUE=\"1\">"; //set edit_po = 1 then we will not need to be here
								$row_bsci = mysql_fetch_array($bsci_result);
								if ( $row_bsci['PackIn'] != "" ) { ?>

								<TR BGCOLOR="#F3E7FD" VALIGN=TOP>
								<TD STYLE="font-size:8pt"><?php echo $row['CustomerPONumber'];?></TD>
								<TD STYLE="font-size:8pt"><?php echo $row['CustomerCodeNumber'];?></TD>
								<TD STYLE="font-size:8pt">
								<SELECT NAME="PackIn_edit" STYLE="font-size: 8pt"><option/>
									<?php
									$sub_sql = "SELECT `ProductNumberInternal` , `Designation` FROM `productmaster` WHERE `ProductNumberInternal` LIKE '6%'";
									$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
									while ( $sub_row = mysql_fetch_array($sub_result) ) {
											echo "<OPTION VALUE='" . $sub_row[ProductNumberInternal] ."' ".($row_bsci['PackIn'] == $sub_row['ProductNumberInternal'] ? "SELECTED":"").">" .$sub_row['Designation'] ."</OPTION>";
									}
								?></SELECT></TD>
								<TD STYLE="font-size:8pt"><INPUT TYPE="text" NAME="NumberOfPackages_edit" SIZE=4 VALUE="<?php echo $row_bsci['NumberOfPackages'];?>"></TD>
								<TD><INPUT TYPE="submit" NAME="edit_pkin" id="edit_pkin" VALUE="Edit" CLASS="submit" onClick="setAction('edit_pkin','')"></TD>
								</TR>
								<TR BGCOLOR="whitesmoke" VALIGN=TOP>
								<TD>&nbsp;</TD>
								<TD>&nbsp;</TD>
								<TD STYLE="font-size:8pt">
								<SELECT NAME="PackIn_add" STYLE="font-size: 7pt"><option/>
									<?php
									$sub_sql = "SELECT `ProductNumberInternal` , `Designation` FROM `productmaster` WHERE `ProductNumberInternal` LIKE '6%' and `ProductNumberInternal` <> '".$row_bsci['PackIn'] ."'";
									$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
									while ( $sub_row = mysql_fetch_array($sub_result) ) {
											echo "<OPTION VALUE='$sub_row[ProductNumberInternal]' ".($row[PackIn] == $sub_row[ProductNumberInternal] ? "SELECTED":"").">$sub_row[Designation]</OPTION>";
									}
									?>
								</SELECT></TD>
								<TD STYLE="font-size:8pt"><INPUT TYPE="text" NAME="NumberOfPackages_add" SIZE=4></TD>
								<TD><INPUT TYPE="submit" NAME="add_pkin" id="add_pkin" VALUE="Add More Pack" CLASS="submit" onClick="setAction('add_pkin','')"></TD>
							</TR>
							<TR><TD colspan="4">&nbsp;</TD><TD><INPUT TYPE="submit" NAME="submit_pkin" id="submit_pkin" VALUE="Submit" CLASS="submit" onClick="setAction('submit_pkin','')">
							<?php if ( $show_cancel ) {?>
								<INPUT TYPE="button" class="submit" VALUE="Cancel" onClick="window.close();">
							<?php } ?> </TD>		
							</TR>	
							<?php } elseif ( $row_bsci['PackInID'] != "" ) { 
								echo "<INPUT name=\"PackInId\" id=\"PackInId\" type=\"hidden\" value=\"\">";
								$sql = "SELECT * FROM bscustomerinfopackins where PackInID in (". $row_bsci['PackInID'] . ")";
				//				echo "<br />" .$sql . "<br />";
								$bscipk_result = mysql_query($sql,$link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
								$i = 0;
								while ( $row_bscipkin = mysql_fetch_array($bscipk_result) ) { 
								  $i++;
								?>
								<TR BGCOLOR="#F3E7FD" VALIGN=TOP>
								<TD STYLE="font-size:8pt"><?php echo $row['CustomerPONumber'];?></TD>
								<TD STYLE="font-size:8pt"><?php echo $row['CustomerCodeNumber'];?></TD>
								<TD STYLE="font-size:8pt">
								<SELECT NAME="PackIn_edit_<?php echo $i;?>" STYLE="font-size: 7pt"><option/>
									<?php
									$sub_sql = "SELECT `ProductNumberInternal` , `Designation` FROM `productmaster` WHERE `ProductNumberInternal` LIKE '6%'";
									$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
									while ( $sub_row = mysql_fetch_array($sub_result) ) {
											echo "<OPTION VALUE='". $sub_row[ProductNumberInternal] ."' ".($row_bscipkin['PackIn'] == $sub_row['ProductNumberInternal'] ? "SELECTED":"").">".$sub_row['Designation'] . "</OPTION>";
									}
								?>
								</SELECT></TD>
								<TD STYLE="font-size:8pt"><INPUT TYPE="text" NAME="NumberOfPackages_edit_<?php echo $i;?>" SIZE=4 VALUE="<?php echo $row_bsci['NumberOfPackages'];?>"></TD>
								<TD><INPUT TYPE="submit" NAME="edit_pkin" id="edit_pkin" VALUE="Edit" CLASS="submit" onClick="setAction('edit_pkin','<?php echo $row_bscipkin['PackInID']."_".$i;?>')"></TD>
								</TR>
								<?php } //while row_bscipkin ?>
								<TR BGCOLOR="whitesmoke" VALIGN=TOP>
								<TD>&nbsp;</TD>
								<TD>&nbsp;</TD>
								<TD STYLE="font-size:8pt">
								<SELECT NAME="PackIn_add" STYLE="font-size: 7pt"><option/>
									<?php
									$sub_sql = "SELECT `ProductNumberInternal` , `Designation` FROM `productmaster` WHERE `ProductNumberInternal` LIKE '6%' and `ProductNumberInternal` not in (SELECT PackIn from bscustomerinfopackins where PackInID in (".$row_bsci['PackInID'] .") )";
									$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
									while ( $sub_row = mysql_fetch_array($sub_result) ) {
											echo "<OPTION VALUE='$sub_row[ProductNumberInternal]'>".$sub_row['Designation'] ."</OPTION>";
									}
									?>
								</SELECT></TD>
								<TD STYLE="font-size:8pt"><INPUT TYPE="text" NAME="NumberOfPackages_add" SIZE=4></TD>
								<TD><INPUT TYPE="submit" NAME="add_pkin" id="add_pkin" VALUE="Add More Pack" CLASS="submit" onClick="setAction('add_pkin','')">
							
								</TD>
							</TR>
							<TR><TD colspan="4">&nbsp;</TD><TD><INPUT TYPE="submit" NAME="submit_pkin" id="submit_pkin" VALUE="Submit" CLASS="submit" onClick="setAction('submit_pkin','')">
								<?php if ( $show_cancel ) {?>
									<INPUT TYPE="button" class="submit" VALUE="Cancel" onClick="window.close();">
								<?php } ?> 
							</TD>		
						
							<?php } //packinid !='' ?>
						<?php	} //$c > 0 ?>
								
				
						</TABLE>
		
					</TD>
				</TR>
			<?php //edit
			} else { //edit_po ?>
			
				<INPUT TYPE="hidden" NAME="edit_po" VALUE="1">

				<TR>
					<TD>

					<?php
					$sql = "SELECT *, c.CustomerCodeNumber AS ccn FROM batchsheetcustomerinfo AS bsci
					LEFT JOIN customerorderdetail AS c ON c.CustomerOrderNumber = bsci.CustomerOrderNumber AND c.CustomerOrderSeqNumber = bsci.CustomerOrderSeqNumber
					WHERE c.CustomerOrderNumber = '$con' AND c.CustomerOrderSeqNumber = '$seq' AND bsci.BatchSheetNumber  = '$bsn'";
			//		echo "<br />" . $sql ."<br />";
					$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					$c = mysql_num_rows($result);
			//		echo "<br /> c= ".$c ."<br />";
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
						} ?>
						<INPUT TYPE="hidden" NAME="update_po" VALUE="<?php echo $row['CustomerCodeNumber'];?>">
						<INPUT TYPE="hidden" NAME="CustomerOrderNumber" VALUE="<?php echo $row['CustomerOrderNumber'];?>">
						<INPUT TYPE="hidden" NAME="CustomerOrderSeqNumber" VALUE="<?php echo $row['CustomerOrderSeqNumber'];?>">
						<?php
				//		echo "<br /> row_PackIn = ".$row['PackIn']."<br />";
				//		echo "<br /> row_PackInID = ". $row['PackInID']."<br />";
						if ( $row['PackIn'] != "" ) { ?>
							<TR BGCOLOR="#F3E7FD" VALIGN=TOP>
							<TD STYLE="font-size:8pt"><?php echo $row['CustomerPONumber'];?></TD>
							<TD STYLE="font-size:8pt"><?php echo $row['CustomerCodeNumber'];?></TD>
							<TD STYLE="font-size:8pt">
								<SELECT NAME="PackIn_edit" STYLE="font-size: 7pt"><option/>
									<?php
									$sub_sql = "SELECT `ProductNumberInternal` , `Designation` FROM `productmaster` WHERE `ProductNumberInternal` LIKE '6%'";
									$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
									while ( $sub_row = mysql_fetch_array($sub_result) ) {
											echo "<OPTION VALUE='". $sub_row[ProductNumberInternal]."' ".($row['PackIn'] == $sub_row['ProductNumberInternal'] ? "SELECTED":"").">". $sub_row['Designation'] . "</OPTION>";
									}
								?>
								</SELECT></TD>
								<TD STYLE="font-size:8pt"><INPUT TYPE="text" NAME="NumberOfPackages_edit" SIZE=4 VALUE="<?php echo $row['NumberOfPackages'];?>"></TD>
								<TD><INPUT TYPE="submit" NAME="edit_pkin" id="edit_pkin" VALUE="Edit" CLASS="submit" onClick="setAction('edit_pkin','')"></TD>
								</TR>
								<TR BGCOLOR="whitesmoke" VALIGN=TOP>
								<TD>&nbsp;</TD>
								<TD>&nbsp;</TD>
								<TD STYLE="font-size:8pt">
								<SELECT NAME="PackIn_add" STYLE="font-size: 7pt"><option/>
									<?php
									$sub_sql = "SELECT `ProductNumberInternal` , `Designation` FROM `productmaster` WHERE `ProductNumberInternal` LIKE '6%' and `ProductNumberInternal` <> '".$row['PackIn'] ."'";
									$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
									while ( $sub_row = mysql_fetch_array($sub_result) ) {
											echo "<OPTION VALUE='". $sub_row[ProductNumberInternal]."' >".$sub_row[Designation]."</OPTION>";
									}
									?>
								</SELECT></TD>
								<TD STYLE="font-size:8pt"><INPUT TYPE="text" NAME="NumberOfPackages_add" SIZE=4></TD>
								<TD><INPUT TYPE="submit" NAME="add_pkin" id="add_pkin" VALUE="Add More Pack" CLASS="submit" onClick="setAction('add_pkin','')"></TD>
							</TR>
							<TR><TD colspan="4">&nbsp;</TD><TD><INPUT TYPE="submit" NAME="submit_pkin" id="submit_pkin" VALUE="Submit" CLASS="submit" onClick="setAction('submit_pkin','')">
							<?php if ( $show_cancel ) {?>
								<INPUT TYPE="button" class="submit" VALUE="Cancel" onClick="window.close();">
							<?php } ?> 
							</TD>		
							</TR>	
						<?php } elseif ( $row['PackInID'] != "" ) { 
								echo "<INPUT name=\"PackInId\" id=\"PackInId\" type=\"hidden\" value=\"\">";
								$sql = "SELECT * FROM bscustomerinfopackins where PackInID in (". $row['PackInID'] . ")";
					//			echo "<br />" .$sql . "<br />";
								$bscipk_result = mysql_query($sql,$link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
								$i = 0;
								while ( $row_bscipkin = mysql_fetch_array($bscipk_result) ) {
                                  $i++;
								?>
								 <TR BGCOLOR="#F3E7FD" VALIGN=TOP>
								 <TD STYLE="font-size:8pt"><?php echo $row['CustomerPONumber'];?></TD>
								 <TD STYLE="font-size:8pt"><?php echo $row['CustomerCodeNumber'];?></TD>
								 <TD STYLE="font-size:8pt">
								 <SELECT NAME="PackIn_edit_<?php echo $i;?>" STYLE="font-size: 7pt"><option/>
									<?php
									$sub_sql = "SELECT `ProductNumberInternal` , `Designation` FROM `productmaster` WHERE `ProductNumberInternal` LIKE '6%'";
									$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
									while ( $sub_row = mysql_fetch_array($sub_result) ) {
											echo "<OPTION VALUE='". $sub_row[ProductNumberInternal]."' ".($row_bscipkin['PackIn'] == $sub_row['ProductNumberInternal'] ? "SELECTED":"").">". $sub_row['Designation'] . "</OPTION>";
									}
								?></SELECT></TD>
								  <TD STYLE="font-size:8pt"><INPUT TYPE="text" NAME="NumberOfPackages_edit_<?php echo $i;?>" SIZE=4 VALUE="<?php echo $row_bscipkin['NumberOfPackages'];?>"></TD>
								  <TD><INPUT TYPE="submit" NAME="edit_pkin" id="edit_pkin" VALUE="Edit" CLASS="submit" onClick="setAction('edit_pkin','<?php echo $row_bscipkin['PackInID']."_".$i;?>')"></TD>
								  </TR>
								<?php } //while row_bscipkin ?>
								<TR BGCOLOR="whitesmoke" VALIGN=TOP>
								<TD>&nbsp;</TD>
								<TD>&nbsp;</TD>
								<TD STYLE="font-size:8pt">
								  <SELECT NAME="PackIn_add" STYLE="font-size: 7pt"><option/>
									<?php
									$sub_sql = "SELECT `ProductNumberInternal` , `Designation` FROM `productmaster` WHERE `ProductNumberInternal` LIKE '6%' and `ProductNumberInternal` not in (SELECT PackIn from bscustomerinfopackins where PackInID in (".$row['PackInID'] .") )";
									$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
									while ( $sub_row = mysql_fetch_array($sub_result) ) {
											echo "<OPTION VALUE='$sub_row[ProductNumberInternal]'>" .$sub_row[Designation] ."</OPTION>";
									}
									?>
								</SELECT></TD>
								<TD STYLE="font-size:8pt"><INPUT TYPE="text" NAME="NumberOfPackages_add" SIZE=4></TD>
								<TD><INPUT TYPE="submit" NAME="add_pkin" id="add_pkin" VALUE="Add More Pack" CLASS="submit" onClick="setAction('add_pkin','')"></TD>
							</TR>
							<TR><TD colspan="4">&nbsp;</TD><TD><INPUT TYPE="submit" NAME="submit_pkin" id="submit_pkin" VALUE="Submit" CLASS="submit" onClick="setAction('submit_pkin','')">
							<?php if ( $show_cancel ) {?>
								<INPUT TYPE="button" class="submit" VALUE="Cancel" onClick="window.close();">
							<?php } ?> 
							</TD>		
						
						<?php } //row_packinid ?>
						<?php } //while row ?>
	
						</TABLE>
					<?php
					} else { //if $c > 0
						echo "<I STYLE='font-size:8pt'>No customer orders recorded yet</I>";
					}
					?>
		
					</TD>
				</TR>

			<?php } //edit_po ?>



		</TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE>
<BR>

<script type="text/javascript">
<!--
function setAction(action, packinid) {
	if ( packinid.length > 0 ) {
		document.getElementById("PackInId").value=packinid;
	}
	if ( action == "add_pkin" || action == "submit_pkin" || action=="edit_pkin" ) {
		document.getElementById("action").value=action;
	//	alert("Now set the action value as " + document.getElementById("action").value );
		return true;
	} else {
		alart("the setAction need arguments in add_pkin and submit_pkin " + action );
		return false;
	}
}
-->
</script>

<?php include("inc_footer.php"); ?>