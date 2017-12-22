<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$adCategoryModel = new admin\adCategory\AdCategoryModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get("action"));

/**添加一个广告分类
 * POST
 * @param size
 * @param title
 * @param description
 * localhost/admin/adCategory/AdCategoryController.php?action=addAdCategory
 */
function addAdCategory(){
    global $adCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足,添加失败");
        $size = BasicTool::post("size", "图片尺寸不能为空");
        $title = BasicTool::post("title", "标题不能为空");
        $description = BasicTool::post("description", "description");
        $adCategoryModel->addAdCategory($size, $title, $description) or BasicTool::throwException("添加成功");
        BasicTool::echoMessage("添加成功","/admin/adCategory/index.php");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}

/**
 * 删除一个广告分类
 * @param $id
 */
function deleteAdCategory(){
    global $adCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("权限不足,删除失败");
        $id = BasicTool::post("id", "请指定将被删除的分类id");
        $adCategoryModel->deleteAdCategory($id[0]) or BasicTool::throwException("删除失败");
        BasicTool::echoMessage("删除成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}

/**
 * 查询一则广告分类
 * @param $id
 */
function getAdCategoryWithJson(){
    global $adCategoryModel;
    $id = BasicTool::get("id","请指定分类id");
    BasicTool::echoJson(1,"成功",$adCategoryModel->getAdCategory($id));
}
/*
 * 查一波广告
 */
function getAdCategoriesWithJson(){
    global $adCategoryModel;
    $result = $adCategoryModel->getAdCategories();
    if ($result)
        BasicTool::echoJson(1,"成功",$adCategoryModel->getAdCategories());
    else
        BasicTool::echoJson(0,"空");
}

/**
 * 更改一则广告分类
 * @param $id,$title,$description,$size
 */
function updateAdCategory(){
    global $adCategoryModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足,更改失败");
        $id = BasicTool::post("id", "请指定分类ID");
        $size = BasicTool::post("size", "尺寸不能为空");
        $title = BasicTool::post("title", "标题不能为空");
        $description = BasicTool::post("description", "description");
        $adCategoryModel->updateAdCategory($id, $size, $title, $description) or BasicTool::throwException("修改失败");
        BasicTool::echoMessage("修改成功","/admin/adCategory/index.php");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}
