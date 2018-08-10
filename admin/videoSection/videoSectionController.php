<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$videoAlbumModel = new \admin\videoAlbum\VideoAlbumModel();
$videoSectionModel = new \admin\videoSection\VideoSectionModel();
$videoSectionValidator = new \admin\videoSection\VideoSectionValidator();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


/**=============**/
/**     API     **/
/**=============**/


/**
 * JSON -  获取一页视频章节 (GET)
 * http://www.atyorku.ca/admin/videoSection/videoSectionController.php?action=getListOfVideoSectionsWithJson
 */
function getListOfVideoSectionsWithJson() {
    global $videoSectionModel;
    try {
        $albumId = BasicTool::get("album_id", "视频专辑ID不能为空");
        $pageSize = BasicTool::get("pageSize") ?: 20;
        $result = $videoSectionModel->getListOfVideoSectionByVideoAlbumId($albumId, $pageSize);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "获取视频章节列表失败");
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

/**
 * JSON - 添加|修改一个视频章节 (POST)
 * http://www.atyorku.ca/admin/videoSection/videoSectionController.php?action=modifyVideoSectionWithJson
 */
function modifyVideoSectionWithJson() {
    modifyVideoSection("json");
}

/**
 * JSON - 删除一个或多个视频章节 (POST)
 * http://www.atyorku.ca/admin/videoSection/videoSectionController.php?action=deleteVideoSectionWithJson
 */
function deleteVideoAlbumWithJson() {
    deleteVideoSection("json");
}


/**=============**/
/**   methods   **/
/**=============**/


/**
 * 添加|修改一个视频章节
 * @param string $echoType
 */
function modifyVideoSection($echoType='normal') {
    global $videoAlbumModel;
    global $videoSectionModel;
    global $videoSectionValidator;

    try{
        $flag = BasicTool::post('flag');
        $currentVideoSection = null;
        $videoAlbumUserId = null;

        // 验证fields
        $title = $videoSectionValidator::validateTitle(BasicTool::post("title"));
        $videoAlbumId = BasicTool::post("album_id", "视频专辑ID不能为空");

        if ($flag=='update') {
            $id = intval(BasicTool::post("id"));
            $id>0 or BasicTool::throwException("无效视频章节ID");
            $currentVideoSection = $videoSectionModel->getRawSectionById($id) or BasicTool::throwException("无法找到视频章节");
            intval($currentVideoSection['album_id']) === intval($videoAlbumId) or BasicTool::throwException("无法修改视频专辑ID");
        } else if ($flag=='add') {

        } else {
            BasicTool::throwException("无效操作");
        }

        $videoAlbum = $videoAlbumModel->getRawAlbumById($videoAlbumId) or BasicTool::throwException("无法找到视频章节对应的专辑");
        $videoAlbumUserId = $videoAlbum['user_id'];

        // 验证权限
        checkAuthority($flag, $videoAlbumUserId);

        // 执行
        if ($flag=='update') {
            $videoSectionModel->updateVideoSectionById(
                $currentVideoSection['id'],
                $title,
                $videoAlbumId
            ) or BasicTool::throwException($videoSectionModel->errorMsg);

            if ($echoType == "normal") {
                BasicTool::echoMessage("修改成功","/admin/videoSection/index.php?s=listVideoSection&album_id={$videoAlbumId}");
            } else {
                BasicTool::echoJson(1, "修改成功");
            }
        } else if ($flag=='add') {
            $videoSectionModel->addVideoSection(
                $title,
                $videoAlbumId
            ) or BasicTool::throwException($videoSectionModel->errorMsg);

            if ($echoType == "normal") {
                BasicTool::echoMessage("添加成功","/admin/videoSection/index.php?s=listVideoSection&album_id={$videoAlbumId}");
            } else {
                BasicTool::echoJson(1, "添加成功");
            }
        }
    }
    catch (Exception $e){
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
 * 删除1个或多个视频章节
 * @param string $echoType
 *
 * [POST] int|string|array id 被指定删除的单个或多个视频章节id
 */
function deleteVideoSection($echoType="normal") {
    global $videoSectionModel;
    try {
        $id = BasicTool::post('id') or BasicTool::throwException("请指定被删除视频章节ID");
        $i = 0;
        if (is_array($id)) {
            // multiple ids
            foreach ($id as $v) {
                $i++;
                $section = $videoSectionModel->getVideoSectionById($v) or BasicTool::throwException("没找到视频章节");
                checkAuthority('delete', $section['user_id']);
                $videoSectionModel->deleteVideoSectionById($v);
            }
        } else {
            // single id
            $i++;
            $section = $videoSectionModel->getVideoSectionById($id) or BasicTool::throwException("没找到视频章节");
            checkAuthority('delete', $section['user_id']);
            $videoSectionModel->deleteVideoSectionById($id);
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}个视频章节", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}个视频章节");
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
 * 检测权限
 * @param string flag 'add' | 'update' | 'delete'
 * @param int $id current video section's album user id
 * @throws Exception
 */
function checkAuthority($flag, $id) {
    global $currentUser;
    $id = intval($id);
    if ($flag == 'add') {
        if($currentUser->userId===$id){
            $currentUser->isUserHasAuthority('VIDEO_ALBUM') or BasicTool::throwException("权限不足");
        } else {
            $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        }
    } else if ($flag == 'update' || $flag == 'delete') {
        $id or BasicTool::throwException("Current video section's album user id is required.");
        $currentUser->userId or BasicTool::throwException("权限不足，请先登录");
        if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('VIDEO_ALBUM'))) {
            $currentUser->userId == $id or BasicTool::throwException("无权修改其他人的视频章节");
        }
    }
}


?>
