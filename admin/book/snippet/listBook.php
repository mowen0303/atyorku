<?php
$bookModel = new \admin\book\BookModel();
$bookCategoryModel = new \admin\bookCategory\BookCategoryModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listUnavailableBook">下架的二手书</a>
    <a class="btn" href="index.php?s=listDeletedBook">删除的二手书</a>
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
                            <select class="input input-select input-50 selectDefault" name="search_type" defvalue="course">
                                <option value="course">科目</option>
                                <option value="keywords">关键词</option>
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
    <form action="bookController.php?action=deleteBookLogically" method="post">
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
                $arr = $bookModel->getListOfBooks(40,false,true,true);
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><?php echo $row["is_e_document"] ?></td>
                        <td><img width="60px" height="auto" src="<?php echo $row['thumbnail_url'] ?>"></td>
                        <td><p><?php echo $row["name"] ?></p></td>
                        <td><p><?php echo ($row["pay_with_points"]?"𝓟 ":"$ ") . $row['price'] ?></p></td>
                        <td><p><?php echo $row['description']?:"" ?></p></td>
                        <td><p><?php echo $row['e_link']?:"" ?></p></td>
                        <td><p><?php echo $row['book_category_name'] ?></p></td>
                        <td><p><?php echo $row['course_code_parent_title'] . $row['course_code_child_title'] ?></p></td>
                        <td><p><?php echo $row['alias'] ?></p></td>
                        <td><p><?php echo $row['publish_time'] ?></p></td>
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