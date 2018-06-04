<?php
$eventModel = new \apps\event\EventModel();
$userModel = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$commentModel = new \admin\comment\CommentModel();

$event_id = BasicTool::get("event_id","event id missing");
$event = $eventModel->getEvent($event_id);
$event_category_id = $event["event_category_id"];
$sponsor =$userModel->getProfileOfUserById($event["sponsor_user_id"]);
?>

<script>
    function removeComment(id){
        $.ajax(
            {url:"/admin/comment/commentController.php?action=deleteChildCommentWithJson",
                type:"POST",
                contentType:"application/x-www-form-urlencoded",
                data:{
                    id:id
                },
                success:function(res){
                    if (res){
                        var dom_id = "comment_"+id;
                        $("#"+dom_id).remove();
                    }
                }
            }
    );
    }
</script>

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
    <header><h2>用户评论</h2></header>
    <!--评论组件 S-->
    <!--
    data-category 产品数据库表名
    data-production-id 产品ID（文章、二手书、分享、同学圈）
    data-receiver-id 产品作者ID
    -->
    <div id="commentComponent"
         data-category="event"
         data-production-id="<?php echo $event_id?>"
         data-receiver-id="<?php echo $event["sponsor_user_id"]?>"
         style="background: transparent">
        <section class="textAreaContainer">
            <textarea name="comment" placeholder="说两句吧..."></textarea>
            <div id="commentButton">评论</div>
        </section>
        <section id="commentListContainer"></section>
        <section id="loadMoreButton">点击加载更多</section>
    </div>
    <!--评论组件 E-->
</article>
