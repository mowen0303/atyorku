<?php
$knowledgeModel = new \admin\knowledge\KnowledgeModel();
$currentUser = new \admin\user\UserModel();
$imageModel = new \admin\image\ImageModel();
$knowledgeCategoryModel = new \admin\knowledgeCategory\KnowledgeCategoryModel();
$courseCodeModel = new \admin\courseCode\CourseCodeModel();
$profModel = new \admin\professor\ProfessorModel();
$knowledge_id = BasicTool::get('knowledge_id');
$flag = !$knowledge_id ? "add" : "update";
$knowledge_categories = $knowledgeCategoryModel->getKnowledgeCategories();
if ($flag == 'add') {
    $row = null;
    $form_action = "/admin/knowledge/knowledgeController.php?action=addKnowledge";
} else {
    $row = $knowledgeModel->getKnowledgeById($knowledge_id);
    $img_id = $row['img_id'];
    $form_action = "/admin/knowledge/knowledgeController.php?action=updateKnowledge";
    $course_code_child = $courseCodeModel->getCourseCodeById($row['course_code_id']);
    $course_code_parent = $courseCodeModel->getCourseCodeById($course_code_child['parent_id']);
    $prof = $profModel->getProfessorById($row['prof_id']);
    $prof_full_name = "{$prof['firstname']} {$prof['lastname']}";
    $category_name = $knowledgeCategoryModel->getKnowledgeCategoryById($row['knowledge_category_id'])['name'];
}

?>
<script>

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
            $('#img').attr('value', 'delete');
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
        <input name="knowledge_id" value="<?php echo $knowledge_id ?>" type="hidden"/>
        <section class="formBox">
            <div>
                <label>用户ID</label>
                <input class="input input-size30" type="text" name="seller_user_id" value="<?php echo $row['seller_user_id']?:$currentUser->userId ?>"> 当前用户<?php echo ": {$currentUser->aliasName} (ID: {$currentUser->userId})" ?>
            </div>
            <div id="courseCodeInputComponent">
                <div style="float:left;width:49.3%;margin-right:6px">
                    <label>课程类别 (例如:ADMS)</label>
                    <input id="parentInput" class="input" type="text" list="parentCodeList" name="course_code_parent" value="<?php echo $course_code_parent['title']?>">
                    <datalist id="parentCodeList"></datalist>
                </div>
                <div style="float:left;width:49.3%">
                    <label>课程代码 (例如:1000)</label>
                    <input id="childInput" class="input" type="text" list="childCodeCodeList" name="course_code_child" value="<?php echo $course_code_child['title']?>">
                    <datalist id="childCodeCodeList"></datalist>
                </div>
            </div>
            <div id="professorInputComponent" style="clear:left">
                <div style="float:left;width:49.3%;margin-right:6px">
                    <label>教授</label>
                    <input class="input" type="text" list="professorList" value="<?php echo $prof_full_name?>" name="prof_name" />
                    <datalist id="professorList"></datalist>
                </div>
                <div style="float:left;width:49.3%">
                    <label>售价(积分)</label>
                    <input class="input" type="number" value="<?php echo $row['price']?>" name="price"/>
                </div>
            </div>
            <div style="clear:left">
                <select name="term_year" class='input input-select' style="float:left;width:32%;margin-right:5px">
                    <?php
                    $output = $row?"<option value='{$row["term_year"]}' selected>{$row["term_year"]}</option>":"<option value='' selected>选择学年</option>";
                    echo $output;
                    for ($i = 2000; $i <= (int)date('Y'); $i++){
                        echo "<option value='{$i}'>{$i}</option>";
                    }
                    ?>
                </select>
                <select class="input input-select" name="term_semester" style="float:left;width:32%;margin-right:5px">
                    <?php   $output = $row?"<option value='{$row["term_semester"]}' selected>{$row['term_semester']}</option>":"<option value='' selected>选择学期</option>";
                    echo $output;
                    ?>
                    <option value="Fall">Fall</option>
                    <option value="Winter">Winter</option>
                    <option value="Year">Year</option>
                    <option value="Summer">Summer</option>
                    <option value="Summer 1">Summer 1</option>
                    <option value="Summer 2">Summer 2</option>
                </select>
                <select class="input input-select" name="knowledge_category_id" style="float:left;width:32%">
                    <?php
                    $output = $row?"<option value='{$row["knowledge_category_id"]}' selected>{$category_name}</option>":"<option value='' selected>选择分类</option>";
                    echo $output;
                    foreach($knowledge_categories as $category)
                        echo "<option value='{$category["id"]}'>{$category['name']}</option>";
                    ?>
                </select>
            </div>
        </section>
        <section class="formBox" style="clear:left">
            <div>
                <label>卖家留言</label>
                <textarea placeholder="说点什么吧。。。" name="description" class="input input-textarea"><?php echo $row["description"]?></textarea>
            </div>
            <div style="float:left;margin-right:5px;width:49.3%">
                <label>考点量</label>
                <input class="input" name="count_knowledge_points" value="<?php echo $row['count_knowledge_points']?>" type="number"/>
            </div>
            <div style="float:left;width:49.3%">
                <label>排序值</label>
                <input class="input" type="number" name="sort" value="<?php echo $row['sort']?1:0?>" />
            </div>
        </section>
        <section class="formBox" style="clear:left">
            <div>
                <div id="currentImages">
                    <label style="margin-top:1.5rem">图片: 最多上传1张</label>
                    <input id='img' name='img_id' value="<?php echo $img_id?>" hidden/>
                    <p style="margin-bottom:1rem"><?php
                                if ($img_id){
                                    $url = $imageModel->getImageById($img_id)['url'];
                                    echo "<img id='imgOfUpload' src = '{$url}'
                                                       style='width: 100px; height: auto'/>";
                                }
                                else
                                    echo "<img id='imgOfUpload'
                                                       style='width: 100px; height: auto; display: none'>";
                        ?></p>
                    <input type="file" name="imgFile[]" id="imgFile"/>
                </div>
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>