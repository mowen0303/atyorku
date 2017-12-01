<?php
$forumModel = new \admin\forum\ForumModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">

    <a class="btn" href="index.php?s=listForumClass">返回论坛</a>
    <a class="btn" href="index.php?s=formForum&forum_class_id=<?php echo BasicTool::get('forum_class_id') ?>">发布</a>
</nav>
<article class="mainBox">
    <header><h2><?php echo BasicTool::get('title')?></h2></header>
    <form action="forumController.php?action=deleteForum" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>头像</th>
                    <th>用户名</th>
                    <th>性别</th>
                    <th>内容</th>
                    <th>图片</th>
                    <th>价格</th>
                    <th>分类</th>
                    <th>评论数</th>
                    <th>赞</th>
                    <th>时间</th>
                    <th>阅读量</th>
                    <th>排序</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>

                <?php
                $arr = $forumModel->getListOfForumByForumClassId(BasicTool::get('forum_class_id'),50);
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                        <td><img width="36" height="36" src="<?php echo $row['img']?>"></td>
                        <td><?php echo $row['alias'] ?></td>
                        <td><?php echo $row['gender'] ?></td>
                        <td><a href="index.php?s=listForumComment&forum_id=<?php echo $row['id']?>&forum_class_id=<?php echo BasicTool::get('forum_class_id') ?>"><?php echo $row['content']?></a></td>
                        <td>
                            <?php
                            $forumModel->echoImage($row[img1]);
                            ?>
                           </td>
                        <td><?php echo $row['price'] ?></td>
                        <td><?php  if($row['category']=="buy"){
                                echo '买';
                            }else{
                                echo '卖';
                            } ?></td>

                        <td><?php echo $row['comment_num'] ?></td>
                        <td><?php echo $row['like'] ?></td>
                        <td><?php echo $row['time'] ?></td>
                        <td><?php echo $row['count_view'] ?></td>
                        <td><?php echo $row['sort'] ?></td>
                        <td><a href="index.php?s=formForum&forum_id=<?php echo $row['id']?>&forum_class_id=<?php echo BasicTool::get('forum_class_id') ?>">修改</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $forumModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
