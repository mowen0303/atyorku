<?php
$guideModel = new \admin\guide\GuideModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listGuideClass">返回</a>
    <a class="btn" href="index.php?s=formGuide&guide_class_id=<?php echo BasicTool::get('guide_class_id') ?>">发布新文章</a>
</nav>
<article class="mainBox">
    <header><h2><?php echo BasicTool::get('title')?></h2></header>
    <form action="guideController.php?action=deleteGuide" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>顺序</th>
                    <th>封面</th>
                    <th width="120px">标题</th>
                    <th>简介</th>
                    <th width="80px">作者</th>
                    <th width="40px">阅读</th>
                    <th width="80px">时间</th>
                    <th width="40px">推送</th>
                    <th width="200px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $guideClassId = BasicTool::get('guide_class_id');
                $arr = $guideModel->getListOfGuideByGuideClassId($guideClassId,20,false);
                foreach ($arr as $row) {
                ?>
                    <tr>
                        <input name="classId" type="hidden" value="<?php echo $guideClassId?>">
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                        <td><?php echo $row['guide_order']?></td>
                        <td><a target="_blank" href="/apps/guide/index.php?guide_id=<?php echo $row['id']?>"><div style="width:200px;height:125px;background-image:url('<?php echo $row['cover']?>');background-size:auto 100%;background-repeat:no-repeat;background-position:center"></div></a></td>
                        <td><a target="_blank" href="/apps/guide/index.php?guide_id=<?php echo $row['id']?>"><?php echo $row['title']?></a></td>
                        <td><?php echo $row['introduction']?></a></td>
                        <td><?php echo $row['alias'] ?></td>
                        <td><?php echo $row['view_no'] ?></td>
                        <td><?php echo $row['time']?></td>
                        <td><a target="_blank" class="btn" href="guideController.php?action=pushToAll&guide_id=<?php echo $row['id']?>&title=<?php echo $row['title']?>">推送</a><a target="_blank" class="btn" href="guideController.php?action=pushToAll&silent=silent&guide_id=<?php echo $row['id']?>&title=<?php echo $row['title']?>">静推</a></td>
                        <td><a class="btn" href="guideController.php?action=renewTime&guide_id=<?php echo $row['id']?>">刷新时间戳</a><a class="btn" href="index.php?s=formGuide&guide_id=<?php echo $row['id']?>&guide_class_id=<?php echo BasicTool::get('guide_class_id') ?>">修改</a></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $guideModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
