<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventModel = new \apps\event\event\EventModel();
$imageModel = new \admin\image\ImageModel();
$userModel = new \admin\user\UserModel();
$event_id = BasicTool::get("event_id");
$event = $eventModel->getEvent($event_id);
$sponsor = $userModel->getProfileOfUserById($event["sponsor_user_id"]);
$imgUrls = array();
if ($event['img_id_1']) $imgUrls[] = $imageModel->getImageById($event['img_id_1'])['url'];
if ($event['img_id_2']) $imgUrls[] = $imageModel->getImageById($event['img_id_2'])['url'];
if ($event['img_id_3']) $imgUrls[] = $imageModel->getImageById($event['img_id_3'])['url'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <meta charset="UTF-8"/>
    <style>
        * {margin:0; padding:0}
        *:focus{outline:none}
        body {background: #f8f8f8; letter-spacing: 0.6px}
        body,pre {color: #5c5c5c;font-size: 17px;font-family: PingFangSC-light,Heiti SC,San Francisco,Helvetica;word-wrap: break-word;}
        #container{max-width:700px;margin:0 auto;overflow:hidden;white-space:normal;}
        a{color:#c91448;text-decoration:none;word-wrap:break-word;word-break:break-all}
        a:hover{text-decoration:none}
        h1,h2,h3,h4,h5,h6{font-weight:normal;text-align: center}
        pre {white-space: pre-wrap;  word-wrap: break-word;}
        .titleBox span {position:relative;top:-6px;font-size: 12px}
        hr {border:0; border-bottom:0px solid #f4f4f4; margin-top:0.5em;margin-bottom:1em}
        h1 {font-size: 1.9em; font-weight: normal;color:#484848;}
        h2 {font-size: 1.1em; font-weight: normal; margin:1.5em 0 0.5em 0; color:#484848}
        .priceBox {color:#F36700;font-size:2em; padding:0.7em 0px; text-align: center;font-family:Impact, Charcoal, sans-serif}
        .priceBox span {position:relative;top:-1em;font-size: 14px;}
        article {padding:0 1em;margin-top:1.1em;}
        .desImg {width:100%;height:auto}
        .infoBlock {margin-bottom: 1.5em; background: #fff;padding:1.5em 1em;border-radius: 6px;}
        .infoBlock pre {font-size:0.875em; color:#787878;line-height: 2em}
        .content {font-size:0.875em;color:#787878; display: flex; align-items: center; padding:6px 0}
        .content span {color: #fff; background: #f55677;display:table-cell;width:74px;border-radius: 50px; font-size: 12px; margin-right: 10px; text-align: center; display: flex; align-items: center; justify-content: center;padding:5px 0}
        .content i {display:inline-block;flex:1; font-style: normal; line-height: 1.5em}
        #sponsorInfoBlock p {text-align: center;margin-bottom:1em;}
        #sponsorInfoBlock span {color: #F36700; border-style:solid; border-radius:20px; border-color:#F36700; border-width:1px; padding:7px 18px;}
    </style>
</head>
<body>
<div id="container">
    <div><img style="width:100%;height:auto" src="<?php echo $imgUrls[0]?>"/></div>
    <article>
        <header class="titleBox">
            <h1><?php echo $event['title']?></h1>
        </header>
        <p class="priceBox"><?php echo $event['registration_fee']? "<span>$</span>".$event['registration_fee'] : "免费"?></p>
        <section class="infoBlock">
            <p class="content"><span>开始时间</span><i><?php echo date("Y/m/d H:m",$event['event_time'])?></i></p>
            <p class="content"><span>结束时间</span><i><?php echo date("Y/m/d H:m",$event['expiration_time'])?></i></p>
            <?php
            if($event['registration_link']){
                echo '<p class="content"><span>报名方式</span><i><a href="'.$event['registration_link'].'" target="_blank">'. $event['registration_way'].'</a></i></p>';
            }else{
                echo '<p class="content"><span>报名方式</span><i>'. $event['registration_way'].'</i></p>';
            }
            ?>

            <?php
            if($event['location_link']>0){
                echo '<p class="content"><span>活动地点</span><i><a href="'.$event['location_link'].'" target="_blank">'. $event['location'].'</a></i></p>';
            }else{
                echo '<p class="content"><span>活动地点</span><i>'. $event['location'].'</i></p>';
            }
            ?>

            <?php
            if($event['max_participants']>0){
                echo '<p class="content"><span>名额限制</span><i>'. $event['max_participants'].'人</i></p>';
            }
            ?>
        </section>
        <h2>活动联系人</h2>
        <section class="infoBlock">
            <p class="content"><span>联系人</span><i><?php echo $event["sponsor_name"]?:$event["alias"] ?></i></p>
            <?php if ($event['sponsor_telephone']) echo "<p class='content'><span>电话</span><i>{$event['sponsor_telephone']}</i></p>"?>
            <?php if ($event['sponsor_email']) echo "<p class='content'><span>邮箱</span><i> {$event['sponsor_email']}</i></p>"?>
            <?php if ($event['sponsor_wechat']) echo "<p class='content'><span>微信</span><i>{$event['sponsor_wechat']}</i></p>"?>
        </section>
        <h2>活动介绍</h2>
        <section class="infoBlock">
            <p><pre><?php echo $event['description']?></pre></p>
            <?php
            if ($imgUrls[1]) {
                echo '<p><img class="desImg" src="' . $imgUrls[1] . '"/></p>';
            }
            if ($imgUrls[2]) {
                echo '<p><img class="desImg"  src="' . $imgUrls[2] . '"/></p>';
            }
            ?>
        </section>
    </article>
</div>
</body>
</html>
