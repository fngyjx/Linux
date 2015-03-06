<?php

include('../inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	//header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

$customer_id="";
if ( isset($_REQUEST['search']) ) {
	$customer_id=(is_numeric($_REQUEST['search'])) ? $_REQUEST['search'] : "";
}

include('../inc_global.php');

if ( ""!=$customer_id) 
{
	$sql = "SELECT contact_id, title, first_name, last_name, suffix FROM customer_contacts WHERE customer_id=".$customer_id;
	$result = mysql_query($sql, $link);
	if (0 == mysql_num_rows($result))
	{
	$sql = "SELECT name FROM customers WHERE customer_id=".$customer_id;
	$result = mysql_query($sql, $link);
	if (0 < mysql_num_rows($result))
		{
		$row_customer = mysql_fetch_array($result);
	?>
		<select name="contact" class="select" onchange="updateCompanyContactPhoneNumbers(this.value)" >
		<option value=""><em>no contacts found for customer <?php echo $row_customer['name']." [".$customer_id."]"; ?></em></option>
		</select>
		Business #: <span id="client_business_phone"></span>
		Mobile #: <span id="client_mobile_phone"></span>
		Fax #: <span id="client_fax"></span>
	<?php
		}
		else { ?>
	<span id="contactspan"><SELECT NAME="contact" CLASS="select" onchange="updateCompanyContactPhoneNumbers(this.value)" ><OPTION VALUE="">Invalid Customer ID: <?php echo $_REQUEST['search']; ?></OPTION></SELECT></span>
	Business #: <span id="client_business_phone"></span>
	Mobile #: <span id="client_mobile_phone"></span>
	Fax #: <span id="client_fax"></span>
		<?php }
	}
	else if (1 == mysql_num_rows($result))
	{ 
		$row_contact = mysql_fetch_array($result);
		print "<select value=\"contact\" CLASS=\"select\" readonly='readonly'> ";
		$name=$row_contact['title']." ".$row_contact['first_name']." ".$row_contact['last_name']." ".$row_contact['suffix'];
		print "<option value=".$row_contact['contact_id'].">$name</option></select> ";
		$sql = "SELECT number, phone_types.description FROM customer_contact_phones LEFT_JOIN phone_types ON (customer_contact_phones.type = phone_types.id) WHERE contact_id=".$customer_id;
		$result = mysql_query($sql, $link);
		if (0 < mysql_num_rows($result))
		{
		//populate business, mobile and fax numbers then print.
		$business_phone="";
		$mobile_phone="";
		$fax="";
		print "Business #: <span id=\"client_business_phone\">$business_phone</span> ";
		print "Mobile #: <span id=\"client_mobile_phone\">$mobile_phone</span> ";
		print "Fax #: <span id=\"client_fax\">$fax</span> ";
		}
	}
	else
	{
		print "<select name=\"contact\" class=\"select\" onchange=\"updateCompanyContactPhoneNumbers(this.value)\" ><option value=\"\" />\n";
		while ( $row_contact = mysql_fetch_array($result) ) 
		{
			$name=$row_contact['title']." ".$row_contact['first_name']." ".$row_contact['last_name']." ".$row_contact['suffix'];
			print "<option value=".$row_contact['contact_id'].">$name</option>\n";
		}
		?>
			</select>
			Business #: <span id="client_business_phone"></span>
			Mobile #: <span id="client_mobile_phone"></span>
			Fax #: <span id="client_fax"></span>
	<?php
	}
}
else
{
?>
	<span id="contactspan"><SELECT NAME="contact" CLASS="select" onchange="updateCompanyContactPhoneNumbers(this.value)" ><OPTION VALUE="">Invalid Customer ID: <?php echo $_REQUEST['search']; ?></OPTION></SELECT></span>
	Business #: <span id="client_business_phone"></span>
	Mobile #: <span id="client_mobile_phone"></span>
	Fax #: <span id="client_fax"></span>
<?php
}
?>