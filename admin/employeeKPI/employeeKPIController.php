<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$currentUser = new \admin\user\UserModel();
$employeeKPIModel = new \admin\employeeKPI\EmployeeKPIModel();
call_user_func(BasicTool::get('action'));


function insertOrUpdateEmployeeKPIProfile(){
    global $currentUser,$employeeKPIModel;
    try{
        $currentUser->userId or BasicTool::throwException("请先登录");
        $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("权限不足");
        $main_uid = BasicTool::post("main_user_id","请指定管理员用户ID");
        $flag = BasicTool::post("flag","missing flag");
        $flag == "add" || $flag == "update" or BasicTool::throwException("incorrect flag");
        $id = BasicTool::post("id");
        $currentUser->getProfileOfUserById($main_uid) or BasicTool::throwException("用户ID({$main_uid})不存在");
        if ($flag == "add"){
            !$employeeKPIModel->getRowIdByMainUserId($main_uid) or BasicTool::throwException("添加失败:Duplicate main_uid");
            $id = false;
        }else{
            $id or BasicTool::post("Missing ID");
            $profile = $employeeKPIModel->getEmployeeKPIProfiles($id)[0] or BasicTool::throwException("更改失败:Profile does not exit");
            !$employeeKPIModel->getRowIdByMainUserId($main_uid) || $profile["main_user_id"] == $main_uid or BasicTool::throwException("更改失败:Duplicate main_uid");
        }
        $username = BasicTool::post("username");
        $uids = [];
        foreach($username as $name){
            if (!$name)
                continue;
            $_id = $currentUser->getUserIdByName($name) or BasicTool::throwException("用户名({$name})不存在");
            if ($main_uid != $_id && !in_array($_id,$uids))
                $uids[] = $_id;
        }
        $employeeKPIModel->insertOrUpdate($main_uid,$uids,$id) or BasicTool::throwException($employeeKPIModel->errorMsg);
        BasicTool::echoMessage("成功");
    }catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
    }
}



function deleteEmployeeKPIProfileById(){
    global $currentUser,$employeeKPIModel;
    try{
        $currentUser->userId or BasicTool::throwException("请先登录");
        $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("删除失败:权限不足");
        $id = BasicTool::post("id","请指定要删除的数据的id");
        $result = $employeeKPIModel->deleteEmployeeKPIProfileById($id) or BasicTool::throwException($employeeKPIModel->errorMsg);
        BasicTool::echoMessage("删除成功");
    }catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
    }
}



?>
