<?php
$lectureAlbumModel = new \admin\lectureAlbum\LectureAlbumModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <div style="display: flex; justify-content: flex-end">
        <a class="btn" href="\admin\lectureAlbumCategory\index.php?s=listLectureAlbumCategory">课程专辑分类</a>
    </div>
</nav>
<article class="mainBox">
    <header><h2>课程专辑列表</h2></header>
    <form action="lectureAlbumController.php?action=deleteLectureAlbum" method="post">
        <section>
            <table class="tab" style="table-layout: fixed;">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="80px">顺序</th>
                    <th width="100px">封面</th>
                    <th width="150px">标题</th>
                    <th width="100px">分类</th>
                    <th width="100px">用户</th>
                    <th width="100px">科目</th>
                    <th width="100px">教授</th>
                    <th width="150px">学期</th>
                    <th width="100px">价钱</th>
                    <th width="100px">课程数量</th>
                    <th>描述</th>
                    <th width="100px">有效期至</th>
                    <th width="100px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $lectureAlbumModel->getListOfLectureAlbums();
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><img width="60px" height="auto" src="<?php echo $row['cover_thumbnail_url'] ?>"></td>
                        <td><?php echo $row["title"] ?></td>
                        <td><?php echo $row['album_category'] ?></td>
                        <td><?php echo $row['username'] ?></td>
                        <td><?php echo $row['course'] ?></td>
                        <td><?php echo $row['professor'] ?></td>
                        <td><?php echo $row['term'] . " " . $row['year'] ?></td>
                        <td><?php echo $row['price'] ?></td>
                        <td><?php echo $row['num_lectures'] ?></td>
                        <td><?php echo $row['expiration'] ?></td>
                        <td><?php echo $row['description'] ?></td>
                        <td><a class="btn" href="index.php?s=formLectureAlbum&flag=update<?php echo $argument?>">修改</a></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $lectureAlbumModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
