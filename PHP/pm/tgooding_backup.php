<?php
//database backup php tool will be used in batch, thus would not use session and ssl
include('global.php');

$backupDir = '../_db_backups/';
$sql="SHOW TABLES";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute sql: $sql \n");

while ( $row=mysql_fetch_array($result) ) {

	$backupFile=$backupDir.$row[0].".sql";
	$query = "SELECT * INTO OUTFILE '$backupFile' FROM $tableName";
	$result_backup = mysql_query($query) ;
	if ( $result_backup )
		echo "Table ".$row[0] ." was backuped into ". $backupFile." \n";
	else
		echo  mysql_error() ." Failed Execute SQL: $sql\n";
}

mysql_close($link);
?>
