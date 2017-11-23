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
            $condition = " WHERE name LIKE '{$str}%'";
        $sql .= "{$condition} ORDER BY view_count DESC, name ASC";
        if ($pageSize) {
            $countSql = "SELECT COUNT(*) FROM {$this->table}{$condition}";
            return parent::getListWithPage($this->table, $sql, $countSql, $pageSize);
        }else {
            return $this->sqltool->getListBySql($sql);
        }
    }


    /**
    * 添加一个 professor
    * @param name professor name
    * @return bool
    * @throws unique_name_exception 教授名称唯一
    */
    public function addProfessor($name) {
        $sql = "SELECT * FROM {$this->table} WHERE name='{$name}' LIMIT 1";
        !$this->sqltool->getRowBySql($sql) or BasicTool::throwException("professor名称:{$name} 已存在");
        return $this->addRow($this->table, array("name"=>$name));
    }

    /**
    * 更新一个 professor
    * @param id 要更新的 professor id
    * @param name 新的 professor name
    * @return bool
    * @throws id_not_found_exception 教授ID未找到
    * @throws unique_name_exception 教授名称唯一
    */
    public function updateProfessor($id, $name) {
        $result = $this->checkProfId($id);
        $oldName = $result["name"];
        if ($oldName == $name) return true;
        $sql = "SELECT * FROM {$this->table} WHERE name='{$name}' LIMIT 1";
        !$this->sqltool->getRowBySql($sql) or BasicTool::throwException("professor名称:{$name} 已存在");
        return $this->updateRowById($this->table, $id, array("name"=>$name));
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
