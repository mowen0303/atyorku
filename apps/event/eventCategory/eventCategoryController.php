<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventCategoryModel = new \apps\event\eventCategory\EventCategoryModel();
$test = new \apps\event\eventCategory\EventCategoryModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get("action"));

/**添加一个活动分类
 * POST
 * @param title 分类标题
 * @param description
 * localhost/admin/eventCategory/eventCategoryController.php?action=addEventCategory
 */
function addEventCategory(){
    global $eventCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足,添加失败");
        $title = BasicTool::post("title", "specify title");
        $description = BasicTool::post("description", "specify description");
        $eventCategoryModel->addEventCategory($title, $description) or BasicTool::throwException("添加失败");
        BasicTool::echoMessage("添加成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}

/**
 * 删除活动分类
 * POST
 * @param id 分类id
 * localhost/admin/eventCategory/eventCategoryController.php?action=deleteEventCategory
 */
function deleteEventCategory()
{
    global $eventCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("权限不足,删除失败");
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

/**查询所有活动分类
 * JSON接口
 * localhost/admin/eventCategory/eventCategoryController.php?action=getEventCategoriesWithJson
 */
function getEventCategoriesWithJson(){
    global $eventCategoryModel;
    BasicTool::echoJson(1,"成功",$eventCategoryModel->getEventCategories());
}

/**更改一个活动分类
 * POST
 * @param id 分类id
 * @param title 分类标题
 * @param description
 * localhost/admin/eventCategory/eventCategoryController.php?action=updateEventCategory
 */
function updateEventCategory(){
    global $eventCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足,更改失败");
        $id = BasicTool::post("id", "请指定分类ID");
        $title = BasicTool::post("title", "missing title");
        $description = BasicTool::post("description", "missing description");
        $eventCategoryModel->updateEventCategory($id, $title, $description) or BasicTool::throwException("更改失败");
        BasicTool::echoMessage("更改成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}
