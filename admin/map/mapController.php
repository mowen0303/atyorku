<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$mapModel = new \admin\map\MapModel();
$currentUser = new admin\user\UserModel();
call_user_func(BasicTool::get('action'));

/**
 * 获取大楼数据版本号
 */
function getVersion() {
    global $mapModel;
    try {
        $version = $mapModel->getMapDataVersion() or BasicTool::throwException($mapModel->errorMsg);
        BasicTool::echoJson(1, "获取版本号成功", $version);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

function getAllBuildings(){
    global $mapModel;
    try {
        $version = $mapModel->getAllBuildings() or BasicTool::throwException($mapModel->errorMsg);
        BasicTool::echoJson(1, "成功", $version);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}