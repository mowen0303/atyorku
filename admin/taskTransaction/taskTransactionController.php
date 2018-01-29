<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$taskTransactionModel = new admin\taskTransaction\TaskTransactionModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


/************** Start JSON API *************/

/**
 * 添加一条成就交易
 * [POST] http://www.atyorku.ca/admin/taskTransaction/taskTransactionController.php?action=addTaskTransactionWithJson
 * @param task_type     成就交易类别 （表名）
 * @param item_id       产品ID
 * @param op            成就交易操作 ('add' | 'delete')
 */
function addTaskTransactionWithJson(){
    addTaskTransaction("json");
}

/**
 * 获得一页当前用户成就交易
 * [GET] http://www.atyorku.ca/admin/taskTransaction/taskTransactionController.php?action=getListOfMyTaskTransactionsWithJson&pageSize=40
 * @param pageSize      每页数量
 */
function getListOfMyTaskTransactionsWithJson(){
    getListOfMyTaskTransactions("json");
}

/**
 * 获得当前用户成就交易总结
 * [GET] http://www.atyorku.ca/admin/taskTransaction/taskTransactionController.php?action=getMyTaskTransactionSummaryWithJson
 */
function getMyTaskTransactionSummaryWithJson(){
    getMyTaskTransactionSummary("json");
}


/************** END JSON API *************/

/**
 * 添加一条成就交易
 * @param string $echoType (normal | json)
 */
function addTaskTransaction($echoType="normal"){
    global $taskTransactionModel;
    global $currentUser;
    try{
        $userId = BasicTool::post("user_id");
        if(!$userId) {
            $userId = $currentUser->userId or BasicTool::throwException("请先登录");
        }
        $type = BasicTool::post("task_type","成就类别不能为空");
        $itemId = BasicTool::post("item_id","成就交易产品ID不能为空");
        $op = BasicTool::post("op","成就交易operation不能为空");

        $result = $taskTransactionModel->addTaskTransaction($type,$userId,$itemId,$op);

        if ($echoType == "normal") {
            BasicTool::echoMessage("添加成功",$_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1,"添加成功",$result);
        }
    }catch(Exception $e){
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
 * 添加一条成就交易
 * @param string $echoType (normal | json)
 */
function getListOfMyTaskTransactions($echoType="normal"){
    global $taskTransactionModel;
    global $currentUser;
    try {
        $userId = $currentUser->userId or BasicTool::throwException("请先登录");
        $q = BasicTool::get("q") ?: false;
        $pageSize = BasicTool::get("pageSize") ?: 40;
        $result = $taskTransactionModel->getListOfTaskTransactionsByUserId($userId,$q,$pageSize);

        if ($echoType == "normal") {
            BasicTool::echoMessage("添加成功",$_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1,"添加成功",$result);
        }
    }catch(Exception $e){
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
 * 获得当前用户成就交易总结
 * @param string $echoType (normal | json)
 */
function getMyTaskTransactionSummary($echoType="normal"){
    global $taskTransactionModel;
    global $currentUser;
    try {
        $userId = $currentUser->userId or BasicTool::throwException("请先登录");
        $result = $taskTransactionModel->getSummaryOfTaskTransactionsByUserId($userId);

        if ($echoType == "normal") {
            BasicTool::echoMessage("添加成功",$_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1,"添加成功",$result);
        }
    }catch(Exception $e){
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
 * 删除一个或多个成就交易
 * @param string $echoType (normal | json)
 */
function deleteTaskTransaction($echoType = "normal") {
    global $taskTransactionModel;
    global $currentUser;
    try {
        $id = BasicTool::post('id') or BasicTool::throwException("请指定被删除成就交易ID");
        $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("无权删除成就交易");
        $i = 0;
        if (is_array($id)) {
            foreach ($id as $v) {
                $i++;
                $taskTransactionModel->deleteTaskTransactionById($v) or BasicTool::throwException("删除多个失败");
            }
        } else {
            $i++;
            $taskTransactionModel->deleteTaskTransactionById($id) or BasicTool::throwException("删除单个失败");
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}个成就交易", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}个成就交易");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}



