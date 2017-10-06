<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventCommentModel = new admin\eventComment\EventCommentModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


function addEventComment(){
    global $eventCommentModel;
    $event_id = BasicTool::post("event_id","specify event_id");
    $user_id = BasicTool::post("user_id","specify user_id");
    $comment = BasicTool::post("comment");
    $parent_id = BasicTool::post("parent_id");
    $eventCommentModel->addEventComment($event_id,$parent_id,$user_id,$comment);
    BasicTool::echoMessage("添加成功");

}
function getEventComment(){
    global $eventCommentModel;
    $id = BasicTool::get("id","请指定id");
    BasicTool::echoJson(1,"获取成功",$eventCommentModel->getEventComment($id));
}

function getCommentsByEvent(){
    global $eventCommentModel;
    $event_id = BasicTool::get("event_id","请指定活动id");
    BasicTool::echoJson(1,"获取成功",$eventCommentModel->getCommentsByEvent($event_id));
}

function deleteEventComment(){
    global $eventCommentModel;
    $id = BasicTool::post("id", "请指定id");
    $bool = $eventCommentModel->deleteEventComment($id[0]);
    !$bool or BasicTool::echoMessage("删除成功");

}

