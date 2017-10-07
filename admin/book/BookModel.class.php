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
        $sql = "SELECT b.id, b.name, b.price, b.description, b.image_id_one, b.image_id_two, b.image_id_three, b.publish_time, user.id as user_id, user.name as user_name, bc.id as book_category_id, bc.name as book_category_name, image.thumbnail_url as thumbnail_url FROM {$this->table} b INNER JOIN book_category bc ON b.book_category_id = bc.id INNER JOIN user ON b.user_id = user.id INNER JOIN image ON b.image_id_one = image.id";
        $countSql = "SELECT COUNT(*) FROM {$this->table} b INNER JOIN book_category bc ON b.book_category_id = bc.id INNER JOIN user ON b.user_id = user.id INNER JOIN image ON b.image_id_one = image.id";
        if ($query) {
            $sql = "{$sql} AND {$query}";
            $countSql = "{$countSql} AND {$query}";
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
        return $this->getListOfBooks($pageSize, "user.id={$userId}");
    }

    /**
    * 调出一页特定用户名下的二手书
    * @param String $username 用户名
    * @param int 每页显示数
    * @return 返回二维数组
    */
    public function getBooksByUsername($username, $pageSize=40) {
        return $this->getListOfBooks($pageSize, "user.name='{$username}'");
    }

    /**
    * 通过模糊搜索调出一页二手书
    * @param String $keywords 搜索关键词
    * @param int 每页显示数
    * @return 返回二维数组
    */
    public function getBooksByKeywords($keywords, $pageSize=40) {
        return $this->getListOfBooks($pageSize, "(b.name LIKE '%{$keywords}%' or b.description LIKE '%{$keywords}%')");
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
        if ($img1) {
            $arr["image_id_one"] = $img1;
        }
        if ($img2) {
            $arr["image_id_two"] = $img2;
        }
        if ($img3) {
            $arr["image_id_three"] = $img3;
        }
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
    * 删除二手书 by ID
    * @param id book id
    * @return 成功返回删除数据一维数组，失败返回false
    */
    public function deleteBookById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        $bookCategoryId = $result["book_category_id"];
        $sql = "DELETE FROM {$this->table} WHERE id in ({$id})";
        $bool = $this->sqltool->query($sql);
        if ($bool) {
            $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id in ({$bookCategoryId})) WHERE id in ({$bookCategoryId})";
            $this->sqltool->query($sql);
            return $result;
        }
        return false;
    }
}



?>
