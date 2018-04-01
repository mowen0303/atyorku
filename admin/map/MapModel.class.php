<?php
namespace admin\map;
use \Model as Model;

class MapModel extends Model {

    const VERSION_FILE = "snippet/version.json";

    /**
     * 获取大楼数据版本号
     * @return bool|mixed
     */
    public function getMapDataVersion() {
        $versionJSON = json_decode(file_get_contents(self::VERSION_FILE));
        if ($versionJSON->version) {
            return $versionJSON;
        } else {
            $this->errorMsg = "获取版本号失败";
            return false;
        }
    }

    public function changeMapDataVersion(){
        $versionJSON = json_decode(file_get_contents(self::VERSION_FILE));
        if ($versionJSON->version) {
            $versionJSON->version+=1;
            $versionJSON->time = date("Y-m-d H:m:s");
            if(file_put_contents(self::VERSION_FILE,json_encode($versionJSON))){
                return true;
            }else{
                $this->errorMsg = "地图版本更新失败";
                return false;
            }
        } else {
            $this->errorMsg = "获取版本号失败";
            return false;
        }
    }

    /**
     * 获取所有大楼
     * @return array|bool
     */
    public function getAllBuildings(){
        $sql = "SELECT * FROM map ORDER BY id DESC";
        $result = $this->getListWithPage("map",$sql,null,500);
        if($result){
            return $result;
        }else{
            $this->errorMsg="没有查到任何数据";
            return false;
        }
    }

    public function getBuildingByID($id){
        $sql = "SELECT * FROM map WHERE id in ({$id})";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * 添加/编辑 大楼数据
     * @param $id               如果id无值，则添加一个新楼，有值则更新数据
     * @param $code             大楼的
     * @param $abbreviation
     * @param $fullName
     * @param $description
     * @param $coordinates
     * @return bool
     */
    public function editBuilding($id,$code,$abbreviation,$fullName,$description,$coordinates){
        $description = $description?:"暂无简介";
        $arr['code'] = $code;
        $arr['abbreviation'] = $abbreviation?strtoupper($abbreviation):"";
        $arr['full_name'] = ucwords($fullName);
        $arr['description'] = $description;
        $arr['coordinates'] = $coordinates;
        if($id){
            return $this->updateRowById('map', $id, $arr);
        }else{
            return $this->addRow('map', $arr);
        }
    }


    /**
     * 删除一条或多条大楼数据
     * @param $IDs                      支持数组或单值，如果是一个id数组，则删掉多条
     * @return bool|\mysqli_result
     */
    public function deleteBuildingByIDs($IDs){
         if(is_array($IDs)){
             $IDs = implode(",",$IDs);
         }
         $sql = "DELETE FROM map WHERE id IN ({$IDs})";
         return $this->sqltool->query($sql);
    }

    public function checkUniqueByFullName($fullName,$id){
        return $this->isExistByFieldValue("map","full_name",$fullName,$id);
    }

}
