<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventParticipantModel = new admin\eventParticipant\EventParticipantModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


function addEventParticipant(){
    global $eventParticipantModel;
    global $currentUser;
        //$currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
    $user_id = BasicTool::post("user_id","specify user_id");
    $event_id = BasicTool::post("event_id","specify event_id");
    $bool = $eventParticipantModel->addEventParticipant($event_id,$user_id);
    if ($bool)
        BasicTool::echoMessage("参与成功");
    else
        BasicTool::echoMessage("参与失败");
}

function addEventParticipantWithJson(){
    global $eventParticipantModel;
    global $currentUser;
    //$currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
    $user_id = $currentUser->userId;
    $event_id = BasicTool::post("event_id","specify event_id");
    $bool = $eventParticipantModel->addEventParticipant($event_id,$user_id);
    if ($bool)
        BasicTool::echoJson(1,"参与成功");
    else
        BasicTool::echoJson(0,"参与失败");
}

function getEventParticipantsByEventWithJson(){
    global $eventParticipantModel;
    $event_id = BasicTool::get("event_id","specify id");
    $result = $eventParticipantModel->getEventParticipantsByEvent($event_id);
    if ($result)
        BasicTool::echoJson(1,"查询成功",$result);

    else
        BasicTool::echoJson(0,"空");
}

function deleteEventParticipant(){
    global $eventParticipantModel;
    $id = BasicTool::post("id","specify id");
    $bool = $eventParticipantModel->deleteEventParticipant($id[0]);
    if ($bool)
        BasicTool::echoMessage("删除成功");
    else
        BasicTool::echoMessage("删除失败");
}

function deleteEventParticipantWithJson(){
    global $eventParticipantModel;
    $id = BasicTool::post("id","specify id");
    $bool = $eventParticipantModel->deleteEventParticipant($id);
    if ($bool)
        BasicTool::echoJson(1,"删除成功",$id);
    else
        BasicTool::echoJson(0,"删除失败");
}




