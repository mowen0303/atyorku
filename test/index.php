<?php
require_once "../commonClass/config.php";
$msgModel = new \admin\msg\MsgModel();

//$msgModel->updateRowById('user',3,['device'=>'APA91bHiQG999TZOvWdoGu-E5Qfrpil2EGudgHpXHtkWOdW8Xp…LTH5xVJb6nvcp-ZMnojBVHvRbjSA3um_GdRAlmyZpPMBV_7Kw']);


$msgModel->pushMsgToUser(1,"good","1","good")

//邮箱验证
?>