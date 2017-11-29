<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$questionModel = new \admin\courseQuestion\CourseQuestionModel();
$solutionModel = new \admin\courseSolution\CourseSolutionModel();
call_user_func(BasicTool::get('action'));

function addSolution($echoType = "normal"){
    global $imageModel,$currentUser,$solutionModel;
    try{
        $question_id= BasicTool::post("question_id","missing q_id");
        $answerer_user_id = $currentUser->userId;
        $description = BasicTool::post("description","Missing Description");

        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        $currImgArr = false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"course_solution");
        $bool = $solutionModel->addSolution($question_id,$answerer_user_id, $description, $imgArr[0], $imgArr[1], $imgArr[2]);

        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("添加成功");
            else
                BasicTool::echoMessage("添加失败");
        }
        else {
            if ($bool){
                $result=$solutionModel->getSolutionById($solutionModel->getInsertId());
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

function addSolutionWithJson(){
    addQuestion("normal");
}

function updateSolution($echoType = "normal"){
    global $imageModel,$currentUser,$solutionModel;
    try{
        $id= BasicTool::post("id","missing id");
        $description = BasicTool::post("description","Missing Description");

        $solution = $solutionModel->getSolutionById($id);
        $imgArr = array(BasicTool::post("img_id_1"),BasicTool::post("img_id_2"),BasicTool::post("img_id_3"));
        $currImgArr = array($solution["img_id_1"],$solution["img_id_2"],$solution["img_id_3"]);
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr,$currImgArr,3,"imgFile",$currentUser->userId,"course_solution");

        $bool = $solutionModel->updateSolution($id,$description, $imgArr[0], $imgArr[1], $imgArr[2]);

        if ($echoType == "normal") {
            if ($bool)
                BasicTool::echoMessage("更改成功");
            else
                BasicTool::echoMessage("更改失败");
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