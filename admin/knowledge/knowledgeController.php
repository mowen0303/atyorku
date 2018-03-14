<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$knowledgeCategoryModel = new \admin\knowledgeCategory\KnowledgeCategoryModel();
$knowledgeModel = new \admin\knowledge\KnowledgeModel();
$currentUser = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$courseCodeModel = new \admin\courseCode\CourseCodeModel();
$profModel = new \admin\professor\ProfessorModel();
$transactionModel = new \admin\transaction\TransactionModel();
call_user_func(BasicTool::get('action'));

/**添加活动
 * POST
 * @param knowledge_category_id 考试类别id
 * @param course_code_parent 学科
 * @param course_code_child 课程代号
 * @param prof_name 教授全名
 * @param price 价格
 * @param description
 * @param knowledge_point_description 所有考点
 * @param count_knowledge_points
 * @param term_year 学年
 * @param term_semester 学期
 * @param sort
 * @param page
 * @param img_id
 * localhost/admin/knowledge/knowledgeController.php?action=addKnowledge
 */
function addKnowledge($echoType = "normal") {
    global $knowledgeModel,$knowledgeCategoryModel,$courseCodeModel,$profModel,$currentUser,$imageModel;
    try {
        //判断权限
        ($currentUser->isUserHasAuthority("ADMIN") || $currentUser->isUserHasAuthority("KNOWLEDGE")) or BasicTool::throwException("权限不足,添加失败");

        $seller_user_id = $currentUser->userId;
        $knowledge_category_id = BasicTool::post("knowledge_category_id", "请指定考试类别");
        $knowledgeCategoryModel->getKnowledgeCategoryById($knowledge_category_id) or BasicTool::throwException("考试类别不存在");
        $course_code_parent = BasicTool::post("course_code_parent", "courseCodeParent 不能为空");
        $course_code_child = BasicTool::post("course_code_child", "courseCodeChild 不能为空");
        $course_code_id = $courseCodeModel->getCourseIdByCourseCode($course_code_parent, $course_code_child) or BasicTool::throwException("此课程不存在");
        $prof_name = BasicTool::post("prof_name", "请指定教授");
        $prof_id = $profModel->getProfessorIdByFullName($prof_name);

        $price = (float)BasicTool::post("price", "请指定价格");
        $price >= 0 or BasicTool::throwException("请输入有效的价格");
        $description = BasicTool::post("description");
        $knowledge_point_description = BasicTool::post("knowledge_point_description");
        $count_knowledge_points = BasicTool::post("count_knowledge_points");
        $term_year = BasicTool::post("term_year","请指定学年");
        $term_semester = BasicTool::post("term_semester","请指定学期");
        $sort = $echoType == "json" ? 0 : BasicTool::post("sort");
        ($sort == 0 || $sort == 1 || $sort == NULL || $echoType == "json") or BasicTool::echoMessage("添加失败,请输入有效的排序值(0或者1)");
        $imgArr = array(BasicTool::post("img_id"));
        $img_id = $imageModel->uploadImagesWithExistingImages($imgArr, false, 1, "imgFile", $currentUser->userId, "knowledge")[0];
        if (($img_id && $knowledge_point_description) || (!$img_id && !$knowledge_point_description)){
            $imageModel->deleteImageById($img_id);
            BasicTool::throwException("添加失败!!");
        }

        $insert_id = $knowledgeModel->addKnowledge($seller_user_id,$knowledge_category_id,$img_id,$course_code_id,$prof_id,$price,$description,$knowledge_point_description,$count_knowledge_points,$term_year,$term_semester,$sort) or BasicTool::throwException($knowledgeModel->errorMsg);
        if ($echoType == "normal") {
            BasicTool::echoMessage("添加成功");
        } else {
            $result = $knowledgeModel->getKnowledgeById($insert_id);
            $result["publish_time"] = BasicTool::translateTime($result["publish_time"]);
            $result["enroll_year"] = BasicTool::translateEnrollYear($result["enroll_year"]);
            BasicTool::echoJson(1, "添加成功",$result);
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


/**添加活动
 * POST
 * JSON接口
 * @param knowledge_category_id 考试类别id
 * @param courseCodeParent 学科
 * @param courseCodeChild 课程代号
 * @param prof_name 教授全名
 * @param price 价格
 * @param description
 * @param knowledge_point_description 所有考点
 * @param count_knowledge_points
 * @param term_year 学年
 * @param term_semester 学期
 * @param sort
 * @param page
 * @param img_id
 * localhost/admin/knowledge/knowledgeController.php?action=addKnowledgeWithJson
 */
function addKnowledgeWithJson() {
    addKnowledge("json");
}

/**根据课程,教授,学年学期查询
 * GET,JSON接口
 * @param course_code_parent 学科
 * @param course_code_child 课程代号
 * @param prof_name
 * @param term_year
 * @param term_semester
 * @param page 页数
 * localhost/admin/knowledge/knowledgeController.php?action=getKnowledgeByCourseCodeProfNameWithJson
 */
function getKnowledgeByCourseCodeProfNameWithJson(){
    global $knowledgeModel, $profModel,$currentUser;
    try {
        $course_code_parent = BasicTool::get("course_code_parent");
        $course_code_child = BasicTool::get("course_code_child");
        $prof_name = BasicTool::get("prof_name");
        $prof_id = $profModel->getProfessorIdByFullName($prof_name);
        $term_year = BasicTool::get("term_year");
        $term_semester = BasicTool::get("term_semester");
        $result = $knowledgeModel->getKnowledgeByCourseCodeIdProfId($currentUser->userId?$currentUser->userId:0,$currentUser->isUserHasAuthority("ADMIN"),$course_code_parent,$course_code_child,$prof_id,$term_year,$term_semester) or BasicTool::throwException("空");
        $results = [];
        foreach ($result as $knowledge) {
            $knowledge["publish_time"] = BasicTool::translateTime($knowledge["publish_time"]);
            $knowledge["enroll_year"] = BasicTool::translateEnrollYear($knowledge["enroll_year"]);
            $knowledge["is_admin"] = $currentUser->isUserHasAuthority("ADMIN")?1:"";
            $knowledge["is_seller"] = $currentUser->userId == $knowledge["seller_user_id"]?1:"";
            array_push($results, $knowledge);
        }
        BasicTool::echoJson(1, "查询成功", $results);

    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**根据ID删除回忆录
 * POST
 * JSON接口
 * @param id 一维数组或integer
 * localhost/admin/knowledge/knowledgeController.php?action=deleteKnowledge
 */
function deleteKnowledge($echoType = "normal") {
    global $knowledgeModel, $imageModel,$currentUser;
    try {
        $id = BasicTool::post("id");
        $img_ids = array();
        if (is_array($id)) {
            //后台的批量删除,判断管理员权限
            $currentUser->isUserHasAuthority("ADMIN") && $currentUser->isUserHasAuthority("KNOWLEDGE") or BasicTool::throwException("权限不足,删除失败");
            $concat = "";
            foreach ($id as $i) {
                $i = $i + 0;
                $i = $i . ",";
                $concat = $concat . $i;
            }
            $concat = substr($concat, 0, -1);
            $sql = "SELECT * FROM knowledge WHERE id in ({$concat})";
            $sqlTool = SqlTool::getSqlTool();
            $knowledges = $sqlTool->getListBySql($sql);
            foreach ($knowledges as $knowledge) {
                if ($knowledge["img_id"])
                    $img_ids[] = $knowledge["img_id"];
            }
        }
        else {
            $knowledge = $knowledgeModel->getKnowledgeById($id) or BasicTool::throwException("考试回忆录不存在");
            //前端删除单个提问,判断发布人或管理员权限
            ($currentUser->isUserHasAuthority("KNOWLEDGE") && ($currentUser->isUserHasAuthority("ADMIN") || $currentUser->userId == $knowledge["seller_user_id"])) or BasicTool::throwException("权限不足,删除失败");
            if ($knowledge["img_id"])
                $img_ids[] = $knowledge["img_id"];
        }
        //删除图片
        if (count($img_ids) != 0)
            $imageModel->deleteImageById($img_ids) or BasicTool::throwException("删除图片失败");
        //删除回忆录,考点因关联而被自动删除
        $knowledgeModel->deleteKnowledgeById($id) or BasicTool::throwException("删除失败");
        if ($echoType == "normal") {
            BasicTool::echoMessage("删除成功");
        } else {
            BasicTool::echoJson(1, "删除成功");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}
function deleteKnowledgeWithJson(){
    deleteKnowledge("json");
}

/**购买考试回忆录
 * POST
 * @param id
 * localhost/admin/knowledge/knowledgeController.php?action=buyKnowledge
 */
function buyKnowledge($echoType = "normal"){
    global $knowledgeModel,$transactionModel,$currentUser;
    try {
        $id = BasicTool::post("id","请指定回忆录id");
        $knowledge = $knowledgeModel->getKnowledgeById($id) or BasicTool::throwException("回忆录不存在");
        !$transactionModel->isPurchased($currentUser->userId,"knowledge",$id) or BasicTool::throwException("已购买当前回忆录");
        $transactionModel->buy($currentUser->userId,$knowledge["seller_user_id"],$knowledge["amount"],"购买考试回忆录","出售考试回忆录","knowledge",$id,0,0) or BasicTool::throwException($transactionModel->errorMsg);
        $knowledgeModel->updateCountSold($id);
        if ($echoType == "normal") {
            BasicTool::echoMessage("购买成功");
        }
        else {
            $result = $knowledgeModel->getKnowledgeById($id);
            $result["is_purchased"] = 1;
            $result["publish_time"] = BasicTool::translateTime($knowledge["publish_time"]);
            $result["enroll_year"] = BasicTool::translateEnrollYear($knowledge["enroll_year"]);
            BasicTool::echoJson(1, "购买成功",$result);
        }
    }
    catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}
/**购买考试回忆录
 * POST,JSON
 * @param id
 * localhost/admin/knowledge/knowledgeController.php?action=buyKnowledgeWithJson
 */
function buyKnowledgeWithJson(){
    buyKnowledge("json");
}
function getKnowledgeByIdWithJson(){
    global $knowledgeModel;
    $id = BasicTool::get("id");
    BasicTool::echoJson(1,"",$knowledgeModel->getKnowledgeById($id));
}