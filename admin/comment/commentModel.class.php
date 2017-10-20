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
            $sql = "UPDATE {$section_name} SET count_comments = (SELECT COUNT(*) from comment WHERE section_name = {$section_name} AND section_id = {$section_id}) WHERE id = {$section_id}";
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
        $sql = "SELECT l_table.*,r_table.* FROM comment l_table LEFT JOIN comment r_table 
                ON l_table.id = r_table.parent_id WHERE l_table.parent_id = 0 AND l_table.section_id = {$section_id} AND r_table.section_name={$section_name} ORDER BY l_table.id desc";

        $countSql = "SELECT COUNT(l_table.*) FROM comment l_table LEFT JOIN comment r_table 
                ON l_table.id = r_table.parent_id WHERE l_table.parent_id = 0 AND l_table.section_id = {$section_id} AND r_table.section_name={$section_name} ORDER BY l_table.id desc";
        return $this->getListWithPage("comment",$sql.$countSql,20);
    }

    /**
     *
     */

    public function deleteComment($id)
    {
        $sql = "SELECT section_name,section_id FROM comment WHERE id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        $section_name = $result["section_name"];
        $section_id = $result["section_id"];
        $sql = "DELETE FROM comment WHERE id = {$id}";
        $bool = $this->sqltool->query($sql);
        if ($bool) {
            $sql = "UPDATE {$section_name} SET count_comments = (SELECT COUNT(*) from comment WHERE section_name = {$section_name} AND section_id = {$section_id}) WHERE id = {$section_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    public function deleteCommentsBySection($section_name,$section_id)
    {
        $sql = "DELETE FROM comment WHERE section_name = {$section_name} AND section_id = {$section_id}";
        $bool = $this->sqltool->query($sql);
        return $bool;
    }
}



?>