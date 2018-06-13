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
    /*格式化*/
    * {margin:0; padding:0}
    *:focus{outline:none}
    body {background: white; letter-spacing: 0.75px}
    body,pre {color: #5c5c5c;font-size: 16px;line-height: 30px;font-family: PingFangSC-light,Heiti SC,San Francisco,Helvetica;word-wrap: break-word;}
    #container{max-width:700px;margin:0 auto;overflow:hidden;white-space:normal;}
    a{color:#c91448;text-decoration:none;word-wrap:break-word;word-break:break-all}
    a:hover{text-decoration:none}
    h1,h2,h3,h4,h5,h6{font-weight:normal}
    pre {white-space: pre-wrap;  word-wrap: break-word;}

    .titleBox {display: flex}
        .titleBox h1 {flex:1}
        .titleBox .price {color:#F36700;font-size:20px; padding:10px 20}
        .titleBox span {position:relative;top:-6px;font-size: 12px}

    hr {border:0; border-bottom:1px dashed #f4f4f4; margin-top:0.5em;margin-bottom:1em}
    h1 {font-size: 19px; font-weight: 600; margin-bottom: 0.9em; color:#484848}
    h2 {font-size: 16px; font-weight: 600; margin-bottom: 0.9em; color:#484848; margin-top: 3em}
    article {padding:0 1em;margin-top:1.1em;}
    #slideBox {width:100%; box-sizing: border-box; position:relative}
    .slider {box-sizing: border-box; max-width:700px; display:inline-block; padding-top:50%; background-size:cover; background-position:center; background-repeat:no-repeat;}
    #indicatorContainer { display: block; width:100%; text-align:center; list-style-type: none; position:absolute; bottom:10%}
    .indicator {display:inline-block; padding:0.3em 0.3em; border-radius:0.3em; margin-right:0.3em; background-color:rgba(0,0,0,0.4)}

    .desImg {width:100%;height:auto}

    .infoBlock {margin-bottom: 1.5em;}
    .infoBlock pre {font-size:0.875em; color:#787878}
    .subtitle {font-size:13px;color:#585858; font-family: "PingFang SC", "SF UI Text", "Helvetica Neue", "Luxi Sans", "DejaVu Sans", Tahoma, STHeiti !important;}
    .subtitleIcon {position:relative;height:13px;width:13px;margin-right:0.3em;top:2px;}
    .content {font-size:0.875em;color:#787878}
    .content span {color: #fff; background: #ccc; padding:4px 7px;border-radius: 6px; font-size: 12px; margin-right: 10px}

    #sponsorInfoBlock {position:relative; text-align:center;}
    #sponsorInfoBlock p {text-align: center;margin-bottom:1em;}
    #sponsorInfoBlock span {color: #F36700; border-style:solid; border-radius:20px; border-color:#F36700; border-width:1px; padding:7px 18px;}
    #profileImg{height:3.75em;width:3.75em; margin-bottom:1em;border-radius:3.75em; background:url("/resource/img/head-default.png") no-repeat center; background-size:cover; position:relative; left:50%; transform: translate3d(-50%,0,0)}
    </style>
</head>
<body>
<div id="container">
    <div><img style="width:100%;height:auto" src="<?php echo $imgUrls[0]?>"/></div>
    <article>
        <header class="titleBox">
            <h1><?php echo $event['title']?></h1>
            <p class="price"><?php echo $event['registration_fee']? "<span>$</span>".$event['registration_fee'] : "免费"?></p>
        </header>
        <section class="infoBlock">

            <p class="content"><span>开始时间</span><?php echo date("Y/m/d H:m",$event['event_time'])?></p>
            <hr/>
            <p class="content"><span>结束时间</span><?php echo date("Y/m/d H:m",$event['expiration_time'])?></p>
            <hr/>
            <?php
                if($event['registration_link']){
                    echo '<p class="content"><span>报名方式</span><a href="'.$event['registration_link'].'" target="_blank">'. $event['registration_way'].'</a></p>';
                }else{
                    echo '<p class="content"><span>报名方式</span>'. $event['registration_way'].'</p>';
                }
            ?>
            <hr/>
            <?php
                if($event['location_link']){
                    echo '<p class="content"><span>活动地点</span><a href="'.$event['location_link'].'" target="_blank">'. $event['location'].'</a></p>';
                }else{
                    echo '<p class="content"><span>活动地点</span>'. $event['location'].'</p>';
                }
            ?>
            <hr/>
            <?php
                if($event['max_participants']>0){
                    echo '<p class="content"><span>名额限制</span>'. $event['max_participants'].'个人</p><hr/>';
                }
            ?>

        </section>
        <section class="infoBlock">
            <h2>活动介绍</h2>
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
        <section class="infoBlock">
            <h2>活动联系人</h2>
            <p class="content"><span>联系人</span><?php echo $event["sponsor_name"]?:$event["alias"] ?></p><hr/>
            <?php if ($event['sponsor_telephone']) echo "<p class='content'><span>电话</span>{$event['sponsor_telephone']}</p><hr/>"?>
            <?php if ($event['sponsor_email']) echo "<p class='content'><span>邮箱</span> {$event['sponsor_email']}</p><hr/>"?>
            <?php if ($event['sponsor_wechat']) echo "<p class='content'><span>微信</span>{$event['sponsor_wechat']}</p><hr/>"?>
        </section>
    </article>
    <!--评论组件 S-->
    <!--
    data-category 产品数据库表名
    data-production-id 产品ID（文章、二手书、分享、同学圈）
    data-receiver-id 产品作者ID
    -->
    <!-- <script type="text/javascript" src="/admin/resource/js/component.js"></script>
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
    </div> -->
    <!--评论组件 E-->
</div>
</body>
</html>
