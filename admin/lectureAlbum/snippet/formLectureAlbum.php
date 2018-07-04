<?php
$lectureAlbumCategoryModel = new \admin\lectureAlbumCategory\LectureAlbumCategoryModel();
$flag = BasicTool::get("flag");
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?listLectureAlbumCategory">返回</a>
</nav>
<article class="mainBox">
    <form action="lectureAlbumCategoryController.php?action=modifyLectureAlbumCategory" method="post">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <input name="id" value="<?php echo BasicTool::get('id')?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改课程专辑类别":"添加课程专辑类别"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>类别名称<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo BasicTool::get('title') ?>">
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
