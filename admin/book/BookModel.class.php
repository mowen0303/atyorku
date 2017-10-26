<?php
namespace admin\book;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use admin\image\ImageModel;
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
    public function addBook($name, $price, $description, $bookCategoryId, $userId, $img1, $img2, $img3)
    {
        $arr = [];
        $arr["name"] = $name;
        $arr["price"] = $price;
        $arr["description"] = $description;
        $arr["book_category_id"] = $bookCategoryId;
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
        $arr["publish_time"] = time();
        $arr["last_modified_time"] = time();
        $bool = $this->addRow($this->table, $arr);
        if ($bool) {
            $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id in ({$bookCategoryId})) WHERE id in ({$bookCategoryId})";
            $this->sqltool->query($sql);
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
    * 调出一页二手书
    * @param int $pageSize 每页显示数
    * @param String $query 附加Query条件 e.x. 如果query个别用户ID下的二手书，$condition = "user_id = 123"
    * @return 返回二维数组
    */
    public function getListOfBooks($pageSize=20, $query=false) {
        $sql = "SELECT b.*, u.name AS user_name, bc.id AS book_category_id, bc.name AS book_category_name, img.thumbnail_url AS thumbnail_url, img.height AS img_height, img.width AS img_width FROM(`{$this->table}` b LEFT JOIN `book_category` bc ON b.book_category_id = bc.id LEFT JOIN `user` u ON b.user_id = u.id LEFT JOIN `image` img ON b.image_id_one = img.id) ORDER BY `sort` DESC,`last_modified_time` DESC";
        $countSql = "SELECT COUNT(*) FROM(`{$this->table}` b LEFT JOIN `book_category` bc ON b.book_category_id = bc.id LEFT JOIN `user` u ON b.user_id = u.id LEFT JOIN `image` img ON b.image_id_one = img.id)";
        if ($query) {
            $sql = "{$sql} WHERE ({$query})";
            $countSql = "{$countSql} WHERE ({$query})";
        }

        return parent::getListWithPage($this->table, $sql, $countSql, $pageSize);
    }

    /**
    * 调出一页特定二手书类别ID下的二手书
    * @param int $bookcategoryId 二手书类别ID
    * @param int 每页显示数
    * @return 返回二维数组
    */
    public function getBooksByCategoryId($bookCategoryId, $pageSize=40) {
        return $this->getListOfBooks($pageSize, "book_category_id = {$bookCategoryId}");
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
        $sql = "SELECT img.* FROM {$this->table} b INNER JOIN `image` img ON img.id in (b.image_id_one, b.image_id_two, b.image_id_three)";
        return $this->sqltool->getRowBySql($sql);
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
    public function updateBook($id, $name, $price, $description, $bookCategoryId, $userId, $img1, $img2, $img3)
    {
        $arr = [];
        $arr["name"] = $name;
        $arr["price"] = $price;
        $arr["description"] = $description;
        $arr["book_category_id"] = $bookCategoryId;
        $arr["user_id"] = $userId;
        $arr["image_id_one"] = $img1 ? $img1 : "NULL";
        $arr["image_id_two"] = $img2 ? $img2 : "NULL";
        $arr["image_id_three"] = $img3 ? $img3 : "NULL";
        $arr["last_modified_time"] = time();

        // check if book category is changed
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        if ($result) {
            $oldBookCategoryId = $result["book_category_id"];

            $bool = $this->updateRowById($this->table, $id, $arr);
            if ($bool) {
                $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id in ({$bookCategoryId})) WHERE id in ({$bookCategoryId})";
                $this->sqltool->query($sql);
                if ($bookCategoryId != $oldBookCategoryId) {
                    $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id in ({$oldBookCategoryId})) WHERE id in ({$oldBookCategoryId})";
                    $this->sqltool->query($sql);
                }
            }

            return $bool;
        }
        return false;
    }

    /**
    * Override updateRowById
    * enable to set null to specified field, pass "NULL" as value to that key
    *
    * 通过主键id修改一条数据
    * @param $table 表名
    * @param $id id的值
    * @param $arrKV 把字段和值封装到键值对数组中
    * @param bool $debug
    * @return bool
    */
    public function updateRowById($table, $id, $arrKV, $debug = false)
    {
        $str = "";
        foreach ($arrKV as $k => $v) {
            if ($v == "NULL") {
                $str .= "{$k}=NULL,";
            } else {
                $str .= "{$k}='{$v}',";
            }
        }
        $str = substr($str, 0, -1);
        $sql = "update $table set {$str} where id in ('{$id}')";
        echo $debug ? $sql : null;
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        }
        $this->errorMsg = "没有数据受到影响";
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
}



?>
