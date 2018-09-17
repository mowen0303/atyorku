<?php
namespace admin\employeeKPI;   //-- 注意 --//
use \Model as Model;



class EmployeeKPIModel extends Model {

    private static $table_name = "employee_kpi";
    private static $pk = "id";
    private static $c_time = "created_time";
    private static $lu_time = "last_updated_time";
    private static $main_uid = "main_user_id";
    private static $uids = "user_ids";
    private static $pageSize = 30;
    private static $count_accounts = "count_accounts";

    public function getEmployeeKPIProfiles($id=false){
        $condition = "true";
        if ($id) $condition .= " AND {$this::$pk} IN ($id)";
        $sql = "SELECT {$this::$table_name}.*,user.img,user.name,user.alias,user.gender FROM (SELECT * FROM {$this::$table_name} WHERE {$condition}) AS {$this::$table_name} INNER JOIN user ON {$this::$table_name}.{$this::$main_uid}=user.id ORDER BY {$this::$table_name}.{$this::$lu_time} DESC";
        $countSql = "SELECT COUNT(*) FROM (SELECT * FROM {$this::$table_name} WHERE {$condition}) AS {$this::$table_name} INNER JOIN user ON {$this::$table_name}.{$this::$main_uid}=user.id ORDER BY {$this::$table_name}.{$this::$lu_time} DESC";
        $result = $this->getListWithPage(self::$table_name,$sql,$countSql,self::$pageSize);
        foreach($result as $index => $row){
            $arr = self::convertUIdsStringToArray($row[self::$uids]);
            $result[$index][self::$count_accounts] = count($arr);
            $result[$index][self::$uids] = $arr;
        }

        return $result;
    }

    public function getEmployeeKPIProfileByMainUserId($main_uid){
        $sql = "SELECT {$this::$pk} FROM {$this::$table_name} WHERE {$this::$main_uid} IN ({$main_uid})";
        $id = $this->sqltool->getRowBySql($sql);
        if (!$id)
            return false;
        else
            return $this->getEmployeeKPIProfiles($id);
    }

    public function insertOrUpdate($main_uid,$uids,$id=false){
        $uids = self::convertArrayToString($uids);

        if ($uids === false){
            $this->errorMsg = $id ? "更改失败:uids必须是个一位数组" : "添加失败:uids必须是个一位数组";
            return false;
        }

        if (self::convertUIdsStringToArray($uids) === false){
            $this->errorMsg = $id ? "更改失败:uids格式错误" : "添加失败:uids格式错误";
            return false;
        }

        if ($id){
            $arr = array(self::$main_uid => $main_uid, self::$uids => $uids, self::$lu_time => time());
            $bool = $this->updateRowById(self::$table_name,$id,$arr);
        }else{
            $arr = array(self::$main_uid => $main_uid, self::$uids => $uids, self::$lu_time => time(), self::$c_time => time());
            $bool = $this->addRow(self::$table_name, $arr);
        }
        if (!$bool){
            $this->errorMsg = $id ? "数据库操作失败:更改失败" : "数据库操作失败:添加失败";
            return false;
        }
        return true;
    }

    public function deleteEmployeeKPIProfileById($id){
        $bool = $this->realDeleteByFieldIn(self::$table_name,self::$pk,$id);
        if (!$bool){
            $this->errorMsg = "数据库操作失败:删除失败";
            return false;
        }
        return true;
    }

    public function getRowIdByMainUserId($main_uid){
        $sql = "SELECT id FROM {$this::$table_name} WHERE {$this::$main_uid} IN ($main_uid)";
        $res = $this->sqltool->getRowBySql($sql);
        if (!$res["id"])
            return false;
        return $res["id"];
    }

    public static function convertArrayToString($arr){
        if (is_array($arr)){
            $concat = "";
            foreach ($arr as $item){
                $concat .= $item.",";
            }
            return $concat ? substr($concat, 0, -1) : "";
        }else{
            return false;
        }
    }

    public static function convertUIdsStringToArray($uid_string){
        if (!$uid_string)
            return [];
        $result = explode(",",$uid_string);
        $bool = true;
        foreach ($result as $item){
            $str = (string) $item;
            $bool = $bool && ctype_digit($str);
        }
        if ($bool)
            return $result;
        else
            return false;
    }


}


?>
