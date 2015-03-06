<?php

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

$pne = "";

$edit = false;

if ( isset($_SESSION['external_number']) ) {
	$pne = $_SESSION['external_number'];
//	$edit = true;
}

if (isset($_REQUEST['pne']) ) {
	$pne = $_REQUEST['pne'];
}

$action="";
if ( isset($_REQUEST['action']) ) {
	$action = $_REQUEST['action'];
}

if ( $action == 'edit' ) {
	$edit = true;
}

if ( isset($_REQUEST['bsn']) ) {
	$bsn = $_REQUEST['bsn'];
	unset($_SESSION['bsn']);
}

if ( isset($_REQUEST['LotID']) ) {
	$LotID = $_REQUEST['LotID'];
}

// FOR TRACKING POs FROM ORDERS PAGE
if ( isset($_REQUEST['con']) ) {
	$con = $_REQUEST['con'];
}
if ( isset($_REQUEST['pni']) ) {
	$pni = $_REQUEST['pni'];
}
if ( isset($_REQUEST['seq']) ) {
	$seq = $_REQUEST['seq'];
}



include('inc_global.php');



if ( !empty($_POST['Commit']) ) {

	$sql = "SELECT NetWeight, TotalQuantityUnitType, NumberOfTimesToMake FROM batchsheetmaster WHERE BatchSheetNumber = " . $bsn;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	if (0>=$row[NumberOfTimesToMake]) {
		$_SESSION[error_message] .= "Number of times to make must be greater than 0.<br/>";
	}
	if (0>=$row['NetWeight']) {
		$_SESSION[error_message] .= "Net weight must be greater than 0.<br/>";
	}
	if (!isset($row['TotalQuantityUnitType']) || ""==$row['TotalQuantityUnitType']) {
		$_SESSION[error_message] .= "Please set the total quantity of the units.<br/>";
	}

	if (""!=$_SESSION[error_message]) {
		header("location: customers_batch_sheets.php?action=edit&bsn=$bsn");
		exit();
	}
	$sql = "SELECT * FROM batchsheetcustomerinfo as bsci
				LEFT JOIN batchsheetmaster AS bsm ON (bsm.BatchSheetnumber = $bsn) 
				LEFT JOIN productmaster AS pm ON (pm.ProductNumberInternal = bsm.ProductNumberInternal) 
			WHERE bsci.BatchSheetNumber = $bsn AND 
				( pm.Intermediary = 0 OR pm.FinalProductNotCreatedByAbelei = 1)";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	while ( $row = mysql_fetch_array($result) ) {

		$sql = "INSERT INTO inventorymovements (ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate) 
					VALUES ($row[PackIn], $row[NumberOfPackages], 8, 'P', '" . date("Y-m-d H:i:s") . "')";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		// echo "<h3>PackIn - $sql</h3>";
		$insert_id = mysql_insert_id();

		$sql = "UPDATE batchsheetcustomerinfo SET 
		InventoryTransactionNumber = $insert_id 
		WHERE BatchSheetNumber = $bsn AND CustomerOrderNumber = $row[CustomerOrderNumber] AND CustomerOrderSeqNumber = $row[CustomerOrderSeqNumber]";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	}


	$sql = "SELECT LotID, ProductNumberInternal, NetWeight, Percentage, Yield, TotalQuantityUnitType, Column1UnitType, NumberOfTimesToMake, IngredientProductNumber, IngredientSEQ FROM batchsheetmaster LEFT JOIN batchsheetdetail USING(BatchSheetNumber) WHERE BatchSheetNumber = " . $bsn;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$total_percentage = 0;

	while ( $row = mysql_fetch_array($result) ) {

		if ( $pni == '' ) {
			$LotID = $row['LotID'];
			$pni = $row[ProductNumberInternal];
			$NumberOfTimesToMake = $row[NumberOfTimesToMake];
			$NetWeight = $row[NetWeight];
			$Percentage = $row[Percentage];
			$Yield = $row[Yield];
			$TotalQuantityUnitType = $row[TotalQuantityUnitType];
			$Column1UnitType = $row[Column1UnitType];
		}

		if ( substr($row[IngredientProductNumber], 0, 1) != 4 and '108290' != $row[IngredientProductNumber]) {   // OMIT INSTRUCTIONS AND WATER

			// $quantity = CalculateBatchSheetQuantity($NetWeight, $row['Percentage'], $Yield, $TotalQuantityUnitType, $Column1UnitType) * $NumberOfTimesToMake;
			$quantity = CalculateBatchSheetQuantity($NetWeight, $row[Percentage], $Yield, $TotalQuantityUnitType, 'grams') * $NumberOfTimesToMake;

			// echo "<h3>$quantity = CalculateBatchSheetQuantity($NetWeight, $Percentage, $Yield, $TotalQuantityUnitType, 'grams') * $NumberOfTimesToMake</h3>";
			$sql = "INSERT INTO inventorymovements 
					(ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate) 
					VALUES ($row[IngredientProductNumber], $quantity, 8, 'P', '" . date("Y-m-d H:i:s") . "')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			// echo "<h3>Ingredients - $sql</h3>";
			$insert_id = mysql_insert_id();

			$sql = "UPDATE batchsheetdetail 
						SET InventoryTransactionNumber = $insert_id 
						WHERE BatchSheetNumber = $bsn AND 
							IngredientProductNumber = $row[IngredientProductNumber] AND 
							IngredientSEQ = $row[IngredientSEQ]";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

			$total_percentage = $total_percentage + $row['Percentage'];

		}

	}


	// $flavor_quantity = CalculateBatchSheetQuantity($NetWeight, $total_percentage, $Yield, $TotalQuantityUnitType, $Column1UnitType) * $NumberOfTimesToMake;
	$flavor_quantity = QuantityConvert($NetWeight, $TotalQuantityUnitType, 'grams') * $NumberOfTimesToMake;
	$sql = "INSERT INTO inventorymovements 
				(ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate) 
				VALUES ($pni, $flavor_quantity, 9, 'P', '" . date("Y-m-d H:i:s") . "')";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// echo "<h3>$flavor_quantity = CalculateBatchSheetQuantity($NetWeight, $total_percentage, $Yield, $TotalQuantityUnitType, 'grams') * $NumberOfTimesToMake;...[".CalculateBatchSheetQuantity($NetWeight, $total_percentage, $Yield, $TotalQuantityUnitType, 'grams')."] $sql</h3>";
	$insert_id = mysql_insert_id();

	$sql = "UPDATE batchsheetmaster 
			SET InventoryMovementTransactionNumber = $insert_id, CommitedToInventory = 1
			WHERE BatchSheetNumber = $bsn";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$_SESSION['note'] = "Batch Sheet ingredients successfully committed to inventory<BR>";
	header("location: customers_batch_sheets.php?action=edit&bsn=" . $bsn);
	exit();

}






if ( !empty($_POST['Remove']) ) {

	$sql = "SELECT InventoryTransactionNumber FROM inventorymovements
	INNER JOIN batchsheetcustomerinfo ON inventorymovements.TransactionNumber = batchsheetcustomerinfo.InventoryTransactionNumber
	WHERE BatchSheetNumber = " . $bsn . " AND MovementStatus = 'P' AND InventoryTransactionNumber IS NOT NULL";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// $_SESSION['note'] .= "<p>$sql</p>";
	while ( $row = mysql_fetch_array($result) ) {

		$sql = "UPDATE batchsheetcustomerinfo SET " .
		" InventoryTransactionNumber = NULL" . 
		" WHERE BatchSheetNumber = " . $bsn . " AND InventoryTransactionNumber = " . $row['InventoryTransactionNumber'];
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		// $_SESSION['note'] .= "<p>$sql</p>";
		
		$sql = "DELETE FROM inventorymovements WHERE TransactionNumber = " . $row['InventoryTransactionNumber'];
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		// $_SESSION['note'] .= "<p>$sql</p>";

	}

	$sql = "SELECT InventoryTransactionNumber FROM inventorymovements INNER JOIN batchsheetdetail ON inventorymovements.TransactionNumber = batchsheetdetail.InventoryTransactionNumber WHERE BatchSheetNumber = " . $bsn . " AND MovementStatus = 'P' AND InventoryTransactionNumber IS NOT NULL";
	// $_SESSION['note'] .= "<p>$sql</p>";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	while ( $row = mysql_fetch_array($result) ) {

		$sql = "UPDATE batchsheetdetail SET " .
		" InventoryTransactionNumber = NULL" . 
		" WHERE BatchSheetNumber = " . $bsn . " AND InventoryTransactionNumber = " . $row['InventoryTransactionNumber'];
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		// $_SESSION['note'] .= "<p>$sql</p>";
		
		$sql = "DELETE FROM inventorymovements WHERE TransactionNumber = " . $row['InventoryTransactionNumber'];
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		// $_SESSION['note'] .= "<p>$sql</p>";
	}

	// $sql = "SELECT InventoryMovementTransactionNumber FROM inventorymovements INNER JOIN batchsheetmaster ON inventorymovements.TransactionNumber = batchsheetmaster.InventoryMovementTransactionNumber WHERE BatchSheetNumber = " . $bsn . " AND MovementStatus = 'P' AND InventoryMovementTransactionNumber IS NOT NULL";
	// $result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// // $_SESSION['note'] .= "<p>$sql</p>";
	// while ( $row = mysql_fetch_array($result) ) {

		$sql = "UPDATE batchsheetmaster SET " .
		" InventoryMovementTransactionNumber = NULL," . 
		" CommitedToInventory = 0" . 
		" WHERE BatchSheetNumber = " . $bsn;
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		// $_SESSION['note'] .= "<p>$sql</p>";
		
		// $_SESSION['note'] .= "<p>$sql</p>";

	// }

	$_SESSION['note'] .= "Batch Sheet ingredients successfully removed from committed inventory<BR>";
	header("location: customers_batch_sheets.php?action=edit&bsn=" . $bsn);
	exit();

}



if ( !empty($_POST) and $action != 'search' and empty($_POST['save_master']) and empty($_POST['save_po']) ) {

	//	$_POST['Print']   $_POST['qcinputform']   $_POST['qcreport']

	if ( empty($_POST['BatchSheetNumber']) and empty($_POST['external_number']) ) {
		$_SESSION['note'] = "Please choose a product before clicking an action<BR>";
		header("location: customers_batch_sheets.php?action=search&Designation=". $_POST['Designation'] . "&ProductNumberExternal=". $_POST['ProductNumberExternal'] . "&ProductNumberInternal=". $_POST['ProductNumberInternal'] . "&Keywords=". $_POST['Keywords']);
		exit();
	}

	if ( !empty($_POST['clone']) ) {

		$sql = "SELECT ProductNumberExternal, ProductNumberInternal, ProductDesignation, CustomerID, NetWeight, TotalQuantity, TotalQuantityUnitType, Column1UnitType, Column2UnitType, Yield, NumberOfTimesToMake, Vessel, ScaleNumber, Filtered, Allergen, Kosher, Notes FROM batchsheetmaster WHERE BatchSheetNumber = " . $_POST['BatchSheetNumber'];
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		//echo $sql . "<BR>";

		$sql = "INSERT INTO batchsheetmaster (ProductNumberExternal, ProductNumberInternal, ProductDesignation, CustomerID, NetWeight, TotalQuantity, TotalQuantityUnitType, Column1UnitType, Column2UnitType, Yield, NumberOfTimesToMake, Vessel, ScaleNumber, Filtered, Allergen, Kosher, Notes) VALUES (" . 
		"'" . $row['ProductNumberExternal'] . "', " .
		"'" . $row['ProductNumberInternal'] . "', " .
		"'" . $row['ProductDesignation'] . "', " .
		"'" . $row['CustomerID'] . "', " .
		"'" . $row['NetWeight'] . "', " .
		"0, " .
		"'" . $row['TotalQuantityUnitType'] . "', " .
		"'" . $row['Column1UnitType'] . "', " .
		"'" . $row['Column2UnitType'] . "', " .
		"'" . $row['Yield'] . "', " .
		"'" . $row['NumberOfTimesToMake'] . "', " .
		"'" . $row['Vessel'] . "', " .
		"'" . $row['ScaleNumber'] . "', " .
		"'" . $row['Filtered'] . "', " .
		"'" . $row['Allergen'] . "', " .
		"'" . $row['Kosher'] . "', " .
		"'" . $row['Notes'] . "'" .
		")";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$new_bsn = mysql_insert_id();

		$sql = "INSERT INTO lots (ID) VALUES (0)";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$lot_id = mysql_insert_id();

		$sql = "UPDATE batchsheetmaster SET LotID = " . $lot_id . " WHERE BatchSheetNumber = " . $new_bsn;
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

		$sql = "SELECT * FROM batchsheetdetail WHERE BatchSheetNumber = " . $_POST[BatchSheetNumber];
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		while ( $row = mysql_fetch_array($result) ) {
			$sql = "INSERT INTO batchsheetdetail 
					(BatchSheetNumber, IngredientProductNumber, IngredientSEQ, 
						IngredientNumberExternal, IngredientDesignation, Intermediary, Percentage, 
						RawMaterialLotNumbers, SubBatchSheetNumber, FEMA_NBR, VendorID) 
					VALUES ($new_bsn, '$row[IngredientProductNumber]', '$row[IngredientSEQ]', 
						'$row[IngredientNumberExternal]', '$row[IngredientDesignation]', 
						'$row[Intermediary]', '$row[Percentage]', NULL, 0, '$row[FEMA_NBR]', ".
						(( $row[VendorID] != "" ) ? $row[VendorID] : "NULL" ).")";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}

		header("location: customers_batch_sheets.php?action=search&Designation=". $_POST['Designation'] . "&ProductNumberExternal=". $_POST['ProductNumberExternal'] . "&ProductNumberInternal=". $_POST['ProductNumberInternal'] . "&Keywords=". $_POST['Keywords']);
		exit();



	} elseif ( !empty($_POST['X-Print']) ) {

		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "location.href='customers_batch_sheets.php?action=search&Designation=". $_POST['Designation'] . "&ProductNumberExternal=". $_POST['ProductNumberExternal'] . "&ProductNumberInternal=". $_POST['ProductNumberInternal'] . "&Keywords=" . $_POST['Keywords'] . "'\n";
		//echo "window.opener.document.add_prod.Designation.value='" . $_REQUEST['des'] . "'\n";
		echo "popup('reports/production_batch_sheet.php?bsn=" . $_POST['BatchSheetNumber'] . "',700,830)\n";
		echo "</SCRIPT>\n";

	}
	elseif ( !empty($_POST['new_sheet']) or !empty($_POST['new_sheet_top']) ) {

		if ( !empty($_POST['new_sheet_top']) ) {
			$sql = "SELECT externalproductnumberreference.ProductNumberInternal, externalproductnumberreference.ProductNumberExternal, Natural_OR_Artificial, Designation, ProductType, productmaster.Kosher, SpecificGravity FROM externalproductnumberreference INNER JOIN productmaster ON externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal WHERE externalproductnumberreference.ProductNumberExternal = '$_POST[external_number]'";
		} else {
			$sql = "SELECT externalproductnumberreference.ProductNumberInternal, externalproductnumberreference.ProductNumberExternal, Natural_OR_Artificial, Designation, ProductType, productmaster.Kosher, SpecificGravity FROM batchsheetmaster INNER JOIN externalproductnumberreference USING(ProductNumberExternal) INNER JOIN productmaster ON externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal WHERE BatchSheetNumber = $_POST[BatchSheetNumber]";
		}
		$i=1;
		// $_SESSION[note] .= "<h3>$i - $sql</h3>"; $i++;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		$pne = $row['ProductNumberExternal'];
		$pni = $row['ProductNumberInternal'];
		$SpecificGravity = $row['SpecificGravity'];
		if ( !is_numeric($SpecificGravity) ) {
			$SpecificGravity = 0;
		}
		$ProductDesignation = (("" != $row['Natural_OR_Artificial']) ? $row['Natural_OR_Artificial']." " : "").
			$row['Designation'].(("" != $row['ProductType']) ? " - ".$row['ProductType'] : "").
			(("" != $row['Kosher']) ? " - ".$row['Kosher'] : "");

		$sql = "INSERT INTO batchsheetmaster (ProductNumberExternal, ProductNumberInternal, ProductDesignation, NetWeight, Column1UnitType, Column2UnitType, Yield, NumberOfTimesToMake) VALUES (" . 
		"'" . $pne . "', " .
		$pni . ", " .
		"'" . $ProductDesignation . "', " .
		"0, " .
		"'lbs', " .
		"'grams', " .
		"0.98, " .
		"0" .
		")";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
    // $_SESSION[note] .= "<h3>$i - $sql</h3>"; $i++;
		$new_bsn = mysql_insert_id();

		$sql = "INSERT INTO lots (ID,VendorID) VALUES (0,2382)";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
     // $_SESSION[note] .= "<h3>$i - $sql</h3>"; $i++;
    $lot_id = mysql_insert_id();

		$sql = "UPDATE batchsheetmaster SET LotID = " . $lot_id . " WHERE BatchSheetNumber = " . $new_bsn;
    // $_SESSION[note] .= "<h3>$i - $sql</h3>"; $i++;
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
/* This was logic to build batchsheet details from a price sheet. Price sheets may be out of date with current formulation.

		$sql = "SELECT COUNT(*) AS count FROM pricesheetmaster WHERE ProductNumberInternal = " . $pni . " AND Original_From_Formulation = 1";
    // $_SESSION[note] .= "<h3>$i - $sql</h3>"; $i++;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

		if ( mysql_num_rows($result) == 0 ) {

			// Add new price sheet master
			$sql = "SELECT ItemID, ItemValue FROM tblsystemdefaultsdetail WHERE ItemID = 6 OR ItemID = 5 OR ItemID = 9 ORDER BY ItemID";
    // $_SESSION[note] .= "<h3>$i - $sql</h3>"; $i++;
			$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			while ( $row = mysql_fetch_array($result) ) {
				if ( $row['ItemID'] == 5 ) {
					$PackagingCost = $row['ItemValue'];
				} elseif ( $row['ItemID'] == 6 ) {
					$ShippingCost = $row['ItemValue'];
				} elseif ( $row['ItemID'] == 9 ) {
					$Terms = $row['ItemValue'];
				}
			}
			$sql = "INSERT INTO pricesheetmaster (ProductNumberInternal, ProductDesignation, DatePriced, ShippingCost, PackagingCost, FOBLocation, Terms, SpecificGravity, Lbs_Per_Gallon, Original_From_Formulation) VALUES (" . $pni . ", '" . $ProductDesignation . "', '" . date("Y-m-d H:i:s") . "', ". $ShippingCost . ", " . $PackagingCost . ", 'N. Aurora, IL', '" . $Terms . "', " . $SpecificGravity . ", " . $SpecificGravity * 8.33 . ", 0)";
			$psn = mysql_insert_id();

			// Add the detail records
			$sql = "SELECT formulationdetail.IngredientSEQ, formulationdetail.IngredientProductNumber, productmaster.Designation, formulationdetail.Percentage, productmaster.Intermediary FROM productmaster INNER JOIN formulationdetail ON productmaster.ProductNumberInternal = formulationdetail.ProductNumberInternal WHERE productmaster.ProductNumberInternal = " . $pni . " ORDER BY productmaster.ProductNumberInternal, formulationdetail.IngredientSEQ";
    // $_SESSION[note] .= "<h3>$i - $sql</h3>"; $i++;
			$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			if ( mysql_num_rows($result) > 0 ) {
				while ( $row = mysql_fetch_array($result) ) {
					$sql = "INSERT INTO pricesheetdetail (PriceSheetNumber, IngredientSEQ, IngredientProductNumber, IngredientDesignation, Percentage, Intermediary) VALUES (" . 
					$psn . ", " .
					$row['IngredientSEQ'] . ", " .
					$row['IngredientProductNumber'] . ", " .
					"'" . $row['Designation'] . "', " .
					$row['Percentage'] . ", " .
					$row['Intermediary'] .
					")";
    $_SESSION[note] .= "<h3>$i - $sql</h3>"; $i++;
					mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				}
			}

			// Create the lowest prices table
			$sql = "SELECT productprices.ProductNumberInternal, MIN( productprices.PricePerPound ) AS MinOfPricePerPound, productprices.PriceEffectiveDate, productprices.Tier, productprices.VendorID, pricesheetdetail.IngredientProductNumber, pricesheetdetail.IngredientSEQ, pricesheetdetail.Intermediary
			FROM pricesheetdetail
			INNER JOIN productprices ON pricesheetdetail.IngredientProductNumber = productprices.ProductNumberInternal
			WHERE productprices.Tier = 'A' AND pricesheetdetail.PriceSheetNumber =  " . $psn . "
			GROUP BY productprices.ProductNumberInternal, productprices.PriceEffectiveDate, productprices.VendorID, productprices.Tier, pricesheetdetail.IngredientProductNumber, pricesheetdetail.IngredientSEQ, pricesheetdetail.Intermediary";
			$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			if ( mysql_num_rows($result) > 0 ) {
				while ( $row = mysql_fetch_array($result) ) {

					// Update the vendor and tier in the detail records
					if ( $row['Intermediary'] == 1 ) {
						$price = $row['MinOfPricePerPound'] * 1.02;
					} else {
						$price = $row['MinOfPricePerPound'];
					}
					$sql = "UPDATE pricesheetdetail SET " .
					" Price = '" . $price . "', " .
					" PriceEffectiveDate = '" . $row['PriceEffectiveDate'] . "', " .
					" VendorID = '" . $row['VendorID'] . "'" .
					" WHERE PriceSheetNumber = " . $psn . " AND IngredientProductNumber = " . $row['IngredientProductNumber'] . " AND IngredientSEQ = " . $row['IngredientProductNumber'] . " AND VendorID = " . $row['VendorID'];
					mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	
					//$sql = "INSERT INTO lowestprices (ProductNumberInternal, MinOfPricePerPound, PriceEffectiveDate, Tier, VendorID) VALUES (" . 
					//$row['ProductNumberInternal'] . ", " .
					//$row['MinOfPricePerPound'] . ", " .
					//"'" . $row['PriceEffectiveDate'] . "', " .
					//"'" . $row['Tier'] . "', " .
					//$row['VendorID'] .
					//")";
					//mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

				}
			}

			// Update the vendor and tier in the detail records
			//$sql = "UPDATE lowestprices INNER JOIN pricesheetdetail ON lowestprices.ProductNumberInternal = pricesheetdetail.IngredientProductNumber SET pricesheetdetail.Price = If(([Intermediary]=True),[MinOfPricePerPound]*1.02,[MinOfPricePerPound]),pricesheetdetail.PriceEffectiveDate=[LowestPrices].PriceEffectiveDate,pricesheetdetail.Tier = [LowestPrices].[Tier], pricesheetdetail.VendorID = [LowestPrices].[VendorID] WHERE pricesheetdetail.PriceSheetNumber =" . $psn;


		} else {
			$sql = "SELECT PriceSheetNumber FROM pricesheetmaster WHERE ProductNumberInternal = $pni AND Original_From_Formulation = 1";
			$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
			if ( mysql_num_rows($result) > 0 ) {
			*/
				$row = mysql_fetch_array($result);
				$psn = $row['PriceSheetNumber'];
/*				$sql = "SELECT IngredientProductNumber, IngredientSEQ, VendorID, IngredientDesignation, Intermediary, Percentage FROM pricesheetdetail WHERE PriceSheetNumber = $psn"; */
				$sql = "SELECT fd.IngredientProductNumber, fd.IngredientSEQ, fd.VendorID, fd.Percentage, pm.Designation, pm.Intermediary
						FROM formulationdetail AS fd LEFT JOIN productmaster AS pm ON pm.ProductNumberInternal = IngredientProductNumber
						WHERE fd.ProductNumberInternal = $pni";
				$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				while ( $row = mysql_fetch_array($result) ) {
					if ( !is_numeric($row['VendorID']) ) {
						$VendorID = "NULL";
					} else {
						$VendorID = $row['VendorID'];
					}
					$row_intermediary = empty($row[Intermediary]) ? 0 : $row[Intermediary];
					$sql = "INSERT INTO batchsheetdetail
								(BatchSheetNumber, IngredientProductNumber, IngredientSEQ, 
								IngredientDesignation, Intermediary, Percentage, VendorID) 
							VALUES 
								($new_bsn,'$row[IngredientProductNumber]','$row[IngredientSEQ]', 
								'$row[IngredientDesignation]', $row_intermediary, '$row[Percentage]', $VendorID)";
					mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				}
			/* }
		 } */

		header("location: customers_batch_sheets.php?action=edit&bsn=$new_bsn");
		exit();

	}

}
			


if ( $edit and isset($_POST['save_master']) ) {

//	foreach (array_keys($_POST) as $key) { 
//		$$key = $_POST[$key]; 
//		print "$key is ${$key}<br />"; 
//	}
//	die();

	$LotID = $_POST['LotID'];
	$ProductNumberExternal = $_POST['ProductNumberExternal'];
	$ProductNumberInternal = $_POST['ProductNumberInternal'];
	$ProductDesignation = $_POST['ProductDesignation'];
	$gross_weight = $_POST['gross_weight'];
	$customer = $_POST['customer'];
	$customer_id = $_POST['customer_id'];
	$NetWeight = $_POST['NetWeight'];
	$TotalQuantityUnitType = $_POST['TotalQuantityUnitType'];
	$Column1UnitType = $_POST['Column1UnitType'];
	$Column2UnitType = $_POST['Column2UnitType'];
	$Yield = $_POST['Yield'];
	$NumberOfTimesToMake = $_POST['NumberOfTimesToMake'];
	$Vessel = $_POST['Vessel'];
	$Vessel = $_POST['Vessel'];
	
	$DueDate = $_POST['DueDate'];
	$date_parts = explode("/", $DueDate);
	$NewDueDate = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
	if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
		if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
			$error_found=true;
			$error_message .= "Invalid due date (" . $DueDate . ") entered<BR>";
		}
	} else {
		$error_found=true;
		$error_message .= "Invalid due date (" . $DueDate . ") entered<BR>";
	}

	$NewDateManufactured = '';
	$DateManufactured = $_POST['DateManufactured'];
	if ( $DateManufactured != '' ) {
		$date_parts = explode("/", $DateManufactured);
		$NewDateManufactured = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
		if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
			if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
				$error_found=true;
				$error_message .= "Invalid manufactured date (" . $DateManufactured . ") entered<BR>";
			}
		} else {
			$error_found=true;
			$error_message .= "Invalid manufactured date (" . $DateManufactured . ") entered<BR>";
		}
	}

	$NewExpirationDate = '';
	$ExpirationDate = $_POST['ExpirationDate'];
	if ( $ExpirationDate != '' ) {
		$date_parts = explode("/", $ExpirationDate);
		$NewExpirationDate = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
		if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
			if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
				$error_found=true;
				$error_message .= "Invalid expiration date (" . $ExpirationDate . ") entered<BR>";
			}
		} else {
			$error_found=true;
			$error_message .= "Invalid expiration date (" . $ExpirationDate . ") entered<BR>";
		}
	}

	//$due_month = $_POST['due_month'];
	//$due_day = $_POST['due_day'];
	//$due_year = $_POST['due_year'];

	//if ( checkdate($due_month, $due_day, $due_year) ) {
	//	$DueDate = $due_year . "-" . $due_month . "-" . $due_day;
	//	$DueDate_clause = " DueDate = '" . $DueDate . "',";
	//} else {
	//	$error_found = true;
	//	$error_message .= "Please enter a Due Date<BR>";
		//$DueDate_clause = "";
	//}

	$ScaleNumber = $_POST['ScaleNumber'];
	if ( 20 < strlen($ScaleNumber) ) {
		$error_found = true;
		$error_message .= "Scale number is too long. Must be 20 or less characters.<BR>";
	}

	//$manu_month = $_POST['manu_month'];
	//$manu_day = $_POST['manu_day'];
	//$manu_year = $_POST['manu_year'];
	//if ( checkdate($manu_month, $manu_day, $manu_year) ) {
	//	$DateManufactured = $manu_year . "-" . $manu_month . "-" . $manu_day;
	//} else {
	//	$DateManufactured = "";
	//}

	//if ( checkdate($manufactured_month, $manufactured_day, $manufactured_year) ) {
	//	$DateManufactured = $manufactured_year . "-" . $manufactured_month . "-" . $manufactured_day;
	//	$DateManufactured_clause = " DateManufactured = '" . $DateManufactured . "',";
	//} else {
	//	$DateManufactured_clause = "";
	//}

	//$expiration_month = $_POST['expiration_month'];
	//$expiration_day = $_POST['expiration_day'];
	//$expiration_year = $_POST['expiration_year'];

	//if ( checkdate($expiration_month, $expiration_day, $expiration_year) ) {
	//	$ExpirationDate = $expiration_year . "-" . $expiration_month . "-" . $expiration_day;
		//$ExpirationDate_clause = " ExpirationDate = '" . $ExpirationDate . "',";
	//} else {
	//	$ExpirationDate = "";
		//$ExpirationDate_clause = "";
	//}

	$MadeBy = $_POST['MadeBy'];
	$Filtered = $_POST['Filtered'];

	$qc_month = $_POST['qc_month'];
	$qc_day = $_POST['qc_day'];
	$qc_year = $_POST['qc_year'];

//	if ( checkdate($qc_month, $qc_day, $qc_year) ) {
//		$QualityControlDate = $qc_year . "-" . $qc_month . "-" . $qc_day;
//		$QualityControlDate_clause = " QualityControlDate = '" . $QualityControlDate . "',";
//	} else {
//		$QualityControlDate_clause = "";
//	}

	$QualityControlEmployeeID = $_POST['QualityControlEmployeeID'];
	$CommitedToInventory = $_POST['CommitedToInventory'];
	$Manufactured = $_POST['Manufactured'];
	//$InventoryMovementRemarks = $_POST['InventoryMovementRemarks'];
	//$abeleiLotNumber = $_POST['abeleiLotNumber'];
	//$LotSequenceNumber = $_POST['LotSequenceNumber'];
	$Notes = $_POST['Notes'];

	if ( $Filtered != 1 ) {
		$Filtered = 0;
	}
	//if ( $CommitedToInventory != 1 ) {
	//	$CommitedToInventory = 0;
	//}
	//if ( $Manufactured != 1 ) {
	//	$Manufactured = 0;
	//}

	if ( $NetWeight == '' ) {
		$NetWeight = 0;
	}
	if ( $TotalQuantity == '' ) {
		$TotalQuantity = 0;
	}
	if ( $NumberOfTimesToMake == '' ) {
		$NumberOfTimesToMake = 0;
	}
	if ( $Yield == '' ) {
		$Yield = 0;
	}
	if ( $LotSequenceNumber == '' ) {
		$LotSequenceNumber = 0;
	}
	if ( $InventoryMovementTransactionNumber == '' ) {
		$InventoryMovementTransactionNumber = 0;
	}

	// check_field() FUNCTION IN global.php
	//check_field($customer_id, 3, 'Customer');
	check_field($NetWeight, 3, 'Net Weight');
	check_field($TotalQuantity, 3, 'Total Quantity');
	check_field($NumberOfTimesToMake, 3, 'Number of Times to Make');
	check_field($Yield, 3, 'Yield');
	check_field($InventoryMovementTransactionNumber, 3, 'Inventory Movement Transaction Number');

	if ( $NumberOfTimesToMake > 1000 ) {
		$error_found = true;
		$error_message .= "Please enter a lower value for 'Number of Times to Make'<BR>";
	}
	else if (0 >= $NumberOfTimesToMake ) {
		$error_found = true;
		$error_message .= "Please enter a positive value for 'Number of Times to Make'<BR>";
	}

	if ( $NetWeight > 10000 ) {
		$error_found = true;
		$error_message .= "Please enter a lower value for 'Net Weight'<BR>";
	}
	else if (0 >= $NetWeight ) {
		$error_found = true;
		$error_message .= "Please enter a positive value for 'Net Weight'<BR>";
	}

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$NetWeight = escape_data($NetWeight);
		$Yield = escape_data($Yield);
		$NumberOfTimesToMake = escape_data($NumberOfTimesToMake);
		$ScaleNumber = escape_data($ScaleNumber);
		$MadeBy = escape_data($MadeBy);
		//$QualityControlEmployeeID = escape_data($QualityControlEmployeeID);
		//$InventoryMovementRemarks = escape_data($InventoryMovementRemarks);
		//$abeleiLotNumber = escape_data($abeleiLotNumber);
		//$LotSequenceNumber = escape_data($LotSequenceNumber);
		$Notes = escape_data($Notes);
		
		if ( $bsn != "" ) {
			$sql = "UPDATE batchsheetmaster SET " .
			" DueDate = '" . $NewDueDate . "'," .
			//$QualityControlDate_clause . 
			" NetWeight = '" . $NetWeight . "'," .
			//" CustomerID = '" . $customer_id . "'," .
			" TotalQuantityUnitType = '" . $TotalQuantityUnitType . "'," .
			" Column1UnitType = '" . $Column1UnitType . "'," .
			" Column2UnitType = '" . $Column2UnitType . "'," .
			" Yield = '" . $Yield . "'," .
			" NumberOfTimesToMake = '" . $NumberOfTimesToMake . "'," .
			" Vessel = '" . $Vessel . "'," .
			" ScaleNumber = '" . $ScaleNumber . "'," .
			" MadeBy = '" . $MadeBy . "'," .
			" Filtered = '" . $Filtered . "'," .
			//" QualityControlEmployeeID = '" . $QualityControlEmployeeID . "'," .
			//" CommitedToInventory = '" . $CommitedToInventory . "'," .
			//" Manufactured = '" . $Manufactured . "'," .
			//" InventoryMovementRemarks = '" . $InventoryMovementRemarks . "'," .
			//" abeleiLotNumber = '" . $abeleiLotNumber . "'," .
			//" LotSequenceNumber = '" . $LotSequenceNumber . "'," .
			" Notes = '" . $Notes . "'" .
			" WHERE BatchSheetNumber = " . $bsn;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

			$sql = "UPDATE lots SET" .
			" DateManufactured=" . (("" != $NewDateManufactured) ? "'$NewDateManufactured'" : "NULL") . ", " .
			" ExpirationDate=" . (("" != $NewExpirationDate) ? "'$NewExpirationDate'" : "NULL") . " " .
			" WHERE ID = " . $LotID;
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		
		}
		//echo $sql;
		//die();
		header("location: customers_batch_sheets.php?action=edit&bsn=" . $bsn);
		exit();

	}

} elseif ( $bsn != '' ) {
	$sql = "SELECT batchsheetmaster.*, name, lots.QualityControlEmployeeID, lots.DateManufactured, lots.ExpirationDate, lots.QualityControlDate, pm.Intermediary, pm.FinalProductNotCreatedByAbelei 
	FROM batchsheetmaster
		LEFT JOIN lots ON batchsheetmaster.LotID = lots.ID
		LEFT JOIN customers ON batchsheetmaster.CustomerID = customers.customer_id 
		LEFT JOIN productmaster AS pm ON batchsheetmaster.`ProductNumberInternal` = pm.`ProductNumberInternal` 
	WHERE BatchSheetNumber = $bsn";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	//echo $sql . "<BR>";
	$LotID = $row['LotID'];
	$ProductNumberExternal = $row['ProductNumberExternal'];
	$ProductNumberInternal = $row['ProductNumberInternal'];
	$ProductDesignation = $row['ProductDesignation'];
	//$customer = $row['name'];
	$customer_id = empty($row[CustomerID]) ? 0 : $row[CustomerID];
	$NetWeight = $row['NetWeight'];
	$TotalQuantityUnitType = $row['TotalQuantityUnitType'];
	$Column1UnitType = $row['Column1UnitType'];
	$Column2UnitType = $row['Column2UnitType'];
	$Yield = $row['Yield'];
	$NumberOfTimesToMake = $row['NumberOfTimesToMake'];
	$Vessel = $row['Vessel'];
	$ScaleNumber = $row['ScaleNumber'];
	$MadeBy = $row['MadeBy'];
	$Filtered = $row['Filtered'];
	$QualityControlEmployeeID = $row['QualityControlEmployeeID'];
	$CommitedToInventory = $row['CommitedToInventory'];
	$Manufactured = $row['Manufactured'];
	//$InventoryMovementRemarks = $row['InventoryMovementRemarks'];
	//$abeleiLotNumber = $row['abeleiLotNumber'];
	//$LotSequenceNumber = $row['LotSequenceNumber'];
	$Notes = $row['Notes'];
	$intermediary = (1 == $row[Intermediary]) ? true : false;
	$FinalProductNotCreatedByAbelei = ( 0 != $row[FinalProductNotCreatedByAbelei] ) ? true : false;

	$gross_weight = ( empty($NetWeight) || empty($Yield) ) ? 0 : $NetWeight/($Yield/100)/100;

	if ( $row['DueDate'] != '' ) {
		$DueDate = date("m/d/Y", strtotime($row['DueDate']));
	} else {
		$DueDate = '';
	}

	if ( $row['DateManufactured'] != '' ) {
		$DateManufactured = date("m/d/Y", strtotime($row['DateManufactured']));
	} else {
		$DateManufactured = '';
	}

	if ( $row['ExpirationDate'] != '' ) {
		$ExpirationDate = date("m/d/Y", strtotime($row['ExpirationDate']));
	} else {
		$ExpirationDate = '';
	}

	if ( $row['QualityControlDate'] != '' ) {
		$QualityControlDate = date("m/d/Y", strtotime($row['QualityControlDate']));
	} else {
		$QualityControlDate = '<I>None entered yet</I>';
	}

}



if ( $action == "back_out" ) {

	$sql = "UPDATE lots, batchsheetmaster AS bsm SET lots.LotNumber = NULL, lots.LotSequenceNumber = NULL WHERE bsm.LotID = lots.ID AND BatchSheetNumber = " . $_GET['bsn'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$sql = "UPDATE inventorymovements SET MovementStatus = 'P' WHERE TransactionNumber=(SELECT InventoryMovementTransactionNumber FROM batchsheetmaster WHERE BatchSheetNumber = " . $_GET['bsn'] . ")";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$sql = "UPDATE batchsheetmaster SET Manufactured = 0 WHERE BatchSheetNumber = " . $_GET['bsn'];   //, abeleiLotNumber=NULL
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$sql = "DELETE im.* FROM inventorymovements AS im, batchsheetdetaillotnumbers AS bsdln WHERE im.TransactionNumber = bsdln.InventoryMovementTransactionNumber AND bsdln.BatchSheetNumber = $_GET[bsn]";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$sql = "DELETE im.* FROM inventorymovements AS im, batchsheetcustomerinfo AS bsci WHERE im.TransactionNumber = bsci.InventoryTransactionNumber AND bsci.BatchSheetNumber = $_GET[bsn]";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$sql = "DELETE FROM batchsheetdetaillotnumbers WHERE BatchSheetNumber = " . $_GET['bsn'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$sql = "DELETE FROM batchsheetdetailpackaginglotnumbers WHERE BatchSheetNumber = " . $_GET['bsn'];
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$sql = "SELECT * FROM batchsheetcustomerinfo WHERE BatchSheetNumber = $bsn AND PackIn IS NOT NULL";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	while ( $row = mysql_fetch_array($result) ) {
		$sql = "INSERT INTO inventorymovements 
				(ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate) 
				VALUES ($row[PackIn], $row[NumberOfPackages], 8, 'P', '" . date("Y-m-d H:i:s") . "')";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$insert_id = mysql_insert_id();

		$sql = "UPDATE batchsheetcustomerinfo 
				SET InventoryTransactionNumber = $insert_id 
				WHERE BatchSheetNumber = $bsn AND 
					CustomerOrderNumber = $row[CustomerOrderNumber] AND 
					CustomerOrderSeqNumber = $row[CustomerOrderSeqNumber]";
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	}


	$sql = "SELECT LotID, ProductNumberInternal, NetWeight, Percentage, Yield, 
					TotalQuantityUnitType, Column1UnitType, NumberOfTimesToMake, 
					IngredientProductNumber, IngredientSEQ 
			FROM batchsheetmaster 
				LEFT JOIN batchsheetdetail USING(BatchSheetNumber) 
			WHERE BatchSheetNumber = $bsn";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");

	$total_percentage = 0;

	while ( $row = mysql_fetch_array($result) ) {

		if ( $pni == '' ) {
			$LotID = $row[LotID];
			$pni = $row[ProductNumberInternal];
			$NumberOfTimesToMake = $row[NumberOfTimesToMake];
			$NetWeight = $row[NetWeight];
			$Percentage = $row[Percentage];
			$Yield = $row[Yield];
			$TotalQuantityUnitType = $row[TotalQuantityUnitType];
			$Column1UnitType = $row[Column1UnitType];
		}

		if ( substr($row[IngredientProductNumber], 0, 1) != 4 ) {   // OMIT INSTRUCTIONS

			// $quantity = CalculateBatchSheetQuantity($NetWeight, $row['Percentage'], $Yield, $TotalQuantityUnitType, $Column1UnitType) * $NumberOfTimesToMake;
			$quantity = CalculateBatchSheetQuantity($NetWeight, $row[Percentage], $Yield, $TotalQuantityUnitType, 'grams') * $NumberOfTimesToMake;

			$sql = "INSERT INTO inventorymovements 
					(ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate) 
					VALUES ( $row[IngredientProductNumber], $quantity, 8, 'P', '" . date("Y-m-d H:i:s") . "')";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute Query: $sql<BR><BR>");
			$insert_id = mysql_insert_id();

			$sql = "UPDATE batchsheetdetail SET InventoryTransactionNumber = $insert_id 
					WHERE BatchSheetNumber = $bsn 
						AND IngredientProductNumber = $row[IngredientProductNumber] AND 
						IngredientSEQ = $row[IngredientSEQ]";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");


		}

	}

	$_SESSION[note] = "Manufacturing successfully backed out of<BR>";
	header("location: customers_batch_sheets.php?action=edit&bsn=$bsn");
	exit();
}








if ( $action == "delete_batch" ) {
	if (!empty($_GET[bsn]) and !empty($_GET[LotID])) {
		$sql = "DELETE FROM batchsheetmaster WHERE BatchSheetNumber = " . $_GET[bsn];
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$sql = "DELETE FROM lots WHERE ID = " . $_GET[LotID];
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$_SESSION[note] = "Batch sheet successfully deleted<BR>";
	}
	else
	{
		$_SESSION[note] = "Missing information to delete batch sheet. Contact Admin.<BR>";
	}
	header("location: customers_batch_sheets.php");
	exit();
}



if ( $action == "delete_po" ) {
	$sql = "DELETE FROM batchsheetcustomerinfo 
			WHERE BatchSheetNumber = $_GET[bsn] AND 
				CustomerOrderNumber = $_GET[con] AND 
				CustomerOrderSeqNumber = $_GET[seq]";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	header("location: customers_batch_sheets.php?action=edit&bsn=" . $bsn);
	exit();
}



if ( isset($_REQUEST['batch_sheet_num']) and $action == 'search' ) {
	$batch_sheet_num = $_REQUEST['batch_sheet_num'];
}
if ( isset($_REQUEST[Designation]) and $action == 'search' ) {
	$Designation = $_REQUEST[Designation];
}
if ( isset($_REQUEST[ProductNumberExternal]) and $action == 'search' ) {
	$ProductNumberExternal = $_REQUEST[ProductNumberExternal];
}
if ( isset($_REQUEST[ProductNumberInternal]) and $action == 'search' ) {
	$ProductNumberInternal = $_REQUEST[ProductNumberInternal];
}
if ( isset($_REQUEST[Keywords]) and $action == 'search' ) {
	$Keywords = $_REQUEST[Keywords];
}
if ( isset($_REQUEST[status]) and $action == 'search' ) {
	$status = $_REQUEST[status];
}



function CalculateBatchSheetQuantity($NetWeight, $Percentage, $Yield, $Total_Unit_Type, $Target_Unit_Type) {
	$percent_quantity = ($NetWeight/$Yield) * ($Percentage/100);
	return QuantityConvert($percent_quantity, $Total_Unit_Type, $Target_Unit_Type);
}


$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

$statuses = array("", "New", "Committed", "Lots assigned", "Complete");

include("inc_header.php");


if ( !empty($_POST['qc_input']) ) {
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "location.href='customers_batch_sheets.php?action=search&Designation=". $_POST['Designation'] . "&ProductNumberExternal=". $_POST['ProductNumberExternal'] . "&ProductNumberInternal=". $_POST['ProductNumberInternal'] . "&Keywords=". $_POST['Keywords'] . "'\n";
	echo "popup('pop_qc_input_form.php?bsn=" . $_POST['BatchSheetNumber'] . "',700,830)\n";
	echo "</SCRIPT>\n";
}

if ( !empty($_POST['qcreport']) ) {
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "location.href='customers_batch_sheets.php?action=search&Designation=". $_POST['Designation'] . "&ProductNumberExternal=". $_POST['ProductNumberExternal'] . "&ProductNumberInternal=". $_POST['ProductNumberInternal'] . "&Keywords=". $_POST['Keywords'] . "'\n";
	echo "popup('reports/qc_form.php?bsn=" . $_POST['BatchSheetNumber'] . "',700,830)\n";
	echo "</SCRIPT>\n";
}

?>









<script type="text/javascript">
<!--
$(document).ready(function(){
	$(":input[readonly]").addClass("readOnly");
	$(":checkbox[readonly]").attr("disabled","disabled");
	$("select[readonly]").attr("disabled","disabled");
	
	$(":submit").click(function() {
		$("#action").val(this.name);
		switch (this.name) {
			case "new_sheet_top":
				if ("" == $("#external_number").val()) {
					$("#external_number").attr("style", "border: solid 1px red")
					alert("Please choose a product by external number");
					return false;
				}
				break;
			case "commit":
				break;
			default:
				break;
		}
	});

	$("#designation_search").autocomplete("search/product_master_formulas_by_designation.php", {
		cacheLength: 1,
		width: 365,
		max: 50,
		scroll: true,
		scrollHeight: 350,
		multipleSeparator: "¬",
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		selectFirst: false
	});
	$("#designation_search").result(function(event, data, formatted) {
		if (data)
			document.search.submit();
	});
	$("#designation_search").keypress(function(e) {
		if (e.which >= 32) {
			$("#external_number_search").val('');
			$("#internal_number_search").val('');
			$("#keyword_search").val('');
			$("#action").val('search');
		}
	});
	$("#external_number_search").autocomplete("search/product_master_formulas_by_external_number.php", {
		cacheLength: 1,
		selectFirst: false,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max:50,
		multipleSeparator: "¬",
		scrollheight: 350
	});
		$("#external_number_search").result(function(event, data, formatted) {
		if (data)
			document.search.submit();
	});
	$("#external_number_search").keypress(function(e) {
		if (e.which >= 32) {
			$("#designation_search").val('');
			$("#internal_number_search").val('');
			$("#keyword_search").val('');
			$("#action").val('search');
		}
	});
	
	$("#internal_number_search").autocomplete("search/product_master_formulas_by_internal_number.php", {
		cacheLength: 1,
		selectFirst: false,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max:50,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#internal_number_search").result(function(event, data, formatted) {
		if (data)
			document.search.submit();
	});
	$("#internal_number_search").keypress(function(e) {
		if (e.which >= 32) {
			$("#designation_search").val('');
			$("#external_number_search").val('');
			$("#keyword_search").val('');
			$("#action").val('search');
		}
	});
	
	$("#keyword_search").autocomplete("search/product_master_formulas_by_keyword.php", {
		cacheLength: 1,
		selectFirst: false,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max:50,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#keyword_search").result(function(event, data, formatted) {
		if (data)
			document.search.submit();
	});
	$("#keyword_search").keypress(function(e) {
		if (e.which >= 32) {
			$("#designation_search").val('');
			$("#external_number_search").val('');
			$("#internal_number_search").val('');
			$("#action").val('search');
		}
	});

	$("#customer").autocomplete("search/customers_by_name.php", {
		cacheLength: 1,
		selectFirst: false,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		width: 350,
		max:50,
		multipleSeparator: "¬",
		scrollheight: 350
	});
	$("#customer").result(function(event, data, formatted) {
		if (data)
			$("#customer_id").val(data[1]);
	});

	
	$("#external_number").autocomplete("search/product_master_formulas_by_external_number.php", {
		cacheLength: 1,
		selectFirst: false,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max:50,
		multipleSeparator: "¬",
		scrollheight: 350
	});

});



function back_out(bsn) {
	if ( confirm('Do you want to back out this manufactured product? All inventory movement, inventory and lot number records will be deleted!') ) {
		document.location.href = "customers_batch_sheets.php?action=back_out&bsn=" + bsn;
	}
}

function delete_batch(bsn,LotID) {
	if ( confirm('Are you sure you want to permanently delete this batch sheet?') ) {
		document.location.href = "customers_batch_sheets.php?action=delete_batch&bsn=" + bsn + "&LotID=" + LotID;
	}
}

function delete_po(bsn, con, seq) {
	if ( confirm('Are you sure you want to delete this Customer Order?') ) {
		document.location.href = "customers_batch_sheets.php?action=delete_po&bsn=" + bsn + "&con=" + con + "&seq=" + seq;
	}
}



function delete_ingredient(pni, seq, pne) {
	if ( confirm('Are you sure you want to delete this ingredient?') ) {
		document.location.href = "customers_batch_sheets.php?action=delete_ingredient&pni=" + pni + "&seq=" + seq + "&pne=" + pne;
	}
}

function validate() {
	switch (document.getElementById("action").value)
	{
		case 'delete':
			var answer = confirm("Delete this order?")
			if (answer) { return true; } else { return false; }
			break;
		default:
			break;
	}
}
function popup(url, width, height, left, top) {
	if (width === undefined) {
		width  = 700;
	}
	if (height === undefined) {
		height  = 540;
	}
	if (left === undefined) {
		left  = (screen.width  - width)/2;
	}
	if (top === undefined) {
		top  = (screen.height - height)/2;
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
	newwin=window.open(url,'pop', params);
	if (window.focus) {newwin.focus()}
	return false;
}
 // End -->
 
</script>



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
$(function() {
	$('#datepicker3').datepicker({
		changeMonth: true,
		changeYear: true
	});
});
</script>



<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>

<?php 

//$quantity = CalculateBatchSheetQuantity(222, 88.2, .98, 'grams', 'lbs');
//echo $quantity;

?>


<?php if ( ($action == 'search' or $action != 'edit') and $bsn == '' ) { ?>

<table class="bounding">
<tr valign="top">
<td class="padded">
	<FORM id="search" name="search" ACTION="customers_batch_sheets.php" METHOD="get">
	<INPUT TYPE="hidden" NAME="action" VALUE="search">
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

		<TR>
			<TD><B>Batch sheet#:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="batch_sheet_num" NAME="batch_sheet_num" VALUE="<?php echo $batch_sheet_num;?>" SIZE="20"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Material designation:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="designation_search" NAME="Designation" VALUE="<?php echo $Designation;?>" SIZE="20"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Abelei number (external):</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="external_number_search" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>" SIZE="20"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Material number (internal):</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="internal_number_search" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>" SIZE="20"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Keywords:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="keyword_search" NAME="Keywords" VALUE="<?php echo $Keywords;?>" SIZE="20"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Status:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><SELECT NAME="status" STYLE="font-size: 7pt">
				<?php
					foreach ( $statuses as $value ) {
						if ( $value == $status ) {
							echo "<OPTION VALUE='" . $value . "' SELECTED>" . $value . "</OPTION>";
						} else {
							echo "<OPTION VALUE='" . $value . "'>" . $value . "</OPTION>";
						}
					}
				?>
				</SELECT></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD>
		</TR>

		<TR>
			<TD COLSPAN="3">
				<INPUT style="float:right" name="search" id="search" TYPE="submit" class="submit_medium" VALUE="Search" />
			</TD>
		</TR>
	</TABLE>
</FORM>
<hr/ style="margin:1em 0 1em 0">
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<FORM ID="add_new" NAME="add_new" ACTION="customers_batch_sheets.php" METHOD="post">
		<TR>
			<TD><B>External Number:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" ID="external_number" NAME="external_number" SIZE="20"></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1"></TD>
			<TD><INPUT TYPE="submit" CLASS="submit new" NAME="new_sheet_top" id="new_sheet_top" VALUE="New Batch Sheet"></TD>
		</TR></FORM>
	</TABLE>
</TD></TR></TABLE>
<BR><BR>

<?php

}









if ( $action == 'search' and $bsn == '' ) {

	$clause = "";

	if ( $batch_sheet_num != '' ) {
		$clause = " AND ( ( bsm.BatchSheetNumber ) LIKE '%$batch_sheet_num%' )";
	}

	if ( $Designation != '' ) {
		$clause .= " AND ( ( pm.Designation ) LIKE '%$Designation%' )";
	} elseif ( $ProductNumberExternal != '' ) {
		$clause .= " AND ( ( bsm.ProductNumberExternal ) LIKE '%$ProductNumberExternal%' )";
	} elseif ( $ProductNumberInternal != '' ) {
		$clause .= " AND ( ( bsm.ProductNumberInternal ) LIKE '%$ProductNumberInternal%' )";
	} elseif ( $Keywords != '' ) {
		$clause .= " AND ( ( Keywords ) LIKE '%$Keywords%' )";
	}

	if ( $status != '' ) {
		// $statuses = array("", "New", "Committed", "Lots assigned", "Complete");
		if ( $status == "New" ) {
			$clause .= " AND ( CommitedToInventory <> 1 AND Manufactured <> 1 )";
		} elseif ( $status == "Committed" ) {
			$clause .= " AND ( CommitedToInventory = 1 AND Manufactured <> 1 )";
		} elseif ( $status == "Lots assigned" ) {
			$clause .= " AND ( Manufactured = 1 )";
		} else {
			$clause .= " AND ( QualityControlDate IS NOT NULL )";
		}
	}

	$sql = "SELECT DISTINCT BatchSheetNumber, bsm.LotID as LotID, DateManufactured, NetWeight, NumberOfTimesToMake, LotSequenceNumber, CommitedToInventory,
	Manufactured, pm.ProductNumberInternal, pm.SpecificGravity, pm.SpecificGravityUnits, pm.Organic, pm.Natural_OR_Artificial,
	pm.Designation, pm.ProductType, pm.Kosher, bsm.ProductNumberExternal, lots.DateManufactured as DateManufactured,
	lots.LotNumber as abeleiLotNumber, lots.LotSequenceNumber as LotSequenceNumber, lots.QualityControlDate
	FROM batchsheetmaster AS bsm
	LEFT JOIN lots ON bsm.LotID = lots.ID
	LEFT JOIN productmaster AS pm ON bsm.ProductNumberInternal = pm.ProductNumberInternal
	LEFT JOIN customerorderdetaillotnumbers AS codln ON codln.LotID = bsm.LotID
	LEFT JOIN batchsheetcustomerinfo AS bsci USING(BatchSheetNumber)
	LEFT JOIN customers AS cust ON bsm.CustomerID = cust.customer_id
	WHERE ( 1 $clause )
	ORDER BY if( Mid( bsm.ProductNumberExternal, 1, 2 ) = 'US', bsm.ProductNumberExternal, BuildExternalSortKeyField1( bsm.ProductNumberExternal) ), if( Mid( bsm.ProductNumberExternal, 4, 1 ) = 'a', 0, bsm.ProductNumberExternal ), BuildExternalSortKeyField3( bsm.ProductNumberExternal), BuildExternalSortKeyField4( bsm.ProductNumberExternal)";
	
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	//echo $sql . "<BR>";

	if ( $c > 0 ) {
		$bg = 0; ?>

		<FORM ACTION="customers_batch_sheets.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="Designation" VALUE="<?php echo $Designation;?>">
		<INPUT TYPE="hidden" NAME="LotID" VALUE="<?php echo $LotID;?>">
		<INPUT TYPE="hidden" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>">
		<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>">
		<INPUT TYPE="hidden" NAME="Keywords" VALUE="<?php echo $Keywords;?>">

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">

			<TR VALIGN=BOTTOM>
				<TD></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="2" HEIGHT="1"></TD>
				<TD COLSPAN=4></TD>
				<TD><B>Batch#</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Abelei#</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Customer</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Customer PO/Code</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B>Manufactured</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B>Net Weight</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B>Times<BR><NOBR>to make</NOBR></B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT width="100px"><B>abelei Lot#</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD ALIGN=RIGHT><B>Seq#</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Status</B></TD>
			</TR>

			<TR>
				<TD COLSPAN=17><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
			</TR>

			<?php 

			while ( $row = mysql_fetch_array($result) ) {
				//$description = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
				if ( $bg == 1 ) {
					$bgcolor = "#F3E7FD";
					$bg = 0;
				} else {
					$bgcolor = "whitesmoke";
					$bg = 1;
				} ?>

				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD><INPUT TYPE="radio" NAME="BatchSheetNumber" VALUE="<?php echo $row['BatchSheetNumber'] ?>"></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="2" HEIGHT="1"></TD>
					<TD><A HREF="customers_batch_sheets.php?bsn=<?php echo $row['BatchSheetNumber'];?>"><IMG SRC="images/pencil.gif" WIDTH="16" HEIGHT="16" BORDER=0></A></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="1"></TD>
					<TD>
					<?php if ( $row['CommitedToInventory'] != 1 and $row['Manufactured'] != 1 ) { ?>
						<A HREF="JavaScript:delete_batch(<?php echo($row['BatchSheetNumber'].','.$row['LotID']);?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A>
					<?php } ?>
					</TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><?php echo $row['BatchSheetNumber'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="1"></TD>
					<TD><NOBR><?php echo $row['ProductNumberExternal'] ?></NOBR></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>

					<?php
					$sql = "SELECT DISTINCT name
					FROM batchsheetcustomerinfo AS bsci
					LEFT JOIN customerorderdetail AS c ON c.CustomerOrderNumber = bsci.CustomerOrderNumber AND c.CustomerOrderSeqNumber = bsci.CustomerOrderSeqNumber AND bsci.BatchSheetNumber = " . $row['BatchSheetNumber'] . "
					LEFT JOIN customerordermaster ON c.CustomerOrderNumber = customerordermaster.OrderNumber
					LEFT JOIN customers ON customers.customer_id = customerordermaster.CustomerID
					WHERE bsci.BatchSheetNumber = " . $row['BatchSheetNumber'];
					$result_cust = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					$cc = mysql_num_rows($result_cust);
					if ( $cc > 0 ) {
						$row_cust = mysql_fetch_array($result_cust);
						$name = $row_cust['name'];
					 } else {
						$name = "";
					}
					?>

					<TD><NOBR><?php echo $name;?></NOBR></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php
					$sql = "SELECT DISTINCT bsci.CustomerPONumber
					FROM batchsheetcustomerinfo AS bsci
					LEFT JOIN customerorderdetail AS c ON c.CustomerOrderNumber = bsci.CustomerOrderNumber AND c.CustomerOrderSeqNumber = bsci.CustomerOrderSeqNumber AND bsci.BatchSheetNumber = " . $row['BatchSheetNumber'] . "
					LEFT JOIN customerordermaster ON c.CustomerOrderNumber = customerordermaster.OrderNumber
					LEFT JOIN customers ON customers.customer_id = customerordermaster.CustomerID
					WHERE bsci.BatchSheetNumber = " . $row['BatchSheetNumber'];
					$result_cust = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					$cc = mysql_num_rows($result_cust);
					$x = 1;
					if ( $cc > 0 ) {
						$po_numbers = "";
						while ( $row_cust = mysql_fetch_array($result_cust) ) {
							echo $row_cust['CustomerPONumber'];
							if ( $x < $cc ) {
								echo "<BR>";
							}
							$x++;
						}
					 }
					?></TD>

					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><?php
					if ( $row['DateManufactured'] != '' ) {
						echo date("n/j/Y", strtotime($row['DateManufactured']));
					}
					?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><?php echo number_format($row['NetWeight'], 2) ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><?php echo $row['NumberOfTimesToMake'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><NOBR><?php echo $row['abeleiLotNumber'] ?></NOBR></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD ALIGN=RIGHT><?php echo $row['LotSequenceNumber'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<?php
					if ( $row[CommitedToInventory] != 1 and $row[Manufactured] != 1 ) {
						$color = 1==$bg ? "LightSalmon" : "DarkOrange";
						echo "<TD style=\"text-align:center; background-color:$color\">New</TD>";
					} elseif ( $row[CommitedToInventory] == 1 and $row[Manufactured] != 1 ) {
						$color = 1==$bg ? "GreenYellow" : "LawnGreen";
						echo "<TD style=\"text-align:center; background-color:$color\">Committed</TD>";
					} elseif ( $row[Manufactured] == 1 and $row[QualityControlDate] == '') {
						$color = 1==$bg ? "AliceBlue" : "PaleTurquoise";
						echo "<TD style=\"text-align:center; background-color:$color\">Lots assigned</TD>";
					} elseif ( $row[QualityControlDate] != '' ) {
						$color = 1==$bg ? "Violet" : "Plum";
						echo "<TD style=\"text-align:center; background-color:$color\">Complete</TD>";
					}  else {
						echo "<TD>&nbsp;</TD>";
					}
					?>
				</TR>

			<?php } ?>
			
			<TR>
				<TD ALIGN="center" COLSPAN=17><BR>
				<INPUT TYPE="submit" class="submit_medium" name="new_sheet" VALUE="New">
				<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
				<INPUT TYPE="submit" class="submit_medium" name="clone" VALUE="Clone">
				<!-- <IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
				<INPUT TYPE="submit" class="submit_medium" name="edit" VALUE="Edit"> -->
				<!-- <IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
				<INPUT TYPE="submit" class="submit_medium" name="delete" VALUE="Delete"> -->
				<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
				<INPUT TYPE="submit" class="submit_medium" name="X-Print" VALUE="X-Print">
				<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
				<INPUT TYPE="submit" class="submit_medium" name="qc_input" VALUE="QC Input Form">
				<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
				<INPUT TYPE="submit" class="submit_medium" name="qcreport" VALUE="X-QC Report">
				</TD>
			</TR>

		</TABLE>
		</FORM>

	<?php } else {
		echo "No matches found in database<BR>";
	}
}

?>









<?php if ( $bsn != "" ) {
	$sql="SELECT l.LotNumber AS abeleiLotNumber, l.LotSequenceNumber AS LotSequenceNumber FROM inventorymovements AS im, batchsheetmaster AS bsm, lots AS l ".
				"WHERE bsm.InventoryMovementTransactionNumber = im.TransactionNumber AND l.id=im.LotID AND bsm.BatchSheetNumber = $bsn";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	if (0 <  mysql_num_rows($result)) {
		$row = mysql_fetch_array($result);
		$abeleiLotNumber = $row[abeleiLotNumber];
		$LotSequenceNumber = $row[LotSequenceNumber];
	} 
	else {
		$abeleiLotNumber = "";
		$LotSequenceNumber = "";
	}
	
	$form_status = "";
	 if ( $_REQUEST['update'] != 1 ) {
		 $form_status = "readonly=\"readonly\"";
	}

	$commit_man_status = '';

	if ( $Filtered == 1 ) {
		$Filtered_status = "CHECKED";
	} else {
		$Filtered_status = "";
	}
	if ( $CommitedToInventory == 1 ) {
		$CommitedToInventory_status = "CHECKED";
		$commit_man_status = "READONLY";
	} else {
		$CommitedToInventory_status = "";
	}
	if ( $Manufactured == 1 ) {
		$Manufactured_status = "CHECKED";
		$commit_man_status = "READONLY";
	} else {
		$Manufactured_status = "";
	}

?>

	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0>
		<TR>
			<TD><B CLASS="black" STYLE="font-size:14pt">Batch sheet#:</B></TD>
			<TD><B CLASS="black" STYLE="font-size:14pt"><?php echo $bsn;?></B></TD>
			<TD><img src="images/spacer.gif" alt="spacer" width="15" border="0" height="1"></TD>
			<TD><B CLASS="black">abelei External#:</B></TD>
			<TD><?php echo $ProductNumberExternal;?></TD>
			<TD><img src="images/spacer.gif" alt="spacer" width="15" border="0" height="1"></TD>
			<TD><B CLASS="black">Description:</B></TD>
			<TD><?php echo $ProductDesignation;?></TD>
		</TR>
	</TABLE><BR>

	<?php
	if ( $CommitedToInventory != 1 and $Manufactured != 1 ) {
		$color = "LightSalmon"; $status_display = "New";
	} elseif ( $CommitedToInventory == 1 and $Manufactured != 1 ) {
		$color = "GreenYellow"; $status_display = "Committed";
	} elseif ( $Manufactured == 1 and $QualityControlDate == '<I>None entered yet</I>') {
		$color = "AliceBlue"; $status_display = "Lots Assigned";
	} elseif ( $QualityControlDate != '<I>None entered yet</I>' ) {
		$color = "Violet"; $status_display = "Complete";
	}
	echo "<div style=\"background-color:$color; border:solid 1px black; width:100%; font-size:20px; text-align:center; margin-bottom: 1em; padding:0.5em 0; font-weight:bold\">$status_display</div>";
	?>


	<?php  // ENTER POs FORM ?>

		<FORM ACTION="customers_batch_sheets.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="action" VALUE="edit">
		<INPUT TYPE="hidden" NAME="bsn" VALUE="<?php echo $bsn;?>">
		<INPUT TYPE="hidden" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>">
		<INPUT TYPE="hidden" NAME="ProductDesignation" VALUE="<?php echo $ProductDesignation;?>">
		<INPUT TYPE="hidden" NAME="con" VALUE="<?php echo $con;?>">

		<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC" WIDTH="100%"><TR><TD>
		<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD" WIDTH="100%"><TR><TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">

				<TR>
					<TD ALIGN=RIGHT>
					<?php if ( $_REQUEST['update'] != 1 ) { ?>
						<INPUT TYPE="button" CLASS="submit_normal" VALUE="Select Customer Order" onClick="window.location='customers_customer_order_shipping.php?action=search<?php echo (0 < $customer_id) ? "&customer_id=".$customer_id : "" ?>&bsn=<?php echo $bsn;?><?php echo ("" != $customer) ? "&customer=".urlencode($customer) : "" ?><?php echo ("" != $ProductNumberExternal) ? "&pne=".urlencode($ProductNumberExternal) : "" ?>'">
					<?php } ?>
					</TD>
				</TR>
				<TR>
					<TD><BR>
					<?php
					$sql = "SELECT bsci.*, customerordermaster.RequestedDeliveryDate, c.CustomerCodeNumber AS ccn, c.Quantity, c.PackSize, c.TotalQuantityOrdered, name
							FROM batchsheetcustomerinfo AS bsci
								LEFT JOIN customerorderdetail AS c 
									ON c.CustomerOrderNumber = bsci.CustomerOrderNumber 
										AND c.CustomerOrderSeqNumber = bsci.CustomerOrderSeqNumber 
										AND bsci.BatchSheetNumber = $bsn
								LEFT JOIN customerordermaster 
									ON c.CustomerOrderNumber = customerordermaster.OrderNumber
								LEFT JOIN customers 
									ON customers.customer_id = customerordermaster.CustomerID
							WHERE bsci.BatchSheetNumber = $bsn";
					//echo $sql;
					$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					$c = mysql_num_rows($result);
					$bg = 0; 
					if ( $c > 0 ) {

					$found_packs = true;

					?>
						
						<TABLE BORDER=0 CELLSPACING="0" CELLPADDING="3">
							<TR VALIGN=BOTTOM>
								<TD></TD>
								<TD></TD>
								<TD><B STYLE="font-size:8pt">Customer</B></TD>
								<TD><B STYLE="font-size:8pt">PO#</B></TD>
								<TD><B STYLE="font-size:8pt">Cust code</B></TD>
								<TD><B STYLE="font-size:8pt">Pack in</B></TD>
								<TD ALIGN=CENTER><B STYLE="font-size:8pt">#Packs</B></TD>
								<TD ALIGN=CENTER><B STYLE="font-size:8pt">Quantity<BR>ordered</B></TD>
								<TD ALIGN=CENTER><B STYLE="font-size:8pt">Pack<BR>size</B></TD>
								<TD ALIGN=CENTER><B STYLE="font-size:8pt">Total qty<BR>ordered</B></TD>
								<TD><B STYLE="font-size:8pt">Due date</B></TD>
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

								<TD>
								<?php if ( $row['CommitedToInventory'] != 1 and $row['Manufactured'] != 1 and $_REQUEST['update'] != 1 ) { ?>
									<INPUT TYPE="button" VALUE="X" CLASS="submit" onClick="delete_po(<?php echo($row['BatchSheetNumber']);?>,<?php echo($row['CustomerOrderNumber']);?>,<?php echo($row['CustomerOrderSeqNumber']);?>)">
								<?php } ?>
								</TD>

								<TD>
									<?php if ( $_REQUEST['update'] != 1 ) { ?>
										<INPUT TYPE="button" CLASS="submit" NAME="Edit" VALUE="Edit" <?php echo ($intermediary and !$FinalProductNotCreatedByAbelei) ? "disabled=\"disabled\"" : "onClick=\"popup('pop_select_customer_order.php?action=edit&edit_po=1&bsn=$row[BatchSheetNumber]&CustomerOrderNumber=$row[CustomerOrderNumber]&CustomerOrderSeqNumber=$row[CustomerOrderSeqNumber]&ccn=$row[ccn]',700,830)\"" ?> >
									<?php } ?>
								</TD>
								
								<?php if ( $_REQUEST['ccn'] == $row['ccn'] ) {
									$po_form_status = "";
								} else {
									$po_form_status = "readonly='readonly'";
								} ?>

								<TD STYLE="font-size:8pt"><?php echo $row['name'];?></TD>
								<TD STYLE="font-size:8pt"><?php echo $row['CustomerPONumber'];?></TD>
								<TD STYLE="font-size:8pt"><?php echo $row['ccn'];?></TD>
								<TD STYLE="font-size:8pt">
									<?php
									$sub_sql = "SELECT ProductNumberInternal, Designation 
												FROM productmaster 
												WHERE ProductNumberInternal LIKE '6%'";
									$sub_result = mysql_query($sub_sql, $link) or 
										die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
									while ( $sub_row = mysql_fetch_array($sub_result) ) {
										if ( $row[PackIn] == $sub_row[ProductNumberInternal] ) {
											echo $sub_row[Designation];
										}
										//echo "<OPTION VALUE='$sub_row[ProductNumberInternal]' ".($row[PackIn] == $sub_row[ProductNumberInternal] ? "SELECTED":"").">$sub_row[Designation]</OPTION>";
									}
									?>
								</TD>
								<TD STYLE="font-size:8pt" ALIGN=CENTER><?php echo $row['NumberOfPackages'];?></TD>
								<TD STYLE="font-size:8pt" ALIGN=CENTER><I><?php echo number_format($row['Quantity'], 2);?></I></TD>
								<TD STYLE="font-size:8pt" ALIGN=CENTER><I><?php echo number_format($row['PackSize'], 2);?></I></TD>
								<TD STYLE="font-size:8pt" ALIGN=CENTER><I><?php echo number_format($row['TotalQuantityOrdered'], 2);?></I></TD>
								<TD STYLE="font-size:8pt"><?php
								if ( $row['RequestedDeliveryDate'] != '' ) {
									echo date("n/j/Y", strtotime($row['RequestedDeliveryDate']));
								} else {
									echo "<I>None entered</I>";
								}
								?></TD>
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

		</TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE><BR>

	<?php   // ENTER POs FORM 
	// else
		// echo "<h3><em>Batches for intermediaries do not require Customer Orders.</em></h3>";
	?>



	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC" WIDTH="100%"><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD" WIDTH="100%"><TR><TD>
	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD" WIDTH="100%"><TR VALIGN=TOP><TD>

	<FORM ID="edit" NAME="edit" ACTION="customers_batch_sheets.php" METHOD="post">
	<INPUT TYPE="hidden" NAME="action" VALUE="edit">
	<INPUT TYPE="hidden" NAME="update" VALUE="1">
	<INPUT TYPE="hidden" NAME="save_master" VALUE="1">
	<INPUT TYPE="hidden" NAME="bsn" VALUE="<?php echo $bsn;?>">
	<INPUT TYPE="hidden" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>">
	<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>">
	<INPUT TYPE="hidden" NAME="ProductDesignation" VALUE="<?php echo $ProductDesignation;?>">
	<INPUT TYPE="hidden" NAME="LotID" VALUE="<?php echo $LotID;?>">
	<!-- <DIV ID="columns_wrapper" STYLE="overflow: auto; height: 450px; clear: both; width: 800px;"> -->

	<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0>

<!-- 
		<TR VALIGN=TOP>
			<TD><B>Customer:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" ID="customer" NAME="customer" VALUE="<?php //echo $customer;?>" SIZE=20 <?php //echo $form_status;?>> -->
			<INPUT TYPE="hidden" ID="customer_id" NAME="customer_id" VALUE="<?php echo $customer_id;?>"></TD>
<!-- 		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>
 -->

		<TR VALIGN=TOP>
			<TD><B>Net Weight:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="NetWeight" VALUE="<?php echo $NetWeight;?>" SIZE="20" <?php echo $commit_man_status;?> <?php echo $form_status;?>></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Gross Weight:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="gross_weight" VALUE="<?php echo number_format($gross_weight,2);?>" SIZE="20" READONLY <?php echo $form_status;?>></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Total Units:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD>
			<?php
			if ( $commit_man_status == "READONLY" ) {
				echo $TotalQuantityUnitType; ?>
				<INPUT TYPE="hidden" NAME="TotalQuantityUnitType" VALUE="<?php echo $TotalQuantityUnitType;?>">
			<?php } else { ?>
				<SELECT NAME="TotalQuantityUnitType" STYLE="font-size: 7pt" <?php echo $form_status;?>>
				<?php if ( $TotalQuantityUnitType == "grams" ) { ?>
					<OPTION VALUE=""></OPTION>
					<OPTION VALUE="grams" SELECTED>grams</OPTION>
					<OPTION VALUE="lbs">lbs</OPTION>
				<?php } elseif ( $TotalQuantityUnitType == "lbs" ) { ?>
					<OPTION VALUE=""></OPTION>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="lbs" SELECTED>lbs</OPTION>
				<?php } else { ?>
					<OPTION VALUE=""></OPTION>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="lbs">lbs</OPTION>
				<?php } ?>
				</SELECT>
			<?php } ?>
			</TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Column 1 Units:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD>
			<?php
			if ( $commit_man_status == "READONLY" ) {
				echo $Column1UnitType; ?>
				<INPUT TYPE="hidden" NAME="Column1UnitType" VALUE="<?php echo $Column1UnitType;?>">
			<?php } else { ?>
				<SELECT NAME="Column1UnitType" STYLE="font-size: 7pt" <?php echo $form_status;?>>
				<?php if ( $Column1UnitType == "grams" ) { ?>
					<OPTION VALUE=""></OPTION>
					<OPTION VALUE="grams" SELECTED>grams</OPTION>
					<OPTION VALUE="lbs">lbs</OPTION>
				<?php } elseif ( $Column1UnitType == "lbs" ) { ?>
					<OPTION VALUE=""></OPTION>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="lbs" SELECTED>lbs</OPTION>
				<?php } else { ?>
					<OPTION VALUE=""></OPTION>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="lbs">lbs</OPTION>
				<?php } ?>
				</SELECT>
			<?php } ?>
			</TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Column 2 Units:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD>
			<?php
			if ( $commit_man_status == "READONLY" ) {
				echo $Column2UnitType; ?>
				<INPUT TYPE="hidden" NAME="Column2UnitType" VALUE="<?php echo $Column2UnitType;?>">
			<?php } else { ?>
				<SELECT NAME="Column2UnitType" STYLE="font-size: 7pt" <?php echo $form_status;?>>
				<?php if ( $Column2UnitType == "grams" ) { ?>
					<OPTION VALUE=""></OPTION>
					<OPTION VALUE="grams" SELECTED>grams</OPTION>
					<OPTION VALUE="lbs">lbs</OPTION>
				<?php } elseif ( $Column2UnitType == "lbs" ) { ?>
					<OPTION VALUE=""></OPTION>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="lbs" SELECTED>lbs</OPTION>
				<?php } else { ?>
					<OPTION VALUE=""></OPTION>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="lbs">lbs</OPTION>
				<?php } ?>
				</SELECT>
			<?php } ?>
			</TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Yield (Percent):</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="Yield" VALUE="<?php echo $Yield;?>" SIZE="20" <?php echo $commit_man_status;?> <?php echo $form_status;?>></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Number of Times to Make:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="NumberOfTimesToMake" VALUE="<?php echo $NumberOfTimesToMake;?>" SIZE="20" <?php echo $commit_man_status;?> <?php echo $form_status;?>></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Vessel:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><SELECT NAME="Vessel" STYLE="font-size: 7pt" <?php echo $form_status;?>><option/>
			<?php
			$sub_sql = "SELECT ItemDescription FROM tblsystemdefaultsdetail WHERE ItemID=22";
			$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
			while ( $sub_row = mysql_fetch_array($sub_result) ) {
					echo "<OPTION VALUE='$sub_row[ItemDescription]' ".(($Vessel == $sub_row[ItemDescription]) ? "SELECTED":"").">$sub_row[ItemDescription]</OPTION>";
			}
			?>
			</SELECT></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Due Date:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="DueDate" ID="datepicker1" VALUE="<?php echo $DueDate;?>" SIZE="20" <?php echo $form_status;?>></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Scale#(s):</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="ScaleNumber" VALUE="<?php echo $ScaleNumber;?>" SIZE="20" <?php echo $form_status;?>></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR>
			<TD><B>Date Manufactured:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="DateManufactured" ID="datepicker2" VALUE="<?php echo $DateManufactured;?>" SIZE="20" <?php echo $form_status;?>></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Expiration Date:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="ExpirationDate" ID="datepicker3" VALUE="<?php echo $ExpirationDate;?>" SIZE="20" <?php echo $form_status;?>></TD>
		</TR>

	</TABLE>




	</TD>
	<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="20" HEIGHT="1"></TD>
	<TD>



	<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0>

		<TR VALIGN=TOP>
			<TD><B>Made by:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><SELECT NAME="MadeBy" STYLE="font-size: 7pt" <?php echo $form_status;?>>
			<OPTION VALUE=""></OPTION>
			<?php
			$sql = "SELECT * FROM users WHERE user_type = 3 AND active = 1 ORDER BY last_name";
			$result = mysql_query($sql, $link);
			if ( mysql_num_rows($result) > 0 ) {
				while ( $row = mysql_fetch_array($result) ) {
					if ( $MadeBy == $row['first_name'] . " " . $row['last_name'] ) {
						echo "<OPTION VALUE='" . $row['first_name'] . " " . $row['last_name'] . "' SELECTED>" . $row['first_name'] . " " . $row['last_name'] . "</OPTION>";
					} else {
						echo "<OPTION VALUE='" . $row['first_name'] . " " . $row['last_name'] . "'>" . $row['first_name'] . " " . $row['last_name'] . "</OPTION>";
					}
				}
			}
			?>
			</SELECT></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Filtered:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><INPUT TYPE="checkbox" NAME="Filtered" VALUE="1" <?php echo $Filtered_status;?> <?php echo $form_status;?>></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>QC Date:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><?php echo $QualityControlDate;?></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>QC Performed by:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><SELECT NAME="QualityControlEmployeeID" STYLE="font-size: 7pt" readonly='readonly'>
			<OPTION VALUE=""></OPTION>
			<?php
			$sql = "SELECT user_id, first_name, last_name FROM users WHERE user_type = 3 AND active = 1 ORDER BY last_name";
			$result = mysql_query($sql, $link);
			if ( mysql_num_rows($result) > 0 ) {
				while ( $row = mysql_fetch_array($result) ) {
					if ( $QualityControlEmployeeID == $row['user_id'] ) {
						echo "<OPTION VALUE='" . $row['user_id'] . "' SELECTED>" . $row['first_name'] . " " . $row['last_name'] . "</OPTION>";
					} else {
						echo "<OPTION VALUE='" . $row['user_id'] . "'>" . $row['first_name'] . " " . $row['last_name'] . "</OPTION>";
					}
				}
			}
			?>
			</SELECT></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Commited to Inventory:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD>
			<INPUT TYPE="hidden" NAME="CommitedToInventory" VALUE="<?php echo $CommitedToInventory;?>">
			<INPUT TYPE="checkbox" NAME="CommitedToInventory" VALUE="1" <?php echo $CommitedToInventory_status;?> readonly='readonly'></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Manufactured:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD>
			<INPUT TYPE="hidden" NAME="Manufactured" VALUE="<?php echo $Manufactured;?>">
			<INPUT TYPE="checkbox" NAME="Manufactured" VALUE="1" <?php echo $Manufactured_status;?> readonly='readonly'></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Inventory Remarks:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><TEXTAREA NAME="InventoryMovementRemarks" cols="50" rows="4" <?php echo $form_status;?>><?php echo $InventoryMovementRemarks;?></TEXTAREA></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B <?php echo (1 != $Manufactured ? "style=\"color:gray\"":"")?>>Abelei Lot#:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="abeleiLotNumber" VALUE="<?php echo $abeleiLotNumber;?>" SIZE="20" readonly="readonly"></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B <?php echo (1 != $Manufactured ? "style=\"color:gray\"":"")?>>Seq#:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" NAME="LotSequenceNumber" VALUE="<?php echo $LotSequenceNumber;?>" SIZE="20" readonly="readonly" ></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B>Notes:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD><TEXTAREA NAME="Notes" <?php echo $form_status;?> cols="50" rows="4"><?php echo $Notes;?></TEXTAREA></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD>
			<?php if ( $_REQUEST['edit_po'] == '' ) { ?>
				<?php if ( "" != $form_status ) { ?>
					<INPUT TYPE="button" VALUE="Edit" CLASS="submit" onClick="window.location='customers_batch_sheets.php?action=edit&update=1&bsn=<?php echo $bsn;?>'">
				<?php } else { ?>
					<INPUT TYPE="submit" VALUE="Save" CLASS="submit"> <INPUT TYPE="button" VALUE="Cancel" CLASS="submit" onClick="window.location='customers_batch_sheets.php?bsn=<?php echo $bsn;?>'">
				<?php } ?>
			<?php } ?>
			</TD>
		</TR>

	</TABLE>


	<!-- </DIV> -->

	</FORM>
	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE>
	<BR>








	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0"><TR VALIGN=TOP><TD>



	<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
	<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
	<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD ALIGN=CENTER>

	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD>

	<TABLE ALIGN=RIGHT BORDER=1 CELLSPACING="0" CELLPADDING="2" BORDERCOLOR="#FFFFFF">
		
		<TR VALIGN=BOTTOM>
			<!-- <TD COLSPAN=2>&nbsp;</TD> -->
			<TD ALIGN=RIGHT><B>Seq#</B></TD>
			<TD ALIGN=RIGHT><B>Internal#</B></TD>
			<TD><B>Natural or Artificial</B></TD>
			<TD style="width:500px"><B>Ingredient</B></TD>
			<TD ALIGN=RIGHT><B>Percentage</B></TD>
			<td ALIGN=RIGHT><b>Batch Amt. lbs</b></td>
			<td ALIGN=RIGHT><b>Batch Amt. grams</b></td>
			<?php echo (1 == $Manufactured) ? "" :"<td><b>Enough In Inv</b></td>
			"?><td ALIGN=RIGHT><b>Inv Curr Amt. lbs</b></td>
			<td ALIGN=RIGHT><b>Amt. On Order lbs</b></td>
			<td ALIGN=RIGHT><b>Amt. Committed lbs</b></td>
			<TD><B>Vendor</B></TD>
			<TD><B><NOBR>Raw Material<BR>Lot Number</NOBR></B></TD>
			<TD ALIGN=RIGHT><B>Quantity (lbs)</B></TD>
			<TD ALIGN=RIGHT><B>Quantity (grams)</B></TD>
			<!-- <TD>&nbsp;</TD> -->
		</TR>

	<?php

	// ARRAY OF INGREDIENTS TO BE USED BELOW TO CHECK WHETHER INVENTORY COMMITTMENT CAN BE MADE
	$ingredients = '';
	$insufficient_inventory = false;

	//, Quantity
	//$sql = "SELECT batchsheetdetail. *, productmaster.Designation, vendors.name, Quantity
	//FROM batchsheetdetail
	//LEFT JOIN inventorymovements ON batchsheetdetail.InventoryTransactionNumber = inventorymovements.TransactionNumber
	//LEFT JOIN productmaster ON batchsheetdetail.IngredientProductNumber = productmaster.ProductNumberInternal
	//LEFT JOIN vendors ON vendors.vendor_id = batchsheetdetail.VendorID
	//WHERE batchsheetdetail.BatchSheetNumber = " . $bsn . "
	//ORDER BY IngredientSEQ";

	$sql = "SELECT batchsheetdetail.*, productmaster.Natural_OR_Artificial, productmaster.Organic, productmaster.Kosher, productmaster.Designation, productmaster.ProductType, vendors.name, Quantity
	FROM batchsheetdetail
	LEFT JOIN inventorymovements ON batchsheetdetail.InventoryTransactionNumber = inventorymovements.TransactionNumber
	LEFT JOIN productmaster ON batchsheetdetail.IngredientProductNumber = productmaster.ProductNumberInternal
	LEFT JOIN vendors ON vendors.vendor_id = batchsheetdetail.VendorID
	WHERE batchsheetdetail.BatchSheetNumber = $bsn 
	ORDER BY IngredientSEQ";

	//$sql = "SELECT batchsheetdetaillotnumbers.*, productmaster.Designation, vendors.name, Quantity
	//FROM batchsheetdetaillotnumbers
	//LEFT JOIN inventorymovements ON batchsheetdetaillotnumbers.InventoryMovementTransactionNumber = inventorymovements.TransactionNumber
	//LEFT JOIN productmaster ON batchsheetdetaillotnumbers.IngredientProductNumber = productmaster.ProductNumberInternal
	//LEFT JOIN vendors ON vendors.vendor_id = batchsheetdetaillotnumbers.VendorID
	//WHERE batchsheetdetaillotnumbers.BatchSheetNumber = " . $bsn . "
	//ORDER BY IngredientSEQ";

	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	if ( $c > 0 ) {
		$total = 0;
		$i = 0;
		while ( $row = mysql_fetch_array($result) ) {
			if ( substr($row[IngredientProductNumber], 0, 1) != 4 ) {
				$ingredients[$i] = $row[IngredientProductNumber];
			}
			
			$description = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");

			$i++;
			if (2==substr($row[IngredientProductNumber],0,1)) {
				$sql_external = "SELECT ProductNumberExternal FROM externalproductnumberreference WHERE ProductNumberInternal = '$row[IngredientProductNumber]'";
				$result_external = mysql_query($sql_external, $link) or die (mysql_error()."<br />Couldn't execute query: $sql_external<BR><BR>");
				$row_external = mysql_fetch_array($result_external);
				$external = $row_external[0];
				$description="<i><b>$external</b> $row[Designation]</i> <form id=\"add_new\" name=\"add_new\" action=\"customers_batch_sheets.php\" method=\"post\"><input type=\"hidden\" id=\"external_number\" name=\"external_number\" value=\"$external\"><input type=\"submit\" class=\"submit new\" name=\"new_sheet_top\" id=\"new_sheet_top\" value=\"New Batch Sheet For Key\"></form>";
			}

			if ( substr($row['IngredientProductNumber'], 0, 1) == 4 ) {
				$bgcolor = "BGCOLOR='#666666'";
      			$cols = (1 == $manufactured ? 11 : 12);
				$colspan = "COLSPAN=$cols";
				$ingredient_string = "<B CLASS='white'>" . $description . "</B>";
			} else {
				$bgcolor = "";
				$colspan = "";
				$ingredient_string = $description;
			}

			?>

			<TR VALIGN=TOP>
			<!-- <FORM ACTION="#" METHOD="post"> -->

				<TD ALIGN=RIGHT><?php echo number_format($row[IngredientSEQ],0);?></TD>
				<TD ALIGN=RIGHT><?php echo $row[IngredientProductNumber];?></TD>
				<TD><?php echo $row[Natural_OR_Artificial];?></TD>
				<TD <?php echo $bgcolor;?> <?php echo $colspan;?>><?php echo $ingredient_string;?></TD>

				<?php if ( substr($row['IngredientProductNumber'], 0, 1) != 4 ) { ?>

					<TD ALIGN=RIGHT><?php echo number_format($row[Percentage], 2);?></TD>

					<?php
					//$sql = "SELECT ".
					//"ROUND(ProductTotal(productmaster.ProductNumberInternal,'C',NULL),2) as total, ".
					//"ROUND(ProductTotal(productmaster.ProductNumberInternal,'P',1),2) as ordered, ".
					//"ROUND(ProductTotal(productmaster.ProductNumberInternal,'P',8),2) as committed, ".
					//"ROUND(ProductTotal(productmaster.ProductNumberInternal,NULL, NULL),2) as net ".
					//"FROM productmaster ".
					//"WHERE productmaster.ProductNumberInternal=$row[IngredientProductNumber]";

					$sql = "Select DISTINCT ProductTotal(inventorymovements.ProductNumberInternal,'C',NULL) as total, ".
					"COALESCE((".
					"SELECT SUM(QuantityConvert( (TotalQuantityExpected), UnitOfMeasure, 'grams')) ".
					"FROM purchaseorderdetail WHERE ProductNumberInternal = productmaster.ProductNumberInternal AND (`Status` = 'O' OR `Status` = 'P')".
					"),0) as ordered, ".
					"ProductTotal(inventorymovements.ProductNumberInternal,'P',NULL) as committed, ".
					"ProductTotal(inventorymovements.ProductNumberInternal,NULL, NULL) as net, ".
					"productmaster.*, externalproductnumberreference.ProductNumberExternal as external ".
					"FROM productmaster ".
					"LEFT JOIN inventorymovements ON (inventorymovements.ProductNumberInternal = productmaster.ProductNumberInternal) ".
					"LEFT JOIN receipts ON ( receipts.LotID = inventorymovements.LotID ) ".
					"LEFT JOIN lots ON (inventorymovements.LotID = lots.ID) ".
					"LEFT JOIN externalproductnumberreference ON (externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal) ".
					"LEFT JOIN vwmaterialpricing ON (vwmaterialpricing.ProductNumberInternal = productmaster.ProductNumberInternal)
					WHERE productmaster.ProductNumberInternal=" . $row[IngredientProductNumber];

					$result_vend = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					// echo $sql . "<BR>";
					if ( mysql_num_rows($result_vend) > 0 ) {
						$row_inv = mysql_fetch_array($result_vend);
						$YorN="Y";
						$InvG = $row_inv[total];
						$AmtOrdG=$row_inv[ordered];
						$AmtComG=$row_inv[committed];
						$InvLbs=QuantityConvert($InvG, "grams", "lbs");
						$AmtOrdLbs=QuantityConvert($AmtOrdG, "grams", "lbs");
						$AmtComLbs=QuantityConvert($AmtComG, "grams", "lbs");
						$BatchAmtLbs = QuantityConvert($gross_weight*($row[Percentage]/100),$TotalQuantityUnitType,"lbs");
						$BatchAmtG   = QuantityConvert($gross_weight*($row[Percentage]/100),$TotalQuantityUnitType,"grams");
						if ( "108290"!=$row['IngredientProductNumber'] AND '6'!=substr($row['IngredientProductNumber'],0,1) ) // make exception for water (108290) and instructions
						{
							if (1 > $BatchAmtLbs AND round($InvG,2) < round($BatchAmtG,2) ) {
								$YorN = "N";
								$insufficient_inventory = true;
							}
							else if ( round($InvLbs,2) < round($BatchAmtLbs,2) ) {
								$YorN = "N";
								$insufficient_inventory = true;
							}
						}
						echo "<td ALIGN=RIGHT>".number_format($BatchAmtLbs,2)."</td><td ALIGN=RIGHT>".number_format($BatchAmtG,2)."</td>";
            if (1 != $Manufactured) {
              $style = ('N'==$YorN ? 'style="background:red;color:white"' : '');
              echo "<td $style ALIGN=RIGHT>$YorN</td>";
            }
            echo "<td ALIGN=RIGHT>".number_format($InvLbs,2)."</td><td ALIGN=RIGHT>".number_format($AmtOrdLbs,2)."</td><td ALIGN=RIGHT>".number_format($AmtComLbs,2)."</td>";
					} else 
					{ "<td>N</td><td><strong>NO INFO</strong></td>"; }
					//--------//
					$sql = "SELECT batchsheetdetaillotnumbers.IngredientProductNumber, vendors.name, lots.LotNumber AS ID, QuantityUsedFromThisLot
					FROM batchsheetdetaillotnumbers
					LEFT JOIN lots ON batchsheetdetaillotnumbers.LotID = lots.ID
					LEFT JOIN vendors ON lots.VendorId = vendors.vendor_id
					WHERE BatchSheetNumber = $bsn 
					AND batchsheetdetaillotnumbers.IngredientProductNumber = $row[IngredientProductNumber] 
					ORDER BY QuantityUsedFromThisLot";
					$result_vend = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					if ( mysql_num_rows($result_vend) > 0 ) {
						$current_id = "";
						while ( $row_vend = mysql_fetch_array($result_vend) ) {
							$vendor_name = $row_vend[name];
							if ( '' == $vendor_name  ) {
								if ('2' == substr($row[IngredientProductNumber], 0, 1)) {
									$vendor_name = 'Abelei';
								} else {
									$vendor_name = '<I>None entered</I>';
								}
							}
							if ( $current_id != $row_vend[IngredientProductNumber] ) {
								echo "<TD>$vendor_name</TD>";
								echo "<TD>$row_vend[ID]</TD>";
								echo "<TD ALIGN=RIGHT>" . number_format( QuantityConvert($row_vend['QuantityUsedFromThisLot'],'grams','lbs'),2) . "</TD>";
								echo "<TD ALIGN=RIGHT>" . number_format($row_vend['QuantityUsedFromThisLot'],2) . "</TD>";
							} else {
								echo "</TR><TR VALIGN=TOP>";
								echo "<TD COLSPAN=10>&nbsp;</TD>";
								echo "<TD>$vendor_name</TD>";
								echo "<TD>$row_vend[ID]</TD>";
								echo "<TD ALIGN=RIGHT>" . number_format( QuantityConvert($row_vend['QuantityUsedFromThisLot'],'grams','lbs'),2) . "</TD>";
								echo "<TD ALIGN=RIGHT>" . number_format($row_vend['QuantityUsedFromThisLot'],2) . "</TD>";
								echo "</TR>";
							}
							$current_id = $row_vend['IngredientProductNumber'];
						}
					} else {
						echo "<TD COLSPAN=4>&nbsp;</TD>";
					}
				}
				?>

			</TR><!-- </FORM> -->

		<?php
		}
	} else {
		echo "<h2>No Ingredients Found</h2><p>$sql</p>";
	}
	?>

	</TABLE>

	</TD></TR></TABLE>

	</TD></TR></TABLE>
	</TD></TR></TABLE>
	</TD></TR></TABLE><BR>


<?php 

		// CHECK TO SEE WHETHER INVENTORY COMMITMENT CAN BE MADE

		$is_intermediary = false;
		$sql = "SELECT Intermediary FROM externalproductnumberreference INNER JOIN productmaster USING (ProductNumberInternal) WHERE ProductNumberExternal = '" . $ProductNumberExternal . "' AND Intermediary = 0";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( mysql_num_rows($result) == 0 ) {
			$is_intermediary = true;
		}

		$is_contract_packing = false;
		$sql = "SELECT Count(*) AS count FROM BatchSheetCustomerInfo WHERE BatchSheetNumber = " . $bsn . " AND PackIn = 600012";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		if ( $row['count'] > 0 ) {
			$is_contract_packing = true;
		}


		// foreach ( $ingredients as $value ) {
			// if ('108290' != $value) {
				// $sql = "SELECT SUM(InventoryCount) AS total FROM vwinventory WHERE ProductNumberInternal = " . $value;
				// $result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				// $row = mysql_fetch_array($result);
				// if ( $row['total'] == 0 or $row['total'] == '' ) {
					// $insufficient_inventory = true;
				// }
			// }
		// }

		?>

		<FORM ACTION="customers_batch_sheets.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="action" VALUE="edit">
		<INPUT TYPE="hidden" NAME="bsn" VALUE="<?php echo $bsn;?>">
		<INPUT TYPE="hidden" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>">
		<INPUT TYPE="hidden" NAME="ProductDesignation" VALUE="<?php echo $ProductDesignation;?>">

		<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC" ALIGN=CENTER><TR><TD>
		<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
				<TR>
					<TD ALIGN="center">
					<INPUT TYPE="button" CLASS="submit_normal" NAME="Print" VALUE="Print" onClick="popup('reports/production_batch_sheet.php?bsn=<?php echo $bsn;?>',700,830)">
					<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1">
					<INPUT TYPE="button" CLASS="submit_normal" NAME="Delete" VALUE="Delete" <?php if ( 0 == $CommitedToInventory and 0 == $Manufactured ) { 
						echo "onClick=\"JavaScript:delete_batch($bsn,$LotID)\""; 
						}
						else { echo "disabled=\"disabled\" title=\"To delete a batch it must not be Committed nor Manufactured.\""; } ?> >
					<?php if ( 0 == $CommitedToInventory and ( ($intermediary and !$FinalProductNotCreatedByAbelei)or $found_packs ) ) { ?>
						<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1">
						<INPUT TYPE="submit" CLASS="submit_normal" NAME="Commit" VALUE="Commit to Inventory">
					<?php } elseif ( $CommitedToInventory != 1 ) { ?>
						<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1">
						<INPUT TYPE="button" CLASS="submit_normal" NAME="Commit" VALUE="Commit to Inventory" disabled="disabled" title="Please Select a Customer P.O. and Pack In">
					<?php } elseif ( $CommitedToInventory == 1 and $Manufactured != 1 ) { ?>
						<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1">
						<INPUT TYPE="submit" CLASS="submit_normal" NAME="Remove" VALUE="Remove from Committed">
					<?php } ?>
					<?php if ( $Manufactured == 1 ) { ?>
						<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1">
						<INPUT TYPE="button" CLASS="submit_normal" NAME="BackOut" VALUE="Back Out Manufactured" onClick="JavaScript:back_out(<?php echo($bsn);?>)">
					<?php } elseif ( $CommitedToInventory == 1 and $Manufactured != 1 and !$insufficient_inventory and $DateManufactured != '' ) { ?>
						<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1">
						<INPUT TYPE="button" CLASS="submit_normal" NAME="Assign" VALUE="Assign Lot Numbers" onClick="popup('pop_select_lots_for_batch_sheet.php?bsn=<?php echo $bsn;?>&pni=<?php echo $ProductNumberInternal;?>', 800, 840)">
					<?php } elseif (1 == $CommitedToInventory and 1 != $Manufactured) { 
						$message = "You cannot assign lot numbers now because:";
						if ( true == $insufficient_inventory) {
							$message .= " You don't have enough inventory to make this product.";
						}
						if ( '' == $DateManufactured) {
							$message .= " You haven't set the manufactured date.";
						}
					?>
						<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1">
						<INPUT TYPE="button" CLASS="submit_normal" NAME="Assign" VALUE="Assign Lot Numbers" onClick="alert('<?php echo addslashes($message) ?>');" title="<?php echo $message ?>">
					<?php } ?>
					<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1">
					<INPUT TYPE="button" VALUE="QC Input Form" onClick="popup('pop_qc_input_form.php?bsn=<?php echo $bsn;?>',700,830)" CLASS="submit_normal">
					<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1">
					<INPUT TYPE="button" VALUE="QC Report" onClick="popup('reports/qc_form.php?bsn=<?php echo $bsn;?>',700,830)" CLASS="submit_normal">
					</TD>
				</TR>
		</TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE></FORM><BR>


<?php } ?>

<BR><BR>



<?php include("inc_footer.php"); ?>