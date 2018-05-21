<?php
$knowledgeCategoryModel = new \admin\knowledgeCategory\KnowledgeCategoryModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="/admin/knowledge/index.php">返回</a>
    <a class="btn" href="index.php?s=addKnowledgeCategory">添加分类</a>
</nav>
<article class="mainBox">
    <header><h2>分类</h2></header>
    <form action="knowledgeCategoryController.php?action=deleteKnowledgeCategory" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>类别</th>
                    <th>count</th>
                    <th width="50px">操作</th>
                    <th>&nbsp;&nbsp;&nbsp;&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $knowledgeCategoryModel->getKnowledgeCategories();
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>">
                        <td><?php echo $row["id"] ?></td>
                        <td><?php echo $row["name"] ?></td>
                        <td><?php echo $knowledgeCategoryModel->getKnowledgeCountByCategoryId($row["id"])?></td>
                        <td><a href="index.php?s=addKnowledgeCategory&id=<?php echo $row['id'] ?>">修改</a></td>
                        <td><a href="/admin/knowledge/index.php?s=getKnowledges&knowledge_category_id=<?php echo $row['id']?>&knowledge_category_name=<?php echo $row["name"] ?>">查看</a></td>
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
