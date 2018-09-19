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
use admin\book\BookAction as BookAction;
call_user_func(BasicTool::get('action'));

//============ Function with JSON ===============//

/**
 * JSON -  获取指定Id的学习资料信息
 * @param book_id 学习资料ID
 * http://www.atyorku.ca/admin/book/bookController.php?action=getBookByIdWithJson&id=1
 */
function getBookByIdWithJson() {
    global $bookModel;
    try {
        $id = BasicTool::get("id","请指定学习资料Id");

        if (validateId($id)) {
            $result = $bookModel->getListOfBooks(1, "b.id = {$id}");
            if ($result && is_array($result)) {
                BasicTool::echoJson(1, "成功", $result[0]);
            } else {
                BasicTool::echoJson(0, "该学习资料未找到或已下架");
            }
        } else {
            BasicTool::echoJson(0,"学习资料ID无效");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON -  获取指定学习资料类别ID下的一页学习资料
 * @param pageSize 每一页学习资料获取量，默认值=40
 * @param book_category_id 学习资料类别ID
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfBooksByCategoryIdWithJson&book_category_id=3&pageSize=20
 */
function getListOfBooksByCategoryIdWithJson() {
    try {
        $id = BasicTool::get("book_category_id","请指定学习资料类别Id");
        if (validateId($id)) {
            getListOfBooksWithJson("book_category_id", (int)$id);
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}

/**
 * JSON -  获取指定用户ID下的一页学习资料
 * @param pageSize 每一页学习资料获取量，默认值=40
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
 * JSON -  获取指定用户名下的一页学习资料
 * @param pageSize 每一页学习资料获取量，默认值=40
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
 * JSON -  获取指定关键词相关的一页学习资料
 * @param pageSize 每一页学习资料获取量，默认值=40
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
 * JSON - 获取一页我的学习资料 [用户需要登录]
 * <GET Parameters>
 * @param int queryType 1=出售中，2=已下架，3=待处理
 * @param int pageSize
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfMyBooksWithJson&queryType=1&pageSize=20
 */
function getListOfMyBooksWithJson() {
    global $bookModel;
    global $currentUser;

    try {
        $userId = $currentUser->userId;
        $userId or BasicTool::throwException("请先登录");
        $q = intval(BasicTool::get("queryType") ?: 1);
        $pageSize = BasicTool::get("pageSize") ?: 20;
        $query = "b.user_id=${userId}";
        $result = [];
        if($q===1){
            // 获取出售中
            $result = $bookModel->getListOfBooks($pageSize, $query, true, true);
        } else if ($q===2){
            // 获取已下架
            $result = $bookModel->getListOfBooks($pageSize, $query . " AND NOT b.is_available", false, true);
        } else if ($q===3){
            // 获取待处理
            $result = $bookModel->getListOfInTransactionSellingBooksByUserId($userId, $pageSize);
        } else {
            BasicTool::throwException("没找到获取类型");
        }
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "没有更多内容");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * JSON -  获取某一页学习资料
 * @param pageSize 每一页学习资料获取量，默认值=40
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
            // 根据指定搜索类别来获取学习资料
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
            $ids = array_column($result, "id");
            $bookModel->incrementCountViewByBookId($ids);
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "没有更多内容");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }

}


/**
 * JSON -  获取某一页学习资料
 * @param pageSize 每一页学习资料获取量，默认值=40
 * @param q $query string 搜索条件
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfBooksWithJsonV2&pageSize=40
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
            $ids = array_column($result, "id");
            $bookModel->incrementCountViewByBookId($ids);
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "没有更多内容");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }

}


/**
 * JSON -  获取一页用户订购的学习资料
 * @param pending 订购状态 -1 = 全部， 0 = 已完成[默认值]， 1 = 已处理/处理中的
 * @param pageSize 每一页学习资料获取量，默认值=20
 * http://www.atyorku.ca/admin/book/bookController.php?action=getListOfOrderedBooksWithJson&pending=0&pageSize=20
 */
function getListOfOrderedBooksWithJson() {
    global $bookModel;
    global $currentUser;

    try {
        $userId = $currentUser->userId;
        $userId or BasicTool::throwException("请先登录");
        $pending = intval(BasicTool::get("pending") ?: 0);
        $pageSize = BasicTool::get("pageSize") ?: 20;
        if($pending!==-1&&$pending!==0&&$pending!==1){
            BasicTool::throwException("无效索引");
        }
        $result = $bookModel->getListOfOrderedBooksByUserId($userId,$pending,$pageSize);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "没有更多内容");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON -  搜索一页学习资料
 * @param search_type 搜索类型
 * @param search_value 搜索值
 * @param pageSize 每一页学习资料获取量，默认值=20
 * http://www.atyorku.ca/admin/book/bookController.php?action=searchBooksWithJson&search_type=keywords&search_value=abc&pageSize=20
 */
function searchBooksWithJson() {
    global $bookModel;
    try {
        $pageSize = BasicTool::get("pageSize") ?: 20;
        $queryType = BasicTool::get("search_type", "搜索类别不能为空");
        $queryValue = BasicTool::get("search_value", "搜索内容不能为空");
        $result = $bookModel->searchBooks($queryType, $queryValue, $pageSize);
        if($result){
            $ids = array_column($result, "id");
            $bookModel->incrementCountViewByBookId($ids);
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "没有更多内容");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
* JSON - 添加或修改一本学习资料
* @param flag 添加or修改 [add/update]
* @param name 学习资料名
* @param price 学习资料价钱, 不能为负数
* @param book_category_id 学习资料所属分类ID
* @param description 学习资料细节描述
* @param id 学习资料ID，修改学习资料时必填
* [POST] http://www.atyorku.ca/admin/book/bookController.php?action=addBookWithJson
*/
function addBookWithJson() {
    modifyBook("json");
}


/**
* JSON - 添加或修改一本学习资料
* @param id 删除学习资料的id
* [POST] http://www.atyorku.ca/admin/book/bookController.php?action=deleteBookWithJson
*/
function deleteBookWithJson() {
    deleteBook("json");
}

/**
* JSON - 添加或修改一本学习资料
* @param id 删除学习资料的id
* [POST] http://www.atyorku.ca/admin/book/bookController.php?action=deleteBookLogicallyWithJson
*/
function deleteBookLogicallyWithJson() {
    deleteBookLogically("json");
}

/**
 * JSON - 上架一本学习资料
 * @param id 学习资料的id
 * [POST] http://www.atyorku.ca/admin/book/bookController.php?action=launchBookWithJson
 */
function launchBookWithJson() {
    launchBookById('json');
}

/**
 * JSON - 下架一本学习资料
 * @param id 学习资料的id
 * [POST] http://www.atyorku.ca/admin/book/bookController.php?action=unlaunchBookWithJson
 */
function unlaunchBookWithJson() {
    unLaunchBookById('json');
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
 * 获取该学习资料ID的所有关联图片
 * 同时学习资料浏览数 + 1
 * @return JSON 二维数组
 */
function getImagesByBookIdWithJson() {
    global $bookModel;
    try {
        $id = BasicTool::get('id','请提供学习资料ID');
        $result = $bookModel->getImagesByBookId($id);
        if ($result) {
            BasicTool::echoJson(1, "获取图片成功", $result);
        } else {
            BasicTool::echoJson(0, '未找到图片');
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
* http://www.atyorku.ca/admin/book/bookController.php?action=purchaseBookWithJson
* 购买一本学习资料
* @param bookId 学习资料ID
* @return JSON
*/
function purchaseBookWithJson() {
    global $bookModel;
    global $currentUser;
    global $transactionModel;
    global $msgModel;
    try {
        $productTransactionModel = new \admin\productTransaction\ProductTransactionModel('book');
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
            $result = $productTransactionModel->buy($buyerId, $sellerId, $price, $buyerDescription, $sellerDescription, $bookId) or BasicTool::throwException($productTransactionModel->errorMsg);
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
 * 恢复一本已删除的学习资料 [ADMIN]操作
 */
function restoreDeletedBookByIdWithJson() {
    global $bookModel;
    global $currentUser;
    try {
        if (!($currentUser->isUserHasAuthority('ADMIN'))) {
            BasicTool::throwException("无权限");
        }
        $bookId = BasicTool::post('book_id','学习资料ID不能为空');
        $bookModel->restoreBookById($bookId);
        BasicTool::echoJson(1, "恢复成功", true);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * 下架一本学习资料
 * @param id 学习资料ID
 */
function unLaunchBookByIdWithJson() {
    unLaunchBookById("json");
}

//=========== END Function with JSON ============//



// 添加或修改一本学习资料
function modifyBook($echoType = "normal") {
    global $bookModel;
    global $imageModel;
    global $currentUser;

    try{
        $flag = BasicTool::post('flag');
        $currentBook = null;
        $userId = null;

        // 验证权限
        if ($flag=='update') {
            $id = intval(BasicTool::post("id"));
            $id>0 or BasicTool::throwException("无效学习资料ID");
            $currentBook = $bookModel->getBookById($id) or BasicTool::throwException("无法找到学习资料");
            $bookUserId = BasicTool::post("user_id") ?: $currentBook['user_id'];
            $bookModel->isAuthorized((($bookUserId===$currentBook['user_id'])?BookAction::UPDATE:BookAction::UPDATE_USERID), $bookUserId);
            $userId = $bookUserId;
        } else if ($flag=='add') {
            $bookModel->isAuthorized(BookAction::ADD);
            $userId = $currentUser->userId;
        }

        // 验证 Fields
        $name = $bookModel->validateName(BasicTool::post("name"));
        $available = $bookModel->validateIsAvailable(BasicTool::post("is_available"));
        $payWithPoints = $bookModel->validatePayWithPoints(BasicTool::post("pay_with_points"));
        $isEDocument = $bookModel->validateIsEDocument(BasicTool::post("is_e_document"),$payWithPoints);
        $eLink = $bookModel->validateELink(BasicTool::post("e_link"),$payWithPoints);
        $price = $bookModel->validatePrice(BasicTool::post("price"), $payWithPoints);
        $description = $bookModel->validateDescription(BasicTool::post("description"));
        $bookCategoryId = $bookModel->validateBookCategoryId(BasicTool::post("book_category_id"));
        $courseCodeId = $bookModel->validateCourseId(BasicTool::post("course_code_parent_title"),BasicTool::post("course_code_child_title"));
        $profId = $bookModel->validateProfessorName(BasicTool::post("prof_name"));
        $year = $bookModel->validateYear(BasicTool::post("term_year"));
        $term = "";
        if($year){
            $term = $bookModel->validateTerm(BasicTool::post("term_semester"));
        }

        // analyze images
        $imgArr = array(BasicTool::post("image_id_one"),BasicTool::post("image_id_two"),BasicTool::post("image_id_three"));
        $currImgArr = ($currentBook!=null) ? array($currentBook['image_id_one'],$currentBook['image_id_two'],$currentBook['image_id_three']) : false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"book");

        // 执行
        if ($flag=='update') {
            $bookModel->updateBook($currentBook['id'], $name, $price, $description, $bookCategoryId, $courseCodeId, $userId, $imgArr[0], $imgArr[1], $imgArr[2], $profId, $year, $term, $payWithPoints, $available, $isEDocument, $eLink) or BasicTool::throwException($bookModel->errorMsg);
            if ($echoType == "normal") {
                BasicTool::echoMessage("修改成功","/admin/book/index.php?listBook");
            } else {
                BasicTool::echoJson(1, "修改成功");
            }
        } else if ($flag=='add') {
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
* 删除1个或多本学习资料
* @param id 要删除的学习资料id或id array
*/
function deleteBook($echoType = "normal") {
    global $bookModel;
    global $currentUser;
    global $imageModel;
    try {
        $id = BasicTool::post('id') or BasicTool::throwException("请指定被删除学习资料ID");
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $i++;
                if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                    $currentUser->userId == $bookModel->getUserIdFromBookId($v) or BasicTool::throwException("无权删除其他人的学习资料");
                }
                deleteBookImagesByBookId($v);
                $bookModel->deleteBookById($v) or BasicTool::throwException("删除多本失败");
            }
        } else {
            $i++;
            if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                $currentUser->userId == $bookModel->getUserIdFromBookId($id) or BasicTool::throwException("无权删除其他人的学习资料");
            }
            deleteBookImagesByBookId($id);
            $bookModel->deleteBookById($id) or BasicTool::throwException("删除1本失败");
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}本学习资料", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}本学习资料");
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
* 逻辑删除1个或多本学习资料
* @param id 要删除的学习资料id或id array
*/
function deleteBookLogically($echoType="normal") {
    global $bookModel;
    global $currentUser;
    try {
        $id = BasicTool::post('id') or BasicTool::throwException("请指定被删除学习资料ID");
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $i++;
                if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                    $currentUser->userId == $bookModel->getUserIdFromBookId($v) or BasicTool::throwException("无权删除其他人的学习资料");
                }
                $bookModel->deleteBookLogicallyById($v) or BasicTool::throwException("删除多本失败");
            }
        } else {
            $i++;
            if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                $currentUser->userId == $bookModel->getUserIdFromBookId($id) or BasicTool::throwException("无权删除其他人的学习资料");
            }
            $bookModel->deleteBookLogicallyById($id) or BasicTool::throwException("删除1本失败");
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}本学习资料", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}本学习资料");
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
* 验证ID
* ID必须为数字且大于0
* @return boolean
*/
function validateId($id) {
    return (is_numeric($id) and ((int)$id > 0));
}

/**
* 删除指定学习资料ID相关联的图片
* @param id 学习资料ID
*/
function deleteBookImagesByBookId($id) {
    global $bookModel;
    global $imageModel;
    try {
        $data = $bookModel->getImagesIdByBookId($id) or BasicTool::throwException("没找到学习资料");
        $imgs = array_values(array_filter([$data["image_id_one"], $data["image_id_two"], $data["image_id_three"]]));
        $imageModel->deleteImageById($imgs) or BasicTool::throwException("删除失败");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

/**
 * 清空已删除的学习资料 [GOD权限]
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
            BasicTool::echoMessage("成功删除{$result['result']}个学习资料", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::throwException($result['result']);
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}


/**
 * 获取一本学习资料的E链接
 * @param $id 学习资料ID
 */
function getELinkById(){
    global $bookModel;
    global $currentUser;
    try {
        if(!$currentUser->isUserHasAuthority('ADMIN')) {
            BasicTool::throwException("无权限查看");
        }
        $id = BasicTool::get("id","学习资料ID不能为空");
        $link = $bookModel->getELinkById($id) or BasicTool::throwException("未找到E-链接");
        BasicTool::echoMessage($link, $_SERVER['HTTP_REFERER']);
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}

/**
 * 上架一本学习资料
 * @param $echoType
 * @param $id 该学习资料ID
 */
function launchBookById($echoType = "normal"){
    global $bookModel;
    global $currentUser;
    try {
        $id = BasicTool::post("id","学习资料ID不能为空");
        $book = $bookModel->getBookById($id) or BasicTool::throwException("未找到该学习资料");
        if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
            $currentUser->userId == $book['user_id'] or BasicTool::throwException("无权限");
        }
        $result = $bookModel->launchBook($id) or BasicTool::throwException("上架失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("成功上架该学习资料", $_SERVER['HTTP_REFERER']);
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
 * 下架一本学习资料
 * @param $echoType
 * @param $id 该学习资料ID
 */
function unLaunchBookById($echoType = "normal"){
    global $bookModel;
    global $currentUser;
    try {
        $id = BasicTool::post("id","学习资料ID不能为空");
        $book = $bookModel->getBookById($id) or BasicTool::throwException("未找到该学习资料");
        if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
            $currentUser->userId == $book['user_id'] or BasicTool::throwException("无权下架其他人的学习资料");
        }
        $result = $bookModel->unLaunchBook($id) or BasicTool::throwException("下架失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("成功下架该学习资料", $_SERVER['HTTP_REFERER']);
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
