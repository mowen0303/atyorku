<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventModel = new admin\event\EventModel();
$currentUser = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
call_user_func(BasicTool::get('action'));

/*
 *!! Authorization implementation required,implementation in controller not in model
*/

function addEvent($echoType = "normal"){
    global $eventModel;
    global $currentUser;

    $event_category_id = BasicTool::post("event_category_id","Missing event_category_id");
    $title = BasicTool::post("title","活动标题不能为空");
    $description = BasicTool::post("description","missing description");
    $expiration_time = BasicTool::post("expiration_time","活动过期时间不能为空");
    $event_time=BasicTool::post("event_time","活动时间不能为空");
    $location_link = BasicTool::post("location_link");
    $registration_fee = BasicTool::post("registration_fee","活动费用不能为空");
    $max_participants = BasicTool::post("max_participants","活动名额");

    $img_id= uploadImages();
    $img_id_1 = $img_id[0];
    $img_id_2 = $img_id[1];
    $img_id_3 = $img_id[2];


   $sponsor_user_id = BasicTool::post("sponsor_user_id");
    $sponsor_name = BasicTool::post("sponsor_name");
    $sponsor_wechat = BasicTool::post("sponsor_wechat");
    $sponsor_email = BasicTool::post("sponsor_email");
    $sponsor_telephone = BasicTool::post("sponsor_telephone");

    $bool = $eventModel->addEvent($event_category_id,$title,$description,$expiration_time,$event_time,$location_link,
        $registration_fee,$img_id_1,$img_id_2,$img_id_3,$max_participants,$sponsor_user_id,$sponsor_name,$sponsor_wechat,$sponsor_email,$sponsor_telephone);

    if ($echoType == "normal")
    {
        if ($bool)
            BasicTool::echoMessage("添加成功");
        else
            BasicTool::echoMessage("添加失败");
    }
    else
    {
        if ($bool)
            BasicTool::echoJson(1,"添加成功");
        else
            BasicTool::echoJson(0,"添加失败");
    }

}
function addEventWithJson(){
    addEvent("json");
}

/*
 * @flag
 */
function getEventsByCategory($echoType="normal")
{
    global $eventModel;
    global $currentUser;

    $event_category_id = BasicTool::get("event_category_id", "请指定广告分类id");
    $flag = BasicTool::get("flag");
    $result = $eventModel->getEventsByCategory($event_category_id, $flag);

    if ($echoType == "normal")
    {
        if ($result)
            BasicTool::echoMessage("查询成功");
        else
            BasicTool::echoMessage("查询失败");
    }
    else
    {
        if ($result)
            BasicTool::echoJson(1,"查询成功");
        else
            BasicTool::echoJson(0,"查询失败");
    }
}
function getEventsByCategoryWithJson(){
    getEventsByCategory("json");
}

function deleteEvent($echoType="normal"){
    global $eventModel;
    global $currentUser;
    $id = BasicTool::post("id", "请指定要删除的广告的id");
    $bool = $eventModel->deleteEvent($id[0]);

    if ($echoType == "normal")
    {
        if ($bool)
            BasicTool::echoMessage("删除成功");
        else
            BasicTool::echoMessage("删除失败");
    }
    else
    {
        if ($bool)
            BasicTool::echoJson(1,"删除成功");
        else
            BasicTool::echoJson(0,"删除失败");
    }
}
function deleteEventWithJson(){
    deleteEvent("json");
}

function updateEvent($echoType = "normal"){
    global $eventModel;
    global $currentUser;
    $id = BasicTool::post("id","必须填写id");
    $event_category_id = BasicTool::post("event_category_id");
    $title = BasicTool::post("title");
    $description = BasicTool::post("description");

    $expiration_time = BasicTool::post("expiration_time");
    $event_time=BasicTool::post("event_time");

    $img_id= uploadImages();
    $img_id_1 = $img_id[0];
    $img_id_2 = $img_id[1];
    $img_id_3 = $img_id[2];

    $location_link = BasicTool::post("location_link");
    $registration_fee = BasicTool::post("registration_fee");
    $max_participants = BasicTool::post("max_participants");

    $sponsor_user_id = BasicTool::post("sponsor_user_id");
    $sponsor_name = BasicTool::post("sponsor_name");
    $sponsor_wechat = BasicTool::post("sponsor_wechat");
    $sponsor_email = BasicTool::post("sponsor_email");
    $sponsor_telephone = BasicTool::post("sponsor_telephone");

    $bool = $eventModel->updateEvent($id,$event_category_id,$title,$description,$expiration_time,$event_time,$location_link,
            $registration_fee,$img_id_1,$img_id_2,$img_id_3,$max_participants,$sponsor_user_id,$sponsor_name,$sponsor_wechat,$sponsor_email,$sponsor_telephone);

    if ($echoType == "normal")
    {
        if ($bool)
            BasicTool::echoMessage("更改成功");
        else
            BasicTool::echoMessage("更改失败");
    }
    else
    {
        if ($bool)
            BasicTool::echoJson(1,"更改成功");
        else
            BasicTool::echoJson(0,"更改失败");
    }

}
function updateEventWithJson(){
   updateEvent("json");

}



function uploadImages() {
    global $imageModel;
    global $currentUser;
    $uploadArr = $imageModel->uploadImg("imgFile", $currentUser->userId, "event") or BasicTool::throwException($imageModel->errorMsg);
    return $uploadArr;
}