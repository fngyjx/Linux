<HTML>
<HEAD>
	<TITLE> abelei </TITLE>
	<LINK HREF="styles.css" REL="stylesheet">

<STYLE TYPE="text/css">
     P.breakhere {page-break-before: always}
</STYLE>

<SCRIPT LANGUAGE=JAVASCRIPT>
 <!-- Hide

salesOut = new Image
salesOut.src = "images/tabs/sales_out.gif"
salesOver = new Image
salesOver.src = "images/tabs/sales_over.gif"

clientOut = new Image
clientOut.src = "images/tabs/client_out.gif"
clientOver = new Image
clientOver.src = "images/tabs/client_over.gif"

notesOut = new Image
notesOut.src = "images/tabs/notes_out.gif"
notesOver = new Image
notesOver.src = "images/tabs/notes_over.gif"

sampleOut = new Image
sampleOut.src = "images/tabs/sample_out.gif"
sampleOver = new Image
sampleOver.src = "images/tabs/sample_over.gif"

summaryOut = new Image
summaryOut.src = "images/tabs/summary_out.gif"
summaryOver = new Image
summaryOver.src = "images/tabs/summary_over.gif"

 // -->
</SCRIPT>
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
<link type="text/css" href="js/custom-theme/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
<script type="text/javascript" language="javascript" src="js/autocomplete/jquery.autocomplete.js"></script>
<link rel="stylesheet" href="js/autocomplete/jquery.autocomplete.css" type="text/css" />
<script type="text/javascript" language="javascript" src="js/helpers.js"></script>
<script type="text/javascript" language="javascript" src="js/editable_dropdown.js"></script>
<script type="text/javascript">
<!--
function popup(url, width, height, left, top) {
	if (width === undefined) {
		width = 820;
	}
	if (height === undefined) {
		height = 680;
	}
	if (left === undefined) {
		left  = (screen.width  - width)/2;
	}
	if (top === undefined) {
		top  = (screen.height - height)/2;
	}
	var params = 'width='+width+', height='+height;
	params += ', top='+top+', left='+left;
	params += ', directories=no';
	params += ', location=no';
	params += ', menubar=yes';
	params += ', resizable=yes';
	params += ', scrollbars=yes';
	params += ', status=no';
	params += ', toolbar=no';
	var newwin=window.open(url,'_balnk', params);
	if (window.focus) {newwin.focus()}
	return false;
}
-->
</script>
</HEAD>

<BODY LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" MARGINHEIGHT="0" BGCOLOR="#99CC33">


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" BGCOLOR="#99CC33" WIDTH="100%"><TR><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" BGCOLOR="#99CC33">
	<TR>
		<TD><A HREF="index.php"><IMG SRC="images/nameplate.gif" WIDTH="418" HEIGHT="67" BORDER="0"></A></TD>
	</TR>
</TABLE>
</TD></TR>
<TR><TD BGCOLOR="#976AC2"><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="2" BORDER="0"></TD></TR>
</TABLE>


<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" BGCOLOR="#FFFFFF" WIDTH="100%"><TR><TD>



<?php if ( $_SESSION['uLoggedInCookie'] ) { ?>

<TABLE BORDER="0" WIDTH="800" CELLSPACING="0" CELLPADDING="0">
	<TR>
		<TD ALIGN=RIGHT><IMG SRC="images/spacer.gif" WIDTH="1" HEIGHT="2" BORDER="0"><BR>

<?php if ( $_SESSION['userTypeCookie'] == 1 ) { ?>

	<A HREF="index.php">Home</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="front_desk_admin.php">Front desk</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="projects.php">Projects</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="projects_history.php">Completed</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="users.php">Users</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="choose_client.php">Clients</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="choose_company.php">Companies</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="login.php?out=1">Logout</A>

<?php } elseif ( $_SESSION['userTypeCookie'] == 2 ) { ?>

	<A HREF="index.php">Home</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="projects.php">Projects</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="projects_history.php">Completed</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="choose_client.php">Clients</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="choose_company.php">Companies</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="login.php?out=1">Logout</A>

<?php } elseif ( $_SESSION['userTypeCookie'] == 3 ) { ?>

	<A HREF="index.php">Home</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="projects_history.php">Completed</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="login.php?out=1">Logout</A>

<?php } elseif ( $_SESSION['userTypeCookie'] == 4 ) { ?>

	<A HREF="index.php">Home</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="projects_history.php">Completed</A> <SPAN STYLE="color:#999999">|</SPAN> 
	<A HREF="login.php?out=1">Logout</A>

<?php } ?>

		</TD>
	</TR>
</TABLE>

<?php } ?>



<BR><BR>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH=750>
	<TR VALIGN=TOP>
		<TD WIDTH=50><IMG SRC="images/spacer.gif" WIDTH="50" HEIGHT="420" BORDER="0"></TD>
		<TD>