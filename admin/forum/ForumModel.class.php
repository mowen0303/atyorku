<?php
namespace admin\forum;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class ForumModel extends Model
{


    /**
     * 重置今日发帖量的统计字段
     * @param $date 表中所记录时间
     * v2.0
     */
    private function resetCountOfToday($today)
    {
        $currentDate = date("Y-m-d");
        //判断今天是否是新的一天
        if ($today != $currentDate) {
            $sql = "UPDATE forum_class SET today = '{$currentDate}',count_today = 0";
            $this->sqltool->query($sql);
        }
    }


    public function getIdOfForumOfUserToCommentByUserId($userId)
    {

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


    public function getRowOfForumClassById($id)
    {
        // id|title|is_del|description|
        $sql = "SELECT f_c.* FROM `forum_class` AS f_c WHERE f_c.id in ({$id})";
        return $this->sqltool->getRowBySql($sql);
    }


    public function getOneRowOfForumById($id)
    {
        $this->countViewOfForumById($id);


        $sql = "SELECT F.*,FC.title AS classTitle, type AS classType FROM (select F.*,u.img,u.alias,u.gender,u.major,u.enroll_year from `forum` as F INNER JOIN `user` as u on F.user_id = u.id WHERE F.id in ({$id})) AS F INNER JOIN forum_class AS FC ON F.forum_class_id = FC.id";

        $arr = $this->sqltool->getRowBySql($sql);

        $currentUser = new UserModel();

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

            if ($k1 == "user_id") {

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


    public function addLikeByForumId($id)
    {
        $sql = "UPDATE forum SET `like`=`like`+1 WHERE id IN ({$id})";
        $this->sqltool->query($sql);
    }

    public function countViewOfForumById($forumId)
    {
        $sql = "UPDATE forum SET count_view = count_view+1 WHERE id in ($forumId)";
        $this->sqltool->query($sql);

    }

    //作弊刷forum阅读量
    public function countViewCheat()
    {
        $sql = "UPDATE forum SET count_view = count_view+20";
        return $this->sqltool->query($sql);
    }


    public function echoImage($img)
    {

        if ($img) {
            echo '<img width="40" src="' . $img . '">';
        }

    }

    /**
     * 获取今日发帖量
     */
    public function updateCountOfToday($classId)
    {
        $timeOfMidnight = strtotime(date("Y-m-d"));
        $sql = "SELECT COUNT(*) AS amount FROM forum WHERE time > {$timeOfMidnight}  AND forum_class_id = {$classId}";
        $row = $this->sqltool->getRowBySql($sql);
        $amount = $row['amount'];
        $sql = "UPDATE forum_class SET count_today = {$amount} WHERE id = {$classId}";
        return $this->sqltool->query($sql);
    }


    public function updateCountOfAll($classId)
    {
        $sql = "SELECT COUNT(*) AS amount FROM forum WHERE forum_class_id = {$classId}";
        $row = $this->sqltool->getRowBySql($sql);
        $amount = $row['amount'];
        $sql = "UPDATE forum_class SET count_all = {$amount} WHERE id = {$classId}";
        return $this->sqltool->query($sql);
    }


    /**
     * 根据帖子id 获取发帖人id
     * @param $forumId
     * @return bool
     */
    public function getUserIdOfForumByForumId($forumId)
    {

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
    public function getUserIdOfForumCommentByCommentId($commentId)
    {

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


    public function deleteOneForumById($forumId)
    {

        $currentUser = new UserModel();

        //判断是否有权限发帖
        if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('FORUM_DELETE'))) {
            //如果用户不是管理员, 检查数据是否是用户发的
            $currentUser->userId == $this->getUserIdOfForumByForumId($forumId) or BasicTool::throwException("无权删除其他人的帖子");
        }

        $sql = "SELECT img1 FROM forum WHERE id IN ({$forumId})";
        $row = $this->sqltool->getRowBySql($sql);
        //删除图片
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


    public function getForumIdOfCommentId($commentId)
    {

        $sql = "SELECT forum_id FROM forum_comment WHERE id = $commentId";
        $result = $this->sqltool->getRowBySql($sql);
        return $result['forum_id'];

    }


    /**
     * 执行添加和删除操作时, 重新计算留言数量
     * @param $courseNumberId
     */
    public function countAmountOfComment($courseNumberId)
    {
        $sql = "SELECT COUNT(id) AS count FROM forum_comment WHERE forum_id = {$courseNumberId}";
        $row = $this->sqltool->getRowBySql($sql);
        $count = $row['count'];

        $sql = "UPDATE forum SET comment_num = {$count} WHERE id = {$courseNumberId}";
        $this->sqltool->query($sql);

    }


    /**
     * 举报一个forum
     * @param $forumId
     * @return bool
     */
    public function reportForum($forumId)
    {

        $sql = "UPDATE forum SET report = report + 1 WHERE id = {$forumId}";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        } else {
            $this->errorMsg = "没有数据受到影响";
            return false;
        }

    }

    public function reportForumComment($forumCommentId)
    {

        $sql = "UPDATE forum_comment SET report = report + 1 WHERE id = {$forumCommentId}";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        } else {
            $this->errorMsg = "没有数据受到影响";
            return false;
        }

    }

    public function reportedForumRestore($forumId)
    {
        $sql = "UPDATE forum SET report = 0 WHERE id = {$forumId}";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        } else {
            $this->errorMsg = "没有数据受到影响";
            return false;
        }
    }

    public function reportedForumCommentRestore($forumCommentId)
    {
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
    public function getAmountOfReport()
    {

        $sql = "SELECT COUNT(*) as c FROM forum WHERE report > 0";
        $result = $this->sqltool->getRowBySql($sql);
        $count = $result['c'];

        $sql = "SELECT COUNT(*) as c FROM forum_comment WHERE report > 0";
        $result = $this->sqltool->getRowBySql($sql);
        $count += $result['c'];

        return $count;
    }

    public function updateForumTime($forumId)
    {
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
    public function getListOfForumClass($displayHidedFormClass)
    {
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
        $countTodayOfAll = 0;
        $countAllOfAll = 0;
        foreach ($result as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                //计算发帖总量
                if ($k2 == "count_today") {
                    $countTodayOfAll += $v2;
                }
                if ($k2 == "count_all") {
                    $countAllOfAll += $v2;
                }
            }
        }
        $all = [];
        $all['id'] = '0';
        $all['title'] = '全部';
        $all['count_today'] = "{$countTodayOfAll}";
        $all['count_all'] = "{$countAllOfAll}";
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

    /**
     * @param $forum_classId
     * @param int $pageSize
     * @param bool $onlyShowReportList
     * @param bool $onlyShowForumOfUserByUserId
     * @param bool $onlyShowSpecificForumId
     * @return array
     */
    public function getListOfForumByForumClassId($forum_classId, $pageSize = 20, $onlyShowReportList = false, $onlyShowForumOfUserByUserId = false, $onlyShowSpecificForumId = false)
    {

        $userIsLogin = false;
        $currentUser = new UserModel();
        if ($currentUser->isLogin()) {
            $currentUser->addActivity();
            $userIsLogin = true;
        }

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

        $sql = "SELECT f.*,fc.title AS classTitle, type AS classType FROM (select f.*,u_c.is_admin from (SELECT f.*,u.user_class_id,u.img,u.alias,u.gender,u.major,u.enroll_year,u.degree FROM `forum` AS f INNER JOIN `user` AS u ON f.user_id = u.id WHERE {$condition} u.is_del = 0 ) as f INNER JOIN user_class as u_c ON f.user_class_id = u_c.id) as f INNER JOIN forum_class AS fc ON f.forum_class_id = fc.id ORDER BY `sort` DESC,`update_time` DESC";

        $countSql = "SELECT COUNT(*)FROM (SELECT f.*,u.img,u.alias,u.gender FROM `forum` AS f INNER JOIN `user` AS u ON f.user_id = u.id WHERE f.`forum_class_id` in (2) AND u.is_del = 0) as f INNER JOIN forum_class AS fc ON f.forum_class_id = fc.id ORDER BY f.sort DESC, `update_time` DESC";
        $result = parent::getListWithPage($table, $sql, $countSql, $pageSize);
        $id = "";
        $idIndex = 0;
        foreach ($result as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                if ($k2 == "id") {
                    if ($idIndex < 5) {
                        $id .= ($v2 . ",");
                        $idIndex++;
                    }
                }
                if ($k2 == "enroll_year") {
                    $result[$k1][$k2] = BasicTool::translateEnrollYear($v1[$k2]);
                }
                if ($k2 == "img1") {
                    $imgWidth = "";
                    $imgHeight = "";
                    if ($v2 != null) {
                        $imgSize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $v2);
                        $imgWidth = $imgSize[0];
                        $imgHeight = $imgSize[1];
                    }
                    if ($imgWidth == null) {
                        $imgWidth = 0;
                        $imgHeight = 0;
                    }
                    $result[$k1]['img1Width'] = "{$imgWidth}";
                    $result[$k1]['img1Height'] = "{$imgHeight}";
                }
                if ($k2 == "time") {
                    $result[$k1][$k2] = BasicTool::translateTime($v2);
                }
                if ($userIsLogin) {
                    if ($k2 == "user_id") {
                        if (($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('FORUM_DELETE')) || ($v2 == $currentUser->userId)) {
                            $editable = 'yes';
                        }else{
                            $editable = 'no';
                        }
                    }
                }
            }
            $result[$k1]['editable'] = $editable;

        }
        //增加阅读量
        if ($id != "") {
            if ($_COOKIE["forumViewTime"] == null) {
                $id = substr($id, 0, -1);
                $this->countViewOfForumById($id);
                setcookie("forumViewTime", time(), time() + 600, '/');
            }
        }
        return $result;
    }

    public function getListOfForumCommentByForumId($forumId, $pageSize = 40, $onlyShowReportList = false)
    {
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
        $sql = "SELECT f_c.*,u.img,u.enroll_year,u.major,u.alias,u.gender,u.degree FROM `forum_comment` AS f_c INNER JOIN `user` AS u ON f_c.user_id = u.id WHERE {$condition} ORDER BY time ASC";
        $countSql = "SELECT count(*) FROM `forum_comment` AS f_c INNER JOIN `user` AS u ON f_c.user_id = u.id WHERE {$condition} ORDER BY time ASC";
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
                        }else{
                            $editable = 'no';
                        }
                    }
                }
            }
            $arr[$k1]['editable'] = $editable;
        }
        return $arr;
    }


    public function getCommentById($id){
        $sql = "SELECT * from forum_comment INNER JOIN user ON forum_comment.user_id = user.id where forum_comment.id = {$id}";
        $result = $this->sqltool->getRowBySql($sql);
        foreach($result as $k => $v){
            if($k == 'time'){
                $result[$k] = BasicTool::translateTime($v);
            }
        }
        return $result;
    }
}