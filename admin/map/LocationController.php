<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$locationModel = new admin\map\LocationModel();
$currentUser = new admin\user\UserModel();
call_user_func(BasicTool::get('action'));

function addLocation() {
    global $locationModel;
    try {
//        $id = BasicTool::post("id", "id不能为空", 3);
        $init = BasicTool::post("init", "缩写不能为空", 3);
        $fullName = BasicTool::post("full_name", "大楼全名不能为空");
        $lat = BasicTool::post("lat");
        $lng = BasicTool::post("lng");
        $info = BasicTool::post("info");
        $shape = BasicTool::post("shape");

        $locationModel->addLocation($init, $fullName, $info, $lat, $lng, $shape);
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
        $shape = BasicTool::post("shape");

        // Validate id format
//        ($id >= 0 && $id < 100) or BasicTool::throwException("请输入正确的id");
        // TODO - In the future, validate coordinate format

        $locationModel->updateLocation($id, $init, $fullName, $info, $lat, $lng, $shape);
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

// Search function is implemented on client side
//function getLocationCoordById() { }

//function getLocationCoordByInit() { }

//function getLocationPolygonById() { }

//function getLocationPolygonByInit() { }

function deleteLocationById() {
    global $locationModel;
    try {
        $ids = BasicTool::post("id", "请选择要删除的ID");
        $idCount = '';
        foreach ($ids as $id) {
            $locationModel->deleteLocationById($id);
            $idCount = $idCount . " " . $id;
        }
        BasicTool::echoMessage("大楼ID {$idCount} 删除成功");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}