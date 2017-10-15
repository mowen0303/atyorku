<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventModel = new admin\event\EventModel();
$currentUser = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
call_user_func(BasicTool::get('action'));

/*
 *!! Authorization implementation required,implementation in controller not in model
*/

function addEvent(){
    global $eventModel;
    global $currentUser;
    global $imageModel;
    $imageModel->uploadImg("imgFile",$currentUser->userId,"event");
    $event_category_id = BasicTool::post("event_category_id","Missing event_category_id");
    $title = BasicTool::post("title","活动标题不能为空");
    $description = BasicTool::post("description","missing description");
    $intro = BasicTool::post("intro");
    $expiration_time = BasicTool::post("expiration_time","活动过期时间不能为空");
    $event_time=BasicTool::post("event_time","活动时间不能为空");
    $location_link = BasicTool::post("location_link");
    $qr_code_url = BasicTool::post("qr_code_url");
    $registration_fee = BasicTool::post("registration_fee","活动费用不能为空");
    $max_participants = BasicTool::post("max_participants","活动名额");
    $id[] = uploadImages();
    $poster_id_1 = $id[0];
    $poster_id_2 = $id[1];
    $poster_id_3 = $id[2];

    $sponsor_user_id = BasicTool::post("sponsor_user_id");
    $sponsor_name = BasicTool::post("sponsor_name");
    $sponsor_wechat = BasicTool::post("sponsor_wechat");
    $sponsor_email = BasicTool::post("sponsor_email");
    $sponsor_telephone = BasicTool::post("sponsor_telephone");

    $bool = $eventModel->addEvent($event_category_id,$title,$intro,$description,$expiration_time,$event_time,$location_link,
        $registration_fee,$poster_id_1,$poster_id_2,$poster_id_3,$qr_code_url,$max_participants,$sponsor_user_id,$sponsor_name,$sponsor_wechat,$sponsor_email,$sponsor_telephone);
    if ($bool)
        BasicTool::echoMessage("添加成功");
    else
        BasicTool::echoMessage("添加失败");

}
function addEventWithJson(){
    global $eventModel;
    global $currentUser;
    $event_category_id = BasicTool::post("event_category_id","Missing event_category_id");
    $title = BasicTool::post("title","活动标题不能为空");
    $description = BasicTool::post("description","missing description");
    $intro = BasicTool::post("intro");

    $expiration_time = BasicTool::post("expiration_time","活动过期时间不能为空");
    $event_time=BasicTool::post("event_time","活动时间不能为空");

    $poster_url = BasicTool::post("poster_url","活动封面url不能为空");
    $location_link = BasicTool::post("location_link","活动地点不能为空");
    $qr_code_url = BasicTool::post("qr_code_url");
    $registration_fee = BasicTool::post("registration_fee","活动费用不能为空");
    $max_participants = BasicTool::post("max_participants","活动名额");

    $sponsor_user_id = $currentUser->userId;

    $bool = $eventModel->addEvent($event_category_id,$title,$intro,$description,$expiration_time,$event_time,$location_link,
        $registration_fee,$poster_url,$qr_code_url,$max_participants,$sponsor_user_id);
    if ($bool)
        BasicTool::echoJson(1,"添加成功");
    else
        BasicTool::echoJson(0,"添加失败");

}
function getEventWithJson(){
    global $eventModel;
    $id = BasicTool::get("id","请指定广告id");
    BasicTool::echoJson(1,"获取广告成功",$eventModel->getEvent($id));
}

/*
 * @flag
 */
function getEventsByCategoryWithJson()
{
    global $eventModel;
    global $currentUser;
    $event_category_id = BasicTool::get("event_category_id", "请指定广告分类id");
    $flag = BasicTool::get("flag");
    $result = $eventModel->getEventsByCategory($event_category_id, $flag);
    if ($result)
        BasicTool::echoJson(1, "查询成功", $result);
    else
        BasicTool::echoJson(0,"空");
}
function deleteEvent(){
    global $eventModel;
    global $currentUser;
    $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
    $id = BasicTool::post("id", "请指定要删除的广告的id");
    $bool = $eventModel->deleteEvent($id[0]);
    if ($bool)
        BasicTool::echoMessage("删除成功");
    else
        BasicTool::echoMessage("删除失败");
}
function deleteEventWithJson(){
    global $eventModel;
    global $currentUser;
    $id = BasicTool::post("id", "请指定要删除的广告的id");
    $bool = $eventModel->deleteEvent($id);
    if ($bool)
        BasicTool::echoJson(1,"删除成功");
    else
        BasicTool::echoJson(0,"删除失败");
}

function updateEvent(){
    global $eventModel;
    global $currentUser;
    $id = BasicTool::post("id","必须填写id");
    $event_category_id = BasicTool::post("event_category_id");
    $title = BasicTool::post("title");
    $description = BasicTool::post("description");
    $intro = BasicTool::post("intro");

    $expiration_time = BasicTool::post("expiration_time");
    $event_time=BasicTool::post("event_time");

    $poster_id_1 = "";
    $poster_id_2 = "";
    $poster_id_3 = "";
    $location_link = BasicTool::post("location_link");
    $qr_code_url = BasicTool::post("qr_code_url");
    $registration_fee = BasicTool::post("registration_fee");
    $max_participants = BasicTool::post("max_participants");

    $sponsor_user_id = BasicTool::post("sponsor_user_id");
    $sponsor_name = BasicTool::post("sponsor_name");
    $sponsor_wechat = BasicTool::post("sponsor_wechat");
    $sponsor_email = BasicTool::post("sponsor_email");
    $sponsor_telephone = BasicTool::post("sponsor_telephone");

    $bool = $eventModel->updateEvent($id,$event_category_id,$title,$intro,$description,$expiration_time,$event_time,$location_link,
            $registration_fee,$poster_id_1,$poster_id_2,$poster_id_3,$qr_code_url,$max_participants,$sponsor_user_id,$sponsor_name,$sponsor_wechat,$sponsor_email,$sponsor_telephone);
    if ($bool)
        BasicTool::echoMessage("添加成功");
    else
        BasicTool::echoMessage("添加失败");

}
function updateEventWithJson(){
    global $eventModel;
    global $currentUser;
    $id = BasicTool::post("id","必须填写id");
    $event_category_id = BasicTool::post("event_category_id");
    $title = BasicTool::post("title");
    $description = BasicTool::post("description");
    $intro = BasicTool::post("intro");

    $expiration_time = BasicTool::post("expiration_time");
    $event_time=BasicTool::post("event_time");

    $poster_url = BasicTool::post("poster_url");
    $location_link = BasicTool::post("location_link");
    $qr_code_url = BasicTool::post("qr_code_url");
    $registration_fee = BasicTool::post("registration_fee");
    $max_participants = BasicTool::post("max_participants");

    $sponsor_user_id = $currentUser->userId;

    $bool = $eventModel->updateEvent($id,$event_category_id,$title,$intro,$description,$expiration_time,$event_time,$location_link,
        $registration_fee,$poster_url,$qr_code_url,$max_participants,$sponsor_user_id);
    if ($bool)
        BasicTool::echoJson(1,"添加成功");
    else
        BasicTool::echoJson(0,"添加失败");

}
function uploadImgWithJson(){
    global $eventModel;
    try {

        $uploadDir =  $eventModel->uploadImg("imgFile","event/images") or BasicTool::throwException($eventModel->errorMsg);

        BasicTool::echoJson(1, "上传成功", $uploadDir);

    } catch (Exception $e) {

        BasicTool::echoJson(0, $e->getMessage());

    }

}
function uploadImages() {
    global $imageModel;
    global $currentUser;
    $uploadArr = $imageModel->uploadImg("imgFile", $currentUser->userId, "event") or BasicTool::throwException($imageModel->errorMsg);
    return $uploadArr;
}