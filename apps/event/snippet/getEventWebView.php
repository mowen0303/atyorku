<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventModel = new \apps\event\EventModel();
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
    <link rel="stylesheet" href="style.css?2">
    <script src="/resource/js/jquery-2.1.4.js" type="text/javascript"></script>
</head>
<body>
<div id="container">
    <div id = 'slideBox'>
        <?php
        $html = "";
        foreach ($imgUrls as $index => $url){
            $html .= "<div class='slider' id='slider{$index}' style='background-image:url({$url})'></div>";
            }
        echo $html;
        ?>
        <ul id = 'indicatorContainer'>
            <?php
            $html = "";
            foreach ($imgUrls as $index => $url) {
                $html .= "<li class='indicator' id='indicator{$index}'></li>";
            }
            echo $html;
            ?>
        </ul>
    </div>
    <article>
        <header>
            <h1><?php echo $event['title']?></h1>
        </header>
        <section class="infoBlock">
            <div>
                <img class="subtitleIcon" src="/resource/img/event_icon/calendar.png"/>
                <span class="subtitle"><?php echo date("Y/m/d",$event['event_time'])?> - <?php echo date("m/d",$event['expiration_time'])?></span>
            </div>
            <hr/>
            <p class="content">活动费用 : <?php echo $event['registration_fee']?></p>
            <p class="content">参与方式 : <?php echo $event['registration_way'] ?></p>
            <p class="content"><span><?php echo $event['max_participants'] ?></span>个活动名额</p>
            <p class="content">地点位于<?php echo $event['location'] ?></p>
        </section>
        <section id="sponsorInfoBlock" class="infoBlock">
            <div id="profileImg"></div>
            <p><?php echo $event['sponsor_name']?></p>
            <span>活动负责人</span>
        </section>
        <section class="infoBlock">
            <div>
                <img class="subtitleIcon" src="/resource/img/event_icon/event.png"/>
                <span class="subtitle">活动介绍</span>
            </div>
            <hr/>
            <pre><?php echo $event['description']?>></pre>
        </section>
        <section class="infoBlock">
            <div>
                <img class="subtitleIcon" src="/resource/img/event_icon/user.png"/>
                <span class="subtitle">活动联系人</span>
            </div>
            <hr/>
            <p class="content">联系人: <?php echo $event['sponsor_name'] ?></p>
            <?php if ($event['sponsor_telephone']) echo "<p class='content'>电话: {$event['sponsor_telephone']}</p>"?>
            <?php if ($event['sponsor_email']) echo "<p class='content'>邮箱: {$event['sponsor_email']}</p>"?>
            <?php if ($event['sponsor_wechat']) echo "<p class='content'>微信: {$event['sponsor_wechat']}</p>"?>
        </section>
    </article>


    <!--评论组件 S-->
    <!--
    data-category 产品数据库表名
    data-production-id 产品ID（文章、二手书、分享、同学圈）
    data-receiver-id 产品作者ID
    -->
    <script type="text/javascript" src="/admin/resource/js/component.js"></script>
    <div id="commentComponent"
         data-category="event"
         data-production-id="<?php echo $event['id']; ?>"
         data-receiver-id="<?php echo $event['sponsor_user_id']; ?>">
        <header><span>用户评论（<?php echo $event['count_comments']; ?>）</span></header>
        <section id="commentListContainer"></section>
        <section id="loadMoreButton">点击加载更多评论</section>
        <section class="textAreaContainer">
            <textarea name="comment" placeholder="说两句吧..."></textarea>
            <div id="commentButton">评论</div>
        </section>
    </div>
    <!--评论组件 E-->
</div>
<script type="text/javascript">
    let count = $('.slider').length;
    let index = 0;
    let width = $('#slideBox').width();
    $(document).ready(function(){
        if (count){
            $("#slider"+index).width(width);
            if (count ===1)
                $("#indicator"+index).css("display","none");
            else{
                $("#indicator"+index).css("background-color","white");
                setInterval(()=>{
                    $("#slider"+index).animate({width:'0'},150);
                    $("#indicator"+index).css("background-color","rgba(0,0,0,0.4)");
                    index = (index + 1) % count;
                    $("#slider"+index).animate({width:`${width}px`},150);
                    $("#indicator"+index).css("background-color","white");
                }, 5000);
            }
        }
    });
</script>
</body>
</html>
