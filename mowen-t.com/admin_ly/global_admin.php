<?
session_start();
include_once("../config/config.php");       //引入配置文件
include_once("../common/mysql_class.php");  //引入mysql类文件
include_once("ckeditor/ckeditor.php");      //引入fckeditor类文件
include_once("ckfinder/ckfinder.php");      //引入fckeditor类文

$db = new mysql($mydbhost,$mydbuser,$mydbpw,$mydbname,$mydbchar);

//配置编辑器
$ed = new CKEditor();
$ed->BasePath = "/admin_ly/keditor/";
$config = array();
$config['toolbar'] = array(
 array( 'Source', '-', 'Bold', 'Italic', 'Underline', 'Strike' ),
 array( 'Image', 'Link', 'Unlink', 'Anchor' )
);
?>