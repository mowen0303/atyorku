<?php
$adCategoryModel = new \admin\adCategory\AdCategoryModel();
$isAdmin = BasicTool::get('isAdmin');
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=addAdCategory">添加新广告分类</a>
</nav>
<article class="mainBox">
    <header><h2>板块列表</h2></header>
    <form action="AdCategoryController.php?action=deleteAdCategory" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>类别</th>
                    <th>大小</th>
                    <th>描述</th>
                    <th>广告数</th>
                    <th width="50px">操作</th>
                    <th>&nbsp;&nbsp;&nbsp;&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $adCategoryModel->getAdCategories();
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>">
                        <td><?php echo $row["title"] ?></td>
                        <td><?php echo $row['size']?></td>
                        <td><?php echo $row['description']?></td>
                        <td><?php echo $row['ads_count']?></td>
                        <td><a href="index.php?s=addAdCategory&id=<?php echo $row['id'] ?>">修改</a></td>
                        <td><a href="/admin/ad/index.php?s=getAdsByCategoryEffective&ad_category_id=<?php echo $row['id']?>&ad_category_title=<?php echo $row["title"] ?>">查看</a></td>
                     </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
        </form>
</article>
