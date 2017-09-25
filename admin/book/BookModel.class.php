<?php
namespace admin\book;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
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
    public function addBook($name, $price, $description, $bookCategoryId, $userId)
    {
        $arr = [];
        $arr["name"] = $name;
        $arr["price"] = $price;
        $arr["description"] = $description;
        $arr["book_category_id"] = $bookCategoryId;
        $arr["user_id"] = $userId;
        $arr["publish_time"] = time();
        $arr["last_modified_time"] = time();
        $bool = $this->addRow($this->table, $arr);
        if ($bool) {
            $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id = {$bookCategoryId}) WHERE id = {$bookCategoryId}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    /**
     * 查询一本书
     * @return 一维键值数组
     */
    public function getBook($id)
    {
        $sql = "SELECT * from {$this->table} WHERE id = {$id}";
        return $this->sqltool->getRowBySql($sql);
    }

    public function getListOfBooks($pageSize=20) {
        $sql = "SELECT b.id, b.name, b.price, b.description, b.publish_time, user.id as user_id, user.name as user_name, bc.id as book_category_id, bc.name as book_category_name FROM {$this->table} b INNER JOIN book_category bc ON b.book_category_id = bc.id INNER JOIN user ON b.user_id = user.id";
        $countSql = "SELECT COUNT(*) FROM {$this->table}";
        return parent::getListWithPage($this->table, $sql, $countSql, $pageSize);
    }

    /*调出一页书
     * 返回二维数组
     */
    public function getBooksByCategory($bookCategoryId){
        $sql = "SELECT * FROM {$this->table} WHERE book_category_id = {$bookCategoryId}";
        $countSql = "SELECT COUNT(*) FROM {$this->table} WHERE book_category_id = {$bookCategoryId}";
        return $this->getListWithPage($this->table, $sql, $countSql, 20);
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
     * 更改一本书
     * @return bool
     */
    public function updateBook($id, $name, $price, $description, $bookCategoryId, $userId)
    {
        $arr = [];
        $arr["name"] = $name;
        $arr["price"] = $price;
        $arr["description"] = $description;
        $arr["book_category_id"] = $bookCategoryId;
        $arr["user_id"] = $userId;
        $arr["last_modified_time"] = time();

        // check if book category is changed
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $oldBookCategoryId = $this->sqltool->getRowBySql($sql)["book_category_id"];
        $bool = $this->updateRowById($this->table, $id, $arr);
        if ($bool) {
            $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id = {$bookCategoryId}) WHERE id = {$bookCategoryId}";
            $this->sqltool->query($sql);
            if ($bookCategoryId != $oldBookCategoryId) {
                $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id = {$oldBookCategoryId}) WHERE id = {$oldBookCategoryId}";
                $this->sqltool->query($sql);
            }
        }

        return $bool;
    }

    public function deleteBook($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $bookCategoryId = $this->sqltool->getRowBySql($sql)["book_category_id"];
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        $bool = $this->sqltool->query($sql);
        if ($bool) {
            $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id = {$bookCategoryId}) WHERE id = {$bookCategoryId}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }
}



?>
