<?php
$bookCategoryModel = new \admin\bookCategory\BookCategoryModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="/admin/book/index.php?listBook">返回</a>
    <a class="btn" href="index.php?s=formBookCategory">添加二手书分类</a>
</nav>
<article class="mainBox">
    <header><h2>二手书分类列表</h2></header>
    <form action="bookCategoryController.php?action=deleteBookCategory" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="80px">顺序</th>
                    <th>标题</th>
                    <th width="100px">二手书数量</th>
                    <th width="100px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $bookCategoryModel->getListOfBookCategory();
                foreach ($arr as $row) {
                    $argument ="&f_id={$row['id']}&f_name={$row['name']}";
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><?php echo $row["name"] ?></td>
                        <td><?php echo $row['books_count'] ?></td>
                        <td><a class="btn" href="index.php?s=formBookCategory&flag=update<?php echo $argument?>">修改</a></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $bookCategoryModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
