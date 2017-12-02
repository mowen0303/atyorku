<?php
namespace admin\eventCategory;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class EventCategoryModel extends Model {

    /**
     * 添加一则活动分类
     * @return bool
     */
    public function addEventCategory($title,$description){
        $arr = [];
        $arr["description"] = $description;
        $arr["title"] = $title;
        $arr["count_events"] = 0;
        $bool = $this->addRow("event_category",$arr);
        return $bool;
    }

    /**
     * 查询一则活动分类
     * @return 一维键值数组
     */
    public function getEventCategory($id){
        $sql = "SELECT * from event_category WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /*调出所有活动分类
     * 二维数组返回
     */
    public function getEventCategories(){
        $sql = "SELECT * FROM event_category";
        return $this->sqltool->getListBySql($sql);
    }

    /*更改一个分类
     * @return $bool
     */
    public function updateEventCategory($id,$title,$description){
        $arr = [];
        $arr["title"] = $title;
        $arr["description"] = $description;
        return $this->updateRowById("event_category",$id,$arr);
    }

    /*删除一个分类，确保该分类下没有任何活动
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
            return false;
        }
    }
}



?>