<?
$root = $_SERVER['DOCUMENT_ROOT'];
include_once($root."/config/config.php");                         //引入配置文件
include_once($root."/common/mysql_class.php");              //引入mysql类文件


$db = new mysql($mydbhost,$mydbuser,$mydbpw,$mydbname,$mydbchar);

?>