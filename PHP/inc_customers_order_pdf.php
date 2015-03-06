<?php

function create_cstordcnfrm_pdf_file($order_number,$customer_id, $email,$phone_id) {
    global $link;
	include("set_pdf.php");
    
	
	if ( $email != "") {
		$sql = "SELECT * FROM customer_contacts join customers USING(customer_id) where email1 = '" . $email . "' AND customers.customer_id='" .$customer_id ."'";
	} else if ( $phone_id != "" ){
		$sql = "SELECT * FROM customer_contacts JOIN customer_contact_phones USING(contact_id) JOIN customers USING(customer_id)
		WHERE customers.customer_id='".$customer_id."' AND customer_contact_phones.phone_id='".$phone_id."'";
	}
	
	$result_contact = mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
	$row_contact=mysql_fetch_array($result_contact);

	$sql = "SELECT * FROM customerordermaster WHERE OrderNumber=$order_number";
	$result_order=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql ");
	$row_order=mysql_fetch_array($result_order);
	
	$sql = "SELECT customerorderdetail.*, ProductNumberExternal, Designation ,Natural_OR_Artificial, ProductType, Kosher
		FROM customerorderdetail
		INNER JOIN externalproductnumberreference ON customerorderdetail.ProductNumberInternal = externalproductnumberreference.ProductNumberInternal
		INNER JOIN productmaster ON productmaster.ProductNumberInternal = customerorderdetail.ProductNumberInternal
		WHERE customerorderdetail.CustomerOrderNumber=" .$order_number;

	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$num_ordered_prds = mysql_num_rows($result);
	$pdf = set_abelei_pdf("abelei","abelei","abelei","abelei",$num_ordered_prds);
	
	$html = '<h3 align="center">CONFIRMATION OF ORDER</h3>';
	$html .= '<br /><br /><br /><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="510">';
	$html .= '<TR><TD>194 Alder Drive</TD><TD align="right">ORDER DATE: ' . date("n/j/Y", strtotime($row_order['OrderDate'])) . '</TD></TR>';
	$html .= '<TR><TD>North Aurora , IL 60542</TD><TD>&nbsp;</TD></TR>';
	$html .= '<TR><TD>Tel Phone : (630) 859-1410</TD><TD>&nbsp;</TD></TR>';
	$html .= '<TR><TD>Toll Free: (866) 4 abelei</TD><TD>&nbsp;</TD></TR>';
	$html .= '<TR><TD>Fax: (630) 859-1448</TD><TD ALIGN="RIGHT">CONFIRM DATE:'. date("n/j/Y"). '</TD></TR>';
	$html .= '<TR><TD colspan="2">&nbsp;</TD></TR>';
	$html .= '<TR><TD><B>BILL TO:</B></TD><TD ALIGN="LEFT"><B>SHIP TO:</B></TD></TR>';
	$sql = "SELECT * FROM customer_addresses WHERE address_id='" . $row_order['BillToLocationID'] . "'";
	$result_billto=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
	$row_billto=mysql_fetch_array($result_billto);
	$sql = "SELECT * FROM customer_addresses WHERE address_id='".$row_order['ShipToLocationID']."'";
	$result_shipto=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
	$row_shipto=mysql_fetch_array($result_shipto);
	$html .= '<TR><TD>' . $row_contact['name'].'</TD><TD ALIGN="LEFT">'. $row_contact['name'] .'</TD></TR>';
	$html .= '<TR><TD>' . $row_billto['address1'];
	if ( $row_billto['address2'] != "" )
		$html .= '<br />'. $row_billto['address2'];
	$html .= '</TD><TD ALIGN="LEFT">'. $row_shipto['address1'];
	if ( $row_shipto['address2'] != "" )
		$html .= '<br />'. $row_shipto['address2'];
	$html .= '</TD></TR>';
	$html .= '<TR><TD>' .$row_billto['city'] . ', '. $row_billto['state']. ' ' . $row_billto['zip'].'</TD>';
	$html .= '<TD ALIGN="LEFT">'. $row_shipto['city'].', ' .$row_shipto['state']. ' ' .$row_shipto['zip'].'</TD></TR>';
	$html .= '<TR><TD colspan="2">&nbsp;</TD></TR><TR><TD colspan="2">&nbsp;</TD></TR>';
	$html .= '<TR><TD>Attn: Accounts Payable</TD><TD ALIGN="LEFT">Receiving:&nbsp;&nbsp;&nbsp;'. $row_contact['first_name'].' '.$row_contact['last_name']. '</TD></TR>';
	$html .= '<TR><TD colspan="2">&nbsp;</TD></TR>';
	$html .= '<TR><TD>CONFIRM TO: &nbsp;&nbsp;'. $row_contact['first_name'] .' '. $row_contact['last_name']. '</TD><TD ALIGN="RIGHT">Prepared by:';
	$sql = "SELECT * FROM users where user_id = '".$_SESSION['user_id']."'";
	$result_user = mysql_query($sql,$link) or die ( mysql_error() . " FAILED Execute SQL : $sql <br />");
	$row_user = mysql_fetch_array($result_user);
	$html .= $row_user['first_name'].' ' .$row_user['last_name']. '</TD></TR>';
	$html .= '</TABLE>';
	$pdf->writeHTML($html, true, 0, true, 0);
	$html = '<hr width="510">';
	$html .= '<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="510"><TR>';
	$html .= '<TD>CUSTOMER P.O.</TD><TD>SHIP VIA</TD><TD>DUE DATE</TD></TR>';
	$html .= '<TR><TD align="center">'. $row_order['CustomerPONumber'].'</TD><TD align="center">'.$row_order['ShipVia'].'</TD><TD align="center">'. date("n/j/Y", strtotime($row_order['RequestedDeliveryDate'])).'</TD></TR>';
	$html .= '</TABLE>';
	$pdf->writeHTML($html, true, 0, true, 0);
	$html = '<hr width="510">';
	$html .= '<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="510" BORDERCOLOR="#CDCDCD"><TR>';
	$html .= '<TD>ITEM No.</TD><TD>F.O.B</TD><TD>TERMS</TD><TD>UNIT</TD><TD>ORDERED</TD><TD>QUANTITY</TD><TD>UNIT PRICE</TD><TD>$Amount</TD></TR>';
	$html .= '<TR><TD colspan="8" style="border-bottom:1px solid black" align="center">_________________________________________________________________________________________</TD></TR>';
	$order_total=0;
	while ( $row = mysql_fetch_array($result) ) {
		$sql = "SELECT * FROM pricesheetmaster where CustomerID='".$row_order['CustomerID']."' AND ProductNumberInternal='". $row['ProductNumberInternal'] . "' AND locked>0";
		$result_price=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
		if ( mysql_num_rows($result_price) == 0 ) {
			$sql = "SELECT * FROM pricesheetmaster where CustomerID='".$row_order['CustomerID']."' AND ProductNumberInternal='". $row['ProductNumberInternal'] . "' AND SellingPrice>0";
			$result_price=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
		}
		$row_price = mysql_fetch_array($result_price);
		$html .= '<TR><TD>' . $row['ProductNumberExternal'].'</TD><TD>'.$row_price['FOBLocation'].'</TD><TD>'.$row_price['Terms'].'</TD><TD>'. $row['UnitOfMeasure'].'</TD><TD>' . number_format($row['Quantity'],0).'x'.number_format($row['PackSize'],2).'</TD><TD>'. number_format($row['TotalQuantityOrdered'],2).'</TD><TD>';
		$order_total += $row_price['SellingPrice']*$row['TotalQuantityOrdered'];
		$html .= number_format($row_price['SellingPrice'],2) .'</TD><TD>'. number_format($row_price['SellingPrice']*$row['TotalQuantityOrdered'],2).'</TD></TR>';
		
		$html .= '<TR><TD Colspan="4" Align="right"><br />';
		$html .= ( $row_price['ProductDesignation'] == "" ? $row['Natural_OR_Artificial']." ". $row['Designation'] ."-".$row['ProductType'] . ( $row['Kosher'] == 1? " - K" : "" ) : $row_price['ProductDesignation'] );
		$html .= '<br />CustomerProduct ID: '. $row['CustomerCodeNumber'];
		$html .= '<br />Packaging: '. ( $row['OrderPackIn'] == "" ? $row_price['Packaged_In'] : $row['OrderPackIn']) ;
		//$html .= '<br />Ship Date: '. $row['ShipDate'];
		//$html .= '<br />Bill Date: '. $row['BilledDate'] . '<br />';
		$html .= '</TD><TD colspan="4">&nbsp;</TD></TR>';
		$html .= '<TR><TD colspan="8" style="border-bottom:solid black" align="center">_________________________________________________________________________________________</TD></TR>';
	}
	$html .= '<TR><TD colspan="5">&nbsp;</TD><TD colspan="2" align="right">Net Order:</TD><TD align="right">'. number_format($order_total,2).'</TD></TR>';
	$html .= '<TR><TD colspan="5">&nbsp;</TD><TD colspan="2" align="right">Freight:</TD><TD align="right">0.00</TD></TR>';
	$html .= '<TR><TD colspan="5">&nbsp;</TD><TD colspan="2" align="right">Sales Tax:</TD><TD align="right" style="text-decoration:underline">0.00</TD></TR>';
	$html .= '<TR><TD colspan="5">&nbsp;</TD><TD colspan="2" align="right">Order Total:</TD><TD align="right">' . number_format($order_total,2).'</TD></TR>';
	$html .= '</TABLE>';
	//echo "<br /" . $html ."<br />";
	$pdf->writeHTML($html, true, 0, true, 0);

	$pdf->lastPage();

	// --------------------------------------------------------- 

	//Close and output PDF document
	//echo "customer name: ". $row_contact['name']."<br />\n";
	$contact="";
	if ( mysql_num_rows($result_contact) > 0 ) {
		$contact=str_replace(" ", "_", $row_contact['name']);
	} else if ( $email != "" ) {
		$contact=$email;
	} else if ( $phone_id != '' ) {
		$contact="phone_id_".$phone_id;
	}
	$file =  $contact. "_order_confirm_" . date("mdy");
	$pdf->Output('pdfs/' . $file . '.pdf', 'F');

	return "pdfs/".$file.".pdf";
	//============================================================+ 
	// END OF FILE                                                  
	//============================================================+ 
}
?>