<?php
require_once "BasicTool.php";
require_once dirname(__FILE__) . "/../config/database.php";

/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 2017-11-06
 * Time: 10:33 PM
 */
abstract class Model
{
    protected $mysqli = false;

    public function __construct()
    {

        if ($this->mysqli == false) {
            global $dbInfo;
            $this->mysqli = @new MySqli($dbInfo['host'], $dbInfo['user'], $dbInfo['password'], $dbInfo['database']);
            !$this->mysqli->connect_error or die("failed to connect to database");
            $this->mysqli->query("set names utf8");
        }
    }

    /**
     * 向数据库发送一条sql语法, 如果有语法错误, 则终止程序运行并将错误报出
     * @param $sql
     * @return mysqli_result
     */
    public function query($sql)
    {
        $result = $this->mysqli->query($sql);
        if ($result) {
            return $result;
        } else {
            BasicTool::throwException($this->mysqli->error . "==== SQL ERROR ===> " . $sql);
        }
    }

    /**
     *  根据select查询语句, 将多行结果封装成一个一维数组返回
     * @param  $sql
     * @return 一维关联数组 | null
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
     * @return array | null
     */
    public function getListBySql($sql)
    {
        $arr = [];
        $result = $this->query($sql);
        while ($row = $result->fetch_assoc()) {
            $arr[] = $row;
        }
        $result->free();
        return $arr;
    }


}