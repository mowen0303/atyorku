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
    <?php
    if($isGod){
        echo "<form action=\"bookController.php?action=emptyAllDeletedBooks\" method=\"post\"><footer class=\"buttonBox\"><input type=\"submit\" value=\"清空回收站\" class=\"btn\" onclick=\"return confirm('确认清空回收站?')\"></footer></form>";
    }
    ?>
    <header><h2>已删除的二手书列表</h2></header>
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
                $arr = $bookModel->getListOfDeletedBooks();
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr id="book<? echo $row['id'] ?>">
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
                        <td><a class="btn restoreBtn" href="#" data-id="<?php echo $row['id']?>">恢复</a></td>
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

<script>
    $(document).ready(function() {
        // 注册恢复按钮
        $(".restoreBtn").click(function (e) {
            var that = this;
            $(this).addClass('isDisabled');
            var $bookId = e.target.dataset.id;
            if ($bookId) {
                $.ajax({
                    url: "/admin/book/bookController.php?action=restoreDeletedBookByIdWithJson",
                    type: "POST",
                    contentType: "application/x-www-form-urlencoded",
                    data: {"book_id":$bookId},
                    dataType: "json",
                }).done(function (json) {
                    console.log(json);
                    if (json.code === 1) {
                        alert("恢复成功");
                        var td = "#book" + $bookId;
                        $(td).remove();
                    }else{
                        alert("恢复失败");
                        $(that).removeClass('isDisabled');
                    }
                }).fail(function (data) {
                    console.log(data);
                    alert("恢复失败");
                    $(that).removeClass('isDisabled');
                });
            } else {
                alert("缺失用户ID或二手书ID");
                $(that).removeClass('isDisabled');
            }
        });
    });
</script>