<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$questionModel = new \admin\courseQuestion\CourseQuestionModel();
$solutionModel = new \admin\courseSolution\CourseSolutionModel();
$imageModel = new \admin\image\ImageModel();
$currentUser = new \admin\user\UserModel();
$transactionModel = new \admin\transaction\TransactionModel();
call_user_func(BasicTool::get('action'));

/*
 * 验证用户是否有足够的积分并进行扣除
 */
function addQuestion($echoType = "normal"){
    global $questionModel,$imageModel,$currentUser,$transactionModel;
    try{

        $course_code_id = BasicTool::post("course_code_id","Missing course_code_id");
        $prof_id = BasicTool::post("prof_id","Missing prof_id");
        $questioner_user_id = $currentUser->userId;
        $description = BasicTool::post("description","Missing Description");
        $reward_amount=BasicTool::post("reward_amount","Missing reward_amount");

        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        $currImgArr = false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"course_question");
        $bool = $questionModel->addQuestion($course_code_id,$prof_id, $questioner_user_id, $description, $imgArr[0], $imgArr[1], $imgArr[2], $reward_amount);

        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("添加成功");
            else
                BasicTool::echoMessage("添加失败");
        } else {
            if ($bool){
                $result["id"] = $questionModel->getInsertId();
                $result["course_code_id"] = $course_code_id;
                $result["prof_id"] = $prof_id;
                $result["description"] = $description;
                $result["img_id_1"] = $imgArr[0];
                $result["img_id_2"] = $imgArr[1];
                $result["img_id_3"] = $imgArr[2];
                $result["questioner_user_id"] = $currentUser->userId;
                $result["answerer_user_id"] = 0;
                $result["time_posted"] = time();
                $result["time_solved"] = 0;
                $result["solution_id"] = 0;
                $result["reward_amount"] = $reward_amount;
                $result["count_solutions"] = 0;
                $result["count_views"] = 0;
                BasicTool::echoJson(1, $result);
            }
            else
                BasicTool::echoJson(0, "添加失败");
        }
    }
    catch (Exception $e){
        if ($echoType == "normal"){
            BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
        }
        else{
            BasicTool::echoJson(0,$e->getMessage());
        }
    }
}

function addQuestionWithJson(){
    addQuestion("normal");
}

/*
 * Controller核对管理员权限,确保提问者没还有采纳答案.
 */
function updateQuestion($echoType = "normal"){
    global $questionModel,$imageModel,$currentUser;
    try{
        $id = BasicTool::post("id");
        $description = BasicTool::post("description");
        $reward_amount = BasicTool::post("reward_amount");

        $question = $questionModel->getQuestionById($id);
        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        $currImgArr = array($question["img_id_1"],$question["img_id_2"],$question["img_id_3"]);
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"course_question");
        $bool=$questionModel->updateQuestion($id,$description, $imgArr[0], $imgArr[1], $imgArr[2], $reward_amount);

        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("更改成功");
            else
                BasicTool::echoMessage("更改失败");
        }
        else {
            if ($bool){
                $result=$questionModel->getQuestionById($id);
                BasicTool::echoJson(1, $result);
            }
            else
                BasicTool::echoJson(0, "更改失败");
        }
    }
    catch (Exception $e){
        if ($echoType == "normal"){
            BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
        }
        else{
            BasicTool::echoJson(0,$e->getMessage());
        }
    }
}
function updateQuestionWithJson(){
    updateQuestion("json");
}
/*
 * 删除之后退还积分,controller验证删除的问题没有被采纳
  */
function deleteQuestion($echoType="normal"){
    global $questionModel,$imageModel;
    try{
        $id = BasicTool::post("id");
        if (is_array($id)) {
            $concat = null;
            foreach ($id as $i) {
                $i = $i + 0;
                $i = $i . ",";
                $concat = $concat . $i;
            }
            $concat = substr($concat, 0, -1);
            $sql = "SELECT * FROM course_question WHERE id in ({$concat})";
            $questions = SqlTool::getSqlTool()->getListBySql($sql);
            $img_ids = array();
            foreach ($questions as $question) {
                if ($question["img_id_1"]) {
                    array_push($img_ids, $question["img_id_1"]);
                }
                if ($question["img_id_2"]) {
                    array_push($img_ids, $question["img_id_2"]);
                }
                if ($question["img_id_3"]) {
                    array_push($img_ids, $question["img_id_3"]);
                }
            }

        } else {
            $question = $questionModel->getQuestionById($id);
            $img_ids = array();
            if ($question["img_id_1"]) {
                array_push($img_ids, $question["img_id_1"]);
            }
            if ($question["img_id_2"]) {
                array_push($img_ids, $question["img_id_2"]);
            }
            if ($question["img_id_3"]) {
                array_push($img_ids, $question["img_id_3"]);
            }
        }
        $bool = $imageModel->deleteImageById($img_ids);

        if ($bool){
            $bool = $questionModel->deleteQuestion($id);
        }

        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("删除成功");
            else
                BasicTool::echoMessage("删除失败");
        } else {
            if ($bool)
                BasicTool::echoJson(1, "删除成功");
            else
                BasicTool::echoJson(0, "删除失败");
        }
    }
    catch (Exception $e){
        if ($echoType == "normal"){
            BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
        }
        else{
            BasicTool::echoJson(0,$e->getMessage());
        }
    }
}
function deleteQuestionWithJson(){
    deleteQuestion("json");
}

/*
 * $flag = 1 查询已解决的提问
 * $flag = 0 查询未解决的提问
 */
function getQuestionsByCourseCodeIdWithJson(){
    global $questionModel;
    try{
        $flag = BasicTool::get("flag");
        $course_code_id = BasicTool::get("course_code_id","Missing Course Code Id");
        $result = $questionModel->getQuestionsByCourseCodeId($course_code_id,$flag);
        if ($result)
            BasicTool::echoJson(1,$result);

        else
            BasicTool::echoJson(0,"空");
    }
    catch (Exception $e){
            BasicTool::echoJson(0,$e->getMessage());
        }
}
function getQuestionsByCourseCodeIdProfIdWithJson(){
    global $questionModel;
    try{
        $flag = BasicTool::get("flag");
        $course_code_id = BasicTool::get("course_code_id","Missing Course Code Id");
        $prof_id = BasicTool::get("prof_id","missing prof id");
        $result = $questionModel->getQuestionsByCourseCodeIdProfId($course_code_id,$prof_id,$flag);
        if ($result)
            BasicTool::echoJson(1,$result);

        else
            BasicTool::echoJson(0,"空");
    }
    catch (Exception $e){
        BasicTool::echoJson(0,$e->getMessage());
    }
}
/*
 * 在问题未被解决的前提下,退还原来的积分,扣除新积分
 */
function updateRewardAmount($echoType="normal"){
    global $questionModel;
    try{
        $id = BasicTool::post("id","Missing Id");
        $reward_amount = BasicTool::post("reward_amount","missing reward_amount");
        $bool = $questionModel->updateRewardAmount($id,$reward_amount);
        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("更改成功");
            else
                BasicTool::echoMessage("更改失败");
        } else {
            if ($bool){
                BasicTool::echoJson(1,"更改成功");
            }
            else
                BasicTool::echoJson(0, "更改失败");
        }
    }
    catch (Exception $e){
        if ($echoType == "normal"){
            BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
        }
        else{
            BasicTool::echoJson(0,$e->getMessage());
        }
    }
}
function updateRewardAmountWithJson(){
    updateRewardAmount("json");
}

/*
* controller执行积分的兑换
 */
function approveSolution(){

}