<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$xmlParser = new admin\xmlParser\XMLParserModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

function updateCourseCodeTable(){
    global $xmlParser,$currentUser;
    try {
        $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("权限不足");
        $file = $_FILES["file"];
        $file["type"] == "text/xml" or BasicTool::throwException("Invalid file format");
        $xmlParser->updateCourseCodeTable($file["tmp_name"]) or BasicTool::throwException($xmlParser->errorMsg);
        BasicTool::echoMessage("更新成功");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
    }
}