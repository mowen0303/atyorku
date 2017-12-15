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
     * 通过课程名称查询课程ID,并给课程增加热度
     * @param $parentCode
     * @param $childCode
     * @return int | 查询失败返回 0
     */
    public function getCourseIdByCourseCode($parentCode,$childCode){

        //查询ID
        $sql = "SELECT id FROM course_code WHERE title = '{$childCode}' AND parent_id IN (SELECT id FROM course_code WHERE title = '$parentCode')";
        $row = $this->sqltool->getRowBySql($sql);

        //增加热度
        if($row){
            $sql = "UPDATE course_code SET view_count = view_count+1 WHERE title = '{$parentCode}';UPDATE course_code SET view_count = view_count+1 WHERE id = '{$row[id]}'";
            $this->sqltool->multiQuery($sql);
        }

        return intval($row['id']);

    }


    /**
    * 添加一行Course Code
    * @param title Course Code title
    * @param fullTitle Course Code full title
    * @param credits Course credit
    * @param parentId Course Code parent_id, 如果是父类，无需提供
    * @return bool
    */
    public function addCourseCode($title, $fullTitle, $credits=0, $parentId=0) {
        if ($parentId != 0) {
            $sql = "SELECT * FROM course_code c WHERE c.id={$parentId}";
            $this->sqltool->getRowBySql($sql) or BasicTool::throwException("Course Code父类ID={$parentId} 不存在");
        }
        $sql = "SELECT * FROM {$this->table} WHERE parent_id={$parentId} AND title='{$title}' LIMIT 1";
        !$this->sqltool->getRowBySql($sql) or BasicTool::throwException("Course Code名称={$title} 已存在");
        $arr = array("title"=>$title, "full_title"=>$fullTitle, "credits"=>$credits, "parent_id"=>$parentId);
        return $this->addRow($this->table,$arr);
    }


    /**
    * 通过ID删除一个CourseCode
    * @param id 要删除的Course Code ID
    * @return bool
    */
    public function deleteCourseCodeById($id) {
        $result = $this->getRowById($this->table, $id) or BasicTool::throwException("没有找到 Course Code");
        $parentId = $result["parent_id"];
        if ($parentId == 0) {
            $sql = "SELECT * FROM {$this->table} WHERE parent_id in ({$id}) LIMIT 1";
            !$this->sqltool->getRowBySql($sql) or BasicTool::throwException("存在 Course Code 子类");
        }
        $sql = "DELETE FROM {$this->table} WHERE id in ({$id})";
        return $this->sqltool->query($sql);
    }


    /**
    * 更新 CourseCode Title by ID
    * @param id 要更新的 Course Code ID
    * @param title 要更新的 Course Code Title
    * @param fullTitle 要更新的 Course Code Full Title
    * @param credits 要更新的 Course Code Credits
    * @return bool
    */
    public function updateCourseCodeById($id, $title, $fullTitle, $credits) {
        $result = $this->getRowById($this->table, $id) or BasicTool::throwException("没有找到 Course Code");
        $arr = [];
        $arr["title"] = $title;
        $arr["full_title"] = $fullTitle;
        $arr["credits"] = $credits;
        return $this->updateRowById($this->table, $id, $arr);
    }



}



?>
