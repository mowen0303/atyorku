<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$adCategoryModel = new admin\adCategory\AdCategoryModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get("action"));

/**
 * 添加一则广告分类
 * @param $size
 * @param $title
 * @param $description
 */
function addAdCategory(){
    global $adCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $size = BasicTool::post("size");
        $title = BasicTool::post("title");
        $description = BasicTool::post("description");
        $adCategoryModel->addAdCategory($size,$title,$description);
        BasicTool::echoMessage("添加成功");
    }
    catch (Exception $e){
        BasicTool::throwException($e->getMessage(),-1);
    }

}

/**
 * 添加一则广告分类
 * @param $id
 */
function deleteAdCategory(){
    global $adCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $id = BasicTool::post("id","请指定将被删除的分类id");
        $adCategoryModel->deleteAdCategory($id[0]);
        BasicTool::echoMessage("删除成功");
    }
    catch (Exception $e){
        BasicTool::throwException($e->getMessage(),-1);
    }
}

/**
 * 查询一则广告分类
 * @param $id
 */
function getAdCategory(){
    global $adCategoryModel;
    $id = BasicTool::get("id","请指定分类id");
    BasicTool::echoJson(1,"成功",$adCategoryModel->getAdCategory($id));
}

function getAdCategories(){
    global $adCategoryModel;
    BasicTool::echoJson(1,"成功",$adCategoryModel->getAdCategories());
}

/**
 * 更改一则广告分类
 * @param $id,$title,$description,$size
 */
function updateAdCategory(){
    global $adCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $id = BasicTool::post("id","请指定分类ID");
        $size = BasicTool::post("size");
        $title = BasicTool::post("title");
        $description = BasicTool::post("description");
        $adCategoryModel->updateAdCategory($id,$size,$title,$description);
        BasicTool::echoMessage("修改成功");
    }
    catch (Exception $e){
        BasicTool::throwException($e->getMessage(),-1);
    }
}
