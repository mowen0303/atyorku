<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$mapModel = new \admin\map\MapModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

/**
 * 获取大楼数据版本号
 */
function getVersionWithJson() {
    global $mapModel;
    try {
        $version = $mapModel->getMapDataVersion() or BasicTool::throwException($mapModel->errorMsg);
        BasicTool::echoJson(1, "获取版本号成功", $version);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

function getAllBuildingsWithJson(){
    global $mapModel;
    try {
        $result = $mapModel->getAllBuildings() or BasicTool::throwException($mapModel->errorMsg);
        BasicTool::echoJson(1, "成功", $result);
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

function editBuilding(){
    global $mapModel,$currentUser;
    try{

        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足,添加失败");

        $id = BasicTool::post("id")?:0;
        $code=BasicTool::post("code","大楼代号CODE不能为空",3);
        $abbreviation=BasicTool::post("abbreviation",false,6);
        $fullName=BasicTool::post("full_name","大楼全名不能为空");
        $coordinates=BasicTool::post("coordinates","大楼坐标和形状坐标不能为空");
        $description=BasicTool::post("description");
        !$mapModel->checkUniqueByFullName($fullName,$id) or BasicTool::throwException("Building已经存在：{$fullName}");
        $mapModel->editBuilding($id,$code,$abbreviation,$fullName,$description,$coordinates) or BasicTool::throwException($mapModel->errorMsg);
        $mapModel->changeMapDataVersion() or BasicTool::throwException($mapModel->errorMsg);
        BasicTool::echoMessage("成功","/admin/map/");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}


function deleteBuildingByIDs(){
    global $mapModel;
    try{
        $IDs = BasicTool::post("ids","IDs不能为空");
        $mapModel->deleteBuildingByIDs($IDs);
        $mapModel->changeMapDataVersion() or BasicTool::throwException($mapModel->errorMsg);
        BasicTool::echoMessage("删除成功","/admin/map/");
    }catch(Exception $e){
        BasicTool::echoMessage($e->getMessage(),$_SERVER["HTTP_REFERER"]);
    }
}
