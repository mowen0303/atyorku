<?
include_once("./global.php");
$l_id = $_GET['l_id'];
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>查看作品</title>
</head>

<body>
<?
	$query = $db->select("imgcontent","*","l_id = $l_id");
	while($row=$db->fetch_array($query))
	{
?>
		<p><img src="<? echo $row['path']?>"></p>
        <p><? echo $row['text']?></p>
<?
	}
?>
</body>
</html>
