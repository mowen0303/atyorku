<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$adModel = new admin\ad\AdModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


function addAd(){
    global $adModel;
    global $currentUser;
    try{
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $title = BasicTool::post("title");
        $sponsor_name = BasicTool::post("sponsor_name");
        $ad_url = BasicTool::post("ad_url");
        $publish_time=BasicTool::post("publish_time");
        $expiration_time = BasicTool::post("expiration_time");
        $description = BasicTool::post("description");
        $ad_category_id = BasicTool::post("ad_category_id");
        $banner_url = BasicTool::post("banner_url");
        $adModel->addAd($title, $description, $sponsor_name, $banner_url,$publish_time, $expiration_time, $ad_category_id, $ad_url);
        BasicTool::echoMessage("添加成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}
function getAd(){
    global $adModel;
    $id = BasicTool::get("id","请指定广告id");
    BasicTool::echoJson(1,"获取广告成功",$adModel->getAd($id));
}

function getAdsByCategory(){
    global $adModel;
    global $currentUser;
    try{
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $ad_category_id = BasicTool::get("ad_category_id","请指定广告分类id");
        $result = $adModel->getAdsByCategory($ad_category_id);
        BasicTool::echoJson(1,"查询成功",$result);
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function deleteAd(){
    global $adModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $id = BasicTool::post("id", "请指定要删除的广告的id");
        $adModel->deleteAd($id[0]);
        BasicTool::echoMessage("删除成功");
    }
    catch(Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function updateAd(){
    global $adModel;
    global $currentUser;
    try{
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $id = BasicTool::post("id");
        $title = BasicTool::post("title");
        $banner_url = BasicTool::post("banner_url");
        $sponsor_name = BasicTool::post("sponsor_name");
        $ad_url = BasicTool::post("ad_url");
        $publish_time=BasicTool::post("publish_time");
        $expiration_time = BasicTool::post("expiration_time");
        $description = BasicTool::post("description");
        $ad_category_id = BasicTool::post("ad_category_id");
        $adModel->updateAd($id, $title, $description, $sponsor_name, $banner_url,$publish_time, $expiration_time, $ad_category_id, $ad_url);
        BasicTool::echoMessage("更改成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}
function uploadImgWithJson(){

    global $adModel;
    try {

        $uploadDir =  $adModel->uploadImg("imgFile","ad/images") or BasicTool::throwException($adModel->errorMsg);

        BasicTool::echoJson(1, "上传成功", $uploadDir);

    } catch (Exception $e) {

        BasicTool::echoJson(0, $e->getMessage());

    }

}