<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$action = BasicTool::get('action');
$forumModel = new \admin\forum\ForumModel();
$currentUser = new \admin\user\UserModel();
$msgModel = new \admin\msg\MsgModel();
call_user_func(BasicTool::get('action'));

/**
 * JSON -  获取(指定用户)的帖子
 * @param forum_class_id 论坛分类id
 * @param userId
 * @param page 第几页的数据
 * http://www.atyorku.ca/admin/forum/forumController.php?action=getForumListOfSpecificUserWithJson&forum_class_id=0&userId=1&page=1
 */
function getForumListOfSpecificUserWithJson()
{

    global $forumModel;
    try {

        $userId = BasicTool::get('userId', "用户ID不能为空");

        //执行逻辑处理
        $result = $forumModel->getListOfForumByForumClassId(BasicTool::get("forum_class_id"), 40, false, $userId);


        if ($result) {
            //输出json结果
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "没有更多内容");
        }


    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON -  查看用户回复的帖子
 * @param forum_class_id 论坛分类id
 * @param userId
 * @param page 第几页的数据
 * http://www.atyorku.ca/admin/forum/forumController.php?action=getForumListOfSpecificUserToCommentWithJson&forum_class_id=0&userId=1&page=1
 */
function getForumListOfSpecificUserToCommentWithJson()
{

    global $forumModel;
    try {

        $userId = BasicTool::get('userId', "用户ID不能为空");
        $str = $forumModel->getIdOfForumOfUserToCommentByUserId($userId);

        //执行逻辑处理
        $result = $forumModel->getListOfForumByForumClassId(BasicTool::get("forum_class_id"), 40, false, false, $str);


        if ($result) {
            //输出json结果
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "没有更多内容");
        }


    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * JSON -  获取某一分类下的帖子列表
 * @param forum_class_id 论坛分类id
 * @param page 第几页的数据
 * http://www.atyorku.ca/admin/forum/forumController.php?action=getOneForumAndCommentWithJson&forum_id=1
 */
function getOneForumAndCommentWithJson()
{

    global $forumModel;

    $forumId = BasicTool::get("forum_id");

    //执行逻辑处理
    $result1 = $forumModel->getOneRowOfForumById($forumId);
    $result2 = $forumModel->getListOfForumCommentByForumId($forumId);

    if ($result1) {
        BasicTool::echoJson(1, "成功", $result1, $result2);

    } else {
        BasicTool::echoJson(0, "帖子已经被删除", $result1, $result2);
    }


}







//---------------------------------------------------------------------------------------------------------------------










//---------------------------------------------------------------------------------------------------------------------

function modifyForumClass()
{
    //增加或修改论坛分类

    global $forumModel;
    global $currentUser;
    global $title;

    try {

        $currentUser->isUserHasAuthority('GOD') or BasicTool::throwException("无权修改");

        $flag = BasicTool::post('flag');
        $id = BasicTool::post('forum_class_id');

        $arr = [];
        $arr['title'] = BasicTool::post('title', '论坛名称不能为空', 50);
        $arr['description'] = BasicTool::post('description', '论坛描述不能为空', 255);
        $arr['type'] = BasicTool::post('type', '论坛类型不能为空', false, 10);
        $arr['icon'] = BasicTool::post('icon', false, 80);
        $arr['sort'] = BasicTool::post('sort', false, 3);
        $arr['display'] = BasicTool::post('display', false, 1);

        if ($flag == 'add') {
            //增加一个新论坛分类
            if ($forumModel->addRow('forum_class', $arr)) {
                BasicTool::echoMessage("{$title} 添加成功", "index.php?s=listForumClass");
            } else {
                throw new Exception("没有添加任何数据");
            }

        } elseif ($flag == 'update') {
            //修改论坛分类
            if ($forumModel->updateRowById('forum_class', $id, $arr)) {
                BasicTool::echoMessage("修改成功", "index.php?s=listForumClass");
            } else {
                throw new Exception("没有修改任何据数");
            }
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}


//---------------------------------------------------------------------------------------------------------------------


function deleteForumClass()
{
    //删除论坛

    global $currentUser;
    global $forumModel;

    try {

        $currentUser->isUserHasAuthority('ADMIN') & $currentUser->isUserHasAuthority('FORUM_DELETE') or BasicTool::throwException("无权操作");

        $id = BasicTool::post('id');
        $isAdmin = BasicTool::post('isAdmin');


        if ($forumModel->logicalDeleteByFieldIn('forum_class', 'id', $id)) {
            throw new Exception('操作成功');
        } else {
            throw new Exception('删除失败,数据未受影响');
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}


//---------------------------------------------------------------------------------------------------------------------

function addLike()
{
    //点赞

    global $forumModel;

    try {

        $id = BasicTool::get('id');

        if ($forumModel->addLikeByForumId($id)) {

            BasicTool::echoMessage("点赞成功", "/admin/forum/index.php?s=listForumComment&forum_id=" . $id);
        }

    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}

//增加一次阅读量  http://www.atyorku.ca/admin/forum/forumController.php?action=countViewOfForumByIdWithJson&id=26

function countViewOfForumByIdWithJson()
{

    global $forumModel;

    $id = BasicTool::get('id');

    if ($id != null) {
        $forumModel->countViewOfForumById($id);
    }

    BasicTool::echoJson(1, "OK");


}

function countViewCheat()
{
    //点赞

    global $forumModel;
    global $currentUser;
    try {

        $currentUser->isUserHasAuthority('GOD') or BasicTool::throwException("无权限");

        if ($forumModel->countViewCheat()) {

            BasicTool::echoMessage("成功");
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage());
    }
}


/**
 * --------------------------------------------------------
 * --------------------------------------------------------
 *  -------------------- 2.0 已审查 -----------------------
 * --------------------------------------------------------
 * --------------------------------------------------------
 */

/**
 * 获取论坛的一个分类列表
 * [GET] http://www.atyorku.ca/admin/forum/forumController.php?action=getForumClassListWithJson
 * @return json
 */
function getForumClassListWithJson() {
    global $forumModel;
    //执行逻辑处理
    $result = $forumModel->getListOfForumClass(false);
    if ($result) {
        //输出json结果
        BasicTool::echoJson(1, "成功", $result);
    } else {
        BasicTool::echoJson(0, "没有分类列表");
    }
}

/**
 * 获取某一分类下的帖子列表
 * [GET] http://www.atyorku.ca/admin/forum/forumController.php?action=getForumListWithJson&pageSize=15&forum_class_id=4&page=1
 * @param forum_class_id 论坛分类id
 * @param page 第几页的数据
 * @return json
 */
function getForumListWithJson() {
    global $forumModel;
    $pageSize = BasicTool::get('pageSize');
    if(!$pageSize){
        $pageSize = 40;
    }
    $result = $forumModel->getListOfForumByForumClassId(BasicTool::get("forum_class_id"), $pageSize);
    if ($result) {
        setcookie("forumRequest", 1, time() + 5, '/');
        BasicTool::echoJson(1, "成功", $result);
    } else {
        BasicTool::echoJson(0, "没有更多内容");
    }
}

/**
 * 添加一条forum
 * [POST] http://www.atyorku.ca/admin/forum/forumController.php?action=addForumWithJson
 * @param flag 添加or修改 [add/update]
 * @param forum_id 帖子id
 * @param forum_class_id 分类id
 * @param content 帖子内容
 * @param price 价格
 * @param category [buy/sell]
 * @param img1
 *
 *
 */
function addForumWithJson(){
    modifyForum("json");
}


//添加一条forum
function modifyForum($echoType = "normal") {
    global $forumModel;
    global $currentUser;
    try {
        //判断是否有权限发帖
        $currentUser->isUserHasAuthority('FORUM_ADD') or BasicTool::throwException($currentUser->errorMsg);
        $currentUser->addActivity();
        $flag = BasicTool::post('flag');
        $id = BasicTool::post('forum_id');
        $arr = [];
        $arr['content'] = BasicTool::post('content', '内容不能为空', 65500);
        $arr['price'] = BasicTool::post('price', false, 8);
        $arr['category'] = BasicTool::post('category', false, 4);
        $arr['forum_class_id'] = BasicTool::post('forum_class_id', '所属论坛组不能为空');
        if ($arr['price'] == null) {
            $arr['price'] = 0;
        }
        is_numeric($arr['price']) or BasicTool::throwException("价格格式不正确");
        $arr['sort'] = BasicTool::post('sort', false, 3);
        if ($arr['sort'] == null) {
            $arr['sort'] = 0;
        }
        if ($flag == 'add') {
            $arr['time'] = time();
            $arr['update_time'] = time();
            //上传图片 --------
            $arr["img1"] = $currentUser->uploadImg("img1", $currentUser->userId) or BasicTool::throwException($currentUser->errorMsg);
            //增加一个新信息
            $arr['user_id'] = $currentUser->userId;
            if ($forumModel->addRow('forum', $arr)) {
                //更新今日数据
                $forumModel->updateCountOfToday($arr['forum_class_id']);
                $forumModel->updateCountOfAll($arr['forum_class_id']);

                if ($echoType == "normal") {
                    BasicTool::echoMessage("新信息添加成功", "/admin/forum/index.php?s=listForum&forum_class_id=" . $arr['forum_class_id']);
                } else {
                    BasicTool::echoJson(1, "新信息添加成功");
                }

            }
        } elseif ($flag == 'update') {
            $currentUser->isUserHasAuthority('FORUM_UPDATE') or BasicTool::throwException($currentUser->errorMsg);
            $arr['img1'] = BasicTool::post('img1');
            //修改信息
            if ($forumModel->updateRowById('forum', $id, $arr)) {
                if ($echoType == "normal") {
                    BasicTool::echoMessage("修改成功", "/admin/forum/index.php?s=listForum&forum_class_id=" . $arr['forum_class_id']);
                } else {
                    BasicTool::echoJson(1, "修改成功");
                }

            } else {
                throw new Exception("没有修改任何数据");
            }
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }

    }
}

/**
 * 删除一条forum
 * [POST] http://www.atyorku.ca/admin/forum/forumController.php?action=deleteForumWithJson
 * @param id  帖子id
 */
function deleteForumWithJson() {
    deleteForum("json");
}
function deleteForum($echoType = "normal") {
    //删除论坛信息
    global $forumModel;
    try {
        $id = BasicTool::post('id');
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $i++;
                $forumModel->deleteOneForumById($v) or BasicTool::throwException("删除多条失败");
            }
        } else {
            $i++;
            $forumModel->deleteOneForumById($id) or BasicTool::throwException("删除1条失败");
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}条帖子", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}条帖子");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


/**
 * 获取论坛中某一内容的评论列表
 * [GET] http://www.atyorku.ca/admin/forum/forumController.php?action=getForumCommentListWithJson&forum_id=300&pageSize=20&page=1
 * @param forum_id 论坛内容id
 * @param pageSize
 * @param page 第几页的数据
 */
function getForumCommentListWithJson(){
    global $forumModel;
    //执行逻辑处理
    if(!$pageSize = BasicTool::get('pageSize')){
        $pageSize = 40;
    }
    $result = $forumModel->getListOfForumCommentByForumId(BasicTool::get("forum_id"), $pageSize);
    $totalPage = $forumModel->getTotalPage();
    if ($result) {
        //输出json结果
        BasicTool::echoJson(1, "成功", "", $result, $totalPage);
    } else {
        BasicTool::echoJson(0, "");
    }
}

/**
 * 删除一条评论
 * [GET] http://www.atyorku.ca/admin/forum/forumController.php?action=deleteCommentWithJson&id=26
 * @param  id 评论ID
 */
function deleteCommentWithJson(){
    deleteComment($echoType = "json");
}

function deleteComment($echoType = "normal"){
    //删除评论
    global $forumModel;
    global $currentUser;
    try {
        $id = BasicTool::get('id');
        //判断是否有权限发帖
        if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('FORUM_DELETE'))) {
            //如果用户不是管理员, 检查数据是否是用户发的
            $currentUser->userId == $forumModel->getUserIdOfForumCommentByCommentId($id) or BasicTool::throwException("无权删除其他人的留言");
        }
        $forumId = $forumModel->getForumIdOfCommentId($id) or BasicTool::throwException("forumId:" . $forumId);
        $forumModel->realDeleteByFieldIn("forum_comment", "id", $id) or BasicTool::throwException($forumModel->errorMsg);
        $forumModel->countAmountOfComment($forumId);
        if ($echoType == "normal") {
            BasicTool::echoMessage("删除成功");
        } else {
            BasicTool::echoJson(1, "删除成功");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


/**
 * 添加一条评论
 * [POST] http://www.atyorku.ca/admin/forum/forumController.php?action=addCommentWithJson
 * @param content_comment
 * @param forum_id
 * @param ownerUserId
 * @param receiveUserId
 * @return json {code,message,forumCommentObject}
 */
function addCommentWithJson()
{
    addComment("json");
}

function addComment($echoType = "normal")
{
    //增加评论
    global $currentUser;
    global $forumModel;
    global $msgModel;
    try {
        //判断是否有权限发帖
        $currentUser->isUserHasAuthority('FORUM_COMMENT') or BasicTool::throwException($currentUser->errorMsg);
        $currentUser->addActivity();
        $arr = [];
        $arr['content_comment'] = BasicTool::post('content_comment', '内容不能为空', 255);
        $arr['user_id'] = $currentUser->userId;
        $arr['time'] = time();
        $forumId = BasicTool::post('forum_id');
        $ownerUserId = BasicTool::post('ownerUserId');
        $receiveUserId = BasicTool::post('receiveUserId');
        $forumId += 0;
        $forumId != 0 or BasicTool::throwException("forum_id非法");
        $arr['forum_id'] = $forumId;
        $forumModel->addRow('forum_comment', $arr) or BasicTool::throwException($forumModel->errorMsg);
        //更新统计
        $forumModel->countAmountOfComment($arr['forum_id']);
        $forumModel->updateForumTime($arr['forum_id']);
        $newComment = $forumModel->getCommentById($forumModel->idOfInsert);
        //推送信息
        $msgModel->pushMsgToUser($ownerUserId,"forumComment", $forumId, $arr['content_comment']);
        if($ownerUserId!=$receiveUserId){
            $msgModel->pushMsgToUser($receiveUserId,"forumComment", $forumId, $arr['content_comment']);
        }

        if ($echoType == "normal") {
            BasicTool::echoMessage("评论添加成功", "/admin/forum/index.php?s=listForumComment&forum_id=" . $arr['forum_id'] . "&forum_class_id=" . $arr['forum_class_id']);
        } else {
            BasicTool::echoJson(1, "评论添加成功",$newComment);
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


/**
 * 获取report的数量
 * [GET] http://www.atyorku.ca/admin/forum/forumController.php?action=getAmountOfReport
 */
function getAmountOfReport()
{
    global $forumModel;
    try {
        $result = $forumModel->getAmountOfReport();
        BasicTool::echoJson(1, "成功", "{$result}");
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * 获取举报的列表
 * http://www.atyorku.ca/admin/forum/forumController.php?action=getReportedListWithJson&page=1
 */
function getReportedListWithJson()
{
    global $forumModel;

    try {
        $result1 = $forumModel->getListOfForumByForumClassId(0, 20, true);
        $result2 = $forumModel->getListOfForumCommentByForumId(0, 20, true);
        !($result1 == null && $result2 == null) or BasicTool::throwException("没有更多内容了");
        BasicTool::echoJson(1, "成功", $result1, $result2);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * 举报一个forum
 * [GET] http://www.atyorku.ca/admin/forum/forumController.php?action=reportForumWithJson&forumId=20
 */
function reportForumWithJson()
{
    global $forumModel;
    try {
        $forumId = BasicTool::get('forumId', "ForumId不能为空");

        $forumModel->reportForum($forumId) or BasicTool::throwException($forumModel->errorMsg);
        BasicTool::echoJson(1, "举报成功");
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * 举报一个Comment
 * [GET] http://www.atyorku.ca/admin/forum/forumController.php?action=reportForumCommentWithJson&forumCommentId=40
 */
function reportForumCommentWithJson()
{
    global $forumModel;
    try {
        $forumCommentId = BasicTool::get('forumCommentId', "ForumId不能为空");
        $forumModel->reportForumComment($forumCommentId) or BasicTool::throwException($forumModel->errorMsg);
        BasicTool::echoJson(1, "举报成功");
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * 还原举报的forum
 * [GET] http://www.atyorku.ca/admin/forum/forumController.php?action=reportForumRestoreWithJson&$forumId=40
 */
function reportForumRestoreWithJson()
{
    global $forumModel;
    try {
        $forumId = BasicTool::get('forumId', "ForumId不能为空");
        $forumModel->reportedForumRestore($forumId) or BasicTool::throwException($forumModel->errorMsg);
        BasicTool::echoJson(1, "还原成功");
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}




/**
 * 还原forumComment
 * [GET] http://www.atyorku.ca/admin/forum/forumController.php?action=reportForumCommentRestoreWithJson&forumCommentId=40
 */
function reportForumCommentRestoreWithJson()
{
    global $forumModel;

    try {
        $forumCommentId = BasicTool::get('forumCommentId', "forumCommentId不能为空");
        $forumModel->reportedForumCommentRestore($forumCommentId) or BasicTool::throwException($forumModel->errorMsg);
        BasicTool::echoJson(1, "还原成功");
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}




?>
