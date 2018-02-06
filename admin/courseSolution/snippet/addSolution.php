<?php
$solutionModel = new \admin\courseSolution\CourseSolutionModel();
$questionModel = new \admin\courseQuestion\CourseQuestionModel();
$userModel = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$question_id = BasicTool::get("question_id");
$question = $questionModel->getQuestionById($question_id);
$solution_id = BasicTool::get("solution_id");
$questioner = $userModel->getProfileOfUserById($question["questioner_user_id"]);
if ($solution_id){
    $flag="更改答案";
    $solution = $solutionModel->getSolutionById($solution_id);
    $img1 = $solution["img_id_1"];
    $img2 = $solution["img_id_2"];
    $img3 = $solution["img_id_3"];
    $form_action = "/admin/courseSolution/courseSolutionController.php?action=updateSolution";
}
else{
    $flag="添加答案";
    $form_action = "/admin/courseSolution/courseSolutionController.php?action=addSolution";
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
        echo $pageTitle.'-'.$flag;
        ?></h1>
</header>

<article class="mainBox">
    <form action="<?php echo $form_action ?>" method="post" enctype="multipart/form-data">
        <section class="formBox">
            <input type="number" name = "question_id" value="<?php echo $question['id']?>" hidden/>
            <input type = "number" name="id" value="<?php echo $solution['id']?>" hidden/>
            <input type="number" name="questioner_user_id" value="<?php echo $question['questioner_user_id']?>" hidden/>
            <h4 style="padding-left:5px;color:#555;">提问ID:&nbsp;<?php echo $question["id"] ?></h4>
            <h4 style="padding-left:5px;color:#555;">提问者:<?php echo $questioner["alias"]?></h4>
            <div>
                <label>答案<i>*</i></label>
                <textarea class="input-textarea" name="description"><?php echo $solution["description"] ?></textarea>
            </div>
            <div>
                <div id="currentImages">
                    <label style="margin-top:1.5rem">图片上传: 最多上传3张</label>
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
                    <p style="margin-bottom:1rem"><img  id="imgOfUpload" style="width: 100px; height: auto; display: none"></p>
                    <input type="file" name="imgFile[]" id="imgFile" multiple/>
                </div>
            </div>

        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>

</article>
