<?php 
include('global.php');
session_start();

if ( ! $_SESSION['uLoggedInCookie'] AND ! $_COOKIE["uLoggedInCookie"]  ) {
	header ("Location: login.php?out=1");
	exit;
}


if ( isset($_SESSION['pid']) ) {
	unset($_SESSION['pid']);
	header("location: projects_history.php");
	exit();
}



if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

$follow_up_array = array("Hot","Warm","Cold","Won","Lost","Cancelled","");
$follow_up_num = array(1,2,3,4,5,6,7);

$bgcolor=escape_data($_REQUEST['bgcolor']);

if ( ! empty($_REQUEST) ){

$project_id=escape_data($_REQUEST['project_id']);
$salesperson=escape_data($_REQUEST['salesperson']);
$company_id=escape_data($_REQUEST['company_id']);
$flavor_id=escape_data($_REQUEST['flavor_id']);
$application=escape_data($_REQUEST['application']);

}

if ( ! empty($_POST) ) {
	$follow_up=escape_data($_POST['follow_up']);

	$sql="UPDATE projects set follow_up='".$follow_up."' WHERE project_id='".$project_id."'";
//	echo "<br />$sql<br >";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL: $sql <br />");
}

$sql="SELECT follow_up FROM projects WHERE project_id='".$project_id."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed to execute SQL: $sql");

$row=mysql_fetch_array($result);

?>
<HTML><HEAD><TITLE>SetProjectFollowup</TITLE></HEAD>
<BODY style="margin:0px 0px 0px 0px" BGCOLOR="<?php echo $bgcolor;?>">
<?php if ( $_SESSION['userTypeCookie'] == 1 or $_SESSION['userTypeCookie'] == 2 ) { ?>
<FORM ACTION="setProjectFollowUp.php" METHOD="post">
<INPUT TYPE="hidden" NAME="project_id" VALUE="<?php echo $project_id?>">
<INPUT TYPE="hidden" NAME="bgcolor" VALUE="<?php echo $bgcolor?>">
 <SELECT NAME="follow_up" onChange="submit()" STYLE="font-size:7pt; text-algin:left top">
<?php
	foreach ( $follow_up_num as $value ) {
	if ( $value == $row['follow_up'] ) { ?>
		<OPTION VALUE="<?php echo $value?>" SELECTED><?php echo $follow_up_array[$value-1];?></OPTION>
<?php } else { ?>
		<OPTION VALUE="<?php echo $value?>"><?php echo $follow_up_array[$value-1];?></OPTION>
<?php }
} ?>
</SELECT>
</FORM>
<?php } else {
	echo $follow_up_array[$row['follow_up']-1];
}
?>
</BODY></HTML>
