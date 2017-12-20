<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$action = BasicTool::get('action');
$currentUser = new \admin\user\UserModel();
$msgModel = new \admin\msg\MsgModel();

call_user_func(BasicTool::get('action'));
//---------------------------------------------------------------------------------------------------------------------

function deleteMsg(){
    global $currentUser;
    global $msgModel;

    try {
        $currentUser->isUserHasAuthority('ADMIN') or BasicTool::throwException("无权删除");
        $id = BasicTool::post('id');
        $msgModel->realDeleteByFieldIn('msg', 'id', $id) or BasicTool::throwException($msgModel->errorMsg);
        BasicTool::echoMessage("成功");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}

/**
 * http://localhost/admin/msg/msgController.php?action=test
 */
function test(){
    global $msgModel;
    $msgModel->pushMsgToUser(9,'forumComment',1,"test");
    //$msgModel->pushMsgToAllUsers('guide',10,"hahaha");
}

function test2(){
    $receiverUser = new \admin\user\UserModel(9);
    $receiverUser->clearBadge();
}







?>