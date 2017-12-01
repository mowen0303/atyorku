<?php
namespace admin\comment;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class CommentModel extends Model
{

    /**返回false或者刚插入的评论
     * @return bool | result
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
            $arr["id"] = $this->sqltool->getInsertId();
            return $arr;
        }
        else{
            return false;
        }

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
     *id 可以是个integer也可以是个integer array但是必须是子级评论的ID
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

    /*@param $section_id can be integer or an array of integers
     *@return bool
     */
    public function deleteCommentsBySectionId($section_name,$section_id)
    {
        if (is_array($section_id)){
            $concat = null;
            foreach($section_id as $id){
                $id = $id+0;
                $id = $id.",";
                $concat = $concat.$id;
            }
            $concat = substr($concat,0,-1);
            $sql = "DELETE FROM comment WHERE section_name='{$section_name}' AND section_id in ({$concat})";
            $bool = $this->sqltool->query($sql);
        }

        else {
            $sql = "DELETE FROM comment WHERE section_name = '{$section_name}' AND section_id = {$section_id}";
            $bool = $this->sqltool->query($sql);
        }
        return $bool;
    }

    /*
     * @param id可以是integer或者integer array,但是必须是父级评论的id
     * @return $bool
     */
    public function deleteParentComment($id){
        if (is_array($id)){
            /*抓section_name跟section_id，用于最后更新评论量*/
            $sql = "SELECT section_name,section_id FROM comment WHERE id = {$id[0]}";
            $result = $this->sqltool->getRowBySql($sql);
            $section_name = $result["section_name"];
            $section_id = $result["section_id"];

            $concat = null;
            foreach($id as $i){
                $i = $i+0;
                $i = $i.",";
                $concat = $concat.$i;
            }
            $concat = substr($concat,0,-1);

            /*删除子级评论*/
            $sql = "DELETE from comment WHERE parent_id IN ({$concat})";
            $bool = $this->sqltool->query($sql);
            if ($bool) {
                /*删除父级评论*/
                $sql = "DELETE FROM comment WHERE id IN ({$concat})";
                $bool = $this->sqltool->query($sql);
            }
            else{
                /*删除子级评论失败，返回false*/
                $this->errorMsg = "子评论删除失败";
                return false;
            }
        }

        else{
            /*抓section_name跟section_id,用于最后更新评论量*/
            $sql = "SELECT section_name,section_id FROM comment WHERE id = {$id}";
            $result = $this->sqltool->getRowBySql($sql);
            $section_name = $result["section_name"];
            $section_id = $result["section_id"];

            /*删除子评论*/
            $sql = "DELETE FROM comment WHERE parent_id = {$id}";
            $bool = $this->sqltool->query($sql);
            if ($bool){
                /*删除父级评论*/
                $sql = "DELETE FROM comment WHERE id = {$id}";
                $bool = $this->sqltool->query($sql);
            }
            else{
                /*删除子评论失败，结束并返回false*/
                $this->errorMsg = "子评论删除失败";
                return false;
            }
        }

        /*更新评论量*/
        $sql = "UPDATE {$section_name} SET count_comments = (SELECT COUNT(*) from comment WHERE section_name = '{$section_name}' AND section_id = {$section_id}) WHERE id = {$section_id}";
        $this->sqltool->query($sql);
        if ($bool == false)
            $this->errorMsg = "子评论删除成功,父级评论删除失败";
        return $bool;
    }


}



?>