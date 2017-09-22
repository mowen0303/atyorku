<?
	$a = "123123123213123";
	$callback = $_GET['jsonpcallback'];
	echo $callback."($a)";
?>