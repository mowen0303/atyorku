<?php
namespace admin\courseSolution;
use \Model as Model;

class CourseSolutionModel extends Model
{
    /**添加答案
     * @param int $question_id 提问id
     * @param int $answerer_user_id 回答者id
     * @param String $description 答案
     * @param int $img_id_1
     * @param int $img_id_2
     * @param int $img_id_3
     * @return bool
     */
    function addSolution($question_id,$answerer_user_id,$description,$img_id_1,$img_id_2,$img_id_3){
        $arr["question_id"]=$question_id;
        $arr["description"]=$description ? $description : "";
        $arr["img_id_1"]=$img_id_1 ? $img_id_1 : 0;
        $arr["img_id_2"]=$img_id_2 ? $img_id_2 : 0;
        $arr["img_id_3"]=$img_id_3 ? $img_id_3 : 0;
        $arr["time_posted"] = time();
        $arr["time_approved"] = 0;
        $arr["questioner_user_id"] = $this->getRowById("course_question",$question_id)["questioner_user_id"];
        $arr["answerer_user_id"] = $answerer_user_id;
        $arr["count_views"] = 0;
        $bool = $this->addRow("course_solution",$arr);
        $insertId = $this->getInsertId();
        if ($bool) {
            $sql = "UPDATE course_question SET count_solutions = (SELECT COUNT(*) FROM course_solution WHERE question_id = {$question_id}) WHERE id = {$question_id}";
            $this->sqltool->query($sql);
        }
        return $bool?$insertId:false;
    }

    /**更改答案
     * @param int $id 答案id
     * @param String $description 答案
     * @param int $img_id_1
     * @param int $img_id_2
     * @param int $img_id_3
     * @return bool
     */
    function updateSolution($id,$description,$img_id_1,$img_id_2,$img_id_3){
        $arr["description"] = $description ? $description : "";
        $arr["img_id_1"] = $img_id_1 ? $img_id_1 : 0;
        $arr["img_id_2"] = $img_id_2 ? $img_id_2 : 0;
        $arr["img_id_3"] = $img_id_3 ? $img_id_3 : 0;
        return $this->updateRowById("course_solution", $id,$arr);
    }

    /**删除答案
     * @param int|array $id 答案id
     * @return bool|\mysqli_result
     */
    function deleteSolutionById($id){
        if (is_array($id)){
            $sql = "SELECT * FROM course_solution WHERE id = {$id[0]}";
            $question_id = $this->sqltool->getRowBySql($sql)["question_id"];
            $concat = null;
            foreach($id as $i){
                $i = $i+0;
                $i = $i.",";
                $concat = $concat.$i;
            }
            $concat = substr($concat,0,-1);
            $sql = "DELETE FROM course_solution WHERE id in ({$concat})";
            $bool = $this->sqltool->query($sql);
        }

        else {
            $sql = "SELECT * FROM course_solution WHERE id = {$id}";
            $question_id = $this->sqltool->getRowBySql($sql)["question_id"];
            $sql = "DELETE FROM course_solution WHERE id in ({$id})";
            $bool = $this->sqltool->query($sql);
        }

        if ($bool) {
            $sql = "UPDATE course_question SET count_solutions = (SELECT COUNT(*) from course_solution WHERE question_id = {$question_id}) WHERE id = {$question_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    function getSolutionById($id){
        return $this->getRowById("course_solution",$id);
    }

    /**查询被采纳的答案
     * @param $question_id 答案id
     * @return \一维关联数组
     */
    function getApprovedSolutionByQuestionId($question_id){
        $sql = "SELECT course_solution.*, image.url as img_url_1 FROM (SELECT course_solution.*,user.alias,user.gender,user.enroll_year,user.img as profile_img_url,user.major FROM (SELECT * FROM course_solution WHERE question_id = {$question_id} AND time_approved !=0) AS course_solution INNER JOIN user on course_solution.answerer_user_id = user.id) AS course_solution LEFT JOIN image ON course_solution.img_id_1 = image.id";
        $solution = $this->sqltool->getRowBySql($sql);
        return $solution;
    }

    /**查询未被采纳的答案
     * @param $question_id 答案ID
     * @return array
     */
    function getSolutionsByQuestionId($question_id){
        $sql = "SELECT course_solution.*, image.url as img_url_1 FROM (SELECT course_solution.*,user.alias,user.gender,user.enroll_year,user.img as profile_img_url,user.major FROM (SELECT * FROM course_solution WHERE question_id = {$question_id} AND time_approved = 0) AS course_solution INNER JOIN user on course_solution.answerer_user_id = user.id) AS course_solution LEFT JOIN image ON course_solution.img_id_1 = image.id ORDER BY time_posted DESC";
        $countSql = "SELECT COUNT(*) FROM course_solution WHERE question_id = {$question_id} AND time_approved = 0 ORDER BY time_posted DESC";
        return $this->getListWithPage("course_solution", $sql, $countSql,20);
    }

    function getInsertId(){
        return $this->sqltool->getInsertId();
    }
}
?>