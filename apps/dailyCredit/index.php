<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/commonClass/config.php";
$dailyCredit = credit::$dailyCredit;
$currentUser = new \admin\user\UserModel();
if(!$currentUser->isLogin()){
    BasicTool::echoWapMessage("请先登录AtYorkU账号","未登录");
    die();
}

$userProfile = $currentUser->getProfileOfUserById($currentUser->userId);
$checkinCount = $userProfile["checkin_count"];
$checkinTime = $userProfile["checkin_last_time"];
$todayTime = BasicTool::getTodayTimestamp();
$checkinState = 1;
$today = strtotime(date("Y-m-d")." 00:00:01");
$timeGap = $today-$checkinTime;
if($timeGap == 0){
    $checkinState=0;
}else if ($timeGap == 86400) {

}else{
    $checkinCount=1;
}
$currentRewardCount = $checkinCount<=count($dailyCredit)?$checkinCount:count($dailyCredit);
$currentRewardCount = $checkinState?$currentRewardCount:$currentRewardCount+1;
$currentRewardCount = $currentRewardCount>=count($dailyCredit)?count($dailyCredit):$currentRewardCount;

$currentReward =(float) $dailyCredit[$currentRewardCount-1]["credit"];
$creditAmount = (float) end($dailyCredit)["credit"];

?>

<!DOCTYPE html>
<head>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport"/>
    <meta charset="UTF-8">
    <link href="css.css?2" rel="stylesheet" type="text/css">
    <title>我的积分</title>
    <script src="/resource/js/jquery-2.1.4.js"></script>
    <script src="/resource/lib/easing.js"></script>
    <script src="/resource/lib/progressbar/progressbar.min.js"></script>
    <script src="js.js"></script>
</head>
<body>
    <div class="progress" id="progress" data-amount="<?php echo $creditAmount?>" data-val="<?php echo $currentReward?>">
        <div class="subtitle" id="statusText">(今日可领)</div>
    </div>
    <div class="descriptionBox">
        <?php
        if($checkinState){
           echo "<p class='subtitle'>今日是连续领取第{$checkinCount}天</p>";
        }else{
           echo "<p class='subtitle'>已连续领取{$checkinCount}天</p>";
        }
        ?>

    </div>
    <div class="btn" data-click-state="<?php echo $checkinState?>" id="creditBtn">立即领取</div>
    <div id="resultCard">
        <div class="iconBox"><img src="credit.png"/></div>
        <h1 id="resultTitle"></h1>
        <div id="resultCon" class="con"></div>
        <div id="closeBtn">确定</div>
    </div>
    <div class="creditRecord">
        <?php
        foreach ($dailyCredit as $dailyRow) {
        ?>
            <div class="desRow">
                <span><?php echo $dailyRow['description']; ?></span><i>积分<?php echo $dailyRow['credit']?></i>
            </div>
        <?php
        }
        ?>
    </div>
</body>
</html>
