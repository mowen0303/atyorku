<?
include_once("../global.php");
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>无标题文档</title>
</head>

<body>
<?
$query = $db->query("show tables");
while($row = $db->fetch_array($query))
{
	$table = $row['Tables_in_myweb'];
	$db->query("DROP TABLE $table");
	echo "删除$table<br>";
}
?>
</body>
</html>
