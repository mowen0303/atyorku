<?php
namespace admin\courseRating;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
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
    * 通过 Parent ID 来获取 Course code, 获取父类Course Code, $id=0
    * @param id course code parent id
    * @return 一维数组
    */
    public function getListOfCourseRating($query=false, $pageSize=20) {
        $sql = "SELECT cr.*, u.id AS user_id, u.name AS user, cc.id AS course_code_child_id, cc2.id AS course_code_parent_id, cc.title AS course_code_child_title, cc2.title AS course_code_parent_title, cc.full_title AS course_full_title, p.id AS prof_id, CONCAT(p.firstname, ' ', p.lastname) AS prof_name FROM {$this->table} cr, user u, course_code cc, course_code cc2, professor p WHERE cr.user_id=u.id AND cr.course_code_id=cc.id AND cc.parent_id=cc2.id AND cr.prof_id=p.id";
        $countSql = "SELECT COUNT(*) FROM {$this->table} cr, user u, course_code cc, professor p WHERE cr.user_id=u.id AND cr.course_code_id=cc.id AND cr.prof_id=p.id";
        if ($query) {
            $sql = "{$sql} AND ({$query})";
            $countSql = "{$countSql} AND ({$query})";
        }
        $sql = "{$sql} ORDER BY `year` DESC, `term`, `publish_time` DESC";
        $arr = parent::getListWithPage($this->table, $sql, $countSql, $pageSize);
         // Format publish time and enroll year
        foreach ($arr as $k => $v) {
            $t = $v["publish_time"];
            if($t) $arr[$k]["publish_time"] = BasicTool::translateTime($t);
        }
        return $arr;
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
            $this->addRow($this->table, $arr);
        } else if($flag=='update') {
            $this->updateRowById($this->table, $id, $arr);
        } else {
            BasicTool::throwException("Unknown flag.");
        }
        if ($bool) {
            $this->updateReports($courseCodeId, $profId);
        }
        return $bool;
    }


    private function updateReports($courseCodeId, $profId) {
        // $from = "SELECT AVG(cr.content_diff) AS avg_content, AVG(cr.homework_diff) AS avg_hw, AVG(cr.test_diff) AS avg_test, COUNT(*) AS sum_rating, SUM(cr.recommendation) AS sum_recommendation FROM course_rating cr WHERE cr.course_code_id={$courseCodeId} AND cr.prof_id={$profId}";
        //
        // $sql = "UPDATE course_prof_report cpr SET cpr.homework_diff=cr.avg_hw, cpr.content_diff=cr.avg_content, cpr.test_diff=cr.avg_test, cpr.overall_diff=(cr.avg_hw+cr.avg_content+cr.avg_test)/3, cpr.recommendation_ratio=CAST(cr.sum_recommendation AS Float)/CAST(cr.sum_rating AS Float) FROM ({$from} WHERE cr.course_code_id={$courseCodeId} AND cr.prof_id={$profId}) WHERE cpr.course_code_id={$courseCodeId} AND cpr.prof_id={$profId};"
        //
        // $sql .= "UPDATE course_report crpt SET crpt.homework_diff=cr.avg_hw, crpt.content_diff=cr.avg_content, crpt.test_diff=cr.avg_test, crpt.overall_diff=(cr.avg_hw+cr.avg_content+cr.avg_test)/3) FROM ({$from} WHERE cr.course_code_id={$courseCodeId}) WHERE crpt.course_code_id={$courseCodeId};"
        //
        // $sql .= "UPDATE professor_report pr SET pr.homework_diff=cr.avg_hw, pr.content_diff=cr.avg_content, pr.test_diff=cr.avg_test, pr.overall_diff=(cr.avg_hw+cr.avg_content+cr.avg_test)/3, pr.recommendation_ratio=(CAST(cr.sum_recommendation AS Float)/CAST(cr.sum_rating AS Float)) FROM ({$from} WHERE cr.prof_id={$profId}) WHERE cpr.prof_id={$profId};"
        //
        // $this->sqltool->multiQuery($sql);
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



}



?>
