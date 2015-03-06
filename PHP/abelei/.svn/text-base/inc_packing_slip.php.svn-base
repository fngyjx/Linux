<?php

$sample_size_array = array("1 oz.","2 oz.","4 oz.","8 oz.","Other");
$sample_size_num = array(1,2,3,4,5);

$message .= "<TABLE CELLPADDING=0 CELLSPACING=0 WIDTH=600 ALIGN=CENTER><TR><TD>";

$sql = "SELECT first_name, last_name, name, address1, address2, city, state, zip, country, sample_size, sample_size_other, project_id, date_created, customer_id, salesperson, shipped_date, shipper, shipper_other, shipping FROM projects LEFT JOIN customer_contacts USING(contact_id) LEFT JOIN customers USING(customer_id) WHERE project_id = " . $_GET['pid'];
$result = mysql_query($sql, $link);
$row = mysql_fetch_array($result);
	
$message .= "<B style='font-size:14pt'>SAMPLE TRANSMITTAL</B><BR><BR><BR>";


// CHECK WHETHER OTHER SHIPPING ADDRESS HAS BEEN ENTERED
$sql = "SELECT * FROM shipping_info WHERE project_id = " . $_GET['pid'];
$result_shipping = mysql_query($sql, $link);
$c = mysql_num_rows($result_shipping);

if ( $c > 0 ) {
	$row_shipping = mysql_fetch_array($result_shipping);
	$message .= "<B>Ship To: " . $row_shipping['first_name'] . " " . $row_shipping['last_name'] . "<BR>";
	if ( $row_shipping['company'] != "" ) {
		$message .= $row_shipping['company'] . "<BR>";
	}
	$message .= $row_shipping['address1'] . "  " . $row_shipping['address2'] . "<BR>";
	$message .= $row_shipping['city'] . ",  " . $row_shipping['state'] . "  " . $row_shipping['zip'] . "  " . $row_shipping['country'] . "<BR><BR>";
} else {
	$message .= "<B>Ship To: " . $row['first_name'] . " " . $row['last_name'] . "<BR>";
	$message .= $row['company'] . "<BR>";
	$message .= $row['address1'] . "  " . $row['address2'] . "<BR>";
	$message .= $row['city'] . ",  " . $row['state'] . "  " . $row['zip'] . "  " . $row['country'] . "<BR><BR>";
}

$message .= "<TABLE BORDER=1 CELLPADDING=8 CELLSPACING=0 WIDTH=600 BORDERCOLOR='black'>";
$message .= "<TR>";
$message .= "<TD WIDTH=84 ALIGN=CENTER><IMG SRC='images/spacer.gif' WIDTH='84' HEIGHT='1'><BR><B>Quantity</B></TD>";
$message .= "<TD WIDTH=376 ALIGN=CENTER><IMG SRC='images/spacer.gif' WIDTH='376' HEIGHT='1'><BR><B>Item Number/Description</B></TD>";
$message .= "<TD WIDTH=84 ALIGN=CENTER><IMG SRC='images/spacer.gif' WIDTH='84' HEIGHT='1'><BR><B>Suggested Use Level (%)</B></TD>";
$message .= "</TR>";
$message .= "</TABLE>";

$message .= "<TABLE BORDER=1 CELLPADDING=0 CELLSPACING=0 BORDERCOLOR='black'><TR><TD>";

$message .= "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0><TR VALIGN=TOP>";
$message .= "<TD><IMG SRC='images/spacer.gif' WIDTH='1' HEIGHT='400'></TD>";
$message .= "<TD><IMG SRC='images/spacer.gif' WIDTH='1' HEIGHT='7'><BR>";

$message .= "<TABLE BORDER=0 CELLPADDING=8 CELLSPACING=0 WIDTH=595>";

$sql = "SELECT * FROM flavors WHERE project_id = " . $_GET['pid'] . " ORDER BY flavor_name";
$result_flavor = mysql_query($sql, $link);
$c = mysql_num_rows($result_flavor);
if ( $c == 0 ) {
	$message .= "<I>None entered</I>";
}
else {
	while ( $row_flavor = mysql_fetch_array($result_flavor) ) {
		$message .= "<TR VALIGN=TOP>";
		$message .= "<TD ALIGN=CENTER WIDTH=100><B>";
		if ( $row['sample_size'] < 5 ) {
			$message .= $sample_size_array[$row['sample_size']-1];
		} else {
			$message .= $row['sample_size_other'];
		}
		$message .= "</B></TD>";
		$message .= "<TD WIDTH=400>";
		$message .= "<B>" . $row_flavor['flavor_id'] . " " . $row_flavor['flavor_name'] . "</B>";
		$message .= "</TD>";
		$message .= "<TD ALIGN=CENTER WIDTH=100><B>";
		$message .= $row_flavor['suggested_level_other'] . " " . $row_flavor['use_in'];
		if ( $row_flavor['other_info'] != '' ) {
			$message .= $row_flavor['other_info'] . "<BR>";
		}
		$message .= "</B></TD>";
		$message .= "</TR>";
	}
}
		
$message .= "</TABLE>";

$message .= "</TD></TR></TABLE>";
$message .= "</TD></TR></TABLE>";

$message .= "<TABLE BORDER=2 CELLPADDING=8 CELLSPACING=0 WIDTH=600 BORDERCOLOR='black'>";

$message .= "<TR ALIGN=CENTER>";
$message .= "<TD>Project No.</TD>";
$message .= "<TD>Project Date</TD>";
$message .= "<TD>Sales Rep</TD>";
$message .= "<TD>Ship Date</TD>";
$message .= "<TD>abelei flavors</TD>";
$message .= "</TR>";

$message .= "<TR ALIGN=CENTER>";

$message .= "<TD><I>" . substr($row['project_id'], 0, 2) . "-" . substr($row['project_id'], -3) . "</I></TD>";
$message .= "<TD><I>" . date("m/d/Y", strtotime($row['date_created'])) . "</I></TD>";
$message .= "<TD><I>";

$sql = "SELECT last_name FROM users WHERE user_id = " . $row['salesperson'];
$result_sales = mysql_query($sql, $link);
$c = mysql_num_rows($result_sales);
if ( $c != 0 ) {
	$row_sales = mysql_fetch_array($result_sales);
	$message .= $row_sales['last_name'];
}

$message .= "</I></TD>";
if ( $row['shipped_date'] == NULL ) {
	$ship_date = date("m/d/Y");
} else {
	$ship_date = date("m/d/Y", strtotime($row['shipped_date']));
}
$message .= "<TD><I>" . $ship_date . "</I></TD>";
$message .= "<TD><I>the source of good taste</I></TD>";

$message .= "</TR>";

$message .= "</TABLE><BR>";

$message .= "<I STYLE='font-size:7pt'>The items listed above are sales samples and have no commercial value. They are shipped F.O.B. from North Aurora, IL. All claims must be made within 10 days of receipt of goods. All return merchandise must receive prior authorization. Seller guarantees that articles listed herein are not adulterated or misbranded within the meaning of the federal food, drug, and cosmetic act. Seller not responsible for results obtained by buyer in use of material sold.</I>";

$message .= "</TD></TR></TABLE>";
		
?>