<?php
$bookModel = new \admin\book\BookModel();
$bookCategoryModel = new \admin\bookCategory\BookCategoryModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="\admin\bookCategory\index.php?s=listBookCategory">二手书分类</a>
    <a class="btn" href="index.php?s=formBook&flag=add">添加新二手书</a>
</nav>
<article class="mainBox">
    <form action="\admin\book\index.php?s=searchBook" method="post">
        <section>
            <table width="100%">
                <tbody>
                    <tr>
                        <td width="180px">
                            <select class="input input-select input-50 selectDefault" name="search_type" defvalue="keywords">
                                <option value="keywords">二手书信息</option>
                                <option value="user_id">用户ID</option>
                                <option value="username">用户名</option>
                                <option value="book_category_id">二手书分类ID</option>
                            </select>
                        </td>
                        <td>
                            <input class="input" type="text" name="search_value" placeholder="输入对应搜索信息" style="margin-left:16px;" />
                        </td>
                        <td width="100px">
                            <a><input type="submit" value="搜索" class="btn" style="float:right;"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </form>
    <header><h2>二手书列表</h2></header>
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
                    <th width="100px">科目</th>
                    <th width="80px">卖家</th>
                    <th width="80px">发布时间</th>
                    <th width="80px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $bookModel->getListOfBooks();
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
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
                        <td><?php echo $row['course_code_parent_title'] . $row['course_code_child_title'] ?></td>
                        <td><?php echo $row['alias'] ?></td>
                        <td><?php echo $row['publish_time'] ?></td>
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
