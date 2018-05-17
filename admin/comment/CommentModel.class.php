<?php
namespace admin\comment;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

/**
 * 评论Model使用指南：
 * 1. 添加2个字段 count_comments,update_time 到需要使用评论功能的数据库表中
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
        $arr["comment"] = $comment ? addslashes($comment) : "";
        $arr["time"] = time();
        $this->addRow("comment", $arr) or BasicTool::throwException($this->errorMsg);
        //更新统计
        self::updateCountNumber($section_name,$section_id);
        //获取新插入的评论及评论人信息
        $sql = "SELECT comment.*,user_class.is_admin,title FROM (SELECT comment.*, user.id AS uid, user.alias,user_class_id,img,gender,major,enroll_year,degree FROM (SELECT * FROM comment WHERE id = {$this->idOfInsert}) AS comment INNER JOIN user ON comment.sender_id = user.id) AS comment LEFT JOIN user_class ON comment.user_class_id = user_class.id";
        $arr = $this->sqltool->getRowBySql($sql);
        $arr['time'] = BasicTool::translateTime($arr['time']);
        $arr['enroll_year'] = BasicTool::translateEnrollYear($arr['enroll_year']);
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
            $arr[$k]['enroll_year'] = BasicTool::translateEnrollYear($row['enroll_year']);
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
        self::updateCountNumber($section_name,$section_id,'delete');
    }


    /**
     * 根据产品表名和产品id删除评论
     * @param $sectionName
     * @param $sectionId    可以是数组
     * @throws Exception
     */
    public function deleteComment($sectionName,$sectionIds){
        $sectionIds = !is_array($sectionIds)?:implode(",",$sectionIds);
        $sql = "DELETE FROM comment WHERE section_name in ('{$sectionName}') AND section_id in ({$sectionIds})";
        return $this->sqltool->query($sql);
    }


    /**
     * 更新评论数量的统计
     * @param $section_name
     * @param $section_id
     * @return bool|\mysqli_result
     */
    private function updateCountNumber($section_name,$section_id,$actionType='add'){
        $time = time();
        if($actionType=='add'){
            $updateCondition = ",update_time = {$time} ";
        }else{
            $updateCondition = "";
        }
        if($section_name=="forum"){
            $today = BasicTool::getTodayTimestamp();
            $todayStart = $today['startTime'];
            $todayEnd = $today['endTime'];
            $sql = "SELECT COUNT(*) AS count FROM comment WHERE section_id in ({$section_id}) AND time > {$todayStart} AND time < {$todayEnd}";
            $countToday = $this->sqltool->getRowBySql($sql)['count'];
            $sql = "UPDATE {$section_name} SET count_comments = (SELECT COUNT(*) from comment WHERE section_name = '{$section_name}' AND section_id = {$section_id}), count_comments_today = {$countToday} {$updateCondition} WHERE id = {$section_id}";
        }else{
            $sql = "UPDATE {$section_name} SET count_comments = (SELECT COUNT(*) from comment WHERE section_name = '{$section_name}' AND section_id = {$section_id}) {$updateCondition} WHERE id = {$section_id}";
        }

        return $this->sqltool->query($sql);
    }




    /**
     * 转移数据临时---------
     */
    public function transferDataFromForum(){
        //验证权限
        $currentUser = new UserModel();
        $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("无权操作");

        $sql = "SELECT * FROM forum_comment";
        $arr = $this->sqltool->getListBySql($sql);
        $amount = count($arr);
        $i = 0;
        foreach($arr as $row){
            $i++;
            //插入数据
            if($this->instert($row['user_id'],$row['forum_id'],$row['content_comment'],$row['time'])){
                echo "{$i}/{$amount}<br>";
            }
        }
    }
    function instert($sender_id,$section_id,$comment,$time){
        $arr["parent_id"] = 0;
        $arr["sender_id"] = $sender_id;
        $arr["receiver_id"] = 0;
        $arr["section_name"] = 'forum';
        $arr["section_id"] = $section_id;
        $arr["comment"] = addslashes($comment);
        $arr["time"] =  $time;
        $bool = $this->addRow("comment", $arr) or BasicTool::throwException($this->errorMsg);
        if($bool){
            if(self::updateCountNumber2('forum',$section_id)) {
                return true;
            }else{
                echo  "更新帖子信息失败<br>";
            }
        }else{
            echo  "插入失败<br>";
        }
    }
    private function updateCountNumber2($section_name,$section_id){
        $time = time();
        $sql = "UPDATE {$section_name} SET count_comments = (SELECT COUNT(*) from comment WHERE section_name = '{$section_name}' AND section_id = {$section_id}) WHERE id = {$section_id}";
        return $this->sqltool->query($sql);
    }
    /**
     * 转移数据临时---------
     */


}



?>