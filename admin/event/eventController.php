<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventModel = new admin\event\EventModel();
$currentUser = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$commentModel = new \admin\comment\CommentModel();
call_user_func(BasicTool::get('action'));



function addEvent($echoType = "normal"){
    global $eventModel;
    global $currentUser;
    global $imageModel;
    try {
        //判断权限
        ($currentUser->isUserHasAuthority("ADMIN") || $currentUser->isUserHasAuthority("EVENT")) or BasicTool::throwException("权限不足,添加失败");

        $event_category_id = BasicTool::post("event_category_id", "Missing event_category_id");
        $title = BasicTool::post("title", "活动标题不能为空");
        $description = BasicTool::post("description", "missing description");
        $expiration_time = BasicTool::post("expiration_time", "活动过期时间不能为空");
        $event_time = BasicTool::post("event_time", "活动时间不能为空");
        $location_link = BasicTool::post("location_link");
        $registration_fee = BasicTool::post("registration_fee", "活动费用不能为空");
        $max_participants = BasicTool::post("max_participants", "活动名额不能为空");
        $registration_fee >= 0 or BasicTool::throwException("活动费用不能小于0");
        $max_participants >= 0 or BasicTool::throwException("活动名额不能小于0");
        $sponsor_user_id = BasicTool::post("sponsor_user_id");
        $sponsor_name = BasicTool::post("sponsor_name");
        $sponsor_wechat = BasicTool::post("sponsor_wechat");
        $sponsor_email = BasicTool::post("sponsor_email");
        $sponsor_telephone = BasicTool::post("sponsor_telephone");
        $sort = BasicTool::post("sort");
        ($sort == 0 || $sort == 1 || $sort == NULL) or BasicTool::echoMessage("添加失败,请输入有效的排序值(0或者1)");

        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        $currImgArr = false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"event");
        $bool = $eventModel->addEvent($event_category_id, $title, $description, $expiration_time, $event_time, $location_link,
            $registration_fee, $imgArr[0], $imgArr[1], $imgArr[2], $max_participants, $sponsor_user_id, $sponsor_name, $sponsor_wechat, $sponsor_email, $sponsor_telephone,$sort);

        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("添加成功","index.php?s=getEventsByCategory&event_category_id={$event_category_id}&flag=1");
            else
                BasicTool::echoMessage("添加失败","index.php?s=getEventsByCategory&event_category_id={$event_category_id}&flag=1");
        } else {
            if ($bool)
                BasicTool::echoJson(1, "添加成功");
            else
                BasicTool::echoJson(0, "添加失败");
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
function addEventWithJson(){
    addEvent("json");
}

/*
 * @flag
 */
function getEventsByCategory($echoType="normal")
{
    global $eventModel;

    $event_category_id = BasicTool::get("event_category_id", "请指定广告分类id");
    $flag = BasicTool::get("flag");
    $result = $eventModel->getEventsByCategory($event_category_id, $flag);

    if ($echoType == "normal")
    {
        if ($result)
            BasicTool::echoMessage("查询成功");
        else
            BasicTool::echoMessage("查询失败");
    }
    else
    {
        if ($result)
            BasicTool::echoJson(1,"查询成功");
        else
            BasicTool::echoJson(0,"查询失败");
    }
}
function getEventsByCategoryWithJson(){
    getEventsByCategory("json");
}

function deleteEvent($echoType="normal"){
    global $eventModel;
    global $currentUser;
    global $commentModel;
    global $imageModel;
    $id = BasicTool::post("id", "请指定要删除的广告的id");

    try{
        //判断权限
        if(!($currentUser->isUserHasAuthority("ADMIN") && $currentUser->isUserHasAuthority("EVENT"))){
            $sponsor_user_id = $eventModel->getEvent($id)["sponsor_user_id"];
            $currentUser->userId == $sponsor_user_id or BasicTool::throwException("权限不足,删除失败");
        }

        //删除评论
        $bool = $commentModel->deleteCommentsBySection("event", $id);

        //删除图片
        if ($bool) {
            if (is_array($id)) {
                $concat = null;
                foreach ($id as $i) {
                    $i = $i + 0;
                    $i = $i . ",";
                    $concat = $concat . $i;
                }
                $concat = substr($concat, 0, -1);
                $sql = "SELECT * FROM event WHERE id in ({$concat})";
                $events = SqlTool::getSqlTool()->getListBySql($sql);
                $img_ids = array();
                foreach ($events as $event) {
                    if ($event["img_id_1"]) {
                        array_push($img_ids, $event["img_id_1"]);
                    }
                    if ($event["img_id_2"]) {
                        array_push($img_ids, $event["img_id_2"]);
                    }
                    if ($event["img_id_3"]) {
                        array_push($img_ids, $event["img_id_3"]);
                    }
                }

            } else {
                $event = $eventModel->getEvent($id);
                $img_ids = array();
                if ($event["img_id_1"]) {
                    array_push($img_ids, $event["img_id_1"]);
                }
                if ($event["img_id_2"]) {
                    array_push($img_ids, $event["img_id_2"]);
                }
                if ($event["img_id_3"]) {
                    array_push($img_ids, $event["img_id_3"]);
                }
            }
            $bool = $imageModel->deleteImageById($img_ids);
        }

        /*删除ueditor图片
        if ($bool){
            foreach ($id as $i){
                if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/uploads/event/" . $i)) {
                   delete($_SERVER["DOCUMENT_ROOT"] . "/uploads/event/" . $i);
                }
            }
        }
        */

        //删除活动
        if ($bool) {
            $bool = $eventModel->deleteEvent($id);
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
function deleteEventWithJson(){
    deleteEvent("json");
}

function updateEvent($echoType = "normal"){
    global $eventModel;
    global $currentUser;
    global $imageModel;
    $id = BasicTool::post("id","必须填写id");
    try {
        //判断权限
        if(!($currentUser->isUserHasAuthority("ADMIN") && $currentUser->isUserHasAuthority("EVENT"))){
            $sponsor_user_id = $eventModel->getEvent($id)["sponsor_user_id"];
            $currentUser->userId == $sponsor_user_id or BasicTool::throwException("权限不足,更改失败");
        }

        $event_category_id = BasicTool::post("event_category_id");
        $title = BasicTool::post("title");
        $description = BasicTool::post("description");

        $expiration_time = BasicTool::post("expiration_time");
        $event_time = BasicTool::post("event_time");

        $location_link = BasicTool::post("location_link");
        $registration_fee = BasicTool::post("registration_fee");
        $max_participants = BasicTool::post("max_participants");
        $registration_fee >= 0 or BasicTool::throwException("活动费用不能小于0");
        $max_participants >= 0 or BasicTool::throwException("活动名额不能小于0");

        $sponsor_user_id = BasicTool::post("sponsor_user_id");
        $sponsor_name = BasicTool::post("sponsor_name");
        $sponsor_wechat = BasicTool::post("sponsor_wechat");
        $sponsor_email = BasicTool::post("sponsor_email");
        $sponsor_telephone = BasicTool::post("sponsor_telephone");
        $sort = BasicTool::post("sort");
        ($sort == 0 || $sort == 1 || $sort == NULL) or BasicTool::echoMessage("添加失败,请输入有效的排序值(0或者1)");

        $event = $eventModel->getEvent($id);
        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        if ($event["img_id_1"] == 0){
            $event["img_id_1"] = NULL;
        }
        if ($event["img_id_2"] == 0){
            $event["img_id_2"] = NULL;
        }
        if ($event["img_id_3"] == 0){
            $event["img_id_3"] = NULL;
        }
        $currImgArr = array($event["img_id_1"],$event["img_id_2"],$event["img_id_3"]);
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"event");

        $bool = $eventModel->updateEvent($id, $event_category_id, $title, $description, $expiration_time, $event_time, $location_link,
            $registration_fee, $imgArr[0], $imgArr[1], $imgArr[2], $max_participants, $sponsor_user_id, $sponsor_name, $sponsor_wechat, $sponsor_email, $sponsor_telephone,$sort);

        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("更改成功","index.php?s=getEventsByCategory&event_category_id={$event_category_id}&flag=1");
            else
                BasicTool::echoMessage("更改失败","index.php?s=getEventsByCategory&event_category_id={$event_category_id}&flag=1");
        } else {
            if ($bool)
                BasicTool::echoJson(1, "更改成功");
            else
                BasicTool::echoJson(0, "更改失败");
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
function updateEventWithJson(){
   updateEvent("json");

}



function uploadImages() {
    global $imageModel;
    global $currentUser;
    $uploadArr = $imageModel->uploadImg("imgFile", $currentUser->userId, "event") or BasicTool::throwException($imageModel->errorMsg);
    return $uploadArr;
}

function delete($path)
{
    if (is_dir($path) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file)
        {
            if (in_array($file->getBasename(), array('.', '..')) !== true)
            {
                if ($file->isDir() === true)
                {
                    rmdir($file->getPathName());
                }

                else if (($file->isFile() === true) || ($file->isLink() === true))
                {
                    unlink($file->getPathname());
                }
            }
        }

        return rmdir($path);
    }

    else if ((is_file($path) === true) || (is_link($path) === true))
    {
        return unlink($path);
    }

    return false;
}