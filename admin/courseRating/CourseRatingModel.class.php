<?php
namespace admin\courseRating;   //-- 注意 --//
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;
use \Credit as Credit;
use \admin\transaction\TransactionModel as TransactionModel;
use \admin\msg\MsgModel as MsgModel;

class CourseRatingModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "course_rating";
    }

    /**
     * 通过ID获取Course
     * @param $id 要查询的CourseRating ID
     * @param bool $withDetail 同时query科目和教授细节
     * @return \一维关联数组
     */
    public function getCourseRatingById($id, $withDetail=false) {
        if($withDetail){
            $select = "SELECT cr.*, cc.id AS course_code_child_id, cc2.id AS course_code_parent_id, cc.title AS course_code_child_title, cc2.title AS course_code_parent_title, cc.full_title AS course_full_title, p.id AS prof_id, CONCAT(p.firstname, ' ', p.lastname) AS prof_name";
            $from = "FROM (course_rating cr INNER JOIN course_code cc ON cr.course_code_id = cc.id INNER JOIN course_code cc2 ON cc.parent_id = cc2.id INNER JOIN professor p ON cr.prof_id = p.id)";
            $sql = "{$select} {$from} WHERE cr.id={$id}";
            return $this->sqltool->getRowBySql($sql);
        }else {
            return $this->getRowById($this->table, $id);
        }
    }


    /**
    * 获取一页课评
    * @param query additional query
    * @param pageSize 每页query数量
    * @return 2维数组
    */
    public function getListOfCourseRating($query=false, $pageSize=20, $orderBy = false, $onlyShowEssence = false) {
        $select = "SELECT cr.*, u.id AS user_id, u.name AS user_name, u.user_class_id, u.img AS user_img, u.alias AS user_alise, u.gender AS user_gender, u.major AS user_major, u.enroll_year AS user_enroll_year, u.degree AS user_degree, uc.is_admin, cc.id AS course_code_child_id, cc2.id AS course_code_parent_id, cc.title AS course_code_child_title, cc2.title AS course_code_parent_title, cc.full_title AS course_full_title, p.id AS prof_id, CONCAT(p.firstname, ' ', p.lastname) AS prof_name";
        $from = "FROM (course_rating cr INNER JOIN course_code cc ON cr.course_code_id = cc.id INNER JOIN course_code cc2 ON cc.parent_id = cc2.id INNER JOIN professor p ON cr.prof_id = p.id INNER JOIN `user` u ON cr.user_id = u.id LEFT JOIN user_class uc ON u.user_class_id = uc.id)";
        $sql = "{$select} {$from}";
        $countSql = "SELECT COUNT(*) {$from}";
        if ($query) {
            $sql = "{$sql} WHERE ({$query})";
            $countSql = "{$countSql} WHERE ({$query})";

            if($onlyShowEssence==true){
                $sql .= " AND essence in (1)";
                $countSql .= " AND essence in (1)";
            }
        }



        $orderCondition = "";
        if($orderBy=="id"){
            $orderCondition .= "`id` DESC, ";
        }else if($orderBy=='essence'){
            $orderCondition .= "`essence` DESC, ";
        }

//        $sql = "{$sql} ORDER BY {$orderCondition} `count_like`*2-`count_dislike` DESC,`publish_time` DESC";
        $sql = "{$sql} ORDER BY {$orderCondition} essence desc,count_like-count_dislike desc,publish_time desc";
        $arr = parent::getListWithPage($this->table, $sql, $countSql, $pageSize);
         // Format publish time and enroll year
        foreach ($arr as $k => $v) {
            $t = $v["publish_time"];
            $enrollYear = $v["user_enroll_year"];
            if($t) $arr[$k]["publish_time"] = BasicTool::translateTime($t);
            if($enrollYear) $arr[$k]["user_enroll_year"] = BasicTool::translateEnrollYear($enrollYear);
        }
        return $arr;
    }


    /**
    * 获取一页课评
    * @param parentTitle course code parent title
    * @param childTitle course code child title
    * @param pageSize 每页query数量
    * @return 2维数组
    */
    public function getListOfCourseRatingByCourseTitle($pageSize=20,$parentTitle=false,$childTitle=false) {
        $q = "";
        if($parentTitle){
            $q .= "cc2.title='{$parentTitle}'";
        }
        if($childTitle){
            if($parentTitle){
                $q .= " AND ";
            }
            $q .= "cc.title='{$childTitle}'";
        }
        return $this->getListOfCourseRating($q,$pageSize);
    }

    /**
    * 通过指定科目ID，获取一页课评
    * @param courseId 科目ID
    * @param pageSize 每页query数量
    * @return 2维数组
    */
    public function getListOfCourseRatingByCourseId($courseId, $orderType, $pageSize=20,$onlyShowEssence=false) {
        if($courseId>0){
            $sql = "UPDATE course_code SET view_count = view_count+1 WHERE id IN ('{$courseId}');";
            $this->sqltool->query($sql);
        }
        $query = "course_code_id in ({$courseId})";
        return $this->getListOfCourseRating($query, $pageSize,$orderType,$onlyShowEssence);
    }

    /**
    * 通过指定教授ID，获取一页课评
    * @param profId 教授ID
    * @param pageSize 每页query数量
    * @return 2维数组
    */
    public function getListOfCourseRatingByProfId($profId, $pageSize=20) {
        $query = "prof_id in ({$profId})";
        return $this->getListOfCourseRating($query, $pageSize);
    }

    /**
    * 通过指定科目ID和教授ID，获取一页课评
    * @param courseId 科目ID
    * @param profId 教授ID
    * @param pageSize 每页query数量
    * @return 2维数组
    */
    public function getListOfCourseRatingByCourseIdProfId($courseId, $profId, $pageSize=20) {
        $query = "course_code_id in ({$courseId}) AND prof_id in ({$profId})";
        return $this->getListOfCourseRating($query, $pageSize);
    }


    /**
     * 通过指定用户Id, 获取一页课评
     * @param $userId 用户ID
     * @param int $pageSize
     * @return array
     */
    public function getListOfCourseRatingByUserId($userId, $pageSize=20) {
        $userId = intval($userId);
        $query = "user_id in ({$userId})";
        return $this->getListOfCourseRating($query, $pageSize);
    }

    /**
     * 获得一页没有奖励的课评
     * @param int $pageSize
     * @return array
     */
    public function getListOfunawardedCourseRating($pageSize=20){
        $query = "award=-1";
        return $this->getListOfCourseRating($query,$pageSize,"id");
    }


    /**
     * Add or Update a Course Rating
     *
     * @param $flag 'add' | 'update'
     * @param $courseCodeId Course Code ID
     * @param $userId user ID
     * @param $profId Professor ID
     * @param $contentDiff Content difficulty
     * @param $homeworkDiff Homework Difficulty
     * @param $testDiff Test Difficulty
     * @param string $grade Letter Grade
     * @param $comment comment to the course
     * @param $year attended year
     * @param $term attended term
     * @param $contentSummary 课程内容总结
     * @param $id modified course rating id (required when for 'update')
     * @return bool
     * @throws Exception ValidationExceptions Use try catch
     */
    public function modifyCourseRating($flag, $courseCodeId, $userId, $profId, $contentDiff, $homeworkDiff, $testDiff, $grade='', $comment, $year, $term, $contentSummary, $id=false) {
        // Validations
        $this->isValidDiff($contentDiff,false) or BasicTool::throwException("内容困难等级 ({$contentDiff}) 不存在");
        $this->isValidDiff($homeworkDiff,true) or BasicTool::throwException("作业困难等级 ({$homeworkDiff}) 不存在");
        $this->isValidDiff($testDiff,true) or BasicTool::throwException("考试困难等级 ({$testDiff}) 不存在");
        $this->isValidGrade($grade) or BasicTool::throwException("该成绩选项 ({$grade}) 不存在");
        $this->isValidYear($year) or BasicTool::throwException("该学年 ({$year}) 不存在");
        $this->isValidTerm($term) or BasicTool::throwException("该学期 ({$term}) 不存在");

        $arr = [];
        $arr["course_code_id"] = $courseCodeId;
        $arr["user_id"] = $userId;
        $arr["prof_id"] = $profId;
        $arr["content_diff"] = $contentDiff;
        $arr["homework_diff"] = $homeworkDiff?:0;
        $arr["test_diff"] = $testDiff?:0;
        $arr["grade"] = $grade?:"";
        $arr["comment"] = $comment;
        $arr["year"] = $year;
        $arr["term"] = $term;
        $arr["content_summary"] = $contentSummary;
        $bool = false;
        if($flag=='add') {
            $arr["publish_time"] = time();
            $bool = $this->addRow($this->table, $arr);
        } else if($flag=='update') {
            $bool = $this->updateRowById($this->table, $id, $arr);
        } else {
            BasicTool::throwException("Unknown flag.");
        }
        if ($bool) {
            $this->updateReports($courseCodeId, $profId, true);
        }
        return $bool;
    }


    /**
    * 通过ID删除一个CourseRating
    * @param id 要删除的Course Code ID
    * @return bool
    */
    public function deleteCourseRatingById($id) {
        $result = $this->getRowById($this->table, $id) or BasicTool::throwException("没有找到 Course Rating");
        $sql = "DELETE FROM {$this->table} WHERE id in ({$id})";
        $bool = $this->sqltool->query($sql);
        if ($bool && $result["course_code_id"] && $result["prof_id"]) {
            $this->updateReports($result["course_code_id"], $result["prof_id"], true);
        }
        return $bool;
    }


    /**
    * 更新 CourseRating Title by ID
    * @param id 要更新的 Course Code ID
    * @param title 要更新的 Course Code Title
    * @param fullTitle 要更新的 Course Code Full Title
    * @param credits 要更新的 Course Code Credits
    * @return bool
    */
    public function updateCourseRatingById($id, $title, $fullTitle, $credits) {
        $result = $this->getRowById($this->table, $id) or BasicTool::throwException("没有找到 Course Code");
        $arr = [];
        $arr["title"] = $title;
        $arr["full_title"] = $fullTitle;
        $arr["credits"] = $credits;
        return $this->updateRowById($this->table, $id, $arr);
    }

    /**
     * 点赞/取消点赞一个课评
     * @param $id 课评ID
     * @return bool
     */
    public function likeCourseRating($id,$on=true){
        return $this->likeDislikeCourseRating($id, "count_like");
    }

    /**
     * 踩/取消踩一个课评
     * @param $id 课评ID
     * @return bool
     */
    public function dislikeCourseRating($id, $on=true){
        return $this->likeDislikeCourseRating($id, "count_dislike");
    }

    /**
     * 赞或踩一个课评 或 相反
     * @param $id 课评ID
     * @param $field 赞或踩的字段 count_like | count_dislike
     * @return bool
     */
    private function likeDislikeCourseRating($id,$field){
        try{
            ($field==="count_like" || $field==="count_dislike") or BasicTool::throwException("Unknown field.");
            $id=intval($id);
            $sql = "SELECT {$field} FROM course_rating WHERE id={$id}";
            $result = $this->sqltool->getRowBySql($sql) or BasicTool::throwException("没找到该课评");
            $arr=[$field=>(max(0,$result[$field]+1))];
            return $this->updateRowById("course_rating",$id,$arr);
        } catch(Exception $e){
            $this->errorMsg = $e->getMessage();
            return false;
        }
    }

    /**
    * validate difficulty
    * @param diff 用户提供的 difficulty level
    * @param allowZero 容许0
    * @return bool
    */
    private function isValidDiff($diff, $allowZero=false) {
        $diff = intval($diff);
        if($allowZero){
            return $diff >= 0 && $diff < 11;
        } else {
            return $diff > 0 && $diff < 11;
        }

    }


    /**
    * validate grade
    * @param grade 用户提供的grade
    * @return bool
    */
    private function isValidGrade($grade) {
        return in_array($grade, array('','A+','A','B+','B','C+','C','D+','D','E','F'));
    }


    /**
    * validate year
    * @param year 用户提供的year
    * @return bool
    */
    private function isValidYear($year) {
        $year = intval($year);
        return $year > 1959 && $year <= date("Y");
    }

    /**
    * validate term
    * @param term 用户提供的term
    * @return bool
    */
    private function isValidTerm($term) {
        return in_array($term, array('Winter','Summer','Summer 1','Summer 2','Year','Fall'));
    }

    /************************************/
    /*         Reports Functions        */
    /************************************/

    /**
    * 获取一页科目报告
    * @return 二维数组
    */
    public function getListOfCourseReports($pageSize=20,$courseParentTitle=false,$courseChildTitle=false) {
        $q = "";
        //$order = "ORDER BY cr.rating_count>0 DESC";
        $order = "ORDER BY ";
        if($courseParentTitle) {
            $order .= "c2.title, c1.title";
            if($courseChildTitle){
                // fixed parent, has child title
                $q .= " AND c2.title='{$courseParentTitle}' AND c1.title LIKE '{$courseChildTitle}%'";
            } else {
                $q .= " AND c2.title LIKE '{$courseParentTitle}%'";
            }
        } else {
            $order .= "cr.update_time DESC, c2.title, c1.title";
        }

        $sql = "SELECT cr.*, c2.id AS course_code_parent_id, c1.title AS course_code_child_title, c1.full_title AS course_full_title,c1.description, c2.title AS course_code_parent_title FROM course_report cr, course_code c1, course_code c2 WHERE c1.parent_id=c2.id AND cr.course_code_id=c1.id{$q} {$order}";
        $countSql = "SELECT COUNT(*) FROM course_report cr, course_code c1, course_code c2 WHERE c1.parent_id=c2.id AND cr.course_code_id=c1.id{$q}";
        $arr = parent::getListWithPage("course_report", $sql, $countSql, $pageSize);
        return $arr;
    }

    /**
     * 根据parent id 获取子分类
     * @param $parentId
     * @param int $pageSize
     * @return array
     */
    public function getListOfCourseReportsByParentId($parentId,$orderField,$orderModel,$pageSize){

        $q = " AND c1.parent_id in ($parentId)";
        if($orderField=="title"){
            $order = "ORDER BY c2.title, c1.title {$orderModel}";
        }else if($orderField=="diff"){
            $order = "ORDER BY nodata,cr.overall_diff {$orderModel}";
        }


        $sql = "SELECT cr.*,cr.overall_diff=0 as nodata, c2.id AS course_code_parent_id, c1.title AS course_code_child_title, c1.full_title AS course_full_title,c1.description, c2.title AS course_code_parent_title FROM course_report cr, course_code c1, course_code c2 WHERE c1.parent_id=c2.id AND cr.course_code_id=c1.id{$q} {$order}";
        $countSql = "SELECT COUNT(*) FROM course_report cr, course_code c1, course_code c2 WHERE c1.parent_id=c2.id AND cr.course_code_id=c1.id{$q}";
        $arr = parent::getListWithPage("course_report", $sql, $countSql, $pageSize);
        return $arr;
    }

    /**
    * 获取一页教授报告
    * @return 二维数组
    */
    public function getListOfProfessorReports($pageSize=20,$profName=false) {
        $q = "";
        $order = "ORDER BY pr.rating_count>0 DESC";
        if($profName){
            $q .= " AND CONCAT(p.firstname, ' ', p.lastname) LIKE '{$profName}%'";
        } else {
            $order .= ", pr.update_time DESC, p.firstname, p.lastname";
        }

        $sql = "SELECT pr.*, CONCAT(p.firstname, ' ', p.lastname) AS prof_name FROM professor_report pr, professor p WHERE pr.prof_id=p.id{$q} {$order}";
        $countSql = "SELECT COUNT(*) FROM professor_report pr, professor p WHERE pr.prof_id=p.id{$q}";
        $arr = parent::getListWithPage("professor_report", $sql, $countSql, $pageSize);
        return $arr;
    }

    /**
    * 获取一页科目教授报告
    * @return 二维数组
    */
    public function getListOfCourseProfessorReports($pageSize=20,$courseId=false,$profId=false) {
        $query = "";
        if($courseId) {
            $query .= " AND cp.course_code_id={$courseId}";
        }
        if($profId) {
            $query .= " AND cp.prof_id={$profId}";
        }
        $sql = "SELECT cp.*, c2.id AS course_code_parent_id, c1.title AS course_code_child_title, c1.full_title AS course_full_title, c2.title AS course_code_parent_title, CONCAT(p.firstname, ' ', p.lastname) AS prof_name FROM course_prof_report cp, course_code c1, course_code c2, professor p WHERE c1.parent_id=c2.id AND cp.course_code_id=c1.id AND cp.prof_id=p.id{$query}";
        $countSql = "SELECT COUNT(*) FROM course_prof_report cp, course_code c1, course_code c2, professor p WHERE c1.parent_id=c2.id AND cp.course_code_id=c1.id AND cp.prof_id=p.id{$query}";
        $arr = parent::getListWithPage("course_prof_report", $sql, $countSql, $pageSize);
        return $arr;
    }

    /**
    * 获取一行科目报告
    * @param id 指定的科目报告ID
    * @return 1维数组
    */
    public function getCourseReportById($id) {
        $sql = "SELECT cr.*, c2.id AS course_code_parent_id, c1.title AS course_code_child_title, c1.full_title AS course_full_title, c2.title AS course_code_parent_title FROM course_report cr, course_code c1, course_code c2 WHERE c1.parent_id=c2.id AND cr.course_code_id=c1.id AND cr.id in ({$id})";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
    * 获取一行教授报告
    * @param id 指定的教授报告ID
    * @return 1维数组
    */
    public function getProfessorReportById($id) {
        $sql = "SELECT pr.*, CONCAT(p.firstname, ' ', p.lastname) AS prof_name FROM professor_report pr, professor p WHERE pr.prof_id=p.id AND pr.id in ({$id})";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
    * 获取一行科目教授报告
    * @param id 指定的科目教授报告ID
    * @return 1维数组
    */
    public function getCourseProfessorReportById($id) {
        $sql = "SELECT cp.*, c2.id AS course_code_parent_id, c1.title AS course_code_child_title, c1.full_title AS course_full_title, c2.title AS course_code_parent_title, CONCAT(p.firstname, ' ', p.lastname) AS prof_name FROM course_prof_report cp, course_code c1, course_code c2, professor p WHERE c1.parent_id=c2.id AND cp.course_code_id=c1.id AND cp.prof_id=p.id AND cp.id in ({$id})";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
    * 获取一行科目报告
    * @param courseId 指定的科目ID
    * @return 1维数组
    */
    public function getCourseReportByCourseId($courseId) {
        $sql = "SELECT cr.*, c2.id AS course_code_parent_id, c1.title AS course_code_child_title, c1.full_title AS course_full_title, c2.title AS course_code_parent_title FROM course_report cr, course_code c1, course_code c2 WHERE c1.parent_id=c2.id AND cr.course_code_id=c1.id AND cr.course_code_id in ({$courseId})";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
    * 获取一行教授报告
    * @param profId 指定的教授ID
    * @return 1维数组
    */
    public function getProfessorReportByProfId($profId) {
        $sql = "SELECT pr.*, CONCAT(p.firstname, ' ', p.lastname) AS prof_name FROM professor_report pr, professor p WHERE pr.prof_id=p.id AND pr.prof_id in ({$profId})";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
    * 获取一行科目教授报告
    * @param courseId 指定的科目ID
    * @param profId 指定教授ID
    * @return 1维数组
    */
    public function getCourseProfessorReportByCourseIdProfId($courseId, $profId) {
        $sql = "SELECT cp.*, c2.id AS course_code_parent_id, c1.title AS course_code_child_title, c1.full_title AS course_full_title, c2.title AS course_code_parent_title, CONCAT(p.firstname, ' ', p.lastname) AS prof_name FROM course_prof_report cp, course_code c1, course_code c2, professor p WHERE c1.parent_id=c2.id AND cp.course_code_id=c1.id AND cp.prof_id=p.id AND cp.course_code_id in ({$courseId}) AND cp.prof_id in ({$profId})";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
    * 删除一行科目报告
    * @param id 指定的科目报告ID
    * @return 1维数组
    */
    public function deleteCourseReportById($id) {
        $bool = $this->getCourseReportById($id);
        if($bool) {
            $sql = "DELETE FROM course_report WHERE id in ({$id})";
            return $this->sqltool->query($sql);
        }
        return $bool;
    }

    /**
    * 删除一行教授报告
    * @param id 指定的教授报告ID
    * @return 1维数组
    */
    public function deleteProfessorReportById($id) {
        $bool = $this->getProfessorReportById($id);
        if($bool) {
            $sql = "DELETE FROM professor_report WHERE id in ({$id})";
            return $this->sqltool->query($sql);
        }
        return $bool;
    }

    /**
    * 删除一行科目教授报告
    * @param id 指定的科目教授报告ID
    * @return 1维数组
    */
    public function deleteCourseProfessorReportById($id) {
        $bool = $this->getCourseProfessorReportById($id);
        if($bool) {
            $sql = "DELETE FROM course_prof_report WHERE id in ({$id})";
            return $this->sqltool->query($sql);
        }
        return $bool;
    }


    /**
     * 奖励用户课评发布
     * @param $id 课评ID
     * @param $credit 奖励积分
     * @return bool|\mysqli_result
     * @throws Exception
     */
    public function awardCreditById($id, $credit) {
        ($credit===0 || key_exists($credit, Credit::$addCourseRating)) or BasicTool::throwException("积分奖励数额无效");
        $result = $this->getCourseRatingById($id,true) or BasicTool::throwException("没找到该课评");
        intval($result['award'])=== -1 or BasicTool::throwException("该课评已被奖励");
        $userId = intval($result['user_id']) or BasicTool::throwException("课评用户ID不存在");
        $sql = "UPDATE course_rating SET award='".(int)Credit::$addCourseRating[$credit]['credit']."' WHERE id in ({$id})";
        $bool = $this->sqltool->query($sql);
        if($bool && $credit!==0) {
            $transactionModel = new TransactionModel();
            $transactionModel->systemAdjustCredit($userId, Credit::$addCourseRating[$credit],'course_rating',$id);
            $msgModel = new MsgModel();
            $title = $result['course_code_parent_title'] . " " . $result['course_code_child_title'];
            $grade = $credit===5 ? "优秀" : "有用";
            $msg = "恭喜，你的课评 {$title} 被评为{$grade}课评，获得奖励 ".Credit::$addCourseRating[$credit]["credit"];
            $msgModel->pushMsgToUser($userId,"course_rating", $id, $msg,28);
        }
        return $bool;
    }

    /**
     * 更新报告 (指定科目ID和教授ID)
     * @param $courseCodeId 指定的科目ID
     * @param $profId 指定的教授ID
     * @return array|bool 返回true如果成功, 失败则返回 [course_code_id=>[[course code id]], prof_id=>[[professor id]], log=>[[ error stack]]]
     */
    private function updateReports($courseCodeId, $profId, $updateTime=false) {
        $errorStack = array("course_code_id"=>$courseCodeId,"prof_id"=>$profId,"log"=>array());
        // Update course_prof_report
        $result = $this->getAnalyzedData($courseCodeId,$profId);
        if(!$result && $this->errorMsg!=="没有数据受到影响"){array_push($errorStack['log'],"Fail to retrieve average data for course_prof_report: ".$this->errorMsg);}
        $result = $this->handleReportChange("course_prof_report", $result, $courseCodeId, $profId, $updateTime);
        if(!$result && $this->errorMsg!=="没有数据受到影响"){array_push($errorStack['log'],"Fail to update course_prof_report: ".$this->errorMsg);}

        // Update course_report
        $result = $this->getAnalyzedData($courseCodeId,false);
        if(!$result && $this->errorMsg!=="没有数据受到影响"){array_push($errorStack['log'],"Fail to retrieve average data for course_report: ".$this->errorMsg);}
        $result = $this->handleReportChange("course_report", $result, $courseCodeId, false, $updateTime);
        if(!$result && $this->errorMsg!=="没有数据受到影响"){array_push($errorStack['log'],"Fail to update course_report: ".$this->errorMsg);}

        // Update professor_report
        $result = $this->getAnalyzedData(false, $profId);
        if(!$result && $this->errorMsg!=="没有数据受到影响"){array_push($errorStack['log'],"Fail to retrieve average data for prof_report: ".$this->errorMsg);}
        $result = $this->handleReportChange("professor_report", $result, false, $profId, $updateTime);
        if(!$result && $this->errorMsg!=="没有数据受到影响"){array_push($errorStack['log'],"Fail to update prof_report: ".$this->errorMsg);}

        if(sizeof($errorStack['log'])===0){
            return true;
        }else{
            return $errorStack;
        }
    }

    /**
     * 处理指定一个report的一行数据
     * @param $table Report表明
     * @param $result 新分析的数据数组
     * @param bool $courseCodeId
     * @param bool $profId
     * @return bool
     */
    private function handleReportChange($table, $result, $courseCodeId=false, $profId=false, $updateTime=false) {
        $arr = array();
        $additionalSql = "";
        if($courseCodeId){
            $arr["course_code_id"] = $courseCodeId;
            $additionalSql .= "course_code_id={$courseCodeId}";
        }

        if($profId){
            $arr["prof_id"] = $profId;
            $additionalSql .= (($additionalSql ? " AND " : "") . "prof_id={$profId}");
        }

        $sql = "SELECT * FROM {$table} WHERE {$additionalSql}";
        $report = $this->sqltool->getRowBySql($sql);
        $this->parseUpdateReportArray($result, $arr, $table, $updateTime);

        // modify report
        if($arr["rating_count"]){
            if($report){
                // update existing report
                return parent::updateRowById($table, $report["id"], $arr);
            } else {
                // add new report
                return parent::addRow($table, $arr);
            }
        }
//        if(!$arr["rating_count"]){
//            // remove empty rating report if exists
//            if($report){
//                return parent::realDeleteByFieldIn($table, "id", $report["id"]);
//            }
//        } else {
//            if($report){
//                // update existing report
//                return parent::updateRowById($table, $report["id"], $arr);
//            } else {
//                // add new report
//                return parent::addRow($table, $arr);
//            }
//        }
    }

    /**
     * 获取指定科目ID或、和教授ID获取新统计的报告
     * @param bool $courseCodeId
     * @param bool $profId
     * @return \一维关联数组
     */
    private function getAnalyzedData($courseCodeId=false, $profId=false){
        $sql = "SELECT ROUND(AVG(NULLIF(cr.content_diff,0)),1) AS avg_content, ROUND(AVG(NULLIF(cr.homework_diff,0)),1) AS avg_hw, ROUND(AVG(NULLIF(cr.test_diff,0)),1) AS avg_test, ROUND(AVG(NULLIF(cr.grade+0,11))) AS avg_grade, COUNT(*) AS sum_rating FROM course_rating cr WHERE ";
        $additionalSql = "";
        if($courseCodeId){
            $additionalSql .= "cr.course_code_id={$courseCodeId}";
        }
        if($profId){
            $additionalSql .= (($additionalSql ? " AND " : "") . "cr.prof_id={$profId}");
        }
        $sql .= $additionalSql;
        return $this->sqltool->getRowBySql($sql);
    }

    /**
     * 加载对应报告表格array
     * @param $result
     * @param $arr
     * @param $table
     */
    private function parseUpdateReportArray($result, &$arr, $table, $updateTime=false) {
        if ($result) {
            $arr["homework_diff"] = $result["avg_hw"] ?: 0.0;
            $arr["test_diff"] = $result["avg_test"] ?: 0.0;
            $arr["content_diff"] = $result["avg_content"] ?: 0.0;

            //动态调整除数
            $divisor = 3;
            if($arr["homework_diff"] == 0) $divisor -= 1;
            if($arr["test_diff"] == 0) $divisor -= 1;

            $arr["overall_diff"] = round(($arr["homework_diff"] + $arr["test_diff"] + $arr["content_diff"])/$divisor,1);
            $arr["rating_count"] = $result["sum_rating"] ?: 0;
            if($table=='course_prof_report' || $table=='course_report') {
                $arr["avg_grade"] = $result["avg_grade"] ?: 11;
            }
            if($updateTime){
                $arr['update_time'] = time();
            }
        }
    }

    /**
     * 加精
     * @param $courseRatingId
     */
    public function addEssence($courseRatingId){
        $sql = "UPDATE course_rating SET essence = 1 WHERE id in ({$courseRatingId})";
        $this->sqltool->query($sql);
    }

    /**
     * 消精
     * @param $courseRatingId
     */
    public function deleteEssence($courseRatingId){
        $sql = "UPDATE course_rating SET essence = 0 WHERE id in ({$courseRatingId})";
        $this->sqltool->query($sql);
    }


    /**
     * 更新全部报告
     * @return array
     * @throws Exception
     */
    public function updateAllReports(){
//        $this->cleanEmptyReports();
        $this->generateAllProfessorReports();
        $this->generateAllCourseReports();
        $sql = "SELECT cr.course_code_id, cr.prof_id FROM course_rating cr GROUP BY cr.course_code_id, cr.prof_id";
        $result = $this->sqltool->query($sql);
        if($result){
            $log = [];
            $count = mysqli_num_rows($result);
            $succeed = 0;
            foreach($result as $row){
                $result = $this->updateReports($row['course_code_id'],$row['prof_id'],false);
                if($result===true){
                    $succeed += 1;
                }else {
                    array_push($log, $result);
                }
            }
            return array("total"=>$count,"succeed"=>$succeed,"failed"=>($count-$succeed),"log"=>$log);
        }else{
            BasicTool::throwException("获取课评失败");
        }
    }

    public function cleanCourseProfReport(){
        $sql = "select cpr.id from course_prof_report as cpr LEFT JOIN course_rating as cr on cpr.prof_id = cr.prof_id and cpr.course_code_id = cr.course_code_id WHERE cr.id is null";
        $result = $this->sqltool->getListBySql($sql);
        $ids = "";
        if(count($result)>0){
            foreach($result as $row){
                $ids.= ($row['id'].',');
            }
            $ids = substr($ids,0,-1);
            $sql = "DELETE FROM course_prof_report WHERE id IN ({$ids})";
            $this->sqltool->query($sql);
            return count($result)."条无用数据被清除";
        }else{
            return "没有无用数据被清除";
        }
    }


//    /**
//     * 清除空报告
//     */
//    private function cleanEmptyReports() {
//        $sql = "DELETE FROM course_report WHERE rating_count=0";
//        $this->sqltool->query($sql);
//        $sql = "DELETE FROM professor_report WHERE rating_count=0";
//        $this->sqltool->query($sql);
//        $sql = "DELETE FROM course_prof_report WHERE rating_count=0";
//        $this->sqltool->query($sql);
//    }

    /**
     * 生成所有科目报告
     * @return bool|\mysqli_result
     */
    private function generateAllCourseReports() {
        $sql = "INSERT INTO course_report (course_code_id, homework_diff, test_diff, content_diff, overall_diff) SELECT cc.id, 0, 0, 0, 0 FROM course_code cc WHERE cc.parent_id>0 AND cc.id NOT IN (SELECT cr.course_code_id FROM course_report cr)";
        $result = $this->sqltool->query($sql);
        return $result;
    }

    /**
     * 生成所有教授报告
     * @return bool|\mysqli_result
     */
    private function generateAllProfessorReports() {
        $sql = "INSERT INTO professor_report (prof_id, homework_diff, test_diff, content_diff, overall_diff) SELECT p.id, 0, 0, 0, 0 FROM professor p WHERE p.id NOT IN (SELECT pr.prof_id FROM professor_report pr)";
        $result = $this->sqltool->query($sql);
        var_dump("Affected rows: ".$this->sqltool->getAffectedRows());
        return $result;
    }

}



?>
