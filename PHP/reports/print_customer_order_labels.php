<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: ../login.php?out=1");
	exit;
}

// ADMIN AND FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 4 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( $_REQUEST['order_num'] != '' ) {
	$order_num = $_REQUEST['order_num'];
}

include('../inc_global.php');
include('../search/system_defaults.php');


if ( ! empty ( $_POST ) ) {

//print_r($_POST);
// print label(3)
$label_len=1300;
$product_name_mlen=20;  //48pt
$product_name_wrd_w=65;

$description_mlen=28; // 33pt
$description_wrd_w=46;

$StorageNote_mlen=69; // 15pt
$StorageNote_wrd_w=18;

$useNote_mlen=50; //19pt
$useNote_wrd_w=26;

$customerPO_mlen=49; //18pt
$customerPO_wrd_w=26;


$fileName="LabelsTmp.txt";
if ( file_exists($fileName) )
	unlink($fileName) or die ( " Failed to delete the file $fileName");

$fh=fopen($fileName,"w") or die ( "Failed open file $fileName");


	for ( $i=0; $i<$_POST['numOfPrd'] ; $i++) { 
		if ( ! isset($_POST['prt_label_'.$i]) )
			continue;
		$product_name = escape_data($_POST['product_name_'.$i]);
		$product_name_len=strlen($product_name);
		$product_name_y=($product_name_mlen-$product_name_len)*.5*$product_name_wrd_w;
		
		$description=escape_data($_POST['description_'.$i]);
		$description_len=strlen($description);
		$description_y=($description_mlen-$description_len)*.5*$description_wrd_w;
		
		$StorageNote = escape_data($_POST['StorageNote_'.$i]);
		$StorageNote_len=strlen($StorageNote);
		$StorageNote_y=($StorageNote_mlen-$StorageNote_len)*.5*$StorageNote_wrd_w;
	
		$useNote = escape_data($_POST['useNote_'.$i]);
		$useNote_len=strlen($useNote);
		$useNote_y=($useNote_mlen-$useNote_len)*.5*$useNote_wrd_w;
		
		$customerPO=escape_data($_POST['customerPO_'.$i]);
		$customerPO_len=strlen($customerPO);
		
		$lotNumber=escape_data($_POST['lotNumber_'.$i]);
		$manufactureDate=escape_data($_POST['manufactureDate_'.$i]);
		$expirationDate=escape_data($_POST['expirationDate_'.$i]);
		$flavorNote=escape_data($_POST['flavorNote_'.$i]);
		$dangerNote=escape_data($_POST['danger_'.$i]);
		$warningNote=escape_data($_POST['warning_'.$i]);
		$warningContact=escape_data($_POST['warningContact_'.$i]);
		$nf_or_wn=escape_data($_POST['NF_OR_WN_'.$i]);
		if ( $nf_or_wn == "NF" ) {
			$dangerNote="";
			$warningNote="";
			$warningContact="";
			
		} else if ( $nf_or_wn == "WN" ) {
			$flavorNote="";
		}
			
		$labelNote1=escape_data($_POST['labelNote1_'.$i]);
		$labelNote2=escape_data($_POST['labelNote2_'.$i]);
		$labelNote3=escape_data($_POST['labelNote3_'.$i]);
		$labelNote4=escape_data($_POST['labelNote4_'.$i]);
		$netWeight=escape_data($_POST['netWeight_'.$i]);
		$quantity=escape_data($_POST['quantity_'.$i]);
		$kosher=(isset($_POST['kosher_'.$i])) ? true : false;
	  for ( $j=0;$j<$quantity;$j++) {
		fwrite($fh,"^XA\n") or die ("failed write file");
		fwrite($fh,"^POI\n") or die ( "Failed write file");
		fwrite($fh,"^FXBELOW, REVERSED First Sample Lable - Z6M^FS\n") or die ( "Failed write file");
		fwrite($fh,"^FO80,600^A0R,60,60^FDNET WEIGHT: ". $netWeight ."^FS\n") or die ( "Failed write file");
		if ( !empty($flavorNote) ) {
			fwrite($fh,"^FO170,300^A0R,45,48^FD". $flavorNote ."^FS\n") or die ( "Failed write file");
		}
		if ( !empty($warningContact) ) {
			fwrite($fh,"^FO155,300^A0R,30,36^FD". $warningContact ."^FS\n") or die ( "Failed write file");
		}
		if ( !empty($warningNote) ) {
			fwrite($fh,"^FO185,300^A0R,30,36^FD". $warningNote ."^FS\n") or die ( "Failed write file");
		}
		if ( !empty($dangerNote) ) {
			fwrite($fh,"^FO215,300^A0R,30,36^FD". $dangerNote ."^FS\n") or die ( "Failed write file");
		}
		
		if ( !empty($labelNote4) ) {
			fwrite($fh,"^FO250,300^A0R,45,48^FD" .str_replace("  "," ",$labelNote4) ."^FS\n") or die ( "Failed write file");
		}
		if ( !empty($labelNote3) ) {
			fwrite($fh,"^FO295,300^A0R,45,48^FD" .str_replace("  "," ",$labelNote3) ."^FS\n") or die ( "Failed write file");
		}
		if ( !empty($labelNote2) ) {
			fwrite($fh,"^FO340,300^A0R,45,48^FD" .str_replace("  "," ",$labelNote2) ."^FS\n") or die ( "Failed write file");
		}
		if ( !empty($labelNote1) ) {
			fwrite($fh,"^FO385,300^A0R,45,48^FD" .str_replace("  "," ",$labelNote1) ."^FS\n") or die ( "Failed write file");
		}
		fwrite($fh,"^FO440,300^A0R,50,60^FDExpiration Date: ". $expirationDate."^FS\n") or die ( "Failed write file");
		fwrite($fh,"^FO490,300^A0R,50,60^FDDate of Manufacture: ". $manufactureDate ."^FS\n") or die ( "Failed write file");
		fwrite($fh,"^FO540,300^A0R,50,60^FDLot#: ". $lotNumber ."^FS\n") or die ( "Failed write file");
		fwrite($fh,"^FO590,300^A0R,50,60^FDP.O.#: ". $customerPO ."^FS\n") or die ( "Failed write file");
		if ( !empty($useNote) ) {
			$tmpY=number_format(300+$useNote_y,0);
			fwrite($fh,"^FO660,".$tmpY."^A0R,60,60^FD". $useNote."^FS\n") or die ( "Failed write file");
		}
		
		if ( ! empty($StorageNote) ) {
			$tmpY=number_format(300+$StorageNote_y,0);
			fwrite($fh,"^FO720,".$tmpY."^A0R,45,48^FD". $StorageNote ."^FS\n") or die ( "Failed write file");
		}

		if ( ! empty($description) ) {
			$tmpY=number_format(300+$description_y,0);
			fwrite($fh,"^FO780,".$tmpY."^A0R,105,94^FD". $description."^FS\n") or die ( "Failed write file");
		}

		$tmpY=number_format(300+$product_name_y,0);
		fwrite($fh,"^FO900,".$tmpY."^A0R,135,132^FD". $product_name ."^FS\n") or die ( "Failed write file");
		
		if ($kosher) { 
			fwrite($fh,"^FXKOSHER MARK\n") or die ( "Failed write file");
			fwrite($fh,"^FO500,1340^GD200,115,10,B,L^FS\n") or die ( "Failed write file");
			fwrite($fh,"^FO500,1455^GD200,115,10,B,R^FS\n") or die ( "Failed write file");
			fwrite($fh,"^FO500,1338^GB0,234,5^FS\n") or die ( "Failed write file");
			fwrite($fh,"^FO560,1410^A0R,60,62^FDcRc^FS\n") or die ( "Failed write file");
			fwrite($fh,"^FO460,1440^AFR,26,13^FD29^FS\n") or die ( "Failed write file");
		}
		fwrite($fh,"^FS\n") or die ( "Failed write file");
		fwrite($fh,"^XZ\n") or die ( "Failed write file");
	  }
	}
	fclose($fh);
//	$cmd="printLabel.bat ".$fileName;
//	$returnMsg=system($cmd);
//	echo "system cmd  $cmd returns: $returnMsg<br />";
//	if ( stristr($returnMsg,"successfully") ) {
//		$cmd = "copy ". $fileName ." lpt1";
//		echo "<br /> print cmd=$cmd <br />";
//		system($cmd);
	//	include("../inc_opener_reload_self_close.php");
//	}
	
	echo "<script language='javascript'>window.location.href='".$fileName."';// window.location.reload();</script>";
exit();
}

?>



<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#976AC2"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#EFEFEF"><TR VALIGN=TOP><TD>
<FORM METHOD="post" name="print_label" id="print_label" ACTION="print_customer_order_labels.php">

<?php
if ( empty($order_num) ) {
	echo "Customer Order Number is required for printing the label(s)<br /><br />";
	echo "<INPUT type='button' value='Close' onClick='window.close();' class='submit'>";
} else {
$sql = "SELECT distinct cod.CustomerCodeNumber, cod.Quantity, cod.PackSize, cod.UnitOfMeasure, cod.description, 
bm.ProductNumberExternal, cod.ProductNumberInternal, bm.ProductDesignation, 
customerordermaster.CustomerPONumber, 
concat(lots.LotNumber, '-', lots.LotSequenceNumber) as lotNumber, lots.DateManufactured,lots.ExpirationDate,
pm.Organic,customerordermaster.Kosher,pm.Halal,pm.Hazard,pm.designation,pm.Natural_OR_Artificial
FROM customerorderdetail as cod
LEFT JOIN customerorderdetaillotnumbers as codlot ON cod.CustomerOrderNumber=codlot.CustomerOrderNumber and
cod.ProductNumberInternal=codlot.ProductNumberInternal and cod.CustomerOrderSeqNumber=codlot.CustomerOrderSeqNumber
LEFT JOIN lots on codlot.LotID=lots.ID 
LEFT JOIN batchsheetmaster as bm ON bm.ProductNumberInternal=cod.ProductnumberInternal
LEFT JOIN customerordermaster on customerordermaster.OrderNumber=cod.CustomerOrderNumber
JOIN batchsheetcustomerinfo as bsci ON bsci.BatchSheetNumber=bm.BatchSheetNumber AND
  bsci.CustomerOrderNumber=cod.CustomerOrderNumber AND
  bsci.CustomerOrderSeqNumber=cod.CustomerOrderSeqNumber
LEFT JOIN productmaster as pm on pm.ProductNumberInternal = cod.ProductNumberInternal
WHERE cod.CustomerOrderNumber = " . $order_num;
// ." AND lots.DateManufactured is not NULL";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$i=0;
if ( mysql_num_rows($result) > 0 ) {
while ( $row = mysql_fetch_array($result) ) {
$CustomerCodeNumber = $row['OrderNumber'];
$Quantity = $row['Quantity'];
$PackSize = $row['PackSize'];
$UnitOfMeasure = $row['UnitOfMeasure'];
$Description = ( $row['description'] == "" ? $row['ProductDesignation'] : $row['description'] );
//echo "<br /> $Description <br/>";
$DescriptionA = explode(" - ",$Description);
//print_r($DescriptionA);
$Description = strtoupper($DescriptionA[0]);
$Description = str_replace(array("NATURAL","TYPE"),array("NAT.",""),$Description);
//echo "<br />sizeof DescriptionA = ".sizeof($DescriptionA); 
//$kosher=$DescriptionA[sizeof($DescriptionA) - 1];
$kosher=$row['Kosher'];
//echo "<br /> Kosher=".$kosher;
$PONumber=$row['CustomerPONumber'];
$lotNumber=$row['lotNumber'];
$dateManufactured=$row['DateManufactured'];
if ( empty($dateManufactured) ) {
	$mkDate = "N/A";
} else {
	$mkDateTime=strtotime($dateManufactured);
    $mkDate = date('n-j-y',$mkDateTime);
}

$expirationDate=$row['ExpirationDate'];

if ( empty($expirationDate) ) {
	if ( $mkDate != "N/A" ) {
		$expDate = date('n-j-y', mktime(0,0,0,date('m',$mkDateTime),date('d',$mkDateTime),date('Y',$mkDateTime)+1)); //expirate one year after made 
	} else {
		$exDate = "N/A";
	}
} else {
	$expDate = date('n-j-y',strtotime($expirationDate));
}
$LabelNote1="";
$LabelNote2="";
$LabelNote3="";
$LabelNote4="";
?>	

	<INPUT type="checkbox" name="prt_label_<?php echo $i;?>" id="prt_label_<?php echo $i;?>" value="label_<?php echo $i?>" onClick="ctrPrint(<?php echo $i?>)" checked> 
	<label for="prt_label_<?php echo $i?>" onClick="ctrPrint(<?php echo $i?>)">Check/Uncheck for print/not print label of <B><?php echo $row['ProductNumberExternal'];?></B></label>
	<div id="label_<?php echo $i;?>">
	<br />
	<br />
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B style="font-size:14pt">Product Name:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT type="text" name="product_name_<?php echo $i?>" id="product_name_<?php echo $i?>" style="font-size:48pt" size="20" VALUE="<?php echo $row['ProductNumberExternal'];?>" maxlength="20"></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B style="font-size:14pt">Description</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT type="text" name="description_<?php echo $i?>" id="description_<?php echo $i?>" style="font-size:33pt" size="31" VALUE="<?php echo $Description;?>" maxlength="28">
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>

		<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B style="font-size:15pt">Storage Note:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><Input type="text" NAME="StorageNote_<?php echo $i?>" style="font-size:15pt" size="70" maxlenth="69"></TD>
	</TR>
	
	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>
	

	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B style="font-size:14pt">Use Note</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT type="text" NAME="useNote_<?php echo $i?>" id="useNote_<?php echo $i?>" style="font-size:19pt" value="*Shake Well Before Use*" size="56" maxlength="50">
		</TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B style="font-size:14pt">P.O.#:</B></TD>
		<TD><IMG SRC="/images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT type="text" name="customerPO_<?php echo $i?>" id="customerPO_<?php echo $i?>" style="font-size:18pt" value="<?php echo $PONumber;?>" size="56" maxlength="30">
		</TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B style="font-size:14pt">Lot#:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT type="text" NAME="lotNumber_<?php echo $i;?>" id="lotNumber_<?php echo $i;?>" style="font-size:18pt" value="<?php echo $lotNumber;?>" size="56" maxlength="32">
	</TD>
	</TR>
	
	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>
	
	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B style="font-size:14pt">Manufacture Date:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT type="text" name="manufactureDate_<?php echo $i;?>" id="manufactureDate" style="font-size:18pt" SIZE="56" value="<?php echo $mkDate?>" maxlength="17">
		</TD>
	</TR>

	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>
	
	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B style="font-size:14pt">Expiration Date:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT type="text" name="expirationDate_<?php echo $i;?>" id="expirationDate_<?php echo $i;?>" style="font-size:18pt" SIZE="56" value="<?php echo $expDate;?>" maxlength="20">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>

	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B style="font-size:14pt">Label Not:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><INPUT name="labelNote1_<?php echo $i;?>" size="70" id="labelNote1_<?php echo $i;?>" style="font-size:14pt" maxlength="70" value="<?php echo ( $labelNote1 =="" ? "Label Declaration: All ingredients contained in this product" : $labelNote1 )?>">
		<BR><INPUT name="labelNote2_<?php echo $i;?>" size="70" id="labelNote2_<?php echo $i;?>" style="font-size:14pt" maxlength="70" value="<?php echo ( $labelNote2 =="" ? "are approved for use by a regulation of the Food & Drug" : $labelNote2 )?>">
		<BR><INPUT name="labelNote3_<?php echo $i;?>" size="70" id="labelNote3_<?php echo $i;?>" style="font-size:14pt" maxlength="70" value="<?php echo ( $labelNote3 =="" ? "Administration and are listed generally recognized as safe on" : $labelNote3 ) ?>">
		<BR><INPUT name="labelNote4_<?php echo $i;?>" size="70" id="labelNote4_<?php echo $i;?>" style="font-size:14pt" maxlength="70" value="<?php echo ( $labelNote4 =="" ? "a reliable publichsed industry association list." : $labelNote4 )?>"></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>
	<TR><TD colspan="3" style="border-top:solid 1px black; border-bottom:solid 1px black"><B>Note: </B>Please use radio button to select printing fields of Following,  <U>Non-Flavor Note</U> field or <U>DANGER</U>, <U>WARNING</U> fields. They cannot be printed at the same time.</TD></TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><input type="radio" name="NF_OR_WN_<?php echo $i?>" id="NF_<?php echo $i?>" value="NF" checked>&nbsp;&nbsp;<B style="font-size:14pt"><label for="NF_<?php echo $i?>">Non-Flavor Note:</label></B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><input type="text" name="flavorNote_<?php echo $i;?>" id="flavorNote_<?php echo $i;?>" style="font-size:14pt" SIZE="78" maxlength="70" value="<?php echo ( $flavorNote == "" ? 
		"Non - Flavor Ingredients: Propylene Glycol" : $flavorNote);?>">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD rowspan="2" ALIGN=RIGHT>
		<TABLE><TR><TD rowspan="2" VALIGN="middle"><INPUT type="radio" name="NF_OR_WN_<?php echo $i?>" id="WN_<?php echo $i?>" value="WN"></TD>
		<TD><B style="font-size:14pt;"><label for="WN_<?php echo $i?>">DANGER:</label></B></TD></TR>
		<TR><TD ALIGN=RIGHT><B style="font-size:14pt"><label for="WN_<?php echo $i?>">WARNING:</label></B></TD></TR>
		</TABLE>
		<TD rowspan="2"><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><input type="text" name="danger_<?php echo $i;?>" id="danger_<?php echo $i;?>" style="font-size:12pt" SIZE="78" maxlength="75" value="<?php echo ( $dangerNote == "" ? 
		"DANGER: Can cause damage to respiratory tract and lungs if inhaled" : $DangerNote);?>">
		</TD>
	</TR>
	
	<TR VALIGN=TOP>
		<TD><input type="text" name="warning_<?php echo $i;?>" id="warning_<?php echo $i;?>" style="font-size:12pt" SIZE="78" maxlength="75" value="<?php echo ( $dangerNote == "" ? 
		"WARNING: Can cause eye,skin, nose and throat irritation." : $Warning);?>">
		<input type="text" name="warningContact_<?php echo $i;?>" id="warningContact_<?php echo $i;?>" style="font-size:12pt" SIZE="78" maxlength="75" value="<?php echo ( $WarningContact == "" ? 
		"Contact Information: ABELEI 630-859-1410" : $Warning);?>">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>	
	<TR VALIGN=TOP>
		<TD ALIGN=RIGHT><B style="font-size:14pt">Net Weight:</B></TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD>
		<input type="hidden" name="quantity_<?php echo $i;?>" id="quantity_"<?php echo $i?>" value="<?php echo $row['Quantity'];?>">
		<input type="text" name="netWeight_<?php echo $i;?>" id="netWeight_<?php echo $i;?>" style="font-size:19pt" SIZE="56" value="<?php echo number_format($PackSize,0) ." ".strtoupper($UnitOfMeasure);?>" maxlength="49">
		</TD>
	</TR>
		
	<TR>
		<TD COLSPAN=3><IMG SRC="../images/spacer_trans.gif" WIDTH="1" HEIGHT="15"></TD>
	</TR>
	<TR VALIGN=TOP>
		<TD align="right"><B style="font-size=:14pt">Kosher:</B> </TD>
		<TD><IMG SRC="../images/spacer_trans.gif" WIDTH="11" HEIGHT="1"></TD>
		<TD><input type="checkbox" name="kosher_<?php echo $i?>" <?php  if ( $kosher == '1' ) echo "CHECKED";?>></TD>
	</TR>

</TABLE>
	<hr />
	</div>
	<br />
	<br />
<?php $i++; };

?>
</TD>
</TR>
<TD><INPUT type="hidden" name="numOfPrd" value="<?php echo $i;?>"> <INPUT type="submit" value="Submit"></TD></TR>
<?php } else {
echo "<br >There is no ordered product ready to print label <br />";
echo "<INPUT type='button' value='close' onClick='window.self.close();'>"; 
}
 } ?>

</FORM>
</TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>
</TD></TR></TABLE>



<BR><BR><BR>

</TD></TR></TABLE>



<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%" BGCOLOR="#99CC33">
<TR><TD BGCOLOR="#976AC2"><IMG SRC="../images/spacer.gif" WIDTH="1" HEIGHT="2" BORDER="0"></TD></TR>
	<TR>
		<TD><IMG SRC="../images/spacer.gif" WIDTH="27" HEIGHT="30" BORDER="0"></TD>
	</TR>
</TABLE>
<SCRIPT LANGUAGE="javascript">
<!-- hide
function ctrPrint(cnt) {
 myLabel=document.getElementById("label_"+cnt);

	if (document.getElementById("prt_label_"+cnt).checked) {
		myLabel.style.display="inline";
	} else {
		myLabel.style.display="none";
	}
}
-->
</SCRIPT>