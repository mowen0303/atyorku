<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$transactionModel = new admin\transaction\transactionModel();
$currentUser = new \admin\user\UserModel();
call_user_func(BasicTool::get('action'));

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
    $transactionModel->addCredit($user_id,$amount,$description);

}
function deductCredit(){
    global $transactionModel;
    $user_id = BasicTool::post("user_id","");
    $amount = BasicTool::post("amount");
    $description = BasicTool::post("description");
    $transactionModel->deductCredit($user_id,$amount,$description);
}

function buy(){
    global $transactionModel;
    $buyer_user_id = BasicTool::post("buyer_user_id","");
    $amount = BasicTool::post("amount","");
    $buyer_description = BasicTool::post("buyer_description");
    $seller_description = BasicTool::post("seller_description");
    $seller_user_id = BasicTool::post("seller_user_id","");
    $transactionModel->buy($buyer_user_id,$seller_user_id,$amount,$buyer_description,$seller_description);
}