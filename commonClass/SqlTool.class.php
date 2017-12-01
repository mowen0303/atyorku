<?php

class SqlTool
{

    //保存一个类的实例
    public static $sqltool = false;
    public $mysqli;

    private function __construct()
    {
        global $dbInfo;
        $this->mysqli = @new MySqli($dbInfo['host'], $dbInfo['user'], $dbInfo['password'], $dbInfo['database']);
        if ($this->mysqli->connect_error) {
            BasicTool::echoJson(0, '连接数据库出错');
            exit();
        }
        $this->mysqli->query("set names utf8mb4_unicode_ci");
    }

    /**
     * 返回一个SqlTool类的实例  (这种写法不能降低3306连接数,但是可以省去每个页面多次因操作数据库,反复连接所需要的时间)
     * @return bool|SqlTool
     */
    public static function getSqlTool()
    {
        if (self::$sqltool == false) {
            self::$sqltool = new self();
        }
        return self::$sqltool;
    }
    /**
     * 向数据库发送一条sql语法, 如果有语法错误, 则终止程序运行并将错误报出
     * @param $sql
     * @return bool|mysqli_result
     */
    public function query($sql)
    {
        $result = $this->mysqli->query($sql);
        if ($result) {
            return $result;
        } else {
            BasicTool::echoJson(0, $this->mysqli->error ." <===SQL===> ".$sql);
            exit();
        }
    }

    /**
     * 批量执行
     * @param $sql
     * @return bool
     */
    public function multiQuery($sql)
    {
        $result = $this->mysqli->multi_query($sql);
        if ($result) {
            return $result;
        } else {
            BasicTool::echoJson(0, $this->mysqli->error ." <===SQL===> ".$sql);
            exit();
        }
    }

    /**
     *  根据select查询语句, 将多行结果封装成一个一维数组返回
     * @param  $sql
     * @return 一维关联数组
     */
    public function getRowBySql($sql)
    {
        $result = $this->query($sql);
        $arr = $result->fetch_assoc();
        $result->free();
        return $arr;
    }

    /**
     * 根据select查询语句, 将多行结果封装成一个二维数组返回
     * @param $sql
     * @return array
     */
    public function getListBySql($sql,$debug=0)
    {
        $arr = [];
        $result = $this->query($sql);
        while ($row = $result->fetch_assoc()) {
            $arr[] = $row;
        }
        $result->free();
        echo $debug ? $sql : null;
        return $arr;
    }

    /**
     * 返回某张表中一共多少条数据
     * @param $table
     * @return int
     */
    public function getCountByTable($table)
    {
        $sql = "select count(*) from {$table}";
        $result = $this->query($sql);
        $row = $result->fetch_row();
        $result->free();
        return $row[0];
    }

    public function getCountBySql($sql)
    {
        $result = $this->query($sql);
        $row = $result->fetch_row();
        $result->free();
        return $row[0];
    }

    /**
     * 返回上一次数据库操作所影响的行数
     * @return int
     */
    public function getAffectedRows()
    {
        return $this->mysqli->affected_rows;
    }

    /**
     * 返回上次执行插入所影响的id
     * @return mixed
     */
    public function getInsertId(){
        return $this->mysqli->insert_id;
    }


    /**
     * 检查数据库中是否已经存在某值
     * @param $value  值
     * @param $fieldName   字段名
     * @param $tableName   表名
     * @return bool
     */
    public function isExistBySql($sql)
    {
        $this->query($sql);
        return $this->getAffectedRows()> 0 ? true : false;
    }




}

?>
