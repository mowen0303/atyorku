<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$adModel = new admin\ad\AdModel();
$imageModel = new admin\image\ImageModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

/*
 * $ad_url
 */
function addAd($echoType="normal"){
    global $adModel;
    global $currentUser;
    global $imageModel;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $title = BasicTool::post("title","请填写标题");
        $sponsor_name = BasicTool::post("sponsor_name","广告商");
        $ad_url = BasicTool::post("ad_url");
        $publish_time=BasicTool::post("publish_time","广告投放时间不能为空");
        $expiration_time = BasicTool::post("expiration_time","广告过期时间不能为空");
        $description = BasicTool::post("description","description");
        $ad_category_id = BasicTool::post("ad_category_id");
        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        $currImgArr = false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"ad");
        $bool = $adModel->addAd($title, $description, $sponsor_name, $imgArr[0],$imgArr[1],$imgArr[2],$publish_time, $expiration_time, $ad_category_id, $ad_url);

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
    catch (Exception $e){
        if ($echoType == "normal"){
            BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
        }
        else{
            BasicTool::echoJson(0,$e->getMessage());
        }
    }
}
function addAdWithJson(){
    addAd("json");
}

function getAdsByCategoryWithJson(){
    global $adModel;
    global $currentUser;
    try {
        $ad_category_id = BasicTool::get("ad_category_id", "请指定广告分类id");
        $result = $adModel->getAdsByCategory($ad_category_id);
        if ($result)
            BasicTool::echoJson(1, "查询成功", $result);
        else
            BasicTool::echoJson(0, "空", $result);
    }
    catch (Exception $e){
       BasicTool::echoJson(0,$e->getMessage());
    }
}


function deleteAd($echoType="normal"){
    global $adModel;
    global $currentUser;
    global $imageModel;
    try {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $id = BasicTool::post("id", "请指定要删除的广告的id");

        //删除图片
        if (is_array($id)) {
            $concat = null;
            foreach ($id as $i) {
                $i = $i + 0;
                $i = $i . ",";
                $concat = $concat . $i;
            }
            $concat = substr($concat, 0, -1);
            $sql = "SELECT * FROM ad WHERE id in ({$concat})";
            $ads = SqlTool::getSqlTool()->getListBySql($sql);
            $img_ids = array();
            foreach ($ads as $ad) {
                if ($ad["img_id_1"]) {
                    if ($ad["img_id_1"]) {
                        array_push($img_ids, $ad["img_id_1"]);
                    }
                    if ($ad["img_id_2"]) {
                        array_push($img_ids, $ad["img_id_2"]);
                    }
                    if ($ad["img_id_3"]) {
                        array_push($img_ids, $ad["img_id_3"]);
                    }
                }
            }

        } else {
            $ad = $adModel->getAd($id);
            $img_ids = array();
            if ($ad["img_id_1"]) {
                array_push($img_ids, $ad["img_id_1"]);
            }
            if ($ad["img_id_2"]) {
                array_push($img_ids, $ad["img_id_2"]);
            }
            if ($ad["img_id_3"]) {
                array_push($img_ids, $ad["img_id_3"]);
            }
        }
        $bool = $imageModel->deleteImageById($img_ids);

        if ($bool) {
            $bool = $adModel->deleteAd($id);
        }

        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("删除成功");
            else
                BasicTool::echoMessage("删除失败");
        } else {
            if ($bool)
                BasicTool::echoJson(1, "删除成功");
            else
                BasicTool::echoJson(0, "删除失败");
        }
    }
    catch (Exception $e){
        if ($echoType == "normal"){
            BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
        }
        else{
            BasicTool::echoJson(0,$e->getMessage());
        }
    }
}
function deleteAdWithJson(){
    deleteAd("json");
}
/*
 *
 */
function updateAd($echoType="normal"){
    global $adModel;
    global $currentUser;
    global $imageModel;
    try{
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $id = BasicTool::post("id","id不能为空");
        $title = BasicTool::post("title","请填写标题");
        $sponsor_name = BasicTool::post("sponsor_name","广告商");
        $ad_url = BasicTool::post("ad_url");
        $publish_time=BasicTool::post("publish_time","广告投放时间不能为空");
        $expiration_time = BasicTool::post("expiration_time","广告过期时间不能为空");
        $description = BasicTool::post("description","description");
        $ad_category_id = BasicTool::post("ad_category_id");

        $event = $adModel->getAd($id);
        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        $currImgArr = array($ad["img_id_1"],$ad["img_id_2"],$ad["img_id_3"]);
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"ad");

        $bool = $adModel->updateAd($id, $title, $description, $sponsor_name, $imgArr[0],$imgArr[1],$imgArr[2],$publish_time, $expiration_time, $ad_category_id, $ad_url);
        if ($echoType == "normal")
        {
            if ($bool)
                BasicTool::echoMessage("修改成功");
            else
                BasicTool::echoMessage("修改失败");
        }
        else
        {
            if ($bool)
                BasicTool::echoJson(1,"修改成功");
            else
                BasicTool::echoJson(0,"修改失败");
        }
    }
    catch (Exception $e){
        if ($echoType == "normal"){
            BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
        }
        else{
            BasicTool::echoJson(0,$e->getMessage());
        }
    }
}
function updateAdWithJson(){
    updateAd("json");
}