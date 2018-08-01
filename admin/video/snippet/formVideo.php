<?php
$imageModel = new \admin\image\ImageModel();
$currentUser = new \admin\user\UserModel();
$flag = BasicTool::get("flag");
$id = BasicTool::get("id");
$sectionId = BasicTool::get("section_id");
$albumId = BasicTool::get("album_id");
?>

<style>
    .row {
        overflow: hidden;
    }
    .col-2 {
        float:left;
        width: 47%;
    }
    .col-2:nth-child(1) {
        margin-right: 5%;
    }
</style>

<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="/admin/video/index.php?s=listVideo&album_id=<?php echo $albumId . '&id=' . $id ?>">返回</a>
</nav>
<article class="mainBox">
    <form action="videoController.php?action=modifyVideo" method="post" enctype="multipart/form-data">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <input name="id" value="<?php echo $id; ?>" type="hidden">
        <input name="is_available" value="<?php echo BasicTool::get('is_available')?:1?>" type="hidden">
        <input name="section_id" value="<?php echo $sectionId; ?>" type="hidden">
        <input name="album_id" value="<?php echo $albumId; ?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改视频":"创建视频"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>视频标题<i>*</i></label>
                <input class="input" name="title" value="<?php echo BasicTool::get('title')?>" type="text">
            </div>
            <div>
                <label>导师<i>*</i></label>
                <input class="input" name="instructor_id" value="<?php echo BasicTool::get('instructor_id'); ?>" type="text">
            </div>
            <div>
                <label>价格<i>*</i></label>
                <input class="input" type="number" name="price" min="0.0" step="0.01" value="<?php echo (float)BasicTool::get('price') ?>">
            </div>
            <div>
                <label>描述</label>
                <textarea class="input input-textarea" name="description"><?php echo BasicTool::get('description') ?></textarea>
            </div>
            <div>
                <label>视频URL<i>*</i></label>
                <input class="input" name="url" value="<?php echo BasicTool::get("url"); ?>" type="text">
            </div>
            <div>
                <label>视频大小(bytes)<i>*</i></label>
                <input class="input" type="number" name="size" min="0" step="1" value="<?php echo (int)BasicTool::get('size') ?>">
            </div>
            <div>
                <label>视频长度(second)<i>*</i></label>
                <input class="input" type="number" name="length" min="0" step="1" value="<?php echo (float)BasicTool::get('length') ?>">
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
