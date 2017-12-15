<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventParticipantModel = new admin\eventParticipant\EventParticipantModel();
$eventModel = new admin\event\EventModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


function addEventParticipant(){
    global $eventParticipantModel,$eventModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足,添加失败");

        $user_id = BasicTool::post("user_id", "specify user_id");
        $currentUser->getProfileOfUserById($user_id) or BasicTool::throwException("用户ID不存在");
        $event_id = BasicTool::post("event_id", "specify event_id");
        $event = $eventModel->getEvent($event_id);
        $event or BasicTool::throwException("活动不存在");
        ($event["max_participants"] > $event["count_participants"]) or BasicTool::throwException("添加失败,名额已满");
        $bool = $eventParticipantModel->addEventParticipant($event_id, $user_id);

        if ($bool)
            BasicTool::echoMessage("添加成功");
        else
            BasicTool::echoMessage("添加失败");
    }

    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}

function addEventParticipantWithJson(){
    global $eventParticipantModel,$eventModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("EVENT") or BasicTool::throwException("权限不足,添加失败");

        $user_id = $currentUser->userId;
        $event_id = BasicTool::post("event_id","specify event_id");
        $event = $eventModel->getEvent($event_id);
        $event or BasicTool::throwException("活动不存在");
        ($event["max_participants"] > $event["count_participants"]) or BasicTool::throwException("添加失败,名额已满");
        $bool = $eventParticipantModel->addEventParticipant($event_id,$user_id);

        if ($bool)
            BasicTool::echoJson(1,"参与成功");
        else
            BasicTool::echoJson(0,"参与失败");
        }

    catch (Exception $e){
        BasicTool::echoJson(0,$e->getMessage());
    }
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

function deleteEventParticipant($echoType="normal"){
    global $eventParticipantModel;
    global $currentUser;
    $id = BasicTool::post("id","specify id");
    try {
        //判断权限
        if(!($currentUser->isUserHasAuthority("ADMIN") && $currentUser->isUserHasAuthority("EVENT"))){
            $user_id = $eventParticipantModel->getEventParticipant($id)["user_id"];
            $currentUser->userId == $user_id or BasicTool::throwException("权限不足,删除失败");
        }

        $bool = $eventParticipantModel->deleteEventParticipant($id);
        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("删除成功");
            else
                BasicTool::echoMessage("删除失败");
        } else {
            if ($bool)
                BasicTool::echoJson(1, "删除成功");
            else
                BasicTool::echoJson(0, "删除失败");
        }
    }
    catch (Exception $e){
        if ($echoType == "normal"){
            BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
        }
        else{
            BasicTool::echoJson(0,$e->getMessage());
        }
    }
}

function deleteEventParticipantWithJson(){
    deleteEventParticipant("json");
}




