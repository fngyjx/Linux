<?php

include('inc_ssl_check.php');
session_start();

if ( !isset($_SESSION["uLoggedInCookie"]) ) {
	header ("Location: login.php?out=1");
	exit;
}

// ADMIN HAS PERMISSIONS
$rights = $_SESSION['userTypeCookie'];
if ( $rights != 1 ) {
	header ("Location: login.php?out=1");
	exit;
}

if ( isset($_SESSION['note']) ) {
	$note = $_SESSION['note'];
	unset($_SESSION['note']);
}

if ( $_REQUEST['psn'] != '' ) {
	$psn = $_REQUEST['psn'];
} else {
	header ("Location: customers_quotes.php");
	exit;
}

include('inc_global.php');

$form_status = "";
if ( $_REQUEST['action'] != 'edit' ) {
	$form_status = "readonly=\"readonly\"";
}

if ( $_REQUEST['action'] != '' ) {
	$action = $_REQUEST['action'];
} else {
	$action = "";
}



if ( !empty($_POST) ) {
	//echo print_r($_POST);
	//die();

	$Notes = $_POST['Notes'];

	if ( !$error_found ) {
		// escape_data() FUNCTION IN global.php; ESCAPES INSECURE CHARACTERS
		$Notes = escape_data($Notes);

		$sql = "UPDATE pricesheetmaster SET Notes = '" . $Notes . "'"
		. " WHERE PriceSheetNumber = " . $psn;
		mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
		//$_SESSION['note'] = "Information successfully saved<BR>";
		if ( $_REQUEST['continue'] == "Save" ) {
			header("location: customers_quotes.comments.php?action=edit&psn=" . $psn);
			exit();
		} else {
			header("location: customers_quotes.comments.php?psn=" . $psn);
			exit();
		}
	}

} else {
	$sql = "SELECT pricesheetmaster.*, productmaster.SpecificGravity AS SpecificGravityMaster FROM pricesheetmaster LEFT JOIN productmaster USING (ProductNumberInternal)WHERE PriceSheetNumber = " . $psn;
	$result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
	$row = mysql_fetch_array($result);
	$Notes = $row['Notes'];
}

include("inc_header.php");

?>



<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

headerOut = new Image
headerOut.src = "images/tabs/quoting_flavors/header_out.gif"
headerOver = new Image
headerOver.src = "images/tabs/quoting_flavors/header_over.gif"

rmc_managementOut = new Image
rmc_managementOut.src = "images/tabs/quoting_flavors/rmc_management_out.gif"
rmc_managementOver = new Image
rmc_managementOver.src = "images/tabs/quoting_flavors/rmc_management_over.gif"

rmc_configurationOut = new Image
rmc_configurationOut.src = "images/tabs/quoting_flavors/rmc_configuration_out.gif"
rmc_configurationOver = new Image
rmc_configurationOver.src = "images/tabs/quoting_flavors/rmc_configuration_over.gif"

pricingOut = new Image
pricingOut.src = "images/tabs/quoting_flavors/pricing_out.gif"
pricingOver = new Image
pricingOver.src = "images/tabs/quoting_flavors/pricing_over.gif"

commentsOut = new Image
commentsOut.src = "images/tabs/quoting_flavors/comments_out.gif"
commentsOver = new Image
commentsOver.src = "images/tabs/quoting_flavors/comments_over.gif"

 // End -->
</SCRIPT>



<?php include("inc_quotes_header.php"); ?>

<!-- <IFRAME SRC="inc_quotes_header.php?psn=<?php //echo $psn;?>" WIDTH="900" HEIGHT="70" FRAMEBORDER="0"></IFRAME><BR> -->



<TABLE WIDTH=450 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="header.src=headerOver.src"
		onMouseOut="header.src=headerOut.src" 
		HREF="customers_quotes.header.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/header_out.gif" WIDTH=74 HEIGHT=18 BORDER=0 NAME="header"></A></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="rmc_management.src=rmc_managementOver.src"
		onMouseOut="rmc_management.src=rmc_managementOut.src" 
		HREF="customers_quotes.rmc_management.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/rmc_management_out.gif" WIDTH=143 HEIGHT=18 BORDER=0 NAME="rmc_management"></A></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="rmc_configuration.src=rmc_configurationOver.src"
		onMouseOut="rmc_configuration.src=rmc_configurationOut.src" 
		HREF="customers_quotes.rmc_configuration.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/rmc_configuration_out.gif" WIDTH=156 HEIGHT=18 BORDER=0 NAME="rmc_configuration"></A></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="pricing.src=pricingOver.src"
		onMouseOut="pricing.src=pricingOut.src" 
		HREF="customers_quotes.pricing.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/pricing_out.gif" WIDTH=77 HEIGHT=18 BORDER=0 NAME="pricing"></A></TD>
		<TD><A onFocus="if(this.blur)this.blur()"
		onMouseOver="comments.src=commentsOver.src"
		onMouseOut="comments.src=commentsOver.src" 
		HREF="customers_quotes.comments.php?psn=<?php echo $psn;?>"><IMG SRC="images/tabs/quoting_flavors/comments_over.gif" WIDTH=98 HEIGHT=18 BORDER=0 NAME="comments"></A></TD>
		<TD><IMG SRC="images/tabs/blank.gif" WIDTH="152" HEIGHT="18" ALT="Blank"></TD>
	</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
	<TR>
		<TD BACKGROUND="images/tabs/tab_rule.gif"><IMG SRC="images/tabs/tab_rule.gif" WIDTH="100%" HEIGHT="8"></TD>
	</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BGCOLOR="#976AC2" WIDTH="100%"><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="5" BGCOLOR="whitesmoke" WIDTH="100%"><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="10" BGCOLOR="whitesmoke" ALIGN=CENTER WIDTH="100%"><TR><TD>

<FORM NAME="add_ingredient" ACTION="customers_quotes.comments.php" METHOD="post">
<INPUT TYPE="hidden" NAME="psn" VALUE="<?php echo $psn;?>">
<INPUT TYPE="hidden" NAME="action" VALUE="<?php echo $action;?>">


<?php if ( $error_found ) {
	echo "<B STYLE='color:#FF0000'>" . $error_message . "</B><BR>";
} ?>

<?php if ( $note ) {
	echo "<B STYLE='color:#990000'>" . $note . "</B><BR>";
} ?>


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
	<TR VALIGN=TOP>
		<TD>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3">

	<TR VALIGN=BOTTOM>
		<TD><TEXTAREA NAME="Notes" ROWS=15 COLS=70 <?php echo $form_status;?>><?php echo $Notes;?></TEXTAREA></TD>
		<TD>
		<?php if ( $form_status != '' ) { ?>
			<INPUT TYPE="button" VALUE="Edit" CLASS="submit" onClick="window.location='customers_quotes.comments.php?action=edit&psn=<?php echo $psn;?>'">
		<?php } else { ?>
			<INPUT TYPE="submit" VALUE="Save" CLASS="submit"> <INPUT TYPE="button" VALUE="Cancel" CLASS="submit" onClick="window.location='customers_quotes.comments.php?psn=<?php echo $psn;?>'">
		<?php } ?>
		</TD>
	</TR>

</TABLE>

		</TD>
	</TR>
</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</TD></TR></TABLE></FORM><RB>

<?php include("inc_footer.php"); ?>