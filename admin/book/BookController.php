<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$bookModel = new admin\book\BookModel();
$bookCategoryModel = new admin\bookCategory\BookCategoryModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


// 添加或修改一本二手书
function modifyBook() {
    global $bookModel;
    global $bookCategoryModel;
    global $currentUser;
    try{
        $isUpdate = (BasicTool::post('flag') == 'update');
        // 验证权限
        if ($isUpdate) {
            $arr['id'] = BasicTool::post('f_id',"二手书ID不能为空");
            if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                $currentUser->userId == $bookModel->getUserIdFromBookId($arr['id']) or BasicTool::throwException("无权修改其他人的二手书");
            }
        } else {
            $currentUser->isUserHasAuthority('BOOK') or BasicTool::throwException("权限不足");
        }

        // 验证 Fields
        $name = BasicTool::post("name", "二手书标题不能为空", 100);
        $price = (float)BasicTool::post("price", "二手书价格不能为空", 9999999999);
        $description = BasicTool::post("description") or "";
        $bookCategoryId = BasicTool::post("book_category_id", "二手书所属分类不能为空");
        // validate and format price
        !($price < 0) or BasicTool::throwException("价钱必须大于或等于0");
        $price = number_format($price, 2, '.', '');
        // check user_id
        $userId = $currentUser->userId or BasicTool::throwException("无法找到用户ID, 请重新登陆");
        $bookCategoryModel->getBookCategory($bookCategoryId) or BasicTool::throwException("此二手书所属分类不存在");

        // 执行
        if ($isUpdate) {
            $bookModel->updateBook($arr['id'], $name, $price, $description, $bookCategoryId, $userId) or BasicTool::throwException($bookModel->errorMsg);
            BasicTool::echoMessage("修改成功","/admin/book/");
        } else {
            $bookModel->addBook($name, $price, $description, $bookCategoryId, $userId) or BasicTool::throwException($bookModel->errorMsg);
            BasicTool::echoMessage("添加成功","/admin/book/");
        }
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function getBook(){
    global $bookModel;
    $id = BasicTool::get("id","请指定二手书Id");
    BasicTool::echoJson(1,"获取二手书成功",$bookModel->getBook($id));
}

function getBooksByCategory(){
    global $bookModel;
    global $currentUser;
    try{
        $bookCategoryId = BasicTool::get("book_category_id","请指定二手书分类Id");
        $result = $bookModel->getBooksByCategory($bookCategoryId);
        BasicTool::echoJson(1,"查询成功",$result);
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

/**
* 删除1个二手书
* @param $id 二手书ID
* @return bool
*/
function deleteBookById($id) {
    global $bookModel;
    global $currentUser;
    if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
        $currentUser->userId == $bookModel->getUserIdFromBookId($id) or BasicTool::throwException("无权删除其他人的二手书");
    }
    return $bookModel->deleteBook($id);
}

/**
* 删除1个或多本二手书
* @param $id 要删除的二手书id array
*/
function deleteBook() {
    $idArray = BasicTool::post("id", "请指定要删除的二手书Id");
    try {
        foreach($idArray as $id) {
            deleteBookById($id) or BasicTool::echoMessage("二手书ID ({$id}) 删除失败");
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
    BasicTool::echoMessage("删除成功");
}


function uploadImgWithJson(){

    global $bookModel;
    try {
        $uploadDir =  $bookModel->uploadImg("imgFile","ad/images") or BasicTool::throwException($bookModel->errorMsg);
        BasicTool::echoJson(1, "上传成功", $uploadDir);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

?>
