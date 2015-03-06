<?php 

session_start();

include('global.php');
require_ssl();

$suggested_level_array = array("Use as desired","Same as target","Other");
$suggested_level_num = array(1,2,3);

$application_array = array("Baked Good","Beverage","Cereal","Confection","Dairy Product","Pet Food","Nutraceutical","Pharmaceutical","Prepared Food","Snack");
$application_num = array(1,2,3,4,5,6,7,8,9,10);

?>



<HEAD>
	<TITLE> abelei labels </TITLE>

<LINK HREF="styles.css" REL="stylesheet">

<STYLE TYPE="text/css">
     P.breakhere {page-break-before: always}
</STYLE>

</HEAD>

<BODY LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" MARGINHEIGHT="0" BGCOLOR="#FFFFFF" onLoad="print()">

<?php

$message .= "<TABLE CELLPADDING=0 CELLSPACING=0 WIDTH=600 ALIGN=CENTER><TR><TD>";

$sql = "SELECT first_name, last_name, company, application FROM projects LEFT JOIN clients USING(client_id) LEFT JOIN companies USING(company_id) WHERE project_id = " . $_GET['pid'];
$result = mysql_query($sql, $link);
$row = mysql_fetch_array($result);

$sales_name = $row['first_name'] . " " . $row['last_name'];
$company = $row['company'];
$application = $row['application'];

$date = date("m/d/y");

$sql = "SELECT * FROM flavors WHERE project_id = " . $_GET['pid'] . " ORDER BY flavor_name";
$result = mysql_query($sql, $link);
$c = mysql_num_rows($result);
$x = 0;
if ( $c == 0 ) {
	$message .= "<I>None entered</I>";
}
else {
	while ( $row = mysql_fetch_array($result) ) {
		$x = $x + 1;
		for ( $i=1; $i<=2; $i++ ) {
			$label = "<TABLE BORDER='0' WIDTH='360' CELLSPACING='0' CELLPADDING='10'><TR>";
			$label .= "<TD WIDTH='80'><IMG SRC='http://www.chicagoconsulting.net/images/home_2005/spacer.gif' WIDTH='80' HEIGHT='156'></TD>";
			$label .= "<TD WIDTH='240'><IMG SRC='http://www.chicagoconsulting.net/images/home_2005/spacer.gif' WIDTH='240' HEIGHT='1'><BR>";
			$label .= "<TABLE BORDER='0' WIDTH='240' CELLSPACING='0' CELLPADDING='0'><TR ALIGN=CENTER>";
			$label .= "<TD ALIGN=CENTER><IMG SRC='http://www.chicagoconsulting.net/images/home_2005/spacer.gif' WIDTH='240' HEIGHT='1'><BR>";
			$label .= "<SPAN STYLE='font-size:7pt;font-weight:bold'>";
			$label .= $row['flavor_id'] . "<BR>";
			$label .= $row['flavor_name'] . "<BR>";
				//if ( $row['suggested_level'] < 3 ) {
				//	$suggested_level = $suggested_level_array[$row['suggested_level']-1];
				//} else {
				//	$suggested_level = $row['suggested_level_other'] . "%";
				//}
			$label .= "Starting use level: " . $row['suggested_level_other'] . " " . $row['use_in'] . "<BR>";
			if ( $row['other_info'] != '' ) {
				$label .= $row['other_info'] . "<BR>";
			}
			$label .= "Sample lot code: " . substr($_GET['pid'], 0, 2) . "-" . substr($_GET['pid'], -3) . "-" . date("mdy");
			$label .= "</SPAN></TD></TR><TR><TD>";
			$label .= "<TABLE BORDER='0' WIDTH='240' CELLSPACING='0' CELLPADDING='0'><TR>";
			$label .= "<TD COLSPAN=2><IMG SRC='http://www.chicagoconsulting.net/images/home_2005/spacer.gif' WIDTH='240' HEIGHT='2'></TD>";
			$label .= "</TR><TR>";
			$label .= "<TD WIDTH=70><SPAN STYLE='font-size:7pt;font-weight:bold'>" . substr($_GET['pid'], 0, 2) . "-" . substr($_GET['pid'], -3) . "</SPAN></TD>";
			$label .= "<TD ALIGN=RIGHT WIDTH=170><SPAN STYLE='font-size:7pt;font-weight:bold'>Expires: " . date("m/d/Y", strtotime($row['expiration_date'])) . "</SPAN></TD>";
			$label .= "</TR>";
			if ( $i == 2 ) {
				$label .= "<TR><TD COLSPAN=2><IMG SRC='http://www.chicagoconsulting.net/images/home_2005/spacer.gif' WIDTH='1' HEIGHT='2'></TD></TR>";
				$label .= "<TR><TD COLSPAN=2><SPAN STYLE='font-size:6pt;font-weight:bold'>";
				$label .= $date;
				$label .= " &bull; " . $sales_name;
				$label .= " &bull; " . $company;
				$label .= "</SPAN></TD></TR>";
			}
			$label .= "</TABLE>";
			$label .= "</TD></TR></TABLE>";
			$label .= "</TD></TR></TABLE>";
			//echo "<P CLASS='breakhere'>" . $label. "</P>";
			if ( $x < $c ) {
				echo "<DIV style='page-break-after: always'>" . $label . "</DIV>";
			} else {
				echo $label;
			}
		}
	}
}

?>



</BODY>
</HTML>