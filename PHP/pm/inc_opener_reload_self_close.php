<?php
echo "<script>\n";
echo "window.opener.location.reload();\n";
echo "{var oMe = window.self;oMe.open('','_self',''); oMe.close();}";
echo "</script>";
?>