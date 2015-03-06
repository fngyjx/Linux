<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) or $_SESSION['userTypeCookie'] != 1 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( $_SESSION['user_id'] != 35 and $_SESSION['user_id'] != 4 and $_SESSION['user_id'] != 5 ) {
	header ("Location: login.php?out=1");
	exit;
}

$note="";

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

include('inc_global.php');

if ( isset($_REQUEST['delete_option']) ) {
	if ( $_REQUEST['delete_option'] and ! empty($_REQUEST['option_id']) ) {
		$sql =" DELETE FROM price_quote_options WHERE option_id = '".escape_data($_REQUEST['option_id'])."'";
		//echo "<br /> $sql <br />"; 
		mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
		$note="The Price item  deleting process is finished";
	}
}

if ( ! empty($_POST) ) {

	if ( $_POST['edit_option'] ) { // edit existing option
		$option_id=escape_data($_POST['option_id']);
		$text=escape_data($_POST['price_type']);
		$sql ="UPDATE price_quote_options SET 
		value='".escape_data($_POST['option_value'])."',
		text='".escape_data($_POST['option_text'])."',
		minAmount='".escape_data($_POST['option_minamount'])."',
		minUnit='".escape_data($_POST['option_minunit'])."',
		PackInID='".escape_data($_POST['option_packinid'])."',
		Notes='".escape_data($_POST['option_note'])."'
		WHERE option_id='".$option_id."'";
		mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql </BR>");
		$note="The upate was success";

	}
	
	if ( $_POST['add_price_type'] ) { // Add pricetype into price_quote_option_types
		$text=escape_data($_POST['price_type']);
		$sql =" SELECT * from price_quote_option_types WHERE text = '".$text."'";
		$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
		if ( mysql_num_rows($result) == 0 and $text != "") {
			$sql="INSERT INTO price_quote_option_types (text) VALUES('".$text."')";
			mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql </BR>");
			$note="The new price type <span style='font-color:red;'> ". $text."</span> is added";

		} else {
			$note="The <span style='font-color:red;'". $text . "</span> price type already exists";

		}
	}
	
	if ( $_POST['add_option_type'] ) {
		$sql = "SELECT * FROM price_quote_options WHERE option_type_id = '".escape_data($_POST['price_type']) ."'
		AND value = ".escape_data($_POST['value'])." AND text = '".escape_data($_POST['text'])."'";
		//echo "<br /> $sql <br />";
		$result = mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
		if ( mysql_num_rows($result) == 0 ) {
			$sql = "INSERT INTO price_quote_options (option_type_id, value,text,minAmount,minUnit,PackInID,Notes) 
			VALUES('".escape_data($_POST['price_type']) ."',".escape_data($_POST['value']).",'".escape_data($_POST['text'])."'
			,".str_replace(",","",escape_data($_POST['minAmount'])).",'".escape_data($_POST['minUnit'])."','".escape_data($_POST['PackInId'])."',
			'".escape_data($_POST['Notes'])."')";
			mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql </BR>");
		//	echo "<br /> $sql <br />";
			$note="The new price option is added";
						
		} else {
			$note="The price option already exists";
			
		}
	}
}


include("inc_header.php"); ?>



<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} 
if ( $note != "") {
	echo "<B>".$note."</B><BR />";
}

?>


<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR="#9966CC"><TR><TD>
<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>
<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 BGCOLOR="#F3E7FD"><TR><TD>

<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=1 width="700">
<TR><TH colspan='6' align='left'><b>Price Type</b></TH></TR>

<?php //list all price options - spry dry 
	$price_type="";
	$sql="select option_id, option_type_id, price_quote_option_types.text as type_description, value, price_quote_options.text as value_text
	, minAmount, minUnit, PackInID, Designation, price_quote_options.Notes from price_quote_options LEFT JOIN price_quote_option_types ON price_quote_options.option_type_id=price_quote_option_types.type_id
	LEFT JOIN productmaster ON ProductNumberInternal=PackInID
	order by option_type_id, type_description, value, value_text";
	
	$result = mysql_query($sql,$link) or die ( mysql_error() ." Failed Execute SQL : $sql ");
	$i=0;
	if ( mysql_num_rows($result) > 0) {
		while($row=mysql_fetch_array($result)){
			
			if ( $price_type != $row['option_type_id'] ) {

				if ( $i > 0 ) {
					echo "<TR><TD colspan='6' align='right'><div id='addOption_".$i."'><INPUT type='button' value='Add Price' onClick='AddOption(".$price_type.",".$i.")'></div></TD></TR>";
					echo "";  
				}
				echo "<TR><TD><B>". $row['type_description'] ."</B></TD><TD><b>Description</b></TD><TD><b>Price </b>($)</TD><TD><b> Min. Order</b></TD><TD><b>Pack In</b></TD><TD><B>Notes</B></TD></TR>";
			}
		?>
			<TR><FORM id="edit_form_<?php echo $i;?>" action="admin_quote_price.php" method='post'>
			<input type='hidden' name='option_id' value='<?php echo $row['option_id'];?>'>
			<TD align="right">
				<A href='javascript:edit_quote_option(<?php echo $i;?>)'><img src="images/pencil.gif"></a>
				<A href='JavaScript:confirmToDelete(<?php echo $row['option_id'];?>)'><img src='images/delete.gif'></a>
			</TD>
			<TD><input type='text' name='option_text' id='option_text_<?php echo $i;?>' readonly='readonly' value='<?php echo $row['value_text'];?>'></TD>
			<TD><input type='text' name='option_value' id='option_value_<?php echo $i;?>' readonly='readonly' value='<?php echo $row['value'];?>'></TD>
			<TD><input type='text' name='option_minamount' id='option_minamount_<?php echo $i;?>' value='<?php echo $row['minAmount'] == "" ? 0 : $row['minAmount'] ;?>' readonly='readonly'> 
			<select name='option_minunit' id='option_minunit_<?php echo $i;?>' disabled='disabled'>
				<option value='lbs' <?php echo $row['minUnit'] == 'lbs' ? "selected" : ""?>>lbs</option>
				<option value='grams' <?php echo $row['minUnit'] == 'grams' ? "selected" : ""?>>grams</option>
				<option value='kg' <?php echo $row['minUnit'] == 'kg' ? "selected" : ""?>>kg</option>
				<option value='N/A' <?php echo $row['minUnit'] == 'N/A' ? "selected" : ""?>>N/A</option>
			</select>
				</TD>
			<TD><select name='option_packinid' id='option_packinid_<?php echo $i;?>' disabled='disabled'>
				<option value=''></option>
			<?php 
				$sql="SELECT Designation, ProductNumberInternal FROM productmaster WHERE ProductNumberInternal like '6%'";
				$result_pkin=mysql_query($sql,$link) or die ( mysql_error() . " Failed execute SQL : $sql <br />");
				while ( $row_pkin = mysql_fetch_array($result_pkin) ) {
					echo "<option value='".$row_pkin['ProductNumberInternal']."' ";
					$selected=($row_pkin['ProductNumberInternal'] == $row['PackInID'] ) ? "selected" : "";
					echo $selected;
					echo ">" .$row_pkin['Designation']."</option>";
				}
			?>
				</select>
			</TD>
			<TD><input type='text' name='option_note' id='option_note_<?php echo $i;?>' value='<?php echo $row['Notes'];?>' readonly='readonly'>
			<br />
			<input type='submit' value='submit' name="edit_option" style='visibility:hidden' id='submit_<?php echo $i;?>'>
			<input type='button' value='Cancel' style='visibility:hidden' id='cancel_<?php echo $i;?>' onClick='window.location.reload();'>
			</TD></FORM></TR>
			
		<?php	
			$price_type=$row['option_type_id'];
			$i++;
		}
		echo "<TR><TD colspan='6' align='right' valign='top'><div id='addOption_".$i."'><INPUT type='button' value='Add Price' onClick='AddOption(".$price_type.",".$i.")'></div></TD></TR>";
	} 
?>

	<?php
		$sql="SELECT * FROM price_quote_option_types  where type_id not in 
		( SELECT DISTINCT option_type_id FROM price_quote_options ) and text <> '' order by text";
		$result=mysql_query($sql,$link) or die ( mysql_error(). " Failed EXECUTE SQL : $sql <br />");
		if ( mysql_num_rows($result) > 0 ) {
		?><FORM action='admin_quote_price.php' method='post'>
			<TR>
			<INPUT type='hidden' name='add_option_type' value='1'>
			<TD>Price Type: <SELECT name='price_type'>
		<?php
			while ( $row = mysql_fetch_array($result) ) {
				echo "<OPTION value='".$row['type_id']."'>".$row['text']."</OPTION>";
			}
			?>
			</SELECT></TD>
			<TD><b>Price:</b><br /><input type='text' name='value'></TD>
			<TD><b>Description:</b><br /><input type='text' name='text'></TD>
			<TD><b>Min Order:</b><br /><input type='text' name='minAmount' value='0'><br /><b>Unit:</b>
			<SELECT name="minUnit"><OPTION value="lbs">lbs</OPTION>
				<OPTION value="grams">grams</OPTION>
				<OPTION value="kg">kg</OPTION>
				<OPTION value="N/A">N/A</OPTION></SELECT></TD>
			<TD colspan='2'><b>Packed In:</b><br /><SELECT name="PackInID">
			<?php
				$sql = " SELECT ProductNumberInternal, Designation FROM productmaster WHERE ProductNumberInternal LIKE '6%'";
				$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
				echo "<OPTION value=''></OPTION>";
				while ( $row = mysql_fetch_array($result) ) {
					echo "<OPTION value='".$row['ProductNumberInternal']."'>".$row['Designation']."</OPTION>\n";
				}
			?>
			</SELECT><br /><b>Notes:</b><br /><input type='text' name='Notes' size='100'></TD></tr><tr><td colspan='6'><input type='submit' value="Add Option"></TD>
			</TR></FORM>
	<?php	
	}
	?>


</TABLE>
<h5>Add New Price Type</h5>
<FORM action='admin_quote_price.php' method='post'>
<INPUT type='hidden' name='add_price_type' value='1'>
<TABLE><TR><TD>Description:</td><td><input type='text' name="price_type" size="30"></TD>
	<TD><input type='submit' value="Add Type"></TD></TR></TABLE>
</FORM>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE><BR><BR>

<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

function AddOption(type_id,fid) {
	document.getElementById("addOption_"+fid).innerHTML="<FORM action='admin_quote_price.php' method='post'>"+
	"<input type='hidden' name='add_option_type' value='1'><input type='hidden' name='price_type' value='"+type_id+"'>"+
		"<table><tr><th>Price</th><th>Description</th><th>Min. Order</th><th>Unit</th><th>PackIn</th></tr><tr><td><input type='text' name='value' size='10'></td><td><input type='text' name='text' size='30'></td>"+
		"<td><input type='text' name='minAmount' value='0'></td><td><select name='minUnit'><option value='lbs'>lbs</option>"+
		"<option value='grams'>grams</option><option value='kg'>kg</option><option value='N/A'>N/A</option></select></td> "+
		"<td><select name='PackInId'><?php 
			$sql="SELECT ProductNumberInternal, Designation FROM productmaster WHERE ProductNumberInternal LIKE '6%'";
			$result=mysql_query($sql,$link) or die ( mysql_error() . " Failed Execute SQL : $sql <br />");
			echo "<option value=''></option>";
			while ( $row = mysql_fetch_array($result) ) {
				echo "<option value='".$row['ProductNumberInternal']."'>".$row['Designation']."</option>";
			}
		?></select></td></tr><tr><td>Notes:</td><td colspan='4'><input type='text' name='Notes' style='width: 700px'></td></tr></table>&nbsp;<input type='submit' value='submit'><input type='submit' value='cancel' onClick='window.location.reload()'></FORM>";
}

function confirmToDelete(opt_id) {
	if ( confirm("Are you sure that you want to delete the item?") ) 
		window.location.href="admin_quote_price.php?option_id="+opt_id+"&delete_option=1";
}

function edit_quote_option(item) {
	document.getElementById("option_text_"+item).removeAttribute('readOnly');
	document.getElementById("option_value_"+item).removeAttribute('readOnly');
	document.getElementById("option_minamount_"+item).removeAttribute('readOnly');
	document.getElementById("option_minunit_"+item).removeAttribute('disabled');
	document.getElementById("option_packinid_"+item).removeAttribute('disabled');
	document.getElementById("option_note_"+item).removeAttribute('readOnly');
	document.getElementById("cancel_"+item).style.visibility="visible";
	document.getElementById("submit_"+item).style.visibility="visible";
}
 // End -->
</SCRIPT>





<?php include("inc_footer.php"); ?>