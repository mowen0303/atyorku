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
        td {border: 0px; margin-top: 10px; margin-bottom: 10px;}
        .head {background-color: #F44336; height: 15%; display: flex}
        .avatar {  height: 100px;  width: 100px;  border-radius: 50%;  background-color: white; margin:10px auto;}
        .container {width:90%; margin-left: 5%; margin-right: 5%; margin-top: 5%}
        .info {flex: 3; margin-left: 15px; margin-top: 5%}
    </style>
</head>
<body>
<div class="head">
    <img style="margin-left: 3%; margin-top: 5%;" class="avatar" src="<?php echo $currentUser->userHeadImg; ?>" />
    <div class="info">
        <p style="color: white">我的积分:</p>
        <h1 style="color: white"><?php echo $transactionModel->getCredit($currentUser->userId); ?></h1>
    </div>
</div>

<h2 style="margin-left: 5%; margin-top: 8%;" >积分明细:</h2>
<div class="container">
    <?php
    $transactionArr = $transactionModel->getTransactionsByUserId($currentUser->userId);
    if(empty($transactionArr)){?>
    <h3 style="text-align: center">暂无积分记录</h3>
    <?php
    }else{?>

        <table>

            <?php
            foreach($transactionArr as $row){
                ?>
                <tr>
                    <td style="flex:3; text-align: left"><?php echo $row['description'] ?></td>
                    <td style="flex:1; text-align: right;">积分<?php echo($row['amount']>=0 ? "+".$row['amount']:$row['amount']) ?></td>
                </tr>
                <?php
            }}
            ?>
        </table>
</div>

</body>
</html>