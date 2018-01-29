<?php
$taskDesignModel = new \admin\taskDesign\TaskDesignModel();
$imageModel = new \admin\image\ImageModel();
$flag = BasicTool::get("flag");
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
    <a class="btn" href="index.php?listTaskDesign">返回</a>
</nav>
<article class="mainBox">
    <form action="taskDesignController.php?action=modifyTaskDesign" method="post" enctype="multipart/form-data">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <input name="id" value="<?php echo BasicTool::get('id')?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改成就设计":"添加成就设计"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>标题<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo BasicTool::get('title') ?>">
            </div>
            <div>
                <label>学习资料数量</label>
                <input type="number" name="book" min="0" step="1" value="<?php echo ((int)BasicTool::get('book') ?: 0)?>"/>
            </div>
            <div>
                <label>课评数量</label>
                <input type="number" name="course_rating" min="0" step="1" value="<?php echo ((int)BasicTool::get('course_rating') ?: 0)?>"/>
            </div>
            <div>
                <label>问答数量</label>
                <input type="number" name="course_question" min="0" step="1" value="<?php echo ((int)BasicTool::get('course_question') ?: 0)?>"/>
            </div>
            <div>
                <label>同学圈数量</label>
                <input type="number" name="forum" min="0" step="1" value="<?php echo ((int)BasicTool::get('forum') ?: 0)?>"/>
            </div>
<!--            <div>-->
<!--                <label>Knowledge数量</label>-->
<!--                <input type="number" name="knowledge" min="0" step="1" value="--><?php //echo ((int)BasicTool::get('knowledge') ?: 0)?><!--"/>-->
<!--            </div>-->
            <div>
                <label>奖励<i>*</i></label>
                <input type="number" name="bonus" min="0" step="1" value="<?php echo ((int)BasicTool::get('bonus') ?: 0)?>"/>
            </div>

            <div>
                <label>成就设计图片:<i>*</i></label>
                <div id="currentImages">
                    <?php
                    $taskDesignId = BasicTool::get('id');
                    $img1 = BasicTool::get('icon_id');
                    if ($img1) {
                        echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic1' src='{$imageModel->getImageById($img1)["thumbnail_url"]}' style='width: 100px; height: auto;'><input id='img1' name='icon_id' value='{$img1}' style='display: none'></div><div><input type='button' id='imgbtn1' value='删除' onclick='removeImg(1);'></div></div>";
                    }
                    ?>
                </div>

                <p><img  id="imgOfUpload" src="<?php echo $row['icon_url'] ?>" style="width: 100px; height: auto; display: none"></p>
                <input type="file" name="imgFile[]" id="imgFile" />
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
