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
        //权限验证
        ($currentUser->isUserHasAuthority("ADMIN") || $currentUser->isUserHasAuthority("GOD")) || $currentUser->isUserHasAuthority("COURSE_QUESTION") or BasicTool::throwException("权限不足");
        //验证fields
        $course_code_id = BasicTool::post("course_code_id","Missing course_code_id");
        $prof_id = BasicTool::post("prof_id","Missing prof_id");
        $questioner_user_id = $currentUser->userId;
        $description = BasicTool::post("description","Missing Description");
        $reward_amount=BasicTool::post("reward_amount","Missing reward_amount");
        //验证积分
        $transactionModel->isCreditDeductible($currentUser->userId,$reward_amount) or BasicTool::throwException("积分不足");
        //图片上传
        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        $currImgArr = false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"course_question");
        //扣除积分
        $transactionModel->deductCredit($currentUser->userId,$reward_amount,"发布提问");

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
                BasicTool::echoJson(1,"添加成功", $result);
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
 * Controller核对管理员权限,确保提问者没还有采纳答案,退还原来的积分,扣除新积分. 确认用户在退还了原积分后有足够的积分进行扣除
 */
function updateQuestion($echoType = "normal"){
    global $questionModel,$imageModel,$currentUser;
    try{
        //判断管理员权限
        ($currentUser->isUserHasAuthority("ADMIN") || $currentUser->isUserHasAuthority("GOD")) or BasicTool::throwException("权限不足");
        //field验证
        $id = BasicTool::post("id");
        $description = BasicTool::post("description");
        $reward_amount = BasicTool::post("reward_amount");
        //确认问题还没有采纳答案
        $question = $questionModel->getQuestionById($id);
        ($question["solution_id"] == 0) or BasicTool::throwException("已解决的问题无法被更改");
        //图片上传
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
                BasicTool::echoJson(1, "更改成功",$result);
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
/*权限验证，必须是管理员或提问者
 * 删除之后退还积分,controller验证删除的问题是否被采纳
  */
function deleteQuestion($echoType="normal"){
    global $questionModel,$imageModel,$transactionModel,$currentUser;
    try{
        $id = BasicTool::post("id");
        $is_questions_solved = false;
        $questioner_user_ids=array();
        $reward_amounts=array();
        $img_ids = array();

        //删除多个提问.串联所有id,并用串联后的字符串执行查询
        if (is_array($id)) {

            //判断管理员权限
            ($currentUser->isUserHasAuthority("ADMIN") || $currentUser->isUserHasAuthority("GOD")) or BasicTool::throwException("权限不足,删除失败");

            $concat = null;
            foreach ($id as $i) {
                $i = $i + 0;
                $i = $i . ",";
                $concat = $concat . $i;
            }
            $concat = substr($concat, 0, -1);
            $sql = "SELECT * FROM course_question WHERE id in ({$concat})";
            $questions = SqlTool::getSqlTool()->getListBySql($sql);

            foreach ($questions as $question) {
                //把所有要删除的图片id添加到img_ids
                if ($question["img_id_1"]) {
                    array_push($img_ids, $question["img_id_1"]);
                }
                if ($question["img_id_2"]) {
                    array_push($img_ids, $question["img_id_2"]);
                }
                if ($question["img_id_3"]) {
                    array_push($img_ids, $question["img_id_3"]);
                }
                //判断若干个提问里是否有已被解决的提问
                $is_questions_solved = $is_questions_solved || ($question["solution_id"] != 0);
                //收集每个提问者的user_id和积分奖励
                array_push($questioner_user_ids, $question["questioner_user_id"]);
                array_push($reward_amounts, $question["reward_amount"]);
            }
        }
        //删除单个提问
        else {
            $question = $questionModel->getQuestionById($id);
            //普通用户权限判断
            ($currentUser->isUserHasAuthority("COURSE_QUESTION") && $currentUser->userId == $question["questioner_user_id"]) or BasicTool::throwException("权限不足,删除失败");
            //把要删除的图片id添加到img_ids
            if ($question["img_id_1"]) {
                array_push($img_ids, $question["img_id_1"]);
            }
            if ($question["img_id_2"]) {
                array_push($img_ids, $question["img_id_2"]);
            }
            if ($question["img_id_3"]) {
                array_push($img_ids, $question["img_id_3"]);
            }
            //判断提问是否已被解决,存取
            $is_questions_solved = $question["solution_id"] != 0;
            //储存提问者id和积分奖励
            $questioner_user_id = $question["questioner_user_id"];
            $reward_amount = $question["reward_amount"];
        }

        !$is_questions_solved or BasicTool::throwException("删除失败,禁止删除已被解决的问题");
        //退还积分
        if(is_array($id))
            $bool = $transactionModel->addCreditWithMultipleTransactions($questioner_user_ids,$reward_amounts,"删除提问");
        else{
            $bool = $transactionModel->addCredit($questioner_user_id,$reward_amount,"删除提问");
        }
        //退还积分成功,删除图片
        if($bool)
            $bool = $imageModel->deleteImageById($img_ids);
        //图片删除成功,删除提问
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
            BasicTool::echoJson(1,"查询成功",$result);
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
            BasicTool::echoJson(1,"查询成功",$result);

        else
            BasicTool::echoJson(0,"空");
    }
    catch (Exception $e){
        BasicTool::echoJson(0,$e->getMessage());
    }
}
/*权限验证，必须是管理员或提问者
 * 在问题未被解决的前提下,退还原来的积分,扣除新积分. 确认用户在退还了原积分后有足够的积分进行扣除
 */
function updateRewardAmount($echoType="normal"){
    global $questionModel,$transactionModel,$currentUser;
    try{

        $id = BasicTool::post("id","Missing Id");
        $reward_amount = BasicTool::post("reward_amount","missing reward_amount");
        $question = $questionModel->getQuestionById($id);
        $questioner_user_id = $question["questioner_user_id"];

        //判断权限
        if(!$currentUser->isUserHasAuthority("GOD") && !$currentUser->isUserHasAuthority("ADMIN") && $currentUser->isUserHasAuthority("COURSE_QUESTION")){
            $currentUser->userId == $questioner_user_id or BasicTool::throwException("权限不足,采纳失败");
        }
        //验证问题是否已被解决
        ($question["solution_id"] == 0) or BasicTool::throwException("更改积分失败,提问已经被解决");
        //积分验证
        $balance = $transactionModel->getCredit($question["questioner_user_id"]);
        (($balance + $question["reward_amount"] - $reward_amount) >= 0) or BasicTool::throwException("更改积分失败,积分不足");
        //添加积分
        $bool = $transactionModel->addCredit($question["questioner_user_id"],$question["reward_amount"],"更改提问积分奖励");
        //消耗积分
        if ($bool)
            $bool = $transactionModel->deductCredit($question["questioner_user_id"],$reward_amount,"更改提问积分奖励");
        //更改
        if ($bool)
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

/*权限验证，必须是管理员或提问者
 * Ensure question is not solved
 */

function approveSolution($echoType="normal"){
    global $solutionModel,$currentUser,$questionModel,$transactionModel;
    try{
        $question_id=BasicTool::post("question_id","Missing question_id");
        $question = $questionModel->getQuestionById($question_id);
        $reward_amount = $question["reward_amount"];
        $questioner_user_id = $question["questioner_user_id"];
        $solution_id=BasicTool::post("solution_id","Missing solution_id");
        $answerer_user_id = $solutionModel->getSolutionById($solution_id)["answerer_user_id"];
        //判断权限
        if(!$currentUser->isUserHasAuthority("GOD") && !$currentUser->isUserHasAuthority("ADMIN") && $currentUser->isUserHasAuthority("COURSE_QUESTION")){
            $currentUser->userId == $questioner_user_id or BasicTool::throwException("权限不足,采纳失败");
        }
        $bool = $transactionModel->addCredit($answerer_user_id,$reward_amount,"答案被采纳");
        if ($bool)
            $bool = $questionModel->approveSolution($question_id,$solution_id);
        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("采纳成功");
            else
                BasicTool::echoMessage("采纳失败");
        } else {
            if ($bool)
                BasicTool::echoJson(1, "采纳成功");
            else
                BasicTool::echoJson(0, "采纳失败");
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
function approveSolutionWithJson(){
    approveSolution("json");
}