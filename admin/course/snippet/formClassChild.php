<?php
$courseModel = new \admin\course\CourseModel();
$flag = BasicTool::get('flag');
$fatherClassId = BasicTool::get('f_cid');
$childClassId = BasicTool::get('c_cid');
$fatherClassTitle = BasicTool::get('f_title');
$row = $flag=="update"?$courseModel->getRowOfChildClassByChildClassId($childClassId):null;
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 添加一门课程</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="/admin/course/index.php?s=listClassChild&f_cid=<?php echo $fatherClassId?>&f_title=<?php echo $fatherClassTitle?>">返回列表</a>
</nav>
<article class="mainBox">
    <form action="courseController.php?action=addSubClass" method="post">
        <input name="f_cid" value="<?php echo $fatherClassId?>" type="hidden">
        <input name="c_cid" value="<?php echo $childClassId?>" type="hidden">
        <input name="f_title" value="<?php echo $fatherClassTitle?>" type="hidden">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <header>
            <h2>添加一个课程</h2>
        </header>
        <section class="formBox">
            <div>
                <label><?php echo $fatherClassTitle?> 课程编号<i>*</i> </label>
                <input class="input" type="text" name="title" placeholder="例如:1000" value="<?php echo $row['title']?>">
            </div>
            <div>
                <label>课程名称 (来自官方)</label>
                <input class="input" type="text" name="course_name" placeholder="例如:Introduction to Administrative Studies" value="<?php echo $row['course_name']?>">
            </div>
            <div>
                <label>学分 (来自官方)</label>
                <input class="input" type="number" name="credits" placeholder="数字" value="<?php echo $row['credits']?>">
            </div>
            <div>
                <label>课程描述 (来自官方)</label>
                <textarea class="input input-textarea" name="descript" placeholder=""><?php echo $row['descript']?></textarea>
            </div>
            <div>
                <label>学分排除 (来自官方)</label>
                <textarea class="input input-textarea" name="credit_ex" placeholder=""><?php echo $row['credit_ex']?></textarea>
            </div>
            <div>
                <label>前置课程 (来自官方)</label>
                <textarea class="input input-textarea" name="prerequest" placeholder=""><?php echo $row['prerequest']?></textarea>
            </div>
            <div>
                <label>教材名称</label>
                <input class="input" type="text" name="textbook" placeholder="书本名" value="<?php echo $row['textbook']?>">
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>