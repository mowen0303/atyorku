<?php
namespace admin\forum;   //-- 注意 --//
use admin\image\ImageModel;
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class ForumModel extends Model {


    /**
     * 重置今日发帖量的统计字段
     * @param $date 表中所记录时间
     * v2.0
     */
    private function resetCountOfToday($today) {
        $currentDate = date("Y-m-d");
        //判断今天是否是新的一天
        if ($today != $currentDate) {
            $sql = "UPDATE forum_class SET today = '{$currentDate}',count_today = 0";
            $this->sqltool->query($sql);
            $sql = "UPDATE forum set count_comments_today = 0";
            $this->sqltool->query($sql);
        }
    }


    public function getIdOfForumOfUserToCommentByUserId($userId) {

        $sql = "SELECT forum_id FROM forum_comment WHERE user_id = {$userId}";

        $arr = $this->sqltool->getListBySql($sql);

        if ($arr) {
            $str = "";
            foreach ($arr as $row) {
                $str .= $row['forum_id'] . ",";
            }
            $str = substr($str, 0, -1);
        }
        if ($str == null) {
            return 0;
        }
        return $str;
    }


    public function getRowOfForumClassById($id) {
        // id|title|is_del|description|
        $sql = "SELECT f_c.* FROM `forum_class` AS f_c WHERE f_c.id in ({$id})";
        return $this->sqltool->getRowBySql($sql);
    }


    public function addLikeByForumId($id) {
        $sql = "UPDATE forum SET `like`=`like`+1 WHERE id IN ({$id})";
        $this->sqltool->query($sql);
    }

    public function countViewOfForumById($forumId) {
        $sql = "UPDATE forum SET count_view = count_view+1 WHERE id in ($forumId)";
        $this->sqltool->query($sql);

    }

    //作弊刷forum阅读量
    public function countViewCheat() {
        $sql = "UPDATE forum SET count_view = count_view+20";
        return $this->sqltool->query($sql);
    }


    public function echoImage($img) {

        if ($img) {
            echo '<img width="40" src="' . $img . '">';
        }

    }


    /**
     * 根据帖子id 获取发帖人id
     * @param $forumId
     * @return bool
     */
    public function getUserIdOfForumByForumId($forumId) {

        if (is_array($forumId)) {
            $forumId = $forumId[0];
        }

        $sql = "SELECT user_id FROM forum WHERE id in ($forumId)";

        if ($row = $this->sqltool->getRowBySql($sql)) {
            return $row['user_id'];
        } else {
            return false;
        }

    }

    /**
     * 根据留言id 获取留言者ID
     * @param $forumId
     * @return bool
     */
    public function getUserIdOfForumCommentByCommentId($commentId) {

        if (is_array($commentId)) {
            $commentId = $commentId[0];
        }


        $sql = "SELECT user_id FROM forum_comment WHERE id in ($commentId)";

        if ($row = $this->sqltool->getRowBySql($sql)) {
            return $row['user_id'];
        } else {
            return false;
        }

    }


    public function deleteOneForumById($forumId) {

        $currentUser = new UserModel();
        $imageModel = new ImageModel();

        //判断是否有权限发帖
        if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('FORUM_DELETE'))) {
            //如果用户不是管理员, 检查数据是否是用户发的
            $currentUser->userId == $this->getUserIdOfForumByForumId($forumId) or BasicTool::throwException("无权删除其他人的帖子");
        }

        $sql = "SELECT img_id_1,img_id_2,img_id_3,img_id_4,img_id_5,img_id_6 FROM forum WHERE id IN ({$forumId})";
        $row = $this->sqltool->getRowBySql($sql);
        //删除图片
        $imageModel->deleteImageById([
            $row['img_id_1'],
            $row['img_id_2'],
            $row['img_id_3'],
            $row['img_id_4'],
            $row['img_id_5'],
            $row['img_id_6']
        ]);
        unlink($_SERVER["DOCUMENT_ROOT"] . $row['img1']);
        //删除forum表内容 和 forum_comment内容
        $sql = "DELETE FROM forum_comment WHERE forum_id IN ({$forumId}); ";
        $this->sqltool->query($sql);
        $sql = "DELETE FROM forum WHERE id IN ({$forumId}); ";
        $this->sqltool->query($sql);

        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        }

        return false;

    }


    public function getForumIdOfCommentId($commentId) {

        $sql = "SELECT forum_id FROM forum_comment WHERE id = $commentId";
        $result = $this->sqltool->getRowBySql($sql);
        return $result['forum_id'];

    }


//    /**
//     * 执行添加和删除操作时, 重新计算留言数量
//     * @param $courseNumberId
//     */
//    public function updateCountData($forumId) {
//
//        //根据forum的id获取forum class id
//        $sql = "SELECT forum_class_id FROM forum WHERE id in ({$forumId})";
//        $row = $this->sqltool->getRowBySql($sql);
//        $forum_class_id = $row['forum_class_id'];
//        //更新forum表的评论数
//        $sql = "UPDATE forum SET comment_num = (SELECT COUNT(id) AS count FROM (SELECT * FROM forum_comment) AS fc WHERE forum_id = {$forumId}) WHERE id = {$forumId};";
//        $this->sqltool->query($sql);
//        //更新forum_class表的总主题数
//        $sql = "UPDATE forum_class SET count_all = (SELECT COUNT(*)  FROM (SELECT * FROM forum) AS f WHERE forum_class_id = {$forum_class_id}) WHERE id = {$forum_class_id}";
//        $this->sqltool->query($sql);
//
//        //更新forum_class表的总帖子数（forum数量+评论数量）
//        $sql = "UPDATE forum_class set count_forum_and_comment = count_all+(SELECT COUNT(*) FROM (select * from forum_comment) as fc WHERE forum_id IN (SELECT id FROM forum WHERE forum_class_id = {$forum_class_id})) WHERE id = {$forum_class_id};";
//        $this->sqltool->query($sql);
//
//        //更新今日发帖量
//        $timeOfMidnight = strtotime(date("Y-m-d"));
//        //获取今日发的帖子数量
//        $sql = "SELECT COUNT(*) AS amount FROM forum WHERE time > {$timeOfMidnight}  AND forum_class_id = {$forum_class_id}";
//        $row = $this->sqltool->getRowBySql($sql);
//        $amount = $row['amount'];
//        //获取今日发的评论的数量
//        $sql = "SELECT COUNT(*) AS amount FROM forum_comment WHERE forum_id in ((SELECT id AS amount FROM forum WHERE time > {$timeOfMidnight}  AND forum_class_id = {$forum_class_id})) AND time > {$timeOfMidnight}";
//        $row = $this->sqltool->getRowBySql($sql);
//        $amount += $row['amount'];
//        $sql = "UPDATE forum_class SET count_today = {$amount} WHERE id = {$forum_class_id}";
//        $this->sqltool->query($sql);
//
//    }

    /**
     * 执行添加和删除操作时, 重新计算留言数量
     * @param $courseNumberId
     */
    public function updateCountData($forumId) {

        //根据forum的id获取forum class id
        $sql = "SELECT forum_class_id FROM forum WHERE id in ({$forumId})";
        $row = $this->sqltool->getRowBySql($sql);
        $forum_class_id = $row['forum_class_id'];

        //更新forum_class表的总主题数
        $sql = "UPDATE forum_class SET count_all = (SELECT COUNT(*) FROM (SELECT * FROM forum) AS f WHERE forum_class_id = {$forum_class_id}) WHERE id = {$forum_class_id}";
        $this->sqltool->query($sql);

        //更新forum_class表的总帖子数（forum数量+评论数量）
        $sql = "UPDATE forum_class set count_forum_and_comment = count_all+(SELECT SUM(count_comments) FROM (SELECT * FROM forum) AS f WHERE forum_class_id = {$forum_class_id}) WHERE id = {$forum_class_id};";
        $this->sqltool->query($sql);

        //更新今日发帖量
        $timeOfMidnight = strtotime(date("Y-m-d"));
        //获取今日发的帖子数量
        $sql = "SELECT COUNT(*) AS amount FROM forum WHERE time > {$timeOfMidnight}  AND forum_class_id = {$forum_class_id}";
        $row = $this->sqltool->getRowBySql($sql);
        $amount = $row['amount'];
        //获取今日发的评论的数量
        $sql = "SELECT SUM(count_comments_today) AS amount FROM forum WHERE forum_class_id in ({$forum_class_id})";
        $row = $this->sqltool->getRowBySql($sql);
        $amount += $row['amount'];

        $sql = "UPDATE forum_class SET count_today = {$amount} WHERE id = {$forum_class_id}";
        $this->sqltool->query($sql);

    }


    /**
     * 举报一个forum
     * @param $forumId
     * @return bool
     */
    public function reportForum($forumId) {

        $sql = "UPDATE forum SET report = report + 1 WHERE id = {$forumId}";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        } else {
            $this->errorMsg = "没有数据受到影响";
            return false;
        }

    }

    public function reportForumComment($forumCommentId) {

        $sql = "UPDATE forum_comment SET report = report + 1 WHERE id = {$forumCommentId}";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        } else {
            $this->errorMsg = "没有数据受到影响";
            return false;
        }

    }

    public function reportedForumRestore($forumId) {
        $sql = "UPDATE forum SET report = 0 WHERE id = {$forumId}";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        } else {
            $this->errorMsg = "没有数据受到影响";
            return false;
        }
    }

    public function reportedForumCommentRestore($forumCommentId) {
        $sql = "UPDATE forum_comment SET report = 0 WHERE id = {$forumCommentId}";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        } else {
            $this->errorMsg = "没有数据受到影响";
            return false;
        }
    }

    /**
     * 获取被举报贴的数量
     * @return int
     */
    public function getAmountOfReport() {

        $sql = "SELECT COUNT(*) as c FROM forum WHERE report > 0";
        $result = $this->sqltool->getRowBySql($sql);
        $count = $result['c'];

        $sql = "SELECT COUNT(*) as c FROM forum_comment WHERE report > 0";
        $result = $this->sqltool->getRowBySql($sql);
        $count += $result['c'];

        return $count;
    }

    public function updateForumTime($forumId) {
        $time = time();
        $sql = "UPDATE forum SET update_time = {$time} WHERE id in ({$forumId})";
        $this->sqltool->query($sql);
    }

    /**
     * --------------------------------------------------------
     * --------------------------------------------------------
     *  -------------------- 2.0 已审查 -----------------------
     * --------------------------------------------------------
     * --------------------------------------------------------
     */

    /**
     * @param boolean $displayHidedForm 是否显示隐藏分类
     * @param int $pageSize 每页展示的条数
     * @return array|bool
     */
    public function getListOfForumClass($displayHidedFormClass) {
        $table = 'forum_class';
        if ($displayHidedFormClass == true) {
            //显示所有分类,包含隐藏分类
            $sql = "SELECT * FROM forum_class WHERE is_del=0 AND display IN (0,1) ORDER BY sort";
        } else {
            //不显示隐藏分类
            $sql = "SELECT * FROM forum_class WHERE is_del=0 AND display IN (1) ORDER BY sort";
        }
        //生成分页代码
        $result = parent::getListWithPage($table, $sql);
        //封装分类集合
        $count_today = 0;
        $count_all = 0;
        $count_forum_and_comment = 0;
        foreach ($result as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                //计算发帖总量
                if ($k2 == "count_today") {
                    $count_today += $v2;
                }
                if ($k2 == "count_all") {
                    $count_all += $v2;
                }
                if ($k2 == "count_forum_and_comment") {
                    $count_forum_and_comment += $v2;
                }
            }
        }
        $all = [];
        $all['id'] = '0';
        $all['title'] = '最新';
        $all['count_today'] = "{$count_today}";
        $all['count_all'] = "{$count_all}";
        $all['count_forum_and_comment'] = "{$count_forum_and_comment}";
        $all['description'] = '最新信息';
        $all['type'] = 'normal';
        $all['icon'] = '/admin/resource/img/icon/f1.png';
        $all['sort'] = '1';
        $all['display'] = '1';
        array_unshift($result, $all);
        if ($result) {
            $this->resetCountOfToday($result[1]['today']);
            return $result;
        } else {
            return false;
        }
    }

    public function getForumImagesByForumIds($ids){
        $sql = "SELECT * from image WHERE id IN ($ids)";
        return $this->sqltool->getListBySql($sql);
    }

    /**
     * @param $forum_classId
     * @param int $pageSize
     * @param bool $onlyShowReportList
     * @param bool $onlyShowForumOfUserByUserId
     * @param bool $onlyShowSpecificForumId
     * @return array
     */
    public function getListOfForumByForumClassId($forum_classId, $pageSize = 20, $onlyShowReportList = false, $onlyShowForumOfUserByUserId = false, $onlyShowSpecificForumId = false, $orderBy = false) {

        $currentUser = new UserModel();
        if ($currentUser->isLogin()) {$currentUser->addActivity();}
        $statisticsModel = new StatisticsModel();
        $statisticsModel->countStatistics(1);
        $table = 'forum';
        $condition = $forum_classId == 0 ? "" : "f.`forum_class_id` in ({$forum_classId}) AND";

        if ($onlyShowReportList == true) {
            $condition .= "f.report > 0 AND";
        }

        if ($onlyShowForumOfUserByUserId !== false) {
            $condition .= " f.user_id = {$onlyShowForumOfUserByUserId} AND ";
        }
        //通过指定的forumid显示
        if ($onlyShowSpecificForumId !== false) {
            $condition .= " f.id IN ({$onlyShowSpecificForumId}) AND ";
        }

        if($orderBy == "countComments"){
            $order = "f.sort DESC,`count_comments` DESC";
        }else{
            $order = "f.sort DESC,`update_time` DESC";
        }

        $sql = "SELECT f.*,fc.id AS classId,fc.title AS classTitle, type AS classType FROM (select f.*,u_c.is_admin,u_c.title as userTitle from (SELECT f.*,u.user_class_id,u.img,u.alias,u.gender,u.major,u.enroll_year,u.degree FROM `forum` AS f INNER JOIN `user` AS u ON f.user_id = u.id WHERE {$condition} u.is_del = 0 ) as f INNER JOIN user_class as u_c ON f.user_class_id = u_c.id) as f INNER JOIN forum_class AS fc ON f.forum_class_id = fc.id ORDER BY {$order}";

        $countSql = "SELECT COUNT(*) FROM (select f.*,u_c.is_admin from (SELECT f.*,u.user_class_id,u.img,u.alias,u.gender,u.major,u.enroll_year,u.degree FROM `forum` AS f INNER JOIN `user` AS u ON f.user_id = u.id WHERE {$condition} u.is_del = 0 ) as f INNER JOIN user_class as u_c ON f.user_class_id = u_c.id) as f INNER JOIN forum_class AS fc ON f.forum_class_id = fc.id ORDER BY f.sort DESC,`update_time` DESC";
        $result = parent::getListWithPage($table, $sql, $countSql, $pageSize);
        $imgIds = [];
        for($i=0;$i<count($result);$i++) {
            $ids[] = $result[$i]['id'];
            $result[$i]['enroll_year'] = BasicTool::translateEnrollYear($result[$i]['enroll_year']);
            $result[$i]['time'] = BasicTool::translateTime($result[$i]['time']);
            if ($result[$i]['img1']) {
                list($result[$i]['img1Width'], $result[$i]['img1Height']) = getimagesize($_SERVER['DOCUMENT_ROOT'] . $result[$i]['img1']);
            }
            $result[$i]['imgs'] = [];
            if ($result[$i]['img_id_1']) $imgIds[] = $result[$i]['img_id_1'];
            if ($result[$i]['img_id_2']) $imgIds[] = $result[$i]['img_id_2'];
            if ($result[$i]['img_id_3']) $imgIds[] = $result[$i]['img_id_3'];
            if ($result[$i]['img_id_4']) $imgIds[] = $result[$i]['img_id_4'];
            if ($result[$i]['img_id_5']) $imgIds[] = $result[$i]['img_id_5'];
            if ($result[$i]['img_id_6']) $imgIds[] = $result[$i]['img_id_6'];
        }
        $imgArr=[];
        if(count($imgIds)>0){
            $imgIds = implode(",",$imgIds);
            $sql = "SELECT * FROM image WHERE id IN ({$imgIds})";
            $imgResult = $this->sqltool->getListBySql($sql);
            foreach($imgResult as $k => $v){
                $imgArr[$v["id"]]=$v;
            }
        }
        if(count($imgArr)>0){
            for($i=0;$i<count($result);$i++) {
                if ($result[$i]['img_id_1']) $result[$i]["imgs"][]=$imgArr[$result[$i]['img_id_1']];
                if ($result[$i]['img_id_2']) $result[$i]["imgs"][]=$imgArr[$result[$i]['img_id_2']];
                if ($result[$i]['img_id_3']) $result[$i]["imgs"][]=$imgArr[$result[$i]['img_id_3']];
                if ($result[$i]['img_id_4']) $result[$i]["imgs"][]=$imgArr[$result[$i]['img_id_4']];
                if ($result[$i]['img_id_5']) $result[$i]["imgs"][]=$imgArr[$result[$i]['img_id_5']];
                if ($result[$i]['img_id_6']) $result[$i]["imgs"][]=$imgArr[$result[$i]['img_id_6']];

            }
            foreach ($imgArr as $img) {

            }
        }

        //增加阅读量
        if (count($ids)>0) {
            $ids = implode(",",$ids);
            if ($_COOKIE["forumViewTime"] == null) {
                $this->countViewOfForumById($ids);
                setcookie("forumViewTime", time(), time() + 600, '/');
            }
        }
        return $result;
    }

    public function getListOfForumCommentByForumId($forumId, $pageSize = 40, $onlyShowReportList = false) {
        $statisticsModel = new StatisticsModel();
        $statisticsModel->countStatistics(1);
        $currentUser = new UserModel();
        if ($currentUser->isLogin()) {
            $currentUser->addActivity();
            $userIsLogin = true;
        }
        $table = 'forum_comment';
        if ($forumId == 0) {
            $condition = "1";
        } else {
            $condition = "f_c.`forum_id` in ({$forumId})";
        }
        if ($onlyShowReportList == true) {
            $condition .= " AND f_c.report > 0";
        }
        $sql = "select f.*,u_c.title as userTitle,u_c.is_admin FROM (SELECT f_c.*,u.img,u.enroll_year,u.major,u.alias,u.gender,u.degree,u.user_class_id FROM `forum_comment` AS f_c INNER JOIN `user` AS u ON f_c.user_id = u.id WHERE {$condition}) as f INNER JOIN user_class as u_c ON f.user_class_id = u_c.id ORDER BY time ASC";
        //$sql = "SELECT f_c.*,u.img,u.enroll_year,u.major,u.alias,u.gender,u.degree FROM `forum_comment` AS f_c INNER JOIN `user` AS u ON f_c.user_id = u.id WHERE {$condition} ORDER BY time ASC";
        $countSql = "SELECT count(*) FROM (SELECT f_c.*,u.img,u.enroll_year,u.major,u.alias,u.gender,u.degree,u.user_class_id FROM `forum_comment` AS f_c INNER JOIN `user` AS u ON f_c.user_id = u.id WHERE {$condition}) as f INNER JOIN user_class as u_c ON f.user_class_id = u_c.id ORDER BY time ASC";
        $arr = parent::getListWithPage($table, $sql, $countSql, $pageSize);   //-- 注意 --//
        foreach ($arr as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                if ($k2 == "time") {
                    $arr[$k1][$k2] = BasicTool::translateTime($v2);
                }
                if ($k2 == "enroll_year") {
                    $arr[$k1][$k2] = BasicTool::translateEnrollYear($v2);
                }
                if ($userIsLogin) {
                    if ($k2 == "user_id") {
                        if (($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('FORUM_DELETE')) || ($v2 == $currentUser->userId)) {
                            $editable = "yes";
                        } else {
                            $editable = 'no';
                        }
                    }
                }
            }
            $arr[$k1]['editable'] = $editable;
        }
        return $arr;
    }


    public function getCommentById($id) {
        //$sql = "SELECT f_c.*,u.img,u.enroll_year,u.major,u.alias,u.gender,u.degree FROM `forum_comment` AS f_c INNER JOIN `user` AS u ON f_c.user_id = u.id WHERE f_c.id = {$id}";
        $sql = "select f.*,u_c.title as userTitle,u_c.is_admin FROM (SELECT f_c.*,u.img,u.enroll_year,u.major,u.alias,u.gender,u.degree,u.user_class_id FROM `forum_comment` AS f_c INNER JOIN `user` AS u ON f_c.user_id = u.id WHERE f_c.id = {$id}) as f INNER JOIN user_class as u_c ON f.user_class_id = u_c.id";
        $result = $this->sqltool->getRowBySql($sql);
        foreach ($result as $k => $v) {
            if ($k == 'time') {
                $result[$k] = BasicTool::translateTime($v);
            }
        }
        return $result;
    }

    /**
     *
     * @param $id
     * @return \一维关联数组
     */
    public function getOneRowOfForumById($id) {
        $this->countViewOfForumById($id);
        $sql = "SELECT F.*,FC.title AS classTitle, type AS classType FROM (select F.*,u.img,u.alias,u.gender,u.major,u.enroll_year from `forum` as F INNER JOIN `user` as u on F.user_id = u.id WHERE F.id in ({$id})) AS F INNER JOIN forum_class AS FC ON F.forum_class_id = FC.id";
        $arr = $this->sqltool->getRowBySql($sql);
        $currentUser = new UserModel();
        $userIsLogin = $currentUser->isLogin();
        foreach ($arr as $k1 => $v1) {
            if ($k1 == "img1") {
                $imgWidth = "";
                $imgHeight = "";
                if ($v1 != null) {
                    $imgSize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $v1);
                    $imgWidth = $imgSize[0];
                    $imgHeight = $imgSize[1];
                }
                $arr['img1Width'] = "{$imgWidth}";
                $arr['img1Height'] = "{$imgHeight}";
            }

            if ($k1 == "time") {
                $arr[$k1] = BasicTool::translateTime($v1);
            }

            if ($k1 == "enroll_year") {
                $arr[$k1] = BasicTool::translateEnrollYear($v1);
            }

            if ($k1 == "user_id" && $userIsLogin) {
                $arr['editable'] = "no";
                if ($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('FORUM_DELETE')) {
                    $arr['editable'] = "yes";
                } else {
                    if (v1 == $currentUser->userId) {
                        $arr['editable'] = "yes";
                    } else {
                        $arr['editable'] = "no";
                    }
                }
            }
        }
        return $arr;
    }

    /**
     * 转换数据 转6张图片
     */
    public function transform(){
        $sql = "SELECT * from forum WHERE img1 <> ''";
        $forumResult = $this->sqltool->getListBySql($sql);
        $amount = count($forumResult);
        $index = 1;
        foreach($forumResult as $forum){
            $sql = "INSERT INTO image (url,thumbnail_url,size,height,width,applied_table,publish_time) VALUE ('{$forum[img1]}','{$forum[img1]}',0,0,0,'forum','{$forum[time]}')";
            $this->sqltool->query($sql);
            $id = $this->sqltool->getInsertId();
            $sql = "UPDATE forum SET img_id_1 = {$id} WHERE id = {$forum[id]}";
            $this->sqltool->query($sql);
            echo "{$index}/{$amount}<br>";
            $index++;
        }

    }
}