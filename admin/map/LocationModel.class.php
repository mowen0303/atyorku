<?php
namespace admin\location;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class LocationModel extends Model
{
    /**
     * LocationModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->table = "building_location";
    }

    /**
     * 添加一条大楼信息
     * @param $id int 两位数大楼ID
     * @param $init char 大楼名称缩写（两个或三个大写字母）
     * @param $fullName String 大楼全称
     * @param $coordinate String 大楼地图坐标
     * @param $info String 大楼简介
     * @return bool
     */
    public function addLocation($id, $init, $fullName, $coordinate, $info)
    {
        $arr = [];
        $arr["id"] = $id;
        $arr["init"] = $init;
        $arr["full_name"] = $fullName;
        $arr["coordinate"] = $coordinate;
        $arr["info"] = $info;

        $bool = $this->addRow($this->table, $arr);
        if ($bool) {
            // TODO - What is this???
            $sql = "TODO";
            $this->sqltool->query($sql);
        }
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
     * @param $init char 大楼名称缩写（两个或三个大写字母）
     * @param $fullName String 大楼全称
     * @param $info String 大楼简介
     * @param $lat double 纬度
     * @param $lng double 经度
     * @return bool
     */
    public function updateLocation($id, $init, $fullName, $info, $lat, $lng)
    {
        $arr = [];
        $arr["id"] = $id;
        $arr["init"] = $init;
        $arr["full_name"] = $fullName;
        $arr["info"] = $info;
        $arr["lat"] = $lat;
        $arr["lng"] = $lng;

        $bool = $this->updateRowById($this->table, $id, $arr);
        return $bool;
    }

    /**
     * 用ID查询大楼信息
     * @param $id 大楼ID
     * @return 大楼条目
     */
    public function getLocationById($id)
    {
        $sql = "SELECT * from {$this->table} WHERE id = {$id}";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * 用大楼缩写查询大楼信息
     * @param $init 大楼缩写
     * @return 大楼条目
     */
    public function getLocationByInit($init)
    {
        $sql = "SELECT * from {$this->table} WHERE init = {$init}";
        return $this->sqltool->getRowBySql($sql);
    }

    public function getLocationPolygonById($id) { /* TODO */ }

    public function getLocationPolygonByInit($init) { /* TODO */ }
}