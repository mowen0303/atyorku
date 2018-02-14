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
<title>查看积分</title>
<meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" /><meta name="viewport" content="width=device-width, initial-scale=1">
<style>
	body {margin: 0; padding:0; background-color: #fff;color:#484848;  font-size:14px; line-height: 25px;font-family: PingFangSC-light,Arial,"Lucida Grande","Heiti SC","San Francisco",Helvetica }
    table {width: 90%; margin: 0 5%}
    td { flex:1; border-bottom: 1px solid #eee; padding:5px}
    .info { background-color: #FFF; height: 160px; left:15px; right:15px; border-radius: 10px; position: absolute; top: 100px;box-shadow: 0px 0px 14px rgba(0,54,140,0.2) }
	.avatar {  height: 56px;  width: 56px;  border-radius: 50%;  background-color: white;  position: absolute;  top: -33px;  right: 10%;  margin:10px auto;}
	.head {background-color: #F44336; height: 200px;}
	.body { padding-top: 80px}
    .infoBtn {display: flex; flex-direction: row}
    .btnStyle{border: none;color: white;padding: 10px 24px;text-align: center;font-size: 16px;margin: 4px 2px;margin-left: 3%;margin-right: 3%;border-radius: 20px; -webkit-appearance: none}
    .btn { display: block; text-align: center; text-decoration: none; background:#fff;box-shadow: 0px 0px 14px rgba(0,54,140,0.2); margin: 0 15px; height: 50px; line-height: 50px; border-radius: 6px; color:#484848}
    h2 { font-weight: normal; color:#585858; font-size: 1.3em}
    h3 { font-weight: normal; color:#585858; font-size: 1.1em; text-align: center}

</style>
</head>
<body>
<div class="info">
	<p style="color: grey; margin-left: 5%;">总积分(点)</p>
	<h1 style="margin-left: 5%; margin-top: 1%;"><?php echo $transactionModel->getCredit($currentUser->userId)?>.00</h1>
	<img class="avatar" src="<?php echo $currentUser->userHeadImg?>" />
    <div class="infoBtn">
        <input style="flex:1; background-color: #fc316c" class="btnStyle" value="积分充值" type="button" onclick="alert('目前不支持自动充值,请联系官方客服微信号 atyorku666 进行积分充值 100人民币=500积分')" />
        <input style="flex:1; background-color: #fc316c" class="btnStyle" value="积分兑现" type="button" onclick="alert('目前不支持自动兑换,请联系官方客服微信号 atyorku666 进行积分充值 500积分=100人民币')" />
    </div>
</div>



<div class="container">
	<div class="head"></div>
	<div class="body">
        <a class="btn" href="showCreditRecord.php">查看我的积分记录</a>
        <div>
            <h2 style="text-align: center; margin-top: 50px;">获取积分途径</h2>
            <h3>每日签到</h3>
            <table>
                <?php
                $dailyDescription = credit::$dailyCredit;
                foreach ($dailyDescription as $dailyRow) {?>
                    <tr>
                        <td><?php echo $dailyRow['description']; ?></td>
                        <td>积分<?php echo($dailyRow['credit']>=0 ? "+" . $dailyRow['credit']:$dailyRow['credit']) ; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>

            <h3>积分机制</h3>
            <table>
                <tr>
                    <td>在问答系统中发布提问</td>
                    <td>积分<?php echo(credit::$addCourseQuestion['credit']>=0 ? "+".credit::$addCourseQuestion['credit']:credit::$addCourseQuestion['credit']) ; ?></td>
                </tr>
                <tr>
                    <td>在问答系统中删除提问</td>
                    <td>积分<?php echo(credit::$deleteCourseQuestion['credit']>=0 ? "+".credit::$deleteCourseQuestion['credit']:credit::$deleteCourseQuestion['credit']); ?></td>
                </tr>
                <tr>
                    <td>在问答系统中发布答案</td>
                    <td>积分<?php echo(credit::$addCourseSolution['credit']>=0 ? "+".credit::$addCourseSolution['credit']:credit::$addCourseSolution['credit']) ; ?></td>
                </tr>
                <tr>
                    <td>在资料市场中发布资料</td>
                    <td>积分<?php echo(credit::$addBook['credit']>=0 ? "+".credit::$addBook['credit']:credit::$addBook['credit']) ; ?></td>
                </tr>
                <tr>
                    <td>在课评系统中发布课评</td>
                    <td>积分<?php echo(credit::$addCourseRating['credit']>=0 ? "+".credit::$addCourseRating['credit']:credit::$addCourseRating['credit']) ; ?></td>
                </tr>
            </table>
            <div style="margin: 10px">
                <h3>兑换政策</h3>
                <p>每5积分可以兑换1元 (人民币)</p>
                <p>兑换用户必须是约克大学 (加拿大) 的学生,将会通过学生号进行认证。</p>
                <p>必须满500分才能进行兑换,每笔兑换中系统将收取10%的手续费。</p>
                <p>联系人工客服,微信号 atyorku666 进行兑换</p>
            </div>

        </div>

	</div>
</div>
</body>
</html>