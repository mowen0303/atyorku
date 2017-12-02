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
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足,添加失败");
        $title = BasicTool::post("title", "specify title");
        $description = BasicTool::post("description", "specify description");
        $bool = $eventCategoryModel->addEventCategory($title, $description);
        if ($bool)
            BasicTool::echoMessage("添加成功");
        else
            BasicTool::echoMessage("添加失败");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}

/**
 * 删除一则活动分类
 * @param $id
 */
function deleteEventCategory()
{
    global $eventCategoryModel;
    global $currentUser;
    $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("权限不足,删除失败");
    try {
        $id = BasicTool::post("id", "请指定将被删除的分类id");
        $bool = $eventCategoryModel->deleteEventCategory($id[0]);
        if ($bool)
            BasicTool::echoMessage("删除成功");
        else
            BasicTool::echoMessage("删除失败");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
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

function getEventCategoriesWithJson(){
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
    $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足,更改失败");
    try {
        $id = BasicTool::post("id", "请指定分类ID");
        $title = BasicTool::post("title", "missing title");
        $description = BasicTool::post("description", "missing description");
        $bool = $eventCategoryModel->updateEventCategory($id, $title, $description);
        if ($bool)
            BasicTool::echoMessage("更改成功");
        else
            BasicTool::echoMessage("更改失败");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}
