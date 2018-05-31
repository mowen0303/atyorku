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
     * 添加一则广告
     * @param int $event_category_id 活动类ID
     * @param String $title 活动标题
     * @param String $description 活动详情
     * @param PHP时间戳 $expiration_time 活动截止时间
     * @param PHP时间戳 $event_time 活动时间
     * @param int $img_id_1
     * @param int $img_id_2
     * @param int $img_id_3
     * @param String $location_link 活动地址,谷歌地图链接
     * @param int $registration_fee 活动费
     * @param int $max_participants 活动名额
     * @param int $sponsor_user_id
     * @param String $sponsor_name
     * @param String $sponsor_telephone
     * @param String $sponsor_wechat
     * @param String $sponsor_email
     * @param int $sort 排序值 0或1
     * @return bool
     */
    public function addEvent($event_category_id,$title,$description,$expiration_time,$event_time,$location,
                             $location_link,$registration_fee,$img_id_1,$img_id_2,$img_id_3,
                             $max_participants,$sponsor_user_id,$sponsor_name,$sponsor_wechat,
                             $sponsor_email,$sponsor_telephone,$sort)
    {
        $arr = [];
        $arr["event_category_id"] = $event_category_id;
        $arr["title"] = $title ? $title : "";
        $arr["description"] = $description ? $description : "";
        $arr["expiration_time"] = $expiration_time ? $expiration_time : 0;
        $arr["publish_time"] = time();
        $arr["event_time"] = $event_time ? $event_time : 0;
        $arr["img_id_1"] = $img_id_1 ? $img_id_1 : 0;
        $arr["img_id_2"] = $img_id_2 ? $img_id_2 : 0;
        $arr["img_id_3"] = $img_id_3 ? $img_id_3 : 0;
        $arr["location"] = $location ? $location : "";
        $arr["location_link"] = $location_link ? $location_link : "";
        $arr["registration_fee"] = $registration_fee ? $registration_fee : 0;
        $arr["max_participants"]=$max_participants ? $max_participants : 0;
        $arr["count_participants"] = 0;
        $arr["count_views"] = 0;
        $arr["count_comments"] = 0;
        $arr["sponsor_user_id"] = $sponsor_user_id;
        $arr["sponsor_name"] = $sponsor_name ? $sponsor_name : "";
        $arr["sponsor_telephone"] = $sponsor_telephone ? $sponsor_telephone : "";
        $arr["sponsor_wechat"] = $sponsor_wechat ? $sponsor_wechat : "";
        $arr["sponsor_email"] = $sponsor_email?$sponsor_email:"";
        $arr["sort"] = $sort ? $sort : 0;
        $bool = $this->addRow("event", $arr);
        if ($bool) {
            $sql = "UPDATE event_category SET count_events = (SELECT COUNT(*) from event WHERE event_category_id = {$event_category_id}) WHERE id = {$event_category_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    public function getEvent($id)
    {
        $sql = "SELECT * from event WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /**
     * 查询一个活动类下的活动
     * @param $event_category_id
     * @param bool $onlyShowEffectEvent 0:显示所有活动 1:只显示即将开始和进行中的活动 2:只显示已经结束的活动
     * @param int $pageSize
     * @return array                    二维数组
     */
    public function getEventsByCategory($event_category_id,$onlyShowEffectEvent=false,$pageSize=20){
        $time = time();
        $condition = "";
        if($event_category_id){
            $condition .= " event_category_id = {$event_category_id}";
        }else{
            $condition .= " true";
        }

        if($onlyShowEffectEvent==1){
            $condition .= " AND expiration_time >= {$time}";
        }else if($onlyShowEffectEvent==2){
            $condition .= " AND expiration_time < {$time}";
        }

        $sql = "SELECT event.*,user.alias,user.img FROM (SELECT event.*,image.url FROM  (SELECT * FROM event WHERE {$condition}) as event LEFT JOIN image ON image.id = event.img_id_1) as event INNER JOIN user ON user.id = event.sponsor_user_id ORDER BY sort DESC, event_time DESC";
        $countSql = "SELECT count(*) FROM (SELECT event.*,image.url FROM  (SELECT * FROM event WHERE {$condition}) as event LEFT JOIN image ON image.id = event.img_id_1) as event INNER JOIN user ON user.id = event.sponsor_user_id ORDER BY sort DESC, event_time DESC";
        return $this->getListWithPage("event", $sql, $countSql, $pageSize);
    }

    /**
     * 更改一则广告
     * @param int $id 活动id
     * @param int $event_category_id 活动类ID
     * @param String $title 活动标题
     * @param String $description 活动详情
     * @param PHP时间戳 $expiration_time 活动截止时间
     * @param PHP时间戳 $event_time 活动时间
     * @param int $img_id_1
     * @param int $img_id_2
     * @param int $img_id_3
     * @param String $location_link 活动地址,谷歌地图链接
     * @param int $registration_fee 活动费
     * @param int $max_participants 活动名额
     * @param int $sponsor_user_id
     * @param String $sponsor_name
     * @param String $sponsor_telephone
     * @param String $sponsor_wechat
     * @param String $sponsor_email
     * @param int $sort 排序值 0或1
     * @return bool
     */
    public function updateEvent($id,$event_category_id,$title,$description,$expiration_time,$event_time,$location,$location_link,
                                $registration_fee,$img_id_1,$img_id_2,$img_id_3,$max_participants,$sponsor_name,$sponsor_wechat,$sponsor_email,$sponsor_telephone,$sort)
    {
        $arr = [];
        $arr["event_category_id"] = $event_category_id;
        $arr["title"] = $title ? $title : "";
        $arr["description"] = $description ? $description : "";
        $arr["expiration_time"] = $expiration_time ? $expiration_time : 0;
        $arr["event_time"] = $event_time ? $event_time : 0;
        $arr["img_id_1"] = $img_id_1 ? $img_id_1 : 0;
        $arr["img_id_2"] = $img_id_2 ? $img_id_2 : 0;
        $arr["img_id_3"] = $img_id_3 ? $img_id_3 : 0;
        $arr["location"] = $location ? $location : "";
        $arr["location_link"] = $location_link ? $location_link : "";
        $arr["registration_fee"] = $registration_fee ? $registration_fee : 0;
        $arr["max_participants"]=$max_participants ? $max_participants : 0;
        $arr["sponsor_name"] = $sponsor_name ? $sponsor_name : "";
        $arr["sponsor_telephone"] = $sponsor_telephone ? $sponsor_telephone : "";
        $arr["sponsor_wechat"] = $sponsor_wechat ? $sponsor_wechat : "";
        $arr["sponsor_email"] = $sponsor_email ? $sponsor_email : "";
        $arr["sort"] = $sort ? $sort : 0;
        $bool = $this->updateRowById("event", $id, $arr);
        if ($bool) {
            $sql = "UPDATE event_category SET count_events = (SELECT COUNT(*) from event WHERE event_category_id = {$event_category_id}) WHERE id = {$event_category_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    /**删除活动
     * @param int|array $id 活动id
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

    /**更新阅读量
     */
    public function addAmountOfRead($id)
    {
        $sql = "UPDATE event SET count_views = count_views + 1 WHERE id = " . $id;
        $this->sqltool->query($sql);
    }
}
?>