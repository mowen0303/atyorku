<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventParticipantModel = new admin\eventParticipant\EventParticipantModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


function addEventParticipant(){
    global $eventParticipantModel;
    global $currentUser;
    try{
        //$currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $user_id = BasicTool::post("user_id","specify user_id");
        $event_id = BasicTool::post("event_id","specify event_id");
        $eventParticipantModel->addEventParticipant($event_id,$user_id);
        BasicTool::echoMessage("添加成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}
function getEventParticipantsByEvent(){
    global $eventParticipantModel;
    $event_id = BasicTool::get("event_id","specify id");
    BasicTool::echoJson(1,"成功",$eventParticipantModel->getEventParticipantsByEvent($event_id));
    }
function deleteEventParticipant(){
    global $eventParticipantModel;
    $id = BasicTool::post("id","specify id");
    $eventParticipantModel->deleteEventParticipant($id[0]);
    BasicTool::echoMessage("删除成功");
}




