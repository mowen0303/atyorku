<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventCategoryModel = new admin\eventCategory\EventCategoryModel();
$test = new \admin\eventCategory\EventCategoryModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get("action"));

/**
 * 添加一则活动分类
 * @param $title
 * @param $description
 */
function addEventCategory(){
    global $eventCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $title = BasicTool::post("title","specify title");
        $description = BasicTool::post("description","specify description");
        $eventCategoryModel->addEventCategory($title,$description);
        BasicTool::echoMessage("添加成功");
    }
    catch (Exception $e){
        BasicTool::throwException($e->getMessage(),-1);
    }

}

/**
 * 删除一则活动分类
 * @param $id
 */
function deleteEventCategory(){
    global $eventCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $id = BasicTool::post("id","请指定将被删除的分类id");
        $eventCategoryModel->deleteEventCategory($id[0]);
        BasicTool::echoMessage("删除成功");
    }
    catch (Exception $e){
        BasicTool::throwException($e->getMessage(),-1);
    }
}

/**
 * 查询一则活动分类
 * @param $id
 */
function getEventCategory(){
    global $eventCategoryModel;
    $id = BasicTool::get("id","请指定分类id");
    BasicTool::echoJson(1,"成功",$eventCategoryModel->getEventCategory($id));
}

function getEventCategories(){
    global $eventCategoryModel;
    BasicTool::echoJson(1,"成功",$eventCategoryModel->getEventCategories());
}

/**
 * 更改一则广告分类
 * @param $id,$title,$description,$size
 */
function updateEventCategory(){
    global $eventCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $id = BasicTool::post("id","请指定分类ID");
        $title = BasicTool::post("title");
        $description = BasicTool::post("description");
        $eventCategoryModel->updateEventCategory($id,$title,$description);
        BasicTool::echoMessage("修改成功");
    }
    catch (Exception $e){
        BasicTool::throwException($e->getMessage(),-1);
    }
}
