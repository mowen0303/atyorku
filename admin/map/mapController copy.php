<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$mapModel = new \admin\map\MapModel();
$currentUser = new admin\user\UserModel();
call_user_func(BasicTool::get('action'));

function addLocation() {
    global $mapModel;
    global $currentUser;

    try {
        $currentUser->isUserHasAuthority('ADMIN') or BasicTool::throwException("无权增加地点");
        $init = BasicTool::post("init", "缩写不能为空", 3);
        $fullName = BasicTool::post("full_name", "大楼全名不能为空");
        $latitude = BasicTool::post("latitude");
        $longitude = BasicTool::post("longitude");
        $info = BasicTool::post("info");
        $shape = BasicTool::post("shape");

        // check if there exist an entry with the same full name or initial
        // doesLocationExist($fullName, $init);


        $mapModel->addLocation($init, $fullName, $info, $latitude, $longitude, $shape);
        BasicTool::echoMessage("大楼添加成功 :)");
    } catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function updateLocation() {
    global $mapModel;
    global $currentUser;
    try
    {
        $currentUser->isUserHasAuthority('ADMIN') or BasicTool::throwException("无权更改地点信息");

        // Validate field
        $id = BasicTool::post("id", "id不能为空", 3);
        $init = BasicTool::post("init", "缩写不能为空", 3);
        $fullName = BasicTool::post("full_name", "大楼全名不能为空");
        $lat = BasicTool::post("latitude");
        $lng = BasicTool::post("longitude");
        $info = BasicTool::post("info");
        $shape = BasicTool::post("shape");

        $mapModel->updateLocation($id, $init, $fullName, $info, $lat, $lng, $shape);
        BasicTool::echoMessage("大楼信息更新成功 :)");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function getAllLocations() {
    global $mapModel;
    BasicTool::echoJson(1, "获取大楼信息成功 :)", $mapModel->getAllLocations());
}

function getLocationById() {
    global $mapModel;
    $id = BasicTool::get("id", "请输入正确的大楼id :(");
    BasicTool::echoJson(1, "获取大楼信息成功 :)", $mapModel->getLocationById($id));
}

function getLocationByInit() {
    global $mapModel;
    $init = BasicTool::get("init", "请输入正确的大楼缩写 :(");
    BasicTool::echoJson(1, "获取大楼信息成功 :)", $mapModel->getLocationByInit($init));
}

function getLocationByFullName() {
    global $mapModel;
    $init = BasicTool::get("init", "请输入正确的大楼 :(");
    BasicTool::echoJson(1, "获取大楼信息成功 :)", $mapModel->getLocationByFullName($init));
}

// Maybe unnecessary, but just in case...
function getLocationsByInitKeyword() {
    global $mapModel;
    $str = BasicTool::get("str", "--");
    BasicTool::echoJson(1, "获取大楼信息成功 :)", $mapModel->getLocationsByInitKeyword($str));
}
// That, too
function getLocationsByFullNameKeyword() {
    global $mapModel;
    $str = BasicTool::get("str", "--");
    BasicTool::echoJson(1, "获取大楼信息成功 :)", $mapModel->getLocationsByFullNameKeyword($str));
}

// Search function is implemented on client side

function deleteLocationById() {
    global $mapModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority('ADMIN') or BasicTool::throwException("无权删除地点");
        $ids = BasicTool::post("id", "请选择要删除的ID");
        $idCount = '';
        foreach ($ids as $id) {
            $mapModel->deleteLocationById($id);
            $idCount = $idCount . " " . $id;
        }
        BasicTool::echoMessage("大楼ID {$idCount} 删除成功 :)");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

/**
 * --------------
 * Code Checked
 * --------------
 */
function getMapDataVersion() {
    global $mapModel;
    $mapModel->getMapDataVersion();
}