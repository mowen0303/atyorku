<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$productTransactionModel = new admin\productTransaction\ProductTransactionModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

