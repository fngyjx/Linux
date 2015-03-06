//Make a new file named: comunread.php

//and write:
<?PHP
//previous class 

require("COutLook.php");
//make new instance of the class

$class= new COutLook;
if ($folder==""){
$class->staticFolders();
}
else {
$class->staticFolders();
$class->getMessages($folder);
}

?>
