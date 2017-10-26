<?php
$commentModel = new \admin\comment\CommentModel();
$userModel = new \admin\user\UserModel();
$parent_id = BasicTool::get("parent_id");
$comment = $commentModel->getComment($parent_id);
$sender=$userModel->getProfileOfUserById($comment["sender_id"]);
?>

<article class="mainBox">
    <header><h2>即将回复的评论</h2></header>
    <section class="formBox">
        <table class="tab">
            <thead>
            <tr>
                <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                <th>ID</th>
                <th>头像</th>
                <th>用户名</th>
                <th>性别</th>
                <th>内容</th>
                <th>时间</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $comment['l_id']?>"></td>
                <td><?php echo $comment['id']?></td>
                <td><img width="36" height="36" src="<?php echo $sender['img'] ?>"></td>
                <td><?php echo $sender['name']?></td>
                <td><?php echo $sender['gender']?></a></td>
                <td><?php echo $comment['comment'] ?></td>
                <td><?php echo $comment['time'] ?></td>
            </tr>
            </tbody>
        </table>
    </section>
</article>


<article class="mainBox">
    <header><h2>添加回复</h2></header>
    <section class="formBox">
        <form action="/admin/comment/commentController.php?action=addComment" method="post">
            <div>
                <input type="hidden" name="parent_id" value="<?php echo $parent_id ?>"/>
                <input type="hidden" name="sender_id" value="<?php echo $userModel->userId?>"/>
                <input type="hidden" name="receiver_id" value="<?php echo $comment["sender_id"]?>"/>
                <input type="hidden" name="section_name" value="event"/>
                <input type="hidden" name="section_id" value="<?php echo $comment["section_id"]?>"/>
                <textarea value="" name="comment" placeholder="输入留言" class="input input-textarea"></textarea>
            </div>
            <div>
                <input type="submit" value="提交" title="提交" class="btn btn-center">
            </div>
        </form>
    </section>
</article>