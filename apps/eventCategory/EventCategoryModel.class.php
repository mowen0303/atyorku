<?php
namespace apps\eventCategory;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class EventCategoryModel extends Model {

    /**添加一则活动分类
     * @param String $title 分类标题
     * @param String $description
     * @return bool
     */
    public function addEventCategory($title,$description){
        $arr = [];
        $arr["description"] = $description ? $description : "";
        $arr["title"] = $title ? $title : "";
        $arr["count_events"] = 0;
        $bool = $this->addRow("event_category",$arr);
        return $bool;
    }

    public function getEventCategory($id){
        $sql = "SELECT * from event_category WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /**调出所有活动分类
     * @return array
     */
    public function getEventCategories(){
        $sql = "SELECT * FROM event_category";
        return $this->sqltool->getListBySql($sql);
    }

    /**更改一个分类
     * @param int $id 分类id
     * @param String $title 分类标题
     * @param String $description
     * @return $bool
     */
    public function updateEventCategory($id,$title,$description){
        $arr = [];
        $arr["title"] = $title ? $title : "";
        $arr["description"] = $description ? $description : "";
        return $this->updateRowById("event_category",$id,$arr);
    }

    /**删除一个分类，确保该分类下没有任何活动
     * @param int $id
     * @return $bool
     */
    public function deleteEventCategory($id){
        $sql = "SELECT count_events FROM event_category WHERE id = {$id}";
        $count_events = $this->sqltool->getRowBySql($sql)["count_events"];
        if($count_events == 0){
            $sql = "DELETE FROM event_category WHERE id = {$id}";
            return $this->sqltool->query($sql);
        }
        else{
            $this->errorMsg = "删除失败,请先删除分类下的所有广告";
            return false;
        }
    }
}



?>