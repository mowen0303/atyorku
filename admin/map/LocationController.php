<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$locationModel = new admin\map\LocationModel(); // TODO - namespace not found?
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

function addLocation() {
    global $locationModel;
    try {
        $id = BasicTool::post("id", "id不能为空", 3);
        $init = BasicTool::post("init", "缩写不能为空", 3);
        $fullName = BasicTool::post("full_name", "大楼全名不能为空");
        $lat = BasicTool::post("lat");
        $lng = BasicTool::post("lng");
        $info = BasicTool::post("info");

        $locationModel->addLocation($id, $init, $fullName, $info, $lat, $lng);
        BasicTool::echoMessage("大楼添加成功");
    } catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function updateLocation() {
    global $locationModel;
    try
    {
        // Validate field
        $id = BasicTool::post("id", "id不能为空", 3);
        $init = BasicTool::post("init", "缩写不能为空", 3);
        $fullName = BasicTool::post("full_name", "大楼全名不能为空");
        $lat = BasicTool::post("lat");
        $lng = BasicTool::post("lng");
        $info = BasicTool::post("info");

        // Validate id format
        ($id >= 0 && $id < 100) or BasicTool::throwException("请输入正确的id");
        // TODO - Validate coordinate format

        $locationModel->updateLocation($id, $init, $fullName, $info, $lat, $lng);
        BasicTool::echoMessage("大楼信息更新成功");
    }
    catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}

function getLocationById() {
    global $locationModel;
    $id = BasicTool::get("id", "请输入正确的大楼id");
    BasicTool::echoJson(1, "获取大楼信息成功", $locationModel->getLocationById($id));
}

function getLocationByInit() {
    global $locationModel;
    $init = BasicTool::get("init", "请输入正确的大楼缩写");
    BasicTool::echoJson(1, "获取大楼信息成功", $locationModel->getLocationByInit($init));
}

// Do we get coordinate on the app from this controller???
//function getLocationCoordById() { }

//function getLocationCoordByInit() { }

//function getLocationPolygonById() { }

//function getLocationPolygonByInit() { }

function deleteLocationById() {
    global $locationModel;
    try {
        $id = BasicTool::post("id", "请输入要删除的大楼id");
        $locationModel->deleteLocationById($id[0]);
        BasicTool::echoMessage("大楼id {$id[0]}删除成功");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}