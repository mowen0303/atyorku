<?php
namespace admin\eventParticipant;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class EventParticipant extends Model
{

    /**
     * 添加一个活动参与人
     * need to check if max_pariticipant< count_participant
     * @return $bool
     */
    public function addEvent($event_id,$user_id)
    {
        $arr = [];
        $arr["event_id"] = $event_id;
        $arr["user_id"] = $user_id;
        $arr["register_time"] = time();
        $bool = $this->addRow("event_participant", $arr);
        if ($bool) {
            $sql = "UPDATE event SET count_participants = (SELECT COUNT(*) from event_participant WHERE event_id = {$event_id}) WHERE id = {$event_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    /**
     *
     */
    public function getEventParticipant($id)
    {
        $sql = "SELECT * from event_participant WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /*调出指定活动下的所有参与者
     *
     */
    public function getParticipantsByEvent($event_id){

            $sql = "SELECT * FROM event_participant WHERE event_id = {$event_id} ";
            $countSql = "SELECT * FROM event_participant WHERE event_id = {$event_id}";
            return $this->getListWithPage("event", $sql, $countSql, 20);

    }

    /**
     *
     *
     */
    public function updateEventParticipant($id,$event_id,$user_id)
    {
        $arr = [];
        $arr["user_id"] = $user_id;
        $arr["event_id"] = $event_id;
        $bool = $this->updateRowById("event_participant", $id, $arr);
        return $bool;
    }

    /**
     * 删除一个活动参与人
     * @return bool
     */
    public function deleteEventParticipant($id)
    {
        $sql = "SELECT * FROM event_participant WHERE id = {$id}";
        $event_id = $this->sqltool->getRowBySql($sql)["event_id"];
        $sql = "DELETE FROM event_participant WHERE id = {$id}";
        $bool = $this->sqltool->query($sql);
        if ($bool) {
            $sql = "UPDATE event SET count_participants = (SELECT COUNT(*) from event_participant WHERE event_id = {$event_id}) WHERE id = {$event_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }


}



?>