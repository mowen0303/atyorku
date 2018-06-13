<?php
require_once "../commonClass/config.php";
require_once "../commonClass/BasicTool.class.php";

$msg = new \admin\msg\MsgModel();
var_dump($msg->pushMsgToUser(1, 'test', 2, "test",$specifySenderId=3));

?>