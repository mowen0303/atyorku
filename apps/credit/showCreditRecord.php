<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/commonClass/config.php";
$currentUser = new \admin\user\UserModel();
$transactionModel = new \admin\transaction\TransactionModel();
use \Credit as Credit;

if(!$currentUser->isLogin()){
    BasicTool::echoWapMessage("请登录AtYorkU账号");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>积分记录</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { margin: 0; padding:0; background-color: #F6F9FF; font-size:16px; line-height: 25px;font-family: PingFangSC-light,Arial,"Lucida Grande","Heiti SC","San Francisco",Helvetica }
        table {border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color:  grey; border-radius: 5px;border-collapse:collapse; width: 100%;  background-color: #F6F9FF; margin-bottom: 50px; border-radius: 5%}
        tr {display:flex;border-top-width: 1px; border-top-style: solid; border-top-color: grey}
        td {border: 0px; text-align: center}
        .container {width:90%; margin-left: 5%; margin-right: 5%; margin-top: 5%}
    </style>
</head>
<body>

<h2 style="margin-left: 5%; margin-top: 10%;" >我的积分记录:</h2>
<div class="container">
    <?php
    $transactionArr = $transactionModel->getTransactionsByUserId($currentUser->userId);
    if(empty($transactionArr)){?>
    <h3 style="text-align: center">暂无积分记录</h3>
    <?php
    }else{?>

        <table>
            <tr>
                <th style="flex:3">说明</th>
                <th style="flex:1">积分</th>
            </tr>
            <?php
            foreach($transactionArr as $row){
                ?>
                <tr>
                    <td style="flex:3;"><?php echo $row['description'] ?></td>
                    <td style="flex:1;">积分<?php echo($row['amount']>=0 ? "+".$row['amount']:$row['amount']) ?></td>
                </tr>
                <?php
            }}
            ?>
        </table>
</div>

</body>
</html>