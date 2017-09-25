<?php
$bookCategoryModel = new \admin\bookCategory\BookCategoryModel();
$flag = BasicTool::get("flag");
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?listBookCategory">返回</a>
</nav>
<article class="mainBox">
    <form action="bookCategoryController.php?action=modifyBookCategory" method="post">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <input name="f_id" value="<?php echo BasicTool::get('f_id')?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改二手书类别":"添加二手书类别"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>类别名称<i>*</i></label>
                <input class="input" type="text" name="name" value="<?php echo BasicTool::get('f_name') ?>">
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
