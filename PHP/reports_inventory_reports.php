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

$action = "";
if ( isset($_REQUEST['action']) ) {
	$action = $_REQUEST['action'];
}

include('inc_global.php');

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

if ( isset($_REQUEST['ProductNumberExternal']) and $action == 'search' ) {
	$ProductNumberExternal = $_REQUEST['ProductNumberExternal'];
}
if ( isset($_REQUEST['ProductNumberInternal']) and $action == 'search' ) {
	$ProductNumberInternal = $_REQUEST['ProductNumberInternal'];
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

</script>

<?php if ( $action == '' ) { ?>

<table class="bounding">
<tr valign="top">
<td class="padded">
	<FORM ACTION="reports_inventory_reports.php" METHOD="get">
	<INPUT TYPE="hidden" NAME="action" VALUE="search">

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

		<TR>
			<TD><B>Storage</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><SELECT NAME="lots_storage">
				<OPTION VALUE="NULL"></OPRION>
				<OPTION VALUE="lab">Lab</OPRION>
				<OPTION VALUE="Lab Fridge">Lab Fridge</OPRION>
				<OPTION VALUE="Walk-in Cooler">Walk-in Cooler</OPRION>
				<OPTION VALUE="Warehouse">Warehouse</OPRION>
				<OPTION VALUE="RM Room">RM Room</OPRION>
				<OPTION VALUE="Oxy Dry">Oxy Dry</OPRION>
				<OPTION VALUE="ORPHAN">ORPHAN</OPRION>
				<OPTION VALUE="Unknown">Unknown</OPRION>
			</SELECT>
			</TD>
			<TD>
				<INPUT TYPE="CHECKBOX" name="hide_zero"> &nbsp;Hide Empty Lots</INPUT>
			</TD> 
		</TR>
		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>
		
		<TR>
			<TD><B>Abelei Number:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="ProductNumberExternal" VALUE="" SIZE="30"></TD><TD>incomplete # for group list</TD> 
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		<TR>
			<TD><B>Internal Number:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="ProductNumberInternal" SIZE=30></TD><TD>incomplete # for group list</TD>
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
			</TD>
		</TR>
	</TABLE>
	</FORM>
	</TD></TR></TABLE>
<BR>

<?php

}

if ( $action == 'search' ) {
	
	$lots_storage=$_REQUEST['lots_storage'];

	if ( isset($_REQUEST['hide_zero'] ) ) {
		$hide_zero=1;
	} else {
		$hide_zero=0;
	}
	
	if ( $ProductNumberInternal != '' ) {
		$ProductNumberInternal_clause = " AND pm.ProductNumberInternal like '" . $ProductNumberInternal ."%'";
	} else {
		$ProductNumberInternal_clause = "";
	}
	
	if ( $ProductNumberExternal != '' ) {
		$ProductNumberExternal_clause = " AND ProductNumberExternal LIKE '%" . $ProductNumberExternal . "%'";
	} else {
		$ProductNumberExternal_clause = "";
	}
	
	if ( $start_date != '' OR $end_date != '' ) {
		$start_date_clause = "";
		$end_date_clause = "";
		if ( '' != $start_date) {
			$start_date_parts = explode("/", $start_date);
			$mysql_start_date = $start_date_parts[2] . "-" . $start_date_parts[0] . "-" . $start_date_parts[1];
			$start_date_clause = "datemanufactured >= '$mysql_start_date'";
			if ( '' != $end_date) $start_date_clause .= " AND ";
		}
		if ( '' != $end_date) {
			$end_date_parts = explode("/", $end_date);
			$mysql_end_date = $end_date_parts[2] . "-" . $end_date_parts[0] . "-" . $end_date_parts[1];
			$end_date_clause = "datemanufactured <= '$mysql_end_date'";
		}
		$date_filter = " AND ( $start_date_clause $end_date_clause )";
	} else {
		$date_filter = "";
	}

	$sql="select im.productnumberinternal as 'ProductNumberInternal',ProductNumberExternal, concat(l.lotnumber,'-',l.lotsequencenumber) as lotsnumber, l.datemanufactured,l.expirationdate,
l.storagelocation,vendors.name as vendor_name, (select vendorproductcode from vendorproductcodes where vendorid=l.vendorid and productnumberinternal=im.productnumberinternal) as vendorprdcode,
pm.FEMA_NBR, concat(pm.designation,'-',pm.natural_Or_artificial,'-',pm.kosher) as description,
Quantityconvert(sum(im.quantity*(select inventorymultiplier from inventorytransactiontypes where transactionid=im.transactiontype)),'grams',pm.unitofmeasure) as total,
pm.unitofmeasure from lots as l
left join vendors on vendors.vendor_id = l.vendorid
left join inventorymovements as im on im.lotid=l.id
left join productmaster as pm on pm.productnumberinternal=im.productnumberinternal
left join externalproductnumberreference as ex on pm.productnumberinternal=ex.productnumberinternal
where l.vendorid <> 2382 and l.storagelocation='".$lots_storage."' and im.movementstatus='C'";
	$sql .= $ProductNumberInternal_clause . $date_filter . $ProductNumberExternal_clause . " group by lotsnumber";

	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	// echo $sql . "<BR>";

	if ( $c > 0 ) {

		$bg = 0; ?>

		<FORM><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">
			<TR VALIGN=BOTTOM>
				<TH>Product#Internal</TH>
				<TH><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TH>
				<TH>Lot#</TH>
				<TH><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TH>
				<TH>Date Manufactured</TH>
				<TH><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TH>
				<TH>Expr Date</TH>
				<TH><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TH>
				<TH>Storage</TH>
				<TH><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TH>
				<TH>Vendor</TH>
				<TH><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TH>
				<TH>vendorprdcode</TH>
				<TH><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TH>
				<TH>FEMA#</TH>
				<TH><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TH>
				<TH>Description</TH>
				<TH><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TH>Amount</TH>
			</TR>

			<?php 

			while ( $row = mysql_fetch_array($result) ) {
			    if ( $hide_zero == 1 and abs($row[10]) < 0.000001 ) {
					continue;
				}
				if ( $bg == 1 ) {
					$bgcolor = "#F3E7FD";
					$bg = 0;
				} else {
					$bgcolor = "whitesmoke";
					$bg = 1;
				} ?>

				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD><?php echo $row[0]."<BR>".$row[1];?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row[2];?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row[3];?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row[4];?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row[5];?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row[6];?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row[7];?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row[8];?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row[9];?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><NOBR><?php echo $row[10]." ".$row[11];?></NOBR></TD>
				</TR>

			<?php } ?>

		</TABLE></FORM>

	<?php } else {
		echo "No matches found in database<BR><BR>";
	}
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