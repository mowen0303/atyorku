<?php
$knowledgeModel = new \admin\knowledge\KnowledgeModel();
$currentUser = new \admin\user\UserModel();
$knowledgeCategoryModel = new \admin\knowledgeCategory\KnowledgeCategoryModel();

$knowledge_id = BasicTool::get('id');
$flag = !$knowledge_id ? "add" : "update";
$knowledge_categories = $knowledgeCategoryModel->getKnowledgeCategories();
if ($flag == 'add') {
    $row = null;
    $form_action = "/admin/knowledge/knowledgeController.php?action=addKnowledge";
} else {
    $row = $knowledgeModel->getKnowledgeById($knowledge_id);
    $form_action = "/admin/knowledge/knowledgeController.php?action=updateKnowledge";
}

?>
<script>
    var nextInputId = 2;
    function addTextInput (){
        let textInput = `<div id="knowledgePointWrapper${nextInputId}"><input class="input" style="width:90%" type="text" name="knowledge_point_description[]" required/><button type="button" onclick="removeTextInput(${nextInputId})">&times;</button></div>`;
        $('#knowledgePointsBox')[0].insertAdjacentHTML('beforeend',textInput);
        nextInputId++;
    }
    function removeTextInput(i){
        $('#knowledgePointWrapper'+i).remove();
    }
</script>

<header class="topBox">
    <h1> <?php
        echo "考试回忆录" . '-';
        echo $flag == 'add' ? '添加回忆录(文字)': '更改回忆录(文字)';
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
            <div style="float:left;width:49.3%;margin-top:1rem">
                <label>排序值</label>
                <input class="input" type="number" name="sort" value="0" />
            </div>
        </section>
        <section class="formBox" id = 'knowledgePointsBox' style="clear:left">
            <label>考点</label>
            <div id="knowledgePointWrapper1">
                <input class="input" type="text" style='width:90%' name="knowledge_point_description[]" required/>
                <button type="button" onclick="removeTextInput(1)">&times;</button>
            </div>
        </section>
        <button type='button' style="background-color:#222222;border-radius: 5px;padding:8px 24px;color:white" onclick="addTextInput()">添加考点</button>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>