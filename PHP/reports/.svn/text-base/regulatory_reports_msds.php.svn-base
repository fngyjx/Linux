<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

include('../inc_global.php');

$sql = "SELECT productmaster.*, externalproductnumberreference.ProductNumberExternal
FROM productmaster LEFT JOIN externalproductnumberreference ON productmaster.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
WHERE productmaster.ProductNumberInternal = " . $_GET['pni'];
$result = mysql_query($sql, $link) or die (mysql_error());
$row = mysql_fetch_array($result);

$ProductDesignation = ("" != $row['Natural_OR_Artificial'] ? $row['Natural_OR_Artificial']." " : "").$row['Designation'].("" != $row['ProductType'] ? " - ".$row['ProductType'] : "").("" != $row['Kosher'] ? " - ".$row['Kosher'] : "");
$external_number = $row['ProductNumberExternal'];
$internal_number = $row['ProductNumberInternal'];

$hazard_info = $row['Hazard']; // None Known
if ( $hazard_info == '' ) {
	$hazard_info = "None Known";
}
$boiling_point = $row['BoilingPoint']; //N/A
if ( $boiling_point == '' ) {
	$boiling_point = "N/A";
}
$specific_gravity = number_format($row['SpecificGravity'], 2); // 0
$vapor_pressure = $row['VaporPressure']; //N/A
if ( $vapor_pressure == '' ) {
	$vapor_pressure = "N/A";
}
$melting_point = $row['MeltingPoint']; //N/A
if ( $melting_point == '' ) {
	$melting_point = "N/A";
}
$vapor_density = $row['VaporDensity']; //N/A
if ( $vapor_density == '' ) {
	$vapor_density = "N/A";
}
$evaporation_rate = $row['EvaporationRate']; //N/A
if ( $evaporation_rate == '' ) {
	$evaporation_rate = "N/A";
}
$solubility_water = $row['SolubilityInWater'];
$appearance_odor = $row['Appearance'];
$flash_point = $row['Flashpoint']; // Not Established
if ( $flash_point == '' ) {
	$flash_point = "Not Established";
}
$flammable_limits = $row['FlammableLimits'];
$lel = $row['LEL'];
$uel = $row['UEL'];
$extinguishing_media = $row['ExtinguishingMedia'];
$special_firefighting = $row['SpecialFirefightingProcedures']; // None Known
if ( $special_firefighting == '' ) {
	$special_firefighting = "None Known";
}
$unusual_fire = $row['UnusualFire']; // None Known
if ( $unusual_fire == '' ) {
	$unusual_fire = "None Known";
}
$stability = $row['Stability']; // "X"
if ( $stability == 1 ) {
	$stability_yes = "X";
	$stability_no = "";
} else {
	$stability_yes = "";
	$stability_no = "X";
}
$stability_conditions_avoid = $row['StabilityConditions'];
$incompatability = $row['Incompatibility']; // None Known
if ( $incompatability == '' ) {
	$incompatability = "None Known";
}
$hazardous_decomposition = $row['HazardousDecomposition'];
$hazardous_polymerization = $row['HazardousPolymerization']; // "X"
if ( $hazardous_polymerization == 1 ) {
	$poly_yes = "X";
	$poly_no = "";
} else {
	$poly_yes = "";
	$poly_no = "X";
}
$hazardous_conditions_avoid = $row['HazardousPolymerizationConditions'];
$route_entry = "";
$inhalation = "";
$skin = "";
$ingestion = "";
$health_hazards = $row['HealthHazards']; // None Known
if ( $health_hazards == '' ) {
	$health_hazards = "None Known";
}
$carcinogenicity = "";
$ntp = "";
$iarc = "";
$osha = "";
$signs_symptoms = $row['SignsAndSymptoms'];
$medical_conditions = $row['MedicalCondition']; // None Known
if ( $medical_conditions == '' ) {
	$medical_conditions = "None Known";
}
$emergency_procedures = $row['EmergencyFirstAidProcedure'];
$steps_spilled = $row['StepsToBeTaken'];
$waste_disposal = $row['WasteDisposalMethod'];
$precautions = $row['Precautions'];
$other_precautions = "";
$respiratory_protection = "";
$ventilation = "";
$local_exhaust = "";
$special = "";
$mechanical = "";
$other = "";
$protective_gloves = $row['ProtectiveGloves'];
$other_protective = $row['OtherProtectiveClothing'];
$work_hygienic = $row['WorkHygienicPractices'];

?>

<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="../styles.css" REL="stylesheet">
</HEAD>

<BODY BGCOLOR="#FFFFFF" STYLE="margin:0" onLoad="window.print()"><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="680" ALIGN=CENTER><TR><TD>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR>
		<TD WIDTH="60"><IMG SRC="../images/abelei_logo.png" WIDTH="60" BORDER="0"></TD>
		<TD WIDTH="10"><IMG SRC="../images/spacer.gif" WIDTH="10" BORDER="0"></TD>
		<TD WIDTH="80" VALIGN=MIDDLE STYLE="font-size:8pt"><NOBR>clever able capable</NOBR><BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<IMG SRC="../images/abelei_font.png" WIDTH="80" BORDER="0"><!-- <BR>
		<IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="7" BORDER="0"><BR>
		<SPAN STYLE="font-size:7pt">US INGREDIENTS</STYLE> --></TD>
	</TR>
</TABLE><BR>



<TABLE BORDER="0" WIDTH="680" HEIGHT="750" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="680">
	<TR VALIGN=TOP>
		<TD WIDTH="340">
		<B>Material Safety Data Sheet</B><BR>
		May be used to comply with<BR>
		OSHA's Hazard Communication Standard, <BR>
		29 CFR 1910.1200. Standard must be<BR>
		consulted for specific requirements.
	    </TD>
		<TD WIDTH="340">
		<B>U.S. Department of Labor</B><BR>
		Occupational Safety and Health Administration <BR>
		(Non-Mandatory Form)<BR>
		Form Approved <BR>
		OMB No. 1218-0072
	</TR>
</TABLE><BR>


<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3" WIDTH="680">
	<TR VALIGN=TOP>
		<TD WIDTH="340"><B CLASS="black" STYLE="font-size:8pt">IDENTITY</B> <I STYLE="font-size:8pt">(As Used on Label and List)</I><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $ProductDesignation . " (abelei# " . $external_number . ")";?></TD>
		<TD WIDTH="340"><I STYLE="font-size:8pt">Note: Blank spaces are not permitted.  If any item is not applicable, or no information is available, the space must be marked to indicate that.</I></TD>
	</TR>
</TABLE><BR>



<B>Section I</B><BR>

<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3" WIDTH="680">
	<TR VALIGN=TOP>
		<TD WIDTH="340"><B CLASS="black" STYLE="font-size:8pt">Manufacturer's Name</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		Abelei/U.S. Ingredients, Inc.</TD>
		<TD WIDTH="340"><B CLASS="black" STYLE="font-size:8pt">Emergency Telephone Number</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		Chemtrac 800-924-9300</TD>
	</TR>
	<TR VALIGN=TOP>
		<TD WIDTH="340" ROWSPAN=3><B CLASS="black" STYLE="font-size:8pt">Address</B> <I STYLE="font-size:8pt">(Number, Street, City, State, and ZIP Code)</I><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		194 Alder Drive<BR>
		North Aurora, IL 60542</TD>
		<TD WIDTH="340"><B CLASS="black" STYLE="font-size:8pt">Telephone Number for Information</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		630.859.1410</TD>
	</TR>
	<TR VALIGN=TOP>
		<TD WIDTH="340"><B CLASS="black" STYLE="font-size:8pt">Date Prepared</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo date("j F Y");?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD WIDTH="340"><B CLASS="black" STYLE="font-size:8pt">Signature of Preparer</B> <I STYLE="font-size:8pt">(optional)</I><BR><IMG SRC="../images/spacer.gif" HEIGHT="19"><BR></TD>
	</TR>
</TABLE><BR>



<B>Section II - Hazardous Ingredients/Identity Information</B><BR>

<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3" WIDTH="680">
	<TR VALIGN=TOP>
		<TD WIDTH="300"><B CLASS="black" STYLE="font-size:8pt">Hazardous Components (Specific Chemical Identity; Common Name(s))</B></TD>
		<TD WIDTH="75"><B CLASS="black" STYLE="font-size:8pt">OSHA PEL</B></TD>
		<TD WIDTH="75"><B CLASS="black" STYLE="font-size:8pt">ACGIH TLV</B></TD>
		<TD WIDTH="85"><B CLASS="black" STYLE="font-size:8pt">Other Limits Recommended</B></TD>
		<TD WIDTH="75"><B CLASS="black" STYLE="font-size:8pt">%</B> <I STYLE="font-size:8pt">(optional)</I></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD COLSPAN="5"><IMG SRC="../images/spacer.gif" HEIGHT="19"><BR>
		<?php echo $hazard_info;?>
		</TD>
	</TR>
</TABLE><BR>



<B>Section III - Physical/Chemical Characteristics</B><BR>

<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3" WIDTH="680">
	<TR VALIGN=TOP>
		<TD WIDTH="265"><B CLASS="black" STYLE="font-size:8pt">Boiling Point</B></TD>
		<TD WIDTH="75"><?php echo $boiling_point;?></TD>
		<TD WIDTH="265"><B CLASS="black" STYLE="font-size:8pt">Specific Gravity (H<SUB>2</SUB>O = 1)</B></TD>
		<TD WIDTH="75"><?php echo $specific_gravity;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD><B CLASS="black" STYLE="font-size:8pt">Vapor Pressure (mm Hg)</B></TD>
		<TD><?php echo $vapor_pressure;?></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Melting Point</B></TD>
		<TD><?php echo $melting_point;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD><B CLASS="black" STYLE="font-size:8pt">Vapor Density (AIR = 1)</B></TD>
		<TD><?php echo $vapor_density;?></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Evaporation Rate (Butyl Acetate = 1)</B></TD>
		<TD><?php echo $evaporation_rate;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD COLSPAN="4"><B CLASS="black" STYLE="font-size:8pt">Solubility in Water</B> 
		<?php echo $solubility_water;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD COLSPAN="4"><B CLASS="black" STYLE="font-size:8pt">Appearance and Odor</B> 
		<?php echo $appearance_odor;?></TD>
	</TR>
</TABLE><BR>

<DIV style='page-break-after: always'>

<B>Section IV - Fire and Explosion Hazard Data</B><BR>

<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3" WIDTH="680">
	<TR VALIGN=TOP>
		<TD WIDTH="265"><B CLASS="black" STYLE="font-size:8pt">Flash Point (Method Used)</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $flash_point;?></TD>
		<TD WIDTH="170"><B CLASS="black" STYLE="font-size:8pt">Flammable Limits</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $flammable_limits;?></TD>
		<TD WIDTH="135"><B CLASS="black" STYLE="font-size:8pt">LEL</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $lel;?></TD>
		<TD WIDTH="75"><B CLASS="black" STYLE="font-size:8pt">UEL</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $uel;?></TD>
	</TR>
	<TR>
		<TD COLSPAN="4"><B CLASS="black" STYLE="font-size:8pt">Extinguishing Media</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $extinguishing_media;?></TD>
	</TR>
	<TR>
		<TD COLSPAN="4"><B CLASS="black" STYLE="font-size:8pt">Special Fire Fighting Procedures</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $special_firefighting;?></TD>
	</TR>
	<TR>
		<TD COLSPAN="4"><B CLASS="black" STYLE="font-size:8pt">Unusual Fire and Explosion Hazards</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $unusual_fire;?></TD>
	</TR>
</TABLE><BR>

</DIV>

<BR><BR>

<B>Section V - Reactivity Data</B><BR>

<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3" WIDTH="680">
	<TR VALIGN=TOP>
		<TD WIDTH="125" ROWSPAN=2><B CLASS="black" STYLE="font-size:8pt">Stability</B></TD>
		<TD WIDTH="125"><B CLASS="black" STYLE="font-size:8pt">Unstable</B></TD>
		<TD WIDTH="25"><?php echo $stability_no;?>&nbsp;</TD>
		<TD WIDTH="405" ROWSPAN=2><B CLASS="black" STYLE="font-size:8pt">Conditions to Avoid</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $stability_conditions_avoid;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD><B CLASS="black" STYLE="font-size:8pt">Stable</B></TD>
		<TD><?php echo $stability_yes;?>&nbsp;</TD>
	</TR>
	<TR VALIGN=TOP>
		<TD COLSPAN="4"><B CLASS="black" STYLE="font-size:8pt">Incompatibility</B> <I STYLE="font-size:8pt">(Materials to Avoid)</I><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $incompatability;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD COLSPAN="4"><B CLASS="black" STYLE="font-size:8pt">Hazardous Decomposition or Byproducts</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $hazardous_decomposition;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD ROWSPAN=2><B CLASS="black" STYLE="font-size:8pt">Hazardous<BR>Polymerization</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">May Occur</B></TD>
		<TD><?php echo $poly_yes;?>&nbsp;</TD>
		<TD ROWSPAN=2><B CLASS="black" STYLE="font-size:8pt">Conditions to Avoid</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $hazardous_conditions_avoid;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD><B CLASS="black" STYLE="font-size:8pt">Will Not Occur</B></TD>
		<TD><?php echo $poly_no;?>&nbsp;</TD>
	</TR>
</TABLE><BR>



<B>Section VI - Health Hazard Data</B><BR>

<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3" WIDTH="680">
	<TR VALIGN=TOP>
		<TD WIDTH="155"><B CLASS="black" STYLE="font-size:8pt">Route(s) of Entry:</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $route_entry;?></TD>
		<TD WIDTH="150"><B CLASS="black" STYLE="font-size:8pt">Inhalation?</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $inhalation;?></TD>
		<TD WIDTH="150"><B CLASS="black" STYLE="font-size:8pt">Skin?</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $skin;?></TD>
		<TD WIDTH="155"><B CLASS="black" STYLE="font-size:8pt">Ingestion?</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $ingestion;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD COLSPAN="4"><B CLASS="black" STYLE="font-size:8pt">Health Hazards</B> <I STYLE="font-size:8pt">(Acute and Chronic)</I><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $health_hazards;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD WIDTH="155"><B CLASS="black" STYLE="font-size:8pt">Carcinogenicity:</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $carcinogenicity;?></TD>
		<TD WIDTH="150"><B CLASS="black" STYLE="font-size:8pt">NTP?</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $ntp;?></TD>
		<TD WIDTH="150"><B CLASS="black" STYLE="font-size:8pt">IARC Monographs?</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $iarc;?></TD>
		<TD WIDTH="155"><B CLASS="black" STYLE="font-size:8pt">OSHA Regulated?</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $osha;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD COLSPAN="4"><B CLASS="black" STYLE="font-size:8pt">Signs and Symptoms of Exposure</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $signs_symptoms;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD COLSPAN="4"><B CLASS="black" STYLE="font-size:8pt">Medical Conditions Generally Aggravated by Exposure</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $medical_conditions;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD COLSPAN="4"><B CLASS="black" STYLE="font-size:8pt">Emergency and First Aid Procedures</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $emergency_procedures;?></TD>
	</TR>
</TABLE><BR>



<B>Section VII - Precautions for Safe Handling and Use</B><BR>

<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3" WIDTH="680">
	<TR VALIGN=TOP>
		<TD><B CLASS="black" STYLE="font-size:8pt">Steps to Be Taken in Case Material is Released or Spilled</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $steps_spilled;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD><B CLASS="black" STYLE="font-size:8pt">Waste Disposal Method</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $waste_disposal;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD><B CLASS="black" STYLE="font-size:8pt">Precautions to Be taken in Handling and Storing</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $precautions;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD VALING="TOP"><B CLASS="black" STYLE="font-size:8pt">Other Precautions</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $other_precautions;?></TD>
	</TR>
</TABLE><BR>



<B>Section VIII - Control Measures</B><BR>

<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="3" WIDTH="680">
	<TR VALIGN=TOP>
		<TD COLSPAN="3"><B CLASS="black" STYLE="font-size:8pt">Respiratory Proctection</B> <I STYLE="font-size:8pt">(Specify Type)</I><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $respiratory_protection;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD WIDTH="110"><B CLASS="black" STYLE="font-size:8pt">Ventilation</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $ventilation;?></TD>
		<TD WIDTH="250"><B CLASS="black" STYLE="font-size:8pt">Local Exhaust</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $local_exhaust;?></TD>
		<TD WIDTH="250"><B CLASS="black" STYLE="font-size:8pt">Special</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $special;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD>&nbsp;</TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Mechanical</B> <I STYLE="font-size:8pt">(General)</I><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $mechanical;?></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Other</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $other;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD COLSPAN="2"><B CLASS="black" STYLE="font-size:8pt">Protective Gloves</B></TD>
		<TD><B CLASS="black" STYLE="font-size:8pt">Eye Protection</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $protective_gloves;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD COLSPAN="3"><B CLASS="black" STYLE="font-size:8pt">Other Protective Clothing or Equipment</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $other_protective;?></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD COLSPAN="3"><B CLASS="black" STYLE="font-size:8pt">Work/Hygienic Practices</B><BR><IMG SRC="../images/spacer.gif" HEIGHT="4"><BR>
		<?php echo $work_hygienic;?></TD>
	</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" WIDTH="680">
	<TR VALIGN=TOP>
		<TD ALIGN="right"><I STYLE="font-size:7pt">* U.S.G.P.O.: 1986 - 491 - 529/45775</I></TD>
	</TR>
</TABLE>



		</TD>
	</TR>
</TABLE>


<BR>
<DIV STYLE="font-size:8pt" ALIGN="CENTER">
194 Alder Drive |
North Aurora, Illinois 60542 |
t-630.859.1410 |
f-630.859.1448 |
<SPAN STYLE="color: blue">toll-free 866-4-abelei</SPAN>
</DIV>
 

</TD></TR></TABLE>

</BODY>
</HTML>