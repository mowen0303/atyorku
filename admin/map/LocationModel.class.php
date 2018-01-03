<?php
namespace admin\map;
use \Model as Model;

class LocationModel extends Model
{
    /**
     * LocationModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->table = "map";
    }

    /**
     * 添加一条大楼信息
     * @param $init varchar 大楼名称缩写（两个或三个大写字母）
     * @param $fullName varchar 大楼全称
     * @param $info varchar 大楼简介
     * @param $lat double 纬度
     * @param $lng double 经度
     * @param $shape varchar TODO
     * @return bool
     */
    public function addLocation($init, $fullName, $info, $lat, $lng, $shape)
    {
        $arr = [];
        $arr["init"] = $init;
        $arr["full_name"] = $fullName;
        $arr["info"] = $info;
        $arr["latitude"] = $lat;
        $arr["longitude"] = $lng;
        $arr["shape"] = $shape;

        $bool = $this->addRow($this->table, $arr);
        return $bool;
    }

    /**
     * 删除大楼信息
     * @param $id int 被删除的大楼的ID
     * @return bool|\mysqli_result
     */
    public function deleteLocation($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        $bool = $this->sqltool->query($sql);
        return $bool;
    }

    /**
     * 更改大楼信息
     * @param $id int *需要更改的大楼的ID
     * @param $init varchar 大楼名称缩写（两个或三个大写字母）
     * @param $fullName varchar 大楼全称
     * @param $info varchar 大楼简介
     * @param $lat double 纬度
     * @param $lng double 经度
     * @param $shape varchar TODO
     * @return bool
     */
    public function updateLocation($id, $init, $fullName, $info, $lat, $lng, $shape)
    {
        $arr = [];
        $arr["id"] = $id;
        $arr["init"] = $init;
        $arr["full_name"] = $fullName;
        $arr["info"] = $info;
        $arr["latitude"] = $lat;
        $arr["longitude"] = $lng;
        $arr["shape"] = $shape;

        $bool = $this->updateRowById($this->table, $id, $arr);
        return $bool;
    }

    /**
     * @return bool|\mysqli_result
     */
    public function getListOfLocation()
    {
        $sql = "SELECT * FROM {$this->table}";

        return $this->sqltool->getListBySql($sql);
    }

    /**
     * 返回大楼信息
     * @param $id int 大楼id
     * @return 大楼条目
     */
    public function getLocationById($id)
    {
        $sql = "SELECT * from {$this->table} WHERE id = {$id}";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * 查找缩写为$init的大楼
     * @param $init varchar 大楼缩写
     * @return 大楼条目
     */
    public function getLocationByInit($init)
    {
        $sql = "SELECT * FROM {$this->table} WHERE init = {$init}";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * 查找全称为$fullName的大楼
     * @oaram $fullName varchar 大楼全称
     * @return 大楼条目
     */
    public function getLocationByFullName($fullName)
    {
        $sql = "SELECT * FROM {$this->table} WHERE full_name = {$fullName}";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * 返回所有*全名*包括$str的大楼
     * @param $str
     * @return \一维关联数组
     */
    public function getLocationsByFullNameKeyword($str) {
        $sql = "SELECT * FROM {$this->table} WHERE full_name LIKE '%{$str}%'";
        return $this->sqltool->query($sql);
    }

    /**
     * 返回所有*缩写*包括$str的大楼
     * @param $str
     * @return \一维关联数组
     */
    public function getLocationsByInitKeyword($str) {
        $sql = "SELECT * FROM {$this->table} WHERE init LIKE '%{$str}%'";
        return $this->sqltool->query();
    }

//    public function getLocationPolygonById($id)

//    public function getLocationPolygonByInit($init)

    /**
     * 删除$id的大楼
     * @param $id
     */
    public function deleteLocationById($id) {
        $this->realDeleteByFieldIn($this->table, 'id', $id) or \BasicTool::throwException("删除大楼失败");
    }
}