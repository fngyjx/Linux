<?php 
include('global.php');
session_start();

if ( ! $_SESSION['uLoggedInCookie'] AND ! $_COOKIE["uLoggedInCookie"] ) {
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

$project_id="";
if ( ! empty($_REQUEST) ){
	$project_id=escape_data($_REQUEST['pid']);
}

if ( ! empty($_POST) ) {
	$follow_up_notes=escape_data($_POST['follow_up_notes']);
	$next_follow_up_dateA=explode("/",$_POST['next_follow_up_date']);
	$next_follow_up_date=$next_follow_up_dateA[2]."-".$next_follow_up_dateA[0]."-".$next_follow_up_dateA[1];

	$sql="UPDATE projects set follow_up_notes='".$follow_up_notes."' , sales_follow_up='1', next_follow_up_date='".$next_follow_up_date."' WHERE project_id='".$project_id."'";
//	echo "<br />$sql<br >";
	mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL: $sql <br />");
	
	echo "<script>window.opener.location.reload();window.close();</script>";
	exit();
	
}

$sql="SELECT follow_up_notes,next_follow_up_date FROM projects WHERE project_id='".$project_id."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed to execute SQL: $sql");
$row=mysql_fetch_array($result);

?>
<HTML><HEAD><TITLE>FollowupProject</TITLE>
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
<link type="text/css" href="js/custom-theme/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
<script type="text/javascript" language="javascript" src="js/autocomplete/jquery.autocomplete.js"></script>
<link rel="stylesheet" href="js/autocomplete/jquery.autocomplete.css" type="text/css" />
<script type="text/javascript" language="javascript" src="js/helpers.js"></script>

<script type="text/javascript">
$(function() {
	$('#datepicker').datepicker({
		 buttonImageOnly: true ,
		changeMonth: true,
		changeYear: true
	});
});

</script>
</HEAD>
<BODY BGCOLOR="<?php echo $bgcolor;?>">
<?php if ( $_SESSION['userTypeCookie'] == 1 or $_COOKIE['userTypeCookie'] == 1
or $_SESSION['userTypeCookie'] == 2 or $_COOKIE['userTypeCookie'] == 2 ) { ?>
<FORM ACTION="FollowUpProject.php" METHOD="post">
<INPUT TYPE="hidden" NAME="pid" VALUE="<?php echo $project_id?>">
<NOBR><B>Next Follow Up Date</B>
<input type="text" name="next_follow_up_date" value="<?php echo (empty($row['next_follow_up_date']) ? "" : date("m/d/Y",strtotime($row['next_follow_up_date'])) );?>" id="datepicker" SIZE="20">
</NOBR><br />
<br /><B>Follow Up Notes</B><br />
<TEXTAREA NAME="follow_up_notes" rows="2" cols="50" ondblclick="submit();"><?php echo $row[0];?></TEXTAREA><br /><br />
<input type="submit" value="Submit">
</FORM>
</BODY></HTML>
<?php } ?>