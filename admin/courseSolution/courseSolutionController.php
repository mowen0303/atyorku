<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$questionModel = new \admin\courseQuestion\CourseQuestionModel();
$solutionModel = new \admin\courseSolution\CourseSolutionModel();
$currentUser = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$transactionModel = new \admin\transaction\TransactionModel();
call_user_func(BasicTool::get('action'));

/**添加答案
 * POST
 * @param question_id 提问id
 * @param description
 * @param img_id_1
 * @param img_id_2
 * @param img_id_3
 * localhost/admin/courseSolution/courseSolutionnController.php?action=addSolution
 */
function addSolution($echoType = "normal"){
    global $imageModel,$currentUser,$solutionModel,$transactionModel;
    try{
        //权限验证
        $currentUser->isUserHasAuthority("COURSE_QUESTION") or BasicTool::throwException("权限不足");
        $answerer_user_id = $currentUser->userId;
        $questioner_user_id = BasicTool::post("questioner_user_id","缺少问题发布者UID");
        $question_id= BasicTool::post("question_id","missing q_id");
        $description = BasicTool::post("description","Missing Description");
        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        $currImgArr = false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"course_solution");
        $insertId = $solutionModel->addSolution($question_id,$answerer_user_id, $description, $imgArr[0], $imgArr[1], $imgArr[2]);
        $insertId or BasicTool::throwException("添加失败");
        //每日发布的前5个答案加积分
        !$solutionModel->shouldRewardAddSolution($questioner_user_id) or $transactionModel->systemAdjustCredit($answerer_user_id,Credit::$addCourseSolution,"course_solution",0);
        //推送
        $msgModel = new \admin\msg\MsgModel();
        $msgModel->pushMsgToUser($questioner_user_id,'course_question',$question_id,substr($description,0,80));

        if ($echoType == "normal") {
            BasicTool::echoMessage("添加成功","/admin/courseSolution/index.php?action=getSolutions&question_id={$question_id}");
        }
        else {
            $result=$solutionModel->getSolutionById($insertId);
            $result["time_posted"] = BasicTool::translateTime($result["time_posted"]);
            $result["enroll_year"] = BasicTool::translateTime($result["enroll_year"]);
            $result["img_urls"] = [];
            !$result["img_id_1"] or array_push($result["img_urls"],$imageModel->getImageById($result["img_id_1"])["url"]);
            !$result["img_id_2"] or array_push($result["img_urls"],$imageModel->getImageById($result["img_id_2"])["url"]);
            !$result["img_id_3"] or array_push($result["img_urls"],$imageModel->getImageById($result["img_id_3"])["url"]);
            BasicTool::echoJson(1, "添加成功",$result);
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

/**添加答案
 * POST,JSON接口
 * @param question_id 提问id
 * @param description
 * @param img_id_1
 * @param img_id_2
 * @param img_id_3
 * localhost/admin/courseSolution/courseSolutionnController.php?action=addSolutionWithJson
 */
function addSolutionWithJson(){
    addSolution("json");
}

/**更改答案
 * POST
 * @param id 答案id
 * @param description
 * @param img_id_1
 * @param img_id_2
 * @param img_id_3
 * localhost/admin/courseSolution/courseSolutionnController.php?action=updateSolution
 */
function updateSolution($echoType = "normal"){
    global $imageModel,$currentUser,$solutionModel;
    try{
        $id= BasicTool::post("id","missing id");
        $description = BasicTool::post("description","Missing Description");
        $solution = $solutionModel->getSolutionById($id);
        $solution["time_approved"] == 0 or BasicTool::throwException("更改失败,答案已被采纳");
        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        $currImgArr = array($solution["img_id_1"],$solution["img_id_2"],$solution["img_id_3"]);
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"course_solution");

        $solutionModel->updateSolution($id,$description, $imgArr[0], $imgArr[1], $imgArr[2]) or BasicTool::throwException("更改失败");

        if ($echoType == "normal") {
            BasicTool::echoMessage("更改成功","/admin/courseSolution/index.php?action=getSolutions&question_id={$solution['question_id']}");
        }
        else {
            $result=$solutionModel->getSolutionById($id);
            BasicTool::echoJson(1, $result);
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
/**更改答案
 * POST,JSON接口
 * @param id 答案id
 * @param description
 * @param img_id_1
 * @param img_id_2
 * @param img_id_3
 * localhost/admin/courseSolution/courseSolutionnController.php?action=updateSolutionWithJson
 */
function updateSolutionWithJson(){
    updateSolution("json");
}
/**查询某个提问下被采纳的答案
 * GET,JSON接口
 * @param question_id
 * localhost/admin/courseSolution/courseSolutionController.php?action=getApprovedSolutionByQuestionIdWithJson&question_id=1
 */
function getApprovedSolutionByQuestionIdWithJson(){
    global $solutionModel,$imageModel;
    $question_id = BasicTool::get("question_id","请指定question_id");
    $result = $solutionModel->getApprovedSolutionByQuestionId($question_id);
    if ($result){
        $result["time_posted"] = BasicTool::translateTime($result["time_posted"]);
        $result["enroll_year"] = BasicTool::translateEnrollYear($result["enroll_year"]);
        $result["img_urls"] = [];
        !$result["img_id_1"] or array_push($result["img_urls"],$result["img_url_1"]);
        !$result["img_id_2"] or array_push($result["img_urls"],$result["img_url_2"]);
        !$result["img_id_3"] or array_push($result["img_urls"],$result["img_url_3"]);
        BasicTool::echoJson(1,"查询成功",$result);
    }
    else
        BasicTool::echoJson(0,"空");
}
/**查询某个提问的一页答案
 * GET,JSON接口
 * @param question_id
 * @param page
 * localhost:8080/admin/courseSolution/courseSolutionController.php?action=getSolutionsByQuestionIdWithJson&question_id=1
 */
function getSolutionsByQuestionIdWithJson(){
    global $solutionModel;
    $question_id = BasicTool::get("question_id","请指定question_id");
    $result = $solutionModel->getSolutionsByQuestionId($question_id);
    if ($result){
        $results = [];
        foreach ($result as $solution){
            $solution["time_posted"] = BasicTool::translateTime($solution["time_posted"]);
            $solution["enroll_year"] = BasicTool::translateEnrollYear($solution["enroll_year"]);
            $solution["img_urls"] = [];
            !$solution["img_id_1"] or array_push($solution["img_urls"],$solution["img_url_1"]);
            !$solution["img_id_2"] or array_push($solution["img_urls"],$solution["img_url_2"]);
            !$solution["img_id_3"] or array_push($solution["img_urls"],$solution["img_url_3"]);
            array_push($results,$solution);
        }
        BasicTool::echoJson(1,"查询成功",$results);
    }
    else
        BasicTool::echoJson(0,"空");
}
/**删除答案
 * POST
 * @param id 答案id，integer或一维数组
 * localhost/admin/courseSolution/courseSolutionnController.php?action=deleteSolutionById
 */
function deleteSolutionById($echoType = "normal"){
    global $imageModel,$currentUser,$solutionModel;
    try{
        $id = BasicTool::post("id","Missing id");
        $is_solution_approved = false;
        $img_ids = array();

        //删除多个solution.串联所有id,并用串联后的字符串执行查询
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
            $sql = "SELECT * FROM course_solution WHERE id in ({$concat})";
            $solutions = SqlTool::getSqlTool()->getListBySql($sql);

            foreach ($solutions as $solution) {
                //把所有要删除的图片id添加到img_ids
                if ($solution["img_id_1"]) {
                    array_push($img_ids, $solution["img_id_1"]);
                }
                if ($solution["img_id_2"]) {
                    array_push($img_ids, $solution["img_id_2"]);
                }
                if ($solution["img_id_3"]) {
                    array_push($img_ids, $solution["img_id_3"]);
                }
                //判断若干个答案里是否有已被采纳的答案
                $is_solution_approved = $is_solution_approved || ($solution["time_approved"] != 0);
            }
        }
        //删除单个solution
        else {
            $solution = $solutionModel->getSolutionById($id);
            //普通用户权限判断
            (($currentUser->isUserHasAuthority("COURSE_QUESTION") && $currentUser->userId == $solution["answerer_user_id"]) || $currentUser->isUserHasAuthority("ADMIN")) or BasicTool::throwException("权限不足,删除失败");
            //把要删除的图片id添加到img_ids
            if ($solution["img_id_1"]) {
                array_push($img_ids, $solution["img_id_1"]);
            }
            if ($solution["img_id_2"]) {
                array_push($img_ids, $solution["img_id_2"]);
            }
            if ($solution["img_id_3"]) {
                array_push($img_ids, $solution["img_id_3"]);
            }
            //判断答案是否已被采纳
            $is_solution_approved = $solution["time_approved"] != 0;
        }

        !$is_solution_approved or BasicTool::throwException("删除失败,禁止删除已被采纳的答案");
        //删除图片
        $imageModel->deleteImageById($img_ids) or BasicTool::throwException("删除图片失败");
        //图片删除成功,删除答案
        $solutionModel->deleteSolutionById($id) or BasicTool::throwException("删除失败");

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
/**删除答案
 * POST,JSON接口
 * @param id 答案id，integer或一维数组
 * localhost/admin/courseSolution/courseSolutionnController.php?action=deleteSolutionByIdWithJson
 */
function deleteSolutionByIdWithJson(){
    deleteSolutionById("json");
}