<?php
$videoAlbumModel = new \admin\videoAlbum\VideoAlbumModel();
$institutionModel= new \admin\institution\InstitutionModel();
$imageModel = new \admin\image\ImageModel();
$videoAlbumTagModel = new \admin\videoAlbumTag\VideoAlbumTagModel();
$currentUser = new \admin\user\UserModel();
$flag = BasicTool::get("flag");
$userId = $flag=="add" ? $currentUser->userId : BasicTool::get("user_id");
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
    <a class="btn" href="index.php?listVideoAlbum">返回</a>
</nav>
<article class="mainBox">
    <form action="videoAlbumController.php?action=modifyVideoAlbum" method="post" enctype="multipart/form-data">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <input name="id" value="<?php echo BasicTool::get('id')?>" type="hidden">
        <input name="is_available" value="<?php echo BasicTool::get('is_available')?:1?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改课程专辑":"创建课程专辑"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>用户ID<i>*</i></label>
                <input class="input" name="user_id" value="<?php echo $userId?>" type="text">
            </div>
            <div>
                <label>专辑标题<i>*</i></label>
                <input class="input" name="title" value="<?php echo BasicTool::get('title')?>" type="text">
            </div>
            <div>
                <label>所属分类<i>*</i></label>
                <select class="input input-select input-size50 selectDefault" name="category_id" defvalue="<?php echo BasicTool::get('category_id') ?>">
                    <?php
                    $arrOfCategory = $videoAlbumTagModel->getListOfVideoAlbumTag();
                    foreach($arrOfCategory as $rowOfCategory){
                        echo '<option value="'.$rowOfCategory['id'].'">'.$rowOfCategory['title'].'</option>';
                    }
                    ?>
                </select>
            </div>
            <div>
                <label>学校<i>*</i></label>
                <select class="input input-select input-size50 selectDefault" name="institution_id" defvalue="<?php echo BasicTool::get('institution_id') ?>">
                    <?php
                    $arrOfInstitutions = $institutionModel->getListOfInstitution();
                    foreach($arrOfInstitutions as $rowOfInstitution){
                        echo '<option value="'.$rowOfInstitution['id'].'">'.$rowOfInstitution['title'].'</option>';
                    }
                    ?>
                </select>
            </div>
            <div id="courseCodeInputComponent" class="row">
                <div class="col-2">
                    <label>课程类别 (例如:ADMS)<i>*</i></label>
                    <input id="parentInput" class="input" type="text" list="parentCodeList" name="course_code_parent_title" value="<?php echo BasicTool::get('course_code_parent_title')?>">
                    <datalist id="parentCodeList"></datalist>
                </div>
                <div class="col-2">
                    <label>课程代码 (例如:1000)<i>*</i></label>
                    <input id="childInput" class="input" type="text" list="childCodeCodeList" name="course_code_child_title" value="<?php echo BasicTool::get('course_code_child_title')?>">
                    <datalist id="childCodeCodeList"></datalist>
                </div>
            </div>
            <div id="professorInputComponent">
                <div>
                    <label>教授</label>
                    <input class="input" type="text" list="professorList" name="prof_name" value="<?php echo BasicTool::get('prof_name') ?>" />
                    <datalist id="professorList"></datalist>
                </div>
            </div>
            <div>
                <label>价格<i>*</i></label>
                <input class="input" type="number" name="price" min="0.0" step="0.01" value="<?php echo (float)BasicTool::get('price') ?>">
            </div>
            <div>
                <label>描述<i>*</i></label>
                <textarea class="input input-textarea" name="description"><?php echo BasicTool::get('description') ?></textarea>
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
