<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$courseRatingModel = new admin\courseRating\CourseRatingModel();
$courseCodeModel = new admin\courseCode\CourseCodeModel();
$professorModel = new admin\professor\ProfessorModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

//============ Function with JSON ===============//

/**
* JSON - 添加Course Code
* @param course_code_title 要添加的 Course Code title
* @param course_code_parent_id 要添加的 Course Code parent id, 如果为空默认为添加父类Course code
* http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=addCourseRatingWithJson&course_code_title=1000&course_code_parent_id=3
*/
function addCourseRatingWithJson() {
    modifyCourseRating("json");
}

/**
* JSON - 删除Course Code
* @param course_code_id 要删除的 Course Code ID
* http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=deleteCourseRatingWithJson&id=3
*/
function deleteCourseRatingWithJson() {
    deleteCourseRating("json");
}

/**
 * JSON -  获取指定ID的课评号
 * @param course_code_id Course Code类别ID
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getCourseRatingByIdWithJson&course_code_id=3
 */
function getCourseRatingByIdWithJson() {
    global $courseRatingModel;
    try {
        $id = BasicTool::get("course_code_id","需要提供Course Code ID");
        $result = $courseRatingModel->getCourseRatingById($id);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "未找到该ID对应的课评");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * JSON -  获取父类课评列表
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getListOfParentCourseRatingWithJson
 */
function getListOfParentCourseRatingWithJson() {
    global $courseRatingModel;
    try {
        $result = $courseRatingModel->getListOfCourseRatingByParentId();
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "获取课评类别列表失败");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * JSON -  通过父类ID获取子类课评列表
 * @param course_code_parent_id Course Code父类别ID
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getListOfChildCourseRatingByParentIdWithJson&course_code_parent_id=3
 */
function getListOfChildCourseRatingByParentIdWithJson() {
    global $courseRatingModel;
    try {
        $id = BasicTool::get("course_code_parent_id","需要提供课评父类ID");
        $result = $courseRatingModel->getListOfCourseRatingByParentId($id);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "获取子课评列表失败");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


// =============== End Function with JSON ================= //

/**
* 通过ID获取一个 Course Rating
* @param id course rating id
* @return mysqliResult course rating
*/
function getCourseRatingById($id, $echoType = "normal") {
    global $courseRatingModel;

    try {
        $currentCourseRating = $courseRatingModel->getCourseRatingById($id);
        if (!$currentCourseRating) {
            BasicTool::throwException("无法找到课评");
        }
        return $currentCourseRating;
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
* 修改或添加一个 Course Rating
* @param flag add | update
*/
function modifyCourseRating($echoType = "normal") {
    global $courseRatingModel;
    global $currentUser;
    global $courseCodeModel;
    global $professorModel;

    try{
        $flag = BasicTool::post('flag');
        $courseRatingUserId = false;    // 课评用户ID
        $currentCourseRating = null;

        // 验证权限
        if ($flag=='update') {
            $id = BasicTool::post('id',"课评ID不能为空");
            $currentCourseRating = getCourseRatingById($id, $echoType);
            $courseRatingUserId = $currentCourseRating['user_id'];
            checkAuthority('update', $courseRatingUserId);
        } else if ($flag=='add') {
            checkAuthority('add');
        }

        // 验证 Fields
        $parentCode = BasicTool::post("course_code_parent_title", "父类课评不能为空");
        $childCode = BasicTool::post("course_code_child_title", "子类课评不能为空");
        $courseCodeId = $courseCodeModel->getCourseIdByCourseCode($parentCode, $childCode);
        $courseCodeId or BasicTool::throwException("未找到指定课评Id");
        $profName = BasicTool::post("prof_name", "教授名称不能为空");
        $profId = $professorModel->getProfessorIdByFullName($profName);
        $profId or BasicTool::throwException("教授名称格式错误");
        $contentDiff = BasicTool::post("content_diff", "内容难度不能为空");
        $homeworkDiff = BasicTool::post("homework_diff", "作业难度不能为空");
        $testDiff = BasicTool::post("test_diff", "考试难度不能为空");
        $hasTextbook = BasicTool::post("has_textbook", "是否需要教科书不能为空");
        $recommendation = BasicTool::post("recommendation", "是否推荐课程不能为空");
        $grade = BasicTool::post("grade")?:"";
        $year = BasicTool::post("year","学年不能为空");
        $term = BasicTool::post("term","学期不能为空");
        $comment = BasicTool::post("comment", "课评评论不能为空");

        // 执行
        if ($flag=='update') {
            $userId = $courseRatingUserId or BasicTool::throwException("无法找到卖家ID, 请重新登陆");
            $courseRatingModel->modifyCourseRating('update', $courseCodeId, $userId, $profId, $contentDiff, $homeworkDiff, $testDiff, $hasTextbook, $grade, $comment, $recommendation, $year, $term, $currentCourseRating["id"]);
            if ($echoType == "normal") {
                BasicTool::echoMessage("修改成功","/admin/courseRating/index.php?listCourseRating");
            } else {
                BasicTool::echoJson(1, "修改成功");
            }
        } else if ($flag=='add') {
            $userId = $currentUser->userId or BasicTool::throwException("无法找到用户ID, 请重新登陆");
            $courseRatingModel->modifyCourseRating('add', $courseCodeId, $userId, $profId, $contentDiff, $homeworkDiff, $testDiff, $hasTextbook, $grade, $comment, $recommendation, $year, $term);
            if ($echoType == "normal") {
                BasicTool::echoMessage("添加成功","/admin/courseRating/index.php?listCourseRating");
            } else {
                BasicTool::echoJson(1, "添加成功");
            }
        }
    }
    catch (Exception $e){
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
* 删除一个或多个课评
* @param id 数字或array 要删除的id
*/
function deleteCourseRating($echoType = "normal") {
    global $courseRatingModel;
    global $currentUser;
    try {
        $id = BasicTool::post('id',"请指定被删除课评ID");
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $currentCourseRating = getCourseRatingById($v, $echoType);
                $courseRatingUserId = $currentCourseRating['user_id'];
                checkAuthority('delete', $courseRatingUserId);
                $courseRatingModel->deleteCourseRatingById($v) or BasicTool::throwException("删除多个课评失败");
                $i++;
            }
        } else {
            $currentCourseRating = getCourseRatingById($id, $echoType);
            $courseRatingUserId = $currentCourseRating['user_id'];
            checkAuthority('delete', $courseRatingUserId);
            $courseRatingModel->deleteCourseRatingById($id) or BasicTool::throwException("删除1个课评失败");
            $i++;
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}个课评", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}个课评");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
* 检测权限
* @param flag 'add' | 'update' | 'delete'
* @param id current course rating user id (required for 'update' and 'delete')
*/
function checkAuthority($flag, $id) {
    global $currentUser;
    if ($flag == 'add') {
        $currentUser->isUserHasAuthority('COURSE_RATING') or BasicTool::throwException("权限不足");
    } else if ($flag == 'update' || $flag == 'delete') {
        $id or BasicTool::throwException("Modified course rating user id is required.");
        $currentUser->userId or BasicTool::throwException("权限不足，请先登录");
        if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('COURSE_RATING'))) {
            $currentUser->userId == $id or BasicTool::throwException("无权修改其他人的课评");
        }
    }
}
