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

    public function getTimetableCourses($user_id,$term_year=false,$term_semester=false){
        $condition = "user_id in ({$user_id})";
        if ($term_year) $condition .= " AND term_year in ('{$term_year}')";
        if ($term_semester){
            if (strtolower($term_semester) == "fall"){
                $term_semester = "'Fall','Year'";
            }else if (strtolower($term_semester) == "winter"){
                $term_semester = "'Winter','Year'";
            }else if (strtolower($term_semester) == "summer 1"){
                $term_semester = "'Summer','Summer 1'";
            }else if (strtolower($term_semester == "summer 2")){
                $term_semester = "'Summer','Summer 2'";
            }
            $condition .= " AND term_semester in ({$term_semester})";
        }
        $sql = "SELECT timetable.*, course_code.title AS course_parent_title FROM (SELECT timetable.*,course_code.parent_id,course_code.title AS course_child_title FROM (SELECT * FROM timetable WHERE {$condition}) AS timetable INNER JOIN course_code ON timetable.course_code_id = course_code.id) AS timetable INNER JOIN course_code ON timetable.parent_id = course_code.id";
        return $this->sqltool->getListBySql($sql);
    }

    public function getTerms($user_id){
        $sql = "SELECT COUNT(*) AS count, term_year, term_semester FROM {$this->table} WHERE user_id in ({$user_id}) GROUP BY term_year, term_semester";
        $flag=false;
        $result = $this->sqltool->getListBySql($sql);
        if (!$result)
            return false;
        foreach ($result as $i => $_result){
            if ($_result["term_semester"] == "Year"){
                $isMergedFall = false;
                $isMergedWinter = false;
                foreach ($result as $j => $__result){
                    if ($__result["term_year"] == $_result["term_year"] && $__result["term_semester"] == "Fall"){
                        $result[$j]["count"] = $result[$j]["count"] + $result[$i]["count"];
                        $isMergedFall = true;
                    }
                    if ($__result["term_year"] == $_result["term_year"] && $__result["term_semester"] == "Winter"){
                        $result[$j]["count"] = $result[$j]["count"] + $result[$i]["count"];
                        $isMergedWinter = true;
                    }
                }
                if (!$isMergedFall){
                    $result[] = array("term_year"=>$_result["term_year"],"term_semester"=>"Fall","count"=>$_result["count"]);
                }
                if (!$isMergedWinter){
                    $result[] = array("term_year"=>$_result["term_year"],"term_semester"=>"Winter","count"=>$_result["count"]);
                }
                unset($result[$i]);
                $flag = true;
            }else if ($_result["term_semester"] == "Summer"){
                $isMergedSummer1 = false;
                $isMergedSummer2 = false;
                foreach ($result as $j => $__result){
                    if ($__result["term_year"] == $_result["term_year"] && $__result["term_semester"] == "Summer 1"){
                        $result[$j]["count"] = $result[$j]["count"] + $result[$i]["count"];
                        $isMergedSummer1 = true;
                    }
                    if ($__result["term_year"] == $_result["term_year"] && $__result["term_semester"] == "Summer 2"){
                        $result[$j]["count"] = $result[$j]["count"] + $result[$i]["count"];
                        $isMergedSummer2 = true;
                    }
                }
                if (!$isMergedSummer1){
                    $result[] = array("term_year"=>$_result["term_year"],"term_semester"=>"Summer 1","count"=>$_result["count"]);
                }
                if (!$isMergedSummer2){
                    $result[] = array("term_year"=>$_result["term_year"],"term_semester"=>"Summer 2","count"=>$_result["count"]);
                }
                unset($result[$i]);
                $flag = true;
            }

        }

        if ($flag){
            $results = array();
            foreach ($result as $_result){
                $results[] = $_result;
                $result = $results;
            }
        }
        return $result;
    }

    public function getTermYears($user_id){
        $sql = "SELECT COUNT(*) AS count, term_year FROM {$this->table} WHERE user_id in ({$user_id}) GROUP BY term_year";
        return $this->sqltool->getListBySql($sql);
    }

    public function getUserList(){
        $sql = "SELECT user.* FROM (SELECT user_id FROM {$this->table} GROUP BY user_id) AS user_list INNER JOIN user ON user_list.user_id = user.id ORDER BY user.id DESC
";
        $countSql = "SELECT COUNT(*) FROM (SELECT user_id FROM {$this->table} GROUP BY user_id) AS user_list";
        return $this->getListWithPage($this->table,$sql,$countSql,20);
    }

    public function updateTimetable($courses,$user_id){
        if (count($courses)>0){
            $term_year = "";
            $temp = array();
            foreach ($courses as $course){
                if ($course["term_year"]){
                    if (!in_array($course["term_year"],$temp)){
                        $term_year .= "'{$course['term_year']}',";
                        $temp[] = $course["term_year"];
                    }
                }
            }
            $term_year = substr($term_year, 0, -1);
            $res = $this->deleteTimetableByTermYear($user_id,$term_year);
            if (!$res){
                $this->errorMsg = "更新课程表失败:删除旧课程表失败";
                return false;
            }
            $res = $this->addCourses($courses,$user_id);
            if (!$res){
                $this->errorMsg = "添加课程表失败";
                return false;
            }
            return true;
        }else{
            return true;
        }
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

    public function deleteTimetableByTermYear($user_id,$term_year){
        $sql = "DELETE FROM {$this->table} WHERE user_id IN ({$user_id}) AND term_year IN ({$term_year})";
        return $this->sqltool->query($sql);
    }

    public function parseTermYear($term){
        preg_match_all("/[0-9]+/",$term,$matches);
        if (count($matches[0]) == 2)
            $term_year = $matches[0][0]."-".$matches[0][1];
        else
            $term_year = $matches[0][0];
        return $term_year;
    }

    public function parseTermSemester($term_semester){
        $term_semester = strtoupper($term_semester);
        if ($term_semester == "F")
            return "Fall";
        else if ($term_semester == "W")
            return "Winter";
        else if ($term_semester == "Y")
            return "Year";
        else if ($term_semester == "S1")
            return "Summer 1";
        else if ($term_semester == "S2")
            return "Summer 2";
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

