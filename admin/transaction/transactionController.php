<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$transactionModel = new \admin\transaction\TransactionModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));


/**
 * JSON -  获取当前用户积分(需要先登录)
 * http://www.atyorku.ca/admin/transaction/transactionController.php?action=getCurrentUserCredit
 */
function getCurrentUserCredit() {
    global $transactionModel,$currentUser;
    try {
        $currentUser->isLogin() or BasicTool::throwException("未登录");
        $credit = $transactionModel->getCredit($currentUser->userId);
        BasicTool::echoJson(1,"获取成功",['credit'=>"{$credit}"]);
    } catch (Exception $e) {
        BasicTool::echoJson(0,$e->getMessage());
    }
}


function getTransactionsWithJson(){
    global $transactionModel;
    $result = $transactionModel->getTransactions();
    if ($result){
        BasicTool::echoJson(1,$result);
    }
    else{
        BasicTool::echoJson(0,"空");
    }
}
function getTransactionsByUserIdWithJson(){
    global $transactionModel;
    $user_id = BasicTool::post("user_id");
    $result = $transactionModel->getTransactionsByUserId($user_id);
    if ($result){
        BasicTool::echoJson(1,$result);
    }
    else{
        BasicTool::echoJson(0,"空");
    }
}


function addCredit(){
    global $transactionModel;
    $user_id = BasicTool::post("user_id","");
    $amount = BasicTool::post("amount","");
    $description = BasicTool::post("description");
    $section_name = BasicTool::post("section_name");
    $section_id = BasicTool::post("section_id");
    $pending = BasicTool::post("pending");
    $result = $transactionModel->addCredit($user_id,$amount,$description,$section_name,$section_id,$pending);
    BasicTool::echoJson(1,"ss",$result);

}
function deductCredit(){
    global $transactionModel;
    $user_id = BasicTool::post("user_id","");
    $amount = BasicTool::post("amount");
    $description = BasicTool::post("description");
    $section_name = BasicTool::post("section_name");
    $section_id = BasicTool::post("section_id");
    $pending = BasicTool::post("pending");
   $result = $transactionModel->deductCredit($user_id,$amount,$description,$section_name,$section_id,$pending);
    BasicTool::echoJson(1,"ss",$result);
}

function buy(){
    global $transactionModel;
    $buyer_user_id = BasicTool::post("buyer_user_id","");
    $amount = BasicTool::post("amount","");
    $buyer_description = BasicTool::post("buyer_description");
    $seller_description = BasicTool::post("seller_description");
    $seller_user_id = BasicTool::post("seller_user_id","");
    $buyer_pending = BasicTool::post("buyer_pending");
    $seller_pending = BasicTool::post("seller_pending");
    $section_name = BasicTool::post("section_name");
    $section_id = BasicTool::post("section_id");
    $result = $transactionModel->buy($buyer_user_id,$seller_user_id,$amount,$buyer_description,$seller_description,$section_name,$section_id,$buyer_pending,$seller_pending);
    BasicTool::echoJson(1,"",$result);
}

function clearCredit(){
    global $transactionModel;
    $user_id = BasicTool::post("user_id");
    $result = $transactionModel->clearCredit($user_id,"test","test",0);
    BasicTool::echoJson(1,"",$result);
}
function setPending(){
    global $transactionModel;
    $transactionId = BasicTool::post("transaction_id");
    $transactionModel->setPending($transactionId,0);
}