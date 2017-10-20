<?php
$eventModel = new \admin\event\EventModel();
$userModel = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$commentModel = new \admin\comment\CommentModel();

$event_id = BasicTool::get("event_id","event id missing");
$event = $eventModel->getEvent($event_id);
$event_category_id = $event["event_category_id"];
$sponsor =$userModel->getProfileOfUserById($event["sponsor_user_id"]);
?>

<header class="topBox" xmlns="http://www.w3.org/1999/html">
    <h1>活动</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=getEventWithParticipants&event_id=<?php echo $event_id ?>">已参与的用户</a>
    <a class="btn" href="index.php?s=getEventsByCategory&event_category_id=<?php echo $event_category_id ?>&flag=1">返回列表</a>
</nav>
<article class="mainBox">

    <header><h2><?php echo $event["title"]?></h2></header>
    <section>
        <table class="tab">
            <thead>
            <tr>
                <th>头像</th>
                <th>用户名</th>
                <th>性别</th>
                <th>联系电话</th>
                <th>电子邮箱</th>
                <th>报名金额</th>
                <th>活动名额</th>
                <th>活动地点</th>
                <th>已参与人数</th>
                <th>活动投放时间</th>
                <th>活动时间</th>
                <th>过期时间</th>
                <th>评论量</th>
                <th>阅读量</th>
            </tr>
            </thead>
            <tbody>

            <tr>
                <td><img width="36" height="36" src="<?php echo $sponsor['img'] ?>"></td>
                <td><?php echo $sponsor['name']?></td>
                <td><?php echo $sponsor["gender"]?></td>
                <td><?php echo $event['sponsor_telephone']?></td>
                <td><?php echo $event['sponsor_email']?></a></td>
                <td><?php echo $event['registration_fee'] ?></td>
                <td><?php echo $event['max_participants'] ?></td>
                <td><?php echo $event['location_link'] ?></td>
                <td><?php echo $event['count_participants']?></td>
                <td><?php echo $event['publish_time']?></td>
                <td><?php echo $event['expiration_time']?></td>
                <td><?php echo $event['event_time']?></td>
                <td><?php echo $event['count_comments']?></td>
                <td><?php echo $event['count_views']?></td>
            </tr>
            </tbody>
        </table>
    </section>
</article>