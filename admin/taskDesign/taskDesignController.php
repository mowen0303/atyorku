<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$taskDesignModel = new admin\taskDesign\TaskDesignModel();
$imageModel = new admin\image\ImageModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


/**
 * 添加一条成就设计
 * @param string $echoType (normal | json)
 */
function modifyTaskDesign($echoType="normal"){
    global $taskDesignModel;
    global $imageModel;
    try{
        $flag = BasicTool::post("flag");
        $title = BasicTool::post("title");
        $bonus = BasicTool::post("bonus");
        $book = BasicTool::post("book");
        $courseRating = BasicTool::post("course_rating");
        $courseQuestion = BasicTool::post("course_question");
        $forum = BasicTool::post("forum");
        $knowledge = BasicTool::post("knowledge");

        $currentTaskDesign = null;
        $result = null;

        if($flag === "update") {
            $currentTaskDesign = $taskDesignModel->getTaskDesignById(BasicTool::post('id'));
        }

        $currImgArr = ($currentTaskDesign!=null) ? array($currentTaskDesign['icon_id']) : false;

        $imgArr = $imageModel->uploadImagesWithExistingImages(array(BasicTool::post("icon_id")),$currImgArr,1,"imgFile","taskDesign","task_design",false);


        if ($flag=='update') {
            // update
            $result = $taskDesignModel->updateTaskDesign($currentTaskDesign['id'],$title,$bonus,$imgArr[0],$book,$courseRating,$courseQuestion,$forum,$knowledge);
        } else {
            // add
            $result = $taskDesignModel->addTaskDesign($title,$bonus,$imgArr[0],$book,$courseRating,$courseQuestion,$forum,$knowledge);
        }

        $msg = $flag==="update" ? "修改成功" : "添加成功";
        if($echoType=="normal"){
            BasicTool::echoMessage($msg,"/admin/taskDesign/index.php?listTaskDesign");
        }else{
            BasicTool::echoJson(1,$msg,$result);
        }
    }catch(Exception $e){
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
 * 添加一条成就交易
 * @param string $echoType (normal | json)
 */
function getListOfTaskDesigns($echoType="normal"){
    global $taskDesignModel;
    try {
        $pageSize = BasicTool::get("pageSize") ?: 40;
        $result = $taskDesignModel->getListOfTaskDesigns($pageSize);

        if ($echoType == "normal") {
            BasicTool::echoMessage("添加成功",$_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1,"添加成功",$result);
        }
    }catch(Exception $e){
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


/**
 * 删除一个或多个成就设计
 * @param string $echoType (normal | json)
 */
function deleteTaskDesign($echoType = "normal") {
    global $taskDesignModel;
    global $currentUser;
    try {
        $id = BasicTool::post('id') or BasicTool::throwException("请指定被删除成就设计ID");
        if (!$currentUser->isUserHasAuthority("ADMIN") && !$currentUser->isUserHasAuthority("TASK_DESIGN")){
            BasicTool::throwException("无权删除成就设计");
        }
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $i++;
                $taskDesignModel->deleteTaskDesignById($v) or BasicTool::throwException("删除多个失败");
            }
        } else {
            $i++;
            $taskDesignModel->deleteTaskDesignById($id) or BasicTool::throwException("删除单个失败");
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}个成就设计", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}个成就设计");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


