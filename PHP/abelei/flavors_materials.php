<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 4 and $rights != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');
include('search/system_defaults.php');


if ( $_REQUEST['action'] != 'edit' and isset($_SESSION['internal_number']) ) {
	unset($_SESSION['internal_number']);
}


$pne="";

$edit=false;
if ( isset($_SESSION['external_number']) ) {
	$pne = $_SESSION['external_number'];
	$edit = true;
}

$pni='';
if (isset($_REQUEST['ProductNumberInternal']) ) {
	$pni = $_REQUEST['ProductNumberInternal'];
} elseif (isset($_SESSION['internal_number'])) {
	$pni = $_SESSION['internal_number'];
}

$action="";
if (isset($_REQUEST['action']) )
{
	$action=$_REQUEST['action'];
}
if ( $action == 'edit' ) 
{
	$edit = true;
}

$external_number_search='';
if (isset($_REQUEST['external_number_search'])) {
	$external_number_search=$_REQUEST['external_number_search'];
	$_SESSION[external_number_search] = $external_number_search;
}
else if (isset($_SESSION[external_number_search])) {
	$external_number_search=$_SESSION[external_number_search];
}

$designation_search='';
if (isset($_REQUEST['designation_search'])) {
	$designation_search=$_REQUEST['designation_search'];
	$_SESSION[designation_search] = $designation_search;
}
else if (isset($_SESSION[designation_search])) {
	$designation_search=$_SESSION[designation_search];
}

$internal_number_search='';
if (isset($_REQUEST['internal_number_search'])) {
	$internal_number_search=$_REQUEST['internal_number_search'];
	$_SESSION[internal_number_search] = $internal_number_search;
}
else if (isset($_SESSION[internal_number_search])) {
	$internal_number_search=$_SESSION[internal_number_search];
}
$keyword_search='';
if (isset($_REQUEST['keyword_search'])) {
	$keyword_search=$_REQUEST['keyword_search'];
	$_SESSION[keyword_search] = $keyword_search;
}
else if (isset($_SESSION[keyword_search])) {
	$keyword_search=$_SESSION[keyword_search];
}

$parity=0;
$parity += ($designation_search != '' ? 1 : 0) + ($external_number_search != '' ? 1 : 0) + ($internal_number_search  != '' ? 1 : 0) + ($keyword_search != '' ? 1 : 0);
if (1 < $parity)
{
	$error_found="Cannot search on more than one field at the same time!";
}

if ( !empty($_POST) and $_POST['ProductNumberInternal'] != '' and $action=="save" ) { // MAIN FORM

//foreach (array_keys($_POST) as $key) { 
//	$$key = $_POST[$key]; 
//	print "$key is ${$key}<br />"; 
//}
//die();

	$ProductNumberInternal = $_POST['ProductNumberInternal'];
	$AllergenEgg = $_POST['AllergenEgg'];
	$AllergenMilk = $_POST['AllergenMilk'];
	$AllergenPeanut = $_POST['AllergenPeanut'];
	$AllergenSeafood = $_POST['AllergenSeafood'];
	$AllergenSeed = $_POST['AllergenSeed'];
	$AllergenSoybean = $_POST['AllergenSoybean'];
	$AllergenSulfites = $_POST['AllergenSulfites'];
	$AllergenTreeNuts = $_POST['AllergenTreeNuts'];
	$AllergenWheat = $_POST['AllergenWheat'];
	$AllergenYellow = $_POST['AllergenYellow'];
	$Appearance = $_POST['Appearance'];
	$Ash = $_POST['Ash'];
	$BatchSize = $_POST['BatchSize'];
	$BatchSizeKg = $_POST['BatchSizeKg'];
	$Biotin = $_POST['Biotin'];
	$BoilingPoint = $_POST['BoilingPoint'];
	$Calcium = $_POST['Calcium'];
	$Calories = $_POST['Calories'];
	$CaloriesFromFat = $_POST['CaloriesFromFat'];
	$Cholesterol = $_POST['Cholesterol'];
	$Copper = $_POST['Copper'];
	$CurrentSellingItem = $_POST['CurrentSellingItem'];
	$Designation = $_POST['Designation'];
	$DietaryFiber = $_POST['DietaryFiber'];
	$DeveloperID = $_POST['DeveloperID'];
	$EmergencyFirstAidProcedure = $_POST['EmergencyFirstAidProcedure'];
	$EvaporationRate = $_POST['EvaporationRate'];
	$ExtinguishingMedia = $_POST['ExtinguishingMedia'];
	$FatCalories = $_POST['FatCalories'];
	$FEMA_NBR = $_POST['FEMA_NBR'];
	$FinalProductNotCreatedByAbelei = $_POST['FinalProductNotCreatedByAbelei'];
	$FlammableLimits = $_POST['FlammableLimits'];
	$Flashpoint = $_POST['Flashpoint'];
	$FlavorAndAroma = $_POST['FlavorAndAroma'];
	$Folate = $_POST['Folate'];
	$FolateFolacinFolicAdic = $_POST['FolateFolacinFolicAdic'];
	$GeneralDescriptionOfFormulation = $_POST['GeneralDescriptionOfFormulation'];
	$GMO = $_POST['GMO'];
	$Halal = $_POST['Halal'];
	$Hazard = $_POST['Hazard'];
	$HazardousComponents = $_POST['HazardousComponents'];
	$HazardousDecomposition = $_POST['HazardousDecomposition'];
	$HazardousPolymerization = $_POST['HazardousPolymerization'];
	$HazardousPolymerizationConditions = $_POST['HazardousPolymerizationConditions'];
	$HealthHazards = $_POST['HealthHazards'];
	$Incompatibility = $_POST['Incompatibility'];
	$InsolubleFiber = $_POST['InsolubleFiber'];
	$Intermediary = $_POST['Intermediary'];
	$inventory_units = isset($_REQUEST['inventory_units']) && ("" != $_REQUEST['inventory_units']) ? escape_data($_REQUEST['inventory_units']) : "" ;
	$Iodine = $_POST['Iodine'];
	$Iron = $_POST['Iron'];
	$Keywords = $_POST['Keywords'];
	$Kosher = $_POST['Kosher'];
	$KosherStatus = $_POST['KosherStatus'];
	$LabelDeclaration = $_POST['LabelDeclaration'];
	$Lactose = $_POST['Lactose'];
	$LEL = $_POST['LEL'];
	$Magnesium = $_POST['Magnesium'];
	$Manganese = $_POST['Manganese'];
	$ManufacturingInstructions = $_POST['ManufacturingInstructions'];
	$MedicalCondition = $_POST['MedicalCondition'];
	$MeltingPoint = $_POST['MeltingPoint'];
	$MonounsaturatedFat = $_POST['MonounsaturatedFat'];
	$MostRecentVendorID = $_POST['MostRecentVendorID'];
	$MostRecentVendorName = $_POST['MostRecentVendorName'];
	$Natural_OR_Artificial = $_POST['Natural_OR_Artificial'];
	$Niacin = $_POST['Niacin'];
	$NonFlavorIngredients = $_POST['NonFlavorIngredients'];
	$NoteForFormulation = $_POST['NoteForFormulation'];
	$OldDescriptionDelete = $_POST['OldDescriptionDelete'];
	$Organic = $_POST['Organic'];
	$OtherCarbohydrates = $_POST['OtherCarbohydrates'];
	$OtherProtectiveClothing = $_POST['OtherProtectiveClothing'];
	$Packaging = $_POST['Packaging'];
	$PantothenicAcid = $_POST['PantothenicAcid'];
	$Phosphorus = $_POST['Phosphorus'];
	$PolyunsaturatedFat = $_POST['PolyunsaturatedFat'];
	$Potassium = $_POST['Potassium'];
	$Precautions = $_POST['Precautions'];
	$PriceOfMaterial = $_POST['PriceOfMaterial'];
	$ProductType = $_POST['ProductType'];
	$ProjectNumber = $_POST['ProjectNumber'];
	$ProtectiveGloves = $_POST['ProtectiveGloves'];
	$Protein = $_POST['Protein'];
	$Quality_Sensitive = $_POST['Quality_Sensitive'];
	$QuickScan = $_POST['QuickScan'];
	$RefractiveIndex = $_POST['RefractiveIndex'];
	$Riboflavin = $_POST['Riboflavin'];
	$ReplacedBy = $_POST['ReplacedBy'];
	$RestrictedAccess = $_POST['RestrictedAccess'];
	$SaturatedFat = $_POST['SaturatedFat'];
	$SaturatedFatCalories = $_POST['SaturatedFatCalories'];
	$ShelfLifeInMonths = $_POST['ShelfLifeInMonths'];
	$SignsAndSymptoms = $_POST['SignsAndSymptoms'];
	$Sodium = $_POST['Sodium'];
	$SolubilityInWater = $_POST['SolubilityInWater'];
	$SolubleFiber = $_POST['SolubleFiber'];
	$SpecialFirefightingProcedures = $_POST['SpecialFirefightingProcedures'];
	$SpecificGravity = $_POST['SpecificGravity'];
	$SpecificGravityUnits = $_POST['SpecificGravityUnits'];
	$Stability = $_POST['Stability'];
	$StabilityConditions = $_POST['StabilityConditions'];
	$StepsToBeTaken = $_POST['StepsToBeTaken'];
	$StorageAndShelfLife = $_POST['StorageAndShelfLife'];
	$SugarAlcohol = $_POST['SugarAlcohol'];
	$Sugars = $_POST['Sugars'];
	$Thiamin = $_POST['Thiamin'];
	$TotalCarbohydrates = $_POST['TotalCarbohydrates'];
	$TotalFat = $_POST['TotalFat'];
	$TotalSolids = $_POST['TotalSolids'];
	$TransFattyAcids = $_POST['TransFattyAcids'];
	$UEL = $_POST['UEL'];
	$UnusualFire = $_POST['UnusualFire'];
	$UseLevel = $_POST['UseLevel'];
	$VaporDensity = $_POST['VaporDensity'];
	$VaporPressure = $_POST['VaporPressure'];
	$VentilatorMechanical = $_POST['VentilatorMechanical'];
	$VentilatorSpecial = $_POST['VentilatorSpecial'];
	$VerifiedYN = $_POST['VerifiedYN'];
	$VitaminA = $_POST['VitaminA'];
	$VitaminB12 = $_POST['VitaminB12'];
	$VitaminB6 = $_POST['VitaminB6'];
	$VitaminC = $_POST['VitaminC'];
	$VitaminD = $_POST['VitaminD'];
	$VitaminE = $_POST['VitaminE'];
	$WasteDisposalMethod = $_POST['WasteDisposalMethod'];
	$Water = $_POST['Water'];
	$WeightPerGallon = $_POST['WeightPerGallon'];
	$WeightPerGallonUnits = $_POST['WeightPerGallonUnits'];
	$WorkHygienicPractices = $_POST['WorkHygienicPractices'];
	$Zinc = $_POST['Zinc'];
	$Notes = $_POST['Notes'];

	$DateOfFormulation = $_POST['DateOfFormulation'];
	if ( $DateOfFormulation != '' ) {
		$date_parts = explode("/", $DateOfFormulation);
		$NewDateOfFormulation = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
		if ( is_numeric($date_parts[0]) and is_numeric($date_parts[1]) and is_numeric($date_parts[2]) and strlen($date_parts[2]) == 4 ) {
			if ( !checkdate($date_parts[0], $date_parts[1], $date_parts[2]) ) {
				$error_found=true;
				$error_message .= "Invalid (" . $DateOfFormulation . ") date entered";
			} else {
				$date_clause = " DateOfFormulation = '" . $NewDateOfFormulation . "',";
			}
		} else {
			$error_found=true;
			$error_message .= "Invalid (" . $DateOfFormulation . ") date entered";
		}
	} else {
		$date_clause = " DateOfFormulation = NULL,";
	}

	if ( $AllergenEgg != 1 ) {
		$AllergenEgg = 0;
	}
	if ( $AllergenMilk != 1 ) {
		$AllergenMilk = 0;
	}
	if ( $AllergenPeanut != 1 ) {
		$AllergenPeanut = 0;
	}
	if ( $AllergenSeafood != 1 ) {
		$AllergenSeafood = 0;
	}
	if ( $AllergenSeed != 1 ) {
		$AllergenSeed = 0;
	}
	if ( $AllergenSoybean != 1 ) {
		$AllergenSoybean = 0;
	}
	if ( $AllergenSulfites != 1 ) {
		$AllergenSulfites = 0;
	}
	if ( $AllergenTreeNuts != 1 ) {
		$AllergenTreeNuts = 0;
	}
	if ( $AllergenWheat != 1 ) {
		$AllergenWheat = 0;
	}
	if ( $AllergenYellow != 1 ) {
		$AllergenYellow = 0;
	}


	if ( $CurrentSellingItem != 1 ) {
		$CurrentSellingItem = 0;
	}
	if ( $FinalProductNotCreatedByAbelei != 1 ) {
		$FinalProductNotCreatedByAbelei = 0;
	}
	if ( $HazardousPolymerization != 1 ) {
		$HazardousPolymerization = 0;
	}
	if ( $Intermediary != 1 ) {
		$Intermediary = 0;
	}
	if ( $Organic != 1 ) {
		$Organic = 0;
	}
	if ( $RestrictedAccess != 1 ) {
		$RestrictedAccess = 0;
	}
	if ( $Stability != 1 ) {
		$Stability = 0;
	}
	if ( $VerifiedYN != 1 ) {
		$VerifiedYN = 0;
	}
	if ( $DeveloperID != 1 ) {
		$DeveloperID = 0;
	}
	if ( $Ash == '' ) {
		$Ash = 0;
	}
	if ( $BatchSizeKg == '' ) {
		$BatchSizeKg = 0;
	}
	if ( $Biotin == '' ) {
		$Biotin = 0;
	}
	if ( $Calcium == '' ) {
		$Calcium = 0;
	}
	if ( $Calories == '' ) {
		$Calories = 0;
	}
	if ( $CaloriesFromFat == '' ) {
		$CaloriesFromFat = 0;
	}
	if ( $Cholesterol == '' ) {
		$Cholesterol = 0;
	}
	if ( $Copper == '' ) {
		$Copper = 0;
	}
	if ( $DietaryFiber == '' ) {
		$DietaryFiber = 0;
	}
	if ( $FatCalories == '' ) {
		$FatCalories = 0;
	}
	if ( $Folate == '' ) {
		$Folate = 0;
	}
	if ( $FolateFolacinFolicAdic == '' ) {
		$FolateFolacinFolicAdic = 0;
	}
	if ( $InsolubleFiber == '' ) {
		$InsolubleFiber = 0;
	}
	if ( $Iodine == '' ) {
		$Iodine = 0;
	}
	if ( $Iron == '' ) {
		$Iron = 0;
	}
	if ( $Lactose == '' ) {
		$Lactose = 0;
	}
	if ( $Magnesium == '' ) {
		$Magnesium = 0;
	}
	if ( $Manganese == '' ) {
		$Manganese = 0;
	}
	if ( $MonounsaturatedFat == '' ) {
		$MonounsaturatedFat = 0;
	}
	if ( $MostRecentVendorID == '' ) {
		$MostRecentVendorID = 0;
	}
	if ( $Niacin == '' ) {
		$Niacin = 0;
	}
	if ( $OtherCarbohydrates == '' ) {
		$OtherCarbohydrates = 0;
	}
	if ( $PantothenicAcid == '' ) {
		$PantothenicAcid = 0;
	}
	if ( $Phosphorus == '' ) {
		$Phosphorus = 0;
	}
	if ( $PolyunsaturatedFat == '' ) {
		$PolyunsaturatedFat = 0;
	}
	if ( $Potassium == '' ) {
		$Potassium = 0;
	}
	if ( $PriceOfMaterial == '' ) {
		$PriceOfMaterial = 0;
	}
	if ( $Protein == '' ) {
		$Protein = 0;
	}
	if ( $Riboflavin == '' ) {
		$Riboflavin = 0;
	}
	if ( $SaturatedFat == '' ) {
		$SaturatedFat = 0;
	}
	if ( $SaturatedFatCalories == '' ) {
		$SaturatedFatCalories = 0;
	}
	if ( $ShelfLifeInMonths == '' ) {
		$ShelfLifeInMonths = 0;
	}
	if ( $Sodium == '' ) {
		$Sodium = 0;
	}
	if ( $SolubleFiber == '' ) {
		$SolubleFiber = 0;
	}
	if ( $SpecificGravity == '' ) {
		$SpecificGravity = 0;
	}
	if ( $SugarAlcohol == '' ) {
		$SugarAlcohol = 0;
	}
	if ( $Sugars == '' ) {
		$Sugars = 0;
	}
	if ( $Thiamin == '' ) {
		$Thiamin = 0;
	}
	if ( $TotalCarbohydrates == '' ) {
		$TotalCarbohydrates = 0;
	}
	if ( $TotalFat == '' ) {
		$TotalFat = 0;
	}
	if ( $TotalSolids == '' ) {
		$TotalSolids = 0;
	}
	if ( $TransFattyAcids == '' ) {
		$TransFattyAcids = 0;
	}
	if ( $VitaminA == '' ) {
		$VitaminA = 0;
	}
	if ( $VitaminB12 == '' ) {
		$VitaminB12 = 0;
	}
	if ( $VitaminB6 == '' ) {
		$VitaminB6 = 0;
	}
	if ( $VitaminC == '' ) {
		$VitaminC = 0;
	}
	if ( $VitaminD == '' ) {
		$VitaminD = 0;
	}
	if ( $VitaminE == '' ) {
		$VitaminE = 0;
	}
	if ( $Water == '' ) {
		$Water = 0;
	}
	if ( $WeightPerGallon == '' ) {
		$WeightPerGallon = 0;
	}
	if ( $Zinc == '' ) {
		$Zinc = 0;
	}

	// check_field() FUNCTION IN global.php
	check_field($Ash, 3, 'Ash');
	check_field($BatchSizeKg, 3, 'Batch Size Kg');
	check_field($Biotin, 3, 'Biotin');
	check_field($Calcium, 3, 'Calcium');
	check_field($Calories, 3, 'Calories');
	check_field($CaloriesFromFat, 3, 'Calories from Fat');
	check_field($Cholesterol, 3, 'Cholesterol');
	check_field($Copper, 3, 'Copper');
	check_field($DietaryFiber, 3, 'Dietary Fiber');
	check_field($FatCalories, 3, 'Fat Calories');
	check_field($Folate, 3, 'Folate');
	check_field($FolateFolacinFolicAdic, 3, 'Folate Folacin Folic Adic');
	check_field($InsolubleFiber, 3, 'Insoluble Fiber');
	check_field($Iodine, 3, 'Iodine');
	check_field($Iron, 3, 'Iron');
	check_field($Lactose, 3, 'Lactose');
	check_field($Magnesium, 3, 'Magnesium');
	check_field($Manganese, 3, 'Manganese');
	check_field($MonounsaturatedFat, 3, 'Monounsaturated Fat');
	check_field($MostRecentVendorID, 3, 'Most Recent Vendor ID');
	check_field($Niacin, 3, 'Niacin');
	check_field($OtherCarbohydrates, 3, 'Other Carbohydrates');
	check_field($PantothenicAcid, 3, 'Pantothenic Acid');
	check_field($Phosphorus, 3, 'Phosphorus');
	check_field($PolyunsaturatedFat, 3, 'Polyunsaturated Fat');
	check_field($Potassium, 3, 'Potassium');
	check_field($PriceOfMaterial, 3, 'Price of Material');
	check_field($Protein, 3, 'Protein');
	check_field($Riboflavin, 3, 'Riboflavin');
	check_field($SaturatedFat, 3, 'Saturated Fat');
	check_field($SaturatedFatCalories, 3, 'Saturated Fat Calories');
	check_field($ShelfLifeInMonths, 3, 'Shelf Life in Months');
	check_field($Sodium, 3, 'Sodium');
	check_field($SolubleFiber, 3, 'Soluble Fiber');
	check_field($SpecificGravity, 3, 'Specific Gravity');
	check_field($SugarAlcohol, 3, 'Sugar Alcohol');
	check_field($Sugars, 3, 'Sugars');
	check_field($Thiamin, 3, 'Thiamin');
	check_field($TotalCarbohydrates, 3, 'Total Carbohydrates');
	check_field($TotalFat, 3, 'Total Fat');
	check_field($TotalSolids, 3, 'Total Solids');
	check_field($TransFattyAcids, 3, 'Trans Fatty Acids');
	check_field($VitaminA, 3, 'Vitamin A');
	check_field($VitaminB12, 3, 'Vitamin B12');
	check_field($VitaminB6, 3, 'Vitamin B6');
	check_field($VitaminC, 3, 'Vitamin C');
	check_field($VitaminD, 3, 'Vitamin D');
	check_field($VitaminE, 3, 'Vitamin E');
	check_field($Water, 3, 'Water');
	check_field($WeightPerGallon, 3, 'Weight Per Gallon');
	check_field($Zinc, 3, 'Zinc');

	if ( !$error_found ) {

		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$AllergenEgg = escape_data($AllergenEgg);
		$AllergenMilk = escape_data($AllergenMilk);
		$AllergenPeanut = escape_data($AllergenPeanut);
		$AllergenSeafood = escape_data($AllergenSeafood);
		$AllergenSeed = escape_data($AllergenSeed);
		$AllergenSoybean = escape_data($AllergenSoybean);
		$AllergenSulfites = escape_data($AllergenSulfites);
		$AllergenTreeNuts = escape_data($AllergenTreeNuts);
		$AllergenWheat = escape_data($AllergenWheat);
		$AllergenYellow = escape_data($AllergenYellow);
		$Appearance = escape_data($Appearance);
		$Ash = escape_data($Ash);
		$BatchSize = escape_data($BatchSize);
		$BatchSizeKg = escape_data($BatchSizeKg);
		$Biotin = escape_data($Biotin);
		$BoilingPoint = escape_data($BoilingPoint);
		$Calcium = escape_data($Calcium);
		$Calories = escape_data($Calories);
		$CaloriesFromFat = escape_data($CaloriesFromFat);
		$Cholesterol = escape_data($Cholesterol);
		$Copper = escape_data($Copper);
		$CurrentSellingItem = escape_data($CurrentSellingItem);
		$Designation = escape_data($Designation);
		$DateOfFormulation = escape_data($DateOfFormulation);
		$DietaryFiber = escape_data($DietaryFiber);
		$DeveloperID = escape_data($DeveloperID);
		$EmergencyFirstAidProcedure = escape_data($EmergencyFirstAidProcedure);
		$EvaporationRate = escape_data($EvaporationRate);
		$ExtinguishingMedia = escape_data($ExtinguishingMedia);
		$FatCalories = escape_data($FatCalories);
		$FEMA_NBR = escape_data($FEMA_NBR);
		$FinalProductNotCreatedByAbelei = escape_data($FinalProductNotCreatedByAbelei);
		$FlammableLimits = escape_data($FlammableLimits);
		$Flashpoint = escape_data($Flashpoint);
		$FlavorAndAroma = escape_data($FlavorAndAroma);
		$Folate = escape_data($Folate);
		$FolateFolacinFolicAdic = escape_data($FolateFolacinFolicAdic);
		$GeneralDescriptionOfFormulation = escape_data($GeneralDescriptionOfFormulation);
		$GMO = escape_data($GMO);
		$Halal = escape_data($Halal);
		$Hazard = escape_data($Hazard);
		$HazardousComponents = escape_data($HazardousComponents);
		$HazardousDecomposition = escape_data($HazardousDecomposition);
		$HazardousPolymerization = escape_data($HazardousPolymerization);
		$HazardousPolymerizationConditions = escape_data($HazardousPolymerizationConditions);
		$HealthHazards = escape_data($HealthHazards);
		$Incompatibility = escape_data($Incompatibility);
		$InsolubleFiber = escape_data($InsolubleFiber);
		$Intermediary = escape_data($Intermediary);
		$Iodine = escape_data($Iodine);
		$Iron = escape_data($Iron);
		$Keywords = escape_data($Keywords);
		$Kosher = escape_data($Kosher);
		$KosherStatus = escape_data($KosherStatus);
		$LabelDeclaration = escape_data($LabelDeclaration);
		$Lactose = escape_data($Lactose);
		$LEL = escape_data($LEL);
		$Magnesium = escape_data($Magnesium);
		$Manganese = escape_data($Manganese);
		$ManufacturingInstructions = escape_data($ManufacturingInstructions);
		$MedicalCondition = escape_data($MedicalCondition);
		$MeltingPoint = escape_data($MeltingPoint);
		$MonounsaturatedFat = escape_data($MonounsaturatedFat);
		$MostRecentVendorID = escape_data($MostRecentVendorID);
		$Natural_OR_Artificial = escape_data($Natural_OR_Artificial);
		$Niacin = escape_data($Niacin);
		$NonFlavorIngredients = escape_data($NonFlavorIngredients);
		$NoteForFormulation = escape_data($NoteForFormulation);
		$OldDescriptionDelete = escape_data($OldDescriptionDelete);
		$Organic = escape_data($Organic);
		$OtherCarbohydrates = escape_data($OtherCarbohydrates);
		$OtherProtectiveClothing = escape_data($OtherProtectiveClothing);
		$Packaging = escape_data($Packaging);
		$PantothenicAcid = escape_data($PantothenicAcid);
		$Phosphorus = escape_data($Phosphorus);
		$PolyunsaturatedFat = escape_data($PolyunsaturatedFat);
		$Potassium = escape_data($Potassium);
		$Precautions = escape_data($Precautions);
		$PriceOfMaterial = escape_data($PriceOfMaterial);
		$ProductType = escape_data($ProductType);
		$ProjectNumber = escape_data($ProjectNumber);
		$ProtectiveGloves = escape_data($ProtectiveGloves);
		$Protein = escape_data($Protein);
		$Quality_Sensitive = escape_data($Quality_Sensitive);
		$QuickScan = escape_data($QuickScan);
		$RefractiveIndex = escape_data($RefractiveIndex);
		$Riboflavin = escape_data($Riboflavin);
		$ReplacedBy = escape_data($ReplacedBy);
		$RestrictedAccess = escape_data($RestrictedAccess);
		$SaturatedFat = escape_data($SaturatedFat);
		$SaturatedFatCalories = escape_data($SaturatedFatCalories);
		$ShelfLifeInMonths = escape_data($ShelfLifeInMonths);
		$SignsAndSymptoms = escape_data($SignsAndSymptoms);
		$Sodium = escape_data($Sodium);
		$SolubilityInWater = escape_data($SolubilityInWater);
		$SolubleFiber = escape_data($SolubleFiber);
		$SpecialFirefightingProcedures = escape_data($SpecialFirefightingProcedures);
		$SpecificGravity = escape_data($SpecificGravity);
		$SpecificGravityUnits = escape_data($SpecificGravityUnits);
		$Stability = escape_data($Stability);
		$StabilityConditions = escape_data($StabilityConditions);
		$StepsToBeTaken = escape_data($StepsToBeTaken);
		$StorageAndShelfLife = escape_data($StorageAndShelfLife);
		$SugarAlcohol = escape_data($SugarAlcohol);
		$Sugars = escape_data($Sugars);
		$Thiamin = escape_data($Thiamin);
		$TotalCarbohydrates = escape_data($TotalCarbohydrates);
		$TotalFat = escape_data($TotalFat);
		$TotalSolids = escape_data($TotalSolids);
		$TransFattyAcids = escape_data($TransFattyAcids);
		$UEL = escape_data($UEL);
		$UnusualFire = escape_data($UnusualFire);
		$UseLevel = escape_data($UseLevel);
		$VaporDensity = escape_data($VaporDensity);
		$VaporPressure = escape_data($VaporPressure);
		$VentilatorMechanical = escape_data($VentilatorMechanical);
		$VentilatorSpecial = escape_data($VentilatorSpecial);
		$VerifiedYN = escape_data($VerifiedYN);
		$VitaminA = escape_data($VitaminA);
		$VitaminB12 = escape_data($VitaminB12);
		$VitaminB6 = escape_data($VitaminB6);
		$VitaminC = escape_data($VitaminC);
		$VitaminD = escape_data($VitaminD);
		$VitaminE = escape_data($VitaminE);
		$WasteDisposalMethod = escape_data($WasteDisposalMethod);
		$Water = escape_data($Water);
		$WeightPerGallon = escape_data($WeightPerGallon);
		$WeightPerGallonUnits = escape_data($WeightPerGallonUnits);
		$WorkHygienicPractices = escape_data($WorkHygienicPractices);
		$Zinc = escape_data($Zinc);
		$Notes = escape_data($Notes);
		
		if ( $ProductNumberInternal != "" ) {
			$sql = "UPDATE productmaster " .
			" SET AllergenEgg = '" . $AllergenEgg . "'," .
			" AllergenMilk = '" . $AllergenMilk . "'," .
			" AllergenPeanut = '" . $AllergenPeanut . "'," .
			" AllergenSeafood = '" . $AllergenSeafood . "'," .
			" AllergenSeed = '" . $AllergenSeed . "'," .
			" AllergenSoybean = '" . $AllergenSoybean . "'," .
			" AllergenSulfites = '" . $AllergenSulfites . "'," .
			" AllergenTreeNuts = '" . $AllergenTreeNuts . "'," .
			" AllergenWheat = '" . $AllergenWheat . "'," .
			" AllergenYellow = '" . $AllergenYellow . "'," .
			" Appearance = '" . $Appearance . "'," .
			" Ash = '" . $Ash . "'," .
			" BatchSize = '" . $BatchSize . "'," .
			" BatchSizeKg = '" . $BatchSizeKg . "'," .
			" Biotin = '" . $Biotin . "'," .
			" BoilingPoint = '" . $BoilingPoint . "'," .
			" Calcium = '" . $Calcium . "'," .
			" Calories = '" . $Calories . "'," .
			" CaloriesFromFat = '" . $CaloriesFromFat . "'," .
			" Cholesterol = '" . $Cholesterol . "'," .
			" Copper = '" . $Copper . "'," .
			" CurrentSellingItem = '" . $CurrentSellingItem . "'," .
			$date_clause .
			" DietaryFiber = '" . $DietaryFiber . "'," .
			" Designation = '" . $Designation . "'," .
			" DeveloperID = '" . $DeveloperID . "'," .
			" EmergencyFirstAidProcedure = '" . $EmergencyFirstAidProcedure . "'," .
			" EvaporationRate = '" . $EvaporationRate . "'," .
			" ExtinguishingMedia = '" . $ExtinguishingMedia . "'," .
			" FatCalories = '" . $FatCalories . "'," .
			" FEMA_NBR = '" . $FEMA_NBR . "'," .
			" FinalProductNotCreatedByAbelei = '" . $FinalProductNotCreatedByAbelei . "'," .
			" FlammableLimits = '" . $FlammableLimits . "'," .
			" Flashpoint = '" . $Flashpoint . "'," .
			" FlavorAndAroma = '" . $FlavorAndAroma . "'," .
			" Folate = '" . $Folate . "'," .
			" FolateFolacinFolicAdic = '" . $FolateFolacinFolicAdic . "'," .
			" GeneralDescriptionOfFormulation = '" . $GeneralDescriptionOfFormulation . "'," .
			" GMO = '" . $GMO . "'," .
			" Halal = '" . $Halal . "'," .
			" Hazard = '" . $Hazard . "'," .
			" HazardousComponents = '" . $HazardousComponents . "'," .
			" HazardousDecomposition = '" . $HazardousDecomposition . "'," .
			" HazardousPolymerization = '" . $HazardousPolymerization . "'," .
			" HazardousPolymerizationConditions = '" . $HazardousPolymerizationConditions . "'," .
			" HealthHazards = '" . $HealthHazards . "'," .
			" Incompatibility = '" . $Incompatibility . "'," .
			" InsolubleFiber = '" . $InsolubleFiber . "'," .
			" Intermediary = '" . $Intermediary . "'," .
			" UnitOfMeasure = '" . $inventory_units . "'," .
			" Iodine = '" . $Iodine . "'," .
			" Iron = '" . $Iron . "'," .
			" Keywords = '" . $Keywords . "'," .
			" Kosher = '" . $Kosher . "'," .
			" KosherStatus = '" . $KosherStatus . "'," .
			" LabelDeclaration = '" . $LabelDeclaration . "'," .
			" Lactose = '" . $Lactose . "'," .
			" LEL = '" . $LEL . "'," .
			" Magnesium = '" . $Magnesium . "'," .
			" Manganese = '" . $Manganese . "'," .
			" ManufacturingInstructions = '" . $ManufacturingInstructions . "'," .
			" MedicalCondition = '" . $MedicalCondition . "'," .
			" MeltingPoint = '" . $MeltingPoint . "'," .
			" MonounsaturatedFat = '" . $MonounsaturatedFat . "'," .
			" MostRecentVendorID = '" . $MostRecentVendorID . "'," .
			" Natural_OR_Artificial = '" . $Natural_OR_Artificial . "'," .
			" Niacin = '" . $Niacin . "'," .
			" NonFlavorIngredients = '" . $NonFlavorIngredients . "'," .
			" NoteForFormulation = '" . $NoteForFormulation . "'," .
			" OldDescriptionDelete = '" . $OldDescriptionDelete . "'," .
			" Organic = '" . $Organic . "'," .
			" OtherCarbohydrates = '" . $OtherCarbohydrates . "'," .
			" OtherProtectiveClothing = '" . $OtherProtectiveClothing . "'," .
			" Packaging = '" . $Packaging . "'," .
			" PantothenicAcid = '" . $PantothenicAcid . "'," .
			" Phosphorus = '" . $Phosphorus . "'," .
			" PolyunsaturatedFat = '" . $PolyunsaturatedFat . "'," .
			" Potassium = '" . $Potassium . "'," .
			" Precautions = '" . $Precautions . "'," .
			" PriceOfMaterial = '" . $PriceOfMaterial . "'," .
			" ProductType = '" . $ProductType . "'," .
			" ProjectNumber = '" . $ProjectNumber . "'," .
			" ProtectiveGloves = '" . $ProtectiveGloves . "'," .
			" Protein = '" . $Protein . "'," .
			" Quality_Sensitive = '" . $Quality_Sensitive . "'," .
			" QuickScan = '" . $QuickScan . "'," .
			" RefractiveIndex = '" . $RefractiveIndex . "'," .
			" Riboflavin = '" . $Riboflavin . "'," .
			" ReplacedBy = '" . $ReplacedBy . "'," .
			" RestrictedAccess = '" . $RestrictedAccess . "'," .
			" SaturatedFat = '" . $SaturatedFat . "'," .
			" SaturatedFatCalories = '" . $SaturatedFatCalories . "'," .
			" ShelfLifeInMonths = '" . $ShelfLifeInMonths . "'," .
			" SignsAndSymptoms = '" . $SignsAndSymptoms . "'," .
			" Sodium = '" . $Sodium . "'," .
			" SolubilityInWater = '" . $SolubilityInWater . "'," .
			" SolubleFiber = '" . $SolubleFiber . "'," .
			" SpecialFirefightingProcedures = '" . $SpecialFirefightingProcedures . "'," .
			" SpecificGravity = '" . $SpecificGravity . "'," .
			" SpecificGravityUnits = '" . $SpecificGravityUnits . "'," .
			" Stability = '" . $Stability . "'," .
			" StabilityConditions = '" . $StabilityConditions . "'," .
			" StepsToBeTaken = '" . $StepsToBeTaken . "'," .
			" StorageAndShelfLife = '" . $StorageAndShelfLife . "'," .
			" SugarAlcohol = '" . $SugarAlcohol . "'," .
			" Sugars = '" . $Sugars . "'," .
			" Thiamin = '" . $Thiamin . "'," .
			" TotalCarbohydrates = '" . $TotalCarbohydrates . "'," .
			" TotalFat = '" . $TotalFat . "'," .
			" TotalSolids = '" . $TotalSolids . "'," .
			" TransFattyAcids = '" . $TransFattyAcids . "'," .
			" UEL = '" . $UEL . "'," .
			" UnusualFire = '" . $UnusualFire . "'," .
			" UseLevel = '" . $UseLevel . "'," .
			" VaporDensity = '" . $VaporDensity . "'," .
			" VaporPressure = '" . $VaporPressure . "'," .
			" VentilatorMechanical = '" . $VentilatorMechanical . "'," .
			" VentilatorSpecial = '" . $VentilatorSpecial . "'," .
			" VerifiedYN = '" . $VerifiedYN . "'," .
			" VitaminA = '" . $VitaminA . "'," .
			" VitaminB12 = '" . $VitaminB12 . "'," .
			" VitaminB6 = '" . $VitaminB6 . "'," .
			" VitaminC = '" . $VitaminC . "'," .
			" VitaminD = '" . $VitaminD . "'," .
			" VitaminE = '" . $VitaminE . "'," .
			" WasteDisposalMethod = '" . $WasteDisposalMethod . "'," .
			" Water = '" . $Water . "'," .
			" WeightPerGallon = '" . $WeightPerGallon . "'," .
			" WeightPerGallonUnits = '" . $WeightPerGallonUnits . "'," .
			" WorkHygienicPractices = '" . $WorkHygienicPractices . "'," .
			" Zinc = '" . $Zinc . "', " .
			" Notes = '" . $Notes . "'" .
			" WHERE ProductNumberInternal = '" . $ProductNumberInternal . "'";
			mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		}
		//else {
		//	$sql = "INSERT INTO customer_contacts (title, first_name, last_name, suffix, job_title, department, customer_id, address1, address2, city, state, zip, country, email1, email2) VALUES ('" . $title . "','" . $first_name . "','" . $last_name . "','" . $suffix . "','" . $job_title . "','" . $department . "', " . $customer_id . ", '" . $address1 . "', '" . $address2 . "', '" . $city . "', '" . $state . "', '" . $zip . "', '" . $country . "', '" . $email1 . "', '" . $email2 . "','" . $notes . "', '" . $notes . "')";
		//	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//	$cid = mysql_insert_id();
		//}
		//die("$sql");
		$_SESSION['note'] = "Material information successfully saved<BR>";
//		$_SESSION['note'] .= "<h3>$sql</h3>";
		header("location: flavors_materials.php");
		exit();

	}

} elseif ( $pni != '' ) {

	$sql = "SELECT * FROM productmaster WHERE ProductNumberInternal = '$pni'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$ProductNumberInternal = $row['ProductNumberInternal'];
	$AllergenEgg = $row['AllergenEgg'];
	$AllergenMilk = $row['AllergenMilk'];
	$AllergenPeanut = $row['AllergenPeanut'];
	$AllergenSeafood = $row['AllergenSeafood'];
	$AllergenSeed = $row['AllergenSeed'];
	$AllergenSoybean = $row['AllergenSoybean'];
	$AllergenSulfites = $row['AllergenSulfites'];
	$AllergenTreeNuts = $row['AllergenTreeNuts'];
	$AllergenWheat = $row['AllergenWheat'];
	$AllergenYellow = $row['AllergenYellow'];

	if ( $AllergenEgg == 1 ) {
		$AllergenEggStatus = "CHECKED";
	} else {
		$AllergenEggStatus = "";
	}
	if ( $AllergenMilk == 1 ) {
		$AllergenMilkStatus = "CHECKED";
	} else {
		$AllergenMilkStatus = "";
	}
	if ( $AllergenPeanut == 1 ) {
		$AllergenPeanutStatus = "CHECKED";
	} else {
		$AllergenPeanutStatus = "";
	}
	if ( $AllergenSeafood == 1 ) {
		$AllergenSeafoodStatus = "CHECKED";
	} else {
		$AllergenSeafoodStatus = "";
	}
	if ( $AllergenSeed == 1 ) {
		$AllergenSeedStatus = "CHECKED";
	} else {
		$AllergenSeedStatus = "";
	}
	if ( $AllergenSoybean == 1 ) {
		$AllergenSoybeanStatus = "CHECKED";
	} else {
		$AllergenSoybeanStatus = "";
	}
	if ( $AllergenSulfites == 1 ) {
		$AllergenSulfitesStatus = "CHECKED";
	} else {
		$AllergenSulfitesStatus = "";
	}
	if ( $AllergenTreeNuts == 1 ) {
		$AllergenTreeNutsStatus = "CHECKED";
	} else {
		$AllergenTreeNutsStatus = "";
	}
	if ( $AllergenWheat == 1 ) {
		$AllergenWheatStatus = "CHECKED";
	} else {
		$AllergenWheatStatus = "";
	}
	if ( $AllergenYellow == 1 ) {
		$AllergenYellowStatus = "CHECKED";
	} else {
		$AllergenYellowStatus = "";
	}

	$Appearance = $row['Appearance'];
	$Ash = $row['Ash'];
	$BatchSize = $row['BatchSize'];
	$BatchSizeKg = $row['BatchSizeKg'];
	$Biotin = $row['Biotin'];
	$BoilingPoint = $row['BoilingPoint'];
	$Calcium = $row['Calcium'];
	$Calories = $row['Calories'];
	$CaloriesFromFat = $row['CaloriesFromFat'];
	$Cholesterol = $row['Cholesterol'];
	$Copper = $row['Copper'];
	$CurrentSellingItem = $row['CurrentSellingItem'];

	$DateOfFormulation = $row['DateOfFormulation'];

	$Designation = $row['Designation'];
	$DietaryFiber = $row['DietaryFiber'];
	$DeveloperID = $row['DeveloperID'];
	$EmergencyFirstAidProcedure = $row['EmergencyFirstAidProcedure'];
	$EvaporationRate = $row['EvaporationRate'];
	$ExtinguishingMedia = $row['ExtinguishingMedia'];
	$FatCalories = $row['FatCalories'];
	$FEMA_NBR = $row['FEMA_NBR'];
	$FinalProductNotCreatedByAbelei = $row['FinalProductNotCreatedByAbelei'];
	$FlammableLimits = $row['FlammableLimits'];
	$Flashpoint = $row['Flashpoint'];
	$FlavorAndAroma = $row['FlavorAndAroma'];
	$Folate = $row['Folate'];
	$FolateFolacinFolicAdic = $row['FolateFolacinFolicAdic'];
	$GeneralDescriptionOfFormulation = $row['GeneralDescriptionOfFormulation'];
	$GMO = $row['GMO'];
	$Halal = $row['Halal'];
	$Hazard = $row['Hazard'];
	$HazardousComponents = $row['HazardousComponents'];
	$HazardousDecomposition = $row['HazardousDecomposition'];
	$HazardousPolymerization = $row['HazardousPolymerization'];
	$HazardousPolymerizationConditions = $row['HazardousPolymerizationConditions'];
	$HealthHazards = $row['HealthHazards'];
	$Incompatibility = $row['Incompatibility'];
	$InsolubleFiber = $row['InsolubleFiber'];
	$Intermediary = $row['Intermediary'];
	$inventory_units = $row['UnitOfMeasure'];
	$Iodine = $row['Iodine'];
	$Iron = $row['Iron'];
	$Keywords = $row['Keywords'];
	$Kosher = $row['Kosher'];
	$KosherStatus = $row['KosherStatus'];
	$LabelDeclaration = $row['LabelDeclaration'];
	$Lactose = $row['Lactose'];
	$LEL = $row['LEL'];
	$Magnesium = $row['Magnesium'];
	$Manganese = $row['Manganese'];
	$ManufacturingInstructions = $row['ManufacturingInstructions'];
	$MedicalCondition = $row['MedicalCondition'];
	$MeltingPoint = $row['MeltingPoint'];
	$MonounsaturatedFat = $row['MonounsaturatedFat'];
	$MostRecentVendorID = $row['MostRecentVendorID'];
	$Natural_OR_Artificial = $row['Natural_OR_Artificial'];
	$Niacin = $row['Niacin'];
	$NonFlavorIngredients = $row['NonFlavorIngredients'];
	$NoteForFormulation = $row['NoteForFormulation'];
	$OldDescriptionDelete = $row['OldDescriptionDelete'];
	$Organic = $row['Organic'];
	$OtherCarbohydrates = $row['OtherCarbohydrates'];
	$OtherProtectiveClothing = $row['OtherProtectiveClothing'];
	$Packaging = $row['Packaging'];
	$PantothenicAcid = $row['PantothenicAcid'];
	$Phosphorus = $row['Phosphorus'];
	$PolyunsaturatedFat = $row['PolyunsaturatedFat'];
	$Potassium = $row['Potassium'];
	$Precautions = $row['Precautions'];
	$PriceOfMaterial = $row['PriceOfMaterial'];
	$ProductType = $row['ProductType'];
	$ProjectNumber = $row['ProjectNumber'];
	$ProtectiveGloves = $row['ProtectiveGloves'];
	$Protein = $row['Protein'];
	$Quality_Sensitive = $row['Quality_Sensitive'];
	$QuickScan = $row['QuickScan'];
	$RefractiveIndex = $row['RefractiveIndex'];
	$Riboflavin = $row['Riboflavin'];
	$ReplacedBy = $row['ReplacedBy'];
	$RestrictedAccess = $row['RestrictedAccess'];
	$SaturatedFat = $row['SaturatedFat'];
	$SaturatedFatCalories = $row['SaturatedFatCalories'];
	$ShelfLifeInMonths = $row['ShelfLifeInMonths'];
	$SignsAndSymptoms = $row['SignsAndSymptoms'];
	$Sodium = $row['Sodium'];
	$SolubilityInWater = $row['SolubilityInWater'];
	$SolubleFiber = $row['SolubleFiber'];
	$SpecialFirefightingProcedures = $row['SpecialFirefightingProcedures'];
	$SpecificGravity = $row['SpecificGravity'];
	$SpecificGravityUnits = $row['SpecificGravityUnits'];
	$Stability = $row['Stability'];
	$StabilityConditions = $row['StabilityConditions'];
	$StepsToBeTaken = $row['StepsToBeTaken'];
	$StorageAndShelfLife = $row['StorageAndShelfLife'];
	$SugarAlcohol = $row['SugarAlcohol'];
	$Sugars = $row['Sugars'];
	$Thiamin = $row['Thiamin'];
	$TotalCarbohydrates = $row['TotalCarbohydrates'];
	$TotalFat = $row['TotalFat'];
	$TotalSolids = $row['TotalSolids'];
	$TransFattyAcids = $row['TransFattyAcids'];
	$UEL = $row['UEL'];
	$UnusualFire = $row['UnusualFire'];
	$UseLevel = $row['UseLevel'];
	$VaporDensity = $row['VaporDensity'];
	$VaporPressure = $row['VaporPressure'];
	$VentilatorMechanical = $row['VentilatorMechanical'];
	$VentilatorSpecial = $row['VentilatorSpecial'];
	$VerifiedYN = $row['VerifiedYN'];
	$VitaminA = $row['VitaminA'];
	$VitaminB12 = $row['VitaminB12'];
	$VitaminB6 = $row['VitaminB6'];
	$VitaminC = $row['VitaminC'];
	$VitaminD = $row['VitaminD'];
	$VitaminE = $row['VitaminE'];
	$WasteDisposalMethod = $row['WasteDisposalMethod'];
	$Water = $row['Water'];
	$WeightPerGallon = $row['WeightPerGallon'];
	$WeightPerGallonUnits = $row['WeightPerGallonUnits'];
	$WorkHygienicPractices = $row['WorkHygienicPractices'];
	$Zinc = $row['Zinc'];
	$Notes = $row['Notes'];

	$designation = $row['Designation'];
	$description = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
	$sql = "SELECT * FROM externalproductnumberreference WHERE ProductNumberInternal = '$ProductNumberInternal'";
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	$external_number='';
	if ( $c > 0 ) {
		$row = mysql_fetch_array($result);
		$external_number = $row['ProductNumberExternal'];
	}
	if ( $MostRecentVendorID > 0 ) {
		$sql = "SELECT * FROM vendors WHERE vendor_id = $MostRecentVendorID";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		$c = mysql_num_rows($result);
		$external_number='';
		if ( $c > 0 ) {
			$row = mysql_fetch_array($result);
			$MostRecentVendorName = $row['name'];
		}
	}
	//$New_ProductNumberInternal = $row['New_ProductNumberInternal'];
	//$Delete_This_REC = $row['Delete_This_REC'];
	//$New_Designation = $row['New_Designation'];
	//$OLD_ProductNumberInternal = $row['OLD_ProductNumberInternal'];
	//$OLD_Designation = $row['OLD_Designation'];

}



$product_types = array("", "O.S.", "PLATED", "S.D.", "W.S.");

$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

include("inc_header.php");
?>
<script type="text/javascript">
<!--
$(document).ready(function(){
	
	$(":submit").click(function() {
		$("#action").val(this.name);
		switch (this.name)
		{
			case 'new':
				popup('pop_add_product.php',800,900);
				return false;
				break;
			case 'unhide_search':
				$("#search_div").toggle("normal");
				if ("Search" == $("#unhide_search").val())
				{
					$("#unhide_search").val("Hide Search Box");
				}
				else
				{
					$("#unhide_search").val("Search");
				}
				return false;
				break;
			case 'cancel':
				window.location = "flavors_materials.php?action=search&<?php 
					if (0 != strlen($external_number_search)) {
						echo "external_number_search=$external_number_search";
					}
					else if (0 != strlen($designation_search)) {
						echo "designation_search=$designation_search";
					}
					else if (0 != strlen($internal_number_search)) {
						echo "internal_number_search=$internal_number_search";
					}
					else if (0 != strlen($keyword_search)) {
						echo "keyword_search=$keyword_search";
					}
?>";
				return false;
				break;
			case 'Formula_Listing':
				return false;
				break;
			default:
				//alert ("this button not yet supported");
				break;
		}
	});
	
	$("#designation_search").autocomplete("search/product_master_by_designation.php", {
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
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max: 50,
		multipleSeparator: "¬",
		scrollHeight: 350,
		selectFirst: false
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
	$("#internal_number_search").autocomplete("search/product_master_by_internal_number.php", {
		cacheLength: 1,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max: 50,
		multipleSeparator: "¬",
		scrollHeight: 350,
		selectFirst: false
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
	$("#keyword_search").autocomplete("search/product_master_by_keyword.php", {
		cacheLength: 1,
		matchContains: true,
		mustMatch: false,
		minChars: 0,
		width: 350,
		max: 50,
		multipleSeparator: "¬",
		scrollHeight: 350,
		selectFirst: false
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
	$("#MostRecentVendorName").autocomplete("search/vendors.php", {
		cacheLength: 1,
		matchContains: true,
		mustMatch: true,
		minChars: 0,
		width: 350,
		max: 50,
		multipleSeparator: "¬",
		scrollHeight: 350,
		selectFirst: false
	});
	$("#MostRecentVendorName").result(function(event, data, formatted) {
		if (data)
			$("#MostRecentVendorID").val(data[1]);
	});
	$("#MostRecentVendorName").bind("onkeypress", function() { if (window.event.keyCode == 13) { setTimeout( function() { $('#edit').submit() }, 500 ) } })
});

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
// -->
</script>



<script type="text/javascript">
$(function() {
	$('#datepicker').datepicker({
		changeMonth: true,
		changeYear: true
	});
});
</script>



<?php if ( $action == 'search' or $action == '' ) { ?>


<div id="search_div"  <?php echo ("edit"==$action ? "style=\"display:none\"": "" ) ?> >
<TABLE class="bounding">
<TR VALIGN=TOP>
<TD class="padded">
<FORM id="search" name="search" ACTION="flavors_materials.php" METHOD="get">
<INPUT TYPE="hidden" id="action" NAME="action" VALUE="search">


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">

		<TR>
			<TD><B>Material designation:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="designation_search" NAME="designation_search" VALUE="<?php echo $designation_search;?>" SIZE="30"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Abelei number (external):</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="external_number_search" NAME="external_number_search" VALUE="<?php echo $external_number_search;?>" SIZE="30"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Material number (internal):</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="internal_number_search" NAME="internal_number_search" VALUE="<?php echo $internal_number_search;?>" SIZE="30"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
		</TR>

		<TR>
			<TD><B>Keywords:</B></TD>
			<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="10" HEIGHT="1"></TD>
			<TD><INPUT TYPE="text" id="keyword_search" NAME="keyword_search" VALUE="<?php echo $keyword_search;?>" SIZE="30"></TD>
		</TR>

		<TR>
			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="12"></TD>
		</TR>

	<TR>
		<TD style="text-align:left;"  valign="bottom" colspan="5">
			<INPUT style="float:right" name="search" id="search" TYPE="submit" class="submit_medium" VALUE="Search"><INPUT style="margin-top:.5em" name="new" id="new" TYPE="submit" class="submit new" VALUE="New Material">
		</TD>
	</TR>

</TABLE>
</FORM>
</TD></TR></TABLE>
</div>
<BR>


<?php } ?>



<?php 
if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
	unset($error_found);
}

 if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} 

if ( $action == 'search' ) {

	$clause = "";

	if ( $designation_search != '' ) {
		$clause = " AND ( ( Designation ) LIKE '%" . str_replace("'","''",$designation_search) . "%' )";
	} else
	if ( $external_number_search != '' ) {
		$clause = " AND ( ( ProductNumberExternal ) LIKE '%" . str_replace("'","''",$external_number_search) . "%' )";
	} else 
	if ( $internal_number_search != '' ) {
		$clause = " AND ( ( ProductMaster.ProductNumberInternal ) LIKE '%" . str_replace("'","''",$internal_number_search) . "%' )";
	} else 
	if ( $keyword_search != '' ) {
		$clause = " AND ( ( ProductMaster.Keywords ) LIKE '%" . str_replace("'","''",$keyword_search) . "%' )";
	} 
	$sql = "SELECT ProductMaster.ProductNumberInternal, ProductMaster.SpecificGravity, ProductMaster.SpecificGravityUnits, ProductMaster.Organic, ";
	$sql .= "ProductMaster.Natural_OR_Artificial, ProductMaster.Designation, ProductMaster.ProductType, ProductMaster.Kosher, ExternalProductNumberReference.ProductNumberExternal ";
	$sql .= "FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal ";
	$sql .= "WHERE ( 1 $clause ) ";
	$sql .= "ORDER BY ProductMaster.ProductNumberInternal";
	
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$c = mysql_num_rows($result);
	//echo $sql . "<BR>";

	if ( $c > 0 ) {
		$bg = 0; ?>

		<FORM id="edit">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" width="100%">

			<TR VALIGN=BOTTOM>
				<TD><B>Internal Number</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Abelei Number (External)</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD width="350"><B>Description</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Organic</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>Specific Gravity</B></TD>
				<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
				<TD><B>SG Units</B></TD>
				<TD COLSPAN=6><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
			</TR>

			<TR>
				<TD COLSPAN=13><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="7"></TD>
			</TR>

			<?php 

			while ( $row = mysql_fetch_array($result) ) {
				$description = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
				if ( $bg == 1 ) {
					$bgcolor = "#F3E7FD";
					$bg = 0;
				} else {
					$bgcolor = "whitesmoke";
					$bg = 1;
				} ?>

				<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
					<TD><?php echo $row['ProductNumberInternal'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['ProductNumberExternal'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><NOBR><?php echo $description ?></NOBR></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><input type=checkbox disabled <?php echo (1 == $row['Organic'] ? "CHECKED": "") ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo number_format($row['SpecificGravity'],2) ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><?php echo $row['SpecificGravityUnits'] ?></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><INPUT TYPE="button" VALUE="Edit" CLASS="submit" onClick="window.location='flavors_materials.php?action=edit&ProductNumberInternal=<?php echo $row['ProductNumberInternal']?>'" STYLE="font-size:7pt"></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><INPUT TYPE="button" VALUE="Inventory by Location" onClick="popup('reports/inventory_reports_by_location.php?pni=<?php echo $row['ProductNumberInternal'];?>')" CLASS="submit"></TD>
					<TD><IMG SRC="images/spacer.gif" WIDTH="7" HEIGHT="1"></TD>
					<TD><INPUT TYPE="button" VALUE="Inventory Movements List" onClick="popup('reports/inventory_reports_movements_list.php?pni=<?php echo $row['ProductNumberInternal'];?>')" CLASS="submit"></TD>
				</TR>

			<?php } ?>

		</TABLE>
		</FORM><BR><BR>

	<?php } else {
		echo "No matches found in database<BR>";
	}
}

elseif ( $pni != '' ) {

	if ( $AllergenEgg == 1 ) {
		$AllergenEggStatus = "CHECKED";
	} else {
		$AllergenEggStatus = "";
	}
	if ( $AllergenMilk == 1 ) {
		$AllergenMilkStatus = "CHECKED";
	} else {
		$AllergenMilkStatus = "";
	}
	if ( $AllergenPeanut == 1 ) {
		$AllergenPeanutStatus = "CHECKED";
	} else {
		$AllergenPeanutStatus = "";
	}
	if ( $AllergenSeafood == 1 ) {
		$AllergenSeafoodStatus = "CHECKED";
	} else {
		$AllergenSeafoodStatus = "";
	}
	if ( $AllergenSeed == 1 ) {
		$AllergenSeedStatus = "CHECKED";
	} else {
		$AllergenSeedStatus = "";
	}
	if ( $AllergenSoybean == 1 ) {
		$AllergenSoybeanStatus = "CHECKED";
	} else {
		$AllergenSoybeanStatus = "";
	}
	if ( $AllergenSulfites == 1 ) {
		$AllergenSulfitesStatus = "CHECKED";
	} else {
		$AllergenSulfitesStatus = "";
	}
	if ( $AllergenTreeNuts == 1 ) {
		$AllergenTreeNutsStatus = "CHECKED";
	} else {
		$AllergenTreeNutsStatus = "";
	}
	if ( $AllergenWheat == 1 ) {
		$AllergenWheatStatus = "CHECKED";
	} else {
		$AllergenWheatStatus = "";
	}
	if ( $AllergenYellow == 1 ) {
		$AllergenYellowStatus = "CHECKED";
	} else {
		$AllergenYellowStatus = "";
	}
	
	if ( $CurrentSellingItem == 1 ) {
		$CurrentSellingItemStatus = "CHECKED";
	} else {
		$CurrentSellingItemStatus = "";
	}
	if ( $FinalProductNotCreatedByAbelei == 1 ) {
		$FinalProductNotCreatedByAbeleiStatus = "CHECKED";
	} else {
		$FinalProductNotCreatedByAbeleiStatus = "";
	}
	if ( $HazardousPolymerization == 1 ) {
		$HazardousPolymerizationStatus = "CHECKED";
	} else {
		$HazardousPolymerizationStatus = "";
	}
	if ( $Intermediary == 1 ) {
		$IntermediaryStatus = "CHECKED";
	} else {
		$IntermediaryStatus = "";
	}
	if ( $Organic == 1 ) {
		$OrganicStatus = "CHECKED";
	} else {
		$OrganicStatus = "";
	}
	if ( $RestrictedAccess == 1 ) {
		$RestrictedAccessStatus = "CHECKED";
	} else {
		$RestrictedAccessStatus = "";
	}
	if ( $Stability == 1 ) {
		$StabilityStatus = "CHECKED";
	} else {
		$StabilityStatus = "";
	}
	if ( $VerifiedYN == 1 ) {
		$VerifiedYNStatus = "CHECKED";
	} else {
		$VerifiedYNStatus = "";
	}

?>

		<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
		<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
		<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
		<FORM id="edit" name="edit" ACTION="flavors_materials.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="action" VALUE="save">
		<INPUT TYPE="hidden" NAME="ProductNumberInternal" VALUE="<?php echo $ProductNumberInternal;?>">
		<TABLE class="bounding" style="margin-bottom:2em" >
			<TR>
				<TD><B>abelei Number:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" id="external_number" NAME="external_number" READONLY VALUE="<?php echo $external_number;?>" SIZE="20"></TD>
				<td><img src="images/spacer.gif" alt="spacer" width="15" border="0" height="1"></td>
				<TD><B>Description:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" id="description" NAME="description" READONLY VALUE="<?php echo $description;?>" SIZE="54"></TD>
				<td><img src="images/spacer.gif" alt="spacer" width="15" border="0" height="1"></td>
				<TD><B>Internal#:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" id="internal_number" NAME="internal_number" READONLY VALUE="<?php echo $pni;?>" SIZE="10"></TD>
			</tr>
		</table>
		<div id="columns_wrapper" style="overflow: auto; clear: both; width: 800px;">
		<table style="float: left;" width="400" border="0" cellpadding="0" cellspacing="0">
			<tbody><tr valign="top">
				<td align="right" style="width:150px"><B>Allergens:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD>

				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">
					<TR VALIGN=TOP>
						<TD>
						<INPUT TYPE="checkbox" NAME="AllergenEgg" id="AllergenEgg" VALUE="1" CLASS="checkbox" <?php echo $AllergenEggStatus;?>><label for="AllergenEgg">Egg</label><BR>
						<INPUT TYPE="checkbox" NAME="AllergenSulfites" id="AllergenSulfites" VALUE="1" CLASS="checkbox" <?php echo $AllergenSulfitesStatus;?>><label for="AllergenSulfites">Sulfites</label><BR>
						<NOBR><INPUT TYPE="checkbox" NAME="AllergenSeafood" id="AllergenSeafood" VALUE="1" CLASS="checkbox" <?php echo $AllergenSeafoodStatus;?>><label for="AllergenSeafood">Seafood</label></NOBR><BR>
						<INPUT TYPE="checkbox" NAME="AllergenYellow" id="AllergenYellow" VALUE="1" CLASS="checkbox" <?php echo $AllergenYellowStatus;?>><label for="AllergenYellow">Yellow</label>
						</TD>
						<TD>
						<INPUT TYPE="checkbox" NAME="AllergenPeanut" id="AllergenPeanut" VALUE="1" CLASS="checkbox" <?php echo $AllergenPeanutStatus;?>><label for="AllergenPeanut">Peanut</label><BR>
						<INPUT TYPE="checkbox" NAME="AllergenWheat" id="AllergenWheat" VALUE="1" CLASS="checkbox" <?php echo $AllergenWheatStatus;?>><label for="AllergenWheat">Wheat</label><BR>
						<NOBR><INPUT TYPE="checkbox" NAME="AllergenSoybean" id="AllergenSoybean" VALUE="1" CLASS="checkbox" <?php echo $AllergenSoybeanStatus;?>><label for="AllergenSoybean">Soybean</label></NOBR>
						</TD>
						<TD>
						<INPUT TYPE="checkbox" NAME="AllergenSeed" VALUE="1" id="AllergenSeed" CLASS="checkbox" <?php echo $AllergenSeedStatus;?>><label for="AllergenSeed">Seed</label><BR>
						<INPUT TYPE="checkbox" NAME="AllergenMilk" VALUE="1" id="AllergenMilk" CLASS="checkbox" <?php echo $AllergenMilkStatus;?>><label for="AllergenMilk">Milk</label><BR>
						<NOBR><INPUT TYPE="checkbox" NAME="AllergenTreeNuts" VALUE="1" id="AllergenTreeNuts" CLASS="checkbox" <?php echo $AllergenTreeNutsStatus;?>><label for="AllergenTreeNuts">Tree nuts</label></NOBR>
						</TD>
					</TR>
				</TABLE>

				</TD>
			</TR>

			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Notes:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><TEXTAREA NAME="Notes" CLASS="textarea"><?php echo $Notes;?></TEXTAREA></TD>
			</TR>

			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Appearance:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><TEXTAREA NAME="Appearance" CLASS="textarea"><?php echo $Appearance;?></TEXTAREA></TD>
			</TR>

			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Ash:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Ash" VALUE="<?php echo $Ash;?>" SIZE="27"></TD>
			</TR>

			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Batch size:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="BatchSize" VALUE="<?php echo $BatchSize;?>" SIZE="27"></TD>
			</TR>

			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Batch size (kg):</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="BatchSizeKg" VALUE="<?php echo $BatchSizeKg;?>" SIZE="27"></TD>
			</TR>

			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Biotin:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Biotin" VALUE="<?php echo $Biotin;?>" SIZE="27"></TD>
			</TR>

			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Boiling point:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="BoilingPoint" VALUE="<?php echo $BoilingPoint;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Calcium:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Calcium" VALUE="<?php echo $Calcium;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Calories:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Calories" VALUE="<?php echo $Calories;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Calories From Fat:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="CaloriesFromFat" VALUE="<?php echo $CaloriesFromFat;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Cholesterol:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Cholesterol" VALUE="<?php echo $Cholesterol;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Copper:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Copper" VALUE="<?php echo $Copper;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B><label for="CurrentSellingItem">Current Selling Item:</label></B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="checkbox" NAME="CurrentSellingItem" id="CurrentSellingItem" VALUE="1" CLASS="checkbox" <?php echo $CurrentSellingItemStatus;?>></TD>
			</TR>

			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Date of Formulation:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" SIZE="12" NAME="DateOfFormulation" id="datepicker" VALUE="<?php
				if ( $DateOfFormulation != '' ) {
					echo date("m/d/Y", strtotime($DateOfFormulation));
				}
				?>"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Designation:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Designation" VALUE="<?php echo $Designation;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Developer:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><SELECT NAME="DeveloperID">
				<OPTION VALUE=''></OPTION>
				<?php
				$sql = "SELECT user_id, first_name, last_name FROM users WHERE user_type = 3";
				$result_dev = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
				while ( $row_dev = mysql_fetch_array($result_dev) ) {
					if ( $DeveloperID == $row_dev['user_id'] ) {
						echo "<OPTION VALUE='" . $row_dev['user_id'] . "' SELECTED>" . $row_dev['first_name'] . ' ' . $row_dev['last_name'] . "</OPTION>";
					} else {
						echo "<OPTION VALUE='" . $row_dev['user_id'] . "'>" . $row_dev['first_name'] . ' ' . $row_dev['last_name'] . "</OPTION>";
					}
				}
				?>
				</SELECT></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Dietary Fiber:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="DietaryFiber" VALUE="<?php echo $DietaryFiber;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Emergency First Aid Procedure:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="EmergencyFirstAidProcedure" VALUE="<?php echo $EmergencyFirstAidProcedure;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Evaporation Rate:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="EvaporationRate" VALUE="<?php echo $EvaporationRate;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Extinguishing Media:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="ExtinguishingMedia" VALUE="<?php echo $ExtinguishingMedia;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Fat Calories:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="FatCalories" VALUE="<?php echo $FatCalories;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>FEMA_NBR:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="FEMA_NBR" VALUE="<?php echo $FEMA_NBR;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B><label for="FinalProductNotCreatedByAbelei">Final Product Not Created by Abelei:</label></B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="checkbox" NAME="FinalProductNotCreatedByAbelei" id="FinalProductNotCreatedByAbelei" VALUE="1" CLASS="checkbox" <?php echo $FinalProductNotCreatedByAbeleiStatus;?>></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Flammable Limits:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="FlammableLimits" VALUE="<?php echo $FlammableLimits;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Flashpoint:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Flashpoint" VALUE="<?php echo $Flashpoint;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Flavor and Aroma:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="FlavorAndAroma" VALUE="<?php echo $FlavorAndAroma;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Folate:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Folate" VALUE="<?php echo $Folate;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Folate Folacin Folic:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="FolateFolacinFolicAdic" VALUE="<?php echo $FolateFolacinFolicAdic;?>" SIZE="27"></TD>
			</TR>
			<TR>
				<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>General Description of Formulation</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="GeneralDescriptionOfFormulation" VALUE="<?php echo $GeneralDescriptionOfFormulation;?>" SIZE="27"></TD>
			</TR>
			<TR>

			<TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD>
			</TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>GMO:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="GMO" VALUE="<?php echo $GMO;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Halal:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Halal" VALUE="<?php echo $Halal;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Hazard:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Hazard" VALUE="<?php echo $Hazard;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Hazardous Components:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="HazardousComponents" VALUE="<?php echo $HazardousComponents;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Hazardous Decomposition:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="HazardousDecomposition" VALUE="<?php echo $HazardousDecomposition;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B><label for="HazardousPolymerization">Hazardous Polymerization:</label></B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="checkbox" NAME="HazardousPolymerization" id="HazardousPolymerization" VALUE="1" CLASS="checkbox" <?php echo $HazardousPolymerizationStatus;?>></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Hazardous Polymerization Conditions:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="HazardousPolymerizationConditions" VALUE="<?php echo $HazardousPolymerizationConditions;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Health Hazards:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="HealthHazards" VALUE="<?php echo $HealthHazards;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Incompatibility:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Incompatibility" VALUE="<?php echo $Incompatibility;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Insoluble Fiber:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="InsolubleFiber" VALUE="<?php echo $InsolubleFiber;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B><label for="Intermediary">Intermediary:</label></B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="checkbox" NAME="Intermediary" id="Intermediary" VALUE="1" CLASS="checkbox" <?php echo $IntermediaryStatus;?>></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Inventory Units:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><select class="input-box" id="inventory_units" name="inventory_units"><?php printInternalInventoryUnitsOptions($pni,$inventory_units);	?></select></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Iodine:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Iodine" VALUE="<?php echo $Iodine;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Iron:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Iron" VALUE="<?php echo $Iron;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Keywords:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Keywords" VALUE="<?php echo $Keywords;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Kosher:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><select id="Kosher" name="Kosher">
			<?php 
				printKosherOptions($Kosher);
			?>
			</select></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Kosher Status:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="KosherStatus" VALUE="<?php echo $KosherStatus;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Label Declaration:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="LabelDeclaration" VALUE="<?php echo $LabelDeclaration;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Lactose:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Lactose" VALUE="<?php echo $Lactose;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>LEL:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="LEL" VALUE="<?php echo $LEL;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Magnesium:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Magnesium" VALUE="<?php echo $Magnesium;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Manganese:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Manganese" VALUE="<?php echo $Manganese;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Manufacturing Instructions:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="ManufacturingInstructions" VALUE="<?php echo $ManufacturingInstructions;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Medical Conditions:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="MedicalCondition" VALUE="<?php echo $MedicalCondition;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Melting Point:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="MeltingPoint" VALUE="<?php echo $MeltingPoint;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Monounsaturated Fats:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="MonounsaturatedFat" VALUE="<?php echo $MonounsaturatedFat;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Most Recent Vendor:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD>
					<INPUT TYPE="hidden" NAME="MostRecentVendorID" id="MostRecentVendorID" VALUE="<?php echo $MostRecentVendorID ?>" />
					<input type="text" id="MostRecentVendorName" name="MostRecentVendorName" value="<?php echo $MostRecentVendorName ?>" size="27" />
				</TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

		</table>
		
		<table style="padding-left:1em; float: left;" width="350" border="0" cellpadding="0" cellspacing="0">
			<tbody>
			<TR VALIGN=TOP>
				<TD align="right"><B>Natural or Artificial:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><select id="Natural_OR_Artificial" name="Natural_OR_Artificial" >
			<?php 
				printNorAOptions($Natural_OR_Artificial);
			?>
			</select></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Niacin:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Niacin" VALUE="<?php echo $Niacin;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>
			<tr valign="top">
				<td align="right" ><B>Non Flavor:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="NonFlavorIngredients" VALUE="<?php echo $NonFlavorIngredients;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B><label for="Organic">Organic:</label></B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="checkbox" NAME="Organic" id="Organic" VALUE="1" CLASS="checkbox" <?php echo $OrganicStatus;?>></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Other Carbohydrates:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="OtherCarbohydrates" VALUE="<?php echo $OtherCarbohydrates;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Other Protective Clothing:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="OtherProtectiveClothing" VALUE="<?php echo $OtherProtectiveClothing;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Packaging:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Packaging" VALUE="<?php echo $Packaging;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Pantothenic Acid:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="PantothenicAcid" VALUE="<?php echo $PantothenicAcid;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Phosphorus:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Phosphorus" VALUE="<?php echo $Phosphorus;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Polyunsaturated Fat:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="PolyunsaturatedFat" VALUE="<?php echo $PolyunsaturatedFat;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Potassium:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Potassium" VALUE="<?php echo $Potassium;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Precautions:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Precautions" VALUE="<?php echo $Precautions;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Price of Material:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="PriceOfMaterial" VALUE="<?php echo $PriceOfMaterial;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Product Type:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><SELECT NAME="ProductType">
				<?php
				foreach ( $product_types as $value ) {
					if ( $value == $ProductType ) {
						echo "<OPTION VALUE='" . $value . "' SELECTED>" . $value . "</OPTION>";
					} else {
						echo "<OPTION VALUE='" . $value . "'>" . $value . "</OPTION>";
					}
				}
				?></SELECT>
				</TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Protective Gloves:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="ProtectiveGloves" VALUE="<?php echo $ProtectiveGloves;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Protein:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Protein" VALUE="<?php echo $Protein;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Quality Sensitive:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><select NAME="Quality_Sensitive"><option value=""/><option value="Y" <?php echo ("Y"==$Quality_Sensitive ? "selected=\"selected\"":"") ?>>Y</option><option value="N" <?php echo ("N"==$Quality_Sensitive ? "selected=\"selected\"":"") ?>>N</option></SELECT></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Quickscan:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="QuickScan" VALUE="<?php echo $QuickScan;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Refractive Index:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="RefractiveIndex" VALUE="<?php echo $RefractiveIndex;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Replaced By:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="ReplacedBy" VALUE="<?php echo $ReplacedBy;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B><label for="RestrictedAccess">Restricted Access:</label></B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="checkbox" NAME="RestrictedAccess" id="RestrictedAccess" VALUE="1" CLASS="checkbox" <?php echo $RestrictedAccessStatus;?>></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Riboflavin:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Riboflavin" VALUE="<?php echo $Riboflavin;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Saturated Fat:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="SaturatedFat" VALUE="<?php echo $SaturatedFat;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Saturated Fat Calories:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="SaturatedFatCalories" VALUE="<?php echo $SaturatedFatCalories;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Shelf Life In Months:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="ShelfLifeInMonths" VALUE="<?php echo $ShelfLifeInMonths;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Signs And Symptoms:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="SignsAndSymptoms" VALUE="<?php echo $SignsAndSymptoms;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Sodium:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Sodium" VALUE="<?php echo $Sodium;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Solubility In Water:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="SolubilityInWater" VALUE="<?php echo $SolubilityInWater;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Soluble Fiber:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="SolubleFiber" VALUE="<?php echo $SolubleFiber;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Special Firefighting Procedures:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="SpecialFirefightingProcedures" VALUE="<?php echo $SpecialFirefightingProcedures;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Specific Gravity (g/ml) :</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="SpecificGravity" VALUE="<?php echo $SpecificGravity;?>" SIZE="27"><INPUT id="SpecificGravityUnits" name="SpecificGravityUnits" TYPE="hidden" Value="g/ml" ></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B><label for="Stability">Stability:</label></B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="checkbox" NAME="Stability" id="Stability" VALUE="1" CLASS="checkbox" <?php echo $StabilityStatus;?>></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Stability Conditions:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="StabilityConditions" VALUE="<?php echo $StabilityConditions;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Steps To Be Taken:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="StepsToBeTaken" VALUE="<?php echo $StepsToBeTaken;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Storage And Shelf Life:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="StorageAndShelfLife" VALUE="<?php echo $StorageAndShelfLife;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Sugar Alcohol:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="SugarAlcohol" VALUE="<?php echo $SugarAlcohol;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Sugars:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Sugars" VALUE="<?php echo $Sugars;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Thiamin:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Thiamin" VALUE="<?php echo $Thiamin;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Total Carbohydrates:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="TotalCarbohydrates" VALUE="<?php echo $TotalCarbohydrates;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Total Fat:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="TotalFat" VALUE="<?php echo $TotalFat;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Total Solids:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="TotalSolids" VALUE="<?php echo $TotalSolids;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Trans Fatty Acids:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="TransFattyAcids" VALUE="<?php echo $TransFattyAcids;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>UEL:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="UEL" VALUE="<?php echo $UEL;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Unusual Fire:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="UnusualFire" VALUE="<?php echo $UnusualFire;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Use Level:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="UseLevel" VALUE="<?php echo $UseLevel;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Vapor Density:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="VaporDensity" VALUE="<?php echo $VaporDensity;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Vapor Pressure:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="VaporPressure" VALUE="<?php echo $VaporPressure;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Ventilator Mechanical:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="VentilatorMechanical" VALUE="<?php echo $VentilatorMechanical;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Ventilator Special:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="VentilatorSpecial" VALUE="<?php echo $VentilatorSpecial;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B><label for="VerifiedYNStatus">Verified Y/N:</label></B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="checkbox" NAME="VerifiedYNStatus" id="VerifiedYNStatus" VALUE="1" CLASS="checkbox" <?php echo $VerifiedYNStatus;?>></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Vitamin A:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="VitaminA" VALUE="<?php echo $VitaminA;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Vitamin B12:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="VitaminB12" VALUE="<?php echo $VitaminB12;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Vitamin B6:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="VitaminB6" VALUE="<?php echo $VitaminB6;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Vitamin C:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="VitaminC" VALUE="<?php echo $VitaminC;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Vitamin D:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="VitaminD" VALUE="<?php echo $VitaminD;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Vitamin E:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="VitaminE" VALUE="<?php echo $VitaminE;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Waste Disposal Method:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="WasteDisposalMethod" VALUE="<?php echo $WasteDisposalMethod;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Water:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Water" VALUE="<?php echo $Water;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Weight Per Gallon:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="WeightPerGallon" VALUE="<?php echo $WeightPerGallon;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Weight Per Gallon Units:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><select id="WeightPerGallonUnits" name="WeightPerGallonUnits"><?php printInventoryUnitsOptions($WeightPerGallonUnits); ?></select></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Work Hygenic Practices:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="WorkHygienicPractices" VALUE="<?php echo $WorkHygienicPractices;?>" SIZE="27"></TD>
			</TR>

			<TR><TD COLSPAN=3><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="1" HEIGHT="5"></TD></TR>

			<TR VALIGN=TOP>
				<TD align="right"><B>Zinc:</B></TD>
				<TD><IMG SRC="images/spacer.gif" ALT="spacer" WIDTH="5" HEIGHT="1" BORDER="0"></TD>
				<TD><INPUT TYPE="text" NAME="Zinc" VALUE="<?php echo $Zinc;?>" SIZE="27"></TD>
			</TR>

		</TABLE>
		</div>
		<INPUT style="float:right" TYPE="submit" id="save" name="save" VALUE="Save" CLASS="submit_medium"/>
		<INPUT style="float:right" TYPE="submit" id="cancel" name="cancel" VALUE="Cancel" CLASS="submit_medium"/>
<?php
// Deos this report needs to be done?
		// <IMG SRC="images/spacer.gif" WIDTH="10" HEIGHT="1" />
		// <INPUT TYPE="submit" class="submit_medium_red" name="Formula_Listing" VALUE="Formula Listing" >
?>

		</FORM>
		</TD></TR></TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE>
		<BR><BR>


<?php

}

?>

<?php include("inc_footer.php"); ?>