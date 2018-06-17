<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventModel = new \apps\event\event\EventModel();
$currentUser = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$commentModel = new \admin\comment\CommentModel();
date_default_timezone_set("America/Toronto");
call_user_func(BasicTool::get('action'));


/**添加活动
 * POST
 * @param event_category_id 活动分类id
 * @param title 活动标题
 * @param description 活动详情
 * @param expiration_time 活动截止时间,PHP时间戳
 * @param event_time 活动时间,PHP时间戳
 * @param location_link 活动地址,谷歌地图URL
 * @param detail_url 活动详情链接
 * @param registration_fee 报名费
 * @param max_participants 活动名额
 * @param sponsor_user_id
 * @param sponsor_name
 * @param sponsor_wechat
 * @param sponsor_email
 * @param sponsor_telephone
 * @param sort 排序值
 * @param img_id_1
 * @param img_id_2
 * @param img_id_3
 * localhost/admin/event/eventController.php?action=addEvent
 */
function addEvent($echoType = "normal") {
    global $eventModel;
    global $currentUser;
    global $imageModel;
    try {
        //判断权限
        ($currentUser->isUserHasAuthority("ADMIN") || $currentUser->isUserHasAuthority("EVENT_ADMIN")) or BasicTool::throwException("权限不足,添加失败");

        $event_category_id = BasicTool::post("event_category_id", "请选择活动分类");
        $title = BasicTool::post("title", "活动标题不能为空");
        $event_time = BasicTool::post("event_time", "请填写开始日期");
        $expiration_time = BasicTool::post("expiration_time", "请填写结束日期");
        $location = BasicTool::post("location", "请填活动地点");
        $location_link = BasicTool::post("location_link");
        $detail_url = BasicTool::post("detail_url");
        $registration_fee = BasicTool::post("registration_fee");
        $registration_way = BasicTool::post("registration_way", "请填写报名方式");
        $registration_link = BasicTool::post("registration_link");
        $max_participants = BasicTool::post("max_participants");
        $description = BasicTool::post("description", "missing description");
        $sponsor_user_id = BasicTool::post("sponsor_user_id");
        $sponsor_name = BasicTool::post("sponsor_name");
        $sponsor_wechat = BasicTool::post("sponsor_wechat");
        $sponsor_email = BasicTool::post("sponsor_email");
        $sponsor_telephone = BasicTool::post("sponsor_telephone");

        $registration_fee >= 0 or BasicTool::throwException("活动费用不能小于0");
        $max_participants >= 0 or BasicTool::throwException("活动名额不能小于0");

        $event_time = BasicTool::translateHTMLTimeToPHPStaple($event_time);
        $expiration_time = BasicTool::translateHTMLTimeToPHPStaple($expiration_time);

        $sort = BasicTool::post("sort");
        ($sort == 0 || $sort == 1 || $sort == NULL) or BasicTool::echoMessage("添加失败,请输入有效的排序值(0或者1)");
        $imgArr = array(BasicTool::post("img_id_1"), BasicTool::post("img_id_2"), BasicTool::post("img_id_3"));
        $currImgArr = false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr, $currImgArr, 3, "imgFile", $currentUser->userId, "event");
        $eventModel->addEvent($event_category_id, $title, $description,
                            $expiration_time, $event_time, $location,
                            $location_link, $detail_url,$registration_fee,$registration_way,$registration_link,
                            $imgArr[0], $imgArr[1], $imgArr[2], $max_participants,
                            $sponsor_user_id, $sponsor_name, $sponsor_wechat,
                            $sponsor_email, $sponsor_telephone, $sort) or BasicTool::throwException("添加失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("添加成功", "index.php?s=getEventsByCategory");
        } else {
            BasicTool::echoJson(1, "添加成功");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


/**添加活动
 * POST
 * JSON接口
 * @param event_category_id 活动分类id
 * @param title 活动标题
 * @param description 活动详情
 * @param expiration_time 活动截止时间,PHP时间戳
 * @param event_time 活动时间,PHP时间戳
 * @param location_link 活动地址,谷歌地图URL
 * @param registration_fee 报名费
 * @param max_participants 活动名额
 * @param sponsor_user_id
 * @param sponsor_name
 * @param sponsor_wechat
 * @param sponsor_email
 * @param sponsor_telephone
 * @param sort 排序值
 * @param img_id_1
 * @param img_id_2
 * @param img_id_3
 * localhost/admin/event/eventController.php?action=addEventWithJson
 */
function addEventWithJson() {
    addEvent("json");
}

/**根据分类ID查询一页活动
 * GET
 * @param event_category_id         分类id 0查询全部
 * @param onlyShowEffectEvent       0:显示所有活动 1:只显示即将开始和进行中的活动 2:只显示已经结束的活动
 * @param page                      页数
 * localhost/admin/event/eventController.php?action=getEventsByCategory&event_category_id=2&onlyShowEffectEvent=true&pageSize=3&page=1
 */
function getEventsByCategory($echoType = "normal") {
    global $eventModel;
    try {
        $event_category_id = BasicTool::get("event_category_id", "请指定广告分类id");
        $onlyShowEffectEvent = BasicTool::get("onlyShowEffectEvent")?:0;
        $pageSize = BasicTool::get("pageSize") ?: 10;
        $result = $eventModel->getEventsByCategory($event_category_id,$onlyShowEffectEvent,$pageSize) or BasicTool::throwException("查询失败");
        $currentTime = time();
//        echo date("Y-m-d H:m:s",time());


        foreach($result as $key => $item){
            if($currentTime<$item['event_time']){
                //还未开始
                $time = $item['event_time']-$currentTime;
                $day = floor($time/(60*60*24));
                $hour = floor(($time%(60*60*24))/(60*60));
                $minute = floor(($time%(60*60))/60);
                $result[$key]['state_code'] = "1";
                if($day){
                    $result[$key]['state'] = "倒计时:{$day}天";
                }else if($hour){
                    $result[$key]['state'] = "倒计时:{$hour}小时";
                }else{
                    $result[$key]['state'] = "倒计时:{$minute}分钟";
                }
            }else if ($currentTime<$item['expiration_time']){
                //进行中
                $result[$key]['state'] = "活动进行中";
                $result[$key]['state_code'] = "2";
            }else{
                //已结束
                $result[$key]['state'] = "已结束";
                $result[$key]['state_code'] = "0";
            }
            $result[$key]['event_time'] = date("Y-m-d H:i",$item['event_time']);
            $result[$key]['expiration_time'] = date("Y-m-d H:i",$item['expiration_time']);
            $result[$key]['publish_time'] = date("Y-m-d H:i",$item['publish_time']);

        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("查询成功");
        } else {
            BasicTool::echoJson(1, "查询成功", $result);
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**根据分类ID查询一页活动
 * GET
 * JSON接口
 * @param event_category_id 分类id
 * @param page 页数
 * localhost/admin/event/eventController.php?action=getEventsByCategoryWithJson&event_category_id=2&pageSize=3&page=1
 */
function getEventsByCategoryWithJson() {
    getEventsByCategory("json");
}

/**根据活动ID删除活动
 * POST
 * @param id int或者一维数组
 *  localhost/admin/event/eventController.php?action=deleteEvent
 */
function deleteEvent($echoType = "normal") {
    global $eventModel;
    global $currentUser;
    global $commentModel;
    global $imageModel;
    $id = BasicTool::post("id", "请指定要删除的广告");
    try {
        //判断权限
        if (!($currentUser->isUserHasAuthority("ADMIN") && $currentUser->isUserHasAuthority("EVENT_ADMIN"))) {
            if ($echoType == 'normal'){
                foreach ($id as $i){
                    $sponsor_user_id = $eventModel->getEvent($i)['sponsor_user_id'];
                    $currentUser->userId == $sponsor_user_id or BasicTool::throwException("权限不足,删除失败");
                }
            }else{
                $sponsor_user_id = $eventModel->getEvent($id)["sponsor_user_id"];
                $currentUser->userId == $sponsor_user_id or BasicTool::throwException("权限不足,删除失败");
            }
        }

        //删除评论
        $commentModel->deleteComment("event", $id) or BasicTool::throwException("删除失败,评论删除失败");

        //删除图片
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
        $imageModel->deleteImageById($img_ids) or BasicTool::throwException("删除失败，删除图片失败");

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
        $eventModel->deleteEvent($id) or BasicTool::throwException("删除失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("删除成功");
        } else {
            BasicTool::echoJson(1, "删除成功");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**根据活动ID删除活动
 * POST
 * JSON接口
 * @param id int或者一维数组
 *  localhost/admin/event/eventController.php?action=deleteEventWithJson
 */
function deleteEventWithJson() {
    deleteEvent("json");
}

/**更改活动
 * POST
 * @param id 活动ID
 * @param event_category_id 活动分类id
 * @param title 活动标题
 * @param description 活动详情
 * @param expiration_time 活动截止时间,PHP时间戳
 * @param event_time 活动时间,PHP时间戳
 * @param location_link 活动地址,谷歌地图URL
 * @param detail_url 活动详情链接
 * @param registration_fee 报名费
 * @param max_participants 活动名额
 * @param sponsor_user_id
 * @param sponsor_name
 * @param sponsor_wechat
 * @param sponsor_email
 * @param sponsor_telephone
 * @param sort 排序值
 * @param img_id_1
 * @param img_id_2
 * @param img_id_3
 *  localhost/admin/event/eventController.php?action=updateEvent
 */
function updateEvent($echoType = "normal") {
    global $eventModel;
    global $currentUser;
    global $imageModel;
    $id = BasicTool::post("id", "必须填写id");
    try {
        //判断权限
        if (!($currentUser->isUserHasAuthority("ADMIN") && $currentUser->isUserHasAuthority("EVENT_ADMIN"))) {
            $sponsor_user_id = $eventModel->getEvent($id)["sponsor_user_id"];
            $currentUser->userId == $sponsor_user_id or BasicTool::throwException("权限不足,更改失败");
        }

        $event_category_id = BasicTool::post("event_category_id", "请选择活动分类");
        $title = BasicTool::post("title", "活动标题不能为空");
        $event_time = BasicTool::post("event_time", "请填写开始日期");
        $expiration_time = BasicTool::post("expiration_time", "请填写结束日期");
        $location = BasicTool::post("location", "请填活动地点");
        $location_link = BasicTool::post("location_link");
        $detail_link = BasicTool::post("detail_url");
        $registration_fee = BasicTool::post("registration_fee");
        $registration_way = BasicTool::post("registration_way", "请填写报名方式");
        $registration_link = BasicTool::post("registration_link");
        $max_participants = BasicTool::post("max_participants");
        $description = BasicTool::post("description", "missing description");
        $sponsor_name = BasicTool::post("sponsor_name");
        $sponsor_wechat = BasicTool::post("sponsor_wechat");
        $sponsor_email = BasicTool::post("sponsor_email");
        $sponsor_telephone = BasicTool::post("sponsor_telephone");

        $registration_fee >= 0 or BasicTool::throwException("活动费用不能小于0");
        $max_participants >= 0 or BasicTool::throwException("活动名额不能小于0");

        $event_time = BasicTool::translateHTMLTimeToPHPStaple($event_time);
        $expiration_time = BasicTool::translateHTMLTimeToPHPStaple($expiration_time);

        $sort = BasicTool::post("sort");

        ($sort == 0 || $sort == 1 || $sort == NULL) or BasicTool::echoMessage("添加失败,请输入有效的排序值(0或者1)");

        $event = $eventModel->getEvent($id);
        $event or BasicTool::throwException("活动不存在");
        $old_event_category_id = $event['event_category_id'];

        $imgArr = array(BasicTool::post("img_id_1"), BasicTool::post("img_id_2"), BasicTool::post("img_id_3"));
        if ($event["img_id_1"] == 0) {
            $event["img_id_1"] = NULL;
        }
        if ($event["img_id_2"] == 0) {
            $event["img_id_2"] = NULL;
        }
        if ($event["img_id_3"] == 0) {
            $event["img_id_3"] = NULL;
        }
        $currImgArr = array($event["img_id_1"], $event["img_id_2"], $event["img_id_3"]);
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr, $currImgArr, 3, "imgFile", $currentUser->userId, "event");

        $eventModel->updateEvent($id, $old_event_category_id, $event_category_id, $title, $description, $expiration_time, $event_time, $location, $location_link, $detail_link,
            $registration_fee,$registration_way,$registration_link, $imgArr[0], $imgArr[1], $imgArr[2], $max_participants, $sponsor_name, $sponsor_wechat, $sponsor_email, $sponsor_telephone, $sort) or BasicTool::throwException("更改失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("更改成功", "index.php?s=getEventsByCategory");
        } else {
            BasicTool::echoJson(1, "更改成功");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**更改活动
 * POST
 * JSON接口
 * @param id 活动ID
 * @param event_category_id 活动分类id
 * @param title 活动标题
 * @param description 活动详情
 * @param expiration_time 活动截止时间,PHP时间戳
 * @param event_time 活动时间,PHP时间戳
 * @param location_link 活动地址,谷歌地图URL
 * @param registration_fee 报名费
 * @param max_participants 活动名额
 * @param sponsor_user_id
 * @param sponsor_name
 * @param sponsor_wechat
 * @param sponsor_email
 * @param sponsor_telephone
 * @param sort 排序值
 * @param img_id_1
 * @param img_id_2
 * @param img_id_3
 *  localhost/admin/event/eventController.php?action=updateEventWithJson
 */
function updateEventWithJson() {
    updateEvent("json");

}

function delete($path) {
    if (is_dir($path) === true) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if (in_array($file->getBasename(), array('.', '..')) !== true) {
                if ($file->isDir() === true) {
                    rmdir($file->getPathName());
                } else if (($file->isFile() === true) || ($file->isLink() === true)) {
                    unlink($file->getPathname());
                }
            }
        }

        return rmdir($path);
    } else if ((is_file($path) === true) || (is_link($path) === true)) {
        return unlink($path);
    }

    return false;
}

// to be deleted
///**增加点击量 GET传值
// * @param $event_id
// */
//function addClickCountWithJson(){
//    global $eventModel;
//    try{
//        $event_id = BasicTool::get('event_id','Missing event_id');
//        $eventModel->addClickCount($event_id) or BasicTool::throwException("增加点击量失败");
//        BasicTool::echoJson(1,"增加点击量成功");
//
//    }catch (Exception $e){
//        BasicTool::echoJson(0, $e->getMessage());
//    }
//}