<?php
$lectureAlbumCategoryModel = new \admin\lectureAlbumCategory\LectureAlbumCategoryModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="/admin/book/index.php?listLectureAlbum">返回</a>
    <a class="btn" href="index.php?s=formLectureAlbumCategory">添加课程专辑分类</a>
</nav>
<article class="mainBox">
    <header><h2>课程专辑分类列表</h2></header>
    <form action="lectureAlbumCategoryController.php?action=deleteLectureAlbumCategory" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="80px">顺序</th>
                    <th>标题</th>
                    <th width="100px">课程专辑数量</th>
                    <th width="100px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $lectureAlbumCategoryModel->getListOfLectureAlbumCategories();
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><?php echo $row["title"] ?></td>
                        <td><?php echo $row['count'] ?></td>
                        <td><a class="btn" href="index.php?s=formLectureAlbumCategory&flag=update<?php echo $argument?>">修改</a></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $lectureAlbumCategoryModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>