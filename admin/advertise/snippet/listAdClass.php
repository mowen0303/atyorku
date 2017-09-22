<?php
$forumModel = new \admin\forum\ForumModel();
$isAdmin = BasicTool::get('isAdmin');
?>
<header class="topBox">
    <h1><?php echo $pageTitle ?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=formForumClass">添加新论坛</a>
</nav>
<article class="mainBox">
    <header><h2>板块列表</h2></header>
    <form action="forumController.php?action=deleteForumClass" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>名称</th>
                    <th>类型</th>
                    <th>描述</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $forumModel->getListOfForumClass(true);
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>">
                            <input type="hidden" name="isAdmin[]" value="<?php echo $row['is_admin'] ?>">
                        </td>
                        <td>
                            <a href="index.php?s=listForum&forum_class_id=<?php echo $row['id'] ?>&title=<?php echo $row['title'] ?>"><?php echo $row['title'] ?></a>
                        </td>
                        <td><?php echo $row['description'] ?></td>
                        <td><?php echo $row['type'] ?></td>
                        <td><a href="index.php?s=formForumClass&forum_class_id=<?php echo $row['id'] ?>">修改</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php $forumModel->echoPageList(); ?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>