<?php
namespace admin\Professor;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class ProfessorModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "professor";
    }

    /**
    * 通过ID获取professor
    * @param id 要查询的Professor ID
    * @return 一维数组
    */
    public function getProfessorById($id) {
        return $this->getRowById($this->table,$id);
    }

    /**
    * 获取多个教授信息
    * @param str 模糊搜索的教授名称
    * @param pageSize 分页功能，每一页query数量
    * @return 二维数组
    */
    public function getListOfProfessor($str=false, $pageSize=false) {
        $sql = "SELECT * FROM {$this->table}";
        $condition = "";
        if ($str)
            $condition = " WHERE firstname LIKE '{$str}%' OR lastname LIKE '{$str}%'";
        $sql .= "{$condition} ORDER BY firstname ASC, lastname ASC, view_count DESC";
        if ($pageSize) {
            $countSql = "SELECT COUNT(*) FROM {$this->table}{$condition}";
            return parent::getListWithPage($this->table, $sql, $countSql, $pageSize);
        }else {
            return $this->sqltool->getListBySql($sql);
        }
    }


    /**
    * 添加一个 professor
    * @param firstname professor firstname
    * @param lastname professor lastname
    * @param middlename professor middlename
    * @return bool
    * @throws unique_name_exception 教授名称唯一
    */
    public function addProfessor($firstname, $lastname, $middlename=false) {
        $firstname = ucwords($firstname);
        $lastname = ucwords($lastname);
        $middlename = ucwords($middlename);
        $sql = "SELECT * FROM {$this->table} WHERE firstname='{$firstname}' AND lastname='{$lastname}' LIMIT 1";
        !$this->sqltool->getRowBySql($sql) or BasicTool::throwException("professor名称:{$firstname} {$lastname} 已存在");
        $arr = array("firstname"=>$firstname,"lastname"=>$lastname,"middlename"=>$middlename);
        return $this->addRow($this->table, $arr);
    }

    /**
    * 更新一个 professor
    * @param id 要更新的 professor id
    * @param firstname professor firstname
    * @param lastname professor lastname
    * @param middlename professor middlename
    * @return bool
    * @throws id_not_found_exception 教授ID未找到
    * @throws unique_name_exception 教授名称唯一
    */
    public function updateProfessor($id, $firstname, $lastname, $middlename=false) {
        $result = $this->checkProfId($id);
        $firstname = ucwords($firstname);
        $lastname = ucwords($lastname);
        $middlename = ucwords($middlename);
        if ($result["firstname"] != $firstname || $result["lastname"] != $lastname) {
            // 姓名变化
            $sql = "SELECT * FROM {$this->table} WHERE firstname='{$firstname}' AND lastname='{$lastname}' LIMIT 1";
            !$this->sqltool->getRowBySql($sql) or BasicTool::throwException("professor名称:{$firstname} {$lastname} 已存在");
        }

        $arr = array("firstname"=>$firstname,"lastname"=>$lastname,"middlename"=>$middlename);
        return $this->updateRowById($this->table, $id, $arr);
    }

    /**
    * 通过ID删除一个 Professor
    * @param id 要删除的 professor ID
    * @return bool
    * @throws id_not_found_exception 教授ID未找到
    */
    public function deleteProfessorById($id) {
        $this->checkProfId($id);
        $sql = "DELETE FROM {$this->table} WHERE id in ({$id})";
        return $this->sqltool->query($sql);
    }


    /**
    * 教授热度 +1
    * @param id 教授ID
    * @return bool
    * @throws id_not_found_exception 教授ID未找到
    */
    public function incrementProfessorViewCountById($id) {
        $result = $this->checkProfId($id);
        $count = $result["view_count"]+1;
        return $this->updateRowById($this->table, $id, array("view_count"=>$count));
    }

    /**
    * 查看Professor ID是否存在
    *
    * @param id 要查看的 Professor ID
    * @return 一维数组
    * @throws id_not_found_exception 教授ID未找到
    */
    private function checkProfId($id) {
        $result = $this->getRowById($this->table, $id) or BasicTool::throwException("没找到 professor ID: {$id}");
        return $result;
    }
}



?>
