<?php
$eventModel = new \admin\event\EventModel();
$userModel = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$eventParticipantModel = new \admin\eventParticipant\EventParticipantModel();

$event_id = BasicTool::get("event_id","event id missing");
$event = $eventModel->getEvent($event_id);
$event_category_id = $event["event_category_id"];
$sponsor =$userModel->getProfileOfUserById($event["sponsor_user_id"]);
?>

<header class="topBox" xmlns="http://www.w3.org/1999/html">
    <h1>活动</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=getEventWithComments&event_id=<?php echo $event_id ?>">查看评论</a>
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
                <td><?php echo date("Y-m-d",$event['publish_time'])?></td>
                <td><?php echo date("Y-m-d",$event['event_time'])?></td>
                <td><?php echo date("Y-m-d",$event['expiration_time'])?></td>
                <td><?php echo $event['count_comments']?></td>
                <td><?php echo $event['count_views']?></td>
            </tr>
            </tbody>
        </table>
    </section>
</article>

<article class="mainBox">
    <header>
        <h2>活动详情</h2>
    </header>
    <?php echo $event["description"] ?>
</article>
<article class="mainBox">
    <header>
        <h2>图片</h2>
    </header>
    <?php
        if($event["img_id_1"]){
            $img_link_1 = $imageModel->getImageById($event["img_id_1"])["url"];
            echo "<img src='{$img_link_1}' height='260' width='390'/>"
        ?>
    <?php }?>
    <?php
        if($event["img_id_2"]){
            $img_link_2 = $imageModel->getImageById($event["img_id_2"])["url"];
            echo "<img src='{$img_link_2}' height='260' width='390'/>"
        ?>
    <?php }?>
    <?php
        if($event["img_id_3"]){
            $img_link_3 = $imageModel->getImageById($event["img_id_3"])["url"];
            echo "<img src='{$img_link_3}' height='260' width='390'/>"
        ?>
    <?php }?>
</article>

<article class="mainBox">
    <header>
        <h2>已参与的用户</h2>
    </header>
    <form action="/admin/eventParticipant/eventParticipantController.php?action=deleteEventParticipant" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>用户ID</th>
                    <th>用户分类ID</th>
                    <th>用户名</th>
                    <th>头像</th>
                    <th>性别</th>
                    <th>专业</th>
                    <th>入学日期</th>
                    <th>参与日期</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $event_participants = $eventParticipantModel->getEventParticipantsByEvent($event_id);
                foreach ($event_participants as $row) {
                    $participant = $userModel->getProfileOfUserById($row["user_id"]);
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                        <td><?php echo $row['id']?></td>
                        <td><?php echo $participant['id']?></td>
                        <td><?php echo $participant["user_class_id"] ?></td>
                        <td><?php echo $participant['name']?></td>
                        <td><img width="36" height="36" src="<?php echo $sponsor['img'] ?>"></td>
                        <td><?php echo $participant['gender']?></a></td>
                        <td><?php echo $participant['major']?></a></td>
                        <td><?php echo $participant['enroll_year']?></a></td>
                        <td><?php echo BasicTool::translateTime($row['register_time']) ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $eventParticipantModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>

<article class="mainBox">
    <header>
        <h2>添加用户</h2>
    </header>
    <form action="/admin/eventParticipant/eventParticipantController.php?action=addEventParticipant" method="post">
        <section class="formBox">
            <input name="event_id" value="<?php echo $event_id?>" type="hidden">
            <div>
                <label>用户ID</label>
                <input type="number" class="input input-size30" type="text" name="user_id">
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn" onclick="return confirm('确认添加吗?')">
        </footer>
    </form>
</article>
