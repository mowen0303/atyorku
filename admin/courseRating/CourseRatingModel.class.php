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
        $sql = "SELECT cr.*, u.alias, cc.title AS course_title, cc.full_title AS course_full_title, p.firstname prof_firstname, p.lastname prof_lastname, p.middlename prof_middlename FROM {$this->table} cr, user u, course_code cc, professor p WHERE cr.user_id=u.id AND cr.course_code_id=cc.id AND cr.prof_id=p.id";
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
    * 添加一行Course Code
    * @param title Course Code title
    * @param fullTitle Course Code full title
    * @param credits Course credit
    * @param parentId Course Code parent_id, 如果是父类，无需提供
    * @return bool
    */
    public function addCourseRating($courseCodeId, $userId, $profId, $contentDiff, $homeworkDiff, $testDiff, $hasTextbook, $grade='U', $comment, $recommendation, $year, $term) {
        $arr = [];
        $arr["course_code_id"] = $courseCodeId;
        $arr["user_id"] = $userId;
        $arr["prof_id"] = $profId;
        $arr["content_diff"] = $contentDiff;
        $arr["homework_diff"] = $homeworkDiff;
        $arr["test_diff"] = $testDiff;
        $arr["has_textbook"] = $hasTextbook;
        $arr["grade"] = $grade;
        $arr["comment"] = $comment;
        $arr["recommendation"] = $recommendation;
        $arr["year"] = $year;
        $arr["term"] = $term;
        $arr["publish_time"] = time();

        $bool = $this->addRow($this->table, $arr);
        if ($bool) {
            $sql = "UPDATE book_category SET books_count = (SELECT COUNT(*) from {$this->table} WHERE book_category_id in ({$bookCategoryId})) WHERE id in ({$bookCategoryId})";
            $this->sqltool->query($sql);
        }
        return $bool;

        if ($parentId != 0) {
            $sql = "SELECT * FROM course_code c WHERE c.id={$parentId}";
            $this->sqltool->getRowBySql($sql) or BasicTool::throwException("Course Code父类ID={$parentId} 不存在");
        }
        $sql = "SELECT * FROM {$this->table} WHERE parent_id={$parentId} AND title='{$title}' LIMIT 1";
        !$this->sqltool->getRowBySql($sql) or BasicTool::throwException("Course Code名称={$title} 已存在");
        $arr = array("title"=>$title, "full_title"=>$fullTitle, "credits"=>$credits, "parent_id"=>$parentId);
        return $this->addRow($this->table,$arr);
    }


    private function updateReports($courseCodeId, $profId) {
        $from = "SELECT AVG(cr.content_diff) AS avg_content, AVG(cr.homework_diff) AS avg_hw, AVG(cr.test_diff) AS avg_test, COUNT(*) AS sum_rating, SUM(cr.recommendation) AS sum_recommendation FROM course_rating cr WHERE cr.course_code_id={$courseCodeId} AND cr.prof_id={$profId}";

        $sql = "UPDATE course_prof_report cpr SET cpr.homework_diff=cr.avg_hw, cpr.content_diff=cr.avg_content, cpr.test_diff=cr.avg_test, cpr.overall_diff=(cr.avg_hw+cr.avg_content+cr.avg_test)/3, cpr.recommendation_ratio=CAST(cr.sum_recommendation AS Float)/CAST(cr.sum_rating AS Float) FROM ({$from} WHERE cr.course_code_id={$courseCodeId} AND cr.prof_id={$profId}) WHERE cpr.course_code_id={$courseCodeId} AND cpr.prof_id={$profId};"

        $sql .= "UPDATE course_report crpt SET crpt.homework_diff=cr.avg_hw, crpt.content_diff=cr.avg_content, crpt.test_diff=cr.avg_test, crpt.overall_diff=(cr.avg_hw+cr.avg_content+cr.avg_test)/3) FROM ({$from} WHERE cr.course_code_id={$courseCodeId}) WHERE crpt.course_code_id={$courseCodeId};"

        $sql .= "UPDATE professor_report pr SET pr.homework_diff=cr.avg_hw, pr.content_diff=cr.avg_content, pr.test_diff=cr.avg_test, pr.overall_diff=(cr.avg_hw+cr.avg_content+cr.avg_test)/3, pr.recommendation_ratio=(CAST(cr.sum_recommendation AS Float)/CAST(cr.sum_rating AS Float)) FROM ({$from} WHERE cr.prof_id={$profId}) WHERE cpr.prof_id={$profId};"

        $this->sqltool->multiQuery($sql);
    }


    /**
    * 通过ID删除一个CourseRating
    * @param id 要删除的Course Code ID
    * @return bool
    */
    public function deleteCourseRatingById($id) {
        $result = $this->getRowById($this->table, $id) or BasicTool::throwException("没有找到 Course Code");
        $parentId = $result["parent_id"];
        if ($parentId == 0) {
            $sql = "SELECT * FROM {$this->table} WHERE parent_id in ({$id}) LIMIT 1";
            !$this->sqltool->getRowBySql($sql) or BasicTool::throwException("存在 Course Code 子类");
        }
        $sql = "DELETE FROM {$this->table} WHERE id in ({$id})";
        return $this->sqltool->query($sql);
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

}



?>
