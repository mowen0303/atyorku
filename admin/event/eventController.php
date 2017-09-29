<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventModel = new admin\event\EventModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


function addEvent(){
    global $eventModel;
    global $currentUser;
    try{
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
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

        $sponsor_name = BasicTool::post("sponsor_name");
        $sponsor_wechat = BasicTool::post("sponsor_wechat");
        $sponsor_email = BasicTool::post("sponsor_email");
        $sponsor_telephone = BasicTool::post("sponsor_telephone");
        $sponsor_profile_img_url = BasicTool::post("sponsor_profile_img_url");

        $eventModel->addEvent($event_category_id,$title,$intro,$description,$expiration_time,$event_time,$location_link,
            $registration_fee,$poster_url,$qr_code_url,$max_participants,$sponsor_name,$sponsor_wechat,$sponsor_email,$sponsor_telephone,$sponsor_profile_img_url);

        BasicTool::echoMessage("添加成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}
function getEvent(){
    global $eventModel;
    $id = BasicTool::get("id","请指定广告id");
    BasicTool::echoJson(1,"获取广告成功",$eventModel->getEvent($id));
}

/*
 * @flag
 */
function getEventsByCategory(){
    global $eventModel;
    global $currentUser;
    try{
        $event_category_id = BasicTool::get("event_category_id","请指定广告分类id");
        $flag = BasicTool::get("flag");
        $result = $eventModel->getEventsByCategory($event_category_id,$flag);
        BasicTool::echoJson(1,"查询成功",$result);
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function deleteEvent(){
    global $eventModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $id = BasicTool::post("id", "请指定要删除的广告的id");
        $eventModel->deleteEvent($id[0]);
        BasicTool::echoMessage("删除成功");
    }
    catch(Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function updateEvent(){
    global $eventModel;
    global $currentUser;
    try{
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");

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

        $sponsor_name = BasicTool::post("sponsor_name");
        $sponsor_wechat = BasicTool::post("sponsor_wechat");
        $sponsor_email = BasicTool::post("sponsor_email");
        $sponsor_telephone = BasicTool::post("sponsor_telephone");
        $sponsor_profile_img_url = BasicTool::post("sponsor_profile_img_url");

        $eventModel->updateEvent($id,$event_category_id,$title,$intro,$description,$expiration_time,$event_time,$location_link,
            $registration_fee,$poster_url,$qr_code_url,$max_participants,$sponsor_name,$sponsor_wechat,$sponsor_email,$sponsor_telephone,$sponsor_profile_img_url);
        BasicTool::echoMessage("更改成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
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