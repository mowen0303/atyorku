<?php
$bookModel = new \admin\book\BookModel();
$imageModel = new \admin\image\ImageModel();
$bookCategoryModel = new \admin\bookCategory\BookCategoryModel();
$flag = htmlspecialchars(BasicTool::get("flag"));
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
    <a class="btn" href="index.php?listBook">返回</a>
</nav>
<article class="mainBox">
    <form action="bookController.php?action=modifyBook" method="post" enctype="multipart/form-data">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <input name="id" value="<?php echo htmlspecialchars(BasicTool::get('id'))?>" type="hidden">
        <input name="is_available" value="<?php echo htmlspecialchars(BasicTool::get('is_available')?:1)?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改二手书":"添加二手书"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>卖家ID<i>*</i></label>
                <input class="input" name="user_id" value="<?php echo htmlspecialchars(BasicTool::get('user_id'))?>" type="text">
            </div>
            <div>
                <label>标题<i>*</i></label>
                <input class="input" type="text" name="name" value="<?php echo htmlspecialchars(BasicTool::get('name')) ?>">
            </div>
            <div>
                <label>积分付款<input type="checkbox" name="pay_with_points" <?php echo htmlspecialchars(BasicTool::get('pay_with_points') ? "checked" : "") ?>></label>
                <label>电子书<input type="checkbox" name="is_e_document" <?php echo htmlspecialchars(BasicTool::get('is_e_document') ? "checked" : "") ?>></label>
            </div>
            <div>
                <label>电子书链接</label>
                <textarea class="input input-textarea" name="e_link"><?php echo htmlspecialchars(BasicTool::get('e_link')) ?></textarea>
            </div>
            <div>
                <label>所属分类<i>*</i></label>
                <select class="input input-select input-size50 selectDefault" name="book_category_id" defvalue="<?php echo htmlspecialchars(BasicTool::get('book_category_id')) ?>">
                    <?php
                        $arrOfCategory = $bookCategoryModel->getListOfBookCategory(100);
                        foreach($arrOfCategory as $rowOfCategory){
                            echo '<option value="'.htmlspecialchars($rowOfCategory['id']).'">'.htmlspecialchars($rowOfCategory['name']).'</option>';
                        }
                    ?>
                </select>
            </div>
            <div id="courseCodeInputComponent" class="row">
                <div class="col-2">
                    <label>课程类别 (例如:ADMS)<i>*</i></label>
                    <input id="parentInput" class="input" type="text" list="parentCodeList" name="course_code_parent_title" value="<?php echo htmlspecialchars(BasicTool::get('course_code_parent_title'))?>">
                    <datalist id="parentCodeList"></datalist>
                </div>
                <div class="col-2">
                    <label>课程代码 (例如:1000)<i>*</i></label>
                    <input id="childInput" class="input" type="text" list="childCodeCodeList" name="course_code_child_title" value="<?php echo htmlspecialchars(BasicTool::get('course_code_child_title'))?>">
                    <datalist id="childCodeCodeList"></datalist>
                </div>
            </div>
            <div id="professorInputComponent">
                <div>
                    <label>教授</label>
                    <input class="input" type="text" list="professorList" name="prof_name" value="<?php echo htmlspecialchars(BasicTool::get('prof_name')) ?>" />
                    <datalist id="professorList"></datalist>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <label>所修学年</label>
                    <select class="input input-select selectDefault" name="term_year" defvalue="<?php echo htmlspecialchars(BasicTool::get('term_year')) ?>">
                        <?php
                            echo "<option value=''>请选择学年</option>";
                            for($i=date("Y");$i>1959;$i--) echo "<option value='{$i}'>{$i}</option>";
                        ?>
                    </select>
                </div>
                <div class="col-2">
                    <label>所修学期</label>
                    <select class="input input-select selectDefault" name="term_semester" defvalue="<?php echo htmlspecialchars(BasicTool::get('term_semester')) ?>">
                        <option value="">选择学期</option>
                        <option value="Fall">Fall</option>
                        <option value="Winter">Winter</option>
                        <option value="Year">Year</option>
                        <option value="Summer">Summer</option>
                        <option value="Summer 1">Summer 1</option>
                        <option value="Summer 2">Summer 2</option>
                    </select>
                </div>
            </div>
            <div>
                <label>价格<i>*</i></label>
                <input class="input" type="number" name="price" min="0.0" step="0.01" value="<?php echo (float)BasicTool::get('price') ?>">
            </div>
            <div>
                <label>描述</label>
                <textarea class="input input-textarea" name="description"><?php echo htmlspecialchars(BasicTool::get('description')) ?></textarea>
            </div>
            <div>
                <label>二手书图片:</label>
                <div id="currentImages">
                    <?php
                        $bookId = BasicTool::get('id');
                        $img1 = BasicTool::get('image_id_one');
                        $img2 = BasicTool::get('image_id_two');
                        $img3 = BasicTool::get('image_id_three');
                        if ($img1) {
                            echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic1' src='{$imageModel->getImageById($img1)["thumbnail_url"]}' style='width: 100px; height: auto;'><input id='img1' name='image_id_one' value='{$img1}' style='display: none'></div><div><input type='button' id='imgbtn1' value='删除' onclick='removeImg(1);'></div></div>";
                        }
                        if ($img2) {
                            echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic2' src='{$imageModel->getImageById($img2)["thumbnail_url"]}' style='width: 100px; height: auto;'><input id='img2' name='image_id_two' value='{$img2}' style='display: none'></div><div><input type='button' id='imgbtn2' value='删除' onclick='removeImg(2);'></div></div>";
                        }
                        if ($img3) {
                            echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic3' src='{$imageModel->getImageById($img3)["thumbnail_url"]}' style='width: 100px; height: auto;'><input id='img3' name='image_id_three' value='{$img3}' style='display: none'></div><div><input type='button' id='imgbtn3' value='删除' onclick='removeImg(3);'></div></div>";
                        }
                    ?>
                </div>

                <!-- <input class="input input-size50" type="hidden" name="cover" id="cover" value="<?php echo $row['cover'] ?>"> -->
                <p><img  id="imgOfUpload" src="<?php echo $row['cover'] ?>" style="width: 100px; height: auto; display: none"></p>
                <input type="file" name="imgFile[]" id="imgFile" multiple/>
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
