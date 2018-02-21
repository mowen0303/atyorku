<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 2018-02-17
 * Time: 2:24 PM
 */

require_once $_SERVER["DOCUMENT_ROOT"]."/commonClass/config.php";
$currentUser = new \admin\user\UserModel();
$transactionModel = new \admin\transaction\TransactionModel();

if(!$currentUser->isLogin()){
    BasicTool::echoWapMessage("请登录AtYorkU账号");
    die();
}
?>

<!DOCTYPE html>
<head>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport"/>
    <meta charset="UTF-8">
    <link href="css.css?9" rel="stylesheet" type="text/css">
    <title>我的积分</title>
</head>
<body>
<div class="creditCard">
    <div class="avatar" style="background-image:url(<?php echo $currentUser->userHeadImg?>)"></div>
    <p class="title">总积分(点)</p>
    <p class="point"><?php echo $transactionModel->getCredit($currentUser->userId)?>.00</p>
    <div class="creditBtnBox">
        <div class="btn_1" href="#" onclick="alert('暂不支持自动充值,请联系官方客服微信:atyorku666. (100人民币=500积分)')">积分充值</div>
        <div class="btn_1" href="#" onclick="alert('暂不支持自动提现,请联系官方客服微信:atyorku666. (500积分=100人民币)')">提现</div>
    </div>
</div>
<div class="pageHeader">
    <a class="moreBtn" href="rule.php">查看积分规则 > </a>
</div>
<div class="pageBody">
    <div class="creditRecord">
        <h1>我的积分记录</h1>
        <?php
        $transactionArr = $transactionModel->getTransactionsByUserId($currentUser->userId);

        if($transactionArr) {
            foreach($transactionArr as $row){
                ?>
                <div class="desRow">
                    <i><?php echo($row['amount']>=0 ? "+".$row['amount']:$row['amount']) ?></i><span><?php echo $row['description'] ?></span><date><?php echo date("Y-m-d",$row['time']) ?></date>
                </div>
                <?php
            }
            $transactionModel->echoPageList();
        }else{
        ?>
            <p><span>暂无记录</span></p>
        <?php
        }
        ?>
    </div>
</div>
</body>
</html>
