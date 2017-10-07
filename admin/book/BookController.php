<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$bookModel = new admin\book\BookModel();
$bookCategoryModel = new admin\bookCategory\BookCategoryModel();
$imageModel = new \admin\image\ImageModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

//============ Function with JSON ===============//

/**
 * JSON -  获取指定Id的二手书信息
 * @param book_id 二手书ID
 * http://www.atyorku.ca/admin/book/bookController.php?action=getBookByIdWithJson&book_id=1
 */
function getBookByIdWithJson() {
    global $bookModel;
    $id = BasicTool::get("book_id","请指定二手书Id");
    $id = validateId($id);
    $result = $bookModel->getBookById($id);
    if ($result) {
        BasicTool::echoJson(1, "成功", $result);
    } else {
        BasicTool::echoJson(0, "未找到该ID对应的二手书");
    }
}

/**
 * JSON -  获取指定二手书类别ID下的一页二手书
 * @param pageSize 每一页二手书获取量，默认值=40
 * @param book_category_id 二手书类别ID
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfBooksByCategoryIdWithJson&book_category_id=3&pageSize=20
 */
function getListOfBooksByCategoryIdWithJson() {
    $id = BasicTool::get("book_category_id","请指定二手书类别Id");
    $id = validateId($id);
    getListOfBooksWithJson("book_category_id", $id);
}

/**
 * JSON -  获取指定用户ID下的一页二手书
 * @param pageSize 每一页二手书获取量，默认值=40
 * @param user_id 用户ID
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfBooksByUserIdWithJson&user_id=1123&pageSize=20
 */
function getListOfBooksByUserIdWithJson() {
    $id = BasicTool::get("user_id","请指定用户ID");
    $id = validateId($id);
    getListOfBooksWithJson("user_id", $id);
}

/**
 * JSON -  获取指定用户名下的一页二手书
 * @param pageSize 每一页二手书获取量，默认值=40
 * @param username 用户名
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfBooksByUsernameWithJson&username=abc@gmail.com&pageSize=20
 */
function getListOfBooksByUsernameWithJson() {
    $username = BasicTool::get("username","请指定二手书类别Id");
    getListOfBooksWithJson("username", $username);
}

/**
 * JSON -  获取指定关键词相关的一页二手书
 * @param pageSize 每一页二手书获取量，默认值=40
 * @param keywords 搜索关键词
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfBooksByKeywordsWithJson&keywords=计算机科学&pageSize=20
 */
function getListOfBooksByKeywordsWithJson() {
    $keywords = BasicTool::get("keywords","请指定搜索关键词");
    getListOfBooksWithJson("keywords", $keywords);
}


/**
 * JSON -  获取某一页二手书
 * @param pageSize 每一页二手书获取量，默认值=40
 * @param String $queryType 搜索类别
 * @param $queryValue 搜索关键词
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfBooksWithJson&pageSize=20
 */
function getListOfBooksWithJson($queryType=NULL, $queryValue=NULL) {
    global $bookModel;
    $pageSize = BasicTool::get('pageSize');
    if(!$pageSize){
        $pageSize = 40;
    }

    $result = NULL;

    if ($queryType) {
        // 根据指定搜索类别来获取二手书
        $queryValue or BasicTool::throwException("请指定搜索类别相对应搜索值");
        switch($queryType) {
            case "user_id":
                $result = $bookModel->getBooksByUserId($queryValue, $pageSize);
                break;
            case "book_category_id":
                $result = $bookModel->getBooksByCategoryId($queryValue, $pageSize);
                break;
            case "username":
                $result = $bookModel->getBooksByUsername($queryValue, $pageSize);
                break;
            case "keywords":
                $result = $bookModel->getBooksByKeywords($queryValue, $pageSize);
                break;
            default:
                BasicTool::throwException("无法识别搜索类别");
        }
    } else {
        // 直接获取
        $result = $bookModel->getListOfBooks($pageSize);
    }

    if ($result) {
        BasicTool::echoJson(1, "成功", $result);
    } else {
        BasicTool::echoJson(0, "没有更多内容");
    }
}


/**
* JSON - 添加或修改一本二手书
* @param flag 添加or修改 [add/update]
* @param name 二手书名
* @param price 二手书价钱, 不能为负数
* @param book_category_id 二手书所属分类ID
* @param description 二手书细节描述
* @param id 二手书ID，修改二手书时必填
* [POST] http://www.atyorku.ca/admin/book/bookController.php?action=addBookWithJson
*/
function addBookWithJson() {
    modifyBook("json");
}


/**
* JSON - 添加或修改一本二手书
* @param id 删除二手书的id
* [POST] http://www.atyorku.ca/admin/book/bookController.php?action=deleteBookWithJson
*/
function deleteBookWithJson() {
    deleteBook("json");
}

/**
 * http://www.atyorku.ca/admin/book/bookController.php?action=uploadImgWithJson
 * 上传图片,成功返回图片路径
 * @return bool|path
 */
function uploadImgWithJson() {
    global $bookModel;
    global $imageModel;
    global $currentUser;
    try {
        $uploadArr = $imageModel->uploadImg("imgFile", $currentUser->userId) or BasicTool::throwException($imageModel->errorMsg);
        BasicTool::echoJson(1, "上传成功", $uploadArr);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

//=========== END Function with JSON ============//

/**
* Upload image to /uploads/book/images/ when user select an image. The file <input>'s name attribute must be "imgFile"
* A thumbnail image will also be created and store at /uploads/book/images/thumbnail/
* ====== Following params are added to $_POST
* @param image_url  re-rendered image url
* @param thumbnail_url  thumbnail url
* @param size   re-rendered image size
* @param width  re-rendered image width
* @param height re-rendered image height
*/
function uploadImages() {
    global $imageModel;
    global $currentUser;
    if ($_FILES["imgFile"]["size"] != 0) {
        $uploadArr = $imageModel->uploadImg("imgFile", $currentUser->userId) or BasicTool::throwException($imageModel->errorMsg);
        $_POST["image_url"] = $uploadArr["image_url"];
        $_POST["thumbnail_url"] = $uploadArr["thumbnail_url"];
        $_POST["size"] = $uploadArr["size"];
        $_POST["width"] = $uploadArr["width"];
        $_POST["height"] = $uploadArr["height"];
        return true;
    }
    return false;
}


// 添加或修改一本二手书
function modifyBook($echoType = "normal") {
    global $bookModel;
    global $imageModel;
    global $bookCategoryModel;
    global $currentUser;
    try{
        // upload new images to server if any
        $newImageId = null;
        if (uploadImages()) {
            addNewImage() or BasicTool::throwException("服务器添加图片失败");
            $newImageId = $imageModel->idOfInsert;
        }

        $flag = BasicTool::post('flag');
        $bookUserId = false;    // 二手书卖家ID
        $currentBook = null;
        // 验证权限
        if ($flag=='update') {
            $arr['id'] = BasicTool::post('f_id',"二手书ID不能为空");
            $currentBook = $bookModel->getBookById($arr['id']) or BasicTool::throwException("无法找到二手书");
            $bookUserId = $currentBook['user_id'];
            if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                $currentUser->userId == $bookUserId or BasicTool::throwException("无权修改其他人的二手书");
            }
        } else if ($flag=='add') {
            $currentUser->isUserHasAuthority('BOOK') or BasicTool::throwException("权限不足");
        }

        // 验证 Fields
        $name = BasicTool::post("name", "二手书标题不能为空", 100);
        $description = BasicTool::post("description") or "";
        $bookCategoryId = BasicTool::post("book_category_id", "二手书所属分类不能为空");
        // validate and format price
        $price = (float)BasicTool::post("price", "二手书价格不能为空", 9999999999);
        ($price >= 0) or BasicTool::throwException("价格必须大于或等于0");
        $price = number_format($price, 2, '.', '');

        $bookCategoryModel->getBookCategory($bookCategoryId) or BasicTool::throwException("此二手书所属分类不存在");
        $img1 = BasicTool::post("image_id_one");
        $img2 = BasicTool::post("image_id_two");
        $img3 = BasicTool::post("image_id_three");

        if ($newImageId) {
            if ($img1 == null) {
                $img1 = $newImageId;
            } else if ($img2 == null) {
                $img2 = $newImageId;
            } else if ($img3 == null) {
                $img3 = $newImageId;
            } else {
                BasicTool::throwException("二手书图片已满，先删除已有图片");
            }
        }

        // 执行
        if ($flag=='update') {
            $userId = $bookUserId or BasicTool::throwException("无法找到卖家ID, 请重新登陆");
            // 删除旧图片
            if ($currentBook['image_id_one'] != null and $currentBook['image_id_one'] != $img1) {
                $imageModel->deleteImageById($currentBook['image_id_one']);
            }
            if ($currentBook['image_id_two'] != null and $currentBook['image_id_two'] != $img2) {
                $imageModel->deleteImageById($currentBook['image_id_two']);
            }
            if ($currentBook['image_id_three'] != null and $currentBook['image_id_three'] != $img3) {
                $imageModel->deleteImageById($currentBook['image_id_three']);
            }
            $bookModel->updateBook($arr['id'], $name, $price, $description, $bookCategoryId, $userId, $img1, $img2, $img3) or BasicTool::throwException($bookModel->errorMsg);
            if ($echoType == "normal") {
                BasicTool::echoMessage("修改成功","/admin/book/index.php?listBook");
            } else {
                BasicTool::echoJson(1, "修改成功");
            }
        } else if ($flag=='add') {
            $userId = $currentUser->userId or BasicTool::throwException("无法找到用户ID, 请重新登陆");
            $bookModel->addBook($name, $price, $description, $bookCategoryId, $userId, $img1, $img2, $img3) or BasicTool::throwException($bookModel->errorMsg);
            if ($echoType == "normal") {
                BasicTool::echoMessage("添加成功","/admin/book/index.php?listBook");
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
* Add a new image to database
* @return boolean true 没有新图片或成功添加Image false 添加失败
*/
function addNewImage() {
    global $bookModel;
    global $currentUser;
    global $imageModel;
    $imageUrl = BasicTool::post("image_url");
    $thumbnailUrl = BasicTool::post("thumbnail_url");
    $size = BasicTool::post("size");
    $width = BasicTool::post("width");
    $height = BasicTool::post("height");
    // BasicTool::echoMessage("$imageUrl");
    if ($imageUrl and $thumbnailUrl) {
        return $imageModel->addImage($imageUrl, $thumbnailUrl, $currentUser->userId, "book", $size, $width, $height);
    }
    return false;
}


/**
* 删除1个或多本二手书
* @param $id 要删除的二手书id或id array
*/
function deleteBook($echoType = "normal") {
    global $bookModel;
    global $currentUser;
    global $imageModel;
    try {
        $id = BasicTool::post('id');
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $i++;
                if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                    $currentUser->userId == $bookModel->getUserIdFromBookId($id) or BasicTool::throwException("无权删除其他人的二手书");
                }
                $data = $bookModel->deleteBookById($v) or BasicTool::throwException("删除多本失败");
                if ($data["image_id_one"]) {
                    $imageModel->deleteImageById('book', $data["image_id_one"]);
                }
                if ($data["image_id_two"]) {
                    $imageModel->deleteImageById('book', $data["image_id_two"]);
                }
                if ($data["image_id_three"]) {
                    $imageModel->deleteImageById('book', $data["image_id_three"]);
                }
            }
        } else {
            $i++;
            if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                $currentUser->userId == $bookModel->getUserIdFromBookId($id) or BasicTool::throwException("无权删除其他人的二手书");
            }
            $bookModel->deleteBookById($id) or BasicTool::throwException("删除1本失败");
            $data = $bookModel->deleteBookById($v) or BasicTool::throwException("删除多本失败");
            if ($data["image_id_one"]) {
                $imageModel->deleteImageById('book', $data["image_id_one"]);
            }
            if ($data["image_id_two"]) {
                $imageModel->deleteImageById('book', $data["image_id_two"]);
            }
            if ($data["image_id_three"]) {
                $imageModel->deleteImageById('book', $data["image_id_three"]);
            }
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}本二手书", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}本二手书");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}




function searchBooks($bookModel, $pageSize=40) {
    $queryType = BasicTool::post("search_type", "搜索类别不能为空");
    $queryValue = BasicTool::post("search_value", "搜索内容不能为空");
    try {
        $result = "";
        switch($queryType) {
            case "keywords":
                $result = $bookModel->getBooksByKeywords($queryValue, $pageSize);
                break;
            case "user_id":
                $result = $bookModel->getBooksByUserId($queryValue, $pageSize);
                break;
            case "username":
                $result = $bookModel->getBooksByUsername($queryValue, $pageSize);
                break;
            case "book_category_id":
                $result = $bookModel->getBooksByCategoryId($queryValue, $pageSize);
                break;
            default:
                BasicTool::echoMessage("搜索类别无法识别");
                break;
        }
        return $result;
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }

}

/**
* 验证ID
* ID必须为数字且大于0，Float数字会自动转换为 integer
* @return integer ID
*/
function validateId($id) {
    is_numeric($id) or BasicTool::echoJson(0, "ID must be integer");
    $id = (int)$id;
    ($id>0) or BasicTool::echoJson(0, "ID must be non-negative integer");
    return $id;
}

?>
