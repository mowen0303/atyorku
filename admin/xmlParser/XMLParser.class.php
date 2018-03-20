<?php
namespace admin\xmlParser;   //-- 注意 --//

use \Model as Model;

class XMLParser extends Model
{
    /**更新数据表
     * @param array $array
     */
    public function updateCourseCodeTable($file_name){
        $array = $this->loadCourseXML($file_name);
        if (!$array){
            $this->errorMsg = "数据未受影响:XML文件加载失败";
            return false;
        }
        $bool = $this->addNonExistingCourseParents($array);
        if (!$bool)
            return false;
        $this->addAndUpdateCourseChildren($array);
        return true;
    }
    private function addNonExistingCourseParents($array){
        $existing_course_parents = array();
        $sql = "SELECT title FROM course_code WHERE parent_id = 0";
        $result = $this->sqltool->getListBySql($sql);
        if (!$result){
            $this->errorMsg="数据未受影响:加载学科失败";
            return false;
        }
        foreach ($result as $course_parent)
            $existing_course_parents[] = $course_parent["title"];
        $concat = "";
        foreach ($array["courseSubject"] as $subject){
            $subject_code = str_replace(" ","",$subject["courseSubjectCode"]);
            if (!in_array($subject_code,$existing_course_parents))
                $concat .=  "(0,'{$subject_code}','{$subject["courseSubjectTitle"]}',0,'',0),";
        }
        if (!$concat)
            return true;
        $concat = substr($concat, 0, -1);
        $sql = "INSERT INTO course_code (parent_id,title,full_title,credits,description,view_count) VALUES {$concat}";
        $bool = $this->sqltool->query($sql);
        if (!$bool){
            $this->errorMsg="数据未受影响:添加新学科失败";
            return false;
        }
        return true;
    }
    private function addAndUpdateCourseChildren($array){
        $concat = "";
        foreach ($array["courseSubject"] as $subject){
            $subject_code = str_replace(" ","",$subject["courseSubjectCode"]);
            $concat .= "'{$subject_code}',";
        }
        $concat = substr($concat, 0, -1);
        $sql = "SELECT t1.*,t2.title AS subject_code FROM course_code as t1, course_code as t2 WHERE t2.parent_id = 0 AND t2.title in ({$concat}) AND t1.parent_id = t2.id";
        $result = $this->sqltool->getListBySql($sql);
        $existing_courses = array();
        foreach ($result as $course)
            $existing_courses[] = str_replace(" ","",$course["subject_code"]) . " " . str_replace(" ","",$course["title"]);

        foreach ($array["courseSubject"] as $subject){
            $subject_courses = array();
            if (array_key_exists("courseCode",$subject["course"]))
                $subject_courses[] = $subject["course"];
            else
                $subject_courses = $subject["course"];
            foreach ($subject_courses as $course){
                $subject_code = explode(" ",$course["courseCode"])[0];
                $course_title = explode(" ",$course["courseCode"])[1];
                $arr = array();
                if (in_array($subject_code . " " . $course_title,$existing_courses)){
                    $sql = "SELECT t2.id FROM course_code AS t1 INNER JOIN course_code AS t2 ON t2.parent_id = t1.id WHERE t1.title in ('{$subject_code}') AND t2.title in ('{$course_title}')";
                    $id = $this->sqltool->getRowBySql($sql)["id"];
                    $arr["description"] = addslashes($course["description"]);
                    $this->updateRowById("course_code",$id,$arr);
                }
                else{
                    $sql = "SELECT id FROM course_code WHERE parent_id = 0 AND title in ('{$subject_code}')";
                    $parent_id = $this->sqltool->getRowBySql($sql)["id"];
                    $arr["title"] = $course_title;
                    $arr["parent_id"] = $parent_id;
                    $arr["full_title"] = $course["courseTitle"];
                    $arr["description"] = $course["description"];
                    $arr["credits"] = intval($course["credit"]);
                    $arr["view_count"] = 0;
                    $this->insert("course_code",$arr);
                }
            }
        }
    }

    /**加载course XML文件.XML文件必须跟model在同一个文件夹
     * @param string $file_name
     * @return array $array
     */
    private function loadCourseXML($file_name){
        $xml = simplexml_load_file($file_name);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        return $array;
    }

    public function insert($table, $arr, $debug = false)
    {
        $field = "";
        $value = "";
        foreach ($arr as $k => $v) {
            $v = addslashes($v);
            $field .= $k . ",";
            $value .= "'$v'" . ",";
        }
        $field = substr($field, 0, -1);
        $value = substr($value, 0, -1);
        $sql = "insert into {$table} ({$field}) values ({$value})";
        echo $debug ? $sql : null;
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            $this->idOfInsert = $this->sqltool->getInsertId();
            return true;
        }
        $this->errorMsg = "没有数据受到影响";
        return false;
    }
}

?>