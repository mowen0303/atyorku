<?php
$videoAlbumModel = new \admin\videoAlbum\VideoAlbumModel();
$userModel = new \admin\user\UserModel();
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <div style="display: flex; justify-content: flex-end">
        <a class="btn" href="/admin/video/index.php?s=listVideo">视频</a>
        <a class="btn" href="/admin/videoAlbum/index.php?s=formVideoAlbum&flag=add">添加视频专辑</a>
        <a class="btn" href="/admin/videoAlbumTag/index.php?s=listVideoAlbumTag">视频专辑分类</a>
    </div>
</nav>
<article class="mainBox">
    <header><h2>视频专辑列表</h2></header>
    <form action="videoAlbumController.php?action=deleteVideoAlbum" method="post">
        <section>
            <table class="tab" style="table-layout: fixed;">
                <thead>
                <tr>
                    <th width="3%"><input id="cBoxAll" type="checkbox"></th>
                    <th width="5%">顺序</th>
                    <th width="6%">封面</th>
                    <th width="8%">标题</th>
                    <th width="8%">分类</th>
                    <th width="8%">用户</th>
                    <th width="6%">科目</th>
                    <th width="8%">教授</th>
                    <th width="8%">价钱</th>
                    <th width="8%">视频数量</th>
                    <th width="6%">参与者</th>
                    <th>描述</th>
                    <th width="8%">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $videoAlbumModel->getListOfVideoAlbum();
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><img width="30px" height="auto" src="<?php echo $row['thumbnail_url'] ?>"></td>
                        <td><?php
                            $id = $row["id"];
                            $title = $row["title"];
                            echo "<a href=\"/admin/videoSection/index.php?s=listVideoSection&album_id={$id}\">{$title}</a>";
                            ?>
                        </td>
                        <td><?php echo $row['video_album_tag_title'] ?></td>
                        <td><?php echo $row['alias'] ?></td>
                        <td><?php echo $row['course_code_parent_title'] + $row['course_code_child_title'] ?></td>
                        <td><?php echo $row['prof_name'] ?></td>
                        <td><?php echo $row['price'] ?></td>
                        <td><?php echo $row['count_video'] ?></td>
                        <td><?php echo $row['count_participants'] ?></td>
                        <td><?php echo $row['description'] ?></td>
                        <td><a class="btn" href="index.php?s=formVideoAlbum&flag=update<?php echo $argument?>">修改</a></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $videoAlbumModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
