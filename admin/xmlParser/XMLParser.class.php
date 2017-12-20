<?php
namespace admin\xmlParser;   //-- 注意 --//

use \Model as Model;

class XMLParser extends Model
{
    public function insert($table, $arr, $debug = false)
    {
        $field = "";
        $value = "";
        foreach ($arr as $k => $v) {
            $v = addslashes($v);
            $field .= $k . ",";
            $value .= "'$v'" . ",";
        }
        $field = substr($field, 0, -1);
        $value = substr($value, 0, -1);
        $sql = "insert into {$table} ({$field}) values ({$value})";
        echo $debug ? $sql : null;
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            $this->idOfInsert = $this->sqltool->getInsertId();
            return true;
        }
        $this->errorMsg = "没有数据受到影响";
        return false;
    }
}

?>