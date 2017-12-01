<?php
$courseModel = new \admin\course\CourseModel();
$arr = $courseModel->getCourseCommentListById(BasicTool::get('c_cid'));
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 课评列表</h1>
</header>
<article class="mainBox">
    <form action="courseController.php?action=addComment" method="post">
        <input name="course_class_id" value="<?php echo BasicTool::get('c_cid')?>" type="hidden">
        <header>
            <h2>留言</h2>
        </header>
        <section>
            <textarea class="input input-textarea" name="comment" placeholder=""></textarea>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
<article class="mainBox">
    <form action="courseController.php?action=delComment&c_cid=<?php echo BasicTool::get('c_cid')?>" method="post">
        <header>
            <h2><?php echo BasicTool::get('f_title').BasicTool::get('c_title');?></h2>
        </header>
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th><input id="cBoxAll" type="checkbox"></th>
                    <th width="50">用户</th>
                    <th>&nbsp;</th>
                    <th>内容</th>
                    <th>时间</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($arr as $row) {
                ?>
                <tr>
                    <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                    <td><em style=" display:inline-block;width: 50px; height: 50px;background-image: url(<?php echo $row['img']?>); background-size: 100% auto; background-position: center; border-radius: 50px"></em></td>
                    <td><?php echo $row['alias']?> &nbsp;&nbsp;&nbsp;&nbsp;(UID: <?php echo $row['user_id']?> )</td>
                    <td><?php echo $row['comment']?></td>
                    <td><?php echo $row['time']?></td>
                </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php $courseModel->echoPageList(); ?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>

<article class="mainBox">
    <h2>调试数据</h2>
    <section>
        <?php
        foreach($arr[0] as $k => $v){
            echo $k." : ".$v."<br>";
        }
        ?>
    </section>

</article>
