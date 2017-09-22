<?php
$forumModel = new \admin\forum\ForumModel();
$currentUser = new \admin\user\UserModel();
$forum_class_id = BasicTool::get('forum_class_id');
$forum_id = BasicTool::get('forum_id');
$user_id = BasicTool::get('uid');
$flag = $forum_id == null ? 'add' : 'update';
$row = $flag=='add' ? null : $forumModel->getOneRowOfForumById($forum_id);


?>

<header class="topBox">
    <h1> <?php
        echo $pageTitle.'-';
        echo $flag=='add'?'发布新信息':'修改帖子信息';
        ?></h1>
</header>

<article class="mainBox">
    <form action="forumController.php?action=modifyForum" method="post">
        <section class="formBox">
            <input name="flag" value="<?php echo $flag?>" type="hidden">
            <input name="id" value="<?php echo $user_id ?>" type="hidden">
            <input name="forum_id" value="<?php echo $forum_id ?>" type="hidden">
            <input name="time" value="<?php echo $row['time'] ?>" type="hidden">
            <div>
                <label>Forum Class</label>
                <input class="input input-size30" type="text" name="forum_class_id" value="<?php echo $forum_class_id ?>">
            </div>
            <div>
                <label>内容<i>*</i></label>
                <textarea class="input input-textarea" placeholder="" name="content" value=""><?php echo $row['content'] ?></textarea>
            </div>

            <div>
                <label>价格</label>
                <input class="input input-size50" type="text" name="price" value="<?php echo $row['price'] ?>">
            </div>

            <div>
                <label>图片</label>
                <p><img style="width: 100px; height: auto" src="<?php echo $row['img1'] ?>"></p>
                <input class="input  input-size50" type="text" name="img1" value="<?php echo $row['img1'] ?>">
            </div>
            

            <div>
                <label>分类</label>
                <select class="input input-select input-size50 selectDefault" name="category" defvalue="<?php echo $row['category'] ?>" required="required">
                    <option value="buy">求购</option>
                    <option value="sell">出售</option>
                </select>
            </div>

            <div>
                <label>置顶(默认为0)</label>
                <input class="input input-size30" type="text" name="sort" value="<?php echo $row['sort'] ?>">
            </div>
            

        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
