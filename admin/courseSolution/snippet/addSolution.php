<?php
$solutionModel = new \admin\courseSolution\CourseSolutionModel();
$userModel = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$question_id = BasicTool::get("question_id");
$solution_id = BasicTool::get("solution_id");
if ($solution_id){
    $flag="更改答案";
    $solution = $solutionModel-
    $form_action = "/admin/courseQuestion/courseQuestionController.php?action=updateQuestion";
}
else{
    $flag="添加答案";
    $form_action = "/admin/courseQuestion/courseQuestionController.php?action=addQuestion";
}
?>
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
<script>
    function fileChangeListener() {
        var file = document.getElementById("imgFile").files[0];
        var reader = new FileReader();
        var img = document.getElementById("imgOfUpload");
        reader.onload = function () {
            img.src = reader.result;
        }
        reader.readAsDataURL(file);
    }
</script>

<header class="topBox">
    <h1> <?php
        echo $pageTitle.'-'."发布提问"
        ?></h1>
</header>

<article class="mainBox">
    <form action="/admin/courseQuestion/courseQuestionController.php?action=addQuestion" method="post" enctype="multipart/form-data">
        <input name="course_code_id" value="<?php echo $course_code_id ?>" type="hidden">
        <input name="prof_id" value="<?php echo $prof_id ?>" type="hidden">
        <section class="formBox">
            <h4 style="padding-left:5px;color:#555;">课程:&nbsp;<?php echo $course_code["full_title"] ?></h4>
            <h5 style="padding-left:5px;color:#555;">教授:&nbsp;<?php echo $prof["firstname"]." ".$prof["lastname"] ?></h5>
            <div>
                <label>问题描述<i>*</i></label>
                <textarea class="input-textarea" name="description"></textarea>
            </div>
            <div>
                <label>积分奖励<i>*</i></label>
                <input type="number" class="input input-size30" name="reward_amount"/>
            </div>
            <div>
                <label>图片上传:</label>
                <div id="currentImages">
                    <div id="currentImages">
                        <?php
                        if ($img1) {
                            echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic1' src='{$imageModel->getImageById($img1)["url"]}' style='width: 100px; height: auto;'><input id='img1' name='img_id_1' value='{$img1}' style='display: none'></div><div><input type='button' id='imgbtn1' value='删除' onclick='removeImg(1);'></div></div>";
                        }
                        if ($img2) {
                            echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic2' src='{$imageModel->getImageById($img2)["url"]}' style='width: 100px; height: auto;'><input id='img2' name='img_id_2' value='{$img2}' style='display: none'></div><div><input type='button' id='imgbtn2' value='删除' onclick='removeImg(2);'></div></div>";
                        }
                        if ($img3) {
                            echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic3' src='{$imageModel->getImageById($img3)["url"]}' style='width: 100px; height: auto;'><input id='img3' name='img_id_3' value='{$img3}' style='display: none'></div><div><input type='button' id='imgbtn3' value='删除' onclick='removeImg(3);'></div></div>";
                        }
                        ?>
                    </div>
                    <p><img  id="imgOfUpload" style="width: 100px; height: auto; display: none"></p>
                    <input type="file" name="imgFile[]" id="imgFile" multiple/>
                </div>
            </div>

        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>

</article>