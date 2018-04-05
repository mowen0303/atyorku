<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/commonClass/config.php";
$dailyDescription = credit::$dailyCredit;
$currentUser = new \admin\user\UserModel();
if(!$currentUser->isLogin()){
    BasicTool::echoWapMessage("请先登录AtYorkU账号","未登录");
    die();
}
$userProfile = $currentUser->getProfileOfUserById($currentUser->userId);
$checkinCount = $userProfile["checkin_count"];
$checkinTime = $userProfile["checkin_last_time"];
$todayTime = BasicTool::getTodayTimestamp();
$checkinState = (int) $checkinTime!=$todayTime["startTime"];
$currentRewardCount = $checkinCount<=count($dailyDescription)?$checkinCount:count($dailyDescription);
$currentReward =(int) $dailyDescription[$currentRewardCount-1]["credit"];
?>

<!DOCTYPE html>
<head>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport"/>
    <meta charset="UTF-8">
    <link href="css.css" rel="stylesheet" type="text/css">
    <title>我的积分</title>
    <script src="/resource/js/jquery-2.1.4.js"></script>
    <script src="/resource/lib/easing.js"></script>
    <script src="/resource/lib/progressbar/progressbar.min.js"></script>
    <script src="js.js"></script>
</head>
<body>
    <div class="progress" id="progress" data-amount="5" data-val="<?php echo $currentReward?>">
        <div class="subtitle">(今日可领)</div>
    </div>
    <div class="descriptionBox">
        <p class="subtitle">已连续登陆<?php echo $checkinCount?>天</p>
    </div>
    <div class="btn" data-click-state="<?php echo $checkinState?>" id="creditBtn">立即领取</div>
    <div id="resultCard">
        <div class="iconBox"><img src="credit.png"/></div>
        <h1 id="resultTitle"></h1>
        <div id="resultCon" class="con"></div>
        <div id="closeBtn">确定</div>
    </div>
</body>
</html>
