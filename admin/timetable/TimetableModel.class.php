<?php
namespace admin\timetable;   //-- 注意 --//
use \Model as Model;
class TimetableModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "timetable";
    }

    public function updateTimetable($courses,$user_id,$term_year,$term_semester){
        $res = $this->deleteTimetableByTerm($user_id,$term_year,$term_semester);
        if (!$res){
            $this->errorMsg = "更新课程表失败:删除旧课程表失败";
            return false;
        }
        $res = $this->addCourses($courses,$user_id);
        if (!$res){
            $this->errorMsg = "添加课程表失败";
            return false;
        }
        $sql = "SELECT timetable.*, course_code.title AS course_parent_title FROM (SELECT timetable.*,course_code.parent_id,course_code.title AS course_child_title FROM (SELECT * FROM timetable WHERE user_id IN ({$user_id}) AND term_year IN ('{$term_year}') AND term_semester IN ({$term_semester})) AS timetable INNER JOIN course_code ON timetable.course_code_id = course_code.id) AS timetable INNER JOIN course_code ON timetable.parent_id = course_code.id";
        $result = $this->sqltool->getListBySql($sql);
        if (!$result)
            return [];
        else
            return $result;
    }

    private function addCourses($courses,$user_id){
        $concat = "";
        $time = time();
        foreach ($courses as $course){
            $concat .= "({$user_id},{$course['course_code_id']},'{$course['term_year']}','{$course['term_semester']}','{$course['section']}','{$course['schedule']}',$time),";
        };
        $concat = substr($concat, 0, -1);
        $sql = "INSERT INTO {$this->table} (user_id,course_code_id,term_year,term_semester,section,schedule,time) VALUES {$concat}";
        return $this->sqltool->query($sql);
    }

    public function getCourseCodeByString($parentCode, $childCode) {
        $sql = "";
        if($childCode){
            $sql = "SELECT c.id AS course_child_id, c.title AS course_child_title, p.id AS course_parent_id, p.title AS course_parent_title FROM course_code c, course_code p WHERE c.parent_id=p.id AND p.title='{$parentCode}' AND c.title='{$childCode}'";
        } else {
            $sql = "SELECT id AS course_parent_id, title AS course_parent_title FROM course_code WHERE title='{$parentCode}'";
        }
        $result = $this->sqltool->getRowBySql($sql);
        if (!$result)
            return false;
        return $result;
    }

    public function deleteTimetableByTerm($user_id,$term_year,$term_semester){
        $sql = "DELETE FROM {$this->table} WHERE user_id IN ({$user_id}) AND term_year IN ('{$term_year}') AND term_semester IN ({$term_semester})";
        return $this->sqltool->query($sql);
    }

    public function extractTermSemester($term){
        $term_LOWER_CASE = strtolower($term);
        $term_semester="''";
        if (strpos($term_LOWER_CASE, "fall/winter") !== false){
            $term_semester = "'Fall','Winter','Year'";
        }
        else if (strpos($term_LOWER_CASE, "summer") !== false){
            $term_semester = "'Summer','Summer1','Summer2";
        }
        return $term_semester;
    }

    public function extractTermYear($term){
        preg_match_all("/[0-9]+/",$term,$matches);
        if (count($matches[0]) == 2)
            $term_year = $matches[0][0]."-".$matches[0][1];
        else
            $term_year = $matches[0][0];
        return $term_year;
    }

    public function parseTermSemester($term_semester){
        if ($term_semester == "F")
            return "Fall";
        else if ($term_semester == "W")
            return "Winter";
        else if ($term_semester == "Y")
            return "Year";
        else if ($term_semester == "SU1")
            return "Summer1";
        else if ($term_semester == "SU2")
            return "Summer2";
        else
            return "Summer";
    }

    public function parseDay($str){
        if (strpos($str,"Monda") !== false){
            return 1;
        }
        else if (strpos($str,"uesda") !== false){
            return 2;
        }
        else if (strpos($str,"ednesda") !== false){
            return 3;
        }
        else if (strpos($str,"hursda") !== false){
            return 4;
        }else{
            return 5;
        }
    }
}

