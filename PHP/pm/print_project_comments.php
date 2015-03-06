<?php 
session_start();
include('global.php');
require_ssl();
// phpinfo();

if ( ! $_SESSION['uLoggedInCookie'] AND ! $_COOKIE["uLoggedInCookie"] ) {
	 header ("Location: login.php?out=1");
	 exit;
}

// print_r($_REQUEST);

$pid="";
$pid=$_REQUEST['pid'];

if ( empty($pid) ) {
	$pid = $_SESSION['pid'];
	echo "pid=".$pid;
}

if ( empty($pid) ) {
	echo "pidinexit=".$pid;
	$_SESSION['note']="The Project ID is needed to print comments";
	echo "<script>window.close();</script>";
	exit();
}

$project_type_array = array("New","Revision","Resample","Other");
$project_type_num = array(1,2,3,4);

$priority_array = array("Low","Medium","High");
$priority_num = array(1,2,3);

$status_array = array("Sales","Lab","Front desk","Shipped","Cancelled");
$status_num = array(1,2,3,4,5);

$n_a1_array = array("Org-Cert","Org-Comp","Natural","WONF","NI","N&A","Art");
$n_a1_num = array(1,2,3,4,5,6,7);

$n_a2_array = array(""," Or Higher","Org-Cert","Org-Comp","Natural","WONF","NI","N&A","Art");
$n_a2_num = array(1,2,3,4,5,6,7,8,9);

$form_array = array("Liquid","Powder","Emulsion","Other");
$form_num = array(1,2,3,4);

$product_type_array = array("W.S.","O.S.","Plated","S.D.");
$product_type_num = array(1,2,3,4);

$kosher_array = array("No","Pareve","Dairy","Passover");
$kosher_num = array(1,2,3,4);

$halal_array = array("No","Yes","DNA");
$halal_num = array(1,2,3);

$base_included_array = array("Yes","No","En route");
$base_included_num = array(1,2,3);

$target_included_array = array("Yes","No","En route");
$target_included_num = array(1,2,3);

$target_rmc_array = array("DNA","Forthcoming");
$target_rmc_num = array(1,2);

$cost_in_use_measure_array = array("lb.","gallon","kilo");
$cost_in_use_measure_num = array(1,2,3);

$suggested_level_array = array("Use as desired","Same as target","Other");
$suggested_level_num = array(1,2,3);



?>
<HTML><HEAD><TITLE>Print Project# <?php echo $pid;?></TITLE></HEAD>
<BODY bgcolor="#FFFFFF">
<?php $print_diabled="disabled='disabled'"; include('inc_project_header.php');

$sql="SELECT * FROM projects WHERE project_id='".escape_data($project_id)."'";
$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL :$sql<br/>");
$row=mysql_fetch_array($result);
if ( $row['comments'] != "" ) {
?>
<TABLE><TR>
<TD style="font-size:0.8em"><b>Lab Assignees:</b></TD>
<TD colspan="4" style="font-size:0.8em">
<?php $sql="SELECT concat(users.first_name,' ',users.last_name) as labName FROM lab_assignees 
LEFT JOIN users USING(user_id) WHERE project_id='".$project_id."'";
$result_lab=mysql_query($sql,$link);
$labUser="";
while( $row_lab = mysql_fetch_array($result_lab) ) {
	$labUser .= ",&nbsp;&nbsp;".$row_lab[0];
}
echo substr($labUser,1);
?>
</TD>
<TD><IMG src="images/spacer.gif" width="10px"></TD>
<TD style="font-size:0.8em"><b>Flavors:</b></TD>
<TD style="font-size:0.8em"><?php
$sql="SELECT concat(flavor_id,' ',flavor_name) as flavor FROM flavors WHERE project_id='".$project_id."'";
$result_flavor=mysql_query($sql,$link);
$flavor="";
while ( $row_flavor=mysql_fetch_array($result_flavor) ) {
	$flavor=",<BR>".$row_flavor[0];
}
echo substr($flavor,5);
?>
</TD>
</TR>
<TR><TD style="font-size:0.8em"><b>N_or_A:</b></TD>
<TD style="font-size:0.8em"><?php echo ( $row['n_a1'] == "" ? "" : "Prefered: ". $n_a1_array[$row['n_a1']-1]) . ( $row['n_a2'] == "" ? "" : "<BR>Optional: ".$n_a2_array[$row['n_a2']-1] )?></TD>
<TD><IMG src="images/spacer.gif" width="10px"></TD>
<TD style="font-size:0.8em"><b>Form:</b></TD>
<TD style="font-size:0.8em"><?php echo ( $row['form'] == "" ? "" : "Prefered: ". $form_array[$row['form']-1]) . ( $row['form_2'] == "" ? "" : "<BR>Optional: ".$form_array[$row['form_2'] - 1])?></TD>
<TD><IMG src="images/spacer.gif" width="10px"></TD>
<TD style="font-size:0.8em"><b>Product Type:</b></TD>
<TD style="font-size:0.8em"><?php echo ( $row['product_type'] == "" ? "" : "Prefered: ". $project_type_array[$row['product_type'] - 1]) . ( $row['product_type_2'] == ""  ? "" : "<BR>Optional: ".$project_type_array[$row['product_type_2'] - 1] )?></TD>
</TR><TR><TD style="font-size:0.8em"><b>Kosher:</b></TD>
<TD style="font-size:0.8em"><?php echo $kosher_array[$row['kosher']-1] ?></TD>
<TD><IMG src="images/spacer.gif" width="10px"></TD>
<TD style="font-size:0.8em"><b>Halal:</b></TD>
<TD style="font-size:0.8em"><?php echo $halal_array[$row['halal']-1] ?></TD>
<TD><IMG src="images/spacer.gif" width="10px"></TD>
<TD style="font-size:0.8em"><b>Sample Size:<b></TD>
<TD style="font-size:0.8em"><?php echo ($row['sample_size'] == 5 ? $row['sample_size_other'] : $sample_size_array[$row['sample_size']-1])?></TD>
</TR>
<TR><TD style="font-size:0.8em"><b>Base Included:</b></TD>
<TD style="font-size:0.8em"><?php echo $base_included_array[$row['base_included']-1] ?></TD>
<TD><IMG src="images/spacer.gif" width="10px"></TD>
<TD style="font-size:0.8em"><b>Target Included:</b></TD>
<TD style="font-size:0.8em"><?php echo $target_included_array[$row['target_included']-1]?></TD>
<TD><IMG src="images/spacer.gif" width="10px"></TD>
<TD style="font-size:0.8em"><b>Target Use Level:</b></TD>
<TD style="font-size:0.8em"><?php echo $row['target_use_level'];?></TD>
</TR>
</TABLE>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
<TR VALIGN="TOP" width="600"><TD><b style="font-size:0.8em;"><hr />Comments:</b><hr /></TD><TR>
	<TR VALIGN="TOP" width="600">

		<TD style="font-size:0.8em"><?php echo str_replace(array("\r\n","\n","\r"),"<BR>", $row['comments']);?></TD>
	</TR>
</TABLE>

<?php }
else {
echo "No comments for project#:".$project_id;
}
 ?>
 </BODY></HTML>