<?php
namespace admin\lectureAlbumCategory;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class LectureAlbumCategoryModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "lecture_album_category";
    }

    /**
     * 添加一个课程专辑类别
     *
     * @param $title
     * @return bool
     */
    public function addLectureAlbumCategory($title)
    {
        $arr = [];
        $arr["title"] = $title;
        return $this->addRow($this->table, $arr);
    }

    /**
     * 查询一个课程专辑类别,返回一维键值数组
     *
     * @param $id
     * @return \一维关联数组
     */
    public function getLectureAlbumCategory($id)
    {
        $sql = "SELECT * from {$this->table} WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /**
     * 获取全部课程专辑类别
     * @param int $pageSize
     * @return array
     */
    public function getListOfLectureAlbumCategories() {
      $sql = "SELECT * FROM {$this->table}";
      $countSql = "SELECT COUNT(*) FROM {$this->table}";
      return $this->getListWithPage($this->table, $sql, $countSql, 999);
    }

    /**
     * 更改一个课程专辑类别名称
     * @param $id
     * @param $title
     * @return bool
     */
    public function updateLectureAlbumCategoryTitle($id, $title)
    {
        $arr = [];
        $arr["title"] = $title;
        return $this->updateRowById($this->table, $id, $arr);
    }

    /**
     * 删除一个课程专辑类别
     * @param $id
     * @return bool|\mysqli_result
     */
    public function deleteLectureAlbumCategory($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        return $this->sqltool->query($sql);
    }

    /**
     * 查看是否课程专辑类别名称已存在
     *
     * @param string $title 新类别名称
     * @param bool $currentId 当前类别id，用于更新类别名称，新建类别无需提供
     * @return bool
     */
    public function isExistOfLectureAlbumCategoryTitle($title,$currentId=false){
        $sql = "SELECT COUNT(*) as amount FROM {$this->table} WHERE title = '{$title}'" . (($currentId)?" AND id<>{$currentId}":"");
        $row = $this->sqltool->getRowBySql($sql);
        if($row['amount']>0){
            return true;
        }
        return false;
    }

    /**
     * 更新对应课程专辑类别数量
     * @param int|string $id 课程专辑类别ID
     * @return bool|\mysqli_result
     */
    public function updateLectureAlbumCategoryCount($id){
        $id = intval($id);
        $sql = "UPDATE {$this->table} SET count = (SELECT COUNT(*) FROM lecture_album WHERE category_id in ({$id}) AND NOT is_deleted AND is_available) WHERE id in ({$id})";
        return $this->sqltool->query($sql);
    }
}



?>
