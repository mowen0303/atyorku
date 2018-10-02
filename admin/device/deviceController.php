<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$currentUser = new \admin\user\UserModel();
$deviceModel = new \admin\device\DeviceModel();
call_user_func(BasicTool::get('action'));



?>
