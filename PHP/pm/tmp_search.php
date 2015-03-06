<?php 

include('global.php');
require_ssl();
session_start();

$sql="select distinct flavor_id, flavor_name from flavors";
$result=mysql_query($sql,$link) or die(mysql_error() . " Failed execute SQL: $sql <br />");
$flavor_id="";
while( $row=mysql_fetch_array($result) ) {
	if ( $flavor_id != $row[0] ) {
		$sql="UPDATE flavor_distinct set flavor_name='".addslashes($row[1])."' WHERE flavor_id='".$row[0]."'";
		mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL:$sql <br />");
	}
	$flavor_id=$row[0];
}
?>