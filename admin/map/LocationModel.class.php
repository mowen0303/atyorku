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
     * @param $id int 两位数大楼ID
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
//        $arr["id"] = $id;  //
        $arr["init"] = $init;
        $arr["full_name"] = $fullName;
        $arr["info"] = $info;
        $arr["lat"] = $lat;
        $arr["lng"] = $lng;
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
        $arr["lat"] = $lat;
        $arr["lng"] = $lng;
        $arr["shape"] = $shape;

        $bool = $this->updateRowById($this->table, $id, $arr);
        return $bool;
    }

    public function getListOfLocation()
    {
        $sql = "SELECT * FROM {$this->table}";
        $result = $this->sqltool->query($sql);
        return $result;
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
     * 用大楼缩写查询大楼信息
     * @param $init varchar 大楼缩写
     * @return 大楼条目
     */
    public function getLocationByInit($init)
    {
        $sql = "SELECT * from {$this->table} WHERE init = {$init}";
        return $this->sqltool->getRowBySql($sql);
    }

//    public function getLocationPolygonById($id) { /* TODO */ }

//    public function getLocationPolygonByInit($init) { /* TODO */ }

    public function deleteLocationById($id) {
        $this->realDeleteByFieldIn($this->table, 'id', $id) or \BasicTool::throwException("删除大楼失败");
    }
}