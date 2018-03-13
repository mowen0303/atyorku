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
 * JSON -  获取指定ID的课评信息
 * @param id course rating id
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getCourseRatingByIdWithJson&id=3
 */
function getCourseRatingByIdWithJson() {
    global $courseRatingModel;
    try {
        $id = BasicTool::get("id","需要提供Course rating ID");
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
 * JSON -  通过指定科目ID获取某一页课评
 * @param course_code_id 科目ID
 * @param pageSize 每一页课评获取量，默认值=20
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getListOfCourseRatingByCourseIdWithJson&course_code_id=3&pageSize=20
 */
function getListOfCourseRatingByCourseIdWithJson() {
    $courseId = BasicTool::get("course_code_id","请指定科目ID");
    getListOfCourseRatingWithJson("courseId", $courseId);
}

/**
 * JSON -  通过指定教授ID获取某一页课评
 * @param prof_id 教授ID
 * @param pageSize 每一页课评获取量，默认值=20
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getListOfCourseRatingByProfIdWithJson&prof_id=3&pageSize=20
 */
function getListOfCourseRatingByProfIdWithJson() {
    $profId = BasicTool::get("prof_id","请指定教授ID");
    getListOfCourseRatingWithJson("profId", $profId);
}

/**
 * JSON -  通过指定科目ID和教授ID获取某一页课评
 * @param course_code_id 科目ID
 * @param prof_id 教授ID
 * @param pageSize 每一页课评获取量，默认值=20
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getListOfCourseRatingByCourseIdProfIdWithJson&course_code_id=3&prof_id=3&pageSize=20
 */
function getListOfCourseRatingByCourseIdProfIdWithJson() {
    $courseId = BasicTool::get("course_code_id","请指定科目ID");
    $profId = BasicTool::get("prof_id","请指定教授ID");
    getListOfCourseRatingWithJson("courseIdProfId", $courseId, $profId);
}

/**
 * JSON -  获取某一页课评
 * @param t 获取类别 (courseId | profId | courseIdProfId)
 * @param v1 类别对应第一个必填值 (ex. 对应的course ID)
 * @param v2 类别对应第二个必填值 (ex. 对应的 prof ID)
 * @param pageSize 每一页课评获取量，默认值=20
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getListOfCourseRatingWithJson&pageSize=20
 */
function getListOfCourseRatingWithJson($t="normal", $v1=false, $v2=false) {
    global $courseRatingModel;
    try {
        $pageSize = BasicTool::get('pageSize') ?: 20;

        $result = NULL;

        switch($t) {
            case "courseId":
                $result = $courseRatingModel->getListOfCourseRatingByCourseId($v1, $pageSize);
                break;
            case "profId":
                $result = $courseRatingModel->getListOfCourseRatingByProfId($v1, $pageSize);
                break;
            case "courseIdProfId":
                $result = $courseRatingModel->getListOfCourseRatingByCourseIdProfId($v1, $v2, $pageSize);
                break;
            default:
                $result = $courseRatingModel->getListOfCourseRating(false, $pageSize);
        }

        if ($result) {
            BasicTool::echoJson(1, "成功", $result, $courseRatingModel->getTotalPage());
        } else {
            BasicTool::echoJson(0, "没有更多内容");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}

/**
 * JSON - 点赞一个指定ID的课评
 * @param id 课评ID
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=likeCourseRatingByIdWithJson
 */
function likeCourseRatingByIdWithJson() {
    global $courseRatingModel;
    try{
        $id = BasicTool::post("id","课评ID不能为空");
        $result = $courseRatingModel->likeCourseRating($id);
        if($result){
            BasicTool::echoJson(1,"成功",$result);
        }else{
            BasicTool::throwException($courseRatingModel->errorMsg);
        }
    }catch(Exception $e){
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON - 点赞一个指定ID的课评
 * @param id 课评ID
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=dislikeCourseRatingByIdWithJson
 */
function dislikeCourseRatingByIdWithJson() {
    global $courseRatingModel;
    try{
        $id = BasicTool::post("id","课评ID不能为空");
        $result = $courseRatingModel->dislikeCourseRating($id);
        if($result){
            BasicTool::echoJson(1,"成功",$result);
        }else{
            BasicTool::throwException($courseRatingModel->errorMsg);
        }
    }catch(Exception $e){
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON -  获取指定ID的课评报告信息
 * @param id course report id
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getCourseReportByIdWithJson&id=3
 */
function getCourseReportByIdWithJson() {
    global $courseRatingModel;
    try {
        $id = BasicTool::get("id","需要提供 Course report ID");
        $result = $courseRatingModel->getCourseReportById($id);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "未找到该ID对应的科目报告");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON -  获取指定ID的教授报告信息
 * @param id professor report id
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getProfReportByIdWithJson&id=3
 */
function getProfReportByIdWithJson() {
    global $courseRatingModel;
    try {
        $id = BasicTool::get("id","需要提供 professor report ID");
        $result = $courseRatingModel->getProfessorReportById($id);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "未找到该ID对应的教授报告");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON -  获取指定ID的科目教授报告信息
 * @param id course professor report id
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getCourseProfReportByIdWithJson&id=3
 */
function getCourseProfReportByIdWithJson() {
    global $courseRatingModel;
    try {
        $id = BasicTool::get("id","需要提供 Course professor report ID");
        $result = $courseRatingModel->getCourseProfessorReportById($id);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "未找到该ID对应的科目教授报告");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON -  获取指定科目ID的课评报告信息
 * @param course_id course code id
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getCourseReportByCourseIdWithJson&course_id=3
 */
function getCourseReportByCourseIdWithJson() {
    global $courseRatingModel;
    try {
        $courseId = BasicTool::get("course_id","需要提供 Course code ID");
        $result = $courseRatingModel->getCourseReportByCourseId($courseId);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "未找到该科目ID对应的科目报告");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON -  获取指定教授ID的教授报告信息
 * @param prof_id professor id
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getProfReportByProfIdWithJson&prof_id=3
 */
function getProfReportByProfIdWithJson() {
    global $courseRatingModel;
    try {
        $profId = BasicTool::get("prof_id","需要提供 professor ID");
        $result = $courseRatingModel->getProfessorReportByProfId($profId);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "未找到该教授ID对应的教授报告");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON -  获取指定科目ID和教授ID的科目教授报告信息
 * @param course_id course code id
 * @param prof_id professor id
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getCourseProfReportByCourseIdProfIdWithJson&course_id=3&prof_id=3
 */
function getCourseProfReportByCourseIdProfIdWithJson() {
    global $courseRatingModel;
    try {
        $courseId = BasicTool::get("course_id","需要提供 Course code ID");
        $profId = BasicTool::get("prof_id","需要提供 professor ID");
        $result = $courseRatingModel->getCourseProfessorReportByCourseIdProfId($courseId, $profId);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "未找到该科目ID和教授ID对应的科目教授报告");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}


/**
 * JSON -  获取某一页科目报告
 * @param pageSize 每一页科目报告获取量，默认值=20
 * @param course_code_parent_title 科目类别 EECS
 * @param course_code_child_title 科目编号 2030
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getListOfCourseReportWithJson&pageSize=20
 */
function getListOfCourseReportWithJson() {
    global $courseRatingModel;
    try {
        $pageSize = BasicTool::get('pageSize') ?: 20;
        $courseParentTitle = BasicTool::get('course_code_parent_title') ?: false;
        $courseChildTitle = BasicTool::get('course_code_child_title') ?: false;
        if(!$courseParentTitle && $courseChildTitle) {
            BasicTool::throwException("没有更多内容");
        }
        $result = $courseRatingModel->getListOfCourseReports($pageSize,$courseParentTitle,$courseChildTitle);

        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "没有更多内容");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}

/**
 * JSON -  获取某一页教授报告
 * @param pageSize 每一页教授报告获取量，默认值=20
 * @param prof_name 教授名
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getListOfProfReportWithJson&pageSize=20
 */
function getListOfProfReportWithJson() {
    global $courseRatingModel;
    try {
        $pageSize = BasicTool::get('pageSize') ?: 20;
        $professorTitle = BasicTool::get('prof_name') ?: false;

        $result = $courseRatingModel->getListOfProfessorReports($pageSize,$professorTitle);

        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "没有更多内容");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}


/**
 * JSON -  获取某一页科目教授报告
 * @param pageSize 每一页科目教授报告获取量，默认值=20
 * @param prof_id 可以指定一个教授id
 * @param course_code_id 可以指定一个科目id
* http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=getListOfCourseProfReportWithJson&pageSize=20&prof_id=123&course_code_id=1312 */
function getListOfCourseProfReportWithJson() {
    global $courseRatingModel;

    try {
        $pageSize = BasicTool::get('pageSize') ?: 20;
        $profId = BasicTool::get("prof_id");
        $courseCodeId = BasicTool::get("course_code_id");

        $result = $courseRatingModel->getListOfCourseProfessorReports($pageSize,$courseCodeId,$profId);

        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "没有更多内容");
        }
    } catch(Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}

/**
 * JSON -  通过课评ID和积分来奖励课评
 * @param id 课评ID
 * @param credit 奖励积分
 * http://www.atyorku.ca/admin/courseRating/courseRatingController.php?action=awardCourseRatingWithJson
 */
function awardCourseRatingWithJson() {
    awardCourseRating("json");
}

// =============== End Function with JSON ================= //


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
        $courseRatingUserId = BasicTool::post("user_id");    // 课评用户ID
        $currentCourseRating = null;

        // 验证权限
        if ($flag=='update') {
            $id = BasicTool::post('id',"课评ID不能为空");
            $currentCourseRating = $courseRatingModel->getCourseRatingById($id);
            $courseRatingUserId = $courseRatingUserId?:$currentCourseRating['user_id'];
            checkAuthority('update', $courseRatingUserId);
        } else if ($flag=='add') {
            $courseRatingUserId = $courseRatingUserId?:$currentUser->userId;
            checkAuthority('add',$courseRatingUserId);
        } else {
            BasicTool::throwException("Unknown Operation: {$flag}");
        }

        // 验证 Fields
        $parentCode = BasicTool::post("course_code_parent_title", "父类课评不能为空");
        $childCode = BasicTool::post("course_code_child_title", "子类课评不能为空");
        $courseCodeId = $courseCodeModel->getCourseIdByCourseCode($parentCode, $childCode);
        $courseCodeId or BasicTool::throwException("未找到指定科目Id");
        $profName = BasicTool::post("prof_name", "教授名称不能为空");
        $profId = $professorModel->getProfessorIdByFullName($profName);
        $profId or BasicTool::throwException("教授名称格式错误");
        $contentDiff = BasicTool::post("content_diff", "内容难度不能为空");
        $homeworkDiff = BasicTool::post("homework_diff");
        $testDiff = BasicTool::post("test_diff");
        $grade = BasicTool::post("grade");
        $year = BasicTool::post("year","学年不能为空");
        $term = BasicTool::post("term","学期不能为空");
        $comment = BasicTool::post("comment", "课评评论不能为空");
        $contentSummary = BasicTool::post("content_summary");

        $courseRatingUserId or BasicTool::throwException("无法找到卖家ID, 请重新登陆");
        // 执行
        if ($flag=='update') {
            $courseRatingModel->modifyCourseRating('update', $courseCodeId, $courseRatingUserId, $profId, $contentDiff, $homeworkDiff, $testDiff, $grade, $comment, $year, $term, $contentSummary, $currentCourseRating["id"]);
            if ($echoType == "normal") {
                BasicTool::echoMessage("修改成功","/admin/courseRating/index.php?listCourseRating");
            } else {
                BasicTool::echoJson(1, "修改成功");
            }
        } else if ($flag=='add') {
            $courseRatingModel->modifyCourseRating('add', $courseCodeId, $courseRatingUserId, $profId, $contentDiff, $homeworkDiff, $testDiff, $grade, $comment, $year, $term, $contentSummary);
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
                $currentCourseRating = $courseRatingModel->getCourseRatingById($v);
                $courseRatingUserId = $currentCourseRating['user_id'];
                checkAuthority('delete', $courseRatingUserId);
                $courseRatingModel->deleteCourseRatingById($v) or BasicTool::throwException("删除多个课评失败");
                $i++;
            }
        } else {
            $currentCourseRating = $courseRatingModel->getCourseRatingById($id);
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
        if($currentUser->userId===$id){
            $currentUser->isUserHasAuthority('COURSE_RATING') or BasicTool::throwException("权限不足");
        } else {
            $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
        }
    } else if ($flag == 'update' || $flag == 'delete') {
        $id or BasicTool::throwException("Modified course rating user id is required.");
        $currentUser->userId or BasicTool::throwException("权限不足，请先登录");
        if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('COURSE_RATING'))) {
            $currentUser->userId == $id or BasicTool::throwException("无权修改其他人的课评");
        }
    }
}



function deleteCourseReport($echoType="normal") {
    global $courseRatingModel;
    global $currentUser;

    try {
        $currentUser->isUserHasAuthority('ADMIN') or BasicTool::throwException("权限不足");
        $id = BasicTool::post('id',"请指定被删除科目报告ID");
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $courseRatingModel->deleteCourseReportById($v) or BasicTool::throwException("删除多个科目报告失败");
                $i++;
            }
        } else {
            $courseRatingModel->deleteCourseReportById($id) or BasicTool::throwException("删除1个科目报告失败");
            $i++;
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}个科目报告", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}个科目报告");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

function deleteProfessorReport($echoType="normal") {
    global $courseRatingModel;
    global $currentUser;

    try {
        $currentUser->isUserHasAuthority('ADMIN') or BasicTool::throwException("权限不足");
        $id = BasicTool::post('id',"请指定被删除教授报告ID");
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $courseRatingModel->deleteProfessorReportById($v) or BasicTool::throwException("删除多个教授报告失败");
                $i++;
            }
        } else {
            $courseRatingModel->deleteProfessorReportById($id) or BasicTool::throwException("删除1个教授报告失败");
            $i++;
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}个教授报告", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}个教授报告");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

function deleteCourseProfessorReport($echoType="normal") {
    global $courseRatingModel;
    global $currentUser;

    try {
        $currentUser->isUserHasAuthority('ADMIN') or BasicTool::throwException("权限不足");
        $id = BasicTool::post('id',"请指定被删除科目教授报告ID");
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $courseRatingModel->deleteCourseProfessorReportById($v) or BasicTool::throwException("删除多个科目教授报告失败");
                $i++;
            }
        } else {
            $courseRatingModel->deleteCourseProfessorReportById($id) or BasicTool::throwException("删除1个科目教授报告失败");
            $i++;
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}个科目教授报告", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}个科目教授报告");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


function updateAllReports(){
    global $courseRatingModel;
    global $currentUser;

    try {
        $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("权限不足");
        $result = $courseRatingModel->updateAllReports();
        $response = "# update = {$result['total']};  # succeed = {$result['succeed']};  # failed = {$result['failed']}\n";
        if(intval($result['failed'])>0){
            $response.="Log: \n";
            foreach($result['log'] as $row){
                $response.="courseCodeId={$row['course_code_id']};  profId={$row['prof_id']}\n";
                foreach($row as $k=>$v){
                    $response.="{$k}: {$v}\n";
                }
            }
        }
        BasicTool::echoMessage($response, $_SERVER['HTTP_REFERER']);
    } catch(Exception $e){
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}


/**
 * 奖励课评
 * @param string $echoType
 * @param id 课评ID
 * @param credit 奖励积分
 */
function awardCourseRating($echoType = "normal") {
    global $courseRatingModel;
    global $currentUser;

    try {
        $currentUser->isUserHasAuthority('ADMIN') or BasicTool::throwException("权限不足");
        $id = intval(BasicTool::post('id',"请指定要奖励的课评ID"));
        $credit = intval(BasicTool::post('credit', "积分奖励数额不能为空"));
        $courseRatingModel->awardCreditById($id, $credit) or BasicTool::throwException("奖励课评失败");
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功奖励课评", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功奖励课评");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}
