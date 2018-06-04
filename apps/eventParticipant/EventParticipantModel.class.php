<?php
namespace apps\eventParticipant;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class EventParticipantModel extends Model
{

    /**
     * @param int $event_id 活动id
     * @param int $user_id 用户id
     * @return $bool
     */
    public function addEventParticipant($event_id,$user_id)
    {
        $arr = [];
        $arr["event_id"] = $event_id;
        $arr["user_id"] = $user_id;
        $arr["register_time"] = time();
        $sql = "SELECT * FROM event_participant WHERE user_id = {$user_id}";
        !$this->sqltool->getRowBySql($sql) or BasicTool::throwException("参与失败,该用户已参与");
        $bool = $this->addRow("event_participant", $arr);
        if ($bool) {
            $sql = "UPDATE event SET count_participants = (SELECT COUNT(*) from event_participant WHERE event_id = {$event_id}) WHERE id = {$event_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    public function getEventParticipant($id)
    {
        $sql = "SELECT * from event_participant WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /**调出一个活动下的所有参与者
     * @param int $event_id 活动id
     * @return array
     */
    public function getEventParticipantsByEvent($event_id){
        $sql = "SELECT * FROM event_participant WHERE event_id = {$event_id} ";
        $countSql = "SELECT * FROM event_participant WHERE event_id = {$event_id}";
        return $this->getListWithPage("event", $sql, $countSql, 20);
    }

    /**删除活动参与人
     * $param int $id eventparticipant_id
     * @return bool
     */
    public function deleteEventParticipant($id)
    {
        if (is_array($id)){
            $sql = "SELECT * FROM event_participant WHERE id = {$id[0]}";
        }
        else{
            $sql = "SELECT * FROM event_participant WHERE id = {$id}";
        }
        $event_id = $this->sqltool->getRowBySql($sql)["event_id"];

        $bool = $this->realDeleteByFieldIn("event_participant","id",$id);
        if ($bool) {
            $sql = "UPDATE event SET count_participants = (SELECT COUNT(*) from event_participant WHERE event_id = {$event_id}) WHERE id = {$event_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }
}
?>