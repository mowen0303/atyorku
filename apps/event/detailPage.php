<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventModel = new \apps\event\event\EventModel();
$imageModel = new \admin\image\ImageModel();
$userModel = new \admin\user\UserModel();
$event_id = BasicTool::get("event_id");
$event = $eventModel->getEvent($event_id);
$eventModel->addClickCount($event_id);
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
    <script>
        window.onload = function(){
            const $status = document.getElementById('time');
            const statusCode = document.getElementById('time').getAttribute('status-code');
            const eventTime = parseInt($status.getAttribute('time'));
            let timeOffset = 0;
            let day = 0;
            let hour = 0;
            let minute = 0;
            let second = 0;
            setInterval(function(){
                if(statusCode == 0){
                    $status.innerHTML = '已结束';
                }else if(statusCode == 1){
                        timeOffset = eventTime  - parseInt(Date.now()/1000);
                        if(timeOffset>0){
                            day = Math.floor(timeOffset/(60*60*24));
                            hour = Math.floor((timeOffset%(60*60*24))/(60*60));
                            minute = Math.floor((timeOffset%(60*60))/60);
                            second = Math.floor((timeOffset%(60)));
                            if(day){
                                $status.innerHTML = `距活动开始还有<br><em><span>${day}</span><i>天</i> <span>${hour}</span><i>时</i><span>${minute}</span><i>分</i><span>${second}</span><i>秒</i><em>`;
                            }else if(hour){
                                $status.innerHTML = `距活动开始还有<br><em> <span>${hour}</span><i>时</i><span>${minute}</span><i>分</i><span>${second}</span><i>秒</i><em>`;
                            }else if(minute){
                                $status.innerHTML = `距活动开始还有<br><em> <span>${minute}</span><i>分</i><span>${second}</span><i>秒</i><em>`;
                            }else if(second){
                                $status.innerHTML = `距活动开始还有<br><em><span>${second}</span><i>秒</i><em>`;
                            }
                        }
                        else{
                            $status.innerHTML = `活动正在进行中`;
                        }
                }else if(statusCode==2){
                    $status.innerHTML = '活动正在进行中';
                }
            },1000)
        }
    </script>
    <style>
        * {margin:0; padding:0}
        *:focus{outline:none}
        body {background: #fafafa; letter-spacing: 0.6px}
        body,pre {color: #5c5c5c;font-size: 17px;font-family: PingFangSC-light,Heiti SC,San Francisco,Helvetica;word-wrap: break-word;}
        #container{max-width:700px;margin:0 auto;overflow:hidden;white-space:normal;}
        a{color:#c91448;text-decoration:none;word-wrap:break-word;word-break:break-all}
        a:hover{text-decoration:none}
        h1,h2,h3,h4,h5,h6{font-weight:normal;text-align: center}
        pre {white-space: pre-wrap;  word-wrap: break-word;}
        .titleBox span {position:relative;top:-6px;font-size: 12px}
        hr {border:0; border-bottom:0px solid #f4f4f4; margin-top:0.5em;margin-bottom:1em}
        h1 {font-size: 1.9em; font-weight: normal;color:#484848;}
        h2 {font-size: 1.1em; font-weight: normal; margin:2em 0 1em 0; color:#484848; }
        article {padding:0 1em;margin-top:1.1em;}
        .desImg {width:100%;height:auto}
        .infoBlock {margin-bottom: 1.5em; background: #fff;padding:1.5em 1em;border-radius: 6px;}
        .infoBlock pre {font-size:0.875em; color:#787878;line-height: 2em}
        .content {font-size:0.875em;color:#787878; display: flex; align-items: center; min-height:42px}
        .content span {color: #fff; background: #f55677;display:table-cell;width:65px;border-radius: 6px; font-size: 12px; margin-right: 10px; text-align: center; display: flex; align-items: center; justify-content: center;padding:5px 0}
        .content i {display:inline-block;flex:1; font-style: normal; line-height: 1.5em}
        #sponsorInfoBlock p {text-align: center;margin-bottom:1em;}
        #sponsorInfoBlock span {color: #F36700; border-style:solid; border-radius:20px; border-color:#F36700; border-width:1px; padding:7px 18px;}
        .detailLink { display: block; width: 80%; margin: 20px auto; text-align: center; background:#c91448; border-radius: 6px; padding:10px; color: #fff}
        .subTitleBox {padding:2em 0; text-align: center}
        .statusBox {display:block; text-align: center; margin-bottom: 2em}
        .statusBox em {display: flex; padding-top:10px;justify-content: center;align-items: center; font-style: normal}
        .statusBox i {font-style: normal;padding:0 4px;font-size: 1em}
        /*.status-0 {color:#fff;}*/
        /*.status-1 {color:#fff;}*/
        /*.status-2 {color:#fff;}*/
        .statusBox span {font-size: 1.5em}
        .priceBox {color:#F36700;font-size:2em;text-align: center;font-family:Impact, Charcoal, sans-serif;margin: 1em 0}
        .priceBox span {position:relative;top:-1em;font-size: 14px;}
        .statusBox span {
            background: #fff;
            border-radius:  6px;
            padding: 10px 8px;
            font-size:  2em;
        }
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
        <p class="statusBox status-<?php echo $event['state_code']?>" id="time" status-code="<?php echo $event['state_code']?>" time="<?php echo $event['event_time']?>">活动状态获取中...</p>
        <section class="infoBlock">
            <p class="content"><span>开始时间</span><i><?php echo date("Y/m/d H:i",$event['event_time'])?></i></p>
            <p class="content"><span>结束时间</span><i><?php echo date("Y/m/d H:i",$event['expiration_time'])?></i></p>
            <?php
            if($event['registration_link']){
                echo '<p class="content"><span>报名方式</span><i><a href="'.$event['registration_link'].'" target="_blank">'. $event['registration_way'].'</a></i></p>';
            }else{
                echo '<p class="content"><span>报名方式</span><i>'. $event['registration_way'].'</i></p>';
            }
            ?>

            <?php
            if($event['location_link']){
                echo '<p class="content"><span>活动地点</span><i><a href="'.$event['location_link'].'" target="_blank">'. $event['location'].' （导航）</a></i></p>';
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
        <h2>活动简介</h2>
        <section class="infoBlock">
            <p><pre id="desc"><?php echo $event['description']?></pre></p>
            <?php
            if ($imgUrls[1]) {
                echo '<p>&nbsp;</p><p><img class="desImg" src="' . $imgUrls[1] . '"/></p>';
            }
            if ($imgUrls[2]) {
                echo '<p>&nbsp;</p><p><img class="desImg"  src="' . $imgUrls[2] . '"/></p>';
            }
            ?>
        </section>
        <section class="infoBlock">
            <p class="content"><span>联系人</span><i><?php echo $event["sponsor_name"]?:$event["alias"] ?></i></p>
            <?php if ($event['sponsor_telephone']) echo "<p class='content'><span>电话</span><i>{$event['sponsor_telephone']}</i></p>"?>
            <?php if ($event['sponsor_email']) echo "<p class='content'><span>邮箱</span><i> {$event['sponsor_email']}</i></p>"?>
            <?php if ($event['sponsor_wechat']) echo "<p class='content'><span>微信</span><i>{$event['sponsor_wechat']}</i></p>"?>
        </section>
        <?php
        if($event['detail_url']){
            echo '<a class="detailLink" href="'.$event['detail_url'].'" target="_blank">更多详情</a>';;
        }
        ?>
    </article>
</div>
</body>
</html>
