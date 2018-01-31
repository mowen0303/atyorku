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
     * @return false|insert_id
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
        $insertId = $this->getInsertId();
        if ($bool) {
            $sql = "UPDATE course_report SET count_questions = (SELECT COUNT(*) FROM course_question WHERE course_code_id = {$course_code_id} AND solution_id =0), count_solved_questions=(SELECT COUNT(*) FROM course_question WHERE course_code_id={$course_code_id} AND solution_id !=0) WHERE course_code_id = {$course_code_id}";
            $this->sqltool->query($sql);
            $sql = "UPDATE course_prof_report SET count_questions = (SELECT COUNT(*) FROM course_question WHERE course_code_id = {$course_code_id} AND prof_id={$prof_id} AND solution_id=0), count_solved_questions=(SELECT COUNT(*) FROM course_question WHERE course_code_id={$course_code_id} AND prof_id={$prof_id} AND solution_id !=0) WHERE course_code_id = {$course_code_id} AND prof_id={$prof_id}";
            $this->sqltool->query($sql);
        }
        return $bool?$insertId:false;
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
        $sql ="SELECT course_question.*, image.url as img_url_1 FROM (SELECT course_question.*,user.alias,user.gender,user.enroll_year,user.img as profile_img_url,user.major FROM (SELECT * FROM course_question WHERE id = {$id}) AS course_question INNER JOIN user on course_question.questioner_user_id = user.id) AS course_question LEFT JOIN image ON course_question.img_id_1 = image.id";
        return $this->sqltool->getRowBySql($sql);

    }

    /**根据course_code_id和教授id查询提问
     * flag=1查询已解决的提问
     * flag=0查询未解决的提问
     * 如果prof_id=0则只根据course_code_id查询
     * @param int $course_code_id
     * @param int $prof_id 教授id
     * @param int $flag
     * @return array
     */
    function getQuestionsByCourseCodeIdProfId($course_code_id,$prof_id, $flag = 1)
    {
        $condition1 = $course_code_id ? "course_code_id = {$course_code_id}" : "course_code_id > 0";
        $condition2 = $prof_id ? "AND prof_id = {$prof_id}" : "";

        if ($flag == 0) {
            $sql = "SELECT course_question.*, image.url as img_url_1 FROM (SELECT course_question.*,user.alias,user.gender,user.enroll_year,user.img as profile_img_url,user.major FROM (SELECT * FROM course_question WHERE {$condition1} {$condition2} AND solution_id = 0) AS course_question INNER JOIN user on course_question.questioner_user_id = user.id) AS course_question LEFT JOIN image ON course_question.img_id_1 = image.id ORDER BY course_question.time_posted DESC";
            $countSql = "SELECT COUNT(*) FROM (SELECT course_question.*,user.alias,user.gender,user.enroll_year,user.img as profile_img_url,user.major FROM (SELECT * FROM course_question WHERE {$condition1} {$condition2} AND solution_id = 0) AS course_question INNER JOIN user on course_question.questioner_user_id = user.id) AS course_question LEFT JOIN image ON course_question.img_id_1 = image.id ORDER BY course_question.time_posted DESC";
        } else {
            $sql = "SELECT course_question.*,image.url AS solution_img_url_1 FROM (SELECT course_question.*, course_solution.img_id_1 AS solution_img_id_1, course_solution.description AS solution_description FROM (SELECT course_question.*,user.alias,user.gender,user.enroll_year,user.img AS profile_img_url, user.major FROM (SELECT * FROM course_question WHERE {$condition1} {$condition2} AND solution_id != 0) AS course_question INNER JOIN user on user.id = course_question.answerer_user_id) AS course_question INNER JOIN course_solution ON course_solution.id = course_question.solution_id) AS course_question LEFT JOIN image ON image.id = course_question.solution_img_id_1 ORDER BY course_question.time_posted DESC";
            $countSql = "SELECT COUNT(*) FROM (SELECT course_question.*, course_solution.img_id_1 AS solution_img_id_1, course_solution.description AS solution_description FROM (SELECT course_question.*,user.alias,user.gender,user.enroll_year,user.img AS profile_img_url, user.major FROM (SELECT * FROM course_question WHERE {$condition1} {$condition2} AND solution_id != 0) AS course_question INNER JOIN user on user.id = course_question.answerer_user_id) AS course_question INNER JOIN course_solution ON course_solution.id = course_question.solution_id) AS course_question LEFT JOIN image ON image.id = course_question.solution_img_id_1 ORDER BY course_question.time_posted DESC";
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

    /**根据course_code_id和教授id提问数
     * flag=1查询已解决的提问数
     * flag=0查询未解决的提问数
     * 如果prof_id=0则只根据course_code_id查询
     * @param int $course_code_id
     * @param int $prof_id 教授id
     * @param int $flag
     * @return int
     */
    function getQuestionCount($course_code_id, $prof_id, $flag){
        $condition1 = $course_code_id ? "course_code_id = {$course_code_id}" : "course_code_id > 0";
        $condition2 = $prof_id ? "AND prof_id = {$prof_id}" : "";

        if ($flag){
            $sql = "SELECT COUNT(*) AS count FROM course_question WHERE {$condition1} {$condition2} AND solution_id != 0";
        }
        else{
            $sql = "SELECT COUNT(*) AS count FROM course_question WHERE {$condition1} {$condition2} AND solution_id = 0";
        }
        return $this->sqltool->getRowBySql($sql);
    }
    //添加浏览量
    function addViewById($id){
        $sql = "UPDATE course_question SET `count_views`=`count_views`+1 WHERE id IN ({$id})";
        $this->sqltool->query($sql);
    }


    function getCourseReportByCourseCodeId($course_code_id){
        $sql = "SELECT * FROM course_report WHERE course_code_id IN ({$course_code_id})";
        return $this->sqltool->getRowBySql($sql);
    }
    function getCourseProfReportByCourseCodeIdProfId($course_code_id,$prof_id){
        $sql = "SELECT * FROM course_prof_report WHERE course_code_id IN ({$course_code_id}) AND prof_id in ({$prof_id})";
        return $this->sqltool->getRowBySql($sql);
    }
    function addCourseReport($course_code_id){
        $arr = [];
        $arr["course_code_id"] = $course_code_id;
        $arr["homework_diff"] = 0;
        $arr["test_diff"] = 0;
        $arr["content_diff"] = 0;
        $arr["overall_diff"] = 0;
        $arr["rating_count"] = 0;
        $arr["count_questions"] = 0;
        $arr["count_solved_questions"] = 0;
        return $this->addRow("course_report", $arr);

    }
    function addCourseProfReport($course_code_id,$prof_id){
        $arr = [];
        $arr["course_code_id"] = $course_code_id;
        $arr["prof_id"] = $prof_id;
        $arr["homework_diff"] = 0;
        $arr["test_diff"] = 0;
        $arr["content_diff"] = 0;
        $arr["overall_diff"] = 0;
        $arr["recommendation_ratio"] = 0;
        $arr["rating_count"] = 0;
        $arr["count_questions"] = 0;
        $arr["count_solved_questions"] = 0;
        return $this->addRow("course_prof_report", $arr);

    }

}
?>