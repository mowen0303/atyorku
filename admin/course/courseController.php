<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$action = BasicTool::get('action');
$courseModel = new admin\course\CourseModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


function addLike()
{
    global  $courseModel;

    try {

        $id = BasicTool::get('id');
        //if(!$currentUser->isblocked()){throw new Exception('您已经被禁言');};
        $courseNumberId = BasicTool::get('c_cid');
        $courseNumberId = BasicTool::get('c_cid');
        $courseClassId = BasicTool::get('ClassId');


        if($courseModel->addLikeByCommentId($id)){

            BasicTool::echoMessage("点赞成功","/admin/course/index.php?s=listCourseDetail&c_cid=".$courseNumberId."&classId=".$courseClassId);
        }

    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }

}

function addLikeWithJson()
{
    global  $courseModel;

    try {

        $id = BasicTool::get('id');
        //if(!$currentUser->isblocked()){throw new Exception('您已经被禁言');};
        $courseNumberId = BasicTool::get('c_cid');
        $courseNumberId = BasicTool::get('c_cid');
        $courseClassId = BasicTool::get('ClassId');


        if($courseModel->addLikeByCommentId($id)){

            BasicTool::echoJson(1,"成功");
        }

    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }

}






/**
 * JSON - 获取课程详情和20条评论 (by Jerry)
 * @param courseId
 * @param $page
 * http://www.atyorku.ca/admin/course/courseController.php?action=getCourseDetailByIdWithJson&courseId=4&page=1
 */
function getCourseDetailByIdWithJson(){

    global $courseModel;
    global $currentUser;

    try{

        $courseId = BasicTool::get('courseId',"请输入课程ID");

        if($courseDetailArr = $courseModel->getRowOfCourseDescriptionByChildClassId($courseId)){
            $courseDetailArr["rateAllowed"] = $courseModel->isExistOfRateUser($currentUser->userId,$courseId) ? "no" : "yes";
            $lastTime = BasicTool::translateTime($courseDetailArr['time']);
            $courseDetailArr["userDescription"] = "注：课程内容可能会由时期和教授的不同而不同，这里的课程总结仅限参考。如果您对此课有更多的看法，欢迎评论反馈，我们会持续关注大家反馈并做进一步总结。（最后一次编辑时间：{$lastTime}）";

            //获取数据课程详情成功, 翻译难度和平均分
            foreach($courseDetailArr as $k => $v){

                if($k == "diff"){
                    $courseDetailArr['diffTranslate'] = $courseModel->translateDifficulty($v);
                }

                if($k == "average"){
                    $courseDetailArr[$k] = $courseModel->translateGrade($v);
                }

                if($k == "pass_rate"){
                    if($v == 0){
                        $courseDetailArr[$k] = "N";
                    }else{
                        $courseDetailArr[$k].="%";
                    }
                }
            }

            if($courseCommentArr = $courseModel->getCourseCommentListById($courseId)){

            } else {
                $courseCommentArr = 0;
            }

            BasicTool::echoJson(1,"获取用课程详情信息成功",$courseDetailArr,$courseCommentArr);

        }else{
            //获取数据失败
            throw new Exception("没有此课程");
        }

    } catch(Exception $e) {
        BasicTool::echoJson($e->getCode(),$e->getMessage());
    }
}


/**
 * JSON - 获取20条评论 (by Jerry)
 * @param courseId
 * @param $page
 * http://www.atyorku.ca/admin/course/courseController.php?action=getCourseCommentByIdWithJson&courseId=4&page=1
 */
function getCourseCommentByIdWithJson(){
    global $courseModel;

    try{

        $courseId = BasicTool::get('courseId',"请输入课程ID");

        if($arr = $courseModel->getCourseCommentListById($courseId)){

            BasicTool::echoJson(1,"获取用课程详情信息成功",0,$arr);

        } else {

            throw new Exception("没有评论");
        }

    } catch(Exception $e) {
        BasicTool::echoJson($e->getCode(),$e->getMessage());
    }
}






/**
 * @jerry
 * 测试 ----------------------------------------------------------------------------------------------------------------------------------------------------------
 * http://www.atyorku.ca/admin/course/courseController.php?action=test
 */
function test(){
    global $courseModel;
    $courseModel->test();
}





//-------------------------------增改增改增改增改增改增改增改增改增改增-------------------------------
//-------------------------------增改增改增改增改增改增改增改增改增改增-------------------------------
//-------------------------------增改增改增改增改增改增改增改增改增改增-------------------------------
//-------------------------------增改增改增改增改增改增改增改增改增改增-------------------------------
/*
 * @jerry
 * (增加)一个父分类
 */
function addCourseClassId() {
    global  $courseModel;
    global  $currentUser;
    try {
        $currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('COURSE_ADD') or BasicTool::throwException($currentUser->errorMsg);
        $arr = [];
        $arr['title'] = BasicTool::post('maintitle','课程代码不能为空',30);
        if(BasicTool::post('flag') == 'update'){
            $arr['id'] = BasicTool::post('f_cid',"子级分类ID不能为空");
            $courseModel->updateRowById('course_class',$arr['id'],$arr) or BasicTool::throwException($courseModel->errorMsg);;
            BasicTool::echoMessage("修改成功","/admin/course/");
        } else {
            !$courseModel->isExistOfCourseTitle($arr['title'],0) or BasicTool::throwException("此分类已经存在");
            $courseModel->addRow('course_class',$arr) or BasicTool::throwException($courseModel->errorMsg);
            BasicTool::echoMessage("添加成功","/admin/course/");
        }
    }
    catch (Exception $e)
    {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

/*
 * @jerry
 * (增加)一个子分类
 */
function addSubClass(){
    global $courseModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('COURSE_ADD') or BasicTool::throwException($currentUser->errorMsg);
        $arr = [];
        $arr['parent_id'] = BasicTool::post('f_cid',"父级分类ID不能为空");
        $arr['title'] = BasicTool::post('title',"课程编号不能为空",30);
        $arr['course_name'] = BasicTool::post('course_name',false,255);
        $arr['credits'] = BasicTool::getFirstOrSecond(BasicTool::post('credits',false,1),"0");
        $arr['descript'] = BasicTool::getFirstOrSecond(BasicTool::post('descript',false,65500),"信息还在整理中");
        $arr['credit_ex'] = BasicTool::getFirstOrSecond(BasicTool::post('credit_ex',false,2000),"信息还在整理中");
        $arr['prerequest'] = BasicTool::getFirstOrSecond(BasicTool::post('prerequest',false,2000),"信息还在整理中");
        $arr['textbook'] = BasicTool::getFirstOrSecond(BasicTool::post('textbook',false,255),"信息还在整理中");
        if(BasicTool::post('flag') == 'update'){
            $arr['id'] = BasicTool::post('c_cid',"子级分类ID不能为空");
            $courseModel->updateRowById('course_class',$arr['id'],$arr);
            BasicTool::echoMessage("修改成功");
        } else {
            !$courseModel->isExistOfCourseTitle($arr['title'],$arr['parent_id']) or BasicTool::throwException("此分类已经存在");
            $courseModel->addRow('course_class',$arr) or BasicTool::throwException($courseModel->errorMsg);
            $courseModel->countAmountOfFatherClass($arr['parent_id']);
            BasicTool::echoMessage("添加成功", "index.php?s=listClassChild&f_cid=".$arr['parent_id']."&f_title=".BasicTool::post('f_title'));
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

/*
 * @jerry
 * (增加)一个课评
 */
function addDescription() {
    global $courseModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('COURSE_ADD') or BasicTool::throwException($currentUser->errorMsg);
        $arr = [];
        $arr['course_class_id'] = BasicTool::post('c_cid',"子分类ID不能为空");
        $arr['summary'] = BasicTool::getFirstOrSecond(BasicTool::post('summary',false,65500),"信息还在整理中");
        $arr['structure'] = BasicTool::getFirstOrSecond(BasicTool::post('structure',false,65500),"信息还在整理中");
        $arr['wisechooes'] = BasicTool::getFirstOrSecond(BasicTool::post('wisechooes',false,65500),"信息还在整理中");
        $arr['strategy'] = BasicTool::getFirstOrSecond(BasicTool::post('strategy',false,65500),"信息还在整理中");
        $arr['user_id'] = BasicTool::getFirstOrSecond(BasicTool::post('user_id',false,255),1);
        $arr['time'] = time();
        $courseModel->isExistByFieldValue('user','id',$arr['user_id']) or BasicTool::throwException("用户不存在");
        if(BasicTool::post('flag') == 'update'){
            $id =  BasicTool::post('id',"子级分类ID不能为空");
            $courseModel->updateRowById('course_description',$id,$arr) or BasicTool::throwException($courseModel->errorMsg);
            BasicTool::echoMessage("修改成功","index.php?s=listDescription".BasicTool::post('argument'));
        } else {
            $courseModel->addRow('course_description',$arr) or BasicTool::throwException($courseModel->errorMsg);
            $courseModel->countAmountOfFatherClass($arr['course_class_id']);
            BasicTool::echoMessage("添加成功","index.php?s=listDescription".BasicTool::post('argument'));
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

/*
 * @jerry
 * (绑定)一个课评
 */
function setValidOfDescription(){
    global $courseModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('COURSE_UPDATE') or BasicTool::throwException($currentUser->errorMsg);
        $descriptionId =  BasicTool::get('id',"课评ID不能为空");
        $childClassId =  BasicTool::get('c_cid',"子级分类ID不能为空");
        $courseModel->setValidOfDescription($descriptionId,$childClassId) or BasicTool::throwException($courseModel->errorMsg);
        BasicTool::echoMessage("修改成功");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}
/**
 * @Jerry
 * JSON - (增加)一条评论
 * @param-GET c_cid 课程id
 * @param-POST comment 评论内容
 * http://www.atyorku.ca/admin/course/courseController.php?action=addCommentWithJson&c_cid=1
 */
function addCommentWithJson() {
    addComment("json");
}
function addComment($echoType = "normal") {
    global  $courseModel;
    global $currentUser;

    try{
        $currentUser->isUserHasAuthority('COURSE_COMMENT') or BasicTool::throwException($currentUser->errorMsg);
        $arr = [];
        //$arr['type'] = BasicTool::post('type','评论类型不能为空');
        $arr['course_class_id'] = BasicTool::post('course_class_id','课程分类ID不能为空');
        $arr['user_id'] = $currentUser->userId;
        $arr['comment'] = BasicTool::post('comment',"内容不能为空",255);
        $arr['time'] = time();
        $courseModel->addRow('course_comment',$arr) or BasicTool::throwException("没有修改任何数据");
        $courseModel->countAmountOfComment($arr['course_class_id']);
        if($echoType=="json"){
            BasicTool::echoJson(1,"留言成功");
        }else{
            BasicTool::echoMessage("留言成功");
        }

        //信息推送
        $ownerUserId = BasicTool::post("ownerUserId");
        $receiveUserId = BasicTool::post("receiveUserId");

        if($ownerUserId != null){

            //推送消息
            if($currentUser->userId != $ownerUserId && $currentUser->userId != $receiveUserId && $receiveUserId != $ownerUserId){
                $ownerUser = new \admin\user\UserModel($ownerUserId);
                $ownerUser->pushMsg($currentUser->userId,$currentUser->aliasName,"courseComment",$arr['course_class_id'],$arr['comment']);
            }

            if($currentUser->userId != $receiveUserId){
                $receiveUser = new \admin\user\UserModel($receiveUserId);
                $receiveUser->pushMsg($currentUser->userId,$currentUser->aliasName,"courseComment",$arr['course_class_id'],$arr['comment']);
            }

        }



    }catch(Exception $e){
        if($echoType=="json"){
            BasicTool::echoJson(0,$e->getMessage());
        }else {
            BasicTool::echoMessage($e->getMessage(),-1);
        }
    }
}


/**
 * @Jerry
 * JSON - (添加)课程评分
 * @param-GET c_cid 课程id
 * @param-POST diff 难度
 * @param-POST grade 成绩
 * http://www.atyorku.ca/admin/course/courseController.php?action=addRateWithJson&c_cid=1
 */
function addRateWithJson(){
    addRate("json");
}
function addRate($echoType = "normal") {
    global $courseModel;
    global $currentUser;
    try{
        //获取传值
        $course_class_id = BasicTool::get('classId');
        $courseNumberId = BasicTool::get('c_cid');

        $arr = [];
        $arr['course_class_id'] = $courseNumberId;
        $arr['diff'] = BasicTool::post('diff');
        $arr['grade'] = BasicTool::post('grade');
        $arr['user_id'] = $currentUser->userId;

        //添加一行
        !$courseModel->isExistOfRateUser($currentUser->userId,$courseNumberId) or BasicTool::throwException("每个课程只能评级一次");
        $courseModel->addRow('course_rate',$arr) or BasicTool::throwException("没有修改任何据数");
        //计算统计数据
        $courseModel->incrementStaticCount($courseNumberId);
        $currentUser->addActivity();

        if($echoType=="json"){
            BasicTool::echoJson(1,"成功");
        }else{
            BasicTool::echoMessage("修改成功","index.php?s=listCourseDetail&c_cid=".$courseNumberId."&classId=".$course_class_id);
        }
    }catch(Exception $e){
        if($echoType=="json"){
            BasicTool::echoJson(0,$e->getMessage());
        }else{
            BasicTool::echoMessage($e->getMessage(),-1);
        }
    }
}

//-------------------------------删删删删删删删删删删删删删删删删删删删-------------------------------
//-------------------------------删删删删删删删删删删删删删删删删删删删-------------------------------
//-------------------------------删删删删删删删删删删删删删删删删删删删-------------------------------
//-------------------------------删删删删删删删删删删删删删删删删删删删-------------------------------
function deleteFatherClass(){
    global $courseModel;
    global $currentUser;
    try{
        $currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('FORUM_DELETE') or BasicTool::throwException("无权删除");
        $fatherId = BasicTool::get('f_cid',"请输入课程ID");
        $courseModel->deleteFatherClass($fatherId) or BasicTool::throwException($courseModel->errorMsg);
        BasicTool::echoMessage("操作成功",$_SERVER['HTTP_REFERER']);
    } catch(Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function deleteChildClass(){
    global $courseModel;
    global $currentUser;
    try{
        $currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('FORUM_DELETE') or BasicTool::throwException("无权删除");
        $fatherClassId = BasicTool::get('f_cid',"父类id不能为空");
        $childClassId = BasicTool::get('c_cid',"子类id不能为空");
        $courseModel->deleteChildClass($childClassId) or BasicTool::throwException($courseModel->errorMsg);
        $courseModel->countAmountOfFatherClass($fatherClassId);
        BasicTool::echoMessage("操作成功",$_SERVER['HTTP_REFERER']);
    } catch(Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function deleteDescription(){
    global $courseModel;
    global $currentUser;
    try{
        $currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('FORUM_DELETE') or BasicTool::throwException("无权删除");
        $childClassId = BasicTool::get('c_cid',"子类id不能为空");
        $descriptionId = BasicTool::get('id',"课评id不能为空");
        $bindId = BasicTool::get('bindId',"课评id不能为空");
        $courseModel->realDeleteByFieldIn('course_description','id',$descriptionId) or BasicTool::throwException($courseModel->errorMsg);
        if($bindId == $descriptionId){
            $courseModel->setNotValidOfDescription($childClassId);
        }
        $courseModel->countAmountOfDescription($childClassId);
        BasicTool::echoMessage("操作成功");
    } catch(Exception $e) {
        BasicTool::echoMessage($e->getMessage());
    }
}
/**
 * JSON - 删除评论
 * @param-GET c_cid course_class的id
 * @param-POST id course_coment的id
 * http://www.atyorku.ca/admin/course/courseController.php?action=delCommentWithJson&c_cid=4
 */
function delCommentWithJson () {
    delComment("json");
}
function delComment($echoType = "normal") {
    global  $courseModel;
    global  $currentUser;

    try {
        $id = BasicTool::post("id");
        $courseNumberId = BasicTool::get("c_cid");
        if(!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('FORUM_DELETE'))){
            //如果用户不是管理员, 检查数据是否是用户发的
            if(is_array($id)){$id = $id[0];}
            $currentUser->userId == $courseModel->getUserIdOfCommentByCommentId($id) or BasicTool::throwException("无权删除其他人的留言");
        }
        $courseModel->realDeleteByFieldIn('course_comment', 'id', $id) or BasicTool::throwException($courseModel->errorMsg);
        $courseModel->countAmountOfComment($courseNumberId);
        $echoType == "normal" ? BasicTool::echoMessage("操作成功") : BasicTool::echoJson(1,"操作成功");

    }
    catch (Exception $e) {
        $echoType == "normal" ? BasicTool::echoMessage($e->getMessage()) : BasicTool::echoJson(0,$e->getMessage());

    }
}
//-------------------------------查查查查查查查查查查查查查查查查查查查-------------------------------
//-------------------------------查查查查查查查查查查查查查查查查查查查-------------------------------
//-------------------------------查查查查查查查查查查查查查查查查查查查-------------------------------
//-------------------------------查查查查查查查查查查查查查查查查查查查-------------------------------
/**
 * @Jerry
 * JSON (查询)获取父级课程列表
 * http://www.atyorku.ca/admin/course/courseController.php?action=getListOfFatherClassWithJson
 */
function getListOfFatherClassWithJson()
{
    global $courseModel;
    try {
        $arr = $courseModel->getListOfFatherClass(true) or BasicTool::throwException("没有课程");
        BasicTool::echoJson(1, "获取用课程大类信息成功", $arr);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON - 获取大类加小类名称如ADMS1000 返回值:id 大类名称ADMS 小类名称:1000
 * @param 大类id
 * http://localhost/admin/course/courseController.php?action=getCourseListWithJson&c_cid=1
 */
function getCourseListWithJson(){
    global $courseModel;
    try{
        $courseList = BasicTool::get('c_cid',"请输入大类ID");
        $arr = $courseModel->getListOfChildClassByParentId($courseList,true);
        if($arr){
            BasicTool::echoJson(1,"获取信息成功",$arr);
        }else{
            throw new Exception("此分类下还没录入信息");
        }
    } catch(Exception $e) {
        BasicTool::echoJson($e->getCode(),$e->getMessage());
    }
}
/**
 * JSON - 根据用户评论获得课程列表
 * @param 大类id
 * http://localhost/admin/course/courseController.php?action=getCourseListSpecificToUserCommentWithJson&userId=1
 */
function getCourseListSpecificToUserCommentWithJson(){
    global $courseModel;
    try{

        $userId = BasicTool::get('userId',"用户ID不能为空");

        $str = $courseModel->getIdOfCourseClassOfUserToCommentByUserId($userId);

        $arr = $courseModel->getListOfChildClassByParentId(0,true,$str);
        if($arr){
            BasicTool::echoJson(1,"获取信息成功",$arr);
        }else{
            throw new Exception("此分类下还没录入信息");
        }
    } catch(Exception $e) {
        BasicTool::echoJson($e->getCode(),$e->getMessage());
    }
}





?>