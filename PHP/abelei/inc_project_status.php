<?php

$sql = "SELECT status FROM projects WHERE project_id = " . $_SESSION['pid'];
$result = mysql_query($sql, $link);
$row = mysql_fetch_array($result);
$status = $row['status'];

if ( $status == 1 ) {

	$sql = "SELECT project_info_submitted, client_info_submitted, sample_info_submitted FROM projects WHERE project_id = " . $_SESSION['pid'];
	$result = mysql_query($sql, $link);
	$c = mysql_num_rows($result);
	$row = mysql_fetch_array($result);

	if ( $row['project_info_submitted'] == 1 and $row['client_info_submitted'] == 1 and $row['sample_info_submitted'] == 1 ) { ?>
		<FORM><INPUT TYPE="button" VALUE="Send to lab" onClick="window.location='project_management_admin.sales.php?stat=2'"></FORM>
	<?php }
	else { ?>
		<FORM><INPUT TYPE="button" VALUE="Send to lab" readonly='readonly'> <B>Incomplete:</B>
		<?php if ( $row['project_info_submitted'] == 0 ) { ?>
			<I>&bull;Sales Info</I>
		<?php } ?>
		<?php if ( $row['client_info_submitted'] == 0 ) { ?>
			<I>&bull;Contact Info</I>
		<?php } ?>
		<?php if ( $row['sample_info_submitted'] == 0 ) { ?>
			<I>&bull;Sample Info</I>
		<?php } ?>
		</FORM>
	<?php }
}


if ( $_SESSION['userTypeCookie'] == 3 ) {

	//$sql = "SELECT * FROM lab_assignees WHERE project_id = " . $_SESSION['pid'];
	//$result = mysql_query($sql, $link);
	//$i = mysql_num_rows($result);

	?>
	<FORM>
	<?php

	//if ( $status == 2 and $_SESSION['userTypeCookie'] == 3 ) {
	if ( $status == 2 ) {
		//if ( $i > 0 ) { ?>
			<INPUT TYPE="button" VALUE="Send to front desk" onClick="window.location='project_management_admin.sales.php?stat=3'">
		<?php //}
		//elseif ( $i == 0 ) { ?>
			<!-- <INPUT TYPE="button" VALUE="Send to front desk" readonly='readonly'>  <B>Incomplete:</B> -->
<!-- 			<I>&bull;No assignees</I></FORM> -->
		<?php //}

	}

}

?>

</FORM>