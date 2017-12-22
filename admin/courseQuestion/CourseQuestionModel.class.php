<?php
namespace admin\courseQuestion;
use \Model as Model;

class CourseQuestionModel extends Model
{
    /**添加一个提问
     * @param int $course_code_id
     * @param int $prof_id 教授id
     * @param int $questioner_user_id 提问者id
     * @param String $description 问题描述
     * @param int $img_id_1
     * @param int $img_id_2
     * @param int $img_id_3
     * @param int $reward_amount 积分奖励
     * @return bool
     */
    function addQuestion($course_code_id,$prof_id, $questioner_user_id, $description, $img_id_1, $img_id_2, $img_id_3, $reward_amount)
    {

        $arr["course_code_id"] = $course_code_id;
        $arr["prof_id"] = $prof_id;

        $arr["description"] = $description ? $description : "";
        $arr["img_id_1"] = $img_id_1 ? $img_id_1 : 0;
        $arr["img_id_2"] = $img_id_2 ? $img_id_2 : 0;
        $arr["img_id_3"] = $img_id_3 ? $img_id_3 : 0;

        $arr["time_posted"] = time();
        $arr["time_solved"] = 0;

        $arr["reward_amount"] = $reward_amount ? $reward_amount : 0;
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

    /**更改一个提问
     * @param int $id 提问id
     * @param String $description 问题描述
     * @param int $img_id_1
     * @param int $img_id_2
     * @param int $img_id_3
     * @param int $reward_amount 积分奖励
     * @return bool
     */
    function updateQuestion($id,$description, $img_id_1, $img_id_2, $img_id_3, $reward_amount)
    {
        $arr["description"] = $description ? $description : "";
        $arr["img_id_1"] = $img_id_1 ? $img_id_1 : 0;
        $arr["img_id_2"] = $img_id_2 ? $img_id_2 : 0;
        $arr["img_id_3"] = $img_id_3 ? $img_id_3 : 0;
        $arr["reward_amount"] = $reward_amount ? $reward_amount : 0;
        return $this->updateRowById("course_question", $id,$arr);
    }

    /**更改积分奖励
     * @param int $id 提问id
     * @param int $reward_amount 积分奖励
     * @return bool
     */
    function updateRewardAmount($id, $reward_amount)
    {
        $arr["reward_amount"] = $reward_amount ? $reward_amount : 0;
        return $this->updateRowById("course_question", $id,$arr);
    }

    /**删除提问
     * @param int|array $id 提问id
     * @return bool|\mysqli_result
     */
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

    /**根据course_code_id和教授id查询提问
     * flag=1查询已解决的提问
     * flag=0查询未解决的提问
     * @param int $course_code_id
     * @param int $prof_id 教授id
     * @param int $flag
     * @return array
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

    /**根据course_code_id查询提问
     * flag=1查询已解决的提问
     * flag=0查询未解决的提问
     * @param int $course_code_id
     * @param int $flag
     * @return array
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

    /**采纳答案
     * @param int $id 提问id
     * @param int $solution_id 答案id
     * @return bool|\mysqli_result
     */
    function approveSolution($id, $solution_id)
    {
        $time = time();
        $question = $this->getQuestionById($id);
        $course_code_id = $question["course_code_id"];
        $prof_id = $question["prof_id"];
        $answerer_user_id = $this->getRowById("course_solution",$solution_id)["answerer_user_id"];
        $sql = "UPDATE course_question SET answerer_user_id = {$answerer_user_id},time_solved={$time},solution_id = {$solution_id} WHERE id = {$id}";
        $this->sqltool->query($sql);
        $sql = "UPDATE course_solution SET time_approved = {$time} WHERE id = {$solution_id}";
        $result = $this->sqltool->query($sql);
        if($result) {
            $sql = "UPDATE course_report SET count_questions = (SELECT COUNT(*) FROM course_question WHERE course_code_id = {$course_code_id} AND solution_id =0), count_solved_questions=(SELECT COUNT(*) FROM course_question WHERE course_code_id={$course_code_id} AND solution_id !=0) WHERE course_code_id = {$course_code_id}";
            $this->sqltool->query($sql);
            $sql = "UPDATE course_prof_report SET count_questions = (SELECT COUNT(*) FROM course_question WHERE course_code_id = {$course_code_id} AND prof_id={$prof_id} AND solution_id=0), count_solved_questions=(SELECT COUNT(*) FROM course_question WHERE course_code_id={$course_code_id} AND prof_id={$prof_id} AND solution_id !=0) WHERE course_code_id = {$course_code_id} AND prof_id={$prof_id}";
            $result = $this->sqltool->query($sql);
        }
        return $result;
    }
    function getInsertId(){
        return $this->sqltool->getInsertId();
    }
}
?>