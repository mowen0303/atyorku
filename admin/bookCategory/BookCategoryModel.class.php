<?php
namespace admin\bookCategory;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class BookCategoryModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "book_category";
    }

    /**
     * 添加一本书
     * @return $bool
     */
    public function addBookCategory($name)
    {
        $arr = [];
        $arr["name"] = $name;
        return $this->addRow($this->table, $arr);
    }

    /**
     * 查询一本书,返回一维键值数组
     */
    public function getBookCategory($id)
    {
        $sql = "SELECT * from {$this->table} WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    public function getListOfBookCategory($pageSize=20) {
      $sql = "SELECT * FROM {$this->table}";
      $countSql = "SELECT COUNT(*) FROM {$this->table}";
      return $this->getListWithPage($this->table, $sql, $countSql, $pageSize);
    }

    /**
     * 更改一本书
     * @return bool
     */
    public function updateBookCategoryName($id, $name)
    {
        $arr = [];
        $arr["name"] = $name;
        return $this->updateRowById($this->table, $id, $arr);
    }

    public function deleteBookCategory($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        return $this->sqltool->query($sql);
    }

    /**
    * 查看是否二手书类别名称已存在
    * @param $name 新类别名称
    * @param $currentId 当前类别id，用于更新类别名称，新建类别无需提供
    * @return bool
    */
    public function isExistOfBookCategoryName($name,$currentId=false){
        $sql = "SELECT COUNT(*) as amount FROM {$this->table} WHERE name = '{$name}'" . (($currentId)?" AND id<>{$currentId}":"");
        $row = $this->sqltool->getRowBySql($sql);
        if($row['amount']>0){
            return true;
        }
        return false;
    }
}



?>
