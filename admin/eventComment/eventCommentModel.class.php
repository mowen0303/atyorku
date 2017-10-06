<?php
namespace admin\eventComment;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class EventCommentModel extends Model
{

    /**
     *
     */
    public function addEventComment($event_id,$parent_id,$user_id,$comment)
    {
        $arr = [];
        $arr["event_id"] = $event_id;
        $arr["parent_id"] = $parent_id;
        $arr["user_id"] = $user_id;
        $arr["comment"] = $comment;
        $arr["posting_time"] = time();
        $bool = $this->addRow("event_comment", $arr);
        if ($bool) {
            $sql = "UPDATE event SET count_comments = (SELECT COUNT(*) from event_comment WHERE event_id = {$event_id}) WHERE id = {$event_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    /**
     *
     */
    public function getEventComment($id)
    {
        $sql = "SELECT * from event_comment WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /*
     */
    public function getCommentsByEvent($event_id){
        $sql = "SELECT l_table.*,r_table.* FROM event_comment l_table LEFT JOIN event_comment r_table 
                ON l_table.id = r_table.parent_id WHERE l_table.parent_id = 0 AND l_table.event_id = {$event_id} AND r_table.event_id={$event_id} ORDER BY l_table.id desc";
        $countSql = "SELECT COUNT(l_table.*) FROM event_comment l_table LEFT JOIN event_comment r_table 
                ON l_table.id = r_table.parent_id WHERE l_table.parent_id = 0 AND l_table.event_id = {$event_id} AND r_table.event_id={$event_id} ORDER BY l_table.id desc";
        return $this->getListWithPage("event_comment",$sql.$countSql,20);
    }

    /**
     *
     */

    public function deleteEventComment($id)
    {
        $sql = "SELECT event_id FROM event_comment WHERE id = {$id}";
        $event_id = $this->sqltool->getRowBySql($sql)["event_id"];
        $sql = "DELETE FROM event_comment WHERE parent_id = {$id}";
        $bool = $this->sqltool->query($sql);
        if ($bool) {
            $sql = "DELETE FROM event_comment WHERE id = {$id}";
            $result=$this->sqltool->query($sql);
            $sql = "UPDATE event SET count_comments = (SELECT COUNT(*) from event_comment WHERE event_id = {$event_id}) WHERE id = {$event_id}";
            $this->sqltool->query($sql);
        }
        return $result;
    }


}



?>