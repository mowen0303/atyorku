<?php
$adModel = new \admin\advertise\adModel();
$userModel = new \admin\user\UserModel();
$aid = BasicTool::get('aid');
$adModel->autoIncreasedReadCounter($aid);

?>
<header class="topBox" xmlns="http://www.w3.org/1999/html">
    <h1><?php echo $pageTitle ?> - 帖子详情 </h1>
</header>
<?php
$arr = $adModel->getListByAdId($aid);
?>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listAdDet">返回列表</a>
</nav>
<article class="mainBox">

    <header><h2>URL</h2></header>
    <section>
        <table class="tab">
            <thead>

            </thead>
            <tbody>
            <?php
            echo $arr['url'];
            ?>
            </tbody>
        </table>
    </section>
    <header><h2>内容</h2></header>
    <section>
        <table class="tab">
            <thead>

            </thead>
            <tbody>
            <?php
            echo $arr['content'];
            ?>
            </tbody>
        </table>

    </section>

</article>
<article class="mainBox">

    <header><h2>图片详情</h2></header>
    <section>
        <table class="tab">
            <thead>

            </thead>
            <tbody>
            <img  src="<?php echo $arr['img']?>">
            </tbody>
        </table>

    </section>

</article>

