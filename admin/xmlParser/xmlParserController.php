<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$xmlParser = new admin\xmlParser\XMLParser();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

function updateCourseCodeTable(){
    global $xmlParser,$currentUser;
    try {
        $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("权限不足");
        $file_name = "su_2017_2018.xml";
        $xmlParser->updateCourseCodeTable($file_name) or BasicTool::throwException($xmlParser->errorMsg);
        BasicTool::echoMessage("更新成功");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
    }
}