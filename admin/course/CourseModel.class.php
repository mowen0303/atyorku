<?php
namespace admin\course;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class CourseModel extends Model {






    public function getCourseDetailedList($courseClassID){

        $this->countAmountOfRead($courseClassID);
        $sql = "SELECT C.*,U.alias FROM `course_description` AS C INNER JOIN `user` AS U ON C.user_id = U.id WHERE `course_class_id` = {$courseClassID} AND C.is_del=0";
        return $this->sqltool->getListBySql($sql);

    }


    public function getCourseNum($courseID){
//       $arr = $this->getCourseClass();SELECT S.id,M.is_del,M.title AS mtitle,S.title AS stitle FROM course_class AS M INNER JOIN course_class AS S ON S.parent_id = M.id WHERE M.id = {$parent}&&M.is_del=0
            $sql = "SELECT`title` FROM `course_class` WHERE `id`= {$courseID}&&is_del=0";
            return $this->sqltool->getRowBySql($sql);
            /*$num = count($arr);
            for($i=0;$i<$num;++$i){
                if($parent==$arr[i])
            }*/
    }




    public function addLikeByCommentId($id)
    {
        $sql = "UPDATE course_comment SET `great`=`great`+1 WHERE id IN ({$id})";
        $this->sqltool->mysqli->query($sql);
        if($this->sqltool->mysqli->error)
        {
            BasicTool::echoMessage("SQL语法错误:".$this->sqltool->mysqli->error."<==SQL==>".$sql);
        } elseif($this->sqltool->getAffectedRows()>0)
        {
            return true;
        }
    }


    public function getCourseDetailedNumberById($id)
    {
        $sql = "SELECT t1.title,t1.id,t3.parent_id FROM course_class as t1 INNER JOIN course_description as t2 on t2.course_class_id = t1.id INNER JOIN course_class as t3 on t3.parent_id = t1.id where t1.id = ({$id});";
        return $this->sqltool->getRowBySql($sql);
    }



    //-----------------------------------------------------------------------------------------------------------------------------------------------


    /**
     * @Jerry 翻译成绩
     * @param $grade
     * @return string
     */
    public function translateGrade($grade){

        if ($grade == 9) {
            return "A+";
        } else if ($grade >= 8) {
            return "A";
        } else if ($grade >= 7) {
            return "B+";
        }else if ($grade >= 6) {
            return "B";
        }else if ($grade >= 5) {
            return "C+";
        }else if ($grade >= 4) {
            return "C";
        }else if ($grade >= 3) {
            return "D+";
        }else if ($grade >= 2) {
            return "D";
        }else if ($grade >= 1) {
            return "E";
        }else if ($grade > 0) {
            return "F";
        }else{
            return "N";
        }

    }

    /**
     * @Jerry 翻译课程难度
     * @param $difficulty
     * @return string
     */
    public function translateDifficulty($difficulty){
        if ($difficulty >= 4.4) {
            return "杀手课";
        } else if ($difficulty >= 3.4) {
            return "困难";
        } else if ($difficulty >= 2.4) {
            return "一般";
        }else if ($difficulty >= 1.4) {
            return "简单";
        }else if ($difficulty > 0) {
            return "水课";
        } else{
            return "暂无难度评级";
        }

    }

    /**
     * @Jerry 当用户完成一次评级的同时, 更新:平均分,通过率,统计个数 (by Jerry)
     * @param $courseNumberId
     */
    public function incrementStaticCount($courseNumberId){

        $sql = "SELECT COUNT(diff) AS count FROM course_rate WHERE course_class_id = {$courseNumberId}";
        $row = $this->sqltool->getRowBySql($sql);
        $count = $row['count'];

        $sql = "SELECT AVG(diff) AS diff FROM course_rate WHERE course_class_id = {$courseNumberId}";
        $row = $this->sqltool->getRowBySql($sql);
        $diff = $row['diff'];

        $sql = "SELECT AVG(grade) AS grade FROM course_rate WHERE course_class_id = {$courseNumberId}";
        $row = $this->sqltool->getRowBySql($sql);
        $grade = $row['grade'];

        $sql = "SELECT (SELECT COUNT(*) FROM `course_rate` WHERE course_class_id = {$courseNumberId} AND grade >= 2)/(SELECT COUNT(*) FROM course_rate WHERE course_class_id = {$courseNumberId}) AS fail_rate";
        $row = $this->sqltool->getRowBySql($sql);
        $passRate = $row['fail_rate'];
        $passRate *= 100;
        $passRate = ceil($passRate);

        $sql = "UPDATE course_class SET average_count = {$count},diff = {$diff}, average = {$grade}, pass_rate = {$passRate} WHERE id = {$courseNumberId}";
        $this->sqltool->query($sql);
    }



    /**
     * @Jerry 判断用户是否已经对课程评级过
     * @param $userId
     * @param $courseClassId
     * @return bool
     */
    public function isExistOfRateUser($userId,$courseClassId){

        $sql = "SELECT id FROM course_rate WHERE course_class_id = {$courseClassId} AND user_id = {$userId}";

        if($this->sqltool->getRowBySql($sql)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @Jerry 根据评论id获取发评论的用户的id
     * @param $commentId
     * @return bool
     */
    public function getUserIdOfCommentByCommentId($commentId){

        if(is_array($commentId)){
            $commentId = $commentId[0];
        }

        $sql = "SELECT user_id FROM course_comment WHERE id = {$commentId}";

        if($row = $this->sqltool->getRowBySql($sql)){
            return $row['user_id'];
        }else{
            return false;
        }
    }




    /**
     * @jerry 获得一条子类的信息
     * @param $childClassId
     * @return bool
     */
    public function getRowOfChildClassByChildClassId($childClassId){
        $statisticsModel = new StatisticsModel();
        $statisticsModel->countStatistics(3);
        $sql = "SELECT * FROM course_class WHERE id IN ({$childClassId})";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * @jerry 获得一个子类下课评列表
     * @param $childClassId
     * @return bool
     */
    public function getListOfCourseDescriptionByChildClassId($childClassId){

        $sql = "SELECT D.*,C.course_description_id FROM (SELECT D.*,U.img,U.alias FROM (SELECT * FROM course_description WHERE course_class_id IN ($childClassId)) AS D INNER JOIN user AS U ON D.user_id = U.id) AS D INNER JOIN course_class AS C ON D.course_class_id = C.id";
        return $this->sqltool->getListBySql($sql);
    }

    /**
     * @Jerry 获取(简化版)课评详情
     * @param $childClassId
     * @return \一维关联数组
     */
    public function getRowOfEasyCourseDescriptionByChildClassId($childClassId){
        $this->countAmountOfRead($childClassId);
        $sql = "SELECT * FROM course_description WHERE course_class_id IN ({$childClassId})";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * @Jerry 根据课评ID获取获取课评详情
     * @param $childClassId
     * @return \一维关联数组
     */
    public function getRowOfEasyCourseDescriptionById($id){
        $sql = "SELECT * FROM course_description WHERE id IN ({$id})";
        return $this->sqltool->getRowBySql($sql);
    }


    /**
     * Jerry 获取(完整版)课评详情
     * @param $descriptionId
     * @return \一维关联数组
     */
    public function getRowOfCourseDescriptionByChildClassId($childClassId){
        $statisticsModel = new StatisticsModel();
        $statisticsModel->countStatistics(3);
        $this->countAmountOfRead($childClassId);
        $currentUser = new UserModel();
        $currentUser->addActivity();

        $sql = "SELECT CT.*,UT.img,alias FROM (SELECT CT.*,DT.user_id,summary,structure,wisechooes,strategy,time FROM (SELECT FT.title AS mtitle,CT.* FROM (SELECT id AS course_class_id,title,parent_id,course_description_id,course_name,credits,descript,credit_ex,prerequest,textbook,average_count,pass_rate,average,diff,readcounter,comment_num FROM course_class WHERE id IN ({$childClassId})) AS CT INNER JOIN course_class AS FT ON CT.parent_id = FT.id) AS CT INNER JOIN course_description AS DT ON CT.course_description_id = DT.id) AS CT INNER JOIN user AS UT ON UT.id = CT.user_id";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * @jerry 判断课程分类里是否已经存在同名title
     * @param $value
     * @param $parentId
     * @return bool
     */
    public function isExistOfCourseTitle($value, $parentId){
        $sql = "SELECT count(*) AS amount FROM course_class WHERE title = '{$value}' AND parent_id = {$parentId}";
        $row = $this->sqltool->getRowBySql($sql);
        if($row['amount']>0){
            return true;
        }
        return false;
    }



    public function test(){
        $sql = "SELECT id FROM course_class WHERE parent_id = 0";
        $arr = $this->sqltool->getListBySql($sql);
        foreach($arr as $row){
            $id = $row['id'];
            $sql = "SELECT count(*) as amount FROM course_class WHERE parent_id = {$id}";
            $row = $this->sqltool->getRowBySql($sql);
            $amount = $row['amount'];
            $sql = "UPDATE course_class SET count_all = {$amount} WHERE id = {$id}";
            $this->sqltool->query($sql);
        }
    }




    //--------------------------------增增增增增增增增增增增增增增增增增增增增增增增增增增增--------------------------------
    //--------------------------------增增增增增增增增增增增增增增增增增增增增增增增增增增增--------------------------------
    //--------------------------------增增增增增增增增增增增增增增增增增增增增增增增增增增增--------------------------------
    //--------------------------------增增增增增增增增增增增增增增增增增增增增增增增增增增增--------------------------------

    //--------------------------------删删删删删删删删删删删删删删删删删删删删删删删删删删删--------------------------------
    //--------------------------------删删删删删删删删删删删删删删删删删删删删删删删删删删删--------------------------------
    //--------------------------------删删删删删删删删删删删删删删删删删删删删删删删删删删删--------------------------------
    //--------------------------------删删删删删删删删删删删删删删删删删删删删删删删删删删删--------------------------------
    /**
     * @jerry (删除)一个父类
     * @param $fatherClassId
     * @return bool
     */
    public function deleteFatherClass($fatherClassId){

        $sql = "SELECT count(*) AS amount FROM course_class WHERE parent_id IN ({$fatherClassId})";
        $row = $this->sqltool->getRowBySql($sql);
        if($row['amount']>0){
            $this->errorMsg = "删除失败,请先手动清除栏目下所有内容";
            return false;
        }

        $sql = "DELETE FROM `course_class` WHERE id IN('$fatherClassId')";
        $this->sqltool->query($sql);
        if($this->sqltool->getAffectedRows()>0) {
            return true;
        }
        $this->errorMsg = "没有数据受影响";
        return false;
    }

    /**
     * @jerry (删除)一个子类
     * @param $childClassId
     * @return bool
     */
    public function deleteChildClass($childClassId){
        $sql = "SELECT count(*) AS amount FROM course_class AS c INNER JOIN course_description AS cd ON cd.course_class_id = c.id WHERE c.id IN ({$childClassId})";
        $row = $this->sqltool->getRowBySql($sql);
        if($row['amount']>0){
            $this->errorMsg = "删除失败,请先手动清除栏目下内容";
            return false;
        }

        $sql = "DELETE FROM `course_class` WHERE id IN('$childClassId')";
        $this->sqltool->query($sql);
        if($this->sqltool->getAffectedRows()>0) {
            return true;
        }
        $this->errorMsg = "没有数据受影响";
        return false;

    }

    //--------------------------------查查查查查查查查查查查查查查查查查查查查查查查查查查查--------------------------------
    //--------------------------------查查查查查查查查查查查查查查查查查查查查查查查查查查查--------------------------------
    //--------------------------------查查查查查查查查查查查查查查查查查查查查查查查查查查查--------------------------------
    //--------------------------------查查查查查查查查查查查查查查查查查查查查查查查查查查查--------------------------------
    /**
     * @Jerry 获取
     * @return array
     */
    public function getListOfFatherClass($onlyShowValid = false){
        $statisticsModel = new StatisticsModel();
        $statisticsModel->countStatistics(3);
        $currentUser = new UserModel();
        $currentUser->addActivity();

        $condition = $onlyShowValid == true?"AND count_all > 0 ":"";
        $sql = "SELECT * FROM course_class WHERE parent_id = 0 {$condition} AND is_del=0 ORDER BY title";
        return $this->sqltool->getListBySql($sql);
    }

    /** 获取用户评论过的课评ID
     * @param $userId
     * @return int|string
     */
    public function getIdOfCourseClassOfUserToCommentByUserId($userId){

        $sql = "SELECT course_class_id FROM course_comment WHERE user_id = {$userId}";
        $arr = $this->sqltool->getListBySql($sql);
        if($arr){
            $str = "";
            foreach($arr as $row){
                $str .= $row['course_class_id'].",";
            }
            $str = substr($str,0,-1);
        }
        if($str == null){
            return 0;
        }
        return $str;
    }

    public function getListOfChildClassByParentId($parentId,$onlyShowValid=false,$onlyShowSpecificListOfCourse = false){

        $statisticsModel = new StatisticsModel();
        $statisticsModel->countStatistics(3);
        $currentUser = new UserModel();
        $currentUser->addActivity();

        $condition = $onlyShowValid == true?"AND S.course_description_id > 0 ":"";

        if($onlyShowSpecificListOfCourse !== false){$condition .= "AND S.id IN ({$onlyShowSpecificListOfCourse}) ";}

        if($parentId != 0){$condition .= "AND S.parent_id = {$parentId} ";}

        $sql = "SELECT M.title AS mtitle,S.*,S.title AS stitle FROM course_class AS M INNER JOIN course_class AS S ON S.parent_id = M.id WHERE S.is_del = 0 {$condition} ORDER BY title";
        return $this->sqltool->getListBySql($sql);
    }


    /**
     * 根据courseClass获取评论 (by Jerry)
     * @param $courseId
     */
    public function getCourseCommentListById($courseClassId,$pageSize = 40){
        $currentUser = new UserModel();
        $table ='course_comment';
        $sql = "SELECT C.*,U.id AS uid,U.alias,img,gender FROM (SELECT * FROM course_comment AS C WHERE course_class_id = {$courseClassId}) AS C INNER JOIN user AS U ON C.user_id = U.id ORDER BY time DESC";
        $countSql = "SELECT count(*) FROM (SELECT * FROM course_comment AS C WHERE course_class_id = {$courseClassId}) AS C INNER JOIN user AS U ON C.user_id = U.id ORDER BY time DESC";
        $arr = parent::getListWithPage($table,$sql,$countSql,$pageSize);

        foreach($arr as $k1 => $v1) {
            foreach($v1 as $k2 => $v2){
                if($k2=="time"){
                    $arr[$k1][$k2] = BasicTool::translateTime($v2);
                }
                if($k2 == "user_id"){
                    if($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('FORUM_DELETE')){
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

    //--------------------------------改改改改改改改改改改改改改改改改改改改改改改改改改改改--------------------------------
    //--------------------------------改改改改改改改改改改改改改改改改改改改改改改改改改改改--------------------------------
    //--------------------------------改改改改改改改改改改改改改改改改改改改改改改改改改改改--------------------------------
    //--------------------------------改改改改改改改改改改改改改改改改改改改改改改改改改改改--------------------------------
    public function countAmountOfFatherClass($fatherClassId){
        $sql = "SELECT count(*) as amount FROM course_class WHERE parent_id = {$fatherClassId}";
        $row = $this->sqltool->getRowBySql($sql);
        $amount = $row['amount'];
        $sql = "UPDATE course_class SET count_all = {$amount} WHERE id = {$fatherClassId}";
        $this->sqltool->query($sql);
    }
    public function countAmountOfDescription($childClassId){
        $sql = "SELECT count(*) amount FROM course_description WHERE course_class_id IN ($childClassId)";
        $row = $this->sqltool->getRowBySql($sql);
        $amount = $row['amount'];
        $sql = "UPDATE course_class SET count_all = {$amount} WHERE id = {$childClassId}";
        $this->sqltool->query($sql);
    }

    /**
     * @Jerry 使生效
     * @param $childClassId
     * @return \一维关联数组
     */
    public function setValidOfDescription($descriptionId,$childClassId){
        $sql = "UPDATE course_class SET course_description_id = {$descriptionId} WHERE id IN ({$childClassId});";
        $this->sqltool->query($sql);
        if($this->sqltool->getAffectedRows()>0){
            return true;
        }
        $this->errorMsg = "没有数据受影响";
        return false;
    }

    /**
     * @Jerry 使生效
     * @param $childClassId
     * @return \一维关联数组
     */
    public function setNotValidOfDescription($childClassId){
        $sql = "UPDATE course_class SET course_description_id = 0 WHERE id IN ({$childClassId});";
        $this->sqltool->query($sql);
        if($this->sqltool->getAffectedRows()>0){
            return true;
        }
        $this->errorMsg = "没有数据受影响";
        return false;
    }

    /**
     * @Jerry 执行添加和删除操作时, 重新计算留言数量
     * @param $courseNumberId
     */
    public function countAmountOfComment($childClassId){
        $sql = "SELECT COUNT(id) AS amount FROM course_comment WHERE course_class_id = {$childClassId}";
        $row = $this->sqltool->getRowBySql($sql);
        $count = $row['amount'];

        $sql = "UPDATE course_class SET comment_num = {$count} WHERE id = {$childClassId}";
        $this->sqltool->query($sql);

    }

    /**
     * @Jerry 更新阅读量
     * @param $courseNumberId
     */
    public function countAmountOfRead($childClassId){
        $sql = "UPDATE course_class SET readcounter = readcounter + 1 WHERE id = {$childClassId}";
        $this->sqltool->query($sql);
    }

    /**
     * @Jerry 获取(学霸智囊团)用户列表
     * @param $courseNumberId
     */
    public function getUserListOfCourseDescription(){

        $sql = "SELECT DISTINCT user_id FROM course_description ORDER BY user_id";
        $arr = $this->sqltool->getListBySql($sql);

        $userId = "";

        foreach($arr as $row){
            $userId .= $row['user_id'].",";
        }

        $userId = substr($userId,0,-1);

        $sql = "SELECT id,img,alias,major,enroll_year,wechat,description FROM user WHERE id in ({$userId})";

        return $this->sqltool->getListBySql($sql);
    }

    /**
     * 根据学霸智囊团用户id获取他写的课评列表
     * @param $userId
     */
    public function getCourseListByUserId($userId){

        $sql = "SELECT C.id,C.mtitle,C.title FROM (SELECT course_class_id FROM course_description WHERE user_id = {$userId}) AS CD INNER JOIN (SELECT S.id,M.title AS mtitle,S.title FROM course_class AS M INNER JOIN course_class AS S ON S.parent_id = M.id) AS C ON CD.course_class_id = C.id ORDER BY C.mtitle,C.title";
        return $this->sqltool->getListBySql($sql);

    }







}



?>