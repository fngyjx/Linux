<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN HAS PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 ) {
	header ("Location: login.php?out=1");
	exit;
}

$note = "";

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( isset($_REQUEST['add_quote']) ) {
	$add_quote = $_REQUEST['add_quote'];
}

if ( isset($_REQUEST['customer_id']) ) {
	$customer_id = $_REQUEST['customer_id'];
}

$action = "";
if ( isset($_REQUEST['action']) ) {
	$action = $_REQUEST['action'];
}

include('inc_global.php');

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];



if ( 'add' == $action and isset($_POST['add_quote']) ) {
	//echo print_r($_POST);
	//die();
	$tmpArr = explode("&nbsp;", $_POST['ProductNumberExternal']);
	$ProductNumberExternal = $tmpArr[0];
	$name = $_POST['name'];
	$customer_id = $_POST['customer_id'];

	// check_field() FUNCTION IN global.php
	check_field($ProductNumberExternal, 1, 'Product Number External');
	check_field($customer_id, 1, 'Customer');

	$pos1 = strpos($ProductNumberExternal, "'");
	$pos2 = strpos($ProductNumberExternal, '"');
	if ( $pos1 !== false or $pos2 !== false ) {
		$error_found = true;
		$error_message .= "'Product Number External' cannot contain an apostrophe or quote<BR>";
	}

	if (  $pos1 === false and $pos2 === false ) {
		$sql = "SELECT ProductNumberInternal FROM externalproductnumberreference WHERE ProductNumberExternal = '" . escape_data($ProductNumberExternal) . "'";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
	}

	if ( $c > 0 and !$error_found ) {

		$row = mysql_fetch_array($result);
		$ProductNumberInternal = $row['ProductNumberInternal'];
		if ( !$error_found ) {

			// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
			// None
			$sql = "INSERT into pricesheetmaster 
					(ProductNumberInternal, CustomerID, DatePriced, Terms, FOBLocation, MinBatch_Units) 
				VALUES 
					('$ProductNumberInternal', '$customer_id', '".date("Y-m-d")."', 'Net - 30 days', 'North Aurora, IL', 'one, 5-Gallon pail')";
			
			start_transaction($link);
			if ( ! mysql_query($sql, $link) ) {
				echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
				end_transaction(0,$link);
				die;
			}
			//$_SESSION['note'] = "Information successfully saved<BR>";
			$psn = mysql_insert_id();

			$sql = "SELECT IngredientProductNumber, IngredientSEQ, Percentage, formulationdetail.VendorID, formulationdetail.Tier, productprices.PricePerPound
			FROM formulationdetail
				LEFT JOIN productprices ON formulationdetail.IngredientProductNumber = productprices.ProductNumberInternal
					AND formulationdetail.VendorID = productprices.VendorID
					AND formulationdetail.Tier = productprices.Tier
			WHERE formulationdetail.ProductNumberInternal = $ProductNumberInternal 
			ORDER BY IngredientSEQ";
			if ( ! $result = mysql_query($sql, $link) ) {
				echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
				end_transaction(0,$link);
				die;
			}
			while ( $row = mysql_fetch_array($result) ) {

				if ( is_numeric($row['PricePerPound']) ) {
					$price = "'" . number_format($row['PricePerPound'], 2) . "'";
				} else {
					$price = "NULL";
				}
				if ( is_numeric($row['VendorID']) ) {
					$VendorID = "'" . $row['VendorID'] . "'";
				} else {
					$VendorID = "NULL";
				}
				if ( $row['Tier'] != '' ) {
					$Tier = "'" . $row['Tier'] . "'";
				} else {
					$Tier = "NULL";
				}

				$sql = "INSERT into pricesheetdetail (PriceSheetNumber, IngredientProductNumber, IngredientSEQ, Percentage, Price, VendorID, Tier) VALUES ('" . $psn . "', '" . $row['IngredientProductNumber'] . "', '" . $row['IngredientSEQ'] . "', '" . $row['Percentage'] . "', " . str_replace(",", "", $price) . ", " . $VendorID . ", " . $Tier . ")";
				if ( ! mysql_query($sql, $link) ) {
					echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
					end_transaction(0,$link);
					die;
					
				}
				end_transaction(1,$link);
				//echo $sql . "<BR>";
			}

			header("location: customers_quotes.header.php?action=edit&psn=" . $psn);
			exit();
		}

	} else {
		$error_found = true;
		$error_message .= "No 'Product Number Internal' found for '".escape_data($ProductNumberExternal)."'<BR>";
	}

}



$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");



if ( isset($_REQUEST['PriceSheetNumber']) and $action == 'search' ) {
	$PriceSheetNumber = $_REQUEST['PriceSheetNumber'];
}
if ( isset($_REQUEST['name']) and $action == 'search' ) {
	$name = $_REQUEST['name'];
	if ( isset($_REQUEST['customer_id']) and $action == 'search' ) {
		$customer_id = $_REQUEST['customer_id'];
	}
} //elseif ( $action == 'search' ) {
	//$customer_id = "";
//}
if ( isset($_REQUEST['ProductNumberExternal']) and $action == 'search' ) {
	$tmpArr = explode("&nbsp;", $_REQUEST['ProductNumberExternal']);
	$ProductNumberExternal = $tmpArr[0];
}
if ( isset($_REQUEST['ProductDesignation']) and $action == 'search' ) {
	$ProductDesignation = $_REQUEST['ProductDesignation'];
}


		
include("inc_header.php");

echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
?>



<script type="text/javascript">

$(function() {
	$('#datepicker1').datepicker({
		changeMonth: true,
		changeYear: true
	});
});

$(function() {
	$('#datepicker2').datepicker({
		changeMonth: true,
		changeYear: true
	});
});

function delete_price(psn) {
	if ( confirm('Are you sure you want to permanently delete this price sheet?') ) {
		document.location.href = "customers_quotes.php?action=delete_price&psn=" + psn;
	}
}

function clone_price(psn) {
	var customer_id=prompt("Please give the customer number for new quote","");
	if ( customer_id != "" ) {
		document.location.href = "customers_quotes.php?action=clone_price&psn=" + psn+"&cid="+customer_id;
	}
}

</script>

<?php if ( ($action == 'search' or $action != 'edit') and ($action != 'add_quote' and $action != 'add') ) { ?>

<table class="bounding">
<tr valign="top">
<td class="padded">
	<FORM ACTION="customers_quotes.php" METHOD="get">
	<INPUT TYPE="hidden" NAME="action" VALUE="search">

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

		<TR>
			<TD><B>Pricesheet ID:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="PriceSheetNumber" VALUE="<?php echo $PriceSheetNumber;?>" SIZE="10"></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR>
			<TD><B>Customer:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" ID="customer" NAME="name" SIZE=30 VALUE="<?php echo stripslashes($name);?>">
			<INPUT TYPE="hidden" ID="customer_id" NAME="customer_id" VALUE="<?php echo stripslashes($customer_id);?>"></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR>
			<TD><B>abelei External Number:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" ID="ProductNumberExternal" NAME="ProductNumberExternal" SIZE=30 VALUE="<?php echo stripslashes($ProductNumberExternal);?>"></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR>
			<TD><B>Flavor name:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="ProductDesignation" SIZE=30 VALUE="<?php echo stripslashes($ProductDesignation);?>"></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR>
			<TD><B>Date quoted:</B>&nbsp;&nbsp;&nbsp;</TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD>
			<INPUT TYPE="text" SIZE="12" NAME="start_date" id="datepicker1" VALUE="<?php
				if ( $start_date != '' ) {
					echo date("m/d/Y", strtotime($start_date));
				}
				?>">
				to 
			<INPUT TYPE="text" SIZE="12" NAME="end_date" id="datepicker2" VALUE="<?php
				if ( $end_date != '' ) {
					echo date("m/d/Y", strtotime($end_date));
				}
				?>">
			</TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="12"></TD></TR>

		<TR>
			<TD colspan="3">
				<INPUT style="float:right" TYPE="submit" VALUE="Search" CLASS="submit_medium" />
				<button type="button" style="margin-top:1em" CLASS="submit new" id="new" name="new">New formula quote</button>
			</TD>
		</TR>
	</TABLE>
	</FORM>
	</TD></TR></TABLE>
<BR>

<?php

}



if ( $action == 'search' ) {

	if ( $PriceSheetNumber != '' ) {
		$PriceSheetNumber_clause = " AND PriceSheetNumber = " . $PriceSheetNumber;
	} else {
		$PriceSheetNumber_clause = "";
	}
	if ( $name != '' and $customer_id != '' ) {
		$CustomerID_clause = " AND CustomerID = " . $customer_id;
	} else 
	if ( '' != $name ) {
		$CustomerID_clause = " AND customers.name LIKE ('%$name%')";
	} 
	else {
		$CustomerID_clause = "";
	}
	if ( $ProductNumberExternal != '' ) {
		$ProductNumberExternal_clause = " AND ProductNumberExternal LIKE '%" . $ProductNumberExternal . "%'";
	} else {
		$ProductNumberExternal_clause = "";
	}
	if ( $ProductDesignation != '' ) {
		$ProductDesignation_clause = " AND ProductDesignation LIKE '%" . $ProductDesignation . "%'";
	} else {
		$ProductDesignation_clause = "";
	}

	if ( $start_date != '' OR $end_date != '' ) {
		$start_date_clause = "";
		$end_date_clause = "";
		if ( '' != $start_date) {
			$start_date_parts = explode("/", $start_date);
			$mysql_start_date = $start_date_parts[2] . "-" . $start_date_parts[0] . "-" . $start_date_parts[1];
			$start_date_clause = "DatePriced >= '$mysql_start_date'";
			if ( '' != $end_date) $start_date_clause .= " AND ";
		}
		if ( '' != $end_date) {
			$end_date_parts = explode("/", $end_date);
			$mysql_end_date = $end_date_parts[2] . "-" . $end_date_parts[0] . "-" . $end_date_parts[1];
			$end_date_clause = "DatePriced <= '$mysql_end_date'";
		}
		$date_filter = " AND ( $start_date_clause $end_date_clause )";
	} else {
		$date_filter = "";
	}

	$sql = "SELECT pricesheetmaster.*, customers.*, externalproductnumberreference.*, productmaster.Designation FROM pricesheetmaster
	LEFT JOIN customers ON pricesheetmaster.CustomerID = customers.customer_id
	LEFT JOIN productmaster ON productmaster.ProductNumberInternal = pricesheetmaster.ProductNumberInternal
	INNER JOIN externalproductnumberreference ON externalproductnumberreference.ProductNumberInternal= pricesheetmaster.ProductNumberInternal
	WHERE 1=1 " . $PriceSheetNumber_clause . $CustomerID_clause . $ProductNumberExternal_clause . $ProductDesignation_clause . $date_filter . 
	" ORDER BY DatePriced DESC";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	// echo $sql . "<BR>";

	if ( $c > 0 ) {

		$bg = 0; ?>

		<FORM><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">

			<TR VALIGN=BOTTOM>
				<TD>&nbsp;</TD>
				<TD><B>ID</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B>Date quoted</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Customer</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>abelei#</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Flavor</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=CENTER><B>Original</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=CENTER><B>Locked</B></TD>
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

				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD>
					<A HREF="JavaScript:clone_price(<?php echo $row['PriceSheetNumber'];?>)"><IMG SRC="images/copy.jpg" WIDTH="16" HEIGHT="16" BORDER="0"></A>
					<A HREF="JavaScript:delete_price(<?php echo $row['PriceSheetNumber'];?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A>
					</TD>
					<TD><A HREF="customers_quotes.header.php?psn=<?php echo $row['PriceSheetNumber'];?>"><?php echo $row['PriceSheetNumber'] ?></A></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><?php
					if ( $row['DatePriced'] != '' ) {
						echo date("n/j/Y", strtotime($row['DatePriced']));
					}
					?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<?php
					if ("" != $row[customer_id]) {
						$sql = "SELECT name FROM customers WHERE customer_id = $row[customer_id]";
						$result_cust = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
						$row_cust = mysql_fetch_array($result_cust);
						echo "<TD><NOBR>$row_cust[name]</NOBR></TD>";
					}
					else {
						echo "<TD style=\"background-color:red\"><nobr><b style=\"color:white\">No Customer Selected<b></nobr></TD>";
					}
					?>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['ProductNumberExternal'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['Designation'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD ALIGN=CENTER><?php
					if ( $row['Original_From_Formulation'] == 0 ) {
						echo "No";
					} else {
						echo "Yes";
					}
					?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD ALIGN=CENTER><?php echo $row['locked'] ? "Yes" : "No" ?></TD><!-- 

					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><INPUT TYPE="button" VALUE="Edit" CLASS="submit" onClick="window.location='flavors_formulations.php?action=edit&pne=<?php //echo $row['ProductNumberExternal']?>'" STYLE="font-size:7pt"></TD>
 -->
				</TR>

			<?php } ?>

		</TABLE></FORM>

	<?php } else {
		echo "No matches found in database<BR><BR>";
	}
}

?>


<?php if ( $action == 'add_quote' or $action == 'add' ) { ?>
    <?php $name = $_REQUEST['name']; 
    	  $customer_id = $_REQUEST['customer_id'];
    	  $ProductNumberExternal = $_REQUEST['ProductNumberExternal']; ?>
	<?php if ( $error_found ) {
		echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
	} ?>

	<?php if ( $note ) {
		echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
	} ?>

	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD ALIGN=CENTER>
	<FORM NAME="add_ingredient" ACTION="customers_quotes.php" METHOD="post">
	<INPUT TYPE="hidden" NAME="action" VALUE="add">
	<INPUT TYPE="hidden" NAME="add_quote" VALUE="1">

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">

		<TR>
			<TD COLSPAN=2><B>Add formula quote</B><BR><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="10"><BR></TD>
		</TR>

		<TR>
			<TD><B CLASS="black">Customer:</B></TD>
			<TD><INPUT TYPE="text" ID="customer" NAME="name" SIZE=30 VALUE="<?php echo stripslashes($name);?>">
			<INPUT TYPE="hidden" ID="customer_id" NAME="customer_id" VALUE="<?php echo stripslashes($customer_id);?>"></TD>
		</TR>

		<TR>
			<TD><B CLASS="black">abelei External Number:</B></TD>
			<TD><INPUT TYPE="text" ID="ProductNumberExternal" NAME="ProductNumberExternal" SIZE=30 VALUE="<?php echo stripslashes($ProductNumberExternal);?>"></TD>
		</TR>

		<TR>
			<TD></TD>
			<TD><INPUT TYPE="button" VALUE="Cancel" CLASS="submit" onClick="window.location='customers_quotes.php'"> <INPUT TYPE="submit" VALUE="Save" CLASS="submit"></TD>
		</TR>

	</TABLE>

	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</FORM><BR>

<?php } ?>

<?php if ( $action == 'delete_price' ) { 
	
	if ( $note ) {
		echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
	}

	$psn = $_REQUEST['psn']; 
    	 
	$sql = "SELECT * FROM pricesheetmaster where PriceSheetNumber = '" . escape_data($psn) . "'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	
	if ( $c < 1) {
		echo "Cannot find Price Sheet $psn in PriceSheetMaster Table";
		exit();
	}
	
	$sql = "SELECT * FROM pricesheetdetail where PriceSheetNumber = '" . escape_data($psn) . "'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	
	if ( $c < 1) {
		echo "Cannot find Price Sheet $psn in PriceSheetDetail Table";
		exit();
	}
	
	start_transaction($link);
	
	$sql = "DELETE FROM pricesheetdetail where PriceSheetNumber = '" . escape_data($psn) . "'";
	
	if ( ! mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}
	
	$sql = "DELETE FROM pricesheetmaster where PriceSheetNumber = '" . escape_data($psn) . "'";
	
	if ( ! mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}
	
	end_transaction(1,$link);
	
	$note = "Price Sheet $psn has been successfully removed";
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
		
	exit();;
}

if ( $action == 'clone_price' ) { 
	
	if ( $note ) {
		echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
	}

	$psn = $_REQUEST['psn'];
	$cid = $_REQUEST['cid'];
	
	$sql="SELECT * FROM customers WHERE customer_id = ".$cid;
	
	$result=mysql_query($sql,$link) or die ( mysql_error() ." failed execute SQL : $sql<br />");
	if ( mysql_num_rows($result) <1  ) {
		echo "Cannot find customer number $cid in Customers Table";
		exit();
    }	 
	
	$sql="SELECT MAX(PriceSheetNumber)+1 FROM pricesheetmaster";
	$result=mysql_query($sql,$link) or die(mysql_error() ." Failed to execute SQL $sql<br />");
//	echo "<BR /> $sql<br />";
	$row=mysql_fetch_array($result);
	$new_psn=$row[0];
	$sql = "INSERT INTO pricesheetmaster SELECT * FROM pricesheetmaster where PriceSheetNumber = '" . escape_data($psn) . "'
	ON DUPLICATE KEY UPDATE PriceSheetNumber='".$new_psn."'";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	//echo "<BR /> $sql </BR>";
	
	
	$sql="UPDATE pricesheetmaster set CustomerID='".$cid."' WHERE PriceSheetNumber='".$new_psn."'";
	
	mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
//	echo "<BR /> $sql </BR>";	
	$note = "Price Sheet $psn has been successfully cloned to $new_psn with customer number $cid";
	$_SESSION['note']=$note;
	
	echo "<SCRIPT language='javascript'>document.location.href='customers_quotes.php?action=search&PriceSheetNumber=".$new_psn."'</SCRIPT>";
		
	exit();
}
				
 ?>

<script>
	$(document).ready(function(){

	$("#customer").autocomplete("search/customers_by_name.php", {
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#customer").result(function(event, data, formatted) {
		if (data)
			document.getElementById("customer_id").value = data[1];
	});
	$("#ProductNumberExternal").autocomplete("search/external_product_numbers.php", {
		cacheLength: 1,
		width: 650,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#ProductNumberExternal").result(function(event, data, formatted) {
		if (data){
		  var datastr = String(data);
		  var rcrdindx = datastr.indexOf('&nbsp;');
		  datastr=datastr.substr(0,rcrdindx);
		  if ( datastr.length > 0) 
		  {
		  	 $("#ProductNumberExternal").val(encodeURIComponent(datastr));
		  }
		}
	});
	
	$("#ProductNumberInternal").autocomplete("search/internal_product_numbers.php", {
		cacheLength: 1,
		width: 650,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#ProductNumberInternal").result(function(event, data, formatted) {
		if (data){
		  var datastr = String(data);
		  var rcrdindx = datastr.indexOf('&nbsp;');
		  datastr=datastr.substr(0,rcrdindx);
		  $("#ProductNumberInternal").val(datastr);
		}
	});
	$("#designation").autocomplete("search/designations.php", {
		cacheLength: 1,
		width: 350,
		max:50,
    scroll: true,
		scrollheight: 350,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		multipleSeparator: "¬",
 		selectFirst: false
	});
	$("#new").click(function() {
		var trgt_lct="customers_quotes.php?action=add_quote";
		if ($("#customer").val().length > 0 )
			trgt_lct += "&name=" + $("#customer").val();
		if ($("#customer_id").val().length > 0 )
			trgt_lct += "&customer_id=" + $("#customer_id").val();
		if ($("#ProductNumberExternal").val().length > 0)
			trgt_lct += "&ProductNumberExternal=" + $("#ProductNumberExternal").val();
		window.location = trgt_lct;
	});
});
</script>

<BR><BR>

<?php include("inc_footer.php"); ?>