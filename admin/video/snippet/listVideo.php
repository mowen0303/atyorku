<?php
$videoModel = new \admin\video\VideoModel();
$userModel = new \admin\user\UserModel();
$albumId = BasicTool::get("album_id");
$sectionId = BasicTool::get("id");
$filterReviewStatus = BasicTool::get("review_status");
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <div style="display: flex; justify-content: flex-end">
        <?php
        if (!$albumId || !$sectionId) {
            if ($filterReviewStatus === null) {
                echo '<a class="btn" href="\admin\video\index.php?s=listVideo&review_status=-1">审核失败</a>';
                echo '<a class="btn" href="\admin\video\index.php?s=listVideo&review_status=0">待审核</a>';
                echo '<a class="btn" href="\admin\video\index.php?s=listVideo&review_status=1">审核通过</a>';
                echo '<a class="btn" href="\admin\videoAlbum\index.php?s=listVideoAlbum">返回</a>';
            } else {
                echo '<a class="btn" href="\admin\video\index.php?s=listVideo">返回</a>';
            }
        } else {
            echo '<a class="btn" href="\admin\video\index.php?s=formVideo&flag=add&album_id='.$albumId.'&section_id='.$sectionId.'">添加视频</a>' .
                '<a class="btn" href="\admin\videoSection\index.php?s=listVideoSection&album_id=' . $albumId . ';">返回</a>';
        }
        ?>
    </div>
</nav>
<article class="mainBox">
    <header><h2>视频专辑列表</h2></header>
    <form action="videoController.php?action=deleteVideo" method="post">
        <section>
            <table class="tab" style="table-layout: fixed;">
                <thead>
                <tr>
                    <th width="3%"><input id="cBoxAll" type="checkbox"></th>
                    <th width="5%">顺序</th>
                    <th width="6%">封面</th>
                    <th width="8%">标题</th>
                    <th width="8%">讲师</th>
                    <th width="8%">价钱</th>
                    <th>描述</th>
                    <th width="15%">视频</th>
                    <th width="8%">大小</th>
                    <th width="8%">状态</th>
                    <th width="8%">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $videoModel->getListOfVideoByConditions($albumId, $sectionId, $filterReviewStatus, 30, true);
                foreach ($arr as $row) {
                    $argument = "";
                    foreach($row as $key=>$value) {
                        $argument .= "&{$key}={$value}";
                    }
                    $status = intval($row['review_status']);
                ?>
                    <tr id="video<?php echo $row['id']; ?>">
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"></td>
                        <td><?php echo $row["id"] ?></td>
                        <td><img width="30px" height="auto" src="<?php echo $row['thumbnail_url'] ?>"></td>
                        <td><?php echo $row["title"] ?></td>
                        <td><?php echo $row['instructor_alias'] ?></td>
                        <td><?php echo $row['price'] ?></td>
                        <td><?php echo $row['description'] ?></td>
                        <td><?php echo $row['url'] ?></td>
                        <td><?php echo $row['size'] ?></td>
                        <td><?php echo $status===0 ? '待审核' : ($status===1 ? '通过' : '失败'); ?></td>
                        <td>
                            <a class="btn" href="index.php?s=formVideo&flag=update<?php echo $argument?>">修改</a>
                            <?php
                            if ($filterReviewStatus !== null && $filterReviewStatus == 0) {
                                echo "<a class=\"btn updateBtn\" href=\"#\" data-id=\"{$row['id']}\" data-status=\"1\">Pass</a>";
                                echo "<a class=\"btn updateBtn\" href=\"#\" data-id=\"{$row['id']}\" data-status=\"-1\">Reject</a>";
                            }
                            ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $videoModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>


<script>
    $(document).ready(function() {
        // 注册审核按钮
        $(".updateBtn").click(function (e) {
            var that = this;
            $(this).addClass('isDisabled');
            var videoId = e.target.dataset.id;
            var videoStatus = e.target.dataset.status;
            var statusResponse = "";

            if (videoId && videoStatus) {
                if (videoStatus == -1) {
                    while (!statusResponse) {
                        var res = prompt("审核拒绝理由:", "");
                        if (res == null || res == "") {
                            $(that).removeClass('isDisabled');
                            alert("审核已取消");
                            return;
                        } else {
                            statusResponse = res;
                        }
                    }
                }

                // 发起审核Request
                $.ajax({
                    url: "/admin/video/videoController.php?action=updateReviewStatusWithJson",
                    type: "POST",
                    contentType: "application/x-www-form-urlencoded",
                    data: {"id":videoId,"review_status":videoStatus,"review_response":statusResponse},
                    dataType: "json",
                }).done(function (json) {
                    console.log(json);
                    if (json.code === 1) {
                        alert("审核成功");
                        var td = "#video" + videoId;
                        $(td).remove();
                    }else{
                        alert("审核失败");
                        $(that).removeClass('isDisabled');
                    }
                }).fail(function (data) {
                    console.log(data);
                    alert("审核失败");
                    $(that).removeClass('isDisabled');
                });
            } else {
                alert("缺失视频ID或审核状态");
                $(that).removeClass('isDisabled');
            }
        });
    });
</script>