<?php
namespace admin\courseSolution;
use \Model as Model;

class CourseSolutionModel extends Model
{
    function addSolution($question_id,$answerer_user_id,$description,$img_id_1,$img_id_2,$img_id_3){
        $arr["question_id"]=$question_id;
        $arr["description"]=$description;
        $arr["img_id_1"]=$img_id_1;
        $arr["img_id_2"]=$img_id_2;
        $arr["img_id_3"]=$img_id_3;
        $arr["time_posted"] = time();
        $arr["time_approved"] = 0;
        $arr["questioner_user_id"] = $this->getRowById("course_question",$question_id)["questioner_user_id"];
        $arr["answerer_user_id"] = $answerer_user_id;
        $arr["count_views"] = 0;
        $bool = $this->addRow("course_solution",$arr);
        if ($bool) {
            $sql = "UPDATE course_question SET count_solutions = (SELECT COUNT(*) FROM course_solution WHERE question_id = {$question_id}) WHERE id = {$question_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }
    /*
     * controller确保指定修改的答案并没有被采纳
     */
    function updateSolution($id,$description,$img_id_1,$img_id_2,$img_id_3){
        $arr["description"] = $description;
        $arr["img_id_1"] = $img_id_1;
        $arr["img_id_2"] = $img_id_2;
        $arr["img_id_3"] = $img_id_3;
        return $this->updateRowById("course_solution", $id,$arr);
    }
    /*
     * controller确保指定删除的答案并没有被采纳
     */
    function deleteSolution($id){
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

    /*
     *@return solution|false
     */
    function getApprovedSolutionByQuestionId($question_id){
        $sql = "SELECT * FROM course_solution WHERE question_id = {$question_id} AND time_approved !=0";
        $solution = $this->sqltool->getRowBySql($sql);
        if ($sql){
            return $solution;
        }
        else
            return false;
    }

    function getSolutionsByQuestionId($question_id){
        $sql = "SELECT * FROM course_solution WHERE question_id = {$question_id} AND time_approved = 0 ORDER BY time_posted DESC";
        $countSql = "SELECT COUNT(*) FROM course_solution WHERE question_id = {$question_id} AND time_approved = 0 ORDER BY time_posted DESC";
        return $this->getListWithPage("course_solution", $sql, $countSql,20);
    }

    function getInsertId(){
        return $this->sqltool->getInsertId();
    }
}
?>