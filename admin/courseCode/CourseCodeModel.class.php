<?php
namespace admin\courseCode;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class CourseCodeModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "course_code";
    }

    /**
    * 通过ID获取Course Code
    * @param id 要查询的CourseCode ID
    * @return 一维数组
    */
    public function getCourseCodeById($id) {
        return $this->getRowById($this->table,$id);
    }

    /**
    * 通过 Parent ID 来获取 Course code, 获取父类Course Code, $id=0
    * @param id course code parent id
    * @return 一维数组
    */
    public function getListOfCourseCodeByParentId($id=0) {
        $sql = "SELECT * FROM course_code c WHERE c.parent_id={$id}";
        return $this->sqltool->getListBySql($sql);
    }


    /**
    * 添加一行Course Code
    * @param title Course Code title
    * @param parentId Course Code parent_id, 如果是父类，无需提供
    * @return bool
    */
    public function addCourseCode($title, $parentId=0) {
        if ($parentId != 0) {
            $sql = "SELECT * FROM course_code c WHERE c.parent_id={$parentId}";
            $this->sqltool->query($sql) or BasicTool::throwException("Course Code父类ID={$parentId} 不存在");
        }
        $arr = array("title"=>$title, "parent_id"=>$parentId);
        return $this->addRow($this->table,$arr);
    }


    /**
    * 通过ID删除一个CourseCode
    * @param id 要删除的Course Code ID
    * @return bool
    */
    public function removeCourseCodeById($id) {
        $result = $this->getRowById($this->table, $id) or BasicTool::throwException("没有找到 Course Code");
        $parentId = $result["parent_id"];
        if ($parentId == 0) {
            $sql = "DELETE FROM {$this->table} WHERE id in {$id} AND NOT EXISTS (SELECT * FROM {$this->table} WHERE parent_id={$id})";
            return $this->sqltool->query($sql);
        } else {
            $sql = "DELETE FROM {$this->table} WHERE id in {$id}";
            return $this->sqltool->query($sql);
        }

    }

}



?>
