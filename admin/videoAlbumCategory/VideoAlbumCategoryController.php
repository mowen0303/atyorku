<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$videoAlbumCategoryModel = new \admin\videoAlbumCategory\VideoAlbumCategoryModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

/**
 * JSON -  获取指定视频专辑类别ID下的一个视频专辑
 * http://www.atyorku.ca/admin/videoAlbumCategory/videoAlbumCategoryController.php?action=getListOfVideoAlbumCategoriesWithJson
 */
function getListOfVideoAlbumCategoriesWithJson() {
    global $videoAlbumCategoryModel;
    $result = $videoAlbumCategoryModel->getListOfVideoAlbumCategories();
    if ($result) {
        BasicTool::echoJson(1, "成功", $result);
    } else {
        BasicTool::echoJson(0, "获取视频专辑类别列表失败");
    }
}

function modifyVideoAlbumCategory($echoType='normal') {
    global $videoAlbumCategoryModel;
    global $currentUser;
    try{
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $title = BasicTool::post("title", "分类名称不能为空");
        if(BasicTool::post('flag') == 'update'){
            $arr['id'] = BasicTool::post('id',"分类ID不能为空");
            !$videoAlbumCategoryModel->isExistOfVideoAlbumCategoryTitle($title, $arr['id']) or BasicTool::throwException("此分类名称已经存在");
            $videoAlbumCategoryModel->updateVideoAlbumCategoryTitle($arr['id'], $title) or BasicTool::throwException($videoAlbumCategoryModel->errorMsg);

            if ($echoType == "normal") {
                BasicTool::echoMessage("修改成功","/admin/videoAlbumCategory/");
            } else {
                BasicTool::echoJson(1, "修改成功");
            }
        } else {
            !$videoAlbumCategoryModel->isExistOfVideoAlbumCategoryTitle($title) or BasicTool::throwException("此分类名称已经存在");
            $videoAlbumCategoryModel->addVideoAlbumCategory($title);
            if ($echoType == "normal") {
                BasicTool::echoMessage("添加成功","/admin/videoAlbumCategory/");
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

//function getVideoAlbumCategoryById($id) {
//    global $videoAlbumCategoryModel;
//    global $currentUser;
//    try {
//        BasicTool::echoJson(1,"获取视频专辑类别成功",$videoAlbumCategoryModel->getVideoAlbumCategory($id));
//    }
//    catch(Exception $e){
//        BasicTool::echoMessage($e->getMessage(),-1);
//    }
//}

/**
* 删除1个视频专辑分类
* @param $id 视频专辑分类ID
* @return bool
*/
function deleteVideoAlbumCategoryById($id) {
    global $videoAlbumCategoryModel;
    return $videoAlbumCategoryModel->deleteVideoAlbumCategory($id);
}

/**
* 删除1个或多个视频专辑分类
* @param $id 要删除的视频专辑分类id array
*/
function deleteVideoAlbumCategory() {
    global $currentUser;
    try {
        $idArray = BasicTool::post("id", "请指定要删除的视频专辑分类Id");
        $currentUser->isUserHasAuthority('GOD') or BasicTool::throwException("权限不足");
        foreach($idArray as $id) {
            deleteVideoAlbumCategoryById($id) or BasicTool::echoMessage("视频专辑分类ID ({$id}) 删除失败");
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
    BasicTool::echoMessage("删除成功");
}


?>
