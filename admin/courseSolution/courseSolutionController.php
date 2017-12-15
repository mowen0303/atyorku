<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$questionModel = new \admin\courseQuestion\CourseQuestionModel();
$solutionModel = new \admin\courseSolution\CourseSolutionModel();
$currentUser = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
call_user_func(BasicTool::get('action'));

function addSolution($echoType = "normal"){
    global $imageModel,$currentUser,$solutionModel;
    try{
        //权限验证
        ($currentUser->isUserHasAuthority("ADMIN") ||  $currentUser->isUserHasAuthority("COURSE_SOLUTION")) or BasicTool::throwException("权限不足");

        $question_id= BasicTool::post("question_id","missing q_id");
        $answerer_user_id = $currentUser->userId;
        $description = BasicTool::post("description","Missing Description");

        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        $currImgArr = false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"course_solution");
        $bool = $solutionModel->addSolution($question_id,$answerer_user_id, $description, $imgArr[0], $imgArr[1], $imgArr[2]);

        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("添加成功","/admin/courseSolution/index.php?action=getSolutions&question_id={$question_id}");
            else
                BasicTool::echoMessage("添加失败","/admin/courseSolution/index.php?action=getSolutions&question_id={$question_id}");
        }
        else {
            if ($bool){
                $result=$solutionModel->getSolutionById($solutionModel->getInsertId());
                BasicTool::echoJson(1, "添加成功",$result);
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

function addSolutionWithJson(){
    addQuestion("normal");
}

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

        $bool = $solutionModel->updateSolution($id,$description, $imgArr[0], $imgArr[1], $imgArr[2]);

        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("更改成功","/admin/courseSolution/index.php?action=getSolutions&question_id={$solution['question_id']}");
            else
                BasicTool::echoMessage("更改失败","/admin/courseSolution/index.php?action=getSolutions&question_id={$solution['question_id']}");
        }
        else {
            if ($bool){
                $result=$solutionModel->getSolutionById($id);
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
function updateSolutionWithJson(){
    updateSolution("json");
}
function getApprovedSolutionByQuestionIdWithJson(){
    global $solutionModel;
    $question_id = BasicTool::get("question_id","请指定question_id");
    $result = $solutionModel->getApprovedSolutionByQuestionId($question_id);
    if ($result)
        BasicTool::echoJson(1,"查询成功",$result);
    else
        BasicTool::echoJson(0,"空");
}
function getSolutionsByQuestionIdWithJson(){
    global $solutionModel;
    $question_id = BasicTool::get("question_id","请指定question_id");
    $result = $solutionModel->getSolutionsByQuestionId($question_id);
    if ($result)
        BasicTool::echoJson(1,"查询成功",$result);
    else
        BasicTool::echoJson(0,"空");
}
/*
 * 确认选中的答案没有被采纳
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
            ($currentUser->isUserHasAuthority("COURSE_QUESTION") && $currentUser->userId == $solution["answerer_user_id"]) or BasicTool::throwException("权限不足,删除失败");
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
        $bool = $imageModel->deleteImageById($img_ids);
        //图片删除成功,删除答案
        if ($bool){
            $bool = $solutionModel->deleteSolutionById($id);
        }
        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("删除成功");
            else
                BasicTool::echoMessage("删除失败");
        }
        else {
            if ($bool){
                BasicTool::echoJson(1, "删除成功");
            }
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
function deleteSolutionByIdWithJson(){
    deleteSolutionById("json");
}