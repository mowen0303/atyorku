<?php
$courseCodeModel = new \admin\courseCode\CourseCodeModel();
$imageModel = new \admin\image\ImageModel();
$flag = BasicTool::get("flag");
$parentId = BasicTool::get("parent_id");
if(!$parentId) $parentId=0;
$typeStr = $parentId > 0 ? "子类" : "父类";
?>

<header class="topBox">
    <h1><?php echo ($typeStr . $pageTitle);?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?listCourseCode">返回</a>
</nav>
<article class="mainBox">
    <form action="courseCodeController.php?action=modifyCourseCode" method="post">
        <input name="flag" id="flag" value="<?php echo $flag?>" type="hidden">
        <input name="id" value="<?php echo BasicTool::get('id')?>" type="hidden">
        <input name="parent_id" value="<?php echo $parentId?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改科目":"添加科目"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label><?php echo $typeStr ?>科目标题<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo BasicTool::get('title') ?>">
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>

</article>
