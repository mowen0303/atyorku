<?php
$bookModel = new \admin\book\BookModel();
$bookCategoryModel = new \admin\bookCategory\BookCategoryModel();
$userModel = new \admin\user\UserModel();
$isGod = $userModel->isUserHasAuthority("GOD");
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?listBook">返回</a>
</nav>
<article class="mainBox">
    <header><h2>已下架的二手书列表</h2></header>
    <form action="bookController.php?action=deleteBookLogically" method="post">
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
                    <th width="100px">科目</th>
                    <th width="80px">卖家</th>
                    <th width="80px">发布时间</th>
                    <th width="80px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $bookModel->getListOfUnavailableBooks();
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr id="book<? echo $row['id'] ?>">
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo htmlspecialchars($row['id']) ?>"></td>
                        <td><?php echo htmlspecialchars($row["id"]) ?></td>
                        <td><img width="60px" height="auto" src="<?php echo htmlspecialchars($row['thumbnail_url']) ?>"></td>
                        <td><?php echo htmlspecialchars($row["name"]) ?></td>
                        <td><?php echo "$" . htmlspecialchars($row['price']) ?></td>
                        <td><?php echo htmlspecialchars($row['description']) ?></td>
                        <td><?php echo htmlspecialchars($row['book_category_name']) ?></td>
                        <td><?php echo htmlspecialchars($row['course_code_parent_title'] . $row['course_code_child_title']) ?></td>
                        <td><?php echo htmlspecialchars($row['alias']) ?></td>
                        <td><?php echo htmlspecialchars($row['publish_time']) ?></td>
                        <td>
                            <a class="btn" href="index.php?s=formBook&flag=update<?php echo $argument?>">修改</a>
                            <a class="btn" href="bookController.php?action=launchBookById&id=<?php echo $row['id']?>">上架</a>
                            <?php
                            if(intval($row['is_e_document'])){
                                $id = $row['id'];
                                echo '<a class="btn" href="bookController.php?action=getELinkById&id=' . $id . '">链接</a>';
                            }
                            ?>
                        </td>
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