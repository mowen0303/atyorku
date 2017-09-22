<?php
namespace admin\advertise;   //-- 注意 --//
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class adModel extends Model
{
    /*
     * forum_class        | id | title | description | is_del |
     */
    public function getListOfAd ($pageSize = 40)
    {
        $table = 'advertisement';
        $sql = "SELECT * FROM {$table} where is_del=0";
        $countSql = null;
        return parent::getListWithPage($table,$sql,$countSql, $pageSize);   //-- 注意 --//

    }


    public function getListWithUserExp($pageSize = 40)
    {
        $date = getdate();

        $table = 'advertisement';
        $sql = "SELECT a.*,u.alias,u.gender FROM `advertisement` as a left join `user` as u ON a.user_id = u.id WHERE a.is_del = 0 && a.expiredate<={$date[0]} ORDER BY `datetopost` DESC";
        $countSql = null;
        return parent::getListWithPage($table,$sql,$countSql, $pageSize);

    }
    public function getListWithUser ($pageSize = 40)
    {
        $date = getdate();
        $table = 'advertisement';
        $sql = "SELECT a.*,u.alias,u.gender FROM `advertisement` as a left join `user` as u ON a.user_id = u.id WHERE a.is_del = 0 && a.expiredate>={$date[0]} ORDER BY `datetopost` DESC";
        $countSql = null;
        return parent::getListWithPage($table,$sql,$countSql, $pageSize);

    }
    public function getListWithUserDel ($pageSize = 40)
    {
        $date = getdate();
        $table = 'advertisement';
        $sql = "SELECT a.*,u.alias,u.gender FROM `advertisement` as a left join `user` as u ON a.user_id = u.id WHERE a.is_del = 1  ORDER BY `datetopost` DESC";
        $countSql = null;
        return parent::getListWithPage($table,$sql,$countSql, $pageSize);

    }
    public function getListByAdId($id)
    {
        $sql = "SELECT * FROM `advertisement`  WHERE is_del = 0 && id = {$id}";
        return $this->sqltool->getRowBySql($sql);
    }


    public function getListOfForumCommentByForumId($forumId,$pageSize=30)
    {
        $table = 'forum_comment';
        $sql = "SELECT f_c.*,u.img,u.alias,u.gender FROM `forum_comment` AS f_c LEFT JOIN `user` AS u ON f_c.user_id = u.id WHERE f_c.`forum_id` = {$forumId} ORDER BY 'time' DESC";
        $countSql = "SELECT count(*) FROM `forum_comment` AS f_c LEFT JOIN `user` AS u ON f_c.user_id = u.id WHERE f_c.`forum_id` = {$forumId}";
        $arr =  parent::getListWithPage($table,$sql,$countSql, $pageSize);   //-- 注意 --//

        $currentUser = new UserModel();
        foreach($arr as $k1 => $v1) {

            foreach($v1 as $k2 => $v2){

                if($k2=="time"){
                    $arr[$k1][$k2] = BasicTool::translateTime($v2);
                }

                if($k2 == "user_id"){

                    if($currentUser->isAdminLogin()&&$currentUser->getUserAuthority('forum')){

                        $arr[$k1]['editable'] = "yes";

                    }else{

                        if($v2 == $currentUser->userId){
                            $arr[$k1]['editable'] = "yes";
                        }else{
                            $arr[$k1]['editable'] = "no";
                        }
                    }
                }

            }
        }

        return $arr;
    }


    public function getRowOfForumClassById($id)
    {
        // id|title|is_del|description|
        $sql = "SELECT f_c.* FROM `forum_class` AS f_c WHERE f_c.id in ({$id})";
        return $this->sqltool->getRowBySql($sql);
    }


    public function getRowOfForumById($id)
    {

        $sql = " select F.*,U.alias from `forum` as F LEFT JOIN `user` as U on F.user_id = U.id WHERE F.id in ({$id});";
        return $this->sqltool->getRowBySql($sql);
    }


    public function getOneRowOfForumById($id)
    {

        $sql = " select F.*,u.img,u.alias,u.gender from `forum` as F left join `user` as u on F.user_id = u.id WHERE F.id in ({$id});";
        return $this->sqltool->getRowBySql($sql);
    }


    public function addLikeByForumId($id)
    {
        $sql = "UPDATE forum SET `like`=`like`+1 WHERE id IN ({$id})";
        $this->sqltool->mysqli->query($sql);
        if($this->sqltool->mysqli->error)
        {
            BasicTool::echoMessage("sql语法错".$this->sqltool->mysqli->error.$sql);
        } elseif($this->sqltool->getAffectedRows()>0)
        {
            return true;
        }
    }


    public function addCommentNumByForumId($id)
    {
        $sql = "UPDATE forum SET comment_num = comment_num+1 WHERE id IN ({$id});";
        $this->sqltool->mysqli->query($sql);
        if($this->sqltool->mysqli->error)
        {
            BasicTool::echoMessage("sql语法错".$this->sqltool->mysqli->error.$sql);
        } elseif($this->sqltool->getAffectedRows()>0)
        {
            return true;
        }
    }


    public function deleteCommentNumByForumId($id)
    {
        $sql = "UPDATE forum SET comment_num = comment_num-1 WHERE id IN ({$id});";
        $this->sqltool->mysqli->query($sql);
        if($this->sqltool->mysqli->error)
        {
            BasicTool::echoMessage("sql语法错".$this->sqltool->mysqli->error.$sql);
        } elseif($this->sqltool->getAffectedRows()>0)
        {
            return true;
        }
    }

    public function echoImage($img){

        if($img){
            echo '<img width="40" src="'.$img.'">';
        }

    }


    //根据帖子获取发帖人id
    public function getUserIdOfForumByForumId($forumId){

        if(is_array($forumId)){
            $forumId = $forumId[0];
        }

        $sql = "SELECT user_id FROM forum WHERE id in ($forumId)";

        if($row = $this->sqltool->getRowBySql($sql)){
            return $row['user_id'];
        }else{
            return false;
        }

    }



    public function deleteOneForumById($forumId){

        $currentUser =new UserModel();


        if(!($currentUser->isAdminLogin() && $currentUser->getUserAuthority('forum') && $currentUser->getUserAuthority('delete'))){
            //如果用户不是管理员, 检查数据是否是用户发的
            $currentUser->userId == $this->getUserIdOfForumByForumId($forumId) or BasicTool::throwException("无权删除其他人的留言");
        }

        $sql = "SELECT img1 FROM forum WHERE id IN ({$forumId})";
        $row = $this->sqltool->getRowBySql($sql);
        unlink($_SERVER["DOCUMENT_ROOT"].$row['img1']);

        $sql = "DELETE FROM forum WHERE id IN ({$forumId})";
        $this->sqltool->query($sql);
        if($this->sqltool->getAffectedRows()>0){
            return true;
        }
        return false;
    }
    public function autoIncreasedReadCounter($id)
    {
        $sql = "UPDATE advertisement SET count = count+1 WHERE id IN ({$id});";
        $this->sqltool->mysqli->query($sql);
        if($this->sqltool->mysqli->error)
        {
            BasicTool::echoMessage("SQL语法错误:".$this->sqltool->mysqli->error."<==SQL==>".$sql);
        } elseif($this->sqltool->getAffectedRows()>0)
        {
            return true;
        }
    }




}