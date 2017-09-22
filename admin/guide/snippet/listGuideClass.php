<?php
$guideModel = new \admin\guide\GuideModel();
$isAdmin = BasicTool::get('isAdmin');
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listGuide&guide_class_id=0">查看全部文章</a>
    <a class="btn" href="index.php?s=formGuideClass">添加新分类</a>
</nav>
<article class="mainBox">
    <header><h2>板块列表</h2></header>
    <form action="guideController.php?action=deleteGuideClass" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="50px">顺序</th>
                    <th>图标</th>
                    <th>类别</th>
                    <th>文章数</th>
                    <th>描述</th>
                    <th width="50px">操作</th>
                    <th>显示/隐藏</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $guideModel->getListOfGuideClass();
                foreach ($arr as $row) {
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>">
                         <input type="hidden" name="isAdmin[]" value="<?php echo $row['is_admin'] ?>">
                        </td>
                        <td><?php echo $row['guide_class_order']?></td>
                        <td><img width="36" height="36" src="<?php echo $row['icon']?>"></td>
                        <td><a href="index.php?s=listGuide&guide_class_id=<?php echo $row['id']?>&title=<?php echo $row['title']?>"><?php echo $row['title']?></a> </td>
                        <td><?php echo $row['amount']?></td>
                        <td><?php echo $row['description']?></td>
                        <td><a href="index.php?s=formGuideClass&guide_class_id=<?php echo $row['id'] ?>">修改</a></td>
                        <td>
                            <?php if($row['visible']==0){
                            ?>
                            显示
                            <?php
                            }
                            elseif ($row['visible']==1) {
                            ?>
                            隐藏
                            <?php
                            }
                            ?></td>

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
