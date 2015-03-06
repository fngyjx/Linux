<?php
//database backup php tool will be used in batch, thus would not use session and ssl
include('global.php');

<?php


$tableNameArray  = array("change_log","cleints","cleints_users","companies","companies_users","flavors","lab_assignees","notes","projects","shipping_info","users");

$backupDir = '../_db_restores/';

$sql="SHOW TABLES";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute sql: $sql \n");
while ( $row=mysql_fetch_array($result) ) {

	$backupFile=$backupDir.$row[0].".sql";
	$query = "LOAD DATA INFILE '$backupFile' INTO TABLE $tableName";
	echo "$query \n";
	$result_backup = mysql_query($query,$link)
	if ( $result_backup ) 
		echo "Table file: $backupFile was restored \n";
	else 
		echo mysql_error() ." Failed Execute SQL: $sql \n";
}

mysql_close($link);
?>

