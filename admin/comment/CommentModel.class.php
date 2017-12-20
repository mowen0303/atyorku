<?php
namespace admin\comment;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

/**
 * 评论Model使用指南：
 * 1. 添加一个字段 count_comments 到需要使用评论功能的数据库表中
 * 2. 使用表名作为 $section_name 的值
 * 3. 使用表中的ID 作为 $section_id 的值
 */

class CommentModel extends Model
{
    /**
     * 添加一条评论，并将整条数据返回，失败则抛出异常
     * @param $parent_id 新回复的parent为0
     * @param $sender_id
     * @param $receiver_id
     * @param $section_name 数据库表名
     * @param $section_id 数据库表里的ID
     * @param $comment
     * @return int
     * @throws Exception
     */
    public function addComment($parent_id,$sender_id,$receiver_id,$section_name,$section_id,$comment)
    {
        $arr["parent_id"] = $parent_id ? $parent_id : 0;
        $arr["sender_id"] = $sender_id;
        $arr["receiver_id"] = $receiver_id;
        $arr["section_name"] = $section_name;
        $arr["section_id"] = $section_id;
        $arr["comment"] = $comment ? $comment : "";
        $arr["time"] = time();
        $this->addRow("comment", $arr) or BasicTool::throwException($this->errorMsg);
        //更新
        $sql = "UPDATE {$section_name} SET count_comments = (SELECT COUNT(*) from comment WHERE section_name = '{$section_name}' AND section_id = {$section_id}) WHERE id = {$section_id}";
        $this->sqltool->query($sql);
        //获取新插入的评论及评论人信息
        $sql = "SELECT comment.*, user.id AS user_id, user.alias AS user_alias, user.gender AS user_gender, user.major AS user_major, user.enroll_year AS user_enroll_year FROM (SELECT * FROM comment WHERE id = {$this->idOfInsert}) AS comment INNER JOIN user ON comment.sender_id = user.id";
        return $this->sqltool->getRowBySql($sql);
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
    public function deleteCommentsBySection($section_name,$section_id)
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