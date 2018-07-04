<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$lectureAlbumCategoryModel = new \admin\lectureAlbumCategory\LectureAlbumCategoryModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

/**
 * JSON -  获取指定课程专辑类别ID下的一个课程专辑
 * http://www.atyorku.ca/admin/lectureAlbumCategory/lectureAlbumCategoryController.php?action=getListOfLectureAlbumCategoriesWithJson
 */
function getListOfLectureAlbumCategoriesWithJson() {
    global $lectureAlbumCategoryModel;
    $result = $lectureAlbumCategoryModel->getListOfLectureAlbumCategories();
    if ($result) {
        BasicTool::echoJson(1, "成功", $result);
    } else {
        BasicTool::echoJson(0, "获取课程专辑类别列表失败");
    }
}

function modifyLectureAlbumCategory() {
    global $lectureAlbumCategoryModel;
    global $currentUser;
    try{
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $title = BasicTool::post("title", "分类名称不能为空");
        if(BasicTool::post('flag') == 'update'){
            $arr['id'] = BasicTool::post('id',"分类ID不能为空");
            !$lectureAlbumCategoryModel->isExistOfLectureAlbumCategoryTitle($title, $arr['id']) or BasicTool::throwException("此分类名称已经存在");
            $lectureAlbumCategoryModel->updateLectureAlbumCategoryTitle($arr['id'], $title) or BasicTool::throwException($lectureAlbumCategoryModel->errorMsg);
            BasicTool::echoMessage("修改成功","/admin/lectureAlbumCategory/");
        } else {
            !$lectureAlbumCategoryModel->isExistOfLectureAlbumCategoryTitle($title) or BasicTool::throwException("此分类名称已经存在");
            $lectureAlbumCategoryModel->addLectureAlbumCategory($title);
            BasicTool::echoMessage("添加成功","/admin/lectureAlbumCategory/");
        }
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

//function getLectureAlbumCategoryById($id) {
//    global $lectureAlbumCategoryModel;
//    global $currentUser;
//    try {
//        BasicTool::echoJson(1,"获取课程专辑类别成功",$lectureAlbumCategoryModel->getLectureAlbumCategory($id));
//    }
//    catch(Exception $e){
//        BasicTool::echoMessage($e->getMessage(),-1);
//    }
//}

/**
* 删除1个课程专辑分类
* @param $id 课程专辑分类ID
* @return bool
*/
function deleteLectureAlbumCategoryById($id) {
    global $lectureAlbumCategoryModel;
    return $lectureAlbumCategoryModel->deleteLectureAlbumCategory($id);
}

/**
* 删除1个或多个课程专辑分类
* @param $id 要删除的课程专辑分类id array
*/
function deleteLectureAlbumCategory() {
    global $currentUser;
    try {
        $idArray = BasicTool::post("id", "请指定要删除的课程专辑分类Id");
        $currentUser->isUserHasAuthority('GOD') or BasicTool::throwException("权限不足");
        foreach($idArray as $id) {
            deleteLectureAlbumCategoryById($id) or BasicTool::echoMessage("课程专辑分类ID ({$id}) 删除失败");
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
    BasicTool::echoMessage("删除成功");
}


?>
