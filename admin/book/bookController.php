<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$bookModel = new admin\book\BookModel();
$bookCategoryModel = new admin\bookCategory\BookCategoryModel();
$imageModel = new \admin\image\ImageModel();
$currentUser = new \admin\user\UserModel();
$courseCodeModel = new \admin\courseCode\CourseCodeModel();
$professorModel = new \admin\professor\ProfessorModel();
$transactionModel = new \admin\transaction\TransactionModel();
$msgModel = new \admin\msg\MsgModel();

call_user_func(BasicTool::get('action'));

//============ Function with JSON ===============//

/**
 * JSON -  获取指定Id的二手书信息
 * @param book_id 二手书ID
 * http://www.atyorku.ca/admin/book/bookController.php?action=getBookByIdWithJson&book_id=1
 */
function getBookByIdWithJson() {
    global $bookModel;
    try {
        $id = BasicTool::get("book_id","请指定二手书Id");
        if (validateId($id)) {
            $result = $bookModel->getBookById((int)$id);
            if ($result) {
                BasicTool::echoJson(1, "成功", $result);
            } else {
                BasicTool::echoJson(0, "未找到该ID对应的二手书");
            }
        } else {
            BasicTool::echoJson("二手书ID无效");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}

/**
 * JSON -  获取指定二手书类别ID下的一页二手书
 * @param pageSize 每一页二手书获取量，默认值=40
 * @param book_category_id 二手书类别ID
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfBooksByCategoryIdWithJson&book_category_id=3&pageSize=20
 */
function getListOfBooksByCategoryIdWithJson() {
    try {
        $id = BasicTool::get("book_category_id","请指定二手书类别Id");
        if (validateId($id)) {
            getListOfBooksWithJson("book_category_id", (int)$id);
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}

/**
 * JSON -  获取指定用户ID下的一页二手书
 * @param pageSize 每一页二手书获取量，默认值=40
 * @param user_id 用户ID
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfBooksByUserIdWithJson&user_id=1123&pageSize=20
 */
function getListOfBooksByUserIdWithJson() {
    try {
        $id = BasicTool::get("user_id","请指定用户ID");
        if (validateId($id)) {
            getListOfBooksWithJson("user_id", (int)$id);
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}

/**
 * JSON -  获取指定用户名下的一页二手书
 * @param pageSize 每一页二手书获取量，默认值=40
 * @param username 用户名
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfBooksByUsernameWithJson&username=abc@gmail.com&pageSize=20
 */
function getListOfBooksByUsernameWithJson() {
    try {
        $username = BasicTool::get("username","请指定用户名");
        getListOfBooksWithJson("username", $username);
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}

/**
 * JSON -  获取指定关键词相关的一页二手书
 * @param pageSize 每一页二手书获取量，默认值=40
 * @param keywords 搜索关键词
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfBooksByKeywordsWithJson&keywords=计算机科学&pageSize=20
 */
function getListOfBooksByKeywordsWithJson() {
    try {
        $keywords = BasicTool::get("keywords","请指定搜索关键词");
        getListOfBooksWithJson("keywords", $keywords);
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
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
    try {
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
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }

}


/**
 * JSON -  获取某一页二手书
 * @param pageSize 每一页二手书获取量，默认值=40
 * @param q $query string 搜索条件
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfBooksWithJsonV2&pageSize=20
 */
function getListOfBooksWithJsonV2() {
    global $bookModel;
    try {
        $pageSize = BasicTool::get('pageSize');
        if(!$pageSize){
            $pageSize = 40;
        }
        $result = false;

        $categoryId = BasicTool::get('book_category_id');
        $courseParentId = BasicTool::get('course_code_parent_id');
        $courseChildId = BasicTool::get('course_code_child_id');
        $year = BasicTool::get('year');
        $term = BasicTool::get('term');
        $isEDocument = BasicTool::get('is_e_document');
        $payWithPoints = BasicTool::get('pay_with_points');
        $profName = BasicTool::get('prof_name');

        if(!$categoryId && !$courseParentId && !$isEDocument && !$payWithPoints && !$profName && !$year){
            $result = $bookModel->getListOfBooks($pageSize);
        } else {
            $qArr = array_filter(array(
                'c2.id'=>intval($courseParentId),
                'c1.id'=>intval($courseChildId),
                'b.term_year'=>intval($year),
                'b.term_semester'=>$term,
                "CONCAT(p.firstname, ' ', p.lastname)"=>$profName)
            );

            if($term){
                $qArr["b.term_semester"] = "'{$term}'";
            }

            if($profName){
                $qArr["CONCAT(p.firstname, ' ', p.lastname)"] = "'{$profName}'";
            }

            if($payWithPoints==="on"){
                $qArr["pay_with_points"] = "1";
            } else if ($payWithPoints==="off"){
                $qArr["pay_with_points"] = "0";
            }

            if($isEDocument==="on"){
                $qArr["is_e_document"] = "1";
            } else if($isEDocument==="off"){
                $qArr["is_e_document"] = "0";
            }

            $q = implode(' AND ', array_map(
                function ($v, $k) { return sprintf("%s=%s", $k, $v); },
                $qArr,
                array_keys($qArr)
            ));
            if($categoryId){
                if($q){
                    $q .= " AND ";
                }
                $q .= "b.book_category_id in ($categoryId)";
            }

            $result = $bookModel->getListOfBooks($pageSize,$q);
        }

        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "没有更多内容");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
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
* JSON - 添加或修改一本二手书
* @param id 删除二手书的id
* [POST] http://www.atyorku.ca/admin/book/bookController.php?action=deleteBookLogicallyWithJson
*/
function deleteBookLogicallyWithJson() {
    deleteBookLogically("json");
}

/**
 * http://www.atyorku.ca/admin/book/bookController.php?action=uploadImgWithJson
 * 上传图片,成功返回图片路径
 * $_FILES的inputname 为 imgFile
 * @return JSON 新图片ID一维数组
 */
function uploadImgWithJson() {
    global $bookModel;
    global $imageModel;
    global $currentUser;
    try {
        $uploadArr = $imageModel->uploadImg("imgFile", $currentUser->userId, "book") or BasicTool::throwException($imageModel->errorMsg);
        BasicTool::echoJson(1, "上传成功", $uploadArr);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * http://www.atyorku.ca/admin/book/bookController.php?action=getImagesByBookIdWithJson&id=3
 * 获取该二手书ID的所有关联图片
 * 同时二手书浏览数 + 1
 * @return JSON 二维数组
 */
function getImagesByBookIdWithJson() {
    global $bookModel;
    try {
        $id = BasicTool::get('id','请提供二手书ID');
        $result = $bookModel->getImagesByBookId($id);
        if ($result) {
            BasicTool::echoJson(1, "获取图片成功", $result);
        } else {
            BasicTool::echoJson(0, '未找到图片');
        }
        $bookModel->incrementCountViewByBookId($id);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
* http://www.atyorku.ca/admin/book/bookController.php?action=purchaseBookWithJson&book_id=3
* 购买一本二手书
* @param bookId 二手书ID
* @return JSON
*/
function purchaseBookWithJson() {
    global $bookModel;
    global $currentUser;
    global $transactionModel;
    global $msgModel;
    try {
        $bookId = intval(BasicTool::post('book_id','请指定资料ID'));
        $buyerId = $currentUser->userId;
        $buyerId or BasicTool::throwException("请先登录");
        $result = $bookModel->getBookById($bookId);

        if ($result) {
            (intval($result["is_available"]) and !intval($result["is_deleted"])) or BasicTool::throwException("资料已下架");
            $name = $result['name'];
            intval($result["pay_with_points"]) or BasicTool::throwException("不支持积分支付");
            $sellerId = intval($result["user_id"]);
            $sellerId !== intval($buyerId) or BasicTool::throwException("无法购买自己的产品");
            $price = floatval($result["price"]);
            $transactionModel->isCreditDeductible($buyerId,$price) || BasicTool::throwException($transactionModel->errorMsg);
            $buyerDescription = "购买资料: " . $result["name"] . " ID: " . $result["id"];
            $sellerDescription = "售出资料: " . $result["name"] . " ID: " . $result["id"];
            $elink = $bookModel->getELinkById($bookId);
            if(!$elink){
                $bookModel->unLaunchBook($bookId);
                $msgModel->pushMsgToUser($sellerId,"notice",0,"下架通知: 你的资料[{$name}]因[无效的网盘链接]被系统自动下架.",28);
                BasicTool::throwException("购买失败: 资料链接不存在, 此资料将被自动下架.");
            }
            $result = $transactionModel->buy($buyerId,$sellerId,$price,$buyerDescription,$sellerDescription,'book',$bookId) or BasicTool::throwException($transactionModel->errorMsg);
            $msgModel->pushMsgToUser($buyerId, 'book', $bookId, $name.": ".$elink, $sellerId);
            $msgModel->pushMsgToUser($sellerId, 'book', $bookId, "我花了[{$price}]点积分,购买了你的资料[{$name}]",$buyerId);
            BasicTool::echoJson(1, "购买成功", $result);
        } else {
            BasicTool::throwException("资料不存在");
        }
    } catch(Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * 恢复一本已删除的二手书 [ADMIN]操作
 */
function restoreDeletedBookByIdWithJson() {
    global $bookModel;
    global $currentUser;
    try {
        if (!($currentUser->isUserHasAuthority('ADMIN'))) {
            BasicTool::throwException("无权限");
        }
        $bookId = BasicTool::post('book_id','二手书ID不能为空');
        $bookModel->restoreBookById($bookId);
        BasicTool::echoJson(1, "恢复成功", true);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * 下架一本二手书
 * @param id 二手书ID
 */
function unLaunchBookByIdWithJson() {
    unLaunchBookById("json");
}

//=========== END Function with JSON ============//


// 添加或修改一本二手书
function modifyBook($echoType = "normal") {
    global $bookModel;
    global $imageModel;
    global $bookCategoryModel;
    global $currentUser;
    global $courseCodeModel;
    global $professorModel;

    try{
        $flag = BasicTool::post('flag');
        $bookUserId = false;    // 二手书卖家ID
        $currentBook = null;
        // 验证权限
        if ($flag=='update') {
            $arr['id'] = BasicTool::post('id',"二手书ID不能为空");
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
        $available = BasicTool::post("is_available") ?: 1;
        $payWithPoints = BasicTool::post("pay_with_points") ?: 0;
        $isEDocument = BasicTool::post("is_e_document") ?: 0;
        $eLink = "";
        if($isEDocument){
            $eLink = BasicTool::post("e_link", "电子书链接不能为空 ".$isEDocument);
        }
        $description = BasicTool::post("description",false,255) or "";
        $bookCategoryId = BasicTool::post("book_category_id", "二手书所属分类不能为空");
        $parentCode = BasicTool::post("course_code_parent_title", "父类课评不能为空");
        $childCode = BasicTool::post("course_code_child_title", "子类课评不能为空");
        $courseCodeId = 0;
        if ($parentCode && $childCode) {
            $courseCodeId = $courseCodeModel->getCourseIdByCourseCode($parentCode, $childCode);
            $courseCodeId or BasicTool::throwException("未找到指定科目Id");
        }

        $profName = BasicTool::post("prof_name");
        $profId = 0;
        if ($profName) {
            $profId = $professorModel->getProfessorIdByFullName($profName);
            $profId or BasicTool::throwException("教授名称格式错误");
        }

        $year = BasicTool::post("term_year") ?: 0;
        $term = BasicTool::post("term_semester") ?: "";

        // validate and format price
        $price = (float)BasicTool::post("price", "二手书价格不能为空", 99999999.99);
        if($payWithPoints){
            $price>=50 or BasicTool::throwException("积分销售不能低于50积分");
        }
        $price = number_format($price, 2, '.', '');

        $bookCategoryModel->getBookCategory($bookCategoryId) or BasicTool::throwException("此二手书所属分类不存在");

        $imgArr = array(BasicTool::post("image_id_one"),BasicTool::post("image_id_two"),BasicTool::post("image_id_three"));
        $currImgArr = ($currentBook!=null) ? array($currentBook['image_id_one'],$currentBook['image_id_two'],$currentBook['image_id_three']) : false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"book");

        // 执行
        if ($flag=='update') {
            $userId = $bookUserId or BasicTool::throwException("无法找到卖家ID, 请重新登陆");
            $bookModel->updateBook($arr['id'], $name, $price, $description, $bookCategoryId, $courseCodeId, $userId, $imgArr[0], $imgArr[1], $imgArr[2], $profId, $year, $term, $payWithPoints, $available, $isEDocument, $eLink) or BasicTool::throwException($bookModel->errorMsg);
            if ($echoType == "normal") {
                BasicTool::echoMessage("修改成功","/admin/book/index.php?listBook");
            } else {
                BasicTool::echoJson(1, "修改成功");
            }
        } else if ($flag=='add') {
            $userId = $currentUser->userId or BasicTool::throwException("无法找到用户ID, 请重新登陆");
            $bookModel->addBook($name, $price, $description, $bookCategoryId, $courseCodeId, $userId, $imgArr[0], $imgArr[1], $imgArr[2], $profId, $year, $term, $payWithPoints, $available, $isEDocument, $eLink) or BasicTool::throwException($bookModel->errorMsg);
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
* 删除1个或多本二手书
* @param id 要删除的二手书id或id array
*/
function deleteBook($echoType = "normal") {
    global $bookModel;
    global $currentUser;
    global $imageModel;
    try {
        $id = BasicTool::post('id') or BasicTool::throwException("请指定被删除二手书ID");
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $i++;
                if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                    $currentUser->userId == $bookModel->getUserIdFromBookId($v) or BasicTool::throwException("无权删除其他人的二手书");
                }
                deleteBookImagesByBookId($v);
                $bookModel->deleteBookById($v) or BasicTool::throwException("删除多本失败");
            }
        } else {
            $i++;
            if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                $currentUser->userId == $bookModel->getUserIdFromBookId($id) or BasicTool::throwException("无权删除其他人的二手书");
            }
            deleteBookImagesByBookId($id);
            $bookModel->deleteBookById($id) or BasicTool::throwException("删除1本失败");
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


/**
* 逻辑删除1个或多本二手书
* @param id 要删除的二手书id或id array
*/
function deleteBookLogically($echoType="normal") {
    global $bookModel;
    global $currentUser;
    try {
        $id = BasicTool::post('id') or BasicTool::throwException("请指定被删除二手书ID");
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $i++;
                if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                    $currentUser->userId == $bookModel->getUserIdFromBookId($v) or BasicTool::throwException("无权删除其他人的二手书");
                }
                $bookModel->deleteBookLogicallyById($v) or BasicTool::throwException("删除多本失败");
            }
        } else {
            $i++;
            if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                $currentUser->userId == $bookModel->getUserIdFromBookId($id) or BasicTool::throwException("无权删除其他人的二手书");
            }
            $bookModel->deleteBookLogicallyById($id) or BasicTool::throwException("删除1本失败");
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
    try {
        $queryType = BasicTool::post("search_type", "搜索类别不能为空");
        $queryValue = BasicTool::post("search_value", "搜索内容不能为空");
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
* ID必须为数字且大于0
* @return boolean
*/
function validateId($id) {
    return (is_numeric($id) and ((int)$id > 0));
}

/**
* 删除指定二手书ID相关联的图片
* @param id 二手书ID
*/
function deleteBookImagesByBookId($id) {
    global $bookModel;
    global $imageModel;
    try {
        $data = $bookModel->getImagesIdByBookId($id) or BasicTool::throwException("没找到二手书");
        $imgs = array_values(array_filter([$data["image_id_one"], $data["image_id_two"], $data["image_id_three"]]));
        $imageModel->deleteImageById($imgs) or BasicTool::throwException("删除失败");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

/**
 * 清空已删除的二手书 [GOD权限]
 */
function emptyAllDeletedBooks() {
    global $bookModel;
    global $currentUser;
    try {
        if (!($currentUser->isUserHasAuthority('GOD'))) {
            BasicTool::throwException("无权限");
        }
        $result = $bookModel->emptyAllDeletedBooks();
        if($result['code']===1){
            BasicTool::echoMessage("成功删除{$result['result']}个二手书", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::throwException($result['result']);
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}


/**
 * 获取一本二手书的E链接
 * @param $id 二手书ID
 */
function getELinkById(){
    global $bookModel;
    global $currentUser;
    try {
        if(!$currentUser->isUserHasAuthority('ADMIN')) {
            BasicTool::throwException("无权限查看");
        }
        $id = BasicTool::get("id","二手书ID不能为空");
        $link = $bookModel->getELinkById($id) or BasicTool::throwException("未找到E-链接");
        BasicTool::echoMessage($link, $_SERVER['HTTP_REFERER']);
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}

/**
 * 上架一本二手书
 * @param $echoType
 * @param $id 该二手书ID
 */
function launchBookById($echoType = "normal"){
    global $bookModel;
    global $currentUser;
    try {
        $id = BasicTool::get("id","二手书ID不能为空");
        $book = $bookModel->getBookById($id) or BasicTool::throwException("未找到该二手书");
        if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
            $currentUser->userId == $book['user_id'] or BasicTool::throwException("无权限");
        }
        $result = $bookModel->launchBook($id) or BasicTool::throwException("上架失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("成功上架该二手书", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, $result);
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
 * 下架一本二手书
 * @param $echoType
 * @param $id 该二手书ID
 */
function unLaunchBookById($echoType = "normal"){
    global $bookModel;
    global $currentUser;
    try {
        $id = BasicTool::get("id","二手书ID不能为空");
        $book = $bookModel->getBookById($id) or BasicTool::throwException("未找到该二手书");
        if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
            $currentUser->userId == $book['user_id'] or BasicTool::throwException("无权下架其他人的二手书");
        }
        $result = $bookModel->unLaunchBook($id) or BasicTool::throwException("下架失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("成功下架该二手书", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, $result);
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
