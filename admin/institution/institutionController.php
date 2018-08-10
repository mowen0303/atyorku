<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$institutionModel = new \admin\institution\InstitutionModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

/**
 * JSON -  获取一页学校
 * http://www.atyorku.ca/admin/insitution/institutionController.php?action=getListOInstitutionWithJson
 */
function getListOInstitutionWithJson() {
    global $institutionModel;
    try {
        $pageSize = BasicTool::get("pageSize") ?: 20;
        $result = $institutionModel->getListOfInstitution($pageSize);
        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "获取课程专辑类别列表失败");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

?>
