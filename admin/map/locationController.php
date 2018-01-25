<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$locationModel = new admin\map\LocationModel();
$currentUser = new admin\user\UserModel();
call_user_func(BasicTool::get('action'));

function addLocation() {
    global $locationModel;
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


        $locationModel->addLocation($init, $fullName, $info, $latitude, $longitude, $shape);
        BasicTool::echoMessage("大楼添加成功 :)");
    } catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function updateLocation() {
    global $locationModel;
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

        $locationModel->updateLocation($id, $init, $fullName, $info, $lat, $lng, $shape);
        BasicTool::echoMessage("大楼信息更新成功 :)");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function getAllLocations() {
    global $locationModel;
    BasicTool::echoJson(1, "获取大楼信息成功 :)", $locationModel->getAllLocations());
}

function getLocationById() {
    global $locationModel;
    $id = BasicTool::get("id", "请输入正确的大楼id :(");
    BasicTool::echoJson(1, "获取大楼信息成功 :)", $locationModel->getLocationById($id));
}

function getLocationByInit() {
    global $locationModel;
    $init = BasicTool::get("init", "请输入正确的大楼缩写 :(");
    BasicTool::echoJson(1, "获取大楼信息成功 :)", $locationModel->getLocationByInit($init));
}

function getLocationByFullName() {
    global $locationModel;
    $init = BasicTool::get("init", "请输入正确的大楼 :(");
    BasicTool::echoJson(1, "获取大楼信息成功 :)", $locationModel->getLocationByFullName($init));
}

// Maybe unnecessary, but just in case...
function getLocationsByInitKeyword() {
    global $locationModel;
    $str = BasicTool::get("str", "--");
    BasicTool::echoJson(1, "获取大楼信息成功 :)", $locationModel->getLocationsByInitKeyword($str));
}
// That, too
function getLocationsByFullNameKeyword() {
    global $locationModel;
    $str = BasicTool::get("str", "--");
    BasicTool::echoJson(1, "获取大楼信息成功 :)", $locationModel->getLocationsByFullNameKeyword($str));
}

// Search function is implemented on client side

function deleteLocationById() {
    global $locationModel;
    global $currentUser;
    try {
        $currentUser->isUserHasAuthority('ADMIN') or BasicTool::throwException("无权删除地点");
        $ids = BasicTool::post("id", "请选择要删除的ID");
        $idCount = '';
        foreach ($ids as $id) {
            $locationModel->deleteLocationById($id);
            $idCount = $idCount . " " . $id;
        }
        BasicTool::echoMessage("大楼ID {$idCount} 删除成功 :)");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function getMapDataVersion() {
    global $locationModel;
    echo json_encode($locationModel->getMapDataVersion());
//    BasicTool::echoJson(1, "获取数据库版本成功", $locationModel->getMapDataVersion());
}