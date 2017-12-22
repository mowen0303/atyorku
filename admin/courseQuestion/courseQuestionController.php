<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$questionModel = new \admin\courseQuestion\CourseQuestionModel();
$solutionModel = new \admin\courseSolution\CourseSolutionModel();
$imageModel = new \admin\image\ImageModel();
$currentUser = new \admin\user\UserModel();
$transactionModel = new \admin\transaction\TransactionModel();
call_user_func(BasicTool::get('action'));

/**添加一个提问
 * POST
 * @param course_code_id
 * @param prof_id
 * @param description 问题描述
 * @param reward_amount 积分奖励
 * @param img_id_1
 * @param img_id_2
 * @param img_id_3
 * localhost/admin/courseQuestion/courseQuestionController.php?action=addQuestion
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
        $reward_amount>0 or BasicTool::throwException("请输入有效的积分数");
        //验证积分
        $transactionModel->isCreditDeductible($currentUser->userId,$reward_amount) or BasicTool::throwException("积分不足");
        //图片上传
        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        $currImgArr = false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"course_question");
        //扣除积分
        $transactionModel->deductCredit($currentUser->userId,$reward_amount,"发布提问");

        $questionModel->addQuestion($course_code_id,$prof_id, $questioner_user_id, $description, $imgArr[0], $imgArr[1], $imgArr[2], $reward_amount) or BasicTool::throwException("添加失败");
        if ($echoType == "normal") {
            BasicTool::echoMessage("添加成功","/admin/courseQuestion/index.php?s=getQuestions&course_code_id={$course_code_id}&prof_id={$prof_id}");
        }
        else {
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
/**添加一个提问
 * POST,JSON接口
 * @param course_code_id
 * @param prof_id
 * @param description 问题描述
 * @param reward_amount 积分奖励
 * @param img_id_1
 * @param img_id_2
 * @param img_id_3
 * localhost/admin/courseQuestion/courseQuestionController.php?action=addQuestionWithJson
 */
function addQuestionWithJson(){
    addQuestion("normal");
}

/**更改一个提问
 * POST
 * @param id 提问id
 * @param description 问题描述
 * @param reward_amount 积分奖励
 * @param img_id_1
 * @param img_id_2
 * @param img_id_3
 * localhost/admin/courseQuestion/courseQuestionController.php?action=updateQuestion
 */
function updateQuestion($echoType = "normal"){
    global $questionModel,$imageModel,$currentUser,$transactionModel;
    try {
        //判断管理员权限
        ($currentUser->isUserHasAuthority("ADMIN") || $currentUser->isUserHasAuthority("GOD")) or BasicTool::throwException("权限不足");
        //field验证
        $id = BasicTool::post("id", "Missing id");
        $description = BasicTool::post("description");
        $reward_amount = BasicTool::post("reward_amount");
        $reward_amount > 0 or BasicTool::throwException("请输入有效的积分");
        $question = $questionModel->getQuestionById($id);
        //确认问题是否已被解决
        ($question["solution_id"] == 0) or BasicTool::throwException("更改失败,提问已经被解决");
        //积分验证
        $balance = $transactionModel->getCredit($question["questioner_user_id"]);
        (($balance + $question["reward_amount"] - $reward_amount) >= 0) or BasicTool::throwException("更改积分失败,积分不足");
        //图片上传
        $imgArr = array(BasicTool::post("img_id_1"), BasicTool::post("img_id_2"), BasicTool::post("img_id_3"));
        $currImgArr = array($question["img_id_1"], $question["img_id_2"], $question["img_id_3"]);
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr, $currImgArr, 3, "imgFile", $currentUser->userId, "course_question");
        //如果传回来的reward_amount跟数据库里的值是一致的.则不做任何积分操作
        if (!($reward_amount == $question["reward_amount"])) {
            //添加积分
            $transactionModel->addCredit($question["questioner_user_id"], $question["reward_amount"], "更改提问积分奖励") or BasicTool::throwException("更改积分失败");
            //消耗积分
            $transactionModel->deductCredit($question["questioner_user_id"], $reward_amount, "更改提问积分奖励") or BasicTool::throwException("更改积分失败");
        }
        $questionModel->updateQuestion($id, $description, $imgArr[0], $imgArr[1], $imgArr[2], $reward_amount) or BasicTool::throwException("更改失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("添加成功", "/admin/courseQuestion/index.php?s=getQuestions&course_code_id={$question['course_code_id']}&prof_id={$question['prof_id']}");
        }
        else {
            $result = $questionModel->getQuestionById($id);
            BasicTool::echoJson(1, "更改成功", $result);
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
/**更改一个提问
 * POST,JSON接口
 * @param id 提问id
 * @param description 问题描述
 * @param reward_amount 积分奖励
 * @param img_id_1
 * @param img_id_2
 * @param img_id_3
 * localhost/admin/courseQuestion/courseQuestionController.php?action=updateQuestionWithJson
 */
function updateQuestionWithJson(){
    updateQuestion("json");
}
/**删除提问
 * POST
 * @param id 提问id,integer或者一维数组
 * localhost/admin/courseQuestion/courseQuestionController.php?action=deleteQuestion
 */
function deleteQuestion($echoType="normal"){
    global $questionModel,$imageModel,$transactionModel,$currentUser,$solutionModel;
    $sqlTool = SqlTool::getSqlTool();
    try{
        $id = BasicTool::post("id");
        $is_questions_solved = false;
        $questioner_user_ids=array();
        $reward_amounts=array();
        $img_ids = array();

        //删除多个提问.串联所有id,并用串联后的字符串执行查询
        if (is_array($id)) {

            //判断管理员权限
            $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足,删除失败");

            $concat = null;
            foreach ($id as $i) {
                $i = $i + 0;
                $i = $i . ",";
                $concat = $concat . $i;
            }
            $concat = substr($concat, 0, -1);
            $sql = "SELECT * FROM course_question WHERE id in ({$concat})";
            $questions = $sqlTool->getListBySql($sql);
            $sql = "SELECT * FROM course_solution WHERE question_id in ({$concat})";
            $solutions = $sqlTool->getListBySql($sql);

            foreach ($questions as $question) {
                //把所有要删除questions的图片id添加到img_ids
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
            foreach ($solutions as $solution){
                //把所有要删除的solutions的图片id添加到img_ids
                if ($solution["img_id_1"]) {
                    array_push($img_ids, $solution["img_id_1"]);
                }
                if ($solution["img_id_2"]) {
                    array_push($img_ids, $solution["img_id_2"]);
                }
                if ($solution["img_id_3"]) {
                    array_push($img_ids, $solution["img_id_3"]);
                }
            }
        }
        //删除单个提问
        else {
            $question = $questionModel->getQuestionById($id);
            $solutions = $solutionModel->getSolutionsByQuestionId($id);
            //权限判断
            (($currentUser->isUserHasAuthority("COURSE_QUESTION") && $currentUser->userId == $question["questioner_user_id"]) || $currentUser->isUserHasAuthority("ADMIN")) or BasicTool::throwException("权限不足,删除失败");
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
            //判断提问是否已被解决
            $is_questions_solved = $question["solution_id"] != 0;
            //储存提问者id和积分奖励
            $questioner_user_id = $question["questioner_user_id"];
            $reward_amount = $question["reward_amount"];

            foreach($solutions as $solution){
                //把所有即要删除的solution的图片id添加到img_ids
                if ($solution["img_id_1"]) {
                    array_push($img_ids, $solution["img_id_1"]);
                }
                if ($solution["img_id_2"]) {
                    array_push($img_ids, $solution["img_id_2"]);
                }
                if ($solution["img_id_3"]) {
                    array_push($img_ids, $solution["img_id_3"]);
                }
            }
        }

        !$is_questions_solved or BasicTool::throwException("删除失败,禁止删除已被解决的问题");
        //退还积分
        if(is_array($id))
            $transactionModel->addCreditWithMultipleTransactions($questioner_user_ids,$reward_amounts,"删除提问") or BasicTool::throwException("删除失败，退还积分失败");
        else{
            $transactionModel->addCredit($questioner_user_id,$reward_amount,"删除提问") or BasicTool::throwException("删除失败，退还积分失败");
        }
        //退还积分成功,删除图片
        $imageModel->deleteImageById($img_ids) or BasicTool::throwException("删除图片失败");
        //图片删除成功,删除提问
        $questionModel->deleteQuestion($id) or BasicTool::throwException("删除失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("删除成功");
        }
        else {
            BasicTool::echoJson(1, "删除成功");
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
/**删除提问
 * POST,JSON接口
 * @param id 提问id
 * localhost/admin/courseQuestion/courseQuestionController.php?action=deleteQuestionWithJson
 */
function deleteQuestionWithJson(){
    deleteQuestion("json");
}

/**根据course_code_id查询一页提问
 * GET,JSON接口
 * @param course_code_id
 * @param flag 0=为解决的提问，1=已解决的提问
 * @param page 页数
 * localhost/admin/courseQuestion/courseQuestionController.php?action=getQuestionsByCourseCodeIdWithJson&page=1&flag=1&course_code_id=1
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
/**根据course_code_id和prof_id查询一页提问
 * GET,JSON接口
 * @param course_code_id
 * @param prof_id
 * @param flag 0=为解决的提问，1=已解决的提问
 * @param page 页数
 * localhost/admin/courseQuestion/courseQuestionController.php?action=getQuestionsByCourseCodeIdWithJson&page=1&flag=1&course_code_id=1&prof_id=1
 */
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
/**更改积分奖励
 * POST
 * @param id 提问id
 * @param reward_amount 积分奖励
 * localhost/admin/courseQuestion/courseQuestionController.php?action=updateRewardAmount
 */
function updateRewardAmount($echoType="normal"){
    global $questionModel,$transactionModel,$currentUser;
    try{

        $id = BasicTool::post("id","Missing Id");
        $reward_amount = BasicTool::post("reward_amount","missing reward_amount");
        $reward_amount>0 or BasicTool::throwException("请输入有效的积分数");
        $question = $questionModel->getQuestionById($id);
        $question or BasicTool::throwException("question_id不存在");
        $questioner_user_id = $question["questioner_user_id"];

        //判断权限
        if(!$currentUser->isUserHasAuthority("GOD") && !$currentUser->isUserHasAuthority("ADMIN") && $currentUser->isUserHasAuthority("COURSE_QUESTION")){
            $currentUser->userId == $questioner_user_id or BasicTool::throwException("权限不足,采纳失败");
        }
        //验证问题是否已被解决
        ($question["solution_id"] == 0) or BasicTool::throwException("更改积分失败,提问已经被解决");
        //如果传回来的reward_amount跟数据库里的值是一致的.则不做任何积分操作
        if ($reward_amount != $question["reward_amount"])
        {
            //积分验证
            $balance = $transactionModel->getCredit($question["questioner_user_id"]);
            (($balance + $question["reward_amount"] - $reward_amount) >= 0) or BasicTool::throwException("更改积分失败,积分不足");
            //添加积分
            $transactionModel->addCredit($question["questioner_user_id"], $question["reward_amount"], "更改提问积分奖励") or BasicTool::throwException("更改积分失败");
            //消耗积分
            $transactionModel->deductCredit($question["questioner_user_id"], $reward_amount, "更改提问积分奖励") or BasicTool::throwException("更改积分失败");
        }

        //更改
        $questionModel->updateRewardAmount($id,$reward_amount) or BasicTool::throwException("更改失败");
        if ($echoType == "normal") {
            BasicTool::echoMessage("更改成功");
        }
        else {
            BasicTool::echoJson(1,"更改成功");
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
/**更改积分奖励
 * POST,JSON接口
 * @param id 提问id
 * @param reward_amount 积分奖励
 * localhost/admin/courseQuestion/courseQuestionController.php?action=updateRewardAmountWithJson
 */
function updateRewardAmountWithJson(){
    updateRewardAmount("json");
}

/**采纳答案
 * GET
 * @param question_id 提问id
 * @param solution_id 答案id
 * localhost/admin/courseQuestion/courseQuestionController.php?action=approveSolution
 */

function approveSolution($echoType="normal"){
    global $solutionModel,$currentUser,$questionModel,$transactionModel;
    try{
        $question_id=BasicTool::get("question_id","Missing question_id");
        $solution_id=BasicTool::get("solution_id","Missing solution_id");
        $question = $questionModel->getQuestionById($question_id);
        $reward_amount = $question["reward_amount"];
        $questioner_user_id = $question["questioner_user_id"];
        $answerer_user_id = $solutionModel->getSolutionById($solution_id)["answerer_user_id"];
        //判断权限
        if(!$currentUser->isUserHasAuthority("ADMIN") && $currentUser->isUserHasAuthority("COURSE_QUESTION")){
            $currentUser->userId == $questioner_user_id or BasicTool::throwException("权限不足,采纳失败");
        }
        $answerer_user_id != $questioner_user_id or BasicTool::throwException("questioner_user_id = answerer_user_id");
        //验证问题是否已被解决
        ($question["solution_id"] == 0) or BasicTool::throwException("采纳失败,提问已经被解决");
        $transactionModel->addCredit($answerer_user_id,$reward_amount,"答案采纳") or BasicTool::throwException("采纳失败");
        $questionModel->approveSolution($question_id,$solution_id) or BasicTool::throwException("采纳失败");
        if ($echoType == "normal") {
            BasicTool::echoMessage("采纳成功");
        }
        else {
            BasicTool::echoJson(1, "采纳成功");
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
/**采纳答案
 * GET,JSON接口
 * @param question_id 提问id
 * @param solution_id 答案id
 * localhost/admin/courseQuestion/courseQuestionController.php?action=approveSolutionWithJson
 */
function approveSolutionWithJson(){
    approveSolution("json");
}