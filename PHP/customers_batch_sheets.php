<?php

//$debug = 0;
$debug = 0;
if ( debug == 0 ) {
include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 6) {
	header ("Location: login.php?out=1");
	exit;
}

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
include('inc_sqlfunctions.php');

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
	$sql = "SELECT PackIn, PackInID, NumberOfPackages, CustomerOrderNumber, CustomerOrderSeqNumber FROM batchsheetcustomerinfo as bsci
				LEFT JOIN batchsheetmaster AS bsm USING(BatchSheetnumber) 
				LEFT JOIN productmaster AS pm ON (pm.ProductNumberInternal = bsm.ProductNumberInternal) 
			WHERE bsci.BatchSheetNumber = $bsn AND 
				( pm.Intermediary = 0 OR pm.FinalProductNotCreatedByAbelei = 1)";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
    
	start_transaction($link);
	while ( $row = mysql_fetch_array($result) ) {
	   if ( $row['PackIn'] != "" ) {
		$sql = "INSERT INTO inventorymovements (ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate) 
					VALUES ($row[PackIn], $row[NumberOfPackages], 8, 'P', '" . date("Y-m-d H:i:s") . "')";
        
	//	echo $sql ."<br />";
		
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}
		// echo "<h3>PackIn - $sql</h3>";
		$insert_id = mysql_insert_id();

		$sql = "UPDATE batchsheetcustomerinfo SET 
		InventoryTransactionNumber = $insert_id 
		WHERE BatchSheetNumber = $bsn AND CustomerOrderNumber = $row[CustomerOrderNumber] AND CustomerOrderSeqNumber = $row[CustomerOrderSeqNumber]";
//		echo $sql . "<br />";
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}
	} elseif ( $row['PackInID'] != "" ) {
            $sql = "SELECT * FROM bscustomerinfopackins where PackInID in (" .$row[PackInID] .")";
            //echo "<br />".$sql."<br />";
            $pack_results = mysql_query($sql,$link) or die(mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
            
            while ( $row_pkins = mysql_fetch_array($pack_results) ) {
                $sql = "INSERT INTO inventorymovements (ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate) ".
				"VALUES (".$row_pkins[PackIn].",". $row_pkins[NumberOfPackages].", 8, 'P', '" . date("Y-m-d H:i:s") . "')";
        
		      if ( ! mysql_query($sql, $link) ) {
			     echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		  	     end_transaction(0,$link);
			     die;
	           }
		// echo "<h3>PackIn - $sql</h3>";
		      $insert_id = mysql_insert_id();

		      $sql = "UPDATE bscustomerinfopackins SET 
		      InventoryMovementTransactionNumber = $insert_id 
		      WHERE PackInID = $row_pkins[PackInID]";
		//echo $sql . "<br />";
	           if ( ! mysql_query($sql, $link) ) {
			     echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		      	end_transaction(0,$link);
		      	die;
	           	}
            
            }
            
        }
     }
        
        


	$sql = "SELECT LotID, ProductNumberInternal, NetWeight, Percentage, Yield, TotalQuantityUnitType,".
	" Column1UnitType, NumberOfTimesToMake, IngredientProductNumber,".
	" IngredientSEQ FROM batchsheetmaster LEFT JOIN batchsheetdetail USING(BatchSheetNumber) WHERE BatchSheetNumber = " . $bsn;
	if ( ! $result = mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}

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

		if ( substr($row[IngredientProductNumber], 0, 1) != 4 and '10829' != substr($row[IngredientProductNumber],0,5)) {   // OMIT INSTRUCTIONS AND WATER

			// $quantity = CalculateBatchSheetQuantity($NetWeight, $row['Percentage'], $Yield, $TotalQuantityUnitType, $Column1UnitType) * $NumberOfTimesToMake;
			$quantity = CalculateBatchSheetQuantity($NetWeight, $row[Percentage], $Yield, $TotalQuantityUnitType, 'grams') * $NumberOfTimesToMake;

			// echo "<h3>$quantity = CalculateBatchSheetQuantity($NetWeight, $Percentage, $Yield, $TotalQuantityUnitType, 'grams') * $NumberOfTimesToMake</h3>";
			$sql = "INSERT INTO inventorymovements 
					(ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate) 
					VALUES ($row[IngredientProductNumber], $quantity, 8, 'P', '" . date("Y-m-d H:i:s") . "')";
			// echo "<br />". $sql ."<br />";
			if ( ! mysql_query($sql, $link) ) {
				echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
				end_transaction(0,$link);
				die;
			}
			// echo "<h3>Ingredients - $sql</h3>";
			$insert_id = mysql_insert_id();

			$sql = "UPDATE batchsheetdetail 
						SET InventoryTransactionNumber = $insert_id 
						WHERE BatchSheetNumber = $bsn AND 
							IngredientProductNumber = $row[IngredientProductNumber] AND 
							IngredientSEQ = $row[IngredientSEQ]";
           // echo "<br />". $sql ."<br />";
			if ( ! mysql_query($sql, $link) ) {
				echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
				end_transaction(0,$link);
				die;
			}

			$total_percentage = $total_percentage + $row['Percentage'];

		}

	}


	// $flavor_quantity = CalculateBatchSheetQuantity($NetWeight, $total_percentage, $Yield, $TotalQuantityUnitType, $Column1UnitType) * $NumberOfTimesToMake;
	$flavor_quantity = QuantityConvert($NetWeight, $TotalQuantityUnitType, 'grams') * $NumberOfTimesToMake;
	$sql = "INSERT INTO inventorymovements 
				(ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate) 
				VALUES ($pni, $flavor_quantity, 9, 'P', '" . date("Y-m-d H:i:s") . "')";
    //echo "<br />". $sql ."<br />";
	if ( ! mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}
	// echo "<h3>$flavor_quantity = CalculateBatchSheetQuantity($NetWeight, $total_percentage, $Yield, $TotalQuantityUnitType, 'grams') * $NumberOfTimesToMake;...[".CalculateBatchSheetQuantity($NetWeight, $total_percentage, $Yield, $TotalQuantityUnitType, 'grams')."] $sql</h3>";
	$insert_id = mysql_insert_id();

	$sql = "UPDATE batchsheetmaster 
			SET InventoryMovementTransactionNumber = $insert_id, CommitedToInventory = 1
			WHERE BatchSheetNumber = $bsn";
	if ( ! mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}
	end_transaction(1,$link);
	$_SESSION['note'] = "Batch Sheet ingredients successfully committed to inventory<BR>";
	header("location: customers_batch_sheets.php?action=edit&bsn=" . $bsn);
	exit();

} //end of batchsheet commit

if ( !empty($_POST['Remove']) ) {

//customer info
	$sql = "SELECT InventoryTransactionNumber FROM inventorymovements
	INNER JOIN batchsheetcustomerinfo ON inventorymovements.TransactionNumber = batchsheetcustomerinfo.InventoryTransactionNumber
	WHERE BatchSheetNumber = " . $bsn . " AND MovementStatus = 'P' AND InventoryTransactionNumber IS NOT NULL";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// $_SESSION['note'] .= "<p>$sql</p>";
	start_transaction($link);
	
	while ( $row = mysql_fetch_array($result) ) {

		$sql = "UPDATE batchsheetcustomerinfo SET " .
		" InventoryTransactionNumber = NULL" . 
		" WHERE InventoryTransactionNumber = " . $row['InventoryTransactionNumber'];
	//	echo $sql . "<br />";
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}	
		// $_SESSION['note'] .= "<p>$sql</p>";
		
		$sql = "DELETE FROM inventorymovements WHERE TransactionNumber = " . $row['InventoryTransactionNumber'];
//		echo $sql . "<br />";
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}
		
		// $_SESSION['note'] .= "<p>$sql</p>";

	}
//bscustomerinfopackins
    $sql= "SELECT PackInID from batchsheetcustomerinfo where PackInID is not null AND PackInID <> '' AND BatchSheetNumber = ". $bsn;
    $result_pkinids = mysql_query($sql,$link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
    $row_pkinids = mysql_fetch_array($result_pkinids);
    if ( $row_pkinids[0] != "") {
        $sql = "SELECT TransactionNumber FROM inventorymovements ".
	   " INNER JOIN bscustomerinfopackins ON inventorymovements.TransactionNumber = bscustomerinfopackins.InventoryMovementTransactionNumber ".
	   " WHERE MovementStatus = 'P' AND InventoryMovementTransactionNumber IS NOT NULL ".
        " AND bscustomerinfopackins.PackInID in (" . $row_pkinids[0] .")";
   
	   $result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// $_SESSION['note'] .= "<p>$sql</p>";
	   start_transaction($link);
	
	   while ( $row = mysql_fetch_array($result) ) {

		$sql = "UPDATE bscustomerinfopackins SET " .
		" InventoryMovementTransactionNumber = NULL" . 
		" WHERE InventoryMovementTransactionNumber = " . $row['TransactionNumber'];
    
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}	
		// $_SESSION['note'] .= "<p>$sql</p>";
		
		$sql = "DELETE FROM inventorymovements WHERE TransactionNumber = " . $row['TransactionNumber'];

		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}
		
		// $_SESSION['note'] .= "<p>$sql</p>";

	   }
       end_transaction(1,$link);
    } //if pkinids <>''

//end bscustomerinfopackins
	$sql = "SELECT InventoryTransactionNumber FROM inventorymovements INNER JOIN batchsheetdetail ON inventorymovements.TransactionNumber = batchsheetdetail.InventoryTransactionNumber WHERE BatchSheetNumber = " . $bsn . " AND MovementStatus = 'P' AND InventoryTransactionNumber IS NOT NULL";
	// $_SESSION['note'] .= "<p>$sql</p>";
    start_transaction($link);
	if ( ! $result = mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}
	while ( $row = mysql_fetch_array($result) ) {

		$sql = "UPDATE batchsheetdetail SET " .
		" InventoryTransactionNumber = NULL" . 
		" WHERE BatchSheetNumber = " . $bsn . " AND InventoryTransactionNumber = " . $row['InventoryTransactionNumber'];
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}
		// $_SESSION['note'] .= "<p>$sql</p>";
		
		$sql = "DELETE FROM inventorymovements WHERE TransactionNumber = " . $row['InventoryTransactionNumber'];
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}		// $_SESSION['note'] .= "<p>$sql</p>";
	}

	// $sql = "SELECT InventoryMovementTransactionNumber FROM inventorymovements INNER JOIN batchsheetmaster ON inventorymovements.TransactionNumber = batchsheetmaster.InventoryMovementTransactionNumber WHERE BatchSheetNumber = " . $bsn . " AND MovementStatus = 'P' AND InventoryMovementTransactionNumber IS NOT NULL";
	// $result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	// // $_SESSION['note'] .= "<p>$sql</p>";
	// while ( $row = mysql_fetch_array($result) ) {

		$sql = "UPDATE batchsheetmaster SET " .
		" InventoryMovementTransactionNumber = NULL," . 
		" CommitedToInventory = 0" . 
		" WHERE BatchSheetNumber = " . $bsn;
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}		// $_SESSION['note'] .= "<p>$sql</p>";
		
		// $_SESSION['note'] .= "<p>$sql</p>";

	// }
	end_transaction(1,$link);
	$_SESSION['note'] .= "Batch Sheet ingredients successfully removed from committed inventory<BR>";
	
	header("location: customers_batch_sheets.php?action=edit&bsn=" . $bsn);
	
	exit();

} //end remove
// Not a search, not master edit and po edit 
if ( !empty($_POST) and $action != 'search' and empty($_POST['save_master']) and empty($_POST['save_po']) ) {

	//	$_POST['Print']   $_POST['qcinputform']   $_POST['qcreport']

	if ( empty($_POST['BatchSheetNumber']) and empty($_POST['external_number']) ) {
		$_SESSION['note'] = "Please choose a product before clicking an action<BR>";
		header("location: customers_batch_sheets.php?action=search&Designation=". $_POST['Designation'] . "&ProductNumberExternal=". $_POST['ProductNumberExternal'] . "&ProductNumberInternal=". $_POST['ProductNumberInternal'] . "&Keywords=". $_POST['Keywords']);
		exit();
	}

	if ( !empty($_POST['clone']) ) {
		
		$batch_sheet_num = $_POST['BatchSheetNumber'];
		$sql = "SELECT ProductNumberExternal, ProductNumberInternal, ProductDesignation, CustomerID, NetWeight, TotalQuantity, TotalQuantityUnitType,
		 Column1UnitType, Column2UnitType, Yield, NumberOfTimesToMake, Vessel, ScaleNumber, Filtered, Allergen, Kosher, Notes 
		 FROM batchsheetmaster WHERE BatchSheetNumber = " . $batch_sheet_num;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);

		//Add transaction, if any following SQL failed, then rollback, otherwise, commit - jdu
		start_transaction($link);
		
		$customerID = ( empty($row['CustomerID']) ) ? "NULL" : $row['CustomerID'];
		
		$sql = "INSERT INTO batchsheetmaster (ProductNumberExternal, ProductNumberInternal, ProductDesignation, CustomerID, NetWeight, TotalQuantity,
		 TotalQuantityUnitType, Column1UnitType, Column2UnitType, Yield, NumberOfTimesToMake, Vessel, ScaleNumber, Filtered, Allergen, Kosher, Notes)
		  VALUES (" . 
		"'" . $row['ProductNumberExternal'] . "', " .
		"'" . $row['ProductNumberInternal'] . "', " .
		"'" . $row['ProductDesignation'] . "', " .
		"" . $customerID . ", " .
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

		if ( mysql_query($sql, $link) ) {
			$new_bsn = mysql_insert_id();
			$IngredientNumberExternal = $row['ProductNumberExternal'];
		}
		else {
			$sql_error = mysql_error();
			end_transaction(0,$link);
			die ( $sql_error."<br />Couldn't execute query: $sql<BR><BR>");
		}
		

		$sql = "INSERT INTO lots (ID) VALUES (0)";
		
		if ( !mysql_query($sql, $link) ) {
			$sql_error = mysql_error();
			end_transaction(0,$link);
			die ( $sql_error."<br />Couldn't execute query: $sql<BR><BR>");
		} else {
			$lot_id = mysql_insert_id();
		}
		
		$sql = "UPDATE batchsheetmaster SET LotID = " . $lot_id . " WHERE BatchSheetNumber = " . $new_bsn;
		
		if ( !mysql_query($sql, $link) ) {
			$sql_error = mysql_error();
			end_transaction(0,$link);
			die ( $sql_error."<br />Couldn't execute query: $sql<BR><BR>");

		}

		$sql = "SELECT * FROM batchsheetdetail WHERE BatchSheetNumber = " . $batch_sheet_num;
		if ( ! $result = mysql_query($sql, $link) ) {
			$sql_error = mysql_error();
			end_transaction(0,$link);
			die ( $sql_error."<br />Couldn't execute query: $sql<BR><BR>");
	
		} else {
		  while ( $row = mysql_fetch_array($result) ) {
		  	// the database is not consistency, the forien key - IngredientNumberExternal most time empty thus failed follwoing SQL
		  	// as a temperory fix, instead of lookup databse, I make a by pass here - jdu
		  	$IngredientNumberExternal = (empty($row[IngredientNumberExternal]) ) ? $IngredientNumberExternal : $row['IngredientNumberExternal'];
			$sql = "INSERT INTO batchsheetdetail 
					(BatchSheetNumber, IngredientProductNumber, IngredientSEQ, 
						IngredientNumberExternal, IngredientDesignation, Intermediary, Percentage, 
						RawMaterialLotNumbers, SubBatchSheetNumber, FEMA_NBR, VendorID) 
					VALUES ($new_bsn, '$row[IngredientProductNumber]', '$row[IngredientSEQ]', 
						'$IngredientNumberExternal', '$row[IngredientDesignation]', 
						'$row[Intermediary]', '$row[Percentage]', NULL, 0, '$row[FEMA_NBR]', ".
						(( $row[VendorID] != "" ) ? $row[VendorID] : "NULL" ).")";
			if ( ! mysql_query($sql, $link) ) {
				$sql_error = mysql_error();
				end_transaction($link);
				die ( $sql_error."<br />Couldn't execute query: $sql<BR><BR>" );
			}
		  }
		}
		end_transaction(1,$link);
	
		header("location: customers_batch_sheets.php?action=search&Designation=". $_POST['Designation'] . "&ProductNumberExternal=". $_POST['ProductNumberExternal'] . "&ProductNumberInternal=". $_POST['ProductNumberInternal'] . "&Keywords=". $_POST['Keywords']);
		exit();



	} elseif ( !empty($_POST['X-Print']) ) {

		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "location.href='customers_batch_sheets.php?action=search&Designation=". $_POST['Designation'] . "&ProductNumberExternal=". $_POST['ProductNumberExternal'] . "&ProductNumberInternal=". $_POST['ProductNumberInternal'] . "&Keywords=" . $_POST['Keywords'] . "'\n";
		//echo "window.opener.document.add_prod.Designation.value='" . $_REQUEST['des'] . "'\n";
		echo "popup('reports/production_batch_sheet_excel.php?bsn=" . $_POST['BatchSheetNumber'] . "',1024,700)\n";
		echo "</SCRIPT>\n";

	}	elseif ( !empty($_POST['new_sheet']) or !empty($_POST['new_sheet_top']) or !empty($_POST['new_sheet_key']) ) {
//new batchsheet creation
		if ( !empty($_POST['new_sheet_key']) or ( !empty($_POST['new_sheet_top']) && !empty($_POST['external_number']) )  ) {
			$tmpArr = explode("&nbsp;",$_POST['external_number']);
			$sql = "SELECT externalproductnumberreference.ProductNumberInternal, externalproductnumberreference.ProductNumberExternal,
			 Natural_OR_Artificial, Designation, ProductType, productmaster.Kosher, SpecificGravity 
			 FROM externalproductnumberreference INNER JOIN productmaster
			  ON externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal
			   WHERE externalproductnumberreference.ProductNumberExternal = '$tmpArr[0]'";
		} else {
			$sql = "SELECT externalproductnumberreference.ProductNumberInternal, externalproductnumberreference.ProductNumberExternal,
			 Natural_OR_Artificial, Designation, ProductType, productmaster.Kosher, SpecificGravity
			  FROM batchsheetmaster INNER JOIN externalproductnumberreference USING(ProductNumberExternal)
			   INNER JOIN productmaster ON externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal
			    WHERE BatchSheetNumber = $_POST[BatchSheetNumber]";
		}
		$i=1;
		// $_SESSION[note] .= "<h3>$i - $sql</h3>"; $i++;
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		$pne = $row['ProductNumberExternal'];
		$pni = $row['ProductNumberInternal'];
		$SpecificGravity = $row['SpecificGravity'];
		$sql_pksz = "SELECT PackSize,UnitOfMeasure FROM productpacksize WHERE ProductNumberInternal='$pni' AND DefaultPksz=1";
		$result_pksz=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
		$row_pksz = mysql_fetch_array($result_pksz);
		$NetWeight=( $row_pksz['PackSize'] != "" ) ? $row_pksz['PackSize'] : 0;
		$TotalQuantityUnitType= ( $row_pksz['UnitOfMeasure'] != "" ) ? $row_pksz['UnitOfMeasure'] : "";
		if ( isset($_REQUEST['NetWeight'])) {
			$NetWeight = escape_data($_REQUEST['NetWeight']);
		}
		if ( isset($_REQUEST['TotalQuantityUnitType'])) {
			$TotalQuantityUnitType = escape_data($_REQUEST['TotalQuantityUnitType']);
		}
		
		$Column1UnitType = "lbs";
		if ( isset($_REQUEST['Column1UnitType'])) {
			$Column1UnitType = escape_data($_REQUEST['Column1UnitType']);
		}
		
		$Column2UnitType = "grams";
		if ( isset($_REQUEST['Column2UnitType'])) {
			$Column2UnitType = escape_data($_REQUEST['Column2UnitType']);
		}
		
		$Yield = 0.98;
		if (isset($_REQUEST['Yield'])) {
			$Yield = escape_data($_REQUEST['Yield']);
		}
		
		$NumberOfTimesToMake = 1;
		if ( isset($_REQUEST['NumberOfTimesToMake'])) {
			$NumberOfTimesToMake = escape_data($_REQUEST['NumberOfTimesToMake']);
		}
		
		if ( !is_numeric($SpecificGravity) ) {
			$SpecificGravity = 0;
		}
		$ProductDesignation = (("" != $row['Natural_OR_Artificial']) ? $row['Natural_OR_Artificial']." " : "").
			$row['Designation'].(("" != $row['ProductType']) ? " - ".$row['ProductType'] : "").
			(("" != $row['Kosher']) ? " - ".$row['Kosher'] : "");
		$sql = "INSERT INTO batchsheetmaster (ProductNumberExternal, ProductNumberInternal, ProductDesignation, NetWeight,
		 TotalQuantityUnitType, Column1UnitType, Column2UnitType, Yield, NumberOfTimesToMake) VALUES (" . 
		"'" . $pne . "', " .
		$pni . ", " .
		"'" . $ProductDesignation . "', " .
		"$NetWeight, " .
		"'".$TotalQuantityUnitType ."',".
		"'".$Column1UnitType ."'," .
		"'".$Column2UnitType ."', " .
		"$Yield, " .
		"$NumberOfTimesToMake" .
		")";
		start_transaction($link);
        
        echo "<br />" .$sql . "<br />";
        
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;			
		}
    // $_SESSION[note] .= "<h3>$i - $sql</h3>"; $i++;
		$new_bsn = mysql_insert_id();

		$sql = "INSERT INTO lots (ID,VendorID) VALUES (0,2382)";
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}
     // $_SESSION[note] .= "<h3>$i - $sql</h3>"; $i++;
        $lot_id = mysql_insert_id();

		$sql = "UPDATE batchsheetmaster SET LotID = " . $lot_id . " WHERE BatchSheetNumber = " . $new_bsn;
    // $_SESSION[note] .= "<h3>$i - $sql</h3>"; $i++;
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
			
		}

/*				$sql = "SELECT IngredientProductNumber, IngredientSEQ, VendorID, IngredientDesignation, Intermediary, Percentage FROM pricesheetdetail WHERE PriceSheetNumber = $psn"; */
				$sql = "SELECT fd.IngredientProductNumber, fd.IngredientSEQ, fd.VendorID, fd.Percentage, pm.Designation, pm.Intermediary
						FROM formulationdetail AS fd LEFT JOIN productmaster AS pm ON pm.ProductNumberInternal = IngredientProductNumber
						WHERE fd.ProductNumberInternal = $pni";
				if ( ! $result = mysql_query($sql, $link) ) {
					echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
					end_transaction(0,$link);
					die;
				}
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
					if ( ! mysql_query($sql, $link) ) {
						echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
						end_transaction(0,$link);
						die;
					}
				}
			/* }
		 } */
		 // Make a link between key ingredient with parent batchsheet
		if (isset($_REQUEST['BatchSheetNumber']) and !empty($_POST['new_sheet_key'])) {
			$sql = "UPDATE batchsheetdetail set SubBatchSheetNumber='$new_bsn' 
			WHERE BatchSheetNumber='".escape_data($_REQUEST['BatchSheetNumber'])."' 
			AND IngredientProductNumber='".$pni."' AND IngredientSEQ=".escape_data($_REQUEST['IngredientSEQ']);
			
			if ( ! mysql_query($sql,$link) ) {
				echo mysql_error() . " Failed execute SQL : $sql <br />";
				end_transaction(0,$link);
				die;
			}
			$sql = "SELECT * FROM batchsheetcustomerinfo 
			WHERE BatchSheetNumber='".escape_data($_REQUEST['BatchSheetNumber'])."'";
			if ( ! ($result = mysql_query($sql,$link) ) ) {
				echo mysql_error() . " Failed execute SQL : $sql <br />";
				end_transaction(0,$link);
				die;
			}
			if ( mysql_num_rows($result) > 0 ) {
				while ( $row = mysql_fetch_array($result) ) {
				$sql = "INSERT INTO batchsheetcustomerinfo (BatchSheetNumber, CustomerOrderNumber,CustomerOrderSeqNumber,
				CustomerPONumber) VALUES('$new_bsn','".$row['CustomerOrderNumber']."','".$row['CustomerOrderSeqNumber']."','".
				$row['CustomerPONumber']."')";
				
				if ( ! mysql_query($sql,$link) ) {
					echo mysql_error() . " Failed execute SQL : $sql <br />";
					end_transaction(0,$link);
					die;
				}
				}
			}
			
			$sql = "SELECT CustomerID FROM batchsheetmaster 
			WHERE BatchSheetNumber='".escape_data($_REQUEST['BatchSheetNumber'])."'";
			if ( ! ($result = mysql_query($sql,$link) ) ) {
				echo mysql_error() . " Failed execute SQL : $sql <br />";
				end_transaction(0,$link);
				die;
			}
			if ( mysql_num_rows($result) > 0 ) {
				$row = mysql_fetch_array($result);
				$sql = "UPDATE batchsheetmaster set CustomerID='".$row[0]."' WHERE BatchsheetNumber='".$new_bsn."'";
				
				if ( ! mysql_query($sql,$link) ) {
					echo mysql_error() . " Failed execute SQL : $sql <br />";
					end_transaction(0,$link);
					die;
				}
			}
			
		}
		// Pull customer PO into new key
		
		end_transaction(1,$link);
		header("location: customers_batch_sheets.php?reload_opener=1&action=edit&bsn=$new_bsn");
		exit();

	}

}//end 
			


if ( $edit and isset($_POST['save_master']) ) { //save changed bachsheet 

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
    if ( $TotalQuantityUnitType == "" or $TotalQuantityUnitType == "N/A") {
    	$Column1UnitType = "";
    	$Column2UnitType = "";
    }
	$Yield = $_POST['Yield'];
	$NumberOfTimesToMake = $_POST['NumberOfTimesToMake'];
	$Vessel = $_POST['Vessel'];
	echo "<br /> Vessel = " .$Vessel ."<br />";	
	$DueDate = $_POST['DueDate'];  // mm/dd/ccyy
	$date_parts = explode("/", $DueDate);
	$NewDueDate = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1]; //ccyy-mm-dd
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


	$MadeBy = $_POST['MadeBy'];
	$Filtered = $_POST['Filtered'];

	$qc_month = $_POST['qc_month'];
	$qc_day = $_POST['qc_day'];
	$qc_year = $_POST['qc_year'];

	$QualityControlEmployeeID = $_POST['QualityControlEmployeeID'];
	$CommitedToInventory = $_POST['CommitedToInventory'];
	$Manufactured = $_POST['Manufactured'];
	//$InventoryMovementRemarks = $_POST['InventoryMovementRemarks'];
	if ( $Manufactured == 1 ) {
		$abeleiLotNumber = $_POST['abeleiLotNumber'];
		$LotSequenceNumber = $_POST['LotSequenceNumber'];
		$abeleiLotNumber_save = $_POST['abeleiLotNumber_save'];
		$LotSequenceNumber_save = $_POST['LotSequenceNumber_save'];
	}

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
		$NumberOfTimesToMake = 1;
	}
	if ( $Yield == '' ) {
		$Yield = 1.00;
	}
	if ( $LotSequenceNumber == '' ) {
		$LotSequenceNumber = 0;
	}
	if ( $InventoryMovementTransactionNumber == '' ) {
		$InventoryMovementTransactionNumber = 0;
	}

	// check_field() FUNCTION IN global.php. 3 - numeric field, following usages are vian.
	//check_field($customer_id, 3, 'Customer');
	//check_field($NetWeight, 3, 'Net Weight');
	//check_field($TotalQuantity, 3, 'Total Quantity');
	///check_field($NumberOfTimesToMake, 3, 'Number of Times to Make');
	//check_field($Yield, 3, 'Yield');
	//check_field($InventoryMovementTransactionNumber, 3, 'Inventory Movement Transaction Number');

	if ( $NumberOfTimesToMake > 1000 ) {
		$error_found = true;
		$error_message .= "Please enter a lower value for 'Number of Times to Make'<BR>";
	}
	else if (0 >= $NumberOfTimesToMake ) {
		$error_found = true;
		$error_message .= "Please enter a positive value for 'Number of Times to Make'<BR>";
	}

//	if ( $NetWeight > 10000 ) { removed as requested jdu
//		$error_found = true;
//		$error_message .= "Please enter a lower value for 'Net Weight'<BR>";
//	}
//	else 
	if (0 >= $NetWeight ) {
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
		$abelei_lot_updt = "";
		$abelei_lot_sq_updt = "";
		$lots_LotNumber_upd = "";
		$lots_LotSeqNumber_upd = "";
		
		echo "Manufactured = " . $Manufactured . "<br />";
		if ( $Manufactured == 1 ) {
			if ( $abeleiLotNumber != $abeleiLotNumber_save )  {
			//	$abelei_lot_updt = " abeleiLotNumber = '" . $abeleiLotNumber . "',";
				$lots_LotNumber_upd =" LotNumber = '" . $abeleiLotNumber . "',";
			}
			if ( $LotSequenceNumber != $LotSequenceNumber_save ) {
			//	$abelei_lot_sq_updt = " LotSequenceNumber = '" . $LotSequenceNumber . "',";
				$lots_LotSeqNumber_upd = " LotSequenceNumber='". $LotSequenceNumber ."',";
			}
		}
		$Notes = escape_data($Notes);
		
		if ( $bsn != "" ) {
			start_transaction($link);
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
			$abelei_lot_updt .
			$abelei_lot_sq_updt .
			//" QualityControlEmployeeID = '" . $QualityControlEmployeeID . "'," .
			//" CommitedToInventory = '" . $CommitedToInventory . "'," .
			//" Manufactured = '" . $Manufactured . "'," .
			//" InventoryMovementRemarks = '" . $InventoryMovementRemarks . "'," .
			//" abeleiLotNumber = '" . $abeleiLotNumber . "'," .
			//" LotSequenceNumber = '" . $LotSequenceNumber . "'," .
			" Notes = '" . $Notes . "'" .
			" WHERE BatchSheetNumber = " . $bsn;
			
			//$_SESSION['note'] = $sql;
			
			if ( ! mysql_query($sql, $link) )
			{	echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
				end_transaction(0,$link);
				die;
			}

			$sql = "UPDATE lots SET" .
			" DateManufactured=" . (("" != $NewDateManufactured) ? "'$NewDateManufactured'" : "NULL") . ", " .
			$lots_LotNumber_upd . $lots_LotSeqNumber_upd .
			" ExpirationDate=" . (("" != $NewExpirationDate) ? "'$NewExpirationDate'" : "NULL") . " " .
			" WHERE ID = '" . $LotID ."'";
		//	echo "<p>" . $sql . "</p>";
			if ( ! mysql_query($sql, $link) ) {
				echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
				end_transaction(0,$link);
				die;
			}
		    end_transaction(1,$link);
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

	$gross_weight = ( empty($NetWeight) || empty($Yield) ) ? 0 : $NetWeight/$Yield;

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
	if ( '2' == substr($ProductNumberInternal,0,1) ) { //check depending batchsheet of this key
	
		$sql = "SELECT distinct BatchSheetNumber,IngredientSEQ, SubBatchSheetNumber FROM batchsheetdetail
		LEFT JOIN batchsheetmaster USING(BatchSheetNumber) 
		WHERE IngredientProductNumber = '$ProductNumberInternal' 
			AND ( batchsheetmaster.Manufactured = 0 OR SubBatchSheetNumber > 100 )";
		$result_bsd = mysql_query($sql,$link) or die ( mysql_error() ." Failed Execute : $sql <br />");
		//$note = $sql;
		$c = mysql_num_rows($result_bsd);
		if ( $c > 0 ) {
		$key_note = "";
		 while ($row_bsd = mysql_fetch_array($result_bsd) ) {
			if ( $row_bsd['SubBatchSheetNumber'] == "" ) {
				$key_note .= "External# $ProductNumberExternal of This BatchSheet# is also used in Open BatchSheet:\n";
				$key_note .= "<a href='customers_batch_sheets.php?bsn=". $row_bsd['BatchSheetNumber'] . "' target='_blank'>".
				$row_bsd['BatchSheetNumber'] . "</a>.&nbsp;Add BS#:" .$bsn." <a href='BatchSheetAddToKey.php?ipn=".$ProductNumberInternal."&bsn=".$row_bsd['BatchSheetNumber']."&key=".$bsn."&iseq=".$row_bsd['IngredientSEQ']."' target='_blank'>as Key of " .$row_bsd['BatchSheetNumber']. "</a><br />\n";
			} else if ( $row_bsd['SubBatchSheetNumber'] == $bsn ) {
				$key_note .= "This Batchsheet# is made for Batch Sheet#:<a href='customers_batch_sheets.php?bsn=".
				$row_bsd['BatchSheetNumber']."'>".$row_bsd['BatchSheetNumber']."</a><br />\n";
			}
		 }
		 
		}
	}	
}

if ( $action == "back_out" ) {
    start_transaction($link);
//back out batchsheet master	
	$bsn=escape_data($_GET['bsn']);
	$sql = "UPDATE lots, batchsheetmaster AS bsm SET lots.LotNumber = NULL, lots.LotSequenceNumber = NULL WHERE bsm.LotID = lots.ID AND BatchSheetNumber = '" . $bsn ."'";
	if ( ! mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}
//reset bsms mvstatus
	$sql = "UPDATE inventorymovements SET MovementStatus = 'P' WHERE TransactionNumber in (SELECT InventoryMovementTransactionNumber FROM batchsheetmaster WHERE BatchSheetNumber = " . $bsn . ")";
   // echo "<br />" .$sql ."<br />";
	if ( ! mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}

	$sql = "UPDATE batchsheetmaster SET Manufactured = 0 WHERE BatchSheetNumber = " . $bsn;   //, abeleiLotNumber=NULL
	if ( ! mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}

//back out batchsheet details
   //lot will be deleted thus its im
	$sql = "DELETE im.* FROM inventorymovements AS im, batchsheetdetaillotnumbers AS bsdln WHERE im.TransactionNumber = bsdln.InventoryMovementTransactionNumber AND bsdln.BatchSheetNumber = $bsn";
	if ( ! mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}
 
   //delete batchsheetdetaillot
   	$sql = "DELETE FROM batchsheetdetaillotnumbers where BatchSheetNumber = $bsn";
		if ( ! mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}
   //create IM transaction# for batchsheetdetail
   	$sql = "SELECT LotID, ProductNumberInternal, NetWeight, Percentage, Yield, 
					TotalQuantityUnitType, Column1UnitType, Column2UnitType, NumberOfTimesToMake, 
					IngredientProductNumber, IngredientSEQ 
			FROM batchsheetmaster 
				LEFT JOIN batchsheetdetail USING(BatchSheetNumber) 
			WHERE BatchSheetNumber = $bsn";
	if ( ! $result=mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}

	$total_percentage = 0;

	while ( $row = mysql_fetch_array($result) ) {

		if ( $pni == '' ) { //only need following master variables once
			$LotID = $row[LotID];
			$pni = $row[ProductNumberInternal];
			$NumberOfTimesToMake = $row[NumberOfTimesToMake];
			$NetWeight = $row[NetWeight];
			$Percentage = $row[Percentage];
			$Yield = $row[Yield];
			$TotalQuantityUnitType = $row[TotalQuantityUnitType];
	//		$Column1UnitType = $row[Column1UnitType];
	//		$Column2UnitType = $row[Column2UnitType];
		}

		if ( substr($row[IngredientProductNumber], 0, 1) != 4 ) {   // OMIT INSTRUCTIONS

			// $quantity = CalculateBatchSheetQuantity($NetWeight, $row['Percentage'], $Yield, $TotalQuantityUnitType, $Column1UnitType) * $NumberOfTimesToMake;
			$quantity = CalculateBatchSheetQuantity($NetWeight, $row[Percentage], $Yield, $TotalQuantityUnitType, 'grams') * $NumberOfTimesToMake;

			$sql = "INSERT INTO inventorymovements 
					(ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate) 
					VALUES ( $row[IngredientProductNumber], $quantity, 8, 'P', '" . date("Y-m-d H:i:s") . "')";
	//		echo "<br />".$sql."<br />";
            if ( ! mysql_query($sql, $link) ) {
				echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
				end_transaction(0,$link);
				die;
			}
			$insert_id = mysql_insert_id();

			$sql = "UPDATE batchsheetdetail SET InventoryTransactionNumber = $insert_id 
					WHERE BatchSheetNumber = $bsn 
						AND IngredientProductNumber = $row[IngredientProductNumber] AND 
						IngredientSEQ = $row[IngredientSEQ]";
			echo "<br />".$sql."<br />";
            if ( ! mysql_query($sql, $link) ) {
				echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
				end_transaction(0,$link);
				die;
			}
		}
	}
   
//backout bsci
    //delete inmvnm of pkgs
    $sql_bsltnm = "DELETE IM.* from inventorymovements AS IM, batchsheetdetailpackaginglotnumbers as bsplnm WHERE IM.TransactionNumber=bsplnm.InventoryMovementTransactionNumber AND bsplnm.BatchSheetNumber = " . $bsn;
	if ( ! mysql_query($sql_bsltnm,$link) ) {
    	echo mysql_error()."<br />Couldn't execute query: $sql_bsltnm<BR><BR>";
		end_transaction(0,$link);
		die;
	}
    // delete lots of batchsheet packages  
    $sql = "DELETE FROM batchsheetdetailpackaginglotnumbers WHERE BatchSheetNumber = " . $bsn;
	if ( ! mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}
    
	$sql = "SELECT * FROM batchsheetcustomerinfo WHERE BatchSheetNumber = $bsn AND PackIn IS NOT NULL AND PackIn <> ''";
	if ( ! $result=mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}
	
	while ( $row = mysql_fetch_array($result) ) {
	  	$sql = "INSERT INTO inventorymovements 
				(ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate) 
				VALUES ($row[PackIn], $row[NumberOfPackages], 8, 'P', '" . date("Y-m-d H:i:s") . "')";
    	if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}
		$insert_id = mysql_insert_id();

		$sql = "UPDATE batchsheetcustomerinfo 
				SET InventoryTransactionNumber = $insert_id 
				WHERE BatchSheetNumber = $bsn AND 
					CustomerOrderNumber = $row[CustomerOrderNumber] AND 
					CustomerOrderSeqNumber = $row[CustomerOrderSeqNumber]";
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}

	}
   //bscustomerinfopackins
   $sql = "SELECT * FROM batchsheetcustomerinfo WHERE BatchSheetNumber = $bsn AND ( PackIn IS NULL  OR PackIn = '' ) AND PackInID IS NOT NULL AND PackInID <> ''";
	if ( ! $result=mysql_query($sql, $link) ) {
		echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		end_transaction(0,$link);
		die;
	}

    while ( $row = mysql_fetch_array($result) ) {
        $sql = "SELECT * FROM bscustomerinfopackins where PackInID in (" .$row[PackInID] .")";
        
        if (! $results_bscipkins = mysql_query($sql, $link) ) {
          echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
		  end_transaction(0,$link);
		  die;
        }
        
        while ( $row_pkins = mysql_fetch_array($results_bscipkins)) {
            
       
		$sql = "INSERT INTO inventorymovements 
				(ProductNumberInternal, Quantity, TransactionType, MovementStatus, TransactionDate) 
				VALUES ($row_pkins[PackIn], $row_pkins[NumberOfPackages], 8, 'P', '" . date("Y-m-d H:i:s") . "')";
    	
        if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}
		$insert_id = mysql_insert_id();

		$sql = "UPDATE bscustomerinfopackins 
				SET InventoryMovementTransactionNumber = $insert_id 
				WHERE PackInID = $row_pkins[PackInID]";
        
		if ( ! mysql_query($sql, $link) ) {
			echo mysql_error()."<br />Couldn't execute query: $sql<BR><BR>";
			end_transaction(0,$link);
			die;
		}
    }
	}
//end bscipkins
    end_transaction(1,$link);
	$_SESSION[note] = "Manufacturing successfully backed out of<BR>";
	header("location: customers_batch_sheets.php?action=edit&bsn=$bsn");
	exit();
} //End Back out

if ( $action == "delete_batch" ) {
	if (!empty($_GET[bsn]) and !empty($_GET[LotID])) {
		$sql = "DELETE FROM batchsheetmaster WHERE BatchSheetNumber = " . $_GET[bsn];
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$sql = "DELETE FROM lots WHERE ID = " . $_GET[LotID];
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		
		//delete the link of key to orig if the deleted is a key
		$sql="UPDATE batchsheetdetail SET SubBatchSheetNumber=null where SubBatchSheetNumber='". 
			escape_data($_GET['bsn'])."'";
			mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
			
		$_SESSION[note] = "<br/>Batch sheet successfully deleted<BR>";
	}
	else
	{
		$_SESSION[note] = "Missing information to delete batch sheet. Contact Admin.<BR>";
	}
	header("location: customers_batch_sheets.php");
	exit();
} //end delete_batch

if ( $action == "delete_po" ) {
    
    $sql ="SELECT PackInID FROM batchsheetcustomerinfo 
			WHERE BatchSheetNumber = $_GET[bsn] AND 
				CustomerOrderNumber = $_GET[con] AND
                CustomerOrderSeqNumber = $_GET[seq] AND
                PackInID is not null AND PackInID <> ''";
    $result_pkin = mysql_query($sql,$link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
    $c = mysql_num_rows($result_pkin);
    if ( $c > 0) {
        $row_pkin=mysql_fetch_array($result_pkin);
        $sql = "DELETE FROM bscustomerinfopackins where PackInID in (". $row_pkin[0].")";
        mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
    }
                
	$sql = "DELETE FROM batchsheetcustomerinfo 
			WHERE BatchSheetNumber = $_GET[bsn] AND 
				CustomerOrderNumber = $_GET[con] AND 
				CustomerOrderSeqNumber = $_GET[seq]";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	header("location: customers_batch_sheets.php?action=edit&bsn=" . $bsn);
	exit();
}  // end delete_po

if ( isset($_REQUEST['batch_sheet_num']) and $action == 'search' ) {
	$batch_sheet_num = $_REQUEST['batch_sheet_num'];
}
if ( isset($_REQUEST[Designation]) and $action == 'search' ) {
	$tmpArr = explode("&nbsp;", $_REQUEST[Designation]);
	$Designation = escape_data($tmpArr[0]);
}
if ( isset($_REQUEST[ProductNumberExternal]) and $action == 'search' ) {
	$tmpArr = explode("&nbsp;",$_REQUEST[ProductNumberExternal]);
	$ProductNumberExternal = $tmpArr[0];
}

if ( $debug > 0 ) {

	// $action="search"; //debugging

	$bsn='1670';

//	$ProductNumberExternal = "144a11"; //debug
}

if ( isset($_REQUEST[ProductNumberInternal]) and $action == 'search' ) {
	$tmpArr = explode("&nbsp;",$_REQUEST[ProductNumberInternal]);
	$ProductNumberInternal = $tmpArr[0];
}
if ( isset($_REQUEST[Keywords]) and $action == 'search' ) {
	$Keywords = $_REQUEST[Keywords];
}
if ( isset($_REQUEST[status]) and $action == 'search' ) {
	$status = $_REQUEST[status];
}

function CalculateBatchSheetQuantity($NetWeight, $Percentage, $Yield, $Total_Unit_Type, $Target_Unit_Type) {
	$percent_quantity = ($NetWeight/$Yield) * ($Percentage*0.01);
	return QuantityConvert($percent_quantity, $Total_Unit_Type, $Target_Unit_Type);
}


$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

$statuses = array("", "New", "Committed", "Lots assigned", "Complete");

include("inc_header.php");

if ( !empty($_POST['qc_input']) ) {
	
	$tmpArr = explode("&nbsp;",$_POST['Designation']);
	
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "location.href='customers_batch_sheets.php?action=search&Designation=". $tmpArr[0];
	$tmpArr = explode("&nbsp;",$_POST['ProductNumberExternal']);
	echo "&ProductNumberExternal=". $tmpArr[0];
	$tmpArr = explode("&nbsp;",$_POST['ProductNumberInternal']);
	echo "&ProductNumberInternal=". $tmpArr[0] . "&Keywords=". $_POST['Keywords'] . "'\n";
	echo "popup('pop_qc_input_form.php?bsn=" . $_POST['BatchSheetNumber'] . "',700,630)\n";
	echo "</SCRIPT>\n";
}

if ( !empty($_POST['qcreport']) ) {
	$tmpArr = explode("&nbsp;",$_POST['Designation']);
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "location.href='customers_batch_sheets.php?action=search&Designation=". $tmpArr[0];
	$tmpArr = explode("&nbsp;",$_POST['ProductNumberExternal']);
	echo "&ProductNumberExternal=". $tmpArr[0];
	$tmpArr = explode("&nbsp;",$_POST['ProductNumberInternal']);
	echo "&ProductNumberInternal=". $tmpArr[0] . "&Keywords=". $_POST['Keywords'] . "'\n";
	echo "popup('reports/qc_form.php?bsn=" . $_POST['BatchSheetNumber'] . "',700,630)\n";
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
		width: 650,
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
		width: 650,
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
		width: 650,
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
		width: 650,
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
	params += ', menubar=yes';
	params += ', resizable=yes';
	params += ', scrollbars=yes';
	params += ', status=no';
	params += ', toolbar=no';
	newwin=window.open(url,'pop', params);
	if (window.focus) {newwin.focus()}
	return false;
}

function popup_print(url, width, height, left, top) {
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
	params += ', menubar=yes';
	params += ', resizable=yes';
	params += ', scrollbars=yes';
	params += ', status=no';
	params += ', toolbar=no';
	newwin=window.open(url,'pop', params);
	
	if (window.focus) {newwin.focus()}
	newwin.print();
	
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

<?php if ( $key_note ) {
	echo "<B STYLE='color:#990000'>" . $key_note . "</B><BR>";
} ?>

<?php 

//$quantity = CalculateBatchSheetQuantity(222, 88.2, .98, 'grams', 'lbs');
//echo $quantity;

?>


<?php if ( ($action == 'search' or $action != 'edit') and $bsn == '' ) { 
//BtachSheet start page?>

<table class="bounding">
<tr valign="top">
<td class="batch_sheet_batch_sheet_padded">
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
			<TD><B>Customer:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="customer" NAME="CustomerName" VALUE="<?php echo $CustomerID;?>" SIZE="20">
				<INPUT TYPE="hidden" ID="customer_id" NAME="CustomerID" VALUE="">
			</TD>
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
<hr style="margin:1em 0 1em 0" />
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
	} elseif ( isset($_REQUEST['CustomerID']) and $_REQUEST['CustomerID'] != "" ) {
		$clause .= " AND CustomerID = '". escape_data($_REQUEST['CustomerID']) . "'";
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

	$row_arr = batchsheet_info_search_npbsn($clause, $link);

	if ( $row_arr ) {
		$bg = 0; ?>

		<FORM ACTION="customers_batch_sheets.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="Designation" VALUE="<?php echo $Designation;?>">
		<INPUT TYPE="hidden" NAME="LotID" VALUE="<?php echo $LotID;?>">
		<INPUT TYPE="hidden" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>">
		<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>">
		<INPUT TYPE="hidden" NAME="Keywords" VALUE="<?php echo $Keywords;?>">

		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

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
				<TD COLSPAN=22><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
			</TR>

			<?php 

			foreach ( $row_arr as $row ) {
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
						$name = get_batchsheet_custname($row['BatchSheetNumber'],$link);
					?>

					<TD><NOBR><?php echo $name;?></NOBR></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php
					$sql = "SELECT DISTINCT bsci.CustomerPONumber ".
					" FROM batchsheetcustomerinfo AS bsci ".
					" LEFT JOIN customerorderdetail AS c ON c.CustomerOrderNumber = bsci.CustomerOrderNumber AND c.CustomerOrderSeqNumber = bsci.CustomerOrderSeqNumber AND bsci.BatchSheetNumber = " . $row['BatchSheetNumber'] .
					" LEFT JOIN customerordermaster ON c.CustomerOrderNumber = customerordermaster.OrderNumber ".
					" LEFT JOIN customers ON customers.customer_id = customerordermaster.CustomerID ".
					" WHERE bsci.BatchSheetNumber = " . $row['BatchSheetNumber'];
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
					<TD ALIGN=RIGHT><NOBR><?php echo number_format($row['NetWeight'], 2)."<samll>".$row['TotalQuantityUnitType'] . "</small>"; ?></NOBR></TD>
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
				<TD ALIGN="center" COLSPAN=25 style="white-space:nowrap;"><BR>
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
}  // end search page

?>

<?php if ( $bsn != "" ) {
	if ( isset($_REQUEST['reload_opener'] ) ) {
		echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
		echo "window.opener.location.reload()\n";
		echo "</SCRIPT>\n";
	}
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

	<TABLE CELLPADDING="2" CELLSPACING="0" BORDER="0" width="100%">
		<TR>
			<TD><B CLASS="black" STYLE="font-size:12pt">Batch sheet#:</B></TD>
			<TD><B CLASS="black" STYLE="font-size:12pt"><?php echo $bsn;?></B></TD>
			<TD><img src="images/spacer.gif" alt="spacer" width="15" border="0" height="1"></TD>
			<TD><B CLASS="black">abelei External#:</B></TD>
			<TD><?php echo $ProductNumberExternal;?></TD>
			<TD><img src="images/spacer.gif" alt="spacer" width="15" border="0" height="1"></TD>
			<TD><B CLASS="black">Description:</B></TD>
			<TD><?php echo $ProductDesignation;?></TD>
            <td>

	<?php
	
	$disable_cst_sec = 0;
	if ( $CommitedToInventory != 1 and $Manufactured != 1 ) {
		$color = "LightSalmon"; $status_display = "New";
	} elseif ( $CommitedToInventory == 1 and $Manufactured != 1 ) {
		$color = "GreenYellow"; $status_display = "Committed";
	} elseif ( $Manufactured == 1 and $QualityControlDate == '<I>None entered yet</I>') {
		$color = "AliceBlue"; $status_display = "Lots Assigned";
		$disable_cst_sec = 1;
	} elseif ( $QualityControlDate != '<I>None entered yet</I>' ) {
		$color = "Violet"; $status_display = "Complete";
	}
	echo "<div style=\"background-color:$color; border:solid 1px black; width:100%; font-size:14px; text-align:center; margin-bottom: 0.5em; padding:0.2em 0; font-weight:bold\">$status_display</div>";
	?>
	</td></TR>
	</TABLE>
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
					<?php if ( $_REQUEST['update'] != 1 and $disable_cst_sec != 1 ) { ?>
						<INPUT TYPE="button" CLASS="submit_normal" VALUE="Select Customer Order" 
						onClick="window.location='customers_customer_order_shipping.php?action=search<?php echo (0 < $customer_id) ? "&customer_id=".$customer_id : "" ?>&bsn=<?php echo $bsn;?><?php echo ("" != $customer) ? "&customer=".urlencode($customer) : "" ?><?php echo ("" != $ProductNumberExternal) ? "&pne=".urlencode($ProductNumberExternal) : "" ;?>'">
					<?php } ?>
					</TD>
				</TR>
				<TR>
					<TD><BR>
					<?php   //code breaked here
					$bg = 0; 
					if ($debug > 0)
						echo "Till now, I am still ok ln 1811 <br />";
					$row_arr = get_batchsheet_custinfo($bsn,$link,$debug);
					if ($debug > 0)
						echo "Till now, I am still ok ln 1812 <br />";
					if ( !empty($row_arr) ) {
						if ($debug > 0)
							echo "I may have probelm here ln 1813 <br />";
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
								<TD ALIGN=CENTER><B STYLE="font-size:8pt">Available<br />Pkgs</B></TD>
                              	<TD ALIGN=CENTER><B STYLE="font-size:8pt">Quantity<BR>ordered</B></TD>
								<TD ALIGN=CENTER><B STYLE="font-size:8pt">Pack<BR>size</B></TD>
								<TD ALIGN=CENTER><B STYLE="font-size:8pt">Total qty<BR>ordered</B></TD>
								<TD><B STYLE="font-size:8pt">Due date</B></TD>
							</TR>

						<?php
						foreach( $row_arr as $row ) {
							if ( $bg == 1 ) {
								$bgcolor = "#F3E7FD";
								$bg = 0;
							} else {
								$bgcolor = "whitesmoke";
								$bg = 1;
							}
							?>
							<TR BGCOLOR="<?php echo $bgcolor; ?>" VALIGN=TOP>
								<INPUT TYPE="hidden" NAME="update_po" VALUE="<?php echo $row['CustomerCodeNumber'];?>">
								<INPUT TYPE="hidden" NAME="CustomerOrderNumber" VALUE="<?php echo $row['CustomerOrderNumber'];?>">
								<INPUT TYPE="hidden" NAME="CustomerOrderSeqNumber" VALUE="<?php echo $row['CustomerOrderSeqNumber'];?>">

								<TD>
								<?php if ( $CommitedToInventory != 1 and $Manufactured != 1 and $_REQUEST['update'] != 1 ) { ?>
									<INPUT TYPE="button" VALUE="X" CLASS="submit" onClick="delete_po(<?php echo($row['BatchSheetNumber']);?>,<?php echo($row['CustomerOrderNumber']);?>,<?php echo($row['CustomerOrderSeqNumber']);?>)">
								<?php } ?>
								</TD>

								<TD>
								<?php if ( $_REQUEST['update'] != 1 and $Manufactured != 1 ) { ?>
									<INPUT TYPE="button" CLASS="submit" NAME="Edit" VALUE="Edit" <?php echo ($intermediary and !$FinalProductNotCreatedByAbelei) ? "disabled=\"disabled\"" : "onClick=\"popup('pop_select_customer_order_multi.php?action=edit&edit_po=1&bsn=$row[BatchSheetNumber]&CustomerOrderNumber=$row[CustomerOrderNumber]&CustomerOrderSeqNumber=$row[CustomerOrderSeqNumber]&ccn=$row[ccn]',700,830)\""; ?> >
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
									
								
										if ( "" != $row['PackIn'] ) {
										  $sub_sql = "SELECT ProductNumberInternal, Designation 
												FROM productmaster 
												WHERE ProductNumberInternal LIKE '6%'";
									      $sub_result = mysql_query($sub_sql, $link) or 
									    	die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
										  while ( $sub_row = mysql_fetch_array($sub_result) ) {
										   if ( $row['PackIn'] == $sub_row[ProductNumberInternal] ) {
											echo $sub_row[Designation];
											//get inventory amt of the package
										//	$sql = "SELECT ProductTotal(".$sub_row[ProductNumberInternal].",'C',NULL)";
                                            $sql = "SELECT LotID, SUM(inventorymovements.Quantity*inventorytransactiontypes.InventoryMultiplier) as qty ".
                                            " FROM inventorymovements ".
                                            " JOIN inventorytransactiontypes ".
                                            " ON (inventorytransactiontypes.TransactionID = inventorymovements.TransactionType) ".
                                            " WHERE inventorymovements.ProductNumberInternal=".$sub_row[ProductNumberInternal]. 
                                            " AND inventorymovements.MovementStatus='C' ".
                                            " AND LotID is not null and LotID <> '' group by LotID";
											$pkg_inv_result = mysql_query($sql,$link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
                                            $pkg_inv = 0;
                                            //$pkg_cmt = 0;
                                            while ($pkg_inv_row = mysql_fetch_array($pkg_inv_result)) {
										     if ( $pkg_inv_row['qty'] > 0 ) { 
                                                $pkg_inv += $pkg_inv_row['qty'];
                                             } 
 						     	            }
                                          echo "</TD>";
                                          echo "<TD STYLE='font-size:8pt' ALIGN=CENTER>". $row['NumberOfPackages'] ."</TD>";
                                          echo "<TD STYLE='font-size:8pt' ALIGN=CENTER>" . number_format($pkg_inv,0) ."</TD>";
                                       
                                          ?>
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
                                                                       
                                       <?php
                                       } //if find sub_row
                                       } // while sub row
                                       } elseif ( "" != $row['PackInID'] ) {
                                            $pkin_sql="SELECT * FROM bscustomerinfopackins where PackInID in (" .$row['PackInID'] . ")";
                                            $pkin_result=mysql_query($pkin_sql,$link) or die(mysql_error()."<br />Couldn't execute query: $pkin_sql<BR><BR>"); 
                                            $i = 0;
                                            while($row_pkin = mysql_fetch_array($pkin_result)) {
                                                $i++;
                                                $sub_sql = "SELECT ProductNumberInternal, Designation 
												FROM productmaster 
												WHERE ProductNumberInternal LIKE '6%'";
									            $sub_result = mysql_query($sub_sql, $link) or 
										          die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
                                             	if ( $i == 1 ) {  
                                                 while ( $sub_row = mysql_fetch_array($sub_result) ) {
                                             	  if ( $row_pkin['PackIn'] == $sub_row[ProductNumberInternal] ) {
                                             	    
											        echo $sub_row[Designation];
											//get inventory amt of the package
											 //       $sql = "SELECT ProductTotal(".$sub_row[ProductNumberInternal].",NULL,NULL)";
											 $sql = "SELECT LotID, SUM(inventorymovements.Quantity*inventorytransactiontypes.InventoryMultiplier) as qty ".
                                            " FROM inventorymovements ".
                                            " JOIN inventorytransactiontypes ".
                                            " ON (inventorytransactiontypes.TransactionID = inventorymovements.TransactionType) ".
                                            " WHERE inventorymovements.ProductNumberInternal=".$sub_row[ProductNumberInternal].
                                            " AND inventorymovements.MovementStatus='C'" .
                                            " AND LotID is not null AND LotID <> '' group by LotID";
											$pkg_inv_result = mysql_query($sql,$link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
                                            $pkg_inv = 0;
                                           // $pkg_cmt = 0;
                                            while ($pkg_inv_row = mysql_fetch_array($pkg_inv_result)) {
                                                if ( $pkg_inv_row['qty'] > 0 ) {
											         $pkg_inv += $pkg_inv_row['qty'];
                                                } 
 						     	            }
                                                    echo "</TD>";
                                                    echo "<TD STYLE='font-size:8pt' ALIGN=CENTER>". $row_pkin['NumberOfPackages'] ."</TD>";
                                                    echo "<TD STYLE='font-size:8pt' ALIGN=CENTER>" . number_format($pkg_inv,0) ."</TD>";
                                                  //  echo "<TD STYLE='font-size:8pt' ALIGN=CENTER>" . number_format($pkg_cmt,0) ."</TD>";
                                 					echo "<TD STYLE='font-size:8pt' ALIGN=CENTER><I>". number_format($row['Quantity'], 2)."</I></TD>";
								                    echo "<TD STYLE='font-size:8pt' ALIGN=CENTER><I>". number_format($row['PackSize'], 2) ."</I></TD>";
								                    echo "<TD STYLE='font-size:8pt' ALIGN=CENTER><I>". number_format($row['TotalQuantityOrdered'], 2)."</I></TD>";
            								        echo "<TD STYLE='font-size:8pt'>"; 
								                    if ( $row['RequestedDeliveryDate'] != '' ) {
									                   echo date("n/j/Y", strtotime($row['RequestedDeliveryDate']));
								                    } else {
									                   echo "<I>None entered</I>";
								                    }
								                    ?></TD>
							                        </TR>
                                                    <?php
        
										          } //if find sub_row pni
                                             	
                                                } //while sub_row
                                            } //$i ==1
                                            else { //$i > 1
                                                   //  echo "<br />row_pkin PackIn =". $row_pkin['PackIn'] ." <br /> i= ". $i;
                                                   if ( $bgcolor == "#F3E7FD")
                                                    $bgcolor = "whitesmoke";
                                                   else
                                                     $bgcolor = "#F3E7FD";
                                                   echo "<TR BGCOLOR='". $bgcolor ."' VALIGN=TOP><TD colspan='5'>&nbsp;</td><TD>";
                                                   while ( $sub_row = mysql_fetch_array($sub_result) ) {
                                             	      if ( $row_pkin['PackIn'] == $sub_row['ProductNumberInternal'] ) {
                                             	    
											           echo $sub_row[Designation];
											//get inventory amt of the package
											          // $sql = "SELECT ProductTotal(".$sub_row[ProductNumberInternal].",NULL,NULL)";
											          $sql = "SELECT LotID, SUM(inventorymovements.Quantity*inventorytransactiontypes.InventoryMultiplier) as qty ".
                                            " FROM inventorymovements ".
                                            " JOIN inventorytransactiontypes ".
                                            " ON (inventorytransactiontypes.TransactionID = inventorymovements.TransactionType) ".
                                            " WHERE inventorymovements.ProductNumberInternal=".$sub_row['ProductNumberInternal'].
                                            " AND LotID is not NULL and LotID <> '' group by LotID";
											$pkg_inv_result = mysql_query($sql,$link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
                                            $pkg_inv = 0;
                                            //$pkg_cmt = 0;
                                            while ($pkg_inv_row = mysql_fetch_array($pkg_inv_result)) {
											// if ( $pkg_inv_row['LotID'] == "") {
											//     $pkg_cmt=$pkg_inv_row['qty'];
											// }
                                            // else
                                            if ( $pkg_inv_row['qty'] > 0 ) {
                                                $pkg_inv += $pkg_inv_row['qty'];
                                             } else { //lot empty
                                                $sql_emptylot = "UPDATE lots SET StorageLocation = 'NULL' WHERE ID='" . $pkg_inv_row['LotID']."'";
                                                mysql_query($sql_emptylot,$link) or die (mysql_error()."Failed execute SQL $sql_emptylot <br />");
                                             }   
						     	            }
                                                       echo "</TD>";
                                                       echo "<TD STYLE='font-size:8pt' ALIGN=CENTER>". $row_pkin['NumberOfPackages'] ."</TD>";
                                 					   echo "<TD STYLE='font-size:8pt' ALIGN=CENTER>" . number_format($pkg_inv,0) ."</TD>";
                                                     //  echo "<TD STYLE='font-size:8pt' ALIGN=CENTER>" . number_format($pkg_cmt,0) ."</TD>";
        
										              } //if find sub_row pni
                                             	
                                                     } //while sub_row
                                                       echo "<TD colspan='4'>&nbsp;</TD>";
								                       echo "</TR>";
                                                        
                                            } //$i > 1
                                            
                                        } // while row_pkin
										//echo "<OPTION VALUE='$sub_row[ProductNumberInternal]' ".($row[PackIn] == $sub_row[ProductNumberInternal] ? "SELECTED":"").">$sub_row[Designation]</OPTION>";
									
                        } // packinid != "" ?>
	
					
					<?php
					  } // foreach row
                      echo "</TABLE>";
                    } // find pkg 
                    else {
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
				<SELECT NAME="TotalQuantityUnitType" id="TotalQuantityUnitType" STYLE="font-size: 7pt" <?php echo $form_status;?>>
				<?php if ( $TotalQuantityUnitType == "grams" ) { ?>
					<OPTION VALUE=""></OPTION>
					<option value="N/A">N/A</option>
					<option value="lbs">lbs</option>
					<OPTION VALUE="grams" selected>grams</OPTION>
					<OPTION VALUE="kg">kg</OPTION>
				<?php } elseif ( $TotalQuantityUnitType == "lbs" ) { ?>
					<OPTION VALUE=""></OPTION>
					<option value="N/A">N/A</option>
					<option value="lbs" selected>lbs</option>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="kg">kg</OPTION>
				<?php } elseif ( $TotalQuantityUnitType == "kg" ) { ?>
					<OPTION VALUE=""></OPTION>
					<option value="N/A">N/A</option>
					<option value="lbs">lbs</option>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="kg" selected>kg</OPTION>
				<?php } else { ?>
					<OPTION VALUE=""></OPTION>
					<option value="N/A">N/A</option>
					<option value="lbs">lbs</option>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="kg">kg</OPTION>
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
				<SELECT NAME="Column1UnitType" id="Column1UnitType" STYLE="font-size: 7pt" <?php echo $form_status;?>>
				<?php if ( $Column1UnitType == "grams" ) { ?>
					<OPTION VALUE=""></OPTION>
					<option value="N/A">N/A</option>
					<option value="lbs">lbs</option>
					<OPTION VALUE="grams" selected>grams</OPTION>
					<OPTION VALUE="kg">kg</OPTION>				
				<?php } elseif ( $Column1UnitType == "lbs" ) { ?>
					<OPTION VALUE=""></OPTION>
					<option value="N/A">N/A</option>
					<option value="lbs" selected>lbs</option>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="kg">kg</OPTION>				
				<?php } elseif ( $Column1UnitType == "kg" ) { ?>
					<OPTION VALUE=""></OPTION>
					<option value="N/A">N/A</option>
					<option value="lbs">lbs</option>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="kg" selected>kg</OPTION>
				<?php } else { ?>
					<OPTION VALUE=""></OPTION>
					<option value="N/A">N/A</option>
					<option value="lbs">lbs</option>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="kg">kg</OPTION>												
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
				<SELECT NAME="Column2UnitType" id="Column2UnitType" STYLE="font-size: 7pt" <?php echo $form_status;?>>
				<?php if ( $Column2UnitType == "grams" ) { ?>
					<OPTION VALUE=""></OPTION>
					<option value="N/A">N/A</option>
					<option value="lbs">lbs</option>
					<OPTION VALUE="grams" selected>grams</OPTION>
					<OPTION VALUE="kg">kg</OPTION>				
				<?php } elseif ( $Column2UnitType == "lbs" ) { ?>
					<OPTION VALUE=""></OPTION>
					<option value="N/A">N/A</option>
					<option value="lbs" selected>lbs</option>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="kg">kg</OPTION>				
				<?php } elseif ( $Column2UnitType == "kg" ) { ?>
					<OPTION VALUE=""></OPTION>
					<option value="N/A">N/A</option>
					<option value="lbs">lbs</option>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="kg" selected>kg</OPTION>				
				<?php } else { ?>
					<OPTION VALUE=""></OPTION>
					<option value="N/A">N/A</option>
					<option value="lbs">lbs</option>
					<OPTION VALUE="grams">grams</OPTION>
					<OPTION VALUE="kg">kg</OPTION>				
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
			$sub_sql="SELECT ItemDescription FROM tblsystemdefaultsdetail WHERE ItemID=22";
			$sub_result = mysql_query($sub_sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sub_sql<BR><BR>");
			while ( $sub_row = mysql_fetch_array($sub_result) ) {
					echo "<OPTION VALUE='".$sub_row[ItemDescription]."' ".(($Vessel == $sub_row[ItemDescription]) ? "SELECTED":"").">$sub_row[ItemDescription]</OPTION>";
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
			$sql = "SELECT * FROM users WHERE (user_type in (3,5) or user_id = 36 ) AND active = 1 ORDER BY last_name";
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
			<TD><TEXTAREA NAME="InventoryMovementRemarks" cols="50" rows="2" <?php echo $form_status;?>><?php echo $InventoryMovementRemarks;?></TEXTAREA></TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B <?php echo (1 != $Manufactured ? "style=\"color:gray\"":"")?>>Abelei Lot#:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD>
				<?php if ( 1 == $Manufactured ) { ?>
				<INPUT TYPE="text" NAME="abeleiLotNumber" VALUE="<?php echo $abeleiLotNumber;?>" SIZE="30">
				<INPUT TYPE="hidden" NAME="abeleiLotNumber_save" VALUE="<?php echo $abeleiLotNumber;?>">
				<?php } ?>
			</TD>
		</TR>

		<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="2"></TD></TR>

		<TR VALIGN=TOP>
			<TD><B <?php echo (1 != $Manufactured ? "style=\"color:gray\"":"")?>>Seq#:</B></TD>
			<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
			<TD>
			<?php if ( 1 == $Manufactured ) { ?>
			<INPUT TYPE="text" NAME="LotSequenceNumber" VALUE="<?php echo $LotSequenceNumber;?>" SIZE="20"></TD>
			<INPUT TYPE="hidden" NAME="LotSequenceNumber_save" VALUE="<?php echo $LotSequenceNumber;?>" ></TD>
			<?php } ?>
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

	<TABLE class="batch_sheet">
			
		<TR VALIGN="top">
			<!-- <TD COLSPAN=2>&nbsp;</TD> -->
			<TD class="batch_sheet_padded" ALIGN=RIGHT><B>Sq#</B></TD>
			<TD class="batch_sheet_padded" ALIGN=RIGHT bgcolor="white"><B>Internal#</B></TD>
			<TD class="batch_sheet_padded" ><B>Natural or Artificial</B></TD>
			<TD class="batch_sheet_padded" style="width:500px" bgcolor="white"><B>Ingredient</B></TD>
			<TD class="batch_sheet_padded" ALIGN=RIGHT><B>%age</B></TD>
			<td class="batch_sheet_padded" ALIGN=RIGHT bgcolor="white"><b>Batch Amt. <?php echo $Column1UnitType; ?></b></td>
			<td class="batch_sheet_padded" ALIGN=RIGHT><b>Batch Amt. <?php echo $Column2UnitType; ?></b></td>
			<?php echo (1 == $Manufactured) ? "" :"<td class='batch_sheet_padded' bgcolor='white'><b>Enough In Inv</b></td>"?>
			<td class="batch_sheet_padded" ALIGN=RIGHT><b>Inv Curr Amt. <?php echo $TotalQuantityUnitType; ?></b></td>
			<td class="batch_sheet_padded" ALIGN=RIGHT bgcolor="white"><b>Amt. On Order <?php echo $TotalQuantityUnitType; ?></b></td>
			<td class="batch_sheet_padded" ALIGN=RIGHT><b>Amt. Committed <?php echo $TotalQuantityUnitType; ?></b></td>
			<TD class="batch_sheet_padded" bgcolor="white"><B>Vendor</B></TD>
			<TD class="batch_sheet_padded" ><B><NOBR>Raw Material<BR>Lot Number</NOBR></B></TD>
			<TD class="batch_sheet_padded" ALIGN=RIGHT bgcolor="white"><B>Quantity (<?php echo $Column1UnitType; ?>)</B></TD>
			<TD class="batch_sheet_padded" ALIGN=RIGHT><B>Quantity (<?php echo $Column2UnitType; ?>)</B></TD>
			<!-- <TD>&nbsp;</TD> -->
		</TR>

	<?php

	// ARRAY OF INGREDIENTS TO BE USED BELOW TO CHECK WHETHER INVENTORY COMMITTMENT CAN BE MADE
	$ingredients = '';
	$insufficient_inventory = false;
		
	$row_arr = get_batchsheet_detail($bsn,$link,$debug);
	if ($debug > 0)
		echo "I am ok at ln 2335";
	
	if ( ! empty($row_arr) ) {
		$total = 0;
		$i = 0;
		$c = 0;
		$bg = 1;
		foreach ( $row_arr as $row ) {
			$c++;
			if ( $bg == 0 ) {
				$r_bgcolor = "";
				$bg = 1;
			} else {
				$r_bgcolor="bgcolor='whitesmoke'";
				$bg = 0;
			}
			if ( substr($row['IngredientProductNumber'], 0, 1) != 4 ) {
				$ingredients[$i] = $row['IngredientProductNumber'];
			}
			
			$description = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
// move ingredient query here
			if ( substr($row['IngredientProductNumber'], 0, 1) != 4 ) {
					
				$sql = "Select DISTINCT ProductTotal(inventorymovements.ProductNumberInternal,'C',NULL) as total, ".
					"COALESCE((".
					"SELECT SUM(QuantityConvert( (TotalQuantityExpected), UnitOfMeasure, 'grams')) ".
					"FROM purchaseorderdetail JOIN purchaseordermaster USING (PurchaseOrderNumber)
					WHERE ProductNumberInternal = productmaster.ProductNumberInternal
					AND ( purchaseordermaster.PurchaseOrderType<> 'Process' or purchaseordermaster.PurchaseOrderType is null) ".
					" AND (`Status` = 'O' OR `Status` = 'P') ),0) as ordered, ".
					"ProductTotal(inventorymovements.ProductNumberInternal,'P',NULL) as committed, ".
					"ProductTotal(inventorymovements.ProductNumberInternal,NULL, NULL) as net, ".
					"productmaster.*, externalproductnumberreference.ProductNumberExternal as external ".
					"FROM productmaster ".
					"LEFT JOIN inventorymovements ON (inventorymovements.ProductNumberInternal = productmaster.ProductNumberInternal) ".
					"LEFT JOIN receipts ON ( receipts.LotID = inventorymovements.LotID ) ".
					"LEFT JOIN lots ON (inventorymovements.LotID = lots.ID) ".
					"LEFT JOIN externalproductnumberreference ON (externalproductnumberreference.ProductNumberInternal = productmaster.ProductNumberInternal) ".
					"LEFT JOIN vwmaterialpricing ON (vwmaterialpricing.ProductNumberInternal = productmaster.ProductNumberInternal)
					WHERE productmaster.ProductNumberInternal=" . $row['IngredientProductNumber'];

					$result_vend = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
					// echo $sql . "<BR>";
					if ( mysql_num_rows($result_vend) > 0 ) {
						$row_inv = mysql_fetch_array($result_vend);
						$YorN="Y";
						$InvG = $row_inv['total'];
						$AmtOrdG=$row_inv['ordered'];
						$AmtComG=$row_inv['committed'];
						if( empty($TotalQuantityUnitType) ) {
							$InvLbs=$InvG;
							$AmtOrdLbs=$AmtOrdG;
							$AmtComLbs=$AmtComG;
							$BatchAmtG = $gross_weight*($row['Percentage']*.01);
							$BatchAmtClmn1 = $BatchAmtG;
							$BatchAmtClmn2 = $BatchAmtG;
						} else {
							$InvLbs=QuantityConvert($InvG, "grams", $TotalQuantityUnitType);
							$AmtOrdLbs=QuantityConvert($AmtOrdG, "grams", $TotalQuantityUnitType);
							$AmtComLbs=QuantityConvert($AmtComG, "grams", $TotalQuantityUnitType);
							$BatchAmtG=$gross_weight*($row['Percentage']*.01);
							$BatchAmtClmn1 = QuantityConvert($BatchAmtG,$TotalQuantityUnitType,$Column1UnitType);
							$BatchAmtClmn2 = QuantityConvert($BatchAmtG,$TotalQuantityUnitType,$Column2UnitType);
						}
						if ( "10829"!=substr($row['IngredientProductNumber'],0,5) AND '6'!=substr($row['IngredientProductNumber'],0,1) 
								AND '4'!=substr($row['IngredientProductNumber'],0,1)) // make exception for water (10829?) and instructions (Vessel)
						{
							if ( (0 < $BatchAmtG ) AND ( $NumberOfTimesToMake > 0 ) ) {
								if ( $CommitedToInventory != 1 and $Manufactured != 1 ) { //new bs
									$chk_InvG = $InvG + $AmtComG;
								} else {
									$chk_InvG = $InvG;
								}
						//		echo $row['IngredientProductNumber'] ." Chkinv=". $chk_InvG . " BatchAmtG = ". $BatchAmtG ."<br />";
								$percentage = 1;
								if ( $TotalQuantityUnitType == 'lbs' ) {
									$percentage = 0.0022;
								} elseif ( $TotalQuantityUnitType == 'kg' ) {
									$percentage = 0.001;
								}
								
								$sql = "SELECT Quantity FROM inventorymovements WHERE Transactionnumber in 
										(SELECT InventoryMovementTransactionNumber FROM batchsheetdetaillotnumbers WHERE BatchSheetNumber='".$bsn."' AND IngredientProductNumber='".$row['IngredientProductNumber']."')";
								$result_assgned_lot=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
								$assigned_amount=0;
								while ( $row_assigned_lot = mysql_fetch_array($result_assgned_lot) ) {
										$assigned_amount += $row_assigned_lot['Quantity'];
								}
								if ( $assigned_amount > 0 and $CommitedToInventory == 1 and $Manufactured != 1 ) {
									$_SESSION['note'] = "WARNING: The Batch Sheet ".$bsn." in the lot assigning process, Click on 'Assign Lot Numbers' button to Complete or Cancel the process ";  
									if ( $note=="" )
										echo "<script>window.location.reload();</script>";
								}
								
								if ( ( ($chk_InvG - QuantityConvert($BatchAmtG,$TotalQuantityUnitType,"grams")*$NumberOfTimesToMake ) * $percentage ) < -0.01 or  $InvG <= 0.0) {
									//Check if the inventory already assigned to this batchsheet

									if ( ( ($chk_InvG + $assigned_amount - QuantityConvert($BatchAmtG,$TotalQuantityUnitType,"grams")*$NumberOfTimesToMake ) * $percentage ) < -0.01 or  $InvG <= 0.0 ) {
										$YorN = "N";
										$insufficient_inventory = true;
									}
								}
							}
						}
						
            	}	
			}
//end

			if (2==substr($row['IngredientProductNumber'],0,1)) {
				if ( empty($row['SubBatchSheetNumber']) ) {
				$sql_external = "SELECT ProductNumberExternal FROM externalproductnumberreference WHERE ProductNumberInternal = '$row[IngredientProductNumber]'";
				$result_external = mysql_query($sql_external, $link) or die (mysql_error()."<br />Couldn't execute query: $sql_external<BR><BR>");
				$row_external = mysql_fetch_array($result_external);
				$external = $row_external[0];
				$description="<i><b>".$external."</b> ".$row[Designation] ."</i>\n" .
				"<form id=\"add_key\" name=\"add_key\" action=\"customers_batch_sheets.php\" method=\"post\" target=\"_blank\">\n".
				"<input type=\"hidden\" id=\"external_number\" name=\"external_number\" value=\"". $external ."\">\n".
				"<input type=\"hidden\" id=\"NetWeight\" name=\"NetWeight\" value=\"". $BatchAmtG*$NumberOfTimesToMake ."\">\n".
				"<input type=\"hidden\" id=\"TotalQuantityUnitType\" name=\"TotalQuantityUnitType\" value=\"".$TotalQuantityUnitType ."\">\n".
				"<input type=\"hidden\" id=\"Column1UnitType\" name=\"Column1UnitType\" value=\"". $Column1UnitType ."\">\n".
				"<input type=\"hidden\" id=\"Column2UnitType\" name=\"Column2UnitType\" value=\"".$Column2UnitType."\">\n".
				"<input type=\"hidden\" id=\"Yield\" name=\"Yield\" value=\"".$Yield."\">\n".
				"<input type=\"hidden\" id=\"NumberOfTimesToMake\" name=\"NumberOfTimesToMake\" value=\"1\">\n".
				"<input type=\"hidden\" id=\"BatchSheetNumber\" name=\"BatchSheetNumber\" value=\"".$bsn."\">\n".
                "<input type=\"hidden\" id=\"IngredientSEQ\" name=\"IngredientSEQ\" value=\"".$row['IngredientSEQ']."\">\n".
				"<input type=\"submit\" class=\"submit new\" name=\"new_sheet_key\" id=\"new_sheet_key\" value=\"New Batch Sheet For Key\">\n".
				"</form>\n";
				} else {
					$description="<i><b>".$external."</b> ".$row[Designation] ."</i><br /><br />
					<div style=\"background-color:#99FF00\">BatchSheet#: 
					<a href=\"customers_batch_sheets.php?bsn=".$row['SubBatchSheetNumber']."\" target='_blank'><b>".
					$row['SubBatchSheetNumber']."</b></a></div>\n";
				}
			}

			if ( substr($row['IngredientProductNumber'], 0, 1) == 4 ) {
				$bgcolor = " align='left' BGCOLOR='#666666'";
      			$cols = (1 == $manufactured ? 11 : 12);
				$colspan = "COLSPAN=$cols";
				$ingredient_string = "<B CLASS='white'>" . $description . "</B>";
			} else {
				$bgcolor = "";
				$colspan = "";
				$ingredient_string = $description;
			}

			?>

			<TR VALIGN=TOP <?php echo $r_bgcolor;?>>
			<!-- <FORM ACTION="#" METHOD="post"> -->
				<TD class="batch_sheet_padded" ALIGN=RIGHT><?php echo number_format($row['IngredientSEQ'],0);?></TD>
				<TD class="batch_sheet_padded" ALIGN=RIGHT bgcolor="white"><?php echo $row['IngredientProductNumber'];?></TD>
				<TD class="batch_sheet_padded"><?php echo $row['Natural_OR_Artificial'];?></TD>
				<TD class="batch_sheet_padded" <?php echo ( $bgcolor == "" ) ? "bgcolor='white'" : $bgcolor ;?> <?php echo $colspan;?>><?php echo $ingredient_string;?></TD>

				<?php if ( substr($row['IngredientProductNumber'], 0, 1) != 4 ) { ?>

					<TD class="batch_sheet_padded" ALIGN=RIGHT><?php echo number_format($row['Percentage'], 2);?></TD>

					<?php
					if ( mysql_num_rows($result_vend) > 0 ) {
			echo "<td class='batch_sheet_padded' ALIGN=RIGHT bgcolor='white'><NOBR>".number_format($BatchAmtClmn1,2)."<small>".$Column1UnitType."</small></NOBR></td>";
			echo "<td class='batch_sheet_padded' ALIGN=RIGHT ><NOBR>".number_format($BatchAmtClmn2,2)."<small>".$Column2UnitType."</small></NOBR></td>";
            if (1 != $Manufactured) {
              $style = ('N'==$YorN ? 'style="background:red;color:white"' : '');
              echo "<td $style class='batch_sheet_padded' ALIGN=RIGHT bgcolor='white'>$YorN</td>";
            }
            echo "<td class='batch_sheet_padded' ALIGN=RIGHT><NOBR>".number_format($InvLbs,2)."<small>".$TotalQuantityUnitType.
            "</small></NOBR></td><td class='batch_sheet_padded' ALIGN=RIGHT bgcolor='white'><NOBR>".number_format($AmtOrdLbs,2).
            "<small>".$TotalQuantityUnitType."</small></NOBR></td><td class='batch_sheet_padded' ALIGN=RIGHT><NOBR>".
            number_format($AmtComLbs,2)."<small>".$TotalQuantityUnitType."</small></NOBR></td>";
					} else 
					{ 
						echo "<td class='batch_sheet_padded' >N</td>";
						echo "<td class='batch_sheet_padded' ><strong>NO INFO</strong></td>"; 
					}
					//--------//
					$sql = "SELECT batchsheetdetaillotnumbers.IngredientProductNumber, vendors.name, lots.LotNumber AS ID, QuantityUsedFromThisLot ".
					" FROM batchsheetdetaillotnumbers ".
					" LEFT JOIN lots ON batchsheetdetaillotnumbers.LotID = lots.ID ".
					" LEFT JOIN vendors ON lots.VendorId = vendors.vendor_id ".
					" WHERE BatchSheetNumber = ". $bsn .
					" AND batchsheetdetaillotnumbers.IngredientProductNumber = ". $row['IngredientProductNumber'] .
					" AND batchsheetdetaillotnumbers.IngredientSEQ = ". $row['IngredientSEQ'] .
					" ORDER BY QuantityUsedFromThisLot";
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
							$vendr_cl1 = (empty($Column1UnitType)) ? $row_vend['QuantityUsedFromThisLot'] : QuantityConvert($row_vend['QuantityUsedFromThisLot'],'grams',$Column1UnitType);
							$vendr_cl2 = QuantityConvert($vendr_cl1,$Column1UnitType,$Column2UnitType);
							$vendr_cl1 = number_format($vendr_cl1,2);
							$vendr_cl2 = number_format($vendr_cl2,2);
							if ( $current_id != $row_vend[IngredientProductNumber] ) {
								echo "<TD class='batch_sheet_padded' bgcolor='white'>$vendor_name</TD>";
								echo "<TD class='batch_sheet_padded' >$row_vend[ID]</TD>";
								echo "<TD class='batch_sheet_padded' ALIGN=RIGHT bgcolor='white'><NOBR>" . $vendr_cl1 . "<small>" . $Column1UnitType ."</small></NOBR></TD>";
								echo "<TD class='batch_sheet_padded' ALIGN=RIGHT><NOBR>" . $vendr_cl2 . "<small>" . $Column2UnitType . "</small></NOBR></TD>";
							} else {
								echo "</TR><TR VALIGN=TOP>";
								echo "<TD COLSPAN=10>&nbsp;</TD>";
								echo "<TD class='batch_sheet_padded' bgcolor='white'>$vendor_name</TD>";
								echo "<TD class='batch_sheet_padded'>$row_vend[ID]</TD>";
								echo "<TD class='batch_sheet_padded' ALIGN=RIGHT bgcolor='white'><NOBR>" . $vendr_cl1 . "<small>" . $Column1UnitType ."</small></NOBR></TD>";
								echo "<TD class='batch_sheet_padded' ALIGN=RIGHT><NOBR>" . $vendr_cl2 . "<small>" . $Column2UnitType . "</small></NOBR></TD>";
								echo "</TR>";
							}
							$current_id = $row_vend['IngredientProductNumber'];
						}
					} else {
						echo "<TD class='batch_sheet_padded' bgcolor='white'>$row[name]</TD>";
						echo "<td class='batch_sheet_padded' COLSPAN=3>&nbsp;</TD>";
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
	if ( $c > 0) {
		// CHECK TO SEE WHETHER INVENTORY COMMITMENT CAN BE MADE

		$is_intermediary = false;
		$sql = "SELECT Intermediary FROM externalproductnumberreference INNER JOIN productmaster USING (ProductNumberInternal) WHERE ProductNumberExternal = '" . $ProductNumberExternal . "' AND Intermediary = 0";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		if ( mysql_num_rows($result) == 0 ) {
			$is_intermediary = true;
		}

		$is_contract_packing = false;
		$sql = "SELECT Count(*) AS count FROM batchsheetcustomerinfo WHERE BatchSheetNumber = " . $bsn . " AND PackIn = 600012";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$row = mysql_fetch_array($result);
		if ( $row['count'] > 0 ) {
			$is_contract_packing = true;
		}


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
					<INPUT TYPE="button" CLASS="submit_normal" NAME="Print" VALUE="Print" onClick="popup_print('reports/production_batch_sheet_excel.php?bsn=<?php echo $bsn;?>',1024,700)">
					<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1">
					<INPUT TYPE="button" CLASS="submit_normal" NAME="Delete" VALUE="Delete" <?php if ( 0 == $CommitedToInventory and 0 == $Manufactured and !(empty($LotID))) { 
						echo "onClick=\"JavaScript:delete_batch($bsn,$LotID)\""; 
						}
						else { echo "disabled=\"disabled\" title=\"To delete a batch it must not be Committed nor Manufactured with LotID exsist.\""; } ?> >
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
						<INPUT TYPE="button" CLASS="submit_normal" NAME="Assign" VALUE="Assign Lot Numbers" onClick="popup('pop_select_lots_for_batch_sheet_new.php?bsn=<?php echo $bsn;?>&pni=<?php echo $ProductNumberInternal;?>', 800, 840)">
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
					<INPUT TYPE="button" VALUE="QC Input Form" onClick="popup('pop_qc_input_form.php?bsn=<?php echo $bsn;?>',700,630)" CLASS="submit_normal">
					<IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1">
					<INPUT TYPE="button" VALUE="QC Report" onClick="popup('reports/qc_form.php?bsn=<?php echo $bsn;?>',700,630)" CLASS="submit_normal">
					</TD>
				</TR>
		</TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE></FORM><BR>


<?php 
	} // if ($c>0)
	} ?>

<BR><BR>



<?php include("inc_footer.php"); ?>