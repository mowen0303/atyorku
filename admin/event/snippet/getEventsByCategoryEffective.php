<?php
$eventModel = new \admin\event\EventModel();
$userModel = new \admin\user\UserModel();
$event_category_id = BasicTool::get('event_category_id');
$event_category_title = BasicTool::get("event_category_title");
?>
    <header class="topBox">
        <h1><?php echo $pageTitle?></h1>
    </header>
    <nav class="mainNav">
        <a class="btn" href="/admin/eventCategory/index.php?s=getEventCategories">返回</a>
        <a class="btn" href="/admin/event/index.php?s=getEventsByCategoryIneffective&event_category_id=<?php echo $event_category_id ?>&event_category_title=<?php echo $event_category_title?>">未生效或过期的活动</a>
        <a class="btn" href="index.php?s=addEvent&ad_category_id=<?php echo $event_category_id ?>&event_category_title=<?php echo $event_category_title?>">发布新活动</a>
    </nav>
    <article class="mainBox">
        <header><h2><?php echo $event_category_title?></h2></header>
        <form action="eventController.php?action=deleteEvent" method="post">
            <section>
                <table class="tab">
                    <thead>
                    <tr>
                        <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                        <th>ID</th>
                        <th>封面</th>
                        <th">标题</th>
                        <th>发起人ID</th>
                        <th>活动金额</th>
                        <th>活动名额</th>
                        <th>已参与人数</th>
                        <th>活动投放时间</th>
                        <th>活动时间</th>
                        <th>过期时间</th>
                        <th>评论量</th>
                        <th>阅读量</th>
                        <th width="50px">操作</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $arr = $eventModel->getEventsByCategory($event_category_id,"effective");
                    foreach ($arr as $row) {
                        ?>
                        <tr>
                            <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                            <td><?php echo $row['id']?></td>
                            <td><img width="200" height="100" src="<?php echo $row['poster_url']?>"></td>
                            <td><?php echo $row['title']?></td>
                            <td><?php echo $row['sponsor_user_id']?></a></td>
                            <td><?php echo $row['registration_fee'] ?></td>
                            <td><?php echo $row['max_participant'] ?></td>
                            <td><?php echo $row['count_participant']?></td>
                            <td><?php echo $row['publish_time']?></td>
                            <td><?php echo $row['expiration_time']?></td>
                            <td><?php echo $row['event_time']?></td>
                            <td><?php echo $row['count_comments']?></td>
                            <td><?php echo $row['count_views']?></td>
                            <td><a href="index.php?s=addEvent&id=<?php echo $row['id'] ?>">修改</a></td>
                            <td><a href="/admin/event/index.php?s=getEventsByCategoryEffective&event_category_id=<?php echo $row['id']?>&event_category_title=<?php echo $row["title"] ?>">查看</a></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php echo $eventModel->echoPageList()?>
            </section>
            <footer class="buttonBox">
                <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
            </footer>
        </form>
    </article>
<?php
/**
 * Created by PhpStorm.
 * User: XIN
 * Date: 2017/9/5
 * Time: 3:40
 */