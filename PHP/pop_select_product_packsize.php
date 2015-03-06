<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN, LAB and FRONT DESK HAVE PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 and $rights != 3 and $rights != 6 ) {
	header ("Location: login.php?out=1");
	exit;
}

include('inc_global.php');

$error_found="";
$Designation="";
$note="";

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( isset($_SESSION[error_message]) ) {
	$error_message = $_SESSION[error_message];
	$error_found=true;
	unset($_SESSION['error_message']);
}

$edit = false;

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ( 'edit' == $action ) $edit = true;

$pni = isset($_REQUEST['pni']) ? $_REQUEST['pni'] : '';

//print_r($_REQUEST);

if ($pni == "" )  {
	$_SESSION['error_message'] = "Product Number is required, please provide it as pni=pni";
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
	exit();
} 

if ( !empty($_POST) ) {
	//$prdpackszid = $_POST['PrdPackSizeID'];
	$PackIn = $_POST['PackIn'];
	$PackSize = $_POST['PackSize'];
	$UnitOfMeasure = $_POST['Units'];
	$PackagingType = $_POST['PackagingType'];
	$ProductNumberExternal = $_POST['ProductNumberExternal'];
	check_field($PackIn, 1, 'Pack in');
	check_field($PackSize, 3, 'Package Size');
	
	if (0> $PackageSize) {
		$error_found=true;
		$error_message = "Package Size must be positive<BR>";
	}

	if ( !$error_found ) {
		if ( $eidt ) {
			$sql = "UPDATE productpacksize set PackIn='$PackIn', PackSize=$PackSize, UnitOfMeasure='$UnitOfMeasure'
			, PackagingType='$PackagingType', Default=1 WHERE id=$prdpackszid";
			mysql_query($sql,$link) or die ( mysql_error() . " Failed to execute SQL : $sql <br />");
		} else if ( $action == "add" ) {
			$sql = "SELECT Designation FROM productmaster WHERE ProductNumberInternal = '$PackIn'";
			$result = mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
			$row=mysql_fetch_array($result);
			$PackagingType=$row[0];
			$sql = "INSERT INTO productpacksize ( ProductNumberInternal, PackSize, UnitOfMeasure, PackagingType,
			ProductNumberExternal,PackIn,DefaultPksz ) VALUES ('$pni', $PackSize, '$UnitOfMeasure','$PackagingType',
			'$ProductNumberExternal','$PackIn',1)";
			start_transaction($link);
			if ( ! mysql_query($sql,$link) ) {
				echo mysql_error() . " Failed execute SQL : $sql <br />";
				end_transaction(0,$link);
				die;
			}
			
			$prdpkszid=mysql_insert_id();
			$sql="UPDATE productpacksize SET DefaultPksz=0 WHERE id <> $prdpkszid";
			if ( ! mysql_query($sql,$link) ) {
				echo mysql_error() . " Failed execute SQL : $sql <br />";
				end_transaction(0,$link);
				die;
			}
			end_transaction(1,$link);
		} else if ( $action == "delete" ) {
			$sql = "SELECT Default FROM productpacksize WHERE id=$prdpackszid";
			$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
			print_r($result);
			$default=$result[0];
			if ( $default != 0 )
				$note .= "Warning: The deleted packaging size is the one in default";
			$sql = "DELETE FROM productpacksize WHERE id=$prdpackszid";
			mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL: $sql <br />");
		}
	}
	
	$_SESSION['note'] = $note;
	echo "<SCRIPT TYPE='text/javascript' LANGUAGE='JavaScript'>\n";
	echo "window.opener.location.reload()\n";
	echo "window.close()\n";
	echo "</SCRIPT>\n";
	exit();

} 
include("inc_pop_header.php");

?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>

	<FORM ACTION="pop_select_product_packsize.php" METHOD="post">
		<INPUT TYPE="hidden" NAME="action" id="action" VALUE="edit">
		<INPUT TYPE="hidden" NAME="pni" VALUE="<?php echo $pni;?>">
		<?php
			$sql="SELECT ProductNumberExternal FROM externalproductnumberreference WHERE ProductNumberInternal='$pni'";
			$result = mysql_query($sql,$link) or die ( mysql_error() . " Failed Extecute SQL : $sql <br />");
			$row=mysql_fetch_array($result);
			$ProductNumberExternal=$row[0];
		?>
		<INPUT TYPE="hidden" NAME="ProductNumberExternal" VALUE="<?php echo $ProductNumberExternal;?>">
		<INPUT TYPE="hidden" NAME="pni" VALUE="<?php echo $pni;?>">
		
		<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC" WIDTH="100%"><TR><TD>
		<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD" WIDTH="100%"><TR><TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
		<?php 
		if ( $edit ) {
		$sql = "SELECT * FROM productpacksize WHERE ProductNumberInternal = " . $pni;
		// echo "<tr><td>$sql</td></tr>";
		$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		

		?>

			<TR><TH>&nbsp;</th><th>Pack Size</th><th>Units</th><th>Packaging</th><th>Default Pack</th><th>&nbsp;</th></TR>
			<?php 
			while ( $row = mysql_fetch_array($result) ) { 
				if ( $bg == 1 ) {
					$bgcolor = "#F3E7FD";
					$bg = 0;
				} else {
					$bgcolor = "whitesmoke";
					$bg = 1;
				}
			?>
			
			<TR BGCOLOR="<?php echo $bgcolor ?>" VALIGN=TOP>
				<TD>&nbsp;<A HREF="JavaScript:delete_pksz(<?php echo $row['id'];?>)"><IMG SRC="images/delete.gif" WIDTH="16" HEIGHT="16" BORDER="0"></A>&nbsp;</TD>
				<TD STYLE="font-size:8pt">&nbsp;<?php echo number_format($row['PackSize'],2);?>&nbsp;</TD>
				<TD STYLE="font-size:8pt">&nbsp;<?php echo $row['UnitOfMeasure'];?>&nbsp;</TD>
				<TD STYLE="font-size:8pt">&nbsp;<?php echo $row['PackagingType'];?>&nbsp;</TD>
				<TD STYLE="font-size:8pt">&nbsp;<?php echo ($row['DefaultPksz']) ? "Yes" : "No";?>&nbsp;</TD>
				<TD><?php if ( ! $row['DefaultPksz'] ) {?>
					<INPUT type="button" value="SetDefault" onClick="popup('pop_default_pksz.php?pkszid=<?php echo $row['id'];?>',300,400)">
					<?php } ?>
				</TD>
			</TR>
			<?php } ?>
			<TR><TD colspan="3" align="left"><input type="button" class="submit" value="Add Packaging" id="btn_add_package" onClick="document.getElementById('add_package').style.visibility='visible';document.getElementById('btn_add_package').style.visibility='hidden';document.getElementById('btn_done').style.visibility='hidden';"></TD>
				<TD colspan="3" align="left"><input type="button" class="submit" id="btn_done" value="Done" onClick="window.opener.location.reload(); window.close();"></TD>
			</TR>
			<?php 
		} ?>
		
		<TR><TD colspan="6">
		<?php if ( $action == "add" ) { ?>
		
			<div id="add_package" style="visibility:visible">
		<?php } else {?>
		<div id="add_package" style="visibility:hidden">	
		<?php } ?>
		<table>
			<TR><TD colspan='6' align="left"><B>Add Default Pakaging Size</B></TD></TR>
		<TR>
		<TD>
			<TABLE BORDER=0 CELLSPACING="0" CELLPADDING="3">
			<TR><TD colspan='6'><b>Internal#:</b> <?php echo $pni; ?>&nbsp;&nbsp;<b>External#:</b> <?php echo $ProductNumberExternal;?> </TD></TR>
			<TR VALIGN=BOTTOM>
				<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
				<TD colspan='2'><B STYLE="font-size:8pt">PackSize</B></TD>
				<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
				<TD colspan='2'><INPUT type="text" name="PackSize" VALUE="" SIZE="20"></TD>
			</TR>
			<TR VALIGN=BOTTOM>
				<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
				<TD colspan='2'><B STYLE="font-size:8pt">Units</B></TD>
				<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
				<TD colspan='2'><SELECT type="text" name="Units">
						<OPTION VALUE="lbs">lbs</OPTION>
						<OPTION VALUE="grams">grams</OPTION>
						<OPTION VALUE="kg">kg</OPTION>
					</SELECT>
				</TD>
			</TR>
			<TR VALIGN=BOTTOM>
				<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
				<TD colspan='2'><B STYLE="font-size:8pt">Packaging Type</B></TD>
				<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
				<TD colspan='2'><SELECT type="text" name="PackIn">
				<?php 
					$sql="SELECT ProductNumberInternal,Designation FROM productmaster WHERE ProductNumberInternal like '6%'";
					$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL: $sql <br />");
					while( $row = mysql_fetch_array($result) ) { ?>
						<OPTION VALUE="<?php echo $row['ProductNumberInternal'];?>"><?php echo $row['Designation'];?></OPTION>
					<?php }?>	
					</SELECT>
				</TD>
			</TR>
			<TR VALIGN=BOTTOM>
				<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
				<TD colspan='2'><B STYLE="font-size:8pt">Default</B></TD>
				<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
				<TD><INPUT type="radio" name="Default" VALUE="0">NO</TD>
				<TD><INPUT type="radio" name="Default" VALUE="1" checked="checked">YES</TD>
			</TR>
			<TR>
				<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
				<TD colspan='2' align="right"><input type="submit" value="Save" class="submit" STYLE="font-size:8pt" onClick="setAction('add')"></TD>
				<TD><IMG SRC="images/spacer.gif"  WIDTH="3" HEIGHT="1"></TD>
				<TD colspan='2' align="center"><input type="button" value="Cancel" class="submit" STYLE="font-size:8pt" onClick="window.close()"></TD>
			</TR>
			</table>
			</div>
		</TD></TR>
		</TABLE>
		</TD></TR></TABLE>
		</TD></TR></TABLE>

<BR>

<script type="text/javascript">
<!--
function setAction(action) {
	document.getElementById("action").value=action;
	//alert("Now set the action value as " + document.getElementById("action").value );
	return true;
}

function delete_pksz(pkszid) {
	if ( confirm('Are you sure you want to delete this packaging?') ) {
		window.location.href = "pop_delete_pksz.php?pkszid=" + pkszid;
	}
}
-->
</script>

<?php include("inc_footer.php"); ?>