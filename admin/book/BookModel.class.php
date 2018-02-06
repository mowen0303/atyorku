<?php
namespace admin\book;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \Credit as Credit;
use admin\transaction\TransactionModel as TransactionModel;
use \BasicTool as BasicTool;
use \Exception as Exception;

class BookModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "book";
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
        $arr["pay_with_points"] = $payWithPoints ? 1 : 0;
        $arr["is_e_document"] = $isEDocument ? 1 : 0;
        $arr["e_link"] = $eLink;
        $arr["publish_time"] = time();
        $arr["last_modified_time"] = time();
        $bool = $this->addRow($this->table, $arr);
        if ($bool) {
            $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id in ({$bookCategoryId}) AND NOT is_deleted AND is_available) WHERE id in ({$bookCategoryId})";
            $this->sqltool->query($sql);
            if($this->shouldRewardAddBook($userId)){
                $transactionModel = new TransactionModel();
                $transactionModel->systemAdjustCredit($userId,Credit::$addBook);
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
    public function getListOfBooks($pageSize=20, $query=false, $availableOnly=true) {
        $q = "NOT b.is_deleted";
        if($availableOnly){
            $q .= " AND b.is_available";
        }
        if($query){
            $q .= " AND ({$query})";
        }
        return $this->getBooks($pageSize,$q);
    }

    /**
     * 获取一页已删除的二手书
     * @param int $pageSize
     * @param bool $query 附加Query条件 e.x. 如果query个别用户ID下的二手书，$condition = "user_id = 123"
     * @return array
     */
    public function getListOfDeletedBooks($pageSize=20, $query=false){
        $q = "b.is_deleted";
        if($query){
            $q .= " AND ({$query})";
        }
        return $this->getBooks($pageSize,$q);
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
        $this->isValidYear($year) or BasicTool::throwException("该学年 ({$year}) 不存在");
        $this->isValidTerm($term) or BasicTool::throwException("该学期 ({$term}) 不存在");

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

            $bool = $this->updateRowById($this->table, $id, $arr);
            if ($bool && $bookCategoryId != $oldBookCategoryId) {
                $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id in ({$bookCategoryId}) AND NOT is_deleted AND is_available) WHERE id in ({$bookCategoryId}, {$oldBookCategoryId})";
                $this->sqltool->query($sql);
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
                $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id in ({$bookCategoryId})) WHERE id in ({$bookCategoryId})";
                $this->sqltool->query($sql);
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
                $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id in ({$bookCategoryId})) WHERE id in ({$bookCategoryId})";
                $this->sqltool->query($sql);
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
                $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id in ({$bookCategoryId})) WHERE id in ({$bookCategoryId})";
                $this->sqltool->query($sql);
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
     * @throws Exception
     */
    function restoreBookById($id){
        $id = intval($id) or BasicTool::throwException("无效二手书ID");
        $result = $this->getBookById($id) or BasicTool::throwException("二手书不存在");
        $result["is_deleted"] = 0;
        $bool = $this->updateRowById($this->table,$id,$result);
        if(!$bool) {BasicTool::throwException($this->errorMsg);}
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
     * @return array
     */
    private function getBooks($pageSize=20, $query=false) {
        $select = "SELECT b.id,b.price,b.name,b.description,b.user_id,b.book_category_id,b.course_id,b.image_id_one,b.image_id_two,b.image_id_three,b.professor_id,b.term_year,b.term_semester,b.count_comments,b.count_view,b.is_e_document,b.pay_with_points,b.is_available,b.publish_time,b.last_modified_time,u.user_class_id,u.img,u.alias,u.gender,u.major,u.enroll_year,u.degree,uc.is_admin,bc.id AS book_category_id, bc.name AS book_category_name, img.thumbnail_url AS thumbnail_url, img.height AS img_height, img.width AS img_width, c1.id AS course_code_child_id, c1.title AS course_code_child_title, c2.id AS course_code_parent_id, c2.title AS course_code_parent_title, CONCAT(p.firstname, ' ', p.lastname) AS prof_name";
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

    private function shouldRewardAddBook($userId) {
        $userId = intval($userId);
        $t = BasicTool::getTodayTimestamp();
        $startTime = $t['startTime'];
        $endTime = $t['endTime'];
        $sql = "SELECT COUNT(*) AS count FROM {$this->table} WHERE user_id in ({$userId}) AND publish_time>=({$startTime}) AND publish_time<=({$endTime})";
        $count = $this->sqltool->getRowBySql($sql)["count"];
        return $count<6;
    }
}



?>
