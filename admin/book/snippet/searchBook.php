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
            <table class="tab" style="table-layout: fixed;">
                <thead>
                <tr>
                    <th width="5%"><input id="cBoxAll" type="checkbox"></th>
                    <th width="5%">顺序</th>
                    <th width="5%">电子版</th>
                    <th width="8%">封面</th>
                    <th width="8%">标题</th>
                    <th width="8%">价钱</th>
                    <th width="12%">描述</th>
                    <th width="14%">链接</th>
                    <th width="6%">类别</th>
                    <th width="6%">科目</th>
                    <th width="8%">卖家</th>
                    <th width="8%">发布时间</th>
                    <th width="8%">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                include 'bookController.php';
                try {
                    $searchType = BasicTool::post("search_type","搜索类别不能为空");
                    $searchValue = BasicTool::post("search_value","搜索内容不能为空");
                    $arr = $bookModel->searchBooks($searchType, $searchValue);
                } catch (Exception $e) {
                    BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
                    return;
                }

                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo htmlspecialchars($row['id']) ?>"></td>
                        <td><?php echo htmlspecialchars($row["id"]) ?></td>
                        <td><?php echo htmlspecialchars($row["is_e_document"]) ?></td>
                        <td><img width="60px" height="auto" src="<?php echo htmlspecialchars($row['thumbnail_url']) ?>"></td>
                        <td><p><?php echo htmlspecialchars($row["name"]) ?></p></td>
                        <td><p><?php echo ($row["pay_with_points"]?"𝓟 ":"$ ") . htmlspecialchars($row['price']) ?></p></td>
                        <td><p><?php echo htmlspecialchars($row['description']?:"") ?></p></td>
                        <td><p><?php echo htmlspecialchars($row['e_link']?:"") ?></p></td>
                        <td><p><?php echo htmlspecialchars($row['book_category_name']) ?></p></td>
                        <td><p><?php echo htmlspecialchars($row['course_code_parent_title'] . $row['course_code_child_title']) ?></p></td>
                        <td><p><?php echo htmlspecialchars($row['alias']) ?></p></td>
                        <td><p><?php echo htmlspecialchars($row['publish_time']) ?></p></td>
                        <td>
                            <a class="btn" href="index.php?s=formBook&flag=update<?php echo $argument?>">修改</a>
                            <a class="btn" href="bookController.php?action=unLaunchBookById&id=<?php echo $row['id']?>">下架</a>
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
