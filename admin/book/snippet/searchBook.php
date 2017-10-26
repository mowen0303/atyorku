<?php
$bookModel = new \admin\book\BookModel();
$bookCategoryModel = new \admin\bookCategory\BookCategoryModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listBook">返回</a>
</nav>
<article class="mainBox">
    <header><h2>二手书搜索结果</h2></header>
    <form action="bookController.php?action=deleteBook" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="40px">顺序</th>
                    <th width="60px">封面</th>
                    <th width="120px">标题</th>
                    <th width="60px">价钱</th>
                    <th>描述</th>
                    <th width="80px">类别</th>
                    <th width="80px">卖家</th>
                    <th width="80px">发布时间</th>
                    <th width="80px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                include 'bookController.php';

                $arr = searchBooks($bookModel);

                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&f_" . $key . "={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><img width="60px" height="auto" src="<?php echo $row['thumbnail_url'] ?>"></td>
                        <td><?php echo $row["name"] ?></td>
                        <td><?php echo "$" . $row['price'] ?></td>
                        <td><?php echo $row['description'] ?></td>
                        <td><?php echo $row['book_category_name'] ?></td>
                        <td><?php echo $row['user_name'] ?></td>
                        <td><?php echo BasicTool::translateTime($row['publish_time']) ?></td>
                        <td><a class="btn" href="index.php?s=formBook&flag=update<?php echo $argument?>">修改</a></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $bookModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
