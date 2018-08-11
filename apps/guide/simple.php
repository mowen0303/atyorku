<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/guide/_header.html";
$guideModel = new \admin\guide\GuideModel();
$guide_id = BasicTool::get('guide_id');
$guideModel->increaseCountNumber($guide_id);
$arr = $guideModel->getRowOfGuideById($guide_id);
?>
<article id="container">
    <div class="articleContainer" style="margin-top: 0;">
        <section class="context">
            <?php echo $arr['content']; ?>
        </section>
    </div>
</article>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/guide/_footer.html";
?>
