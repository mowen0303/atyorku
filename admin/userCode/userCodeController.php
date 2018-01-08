<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$commentModel = new admin\comment\CommentModel();
$currentUser = new \admin\user\UserModel();
$msgModel = new \admin\msg\MsgModel();
call_user_func(BasicTool::get('action'));



/**
 * 添加一条评论，并将评论内容返回
 * [POST] http://www.atyorku.ca/admin/comment/commentController.php?action=addCommentWithJson
 * @param parent_id     一级评论为0；二级评论为一级评论的id
 * @param receiver_id   一级评论默认为文章作者id；二级评论为父级评论者id
 * @param section_name  数据库表名
 * @param section_id    数据库表id
 * @param comment
 */
function addCommentWithJson(){
    addComment("json");
}

function addComment($echoType="normal"){
    global $commentModel;
    global $currentUser;
    global $msgModel;
    try{
        $sender_id = $currentUser->userId;
        $parent_id = BasicTool::post("parent_id");
        $receiver_id = BasicTool::post("receiver_id","receiver_Id不能为空");
        $section_name = BasicTool::post("section_name","section_name不能为空");
        $section_id = BasicTool::post("section_id","section_id不能为空");
        $comment = BasicTool::post("comment","说点什么吧...",255);
        $row = $commentModel->addComment($parent_id,$sender_id,$receiver_id,$section_name,$section_id,$comment);
        //推送
        $msgModel->pushMsgToUser($receiver_id,$section_name."Comment",$section_id,$comment);

        if ($echoType == "normal") {
            BasicTool::echoMessage("添加成功",$_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1,"添加成功",$row);
        }
    }catch(Exception $e){
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


