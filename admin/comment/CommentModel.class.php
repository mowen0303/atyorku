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
        //验证权限
        $currentUser = new UserModel();
        $currentUser->isUserHasAuthority("COMMENT") or BasicTool::throwException("无权限进行评论");
        //插入数据
        $arr["parent_id"] = $parent_id ? $parent_id : 0;
        $arr["sender_id"] = $sender_id;
        $arr["receiver_id"] = $receiver_id;
        $arr["section_name"] = $section_name;
        $arr["section_id"] = $section_id;
        $arr["comment"] = $comment ? $comment : "";
        $arr["time"] = time();
        $this->addRow("comment", $arr) or BasicTool::throwException($this->errorMsg);
        //更新统计
        self::updateCountNumber($section_name,$section_id);
        //获取新插入的评论及评论人信息
        $sql = "SELECT comment.*, user.id AS uid, user.alias,img,gender,major,enroll_year FROM (SELECT * FROM comment WHERE id = {$this->idOfInsert}) AS comment INNER JOIN user ON comment.sender_id = user.id";
        $arr = $this->sqltool->getRowBySql($sql);
        $arr['time'] = BasicTool::translateTime($arr['time']);
        return $arr;
    }


    /*
     * 获取评论列表
     */
    public function getComments($section_name, $section_id){

        $condition = $section_name == "all" ? "":"AND section_name = '{$section_name}' AND section_id = {$section_id}";
        //父级结果
        $sql = "SELECT comment.*,user_class.title,is_admin FROM (SELECT comment.*,user.id AS uid,user.user_class_id,alias,img,gender,degree,major,enroll_year FROM (SELECT * FROM comment WHERE parent_id = 0 {$condition}) AS comment INNER JOIN user ON comment.sender_id = user.id) AS comment LEFT JOIN user_class ON comment.user_class_id = user_class.id ORDER BY id DESC";
        $countSql = "SELECT COUNT(*) FROM comment WHERE parent_id = 0 {$condition} ORDER BY id DESC";
        $arr = $this->getListWithPage("comment",$sql,$countSql,10);
        //子集结果
        $parentIdArr = [];
        foreach ($arr as $k => $row){
            $arr[$k]['time'] = BasicTool::translateTime($row['time']);
            $arr[$k]['enroll_year'] = BasicTool::translateTime($row['enroll_year']);
            $parentIdArr[] = $row['id'];
        }
        $parentId = implode(",",$parentIdArr);
        if(!$parentId) return $arr;
        $sql = "SELECT comment.*,user.id AS uid,user.alias FROM (SELECT * FROM comment WHERE parent_id in ({$parentId})) AS comment INNER JOIN user ON comment.sender_id = user.id";
        $childArr = $this->sqltool->getListBySql($sql);
        //将子集放到父级中
        foreach ($childArr as $k => $childRow){
            $childRow['time'] = BasicTool::translateTime($childRow['time']);
            $parentId = $childRow['parent_id'];
            foreach ($arr as $k => $parentRow){
                if($parentId == $parentRow['id']){
                    $arr[$k]['child_comment'][] = $childRow;
                }
            }
        }
        return $arr;
    }

    /**
     * 根据comment id删除评论及其子评论
     * @param $commentId
     */
    public function deleteCommentById($commentId){

        //根据ID获取其他信息
        $sql = "SELECT * FROM comment WHERE id in ({$commentId})";
        $commentRow = $this->sqltool->getRowBySql($sql);
        $section_name = $commentRow['section_name'];
        $section_id = $commentRow['section_id'];
        $sender_id = $commentRow['sender_id'];

        //操作权限验证
        $currentUser = new UserModel();
        ($currentUser->isUserHasAuthority("ADMIN") || $currentUser->userId == $sender_id) or BasicTool::throwException("无权删除");

        //删除数据
        $sql ="DELETE FROM comment WHERE id in ({$commentId}) or parent_id in ({$commentId})";
        $this->sqltool->query($sql);
        //更新统计
        self::updateCountNumber($section_name,$section_id);
    }


    /**
     * 更新评论数量的统计
     * @param $section_name
     * @param $section_id
     * @return bool|\mysqli_result
     */
    private function updateCountNumber($section_name,$section_id){
        $sql = "UPDATE {$section_name} SET count_comments = (SELECT COUNT(*) from comment WHERE section_name = '{$section_name}' AND section_id = {$section_id}) WHERE id = {$section_id}";
        return $this->sqltool->query($sql);
    }


}



?>