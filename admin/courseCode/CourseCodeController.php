<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$courseCodeModel = new admin\courseCode\CourseCodeModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

//============ Function with JSON ===============//

/**
* JSON - 添加Course Code
* @param course_code_title 要添加的 Course Code title
* @param course_code_parent_id 要添加的 Course Code parent id, 如果为空默认为添加父类Course code
* http://www.atyorku.ca/admin/courseCode/courseCodeController.php?action=addCourseCodeWithJson&course_code_title=1000&course_code_parent_id=3
*/
function addCourseCodeWithJson() {
    global $courseCodeModel;
    $title = BasicTool::get("course_code_title","需要提供Course Code Title");
    $parentId = BasicTool::get("course_code_parent_id");
    if(!$parentId)
        $parentId = 0;
    try {
        $result = $courseCodeModel->addCourseCode($title, $parentId) or BasicTool::echoJson(0, "添加Course Code失败");
        BasicTool::echoJson(1, "添加成功");
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
* JSON - 删除Course Code
* @param course_code_id 要删除的 Course Code ID
* http://www.atyorku.ca/admin/courseCode/courseCodeController.php?action=removeCourseCodeWithJson&course_code_id=3
*/
function removeCourseCodeWithJson() {
    global $courseCodeModel;
    $id = BasicTool::get("course_code_id","需要提供要删除的Course Code ID");
    try {
        $result = $courseCodeModel->removeCourseCodeById($id) or BasicTool::echoJson(0, "删除Course Code失败");
        BasicTool::echoJson(1,"删除成功");
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

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
 * http://www.atyorku.ca/admin/courseCode/courseCodeController.php?action=getListOfChildCourseCodeByParentIdWithJson&course_code_parent_id=3
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
