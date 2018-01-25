<?php
require_once "../commonClass/BasicTool.class.php";
$mailBody = '<p>AtYorkU账号注册成功,请点击下面链接进行激活:</p><p></p>';
var_dump(BasicTool::mailTo("mowen0303@gmail.com","welcome to atyorku",$mailBody));


?>