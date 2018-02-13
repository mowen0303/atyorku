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
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
	body {margin: 0; padding:0; background-color: #F6F9FF; font-size:16px; line-height: 25px;font-family: PingFangSC-light,Arial,"Lucida Grande","Heiti SC","San Francisco",Helvetica }
    table {border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color:  grey; border-radius: 5px;border-collapse:collapse; width: 100%;  background-color: white; margin-bottom: 25px; border-radius: 5%}
    tr {display:flex;border-top-width: 1px; border-top-style: solid; border-top-color: grey}
    td {border: 0px; text-align: center; margin-top: 10px; margin-bottom: 10px;}
    .info { background-color: #FFF; height: 180px; width: 94%; margin-left: 3%; margin-right: 3%; border-radius: 10px; position: absolute; top: 20%; box-shadow: 0 3px 0 #EDF4FE;  }
	.avatar {  height: 100px;  width: 100px;  border-radius: 50%;  background-color: white;  position: absolute;  top: -35%;  right: 10%;  margin:10px auto;}
	.head {background-color: #F44336; height: 200px;}
	.body { position: absolute; top: 65%;padding-top: 0px; padding-left: 20px; padding-right: 20px; width: 90%}
    .infoBtn {display: flex; flex-direction: row}
    .btnStyle{border: none;color: white;padding: 10px 24px;text-align: center;font-size: 16px;margin: 4px 2px;margin-left: 3%;margin-right: 3%;border-radius: 5px; -webkit-appearance: none}

</style>
</head>
<body>
<div class="info">
	<p style="color: grey; margin-left: 5%;">总积分(点)</p>
	<h1 style="margin-left: 5%; margin-top: 1%;"><?php echo $transactionModel->getCredit($currentUser->userId)?></h1>
	<img class="avatar" src="<?php echo $currentUser->userHeadImg?>" />
    <div class="infoBtn">
        <input style="flex:1; background-color: #30a371" class="btnStyle" value="积分充值" type="button" onclick="alert('请联系管理员')" />
        <input style="flex:1; background-color: #30a371" class="btnStyle" value="积分兑现" type="button" onclick="alert('将在atYorkU 2.0正式版开放')" />
    </div>
</div>

<input style="background-color:#13a2a3; flex:1; width: 90%; height: 8%; margin-left:5%; margin-right: 5%; position: absolute; top: 55%" class="btnStyle" value="查看我的积分记录" type="button" onclick="location.href='showCreditRecord.php'" />

<div class="container">
	<div class="head"></div>
	<div class="body">
        <h2 style="text-align: center; margin-top: 50px;">积分政策</h2>
        <h3>每日签到:</h3>
        <table>

        <?php
        $dailyDescription = credit::$dailyCredit;
        foreach ($dailyDescription as $dailyRow) {?>
            <tr>
                <td style="flex:3;"><?php echo $dailyRow['description']; ?></td>
                <td style="flex:1;">积分<?php echo($dailyRow['credit']>=0 ? "+" . $dailyRow['credit']:$dailyRow['credit']) ; ?></td>
            </tr>
        <?php
        }
        ?>
        </table>

        <h3>积分机制:</h3>
        <table>
            <tr>
                <td style="flex:3">在问答系统中发布提问</td>
                <td style="flex:1">积分<?php echo(credit::$addCourseQuestion['credit']>=0 ? "+".credit::$addCourseQuestion['credit']:credit::$addCourseQuestion['credit']) ; ?></td>
            </tr>
            <tr>
                <td style="flex:3">在问答系统中删除提问</td>
                <td style="flex:1">积分<?php echo(credit::$deleteCourseQuestion['credit']>=0 ? "+".credit::$deleteCourseQuestion['credit']:credit::$deleteCourseQuestion['credit']); ?></td>
            </tr>
            <tr>
                <td style="flex:3">在问答系统中发布答案</td>
                <td style="flex:1">积分<?php echo(credit::$addCourseSolution['credit']>=0 ? "+".credit::$addCourseSolution['credit']:credit::$addCourseSolution['credit']) ; ?></td>
            </tr>
            <tr>
                <td style="flex:3">在资料市场中发布资料</td>
                <td style="flex:1">积分<?php echo(credit::$addBook['credit']>=0 ? "+".credit::$addBook['credit']:credit::$addBook['credit']) ; ?></td>
            </tr>
            <tr>
                <td style="flex:3">在课评系统中发布课评</td>
                <td style="flex:1">积分<?php echo(credit::$addCourseRating['credit']>=0 ? "+".credit::$addCourseRating['credit']:credit::$addCourseRating['credit']) ; ?></td>
            </tr>
        </table>

        <h3>兑换政策:</h3>
        <p>每5积分可以兑换1元 (人民币)</p>
        <ol>
            <li>兑换用户必须是约克大学 (加拿大) 的学生,将会通过学生号进行认证。</li>
            <li>必须满500分才能进行兑换,每笔兑换中系统将收取10%的手续费。</li>
        </ol>
        <br>


	</div>
</div>
</body>
</html>