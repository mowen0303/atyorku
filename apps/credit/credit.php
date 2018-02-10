<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/commonClass/config.php";
$currentUser = new \admin\user\UserModel();
$transactionModel = new \admin\transaction\TransactionModel();
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
	body { margin: 0; padding:0; background-color: #F6F9FF; font-size:16px; line-height: 25px;font-family: PingFangSC-light,Arial,"Lucida Grande","Heiti SC","San Francisco",Helvetica }
	.info { background-color: #FFF; height: 180px; width: 94%; margin-left: 3%; margin-right: 3%; border-radius: 10px; position: absolute; top: 20%; box-shadow: 0 3px 0 #EDF4FE;  }
	.avatar {  height: 100px;  width: 100px;  border-radius: 50%;  background-color: white;  position: absolute;  top: -35%;  right: 10%;  margin:10px auto;}
	.head {background-color: #F44336; height: 200px;}
	.body { padding-top: 120px; padding-left: 20px; padding-right: 20px}
</style>
</head>
<body>
<div class="info">
	<p style="color: grey; margin-left: 5%;">总积分</p>
	<h1 style="margin-left: 5%; margin-top: 1%;"><?php echo $transactionModel->getCredit($currentUser->userId)?></h1>
	<img class="avatar" src="<?php echo $currentUser->userHeadImg?>" />
</div>
<div class="container">
	<div class="head"></div>
	<div class="body">
		<table>
			<tr>
				<th>积分值</th>
				<th>原因</th>
			</tr>
		<?php
		$transactionArr = $transactionModel->getTransactionsByUserId($currentUser->userId);
		foreach($transactionArr as $row){
		?>
			<tr>
				<td><?php echo $row['amount'] ?></td>
				<td><?php echo $row['description'] ?></td>
			</tr>
		<?php
		}
		?>

		</table>
	</div>
</div>
</body>
</html>