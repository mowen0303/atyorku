<?php
namespace admin\event;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;
class EventModel extends Model
{

    /**
     * 添加一则活动
     * @return $bool
     */
    public function addEvent($event_category_id,$title,$description,$expiration_time,$event_time,$location_link,
                             $registration_fee,$img_id_1,$img_id_2,$img_id_3,$max_participants,$sponsor_user_id,$sponsor_name,$sponsor_wechat,$sponsor_email,$sponsor_telephone)
    {
        $arr = [];
        $arr["event_category_id"] = $event_category_id;
        $arr["title"] = $title;
        $arr["description"] = $description;
        $arr["expiration_time"] = $expiration_time;
        $arr["publish_time"] = time();
        $arr["event_time"] = $event_time;
        $arr["img_id_1"]= $img_id_1;
        $arr["img_id_2"]= $img_id_2;
        $arr["img_id_3"]= $img_id_3;
        $arr["location_link"] = $location_link;
        $arr["registration_fee"] = $registration_fee;
        $arr["max_participants"]=$max_participants;
        $arr["count_participants"] = 0;
        $arr["count_views"] = 0;
        $arr["count_comments"] = 0;
        $arr["sponsor_user_id"] = $sponsor_user_id;
        $arr["sponsor_name"] = $sponsor_name;
        $arr["sponsor_telephone"] = $sponsor_telephone;
        $arr["sponsor_wechat"] = $sponsor_wechat;
        $arr["sponsor_email"] = $sponsor_email;
        $bool = $this->addRow("event", $arr);
        if ($bool) {
            $sql = "UPDATE event_category SET count_events = (SELECT COUNT(*) from event WHERE event_category_id = {$event_category_id}) WHERE id = {$event_category_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    /**
     * 查询一则活动，返回一维键值数组
     */
    public function getEvent($id)
    {
        $sql = "SELECT * from event WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /*调出一页广告
     * 返回二维数组
     */
    public function getEventsByCategory($event_category_id,$flag = 1){
        $currentTime = time();

        if ($flag == 1) {
            $sql = "SELECT * FROM event WHERE event_category_id = {$event_category_id} and {$currentTime}>event_time and {$currentTime} <expiration_time ";
            $countSql = "SELECT COUNT(*) FROM event WHERE event_category_id = {$event_category_id} and {$currentTime}>event_time and {$currentTime} <expiration_time";
            return $this->getListWithPage("event", $sql, $countSql, 20);
        }
        else{
            $sql = "SELECT * FROM event WHERE event_category_id = {$event_category_id} and ({$currentTime} < event_time or {$currentTime}>expiration_time)";
            $countSql = "SELECT count(*) FROM event WHERE event_category_id = {$event_category_id} and ({$currentTime} < event_time or {$currentTime}>expiration_time)";
            return $this->getListWithPage("event", $sql, $countSql, 20);
        }
    }

    /**
     * 更改一则活动-----------update count on switching event_category_id---requires further implementation
     *
     * @return bool
     */
    public function updateEvent($id,$event_category_id,$title,$description,$expiration_time,$event_time,$location_link,
                                $registration_fee,$img_id_1,$img_id_2,$img_id_3,$max_participants,$sponsor_user_id,$sponsor_name,$sponsor_wechat,$sponsor_email,$sponsor_telephone)
    {
        $arr = [];
        $arr["event_category_id"] = $event_category_id;
        $arr["title"] = $title;
        $arr["description"] = $description;
        $arr["expiration_time"] = $expiration_time;
        $arr["event_time"] = $event_time;
        $arr["img_id_1"]= $img_id_1;
        $arr["img_id_2"]= $img_id_2;
        $arr["img_id_3"]= $img_id_3;
        $arr["location_link"] = $location_link;
        $arr["registration_fee"] = $registration_fee;
        $arr["max_participants"]=$max_participants;
        $arr["sponsor_user_id"] = $sponsor_user_id;
        $arr["sponsor_name"] = $sponsor_name;
        $arr["sponsor_telephone"] = $sponsor_telephone;
        $arr["sponsor_wechat"] = $sponsor_wechat;
        $arr["sponsor_email"] = $sponsor_email;
        $bool = $this->updateRowById("event", $id, $arr);
        if ($bool) {
            $sql = "UPDATE event_category SET count_events = (SELECT COUNT(*) from event WHERE event_category_id = {$event_category_id}) WHERE id = {$event_category_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    /**
     * 删除活动
     * @id can be an integer or an array of integers
     * @return bool
     */
    public function deleteEvent($id)
    {
        if (is_array($id)){
            $sql = "SELECT * FROM event WHERE id = {$id[0]}";
            $event_category_id = $this->sqltool->getRowBySql($sql)["event_category_id"];
            $concat = null;
            foreach($id as $i){
                $i = $i+0;
                $i = $i.",";
                $concat = $concat.$i;
            }
            $concat = substr($concat,0,-1);
            $sql = "DELETE FROM event WHERE id in ({$concat})";
            $bool = $this->sqltool->query($sql);
        }

        else {
            $sql = "SELECT * FROM event WHERE id = {$id}";
            $event_category_id = $this->sqltool->getRowBySql($sql)["event_category_id"];
            $sql = "DELETE FROM event WHERE id = {$id}";
            $bool = $this->sqltool->query($sql);
        }

        //更新活动数
        if ($bool) {
            $sql = "UPDATE event_category SET count_events = (SELECT COUNT(*) from event WHERE event_category_id = {$event_category_id}) WHERE id = {$event_category_id}";
            $this->sqltool->query($sql);
        }

        return $bool;
    }

    /**
     * @更新阅读量
     */
    public function addAmountOfRead($id)
    {
        $sql = "UPDATE event SET count_views = count_views + 1 WHERE id = " . $id;
        $this->sqltool->query($sql);
    }
}




?>