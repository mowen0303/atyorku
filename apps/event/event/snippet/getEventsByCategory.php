<?php
$eventModel = new \apps\event\event\EventModel();
$userModel = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$eventCategoryModel = new \apps\event\eventCategory\EventCategoryModel();
$event_category_id = BasicTool::get('event_category_id');
$event_category_title = $event_category_id ? $eventCategoryModel->getEventCategory($event_category_id)['title'] : "";
$isAdmin = $userModel->isUserHasAuthority('ADMIN');

$arr = $isAdmin?$eventModel->getEventsByCategory($event_category_id):$eventModel->getEventsByCategory($event_category_id,false,false);

?>
    <header class="topBox">
        <h1><?php echo $pageTitle?></h1>
    </header>
    <nav class="mainNav">
        <a class="btn" href="/admin/login/loginController.php?action=logout&url=/index.html">注销</a>
        <?php if ($isAdmin) echo '<a class="btn" href="./../eventCategory/index.php?s=getEventCategories">分类管理</a>';?>
        <a class="btn" href="index.php?s=addEvent">发布新活动</a>

    </nav>
    <article class="mainBox">
        <header><h2>活动列表 <?php echo $event_category_title?></h2></header>
        <form action="eventController.php?action=deleteEvent" method="post">
            <section>
                <table class="tab">
                    <thead>
                    <tr>
                        <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                        <th>ID</th>
                        <th width="230">封面</th>
                        <th>活动信息</th>
                        <?php
                        if ($isAdmin)
                            echo "<th>展示次数</th><th>点击量</th>";
                        ?>
                        <th>人数限制</th>
                        <?php
                        if ($userModel->isAdmin)
                            echo "<th>顺序</th>";
                        ?>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($arr as $row) {
                        $banner_url = $imageModel->getImageById($row["img_id_1"])["url"];
                        ?>
                        <tr>
                            <?php
                            if ($isAdmin || $userModel->userId == $row["sponsor_user_id"])
                                echo "<td><input type='checkbox' class='cBox' name='id[]' value='{$row["id"]}'></td>";
                            else
                                echo "<td> </td>";
                            ?>
                            <td><?php echo $row['id']?></td>

                            <td><a href="/apps/event/detailPage.php?event_id=<?php echo $row['id'] ?>" target="_blank"><img width="200" height="100" src="<?php echo $banner_url?>"></a></td>
                            <td>
                                <p><b><a href="/apps/event/detailPage.php?event_id=<?php echo $row['id'] ?>" target="_blank"><?php echo $row['title']?></a></b></p>
                                活动类别：<?php echo $eventCategoryModel->getEventCategory($row['event_category_id'])['title'] ?></br>
                                报名费：<?php echo $row['registration_fee'] ?> &nbsp;&nbsp;&nbsp;&nbsp;报名方式：<?php echo $row['registration_way'] ?>&nbsp;&nbsp;&nbsp;&nbsp;发起人：<?php echo $userModel->getProfileOfUserById($row['sponsor_user_id'])["alias"]?></br>
                                <?php echo date("Y-m-d H:i",$row['event_time'])?> 至 <?php echo date("Y-m-d H:i",$row['expiration_time'])?></br>
                            </td>
                            <?php
                            if ($userModel->isUserHasAuthority("ADMIN")){
                                echo "<td>{$row[count_exhibits]}</td><td>{$row[count_clicks]}</td>";

                            }
                            ?>

                            <td><?php echo $row['max_participants']?:'' ?></td>
                            <?php
                            if ($userModel->isAdmin)
                                echo "<td>{$row['sort']}</td>";
                            ?>
                            <td>
                                <?php
                                if ($isAdmin || $userModel->userId == $row["sponsor_user_id"])
                                    echo "<a href='index.php?s=addEvent&id={$row['id']}'>编辑</a>";
                                ?>
                            </td>
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
