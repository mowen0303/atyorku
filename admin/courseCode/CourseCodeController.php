<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$courseCodeModel = new admin\courseCode\CourseCodeModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

//============ Function with JSON ===============//

/**
 * JSON -  获取指定ID的科目号
 * @param course_code_id Course Code类别ID
 * http://www.atyorku.ca/admin/courseCode/courseCodeController.php?action=getCourseCodeByIdWithJson&course_code_id=3
 */
function getCourseCodeByIdWithJson() {
    global $courseCodeModel;
    $id = BasicTool::get("course_code_id","需要提供Course Code ID");
    $result = $courseCodeModel->getCourseCodeById($id);
    if ($result) {
        BasicTool::echoJson(1, "成功", $result);
    } else {
        BasicTool::echoJson(0, "未找到该ID对应的科目");
    }
}


/**
 * JSON -  获取父类科目列表
 * http://www.atyorku.ca/admin/courseCode/courseCodeController.php?action=getListOfParentCourseCodeWithJson
 */
function getListOfParentCourseCodeWithJson() {
    global $courseCodeModel;
    $result = $courseCodeModel->getListOfCourseCodeByParentId();
    if ($result) {
        BasicTool::echoJson(1, "成功", $result);
    } else {
        BasicTool::echoJson(0, "获取科目类别列表失败");
    }
}


/**
 * JSON -  通过父类ID获取子类科目列表
 * @param course_code_parent_id Course Code父类别ID
 * http://www.atyorku.ca/admin/courseCode/courseCodeController.php?action=getListOfParentCourseCodeWithJson&course_code_parent_id=3
 */
function getListOfChildCourseCodeByParentIdWithJson() {
    global $courseCodeModel;
    $id = BasicTool::get("course_code_parent_id","需要提供科目父类ID");
    $result = $courseCodeModel->getListOfCourseCodeByParentId($id);
    if ($result) {
        BasicTool::echoJson(1, "成功", $result);
    } else {
        BasicTool::echoJson(0, "获取子科目列表失败");
    }
}
