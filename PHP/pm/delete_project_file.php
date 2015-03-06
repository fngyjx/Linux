<?php
session_start();
include('global.php');
require_ssl();

$file_id=$_REQUEST['file_id'];

if ( $file_id != "" ) {
//print_r($_REQUEST);

$sql="UPDATE project_files SET status=0 WHERE ID='".$file_id."'";
mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
}

include('inc_opener_reload_self_close.php'); 

exit();
?>