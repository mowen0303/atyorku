<?php
namespace apps\event\event;  //-- 注意 --//
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
     * @param String $detail_url 活动详情链接
     * @param int $registration_fee 活动费
     * @param String $registration_link
     * @param String $registration_way
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
                             $location_link,$detail_url,$registration_fee,$registration_way,$registration_link,$img_id_1,$img_id_2,$img_id_3,
                             $max_participants,$sponsor_user_id,$sponsor_name,$sponsor_wechat,
                             $sponsor_email,$sponsor_telephone,$sort)
    {
        $arr = [];
        $arr["event_category_id"] = $event_category_id;
        $arr["title"] = $title ?: "";
        $arr["description"] = $description ?: "";
        $arr["expiration_time"] = $expiration_time ?: 0;
        $arr["publish_time"] = time();
        $arr["event_time"] = $event_time ?: 0;
        $arr["img_id_1"] = $img_id_1 ?: 0;
        $arr["img_id_2"] = $img_id_2 ?: 0;
        $arr["img_id_3"] = $img_id_3 ?: 0;
        $arr["location"] = $location ?: "";
        $arr["location_link"] = $location_link ?: "";
        $arr["detail_url"] = $detail_url ?: "";
        $arr["registration_fee"] = $registration_fee ?: 0;
        $arr["registration_way"] = $registration_way ?: "";
        $arr["registration_link"] = $registration_link ?: "";
        $arr["max_participants"]=$max_participants ?: 0;
        $arr["count_participants"] = 0;
        $arr["count_clicks"] = 0;
        $arr["count_exhibits"] = 0;
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
        $item = $this->sqltool->getRowBySql($sql);
        $currentTime = time();
        if($currentTime<$item['event_time']){
            //还未开始
            $time = $item['event_time']-$currentTime;
            $day = floor($time/(60*60*24));
            $hour = floor(($time%(60*60*24))/(60*60));
            $minute = floor(($time%(60*60))/60);
            $item['state_code'] = "1";
            if($day){
                $item['state'] = "倒计时:{$day}天";
            }else if($hour){
                $item['state'] = "倒计时:{$hour}小时";
            }else{
                $item['state'] = "倒计时:{$minute}分钟";
            }
        }else if ($currentTime<$item['expiration_time']){
            //进行中
            $item['state'] = "活动进行中";
            $item['state_code'] = "2";
        }else{
            //已结束
            $item['state'] = "已结束";
            $item['state_code'] = "0";
        }
        return $item;
    }

    /**
     * 查询一个活动类下的活动
     * @param $event_category_id
     * @param bool $onlyShowEffectEvent 0:显示所有活动 1:只显示即将开始和进行中的活动 2:只显示已经结束的活动
     * @param int $pageSize
     * @return array                    二维数组
     */
    public function getEventsByCategory($event_category_id,$onlyShowEffectEvent=false,$addExhibitCount=true,$pageSize=20){
        $time = time();
        $condition = "";

        if($event_category_id)
            $condition .= "event_category_id = {$event_category_id}";
        else
            $condition .= "true";

        if ($onlyShowEffectEvent != 2){
            $condition1 = $condition . " AND event_time <= {$time} AND expiration_time > {$time}";
            $order = "diff DESC, CASE WHEN (diff = 1 OR diff = 2) THEN event_time END ASC, CASE WHEN diff = 0 THEN event_time END DESC";
            if ($onlyShowEffectEvent==0)
                $condition2 = $condition . " AND NOT(event_time <= {$time} AND expiration_time > {$time})";
            else
                $condition2 = $condition . " AND event_time > {$time} AND expiration_time >{$time}";

            $innerSql = "SELECT *, 2 AS diff from event WHERE {$condition1} UNION SELECT *, IF(({$time}-CAST(expiration_time as SIGNED))<=0,1,0) AS diff FROM event WHERE {$condition2} ORDER BY {$order}";
        }
        else{
            $order = "event_time DESC";
            $condition .= " AND event_time < {$time} AND expiration_time <= {$time}";
            $innerSql = "SELECT * FROM event WHERE {$condition} ORDER BY {$order}";
        }

        $sql = "SELECT event.*,user.alias,user.img FROM (SELECT event.*,image.url FROM  ({$innerSql}) as event LEFT JOIN image ON image.id = event.img_id_1) as event INNER JOIN user ON user.id = event.sponsor_user_id";
        $countSql = "SELECT count(*) FROM (SELECT event.*,image.url FROM ({$innerSql}) as event LEFT JOIN image ON image.id = event.img_id_1) as event INNER JOIN user ON user.id = event.sponsor_user_id";
        $result = $this->getListWithPage("event", $sql, $countSql, $pageSize);
        $addExhibitCount && $this->addExhibitCount($result);
        return $result;
    }

    private function addExhibitCount($events){
        if (is_array($events) && count($events) > 0){
            $ids = "";
            foreach($events as $event){
                $ids .= $event['id'].",";
            }
            $ids = substr($ids,0,-1);
            $sql = "UPDATE event SET count_exhibits = count_exhibits + 1 WHERE id in ({$ids})";
            $this->sqltool->query($sql);
        }
    }

    /**
     * 更改一则广告
     * @param int $id 活动id
     * @param int $old_event_category_id 活动id
     * @param int $event_category_id 活动类ID
     * @param String $title 活动标题
     * @param String $description 活动详情
     * @param PHP时间戳 $expiration_time 活动截止时间
     * @param PHP时间戳 $event_time 活动时间
     * @param int $img_id_1
     * @param int $img_id_2
     * @param int $img_id_3
     * @param String $location_link 活动地址,谷歌地图链接
     * @param String $detail_url 活动详情链接
     * @param int $registration_fee 活动费
     * @param String $registration_way
     * @param String $registration_link
     * @param int $max_participants 活动名额
     * @param int $sponsor_user_id
     * @param String $sponsor_name
     * @param String $sponsor_telephone
     * @param String $sponsor_wechat
     * @param String $sponsor_email
     * @param int $sort 排序值 0或1
     * @return bool
     */
    public function updateEvent($id, $old_event_category_id, $event_category_id,$title,$description,$expiration_time,$event_time,$location,$location_link, $detail_url,
                                $registration_fee,$registration_way,$registration_link,$img_id_1,$img_id_2,$img_id_3,$max_participants,$sponsor_user_id,$sponsor_name,$sponsor_wechat,$sponsor_email,$sponsor_telephone,$sort)
    {
        $arr = [];
        $arr["event_category_id"] = $event_category_id;
        $arr["title"] = $title ? $title : "";
        $arr["description"] = $description ? $description : "";
        $arr["expiration_time"] = $expiration_time ? $expiration_time : 0;
        $arr["event_time"] = $event_time ? $event_time : 0;
        $arr["img_id_1"] = $img_id_1 ?: 0;
        $arr["img_id_2"] = $img_id_2 ?: 0;
        $arr["img_id_3"] = $img_id_3 ?: 0;
        $arr["location"] = $location ?: "";
        $arr["location_link"] = $location_link ?: "";
        $arr["detail_url"] = $detail_url ?: "";
        $arr["registration_fee"] = $registration_fee ?: 0;
        $arr["registration_way"] = $registration_way ?: "";
        $arr["registration_link"] = $registration_link ?: "";
        $arr["max_participants"]=$max_participants ? $max_participants : 0;
        $arr["sponsor_user_id"] = $sponsor_user_id ? : 1;
        $arr["sponsor_name"] = $sponsor_name ? $sponsor_name : "";
        $arr["sponsor_telephone"] = $sponsor_telephone ? $sponsor_telephone : "";
        $arr["sponsor_wechat"] = $sponsor_wechat ? $sponsor_wechat : "";
        $arr["sponsor_email"] = $sponsor_email ? $sponsor_email : "";
        $arr["sort"] = $sort ? $sort : 0;
        $bool = $this->updateRowById("event", $id, $arr);
        if ($bool) {
            $sql = "UPDATE event_category SET count_events = (SELECT COUNT(*) from event WHERE event_category_id = {$event_category_id}) WHERE id = {$event_category_id}";
            $this->sqltool->query($sql);
            if ($old_event_category_id != $event_category_id){
                $sql = "UPDATE event_category SET count_events = (SELECT COUNT(*) from event WHERE event_category_id = {$old_event_category_id}) WHERE id = {$old_event_category_id}";
                $this->sqltool->query($sql);
            }
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

    /**添加点击量
     */
    public function addClickCount($id)
    {
        $sql = "UPDATE event SET count_clicks = count_clicks + 1 WHERE id in ({$id})";
        return $this->sqltool->query($sql);
    }
}
?>
