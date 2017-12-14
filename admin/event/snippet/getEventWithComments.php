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
    <form action="/admin/comment/commentController.php?action=deleteParentComment" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>评论ID</th>
                    <th>父ID</th>
                    <th>头像</th>
                    <th>用户名</th>
                    <th>性别</th>
                    <th>内容</th>
                    <th>时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $comments = $commentModel->getCommentsBySection("event",$event_id);
                $parent = 0;
                foreach ($comments as $comment) {

                    if ($comment["r_id"]==null){
                        unset($l_id);
                        $parent = 0;
                        $sender=$userModel->getProfileOfUserById($comment["l_sender_id"]);
                        ?>
                        <tr>
                            <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $comment['l_id']?>"></td>
                            <td><?php echo $comment['l_id']?></td>
                            <td><?php echo $comment['l_parent_id']?></td>
                            <td><img width="36" height="36" src="<?php echo $sender['img'] ?>"></td>
                            <td><?php echo $sender['name']?></td>
                            <td><?php echo $sender['gender']?></a></td>
                            <td><?php echo $comment['l_comment'] ?></td>
                            <td><?php echo BasicTool::translateTime($comment['l_time']) ?></td>
                            <td><a href="index.php?s=addReply&parent_id=<?php echo $comment["l_id"] ?>&event_id=<?php echo $event_id?>">回复</a></td>
                        </tr>
                    <?php }
                    else if ($parent == 0 && $comment["r_id"] != null ){

                        $parent = 1;
                        $sender = $userModel->getProfileOfUserById($comment["l_sender_id"]);
                        $l_id = $comment["l_id"];

                     ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $comment['l_id']?>"></td>
                        <td><?php echo $comment['l_id']?></td>
                        <td><?php echo $comment['l_parent_id']?></td>
                        <td><img width="36" height="36" src="<?php echo $sender['img'] ?>"></td>
                        <td><?php echo $sender['name']?></td>
                        <td><?php echo $sender['gender']?></a></td>
                        <td><?php echo $comment['l_comment'] ?></td>
                        <td><?php echo BasicTool::translateTime($comment['l_time']) ?></td>
                        <td><a href="index.php?s=addReply&parent_id=<?php echo $comment["l_id"] ?>&event_id=<?php echo $event_id?>">回复</a></td>
                    </tr>
                        <?php

                            $sender = $userModel->getProfileOfUserById($comment["r_sender_id"]);
                        ?>
                    <tr id="comment_<?php echo $comment["r_id"]?>">
                        <td>&nbsp;&nbsp;</td>
                        <td><?php echo $comment['r_id']?></td>
                        <td><?php echo $comment['r_parent_id']?></td>
                        <td><img width="36" height="36" src="<?php echo $sender['img'] ?>"></td>
                        <td><?php echo $sender['name']?></td>
                        <td><?php echo $sender['gender']?></a></td>
                        <td><?php echo $comment['r_comment'] ?></td>
                        <td><?php echo BasicTool::translateTime($comment['r_time']) ?></td>
                        <td><span style="cursor:pointer;" onclick="<?php echo "removeComment({$comment['r_id']})" ?>">删除</span></td>
                    </tr>
                <?php
                }
                else if ($parent == 1 && $comment["r_id"] != null && $comment["l_id"] == $l_id){
                        $sender = $userModel->getProfileOfUserById($comment["r_sender_id"]);
                ?>
                    <tr id="comment_<?php echo $comment["r_id"]?>">
                        <td>&nbsp;&nbsp;</td>
                        <td><?php echo $comment['r_id']?></td>
                        <td><?php echo $comment['r_parent_id']?></td>
                        <td><img width="36" height="36" src="<?php echo $sender['img'] ?>"></td>
                        <td><?php echo $sender['name']?></td>
                        <td><?php echo $sender['gender']?></a></td>
                        <td><?php echo $comment['r_comment'] ?></td>
                        <td><?php echo BasicTool::translateTime($comment['r_time']) ?></td>
                        <td><span style="cursor:pointer;" onclick="<?php echo "removeComment({$comment['r_id']})" ?>">删除</span></td>

                    </tr>
                <?php }
                else if ($parent == 1 && $comment["r_id"] != null && $comment["l_id"] != $l_id){
                    $l_id = $comment["l_id"];
                    $parent = 1;
                    $sender = $userModel->getProfileOfUserById($comment["l_sender_id"]);
                    $l_id = $comment["l_id"];

                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $comment['l_id']?>"></td>
                        <td><?php echo $comment['l_id']?></td>
                        <td><?php echo $comment['l_parent_id']?></td>
                        <td><img width="36" height="36" src="<?php echo $sender['img'] ?>"></td>
                        <td><?php echo $sender['name']?></td>
                        <td><?php echo $sender['gender']?></a></td>
                        <td><?php echo $comment['l_comment'] ?></td>
                        <td><?php echo BasicTool::translateTime($comment['l_time']) ?></td>
                        <td><a href="index.php?s=addReply&parent_id=<?php echo $comment["l_id"] ?>&event_id=<?php echo $event_id?>">回复</a></td>
                    </tr>
                    <?php

                    $sender = $userModel->getProfileOfUserById($comment["r_sender_id"]);
                    ?>
                    <tr id="comment_<?php echo $comment["r_id"]?>">
                        <td>&nbsp;&nbsp;</td>
                        <td><?php echo $comment['r_id']?></td>
                        <td><?php echo $comment['r_parent_id']?></td>
                        <td><img width="36" height="36" src="<?php echo $sender['img'] ?>"></td>
                        <td><?php echo $sender['name']?></td>
                        <td><?php echo $sender['gender']?></a></td>
                        <td><?php echo $comment['r_comment'] ?></td>
                        <td><?php echo BasicTool::translateTime($comment['r_time']) ?></td>
                        <td><span style="cursor:pointer;" onclick="<?php echo "removeComment({$comment['r_id']})" ?>">删除</span></td>
                    </tr>
                    <?php
                }
                }
                ?>
                </tbody>
            </table>
            <?php echo $commentModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
<article class="mainBox">
    <header><h2>添加评论</h2></header>
    <section class="formBox">
        <form action="/admin/comment/commentController.php?action=addComment" method="post">
            <div>
                <input type="hidden" name="parent_id" value="0"/>
                <input type="hidden" name="sender_id" value="<?php echo $userModel->userId?>"/>
                <input type="hidden" name="receiver_id" value="<?php echo $event["sponsor_user_id"]?>"/>
                <input type="hidden" name="section_name" value="event"/>
                <input type="hidden" name="section_id" value="<?php echo $event["id"]?>"/>
                <textarea value="" name="comment" placeholder="输入留言" class="input input-textarea"></textarea>
            </div>
            <div>
                <input type="submit" value="提交" title="提交" class="btn btn-center">
            </div>
        </form>
    </section>
</article>