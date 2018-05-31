<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$eventModel = new \admin\event\EventModel();
$imageModel = new \admin\image\ImageModel();
$userModel = new \admin\user\UserModel();
$event_id = BasicTool::get("event_id");
$event = $eventModel->getEvent($event_id);
$sponsor = $userModel->getProfileOfUserById($event["sponsor_user_id"]);
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
    <section class="titleBlock">
        <h1><?php echo $event["title"] ?></h1>
        <article>
            <table class="infoTable">
                <tr>
                    <th>参与费用：</th>
                    <td><?php echo $event["registration_fee"]?:"免费" ?></td>
                </tr>
                <tr>
                    <th>参与方式：</th>
                    <td><?php echo $event["registration_fee"]?:"免费" ?></td>
                </tr>
                <tr>
                    <th>人数限制：</th>
                    <td><?php echo $event["max_participants"]?:"不限人数"?></td>
                </tr>
                <tr>
                    <th>活动地点：</th>
                    <td><?php echo $event["location_link"]?"<a href='{$event[location_link]}'>{$event[location]}</a>":$event["location"] ?></td>
                </tr>
                <tr>
                    <th>开始时间：</th>
                    <td><?php echo date("Y-m-d H:m",$event["event_time"]) ?></td>
                </tr>
                <tr>
                    <th>结束时间：</th>
                    <td><?php echo date("Y-m-d H:m",$event["expiration_time"]) ?></td>
                </tr>
            </table>
        </article>
    </section>
    <?php if($event["img_id_2"]||$event["img_id_3"]){?>
        <section class="infoBlock">
            <header>
                <div><h2>活动图片</h2><i></i></div>
            </header>
            <article>
                <div class="imgContainer">
                    <?php
                    if ($event["img_id_2"]) {
                        $img2 = $imageModel->getImageById($event["img_id_2"])["url"];
                        echo '<p><img src="' . $img2 . '"/></p>';
                    }
                    if ($event["img_id_3"]) {
                        $img3 = $imageModel->getImageById($event["img_id_3"])["url"];
                        echo '<p><img src="' . $img3 . '"/></p>';
                    }
                    ?>
                </div>
            </article>
        </section>
    <?php }?>
    <section class="infoBlock">
        <header>
            <div><h2>活动简介</h2><i></i></div>
        </header>
        <article>
            <pre><?php echo $event["description"] ?></pre>
        </article>
    </section>
    <section class="infoBlock">
        <header>
            <div><h2>活动负责人</h2><i></i></div>
        </header>
        <article>
            <table class="infoTable">
                <tr>
                    <th>联系人：</th>
                    <td><?php echo $event["sponsor_name"]?:$event["alias"] ?></td>
                </tr>
                <?php echo $event["sponsor_telephone"]!=""?"<tr><th>电 话：</th><td>{$event["sponsor_telephone"]}</td></tr>":null ?>
                <?php echo $event["sponsor_wechat"]!=""?"<tr><th>微 信：</th><td>{$event["sponsor_wechat"]}</td></tr>":null ?>
                <?php echo $event["sponsor_email"]!=""?"<tr><th>Email：</th><td>{$event["sponsor_email"]}</td></tr>":null ?>
            </table>
        </article>
    </section>
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
</body>
</html>