<?php
$videoSectionModel = new \admin\videoSection\VideoSectionModel();
$userModel = new \admin\user\UserModel();
$videoAlbumId = BasicTool::get("album_id");
if (!$videoAlbumId) {
    BasicTool::echoMessage("没找到视频专辑ID", $_SERVER['HTTP_REFERER']);
}
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <div style="display: flex; justify-content: flex-end">
        <a class="btn" href="/admin/videoSection/index.php?s=formVideoSection&flag=add&album_id=<?php echo $videoAlbumId; ?>">添加章节</a>
        <a class="btn" href="/admin/videoAlbum/index.php?s=listVideoAlbum">返回</a>
    </div>
</nav>
<article class="mainBox">
    <header><h2>视频章节列表</h2></header>
    <form action="videoSectionController.php?action=deleteVideoSection" method="post">
        <section>
            <table class="tab" style="table-layout: fixed;">
                <thead>
                <tr>
                    <th width="60px"><input id="cBoxAll" type="checkbox"></th>
                    <th width="100px">顺序</th>
                    <th>标题</th>
                    <th width="150px">视频数量</th>
                    <th width="150px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $videoSectionModel->getListOfVideoSectionByVideoAlbumId($videoAlbumId, 20, 0);
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><?php
                            $id = $row["id"];
                            $title = $row["title"];
                            echo "<a href=\"/admin/video/index.php?s=listVideo${argument}\">{$title}</a>";
                            ?>
                        </td>
                        <td><?php echo $row['count_video'] ?></td>
                        <td><a class="btn" href="index.php?s=formVideoSection&flag=update<?php echo $argument?>">修改</a></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $videoSectionModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
