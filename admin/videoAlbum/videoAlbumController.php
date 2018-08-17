<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$videoAlbumTagModel = new \admin\videoAlbumTag\VideoAlbumTagModel();
$videoAlbumModel = new \admin\videoAlbum\VideoAlbumModel();
$videoAlbumValidator = new \admin\videoAlbum\VideoAlbumValidator();
$imageModel = new \admin\image\ImageModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


/**=============**/
/**     API     **/
/**=============**/


/**
 * JSON -  获取一页视频专辑 (GET)
 * http://www.atyorku.ca/admin/videoAlbum/videoAlbumController.php?action=getListOfVideoAlbumWithJson
 *
 * [POST][optional] - categories : string with category ids, separated with comma ( '2,3,5' )
 * [POST][optional] - text : string to search album title and description
 */
function getListOfVideoAlbumWithJson() {
    global $videoAlbumModel;
    try {
        $pageSize = BasicTool::get("pageSize") ?: 20;
        $conditions = [];

        // Check and build tag filter
        $tags = trim(BasicTool::get("tags"));
        if ($tags) {
            $tags = implode(
                ",",
                array_unique(
                    array_filter(
                        explode(",", $tags),
                        function($id){
                            return intval($id);
                        }
                    )
                )
            );
        }
        if ($tags) {
            array_push($conditions, "(ta.tag_id IN ({$tags}))");
        }

        // Check and build category filter
        $categories = trim(BasicTool::get("categories")); // string of ids with comma
        if ($categories) {
            $categories = implode(
                ",",
                array_unique(
                    array_filter(
                        explode(",", $categories),
                        function($id){
                            return intval($id);
                        }
                    )
                )
            );
        }

        if ($categories) {
            array_push($conditions, "(va.category_id IN ({$categories}))");
        }

        // Check and build full text filter
        $text = trim(BasicTool::get("text"));
        if ($text) {
            array_push($conditions, "(va.title LIKE '%{$text}%' OR va.description LIKE '%{$text}%')");
        }

        $q = implode(" AND ", $conditions);

        $result = $videoAlbumModel->getListOfVideoAlbum($pageSize, 0, 1, $q);

        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "获取视频专辑类别列表失败");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON - 添加|修改一个视频专辑 (POST)
 * http://www.atyorku.ca/admin/videoAlbum/videoAlbumController.php?action=modifyVideoAlbumWithJson
 */
function modifyVideoAlbumWithJson() {
    modifyVideoAlbum("json");
}

/**
 * JSON - 删除一个或多个视频专辑 (POST)
 * http://www.atyorku.ca/admin/videoAlbum/videoAlbumController.php?action=deleteVideoAlbumWithJson
 */
function deleteVideoAlbumWithJson() {
    deleteVideoAlbum("json");
}


/**=============**/
/**   methods   **/
/**=============**/


/**
 * 添加|修改一个视频专辑
 * @param string $echoType
 */
function modifyVideoAlbum($echoType='normal') {
    global $videoAlbumModel;
    global $videoAlbumValidator;
    global $imageModel;
    global $currentUser;

    try{
        $flag = BasicTool::post('flag');
        $currentVideoAlbum = null;
        $videoAlbumUserId = null;

        // 获取要更新|添加的视频专辑的用户ID
        if ($flag=='update') {
            $id = intval(BasicTool::post("id"));
            $id>0 or BasicTool::throwException("无效视频专辑ID");
            $currentVideoAlbum = $videoAlbumModel->getRawAlbumById($id) or BasicTool::throwException("无法找到视频专辑");
            $videoAlbumUserId = BasicTool::post("user_id") ?: $currentVideoAlbum['user_id'];
        } else if ($flag=='add') {
            $videoAlbumUserId = BasicTool::post("user_id") ?: $currentUser->userId;
        } else {
            BasicTool::throwException("无效操作");
        }

        // 验证权限
        checkAuthority($flag, $videoAlbumUserId);

        // 验证fields
        $title = $videoAlbumValidator::validateTitle(BasicTool::post("title"));
        $description = $videoAlbumValidator::validateDescription(BasicTool::post("description"));
        $categoryId = $videoAlbumValidator::validateVideoAlbumTagId(BasicTool::post("category_id"));
        $institutionId = $videoAlbumValidator::validateInstitutionId(BasicTool::post("institution_id"));
        $courseCodeId = $videoAlbumValidator::validateCourseId(BasicTool::post("course_code_parent_title"), BasicTool::post("course_code_child_title"));
        $professorId = $videoAlbumValidator::validateProfessorName(BasicTool::post("prof_name"));
        $price = $videoAlbumValidator::validatePrice(BasicTool::post("price"));
        $coverImgId = BasicTool::post("cover_img_id");
        $videoAlbumValidator::validateCoverImage($coverImgId, "imgFile");

        // analyze images
        $imgArr = array_filter(array($coverImgId));
        $currImgArr = ($currentVideoAlbum!=null) ? array_filter(array($currentVideoAlbum['cover_img_id'])) : false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr, $currImgArr,1,"imgFile", $currentUser->userId,"video_album");

        // 执行
        if ($flag=='update') {
            $videoAlbumModel->updateVideoAlbumById(
                $currentVideoAlbum['id'],
                $title,
                $description,
                $categoryId,
                $videoAlbumUserId,
                $courseCodeId,
                $professorId,
                $institutionId,
                $price,
                $imgArr[0]
            ) or BasicTool::throwException($videoAlbumModel->errorMsg);
            if ($echoType == "normal") {
                BasicTool::echoMessage("修改成功","/admin/videoAlbum/index.php?listVideoAlbum");
            } else {
                BasicTool::echoJson(1, "修改成功");
            }
        } else if ($flag=='add') {
            $videoAlbumModel->addVideoAlbum(
                $title,
                $description,
                $categoryId,
                $videoAlbumUserId,
                $courseCodeId,
                $professorId,
                $institutionId,
                $price,
                $imgArr[0]
            ) or BasicTool::throwException($videoAlbumModel->errorMsg);
            if ($echoType == "normal") {
                BasicTool::echoMessage("添加成功","/admin/videoAlbum/index.php?listVideoAlbum");
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
 * 删除1个或多个视频专辑
 * @param string $echoType
 *
 * [POST] int|string|array id 被指定删除的单个或多个视频专辑id
 */
function deleteVideoAlbum($echoType="normal") {
    global $videoAlbumModel;
    global $imageModel;

    try {
        $id = BasicTool::post('id') or BasicTool::throwException("请指定被删除视频专辑ID");
        $i = 0;
        if (is_array($id)) {
            // multiple ids
            foreach ($id as $v) {
                $i++;
                $album = $videoAlbumModel->getRawAlbumById($v) or BasicTool::throwException("没找到视频专辑");
                checkAuthority('delete', $album['user_id']);
                !$album['cover_img_id'] or $imageModel->deleteImageById($album['cover_img_id']);
                $videoAlbumModel->deleteVideoAlbumById($v);
            }
        } else {
            // single id
            $i++;
            $album = $videoAlbumModel->getRawAlbumById($id) or BasicTool::throwException("没找到视频专辑");
            checkAuthority('delete', $album['user_id']);
            !$album['cover_img_id'] or $imageModel->deleteImageById($album['cover_img_id']);
            $videoAlbumModel->deleteVideoAlbumById($id);
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}个视频专辑", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}个视频专辑");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


function updateAvailability($echoType="normal") {

}


/**
 * 检测权限
 * @param string flag 'add' | 'update' | 'delete'
 * @param int $id current video album user id
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
        $id or BasicTool::throwException("Modified video album user id is required.");
        $currentUser->userId or BasicTool::throwException("权限不足，请先登录");
        if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('VIDEO_ALBUM'))) {
            $currentUser->userId == $id or BasicTool::throwException("无权修改其他人的视频专辑");
        }
    }
}


?>
