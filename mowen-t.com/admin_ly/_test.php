<?
include_once("global_admin.php");
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>无标题文档</title>
</head>

<body>
<?
/*
$name = "jiyu";
$query = $db->select("admin","*","name='".$name."'");
//$query = mysql_query("select * from admin");
$row=$db->fetch_array($query);
echo "后台配置正常，数据库内容：".$row['name']."<br>";
*/

//$a = $db->query("INSERT INTO `myweb`.`imgcon` (`id` ,`dress` ,`title` ,`l_id`) VALUES (NULL , '123', NULL , '1');");
//echo $db->insert_id();
echo session_id("123");
?>
</body>
</html>