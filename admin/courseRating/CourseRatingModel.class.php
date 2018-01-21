<?php
namespace admin\courseRating;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class CourseRatingModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "course_rating";
    }

    /**
    * 通过ID获取Course
    * @param id 要查询的CourseRating ID
    * @return 一维数组
    */
    public function getCourseRatingById($id) {
        return $this->getRowById($this->table,$id);
    }


    /**
    * 获取一页课评
    * @param query additional query
    * @param pageSize 每页query数量
    * @return 2维数组
    */
    public function getListOfCourseRating($query=false, $pageSize=20) {
        $select = "SELECT cr.*,u.id AS user_id,u.name AS user_name,u.user_class_id,u.img AS user_img,u.alias AS user_alise,u.gender AS user_gender,u.major AS user_major,u.enroll_year AS user_enroll_year,u.degree AS user_degree,uc.is_admin,cc.id AS course_code_child_id, cc2.id AS course_code_parent_id, cc.title AS course_code_child_title, cc2.title AS course_code_parent_title, cc.full_title AS course_full_title, p.id AS prof_id, CONCAT(p.firstname, ' ', p.lastname) AS prof_name";
        $from = "FROM {$this->table} cr, user u, user_class uc, course_code cc, course_code cc2, professor p";
        $where = "WHERE cr.user_id=u.id AND cr.user_id=uc.id AND cr.course_code_id=cc.id AND cc.parent_id=cc2.id AND cr.prof_id=p.id";
        $sql = "{$select} {$from} {$where}";
        $countSql = "SELECT COUNT(*) {$from} {$where}";
        if ($query) {
            $sql = "{$sql} AND ({$query})";
            $countSql = "{$countSql} AND ({$query})";
        }
        $sql = "{$sql} ORDER BY `year` DESC, `term`, `publish_time` DESC";
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
    public function getListOfCourseRatingByCourseId($courseId, $pageSize=20) {
        $query = "course_code_id in ({$courseId})";
        return $this->getListOfCourseRating($query, $pageSize);
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
    * Add or Update a Course Rating
    *
    * @param flag 'add' | 'update'
    * @param courseCodeId Course Code ID
    * @param userId user ID
    * @param profId Professor ID
    * @param contentDiff Content difficulty
    * @param homeworkDiff Homework Difficulty
    * @param testDiff Test Difficulty
    * @param hasTextbook if textbook is required
    * @param grade Letter Grade
    * @param comment comment to the course
    * @param recommendation if recommend the course
    * @param year attended year
    * @param term attended term
    * @param id modified course rating id (required when for 'update')
    * @return bool
    * @throws ValidationExceptions Use try catch
    */
    public function modifyCourseRating($flag, $courseCodeId, $userId, $profId, $contentDiff, $homeworkDiff, $testDiff, $hasTextbook, $grade='', $comment, $recommendation, $year, $term, $id) {
        // Validations
        $this->isValidDiff($contentDiff) or BasicTool::throwException("内容困难等级 ({$contentDiff}) 不存在");
        $this->isValidDiff($homeworkDiff) or BasicTool::throwException("作业困难等级 ({$homeworkDiff}) 不存在");
        $this->isValidDiff($testDiff) or BasicTool::throwException("考试困难等级 ({$testDiff}) 不存在");
        $this->isValidGrade($grade) or BasicTool::throwException("该成绩选项 ({$grade}) 不存在");
        $this->isValidYear($year) or BasicTool::throwException("该学年 ({$year}) 不存在");
        $this->isValidTerm($term) or BasicTool::throwException("该学期 ({$term}) 不存在");

        $arr = [];
        $arr["course_code_id"] = $courseCodeId;
        $arr["user_id"] = $userId;
        $arr["prof_id"] = $profId;
        $arr["content_diff"] = $contentDiff;
        $arr["homework_diff"] = $homeworkDiff;
        $arr["test_diff"] = $testDiff;
        $arr["has_textbook"] = $hasTextbook ? 1 : 0;
        $arr["grade"] = $grade;
        $arr["comment"] = $comment;
        $arr["recommendation"] = $recommendation;
        $arr["year"] = $year;
        $arr["term"] = $term;
        $bool;
        if($flag=='add') {
            $arr["publish_time"] = time();
            $bool = $this->addRow($this->table, $arr);
        } else if($flag=='update') {
            $bool = $this->updateRowById($this->table, $id, $arr);
        } else {
            BasicTool::throwException("Unknown flag.");
        }
        if ($bool) {
            $this->updateReports($courseCodeId, $profId);
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
            $this->updateReports($result["course_code_id"], $result["prof_id"]);
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
    * validate difficulty
    * @param diff 用户提供的 difficulty level
    * @return bool
    */
    private function isValidDiff($diff) {
        $diff = intval($diff);
        return $diff > 0 && $diff < 11;
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
        if($courseParentTitle) $q .= " AND c2.title='{$courseParentTitle}'";
        if($courseChildTitle) $q .= " AND c1.title='{$courseChildTitle}'";
        $sql = "SELECT cr.*, c2.id AS course_code_parent_id, c1.title AS course_code_child_title, c1.full_title AS course_full_title, c2.title AS course_code_parent_title FROM course_report cr, course_code c1, course_code c2 WHERE c1.parent_id=c2.id AND cr.course_code_id=c1.id{$q}";
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
        if($profName) $q .= " AND CONCAT(p.firstname, ' ', p.lastname)='{$profName}'";
        $sql = "SELECT pr.*, CONCAT(p.firstname, ' ', p.lastname) AS prof_name FROM professor_report pr, professor p WHERE pr.prof_id=p.id{$q}";
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
    * 更新报告 (指定科目ID和教授ID)
    * @param courseCodeId 指定的科目ID
    * @param profId 指定的教授ID
    */
    private function updateReports($courseCodeId, $profId) {
        $querySql = "SELECT AVG(cr.content_diff) AS avg_content, AVG(cr.homework_diff) AS avg_hw, AVG(cr.test_diff) AS avg_test, ROUND(AVG(NULLIF(cr.grade+0,1))) AS avg_grade, COUNT(*) AS sum_rating, SUM(cr.recommendation) AS sum_recommendation FROM course_rating cr WHERE";
        // Update course_prof_report
        $sql = "{$querySql} cr.course_code_id={$courseCodeId} AND cr.prof_id={$profId}";

        $result = $this->sqltool->getRowBySql($sql);
        $arr = array("course_code_id"=>$courseCodeId, "prof_id"=>$profId);
        $this->parseUpdateReportArray($result, $arr, 'course_prof_report');

        $sql = "SELECT * FROM course_prof_report WHERE course_code_id={$courseCodeId} AND prof_id={$profId}";
        $courseProfReport = $this->sqltool->getRowBySql($sql);
        if($courseProfReport){
            parent::updateRowById("course_prof_report", $courseProfReport["id"], $arr);
        } else {
            parent::addRow("course_prof_report", $arr);
        }


        // Update course_report
        $sql = "{$querySql} cr.course_code_id={$courseCodeId}";

        $result = $this->sqltool->getRowBySql($sql);
        $arr = array("course_code_id"=>$courseCodeId);
        $this->parseUpdateReportArray($result, $arr, 'course_report');

        $sql = "SELECT * FROM course_report WHERE course_code_id={$courseCodeId}";
        $courseReport = $this->sqltool->getRowBySql($sql);
        if($courseReport){
            parent::updateRowById("course_report", $courseReport["id"], $arr);
        } else {
            parent::addRow("course_report", $arr);
        }

        // Update professor_report
        $sql = "{$querySql} cr.prof_id={$profId}";

        $result = $this->sqltool->getRowBySql($sql);
        $arr = array("prof_id"=>$profId);
        $this->parseUpdateReportArray($result, $arr, 'professor_report');

        $sql = "SELECT * FROM professor_report WHERE prof_id={$profId}";
        $profReport = $this->sqltool->getRowBySql($sql);
        if($profReport){
            parent::updateRowById("professor_report", $profReport["id"], $arr);
        } else {
            parent::addRow("professor_report", $arr);
        }
    }

    /**
    * 加载对应报告表格array
    */
    private function parseUpdateReportArray($result, &$arr, $table) {
        if ($result) {
            $arr["homework_diff"] = $result["avg_hw"] ?: 0;
            $arr["test_diff"] = $result["avg_test"] ?: 0;
            $arr["content_diff"] = $result["avg_content"] ?: 0;
            $arr["overall_diff"] = intval(round(($arr["homework_diff"] + $arr["test_diff"] + $arr["content_diff"])/3));
            $arr["rating_count"] = $result["sum_rating"] ?: 0;
            if($table=='course_prof_report' || $table=='professor_report') {
                $arr["recommendation_ratio"] = max(0, $result["sum_recommendation"]) / max($result["sum_rating"],1);
            }
            if($table=='course_prof_report' || $table=='course_report') {
                $arr["avg_grade"] = $result["avg_grade"] ?: 1;
            }
        }
    }

}



?>
