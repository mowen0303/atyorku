<?php
$eventCategoryModel = new \admin\eventCategory\EventCategoryModel();
$isAdmin = BasicTool::get('isAdmin');
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=addEventCategory">添加活动分类</a>
</nav>
<article class="mainBox">
    <header><h2>活动分类</h2></header>
    <form action="EventCategoryController.php?action=deleteEventCategory" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>类别</th>
                    <th>描述</th>
                    <th>活动数</th>
                    <th width="50px">操作</th>
                    <th>&nbsp;&nbsp;&nbsp;&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $eventCategoryModel->getEventCategories();
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>">
                        <td><?php echo $row["id"] ?></td>
                        <td><?php echo $row["title"] ?></td>
                        <td><?php echo $row['description']?></td>
                        <td><?php echo $row['count_events']?></td>
                        <td><a href="index.php?s=addEventCategory&id=<?php echo $row['id'] ?>">修改</a></td>
                        <td><a href="/admin/event/index.php?s=getEventsByCategory&event_category_id=<?php echo $row['id']?>&event_category_title=<?php echo $row["title"] ?>&flag=1">查看</a></td>
                     </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
        </form>
</article>
