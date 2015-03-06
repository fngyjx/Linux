<?php
include('../inc_global.php');

echo "YES?";
$sql = "SELECT COUNT(*) FROM batchsheetmaster WHERE LotID IS NULL";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
$count = mysql_result($result,0,0);
for ($i=0; $i < $count; $i++) {
	$sql = "INSERT INTO lots () VALUES ()";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$sql = "UPDATE batchsheetmaster SET LotID = LAST_INSERT_ID() WHERE LotID IS NULL LIMIT 1";
	mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
}
$sql = "SELECT COUNT(*) FROM batchsheetmaster WHERE LotID IS NULL";
$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
echo "<h1>LA - ".mysql_result($result,0,0)."<h1>";
?>