<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$adModel = new admin\ad\AdModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

/*
 * $ad_url
 */
function addAd($echoType="normal"){
    global $adModel;
    global $currentUser;
    $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
    $title = BasicTool::post("title","请填写标题");
    $sponsor_name = BasicTool::post("sponsor_name","广告商");
    $ad_url = BasicTool::post("ad_url");
    $publish_time=BasicTool::post("publish_time","广告投放时间不能为空");
    $expiration_time = BasicTool::post("expiration_time","广告过期时间不能为空");
    $description = BasicTool::post("description","description");
    $ad_category_id = BasicTool::post("ad_category_id");
    $banner_url = BasicTool::post("banner_url");
    $bool = $adModel->addAd($title, $description, $sponsor_name, $banner_url,$publish_time, $expiration_time, $ad_category_id, $ad_url);

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
function addAdWithJson(){
    addAd("json");
}

function getAdsByCategoryWithJson(){
    global $adModel;
    global $currentUser;
    $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
    $ad_category_id = BasicTool::get("ad_category_id","请指定广告分类id");
    $result = $adModel->getAdsByCategory($ad_category_id);
    if ($result)
        BasicTool::echoJson(1,"查询成功",$result);
    else
        BasicTool::echoJson(0,"空",$result);
}


function deleteAd($echoType="normal"){
    global $adModel;
    global $currentUser;
    $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
    $id = BasicTool::post("id", "请指定要删除的广告的id");
    $bool = $adModel->deleteAd($id);

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
function deleteAdWithJson(){
    deleteAd("json");
}
/*
 * $ad_url
 */
function updateAd(){
    global $adModel;
    global $currentUser;

    $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
    $id = BasicTool::post("id","id不能为空");
    $title = BasicTool::post("title","请填写标题");
    $banner_url = BasicTool::post("banner_url");
    $sponsor_name = BasicTool::post("sponsor_name","广告商");
    $ad_url = BasicTool::post("ad_url");
    $publish_time=BasicTool::post("publish_time","广告投放时间不能为空");
    $expiration_time = BasicTool::post("expiration_time","广告过期时间不能为空");
    $description = BasicTool::post("description","description");
    $ad_category_id = BasicTool::post("ad_category_id");
    $bool = $adModel->updateAd($id, $title, $description, $sponsor_name, $banner_url,$publish_time, $expiration_time, $ad_category_id, $ad_url);
    if ($bool)
        BasicTool::echoMessage("更改成功");
    else
        BasicTool::echoMessage("更改失败");
}
function uploadImgWithJson(){

    global $adModel;
    try {

        $uploadDir =  $adModel->uploadImg("imgFile","ad") or BasicTool::throwException($adModel->errorMsg);

        BasicTool::echoJson(1, "上传成功", $uploadDir);

    } catch (Exception $e) {

        BasicTool::echoJson(0, $e->getMessage());

    }

}