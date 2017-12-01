<?php
namespace admin\courseQuestion;
use \Model as Model;

class CourseQuestionModel extends Model
{
    /*
     * controller必须先验证用户是否有足够的积分，并扣除积分
     */
    function addQuestion($course_code_id,$prof_id, $questioner_user_id, $description, $img_id_1, $img_id_2, $img_id_3, $reward_amount)
    {

        $arr["course_code_id"] = $course_code_id;
        $arr["prof_id"] = $prof_id;

        $arr["description"] = $description;
        $arr["img_id_1"] = $img_id_1;
        $arr["img_id_2"] = $img_id_2;
        $arr["img_id_3"] = $img_id_3;

        $arr["time_posted"] = time();
        $arr["time_solved"] = 0;

        $arr["reward_amount"] = $reward_amount;
        $arr["count_solutions"] = 0;
        $arr["count_views"] = 0;

        $arr["questioner_user_id"] = $questioner_user_id;
        $arr["solution_id"] = 0;
        $arr["answerer_user_id"] = 0;

        $bool = $this->addRow("course_question", $arr);
        if ($bool) {
            $sql = "UPDATE course_report SET count_questions = (SELECT COUNT(*) FROM course_question WHERE course_code_id = {$course_code_id} AND solution_id =0), count_solved_questions=(SELECT COUNT(*) FROM course_question WHERE course_code_id={$course_code_id} AND solution_id !=0) WHERE course_code_id = {$course_code_id}";
            $this->sqltool->query($sql);
            $sql = "UPDATE course_prof_report SET count_questions = (SELECT COUNT(*) FROM course_question WHERE course_code_id = {$course_code_id} AND prof_id={$prof_id} AND solution_id=0), count_solved_questions=(SELECT COUNT(*) FROM course_question WHERE course_code_id={$course_code_id} AND prof_id={$prof_id} AND solution_id !=0) WHERE course_code_id = {$course_code_id} AND prof_id={$prof_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    /*
     * Controller核对管理员权限,确保提问者没还有采纳答案.
    */
    function updateQuestion($id,$description, $img_id_1, $img_id_2, $img_id_3, $reward_amount)
    {
        $arr["description"] = $description;
        $arr["img_id_1"] = $img_id_1;
        $arr["img_id_2"] = $img_id_2;
        $arr["img_id_3"] = $img_id_3;
        $arr["reward_amount"] = $reward_amount;
        return $this->updateRowById("course_question", $id,$arr);
    }

    function updateRewardAmount($id, $reward_amount)
    {
        $arr["reward_amount"] = $reward_amount;
        return $this->updateRowById("course_question", $id,$arr);
    }
    /*
     * 删除之后退还积分,controller验证删除的问题没有被采纳
     **/
    function deleteQuestion($id)
    {
        if (is_array($id)) {
            $question = $this->getQuestionById($id[0]);
            $course_code_id = $question["course_code_id"];
            $prof_id = $question["prof_id"];
            $concat = null;
            foreach ($id as $i) {
                $i = $i + 0;
                $i = $i . ",";
                $concat = $concat . $i;
            }
            $concat = substr($concat, 0, -1);
            $sql = "DELETE FROM course_question WHERE id in ({$concat})";
            $bool = $this->sqltool->query($sql);
        } else {
            $question = $this->getQuestionById($id);
            $course_code_id = $question["course_code_id"];
            $prof_id = $question["prof_id"];
            $sql = "DELETE FROM course_question WHERE id in ({$id})";
            $bool = $this->sqltool->query($sql);
        }

        //更新count_questions
        if ($bool) {
            $sql = "UPDATE course_report SET count_questions = (SELECT COUNT(*) FROM course_question WHERE course_code_id = {$course_code_id} AND solution_id =0), count_solved_questions=(SELECT COUNT(*) FROM course_question WHERE course_code_id={$course_code_id} AND solution_id !=0) WHERE course_code_id = {$course_code_id}";
            $this->sqltool->query($sql);
            $sql = "UPDATE course_prof_report SET count_questions = (SELECT COUNT(*) FROM course_question WHERE course_code_id = {$course_code_id} AND prof_id={$prof_id} AND solution_id=0), count_solved_questions=(SELECT COUNT(*) FROM course_question WHERE course_code_id={$course_code_id} AND prof_id={$prof_id} AND solution_id !=0) WHERE course_code_id = {$course_code_id} AND prof_id={$prof_id}";
            $this->sqltool->query($sql);
        }
        return $bool;
    }

    function getQuestionById($id)
    {
        return $this->getRowById("course_question", $id);

    }

    /*
     * $flag = 1 查询已解决的提问
     * $flag = 0 查询未解决的提问
     */
    function getQuestionsByCourseCodeIdProfId($course_code_id,$prof_id, $flag = 1)
    {
        if ($flag == 0) {
            $sql = "SELECT * FROM course_question WHERE course_code_id = {$course_code_id} AND prof_id = {$prof_id} AND solution_id = 0 ORDER BY time_posted DESC";
            $countSql = "SELECT COUNT(*) FROM course_question WHERE course_code_id = {$course_code_id} AND prof_id = {$prof_id} AND solution_id = 0 ORDER BY time_posted DESC";
        } else {
            $sql = "SELECT * FROM course_question WHERE course_code_id = {$course_code_id} AND prof_id = {$prof_id} AND solution_id != 0 ORDER BY time_posted DESC";
            $countSql = "SELECT COUNT(*) FROM course_question WHERE course_code_id = {$course_code_id} AND prof_id = {$prof_id} AND solution_id != 0 ORDER BY time_posted DESC";
        }

        return $this->getListWithPage("course_question", $sql, $countSql, 20);
    }

    /*
     * $flag = 1 查询已解决的提问
     * $flag = 0 查询未解决的提问
     */
    function getQuestionsByCourseCodeId($course_code_id,$flag=1){
        if ($flag == 0) {
            $sql = "SELECT * FROM course_question WHERE course_code_id = {$course_code_id} AND solution_id = 0 ORDER BY time_posted DESC";
            $countSql = "SELECT COUNT(*) FROM course_question WHERE course_code_id = {$course_code_id} AND solution_id = 0 ORDER BY time_posted DESC";
        } else {
            $sql = "SELECT * FROM course_question WHERE course_code_id = {$course_code_id} AND solution_id != 0 ORDER BY time_posted DESC";
            $countSql = "SELECT COUNT(*) FROM course_question WHERE course_code_id = {$course_code_id} AND solution_id != 0 ORDER BY time_posted DESC";
        }

        return $this->getListWithPage("course_question", $sql, $countSql, 20);
    }

    /*
     * controller执行积分的兑换
     */
    function approveSolution($id, $solution_id)
    {
        $time = time();
        $answerer_user_id = $this->getRowById("course_solution",$solution_id)["answerer_user_id"];
        $sql = "UPDATE course_question SET answerer_user_id = {$answerer_user_id},time_solved={$time},solution_id = {$solution_id} WHERE id = {$id};
                UPDATE course_solution SET time_approved = {$time} WHERE id = {$solution_id}";
        $result = $this->sqltool->mysqli->multi_query($sql);
        if($result)
            return true;
        else
            return false;
    }
    function getInsertId(){
        return $this->sqltool->getInsertId();
    }
}
?>