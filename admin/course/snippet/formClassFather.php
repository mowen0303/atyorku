<?php
$courseModel = new \admin\course\CourseModel();
$flag = BasicTool::get("flag");
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 课程大类</h1>
</header>
<nav class="mainNav"></nav>
<article class="mainBox">
    <form action="courseController.php?action=addCourseClassId" method="post">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <input name="f_cid" value="<?php echo BasicTool::get('f_cid')?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改大类":"添加大类"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>课程代码<i>*</i> </label>
                <input class="input" type="text" name="maintitle" placeholder="填一下" value="<?php echo BasicTool::get('f_title') ?>">
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>