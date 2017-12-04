<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$commentModel = new admin\comment\CommentModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


function addComment($echoType="normal"){
    global $commentModel;
    $parent_id = BasicTool::post("parent_id");
    $sender_id = BasicTool::post("sender_id","sender_id不能为空");
    $receiver_id = BasicTool::post("receiver_id","receiver_Id不能为空");
    $section_name = BasicTool::post("section_name","section_name不能为空");
    $section_id = BasicTool::post("section_id","section_id不能为空");
    $comment = BasicTool::post("comment","评论不能为空");
    $redirect_url = BasicTool::post("redirect_url");
    $bool = $commentModel->addComment($parent_id,$sender_id,$receiver_id,$section_name,$section_id,$comment);
    if ($echoType == "normal")
    {
        if ($redirect_url){
            if ($bool)
                BasicTool::echoMessage("添加成功",$redirect_url);
            else
                BasicTool::echoMessage("添加失败",$redirect_url);
        }
        else{
            if ($bool)
                BasicTool::echoMessage("添加成功");
            else
                BasicTool::echoMessage("添加失败");
        }

    }
    else
    {
        if ($bool)
            BasicTool::echoJson(1,"添加成功",$bool);
        else
            BasicTool::echoJson(0,"添加失败");
    }
}
function addCommentWithJson(){
    addComment("json");
}

function deleteCommentsBySectionId($echoType = "normal"){
    global $commentModel;
    $section_name = BasicTool::post("section_name");
    $section_id = BasicTool::post("section_id");
    $bool = $commentModel->deleteCommentsBySection($section_name,$section_id);
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
function deleteCommentsBySectionIdWithJson(){
    deleteCommentsBySectionId("json");
}


function deleteChildComment($echoType="normal"){
    global $commentModel;
    $id = BasicTool::post("id","id不能为空");
    $bool = $commentModel->deleteChildComment($id);
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
function deleteChildCommentWithJson(){
    deleteChildComment("json");
}
function deleteParentComment($echoType="normal"){
    global $commentModel;
    $id = BasicTool::post("id","id不能为空");
    $bool = $commentModel->deleteParentComment($id);
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
function deleteParentCommentWithJson(){
    deleteParentComment("json");
}
function getCommentsBySectionWithJson(){
    global $commentModel;
    $section_name = BasicTool::post("section_name");
    $section_id = BasicTool::post("section_Id");
    $result = $commentModel->getCommentsBySection($section_name,$section_id);
    if($result)
        BasicTool::echoJson(1,"查询成功",$result);
    else
        BasicTool::echoJson(0,"查询失败");
}

