<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventParticipantModel = new \apps\event\eventParticipant\EventParticipantModel();
$eventModel = new \apps\event\event\EventModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

/**添加活动参与人
 * POST
 * @param user_id 参与者id
 * @param event_id 活动id
 *localhost/admin/eventParticipant/eventParticipantController.php?action=addEventParticipant
 */
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
        $eventParticipantModel->addEventParticipant($event_id, $user_id) or BasicTool::throwException("添加失败");
        BasicTool::echoMessage("添加成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}
/**添加活动参与人
 * POST，JSON接口
 * @param user_id 参与者id
 * @param event_id 活动id
 *localhost/admin/eventParticipant/eventParticipantController.php?action=addEventParticipantWithJson
 */
function addEventParticipantWithJson(){
    global $eventParticipantModel,$eventModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("EVENT") or BasicTool::throwException("权限不足,添加失败");

        $user_id = $currentUser->userId;
        $event_id = BasicTool::post("event_id", "specify event_id");
        $event = $eventModel->getEvent($event_id);
        $event or BasicTool::throwException("活动不存在");
        ($event["max_participants"] > $event["count_participants"]) or BasicTool::throwException("添加失败,名额已满");
        $eventParticipantModel->addEventParticipant($event_id, $user_id) or BasicTool::throwException("添加失败");
        BasicTool::echoJson(1, "参与成功");
    }
    catch (Exception $e){
        BasicTool::echoJson(0,$e->getMessage());
    }
}

/**查询某个活动的参与者
 * GET，JSON接口
 * @param event_id 活动id
 * @param page 页数
 *localhost/admin/eventParticipant/eventParticipantController.php?action=getEventParticipantsByEventWithJson?page=1&event_id=2
 */
function getEventParticipantsByEventWithJson(){
    global $eventParticipantModel;
    $event_id = BasicTool::get("event_id","specify id");
    $result = $eventParticipantModel->getEventParticipantsByEvent($event_id);
    if ($result)
        BasicTool::echoJson(1,"查询成功",$result);

    else
        BasicTool::echoJson(0,"空");
}

/**删除活动参与者
 * POST
 * @param id event_participant_id,int或者一维数组
 *localhost/admin/eventParticipant/eventParticipantController.php?action=deleteEventParticipant
 */
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

        $eventParticipantModel->deleteEventParticipant($id) or BasicTool::throwException("删除失败");
        if ($echoType == "normal") {
            BasicTool::echoMessage("删除成功");
        }
        else {
            BasicTool::echoJson(1, "删除成功");
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

/**删除活动参与者
 * POST,JSON接口
 * @param id event_participant_id,int或者一维数组
 *localhost/admin/eventParticipant/eventParticipantController.php?action=deleteEventParticipantWithJson
 */
function deleteEventParticipantWithJson(){
    deleteEventParticipant("json");
}




