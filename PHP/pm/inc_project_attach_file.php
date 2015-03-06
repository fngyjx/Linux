<?php
session_start();
include('global.php');
require_ssl();

$pid=$_REQUEST['pid'];
// print_r($_REQUEST);
//print_r($_POST);

if ( !empty($_POST)  and $pid != "" ) {
//print_r($_FILES);

	$target_path = "uploads/";
	$file_name=basename( $_FILES['uploadedfile']['name']);
	$file_name=str_replace(" ","_",$file_name);
	$target_path = $target_path . escape_data($file_name); 
//	echo "target_path = '".$target_path."'";
	
	if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
//		echo "The file ".  basename( $_FILES['uploadedfile']['name']). " has been uploaded";
		// record the attachement to the database
		$sql="INSERT INTO project_files ( project_id, file_name ) VALUES( '".$pid."', '".$target_path."')";
		mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL: $sql<br />");
	} else{
		echo "There was an error uploading the file, please try again!";
	}
	include('inc_onper_reload_self_close.php');
}

?>

<form enctype="multipart/form-data" action="inc_project_attach_file.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
<input type="hidden" name="pid" value="<?php echo $pid;?>">
<b>Upload File:</b> <input name="uploadedfile" type="file" size="60" />&nbsp;&nbsp;<input type="submit" style="font-size:12px" value="Upload" />
</form>

<?php
// list attached file(s) to the projet
$sql="SELECT * FROM project_files WHERE project_id='".$pid."' AND status=1";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
if ( mysql_num_rows($result) > 0 ) { 
	echo "<b>Attached File(s):<b><br /><img src='/images/spacer.gif' height='5'><br />";
	while( $row = mysql_fetch_array($result) ) { 
		echo "<a href='#' onClick='confirm_delete(this,".$row['ID'].");'><img src='images/delete.gif' alt='delete' style='border:none' align='bottom'></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='". $row['file_name'] ."' target='_blank'>". basename($row['file_name']) ."</a>&nbsp;<br /><img src='/images/spacer.gif' height='5'><br />";
	}
}
?>
	
<SCRIPT language="javascript">
function confirm_delete(obj,fid) {
	if ( confirm("Are you sure you want to delete the file?") ) {
		obj.target='_blank';
		obj.href='delete_project_file.php?file_id='+fid; 
	} else {
		document.location.reload(); //if not relead, next time, the function won't work
	}
}
</SCRIPT>