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
    modifyCourseCode("json");
}

/**
* JSON - 删除Course Code
* @param course_code_id 要删除的 Course Code ID
* http://www.atyorku.ca/admin/courseCode/courseCodeController.php?action=deleteCourseCodeWithJson&id=3
*/
function deleteCourseCodeWithJson() {
    deleteCourseCode("json");
}

/**
 * JSON -  获取指定ID的科目号
 * @param course_code_id Course Code类别ID
 * http://www.atyorku.ca/admin/courseCode/courseCodeController.php?action=getCourseCodeByIdWithJson&course_code_id=3
 */
function getCourseCodeByIdWithJson() {
    global $courseCodeModel;
    try {
        $id = BasicTool::get("course_code_id","需要提供Course Code ID");
        $result = $courseCodeModel->getCourseCodeById($id);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "未找到该ID对应的科目");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * JSON -  获取父类科目列表
 * http://www.atyorku.ca/admin/courseCode/courseCodeController.php?action=getListOfParentCourseCodeWithJson
 */
function getListOfParentCourseCodeWithJson() {
    global $courseCodeModel;
    try {
        $result = $courseCodeModel->getListOfCourseCodeByParentId();
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "获取科目类别列表失败");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * JSON -  通过父类ID获取子类科目列表
 * @param course_code_parent_id Course Code父类别ID
 * http://www.atyorku.ca/admin/courseCode/courseCodeController.php?action=getListOfChildCourseCodeByParentIdWithJson&course_code_parent_id=3
 */
function getListOfChildCourseCodeByParentIdWithJson() {
    global $courseCodeModel;
    try {
        $id = BasicTool::get("course_code_parent_id","需要提供科目父类ID");
        $result = $courseCodeModel->getListOfCourseCodeByParentId($id);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "获取子科目列表失败");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * JSON -  通过搜索字段获取科目列表
 * @param q 搜索字段
 * @param parent_only 容许只搜索父类 默认false
 * http://www.atyorku.ca/admin/courseCode/courseCodeController.php?action=getListOfCourseCodeByStringWithJson&q=
 */
function getListOfCourseCodeByStringWithJson(){
    global $courseCodeModel;
    try{
        $str = BasicTool::get("q") ?: "";
        $allowParentOnly = BasicTool::get("parent_only") ?: false;
        $result = $courseCodeModel->getListOfCourseCodeByString($str, $allowParentOnly);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "获取科目列表失败");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON - 通过索引来获取科目ID和Title
 * @param parent
 * @param child
 * http://www.atyorku.ca/admin/courseCode/courseCodeController.php?action=getCourseCodeByStringWithJson&parent=ADMS&child=1000
 */
function getCourseCodeByStringWithJson(){
    global $courseCodeModel;
    try{
        $p = BasicTool::get("parent", "科目大类名称不能为空");
        $c = BasicTool::get("child") ?: "";
        $result = $courseCodeModel->getCourseCodeByString($p,$c);
        BasicTool::echoJson(1, "找到匹配的科目", $result);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


// =============== End Function with JSON ================= //

/**
* 修改或添加一个Course Code
* @param flag add | update
* @param title 新的或修改的Course Code 名称
* @param parent_id 父类科目ID
* @param id 需要修改的科目ID (flag=="update"时必填)
*/
function modifyCourseCode($echoType = "normal") {
    global $courseCodeModel;
    try {
        $flag = BasicTool::post("flag");
        $title = BasicTool::post("title","需要提供 Course Code Title");
        $fullTitle = BasicTool::post("full_title","需要提供 Course Code Full Title");
        $credits = BasicTool::post("credits");
        $description = BasicTool::post("description");
        $course_code_sort = BasicTool::post("course_code_sort");
        if(!$credits) $credits = 0;
        $parentId = (int) BasicTool::post("parent_id","请提供父类科目ID");
        checkAuthority();
        if ($flag == "add") {
            $result = $courseCodeModel->addCourseCode($title, $fullTitle, $credits, $parentId,$description,$course_code_sort);
            if ($echoType == "normal") {
                BasicTool::echoMessage("添加成功","/admin/courseCode/index.php?listCourseCode&parent_id={$parentId}");
            } else {
                BasicTool::echoJson(1, "添加成功");
            }
        } else if ($flag == "update") {
            $id = BasicTool::post("id","需要提供要修改的Course Code ID");
            $result = $courseCodeModel->updateCourseCodeById($id, $title, $fullTitle, $credits,$description,$course_code_sort);
            if ($echoType == "normal") {
                BasicTool::echoMessage("修改成功","/admin/courseCode/index.php?listCourseCode&parent_id={$parentId}");
            } else {
                BasicTool::echoJson(1, "修改成功");
            }
        }
    } catch(Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }

}

/**
* 删除一个或多个科目
* @param id 数字或array 要删除的id
*/
function deleteCourseCode($echoType = "normal") {
    global $courseCodeModel;
    global $currentUser;
    try {
        checkAuthority();
        $id = BasicTool::post('id',"请指定被删除科目ID");
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $courseCodeModel->deleteCourseCodeById($v) or BasicTool::throwException("删除多个科目失败");
                $i++;
            }
        } else {
            $courseCodeModel->deleteCourseCodeById($id) or BasicTool::throwException("删除1个科目失败");
            $i++;
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}个科目", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}个科目");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

function checkAuthority() {
    global $currentUser;
    if (!($currentUser->isUserHasAuthority('ADMIN'))) {
        BasicTool::throwException("无权限操作");
    }
}
