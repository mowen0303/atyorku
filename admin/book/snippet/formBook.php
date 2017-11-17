<?php
$bookModel = new \admin\book\BookModel();
$imageModel = new \admin\image\ImageModel();
$bookCategoryModel = new \admin\bookCategory\BookCategoryModel();
$flag = BasicTool::get("flag");
?>

<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?listBook">返回</a>
</nav>
<article class="mainBox">
    <form action="bookController.php?action=modifyBook" method="post" enctype="multipart/form-data">
        <input name="flag" value="<?php echo $flag?>" type="hidden">
        <input name="id" value="<?php echo BasicTool::get('id')?>" type="hidden">
        <header>
            <h2><?php echo $flag=="update"?"修改二手书":"添加二手书"; ?></h2>
        </header>
        <section class="formBox">
            <div>
                <label>标题<i>*</i></label>
                <input class="input" type="text" name="name" value="<?php echo BasicTool::get('name') ?>">
            </div>
            <div>
                <label>所属分类<i>*</i></label>
                <select class="input input-select input-size50 selectDefault" name="book_category_id" defvalue="<?php echo BasicTool::get('book_category_id') ?>">
                    <?php
                        $arrOfCategory = $bookCategoryModel->getListOfBookCategory(100);
                        foreach($arrOfCategory as $rowOfCategory){
                            echo '<option value="'.$rowOfCategory['id'].'">'.$rowOfCategory['name'].'</option>';
                        }
                    ?>
                </select>
            </div>
            <div id="courseCodeDiv" data-parent-id="<?php echo BasicTool::get('course_code_parent_id') ?>" data-child-id="<?php echo BasicTool::get('course_code_child_id') ?>"></div>
            <div>
                <label>价格<i>*</i></label>
                <input class="input" type="number" name="price" min="0.0" step="0.01" value="<?php echo (float)BasicTool::get('price') ?>">
            </div>
            <div>
                <label>描述</label>
                <textarea class="input input-textarea" name="description"><?php echo BasicTool::get('description') ?></textarea>
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
