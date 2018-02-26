<?php
namespace admin\map;
use \Model as Model;

class MapModel extends Model {

    /**
     * 获取大楼数据版本号
     * @return bool|mixed
     */
    public function getMapDataVersion() {
        $versionJSON = json_decode(file_get_contents("version.json"));
        if ($versionJSON) {
            return $versionJSON;
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
        $sql = "SELECT * FROM map";
        $result = $this->getListWithPage("map",$sql,null,500);
        if($result){
            return $result;
        }else{
            $this->errorMsg="没有查到任何数据";
            return false;
        }
    }
}