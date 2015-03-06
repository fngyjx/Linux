<HTML>
<Head>
<Script Language=JavaScript>

	var isChoice = 0;
	
	function callAlert(Msg,Title){
		
		txt = Msg;
		caption = Title;
		vbMsg(txt,caption)
		alert(isChoice);
		//yes, isChoice = 6, no isChoice=7, cancel isChoice=2
		
	}
</Script>

<Script Language=VBScript>

	Function vbMsg(isTxt,isCaption)
	
	testVal = MsgBox(isTxt,3,isCaption)
	isChoice = testVal

	End Function

</Script>
</Head>
<Body>
<input type=button value="Test" onclick="callAlert('Can you get there from here?','This is a Title')">
</Body>
</HTML>

<?php $myArr = explode("&","test1&nbsp;test2") ; echo "test2 = \'\& \'  " . $myArr[0]. "<br />"; ?>

<?php // echo "what eEMPTY(NULL) will give me? ". empty() . "<br />";?>

<?php $str_null = NULL; echo "what will e M  P T Y ( NULL ) will give me? ". empty($str_null) . "<br />"; ?>
<?php $str_null = '123'; echo "what will e M  P T Y ( '' ) will give me? ". empty($str_null) . "<br />"; ?>

<?php echo substr("TestString", 0,1)?>
<?php phpinfo(); ?> 