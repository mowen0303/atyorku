<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$bookCategoryModel = new \admin\bookCategory\BookCategoryModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


function modifyBookCategory() {
    global $bookCategoryModel;
    global $currentUser;
    try{
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        $name = BasicTool::post("name", "分类名称不能为空");
        if(BasicTool::post('flag') == 'update'){
            $arr['id'] = BasicTool::post('f_id',"分类ID不能为空");
            !$bookCategoryModel->isExistOfBookCategoryName($name,$arr['id']) or BasicTool::throwException("此分类名称已经存在");
            $bookCategoryModel->updateBookCategoryName($arr['id'],$name) or BasicTool::throwException($bookCategoryModel->errorMsg);
            BasicTool::echoMessage("修改成功","/admin/bookCategory/");
        } else {
            !$bookCategoryModel->isExistOfBookCategoryName($name) or BasicTool::throwException("此分类名称已经存在");
            $bookCategoryModel->addBookCategory($name);
            BasicTool::echoMessage("添加成功","/admin/bookCategory/");
        }
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function getBookCategoryById($id) {
    global $bookCategoryModel;
    global $currentUser;
    try {
        BasicTool::echoJson(1,"获取二手书类别成功",$bookCategoryModel->getBookCategory($id));
    }
    catch(Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

/**
* 删除1个二手书分类
* @param $id 二手书分类ID
* @return bool
*/
function deleteBookCategoryById($id) {
    global $bookCategoryModel;
    global $currentUser;
    $currentUser->isUserHasAuthority('ADMIN') or BasicTool::throwException("权限不足");
    return $bookCategoryModel->deleteBookCategory($id);
}

/**
* 删除1个或多个二手书分类
* @param $id 要删除的二手书分类id array
*/
function deleteBookCategory() {
    $idArray = BasicTool::post("id", "请指定要删除的二手书分类Id");
    try {
        foreach($idArray as $id) {
            deleteBookCategoryById($id) or BasicTool::echoMessage("二手书分类ID ({$id}) 删除失败");
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
    BasicTool::echoMessage("删除成功");
}


?>
