<?php
global $link;
$link = mysql_connect("localhost","root","addga0");
mysql_select_db("test",$link);
$diaplay="";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<HEAD>
<TITLE>WebToMed Test</TITLE>
</HEAD>
<BODY>

<h1>
<form action="showProduct.php" method="post">
<?php
if (empty($_POST) ) { ?>
<h2>Please Select a Maker</h2>
<select name="productName">
<?php
 $sql = "SELECT product_name FROM test_products order by product_name";
 $result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
 while ( $row = mysql_fetch_array($result) ) {
	$productName=$row['product_name'];
	echo "<option value=\"" . $row['product_name'] . "\">" . $row['product_name'] . "</option>";
	}
 ?>
 </select>
 <input type="submit" name="Submit">

<?php 
} else {
  $productName=$_POST['productName'];
  $sql="SELECT cat_name, cat.cat_id, cat_parent_id from test_categories cat, test_products prd where prd.cat_id=cat.cat_id and prd.product_name='" . $productName . "'";
 $result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
 $row =  mysql_fetch_array($result);
 $display = $row['cat_name'] . " -> " . $productName;
 while ( ! is_null($row['cat_parent_id'])) {
   $sql="SELECT cat_name, cat_id, cat_parent_id from test_categories where cat_id='" . $row['cat_parent_id'] . "'";
   $result = mysql_query($sql, $link) or die (mysql_error()."<br />Couldn't execute query: $sql<BR><BR>");
   $row =  mysql_fetch_array($result);
   $display = $row['cat_name'] . " -> " . $display;
 }
 echo "<br />" . $display . "<br />";
 }
 ?>

 </BODY>
 </HTML>
