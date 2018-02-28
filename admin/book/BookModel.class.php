<?php
namespace admin\book;   //-- 注意 --//
use admin\courseCode\CourseCodeModel;
use admin\image\ImageModel;
use admin\professor\ProfessorModel;
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \Credit as Credit;
use admin\transaction\TransactionModel as TransactionModel;
use admin\bookCategory\BookCategoryModel as BookCategoryModel;
use \BasicTool as BasicTool;
use \Exception as Exception;

abstract class BookAction {
    const ADD = 0;
    const UPDATE = 1;
    const DELETE = 2;
}

class BookModel extends Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "book";
    }

    /**
    * 检验用户操作权限
    * @param int $action BookAction
    * @param int $bookUserId 要修改或删除的book的用户ID
    * @throws Exception
    */
    public function isAuthorized($action, $bookUserId=0){
        $currentUser = new UserModel();
        $userId = $currentUser->userId or BasicTool::throwException("无法找到用户ID, 请重新登陆");
        if($action===BookAction::ADD) {
            $currentUser->isUserHasAuthority('BOOK') or BasicTool::throwException("权限不足");
        } else if(($action===BookAction::UPDATE || $action===BookAction::DELETE)) {
            $bookUserId>0 or BasicTool::throwException("请提供要修改的学习资料ID");
            if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('BOOK'))) {
                $userId == $bookUserId or BasicTool::throwException("无权修改其他人的学习资料");
            }
        } else {
            BasicTool::throwException("Unknown authorized action");
        }
    }


    public function validateName($name){
        $name = trim($name) or BasicTool::throwException("学习资料标题不能为空");
        (strlen($name)>0 && strlen($name)<=255) or BasicTool::throwException("学习资料标题长度不能超过255字节");
        return $name;
    }

    public function validateIsAvailable($available) {
        $available !== null or BasicTool::throwException("学习资料可用状态不能为空");
        return intval($available == true);
    }

    public function validatePayWithPoints($payWithPoints) {
        return intval($payWithPoints==true);
    }

    public function validateIsEDocument($isEDocument, $payWithCredit=false) {
        $isEDocument = intval($isEDocument==true);
        if($payWithCredit && !$isEDocument){
            BasicTool::throwException("积分支付目前只支持电子版");
        }
        return $isEDocument;
    }

    public function validateELink($eLink, $payWithCredit=false) {
        $eLink = trim($eLink);
        if($payWithCredit) {
            strlen($eLink)>0 or BasicTool::throwException("学习资料电子书链接不能为空");
            strlen($eLink)<=255 or BasicTool::throwException("学习资料电子书链接长度不能超过255字节");
        } else {
            $eLink = "";
        }
        return $eLink;
    }

    public function validatePrice($price, $payWithCredit=false) {
        if($price === null){ BasicTool::throwException("学习资料价格不能为空"); }
        $price = floatval($price);
        if($payWithCredit) {
            $price>=50 or BasicTool::throwException("积分支付最低50积分");
        } else {
            $price>=0 or BasicTool::throwException("学习资料价格不能为负数");
        }
        $price<=99999999.99 or BasicTool::throwException("学习资料价格不能大于 $99,999,999.99");
        return floor($price*100)/100;
    }

    public function validateDescription($description) {
        $description = trim($description);
        (strlen($description)<=255) or BasicTool::throwException("学习资料描述长度不能超过255字节");
        return $description;
    }

    public function validateCourseId($parentCode,$childCode) {
        $parentCode = trim($parentCode) or BasicTool::throwException("所属学科大类不能为空");
        $childCode = trim($childCode) or BasicTool::throwException("所属学科课号不能为空");
        $courseCodeModel = new CourseCodeModel();
        $courseId = $courseCodeModel->getCourseIdByCourseCode($parentCode, $childCode) or BasicTool::throwException("未找到指定科目Id");
        return $courseId;
    }

    public function validateProfessorName($profName) {
        $profName = trim($profName);
        $profId = 0;
        if ($profName) {
            $professorModel = new ProfessorModel();
            $profId = $professorModel->getProfessorIdByFullName($profName) or BasicTool::throwException("教授名称格式错误");
        }
        return $profId;
    }

    public function validateYear($year) {
        $year = intval($year);
        $this->isValidYear($year) or BasicTool::throwException("该学年 ({$year}) 不存在");
        return $year;
    }

    public function validateTerm($term) {
        $term = trim($term);
        $this->isValidTerm($term) or BasicTool::throwException("该学期 ({$term}) 不存在");
        return $term;
    }

    public function validateBookCategoryId($bookCategoryId) {
        $bookCategoryId = intval($bookCategoryId);
        $bookCategoryId > 0 or BasicTool::throwException("学习资料所属分类不能为空");
        $bookCategoryModel = new BookCategoryModel();
        $bookCategoryModel->getBookCategory($bookCategoryId) or BasicTool::throwException("学习资料所属分类不存在");
        return $bookCategoryId;
    }

    /**
     * 检验学习资料添加、修改POST请求
     * @return array 返回验证过的学习资料内容
     * @throws Exception
     */
    public function validate(){
        $currentUser = new UserModel();
        $arr = [];
        $flag = BasicTool::post('flag');
        $currentBook = null;

        /***********************/
        /*  Fields Validation  */
        /***********************/

        // 验证权限
        if ($flag=='update') {
            $id = intval(BasicTool::post("id"));
            $id>0 or BasicTool::throwException("无效学习资料ID");
            $currentBook = $this->getBookById($id) or BasicTool::throwException("无法找到学习资料");
            $bookUserId = $currentBook['user_id'];
            $this->isAuthorized(BookAction::UPDATE, $bookUserId);
            $arr['user_id'] = $bookUserId;
        } else if ($flag=='add') {
            $this->isAuthorized(BookAction::ADD);
            $arr['user_id'] = $currentUser->userId;
        }

        // validate name
        $name = trim(BasicTool::post("name")) or BasicTool::throwException("学习资料标题不能为空");
        (strlen($name)>0 && strlen($name)<=255) or BasicTool::throwException("学习资料标题长度不能超过255字节");

        $available = BasicTool::post("is_available");
        $available = ($available !== null) ? intval($available != 0) : 1;

        $payWithPoints = intval(BasicTool::post("pay_with_points"));
        $isEDocument = intval(BasicTool::post("is_e_document"));
        $eLink = trim(BasicTool::post("e_link"));

        // validate and format price
        $price = BasicTool::post("price");
        ($price !== null) or BasicTool::throwException("学习资料价格不能为空");
        $price = floatval($price);
        if($payWithPoints){
            $isEDocument or BasicTool::throwException("积分支付目前只支持电子版");
            $eLink or BasicTool::throwException("积分支付的电子版链接不能为空");
            $price>=50 or BasicTool::throwException("积分支付最低50积分");
        }
        $price>=0 or BasicTool::throwException("学习资料价格不能为负数");
        $price<=99999999.99 or BasicTool::throwException("学习资料价格不能大于 $99,999,999.99");
        $price = floor($price*100)/100;

        // validate description
        $description = trim(BasicTool::post("description"));
        (strlen($description)>0 && strlen($description)<=255) or BasicTool::throwException("学习资料描述长度不能超过255字节");


        // validate and get course code id
        $parentCode = trim(BasicTool::post("course_code_parent_title")) or BasicTool::throwException("所属学科大类不能为空");
        $childCode = trim(BasicTool::post("course_code_child_title")) or BasicTool::throwException("所属学科课号不能为空");
        $courseCodeModel = new CourseCodeModel();
        $courseId = $courseCodeModel->getCourseIdByCourseCode($parentCode, $childCode) or BasicTool::throwException("未找到指定科目Id");

        // validate and get professor id
        $profName = BasicTool::post("prof_name");
        $profId = 0;
        if ($profName) {
            $professorModel = new ProfessorModel();
            $profId = $professorModel->getProfessorIdByFullName($profName) or BasicTool::throwException("教授名称格式错误");
        }

        // validate year and term
        $year = intval(BasicTool::post("term_year"));
        $term = trim(BasicTool::post("term_semester"));
        $this->isValidYear($year) or BasicTool::throwException("该学年 ({$year}) 不存在");
        $this->isValidTerm($term) or BasicTool::throwException("该学期 ({$term}) 不存在");

        // validate and get book category id
        $bookCategoryId = intval(BasicTool::post("book_category_id", "学习资料所属分类不能为空"));
        $bookCategoryModel = new BookCategoryModel();
        $bookCategoryModel->getBookCategory($bookCategoryId) or BasicTool::throwException("学习资料所属分类不存在");

        // process images
        $imgArr = array(BasicTool::post("image_id_one"),BasicTool::post("image_id_two"),BasicTool::post("image_id_three"));
        $currImgArr = ($currentBook!=null) ? array($currentBook['image_id_one'],$currentBook['image_id_two'],$currentBook['image_id_three']) : false;
        $imageModel = new ImageModel();
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"book");


        /***********************/
        /* Set fields to array */
        /***********************/


        $arr["name"] = $name;
        $arr["price"] = $price;
        $arr["description"] = $description;
        $arr["book_category_id"] = $bookCategoryId;
        $arr["course_id"] = $courseId;
        if ($imgArr[0]) {
            $arr["image_id_one"] = $imgArr[0];
        }
        if ($imgArr[1]) {
            $arr["image_id_two"] = $imgArr[1];
        }
        if ($imgArr[2]) {
            $arr["image_id_three"] = $imgArr[2];
        }
        $arr["professor_id"] = $profId;
        $arr["is_available"] = $available;
        $arr["term_year"] = $year;
        $arr["term_semester"] = $term;
        $arr["pay_with_points"] = $payWithPoints;
        $arr["is_e_document"] = $isEDocument;
        $arr["e_link"] = $eLink;
        $time = time();

        if ($flag=='add') {
            $arr["publish_time"] = $time;
        }
        $arr["last_modified_time"] = $time;

        return $arr;
    }


    /**
     * 添加一本书
     * @return $bool
     */
    public function addBook($name, $price, $description, $bookCategoryId, $courseId, $userId, $img1, $img2, $img3, $profId, $year, $term, $payWithPoints,$available, $isEDocument, $eLink)
    {
        $arr = [];
        $arr["name"] = $name;
        $arr["price"] = $price;
        $arr["description"] = $description;
        $arr["book_category_id"] = $bookCategoryId;
        $arr["course_id"] = $courseId;
        $arr["user_id"] = $userId;
        if ($img1) {
            $arr["image_id_one"] = $img1;
        }
        if ($img2) {
            $arr["image_id_two"] = $img2;
        }
        if ($img3) {
            $arr["image_id_three"] = $img3;
        }
        $arr["professor_id"] = $profId;
        $arr["is_available"] = $available;
        $arr["term_year"] = $year;
        $arr["term_semester"] = $term;
        $arr["pay_with_points"] = $payWithPoints;
        $arr["is_e_document"] = $isEDocument;
        $arr["e_link"] = $eLink;
        $t = time();
        $arr["publish_time"] = $t;
        $arr["last_modified_time"] = $t;
        $bool = $this->addRow($this->table, $arr);
        $id = $this->idOfInsert;
        if ($bool && $id>0) {
            $bcm = new BookCategoryModel();
            $bcm->updateBookCategoryCount($bookCategoryId);
            $this->updateCourseReport($courseId);
//            $this->updateReports(intval($courseId), intval($profId));
            if($this->shouldRewardAddBook($userId)){
                $transactionModel = new TransactionModel();
                $transactionModel->systemAdjustCredit($userId,Credit::$addBook,"book",$id);
            }
        }
        return $bool;
    }

    /**
     * 查询一本书
     * @return 一维键值数组
     */
    public function getBookById($id)
    {
        $sql = "SELECT * from {$this->table} WHERE id = {$id}";
        return $this->sqltool->getRowBySql($sql);
    }


    /**
     * 获取一页二手书（非删除）
     * @param int $pageSize 每页显示数
     * @param bool $query 附加Query条件 e.x. 如果query个别用户ID下的二手书，$condition = "user_id = 123"
     * @param bool $availableOnly
     * @return array
     */
    public function getListOfBooks($pageSize=20, $query=false, $availableOnly=true, $elink=false) {
        $q = "NOT b.is_deleted";
        if($availableOnly){
            $q .= " AND b.is_available";
        }
        if($query){
            $q .= " AND ({$query})";
        }
        $selectSql = "";
        if($elink){
            $selectSql = "b.e_link";
        }
        return $this->getBooks($pageSize,$q,$selectSql);
    }

    /**
     * 获取一页未上架的二手书（非删除)(二手书含电子版链接)
     * @param int $pageSize
     * @param bool $query
     * @return array
     */
    public function getListOfUnavailableBooks($pageSize=20, $query=false) {
        $q = "NOT b.is_deleted AND NOT b.is_available";
        if($query){
            $q .= " AND ({$query})";
        }
        return $this->getBooks($pageSize,$q,"b.e_link");
    }

    /**
     * 获取一页已删除的二手书(二手书含电子版链接)
     * @param int $pageSize
     * @param bool $query 附加Query条件 e.x. 如果query个别用户ID下的二手书，$condition = "user_id = 123"
     * @return array
     */
    public function getListOfDeletedBooks($pageSize=20, $query=false){
        $q = "b.is_deleted";
        if($query){
            $q .= " AND ({$query})";
        }
        return $this->getBooks($pageSize,$q,"b.e_link");
    }

    /**
    * 调出一页特定二手书类别ID下的二手书
    * @param int $bookcategoryId 二手书类别ID
    * @param int 每页显示数
    * @return 返回二维数组
    */
    public function getBooksByCategoryId($bookCategoryId, $pageSize=40) {
        return $this->getListOfBooks($pageSize, "book_category_id={$bookCategoryId}");
    }

    /**
    * 调出一页特定用户ID下的二手书
    * @param int $userId 用户ID
    * @param int 每页显示数
    * @return 返回二维数组
    */
    public function getBooksByUserId($userId, $pageSize=40) {
        return $this->getListOfBooks($pageSize, "b.user_id={$userId}");
    }

    /**
    * 调出一页特定用户名下的二手书
    * @param String $username 用户名
    * @param int 每页显示数
    * @return 返回二维数组
    */
    public function getBooksByUsername($username, $pageSize=40) {
        return $this->getListOfBooks($pageSize, "u.name='{$username}'");
    }

    /**
    * 通过模糊搜索调出一页二手书
    * @param String $keywords 搜索关键词
    * @param int 每页显示数
    * @return 返回二维数组
    */
    public function getBooksByKeywords($keywords, $pageSize=40) {
        return $this->getListOfBooks($pageSize, "b.name LIKE '%{$keywords}%' or b.description LIKE '%{$keywords}%'");
    }


    /**
    * 通过二手书ID获取用户ID
    * @return 用户ID
    **/
    public function getUserIdFromBookId($id) {
        $sql = "SELECT user_id FROM {$this->table} WHERE id={$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result["user_id"];
    }

    /**
    * 获取指定二手书封面图片（第一张图）
    * @param id 二手书ID
    * @return 返回一维数组
    */
    public function getFirstImageByBookId($id) {
        $sql = "SELECT img.* FROM {$this->table} b INNER JOIN `image` img ON b.image_id_one = img.id";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
    * 获取指定二手书所有图片
    * @param id 二手书ID
    * @return 返回二维数组
    */
    public function getImagesByBookId($id) {
        $sql = "SELECT i.* FROM {$this->table} b, image i WHERE b.id={$id} AND (b.image_id_one=i.id or b.image_id_two=i.id or b.image_id_three=i.id)";
        return $this->sqltool->getListBySql($sql);
    }

    /**
    * 仅获得指定二手书关联图片ID
    * @param id 二手书ID
    * @return 返回一维数组
    */
    public function getImagesIdByBookId($id) {
        $sql = "SELECT `image_id_one`, `image_id_two`, `image_id_three` FROM `{$this->table}` WHERE `id`={$id}";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
    * 浏览数 +1
    * @param id 二手书ID
    */
    public function incrementCountViewByBookId($id)
    {
        $sql = "UPDATE book SET count_view = count_view+1 WHERE id in ($id)";
        $this->sqltool->query($sql);
    }

    /**
     * 更改一本书
     * @return bool
     */
    public function updateBook($id, $name, $price, $description, $bookCategoryId, $courseId, $userId, $img1, $img2, $img3, $profId, $year, $term, $payWithPoints, $available, $isEDocument, $eLink)
    {
        $arr = [];
        $arr["name"] = $name;
        $arr["price"] = $price;
        $arr["description"] = $description;
        $arr["book_category_id"] = $bookCategoryId;
        $arr["course_id"] = $courseId;
        $arr["user_id"] = $userId;
        $arr["image_id_one"] = $img1 ? $img1 : 0;
        $arr["image_id_two"] = $img2 ? $img2 : 0;
        $arr["image_id_three"] = $img3 ? $img3 : 0;
        $arr["professor_id"] = $profId;
        $arr["term_year"] = $year;
        $arr["term_semester"] = $term;
        $arr["pay_with_points"] = $payWithPoints ? 1 : 0;
        $arr["is_available"] = $available ? 1 : 0;
        $arr["is_e_document"] = $isEDocument ? 1 : 0;
        $arr["e_link"] = $eLink;
        $arr["last_modified_time"] = time();

        // check if book category is changed
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        if ($result) {
            $oldBookCategoryId = $result["book_category_id"];
            $oldCourseId = $result["course_id"];
            $oldProfId = $result["professor_id"];

            $bool = $this->updateRowById($this->table, $id, $arr);
            if($bool){
                // update book category counts
                if($bookCategoryId != $oldBookCategoryId){
                    $bcm = new BookCategoryModel();
                    $bcm->updateBookCategoryCount($bookCategoryId);
                    $bcm->updateBookCategoryCount($oldBookCategoryId);
                }
                // update reports book counts
                if($courseId != $oldCourseId){
                    $this->updateCourseReport($oldCourseId);
                    $this->updateCourseReport($courseId);
//                    $this->updateCourseProfReport($oldCourseId,$oldProfId);
//                    $this->updateCourseProfReport($courseId,$profId);
                }
//                if($profId != $oldProfId){
//                    $this->updateProfessorReport($oldProfId);
//                    $this->updateProfessorReport($profId);
//                    $this->updateCourseProfReport($oldCourseId,$oldProfId);
//                    $this->updateCourseProfReport($courseId,$profId);
//                }

            }

            return $bool;
        }
        return false;
    }


    /**
    * 删除二手书 by ID
    * @param id book id
    * @return 成功返回删除数据一维数组，失败返回false
    */
    public function deleteBookById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        if ($result) {
            $bookCategoryId = $result["book_category_id"];
            $sql = "DELETE FROM {$this->table} WHERE id in ({$id})";
            $bool = $this->sqltool->query($sql);
            if ($bool) {
                $bcm = new BookCategoryModel();
                $bcm->updateBookCategoryCount($bookCategoryId);
//                $this->updateReports($result['course_id'], $result['professor_id']);
                $this->updateCourseReport($result['course_id']);
                return $result;
            }
        }
        return false;
    }


    /**
    * 逻辑删除二手书 by ID
    * @param id book id
    * @return 成功返回删除数据一维数组，失败返回false
    */
    public function deleteBookLogicallyById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        if ($result) {
            $bookCategoryId = $result["book_category_id"];
            $sql = "UPDATE {$this->table} SET is_deleted=1 WHERE id in ({$id})";
            $bool = $this->sqltool->query($sql);
            if ($bool) {
                $bcm = new BookCategoryModel();
                $bcm->updateBookCategoryCount($bookCategoryId);
//                $this->updateReports($result['course_id'], $result['professor_id']);
                $this->updateCourseReport($result['course_id']);
                return $result;
            }
        }
        return false;
    }

    /**
     * 下架一本二手书
     * @param $id
     * @return bool|\一维关联数组
     */
    public function unLaunchBook($id){
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        if ($result) {
            $bookCategoryId = $result["book_category_id"];
            $sql = "UPDATE {$this->table} SET is_available=0 WHERE id in ({$id})";
            $bool = $this->sqltool->query($sql);
            if ($bool) {
                $bcm = new BookCategoryModel();
                $bcm->updateBookCategoryCount($bookCategoryId);
//                $this->updateReports($result['course_id'], $result['professor_id']);
                $this->updateCourseReport($result['course_id']);
                return $result;
            }
        }
        return false;
    }

    /**
     * 上架一本二手书
     * @param $id
     * @return bool|\一维关联数组
     */
    public function launchBook($id){
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        if ($result) {
            $bookCategoryId = $result["book_category_id"];
            $sql = "UPDATE {$this->table} SET is_available=1 WHERE id in ({$id})";
            $bool = $this->sqltool->query($sql);
            if ($bool) {
                $bcm = new BookCategoryModel();
                $bcm->updateBookCategoryCount($bookCategoryId);
//                $this->updateReports($result['course_id'], $result['professor_id']);
                $this->updateCourseReport($result['course_id']);
                return $result;
            }
        }
        return false;
    }

    function getELinkById($id){
        $sql = "SELECT e_link FROM {$this->table} WHERE id in ({$id})";
        $row = $this->sqltool->getRowBySql($sql);
        return $row['e_link'];
    }

    /**
     * 恢复一本已删除的二手书
     * @param $id 二手书ID
     * @return 回复的二手书 一维关联数组
     * @throws Exception
     */
    function restoreBookById($id){
        $id = intval($id) or BasicTool::throwException("无效二手书ID");
        $result = $this->getBookById($id) or BasicTool::throwException("二手书不存在");
        $result["is_deleted"] = 0;
        $bookCategoryId = $result["book_category_id"];
        $bool = $this->updateRowById($this->table,$id,$result) or BasicTool::throwException($this->errorMsg);
        $bcm = new BookCategoryModel();
        $bcm->updateBookCategoryCount($bookCategoryId);
//        $this->updateReports($result['course_id'], $result['professor_id']);
        $this->updateCourseReport($result['course_id']);
        return $result;
    }

    /**
     * 清空所有已删除的二手书
     * @return array [code=> {1,0}, result=> {成功返回删除数量 | 失败返回错误信息}]
     */
    function emptyAllDeletedBooks(){
        $sql = "DELETE FROM {$this->table} WHERE is_deleted=1";
        $bool = $this->sqltool->query($sql);
        $result = ["code"=>intval($bool),"result"=>($bool?($this->sqltool->getAffectedRows()):($this->errorMsg))];
        return $result;
    }


    /**=====================**/
    /*** Private Functions ***/
    /**=====================**/


    /**
    * validate year
    * @param year 用户提供的year
    * @return bool
    */
    private function isValidYear($year) {
        $year = intval($year);
        return $year == 0 || ($year > 1959 && $year <= date("Y"));
    }

    /**
    * validate term
    * @param term 用户提供的term
    * @return bool
    */
    private function isValidTerm($term) {
        return in_array($term, array('','Winter','Summer','Summer 1','Summer 2','Year','Fall'));
    }

    /**
     * 获取一页二手书
     * @param int $pageSize 每页数量
     * @param bool $query additional query
     * @param bool $selectSql additional select query
     * @return array
     */
    private function getBooks($pageSize=20, $query=false, $selectSql=false) {
        $select = "SELECT b.id,b.price,b.name,b.description,b.user_id,b.book_category_id,b.course_id,b.image_id_one,b.image_id_two,b.image_id_three,b.professor_id,b.term_year,b.term_semester,b.count_comments,b.count_view,b.is_e_document,b.pay_with_points,b.is_available,b.publish_time,b.last_modified_time,u.user_class_id,u.img,u.alias,u.gender,u.major,u.enroll_year,u.degree,uc.is_admin,bc.id AS book_category_id, bc.name AS book_category_name, img.thumbnail_url AS thumbnail_url, img.height AS img_height, img.width AS img_width, c1.id AS course_code_child_id, c1.title AS course_code_child_title, c2.id AS course_code_parent_id, c2.title AS course_code_parent_title, CONCAT(p.firstname, ' ', p.lastname) AS prof_name";
        if($selectSql){$select .= ",{$selectSql}";};
        $from = "FROM(`{$this->table}` b LEFT JOIN `book_category` bc ON b.book_category_id = bc.id LEFT JOIN `user` u ON b.user_id = u.id LEFT JOIN `user_class` uc ON u.user_class_id = uc.id LEFT JOIN `image` img ON b.image_id_one = img.id LEFT JOIN `course_code` c1 ON b.course_id = c1.id LEFT JOIN `course_code` c2 ON c1.parent_id = c2.id LEFT JOIN `professor` p ON b.professor_id = p.id)";

        $where = $query ? ("WHERE " . "({$query})") : "";
        $order = "ORDER BY `sort` DESC,`last_modified_time` DESC";

        $sql = "{$select} {$from} {$where} {$order}";
        $countSql = "SELECT COUNT(*) {$from} {$where}";
        $arr = parent::getListWithPage($this->table, $sql, $countSql, $pageSize);
        // Format publish time and enroll year
        foreach ($arr as $k => $v) {
            $t = $v["publish_time"];
            $y = $v["enroll_year"];
            if($t) $arr[$k]["publish_time"] = BasicTool::translateTime($t);
            if($y) $arr[$k]["enroll_year"] = BasicTool::translateEnrollYear($y);
        }
        return $arr;
    }

    /**
     * 是否奖励积分给此次二手书添加 Rule: [每个用户、每天前五次添加二手书奖励积分]
     * @param $userId 用户ID
     * @return bool 是否奖励
     */
    private function shouldRewardAddBook($userId) {
        $userId = intval($userId);
        $t = BasicTool::getTodayTimestamp();
        $startTime = $t['startTime'];
        $endTime = $t['endTime'];
        $sql = "SELECT COUNT(*) AS count FROM {$this->table} WHERE user_id in ({$userId}) AND publish_time>=({$startTime}) AND publish_time<=({$endTime})";
        $count = $this->sqltool->getRowBySql($sql)["count"];
        return $count<6;
    }

//    /**
//     * 更新对应courseId和profId的报告
//     * @param bool $courseId
//     * @param bool $profId
//     */
//    private function updateReports($courseId=false,$profId=false){
//        if($courseId){
//            $this->updateCourseReport($courseId);
//        }
//        if($profId){
//            $this->updateProfessorReport($profId);
//        }
//        if($courseId && $profId){
//            $this->updateCourseProfReport($courseId,$profId);
//        }
//    }

    /**
     * 更新 course_report 里指定的一个 course id 的二手书数量
     * @param $courseId
     * @return bool|\mysqli_result|\一维关联数组
     */
    private function updateCourseReport($courseId){
        $courseId = intval($courseId);
        $sql = "SELECT * FROM course_report WHERE course_code_id = {$courseId}";
        $result = $this->sqltool->getRowBySql($sql);
        if($result){
            // 有对应的报告
            $availSql = BookModel::getCountOfAvailableBookSql();
            $sql = "UPDATE course_report SET book_count=({$availSql} AND course_id={$courseId}) WHERE course_code_id={$courseId}";
            $result = $this->sqltool->query($sql);
        }
        return $result;
    }

//    /**
//     * 更新 course_prof_report 里指定的一个 course id 和 prof id 的二手书数量
//     * @param $courseId
//     * @param $profId
//     * @return bool|\mysqli_result|\一维关联数组
//     */
//    private function updateCourseProfReport($courseId,$profId){
//        $courseId = intval($courseId);
//        $profId = intval($profId);
//        if($courseId && $profId){
//            $sql = "SELECT * FROM course_prof_report WHERE course_code_id = {$courseId} AND prof_id = {$profId}";
//            $result = $this->sqltool->getRowBySql($sql);
//            if($result){
//                // 有对应的报告
//                $availSql = BookModel::getCountOfAvailableBookSql();
//                $sql = "UPDATE course_prof_report SET book_count=({$availSql} AND course_id={$courseId} AND professor_id={$profId}) WHERE course_code_id={$courseId} AND prof_id={$profId}";
//                $result = $this->sqltool->query($sql);
//            }
//            return $result;
//        }
//        return false;
//    }
//
//    /**
//     * 更新 professor_report 里指定的一个 prof id 的二手书数量
//     * @param $profId
//     * @return bool|\mysqli_result|\一维关联数组
//     */
//    private function updateProfessorReport($profId){
//        $profId = intval($profId);
//        $sql = "SELECT * FROM professor_report WHERE prof_id = {$profId}";
//        $result = $this->sqltool->getRowBySql($sql);
//        if($result){
//            $availSql = BookModel::getCountOfAvailableBookSql();
//            $sql = "UPDATE professor_report SET book_count=({$availSql} AND professor_id={$profId}) WHERE prof_id={$profId}";
//            $result = $this->sqltool->query($sql);
//        }
//        return $result;
//    }

    /**
     * 获得有效二手书数量SQL字符串
     * @return string
     */
    private static function getCountOfAvailableBookSql(){
        return "SELECT COUNT(*) FROM book WHERE NOT is_deleted AND is_available";
    }
}



?>
