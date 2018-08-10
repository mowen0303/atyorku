<?php
$videoAlbumTagModel = new \admin\videoAlbumTag\VideoAlbumTagModel();
$imageModel = new \admin\image\ImageModel();
$flag = BasicTool::get("flag");
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?listVideoAlbumTag">返回</a>
</nav>
<article class="mainBox">
    <form action="videoAlbumTagController.php?action=modifyVideoAlbumTag" method="post" enctype="multipart/form-data">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <input name="id" value="<?php echo BasicTool::get('id')?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改视频专辑Tag":"添加视频专辑Tag"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>Tag名称<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo BasicTool::get('title') ?>">
            </div>
            <div>
                <label>Cover图片:</label>
                <div id="currentImages">
                    <?php
                    $img1 = BasicTool::get('cover_img_id');
                    if ($img1) {
                        echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic1' src='{$imageModel->getImageById($img1)["thumbnail_url"]}' style='width: 100px; height: auto;'><input id='img1' name='cover_img_id' value='{$img1}' style='display: none'></div><div><input type='button' id='imgbtn1' value='删除' onclick='removeImg(1);'></div></div>";
                    }
                    ?>
                </div>
                <p><img  id="imgOfUpload" src="<?php echo $row['cover'] ?>" style="width: 100px; height: auto; display: none"></p>
                <input type="file" name="imgFile[]" id="imgFile"/>
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
    <script>
        //删除已上传图片 (点击更新后生效)
        function removeImg(i) {
            var v = $('#img'+i).val();
            if (v) {
                $('#img'+i).attr('value', '');
                $('#pic'+i).attr('src', '').show();
                $('#imgbtn'+i).hide();
            }
        }

        $(function() {

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#imgOfUpload').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#imgFile").change(function(){
                readURL(this);
            });
        })
    </script>
</article>
