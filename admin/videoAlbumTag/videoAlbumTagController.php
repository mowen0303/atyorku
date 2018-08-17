<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$videoAlbumValidator = new \admin\videoAlbum\VideoAlbumValidator();
$videoAlbumTagModel = new \admin\videoAlbumTag\VideoAlbumTagModel();
$currentUser = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
call_user_func(BasicTool::get('action'));

/**
 * JSON -  获取一页视频专辑Tag
 * http://www.atyorku.ca/admin/videoAlbumTag/videoAlbumTagController.php?action=getListOfVideoAlbumTagWithJson
 */
function getListOfVideoAlbumTagWithJson() {
    global $videoAlbumTagModel;
    try {
        $pageSize = BasicTool::get("pageSize") ?: 30;
        $result = $videoAlbumTagModel->getListOfVideoAlbumTag($pageSize);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "获取视频专辑类别Tag列表失败");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

function modifyVideoAlbumTag($echoType='normal') {
    global $videoAlbumTagModel;
    global $currentUser;
    global $videoAlbumValidator;
    global $imageModel;
    try{
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $title = BasicTool::post("title", "Tag名称不能为空");
        $flag = BasicTool::post("flag");
        $currentVideoAlbumTag = null;

        if ($flag=='update') {
            $id = intval(BasicTool::post("id"));
            $id>0 or BasicTool::throwException("无效视频专辑Tag ID");
            !$videoAlbumTagModel->isExistOfVideoAlbumTagTitle($title, $id) or BasicTool::throwException("此Tag名称已经存在");
            $currentVideoAlbumTag = $videoAlbumTagModel->getVideoAlbumTag($id) or BasicTool::throwException("无法找到视频专辑Tag");
        } else {
            !$videoAlbumTagModel->isExistOfVideoAlbumTagTitle($title) or BasicTool::throwException("此Tag名称已经存在");
        }

        $coverImgId = BasicTool::post("cover_img_id");
        $videoAlbumValidator::validateCoverImage($coverImgId, "imgFile");

        // analyze images
        $imgArr = array_filter(array($coverImgId));
        $currImgArr = ($currentVideoAlbumTag!=null) ? array_filter(array($currentVideoAlbumTag['cover_img_id'])) : false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr, $currImgArr,1,"imgFile", $currentUser->userId,"video_album_tag");

        if(BasicTool::post('flag') == 'update'){
            $videoAlbumTagModel->updateVideoAlbumTag($currentVideoAlbumTag['id'], $title, $imgArr[0]) or BasicTool::throwException($videoAlbumTagModel->errorMsg);
            if ($echoType == "normal") {
                BasicTool::echoMessage("修改成功","/admin/videoAlbumTag/");
            } else {
                BasicTool::echoJson(1, "修改成功");
            }
        } else {
            $videoAlbumTagModel->addVideoAlbumTag($title, $imgArr[0]);
            if ($echoType == "normal") {
                BasicTool::echoMessage("添加成功","/admin/videoAlbumTag/");
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
 * 删除1个或多个视频专辑Tag
 * @param string $echoType
 */
function deleteVideoAlbumTag($echoType="normal") {
    global $currentUser;
    global $videoAlbumTagModel;
    global $imageModel;
    try {
        $currentUser->isUserHasAuthority('GOD') or BasicTool::throwException("权限不足");
        $id = BasicTool::post('id') or BasicTool::throwException("请指定被删除视频专辑Tag ID");
        $i = 0;
        if (is_array($id)) {
            // multiple ids
            foreach ($id as $v) {
                $i++;
                $albumTag = $videoAlbumTagModel->getVideoAlbumTag($v) or BasicTool::throwException("没找到视频专辑Tag");
                !$albumTag['cover_img_id'] or $imageModel->deleteImageById($albumTag['cover_img_id']);
                $videoAlbumTagModel->deleteVideoAlbumTag($v);
            }
        } else {
            // single id
            $i++;
            $albumTag = $videoAlbumTagModel->getVideoAlbumTag($id) or BasicTool::throwException("没找到视频专辑Tag");
            !$albumTag['cover_img_id'] or $imageModel->deleteImageById($albumTag['cover_img_id']);
            $videoAlbumTagModel->deleteVideoAlbumTag($id);
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}个视频专辑Tag", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}个视频专辑Tag");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


?>
