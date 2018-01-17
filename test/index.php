<?php
require_once "../commonClass/config.php";
$msgModel = new \admin\msg\MsgModel();
var_dump($msgModel->pushMsgToUser(2,"good","1","good"));

//邮箱验证
?>