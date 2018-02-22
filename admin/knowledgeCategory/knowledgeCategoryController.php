<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$knowledgeCategoryModel = new admin\knowledgeCategory\KnowledgeCategoryModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get("action"));

/**添加一个分类
 * POST
 * @param description
 * localhost:8080/admin/knowledgeCategory/knowledgeCategoryController.php?action=addKnowledgeCategory
 */
function addKnowledgeCategory(){
    global $knowledgeCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足,添加失败");
        $name = BasicTool::post("name");
        $knowledgeCategoryModel->addKnowledgeCategory($name) or BasicTool::throwException("添加失败");
        BasicTool::echoMessage("添加成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}

/**
 * 删分类
 * POST
 * @param id 分类id
 * localhost:8080/admin/knowledgeCategory/knowledgeCategoryController.php?action=deleteKnowledgeCategory
 */
function deleteKnowledgeCategory()
{
    global $knowledgeCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("权限不足,删除失败");
        $id= BasicTool::post("id", "请指定将被删除的分类id");
        $bool = $knowledgeCategoryModel->deleteKnowledgeCategory($id) or BasicTool::throwException($knowledgeCategoryModel->errorMsg);
        BasicTool::echoMessage("删除成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}




/**更改一个分类
 * POST
 * @param id 分类id
 * @param name
 * localhost:8080/admin/knowledgeCategory/knowledgeCategoryController.php?action=updateKnowledgeCategory
 */
function updateKnowledgeCategory(){
    global $knowledgeCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足,更改失败");
        $id = BasicTool::post("id", "请指定分类ID");
        $name= BasicTool::post("name","分类名不能为空");
        $knowledgeCategoryModel->updateKnowledgeCategory($id,$name) or BasicTool::throwException("更改失败");
        BasicTool::echoMessage("更改成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}
