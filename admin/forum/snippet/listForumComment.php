<?php
$forumModel = new \admin\forum\ForumModel();
$currentUser = new \admin\user\UserModel();
?>
<header class="topBox" xmlns="http://www.w3.org/1999/html">
    <h1><?php echo $pageTitle ?> - 帖子详情 </h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listForum&forum_class_id=<?php echo BasicTool::get('forum_class_id') ?>">返回列表</a>
</nav>
<article class="mainBox">

    <header><h2>帖子详情</h2></header>
    <section>
        <table class="tab">
            <thead>
            <tr>
                <th>头像</th>
                <th>用户名</th>
                <th>性别</th>
                <th>内容</th>
                <th>图片</th>
                <th>价格</th>
                <th>评论数</th>
                <th>赞</th>
                <th>时间</th>
                <th>阅读量</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $arr = $forumModel->getOneRowOfForumById(BasicTool::get('forum_id'));
            ?>
            <tr>
                <td><img width="36" height="36" src="<?php echo $arr['img'] ?>"></td>
                <td><?php echo $arr['alias'] ?></td>
                <td><?php echo $arr['gender'] ?></td>
                <td><?php echo $arr['content'] ?></td>
                <td>
                    <?php
                    $forumModel->echoImage($arr[img1]);
                    $forumModel->echoImage($arr[img2]);
                    $forumModel->echoImage($arr[img3]);
                    $forumModel->echoImage($arr[img4]);
                    $forumModel->echoImage($arr[img5]);
                    $forumModel->echoImage($arr[img6]);
                    ?>
                </td>
                <td><?php echo $arr['price'] ?></td>
                <td><?php echo $arr['comment_num'] ?></td>
                <td><?php echo $arr['like'] ?><a href="/admin/forum/forumController.php?action=addLike&id=<?php echo $arr['id'] ?>">赞</a></td>
                <td><?php echo $arr['time'] ?></td>
                <td><?php echo $arr['count_view'] ?></td>
            </tr>
            </tbody>
        </table>
    </section>
</article>

<article class="mainBox">
    <header>
        <h2>用户评论</h2>
    </header>
    <section>
        <table class="tab">
            <thead>
            <tr>
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
            $arr = $forumModel->getListOfForumCommentByForumId(BasicTool::get('forum_id'));
            foreach ($arr as $row) {
            ?>
                <tr>
                    <td><img width="36" height="36" src="<?php echo $row['img'] ?>"></td>
                    <td><?php echo $row['alias'] ?></td>
                    <td><?php echo $row['gender'] ?></td>
                    <td><?php echo $row['content_comment'] ?></td>
                    <td><?php echo $row['time'] ?></td>
                    <td>
                        <?php
                        if ($row['user_id'] == $currentUser->userId || $currentUser->isUserHasAuthority('forum')) {
                        ?>
                            <a href="/admin/forum/forumController.php?action=deleteComment&id=<?php echo $row['id']?>&forum_id=<?php echo BasicTool::get('forum_id') ?>">删除</a>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </section>
</article>

<article class="mainBox">
    <header><h2>添加评论</h2></header>
    <section class="formBox">
        <form action="forumController.php?action=addComment" method="post">
            <div>
                <input type="hidden" name="forum_id" value="<?php echo BasicTool::get('forum_id') ?>">
                <input type="hidden" name="forum_class_id" value="<?php echo BasicTool::get('forum_class_id') ?>">
                <textarea value="" name="content_comment" placeholder="输入留言" class="input input-textarea"></textarea>
            </div>
            <div>
                <input type="submit" value="提交" title="提交" class="btn btn-center">
            </div>
        </form>
    </section>
</article>
