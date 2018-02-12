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
     * @param int $id course code parent id
     * @return array 一维数组
     */
    public function getListOfCourseCodeByParentId($id=0) {
        $sql = "SELECT * FROM course_code c WHERE c.parent_id={$id}";
        return $this->sqltool->getListBySql($sql);
    }

    /**
     * 通过字段索引科目类匹配
     * @param $str [ParentTitle ChildTitle] 空格可有可无
     * @param $allowParentOnly 容许单独搜索父类科目
     * @return array
     */
    public function getListOfCourseCodeByString($str, $allowParentOnly=false) {
        if(!$allowParentOnly){
            return $this->getListOfCombinedCourseCodeByString($str);
        }else{
            $sql = "";
            if(!$str) {
                // 无任何输入, 返回空Array
                return [];
//                $sql = "SELECT c1.id, c1.title FROM course_code c1 WHERE c1.parent_id=0 ORDER BY c1.title";
            } else {
                // 分析获取父类子类搜索字段
                $str = trim($str);
                $parentTitle = "";
                $childTitle = "";
                for($i=0;$i<strlen($str);$i++){
                    if(ctype_alpha($str[$i])){
                        $parentTitle .= $str[$i];
                    }else {
                        $childTitle = trim(substr($str,$i));
                        break;
                    }
                }

                if(!$childTitle){
                    // 暂无子类科目输入, 返回匹配的父类科目
                    $sql = "SELECT c1.id, c1.title FROM course_code c1 WHERE c1.parent_id=0 AND c1.title LIKE '{$parentTitle}%' ORDER BY c1.title";
                } else {
                    // 已确定父类字段, 搜索对应子类科目
                    $sql = "SELECT CONCAT(c1.id,'-',c2.id) AS id, CONCAT(c1.title,' ',c2.title) AS title, c1.id AS course_parent_id, c1.title AS course_parent_title, c2.id AS course_child_id, c2.title AS course_child_title FROM course_code c1, course_code c2 WHERE c2.parent_id=c1.id AND c1.title='{$parentTitle}' AND c2.title LIKE '{$childTitle}%' ORDER BY c1.title, c2.title";
                }
            }
            return $this->sqltool->getListBySql($sql);
        }
    }

    /**
     * 通过索引获取一页（40行）科目
     * @param $str 索引字符串
     * @return array 数据结构[0=>[id=>(parentId-childId), title=>(parentTitle childTitle), course_parent_id=>(parent id), course_parent_title=>(parent title), course_child_id=>(child id), course_child_title=>(child title)]
     */
    public function getListOfCombinedCourseCodeByString($str) {
        // 分析获取父类子类搜索字段
        $str = trim($str);
        $parentTitle = "";
        $childTitle = "";
        for($i=0;$i<strlen($str);$i++){
            if(ctype_alpha($str[$i])){
                $parentTitle .= $str[$i];
            }else {
                $childTitle = trim(substr($str,$i));
                break;
            }
        }
        $fullStr = trim($parentTitle." ".$childTitle);
        $sql = "SELECT CONCAT(c1.id,'-',c2.id) AS id, CONCAT(c1.title,' ',c2.title) AS title, c1.id AS course_parent_id, c1.title AS course_parent_title, c2.id AS course_child_id, c2.title AS course_child_title FROM course_code c1, course_code c2 WHERE c2.parent_id=c1.id AND CONCAT(c1.title,' ',c2.title) LIKE '{$fullStr}%' ORDER BY title LIMIT 40";
        return $this->sqltool->getListBySql($sql);
    }


    /**
     * 获取科目ID和缩写（如果子类名称不为空，提供父类和子类的id和缩写）
     * @param $parentCode
     * @param $childCode
     * @return \一维关联数组 [parent_code_id=>1, parent_code_title=>'ADMS', child_code_id=>222, child_code_title=>'1000']
     * @throws
     */
    public function getCourseCodeByString($parentCode, $childCode) {
        $sql = "";
        if($childCode){
            $sql = "SELECT c.id AS course_child_id, c.title AS course_child_title, p.id AS course_parent_id, p.title AS course_parent_title FROM course_code c, course_code p WHERE c.parent_id=p.id AND p.title='{$parentCode}' AND c.title='{$childCode}'";
        } else {
            $sql = "SELECT id AS course_parent_id, title AS course_parent_title FROM course_code WHERE title='{$parentCode}'";
        }
        $result = $this->sqltool->getRowBySql($sql) or BasicTool::throwException("没有匹配的科目");
        return $result;
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
            $sql = "UPDATE course_code SET view_count = view_count+1 WHERE title = '{$parentCode}';";
            $this->sqltool->query($sql);
            $sql = "UPDATE course_code SET view_count = view_count+1 WHERE id = '{$row[id]}'";
            $this->sqltool->query($sql);
        }

        return intval($row['id']);

    }

    /**
     * 通过科目类别名称查询科目类别ID
     * @param $parentCode
     * @return int | 查询失败返回 0
     */
    public function getCourseParentIdByCourseCode($parentCode) {
        $sql = "SELECT id FROM course_code WHERE title='{$parentCode}'";
        $row = $this->sqltool->getRowBySql($sql);

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
