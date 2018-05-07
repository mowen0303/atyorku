<?php
$knowledgeModel = new \admin\knowledge\KnowledgeModel();
$currentUser = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$knowledgeCategoryModel = new \admin\knowledgeCategory\KnowledgeCategoryModel();

$knowledge_id = BasicTool::get('id');
$flag = !$knowledge_id ? "add" : "update";
$knowledge_categories = $knowledgeCategoryModel->getKnowledgeCategories();
if ($flag == 'add') {
    $row = null;
    $seller_user_id = $currentUser->userId;
    $form_action = "/admin/knowledge/knowledgeController.php?action=addKnowledge";
} else {
    $row = $knowledgeModel->getKnowledgeById($knowledge_id);
    $img_id = $row['img_id'];
    $form_action = "/admin/knowledge/knowledgeController.php?action=updateKnowledge";
}

?>
<script>
    //删除已上传图片 (点击更新后生效)
    function removeImg(i) {
        var v = $('#img' + i).val();
        if (v) {
            $('#img' + i).attr('value', '');
            $('#pic' + i).attr('src', '').show();
            $('#imgbtn' + i).hide();
        }
    }

    $(function () {
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#imgOfUpload').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#imgFile").change(function () {
            readURL(this);
        });
    })
</script>

<header class="topBox">
    <h1> <?php
        echo "考试回忆录" . '-';
        echo $flag == 'add' ? '添加回忆录(图片)': '更改回忆录(图片)';
        ?></h1>
</header>

<article class="mainBox">
    <form action="<?php echo $form_action ?>" method="post" enctype="multipart/form-data">
        <input name="id" value="<?php echo $knowledge_id ?>" type="hidden"/>
        <section class="formBox">
            <div id="courseCodeInputComponent">
                <div style="float:left;width:49.3%;margin-right:6px">
                    <label>课程类别 (例如:ADMS)</label>
                    <input id="parentInput" class="input" type="text" list="parentCodeList" name="course_code_parent" value="">
                    <datalist id="parentCodeList"></datalist>
                </div>
                <div style="float:left;width:49.3%">
                    <label>课程代码 (例如:1000)</label>
                    <input id="childInput" class="input" type="text" list="childCodeCodeList" name="course_code_child" value="">
                    <datalist id="childCodeCodeList"></datalist>
                </div>
            </div>
            <div id="professorInputComponent" style="clear:left">
                <div style="float:left;width:49.3%;margin-right:6px">
                    <label>教授</label>
                    <input class="input" type="text" list="professorList" name="prof_name" />
                    <datalist id="professorList"></datalist>
                </div>
                <div style="float:left;width:49.3%">
                    <label>售价(积分)</label>
                    <input class="input" type="number" name="price"/>
                </div>
            </div>
            <div style="clear:left">
                <select name="term_year" class='input input-select' style="float:left;width:32%;margin-right:5px">
                    <option value="" selected>选择学年</option>
                    <?php
                        for ($i = 2000; $i <= (int)date('Y'); $i++){
                            echo "<option value='{$i}'>{$i}</option>";
                        }
                    ?>
                </select>
                <select class="input input-select" name="term_semester" style="float:left;width:32%;margin-right:5px">
                    <option value="" selected>选择学期</option>
                    <option value="Fall">Fall</option>
                    <option value="Winter">Winter</option>
                    <option value="Year">Year</option>
                    <option value="Summer">Summer</option>
                    <option value="Summer 1">Summer 1</option>
                    <option value="Summer 2">Summer 2</option>
                </select>
                <select class="input input-select" name="knowledge_category_id" style="float:left;width:32%">
                    <option value="" selected>选择分类</option>
                    <?php
                        foreach($knowledge_categories as $category)
                            echo "<option value='{$category["id"]}'>{$category['name']}</option>";
                        ?>
                </select>
            </div>
        </section>
        <section class="formBox" style="clear:left">
            <div>
                <label>卖家留言</label>
                <textarea placeholder="说点什么吧。。。" name="description" class="input input-textarea"></textarea>
            </div>
            <div style="float:left;margin-right:5px;width:49.3%">
                <label>考点量</label>
                <input class="input" name="count_knowledge_points" type="number"/>
            </div>
            <div style="float:left;width:49.3%">
                <label>排序值</label>
                <input class="input" type="number" name="sort" value="0" />
            </div>
        </section>
        <section class="formBox" style="clear:left">
            <div>
                <div id="currentImages">
                    <label style="margin-top:1.5rem">图片: 最多上传1张</label>
                    <div id="currentImages">
                        <?php
                        if ($img_id) {
                            echo "<div style='display: inline-block; vertical-align: middle;'><div><img id='pic1' src='{$imageModel->getImageById($img_id)["url"]}' style='width: 100px; height: auto;'><input id='img1' name='img_id' value='{$img_id}' style='display: none'></div><div><input type='button' id='imgbtn1' value='删除' onclick='removeImg(1);'></div></div>";
                        }
                        ?>
                    </div>
                    <p style="margin-bottom:1rem"><img id="imgOfUpload"
                                                       style="width: 100px; height: auto; display: none"></p>
                    <input type="file" name="imgFile[]" id="imgFile"/>
                </div>
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>