<?php
$forumModel = new \admin\forum\ForumModel();
$currentUser = new \admin\user\UserModel();
$forumClassId = BasicTool::get('forum_class_id');
$flag = $forumClassId == null ? 'add' : 'update';
$row = $flag == 'add' ? null : $forumModel->getRowOfForumClassById($forumClassId);
?>

<header class="topBox">
    <h1>
        <?php
        echo $pageTitle.'-';
        echo $flag=='add'?'添加新论坛':'修改论坛';
        ?>
    </h1>
</header>
<article class="mainBox">
    <form action="forumController.php?action=modifyForumClass" method="post">
        <section class="formBox">
            <input name="flag" value="<?php echo $flag?>" type="hidden">
            <input name="forum_class_id" value="<?php echo $forumClassId?>" type="hidden">

                <div>
                    <label>论坛名称</label>
                    <input class="input" type="text" name="title" value="<?php echo $row['title']?>">
                </div>
                <div>
                    <label>排序</label>
                    <input class="input" type="text" name="sort" value="<?php echo $row['sort']?>">
                </div>
                <div>
                    <label>描述</label>
                    <textarea class="input input-textarea" placeholder="" name="description" value=""><?php echo $row['description']?></textarea>
                </div>
                <div>
                    <label>版规</label>
                    <textarea class="input input-textarea" placeholder="" name="regulation" value=""><?php echo $row['regulation']?></textarea>
                </div>

                <div>
                    <label>类型</label>
                    <select class="input input-select input-size50 selectDefault" name="type" defvalue="<?php echo $row['type']?>">
                        <option value="normal" selected="selected">普通型</option>
                        <option value="commercial">交易型</option>
                    </select>
                </div>
                <div>
                    <label>APP图标地址</label>
                    <input class="input" type="text" name="icon" value="<?php echo $row['icon']?>">
                </div>
                <div>
                    <label>状态</label>
                    <select class="input input-select input-size50 selectDefault" name="display" defvalue="<?php echo $row['display']?>">
                        <option value="1" selected="selected">显示</option>
                        <option value="0">隐藏</option>
                    </select>
                </div>
            <div>
                <input class="btn btn-center" type="submit" title="提交" value="提交">
            </div>
        </section>
    </form>
</article>
