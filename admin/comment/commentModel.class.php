<?php
namespace admin\comment;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class CommentModel extends Model
{

    /**
     *
     */
    public function addComment($parent_id,$sender_id,$receiver_id,$section_name,$section_id,$comment)
    {
        $arr["parent_id"] = $parent_id;
        $arr["sender_id"] = $sender_id;
        $arr["receiver_id"] = $receiver_id;
        $arr["section_name"] = $section_name;
        $arr["section_id"] = $section_id;
        $arr["comment"] = $comment;
        $arr["time"] = time();
        $bool = $this->addRow("comment", $arr);
        if ($bool) {
            $sql = "UPDATE {$section_name} SET count_comments = (SELECT COUNT(*) from comment WHERE section_name = '{$section_name}' AND section_id = {$section_id}) WHERE id = {$section_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    /**
     *
     */
    public function getComment($id)
    {
        $sql = "SELECT * from comment WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        return $result;
    }

    /*
     */
    public function getCommentsBySection($section_name,$section_id){
        $sql = "SELECT l_table.id AS l_id,l_table.parent_id AS l_parent_id,l_table.sender_id AS 
            l_sender_id,l_table.receiver_id AS l_receiver_id,l_table.section_name AS l_section_name,l_table.section_id AS 
            l_section_id,l_table.comment AS l_comment,l_table.time AS l_time,r_table.id AS r_id,r_table.parent_id AS 
            r_parent_id,r_table.sender_id AS r_sender_id, r_table.receiver_id AS r_receiver_id, r_table.section_name AS 
            r_section_name,r_table.section_id AS r_section_id,r_table.comment AS r_comment, r_table.time AS r_time FROM 
            (SELECT * from comment WHERE section_name='{$section_name}' AND section_id={$section_id} AND parent_id=0) l_table LEFT JOIN comment r_table ON l_table.id = r_table.parent_id ORDER BY l_table.id desc,r_table.time desc";

        $countSql = "SELECT count(*) FROM (SELECT * from comment WHERE section_name='{$section_name}' AND parent_id = 0 AND section_id={$section_id}) l_table LEFT JOIN comment r_table ON l_table.id = r_table.parent_id ORDER BY l_table.id desc,r_table.time desc";
        return $this->getListWithPage("comment",$sql,$countSql,20);
    }

    /**
     *id 可以个integer也可以是个array但是必须是子级评论的ID
     */

    public function deleteChildComment($id)
    {
        if (is_array($id))
            $sql = "SELECT section_name,section_id FROM comment WHERE id = {$id[0]}";
        else
            $sql = "SELECT section_name,section_id FROM comment WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        $section_name = $result["section_name"];
        $section_id = $result["section_id"];
        $bool = $this->realDeleteByFieldIn("comment","id",$id);
        if ($bool) {
            $sql = "UPDATE {$section_name} SET count_comments = (SELECT COUNT(*) from comment WHERE section_name = '{$section_name}' AND section_id = {$section_id}) WHERE id = {$section_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    public function deleteCommentsBySection($section_name,$section_id)
    {
        $sql = "DELETE FROM comment WHERE section_name = '{$section_name}' AND section_id = {$section_id}";
        $bool = $this->sqltool->query($sql);
        return $bool;
    }
}



?>