<?php
$flag = BasicTool::get("flag");
$videoAlbumId = BasicTool::get("album_id");
?>

<style>
    .row {
        overflow: hidden;
    }
    .col-2 {
        float:left;
        width: 47%;
    }
    .col-2:nth-child(1) {
        margin-right: 5%;
    }
</style>

<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="\admin\videoSection\index.php?s=listVideoSection&album_id=<?php echo $videoAlbumId; ?>">返回</a>
</nav>
<article class="mainBox">
    <form action="videoSectionController.php?action=modifyVideoSection" method="post">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <input name="id" value="<?php echo BasicTool::get('id')?>" type="hidden">
        <input name="album_id" value="<?php echo $videoAlbumId; ?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改课程章节":"创建课程章节"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>章节标题<i>*</i></label>
                <input class="input" name="title" value="<?php echo BasicTool::get('title')?>" type="text">
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
