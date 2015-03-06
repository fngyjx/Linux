<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

//SELECT * 
//FROM customerordermaster
//LEFT JOIN customerorderdetail ON customerordermaster.OrderNumber = customerorderdetail.CustomerOrderNumber
//WHERE CustomerPONumber = '4500470957'

include('../inc_global.php');

$sql = "SELECT DISTINCT cstm.OrderNumber, RequestedDeliveryDate, cstm.CustomerPONumber,
 cstd.CustomerOrderSeqNumber, externalproductnumberreference.ProductNumberInternal, Quantity, PackSize,
 cstd.UnitOfMeasure, Natural_OR_Artificial, Designation, ProductType, pm.Kosher,
 externalproductnumberreference.ProductNumberExternal, name, ConfirmedToCustomer, ConfirmedBy, 
 CONCAT( customer_contacts.first_name,  ' ', customer_contacts.last_name ) AS contact_name,
 CONCAT( customer_addresses.address1,  ' ', customer_addresses.address2, ' ', customer_addresses.city, ' ', customer_addresses.state ) AS ship_to_address, bsm.BatchSheetNumber,
 bsm.CommitedToInventory AS committed, bsm.Manufactured AS manufactured,
 NetWeight,Yield,TotalQuantityUnitType,Column1UnitType,Column2UnitType,
 NumberOfTimesToMake
FROM customerordermaster as cstm
LEFT JOIN customerorderdetail as cstd ON cstm.OrderNumber = cstd.CustomerOrderNumber
LEFT JOIN productmaster as pm ON cstd.ProductNumberInternal = pm.ProductNumberInternal
LEFT JOIN customers ON cstm.CustomerID = customers.customer_id
LEFT JOIN customer_contacts ON cstm.ContactID = customer_contacts.contact_id
LEFT JOIN customer_addresses ON cstm.ShipToLocationID = customer_addresses.address_id
LEFT JOIN batchsheetcustomerinfo AS bsci ON bsci.CustomerOrderNumber = cstm.OrderNumber AND  bsci.CustomerOrderSeqNumber = cstd.CustomerOrderSeqNumber
LEFT JOIN batchsheetmaster AS bsm ON bsm.BatchSheetNumber = bsci.BatchSheetNumber AND
bsm.ProductNumberInternal = cstd.ProductNumberInternal
LEFT JOIN externalproductnumberreference ON (pm.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal)
WHERE (ShipDate IS NULL OR ShipDate = '')
ORDER BY RequestedDeliveryDate ASC, cstd.ProductNumberInternal, cstm.OrderNumber";
//  GROUP BY customerordermaster.OrderNumber
$result = mysql_query($sql, $link) or die (mysql_error());

?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="790" ALIGN=CENTER><TR><TD>



<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD WIDTH="60"><IMG SRC="../images/abelei_logo.png" WIDTH="60" BORDER="0"></TD>
		<TD WIDTH="10"><IMG SRC="../images/spacer.gif" WIDTH="10" BORDER="0"></TD>
		<TD WIDTH="80" VALIGN=MIDDLE STYLE="font-size:8pt"><NOBR>clever able capable</NOBR><BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<IMG SRC="../images/abelei_font.png" WIDTH="80" BORDER="0"><!-- <BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<SPAN STYLE="font-size:7pt">US INGREDIENTS</STYLE> --></TD>
		<TD ALIGN=RIGHT STYLE="font-size:8pt"><?php echo date("l, F j, Y")?></TD>
	</TR>
</TABLE><BR><BR>



<TABLE BORDER="0" WIDTH="790" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>

<B CLASS="header">Customer Order Review</B><BR><BR>



<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="2">
	<TR VALIGN=BOTTOM>
		<TD><B CLASS="black" STYLE="font-size:8pt">Due Date</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Product</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Customer Name</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Customer PO#</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Contact Name</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Customer Confirmed</B></TD>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Qty</B></TD>
		<TD ALIGN=RIGHT><B CLASS="black" STYLE="font-size:8pt">Pack Size</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Units</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Batch<br/>Status</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Location</B></TD>
	</TR>

	<?php
	$current_date = '';
	$current_con = '';
	$current_pni = '';
	$current_cosq = '';
	while ( $row = mysql_fetch_array($result) ) {
		if ( ($current_con != $row["OrderNumber"]) or ( $current_con == $row["OrderNumber"] and 
			( $current_pne != $row["ProductNumberExternal"] or $current_cosq != $row["CustomerOrderSeqNumber"] ) ) ) {
			$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "" ) . " - " . $row['ProductNumberInternal'];
			//Key ingredient search
			if ( $row['BatchSheetNumber'] != "" ) { // this query lost the key(s) of product that not made by abelei
				$sql_key="SELECT bsd.*, externalproductnumberreference.ProductNumberInternal, 
			Natural_OR_Artificial, Designation, ProductType, pm.Kosher,
			externalproductnumberreference.ProductNumberExternal FROM batchsheetdetail as bsd
			LEFT JOIN productmaster AS pm ON pm.ProductNumberInternal=bsd.IngredientProductNumber
			LEFT JOIN externalproductnumberreference on pm.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
			WHERE BatchSheetNumber=$row[BatchSheetNumber] 
				AND IngredientProductNumber LIKE '2%'"; 
			} else {
				$sql_key="select formulationdetail.* , pm.Natural_OR_Artificial, pm.Designation, pm.ProductType, pm.Kosher,
			externalproductnumberreference.ProductNumberExternal FROM formulationdetail
			LEFT JOIN productmaster AS pm ON pm.ProductNumberInternal=formulationdetail.IngredientProductNumber
			LEFT JOIN externalproductnumberreference on formulationdetail.IngredientProductNumber = externalproductnumberreference.ProductNumberInternal
			WHERE formulationdetail.ProductNumberInternal='".$row['ProductNumberInternal']."' AND formulationdetail.IngredientProductNumber LIKE '2%'";
			}
		//	echo "<br /> $sql_key <br />";
			$result_key = mysql_query($sql_key,$link) or die ( mysql_error() . " Failed Execute SQL: $sql_key<br />");

			?>
			<TR>
				<TD><?php
				if ( $current_date != $row["RequestedDeliveryDate"] ) {
					echo date("m/d/Y", strtotime($row["RequestedDeliveryDate"]));
				}
				?>&nbsp;</TD>
				<TD><?php echo $row["ProductNumberExternal"] . " " . $ProductDesignation;?>&nbsp;</TD>
				<TD><?php echo $row['name'];?>&nbsp;</TD>
				<TD><?php echo $row['CustomerPONumber'];?>&nbsp;</TD>
				<TD><?php echo $row['contact_name'];?>&nbsp;</TD>
				<?php if ( $row['ConfirmedToCustomer'] ) { ?>
					<TD><B>Yes</B>&nbsp;<?php echo $row['ConfirmedBy'];?></TD>
				<?php } else { ?>
					<TD><B>Not Yet</B>&nbsp;</TD>
				<?php } ?>
				<TD ALIGN=RIGHT>&nbsp;<?php echo number_format($row['Quantity'], 2);?></TD>
				<TD ALIGN=RIGHT>&nbsp;<?php echo number_format($row['PackSize'], 2);?></TD>
				<TD><?php echo $row['UnitOfMeasure'];?>&nbsp;</TD>
				<TD><?php 
					if (0 != $row[manufactured] )
						$status = "Mfg'd";
					else
					if (0 != $row[committed] )
						$status = "Comt'd";
					else
						$status = "NONE";
					echo $status;
				?>&nbsp;</TD>
				<TD><?php echo $row['ship_to_address'];?>&nbsp;</TD>
			</TR>
			<?php
			
			while (	$row_key = mysql_fetch_array($result_key) ) {
			  $ProductDesignation = ("" != $row_key['Natural_OR_Artificial'] ? $row_key['Natural_OR_Artificial']." " : "").$row_key['Designation'].("" != $row_key['ProductType'] ? " - ".$row_key['ProductType'] : "").("" != $row_key['Kosher'] ? " - ".$row_key['Kosher'] : "" ) ;
			  if ( $row['BatchSheetNumber'] != "" ) {
			    $ProductDesignation .=  " - " . $row_key['ProductNumberInternal'];
				if ( $row_key['SubBatchSheetNumber'] == "" ) {
					$ProductDesignation .=	"<form id=\"add_key\" name=\"add_key\" action=\"../customers_batch_sheets.php\" method=\"post\" target=\"_blank\">\n".
				"<input type=\"hidden\" id=\"external_number\" name=\"external_number\" value=\"". $row_key['ProductNumberExternal'] ."\">\n".
				"<input type=\"hidden\" id=\"NetWeight\" name=\"NetWeight\" value=\"". ($row['PackSize']*$row_key['Percentage']*0.01*$row['Quantity'])/$row['Yield'] ."\">\n".
				"<input type=\"hidden\" id=\"TotalQuantityUnitType\" name=\"TotalQuantityUnitType\" value=\"".$row['UnitOfMeasure'] ."\">\n".
				"<input type=\"hidden\" id=\"Column1UnitType\" name=\"Column1UnitType\" value=\"". $row['Column1UnitType'] ."\">\n".
				"<input type=\"hidden\" id=\"Column2UnitType\" name=\"Column2UnitType\" value=\"".$row['Column2UnitType']."\">\n".
				"<input type=\"hidden\" id=\"Yield\" name=\"Yield\" value=\"".$row['Yield']."\">\n".
				"<input type=\"hidden\" id=\"NumberOfTimesToMake\" name=\"NumberOfTimesToMake\" value=\"1\">\n".
				"<input type=\"hidden\" id=\"BatchSheetNumber\" name=\"BatchSheetNumber\" value=\"".$row['BatchSheetNumber']."\">\n".
                "<input type=\"hidden\" id=\"CustomerOrderNumber\" name=\"CustomerOrderNumber\" value=\"".$row['OrderNumber']."\">\n".
				"<input type=\"hidden\" id=\"IngredientSEQ\" name=\"IngredientSEQ\" value=\"".$row_key['IngredientSEQ']."\">\n".
				"<input type=\"submit\" class=\"submit new\" name=\"new_sheet_key\" id=\"new_sheet_key\" value=\"New Batch Sheet For Key\">\n".
				"</form>\n";
				} else {
					$ProductDesignation .= "<br />Make in BatchSheet# ". $row_key['SubBatchSheetNumber']; 
				}
			  } // end if row batchsheetnumber != ''
			  else {
				$ProductDesignation .=  " - " . $row_key['IngredientProductNumber'];
			  }
			  $yield = ( $row['Yield'] == "" ) ? 1.0 : $row['Yield'] ;
			 // echo " yeld = $yield<br />";
			?>
			<TR>
				<TD>&nbsp;</TD>
				<TD><?php echo $row_key["ProductNumberExternal"] . " " . $ProductDesignation;?>&nbsp;</TD>
				<TD><?php echo $row['name'];?>&nbsp;</TD>
				<TD><?php echo $row['CustomerPONumber'];?>&nbsp;</TD>
				<TD><?php echo $row['contact_name'];?>&nbsp;</TD>
				<TD>&nbsp;</TD>
				<TD ALIGN=RIGHT>&nbsp;<?php echo number_format($row['Quantity'], 2);?></TD>
				<TD ALIGN=RIGHT>&nbsp;<?php echo number_format(($row['PackSize']*$row_key['Percentage']*0.01)/$yield , 2);?></TD>
				<TD><?php echo $row['UnitOfMeasure'];?>&nbsp;</TD>
				<TD><?php 
					if (0 != $row[manufactured] )
						$status = "Mfg'd";
					else
					if (0 != $row[committed] )
						$status = "Comt'd";
					else 
					if ( '' != $row['BatchSheetNumber'] ) 
						$status = "New";
					else
						$status = "NONE";
					echo $status;
				?>&nbsp;</TD>
				<TD><?php echo $row['ship_to_address'];?>&nbsp;</TD>
			</TR>
			<?php }
		}
		$current_date = $row["RequestedDeliveryDate"];
		$current_con = $row["OrderNumber"];
		$current_pne = $row["ProductNumberExternal"];
		$current_cosq = $row["CustomerOrderSeqNumber"];
	} ?>

</TABLE>



		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE><BR><BR>
</BODY>
</HTML>