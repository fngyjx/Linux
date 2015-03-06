<?php



function batchsheet_info_search_npbsn($clause,$link) {
	
		$sql = "SELECT DISTINCT BatchSheetNumber, bsm.LotID as LotID, DateManufactured, NetWeight, NumberOfTimesToMake, LotSequenceNumber, CommitedToInventory, ".
	" Manufactured, pm.ProductNumberInternal, pm.SpecificGravity, pm.SpecificGravityUnits, pm.Organic, pm.Natural_OR_Artificial, ".
	" pm.Designation, pm.ProductType, pm.Kosher, bsm.ProductNumberExternal, lots.DateManufactured as DateManufactured, ".
	" lots.LotNumber as abeleiLotNumber, lots.LotSequenceNumber as LotSequenceNumber, lots.QualityControlDate, pm.UnitOfMeasure, ".
	" bsm.TotalQuantity, bsm.TotalQuantityUnitType ".
	" FROM batchsheetmaster AS bsm ".
	" LEFT JOIN productmaster AS pm ON bsm.ProductNumberInternal = pm.ProductNumberInternal ".
	" LEFT JOIN lots ON bsm.LotID = lots.ID ".
	" WHERE ( 1 $clause ) ".
	" ORDER BY if( Mid( bsm.ProductNumberExternal, 1, 2 ) = 'US', bsm.ProductNumberExternal, BuildExternalSortKeyField1( bsm.ProductNumberExternal) ),".
	" if( Mid( bsm.ProductNumberExternal, 4, 1 ) = 'a', 0, bsm.ProductNumberExternal ), BuildExternalSortKeyField3( bsm.ProductNumberExternal),".
	" BuildExternalSortKeyField4( bsm.ProductNumberExternal)";
	
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql in batchsheet_info_search_npbsn<BR><BR>");
	$c = mysql_num_rows($result);
	
	
	if ( $c > 0) {
		$i = 0;
		while ( $row = mysql_fetch_array($result)) {
			$row_arr[$i] = $row;
			$i++;
		}
		return $row_arr;
	}
	else 
		return NULL;
		
}

function get_batchsheet_custname($batchsheetNumber, $link) {
		$sql = "SELECT DISTINCT name ".
		" FROM batchsheetcustomerinfo AS bsci ".
		" LEFT JOIN customerorderdetail AS c ON c.CustomerOrderNumber = bsci.CustomerOrderNumber AND ".
		" c.CustomerOrderSeqNumber = bsci.CustomerOrderSeqNumber AND bsci.BatchSheetNumber = " . $batchsheetNumber .
		" LEFT JOIN customerordermaster ON c.CustomerOrderNumber = customerordermaster.OrderNumber " .
		" LEFT JOIN customers ON customers.customer_id = customerordermaster.CustomerID " .
		" WHERE bsci.BatchSheetNumber = " . $batchsheetNumber;
		$result_cust = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql in get_batchsheet_custname<BR><BR>");
		$cc = mysql_num_rows($result_cust);
		if ( $cc > 0 ) {
			$row_cust = mysql_fetch_array($result_cust);
			return $row_cust['name'];
		 } else {
			return "";
		}
}
					
function get_batchsheet_custinfo($batchsheetNumber, $link, $debug) {
	if ($debug > 0) 
 	   echo "I am ok in func ln 59 <br />";
	$sql = "SELECT bsci.BatchSheetNumber, bsci.CustomerOrderNumber,bsci.CustomerOrderSeqNumber, bsci.CustomerCodeNumber,bsci.Description,
		PackIn, PackInID, NumberOfPackages, InventoryTransactionNumber, customerordermaster.CustomerPONumber, 
		customerordermaster.RequestedDeliveryDate, c.CustomerCodeNumber AS ccn, c.Quantity, c.PackSize, c.TotalQuantityOrdered, name
			FROM batchsheetcustomerinfo AS bsci 
			LEFT JOIN customerorderdetail AS c 
			ON c.CustomerOrderNumber = bsci.CustomerOrderNumber
			AND c.CustomerOrderSeqNumber = bsci.CustomerOrderSeqNumber 
			AND bsci.BatchSheetNumber = ". $batchsheetNumber ."
			LEFT JOIN customerordermaster 
			ON c.CustomerOrderNumber = customerordermaster.OrderNumber
			LEFT JOIN customers 
			ON customers.customer_id = customerordermaster.CustomerID 
			WHERE bsci.BatchSheetNumber = ".$batchsheetNumber;
	if ($debug > 0 )
		echo $sql . "<br />";
		
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR /><BR />");
	$c = mysql_num_rows($result);
	if ( $c > 0) {
		$i = 0;
		while ( $row = mysql_fetch_array($result)) {
			$row_arr[$i] = $row;
			$i++;
		}
		return $row_arr;
	}
	else {
		return NULL;
	}
		
}

function get_batchsheet_detail($batchsheetNumber, $link, $debug) {
	if ($debug > 0)
		echo "func get_batchsheet_detail $batchsheetNumber, <br />";
	
$sql = "SELECT *, productmaster.Natural_OR_Artificial Natural_OR_Artificial , productmaster.Organic Organic, productmaster.Kosher Kosher,".
	" productmaster.Designation, productmaster.ProductType, vendors.name, Quantity ".
	" FROM batchsheetdetail ".
	" LEFT JOIN inventorymovements ON batchsheetdetail.InventoryTransactionNumber = inventorymovements.TransactionNumber ".
	" LEFT JOIN productmaster ON batchsheetdetail.IngredientProductNumber = productmaster.ProductNumberInternal ".
	" LEFT JOIN vendors ON vendors.vendor_id = batchsheetdetail.VendorID ".
	" WHERE batchsheetdetail.BatchSheetNumber = '$batchsheetNumber' ".
	" ORDER BY IngredientSEQ";
	if ($debug > 0) 
		echo $sql . '<br />';

	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR /><BR />");
	$c = mysql_num_rows($result);
	if ( $c > 0) {
		$i = 0;
		while ( $row = mysql_fetch_array($result)) {
			$row_arr[$i] = $row;
			$i++;
		}
		return $row_arr;
	}
	else {
		return NULL;
	}
		
}		

function select_bsd_with_units($batchsheetNumber,$link,$debug) {
	if ($debug > 0)
		echo "in select_bsd_with_units func bsn=$batchsheetNumber, $debug <br />";
	$sql = "SELECT bsd.*, pm.Designation, pm.UnitOfMeasure, im.Quantity ". 
	" FROM batchsheetdetail AS bsd, inventorymovements AS im, productmaster AS pm ". 
	" WHERE bsd.InventoryTransactionNumber = im.TransactionNumber AND bsd.IngredientProductNumber = pm.ProductNumberInternal AND ".
	" bsd.BatchSheetNumber = $batchsheetNumber ". 
	" ORDER BY IngredientSEQ";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	if ($debug > 0)
		echo "$c rows result - $sql";

	if ( $c > 0) {
		$i = 0;
		while ( $row = mysql_fetch_array($result)) {
			$row_arr[$i] = $row;
			$i++;
		}
		return $row_arr;
	}
	else {
		return NULL;
	}
		
}
