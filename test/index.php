<?php
require_once "../commonClass/config.php";
$msgModel = new \admin\msg\MsgModel();

//$msgModel->updateRowById('user',3,['device'=>'APA91bHiQG999TZOvWdoGu-E5Qfrpil2EGudgHpXHtkWOdW8Xp…LTH5xVJb6nvcp-ZMnojBVHvRbjSA3um_GdRAlmyZpPMBV_7Kw']);


//var_dump($msgModel->pushMsgToUser(1,"good","1","good"));

//$time = BasicTool::getTodayTimestamp();
//print_r($time);

$user = new \admin\user\UserModel();

$user->getDailyCredit() or BasicTool::throwException($user->errorMsg);

//邮箱验证
?>