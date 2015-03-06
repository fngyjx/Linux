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

include('../inc_global.php');
if (!isset($_REQUEST["c_id"]))
{
	$_REQUEST["c_id"] = "";
} 

$limit = 0;
if (isset($_REQUEST["limit"]))
{
	if (is_numeric($_REQUEST["limit"]))
	{
		$limit = $_REQUEST["limit"];
	}
} 

$c_id = strtolower(escape_data($_REQUEST["c_id"]));
if ("" != $c_id)
{

	$sql = "SELECT contact_id, title, first_name, last_name, suffix FROM customer_contacts WHERE customer_id=$c_id AND customer_contacts.active = 1 ORDER BY last_name, first_name ASC".(0 != $limit ? " LIMIT $limit" : "");
	$result = mysql_query($sql, $link);
	$result_count = mysql_num_rows($result);
	if (0 < $result_count)
	{
		while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
			$name = trim(("" != $row["title"] ? $row["title"]." " : "").("" != $row["first_name"] ? $row["first_name"]." " : "").("" != $row["last_name"] ? $row["last_name"]." " : "").("" != $row["suffix"] ? $row["suffix"] :""));
			$id = $row["contact_id"];
			
			$sql = "SELECT number, description FROM customer_contact_phones inner join phone_types on ( customer_contact_phones.type = phone_types.type_id ) WHERE contact_id=$id ORDER BY description ASC".(0 != $limit ? " LIMIT $limit" : "");
			$result2 = mysql_query($sql, $link);
			$result2_count = mysql_num_rows($result2);
			if (0 < $result2_count)
			{
				$phone="";
				while ( $row = mysql_fetch_array($result2, MYSQL_ASSOC) ) {
					$phone = $row["description"].": ".$row["number"]."<br/>";
				}
			}

			echo "$name|$id|$phone\n";
		}
	}
}
else
{
	echo "no contacts selected||no contacts selected";
}

?>